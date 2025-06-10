<template>
    <form @submit.prevent="submitForm" class="bg-white rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column - Basic Information -->
            <div class="col-span-1 md:col-span-2">
                <BasicInfoSection
                    :form="form"
                    :errors="errors"
                    :product-types="productTypes"
                    :visibility-options="visibilityOptions"
                    :status-options="statusOptions"
                />

                <AttributesSection
                    :form="form"
                    :errors="errors"
                    :attributes="attributes"
                />

                <!-- Media Section -->
                <MediaSection
                    :form="form"
                    :errors="errors"
                />

                <!-- Inventory Section - only for simple products -->
                <InventorySection
                    :form="form"
                    :errors="errors"
                />

                <!-- Variants Section - only for configurable products -->
                <VariantsSection
                    :form="form"
                    :errors="errors"
                    :attributes="attributes"
                />
            </div>

            <!-- Right Column - Categories -->
            <div class="col-span-1">
                <CategoriesSection
                    :form="form"
                    :errors="errors"
                    :categories="categories"
                />
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex justify-end">
            <Link
                :href="route('admin.products.index')"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3"
            >
                Cancel
            </Link>
            <button
                type="submit"
                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                :class="{ 'opacity-75 cursor-wait': form.processing }"
                :disabled="form.processing"
            >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isEdit ? 'Update Product' : 'Create Product' }}
            </button>
        </div>
    </form>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import BasicInfoSection from './BasicInfoSection.vue';
import AttributesSection from './AttributesSection.vue';
import CategoriesSection from './CategoriesSection.vue';
import VariantsSection from './VariantsSection.vue';
import MediaSection from './MediaSection.vue';
import InventorySection from './InventorySection.vue';

const props = defineProps({
    form: Object,
    errors: Object,
    categories: {
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
    isEdit: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['submit']);

const submitForm = () => {
    emit('submit');
};
</script> 