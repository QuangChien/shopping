import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import axios from 'axios';
import AlertContainer from './Components/AlertContainer.vue';
import AlertService from './Services/AlertService';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Fetch translations only for frontend
const initApp = async () => {
    try {
        // Only fetch translations for frontend pages, not admin
        const response = await axios.get('/api/translations');
        window.translations = response.data;

        // Add global translation function
        window.trans = (key) => {
            // Split the key by dots to navigate nested translations
            const keys = key.split('.');
            let translation = window.translations;

            // Navigate through the translation object
            for (const k of keys) {
                if (translation && translation[k]) {
                    translation = translation[k];
                } else {
                    return key; // Return the key if translation not found
                }
            }

            return translation;
        };

        // Initialize Inertia app
        createInertiaApp({
            title: (title) => `${title} - ${appName}`,
            resolve: (name) =>
                resolvePageComponent(
                    `./Pages/${name}.vue`,
                    import.meta.glob('./Pages/**/*.vue'),
                ),
            setup({ el, App, props, plugin }) {
                const app = createApp({ render: () => h(App, props) });

                // Add global translation method (only needed for frontend)
                app.config.globalProperties.$t = window.trans;
                
                // Register global components
                app.component('AlertContainer', AlertContainer);

                // Global alert management - sử dụng AlertService thay vì truy cập DOM
                app.config.globalProperties.$alert = {
                    success: (message, options = {}) => {
                        AlertService.success(message, options);
                    },
                    error: (message, options = {}) => {
                        AlertService.error(message, options);
                    },
                    warning: (message, options = {}) => {
                        AlertService.warning(message, options);
                    },
                    info: (message, options = {}) => {
                        AlertService.info(message, options);
                    }
                };

                return app
                    .use(plugin)
                    .use(ZiggyVue)
                    .mount(el);
            },
            progress: {
                color: '#4B5563',
            },
        });
    } catch (error) {
        console.error('Failed to load translations:', error);
        
        // Fallback: Initialize app without translations
        createInertiaApp({
            title: (title) => `${title} - ${appName}`,
            resolve: (name) =>
                resolvePageComponent(
                    `./Pages/${name}.vue`,
                    import.meta.glob('./Pages/**/*.vue'),
                ),
            setup({ el, App, props, plugin }) {
                return createApp({ render: () => h(App, props) })
                    .use(plugin)
                    .use(ZiggyVue)
                    .mount(el);
            },
            progress: {
                color: '#4B5563',
            },
        });
    }
};

initApp();
