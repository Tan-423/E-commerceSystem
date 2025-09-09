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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('card_number', 16)->nullable()->after('mode');
            $table->string('card_expiry', 5)->nullable()->after('card_number');
            $table->string('card_cvv', 3)->nullable()->after('card_expiry');
            $table->string('card_holder_name', 100)->nullable()->after('card_cvv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['card_number', 'card_expiry', 'card_cvv', 'card_holder_name']);
        });
    }
};
