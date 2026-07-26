<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 8, 2); // CHECK (price >= 0)
            $table->integer('stock'); // CHECK (stock >= 0)
            $table->text('description')->nullable();
            $table->foreignId('brand_id')->constrained('brands')->restrictOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
            ALTER TABLE products
            ADD CONSTRAINT chk_price
            CHECK (price >= 0)
            ");

            DB::statement("
            ALTER TABLE products
            ADD CONSTRAINT chk_stock
            CHECK (stock >= 0)
            ");
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE products
                DROP CHECK chk_price
            ");

            DB::statement("
                ALTER TABLE products
                DROP CHECK chk_stock
            ");
        }
        Schema::dropIfExists('products');
    }
};
