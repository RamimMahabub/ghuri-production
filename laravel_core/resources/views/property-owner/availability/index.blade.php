<x-pms-layout pageTitle="Availability Calendar" :pageSubtitle="$hotel->name">

<div class="space-y-5">
    {{-- Property Switcher --}}
    @if($properties->count() > 1)
    <div class="card card-body flex items-center justify-between">
        <h3 class="font-medium text-brand-black text-sm">Select Property to Manage</h3>
        <select onchange="window.location.href=this.value" class="form-input-styled text-sm w-64 bg-white border-gray-200">
            @foreach($properties as $prop)
                <option value="{{ route('property-owner.availability.index', ['hotel' => $prop->id, 'month' => $month]) }}" {{ $prop->id === $hotel->id ? 'selected' : '' }}>
                    {{ $prop->name }}
                </option>
            @endforeach
        </select>
    </div>
    @endif

    {{-- Month Navigation --}}
    <div class="card card-body flex items-center justify-between">
        <a href="{{ route('property-owner.availability.index', ['hotel' => $hotel, 'month' => $startDate->copy()->subMonth()->format('Y-m')]) }}" class="btn-ghost btn-sm">
            <i class="fas fa-chevron-left"></i> Previous
        </a>
        <h2 class="font-heading font-bold text-brand-black text-lg">{{ $startDate->format('F Y') }}</h2>
        <a href="{{ route('property-owner.availability.index', ['hotel' => $hotel, 'month' => $startDate->copy()->addMonth()->format('Y-m')]) }}" class="btn-ghost btn-sm">
            Next <i class="fas fa-chevron-right"></i>
        </a>
    </div>

    <div class="flex gap-5">
        {{-- Calendar Grid --}}
        <div class="flex-1 min-w-0">
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                        <tr>
                            <th class="sticky left-0 bg-brand-surface px-3 py-2 text-left text-brand-black font-semibold border-b border-r border-brand-border z-10 min-w-[140px]">Room Type</th>
                            @foreach($dates as $date)
                                <th class="px-1 py-2 text-center border-b border-brand-border font-medium cursor-pointer hover:bg-brand-muted/20 {{ in_array($date->dayOfWeek, [5, 6]) ? 'bg-yellow-50' : 'bg-brand-surface' }}"
                                    data-date="{{ $date->format('Y-m-d') }}">
                                    <div class="text-[10px] text-brand-muted">{{ $date->format('D') }}</div>
                                    <div class="{{ $date->isToday() ? 'text-brand-primary font-bold' : 'text-brand-black' }}">{{ $date->format('d') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roomTypes as $roomType)
                            <tr>
                                <td class="sticky left-0 bg-white px-3 py-2 border-b border-r border-brand-border z-10">
                                    <p class="font-medium text-brand-black">{{ $roomType->name }}</p>
                                    <p class="text-[10px] text-brand-muted">{{ $roomType->inventory_count }} rooms · {{ \App\Helpers\Currency::format($roomType->base_price_per_night) }}/night</p>
                                </td>
                                @foreach($dates as $date)
                                    @php
                                        $dateStr = $date->format('Y-m-d');
                                        $cell = $calendarData[$roomType->id][$dateStr] ?? null;
                                        $available = $cell ? $cell['available'] : $roomType->inventory_count;
                                        $total = $cell ? $cell['total'] : $roomType->inventory_count;
                                        $price = $cell ? $cell['price'] : $roomType->base_price_per_night;
                                        $isClosed = $cell ? $cell['is_closed'] : false;
                                        $blocked = $cell ? $cell['blocked'] : 0;
                                        $minStay = $cell ? $cell['min_stay'] : 1;
                                        $isWeekend = $cell ? $cell['is_weekend'] : in_array($date->dayOfWeek, [5, 6]);
                                    @endphp
                                    <td class="avail-cell cursor-pointer hover:bg-brand-muted/20 {{ $isClosed ? 'blocked' : ($available <= 0 ? 'sold-out' : ($isWeekend ? 'weekend' : '')) }}" 
                                        title="{{ $dateStr }}" data-date="{{ $dateStr }}" data-room-type-id="{{ $roomType->id }}"
                                        data-price="{{ $price }}" data-total="{{ $total }}" data-blocked="{{ $blocked }}" data-min-stay="{{ $minStay }}">
                                        @if(!$isClosed)
                                            <div class="avail-count">{{ $available }}/{{ $total }}</div>
                                            <div class="avail-price">{{ \App\Helpers\Currency::format($price) }}</div>
                                        @else
                                            <div class="text-[10px]">Closed</div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>

            {{-- Legend --}}
            <div class="flex items-center gap-4 mt-3 text-xs text-brand-muted">
                <span><span class="inline-block w-4 h-3 bg-white border border-brand-border rounded mr-1"></span> Available</span>
                <span><span class="inline-block w-4 h-3 bg-yellow-50 border border-brand-border rounded mr-1"></span> Weekend</span>
                <span><span class="inline-block w-4 h-3 bg-brand-primary rounded mr-1"></span> Sold Out</span>
                <span><span class="inline-block w-4 h-3 rounded mr-1" style="background: repeating-linear-gradient(45deg,#F5F5F5,#F5F5F5 2px,#E0E0E0 2px,#E0E0E0 4px)"></span> Blocked</span>
            </div>
        </div>

        {{-- Bulk Editor Panel --}}
        <div class="w-80 flex-shrink-0 hidden xl:block">
            <div class="card card-body sticky top-24">
                <h3 class="font-heading font-bold text-brand-black text-sm mb-4"><i class="fas fa-edit text-brand-primary mr-2"></i>Bulk Update</h3>

                <form method="POST" action="{{ route('property-owner.availability.bulk-update', $hotel) }}" class="space-y-4" x-data="{ 
                    action: 'set_price', 
                    value: '', 
                    currentData: { price: '', total: '', blocked: '', minStay: '' },
                    updateValue() {
                        if (this.action === 'set_price') this.value = this.currentData.price;
                        else if (this.action === 'set_total_rooms') this.value = this.currentData.total;
                        else if (this.action === 'block') this.value = (this.currentData.blocked > 0 ? this.currentData.blocked : '');
                        else if (this.action === 'set_min_stay') this.value = this.currentData.minStay;
                        else this.value = '';
                    }
                }" @cell-selected.window="currentData = $event.detail; updateValue()" x-init="$watch('action', () => updateValue())">
                    @csrf

                    <div class="form-group">
                        <label class="form-label text-xs">Date Range</label>
                        <input type="date" name="start_date" class="form-input-styled text-sm mb-1" required>
                        <input type="date" name="end_date" class="form-input-styled text-sm" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-xs">Apply to Room Types</label>
                        <div class="max-h-32 overflow-y-auto pr-2">
                            @foreach($roomTypes as $rt)
                                <label class="flex items-center gap-2 text-sm mb-1">
                                    <input type="checkbox" name="room_type_ids[]" value="{{ $rt->id }}" class="rounded border-brand-border text-brand-primary" checked>
                                    {{ $rt->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label text-xs mb-2 block">Action</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center p-2 border border-brand-border rounded cursor-pointer transition-colors" :class="{'bg-brand-primary text-white border-brand-primary': action === 'set_price', 'hover:bg-brand-muted/10 text-brand-black': action !== 'set_price'}">
                                <input type="radio" name="action" value="set_price" x-model="action" class="hidden">
                                <span class="text-xs font-medium">Set Price</span>
                            </label>
                            <label class="flex items-center justify-center p-2 border border-brand-border rounded cursor-pointer transition-colors" :class="{'bg-brand-primary text-white border-brand-primary': action === 'set_total_rooms', 'hover:bg-brand-muted/10 text-brand-black': action !== 'set_total_rooms'}">
                                <input type="radio" name="action" value="set_total_rooms" x-model="action" class="hidden">
                                <span class="text-xs font-medium">Total Rooms</span>
                            </label>
                            <label class="flex items-center justify-center p-2 border border-brand-border rounded cursor-pointer transition-colors" :class="{'bg-brand-primary text-white border-brand-primary': action === 'block', 'hover:bg-brand-muted/10 text-brand-black': action !== 'block'}">
                                <input type="radio" name="action" value="block" x-model="action" class="hidden">
                                <span class="text-xs font-medium">Block Rooms</span>
                            </label>
                            <label class="flex items-center justify-center p-2 border border-brand-border rounded cursor-pointer transition-colors" :class="{'bg-brand-primary text-white border-brand-primary': action === 'unblock', 'hover:bg-brand-muted/10 text-brand-black': action !== 'unblock'}">
                                <input type="radio" name="action" value="unblock" x-model="action" class="hidden">
                                <span class="text-xs font-medium">Unblock</span>
                            </label>
                            <label class="flex items-center justify-center p-2 border border-brand-border rounded cursor-pointer transition-colors" :class="{'bg-brand-primary text-white border-brand-primary': action === 'close', 'hover:bg-brand-muted/10 text-brand-black': action !== 'close'}">
                                <input type="radio" name="action" value="close" x-model="action" class="hidden">
                                <span class="text-xs font-medium">Close Dates</span>
                            </label>
                            <label class="flex items-center justify-center p-2 border border-brand-border rounded cursor-pointer transition-colors" :class="{'bg-brand-primary text-white border-brand-primary': action === 'open', 'hover:bg-brand-muted/10 text-brand-black': action !== 'open'}">
                                <input type="radio" name="action" value="open" x-model="action" class="hidden">
                                <span class="text-xs font-medium">Open Dates</span>
                            </label>
                            <label class="flex items-center justify-center p-2 border border-brand-border rounded cursor-pointer transition-colors col-span-2" :class="{'bg-brand-primary text-white border-brand-primary': action === 'set_min_stay', 'hover:bg-brand-muted/10 text-brand-black': action !== 'set_min_stay'}">
                                <input type="radio" name="action" value="set_min_stay" x-model="action" class="hidden">
                                <span class="text-xs font-medium">Set Min Stay</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group" x-show="!['unblock', 'close', 'open'].includes(action)">
                        <label class="form-label text-xs flex items-center justify-between">
                            <span x-text="action === 'set_price' ? 'Price Per Night' : (action === 'set_total_rooms' ? 'Total Available Rooms' : (action === 'block' ? 'Number of Rooms to Block' : 'Minimum Stay (Nights)'))">Value</span>
                            <span class="text-[10px] text-brand-muted font-normal" x-show="action === 'block'">(leave empty to block all)</span>
                        </label>
                        <input type="number" name="value" x-model="value" class="form-input-styled text-sm" step="0.01" :placeholder="action === 'set_price' ? 'e.g. 5000' : (action === 'set_total_rooms' ? 'e.g. 5' : (action === 'block' ? 'e.g. 2' : 'e.g. 3'))">
                    </div>

                    <button type="submit" class="btn-primary w-full"><i class="fas fa-save"></i> Apply Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let firstClickDate = null;
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        const roomTypeCheckboxes = document.querySelectorAll('input[name="room_type_ids[]"]');

        function updateSelectionHighlight() {
            const start = startDateInput.value;
            const end = endDateInput.value;
            
            const checkedRoomTypes = Array.from(roomTypeCheckboxes)
                                          .filter(cb => cb.checked)
                                          .map(cb => cb.value);

            document.querySelectorAll('th[data-date], td[data-date]').forEach(cell => {
                const date = cell.getAttribute('data-date');
                if (!date) return;
                
                const isTh = cell.tagName.toLowerCase() === 'th';
                const cellRoomTypeId = cell.getAttribute('data-room-type-id');
                const isRoomTypeChecked = isTh || (cellRoomTypeId && checkedRoomTypes.includes(cellRoomTypeId));
                
                // Remove previous highlights
                cell.classList.remove('ring-2', 'ring-inset', 'ring-brand-primary', 'bg-brand-primary/10');
                
                let inRange = false;
                if (firstClickDate && date === firstClickDate) {
                    inRange = true;
                } else if (start && end && date >= start && date <= end) {
                    inRange = true;
                }
                
                if (inRange && isRoomTypeChecked) {
                    cell.classList.add('ring-2', 'ring-inset', 'ring-brand-primary', 'bg-brand-primary/10');
                }
            });
        }

        document.querySelectorAll('th[data-date], td[data-date]').forEach(cell => {
            cell.addEventListener('click', (e) => {
                const date = e.currentTarget.getAttribute('data-date');
                if (!date) return;
                
                const cellRoomTypeId = e.currentTarget.getAttribute('data-room-type-id');
                if (cellRoomTypeId) {
                    roomTypeCheckboxes.forEach(cb => {
                        cb.checked = cb.value === cellRoomTypeId;
                    });
                    
                    const price = e.currentTarget.getAttribute('data-price');
                    const total = e.currentTarget.getAttribute('data-total');
                    const blocked = e.currentTarget.getAttribute('data-blocked');
                    const minStay = e.currentTarget.getAttribute('data-min-stay');
                    window.dispatchEvent(new CustomEvent('cell-selected', {
                        detail: { price, total, blocked, minStay }
                    }));
                } else {
                    roomTypeCheckboxes.forEach(cb => {
                        cb.checked = true;
                    });
                    
                    window.dispatchEvent(new CustomEvent('cell-selected', {
                        detail: { price: '', total: '', blocked: '', minStay: '' }
                    }));
                }
                
                if (!firstClickDate) {
                    firstClickDate = date;
                    startDateInput.value = date;
                    endDateInput.value = date;
                } else {
                    const d1 = new Date(firstClickDate);
                    const d2 = new Date(date);
                    if (d2 < d1) {
                        startDateInput.value = date;
                        endDateInput.value = firstClickDate;
                    } else {
                        startDateInput.value = firstClickDate;
                        endDateInput.value = date;
                    }
                    firstClickDate = null; // Reset for the next pair of clicks
                }
                
                updateSelectionHighlight();
            });
        });
        
        // Also update highlight if inputs or checkboxes are changed manually
        startDateInput.addEventListener('change', () => {
            firstClickDate = null;
            updateSelectionHighlight();
        });
        endDateInput.addEventListener('change', () => {
            firstClickDate = null;
            updateSelectionHighlight();
        });
        roomTypeCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateSelectionHighlight);
        });
    });
</script>
@endpush
</x-pms-layout>
