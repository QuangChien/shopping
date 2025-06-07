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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group_name', 100);
            $table->string('key');
            $table->longText('value')->nullable();
            $table->enum('type', ['string', 'text', 'number', 'boolean', 'json'])->default('string');
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->unique(['group_name', 'key'], 'unique_group_key');
            $table->index(['group_name']);
            $table->index(['is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
}; 