<template>
    <AdminLayout>
        <Head :title="translations.dashboard.title" />

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">{{ translations.dashboard.title }}</h1>
            <p class="mt-2 text-gray-600">{{ translations.dashboard.welcome }}</p>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <StatCard
                :title="translations.dashboard.stats.total_customers"
                :value="stats.totalCustomers"
                icon="customer"
                color="blue"
            />
            <StatCard
                :title="translations.dashboard.stats.total_orders"
                :value="stats.totalOrders"
                icon="order"
                color="green"
            />
            <StatCard
                :title="translations.dashboard.stats.total_products"
                :value="stats.totalProducts"
                icon="product"
                color="purple"
            />
        </div>

        <!-- Recent Orders -->
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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="order in stats.recentOrders" :key="order.id">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">#{{ order.id }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ order.customer.first_name }} {{ order.customer.last_name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">${{ order.grand_total }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                <span :class="`inline-flex rounded-full px-2 text-xs font-semibold leading-5 ${getStatusColor(order.status)}`">
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from './Components/AdminLayout.vue';
import StatCard from './Components/StatCard.vue';

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
            total_customers: 'Total Customers',
            total_orders: 'Total Orders',
            total_products: 'Total Products',
            recent_orders: 'Recent Orders'
        }
    }
};

const translations = computed(() => {
    if (page.props.translations && page.props.translations.admin) {
        return {
            dashboard: {
                title: page.props.translations.admin.dashboard?.title || 'Dashboard',
                welcome: page.props.translations.admin.dashboard?.welcome || 'Welcome to Admin Dashboard',
                stats: {
                    total_customers: page.props.translations.admin.dashboard?.stats?.total_customers || 'Total Customers',
                    total_orders: page.props.translations.admin.dashboard?.stats?.total_orders || 'Total Orders',
                    total_products: page.props.translations.admin.dashboard?.stats?.total_products || 'Total Products',
                    recent_orders: page.props.translations.admin.dashboard?.stats?.recent_orders || 'Recent Orders'
                }
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
</script>
