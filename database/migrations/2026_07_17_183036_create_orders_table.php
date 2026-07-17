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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->decimal('final_price', 8, 2);
            $table->decimal('discount_amount', 8, 2);
            $table->enum('status', [
                'pending',
                'preparing',
                'delivered',
                'cancelled',
            ])->index();
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code', 20)->nullable();

            $table->string('shipping_country');
            $table->string('shipping_state');
            $table->string('shipping_city');
            $table->string('shipping_district');
            $table->string('shipping_street');
            $table->string('shipping_building');

            $table->string('shipping_floor')->nullable();
            $table->string('shipping_apartment')->nullable();
            $table->string('shipping_landmark')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
