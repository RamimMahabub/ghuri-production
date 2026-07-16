<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('bank_details')->nullable();
            $table->json('card_details')->nullable();
        });

        Schema::table('payouts', function (Blueprint $table) {
            $table->string('payment_proof')->nullable();
            $table->string('bank_invoice')->nullable();
            $table->string('platform_invoice')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bank_details', 'card_details']);
        });

        Schema::table('payouts', function (Blueprint $table) {
            $table->dropColumn(['payment_proof', 'bank_invoice', 'platform_invoice']);
        });
    }
};
