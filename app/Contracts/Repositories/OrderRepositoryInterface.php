<?php

namespace App\Contracts\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function find(int $id): ?Order;
    public function findByOrderNumber(string $orderNumber): ?Order;
    public function getAll(array $filters = []): Collection;
    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function create(array $data): Order;
    public function update(Order $order, array $data): bool;
    public function delete(Order $order): bool;
    public function getByStatus(string $status, int $limit = null): Collection;
    public function getByCustomer(int $customerId, array $filters = []): Collection;
    public function getRecentOrders(int $days = 30, int $limit = null): Collection;
} 