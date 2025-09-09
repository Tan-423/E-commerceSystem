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
        // Restore user-based order numbers
        $users = \App\Models\User::with('orders')->get();
        foreach ($users as $user) {
            $userOrders = $user->orders->sortBy('created_at');
            foreach ($userOrders as $index => $order) {
                $order->order_number = $index + 1;
                $order->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration doesn't need a rollback as it's restoring the original behavior
    }
};
