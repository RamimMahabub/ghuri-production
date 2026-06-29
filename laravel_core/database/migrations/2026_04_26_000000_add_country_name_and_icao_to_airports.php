<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('airports', function (Blueprint $table) {
            $table->string('icao_code', 4)->nullable()->after('iata_code')->index();
            $table->string('country_name')->nullable()->after('country')->index();
        });
    }

    public function down(): void
    {
        Schema::table('airports', function (Blueprint $table) {
            $table->dropColumn(['icao_code', 'country_name']);
        });
    }
};
