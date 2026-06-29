<?php

namespace App\Http\Controllers\PropertyOwner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function __construct(protected AvailabilityService $availabilityService) {}

    public function index(Property $hotel, Request $request)
    {
        if ($hotel->owner_id !== Auth::id()) abort(403);

        $month = $request->get('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $roomTypes = $hotel->roomTypes()->active()->get();

        $calendarData = [];
        foreach ($roomTypes as $roomType) {
            $calendarData[$roomType->id] = $this->availabilityService->getCalendarData(
                $roomType->id,
                $startDate,
                $endDate
            );
        }

        $dates = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dates[] = $current->copy();
            $current->addDay();
        }

        return view('property-owner.availability.index', compact(
            'hotel', 'roomTypes', 'calendarData', 'dates', 'month', 'startDate', 'endDate'
        ));
    }

    public function bulkUpdate(Request $request, Property $hotel)
    {
        if ($hotel->owner_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'room_type_ids' => 'required|array',
            'room_type_ids.*' => 'exists:room_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'action' => 'required|in:set_price,block,unblock,set_min_stay,close,open',
            'value' => 'nullable|numeric',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        $data = match ($validated['action']) {
            'set_price' => ['price_override' => $validated['value']],
            'block' => ['blocked_rooms' => $validated['value'] ?? 999],
            'unblock' => ['blocked_rooms' => 0],
            'set_min_stay' => ['min_stay' => (int)($validated['value'] ?? 1)],
            'close' => ['is_closed' => true],
            'open' => ['is_closed' => false],
        };

        foreach ($validated['room_type_ids'] as $roomTypeId) {
            $this->availabilityService->bulkUpdate($roomTypeId, $startDate, $endDate, $data);
        }

        return back()->with('success', 'Availability updated for selected dates.');
    }
}
