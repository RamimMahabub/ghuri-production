<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->string('type')->default('promo_code'); // promo_code, flash_deal, package
            $table->string('discount_type')->default('percent'); // percent, flat
            $table->decimal('discount_value', 10, 2);
            $table->text('applies_to')->nullable(); // JSON: room type IDs or "all"
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->integer('min_nights')->default(1);
            $table->integer('max_usage_total')->nullable();
            $table->integer('max_usage_per_guest')->nullable();
            $table->boolean('is_single_use')->default(false);
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['property_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
