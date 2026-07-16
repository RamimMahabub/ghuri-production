<?php
foreach(\App\Models\HotelBooking::where('commission_amount', 0)->get() as $b) { 
    $rate = 15.0; 
    $prop = $b->property; 
    if ($prop) { 
        $comm = $prop->commissions()->where('effective_from', '<=', now())->orderBy('effective_from', 'desc')->first(); 
        if ($comm) {
            $rate = (float) $comm->rate_percent; 
        }
    } 
    $b->update(['commission_amount' => round($b->total * ($rate / 100), 2)]); 
}
echo "Backfill complete.\n";
