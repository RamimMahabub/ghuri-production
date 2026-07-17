<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Older installations already had support_tickets.booking_id pointing to
        // the flight bookings table. Support tickets use hotel bookings instead.
        if (DB::getDriverName() !== 'mysql' || ! Schema::hasColumn('support_tickets', 'booking_id')) {
            return;
        }

        $foreignKey = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'support_tickets')
            ->where('COLUMN_NAME', 'booking_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->first(['CONSTRAINT_NAME', 'REFERENCED_TABLE_NAME']);

        if ($foreignKey?->REFERENCED_TABLE_NAME === 'hotel_bookings') {
            return;
        }

        if ($foreignKey) {
            $constraint = str_replace('`', '``', $foreignKey->CONSTRAINT_NAME);
            DB::statement("ALTER TABLE `support_tickets` DROP FOREIGN KEY `{$constraint}`");
        }

        Schema::table('support_tickets', function (Blueprint $table) {
            $table->foreign('booking_id')
                ->references('id')
                ->on('hotel_bookings')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // This migration repairs an invalid legacy constraint. Restoring it would
        // make property-owner support requests fail again.
    }
};
