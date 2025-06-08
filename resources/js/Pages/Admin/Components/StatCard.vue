<template>
    <div class="overflow-hidden rounded-lg bg-white shadow hover:shadow-md transition-shadow duration-300">
        <div class="p-5">
            <div class="flex items-center">
                <div :class="['flex h-12 w-12 items-center justify-center rounded-full', colorClass]">
                    <component :is="iconComponent" />
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">{{ title }}</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">{{ formattedValue }}</div>
                            
                            <div v-if="trend !== null" :class="[
                                'ml-2 flex items-baseline text-sm font-semibold',
                                trend >= 0 ? 'text-green-600' : 'text-red-600'
                            ]">
                                <svg v-if="trend >= 0" class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="sr-only">
                                    {{ trend >= 0 ? 'Increased' : 'Decreased' }} by
                                </span>
                                {{ Math.abs(trend) }}%
                            </div>
                        </dd>
                        <dd v-if="subtitle" class="mt-1">
                            <span class="text-sm text-gray-500">{{ subtitle }}</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, h } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [Number, String],
        required: true
    },
    icon: {
        type: String,
        required: true
    },
    color: {
        type: String,
        default: 'blue'
    },
    trend: {
        type: Number,
        default: null
    },
    subtitle: {
        type: String,
        default: ''
    },
    isCurrency: {
        type: Boolean,
        default: false
    }
});

// Format value as currency if needed
const formattedValue = computed(() => {
    if (props.isCurrency) {
        return new Intl.NumberFormat('vi-VN', { 
            style: 'currency', 
            currency: 'VND',
            maximumFractionDigits: 0
        }).format(props.value);
    }
    
    // Format number with thousands separators
    if (typeof props.value === 'number') {
        return new Intl.NumberFormat('vi-VN').format(props.value);
    }
    
    return props.value;
});

const colorClass = computed(() => {
    const colors = {
        blue: "bg-blue-500",
        green: "bg-green-500",
        purple: "bg-purple-500",
        yellow: "bg-yellow-500",
        red: "bg-red-500",
        indigo: "bg-indigo-500",
        pink: "bg-pink-500"
    };
    return colors[props.color] || colors.blue;
});

const iconComponent = computed(() => {
    const icons = {
        customer: {
            render() {
                return h('svg', { class: 'h-6 w-6 text-white', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { d: 'M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z' })
                ]);
            }
        },
        order: {
            render() {
                return h('svg', { class: 'h-6 w-6 text-white', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { d: 'M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z' })
                ]);
            }
        },
        product: {
            render() {
                return h('svg', { class: 'h-6 w-6 text-white', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { 'fill-rule': 'evenodd', d: 'M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z', 'clip-rule': 'evenodd' })
                ]);
            }
        },
        category: {
            render() {
                return h('svg', { class: 'h-6 w-6 text-white', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { d: 'M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z' })
                ]);
            }
        },
        revenue: {
            render() {
                return h('svg', { class: 'h-6 w-6 text-white', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { d: 'M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z' }),
                    h('path', { 'fill-rule': 'evenodd', d: 'M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z', 'clip-rule': 'evenodd' })
                ]);
            }
        },
        chart: {
            render() {
                return h('svg', { class: 'h-6 w-6 text-white', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { d: 'M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z' })
                ]);
            }
        }
    };
    
    return icons[props.icon] || null;
});
</script> 