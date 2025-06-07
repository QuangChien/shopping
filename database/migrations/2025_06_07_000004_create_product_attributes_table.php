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
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->enum('backend_type', ['varchar', 'text', 'int', 'decimal', 'datetime', 'boolean']);
            $table->enum('frontend_input', ['text', 'textarea', 'select', 'multiselect', 'boolean', 'date', 'datetime', 'price', 'weight', 'media_image']);
            $table->string('frontend_label');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_unique')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_searchable')->default(false);
            $table->boolean('is_comparable')->default(false);
            $table->boolean('is_visible_on_front')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('default_value')->nullable();
            $table->string('frontend_class')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['code']);
            $table->index(['backend_type']);
            $table->index(['is_filterable']);
            $table->index(['is_searchable']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
}; 