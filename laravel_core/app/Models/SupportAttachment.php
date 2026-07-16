<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportAttachment extends Model
{
    protected $fillable = ['support_message_id', 'original_name', 'path', 'mime_type', 'size'];
    public function message() { return $this->belongsTo(SupportMessage::class, 'support_message_id'); }
}
