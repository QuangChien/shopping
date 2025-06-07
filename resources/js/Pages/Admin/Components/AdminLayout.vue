<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div :class="[
            sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in',
            'fixed inset-y-0 left-0 z-30 w-64 transform overflow-y-auto bg-gray-900 p-4 transition duration-300 lg:static lg:inset-0 lg:translate-x-0'
        ]">
            <div class="flex justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl font-bold text-white">Shop Admin</span>
                </div>
                <button
                    @click="sidebarOpen = false"
                    class="text-white focus:outline-none lg:hidden"
                >
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                        <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>

            <nav class="mt-8">
                <SidebarLink :href="route('admin.dashboard')" :label="translations.nav.dashboard" icon="dashboard" />
                <SidebarLink href="#" :label="translations.nav.products" icon="product" />
                <SidebarLink href="#" :label="translations.nav.categories" icon="category" />
                <SidebarLink href="#" :label="translations.nav.orders" icon="order" />
                <SidebarLink href="#" :label="translations.nav.customers" icon="customer" />
                <SidebarLink href="#" :label="translations.nav.settings" icon="setting" />
            </nav>
        </div>

        <!-- Content -->
        <div class="flex flex-1 flex-col overflow-hidden">
            <!-- Header -->
            <header class="flex items-center justify-between border-b bg-white p-4">
                <div class="flex items-center">
                    <button
                        @click="sidebarOpen = true"
                        class="text-gray-500 focus:outline-none lg:hidden"
                    >
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                            <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex items-center space-x-4">
                    <LanguageSwitcher />

                    <div class="relative">
                        <button
                            @click="logout"
                            type="button"
                            class="flex items-center space-x-2 rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                        >
                            <svg class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm9 12V8.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L8 8.414V15.5a.5.5 0 001 0z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ translations.auth.logout }}</span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4">
                <slot></slot>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import SidebarLink from './SidebarLink.vue';

const page = usePage();
const defaultTranslations = {
    auth: {
        logout: 'Logout'
    },
    nav: {
        dashboard: 'Dashboard',
        products: 'Products',
        categories: 'Categories',
        orders: 'Orders',
        customers: 'Customers',
        settings: 'Settings'
    }
};

const translations = computed(() => {
    if (page.props.translations && page.props.translations.admin) {
        return {
            auth: {
                logout: page.props.translations.admin.auth?.logout || 'Logout'
            },
            nav: {
                dashboard: page.props.translations.admin.nav?.dashboard || 'Dashboard',
                products: page.props.translations.admin.nav?.products || 'Products',
                categories: page.props.translations.admin.nav?.categories || 'Categories',
                orders: page.props.translations.admin.nav?.orders || 'Orders',
                customers: page.props.translations.admin.nav?.customers || 'Customers',
                settings: page.props.translations.admin.nav?.settings || 'Settings'
            }
        };
    }
    return defaultTranslations;
});

const sidebarOpen = ref(false);

const logout = () => {
    router.post('/admin/logout');
};
</script>
