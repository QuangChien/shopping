<template>
    <div class="ml-6 space-y-2">
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
            
            <!-- Recursively render child categories -->
            <template v-if="category.children && category.children.length > 0">
                <CategoryChildrenCheckboxes 
                    :categories="category.children" 
                    :form="form"
                />
            </template>
        </template>
    </div>
</template>

<script setup>
const props = defineProps({
    categories: {
        type: Array,
        required: true
    },
    form: {
        type: Object,
        required: true
    }
});

// Helper to add prefix based on category level
const getCategoryPrefix = (level) => {
    return level > 0 ? '—'.repeat(level) + ' ' : '';
};
</script> 