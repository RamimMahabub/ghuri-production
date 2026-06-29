<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('nationality')->nullable();
            $table->string('passport_id')->nullable();
            $table->string('profile_photo')->nullable();
            $table->text('notes')->nullable();
            $table->text('preferences')->nullable(); // JSON
            $table->boolean('is_vip')->default(false);
            $table->text('tags')->nullable(); // JSON array: ['VIP', 'Repeat Guest', etc.]
            $table->boolean('marketing_opt_in')->default(false);
            $table->timestamps();

            $table->index('email');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
