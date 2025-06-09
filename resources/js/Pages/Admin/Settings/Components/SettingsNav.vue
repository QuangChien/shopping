<script setup>
defineProps({
    groupNames: Array,
    groupLabels: Object,
    activeTab: String,
    isMobile: {
        type: Boolean,
        default: false
    }
});

defineEmits(['changeTab']);
</script>

<template>
    <div class="settings-nav bg-white rounded-lg shadow-sm p-4" :class="{ 'p-2': isMobile }">
        <h3 v-if="!isMobile" class="text-lg font-medium text-gray-700 mb-4 flex items-center">
            <svg class="h-5 w-5 mr-2 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
            </svg>
            Setting Groups
        </h3>
        
        <ul class="space-y-1 max-h-[70vh] overflow-y-auto">
            <li v-for="groupName in groupNames" :key="groupName">
                <button
                    class="w-full text-left px-4 py-2 rounded-md transition-all duration-150 hover:bg-gray-100 focus:outline-none flex items-center"
                    :class="{ 
                        'bg-blue-50 text-blue-700 font-medium shadow-sm border-l-4 border-blue-500': activeTab === groupName,
                        'py-3': isMobile
                    }"
                    @click="$emit('changeTab', groupName)"
                >
                    <span class="truncate">{{ groupLabels[groupName] || groupName }}</span>
                    <svg v-if="activeTab === groupName" class="ml-auto h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            </li>
        </ul>
    </div>
</template>

<style scoped>
.settings-nav ul {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.settings-nav ul::-webkit-scrollbar {
    width: 4px;
}

.settings-nav ul::-webkit-scrollbar-track {
    background: transparent;
}

.settings-nav ul::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 20px;
}

/* Smooth transition for active state */
button {
    position: relative;
    overflow: hidden;
}

button::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 100%;
    background-color: rgba(59, 130, 246, 0.1);
    transition: width 0.3s ease;
    z-index: -1;
}

button:hover::after {
    width: 100%;
}
</style> 