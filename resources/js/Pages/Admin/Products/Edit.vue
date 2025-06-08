<template>
    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ translations.admin.products.edit }}
                </h2>
                <Link
                    :href="route('admin.products.index')"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <ArrowLeftIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                    {{ translations.admin.products.actions.back }}
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1>Edit Product: {{ product.name }}</h1>
                        <p>This is a placeholder for the product edit form. Implementation coming soon.</p>
                        
                        <div class="mt-4">
                            <h2 class="text-lg font-medium">Product Details</h2>
                            <div class="mt-2">
                                <p><strong>ID:</strong> {{ product.id }}</p>
                                <p><strong>Name:</strong> {{ product.name }}</p>
                                <p><strong>Description:</strong> {{ product.description }}</p>
                                <p><strong>Price:</strong> ${{ product.price }}</p>
                                <p><strong>Status:</strong> {{ product.is_active ? 'Active' : 'Inactive' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/24/solid';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const page = usePage();
const defaultTranslations = {
    admin: {
        products: {
            edit: 'Edit Product',
            actions: {
                back: 'Back to List'
            }
        }
    }
};

const translations = computed(() => {
    if (page.props.translations && page.props.translations.admin && page.props.translations.admin.products) {
        return page.props.translations;
    }
    return defaultTranslations;
});

const props = defineProps({
    product: Object,
    errors: Object
});
</script> 