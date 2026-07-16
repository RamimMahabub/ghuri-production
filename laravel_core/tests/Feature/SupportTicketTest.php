<?php

namespace Tests\Feature;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_and_continue_a_support_conversation(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->post(route('support.store'), [
            'category' => 'hotel_booking', 'priority' => 'normal',
            'subject' => 'I need help with my hotel', 'message' => 'My booking details look incorrect.',
        ])->assertRedirect();

        $ticket = SupportTicket::firstOrFail();
        $this->assertStringStartsWith('GHR-', $ticket->ticket_number);
        $this->assertSame('new', $ticket->status);
        $this->assertDatabaseHas('support_messages', ['support_ticket_id' => $ticket->id, 'sender_id' => $customer->id]);

        $this->actingAs($customer)->post(route('support.reply', $ticket), ['message' => 'Here is one more detail.'])->assertRedirect();
        $this->assertSame('waiting_for_support', $ticket->fresh()->status);
    }

    public function test_requesters_cannot_access_each_others_tickets(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $stranger = User::factory()->create(['role' => 'customer']);
        $ticket = SupportTicket::create(['requester_id' => $owner->id, 'requester_type' => 'customer', 'category' => 'other', 'subject' => 'Private issue']);

        $this->actingAs($stranger)->get(route('support.show', $ticket))->assertForbidden();
        $this->actingAs($stranger)->post(route('support.reply', $ticket), ['message' => 'Unauthorized'])->assertForbidden();
    }

    public function test_support_agent_can_reply_and_internal_notes_stay_hidden_from_requester(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $agent = User::factory()->create(['role' => 'support_agent']);
        $ticket = SupportTicket::create(['requester_id' => $customer->id, 'requester_type' => 'customer', 'category' => 'other', 'subject' => 'Please help']);
        $ticket->messages()->create(['sender_id' => $customer->id, 'body' => 'Initial message']);

        $this->actingAs($agent)->post(route('admin.support.reply', $ticket), ['message' => 'Staff-only context', 'is_internal' => 1])->assertRedirect();
        $this->actingAs($agent)->post(route('admin.support.reply', $ticket), ['message' => 'We are working on this.'])->assertRedirect();

        $this->assertSame($agent->id, $ticket->fresh()->assigned_to);
        $this->assertSame('waiting_for_customer', $ticket->fresh()->status);
        $this->actingAs($customer)->get(route('support.show', $ticket))
            ->assertOk()->assertSee('We are working on this.')->assertDontSee('Staff-only context');
    }

    public function test_resolution_requires_a_summary(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = SupportTicket::create(['requester_id' => $customer->id, 'requester_type' => 'customer', 'category' => 'other', 'subject' => 'Resolve me']);

        $this->actingAs($admin)->patch(route('admin.support.update', $ticket), ['status' => 'resolved', 'priority' => 'normal', 'assigned_to' => $admin->id, 'resolution_summary' => ''])
            ->assertSessionHasErrors('resolution_summary');

        $this->actingAs($admin)->patch(route('admin.support.update', $ticket), ['status' => 'resolved', 'priority' => 'normal', 'assigned_to' => $admin->id, 'resolution_summary' => 'The issue was corrected.'])
            ->assertRedirect();
        $this->assertSame('resolved', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->resolved_at);
    }
}
