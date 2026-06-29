<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('available_rooms')->nullable(); // null = use inventory_count
            $table->integer('blocked_rooms')->default(0);
            $table->decimal('price_override', 10, 2)->nullable();
            $table->integer('min_stay')->default(1);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->unique(['room_type_id', 'date']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability');
    }
};
