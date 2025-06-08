<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Services\Admin\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Services\OrderService;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderUpdateRequest;

class DashboardController extends Controller
{
    protected AuthService $authService;
    protected OrderService $orderService;

    /**
     * Initialize controller with dependency injection
     */
    public function __construct(AuthService $authService, OrderService $orderService)
    {
        $this->authService = $authService;
        $this->orderService = $orderService;
    }

    /**
     * Display dashboard page
     */
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => $this->getStats(),
            'auth' => [
                'user' => $this->authService->getAuthenticatedUser(),
            ],
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    private function getStats()
    {
        // Get current time and first day of month
        $now = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        // Get monthly order data
        $ordersThisMonth = Order::whereBetween('created_at', [$startOfMonth, $now])->count();
        $ordersLastMonth = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $ordersTrend = $ordersLastMonth > 0
            ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100, 1)
            : 100;

        // Revenue this month and last month
        $revenueThisMonth = Order::whereBetween('created_at', [$startOfMonth, $now])
            ->whereHas('status', function($query) {
                $query->where('name', 'completed');
            })
            ->sum('total');
        $revenueLastMonth = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->whereHas('status', function($query) {
                $query->where('name', 'completed');
            })
            ->sum('total');
        $revenueTrend = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 100;

        // New customers this month
        $newCustomersThisMonth = Customer::whereBetween('created_at', [$startOfMonth, $now])->count();
        $newCustomersLastMonth = Customer::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $customersTrend = $newCustomersLastMonth > 0
            ? round((($newCustomersThisMonth - $newCustomersLastMonth) / $newCustomersLastMonth) * 100, 1)
            : 100;

        // Sort orders by status
        $ordersByStatus = Order::select('order_statuses.name', DB::raw('count(*) as count'))
            ->join('order_statuses', 'orders.status_id', '=', 'order_statuses.id')
            ->groupBy('order_statuses.name')
            ->get()
            ->pluck('count', 'name')
            ->toArray();

        // Top 5 best selling products
        $topProducts = Product::select('products.id', 'products.sku', DB::raw('COUNT(*) as order_count'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id', 'products.sku')
            ->orderBy('order_count', 'desc')
            ->limit(5)
            ->get();

        // Top 5 categories
        $topCategories = Category::select('categories.id', 'categories.name', DB::raw('COUNT(DISTINCT orders.id) as order_count'))
            ->join('product_categories', 'categories.id', '=', 'product_categories.category_id')
            ->join('products', 'products.id', '=', 'product_categories.product_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('order_count', 'desc')
            ->limit(5)
            ->get();

        // 7 Day Order Chart
        $last7Days = collect(range(0, 6))->map(function ($i) {
            return Carbon::now()->subDays($i)->format('Y-m-d');
        });

        $ordersLast7Days = Order::whereBetween('created_at', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        $chartData = $last7Days->mapWithKeys(function ($date) use ($ordersLast7Days) {
            return [$date => $ordersLast7Days[$date] ?? 0];
        })->toArray();

        return [
            'totalCustomers' => Customer::count(),
            'totalOrders' => Order::count(),
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
            'recentOrders' => Order::with('customer')
                ->latest()
                ->limit(5)
                ->get(),
            'ordersTrend' => $ordersTrend,
            'revenueTrend' => $revenueTrend,
            'customersTrend' => $customersTrend,
            'revenueThisMonth' => $revenueThisMonth,
            'ordersByStatus' => $ordersByStatus,
            'topProducts' => $topProducts,
            'topCategories' => $topCategories,
            'ordersChart' => [
                'labels' => array_keys($chartData),
                'data' => array_values($chartData)
            ]
        ];
    }
}
