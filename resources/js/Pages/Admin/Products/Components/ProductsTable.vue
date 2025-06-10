<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        ID / SKU
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 hidden md:table-cell">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 hidden lg:table-cell">
                        Categories
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 hidden sm:table-cell">
                        Visibility
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="(product, index) in products" :key="product.id"
                    class="transition-colors hover:bg-indigo-50/50"
                    :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'"
                >
                    <!-- ID & SKU -->
                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-indigo-700 w-24">
                        <div class="font-bold">#{{ product.id }}</div>
                        <div class="text-xs text-gray-500 font-mono">{{ product.sku }}</div>
                    </td>
                    
                    <!-- Name -->
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 text-indigo-700 rounded-md flex items-center justify-center mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ getProductName(product) }}
                                </div>
                                <div v-if="getProductDescription(product)" class="text-xs text-gray-500 truncate max-w-xs">
                                    {{ getProductDescription(product) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Type -->
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 hidden md:table-cell">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800">
                            {{ formatProductType(product.type_id) }}
                        </span>
                    </td>
                    
                    <!-- Categories -->
                    <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="category in product.categories"
                                :key="category.id"
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800"
                            >
                                {{ category.name }}
                            </span>
                            <span v-if="product.categories.length === 0" class="text-gray-400">-</span>
                        </div>
                    </td>
                    
                    <!-- Status -->
                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="product.status === 'enabled'
                                ? 'bg-green-100 text-green-800 border border-green-200'
                                : 'bg-red-100 text-red-800 border border-red-200'"
                        >
                            <span class="mr-1 h-1.5 w-1.5 rounded-full" :class="product.status === 'enabled' ? 'bg-green-500' : 'bg-red-500'"></span>
                            {{ product.status === 'enabled' ? 'Enabled' : 'Disabled' }}
                        </span>
                    </td>
                    
                    <!-- Visibility -->
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 hidden sm:table-cell">
                        <span class="bg-gray-100 px-2 py-1 rounded text-xs">
                            {{ formatVisibility(product.visibility) }}
                        </span>
                    </td>
                    
                    <!-- Actions -->
                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <Link
                                :href="route('admin.products.edit', product.id)"
                                class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors flex items-center"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </Link>
                            <button
                                @click="$emit('delete', product)"
                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors flex items-center"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    products: {
        type: Array,
        required: true
    }
});

// Format the product type to be more readable
const formatProductType = (type) => {
    const types = {
        'simple': 'Simple',
        'configurable': 'Configurable',
        'grouped': 'Grouped',
        'virtual': 'Virtual',
        'bundle': 'Bundle',
        'downloadable': 'Downloadable'
    };
    
    return types[type] || type;
};

// Format the visibility status
const formatVisibility = (visibility) => {
    const visibilities = {
        'not_visible': 'Not Visible',
        'catalog': 'Catalog',
        'search': 'Search',
        'catalog_search': 'Catalog & Search'
    };
    
    return visibilities[visibility] || visibility;
};

// Get product name from attributes if available
const getProductName = (product) => {
    if (product.attribute_values && product.attribute_values.name) {
        return product.attribute_values.name.value;
    }
    return `Product #${product.id}`;
};

// Get product description from attributes if available
const getProductDescription = (product) => {
    if (product.attribute_values && product.attribute_values.description) {
        return product.attribute_values.description.value;
    }
    return '';
};

defineEmits(['delete']);
</script> 