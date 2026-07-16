<?php

namespace App\Http\Controllers\PropertyOwner;

use App\Http\Controllers\Controller;
use App\Models\HotelBooking;
use App\Models\Property;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayoutController extends Controller
{
    public function index()
    {
        $ownerId = Auth::id();
        $properties = Property::where('owner_id', $ownerId)->get();
        $propertyIds = $properties->pluck('id');

        $bookings = HotelBooking::with(['guest', 'property', 'payout'])
            ->whereIn('property_id', $propertyIds)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'checked_out'])
            ->orderBy('check_in', 'desc')
            ->get();

        $payouts = Payout::whereIn('property_id', $propertyIds)->get();

        $propertyBalances = [];
        foreach ($properties as $prop) {
            $propertyBalances[$prop->id] = [
                'name' => $prop->name,
                'withdrawable' => 0,
                'pending' => 0,
                'upcoming' => 0,
                'paid_out' => $payouts->where('property_id', $prop->id)->where('status', 'processed')->sum('amount'),
            ];
        }

        $totalPayoutsAllocated = Payout::whereIn('property_id', $propertyIds)
            ->where('status', '!=', 'rejected')
            ->sum('amount');

        $totalPaidOut = $payouts->where('status', 'processed')->sum('amount');

        $withdrawable = 0;
        $pending = 0;
        $upcoming = 0;

        $today = now()->startOfDay();

        foreach ($bookings as $booking) {
            $payoutAmount = $booking->total - $booking->commission_amount;
            
            if ($booking->status === 'checked_out') {
                if ($booking->payout_id !== null) {
                    $booking->payout_status = 'Paid Out';
                } else {
                    $booking->payout_status = 'Withdrawable';
                    $withdrawable += $payoutAmount;
                    $propertyBalances[$booking->property_id]['withdrawable'] += $payoutAmount;
                }
            } elseif ($booking->status === 'checked_in' || ($booking->check_in <= $today && $booking->check_out > $today)) {
                $booking->payout_status = 'Ongoing';
                $pending += $payoutAmount;
                $propertyBalances[$booking->property_id]['pending'] += $payoutAmount;
            } else {
                $booking->payout_status = 'Upcoming';
                $upcoming += $payoutAmount;
                $propertyBalances[$booking->property_id]['upcoming'] += $payoutAmount;
            }
        }

        // Deduct manual payouts (created without period_start)
        $manualPayouts = $payouts->where('status', '!=', 'rejected')->whereNull('period_start');
        $manualPayoutsTotal = $manualPayouts->sum('amount');
        
        $withdrawable = max(0, $withdrawable - $manualPayoutsTotal);

        foreach ($manualPayouts as $manualPayout) {
             if (isset($propertyBalances[$manualPayout->property_id])) {
                  $propertyBalances[$manualPayout->property_id]['withdrawable'] = max(0, $propertyBalances[$manualPayout->property_id]['withdrawable'] - $manualPayout->amount);
             }
        }

        $user = Auth::user();
        $userCurrency = session('currency', 'BDT');
        $currencyConverter = app(\App\Services\CurrencyConverterService::class);
        
        $withdrawableInUserCurrency = $withdrawable;
        if ($userCurrency === 'USD') {
            $rateToUsd = $currencyConverter->getExchangeRate('BDT', 'USD');
            $withdrawableInUserCurrency = round($withdrawable * $rateToUsd, 2);
        }

        return view('property-owner.payouts.index', compact(
            'bookings',
            'withdrawable',
            'withdrawableInUserCurrency',
            'pending',
            'upcoming',
            'totalPaidOut',
            'user',
            'propertyBalances'
        ));
    }

    public function updatePaymentMethod(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:bank,card,mfs',
            'bank_name' => 'required_if:payment_type,bank|nullable|string',
            'account_name' => 'required_if:payment_type,bank|nullable|string',
            'account_number' => 'required_if:payment_type,bank|nullable|string',
            'branch' => 'nullable|string',
            'routing_number' => 'nullable|string',
            'cardholder_name' => 'required_if:payment_type,card|nullable|string',
            'card_number' => 'required_if:payment_type,card|nullable|string',
            'expiry_month' => 'required_if:payment_type,card|nullable|string',
            'expiry_year' => 'required_if:payment_type,card|nullable|string',
            'mfs_provider' => 'required_if:payment_type,mfs|nullable|in:bkash,nagad,rocket,upay',
            'mfs_number' => 'required_if:payment_type,mfs|nullable|string',
        ]);

        $user = Auth::user();

        if ($request->payment_type === 'bank') {
            $user->bank_details = [
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'branch' => $request->branch,
                'routing_number' => $request->routing_number,
            ];
            $user->save();
            return back()->with('success', 'Bank details updated successfully.');
        }

        if ($request->payment_type === 'card') {
            // Usually, full card number is not saved without PCI compliance. 
            // For this implementation we will save the last 4 digits or masked.
            $cardNumber = $request->card_number;
            $maskedCard = str_repeat('*', max(0, strlen($cardNumber) - 4)) . substr($cardNumber, -4);
            
            $user->card_details = [
                'cardholder_name' => $request->cardholder_name,
                'card_number' => $maskedCard, // In a real app, use a secure vault/token
                'expiry_month' => $request->expiry_month,
                'expiry_year' => $request->expiry_year,
            ];
            $user->save();
            return back()->with('success', 'Card details updated successfully.');
        }

        if ($request->payment_type === 'mfs') {
            $user->mfs_details = [
                'mfs_provider' => $request->mfs_provider,
                'mfs_number' => $request->mfs_number,
            ];
            $user->save();
            return back()->with('success', 'Mobile Banking details updated successfully.');
        }

        return back()->with('error', 'Invalid payment type.');
    }

    public function requestWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:bank,card,mfs'
        ]);

        $user = Auth::user();
        
        if ($request->payment_method === 'bank' && empty($user->bank_details)) {
            return back()->with('error', 'Bank details are missing.');
        }
        if ($request->payment_method === 'card' && empty($user->card_details)) {
            return back()->with('error', 'Card details are missing.');
        }
        if ($request->payment_method === 'mfs' && empty($user->mfs_details)) {
            return back()->with('error', 'Mobile Banking details are missing.');
        }

        $propertyIds = Property::where('owner_id', $user->id)->pluck('id');
        
        $bookings = HotelBooking::whereIn('property_id', $propertyIds)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'checked_out'])
            ->get();

        $withdrawable = 0;
        foreach ($bookings as $booking) {
            if ($booking->status === 'checked_out' && $booking->payout_id === null) {
                $withdrawable += ($booking->total - $booking->commission_amount);
            }
        }

        $manualPayouts = Payout::whereIn('property_id', $propertyIds)
            ->where('status', '!=', 'rejected')
            ->whereNull('period_start')
            ->sum('amount');

        $withdrawable = max(0, $withdrawable - $manualPayouts);

        $userCurrency = session('currency', 'BDT');
        $currencyConverter = app(\App\Services\CurrencyConverterService::class);
        
        $requestedAmountBase = $request->amount;
        $withdrawableInUserCurrency = $withdrawable;
        
        if ($userCurrency === 'USD') {
            $rateToBdt = $currencyConverter->getExchangeRate('USD', 'BDT');
            $requestedAmountBase = round($request->amount * $rateToBdt, 2);
            
            $rateToUsd = $currencyConverter->getExchangeRate('BDT', 'USD');
            $withdrawableInUserCurrency = round($withdrawable * $rateToUsd, 2);
        }

        if ($requestedAmountBase > $withdrawable) {
            return back()->with('error', "Amount exceeds withdrawable balance. (Requested: {$request->amount}, Withdrawable: {$withdrawableInUserCurrency})");
        }

        $propertyId = $propertyIds->first();
        
        Payout::create([
            'property_id' => $propertyId,
            'amount' => $requestedAmountBase,
            'status' => 'requested',
            'reference' => 'REQ-' . strtoupper(\Illuminate\Support\Str::random(6))
        ]);

        return back()->with('success', 'Withdrawal requested successfully.');
    }

    public function showInvoice(Payout $payout)
    {
        $user = Auth::user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();

        if (!in_array($payout->property_id, $propertyIds)) {
            abort(403);
        }

        $bookings = HotelBooking::where('payout_id', $payout->id)->get();

        return view('admin.payouts.invoice', compact('payout', 'bookings'));
    }
}
