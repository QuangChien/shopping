<template>
    <div class="mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center border-b pb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Product Attributes
        </h3>

        <div class="grid grid-cols-1 gap-6 mt-4">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    Product Name <span class="text-red-500 ml-1">*</span>
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input
                        type="text"
                        id="name"
                        v-model="form.attributes.name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                        placeholder="Enter product name"
                        :class="{ 'border-red-500 ring-red-500': getAttributeError('name') }"
                    />
                </div>
                <div v-if="getAttributeError('name')" class="mt-1 text-sm text-red-600 flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ getAttributeError('name') }}
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    Description
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <textarea
                        id="description"
                        v-model="form.attributes.description"
                        rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                        placeholder="Enter product description"
                        :class="{ 'border-red-500 ring-red-500': getAttributeError('description') }"
                    ></textarea>
                </div>
                <div v-if="getAttributeError('description')" class="mt-1 text-sm text-red-600 flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ getAttributeError('description') }}
                </div>
            </div>

            <!-- Price and Weight on same row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Price
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input
                            type="number"
                            id="price"
                            v-model="form.attributes.price"
                            min="0"
                            step="0.01"
                            class="mt-1 block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            placeholder="0.00"
                            :class="{ 'border-red-500 ring-red-500': getAttributeError('price') }"
                        />
                    </div>
                    <div v-if="getAttributeError('price')" class="mt-1 text-sm text-red-600 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ getAttributeError('price') }}
                    </div>
                </div>

                <!-- Weight -->
                <div>
                    <label for="weight" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                        Weight (kg)
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input
                            type="number"
                            id="weight"
                            v-model="form.attributes.weight"
                            min="0"
                            step="0.01"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            placeholder="0.00"
                            :class="{ 'border-red-500 ring-red-500': getAttributeError('weight') }"
                        />
                    </div>
                    <div v-if="getAttributeError('weight')" class="mt-1 text-sm text-red-600 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ getAttributeError('weight') }}
                    </div>
                </div>
            </div>

            <!-- Dynamic Attributes -->
            <template v-for="attribute in filteredAttributes" :key="attribute.code">
                <div>
                    <label :for="attribute.code" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        {{ attribute.label }} <span v-if="attribute.required" class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <!-- Text Input -->
                        <input
                            v-if="['text', 'price', 'weight'].includes(attribute.input)"
                            :type="getInputType(attribute.input)"
                            :id="attribute.code"
                            v-model="form.attributes[attribute.code]"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            :placeholder="`Enter ${attribute.label.toLowerCase()}`"
                            :class="{ 'border-red-500 ring-red-500': getAttributeError(attribute.code) }"
                        />
                        
                        <!-- Textarea -->
                        <textarea
                            v-else-if="attribute.input === 'textarea'"
                            :id="attribute.code"
                            v-model="form.attributes[attribute.code]"
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            :placeholder="`Enter ${attribute.label.toLowerCase()}`"
                            :class="{ 'border-red-500 ring-red-500': getAttributeError(attribute.code) }"
                        ></textarea>
                        
                        <!-- Select -->
                        <select
                            v-else-if="['select', 'multiselect'].includes(attribute.input)"
                            :id="attribute.code"
                            v-model="form.attributes[attribute.code]"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200"
                            :class="{ 'border-red-500 ring-red-500': getAttributeError(attribute.code) }"
                            :multiple="attribute.input === 'multiselect'"
                        >
                            <option value="">Select {{ attribute.label }}</option>
                            <option 
                                v-for="option in attribute.options" 
                                :key="option.value" 
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        
                        <!-- Boolean -->
                        <div v-else-if="attribute.input === 'boolean'" class="flex items-center mt-1">
                            <input
                                :id="attribute.code"
                                type="checkbox"
                                v-model="form.attributes[attribute.code]"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            />
                            <label :for="attribute.code" class="ml-2 block text-sm text-gray-900">
                                {{ attribute.label }}
                            </label>
                        </div>
                    </div>
                    <div v-if="getAttributeError(attribute.code)" class="mt-1 text-sm text-red-600 flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{ getAttributeError(attribute.code) }}
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    form: Object,
    errors: Object,
    attributes: {
        type: Array,
        default: () => [],
    },
});

// Filter out attributes based on product type
const filteredAttributes = computed(() => {
    const baseAttributes = props.attributes.filter(attr => 
        !['name', 'description', 'price', 'weight'].includes(attr.code)
    );
    
    // For configurable products, filter out attributes that should be used as variants
    if (props.form.type_id === 'configurable') {
        return baseAttributes.filter(attr => 
            !['select', 'multiselect'].includes(attr.input) || 
            !attr.options || 
            attr.options.length === 0
        );
    }
    
    return baseAttributes;
});

// Helper function to get the appropriate input type
const getInputType = (inputType) => {
    switch (inputType) {
        case 'price':
        case 'weight':
            return 'number';
        case 'date':
            return 'date';
        case 'datetime':
            return 'datetime-local';
        default:
            return 'text';
    }
};

// Helper to get errors for specific attributes
const getAttributeError = (code) => {
    if (props.errors && props.errors.attributes && props.errors.attributes[code]) {
        return props.errors.attributes[code];
    }
    return null;
};
</script> 