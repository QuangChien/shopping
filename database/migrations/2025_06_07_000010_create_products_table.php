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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 100)->unique();
            $table->foreignId('tax_class_id')->nullable()->constrained('product_tax_classes')->onDelete('set null');
            $table->enum('status', ['enabled', 'disabled'])->default('disabled');
            $table->enum('visibility', ['not_visible', 'catalog', 'search', 'catalog_search'])->default('catalog_search');
            $table->enum('type_id', ['simple', 'configurable', 'grouped', 'virtual', 'bundle', 'downloadable'])->default('simple');
            $table->timestamps();

            $table->index(['sku']);
            $table->index(['status']);
            $table->index(['visibility']);
            $table->index(['type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}; 