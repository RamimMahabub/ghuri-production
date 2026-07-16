<x-admin-layout>
    <x-slot name="title">{{ $ticket->ticket_number }}</x-slot><x-slot name="pageTitle">{{ $ticket->subject }}</x-slot><x-slot name="pageSubtitle">{{ $ticket->ticket_number }} · {{ str($ticket->requester_type)->replace('_',' ')->title() }}</x-slot>
    <div class="mb-5"><a href="{{ route('admin.support.index') }}" class="text-sm font-semibold text-gray-500 hover:text-[#d00e15]"><i class="fas fa-arrow-left mr-2"></i>Back to inbox</a></div>
    @if($errors->any())<div class="mb-5 rounded-2xl bg-red-50 border border-red-100 p-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    <div class="grid xl:grid-cols-[minmax(0,1fr)_330px] gap-6 items-start">
        <section class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div id="conversation" class="p-5 md:p-7 space-y-6 bg-[#fbfbfc] min-h-[500px] max-h-[65vh] overflow-y-auto custom-scrollbar">
                @foreach($ticket->messages as $message)
                    @php($staff = $message->sender->isInternalUser())
                    <div class="flex gap-3 {{ $staff ? 'justify-end' : '' }}"><div class="max-w-[82%]"><div class="rounded-2xl px-4 py-3 {{ $message->is_internal ? 'bg-amber-50 border border-amber-200 text-amber-900' : ($staff ? 'bg-[#19100F] text-white rounded-br-md' : 'bg-white border border-gray-100 shadow-sm rounded-bl-md') }}">@if($message->is_internal)<p class="text-[10px] font-bold uppercase tracking-wider text-amber-600 mb-2"><i class="fas fa-lock mr-1"></i> Internal note</p>@endif<p class="text-sm whitespace-pre-wrap leading-relaxed">{{ $message->body }}</p>@if($message->attachments->isNotEmpty())<div class="mt-3 space-y-2">@foreach($message->attachments as $attachment)<a href="{{ route('support.attachments.download',$attachment) }}" class="flex items-center gap-2 rounded-xl bg-black/5 px-3 py-2 text-xs font-semibold"><i class="fas fa-paperclip"></i>{{ $attachment->original_name }}</a>@endforeach</div>@endif</div><p class="text-[11px] text-gray-400 mt-1 {{ $staff ? 'text-right' : '' }}">{{ $message->sender->name }} · {{ $message->created_at->format('d M, g:i A') }}</p></div></div>
                @endforeach
            </div>
            @if($ticket->status !== 'closed')<form method="POST" action="{{ route('admin.support.reply',$ticket) }}" enctype="multipart/form-data" class="p-5 border-t border-gray-100" x-data="{ internal: false }">@csrf<div class="mb-3 flex items-center justify-between"><label class="flex items-center gap-2 text-sm font-semibold cursor-pointer"><input type="checkbox" name="is_internal" value="1" x-model="internal" class="rounded border-gray-300 text-amber-500 focus:ring-amber-400"> Internal note</label><span class="text-xs" :class="internal ? 'text-amber-600' : 'text-gray-400'" x-text="internal ? 'Only staff can see this' : 'The requester will be notified'"></span></div><div class="rounded-2xl border border-gray-200" :class="internal ? 'bg-amber-50 border-amber-200' : ''"><textarea name="message" required rows="3" maxlength="10000" :placeholder="internal ? 'Leave context for your team…' : 'Write a helpful reply…'" class="w-full resize-none border-0 bg-transparent rounded-2xl focus:ring-0 text-sm"></textarea><div class="flex justify-between items-center px-3 pb-3"><label class="cursor-pointer text-gray-400 hover:text-[#d00e15]"><i class="fas fa-paperclip"></i><input type="file" name="attachments[]" multiple class="hidden"></label><button class="rounded-xl px-5 py-2.5 text-white text-sm font-bold" :class="internal ? 'bg-amber-500' : 'bg-[#d00e15]'" x-text="internal ? 'Add note' : 'Send reply'"></button></div></div></form>@endif
        </section>
        <aside class="space-y-5">
            <form method="POST" action="{{ route('admin.support.update',$ticket) }}" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5 space-y-4">@csrf @method('PATCH')<h3 class="font-heading font-bold text-lg">Ticket controls</h3><label class="block"><span class="text-xs font-bold text-gray-500">Status</span><select name="status" class="mt-1 w-full rounded-xl border-gray-200 text-sm">@foreach(\App\Models\SupportTicket::STATUSES as $status)<option value="{{ $status }}" @selected($ticket->status===$status)>{{ str($status)->replace('_',' ')->title() }}</option>@endforeach</select></label><label class="block"><span class="text-xs font-bold text-gray-500">Priority</span><select name="priority" class="mt-1 w-full rounded-xl border-gray-200 text-sm">@foreach(\App\Models\SupportTicket::PRIORITIES as $priority)<option value="{{ $priority }}" @selected($ticket->priority===$priority)>{{ str($priority)->title() }}</option>@endforeach</select></label><label class="block"><span class="text-xs font-bold text-gray-500">Assigned agent</span><select name="assigned_to" class="mt-1 w-full rounded-xl border-gray-200 text-sm"><option value="">Unassigned</option>@foreach($agents as $agent)<option value="{{ $agent->id }}" @selected($ticket->assigned_to===$agent->id)>{{ $agent->name }} · {{ str($agent->role)->headline() }}</option>@endforeach</select></label><label class="block"><span class="text-xs font-bold text-gray-500">Resolution summary</span><textarea name="resolution_summary" rows="3" class="mt-1 w-full rounded-xl border-gray-200 text-sm" placeholder="Required when resolving">{{ $ticket->resolution_summary }}</textarea></label><button class="w-full rounded-xl bg-[#19100F] py-3 text-white text-sm font-bold">Save changes</button></form>
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-heading font-bold text-lg">Requester</h3>
                <div class="flex items-center gap-3 mt-4">
                    <span class="h-11 w-11 rounded-2xl bg-red-50 text-[#d00e15] flex items-center justify-center font-bold">
                        {{ substr($ticket->requester->name, 0, 1) }}
                    </span>
                    <div class="min-w-0">
                        <p class="font-bold truncate">{{ $ticket->requester->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $ticket->requester->email }}</p>
                    </div>
                </div>

                <dl class="mt-5 space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400">Category</dt>
                        <dd class="font-semibold">{{ str($ticket->category)->replace('_', ' ')->title() }}</dd>
                    </div>

                    @if ($ticket->property)
                        <div>
                            <dt class="text-xs text-gray-400">Property</dt>
                            <dd class="font-semibold">{{ $ticket->property->name }}</dd>
                        </div>
                    @endif

                    @if ($ticket->booking)
                        <div>
                            <dt class="text-xs text-gray-400">Booking</dt>
                            <dd class="font-semibold">#{{ $ticket->booking->booking_reference ?? $ticket->booking->id }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </aside>
    </div>
    @push('scripts')<script>const chat=document.getElementById('conversation');if(chat)chat.scrollTop=chat.scrollHeight;</script>@endpush
</x-admin-layout>
