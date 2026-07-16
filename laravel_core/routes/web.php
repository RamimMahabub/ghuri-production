<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $offers = \App\Models\Promotion::where('is_active', true)
        ->where(function ($query) {
            $query->whereNull('valid_to')
                  ->orWhere('valid_to', '>=', now());
        })
        ->latest()
        ->take(4)
        ->get();

    $recentSearches = collect(session()->get('recent_searches', []))->map(function ($search) {
        // Try to find a property that matches the destination to get an image
        $property = \App\Models\Property::approved()
            ->where('name', 'like', "%{$search['destination']}%")
            ->orWhere('city', 'like', "%{$search['destination']}%")
            ->with('photos')
            ->first();

        $search['property_name'] = $property ? $property->name : $search['destination'];
        $search['image_url'] = $property 
            ? $property->cover_photo_url 
            : 'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=400&q=80'; // Default image
        
        return $search;
    });

    $topProperties = \App\Models\Property::approved()
        ->with(['activeRoomTypes', 'photos'])
        ->withCount(['reviews as reviews_count' => function ($query) {
            $query->where('status', 'published');
        }])
        ->withAvg(['reviews as average_rating' => function ($query) {
            $query->where('status', 'published');
        }], 'overall_score')
        ->orderByDesc('average_rating')
        ->take(4)
        ->get();

    $promotedProperties = \App\Models\Property::approved()
        ->whereHas('promotions', function($query) {
            $query->where('is_active', true)
                  ->where(function ($q) {
                      $q->whereNull('valid_to')
                        ->orWhere('valid_to', '>=', now());
                  });
        })
        ->with(['activeRoomTypes', 'photos', 'promotions' => function($query) {
            $query->where('is_active', true)
                  ->where(function ($q) {
                      $q->whereNull('valid_to')
                        ->orWhere('valid_to', '>=', now());
                  });
        }])
        ->withCount(['reviews as reviews_count' => function ($query) {
            $query->where('status', 'published');
        }])
        ->withAvg(['reviews as average_rating' => function ($query) {
            $query->where('status', 'published');
        }], 'overall_score')
        ->latest()
        ->take(4)
        ->get();

    // Stays for every travel style (Dynamic Grouping)
    $travelStyles = [
        'Beach' => \App\Models\Property::approved()
            ->where(function($q) {
                $q->where('city', 'like', "%Cox's Bazar%")
                  ->orWhere('city', 'like', '%Kuakata%');
            })->with('photos')->take(4)->get(),
            
        'Nature' => \App\Models\Property::approved()
            ->where(function($q) {
                $q->where('city', 'like', '%Sylhet%')
                  ->orWhere('city', 'like', '%Bandarban%')
                  ->orWhere('city', 'like', '%Sreemangal%');
            })->with('photos')->take(4)->get(),
            
        'Heritage' => \App\Models\Property::approved()
            ->where(function($q) {
                $q->where('city', 'like', '%Rajshahi%')
                  ->orWhere('city', 'like', '%Bagerhat%')
                  ->orWhere('city', 'like', '%Bogura%');
            })->with('photos')->take(4)->get(),
            
        'City Vibes' => \App\Models\Property::approved()
            ->where(function($q) {
                $q->where('city', 'like', '%Dhaka%')
                  ->orWhere('city', 'like', '%Chittagong%');
            })->with('photos')->take(4)->get(),
    ];

    // Filter out empty categories to only show tabs that have properties
    $travelStyles = array_filter($travelStyles, function($properties) {
        return $properties->count() > 0;
    });

    return view('welcome', compact('offers', 'recentSearches', 'topProperties', 'promotedProperties', 'travelStyles'));
})->name('home');

Route::get('/list-your-property', function () {
    return view('list-your-property');
})->name('list-your-property');

