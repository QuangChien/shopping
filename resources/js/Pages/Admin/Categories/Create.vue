<template>
    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-2xl font-semibold leading-tight">
                    Create Category
                </h2>
                <Link
                    :href="route('admin.categories.index')"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <ArrowLeftIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                    Back to List
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-2xl font-bold text-gray-700 mb-6">Create Category</h1>

                        <CategoryForm
                            :form="form"
                            :errors="form.errors"
                            :category-hierarchy="categoryHierarchy"
                            @submit="submit"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/solid';
import AdminLayout from '@/Pages/Admin/Components/AdminLayout.vue';
import CategoryForm from './Partials/CategoryForm.vue';

const props = defineProps({
    categoryHierarchy: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: '',
    slug: '',
    description: '',
    parent_id: null,
    sort_order: 0,
    is_active: true,
    meta_title: '',
    meta_description: '',
    image: '',
});

const submit = () => {
    form.post(route('admin.categories.store'));
};
</script>
