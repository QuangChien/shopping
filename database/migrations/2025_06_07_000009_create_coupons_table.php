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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['fixed_cart', 'percent_cart', 'fixed_product', 'percent_product']);
            $table->decimal('amount', 12, 4);
            $table->decimal('minimum_amount', 12, 4)->nullable();
            $table->decimal('maximum_amount', 12, 4)->nullable();
            $table->integer('usage_limit_per_coupon')->nullable();
            $table->integer('usage_limit_per_customer')->nullable();
            $table->integer('used_count')->default(0);
            $table->boolean('individual_use')->default(false);
            $table->boolean('exclude_sale_items')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['code']);
            $table->index(['is_active']);
            $table->index(['starts_at', 'expires_at']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
