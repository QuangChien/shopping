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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique();
            $table->decimal('price', 15, 4)->nullable();
            $table->decimal('special_price', 15, 4)->nullable();
            $table->integer('quantity')->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_default')->default(false);
            $table->enum('status', ['enabled', 'disabled'])->default('enabled');
            $table->timestamps();

            $table->index(['product_id']);
            $table->index(['sku']);
            $table->index(['status']);
        });

        // Variant attribute values - links product variants with attribute options
        Schema::create('variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained('product_attributes')->onDelete('cascade');
            $table->foreignId('attribute_option_id')->constrained('attribute_options')->onDelete('cascade');

            $table->unique(['variant_id', 'attribute_id'], 'unique_variant_attribute');
            $table->index(['variant_id']);
            $table->index(['attribute_id']);
            $table->index(['attribute_option_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_attribute_values');
        Schema::dropIfExists('product_variants');
    }
}; 