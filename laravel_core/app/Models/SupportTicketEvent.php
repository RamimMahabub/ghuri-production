<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicketEvent extends Model
{
    protected $fillable = ['support_ticket_id', 'actor_id', 'event', 'metadata'];
    protected function casts(): array { return ['metadata' => 'array']; }
    public function ticket() { return $this->belongsTo(SupportTicket::class, 'support_ticket_id'); }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}
