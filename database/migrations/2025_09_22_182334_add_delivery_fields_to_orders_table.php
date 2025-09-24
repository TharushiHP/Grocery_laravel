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
            $table->string('status')->default('pending');
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('delivery_address', 500)->nullable();
            $table->string('delivery_city', 100)->nullable();
            $table->string('delivery_postal_code', 10)->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'total_amount',
                'delivery_address',
                'delivery_city', 
                'delivery_postal_code',
                'customer_name',
                'customer_phone'
            ]);
        });
    }
};
