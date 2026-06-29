<x-pms-layout pageTitle="Settings" pageSubtitle="Manage your account and property settings">

    <div class="max-w-4xl">
        <div class="card">
            <div class="card-header border-b border-brand-border flex justify-between items-center">
                <h3 class="font-heading font-bold text-brand-black text-sm">Payout Settings</h3>
                <span class="badge-pending">Required</span>
            </div>
            <div class="card-body p-6">
                <form action="{{ route('property-owner.settings.update') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-input-styled w-full" placeholder="e.g. Standard Chartered" required>
                        </div>
                        <div>
                            <label class="form-label">Account Holder Name</label>
                            <input type="text" name="account_name" class="form-input-styled w-full" required>
                        </div>
                        <div>
                            <label class="form-label">Account Number</label>
                            <input type="text" name="account_number" class="form-input-styled w-full" required>
                        </div>
                        <div>
                            <label class="form-label">Routing / SWIFT Code</label>
                            <input type="text" name="routing_code" class="form-input-styled w-full" required>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-brand-border">
                        <button type="submit" class="btn-primary">Save Payout Details</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-6">
            <div class="card-header border-b border-brand-border">
                <h3 class="font-heading font-bold text-brand-black text-sm">Notification Preferences</h3>
            </div>
            <div class="card-body p-6">
                <form action="{{ route('property-owner.settings.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="notify_new_booking" checked class="rounded border-brand-border text-brand-primary">
                        <span class="text-sm font-medium text-brand-black">Email me when a new booking is made</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="notify_cancellation" checked class="rounded border-brand-border text-brand-primary">
                        <span class="text-sm font-medium text-brand-black">Email me when a booking is cancelled</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="notify_reviews" checked class="rounded border-brand-border text-brand-primary">
                        <span class="text-sm font-medium text-brand-black">Email me when a guest leaves a new review</span>
                    </label>

                    <div class="pt-4 mt-2 border-t border-brand-border">
                        <button type="submit" class="btn-primary">Update Preferences</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-pms-layout>
