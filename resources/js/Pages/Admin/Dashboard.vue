<template>
    <AdminLayout>
        <Head :title="translations.dashboard.title" />

        <div class="mb-8 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-800">{{ translations.dashboard.title }}</h1>
            <p class="mt-2 text-gray-600">{{ translations.dashboard.welcome }}</p>
        </div>

        <!-- General statistics -->
        <div class="mb-8 grid grid-cols-1 gap-6 px-4 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
            <StatCard
                :title="translations.dashboard.stats.total_customers"
                :value="stats.totalCustomers"
                icon="customer"
                color="blue"
                :trend="stats.customersTrend"
                :subtitle="translations.dashboard.stats.vs_last_month"
            />
            <StatCard
                :title="translations.dashboard.stats.total_orders"
                :value="stats.totalOrders"
                icon="order"
                color="green"
                :trend="stats.ordersTrend"
                :subtitle="translations.dashboard.stats.vs_last_month"
            />
            <StatCard
                :title="translations.dashboard.stats.total_products"
                :value="stats.totalProducts"
                icon="product"
                color="purple"
            />
            <StatCard
                :title="translations.dashboard.stats.total_categories || 'Total category'"
                :value="stats.totalCategories"
                icon="category"
                color="indigo"
            />
        </div>

        <!-- Charts and Revenue -->
        <div class="mb-8 grid grid-cols-1 gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
            <!-- Order chart last 7 days -->
            <div class="lg:col-span-2 rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-4 text-xl font-bold text-gray-800">{{ translations.dashboard.stats.orders_last_7_days || 'Đơn hàng 7 ngày qua' }}</h2>
                <div class="h-80">
                    <OrdersChart
                        v-if="stats.ordersChart"
                        :chart-data="stats.ordersChart"
                    />
                </div>
            </div>

            <!-- Revenue this month -->
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-4 text-xl font-bold text-gray-800">{{ translations.dashboard.stats.revenue || 'Doanh thu' }}</h2>
                <div class="mb-6">
                    <StatCard
                        :title="translations.dashboard.stats.revenue_this_month || 'Doanh thu tháng này'"
                        :value="stats.revenueThisMonth"
                        icon="revenue"
                        color="green"
                        :trend="stats.revenueTrend"
                        :subtitle="translations.dashboard.stats.vs_last_month"
                        :is-currency="true"
                    />
                </div>

                <!-- Sort orders by status -->
                <div class="mt-8">
                    <h3 class="mb-3 text-lg font-semibold text-gray-700">{{ translations.dashboard.stats.orders_by_status || 'Đơn hàng theo trạng thái' }}</h3>
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
                <h2 class="mb-4 text-xl font-bold text-gray-800">{{ translations.dashboard.stats.top_products || 'Top products' }}</h2>
                <div class="overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="(product, index) in stats.topProducts" :key="product.id" class="py-3 flex items-center">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                {{ index + 1 }}
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900">{{ product.name }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ product.order_count }} {{ translations.dashboard.stats.orders || 'orders' }}
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Top categories -->
            <div class="rounded-lg bg-white p-6 shadow-md">
                <h2 class="mb-4 text-xl font-bold text-gray-800">{{ translations.dashboard.stats.top_categories || 'Top danh mục' }}</h2>
                <div class="overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="(category, index) in stats.topCategories" :key="category.id" class="py-3 flex items-center">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-purple-100 text-purple-600">
                                {{ index + 1 }}
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-sm font-medium text-gray-900">{{ category.name }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ category.order_count }} {{ translations.dashboard.stats.orders || 'orders' }}
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
                <h2 class="mb-4 text-xl font-bold text-gray-800">{{ translations.dashboard.stats.recent_orders }}</h2>
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
                                        {{ translations.dashboard.view_details || 'Detail' }}
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
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatCard from './Components/StatCard.vue';
import OrdersChart from './Components/OrdersChart.vue';

const props = defineProps({
    stats: Object,
    auth: Object
});

const page = usePage();
const defaultTranslations = {
    dashboard: {
        title: 'Dashboard',
        welcome: 'Welcome to Admin Dashboard',
        stats: {
            total_customers: 'Total customers',
            total_orders: 'Total orders',
            total_products: 'Total products',
            total_categories: 'Total categories',
            revenue: 'Revenue',
            revenue_this_month: 'Revenue this month',
            recent_orders: 'Recent Orders',
            orders_by_status: 'Order by status',
            vs_last_month: 'Compared to last month',
            top_products: 'Top products',
            top_categories: 'Top categories',
            orders: 'orders',
            orders_last_7_days: 'Orders last 7 days'
        },
        view_details: 'Detail'
    }
};

const translations = computed(() => {
    if (page.props.translations && page.props.translations.admin) {
        return {
            dashboard: {
                title: page.props.translations.admin.dashboard?.title || defaultTranslations.dashboard.title,
                welcome: page.props.translations.admin.dashboard?.welcome || defaultTranslations.dashboard.welcome,
                stats: {
                    total_customers: page.props.translations.admin.dashboard?.stats?.total_customers || defaultTranslations.dashboard.stats.total_customers,
                    total_orders: page.props.translations.admin.dashboard?.stats?.total_orders || defaultTranslations.dashboard.stats.total_orders,
                    total_products: page.props.translations.admin.dashboard?.stats?.total_products || defaultTranslations.dashboard.stats.total_products,
                    total_categories: page.props.translations.admin.dashboard?.stats?.total_categories || defaultTranslations.dashboard.stats.total_categories,
                    revenue: page.props.translations.admin.dashboard?.stats?.revenue || defaultTranslations.dashboard.stats.revenue,
                    revenue_this_month: page.props.translations.admin.dashboard?.stats?.revenue_this_month || defaultTranslations.dashboard.stats.revenue_this_month,
                    recent_orders: page.props.translations.admin.dashboard?.stats?.recent_orders || defaultTranslations.dashboard.stats.recent_orders,
                    orders_by_status: page.props.translations.admin.dashboard?.stats?.orders_by_status || defaultTranslations.dashboard.stats.orders_by_status,
                    vs_last_month: page.props.translations.admin.dashboard?.stats?.vs_last_month || defaultTranslations.dashboard.stats.vs_last_month,
                    top_products: page.props.translations.admin.dashboard?.stats?.top_products || defaultTranslations.dashboard.stats.top_products,
                    top_categories: page.props.translations.admin.dashboard?.stats?.top_categories || defaultTranslations.dashboard.stats.top_categories,
                    orders: page.props.translations.admin.dashboard?.stats?.orders || defaultTranslations.dashboard.stats.orders,
                    orders_last_7_days: page.props.translations.admin.dashboard?.stats?.orders_last_7_days || defaultTranslations.dashboard.stats.orders_last_7_days
                },
                view_details: page.props.translations.admin.dashboard?.view_details || defaultTranslations.dashboard.view_details
            }
        };
    }
    return defaultTranslations;
});

function getStatusColor(status) {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'processing':
            return 'bg-blue-100 text-blue-800';
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}
</script>
