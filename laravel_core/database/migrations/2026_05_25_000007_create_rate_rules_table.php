<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('rule_type'); // weekend_surcharge, seasonal, occupancy, last_minute, early_bird, long_stay
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('adjustment_type')->default('percent'); // percent, flat
            $table->decimal('adjustment_value', 10, 2)->default(0);
            $table->text('condition_value')->nullable(); // JSON — e.g. { min_nights: 5, days_before: 7 }
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['room_type_id', 'rule_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_rules');
    }
};
