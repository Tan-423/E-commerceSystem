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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('global_order_number')->nullable()->after('order_number');
        });
        
        // Populate global_order_number for existing orders
        $orders = \App\Models\Order::orderBy('created_at')->get();
        foreach ($orders as $index => $order) {
            $order->global_order_number = $index + 1;
            $order->save();
        }
        
        // Make it not nullable after populating
        Schema::table('orders', function (Blueprint $table) {
            $table->string('global_order_number')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('global_order_number');
        });
    }
};
