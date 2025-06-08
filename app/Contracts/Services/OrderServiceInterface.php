<?php

namespace App\Contracts\Services;

use App\Models\Order;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderServiceInterface
{
    public function getOrderById(int $id): ?Order;
    public function getOrderByNumber(string $orderNumber): ?Order;
    public function getAllOrders(array $filters = []): Collection;
    public function getPaginatedOrders(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function createOrder(array $orderData): Order;
    public function createOrderFromQuote(Quote $quote, array $additionalData = []): Order;
    public function updateOrder(Order $order, array $data): bool;
    public function updateOrderStatus(Order $order, string $statusName): bool;
    public function cancelOrder(Order $order): bool;
    public function deleteOrder(Order $order): bool;
    public function processPayment(Order $order, array $paymentData): bool;
    public function getOrderStatistics(int $days = 30): array;
} 