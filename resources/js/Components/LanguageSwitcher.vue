<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale);

const languages = [
    { code: 'en', name: 'English', flag: '🇺🇸' },
    { code: 'vi', name: 'Vietnamese', flag: '🇻🇳' },
];
</script>

<template>
    <div class="relative inline-block text-left">
        <div class="group">
            <button
                type="button"
                class="inline-flex items-center justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500"
                id="language-menu"
                aria-expanded="true"
                aria-haspopup="true"
            >
                <span class="mr-2">
                    {{ languages.find(lang => lang.code === currentLocale)?.flag }}
                </span>
                {{ languages.find(lang => lang.code === currentLocale)?.name }}
                <svg
                    class="-mr-1 ml-2 h-5 w-5"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>

            <div
                class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="language-menu"
            >
                <div class="py-1" role="none">
                    <Link
                        v-for="language in languages"
                        :key="language.code"
                        :href="route('language.switch', language.code)"
                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                        :class="{ 'bg-gray-100 text-gray-900': currentLocale === language.code }"
                        role="menuitem"
                    >
                        <span class="mr-3">{{ language.flag }}</span>
                        {{ language.name }}
                        <svg
                            v-if="currentLocale === language.code"
                            class="ml-auto h-5 w-5 text-indigo-600"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template> 