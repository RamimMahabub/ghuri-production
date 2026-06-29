<x-pms-layout :pageTitle="'Rate Rules: ' . $hotel->name" pageSubtitle="Manage automated pricing rules and discounts">

    <x-slot name="headerActions">
        <a href="{{ route('property-owner.hotels.show', $hotel) }}" class="btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Property
        </a>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            @foreach($roomTypes as $room)
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h3 class="font-heading font-bold text-brand-black text-sm">{{ $room->name }}</h3>
                        <span class="text-xs text-brand-muted">Base: ${{ number_format($room->base_price_per_night, 2) }}</span>
                    </div>
                    
                    <div class="p-0">
                        @if($room->rateRules->isEmpty())
                            <div class="p-6 text-center text-brand-muted text-sm">
                                No rate rules applied. Standard base price will be used.
                            </div>
                        @else
                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100">
                                        <th class="py-2 px-4 font-semibold text-xs text-brand-muted">Type</th>
                                        <th class="py-2 px-4 font-semibold text-xs text-brand-muted">Valid Dates</th>
                                        <th class="py-2 px-4 font-semibold text-xs text-brand-muted text-right">Adjustment</th>
                                        <th class="py-2 px-4 font-semibold text-xs text-brand-muted text-center">Status</th>
                                        <th class="py-2 px-4"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($room->rateRules as $rule)
                                        <tr>
                                            <td class="py-3 px-4 font-medium text-brand-black">{{ \App\Models\RateRule::getRuleTypes()[$rule->rule_type] ?? $rule->rule_type }}</td>
                                            <td class="py-3 px-4 text-brand-muted">
                                                @if($rule->start_date && $rule->end_date)
                                                    {{ $rule->start_date->format('M d, Y') }} - {{ $rule->end_date->format('M d, Y') }}
                                                @else
                                                    Always active
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-right font-medium {{ $rule->adjustment_value > 0 ? 'text-status-danger' : 'text-status-confirmed' }}">
                                                {{ $rule->adjustment_value > 0 ? '+' : '' }}{{ rtrim(rtrim($rule->adjustment_value, '0'), '.') }}{{ $rule->adjustment_type === 'percent' ? '%' : '$' }}
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="badge-{{ $rule->is_active ? 'confirmed' : 'pending' }} text-[10px]">
                                                    {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-right">
                                                <form action="{{ route('property-owner.hotels.rate-rules.destroy', [$hotel, $rule]) }}" method="POST" onsubmit="return confirm('Delete this rule?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-brand-muted hover:text-status-danger transition">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div>
            <div class="card card-body sticky top-24">
                <h3 class="font-heading font-bold text-brand-black text-sm mb-4">Add New Rule</h3>
                <form action="{{ route('property-owner.hotels.rate-rules.store', $hotel) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="form-label text-xs">Room Type</label>
                        <select name="room_type_id" class="form-input-styled text-sm w-full" required>
                            @foreach($roomTypes as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label text-xs">Rule Type</label>
                        <select name="rule_type" class="form-input-styled text-sm w-full" required>
                            @foreach(\App\Models\RateRule::getRuleTypes() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label text-xs">Start Date</label>
                            <input type="date" name="start_date" class="form-input-styled text-sm w-full">
                        </div>
                        <div>
                            <label class="form-label text-xs">End Date</label>
                            <input type="date" name="end_date" class="form-input-styled text-sm w-full">
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-xs">Adjustment</label>
                        <div class="flex">
                            <select name="adjustment_type" class="form-input-styled text-sm rounded-r-none border-r-0 w-1/3 bg-gray-50">
                                <option value="percent">%</option>
                                <option value="flat">$</option>
                            </select>
                            <input type="number" name="adjustment_value" class="form-input-styled text-sm w-2/3 rounded-l-none" step="0.01" required placeholder="Use negative for discount">
                        </div>
                        <p class="text-[10px] text-brand-muted mt-1">Example: 10 (Surcharge), -15 (Discount)</p>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-brand-border text-brand-primary">
                            <span class="text-sm font-medium text-brand-black">Rule is active</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary w-full mt-2">Add Rule</button>
                </form>
            </div>
        </div>
    </div>

</x-pms-layout>
