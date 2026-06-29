<?php

namespace App\Services;

use App\Models\HotelBooking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSLCommerzService
{
    protected string $storeId;
    protected string $storePassword;
    protected string $baseUrl;

    public function __construct()
    {
        $this->storeId = config('services.sslcommerz.store_id');
        $this->storePassword = config('services.sslcommerz.store_password');
        
        $isSandbox = config('services.sslcommerz.sandbox');
        $this->baseUrl = $isSandbox 
            ? 'https://sandbox.sslcommerz.com' 
            : 'https://securepay.sslcommerz.com';
    }

    /**
     * Initiate payment session with SSLCommerz.
     * Returns the GatewayPageURL if successful, otherwise throws an Exception.
     */
    public function initiatePayment(HotelBooking $booking, array $guestData): string
    {
        $endpoint = $this->baseUrl . '/gwprocess/v4/api.php';

        $postData = [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => $booking->total,
            'currency' => 'BDT', // Adjust if multi-currency is supported
            'tran_id' => $booking->booking_ref . '_' . time(),
            'success_url' => route('payment.sslcommerz.callback'),
            'fail_url' => route('payment.sslcommerz.callback'),
            'cancel_url' => route('payment.sslcommerz.callback'),
            'emi_option' => 0,
            
            // Customer Information
            'cus_name' => trim(($guestData['first_name'] ?? '') . ' ' . ($guestData['last_name'] ?? 'Guest')),
            'cus_email' => $guestData['email'] ?? 'guest@ghuri.org',
            'cus_phone' => $guestData['phone'] ?? '01700000000',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'cus_add1' => 'Dhaka',
            
            // Product Information
            'product_name' => 'Hotel Booking - ' . $booking->booking_ref,
            'product_category' => 'Hotel',
            'product_profile' => 'general',
            
            // Extra info useful for callback validation
            'value_a' => $booking->id,
        ];

        try {
            $response = Http::asForm()->post($endpoint, $postData);
            
            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['status']) && $result['status'] === 'SUCCESS' && isset($result['GatewayPageURL'])) {
                    return $result['GatewayPageURL'];
                }
                
                Log::error('SSLCommerz Initiate Failed', ['response' => $result]);
                throw new \Exception('Failed to initiate payment: ' . ($result['failedreason'] ?? 'Unknown error'));
            }
            
            throw new \Exception('HTTP Error communicating with payment gateway');
        } catch (\Exception $e) {
            Log::error('SSLCommerz Exception', ['error' => $e->getMessage()]);
            throw new \Exception('Payment Error: ' . $e->getMessage());
        }
    }

    /**
     * Validate the transaction using the Validation API.
     */
    public function validatePayment(string $valId): array
    {
        $endpoint = $this->baseUrl . '/validator/api/validationserverAPI.php';

        $response = Http::get($endpoint, [
            'val_id' => $valId,
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'v' => 1,
            'format' => 'json'
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return ['status' => 'INVALID_HTTP', 'error' => 'Could not connect to validator'];
    }
}
