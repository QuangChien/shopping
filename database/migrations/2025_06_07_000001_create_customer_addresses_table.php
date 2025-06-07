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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->enum('address_type', ['billing', 'shipping']);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('street_address_1');
            $table->string('street_address_2')->nullable();
            $table->string('city', 100);
            $table->string('state_province', 100);
            $table->string('postal_code', 20);
            $table->string('country_code', 2)->default('US');
            $table->string('phone', 20)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['customer_id']);
            $table->index(['address_type']);
            $table->index(['is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
}; 