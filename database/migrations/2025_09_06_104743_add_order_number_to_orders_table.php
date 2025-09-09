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
            $table->string('order_number')->nullable()->after('id');
        });
        
        // Update existing orders with order numbers per user
        $users = \App\Models\User::with('orders')->get();
        foreach ($users as $user) {
            $userOrders = $user->orders->sortBy('created_at');
            foreach ($userOrders as $index => $order) {
                $order->order_number = $index + 1;
                $order->save();
            }
        }
        
        // Now make it not nullable (but not unique since multiple users can have same order numbers)
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });
    }
};
