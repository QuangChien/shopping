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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('name', 255)->default('My Wishlist');
            $table->boolean('is_default')->default(true);
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index('customer_id', 'idx_wishlists_customer');
            $table->index('is_default', 'idx_wishlists_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
