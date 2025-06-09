<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\OrderServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected OrderServiceInterface $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Show order list.
     */
    public function index(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);

        $orders = $this->orderService->getPaginatedOrders(
            perPage: $request->input('per_page', 15),
            filters: $filters
        );

        return inertia('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $filters
        ]);
    }

    /**
     * Display the new order creation form.
     */
    public function create()
    {
        return inertia('Admin/Orders/Create');
    }

    /**
     * Save new order to database.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'billing_address' => 'required|array',
                'shipping_address' => 'required|array',
                'payment_method_id' => 'required|exists:payment_methods,id',
                'shipping_method_id' => 'required|exists:shipping_methods,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'shipping_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
            ]);

            $order = $this->orderService->createOrder($validatedData);

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Order has been created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating order: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to create order: ' . $e->getMessage());
        }
    }

    /**
     * Display order details.
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'customer', 'status', 'paymentMethod', 'shippingMethod']);

        return inertia('Admin/Orders/Show', [
            'order' => $order
        ]);
    }

    /**
     * Display order edit form.
     */
    public function edit(Order $order)
    {
        $order->load(['items.product', 'customer', 'status', 'paymentMethod', 'shippingMethod']);

        return inertia('Admin/Orders/Edit', [
            'order' => $order
        ]);
    }

    /**
     * Update order information.
     */
    public function update(Request $request, Order $order)
    {
        try {
            $validatedData = $request->validate([
                'billing_address' => 'sometimes|required|array',
                'shipping_address' => 'sometimes|required|array',
                'status' => 'sometimes|required|string',
                'payment_status' => 'sometimes|required|string',
                'shipping_status' => 'sometimes|required|string',
            ]);

            $this->orderService->updateOrder($order, $validatedData);

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Order has been updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating order: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Unable to update order: ' . $e->getMessage());
        }
    }

    /**
     * Delete order.
     */
    public function destroy(Order $order)
    {
        try {
            $this->orderService->deleteOrder($order);

            return redirect()->route('admin.orders.index')
                ->with('success', 'The order was successfully deleted.');
        } catch (\Exception $e) {
            Log::error('Error when deleting order: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Unable to delete order: ' . $e->getMessage());
        }
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        try {
            $validatedData = $request->validate([
                'status' => 'required|string',
            ]);

            $this->orderService->updateOrderStatus($order, $validatedData['status']);

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Order status has been updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Unable to update order status: ' . $e->getMessage());
        }
    }

    /**
     * Process payment for orders.
     */
    public function processPayment(Request $request, Order $order)
    {
        try {
            $validatedData = $request->validate([
                'method' => 'required|string',
                'amount' => 'sometimes|required|numeric',
                'details' => 'sometimes|array',
            ]);

            $result = $this->orderService->processPayment($order, $validatedData);

            if ($result) {
                return redirect()->route('admin.orders.show', $order->id)
                    ->with('success', 'Payment has been processed successfully.');
            } else {
                return redirect()->back()
                    ->with('error', 'Payment could not be processed.');
            }
        } catch (\Exception $e) {
            Log::error('Error processing payment: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Unable to process payment: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order.
     */
    public function cancel(Order $order)
    {
        try {
            $this->orderService->cancelOrder($order);

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Order was successfully canceled.');
        } catch (\Exception $e) {
            Log::error('Error when canceling order: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Order cannot be cancelled: ' . $e->getMessage());
        }
    }

    /**
     * Get filter from request.
     */
    private function getFiltersFromRequest(Request $request): array
    {
        $filters = [];

        if ($request->filled('status')) {
            $filters['status'] = $request->input('status');
        }

        if ($request->filled('payment_status')) {
            $filters['payment_status'] = $request->input('payment_status');
        }

        if ($request->filled('date_from')) {
            $filters['date_from'] = $request->input('date_from');
        }

        if ($request->filled('date_to')) {
            $filters['date_to'] = $request->input('date_to');
        }

        if ($request->filled('customer_id')) {
            $filters['customer_id'] = $request->input('customer_id');
        }

        if ($request->filled('search')) {
            $filters['search'] = $request->input('search');
        }

        return $filters;
    }
}
