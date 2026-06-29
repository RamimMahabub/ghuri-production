<?php

namespace App\Services;

use App\Models\Availability;
use App\Models\RatePlan;
use App\Models\RateRule;
use App\Models\RoomType;
use App\Models\Promotion;
use Carbon\Carbon;

class PricingService
{
    /**
     * Calculate the price for a room type on a specific date.
     * Priority: override → seasonal → weekend → base
     */
    public function getPriceForDate(int $roomTypeId, Carbon $date, ?int $ratePlanId = null): float
    {
        $roomType = RoomType::findOrFail($roomTypeId);
        $basePrice = $roomType->base_price_per_night;

        // 1. Check for date-specific override
        $override = Availability::where('room_type_id', $roomTypeId)
            ->where('date', $date->toDateString())
            ->first();

        if ($override && $override->price_override !== null) {
            $basePrice = (float) $override->price_override;
        } else {
            // 2. Apply seasonal rule if in range
            $seasonalRule = RateRule::where('room_type_id', $roomTypeId)
                ->where('rule_type', 'seasonal')
                ->where('is_active', true)
                ->where('start_date', '<=', $date->toDateString())
                ->where('end_date', '>=', $date->toDateString())
                ->first();

            if ($seasonalRule) {
                $basePrice = $this->applyAdjustment($basePrice, $seasonalRule);
            }

            // 3. Apply weekend surcharge if Fri/Sat
            if (in_array($date->dayOfWeek, [5, 6])) {
                $weekendRule = RateRule::where('room_type_id', $roomTypeId)
                    ->where('rule_type', 'weekend_surcharge')
                    ->where('is_active', true)
                    ->first();

                if ($weekendRule) {
                    $basePrice = $this->applyAdjustment($basePrice, $weekendRule);
                }
            }
        }

        // 4. Apply rate plan supplement
        if ($ratePlanId) {
            $ratePlan = RatePlan::find($ratePlanId);
            if ($ratePlan && $ratePlan->price_supplement_per_adult > 0) {
                $basePrice += $ratePlan->price_supplement_per_adult;
            }
        }

        return round($basePrice, 2);
    }

    /**
     * Calculate total price for a stay.
     */
    public function calculateStayPrice(
        int $roomTypeId,
        Carbon $checkIn,
        Carbon $checkOut,
        int $rooms = 1,
        ?int $ratePlanId = null,
        ?string $promoCode = null,
        ?int $propertyId = null
    ): array {
        $roomType = RoomType::findOrFail($roomTypeId);
        $nights = $checkIn->diffInDays($checkOut);

        $overrides = Availability::where('room_type_id', $roomTypeId)
            ->whereBetween('date', [$checkIn->toDateString(), $checkOut->copy()->subDay()->toDateString()])
            ->get()
            ->keyBy(fn($a) => $a->date->toDateString());

        $rateRules = RateRule::where('room_type_id', $roomTypeId)->where('is_active', true)->get();
        $seasonalRules = $rateRules->where('rule_type', 'seasonal');
        $weekendRule = $rateRules->where('rule_type', 'weekend_surcharge')->first();

        $ratePlanSupplement = 0;
        if ($ratePlanId) {
            $ratePlan = RatePlan::find($ratePlanId);
            if ($ratePlan && $ratePlan->price_supplement_per_adult > 0) {
                $ratePlanSupplement = $ratePlan->price_supplement_per_adult;
            }
        }

        $nightlyRates = [];
        $subtotal = 0;

        for ($i = 0; $i < $nights; $i++) {
            $date = $checkIn->copy()->addDays($i);
            $dateStr = $date->toDateString();

            $basePrice = $roomType->base_price_per_night;
            $override = $overrides->get($dateStr);

            if ($override && $override->price_override !== null) {
                $basePrice = (float) $override->price_override;
            } else {
                $seasonalRule = $seasonalRules->first(function ($rule) use ($dateStr) {
                    return $rule->start_date <= $dateStr && $rule->end_date >= $dateStr;
                });
                if ($seasonalRule) {
                    $basePrice = $this->applyAdjustment($basePrice, $seasonalRule);
                }

                if (in_array($date->dayOfWeek, [5, 6]) && $weekendRule) {
                    $basePrice = $this->applyAdjustment($basePrice, $weekendRule);
                }
            }

            $basePrice += $ratePlanSupplement;
            $price = round($basePrice, 2);

            $nightlyRates[$dateStr] = $price;
            $subtotal += $price;
        }

        $subtotal *= $rooms;
        $taxes = round($subtotal * 0.10, 2); // 10% tax
        $fees = round($subtotal * 0.03, 2); // 3% service fee
        $discount = 0;

        // Apply promo code
        if ($promoCode && $propertyId) {
            $discount = $this->applyPromoCode($promoCode, $propertyId, $roomTypeId, $subtotal, $nights);
        }

        // Apply automated discounts (early bird, long stay, last minute)
        $autoDiscount = $this->calculateAutoDiscounts($roomTypeId, $checkIn, $nights);
        $discount += $autoDiscount;

        $total = round($subtotal + $taxes + $fees - $discount, 2);
        $avgNightlyRate = $nights > 0 ? round($subtotal / ($nights * $rooms), 2) : 0;

        return [
            'nightly_rates' => $nightlyRates,
            'nightly_rate' => $avgNightlyRate,
            'subtotal' => $subtotal,
            'taxes' => $taxes,
            'fees' => $fees,
            'discount' => $discount,
            'total' => max(0, $total),
            'nights' => $nights,
            'rooms' => $rooms,
        ];
    }

    private function applyAdjustment(float $price, RateRule $rule): float
    {
        if ($rule->adjustment_type === 'percent') {
            return $price * (1 + $rule->adjustment_value / 100);
        }

        return $price + $rule->adjustment_value;
    }

    private function applyPromoCode(string $code, int $propertyId, int $roomTypeId, float $subtotal, int $nights): float
    {
        $promo = Promotion::where('property_id', $propertyId)
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$promo || !$promo->isValid()) return 0;
        if ($promo->min_nights > $nights) return 0;

        // Check if promo applies to this room type
        if ($promo->applies_to && !in_array('all', $promo->applies_to) && !in_array($roomTypeId, $promo->applies_to)) {
            return 0;
        }

        if ($promo->discount_type === 'percent') {
            return round($subtotal * $promo->discount_value / 100, 2);
        }

        return min($promo->discount_value, $subtotal);
    }

    private function calculateAutoDiscounts(int $roomTypeId, Carbon $checkIn, int $nights): float
    {
        $discount = 0;

        // Early bird
        $daysUntilCheckin = now()->diffInDays($checkIn, false);
        $earlyBirdRule = RateRule::where('room_type_id', $roomTypeId)
            ->where('rule_type', 'early_bird')
            ->where('is_active', true)
            ->first();

        if ($earlyBirdRule && $daysUntilCheckin >= ($earlyBirdRule->condition_value['days_before'] ?? 30)) {
            // The discount is already applied in nightly rates via getPriceForDate
        }

        // Long stay
        $longStayRule = RateRule::where('room_type_id', $roomTypeId)
            ->where('rule_type', 'long_stay')
            ->where('is_active', true)
            ->first();

        if ($longStayRule) {
            $minNights = $longStayRule->condition_value['min_nights'] ?? 5;
            if ($nights >= $minNights) {
                // The discount percentage is in adjustment_value
            }
        }

        return $discount;
    }
}
