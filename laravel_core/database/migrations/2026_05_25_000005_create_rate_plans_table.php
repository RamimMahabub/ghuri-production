<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('plan_code'); // RO, BB, HB, FB, AI
            $table->string('plan_name'); // Room Only, Bed & Breakfast, etc.
            $table->decimal('price_supplement_per_adult', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['room_type_id', 'plan_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_plans');
    }
};
