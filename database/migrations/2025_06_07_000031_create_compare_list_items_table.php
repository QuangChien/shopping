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
        Schema::create('compare_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compare_list_id')->constrained('compare_lists')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['compare_list_id', 'product_id'], 'unique_compare_product');
            $table->index('compare_list_id', 'idx_compare_items_list');
            $table->index('product_id', 'idx_compare_items_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compare_list_items');
    }
};
