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
            $table->index(['id_user', 'status', 'created_at'], 'orders_customer_status_created_idx');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->index('id_order', 'order_details_order_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_customer_status_created_idx');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropIndex('order_details_order_idx');
        });
    }
};
