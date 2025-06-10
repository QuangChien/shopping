<template>
    <div>
        <Link
            :href="href"
            class="mt-2 flex items-center rounded-md p-2 transition-colors duration-200"
            :class="[
                isActive
                    ? 'bg-blue-700 text-white font-medium shadow-md'
                    : 'text-gray-300 hover:bg-gray-800 hover:text-white'
            ]"
        >
            <span class="mr-2" :class="{ 'text-white': isActive }">
                <component :is="iconComponent" />
            </span>
            <span>{{ label }}</span>
            <span v-if="$slots.default" class="ml-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </span>
        </Link>
        <div v-if="$slots.default">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, h } from 'vue';

const props = defineProps({
    href: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    icon: {
        type: String,
        required: true
    }
});

const page = usePage();

// Check if the current link is active
const isActive = computed(() => {
    // Handling for routes with parameters
    if (props.href === '#') return false;

    // If href is full URL, extract path
    let currentPath = page.url;
    let linkPath = props.href;

    // Check if href contains full URL
    if (linkPath.startsWith('http')) {
        const url = new URL(linkPath);
        linkPath = url.pathname;
    }

    // Simple route matching handling
    return currentPath.startsWith(linkPath);
});

const iconComponent = computed(() => {
    const icons = {
        dashboard: {
            render() {
                return h('svg', { class: 'h-5 w-5', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { d: 'M2 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V4zM8 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H9a1 1 0 01-1-1V4zM15 3a1 1 0 00-1 1v12a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1h-2z' })
                ]);
            }
        },
        product: {
            render() {
                return h('svg', { class: 'h-5 w-5', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { 'fill-rule': 'evenodd', d: 'M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z', 'clip-rule': 'evenodd' })
                ]);
            }
        },
        category: {
            render() {
                return h('svg', { class: 'h-5 w-5', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { d: 'M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z' })
                ]);
            }
        },
        order: {
            render() {
                return h('svg', { class: 'h-5 w-5', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { 'fill-rule': 'evenodd', d: 'M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z', 'clip-rule': 'evenodd' })
                ]);
            }
        },
        customer: {
            render() {
                return h('svg', { class: 'h-5 w-5', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { d: 'M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z' })
                ]);
            }
        },
        setting: {
            render() {
                return h('svg', { class: 'h-5 w-5', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { 'fill-rule': 'evenodd', d: 'M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z', 'clip-rule': 'evenodd' })
                ]);
            }
        },
        attribute: {
            render() {
                return h('svg', { class: 'h-5 w-5', viewBox: '0 0 20 20', fill: 'currentColor' }, [
                    h('path', { 'fill-rule': 'evenodd', d: 'M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z', 'clip-rule': 'evenodd' })
                ]);
            }
        }
    };

    return icons[props.icon] || null;
});
</script>
