<?php

namespace App\Services;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\PaymentServiceInterface;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService implements OrderServiceInterface
{
    protected OrderRepositoryInterface $orderRepository;
    protected InventoryServiceInterface $inventoryService;
    protected PaymentServiceInterface $paymentService;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        InventoryServiceInterface $inventoryService,
        PaymentServiceInterface $paymentService
    ) {
        $this->orderRepository = $orderRepository;
        $this->inventoryService = $inventoryService;
        $this->paymentService = $paymentService;
    }

    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->find($id);
    }

    public function getOrderByNumber(string $orderNumber): ?Order
    {
        return $this->orderRepository->findByOrderNumber($orderNumber);
    }

    public function getAllOrders(array $filters = []): Collection
    {
        return $this->orderRepository->getAll($filters);
    }

    public function getPaginatedOrders(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->orderRepository->getPaginated($perPage, $filters);
    }

    public function createOrder(array $orderData): Order
    {
        try {
            DB::beginTransaction();

            // Create order
            $order = $this->orderRepository->create($orderData);

            // Thêm các mặt hàng vào đơn hàng
            if (isset($orderData['items']) && is_array($orderData['items'])) {
                foreach ($orderData['items'] as $itemData) {
                    $this->addOrderItem($order, $itemData);
                }
            }

            // Update total amount
            $this->recalculateOrderTotals($order);

            // Check inventory
            $this->checkInventoryAvailability($order);

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order: ' . $e->getMessage(), [
                'order_data' => $orderData,
                'exception' => $e
            ]);

            throw new OrderException('Unable to create order: ' . $e->getMessage());
        }
    }

    public function createOrderFromQuote(Quote $quote, array $additionalData = []): Order
    {
        try {
            DB::beginTransaction();

            // Basic data for orders from quotes
            $orderData = [
                'customer_id' => $quote->customer_id,
                'billing_address' => $quote->billing_address,
                'shipping_address' => $quote->shipping_address,
                'payment_method_id' => $quote->payment_method_id,
                'shipping_method_id' => $quote->shipping_method_id,
                'subtotal' => $quote->subtotal,
                'tax_amount' => $quote->tax_amount,
                'shipping_amount' => $quote->shipping_amount,
                'discount_amount' => $quote->discount_amount,
                'total' => $quote->total,
            ];

            // Merge with additional data
            $orderData = array_merge($orderData, $additionalData);

            // Create order
            $order = $this->orderRepository->create($orderData);

            // Add items from quote
            foreach ($quote->items as $quoteItem) {
                $itemData = [
                    'product_id' => $quoteItem->product_id,
                    'product_sku' => $quoteItem->product->sku,
                    'product_name' => $quoteItem->product->name,
                    'quantity' => $quoteItem->quantity,
                    'price' => $quoteItem->price,
                    'row_total' => $quoteItem->row_total,
                    'product_options' => $quoteItem->product_options,
                ];

                $this->addOrderItem($order, $itemData);
            }

            // Update quote
            $quote->update(['is_active' => false, 'converted_to_order' => true]);

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order from quote: ' . $e->getMessage(), [
                'quote_id' => $quote->id,
                'additional_data' => $additionalData,
                'exception' => $e
            ]);

            throw new OrderException('Unable to create order from quote: ' . $e->getMessage());
        }
    }

    public function updateOrder(Order $order, array $data): bool
    {
        try {
            DB::beginTransaction();

            $result = $this->orderRepository->update($order, $data);

            // Nếu có cập nhật trạng thái
            if (isset($data['status']) && $data['status'] !== $order->status->name) {
                $this->updateOrderStatus($order, $data['status']);
            }

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update order: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'data' => $data,
                'exception' => $e
            ]);

            throw new OrderException('Unable to update order: ' . $e->getMessage());
        }
    }

    public function updateOrderStatus(Order $order, string $statusName): bool
    {
        try {
            DB::beginTransaction();

            $status = OrderStatus::where('name', $statusName)->first();

            if (!$status) {
                throw new OrderException("Invalid order status: {$statusName}");
            }

            // Save old state to test changes
            $oldStatus = $order->status->name;

            // Update status
            $order->status_id = $status->id;
            $order->save();

            // Handling state related actions
            switch ($statusName) {
                case 'shipped':
                    $order->update(['shipping_status' => 'shipped', 'shipped_at' => now()]);
                    $this->inventoryService->reserveStock($order);
                    break;

                case 'delivered':
                    $order->update(['shipping_status' => 'delivered', 'delivered_at' => now()]);
                    $this->inventoryService->reduceStock($order);
                    break;

                case 'cancelled':
                    if ($oldStatus === 'shipped' || $oldStatus === 'delivered') {
                        throw new OrderException("Orders that have been shipped cannot be cancelled.");
                    }
                    $this->inventoryService->releaseStock($order);
                    break;
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update order status: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'status' => $statusName,
                'exception' => $e
            ]);

            throw new OrderException('Unable to update order status: ' . $e->getMessage());
        }
    }

    public function cancelOrder(Order $order): bool
    {
        if (!$order->canBeCancelled()) {
            throw new OrderException("This order cannot be cancelled.");
        }

        return $this->updateOrderStatus($order, 'cancelled');
    }

    public function deleteOrder(Order $order): bool
    {
        try {
            DB::beginTransaction();

            // Clear inventory if needed
            if ($order->status->name !== 'cancelled' && $order->status->name !== 'delivered') {
                $this->inventoryService->releaseStock($order);
            }

            $result = $this->orderRepository->delete($order);

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete order: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'exception' => $e
            ]);

            throw new OrderException('Unable to delete order: ' . $e->getMessage());
        }
    }

    public function processPayment(Order $order, array $paymentData): bool
    {
        try {
            $result = $this->paymentService->processPayment($order, $paymentData);

            if ($result) {
                $order->update(['payment_status' => 'paid']);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Payment processing failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'payment_data' => $paymentData,
                'exception' => $e
            ]);

            $order->update(['payment_status' => 'failed']);

            throw new OrderException('Payment processing failed: ' . $e->getMessage());
        }
    }

    public function getOrderStatistics(int $days = 30): array
    {
        // Get current time and first day of month
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $lastMonth = $now->copy()->subMonth();
        $startOfLastMonth = $lastMonth->copy()->startOfMonth();
        $endOfLastMonth = $lastMonth->copy()->endOfMonth();

        // Get monthly order data
        $ordersThisMonth = Order::whereBetween('created_at', [$startOfMonth, $now])->count();
        $ordersLastMonth = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $ordersTrend = $ordersLastMonth > 0
            ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100, 1)
            : 100;

        // Revenue this month and last month
        $revenueThisMonth = Order::whereBetween('created_at', [$startOfMonth, $now])
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['completed', 'delivered']);
            })
            ->sum('total');
        $revenueLastMonth = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['completed', 'delivered']);
            })
            ->sum('total');
        $revenueTrend = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 100;

        // Sort orders by status
        $ordersByStatus = Order::select('order_statuses.name', DB::raw('count(*) as count'))
            ->join('order_statuses', 'orders.status_id', '=', 'order_statuses.id')
            ->groupBy('order_statuses.name')
            ->get()
            ->pluck('count', 'name')
            ->toArray();

        // 7 Day Order Chart
        $last7Days = collect(range(0, 6))->map(function ($i) {
            return now()->subDays($i)->format('Y-m-d');
        });

        $ordersLast7Days = Order::whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        $chartData = $last7Days->mapWithKeys(function ($date) use ($ordersLast7Days) {
            return [$date => $ordersLast7Days[$date] ?? 0];
        })->toArray();

        return [
            'totalOrders' => Order::count(),
            'recentOrders' => Order::with('customer', 'status')
                ->latest()
                ->limit(5)
                ->get(),
            'ordersTrend' => $ordersTrend,
            'revenueTrend' => $revenueTrend,
            'revenueThisMonth' => $revenueThisMonth,
            'ordersByStatus' => $ordersByStatus,
            'ordersChart' => [
                'labels' => array_keys($chartData),
                'data' => array_values($chartData)
            ]
        ];
    }

    // Helper methods

    private function addOrderItem(Order $order, array $itemData): OrderItem
    {
        return $order->items()->create($itemData);
    }

    private function recalculateOrderTotals(Order $order): void
    {
        // Recalculate subtotal based on items
        $subtotal = $order->items->sum('row_total');

        // Update order
        $order->update([
            'subtotal' => $subtotal,
            'total' => $subtotal + $order->tax_amount + $order->shipping_amount - $order->discount_amount
        ]);
    }

    private function checkInventoryAvailability(Order $order): void
    {
        foreach ($order->items as $item) {
            $available = $this->inventoryService->checkAvailability($item->product_id, $item->quantity);

            if (!$available) {
                throw new OrderException("Product '{$item->product_name}' không đủ số lượng trong kho");
            }
        }
    }
}
