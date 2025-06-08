<template>
    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ translations.admin.categories.edit }}
                </h2>
                <Link
                    :href="route('admin.categories.index')"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105"
                >
                    <ArrowLeftIcon class="-ml-1 mr-2 h-5 w-5 text-indigo-500" aria-hidden="true" />
                    {{ translations.admin.categories.actions.back }}
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-md sm:rounded-lg border border-gray-200">
                    <div class="p-4 sm:p-6 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <div class="flex items-center text-lg font-medium text-indigo-700 mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ translations.admin.categories.edit }}: {{ category.name }}
                        </div>
                        <p class="text-sm text-gray-600">Update category information</p>
                    </div>

                    <div class="p-4 sm:p-6 bg-white">
                        <CategoryForm
                            :category="category"
                            :category-hierarchy="categoryHierarchy"
                            :errors="errors"
                            :is-editing="true"
                            @submit="updateCategory"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/24/solid';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CategoryForm from './Partials/CategoryForm.vue';

const page = usePage();
const defaultTranslations = {
    admin: {
        categories: {
            edit: 'Edit Category',
            actions: {
                back: 'Back'
            }
        }
    }
};

const translations = computed(() => {
    if (page.props.translations && page.props.translations.admin && page.props.translations.admin.categories) {
        return page.props.translations;
    }
    return defaultTranslations;
});

const props = defineProps({
    category: Object,
    categoryHierarchy: Array,
    errors: Object
});

// Update category
const updateCategory = (categoryData) => {
    router.put(route('admin.categories.update', props.category.id), categoryData);
};
</script>
