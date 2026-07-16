<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        $upgradingLegacy = Schema::hasTable('support_tickets');
        if (! $upgradingLegacy) {
            Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->string('requester_type', 30);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('hotel_bookings')->nullOnDelete();
            $table->string('category', 60);
            $table->string('priority', 20)->default('normal');
            $table->string('status', 30)->default('new');
            $table->string('subject', 180);
            $table->timestamp('requester_last_read_at')->nullable();
            $table->timestamp('staff_last_read_at')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_summary')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('rating_comment')->nullable();
            $table->timestamps();
            $table->index(['status', 'assigned_to', 'last_message_at']);
            $table->index(['requester_id', 'last_message_at']);
            });
        } else {
            // Upgrade the small placeholder table shipped by older Bookdei installs.
            if (Schema::hasColumn('support_tickets', 'user_id') && ! Schema::hasColumn('support_tickets', 'requester_id')) {
                Schema::table('support_tickets', fn (Blueprint $table) => $table->renameColumn('user_id', 'requester_id'));
            }
            Schema::table('support_tickets', function (Blueprint $table) {
                if (! Schema::hasColumn('support_tickets', 'ticket_number')) $table->string('ticket_number')->nullable();
                if (! Schema::hasColumn('support_tickets', 'requester_type')) $table->string('requester_type', 30)->nullable();
                if (! Schema::hasColumn('support_tickets', 'assigned_to')) $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                if (! Schema::hasColumn('support_tickets', 'property_id')) $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
                if (! Schema::hasColumn('support_tickets', 'category')) $table->string('category', 60)->default('other');
                if (! Schema::hasColumn('support_tickets', 'requester_last_read_at')) $table->timestamp('requester_last_read_at')->nullable();
                if (! Schema::hasColumn('support_tickets', 'staff_last_read_at')) $table->timestamp('staff_last_read_at')->nullable();
                if (! Schema::hasColumn('support_tickets', 'last_message_at')) $table->timestamp('last_message_at')->nullable();
                if (! Schema::hasColumn('support_tickets', 'resolved_at')) $table->timestamp('resolved_at')->nullable();
                if (! Schema::hasColumn('support_tickets', 'resolution_summary')) $table->text('resolution_summary')->nullable();
                if (! Schema::hasColumn('support_tickets', 'rating')) $table->unsignedTinyInteger('rating')->nullable();
                if (! Schema::hasColumn('support_tickets', 'rating_comment')) $table->text('rating_comment')->nullable();
            });
        }

        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('is_internal')->default(false);
            $table->timestamps();
            $table->index(['support_ticket_id', 'created_at']);
        });

        Schema::create('support_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_message_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });

        Schema::create('support_ticket_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 60);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Preserve any requests created against the legacy placeholder table.
        if (Schema::hasColumn('support_tickets', 'message')) {
            foreach (DB::table('support_tickets')->orderBy('id')->get() as $legacy) {
                $number = $legacy->ticket_number ?: 'GHR-'.now()->format('Ym').'-'.Str::upper(Str::random(6));
                $role = DB::table('users')->where('id', $legacy->requester_id)->value('role') ?: 'customer';
                DB::table('support_tickets')->where('id', $legacy->id)->update(['ticket_number' => $number, 'requester_type' => $legacy->requester_type ?: $role, 'category' => $legacy->category ?: 'other', 'last_message_at' => $legacy->updated_at ?: now()]);
                if ($legacy->message && $legacy->requester_id) DB::table('support_messages')->insert(['support_ticket_id' => $legacy->id, 'sender_id' => $legacy->requester_id, 'body' => $legacy->message, 'is_internal' => false, 'created_at' => $legacy->created_at ?: now(), 'updated_at' => $legacy->updated_at ?: now()]);
            }
            Schema::table('support_tickets', fn (Blueprint $table) => $table->dropColumn('message'));
        }

        if ($upgradingLegacy) {
            Schema::table('support_tickets', fn (Blueprint $table) => $table->unique('ticket_number'));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_events');
        Schema::dropIfExists('support_attachments');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
    }
};
