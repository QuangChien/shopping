<template>
    <AdminLayout>
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Product List
            </h2>
            <Link
                :href="route('admin.products.create')"
                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105"
            >
                <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                Create New
            </Link>
        </div>

        <div class="py-6">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-md sm:rounded-lg border border-gray-200">
                    <!-- Filters Bar with Gradient Background -->
                    <div class="p-6 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-700 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            All Products
                        </h3>

                        <!-- Filter and Search -->
                        <div class="grid gap-4 md:grid-cols-4">
                            <div class="col-span-2">
                                <label for="search" class="sr-only">Search Products</label>
                                <div class="relative mt-1 rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <MagnifyingGlassIcon class="h-5 w-5 text-indigo-500" aria-hidden="true" />
                                    </div>
                                    <input
                                        type="text"
                                        name="search"
                                        id="search"
                                        v-model="searchInput"
                                        @focus="handleInputChange"
                                        class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="Search products"
                                    />
                                </div>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select
                                    id="status"
                                    name="status"
                                    v-model="statusFilter"
                                    @change="handleInputChange"
                                    class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">All</option>
                                    <option value="enabled">Enabled</option>
                                    <option value="disabled">Disabled</option>
                                </select>
                            </div>
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                                <select
                                    id="category_id"
                                    name="category_id"
                                    v-model="categoryFilter"
                                    @change="handleInputChange"
                                    class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">All</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Products Stats -->
                        <div class="mb-6 flex flex-wrap gap-4">
                            <div class="flex items-center bg-blue-50 p-3 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-blue-700">Total Products</div>
                                    <div class="text-2xl font-bold text-blue-900">{{ products.total }}</div>
                                </div>
                            </div>
                            <div class="flex items-center bg-green-50 p-3 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-green-700">Active Products</div>
                                    <div class="text-2xl font-bold text-green-900">
                                        {{ products.data.filter(p => p.status === 'enabled').length }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Loading Indicator -->
                        <LoadingIndicator
                            v-if="isLoading"
                            overlay
                            text="Loading..."
                            color="indigo"
                        >
                            <!-- Products Table -->
                            <ProductsTable :products="products.data" @delete="confirmDelete" />
                        </LoadingIndicator>

                        <!-- Table without loading -->
                        <div v-else>
                            <ProductsTable v-if="products.data.length > 0" :products="products.data" @delete="confirmDelete" />
                            <div v-else class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-gray-500 text-lg">No products found</span>
                                    <span class="text-gray-400 text-sm mt-1">Try changing your search criteria or create a new product</span>
                                    <Link
                                        :href="route('admin.products.create')"
                                        class="mt-4 inline-flex items-center rounded-md border border-transparent bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    >
                                        <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                        Create Product
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <Pagination :links="products.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete this product?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    This action cannot be undone.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <SecondaryButton @click="showDeleteModal = false">Cancel</SecondaryButton>
                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': processing }"
                        :disabled="processing"
                        @click="deleteProduct"
                    >
                        Delete Product
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { PlusIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import AdminLayout from '@/Pages/Admin/Components/AdminLayout.vue';
import LoadingIndicator from '@/Components/LoadingIndicator.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ProductsTable from './Components/ProductsTable.vue';
import { debounce } from 'lodash';

const props = defineProps({
    products: Object,
    filters: Object,
    categories: Array,
});

const isLoading = ref(false);
const showDeleteModal = ref(false);
const productToDelete = ref(null);
const processing = ref(false);

// Filter refs
const searchInput = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const categoryFilter = ref(props.filters.category_id || '');

// Debounced search handler
const debouncedSearch = debounce(() => {
    if (searchInput.value !== props.filters.search) {
        applyFilters();
    }
}, 500);

// Watch for search input changes
watch(searchInput, (value) => {
    debouncedSearch();
});

// Apply filters function
const applyFilters = () => {
    isLoading.value = true;
    router.get(
        route('admin.products.index'),
        {
            search: searchInput.value,
            status: statusFilter.value,
            category_id: categoryFilter.value,
        },
        {
            preserveState: true,
            replace: true,
            onSuccess: () => {
                isLoading.value = false;
            },
        }
    );
};

const handleInputChange = () => {
    applyFilters();
};

// Delete confirmation
const confirmDelete = (product) => {
    productToDelete.value = product;
    showDeleteModal.value = true;
};

// Delete product
const deleteProduct = () => {
    if (!productToDelete.value) return;
    
    processing.value = true;
    router.delete(route('admin.products.destroy', productToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            productToDelete.value = null;
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        },
    });
};
</script> 