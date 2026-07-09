<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $property->name }} — {{ config('app.name') }}</title>
    <meta name="description" content="{{ $property->short_description ?? $property->name . ' in ' . $property->city }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --brand: #d00e15;
            --brand-dark: #A90B16;
            --brand-light: #FFF5F5;
            --green: #2E7D32;
            --orange: #F57F17;
            --gold: #f5a623;
            --bg: #F8F7F7;
            --card: #ffffff;
            --text: #19100F;
            --muted: #6B7280;
            --border: #E5E7EB;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
        }

        /* ── Header ─────────────────────────────── */
        .hotel-header {
            background: #19100F;
            color: white;
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }
        .hotel-header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
        }
        .back-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .back-link:hover { color: white; }
        .anchor-nav {
            display: flex;
            gap: 2px;
        }
        .anchor-nav a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 20px;
            transition: all 0.2s;
        }
        .anchor-nav a:hover, .anchor-nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .header-auth { display: flex; align-items: center; gap: 10px; }
        .header-auth a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 13px;
        }
        .btn-sign-in {
            background: rgba(255,255,255,0.12);
            color: white !important;
            padding: 7px 16px;
            border-radius: 6px;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.2);
            transition: background 0.2s;
        }
        .btn-sign-in:hover { background: rgba(255,255,255,0.2); }
        .btn-register {
            background: #d00e15;
            color: white !important;
            padding: 7px 16px;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-register:hover { background: #A90B16; }

        .gallery-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            grid-template-rows: 200px 200px;
            gap: 4px;
            border-radius: 0;
            overflow: hidden;
            max-height: 404px;
            position: relative;
        }
        .gallery-grid .hero { grid-row: span 2; }
        
        /* Modifiers for fewer photos */
        .gallery-grid-1 { grid-template-columns: 1fr; grid-template-rows: 404px; }
        .gallery-grid-1 .hero { grid-row: span 1; }
        
        .gallery-grid-2 { grid-template-columns: 1fr 1fr; grid-template-rows: 404px; }
        .gallery-grid-2 .hero { grid-row: span 1; }
        
        .gallery-grid-3 { grid-template-columns: 2fr 1fr; grid-template-rows: 200px 200px; }
        
        .gallery-grid-4 .gallery-slot:nth-child(2) { grid-column: span 2; }

        .gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s;
            cursor: pointer;
        }
        .gallery-img:hover { transform: scale(1.03); }
        .gallery-slot { overflow: hidden; position: relative; background: #f0d8d8; }
        .show-all-btn {
            position: absolute;
            bottom: 16px;
            right: 16px;
            background: white;
            color: #1a1a1a;
            border: 2px solid #1a1a1a;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            z-index: 5;
            text-decoration: none;
        }
        .show-all-btn:hover { background: #f0f0f0; }

        /* ── Layout ─────────────────────────────── */
        .page-wrap { max-width: 1200px; margin: 0 auto; padding: 24px 16px; }
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            align-items: start;
        }

        /* ── Property Title Block ─────────────────── */
        .prop-header { margin-bottom: 20px; }
        .stars-row { display: flex; align-items: center; gap: 6px; margin-bottom: 6px; }
        .star-icon { color: #f5a623; font-size: 13px; }
        .prop-type-badge {
            background: #FFF5F5;
            color: #d00e15;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .prop-name {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 6px 0;
            line-height: 1.2;
        }
        .prop-location {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #6B7280;
            margin-bottom: 12px;
            cursor: pointer;
        }
        .prop-location i { color: #d00e15; }
        .prop-location:hover { color: #19100F; }
        .prop-badges { display: flex; flex-wrap: wrap; gap: 8px; }
        .prop-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: #008009;
            font-weight: 500;
        }

        /* ── Rating summary ─────────────────────── */
        .rating-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--brand-light);
            border-radius: 8px;
            padding: 8px 14px;
            margin-bottom: 20px;
        }
        .rating-score {
            background: #19100F;
            color: white;
            font-weight: 700;
            font-size: 18px;
            width: 44px;
            height: 44px;
            border-radius: 8px 8px 8px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .rating-label { font-weight: 700; font-size: 14px; color: #1a1a1a; }
        .rating-count { font-size: 12px; color: #6b7280; }

        /* ── Section Cards ─────────────────────── */
        .section-card {
            background: white;
            border-radius: 8px;
            border: 1px solid var(--border);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .section-card-header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid var(--border);
        }
        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }
        .section-card-body { padding: 20px; }

        /* ── Room Type Rows (Booking.com style) ─── */
        .rooms-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .rooms-table thead th {
            background: #f8f9fa;
            padding: 10px 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border);
            white-space: nowrap;
        }
        .rooms-table td {
            padding: 16px 12px;
            vertical-align: top;
            border-bottom: 1px solid var(--border);
        }
        .rooms-table tr:last-child td { border-bottom: none; }
        .room-type-cell { min-width: 220px; }
        .room-photo-wrap {
            width: 100%;
            height: 130px;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 10px;
            background: #e8eef5;
            cursor: pointer;
        }
        .room-photo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .room-photo-wrap:hover img { transform: scale(1.05); }
        .room-type-name {
            font-weight: 700;
            color: #19100F;
            font-size: 15px;
            margin-bottom: 4px;
            cursor: pointer;
        }
        .room-type-name:hover { color: #d00e15; text-decoration: underline; }
        .room-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 8px;
        }
        .room-meta span { display: flex; align-items: center; gap: 4px; }
        .room-amenity-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 6px;
        }
        .room-amenity-tag {
            background: #f0f4f8;
            border-radius: 4px;
            padding: 2px 7px;
            font-size: 11px;
            color: #374151;
        }
        .urgency-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #fff3e0;
            color: #e65100;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
            margin-top: 6px;
        }

        /* Rate Plan Cell */
        .rate-plan-cell { min-width: 220px; }
        .rate-plan-row {
            padding: 10px 0;
            border-bottom: 1px dashed #e4e8ed;
        }
        .rate-plan-row:last-child { border-bottom: none; }
        .rate-plan-name {
            font-weight: 600;
            font-size: 13px;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        .rate-inclusion {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: #008009;
            margin-top: 3px;
        }
        .rate-inclusion.neg { color: #c62828; }
        .rate-inclusion.neutral { color: #6b7280; }

        /* Price Cell */
        .price-cell { min-width: 130px; text-align: right; }
        .price-nightly {
            font-size: 22px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            color: #1a1a1a;
            line-height: 1;
        }
        .price-per-night-label { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .price-total { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .price-taxes-note { font-size: 11px; color: #6b7280; }

        /* Reserve Cell */
        .reserve-cell { min-width: 140px; text-align: center; vertical-align: middle; }
        .rooms-select {
            width: 100%;
            padding: 7px 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 8px;
            background: white;
        }
        .btn-reserve {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 11px 20px;
            background: #d00e15;
            color: white;
            font-size: 14px;
            font-weight: 700;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .btn-reserve:hover { background: #A90B16; color: white; }
        .btn-sold-out {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 11px 20px;
            background: #f0f0f0;
            color: #9ca3af;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: not-allowed;
        }

        /* ── Sticky Booking Widget ─────────────── */
        .booking-widget {
            background: white;
            border: 1px solid #E5E7EB;
            border-top: 3px solid #d00e15;
            border-radius: 10px;
            padding: 20px;
            position: sticky;
            top: 72px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .widget-title { font-size: 13px; color: #6b7280; margin-bottom: 4px; }
        .widget-price {
            font-size: 32px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            color: #1a1a1a;
            line-height: 1;
        }
        .widget-price span { font-size: 14px; font-weight: 400; color: #6b7280; }
        .widget-form { margin-top: 16px; }
        .widget-date-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border: 1px solid #ccc;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .widget-date-row > div:first-child { border-right: 1px solid #ccc; }
        .widget-date-block {
            padding: 8px 10px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .widget-date-block:hover { background: #FFF5F5; }
        .widget-date-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.5px;
        }
        .widget-date-input {
            border: none;
            background: transparent;
            font-size: 13px;
            font-weight: 600;
            color: #1a1a1a;
            width: 100%;
            padding: 0;
            cursor: pointer;
            position: relative;
        }
        .widget-date-input:focus { outline: none; }
        .widget-date-input::-webkit-calendar-picker-indicator {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .widget-guests-row {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 14px;
        }
        .widget-guests-select {
            border: none;
            background: transparent;
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            width: 100%;
            padding: 0;
        }
        .widget-guests-select:focus { outline: none; }
        .btn-check-avail {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px;
            background: #d00e15;
            color: white;
            font-size: 15px;
            font-weight: 700;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-check-avail:hover { background: #A90B16; color: white; }
        .widget-free-cancel {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 10px;
            font-size: 12px;
            color: #008009;
            font-weight: 500;
        }
        .widget-divider { border: none; border-top: 1px solid var(--border); margin: 14px 0; }

        /* ── Guest Score ─────────────────────── */
        .score-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: #19100F;
            color: white;
            font-weight: 700;
            font-size: 17px;
            border-radius: 8px 8px 8px 0;
        }
        .score-badge.excellent { background: #2E7D32; }
        .score-badge.very-good { background: #19100F; }
        .score-badge.good { background: #F57F17; }
        .score-badge.avg { background: #9ca3af; }
        .review-score-bar { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
        .review-score-label { font-size: 12px; width: 90px; flex-shrink: 0; }
        .review-score-track {
            flex: 1;
            height: 8px;
            background: #e4e8ed;
            border-radius: 4px;
            overflow: hidden;
        }
        .review-score-fill { height: 100%; background: #19100F; border-radius: 4px; transition: width 1s ease; }
        .review-score-val { font-size: 12px; font-weight: 600; width: 28px; flex-shrink: 0; }

        /* Review Cards */
        .review-card {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
        }
        .review-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #19100F;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }
        .review-score-inline {
            background: #19100F;
            color: white;
            font-weight: 700;
            font-size: 13px;
            padding: 3px 8px;
            border-radius: 4px;
        }

        /* Amenities */
        .amenity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 8px;
        }
        .amenity-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #374151;
        }
        .amenity-item i { color: #2E7D32; width: 16px; text-align: center; }

        /* ── Description ─────────────────────── */
        .description-text {
            font-size: 14px;
            line-height: 1.8;
            color: #374151;
        }

        /* ── Check-in/out cards ─────────────── */
        .checkin-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .checkin-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 14px 16px;
        }
        .checkin-card-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; font-weight: 600; margin-bottom: 4px; }
        .checkin-card-value { font-size: 18px; font-weight: 700; font-family: 'Outfit', sans-serif; color: #1a1a1a; }
        .checkin-card-sub { font-size: 12px; color: #6b7280; margin-top: 2px; }

        /* ── Location ─────────────────────── */
        .location-placeholder {
            background: linear-gradient(135deg, #e8f0fc 0%, #f0f4ff 100%);
            height: 200px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 12px;
        }
        .location-icon { font-size: 36px; color: #d00e15; }

        /* ── Responsive ─────────────────────── */
        @media (max-width: 768px) {
            .main-grid { grid-template-columns: 1fr; }
            .gallery-grid { grid-template-columns: 1fr; grid-template-rows: 200px; }
            .gallery-grid .gallery-slot:not(.hero) { display: none; }
            .anchor-nav { display: none; }
            .rooms-table thead { display: none; }
            .rooms-table, .rooms-table tbody, .rooms-table tr, .rooms-table td { display: block; }
            .rooms-table td { padding: 12px 16px; border: none; }
            .rooms-table tr { border-bottom: 2px solid var(--border); }
            .price-cell { text-align: left; }
            .reserve-cell { text-align: left; }
            .booking-widget { position: static; border: 1px solid var(--border); border-radius: 8px; }
        }

        /* Loading state */
        .loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 8px;
        }
        .spinner {
            width: 24px; height: 24px;
            border: 3px solid #e4e8ed;
            border-top-color: #d00e15;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

{{-- ── Header ─── --}}
@php
    $userCurrency = session('currency', 'BDT');
    $currencySymbol = $userCurrency === 'USD' ? '$' : '৳';
    $exchangeRate = 1;
    if ($userCurrency === 'BDT') {
        $exchangeRate = app(\App\Services\CurrencyConverterService::class)->getExchangeRate('USD', 'BDT');
    }
@endphp
<header class="hotel-header">
    <div class="hotel-header-inner">
        <a href="{{ url()->previous() }}" class="back-link">
            <i class="fas fa-arrow-left"></i>
            <span>Back to results</span>
        </a>
        <nav class="anchor-nav">
            <a href="#overview" class="active">Overview</a>
            <a href="#rooms">Rooms</a>
            <a href="#amenities">Amenities</a>
            <a href="#location">Location</a>
            <a href="#reviews">Reviews</a>
        </nav>
        <div class="header-auth">
            @auth
                <a href="{{ route('dashboard') }}" style="color:rgba(255,255,255,0.85); font-size:13px;">My Bookings</a>
                <div style="width:32px;height:32px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-sign-in">Sign in</a>
                <a href="{{ route('register') }}" class="btn-register">Register</a>
            @endauth
        </div>
    </div>
</header>

{{-- ── Photo Gallery ─── --}}
<div style="background:#1a1a1a; position:relative;">
    @if($property->photos->isNotEmpty())
    @php
        $photoCount = $property->photos->count();
        $gridClass = 'gallery-grid';
        if ($photoCount === 1) $gridClass .= ' gallery-grid-1';
        elseif ($photoCount === 2) $gridClass .= ' gallery-grid-2';
        elseif ($photoCount === 3) $gridClass .= ' gallery-grid-3';
        elseif ($photoCount === 4) $gridClass .= ' gallery-grid-4';
    @endphp
    <div class="{{ $gridClass }}">
        {{-- Hero --}}
        <div class="gallery-slot hero">
            <a href="{{ $property->photos[0]->url }}" class="glightbox" data-gallery="hotel">
                <img src="{{ $property->photos[0]->url }}" alt="{{ $property->name }}" class="gallery-img">
            </a>
        </div>
        @foreach($property->photos->skip(1)->take(4) as $photo)
        <div class="gallery-slot">
            <a href="{{ $photo->url }}" class="glightbox" data-gallery="hotel">
                <img src="{{ $photo->url }}" alt="{{ $property->name }}" class="gallery-img">
            </a>
        </div>
        @endforeach
        {{-- Hidden remaining for lightbox --}}
        @foreach($property->photos->skip(5) as $photo)
            <a href="{{ $photo->url }}" class="glightbox hidden" data-gallery="hotel"></a>
        @endforeach
    </div>
    @if($property->photos->count() > 5)
        <a href="{{ $property->photos[5]->url }}" class="show-all-btn glightbox" data-gallery="hotel">
            <i class="fas fa-th"></i> Show all {{ $property->photos->count() }} photos
        </a>
    @endif
    @else
    <div style="height:350px;background:linear-gradient(135deg,#d00e15,#A90B16);display:flex;align-items:center;justify-content:center;">
        <i class="fas fa-hotel" style="font-size:60px;color:rgba(255,255,255,0.3);"></i>
    </div>
    @endif
</div>

{{-- ── Page Content ─── --}}
<div class="page-wrap">
    <div class="main-grid">

        {{-- ── LEFT COLUMN ─── --}}
        <div>

            {{-- Property Title & Info --}}
            <section id="overview">
                <div class="prop-header">
                    <div class="stars-row">
                        @for($i = 1; $i <= $property->stars; $i++)
                            <i class="fas fa-star star-icon"></i>
                        @endfor
                        <span class="prop-type-badge">{{ ucfirst($property->type) }}</span>
                    </div>
                    <h1 class="prop-name">{{ $property->name }}</h1>
                    <div class="prop-location" onclick="document.getElementById('location').scrollIntoView({behavior:'smooth'})">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $property->full_address }}</span>
                        <span style="color:#A90B16;text-decoration:underline;font-size:12px;">Excellent location — show on map</span>
                    </div>
                    <div class="prop-badges">
                        @if($property->cancellation_policy && ($property->cancellation_policy['type'] ?? '') === 'free')
                            <span class="prop-badge"><i class="fas fa-check-circle"></i> Free cancellation</span>
                        @endif
                        @if($property->check_in_time)
                            <span class="prop-badge" style="color:#6b7280;"><i class="fas fa-clock"></i> Check-in from {{ $property->check_in_time }}</span>
                        @endif
                    </div>
                </div>

                @if($property->average_rating)
                <div class="rating-pill">
                    @php
                        $score = $property->average_rating;
                        $label = $score >= 9 ? 'Exceptional' : ($score >= 8 ? 'Excellent' : ($score >= 7 ? 'Very Good' : ($score >= 6 ? 'Good' : 'Pleasant')));
                    @endphp
                    <div class="rating-score">{{ number_format($score, 1) }}</div>
                    <div>
                        <div class="rating-label">{{ $label }}</div>
                        <div class="rating-count">{{ $property->review_count }} reviews</div>
                    </div>
                </div>
                @endif

                {{-- Description --}}
                @if($property->full_description || $property->short_description)
                <div class="section-card">
                    <div class="section-card-body">
                        <p class="description-text">{{ $property->full_description ?? $property->short_description }}</p>
                    </div>
                </div>
                @endif
            </section>

            {{-- ── Rooms & Rates ─────────────────── --}}
            <section id="rooms">
                <div class="section-card">
                    <div class="section-card-header" style="display:flex;align-items:center;justify-content:space-between;">
                        <h2 class="section-title">Available Rooms</h2>
                        <div style="font-size:12px;color:#6b7280;">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $checkIn->format('M d') }} – {{ $checkOut->format('M d, Y') }} &bull;
                            {{ $pricing['nights'] ?? $checkIn->diffInDays($checkOut) }} nights &bull;
                            {{ $guests }} guests
                        </div>
                    </div>

                    @if($property->activeRoomTypes->isEmpty())
                        <div class="section-card-body" style="text-align:center;padding:40px;">
                            <i class="fas fa-bed" style="font-size:36px;color:#d1d5db;margin-bottom:12px;"></i>
                            <p style="color:#6b7280;">No rooms available for the selected dates.</p>
                        </div>
                    @else
                    <div style="overflow-x:auto;">
                        <table class="rooms-table">
                            <thead>
                                <tr>
                                    <th>Room Type</th>
                                    <th>Includes</th>
                                    <th>Price per night</th>
                                    <th>Rooms</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($property->activeRoomTypes as $roomType)
                                @php
                                    $data = $roomsData[$roomType->id] ?? ['available' => 0, 'pricing' => ['total' => 0, 'nightly_rate' => 0, 'nights' => 1]];
                                    $isAvailable = $data['available'] > 0;
                                    $plans = $roomType->activeRatePlans;
                                    $planCount = max($plans->count(), 1);
                                @endphp
                                @if($plans->isEmpty())
                                {{-- Room with no rate plans --}}
                                <tr>
                                    <td class="room-type-cell" rowspan="1">
                                        @if($roomType->photos->isNotEmpty())
                                        <div class="room-photo-wrap">
                                            <a href="{{ $roomType->photos[0]->url }}" class="glightbox" data-gallery="room-{{ $roomType->id }}">
                                                <img src="{{ $roomType->photos[0]->url }}" alt="{{ $roomType->name }}">
                                            </a>
                                            @foreach($roomType->photos->skip(1) as $photo)
                                                <a href="{{ $photo->url }}" class="glightbox hidden" data-gallery="room-{{ $roomType->id }}"></a>
                                            @endforeach
                                        </div>
                                        @endif
                                        <div class="room-type-name">{{ $roomType->name }}</div>
                                        <div class="room-meta">
                                            @if($roomType->size_sqm)
                                                <span><i class="fas fa-expand-arrows-alt"></i> {{ $roomType->size_sqm }} m²</span>
                                            @endif
                                            <span><i class="fas fa-user"></i> {{ $roomType->max_adults }} adults</span>
                                            @if($roomType->max_children > 0)
                                                <span><i class="fas fa-child"></i> {{ $roomType->max_children }} children</span>
                                            @endif
                                            <span><i class="fas fa-bed"></i> {{ $roomType->bed_config_display }}</span>
                                        </div>
                                        @if($roomType->amenities)
                                        <div class="room-amenity-tags">
                                            @foreach(array_slice($roomType->amenities, 0, 5) as $amenity)
                                                <span class="room-amenity-tag">{{ $amenity }}</span>
                                            @endforeach
                                        </div>
                                        @endif
                                        @if($isAvailable && $data['available'] <= 3)
                                            <div class="urgency-tag"><i class="fas fa-fire"></i> Only {{ $data['available'] }} left!</div>
                                        @endif
                                    </td>
                                    <td class="rate-plan-cell">
                                        <div class="rate-inclusion"><i class="fas fa-times-circle" style="color:#c62828;"></i> No meals</div>
                                        @if($property->cancellation_policy && ($property->cancellation_policy['type'] ?? '') === 'free')
                                            <div class="rate-inclusion"><i class="fas fa-check-circle"></i> Free cancellation</div>
                                        @else
                                            <div class="rate-inclusion neg"><i class="fas fa-times-circle"></i> Non-refundable</div>
                                        @endif
                                    </td>
                                    <td class="price-cell">
                                        <div class="price-nightly">{{ $currencySymbol }}{{ number_format($data['pricing']['nightly_rate'] * $exchangeRate, 0) }}</div>
                                        <div class="price-per-night-label">per night</div>
                                        @if($data['pricing']['nights'] > 1)
                                            <div class="price-total">{{ $currencySymbol }}{{ number_format($data['pricing']['total'] * $exchangeRate, 0) }} total</div>
                                        @endif
                                        <div class="price-taxes-note" style="margin-top:3px;">Incl. taxes &amp; fees</div>
                                    </td>
                                    <td class="reserve-cell">
                                        @if($isAvailable)
                                            <a href="{{ route('hotels.book.step1', ['property' => $property, 'roomType' => $roomType, 'check_in' => $checkIn->format('Y-m-d'), 'check_out' => $checkOut->format('Y-m-d'), 'adults' => $guests]) }}" class="btn-reserve">
                                                Reserve
                                            </a>
                                        @else
                                            <div class="btn-sold-out">Sold Out</div>
                                        @endif
                                    </td>
                                </tr>
                                @else
                                {{-- Rooms with rate plans --}}
                                @foreach($plans as $planIndex => $plan)
                                <tr>
                                    @if($planIndex === 0)
                                    <td class="room-type-cell" rowspan="{{ $planCount }}">
                                        @if($roomType->photos->isNotEmpty())
                                        <div class="room-photo-wrap">
                                            <a href="{{ $roomType->photos[0]->url }}" class="glightbox" data-gallery="room-{{ $roomType->id }}">
                                                <img src="{{ $roomType->photos[0]->url }}" alt="{{ $roomType->name }}">
                                            </a>
                                            @foreach($roomType->photos->skip(1) as $photo)
                                                <a href="{{ $photo->url }}" class="glightbox hidden" data-gallery="room-{{ $roomType->id }}"></a>
                                            @endforeach
                                        </div>
                                        @endif
                                        <div class="room-type-name">{{ $roomType->name }}</div>
                                        <div class="room-meta">
                                            @if($roomType->size_sqm)
                                                <span><i class="fas fa-expand-arrows-alt"></i> {{ $roomType->size_sqm }} m²</span>
                                            @endif
                                            <span><i class="fas fa-user"></i> {{ $roomType->max_adults }} adults</span>
                                            @if($roomType->max_children > 0)
                                                <span><i class="fas fa-child"></i> {{ $roomType->max_children }} children</span>
                                            @endif
                                            <span><i class="fas fa-bed"></i> {{ $roomType->bed_config_display }}</span>
                                        </div>
                                        @if($roomType->amenities)
                                        <div class="room-amenity-tags">
                                            @foreach(array_slice($roomType->amenities, 0, 5) as $amenity)
                                                <span class="room-amenity-tag">{{ $amenity }}</span>
                                            @endforeach
                                        </div>
                                        @endif
                                        @if($isAvailable && $data['available'] <= 3)
                                            <div class="urgency-tag"><i class="fas fa-fire"></i> Only {{ $data['available'] }} left!</div>
                                        @endif
                                    </td>
                                    @endif

                                    <td class="rate-plan-cell">
                                        <div class="rate-plan-name">{{ $plan->plan_display_name }}</div>
                                        @php
                                            $mealType = strtolower($plan->meal_plan ?? '');
                                            $mealLabel = match(true) {
                                                str_contains($mealType, 'breakfast') => '🍳 Breakfast included',
                                                str_contains($mealType, 'half') => '🍽️ Half board',
                                                str_contains($mealType, 'full') => '🍽️ Full board',
                                                str_contains($mealType, 'all') => '🍽️ All inclusive',
                                                default => null,
                                            };
                                        @endphp
                                        @if($mealLabel)
                                            <div class="rate-inclusion"><i class="fas fa-check-circle"></i> {{ $mealLabel }}</div>
                                        @else
                                            <div class="rate-inclusion" style="color:#6b7280;"><i class="fas fa-times-circle" style="color:#d1d5db;"></i> Room only</div>
                                        @endif
                                        @if($property->cancellation_policy && ($property->cancellation_policy['type'] ?? '') === 'free')
                                            <div class="rate-inclusion"><i class="fas fa-check-circle"></i> Free cancellation</div>
                                        @else
                                            <div class="rate-inclusion neg"><i class="fas fa-times-circle"></i> Non-refundable</div>
                                        @endif
                                        <div class="rate-inclusion neutral" style="margin-top:4px;">
                                            <i class="fas fa-credit-card" style="color:#6b7280;"></i> Pay at hotel
                                        </div>
                                    </td>

                                    <td class="price-cell">
                                        @php
                                            $planRate = ($data['pricing']['nightly_rate'] ?? 0) + $plan->price_supplement_per_adult;
                                            $planTotal = ($data['pricing']['total'] ?? 0) + ($plan->price_supplement_per_adult * ($data['pricing']['nights'] ?? 1));
                                        @endphp
                                        <div class="price-nightly">{{ $currencySymbol }}{{ number_format($planRate * $exchangeRate, 0) }}</div>
                                        <div class="price-per-night-label">per night</div>
                                        @if(($data['pricing']['nights'] ?? 1) > 1)
                                            <div class="price-total">{{ $currencySymbol }}{{ number_format($planTotal * $exchangeRate, 0) }} total</div>
                                        @endif
                                        <div class="price-taxes-note" style="margin-top:3px;">Incl. taxes &amp; fees</div>
                                    </td>

                                    <td class="reserve-cell">
                                        @if($isAvailable)
                                            <a href="{{ route('hotels.book.step1', ['property' => $property, 'roomType' => $roomType, 'check_in' => $checkIn->format('Y-m-d'), 'check_out' => $checkOut->format('Y-m-d'), 'adults' => $guests, 'rate_plan_id' => $plan->id]) }}" class="btn-reserve">
                                                Reserve
                                            </a>
                                        @else
                                            <div class="btn-sold-out">Sold Out</div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </section>

            {{-- ── Amenities ─────────────────────── --}}
            @if($property->amenities)
            <section id="amenities">
                <div class="section-card">
                    <div class="section-card-header">
                        <h2 class="section-title">Amenities</h2>
                    </div>
                    <div class="section-card-body">
                        <div class="amenity-grid">
                            @php
                                $amenityIcons = [
                                    'wifi' => 'fa-wifi', 'pool' => 'fa-swimming-pool', 'gym' => 'fa-dumbbell',
                                    'parking' => 'fa-parking', 'restaurant' => 'fa-utensils', 'spa' => 'fa-spa',
                                    'bar' => 'fa-glass-martini-alt', 'ac' => 'fa-snowflake', 'airport_shuttle' => 'fa-bus',
                                    'beach' => 'fa-umbrella-beach', 'business_center' => 'fa-briefcase',
                                    'laundry' => 'fa-tshirt', 'room_service' => 'fa-concierge-bell',
                                    'pet_friendly' => 'fa-paw', 'non_smoking' => 'fa-smoking-ban',
                                    'wheelchair' => 'fa-wheelchair', 'breakfast' => 'fa-coffee',
                                    'concierge' => 'fa-concierge-bell', 'security' => 'fa-shield-alt',
                                    'elevator' => 'fa-sort',
                                ];
                                $allAmenities = collect($property->amenities)->flatten()->toArray();
                            @endphp
                            @foreach($allAmenities as $amenity)
                                @php
                                    $key = strtolower(str_replace([' ', '-'], '_', $amenity));
                                    $icon = collect($amenityIcons)->first(fn($ic, $k) => str_contains($key, $k)) ?? 'fa-check';
                                @endphp
                                <div class="amenity-item">
                                    <i class="fas {{ $icon }}"></i>
                                    <span>{{ str_replace('_', ' ', ucfirst($amenity)) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
            @endif

            {{-- ── Check-in / Check-out ─────────── --}}
            <div class="section-card">
                <div class="section-card-header">
                    <h2 class="section-title">Check-in / Check-out</h2>
                </div>
                <div class="section-card-body">
                    <div class="checkin-cards">
                        <div class="checkin-card">
                            <div class="checkin-card-label">Check-in</div>
                            <div class="checkin-card-value">{{ $property->check_in_time ?? 'From 14:00' }}</div>
                            <div class="checkin-card-sub">Reception is open 24 hours</div>
                        </div>
                        <div class="checkin-card">
                            <div class="checkin-card-label">Check-out</div>
                            <div class="checkin-card-value">{{ $property->check_out_time ?? 'Until 12:00' }}</div>
                            <div class="checkin-card-sub">Late check-out may be available</div>
                        </div>
                    </div>
                    @if($property->cancellation_policy)
                        <div style="margin-top:16px;background:#f0fff4;border:1px solid #c6f6d5;border-radius:8px;padding:14px 16px;">
                            <div style="font-size:13px;font-weight:600;color:#276749;margin-bottom:4px;"><i class="fas fa-shield-check"></i> Cancellation Policy</div>
                            <div style="font-size:13px;color:#374151;">
                                @if(($property->cancellation_policy['type'] ?? '') === 'free')
                                    Free cancellation. Cancel before check-in for a full refund.
                                @else
                                    This property has a non-refundable policy.
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Location ─────────────────────── --}}
            <section id="location">
                <div class="section-card">
                    <div class="section-card-header">
                        <h2 class="section-title">Location</h2>
                    </div>
                    <div class="section-card-body">
                        <div class="mb-4">
                            <div style="font-weight:600;color:#d00e15;font-size:15px; margin-bottom: 4px;">{{ $property->full_address }}</div>
                            @if($property->city)
                                <div style="font-size:13px;color:#6b7280;margin-bottom: 12px;">{{ $property->city }}{{ $property->country ? ', ' . $property->country : '' }}</div>
                            @endif
                        </div>
                        <div id="property-map" style="height: 300px; width: 100%; border-radius: 8px; margin-bottom: 20px; background: #e5e7eb;"></div>
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                            @if($property->airport_distance)
                                <div style="text-align:center;background:#f8f9fa;border-radius:8px;padding:12px;">
                                    <i class="fas fa-plane" style="color:#d00e15;margin-bottom:4px;"></i>
                                    <div style="font-size:13px;font-weight:600;color:#1a1a1a;">{{ $property->airport_distance }}</div>
                                    <div style="font-size:11px;color:#6b7280;">to Airport</div>
                                </div>
                            @endif
                            @if($property->beach_distance)
                                <div style="text-align:center;background:#f8f9fa;border-radius:8px;padding:12px;">
                                    <i class="fas fa-umbrella-beach" style="color:#d00e15;margin-bottom:4px;"></i>
                                    <div style="font-size:13px;font-weight:600;color:#1a1a1a;">{{ $property->beach_distance }}</div>
                                    <div style="font-size:11px;color:#6b7280;">to Beach</div>
                                </div>
                            @endif
                            @if($property->city_center_distance)
                                <div style="text-align:center;background:#f8f9fa;border-radius:8px;padding:12px;">
                                    <i class="fas fa-city" style="color:#d00e15;margin-bottom:4px;"></i>
                                    <div style="font-size:13px;font-weight:600;color:#1a1a1a;">{{ $property->city_center_distance }}</div>
                                    <div style="font-size:11px;color:#6b7280;">to City Center</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            {{-- ── Guest Reviews ─────────────────── --}}
            <section id="reviews">
                <div class="section-card">
                    <div class="section-card-header">
                        <h2 class="section-title">Guest Reviews</h2>
                    </div>
                    <div class="section-card-body">

                        @if($reviewStats['average'])
                        <div style="display:grid;grid-template-columns:auto 1fr;gap:24px;margin-bottom:24px;align-items:start;">
                            {{-- Score side --}}
                            <div style="text-align:center;">
                                @php
                                    $avg = $reviewStats['average'];
                                    $scoreClass = $avg >= 9 ? 'exceptional' : ($avg >= 8 ? 'excellent' : ($avg >= 7 ? 'very-good' : 'good'));
                                    $scoreWord = $avg >= 9 ? 'Exceptional' : ($avg >= 8 ? 'Excellent' : ($avg >= 7 ? 'Very Good' : ($avg >= 6 ? 'Good' : 'Pleasant')));
                                @endphp
                                <div class="score-badge excellent" style="width:80px;height:80px;font-size:28px;border-radius:12px 12px 12px 0;margin:0 auto 8px;">
                                    {{ number_format($avg, 1) }}
                                </div>
                                <div style="font-weight:700;font-size:15px;color:#1a1a1a;">{{ $scoreWord }}</div>
                                <div style="font-size:12px;color:#6b7280;">{{ $reviewStats['count'] }} reviews</div>
                            </div>
                            {{-- Score bars --}}
                            <div>
                                @foreach(['cleanliness' => 'Cleanliness', 'location' => 'Location', 'service' => 'Service', 'value' => 'Value for money', 'facilities' => 'Facilities'] as $key => $label)
                                    @if($reviewStats[$key])
                                        <div class="review-score-bar">
                                            <div class="review-score-label">{{ $label }}</div>
                                            <div class="review-score-track">
                                                <div class="review-score-fill" style="width:{{ ($reviewStats[$key] / 10) * 100 }}%"></div>
                                            </div>
                                            <div class="review-score-val">{{ number_format($reviewStats[$key], 1) }}</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Review List --}}
                        @forelse($reviews as $review)
                        <div class="review-card">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div class="review-avatar">
                                        {{ substr($review->guest->name ?? 'G', 0, 1) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:14px;color:#1a1a1a;">{{ $review->guest->name ?? 'Guest' }}</div>
                                        <div style="font-size:12px;color:#6b7280;">{{ $review->created_at->format('M Y') }}</div>
                                    </div>
                                </div>
                                <span class="review-score-inline">{{ number_format($review->overall_score, 1) }}</span>
                            </div>
                            @if($review->comment)
                                <p style="font-size:14px;color:#374151;line-height:1.7;margin:0;">{{ $review->comment }}</p>
                            @endif
                            @if($review->hotel_response)
                                <div style="margin-top:12px;background:#f0f4f8;border-left:3px solid #d00e15;padding:10px 14px;border-radius:0 6px 6px 0;">
                                    <div style="font-size:12px;font-weight:600;color:#d00e15;margin-bottom:4px;"><i class="fas fa-reply"></i> Property response</div>
                                    <p style="font-size:13px;color:#374151;margin:0;">{{ $review->hotel_response }}</p>
                                </div>
                            @endif
                        </div>
                        @empty
                        <div style="text-align:center;padding:32px;color:#6b7280;">
                            <i class="fas fa-star" style="font-size:32px;color:#e4e8ed;margin-bottom:12px;"></i>
                            <p>No reviews yet for this property.</p>
                        </div>
                        @endforelse

                        @if($reviews->hasPages())
                            <div style="margin-top:16px;">{{ $reviews->links() }}</div>
                        @endif
                    </div>
                </div>
            </section>

        </div>{{-- end LEFT --}}

        {{-- ── RIGHT COLUMN: Booking Widget ─── --}}
        <div>
            <div class="booking-widget">
                <div class="widget-title">Starting from</div>
                <div class="widget-price">{{ $currencySymbol }}{{ number_format(($property->lowest_price ?? 0) * $exchangeRate, 0) }} <span>/ night</span></div>

                @if($property->average_rating)
                <div style="display:flex;align-items:center;gap:8px;margin-top:10px;">
                    <span style="background:#d00e15;color:white;font-weight:700;font-size:13px;padding:3px 8px;border-radius:4px;">{{ number_format($property->average_rating, 1) }}</span>
                    <span style="font-size:13px;color:#1a1a1a;font-weight:600;">
                        {{ $property->average_rating >= 9 ? 'Exceptional' : ($property->average_rating >= 8 ? 'Excellent' : ($property->average_rating >= 7 ? 'Very Good' : 'Good')) }}
                    </span>
                    <span style="font-size:12px;color:#6b7280;">· {{ $property->review_count }} reviews</span>
                </div>
                @endif

                <hr class="widget-divider">

                <form action="{{ route('hotels.show', $property) }}" method="GET" class="widget-form" id="availability-form">
                    <div style="font-size:12px;font-weight:600;color:#1a1a1a;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;">Your stay</div>
                    <div class="widget-date-row">
                        <div class="widget-date-block" onclick="document.getElementById('widget-checkin').showPicker()">
                            <div class="widget-date-label">Check-in</div>
                            <input type="date" name="check_in" class="widget-date-input"
                                value="{{ $checkIn->format('Y-m-d') }}"
                                min="{{ now()->format('Y-m-d') }}"
                                id="widget-checkin">
                        </div>
                        <div class="widget-date-block" onclick="document.getElementById('widget-checkout').showPicker()">
                            <div class="widget-date-label">Check-out</div>
                            <input type="date" name="check_out" class="widget-date-input"
                                value="{{ $checkOut->format('Y-m-d') }}"
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                id="widget-checkout">
                        </div>
                    </div>
                    <div class="widget-guests-row">
                        <div class="widget-date-label">Guests</div>
                        <select name="guests" class="widget-guests-select">
                            @for($g = 1; $g <= 10; $g++)
                                <option value="{{ $g }}" {{ $guests == $g ? 'selected' : '' }}>
                                    {{ $g }} {{ $g === 1 ? 'Guest' : 'Guests' }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="btn-check-avail" id="check-avail-btn">
                        <i class="fas fa-search"></i>
                        <span>Check availability</span>
                    </button>
                </form>

                @if($property->cancellation_policy && ($property->cancellation_policy['type'] ?? '') === 'free')
                    <div class="widget-free-cancel">
                        <i class="fas fa-shield-check"></i>
                        <span>Free cancellation available</span>
                    </div>
                @endif

                <hr class="widget-divider">

                {{-- Price breakdown --}}
                @php
                    $firstRoom = $property->activeRoomTypes->first();
                    $firstData = $firstRoom ? ($roomsData[$firstRoom->id] ?? null) : null;
                @endphp
                @if($firstData)
                <div style="font-size:13px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;color:#374151;">
                        <span>{{ $currencySymbol }}{{ number_format($firstData['pricing']['nightly_rate'] * $exchangeRate, 0) }} × {{ $firstData['pricing']['nights'] }} nights</span>
                        <span>{{ $currencySymbol }}{{ number_format($firstData['pricing']['subtotal'] * $exchangeRate, 0) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;color:#374151;">
                        <span>Taxes &amp; fees</span>
                        <span>{{ $currencySymbol }}{{ number_format((($firstData['pricing']['taxes'] ?? 0) + ($firstData['pricing']['fees'] ?? 0)) * $exchangeRate, 0) }}</span>
                    </div>
                    @if(($firstData['pricing']['discount'] ?? 0) > 0)
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;color:#008009;">
                        <span>Discount</span>
                        <span>-{{ $currencySymbol }}{{ number_format($firstData['pricing']['discount'] * $exchangeRate, 0) }}</span>
                    </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;font-weight:700;font-size:16px;color:#1a1a1a;padding-top:10px;border-top:2px solid #1a1a1a;margin-top:10px;">
                        <span>Total</span>
                        <span>{{ $currencySymbol }}{{ number_format($firstData['pricing']['total'] * $exchangeRate, 0) }}</span>
                    </div>
                </div>
                @endif

                <div style="margin-top:16px;font-size:11px;color:#6b7280;text-align:center;">
                    <i class="fas fa-lock"></i> Secure booking — no hidden charges
                </div>
            </div>

            {{-- Trust Signals --}}
            <div style="background:white;border:1px solid var(--border);border-radius:8px;padding:16px;margin-top:16px;">
                <div style="font-size:12px;font-weight:700;color:#1a1a1a;margin-bottom:12px;">Why book here?</div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div style="display:flex;align-items:start;gap:10px;">
                        <i class="fas fa-medal" style="color:#f5a623;margin-top:2px;font-size:14px;"></i>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#1a1a1a;">Lowest price guarantee</div>
                            <div style="font-size:11px;color:#6b7280;">Find it cheaper? We'll match it</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:start;gap:10px;">
                        <i class="fas fa-shield-alt" style="color:#008009;margin-top:2px;font-size:14px;"></i>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#1a1a1a;">Secure & safe booking</div>
                            <div style="font-size:11px;color:#6b7280;">Your data is always protected</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:start;gap:10px;">
                        <i class="fas fa-headset" style="color:#A90B16;margin-top:2px;font-size:14px;"></i>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#1a1a1a;">24/7 customer support</div>
                            <div style="font-size:11px;color:#6b7280;">We're here when you need us</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- end RIGHT --}}

    </div>
</div>

<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lightbox
    GLightbox({ selector: '.glightbox', touchNavigation: true, loop: true });

    // Check-in date constraint
    const checkin = document.getElementById('widget-checkin');
    const checkout = document.getElementById('widget-checkout');
    if (checkin && checkout) {
        checkin.addEventListener('change', function() {
            const minOut = new Date(this.value);
            minOut.setDate(minOut.getDate() + 1);
            checkout.min = minOut.toISOString().split('T')[0];
            if (checkout.value <= this.value) {
                checkout.value = minOut.toISOString().split('T')[0];
            }
        });
    }

    // Active anchor nav on scroll
    const anchors = document.querySelectorAll('.anchor-nav a');
    const sections = [...anchors].map(a => document.querySelector(a.getAttribute('href')));
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                anchors.forEach(a => a.classList.remove('active'));
                const active = [...anchors].find(a => a.getAttribute('href') === '#' + entry.target.id);
                if (active) active.classList.add('active');
            }
        });
    }, { rootMargin: '-30% 0px -60% 0px' });
    sections.forEach(s => s && observer.observe(s));

    // Loading state on availability check
    const form = document.getElementById('availability-form');
    const btn = document.getElementById('check-avail-btn');
    if (form && btn) {
        form.addEventListener('submit', function() {
            btn.innerHTML = '<span class="spinner" style="border-color:#cce;border-top-color:white;"></span> Checking...';
            btn.disabled = true;
        });
    }
});

function initPropertyMap() {
    const lat = {{ $property->latitude ?? '23.8103' }};
    const lng = {{ $property->longitude ?? '90.4125' }};
    const mapElement = document.getElementById('property-map');
    
    if (mapElement) {
        const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
        const map = new google.maps.Map(mapElement, {
            center: position,
            zoom: 15,
            mapTypeControl: false,
            streetViewControl: false,
        });
        
        new google.maps.Marker({
            position: position,
            map: map,
            title: "{{ addslashes($property->name) }}"
        });
    }
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initPropertyMap" async defer></script>
</body>
</html>

