<?php

namespace App\Http\Controllers;

use App\Models\HotelBooking;
use App\Services\SSLCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(protected SSLCommerzService $sslcommerz) {}

    /**
     * Handle the callback from SSLCommerz.
     * This route should be exempted from CSRF.
     */
    public function callback(Request $request)
    {
        $status = $request->input('status');
        $valId = $request->input('val_id');
        $bookingId = $request->input('value_a'); // We passed booking ID here

        Log::info('SSLCommerz Callback received', $request->all());

        if (!$bookingId) {
            return redirect('/my-bookings')->with('error', 'Invalid payment response missing booking reference.');
        }

        $booking = HotelBooking::findOrFail($bookingId);

        if ($status === 'SUCCESS' || $status === 'VALID') {
            if (!$valId) {
                return redirect()->route('hotels.book.confirmation', $booking)
                    ->with('error', 'Payment validation ID missing. Please contact support.');
            }

            // Validate the transaction with SSLCommerz server
            $validation = $this->sslcommerz->validatePayment($valId);

            if (isset($validation['status']) && ($validation['status'] === 'VALID' || $validation['status'] === 'VALIDATED')) {
                // Ensure amount matches
                if (floatval($validation['amount']) >= floatval($booking->total)) {
                    $booking->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed'
                    ]);
                    
                    $booking->logActivity('payment_received', 'Payment successful via SSLCommerz (Tran ID: ' . $validation['tran_id'] . ')', $booking->guest_id);
                    
                    return redirect()->route('hotels.book.confirmation', $booking)
                        ->with('success', 'Payment successful and booking confirmed!');
                } else {
                    Log::warning('SSLCommerz Validation Amount Mismatch', ['validation' => $validation, 'booking' => $booking->total]);
                    return redirect()->route('hotels.book.confirmation', $booking)
                        ->with('error', 'Payment amount mismatch. Please contact support.');
                }
            } else {
                Log::error('SSLCommerz Validation Failed', ['validation' => $validation]);
                return redirect()->route('hotels.book.confirmation', $booking)
                    ->with('error', 'Payment validation failed.');
            }
        } elseif ($status === 'CANCELLED') {
            $booking->logActivity('payment_cancelled', 'Guest cancelled the payment process.', $booking->guest_id);
            return redirect()->route('hotels.book.confirmation', $booking)
                ->with('error', 'Payment was cancelled. You can retry payment from your bookings page.');
        } elseif ($status === 'FAILED') {
            $booking->logActivity('payment_failed', 'Payment failed.', $booking->guest_id);
            return redirect()->route('hotels.book.confirmation', $booking)
                ->with('error', 'Payment failed. Please try again with a different card or contact your bank.');
        }

        // Fallback for unknown status
        return redirect()->route('hotels.book.confirmation', $booking)
            ->with('error', 'Unknown payment status: ' . $status);
    }
}
