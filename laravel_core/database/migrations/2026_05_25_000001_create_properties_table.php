<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('hotel'); // hotel, resort, villa, hostel, apartment, guesthouse
            $table->integer('stars')->default(3);
            $table->string('short_description', 255)->nullable();
            $table->text('full_description')->nullable();
            $table->string('check_in_time')->default('14:00');
            $table->string('check_out_time')->default('12:00');
            $table->text('languages_spoken')->nullable(); // JSON array
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('airport_distance')->nullable();
            $table->string('beach_distance')->nullable();
            $table->string('city_center_distance')->nullable();
            $table->text('amenities')->nullable(); // JSON object grouped by category
            $table->text('cancellation_policy')->nullable(); // JSON
            $table->string('children_policy')->nullable();
            $table->string('pet_policy')->nullable();
            $table->string('payment_policy')->nullable();
            $table->text('extra_bed_policy')->nullable();
            $table->text('early_checkin_policy')->nullable();
            $table->text('late_checkout_policy')->nullable();
            $table->string('cover_photo')->nullable();
            $table->string('status')->default('draft'); // draft, pending_approval, approved, rejected, suspended
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('city');
            $table->index('country');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
