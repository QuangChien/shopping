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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 100)->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('status_id')->constrained('order_statuses');
            $table->string('currency_code', 3)->default('USD');
            $table->decimal('subtotal', 12, 4)->default(0);
            $table->decimal('tax_amount', 12, 4)->default(0);
            $table->decimal('discount_amount', 12, 4)->default(0);
            $table->decimal('shipping_amount', 12, 4)->default(0);
            $table->decimal('total', 12, 4)->default(0);
            $table->json('billing_address');
            $table->json('shipping_address')->nullable();
            $table->foreignId('shipping_method_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('payment_method_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded', 'partially_refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('ordered_at')->useCurrent();
            $table->timestamps();

            $table->index(['order_number']);
            $table->index(['customer_id']);
            $table->index(['status_id']);
            $table->index(['payment_status']);
            $table->index(['ordered_at']);
            $table->index(['total']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}; 