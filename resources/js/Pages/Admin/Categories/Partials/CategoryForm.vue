<template>
    <form @submit.prevent="submitForm" class="bg-white rounded-lg">
        <!-- Basic information -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center border-b pb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Basic information
            </h3>

            <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                <!-- Category name -->
                <div class="col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                        Name <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input
                            type="text"
                            id="name"
                            name="name"
                            v-model="form.name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            placeholder="Enter category name"
                            :class="{ 'border-red-500 ring-red-500': errors.name }"
                        />
                    </div>
                    <div v-if="errors.name" class="mt-1 text-sm text-red-600 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ errors.name }}
                    </div>
                </div>

                <!-- Slug -->
                <div class="col-span-1">
                    <label for="slug" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        Slug
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input
                            type="text"
                            id="slug"
                            name="slug"
                            v-model="form.slug"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            placeholder="Enter slug or leave empty to auto-generate"
                            :class="{ 'border-red-500 ring-red-500': errors.slug }"
                        />
                    </div>
                    <div v-if="errors.slug" class="mt-1 text-sm text-red-600 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ errors.slug }}
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        Leave blank to auto generate from category name
                    </div>
                </div>

                <!-- Parent category -->
                <div class="col-span-1">
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Parent Category
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <select
                            id="parent_id"
                            name="parent_id"
                            v-model="form.parent_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            :class="{ 'border-red-500 ring-red-500': errors.parent_id }"
                        >
                            <option value="">No Parent Category</option>
                            <option
                                v-for="cat in categoryOptions"
                                :key="cat.id"
                                :value="cat.id"
                                :disabled="isEdit && cat.id === category.id"
                                :class="getCategoryOptionClass(cat.level)"
                            >
                                {{ getCategoryPrefix(cat.level) + cat.name }}
                            </option>
                        </select>
                    </div>
                    <div v-if="errors.parent_id" class="mt-1 text-sm text-red-600 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ errors.parent_id }}
                    </div>
                </div>

                <!-- Arrange -->
                <div class="col-span-1">
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                        Sort Order
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input
                            type="number"
                            id="sort_order"
                            name="sort_order"
                            min="0"
                            v-model="form.sort_order"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            placeholder="Enter sort order"
                            :class="{ 'border-red-500 ring-red-500': errors.sort_order }"
                        />
                    </div>
                    <div v-if="errors.sort_order" class="mt-1 text-sm text-red-600 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ errors.sort_order }}
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        The smaller the number, the earlier the category appears.
                    </div>
                </div>
            </div>
        </div>

        <!-- Display information -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center border-b pb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Display information
            </h3>

            <div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
                <!-- Image -->
                <div class="col-span-1">
                    <ImageUpload
                        v-model="form.image"
                        name="image"
                        label="Image"
                        folder="categories"
                        :error="errors.image"
                    />
                </div>

                <!-- Status -->
                <div class="col-span-1">
                    <label for="is_active" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Status
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <select
                            id="is_active"
                            name="is_active"
                            v-model="form.is_active"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            :class="{ 'border-red-500 ring-red-500': errors.is_active }"
                        >
                            <option :value="true">Active</option>
                            <option :value="false">Inactive</option>
                        </select>
                    </div>
                    <div v-if="errors.is_active" class="mt-1 text-sm text-red-600 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ errors.is_active }}
                    </div>
                </div>

                <!-- Meta title -->
                <div class="col-span-1">
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Meta Title
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input
                            type="text"
                            id="meta_title"
                            name="meta_title"
                            v-model="form.meta_title"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            placeholder="Enter meta title"
                            :class="{ 'border-red-500 ring-red-500': errors.meta_title }"
                        />
                    </div>
                    <div v-if="errors.meta_title" class="mt-1 text-sm text-red-600 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ errors.meta_title }}
                    </div>
                </div>

                <!--Describe -->
                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        Description
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            v-model="form.description"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            placeholder="Enter category description"
                            :class="{ 'border-red-500 ring-red-500': errors.description }"
                        ></textarea>
                    </div>
                    <div v-if="errors.description" class="mt-1 text-sm text-red-600 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ errors.description }}
                    </div>
                </div>

                <!-- Meta description -->
                <div class="col-span-2">
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Meta Description
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <textarea
                            id="meta_description"
                            name="meta_description"
                            rows="2"
                            v-model="form.meta_description"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            placeholder="Enter meta description"
                            :class="{ 'border-red-500 ring-red-500': errors.meta_description }"
                        ></textarea>
                    </div>
                    <div v-if="errors.meta_description" class="mt-1 text-sm text-red-600 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ errors.meta_description }}
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end">
            <Link
                :href="route('admin.categories.index')"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mr-3 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
            </Link>
            <button
                type="submit"
                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                :disabled="processing"
            >
                <span v-if="processing" class="mr-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" v-else>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save
            </button>
        </div>
    </form>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import ImageUpload from '@/Components/ImageUpload.vue';

const props = defineProps({
    form: {
        type: Object,
        required: true
    },
    category: {
        type: Object,
        default: () => ({})
    },
    categoryHierarchy: {
        type: Array,
        required: true
    },
    errors: {
        type: Object,
        default: () => ({})
    },
    isEdit: {
        type: Boolean,
        default: false
    }
});

const processing = ref(false);

// Process category hierarchy for dropdown
const categoryOptions = computed(() => {
    const options = [];

    const processCategory = (category, level = 0, parentPath = '') => {
        // Do not add current category to list (when editing)
        if (props.isEdit && props.category && category.id === props.category.id) {
            return;
        }

        options.push({
            id: category.id,
            name: category.name,
            level: level,
            path: parentPath ? `${parentPath} > ${category.name}` : category.name
        });

        if (category.children && category.children.length > 0) {
            category.children.forEach(child => {
                processCategory(child, level + 1, parentPath ? `${parentPath} > ${category.name}` : category.name);
            });
        }
    };

    props.categoryHierarchy.forEach(category => {
        processCategory(category);
    });

    return options;
});

// CSS taxonomy for different category levels
const getCategoryOptionClass = (level) => {
    switch (level) {
        case 0:
            return 'font-semibold text-gray-900';
        case 1:
            return 'pl-4 text-indigo-700';
        case 2:
            return 'pl-8 text-blue-600';
        case 3:
            return 'pl-12 text-cyan-600';
        default:
            return `pl-${4 * level} text-gray-600`;
    }
};

// Add hierarchy icon before category name
const getCategoryPrefix = (level) => {
    if (level === 0) {
        return '';
    } else if (level === 1) {
        return '↳ ';
    } else if (level === 2) {
        return '↳ ↳ ';
    } else if (level === 3) {
        return '↳ ↳ ↳ ';
    } else {
        return '↳'.repeat(level) + ' ';
    }
};

// Submit form
const submitForm = () => {
    processing.value = true;
    emit('submit');

    // Simulate processing for better UX
    setTimeout(() => {
        processing.value = false;
    }, 500);
};

const emit = defineEmits(['submit']);
</script>
