<template>
    <div v-bind="$attrs">
        <div v-if="links.length > 3" class="flex flex-wrap justify-between sm:hidden">
            <Link
                v-if="hasPrevPage"
                :href="prevPageUrl"
                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                &laquo; Trang trước
            </Link>
            <Link
                v-if="hasNextPage"
                :href="nextPageUrl"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Trang sau &raquo;
            </Link>
        </div>
        <div v-if="links.length > 3" class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p v-if="paginationInfo" class="text-sm text-gray-700">
                    Hiển thị
                    <span class="font-medium">{{ paginationInfo.from }}</span> đến
                    <span class="font-medium">{{ paginationInfo.to }}</span> của
                    <span class="font-medium">{{ paginationInfo.total }}</span> kết quả
                </p>
            </div>
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <Link
                        v-for="(link, index) in paginationLinks"
                        :key="index"
                        :href="link.url || '#'"
                        v-html="link.label"
                        :class="[
                            link.active
                                ? 'relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600'
                                : link.url
                                ? 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'
                                : 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-500 ring-1 ring-inset ring-gray-300 focus:outline-offset-0 cursor-not-allowed',
                            index === 0 ? 'rounded-l-md' : '',
                            index === paginationLinks.length - 1 ? 'rounded-r-md' : '',
                        ]"
                    ></Link>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
});

// Filter pagination links (remove "elements")
const paginationLinks = computed(() => {
    // Replace English labels with Vietnamese
    return props.links.filter((link, index) => {
        if (index !== props.links.length - 1 || link.label !== 'elements') {
            // Replace English pagination labels with Vietnamese
            if (link.label === '&laquo; Previous') {
                link.label = '&laquo; Trước';
            } else if (link.label === 'Next &raquo;') {
                link.label = 'Sau &raquo;';
            }
            return true;
        }
        return false;
    });
});

// Pagination data analysis
const paginationInfo = computed(() => {
    try {
        // In Laravel, the last element usually contains meta data.
        if (props.links.length > 0) {
            const lastItem = props.links[props.links.length - 1];

            // Check if there is meta in the last element
            if (lastItem && typeof lastItem === 'object') {
                // Check if there is meta in the last element
                if (lastItem.meta) {
                    return {
                        from: lastItem.meta.from || 1,
                        to: lastItem.meta.to || 1,
                        total: lastItem.meta.total || 0
                    };
                }
            }

            // Another way to determine: search all links to get information
            for (const link of props.links) {
                if (link.meta) {
                    return {
                        from: link.meta.from || 1,
                        to: link.meta.to || 1,
                        total: link.meta.total || 0
                    };
                }
            }
        }

        // If metadata is not found, try getting it from the links array itself.
        // Laravel Pagination display: links[0] = "Previous", links[1..n-2] = page numbers, links[n-1] = "Next", links[n] = pagination data

        const numLinks = props.links.length;
        if (numLinks >= 3) {
            // Find current page
            const currentPage = props.links.findIndex(link => link.active);
            const activePage = currentPage >= 0 ? parseInt(props.links[currentPage].label) : 1;

            // Find the last page
            let lastPage = 1;
            for (let i = numLinks - 3; i >= 1; i--) {
                if (!isNaN(parseInt(props.links[i].label))) {
                    lastPage = parseInt(props.links[i].label);
                    break;
                }
            }

            // Assume each page has 15 items.
            const perPage = 15;

            return {
                from: ((activePage - 1) * perPage) + 1,
                to: Math.min(activePage * perPage, lastPage * perPage),
                total: lastPage * perPage
            };
        }

        // Default returns safe value
        return { from: 1, to: 1, total: 0 };

    } catch (error) {
        console.error('Error parsing pagination info', error);
        return { from: 1, to: 1, total: 0 };
    }
});

// Check if there is a previous page
const hasPrevPage = computed(() => {
    return props.links.some(link => (link.label === '&laquo; Previous' || link.label === '&laquo; Trước') && link.url !== null);
});

// Check if there is a next page
const hasNextPage = computed(() => {
    return props.links.some(link => (link.label === 'Next &raquo;' || link.label === 'Sau &raquo;') && link.url !== null);
});

// Previous page URL
const prevPageUrl = computed(() => {
    const link = props.links.find(link => link.label === '&laquo; Previous' || link.label === '&laquo; Trước');
    return link ? link.url : '#';
});

// URL trang sau
const nextPageUrl = computed(() => {
    const link = props.links.find(link => link.label === 'Next &raquo;' || link.label === 'Sau &raquo;');
    return link ? link.url : '#';
});

// Debug log
onMounted(() => {
    console.log('Pagination links:', props.links);
    console.log('Pagination info:', paginationInfo.value);
});
</script>
