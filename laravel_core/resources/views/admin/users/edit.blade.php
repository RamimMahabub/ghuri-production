<x-admin-layout>
    <x-slot name="pageTitle">Edit User</x-slot>
    
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline text-sm font-medium">
            <i class="fas fa-arrow-left mr-1"></i> Back to Users
        </a>
    </div>

    <div class="max-w-2xl bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-slate-800">Edit User: {{ $user->name }}</h2>
        </div>
        
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-slate-700 mb-1">Platform Role</label>
                <select name="role" id="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="customer" {{ (old('role', $user->role) === 'customer') ? 'selected' : '' }}>Customer</option>
                    <option value="property_owner" {{ (old('role', $user->role) === 'property_owner') ? 'selected' : '' }}>Property Owner</option>
                    <option value="admin" {{ (old('role', $user->role) === 'admin') ? 'selected' : '' }}>Administrator</option>
                    <option value="manager" {{ (old('role', $user->role) === 'manager') ? 'selected' : '' }}>Manager</option>
                    <option value="support_agent" {{ (old('role', $user->role) === 'support_agent') ? 'selected' : '' }}>Support Agent</option>
                </select>
                @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
