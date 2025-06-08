import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import axios from 'axios';
import AlertContainer from './Components/AlertContainer.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Fetch translations before app initialization
const initApp = async () => {
    try {
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

                // Add global translation method
                app.config.globalProperties.$t = window.trans;
                
                // Register global components
                app.component('AlertContainer', AlertContainer);

                // Global alert management
                app.config.globalProperties.$alert = {
                    success: (message, options = {}) => {
                        const alertContainer = document.querySelector('#app').__vue_app__.config.globalProperties.$refs.alertContainer;
                        if (alertContainer) {
                            alertContainer.addAlert('success', message, options);
                        }
                    },
                    error: (message, options = {}) => {
                        const alertContainer = document.querySelector('#app').__vue_app__.config.globalProperties.$refs.alertContainer;
                        if (alertContainer) {
                            alertContainer.addAlert('error', message, options);
                        }
                    },
                    warning: (message, options = {}) => {
                        const alertContainer = document.querySelector('#app').__vue_app__.config.globalProperties.$refs.alertContainer;
                        if (alertContainer) {
                            alertContainer.addAlert('warning', message, options);
                        }
                    },
                    info: (message, options = {}) => {
                        const alertContainer = document.querySelector('#app').__vue_app__.config.globalProperties.$refs.alertContainer;
                        if (alertContainer) {
                            alertContainer.addAlert('info', message, options);
                        }
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
    }
};

initApp();
