<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., Deluxe Double, Superior King, Junior Suite
            $table->integer('size_sqm')->nullable();
            $table->string('floor_level')->nullable();
            $table->integer('max_adults')->default(2);
            $table->integer('max_children')->default(0);
            $table->integer('max_infants')->default(0);
            $table->text('bed_config')->nullable(); // JSON: [{type: 'king', count: 1}]
            $table->text('amenities')->nullable(); // JSON array
            $table->decimal('base_price_per_night', 10, 2);
            $table->integer('inventory_count')->default(1);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();

            $table->index('property_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
