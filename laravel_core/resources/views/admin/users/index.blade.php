<x-admin-layout>
    <x-slot name="pageTitle">User Management</x-slot>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('role') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:bg-gray-50' }}">All</a>
                <a href="{{ route('admin.users.index', ['role' => 'customer']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('role') === 'customer' ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:bg-gray-50' }}">Customers</a>
                <a href="{{ route('admin.users.index', ['role' => 'property_owner']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('role') === 'property_owner' ? 'bg-purple-50 text-purple-700' : 'text-gray-500 hover:bg-gray-50' }}">Property Owners</a>
                <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('role') === 'admin' ? 'bg-red-50 text-red-700' : 'text-gray-500 hover:bg-gray-50' }}">Admins</a>
            </div>
            
            <form method="GET" action="{{ route('admin.users.index') }}" class="relative">
                @if(request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Search name or email..." value="{{ request('search') }}" class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Role</th>
                        <th class="px-6 py-4 font-medium">Joined</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->role === 'admin')
                                    <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Admin</span>
                                @elseif($user->role === 'property_owner')
                                    <span class="px-2.5 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">Owner</span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-full">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-800">{{ $user->created_at->format('M d, Y') }}</p>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    Edit
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="p-6 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
