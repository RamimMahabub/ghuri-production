<?php

namespace App\Http\Controllers;

use App\Models\SupportAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupportAttachmentController extends Controller
{
    public function __invoke(Request $request, SupportAttachment $attachment)
    {
        $attachment->load('message.ticket');
        $ticket = $attachment->message->ticket;
        $allowed = $request->user()->isInternalUser() || ($ticket->requester_id === $request->user()->id && ! $attachment->message->is_internal);
        abort_unless($allowed, 403);
        abort_unless(Storage::disk('local')->exists($attachment->path), 404);
        return Storage::disk('local')->download($attachment->path, $attachment->original_name);
    }
}
