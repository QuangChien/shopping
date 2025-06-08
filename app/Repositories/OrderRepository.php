<?php

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function find(int $id): ?Order
    {
        return Order::with(['items', 'status', 'customer', 'paymentMethod', 'shippingMethod'])->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return Order::with(['items', 'status', 'customer', 'paymentMethod', 'shippingMethod'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function getAll(array $filters = []): Collection
    {
        $query = $this->applyFilters(Order::query(), $filters);

        return $query->with(['status', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->applyFilters(Order::query(), $filters);

        return $query->with(['status', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Order
    {
        // If there is no status_id, use the default status (pending)
        if (!isset($data['status_id'])) {
            $defaultStatus = OrderStatus::where('is_default', true)->first();
            $data['status_id'] = $defaultStatus->id ?? null;
        }

        return Order::create($data);
    }

    public function update(Order $order, array $data): bool
    {
        return $order->update($data);
    }

    public function delete(Order $order): bool
    {
        return $order->delete();
    }

    public function getByStatus(string $status, int $limit = null): Collection
    {
        $query = Order::whereHas('status', function ($query) use ($status) {
            $query->where('name', $status);
        });

        if ($limit) {
            $query->limit($limit);
        }

        return $query->with(['status', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByCustomer(int $customerId, array $filters = []): Collection
    {
        $query = $this->applyFilters(
            Order::where('customer_id', $customerId),
            $filters
        );

        return $query->with(['status'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRecentOrders(int $days = 30, int $limit = null): Collection
    {
        $query = Order::where('created_at', '>=', now()->subDays($days));

        if ($limit) {
            $query->limit($limit);
        }

        return $query->with(['status', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function applyFilters($query, array $filters): \Illuminate\Database\Eloquent\Builder
    {
        if (isset($filters['status'])) {
            $query->whereHas('status', function ($q) use ($filters) {
                $q->where('name', $filters['status']);
            });
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        return $query;
    }
}
