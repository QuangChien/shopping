<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\Admin\AuthService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected AuthService $authService;

    /**
     * Khởi tạo controller với dependency injection
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Hiển thị trang dashboard
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
        return [
            'totalCustomers' => Customer::count(),
            'totalOrders' => Order::count(),
            'totalProducts' => Product::count(),
            'recentOrders' => Order::with('customer')
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }
}
