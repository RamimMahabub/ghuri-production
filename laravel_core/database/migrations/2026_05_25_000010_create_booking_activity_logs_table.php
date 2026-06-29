<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_booking_id')->constrained()->cascadeOnDelete();
            $table->string('action'); // booked, confirmed, modified, cancelled, checked_in, checked_out, note_added
            $table->text('description')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('hotel_booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_activity_logs');
    }
};
