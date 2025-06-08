<?php

namespace App\Contracts\Services;

use App\Models\Order;

interface PaymentServiceInterface
{
    public function processPayment(Order $order, array $paymentData): bool;
    public function refundPayment(Order $order, float $amount = null): bool;
    public function getPaymentMethods(): array;
    public function validatePaymentMethod(string $methodCode): bool;
    public function getPaymentGateway(string $methodCode): ?object;
} 