Route::get('/sitemap.xml', function () {
    $staticUrls = [
        ['url' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
        ['url' => route('hotels.search'), 'changefreq' => 'daily', 'priority' => '0.9'],
        ['url' => route('list-your-property'), 'changefreq' => 'monthly', 'priority' => '0.7'],
    ];

    $properties = \App\Models\Property::approved()
        ->select(['id', 'updated_at'])
        ->latest('updated_at')
        ->get();

    return response()
        ->view('sitemap', compact('staticUrls', 'properties'))
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

Route::get('/dashboard', function () {
    $user = auth()->user();
    $chartLabels = [];
    $spendingData = [];
    $bookingsData = [];

    // Last 6 months data for customer
    for ($i = 5; $i >= 0; $i--) {
        $month = \Carbon\Carbon::now()->subMonths($i);
        $chartLabels[] = $month->format('M Y');
        
        $spend = \App\Models\HotelBooking::where('guest_id', $user->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->sum('total');
            
        $count = \App\Models\HotelBooking::where('guest_id', $user->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->count();
            
        $spendingData[] = round((float) $spend, 2);
        $bookingsData[] = $count;
    }

    return view('dashboard', compact('chartLabels', 'spendingData', 'bookingsData'));
})->middleware(['auth', 'role:customer'])->name('dashboard');

Route::get('/admin', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->middleware([
        'auth',

        'role:admin,manager,support_agent,ticketing_officer,accounts_officer',
    ])
    ->name('admin.dashboard');

Route::get('/support-center', function (\Illuminate\Http\Request $request) {
    $user = $request->user();

    if ($user->isPropertyOwner()) {
        return redirect()->route('property-owner.support.index');
    }

    if ($user->isCustomer()) {
        return redirect()->route('support.index');
    }

    if ($user->isInternalUser()) {
        return redirect()->route('admin.support.index');
    }

    return redirect()->route('pages.help');
})->middleware('auth')->name('navbar.support');


Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/support', [\App\Http\Controllers\SupportTicketController::class, 'index'])->name('support.index');
    Route::get('/support/create', [\App\Http\Controllers\SupportTicketController::class, 'create'])->name('support.create');
    Route::post('/support', [\App\Http\Controllers\SupportTicketController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [\App\Http\Controllers\SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [\App\Http\Controllers\SupportTicketController::class, 'reply'])->name('support.reply');
    Route::post('/support/{ticket}/rate', [\App\Http\Controllers\SupportTicketController::class, 'rate'])->name('support.rate');

    Route::get('/booking/checkout/{flightId}', [\App\Http\Controllers\BookingController::class, 'checkout'])->name('booking.checkout');
    Route::post('/booking/store', [\App\Http\Controllers\BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{id}', [\App\Http\Controllers\BookingController::class, 'show'])->name('booking.show');
});

Route::get('/flights/search', function () {
    return view('flights.coming-soon');
})->name('flights.search');
Route::post('/currency', [\App\Http\Controllers\CurrencyController::class, 'setCurrency'])->name('currency.set');
Route::get('/ajax/airports/search', [\App\Http\Controllers\AirportController::class, 'search'])->name('airports.search');

Route::get('/integration-tester', function () {
    return view('integration-tester');
});

Route::get('/ajax/integration-test-execute', function (\App\Services\FlightServiceInterface $flightService) {
    try {
        $logs = [];

        // 1. Search
        $flights = $flightService->search('DEL', 'DXB', '2026-06-05', 1, 'OneWay');
        if(empty($flights)) {
            $logs[] = ['step' => 'Search', 'message' => 'No flights found. Check credentials, dates, or IP.', 'type' => 'error'];
            return response()->json(['logs' => $logs]);
        }
        $logs[] = [
            'step' => 'Search', 
            'message' => 'Successfully found ' . count($flights) . ' flights.', 
            'type' => 'success', 
            'data' => array_slice($flights, 0, 3) // show top 3
        ];

        // 2. Revalidate
        $flightId = $flights[0]['id'];
        $priceData = $flightService->price($flightId);

        if($priceData['status'] !== 'available') {
            $logs[] = ['step' => 'Validate Fare', 'message' => 'Fare validation rejected or expired.', 'type' => 'error', 'data' => $priceData];
            return response()->json(['logs' => $logs]);
        }
        $logs[] = [
            'step' => 'Validate Fare', 
            'message' => 'Fare validated successfully.', 
            'type' => 'success', 
            'data' => $priceData
        ];

        // 3. Book
        $passengers = [[
            'first_name' => 'Paul',
            'last_name' => 'Richard',
            'dob' => '1990-01-01',
            'title' => 'Mr',
            'email' => 'test@bookdei.com',
            'phone' => '1234567890',
            'nationality' => 'IN'
        ]];

        try {
            $bookingData = $flightService->book($flightId, $passengers);
            $logs[] = ['step' => 'Book Flight', 'message' => 'Booking API Hit Successfully.', 'type' => 'success', 'data' => $bookingData];
        } catch (\Exception $e) {
            $logs[] = ['step' => 'Book Flight', 'message' => 'Booking Provider Rejection (Expected in Sandbox)', 'type' => 'error', 'data' => ['error' => $e->getMessage()]];
        }

        return response()->json(['logs' => $logs]);
    } catch (\Exception $e) {
        return response()->json(['logs' => [['step' => 'System', 'message' => $e->getMessage(), 'type' => 'error']]]);
    }
});

// Google OAuth Routes
Route::get('/auth/google/{role}/redirect', [\App\Http\Controllers\Auth\GoogleLoginController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleLoginController::class, 'callback'])->name('google.callback');

require __DIR__.'/auth.php';

/* ================================================================
   VERCEL SETUP PANEL — Protected by SETUP_TOKEN env variable
   Access: yourdomain.vercel.app/setup?token=YOUR_SECRET_TOKEN
   ================================================================ */

Route::get('/setup', function (\Illuminate\Http\Request $request) {
    $token = config('app.setup_token', env('SETUP_TOKEN', ''));

    // Token check disabled for easy access

    $action = $request->query('action');
    $logs   = [];
    $error  = null;

    // ── Execute requested action ──────────────────────────────────
    if ($action) {
        try {
            set_time_limit(120);

            switch ($action) {

                case 'fresh':
                    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
                    $logs[] = '✅ migrate:fresh — all tables dropped & recreated.';
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DemoSeeder', '--force' => true]);
                    $logs[] = '✅ DemoSeeder — all demo accounts seeded.';
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\AirportSeeder', '--force' => true]);
                    $logs[] = '✅ AirportSeeder — airports seeded.';
                    break;

                case 'migrate':
                    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                    $logs[] = '✅ migrate — pending migrations run.';
                    break;

                case 'clear-cache':
                    \Illuminate\Support\Facades\Artisan::call('config:clear');
                    \Illuminate\Support\Facades\Artisan::call('route:clear');
                    \Illuminate\Support\Facades\Artisan::call('view:clear');
                    $logs[] = '✅ Cache cleared successfully (Config, Route, View).';
                    break;

                case 'wipe-payouts':
                    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    \App\Models\Payout::truncate();
                    \App\Models\HotelBooking::whereNotNull('id')->delete(); // clears all test bookings
                    return response()->json(['message' => '✅ Wiped all Payouts and HotelBookings successfully for a fresh start.']);
                
                case 'debug-payouts':
                    $props = \App\Models\Property::all();
                    $output = [];
                    foreach($props as $p) {
                        $unlinked = \App\Models\HotelBooking::where('property_id', $p->id)
                            ->where('status', 'checked_out')
                            ->whereNull('payout_id')
                            ->get()->sum(function($b) { return $b->total - $b->commission_amount; });
                        $manual = \App\Models\Payout::where('property_id', $p->id)
                            ->where('status', '!=', 'rejected')
                            ->whereNull('period_start')->sum('amount');
                        $output[] = [
                            'owner_id' => $p->owner_id,
                            'property_id' => $p->id,
                            'unlinkedEarned' => $unlinked,
                            'manualPayouts' => $manual,
                            'withdrawable' => $unlinked - $manual
                        ];
                    }
                    return response()->json($output);

                case 'debug-room-prices':
                    $rooms = \App\Models\RoomType::where('property_id', 47)->get(['id', 'name', 'base_price_per_night', 'status']);
                    return response()->json($rooms);

                case 'backfill-commission':
                    $updated = 0;
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
                        $updated++;
                    }
                    return response()->json(['message' => "Successfully backfilled commission for {$updated} bookings!"]);

                case 'seed-demo':
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DemoSeeder', '--force' => true]);
                    $logs[] = '✅ DemoSeeder — demo accounts seeded.';
                    break;

                case 'seed-airports':
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\AirportSeeder', '--force' => true]);
                    $logs[] = '✅ AirportSeeder — airports seeded.';
                    break;

                case 'seed-all':
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DemoSeeder', '--force' => true]);
                    $logs[] = '✅ DemoSeeder — demo accounts seeded.';
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\AirportSeeder', '--force' => true]);
                    $logs[] = '✅ AirportSeeder — airports seeded.';
                    break;

                default:
                    $error = 'Unknown action: ' . htmlspecialchars($action);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
    }

    // ── Demo accounts table ───────────────────────────────────────
    $accounts = [
        ['Admin',             'admin@bookdei.com',      'Admin@1234',     '/admin'],
        ['Manager',           'manager@bookdei.com',    'Manager@1234',   '/admin'],
        ['Support Agent',     'support@bookdei.com',    'Support@1234',   '/admin'],
        ['Ticketing Officer', 'ticketing@bookdei.com',  'Ticket@1234',    '/admin'],
        ['Accounts Officer',  'accounts@bookdei.com',   'Accounts@1234',  '/admin'],
        ['Property Owner',    'owner@bookdei.com',      'Owner@1234',     '/property-owner/dashboard'],
        ['Customer',          'customer@bookdei.com',   'Customer@1234',  '/'],
    ];

    $t = $request->query('token');

    return response()->make(view('setup-panel', compact('logs','error','accounts','t'))->render());

})->name('setup');


/* ================================================================
   HOTEL BOOKING MODULE — PROPERTY OWNER PMS ROUTES
   ================================================================ */
Route::middleware(['auth', 'role:property_owner'])->prefix('property-owner')->name('property-owner.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\PropertyOwner\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/support', [\App\Http\Controllers\SupportTicketController::class, 'index'])->name('support.index');
    Route::get('/support/create', [\App\Http\Controllers\SupportTicketController::class, 'create'])->name('support.create');
    Route::post('/support', [\App\Http\Controllers\SupportTicketController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [\App\Http\Controllers\SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [\App\Http\Controllers\SupportTicketController::class, 'reply'])->name('support.reply');
    Route::post('/support/{ticket}/rate', [\App\Http\Controllers\SupportTicketController::class, 'rate'])->name('support.rate');

    // Properties (Hotels)
    Route::resource('hotels', \App\Http\Controllers\PropertyOwner\HotelController::class);
    Route::post('hotels/{hotel}/submit-approval', [\App\Http\Controllers\PropertyOwner\HotelController::class, 'submitForApproval'])->name('hotels.submit-approval');
    Route::delete('hotels/{hotel}/photos/{photo}', [\App\Http\Controllers\PropertyOwner\HotelController::class, 'destroyPhoto'])->name('hotels.photos.destroy');
    Route::post('hotels/{hotel}/photos/reorder', [\App\Http\Controllers\PropertyOwner\HotelController::class, 'reorderPhotos'])->name('hotels.photos.reorder');
    Route::post('hotels/{hotel}/photos/{photo}/set-cover', [\App\Http\Controllers\PropertyOwner\HotelController::class, 'setCoverPhoto'])->name('hotels.photos.set-cover');

    // Room Types (nested under hotels)
    Route::resource('hotels.rooms', \App\Http\Controllers\PropertyOwner\RoomController::class);
    Route::patch('hotels/{hotel}/rooms/{room}/toggle-status', [\App\Http\Controllers\PropertyOwner\RoomController::class, 'toggleStatus'])->name('hotels.rooms.toggle-status');
    Route::delete('hotels/{hotel}/rooms/{room}/photos/{photo}', [\App\Http\Controllers\PropertyOwner\RoomController::class, 'destroyPhoto'])->name('hotels.rooms.photos.destroy');
    Route::post('hotels/{hotel}/rooms/{room}/duplicate', [\App\Http\Controllers\PropertyOwner\RoomController::class, 'duplicate'])->name('hotels.rooms.duplicate');

    // Rate Rules (nested under hotels)
    Route::resource('hotels.rate-rules', \App\Http\Controllers\PropertyOwner\RateRuleController::class);

    // Availability & Calendar
    Route::get('hotels/{hotel}/availability', [\App\Http\Controllers\PropertyOwner\AvailabilityController::class, 'index'])->name('availability.index');
    Route::post('hotels/{hotel}/availability/bulk-update', [\App\Http\Controllers\PropertyOwner\AvailabilityController::class, 'bulkUpdate'])->name('availability.bulk-update');

    // Bookings
    Route::resource('bookings', \App\Http\Controllers\PropertyOwner\BookingController::class)->only(['index', 'show', 'create', 'store']);
    Route::post('bookings/{booking}/confirm', [\App\Http\Controllers\PropertyOwner\BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('bookings/{booking}/cancel', [\App\Http\Controllers\PropertyOwner\BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('bookings/{booking}/check-in', [\App\Http\Controllers\PropertyOwner\BookingController::class, 'checkIn'])->name('bookings.check-in');
    Route::post('bookings/{booking}/check-out', [\App\Http\Controllers\PropertyOwner\BookingController::class, 'checkOut'])->name('bookings.check-out');
    Route::post('bookings/{booking}/no-show', [\App\Http\Controllers\PropertyOwner\BookingController::class, 'noShow'])->name('bookings.no-show');

    // Guests
    Route::resource('guests', \App\Http\Controllers\PropertyOwner\GuestController::class)->only(['index', 'show']);

    // Promotions
    Route::resource('promotions', \App\Http\Controllers\PropertyOwner\PromotionController::class);

    // Reviews
    Route::get('reviews', [\App\Http\Controllers\PropertyOwner\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('reviews/{review}/respond', [\App\Http\Controllers\PropertyOwner\ReviewController::class, 'respond'])->name('reviews.respond');

    // Settings
    Route::get('settings', [\App\Http\Controllers\PropertyOwner\SettingsController::class, 'index'])->name('settings');
    Route::put('settings', [\App\Http\Controllers\PropertyOwner\SettingsController::class, 'update'])->name('settings.update');

    // Payouts
    Route::get('payouts', [\App\Http\Controllers\PropertyOwner\PayoutController::class, 'index'])->name('payouts.index');
    Route::post('payouts/payment-method', [\App\Http\Controllers\PropertyOwner\PayoutController::class, 'updatePaymentMethod'])->name('payouts.payment-method');
    Route::post('payouts/request', [\App\Http\Controllers\PropertyOwner\PayoutController::class, 'requestWithdrawal'])->name('payouts.request');
    Route::get('payouts/{payout}/invoice', [\App\Http\Controllers\PropertyOwner\PayoutController::class, 'showInvoice'])->name('payouts.invoice');
});

/* ================================================================
   HOTEL BOOKING MODULE — GUEST-FACING ROUTES
   ================================================================ */

// Public hotel browsing
Route::get('/hotels/search', [\App\Http\Controllers\HotelSearchController::class, 'index'])->name('hotels.search');
Route::get('/hotels/{property}', [\App\Http\Controllers\HotelController::class, 'show'])->name('hotels.show');

// Authenticated guest booking
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/hotels/book/{property}/{roomType}', [\App\Http\Controllers\HotelBookingController::class, 'step1'])->name('hotels.book.step1');
    Route::post('/hotels/book/step2', [\App\Http\Controllers\HotelBookingController::class, 'step2'])->name('hotels.book.step2');
    Route::post('/hotels/book/step3', [\App\Http\Controllers\HotelBookingController::class, 'step3'])->name('hotels.book.step3');
    Route::post('/hotels/book/confirm', [\App\Http\Controllers\HotelBookingController::class, 'confirm'])->name('hotels.book.confirm');
    Route::post('/hotels/apply-coupon', [\App\Http\Controllers\HotelBookingController::class, 'applyCoupon'])->name('hotels.apply-coupon');
    Route::get('/hotels/booking/{booking}/confirmation', [\App\Http\Controllers\HotelBookingController::class, 'confirmation'])->name('hotels.book.confirmation');

    Route::get('/my-bookings', [\App\Http\Controllers\MyBookingsController::class, 'index'])->name('my-bookings.index');
    Route::get('/my-bookings/{booking}', [\App\Http\Controllers\MyBookingsController::class, 'show'])->name('my-bookings.show');
    Route::post('/my-bookings/{booking}/cancel', [\App\Http\Controllers\MyBookingsController::class, 'cancel'])->name('my-bookings.cancel');
    Route::post('/my-bookings/{booking}/review', [\App\Http\Controllers\MyBookingsController::class, 'review'])->name('my-bookings.review');
    Route::get('/my-bookings/{booking}/voucher', [\App\Http\Controllers\MyBookingsController::class, 'voucher'])->name('my-bookings.voucher');
});

// SSLCommerz Callback (CSRF Exempted)
Route::post('/payment/sslcommerz/callback', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.sslcommerz.callback');

/* ================================================================
   STATIC PAGES (FOOTER LINKS)
   ================================================================ */
$staticPages = [
    'about' => 'About Us',
    'careers' => 'Careers',
    'press' => 'Press Center',
    'sustainability' => 'Sustainability',
    'investors' => 'Investor Relations',
    'contact' => 'Contact Us',
    'help' => 'Help Center & FAQ',
    'cancellation' => 'Cancellation Policy',
    'trust-safety' => 'Trust & Safety',
    'complaint' => 'Submit a Complaint',
    'affiliates' => 'Affiliate Network',
    'travel-agencies' => 'Travel Agencies',
    'corporate' => 'Corporate Travel',
    'partner-portal' => 'Partner Portal',
    'privacy' => 'Privacy Policy',
    'terms' => 'Terms of Service',
    'cookies' => 'Cookie Settings',
    'sitemap' => 'Sitemap'
];

foreach ($staticPages as $slug => $title) {
    Route::get("/page/{$slug}", function () use ($title) {
        return view('pages.show', ['title' => $title]);
    })->name("pages.{$slug}");
}

/* ================================================================
   HOTEL BOOKING MODULE — ADMIN ROUTES
   ================================================================ */
Route::middleware(['auth', 'role:admin,manager'])->prefix('admin')->name('admin.')->group(function () {
    // Property Approval
    Route::get('/properties', [\App\Http\Controllers\Admin\PropertyApprovalController::class, 'index'])->name('properties.index');
    Route::get('/properties/{property}/review', [\App\Http\Controllers\Admin\PropertyApprovalController::class, 'review'])->name('properties.review');
    Route::post('/properties/{property}/approve', [\App\Http\Controllers\Admin\PropertyApprovalController::class, 'approve'])->name('properties.approve');
    Route::post('/properties/{property}/reject', [\App\Http\Controllers\Admin\PropertyApprovalController::class, 'reject'])->name('properties.reject');
    Route::post('/properties/{property}/request-changes', [\App\Http\Controllers\Admin\PropertyApprovalController::class, 'requestChanges'])->name('properties.request-changes');

    // Commissions & Payouts
    Route::get('/commissions', [\App\Http\Controllers\Admin\CommissionController::class, 'index'])->name('commissions.index');
    Route::post('/commissions', [\App\Http\Controllers\Admin\CommissionController::class, 'updateGlobal'])->name('commissions.update-global');
    Route::post('/commissions/property/{property}', [\App\Http\Controllers\Admin\CommissionController::class, 'updateProperty'])->name('commissions.update-property');
    
    Route::get('/payouts', [\App\Http\Controllers\Admin\PayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts/{payout}/status', [\App\Http\Controllers\Admin\PayoutController::class, 'updateStatus'])->name('payouts.update-status');
    Route::get('/payouts/{payout}/invoice', [\App\Http\Controllers\Admin\PayoutController::class, 'showInvoice'])->name('payouts.invoice');

    // Global Bookings
    Route::get('/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show');

    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['create', 'store', 'show']);

    // Promotions
    Route::resource('promotions', \App\Http\Controllers\Admin\PromotionController::class);

    // Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');
});

Route::middleware(['auth', 'role:admin,manager,support_agent,ticketing_officer,accounts_officer'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/support', [\App\Http\Controllers\Admin\SupportController::class, 'index'])->name('support.index');
    Route::get('/support/{ticket}', [\App\Http\Controllers\Admin\SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [\App\Http\Controllers\Admin\SupportController::class, 'reply'])->name('support.reply');
    Route::patch('/support/{ticket}', [\App\Http\Controllers\Admin\SupportController::class, 'update'])->name('support.update');
});

Route::get('/support-attachments/{attachment}', \App\Http\Controllers\SupportAttachmentController::class)
    ->middleware('auth')->name('support.attachments.download');
