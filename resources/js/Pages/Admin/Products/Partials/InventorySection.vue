<template>
    <div class="mb-8" v-if="!isConfigurableProduct">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center border-b pb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Inventory Management
        </h3>

        <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
            <!-- Quantity -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12h10l-3 -3m0 6l3 -3" />
                    </svg>
                    Quantity
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input
                        type="number"
                        id="quantity"
                        v-model="form.inventory.quantity"
                        min="0"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                        placeholder="0"
                        :class="{ 'border-red-500 ring-red-500': getInventoryError('quantity') }"
                    />
                </div>
                <div v-if="getInventoryError('quantity')" class="mt-1 text-sm text-red-600 flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ getInventoryError('quantity') }}
                </div>
            </div>

            <!-- Min Quantity (for stock alerts) -->
            <div>
                <label for="min_quantity" class="block text-sm font-medium text-gray-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                    </svg>
                    Min Stock Level
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input
                        type="number"
                        id="min_quantity"
                        v-model="form.inventory.min_quantity"
                        min="0"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                        placeholder="0"
                        :class="{ 'border-red-500 ring-red-500': getInventoryError('min_quantity') }"
                    />
                </div>
                <div v-if="getInventoryError('min_quantity')" class="mt-1 text-sm text-red-600 flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ getInventoryError('min_quantity') }}
                </div>
                <div class="mt-1 text-xs text-gray-500">
                    You'll be notified when stock falls below this level
                </div>
            </div>

            <!-- Stock Management -->
            <div class="col-span-1 sm:col-span-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="manage_stock"
                            v-model="form.inventory.manage_stock"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        />
                        <label for="manage_stock" class="ml-2 block text-sm text-gray-900">
                            Manage Stock
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="is_in_stock"
                            v-model="form.inventory.is_in_stock"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        />
                        <label for="is_in_stock" class="ml-2 block text-sm text-gray-900">
                            In Stock
                        </label>
                    </div>
                </div>
                <div class="mt-1 text-xs text-gray-500">
                    When "Manage Stock" is enabled, stock will be tracked automatically based on orders
                </div>
            </div>

            <!-- Backorders Setting -->
            <div class="col-span-1 sm:col-span-2" v-if="form.inventory.manage_stock">
                <label for="backorders" class="block text-sm font-medium text-gray-700">
                    Backorders
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <select
                        id="backorders"
                        v-model="form.inventory.backorders"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                        :class="{ 'border-red-500 ring-red-500': getInventoryError('backorders') }"
                    >
                        <option value="no">Do not allow</option>
                        <option value="yes">Allow</option>
                        <option value="notify">Allow, but notify customer</option>
                    </select>
                </div>
                <div v-if="getInventoryError('backorders')" class="mt-1 text-sm text-red-600 flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ getInventoryError('backorders') }}
                </div>
                <div class="mt-1 text-xs text-gray-500">
                    Controls whether customers can place orders when a product is out of stock
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    form: Object,
    errors: Object,
});

// Initialize inventory object if it doesn't exist
if (!props.form.inventory) {
    props.form.inventory = {
        quantity: 0,
        min_quantity: 0,
        is_in_stock: true,
        manage_stock: true,
        backorders: 'no'
    };
}

// Check if product is configurable (inventory will be managed at variant level for configurable products)
const isConfigurableProduct = computed(() => {
    return props.form.type_id === 'configurable';
});

// Helper to get errors for inventory fields
const getInventoryError = (field) => {
    if (props.errors && props.errors.inventory && props.errors.inventory[field]) {
        return props.errors.inventory[field];
    }
    return null;
};
</script> 