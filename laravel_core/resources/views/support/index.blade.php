<x-dynamic-component :component="$layout">
    <x-slot name="title">Help & Support</x-slot>
    <x-slot name="pageTitle">Help & Support</x-slot>
    <x-slot name="pageSubtitle">Friendly help whenever you need it</x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#d00e15] to-[#8d0710] px-6 py-8 md:px-10 text-white shadow-xl">
            <div class="absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10"></div>
            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div><p class="text-white/75 text-sm font-semibold mb-2">Bookdei SUPPORT</p><h2 class="font-heading text-3xl font-bold">How can we help?</h2><p class="mt-2 text-white/80 max-w-xl">Tell us what happened and continue the conversation with our support team in one simple place.</p></div>
                <a href="{{ route($routePrefix.'.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 font-bold text-[#d00e15] shadow-lg hover:-translate-y-0.5 transition"><i class="fas fa-plus"></i> New support request</a>
            </div>
        </section>

        <div class="flex flex-wrap gap-2">
            @foreach(['' => 'All', 'new' => 'New', 'waiting_for_support' => 'Waiting for support', 'waiting_for_customer' => 'Needs your reply', 'resolved' => 'Resolved'] as $value => $label)
                <a href="{{ route($routePrefix.'.index', $value ? ['status' => $value] : []) }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ request('status','') === $value ? 'bg-[#19100F] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#d00e15]' }}">{{ $label }}</a>
            @endforeach
        </div>

        <section class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            @forelse($tickets as $ticket)
                <a href="{{ route($routePrefix.'.show', $ticket) }}" class="flex gap-4 p-5 md:p-6 border-b border-gray-100 last:border-0 hover:bg-red-50/30 transition">
                    <div class="h-12 w-12 shrink-0 rounded-2xl {{ $ticket->isUnreadFor(auth()->user()) ? 'bg-[#d00e15] text-white' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center"><i class="fas fa-comments"></i></div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2"><h3 class="font-bold text-gray-900 truncate">{{ $ticket->subject }}</h3>@if($ticket->isUnreadFor(auth()->user()))<span class="h-2 w-2 rounded-full bg-[#d00e15]"></span>@endif</div>
                        <p class="text-xs text-gray-400 mt-1">{{ $ticket->ticket_number }} · {{ $categories[$ticket->category] ?? str($ticket->category)->headline() }}</p>
                        <p class="text-sm text-gray-500 mt-2 truncate">{{ optional($ticket->messages->first())->body ?? 'Your support conversation' }}</p>
                    </div>
                    <div class="text-right shrink-0"><span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ in_array($ticket->status,['resolved','closed']) ? 'bg-emerald-50 text-emerald-700' : ($ticket->status === 'waiting_for_customer' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700') }}">{{ str($ticket->status)->replace('_',' ')->title() }}</span><p class="text-xs text-gray-400 mt-3">{{ optional($ticket->last_message_at)->diffForHumans() }}</p></div>
                </a>
            @empty
                <div class="text-center py-16 px-6"><div class="mx-auto h-16 w-16 rounded-3xl bg-red-50 text-[#d00e15] flex items-center justify-center text-2xl"><i class="far fa-comment-dots"></i></div><h3 class="font-heading font-bold text-xl mt-5">No conversations yet</h3><p class="text-gray-500 mt-2">When you need us, starting a support request takes less than a minute.</p></div>
            @endforelse
        </section>
        {{ $tickets->links() }}
    </div>
</x-dynamic-component>
