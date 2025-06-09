<template>
    <div v-bind="$attrs">
        <div v-if="links.length > 3" class="flex flex-wrap justify-between sm:hidden">
            <Link
                v-if="hasPrevPage"
                :href="prevPageUrl"
                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                &laquo; Previous
            </Link>
            <Link
                v-if="hasNextPage"
                :href="nextPageUrl"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Next &raquo;
            </Link>
        </div>
        <div v-if="links.length > 3" class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p v-if="paginationInfo" class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ paginationInfo.from }}</span> to
                    <span class="font-medium">{{ paginationInfo.to }}</span> of
                    <span class="font-medium">{{ paginationInfo.total }}</span> results
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
import { computed } from 'vue';

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
});

// Filter out non-useful pagination links
const paginationLinks = computed(() => {
    return props.links.filter(link => link.label !== 'elements');
});

// Extract pagination metadata
const paginationInfo = computed(() => {
    try {
        // Check if meta exists in any link
        for (const link of props.links) {
            if (link.meta) {
                return {
                    from: link.meta.from || 1,
                    to: link.meta.to || 1,
                    total: link.meta.total || 0
                };
            }
        }

        // If no meta found, calculate pagination info from links
        if (props.links.length >= 3) {
            const currentPage = props.links.findIndex(link => link.active);
            const activePage = currentPage >= 0 ? parseInt(props.links[currentPage].label) || 1 : 1;
            
            let lastPage = 1;
            for (let i = props.links.length - 3; i >= 1; i--) {
                const pageNum = parseInt(props.links[i].label);
                if (!isNaN(pageNum)) {
                    lastPage = pageNum;
                    break;
                }
            }

            const perPage = 15; // Default page size
            return {
                from: ((activePage - 1) * perPage) + 1,
                to: Math.min(activePage * perPage, lastPage * perPage),
                total: lastPage * perPage
            };
        }

        return { from: 1, to: 1, total: 0 };
    } catch (error) {
        console.error('Error parsing pagination info', error);
        return { from: 1, to: 1, total: 0 };
    }
});

// Check if there is a previous page
const hasPrevPage = computed(() => {
    const prevLink = props.links.find(link => link.label === '&laquo; Previous');
    return prevLink && prevLink.url !== null;
});

// Check if there is a next page
const hasNextPage = computed(() => {
    const nextLink = props.links.find(link => link.label === 'Next &raquo;');
    return nextLink && nextLink.url !== null;
});

// Previous page URL
const prevPageUrl = computed(() => {
    const link = props.links.find(link => link.label === '&laquo; Previous');
    return link?.url || '#';
});

// Next page URL
const nextPageUrl = computed(() => {
    const link = props.links.find(link => link.label === 'Next &raquo;');
    return link?.url || '#';
});
</script>
