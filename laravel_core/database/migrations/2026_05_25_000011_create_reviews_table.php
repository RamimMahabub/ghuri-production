<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->decimal('overall_score', 3, 1); // 1.0 to 10.0
            $table->decimal('cleanliness_score', 3, 1)->nullable();
            $table->decimal('location_score', 3, 1)->nullable();
            $table->decimal('service_score', 3, 1)->nullable();
            $table->decimal('value_score', 3, 1)->nullable();
            $table->decimal('facilities_score', 3, 1)->nullable();
            $table->text('comment')->nullable();
            $table->text('hotel_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->string('status')->default('published'); // published, flagged, hidden
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('property_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
