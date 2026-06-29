<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Confirmed — {{ $booking->property->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f2f6fa;
            color: #1a1a1a;
            margin: 0;
            min-height: 100vh;
        }

        .conf-topbar {
            height: 4px;
            background: #d00e15;
        }
        .conf-header {
            background: white;
            border-bottom: 1px solid #e4e8ed;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .conf-logo {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 20px;
            color: #d00e15;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── Hero confirmation banner ── */
        .conf-hero {
            background: linear-gradient(160deg, #19100F 0%, #2c1a18 60%, #19100F 100%);
            color: white;
            padding: 48px 24px 64px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .conf-hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #d00e15;
        }
        .conf-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Ccircle cx='30' cy='30' r='1.5' fill='rgba(255,255,255,0.08)'/%3E%3C/svg%3E");
        }
        .conf-check-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 3px solid rgba(255,255,255,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pop-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        .conf-check-circle i { font-size: 40px; color: white; }
        @keyframes pop-in {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .conf-title {
            font-family: 'Outfit', sans-serif;
            font-size: 34px;
            font-weight: 800;
            margin: 0 0 8px;
            animation: fade-up 0.4s 0.2s ease both;
        }
        .conf-subtitle {
            font-size: 16px;
            opacity: 0.88;
            margin: 0;
            animation: fade-up 0.4s 0.3s ease both;
        }
        @keyframes fade-up {
            0% { transform: translateY(12px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        /* ── Ref pill ── */
        .ref-pill {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: white;
            border-radius: 50px;
            padding: 10px 24px;
            margin-top: 24px;
            animation: fade-up 0.4s 0.4s ease both;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .ref-label { font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .ref-value { font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 800; color: #d00e15; letter-spacing: 2px; }

        /* ── Main content ── */
        .page-wrap {
            max-width: 760px;
            margin: -28px auto 0;
            padding: 0 16px 48px;
        }

        /* ── Cards ── */
        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e4e8ed;
            overflow: hidden;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            animation: fade-up 0.4s 0.5s ease both;
        }
        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-bottom: 1px solid #e4e8ed;
            background: #fafbfc;
        }
        .card-header-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: #FFF5F5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d00e15;
            font-size: 15px;
            flex-shrink: 0;
        }
        .card-header h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 700;
            margin: 0;
            color: #1a1a1a;
        }
        .card-body { padding: 20px; }

        /* ── Detail rows ── */
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #6b7280; flex-shrink: 0; }
        .detail-value { font-weight: 600; color: #1a1a1a; text-align: right; }

        /* ── Stay summary grid ── */
        .stay-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .stay-block {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 14px 16px;
        }
        .stay-block-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #9ca3af; font-weight: 600; margin-bottom: 4px; }
        .stay-block-val { font-size: 20px; font-weight: 700; font-family: 'Outfit', sans-serif; color: #1a1a1a; }
        .stay-block-sub { font-size: 12px; color: #6b7280; margin-top: 2px; }

        /* ── Status badge ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending { background: #fff3e0; color: #e65100; }
        .status-confirmed { background: #f0fff4; color: #276749; }

        /* ── Total row ── */
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            background: #d00e15;
            color: white;
        }
        .total-label { font-size: 14px; opacity: 0.85; }
        .total-amount { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 800; }

        /* ── Whats next ── */
        .next-step {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .next-step:last-child { border-bottom: none; }
        .next-step-num {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #d00e15;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }
        .next-step-title { font-weight: 600; font-size: 14px; margin-bottom: 3px; }
        .next-step-desc { font-size: 13px; color: #6b7280; }

        /* ── Action buttons ── */
        .actions { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 24px;
            background: #A90B16;
            color: white;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            transition: background 0.2s;
        }
        .btn-primary:hover { background: #8a0a11; color: white; }
        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 24px;
            background: white;
            color: #1a1a1a;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid #d1d5db;
            font-family: 'Outfit', sans-serif;
            transition: all 0.2s;
        }
        .btn-outline:hover { border-color: #d00e15; color: #d00e15; }

        /* ── Important notice ── */
        .notice-box {
            background: #fff8e1;
            border: 1px solid #f5c842;
            border-radius: 8px;
            padding: 14px 16px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: #6b4e00;
        }
        .notice-box i { color: #f5a623; flex-shrink: 0; margin-top: 1px; }

        @media (max-width: 600px) {
            .conf-title { font-size: 26px; }
            .stay-grid { grid-template-columns: 1fr; }
            .actions { flex-direction: column; }
            .btn-primary, .btn-outline { justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="conf-topbar"></div>

    <header class="conf-header">
        <a href="/" class="conf-logo">
            <i class="fas fa-plane-departure"></i>
            GhuriTravel
        </a>
        <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:#6b7280;">
            <i class="fas fa-lock" style="color:#008009;"></i>
            Booking secured
        </div>
    </header>

    {{-- Hero Banner --}}
    <div class="conf-hero">
        <div class="conf-check-circle">
            <i class="fas fa-check"></i>
        </div>
        <h1 class="conf-title">Your booking is confirmed!</h1>
        <p class="conf-subtitle">We've received your reservation and {{ $booking->property->name }} is expecting you.</p>
        <div class="ref-pill">
            <div>
                <div class="ref-label">Booking Reference</div>
                <div class="ref-value">{{ $booking->booking_ref }}</div>
            </div>
            <div style="width:1px;height:40px;background:#e4e8ed;"></div>
            <div>
                <div class="ref-label">Status</div>
                <div style="display:flex;align-items:center;gap:6px;margin-top:2px;">
                    <span style="width:8px;height:8px;border-radius:50%;background:#e65100;"></span>
                    <span style="font-size:13px;font-weight:700;color:#e65100;">{{ ucfirst($booking->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="page-wrap">

        {{-- ── Stay Details ── --}}
        <div class="card" style="animation-delay:0.5s;">
            <div class="card-header">
                <div class="card-header-icon"><i class="fas fa-bed"></i></div>
                <div>
                    <h3>{{ $booking->property->name }}</h3>
                    <div style="font-size:12px;color:#6b7280;margin-top:1px;">
                        <i class="fas fa-map-marker-alt" style="color:#c62828;"></i>
                        {{ $booking->property->city }}{{ $booking->property->country ? ', ' . $booking->property->country : '' }}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="stay-grid" style="margin-bottom:16px;">
                    <div class="stay-block">
                        <div class="stay-block-label">Check-in</div>
                        <div class="stay-block-val">{{ $booking->check_in->format('d M Y') }}</div>
                        <div class="stay-block-sub">from {{ $booking->property->check_in_time ?? '14:00' }}</div>
                    </div>
                    <div class="stay-block">
                        <div class="stay-block-label">Check-out</div>
                        <div class="stay-block-val">{{ $booking->check_out->format('d M Y') }}</div>
                        <div class="stay-block-sub">until {{ $booking->property->check_out_time ?? '12:00' }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Room</span>
                    <span class="detail-value">{{ $booking->roomType->name }}</span>
                </div>
                @if($booking->ratePlan)
                <div class="detail-row">
                    <span class="detail-label">Rate plan</span>
                    <span class="detail-value">{{ $booking->ratePlan->plan_display_name }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Duration</span>
                    <span class="detail-value">{{ $booking->nights }} night{{ $booking->nights > 1 ? 's' : '' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Guests</span>
                    <span class="detail-value">
                        {{ $booking->adults }} adult{{ $booking->adults > 1 ? 's' : '' }}
                        {{ $booking->children > 0 ? ', ' . $booking->children . ' children' : '' }}
                    </span>
                </div>
                @if($booking->estimated_arrival)
                <div class="detail-row">
                    <span class="detail-label">Estimated arrival</span>
                    <span class="detail-value">{{ $booking->estimated_arrival }}</span>
                </div>
                @endif
                @if($booking->special_requests)
                <div class="detail-row">
                    <span class="detail-label">Special requests</span>
                    <span class="detail-value" style="max-width:300px;">{{ $booking->special_requests }}</span>
                </div>
                @endif
            </div>

            {{-- Total Price Row --}}
            <div class="total-row">
                <div>
                    <div class="total-label">Total amount</div>
                    <div style="font-size:12px;opacity:0.7;">Includes taxes &amp; fees — pay at hotel</div>
                </div>
                <div class="total-amount">${{ number_format($booking->total, 2) }}</div>
            </div>
        </div>

        {{-- ── Important Notice ── --}}
        <div class="notice-box" style="margin-bottom:16px; {{ $booking->payment_status === 'paid' ? 'background:#f0fff4;border-color:#48bb78;color:#276749;' : '' }}">
            <i class="fas {{ $booking->payment_status === 'paid' ? 'fa-check-circle' : 'fa-exclamation-triangle' }}" style="{{ $booking->payment_status === 'paid' ? 'color:#48bb78;' : '' }}"></i>
            <div>
                @if($booking->payment_status === 'paid')
                    <strong>Payment successful:</strong> You have securely paid ${{ number_format($booking->total, 2) }} online. Your room is fully guaranteed.
                @else
                    <strong>Payment at hotel:</strong> You will pay ${{ number_format($booking->total, 2) }} directly at the property during check-in. No charges have been made today.
                @endif
            </div>
        </div>

        {{-- ── What Happens Next ── --}}
        <div class="card" style="animation-delay:0.65s;">
            <div class="card-header">
                <div class="card-header-icon"><i class="fas fa-list-check"></i></div>
                <h3>What happens next?</h3>
            </div>
            <div class="card-body">
                <div class="next-step">
                    <div class="next-step-num">1</div>
                    <div>
                        <div class="next-step-title">Check your email</div>
                        <div class="next-step-desc">A confirmation email has been sent to your registered email address with your booking details.</div>
                    </div>
                </div>
                <div class="next-step">
                    <div class="next-step-num">2</div>
                    <div>
                        <div class="next-step-title">Hotel will contact you</div>
                        <div class="next-step-desc">{{ $booking->property->name }} will confirm your booking directly. They may reach out for any special arrangements.</div>
                    </div>
                </div>
                <div class="next-step">
                    <div class="next-step-num">3</div>
                    <div>
                        <div class="next-step-title">Arrive on {{ $booking->check_in->format('D, M d, Y') }}</div>
                        <div class="next-step-desc">Show your booking reference <strong>{{ $booking->booking_ref }}</strong> at reception. Check-in is from {{ $booking->property->check_in_time ?? '14:00' }}.</div>
                    </div>
                </div>
                <div class="next-step">
                    <div class="next-step-num">4</div>
                    <div>
                        @if($booking->payment_status === 'paid')
                            <div class="next-step-title">All paid up!</div>
                            <div class="next-step-desc">Your payment of ${{ number_format($booking->total, 2) }} has been securely received. Just bring your ID for check-in.</div>
                        @else
                            <div class="next-step-title">Pay at hotel</div>
                            <div class="next-step-desc">Payment of ${{ number_format($booking->total, 2) }} will be collected at check-in. Please have your ID and payment method ready.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Price Breakdown ── --}}
        <div class="card" style="animation-delay:0.7s;">
            <div class="card-header">
                <div class="card-header-icon"><i class="fas fa-receipt"></i></div>
                <h3>Price breakdown</h3>
            </div>
            <div class="card-body">
                <div class="detail-row">
                    <span class="detail-label">Room rate × {{ $booking->nights }} nights</span>
                    <span class="detail-value">${{ number_format($booking->subtotal, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tax (10%)</span>
                    <span class="detail-value">${{ number_format($booking->taxes, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Service fee</span>
                    <span class="detail-value">${{ number_format($booking->fees, 2) }}</span>
                </div>
                @if($booking->discount_amount > 0)
                <div class="detail-row">
                    <span class="detail-label" style="color:#008009;">Discount applied</span>
                    <span class="detail-value" style="color:#008009;">-${{ number_format($booking->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="detail-row" style="font-size:16px;padding-top:14px;">
                    <span style="font-weight:700;color:#1a1a1a;">Total</span>
                    <span style="font-weight:800;color:#1a1a1a;font-family:'Outfit',sans-serif;font-size:20px;">${{ number_format($booking->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="actions" style="margin-top:8px;">
            <a href="{{ route('my-bookings.show', $booking) }}" class="btn-primary">
                <i class="fas fa-calendar-check"></i>
                Manage my booking
            </a>
            <a href="{{ route('dashboard') }}" class="btn-outline">
                <i class="fas fa-list"></i>
                All my bookings
            </a>
            <a href="{{ route('hotels.search') }}" class="btn-outline">
                <i class="fas fa-search"></i>
                Explore more hotels
            </a>
        </div>

        <p style="text-align:center;color:#9ca3af;font-size:12px;margin-top:28px;">
            Booking reference: <strong style="color:#d00e15;">{{ $booking->booking_ref }}</strong> &bull;
            Questions? Contact our <a href="#" style="color:#d00e15;">support team</a>
        </p>
    </div>

</body>
</html>

