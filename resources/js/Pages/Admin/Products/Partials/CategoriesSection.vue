<template>
    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center border-b pb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Categories
        </h3>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Select Categories
            </label>
            <p class="text-xs text-gray-500 mb-4">
                Select one or more categories for this product.
            </p>

            <div class="space-y-2 max-h-80 overflow-y-auto p-2 border border-gray-200 rounded-md bg-white">
                <div v-if="categories.length === 0" class="text-gray-500 text-sm p-2">
                    No categories available. Please create categories first.
                </div>
                
                <template v-for="category in categories" :key="category.id">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input
                                :id="`category-${category.id}`"
                                type="checkbox"
                                :value="category.id"
                                v-model="form.category_ids"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            />
                        </div>
                        <div class="ml-3 text-sm">
                            <label :for="`category-${category.id}`" class="font-medium text-gray-700">
                                {{ getCategoryPrefix(category.level) + category.name }}
                            </label>
                        </div>
                    </div>
                    
                    <!-- Render child categories with indentation -->
                    <template v-if="category.children && category.children.length > 0">
                        <CategoryChildrenCheckboxes 
                            :categories="category.children" 
                            :form="form"
                        />
                    </template>
                </template>
            </div>
            
            <div v-if="errors.category_ids" class="mt-1 text-sm text-red-600 flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ errors.category_ids }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';
import CategoryChildrenCheckboxes from './CategoryChildrenCheckboxes.vue';

const props = defineProps({
    form: Object,
    errors: Object,
    categories: {
        type: Array,
        default: () => [],
    },
});

// Helper to add prefix based on category level
const getCategoryPrefix = (level) => {
    return level > 0 ? '—'.repeat(level) + ' ' : '';
};
</script> 