<template>
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="p-5">
            <div class="flex items-center">
                <div :class="['flex h-10 w-10 items-center justify-center rounded-full', colorClass]">
                    <component :is="iconComponent" />
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="truncate text-sm font-medium text-gray-500">{{ title }}</dt>
                        <dd>
                            <div class="text-lg font-medium text-gray-900">{{ value }}</div>
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
        required: true
    }
});

const colorClass = computed(() => {
    const colors = {
        blue: "bg-blue-500",
        green: "bg-green-500",
        purple: "bg-purple-500",
        yellow: "bg-yellow-500"
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
        }
    };
    
    return icons[props.icon] || null;
});
</script> 