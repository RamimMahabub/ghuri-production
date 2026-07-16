<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\Property;
use App\Models\HotelBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PayoutController extends Controller
{
    public function index()
    {
        $payouts = Payout::with(['property.owner'])->orderBy('created_at', 'desc')->get();
        
        $totalPayoutsAmount = $payouts->sum('amount');
        $totalRequestedAmount = $payouts->where('status', 'requested')->sum('amount');
        $totalGivenAmount = $payouts->where('status', 'processed')->sum('amount');

        return view('admin.payouts.index', compact(
            'payouts',
            'totalPayoutsAmount',
            'totalRequestedAmount',
            'totalGivenAmount'
        ));
    }

    public function updateStatus(Request $request, Payout $payout)
    {
        $request->validate([
            'status' => 'required|in:requested,processing,processed,rejected',
            'payment_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'bank_invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $payout->status = $request->status;

        if ($request->status === 'processed') {
            $payout->processed_at = now();
        }

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payouts/proofs', 'public');
            $payout->payment_proof = $path;
        }

        if ($request->hasFile('bank_invoice')) {
            $path = $request->file('bank_invoice')->store('payouts/invoices', 'public');
            $payout->bank_invoice = $path;
        }

        $payout->save();

        return back()->with('success', 'Payout status updated successfully.');
    }

    public function showInvoice(Payout $payout)
    {
        $payout->load('property.owner');
        $bookings = HotelBooking::where('payout_id', $payout->id)->get();

        return view('admin.payouts.invoice', compact('payout', 'bookings'));
    }
}
