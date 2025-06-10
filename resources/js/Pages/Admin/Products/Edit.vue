<template>
    <AdminLayout>
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Product: {{ getProductName(product) }}
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
                            :is-edit="true"
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
    product: {
        type: Object,
        required: true,
    },
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

// Get product name from attributes if available
const getProductName = (product) => {
    if (product.attributeValues && product.attributeValues.name) {
        return product.attributeValues.name.value;
    }
    return `Product #${product.id}`;
};

// Extract category IDs from product categories
const categoryIds = props.product.categories ? props.product.categories.map(cat => cat.id) : [];

// Prepare attribute values from product attribute values
const attributeValues = {};
if (props.product.attributeValues) {
    Object.entries(props.product.attributeValues).forEach(([code, data]) => {
        attributeValues[code] = data.value;
    });
}

// Initialize form with product data
const form = useForm({
    sku: props.product.sku || '',
    type_id: props.product.type_id || 'simple',
    status: props.product.status || 'disabled',
    visibility: props.product.visibility || 'catalog_search',
    tax_class_id: props.product.tax_class_id || null,
    category_ids: categoryIds,
    attributes: attributeValues
});

const submit = () => {
    form.put(route('admin.products.update', props.product.id), {
        onSuccess: () => {
            // Form submitted successfully
        },
    });
};
</script> 