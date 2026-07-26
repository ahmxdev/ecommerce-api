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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('stars');
            $table->text('comment')->nullable();
            $table->unique(['product_id', 'user_id']);
            $table->timestamps();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE reviews
                ADD CONSTRAINT chk_stars
                CHECK (stars BETWEEN 1 AND 5)
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
                ALTER TABLE reviews
                DROP CHECK chk_stars
            ");
        }
        Schema::dropIfExists('reviews');
    }
};
