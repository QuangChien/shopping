<template>
    <div>
        <!-- Full page overlay -->
        <div v-if="fullPage" class="fixed inset-0 flex items-center justify-center z-50 bg-gray-900 bg-opacity-70">
            <div class="bg-white p-6 rounded-lg shadow-xl flex flex-col items-center">
                <svg class="animate-spin" :class="sizeClasses" :width="size" :height="size" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" :class="colorClasses" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span v-if="text" class="mt-3 text-base font-medium text-gray-800">{{ text }}</span>
            </div>
        </div>

        <!-- Inline spinner -->
        <div v-else-if="overlay" class="relative min-h-[200px]">
            <div class="absolute inset-0 flex items-center justify-center bg-gray-50 bg-opacity-90 z-20 rounded-md shadow-inner">
                <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                    <svg class="animate-spin" :class="sizeClasses || 'h-10 w-10'" :width="size" :height="size" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" :class="colorClasses" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span v-if="text" class="mt-2 text-sm font-medium" :class="textColorClass">{{ text }}</span>
                </div>
            </div>
            <slot></slot>
        </div>

        <!-- Just the spinner -->
        <div v-else class="inline-flex items-center">
            <svg class="animate-spin" :class="sizeClasses" :width="size" :height="size" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" :class="colorClasses" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="text" class="ml-2 text-sm font-medium" :class="textColorClass">{{ text }}</span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: [Number, String],
        default: 24
    },
    color: {
        type: String,
        default: 'indigo', // indigo, blue, red, green, gray
        validator: (value) => ['indigo', 'blue', 'red', 'green', 'gray', 'white'].includes(value)
    },
    text: {
        type: String,
        default: ''
    },
    fullPage: {
        type: Boolean,
        default: false
    },
    overlay: {
        type: Boolean,
        default: false
    }
});

// Computed classes based on props
const sizeClasses = computed(() => {
    const sizeMap = {
        'sm': 'h-4 w-4',
        'md': 'h-6 w-6',
        'lg': 'h-8 w-8',
        'xl': 'h-12 w-12',
        '2xl': 'h-16 w-16'
    };
    
    if (typeof props.size === 'string' && sizeMap[props.size]) {
        return sizeMap[props.size];
    }
    
    return '';
});

const colorClasses = computed(() => {
    const colorMap = {
        'indigo': 'text-indigo-600',
        'blue': 'text-blue-600',
        'red': 'text-red-600',
        'green': 'text-green-600',
        'gray': 'text-gray-600',
        'white': 'text-white'
    };
    
    return colorMap[props.color] || 'text-indigo-600';
});

const textColorClass = computed(() => {
    const colorMap = {
        'indigo': 'text-indigo-700',
        'blue': 'text-blue-700',
        'red': 'text-red-700',
        'green': 'text-green-700',
        'gray': 'text-gray-700',
        'white': 'text-white'
    };
    
    return colorMap[props.color] || 'text-gray-700';
});
</script> 