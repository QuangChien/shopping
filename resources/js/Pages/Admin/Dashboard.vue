<template>
    <AdminLayout>
        <Head title="Dashboard" />

        <div class="mb-8 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
            <p class="mt-2 text-gray-600">Welcome to Admin Dashboard</p>
        </div>

        <!-- General statistics -->
        <div class="mb-8 grid grid-cols-1 gap-6 px-4 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
            <StatCard
                title="Total customers"
                :value="stats.totalCustomers"
                icon="customer"
                color="blue"
                :trend="stats.customersTrend"
                subtitle="Compared to last month"
            />
            <StatCard
                title="Total orders"
                :value="stats.totalOrders"
                icon="order"
                color="green"
                :trend="stats.ordersTrend"
                subtitle="Compared to last month"
            />
            <StatCard
                title="Total products"
                :value="stats.totalProducts"
                icon="product"
                color="purple"
            />
            <StatCard
                title="Total categories"
                :value="stats.totalCategories"
                icon="category"
                color="indigo"
            />
        </div>

        <!-- Charts and Revenue -->
        <div class="mb-8 grid grid-cols-1 gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
            <!-- Order chart last 7 days -->
            <div class="lg:col-span-2 rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-4 text-xl font-bold text-gray-800">Orders last 7 days</h2>
                <div class="h-80">
                    <OrdersChart
                        v-if="stats.ordersChart"
                        :chart-data="stats.ordersChart"
                    />
                </div>
            </div>

            <!-- Revenue this month -->
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-4 text-xl font-bold text-gray-800">Revenue</h2>
                <div class="mb-6">
                    <StatCard
                        title="Revenue this month"
                        :value="stats.revenueThisMonth"
                        icon="revenue"
                        color="green"
                        :trend="stats.revenueTrend"
                        subtitle="Compared to last month"
                        :is-currency="true"
                    />
                </div>

                <!-- Sort orders by status -->
                <div class="mt-8">
                    <h3 class="mb-3 text-lg font-semibold text-gray-700">Orders by status</h3>
                    <div class="space-y-4">
                        <div v-for="(count, status) in stats.ordersByStatus" :key="status" class="flex items-center">
                            <span :class="`inline-flex rounded-full w-3 h-3 mr-2 ${getStatusColor(status).replace('text-', 'bg-')}`"></span>
                            <span class="flex-1 capitalize">{{ status }}</span>
                            <span class="font-medium">{{ count }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products and categories -->
        <div class="mb-8 grid grid-cols-1 gap-6 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
            <!-- Top products -->
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-4 text-xl font-bold text-gray-800">Top products</h2>
                <div class="overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="(product, index) in stats.topProducts" :key="product.id" class="py-3 flex items-center">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                {{ index + 1 }}
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900">{{ product.name }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ product.order_count }} orders
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Top categories -->
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-4 text-xl font-bold text-gray-800">Top categories</h2>
                <div class="overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="(category, index) in stats.topCategories" :key="category.id" class="py-3 flex items-center">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-600">
                                {{ index + 1 }}
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900">{{ category.name }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ category.order_count }} orders
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="px-4 sm:px-6 lg:px-8 mb-8">
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-4 text-xl font-bold text-gray-800">Recent Orders</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="order in stats.recentOrders" :key="order.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">#{{ order.id }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ order.customer.first_name }} {{ order.customer.last_name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 font-medium">
                                    {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'USD' }).format(order.total) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    <span :class="`inline-flex rounded-full px-2 text-xs font-semibold leading-5 ${getStatusColor(order.status)}`">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ formatDate(order.created_at) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <a href="#" class="text-blue-600 hover:text-blue-900">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Pages/Admin/Components/AdminLayout.vue';
import StatCard from './Components/StatCard.vue';
import OrdersChart from './Components/OrdersChart.vue';

const props = defineProps({
    stats: Object,
    auth: Object
});

/**
 * Format date for display
 */
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

/**
 * Get color class for order status
 */
function getStatusColor(status) {
    const colors = {
        'pending': 'text-yellow-800 bg-yellow-100',
        'processing': 'text-blue-800 bg-blue-100',
        'completed': 'text-green-800 bg-green-100',
        'cancelled': 'text-red-800 bg-red-100',
        'shipped': 'text-purple-800 bg-purple-100',
        'delivered': 'text-indigo-800 bg-indigo-100'
    };

    return colors[status] || 'text-gray-800 bg-gray-100';
}
</script>
