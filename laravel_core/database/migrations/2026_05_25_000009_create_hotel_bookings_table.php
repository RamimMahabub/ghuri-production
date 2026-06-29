<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_ref')->unique();
            $table->foreignId('guest_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rate_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->integer('infants')->default(0);
            $table->integer('rooms_booked')->default(1);
            $table->decimal('nightly_rate', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('taxes', 10, 2)->default(0);
            $table->decimal('fees', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending, confirmed, checked_in, checked_out, cancelled, no_show
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded, partial_refund
            $table->string('payment_method')->nullable();
            $table->string('source')->default('direct'); // direct, ota, walk_in
            $table->text('special_requests')->nullable();
            $table->string('estimated_arrival')->nullable();
            $table->string('promo_code_used')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('check_in');
            $table->index('check_out');
            $table->index('property_id');
            $table->index('guest_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
    }
};
