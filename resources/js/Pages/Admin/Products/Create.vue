<template>
    <AdminLayout>
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create New Product
            </h2>
            <Link
                :href="route('admin.products.index')"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                <ArrowLeftIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                Back to List
            </Link>
        </div>

        <div class="py-6">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <ProductForm
                            :form="form"
                            :errors="form.errors"
                            :categories="categoryHierarchy"
                            :attributes="attributes"
                            :product-types="productTypes"
                            :visibility-options="visibilityOptions"
                            :status-options="statusOptions"
                            @submit="submit"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';
import AdminLayout from '@/Pages/Admin/Components/AdminLayout.vue';
import ProductForm from './Partials/ProductForm.vue';

const props = defineProps({
    categoryHierarchy: {
        type: Array,
        default: () => [],
    },
    attributes: {
        type: Array,
        default: () => [],
    },
    productTypes: {
        type: Array,
        default: () => [],
    },
    visibilityOptions: {
        type: Array,
        default: () => [],
    },
    statusOptions: {
        type: Array,
        default: () => [],
    },
});

// Initialize form with default values
const form = useForm({
    sku: '',
    type_id: 'simple',
    status: 'disabled',
    visibility: 'catalog_search',
    tax_class_id: null,
    category_ids: [],
    attributes: {
        name: '',
        description: '',
        price: '',
        weight: '',
        // Additional attributes can be added dynamically
    }
});

const submit = () => {
    form.post(route('admin.products.store'), {
        onSuccess: () => {
            // Clear form data after successful submission
            form.reset();
        },
    });
};
</script> 