<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Complete your booking — {{ $property->name }}</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f2f6fa; color: #1a1a1a; margin: 0; }

        /* ── Header ── */
        .checkout-header {
            background: #19100F;
            color: white;
            padding: 0;
        }
        .checkout-header-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 16px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 18px;
        }
        .logo-link i { font-size: 20px; }
        .steps-bar {
            display: flex;
            align-items: center;
            gap: 0;
        }
        .step-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            position: relative;
        }
        .step-item.active { color: white; font-weight: 600; }
        .step-item.done { color: rgba(255,255,255,0.75); }
        .step-num {
            width: 24px; height: 24px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
        }
        .step-item.active .step-num { background: #d00e15; color: white; border-color: #d00e15; }
        .step-item.done .step-num { background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.4); }
        .step-arrow { color: rgba(255,255,255,0.3); font-size: 11px; }

        /* ── Security bar ── */
        .security-bar {
            background: #FFF5F5;
            border-bottom: 1px solid #ffd0d1;
            padding: 8px 16px;
            text-align: center;
            font-size: 12px;
            color: #6B7280;
        }

        /* ── Page Layout ── */
        .page-wrap { max-width: 1100px; margin: 0 auto; padding: 24px 16px; }
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
            align-items: start;
        }

        /* ── Cards ── */
        .card {
            background: white;
            border-radius: 10px;
            border: 1px solid #e4e8ed;
            overflow: hidden;
            margin-bottom: 16px;
        }
        .card-header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid #e4e8ed;
        }
        .card-header h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            color: #1a1a1a;
        }
        .card-header .subtitle { font-size: 13px; color: #6b7280; margin-top: 2px; }
        .card-body { padding: 20px; }

        /* ── Room Summary Card ── */
        .room-summary {
            display: flex;
            gap: 14px;
            align-items: start;
            padding: 16px 20px;
        }
        .room-thumb {
            width: 100px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background: #e4e8ed;
        }
        .room-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .room-info { flex: 1; }
        .room-name {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        .room-meta-row { display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; color: #6b7280; margin-top: 4px; }
        .room-meta-row span { display: flex; align-items: center; gap: 4px; }

        /* ── Date Summary Block ── */
        .date-summary {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            border: 1px solid #e4e8ed;
            border-radius: 8px;
            overflow: hidden;
            margin: 0 20px 16px;
        }
        .date-col {
            padding: 12px 16px;
            border-right: 1px solid #e4e8ed;
        }
        .date-col:last-child { border-right: none; }
        .date-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.6px; color: #6b7280; font-weight: 600; margin-bottom: 3px; }
        .date-value { font-size: 16px; font-weight: 700; font-family: 'Outfit', sans-serif; color: #1a1a1a; }
        .date-sub { font-size: 11px; color: #6b7280; margin-top: 1px; }

        /* ── Inclusions ── */
        .inclusions-list { padding: 0 20px 16px; }
        .inclusion-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            padding: 5px 0;
        }
        .inclusion-item.pos { color: #008009; }
        .inclusion-item.neg { color: #c62828; }
        .inclusion-item.neutral { color: #374151; }

        /* ── Form Styles ── */
        .form-section { margin-bottom: 0; }
        .form-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 14px;
        }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-group.full { grid-column: span 2; }
        label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }
        label span.req { color: #c62828; }
        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            color: #1a1a1a;
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .form-input:focus {
            outline: none;
            border-color: #d00e15;
            box-shadow: 0 0 0 3px rgba(0, 59, 149, 0.1);
        }
        .form-input::placeholder { color: #9ca3af; }
        textarea.form-input { resize: vertical; min-height: 80px; }

        /* ── Special Requests ── */
        .special-req-row { display: flex; flex-direction: column; gap: 6px; margin-top: 14px; }
        .sr-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid #e4e8ed;
            border-radius: 6px;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            font-size: 13px;
        }
        .sr-option:hover { border-color: #d00e15; background: #FFF5F5; }
        .sr-option input[type=checkbox] { accent-color: #d00e15; width: 16px; height: 16px; }

        /* ── Payment Method ── */
        .payment-option {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border: 2px solid #e4e8ed;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 8px;
        }
        .payment-option.selected { border-color: #d00e15; background: #FFF5F5; }
        .payment-option:hover { border-color: #A90B16; }
        .payment-option input[type=radio] { accent-color: #d00e15; width: 18px; height: 18px; }
        .payment-option-info .title { font-weight: 600; font-size: 14px; color: #1a1a1a; }
        .payment-option-info .desc { font-size: 12px; color: #6b7280; margin-top: 2px; }
        .coming-soon-badge {
            background: #f3f4f6;
            color: #9ca3af;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            margin-left: auto;
        }

        /* ── Price Sidebar ── */
        .price-sidebar { position: sticky; top: 72px; }
        .price-card {
            background: white;
            border-radius: 10px;
            border: 1px solid #e4e8ed;
            overflow: hidden;
        }
        .price-card-header {
            background: #d00e15;
            color: white;
            padding: 16px 20px;
        }
        .price-card-hotel { font-weight: 700; font-size: 15px; margin-bottom: 2px; }
        .price-card-room { font-size: 13px; opacity: 0.85; }
        .price-card-dates {
            font-size: 12px;
            opacity: 0.75;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .price-card-body { padding: 18px 20px; }
        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            margin-bottom: 10px;
            color: #374151;
        }
        .price-row.total {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            padding-top: 12px;
            border-top: 2px solid #1a1a1a;
            margin-bottom: 0;
        }
        .price-row.discount { color: #008009; }
        .nightly-breakdown {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 14px;
            font-size: 12px;
            color: #6b7280;
        }
        .nightly-breakdown-row { display: flex; justify-content: space-between; padding: 2px 0; }

        /* ── Cancellation policy badge ── */
        .cancel-policy {
            background: #f0fff4;
            border: 1px solid #b7e8c8;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 12px;
            color: #276749;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-top: 14px;
        }

        /* ── Trust signals ── */
        .trust-box {
            background: white;
            border-radius: 10px;
            border: 1px solid #e4e8ed;
            padding: 16px 20px;
            margin-top: 14px;
        }
        .trust-row { display: flex; align-items: center; gap: 10px; font-size: 12px; color: #374151; padding: 5px 0; }
        .trust-row i { width: 18px; text-align: center; font-size: 14px; }

        /* ── CTA Button ── */
        .btn-confirm {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 16px;
            background: #A90B16;
            color: white;
            font-size: 16px;
            font-weight: 700;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            transition: background 0.2s, transform 0.1s;
            text-decoration: none;
        }
        .btn-confirm:hover { background: #8a0a11; color: white; }
        .btn-confirm:active { transform: scale(0.99); }
        .btn-confirm:disabled { background: #9ca3af; cursor: not-allowed; }

        /* ── Error block ── */
        .error-box {
            background: #fff0f0;
            border: 1px solid #fca5a5;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 13px;
            color: #c62828;
            margin-bottom: 16px;
        }

        /* ── Offers & Promo ── */
        .promo-box {
            background: white;
            border-radius: 10px;
            border: 1px dashed #A90B16;
            padding: 16px 20px;
            margin-top: 14px;
        }
        .promo-title { font-weight: 700; font-size: 14px; color: #1a1a1a; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        .promo-title i { color: #A90B16; }
        .offer-card { background: #FFF5F5; border: 1px solid #ffd0d1; border-radius: 6px; padding: 10px; margin-bottom: 8px; font-size: 12px; }
        .offer-code { font-weight: 700; color: #A90B16; background: rgba(169, 11, 22, 0.1); padding: 2px 6px; border-radius: 4px; letter-spacing: 0.5px; margin-right: 5px; cursor: pointer; }
        .offer-code:hover { background: rgba(169, 11, 22, 0.2); }
        .promo-input-group { display: flex; gap: 8px; margin-top: 14px; }
        .promo-input-group input { flex: 1; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; font-family: 'Inter', sans-serif; }
        .promo-input-group input:focus { outline: none; border-color: #d00e15; }
        .promo-btn { background: #374151; color: white; border: none; border-radius: 6px; padding: 0 16px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .promo-btn:hover { background: #1f2937; }
        .promo-btn:disabled { background: #9ca3af; cursor: not-allowed; }
        .promo-msg { font-size: 12px; margin-top: 8px; font-weight: 500; }
        .promo-msg.success { color: #008009; }
        .promo-msg.error { color: #c62828; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .checkout-grid { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: span 1; }
            .date-summary { grid-template-columns: 1fr; }
            .date-col { border-right: none; border-bottom: 1px solid #e4e8ed; }
            .date-col:last-child { border-bottom: none; }
            .steps-bar { display: none; }
            .price-sidebar { position: static; }
        }
    </style>
</head>
<body>

{{-- Header --}}
<header class="checkout-header">
    <div class="checkout-header-inner">
        <a href="{{ route('hotels.show', $property) }}" class="logo-link">
            <i class="fas fa-plane-departure"></i>
            Bookdei
        </a>
        <div class="steps-bar">
            <div class="step-item active">
                <div class="step-num">1</div>
                <span>Your details</span>
            </div>
            <i class="fas fa-chevron-right step-arrow"></i>
            <div class="step-item">
                <div class="step-num">2</div>
                <span>Final confirmation</span>
            </div>
        </div>
        <div style="font-size:12px;color:rgba(255,255,255,0.75);display:flex;align-items:center;gap:6px;">
            <i class="fas fa-lock"></i> Secure booking
        </div>
    </div>
</header>

<div class="security-bar">
    <i class="fas fa-shield-alt"></i>
    This is a secure booking page. Your information is encrypted and protected.
</div>

<div class="page-wrap">
    @if(session('error'))
        <div class="error-box"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="checkout-grid">
        {{-- ── LEFT: Guest Form ── --}}
        <div>
            {{-- Hotel + Room Summary --}}
            <div class="card">
                <div style="padding:14px 20px;border-bottom:1px solid #e4e8ed;display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:#6b7280;font-weight:600;">Your booking</div>
                        <div style="font-weight:700;font-size:16px;color:#1a1a1a;font-family:'Outfit',sans-serif;">{{ $property->name }}</div>
                        <div style="font-size:12px;color:#6b7280;display:flex;align-items:center;gap:4px;margin-top:2px;">
                            <i class="fas fa-map-marker-alt" style="color:#c62828;"></i>
                            {{ $property->city }}{{ $property->country ? ', ' . $property->country : '' }}
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="display:flex;align-items:center;gap:3px;justify-content:flex-end;margin-bottom:2px;">
                            @for($i = 1; $i <= $property->stars; $i++)
                                <i class="fas fa-star" style="color:#f5a623;font-size:11px;"></i>
                            @endfor
                        </div>
                        @if($property->average_rating)
                            <span style="background:#d00e15;color:white;font-weight:700;font-size:12px;padding:2px 8px;border-radius:4px;">
                                {{ number_format($property->average_rating, 1) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="room-summary">
                    @if($roomType->photos->isNotEmpty())
                    <div class="room-thumb">
                        @php $photoUrl = $roomType->photos[0]->url; @endphp
                        <img src="{{ Str::startsWith($photoUrl, ['http://', 'https://']) ? $photoUrl : asset('storage/' . $photoUrl) }}" alt="{{ $roomType->name }}" onerror="this.onerror=null; this.style.display='none';">
                    </div>
                    @else
                    <div class="room-thumb" style="display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-bed" style="color:#d1d5db;font-size:24px;"></i>
                    </div>
                    @endif
                    <div class="room-info">
                        <div class="room-name">{{ $roomType->name }}</div>
                        @if($ratePlan)
                            <div style="font-size:13px;color:#A90B16;font-weight:500;">{{ $ratePlan->plan_display_name }}</div>
                        @endif
                        <div class="room-meta-row">
                            @if($roomType->size_sqm)
                                <span><i class="fas fa-expand-arrows-alt"></i> {{ $roomType->size_sqm }} m²</span>
                            @endif
                            <span><i class="fas fa-user"></i> {{ $adults }} adults{{ $children > 0 ? ', ' . $children . ' children' : '' }}</span>
                            <span><i class="fas fa-bed"></i> {{ $roomType->bed_config_display }}</span>
                        </div>
                    </div>
                </div>

                {{-- Date Summary --}}
                <div class="date-summary">
                    <div class="date-col">
                        <div class="date-label">Check-in</div>
                        <div class="date-value">{{ $checkIn->format('d M Y') }}</div>
                        <div class="date-sub">from {{ $property->check_in_time ?? '14:00' }}</div>
                    </div>
                    <div class="date-col">
                        <div class="date-label">Check-out</div>
                        <div class="date-value">{{ $checkOut->format('d M Y') }}</div>
                        <div class="date-sub">until {{ $property->check_out_time ?? '12:00' }}</div>
                    </div>
                    <div class="date-col">
                        <div class="date-label">Stay duration</div>
                        <div class="date-value">{{ $pricing['nights'] }}</div>
                        <div class="date-sub">night{{ $pricing['nights'] > 1 ? 's' : '' }}</div>
                    </div>
                </div>

                {{-- Inclusions --}}
                <div class="inclusions-list">
                    @if($ratePlan && str_contains(strtolower($ratePlan->meal_plan ?? ''), 'breakfast'))
                        <div class="inclusion-item pos"><i class="fas fa-check-circle"></i> Breakfast included</div>
                    @else
                        <div class="inclusion-item neutral" style="color:#6b7280;"><i class="fas fa-times-circle" style="color:#d1d5db;"></i> Room only (no meals)</div>
                    @endif
                    @if($property->cancellation_policy && ($property->cancellation_policy['type'] ?? '') === 'free')
                        <div class="inclusion-item pos"><i class="fas fa-check-circle"></i> Free cancellation</div>
                    @else
                        <div class="inclusion-item neg"><i class="fas fa-times-circle"></i> Non-refundable</div>
                    @endif
                    <div class="inclusion-item neutral"><i class="fas fa-credit-card" style="color:#A90B16;"></i> No payment needed today</div>
                </div>
            </div>

            {{-- Guest Details Form --}}
            <div class="card">
                <div class="card-header">
                    <h2>Enter your details</h2>
                    <div class="subtitle">Almost done — just a few details and your room is reserved.</div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('hotels.book.confirm') }}" id="booking-form">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                        <input type="hidden" name="check_in" value="{{ $checkIn->format('Y-m-d') }}">
                        <input type="hidden" name="check_out" value="{{ $checkOut->format('Y-m-d') }}">
                        <input type="hidden" name="adults" value="{{ $adults }}">
                        <input type="hidden" name="children" value="{{ $children }}">
                        <input type="hidden" name="rate_plan_id" value="{{ $ratePlan?->id }}">
                        <input type="hidden" name="promo_code" id="hidden_promo_code" value="">

                        {{-- Who is staying? --}}
                        <div class="form-section">
                            <div class="form-section-title">Guest information</div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>First name <span class="req">*</span></label>
                                    <input type="text" name="first_name" class="form-input" required
                                        value="{{ old('first_name', Auth::user()->name ? explode(' ', Auth::user()->name)[0] : '') }}"
                                        placeholder="First name">
                                </div>
                                <div class="form-group">
                                    <label>Last name <span class="req">*</span></label>
                                    <input type="text" name="last_name" class="form-input" required
                                        value="{{ old('last_name', Auth::user()->name && count(explode(' ', Auth::user()->name)) > 1 ? explode(' ', Auth::user()->name, 2)[1] : '') }}"
                                        placeholder="Last name">
                                </div>
                                <div class="form-group">
                                    <label>Email address <span class="req">*</span></label>
                                    <input type="email" name="email" class="form-input" required
                                        value="{{ old('email', Auth::user()->email) }}"
                                        placeholder="you@example.com">
                                    <span style="font-size:11px;color:#6b7280;">Confirmation will be sent here</span>
                                </div>
                                <div class="form-group">
                                    <label>Phone number <span class="req">*</span></label>
                                    <input type="tel" name="phone" class="form-input" required
                                        value="{{ old('phone', Auth::user()->phone ?? '') }}"
                                        placeholder="+880 1234 567890">
                                </div>
                                <div class="form-group">
                                    <label>Country / Region</label>
                                    <input type="text" name="country" class="form-input"
                                        value="{{ old('country') }}"
                                        placeholder="Bangladesh">
                                </div>
                                <div class="form-group">
                                    <label>Estimated arrival time</label>
                                    <select name="estimated_arrival" class="form-input">
                                        <option value="">I don't know</option>
                                        @for($h = 0; $h < 24; $h++)
                                            <option value="{{ sprintf('%02d:00', $h) }}">{{ sprintf('%02d:00', $h) }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Special Requests --}}
                        <div style="margin-top:20px;padding-top:20px;border-top:1px solid #e4e8ed;">
                            <div class="form-section-title">Special requests <span style="font-size:11px;font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;">(optional)</span></div>
                            <p style="font-size:13px;color:#6b7280;margin-bottom:12px;">Requests can't be guaranteed, but the property will do their best to accommodate.</p>
                            <div class="special-req-row">
                                @foreach(['Non-smoking room', 'High floor', 'Twin beds', 'Quiet room', 'Early check-in', 'Late check-out'] as $req)
                                    <label class="sr-option">
                                        <input type="checkbox" name="request_options[]" value="{{ $req }}">
                                        {{ $req }}
                                    </label>
                                @endforeach
                            </div>
                            <textarea name="special_requests" class="form-input" style="margin-top:10px;" rows="3"
                                placeholder="Anything else? E.g. crib needed, allergies, room preferences...">{{ old('special_requests') }}</textarea>
                        </div>

                        {{-- Payment Method --}}
                        <div style="margin-top:20px;padding-top:20px;border-top:1px solid #e4e8ed;">
                            <div class="form-section-title">How do you want to pay?</div>
                            <label class="payment-option selected" id="pay-hotel-label">
                                <input type="radio" name="payment_method" value="pending" checked
                                    onchange="selectPayment(this.closest('.payment-option'))">
                                <div>
                                    <i class="fas fa-hotel" style="font-size:18px;color:#d00e15;"></i>
                                </div>
                                <div class="payment-option-info">
                                    <div class="title">Pay at the property</div>
                                    <div class="desc">You'll pay when you check in. No charges today.</div>
                                </div>
                            </label>
                            <label class="payment-option" id="pay-card-label">
                                <input type="radio" name="payment_method" value="sslcommerz" onchange="selectPayment(this.closest('.payment-option'))">
                                <div>
                                    <i class="fas fa-credit-card" style="font-size:18px;color:#A90B16;"></i>
                                </div>
                                <div class="payment-option-info">
                                    <div class="title">Credit / Debit card (SSLCommerz)</div>
                                    <div class="desc">Pay securely online via SSLCommerz</div>
                                </div>
                            </label>
                            
                            <div style="margin-top:20px;text-align:center;padding:10px;background:white;border-radius:8px;border:1px solid #e4e8ed;">
                                <div style="font-size:11px;color:#6b7280;margin-bottom:8px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Supported Payment Methods</div>
                                <img src="{{ asset('images/sslcommerz_banner.png') }}" alt="Pay securely with SSLCommerz" style="max-width:100%;height:auto;">
                            </div>
                        </div>

                        {{-- Terms --}}
                        <div style="margin-top:20px;padding-top:20px;border-top:1px solid #e4e8ed;">
                            <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-size:13px;color:#374151;font-weight:400;">
                                <input type="checkbox" id="terms-cb" required style="margin-top:2px;accent-color:#d00e15;width:16px;height:16px;flex-shrink:0;">
                                I have read and accept the
                                <a href="#" style="color:#d00e15;">booking terms and conditions</a>
                                and the property's cancellation policy.
                            </label>
                        </div>

                        <button type="submit" class="btn-confirm" id="confirm-btn" style="margin-top:20px;" disabled>
                            <i class="fas fa-lock"></i>
                            Complete booking
                        </button>

                        <p style="text-align:center;font-size:11px;color:#9ca3af;margin-top:10px;">
                            <i class="fas fa-shield-alt"></i>
                            Your information is secured with 256-bit SSL encryption
                        </p>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: Price Sidebar ── --}}
        <div class="price-sidebar">
            <div class="price-card">
                <div class="price-card-header">
                    <div class="price-card-hotel">{{ $property->name }}</div>
                    <div class="price-card-room">{{ $roomType->name }}</div>
                    <div class="price-card-dates">
                        <i class="fas fa-calendar"></i>
                        {{ $checkIn->format('d M') }} – {{ $checkOut->format('d M Y') }}
                        &bull; {{ $pricing['nights'] }} nights &bull; {{ $adults }} guests
                    </div>
                </div>
                <div class="price-card-body">
                    {{-- Nightly breakdown --}}
                    @if(count($pricing['nightly_rates'] ?? []) > 0 && count($pricing['nightly_rates']) <= 7)
                    <div class="nightly-breakdown">
                        <div style="font-weight:600;color:#374151;font-size:12px;margin-bottom:6px;">Nightly rates</div>
                        @foreach($pricing['nightly_rates'] as $date => $rate)
                            <div class="nightly-breakdown-row">
                                <span>{{ \Carbon\Carbon::parse($date)->format('D, M d') }}</span>
                                <span>{{ \App\Helpers\Currency::format($rate) }}</span>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="price-row">
                        <span style="color:#6b7280;">{{ \App\Helpers\Currency::format($pricing['nightly_rate']) }} × {{ $pricing['nights'] }} nights</span>
                        <span>{{ \App\Helpers\Currency::format($pricing['subtotal']) }}</span>
                    </div>
                    <div class="price-row">
                        <span style="color:#6b7280;">Tax (10%)</span>
                        <span>{{ \App\Helpers\Currency::format($pricing['taxes']) }}</span>
                    </div>
                    <div class="price-row">
                        <span style="color:#6b7280;">Service fee</span>
                        <span>{{ \App\Helpers\Currency::format($pricing['fees']) }}</span>
                    </div>
                    <div class="price-row discount" id="discount-row" style="{{ $pricing['discount'] > 0 ? '' : 'display:none;' }}">
                        <span>Discount</span>
                        <span id="discount-amount">-{{ \App\Helpers\Currency::format($pricing['discount']) }}</span>
                    </div>
                    <div class="price-row total">
                        <span>Total</span>
                        <span id="total-amount">{{ \App\Helpers\Currency::format($pricing['total']) }}</span>
                    </div>

                    <div style="margin-top:12px;font-size:12px;color:#6b7280;text-align:center;">
                        Includes all taxes and fees
                    </div>

                    @if($property->cancellation_policy && ($property->cancellation_policy['type'] ?? '') === 'free')
                        <div class="cancel-policy">
                            <i class="fas fa-shield-check" style="flex-shrink:0;"></i>
                            <div>
                                <div style="font-weight:600;margin-bottom:2px;">Free cancellation</div>
                                <div>Cancel before your check-in date for a full refund.</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="trust-box">
                <div style="font-weight:700;font-size:13px;color:#1a1a1a;margin-bottom:10px;">Your booking is safe</div>
                <div class="trust-row"><i class="fas fa-lock" style="color:#008009;"></i> Secure transaction</div>
                <div class="trust-row"><i class="fas fa-medal" style="color:#f5a623;"></i> Best price guarantee</div>
                <div class="trust-row"><i class="fas fa-headset" style="color:#A90B16;"></i> 24/7 support</div>
                <div class="trust-row"><i class="fas fa-file-invoice" style="color:#d00e15;"></i> Instant confirmation</div>
            </div>

            {{-- Promo Code Section --}}
            <div class="promo-box">
                <div class="promo-title"><i class="fas fa-tags"></i> Have a promo code?</div>
                
                @if(isset($promotions) && $promotions->isNotEmpty())
                    <div style="margin-bottom: 12px;">
                        <div style="font-size:11px; color:#6b7280; margin-bottom: 6px; text-transform:uppercase; font-weight:600;">Available Offers</div>
                        @foreach($promotions as $promo)
                            <div class="offer-card">
                                <span class="offer-code" onclick="document.getElementById('promo_input').value='{{ $promo->code }}'">{{ $promo->code }}</span>
                                <span style="color:#374151;">{{ $promo->discount_type === 'percent' ? $promo->discount_value.'%' : \App\Helpers\Currency::format($promo->discount_value) }} off {{ strtolower($promo->title) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="promo-input-group">
                    <input type="text" id="promo_input" placeholder="Enter code">
                    <button type="button" class="promo-btn" id="apply_promo_btn" onclick="applyPromo()">Apply</button>
                </div>
                <div id="promo_msg" class="promo-msg"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // Enable confirm button only after terms accepted
    const termsCb = document.getElementById('terms-cb');
    const confirmBtn = document.getElementById('confirm-btn');
    termsCb.addEventListener('change', function() {
        confirmBtn.disabled = !this.checked;
        confirmBtn.style.opacity = this.checked ? '1' : '0.5';
    });

    // Payment option selection highlight
    function selectPayment(el) {
        document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
        if (el) el.classList.add('selected');
    }

    // Loading state on submit
    document.getElementById('booking-form').addEventListener('submit', function() {
        confirmBtn.innerHTML = '<span style="display:inline-block;width:18px;height:18px;border:3px solid rgba(255,255,255,0.4);border-top-color:white;border-radius:50%;animation:spin 0.6s linear infinite;"></span> Processing...';
        confirmBtn.disabled = true;
    });

    // Apply Promo Code AJAX
    async function applyPromo() {
        const code = document.getElementById('promo_input').value.trim();
        const msgEl = document.getElementById('promo_msg');
        const btn = document.getElementById('apply_promo_btn');
        
        if (!code) {
            msgEl.textContent = 'Please enter a promo code';
            msgEl.className = 'promo-msg error';
            return;
        }

        btn.disabled = true;
        btn.textContent = '...';
        msgEl.textContent = '';

        try {
            const response = await fetch('{{ route('hotels.apply-coupon') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    property_id: {{ $property->id }},
                    room_type_id: {{ $roomType->id }},
                    check_in: '{{ $checkIn->format('Y-m-d') }}',
                    check_out: '{{ $checkOut->format('Y-m-d') }}',
                    rooms: 1, // Fixed for now as per UI
                    rate_plan_id: {{ $ratePlan?->id ?? 'null' }},
                    promo_code: code
                })
            });

            const data = await response.json();
            
            if (response.ok && data.success) {
                // Update UI with discount
                msgEl.textContent = data.message;
                msgEl.className = 'promo-msg success';
                
                // Set hidden input
                document.getElementById('hidden_promo_code').value = code;
                
                // Show discount row
                document.getElementById('discount-row').style.display = 'flex';
                document.getElementById('discount-amount').textContent = '-' + data.discount_formatted;
                document.getElementById('total-amount').textContent = data.total_formatted;
            } else {
                msgEl.textContent = data.message || 'Invalid promo code';
                msgEl.className = 'promo-msg error';
                
                // Clear hidden input and discount if previously applied
                document.getElementById('hidden_promo_code').value = '';
                document.getElementById('discount-row').style.display = 'none';
                document.getElementById('total-amount').textContent = '{{ \App\Helpers\Currency::format($pricing["subtotal"] + $pricing["taxes"] + $pricing["fees"]) }}';
            }
        } catch (error) {
            msgEl.textContent = 'An error occurred. Please try again.';
            msgEl.className = 'promo-msg error';
        }
        
        btn.disabled = false;
        btn.textContent = 'Apply';
    }
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
</body>
</html>

