<template>
    <AdminLayout>
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-2xl font-semibold leading-tight">
                    Edit Category
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
                        <h1 class="text-2xl font-bold text-gray-700 mb-6">
                            Edit Category: {{ category.name }}
                        </h1>

                        <CategoryForm
                            :form="form"
                            :errors="form.errors"
                            :category="category"
                            :category-hierarchy="categoryHierarchy"
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
import { Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon } from '@heroicons/vue/24/solid';
import AdminLayout from '@/Pages/Admin/Components/AdminLayout.vue';
import CategoryForm from './Partials/CategoryForm.vue';

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
    categoryHierarchy: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: props.category.name || '',
    slug: props.category.slug || '',
    description: props.category.description || '',
    parent_id: props.category.parent_id || null,
    sort_order: props.category.sort_order || 0,
    is_active: props.category.is_active === undefined ? true : props.category.is_active,
    meta_title: props.category.meta_title || '',
    meta_description: props.category.meta_description || '',
    image: props.category.image || '',
});

const submit = () => {
    form.patch(route('admin.categories.update', props.category.id));
};
</script>
