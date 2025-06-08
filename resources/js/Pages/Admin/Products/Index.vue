<template>
    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ translations.admin.products.index }}
                </h2>
                <Link
                    :href="route('admin.products.create')"
                    class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                    {{ translations.admin.products.actions.create }}
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1>Products List</h1>
                        <p>This is a placeholder for the products list. Implementation coming soon.</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { PlusIcon } from '@heroicons/vue/24/solid';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const page = usePage();
const defaultTranslations = {
    admin: {
        products: {
            index: 'Products List',
            title: 'Products',
            create: 'Create Product',
            edit: 'Edit Product',
            actions: {
                create: 'Create New',
                edit: 'Edit',
                delete: 'Delete',
                save: 'Save',
                cancel: 'Cancel',
                back: 'Back',
            },
        }
    }
};

const translations = computed(() => {
    if (page.props.translations && page.props.translations.admin && page.props.translations.admin.products) {
        return page.props.translations;
    }
    return defaultTranslations;
});
</script> 