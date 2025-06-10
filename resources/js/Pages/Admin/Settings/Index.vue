<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Pages/Admin/Components/AdminLayout.vue';
import SettingsNav from '@/Pages/Admin/Settings/Components/SettingsNav.vue';
import SettingsPanel from '@/Pages/Admin/Settings/Components/SettingsPanel.vue';
import SaveButton from '@/Pages/Admin/Settings/Components/SaveButton.vue';

const props = defineProps({
    settings: Object,
    groupNames: Array,
});

const page = usePage();
const layout = ref(null);
const mobileMenuOpen = ref(false);
const fieldErrors = ref({});
const errorFields = ref([]);
const settingRefs = ref({});
const settingsPanel = ref(null);

// Form for saving settings
const form = useForm({
    settings: []
});

// Current active tab (settings group)
const activeTab = ref(props.groupNames[0] || 'general');

// Group labels
const groupLabels = computed(() => {
    return {
        general: 'General Information',
        currency: 'Currency',
        catalog: 'Product Catalog',
        inventory: 'Inventory',
        cart: 'Shopping Cart',
        checkout: 'Checkout',
        shipping: 'Shipping',
        tax: 'Tax',
        payment: 'Payment Methods',
        orders: 'Orders',
        customers: 'Customers',
        mail: 'Email',
        seo: 'SEO',
        social: 'Social Media',
        security: 'Security',
        maintenance: 'Maintenance'
    };
});

// Handle tab change
const changeTab = (tabName) => {
    activeTab.value = tabName;
    mobileMenuOpen.value = false; // Close mobile menu when changing tab
};

// Handle setting value change
const updateSettingValue = (settingId, value) => {
    // Check if setting already exists in form
    const index = form.settings.findIndex(item => item.id === settingId);

    if (index !== -1) {
        // If exists, update value
        form.settings[index].value = value;
    } else {
        // If not exists, add new
        form.settings.push({ id: settingId, value: value });
    }

    // Clear error for this field if exists
    if (fieldErrors.value[settingId]) {
        delete fieldErrors.value[settingId];
    }
};

// Toggle mobile menu
const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

// Find setting group for a setting ID
const findSettingGroup = (settingId) => {
    for (const groupName in props.settings) {
        const setting = props.settings[groupName].find(s => s.id === settingId);
        if (setting) {
            return groupName;
        }
    }
    return null;
};

// Force smooth scroll to element
const forceSmoothScroll = (element) => {
    // Get current location
    const startPosition = window.pageYOffset;

    // Calculate where to scroll to
    const elementRect = element.getBoundingClientRect();
    const absoluteElementTop = elementRect.top + window.pageYOffset;
    const middle = absoluteElementTop - (window.innerHeight / 2);

    // Calculate scroll time
    const duration = 800; // ms
    const start = performance.now();

    // Animation function
    const animateScroll = (currentTime) => {
        const elapsedTime = currentTime - start;
        const progress = Math.min(elapsedTime / duration, 1);

        // Easing function - makes scrolling smoother
        const easeInOutQuad = t => t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
        const easedProgress = easeInOutQuad(progress);

        // Calculate new position
        const position = startPosition + (middle - startPosition) * easedProgress;

        // Scroll to new location
        window.scrollTo(0, position);

        // Continue animation if not completed
        if (progress < 1) {
            requestAnimationFrame(animateScroll);
        }
    };

    // Start animation
    requestAnimationFrame(animateScroll);

    // Highlight element
    element.classList.add('error-highlight');
    setTimeout(() => {
        element.classList.remove('error-highlight');
    }, 3000);
};

// Scroll to error field
const scrollToError = async (fieldId) => {
    // Wait for DOM to update
    await nextTick();

    // Wait another 300ms to make sure the DOM has fully updated
    setTimeout(() => {
        // Phương thức 1: Tìm bằng ID
        const element = document.getElementById(`setting-${fieldId}`);
        console.log('Tìm phần tử có ID:', `setting-${fieldId}`, element);

        if (element) {
            // Make sure element is visible
            element.style.display = 'block';

            // Using custom scroll function
            forceSmoothScroll(element);
        } else {
            console.error('Error element not found:', fieldId);

            // Method 2: Search by data attribute
            const elementsByData = document.querySelectorAll(`[data-setting-id="setting-${fieldId}"]`);
            if (elementsByData.length > 0) {
                const firstElement = elementsByData[0];
                // Using custom scroll function
                forceSmoothScroll(firstElement);
            } else {
                // Method 3: Scroll to the content tab
                const tabContent = document.querySelector('.settings-panel');
                if (tabContent) {
                    // Scroll to the top of the content tab
                    tabContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        }
    }, 300);
};

onMounted(() => {
    // Initialization
    for (const groupName in props.settings) {
        props.settings[groupName].forEach(setting => {
            if (setting.value !== null && setting.value !== undefined) {
                form.settings.push({
                    id: setting.id,
                    value: setting.value
                });
            }
        });
    }
});

// Handle form submission
const saveSettings = () => {
    // Reset errors
    fieldErrors.value = {};
    errorFields.value = [];

    form.post(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            
        },
        onError: (errors) => {
            // Process errors and extract setting IDs
            const errorMessages = [];

            for (const key in errors) {
                if (key.startsWith('settings.')) {
                    // Extract the index from the key (e.g., settings.0.value)
                    const parts = key.split('.');
                    if (parts.length >= 2) {
                        const index = parseInt(parts[1]);
                        // Get the setting id from the form data
                        const settingId = form.settings[index]?.id;

                        if (settingId) {
                            // Store the error
                            fieldErrors.value[settingId] = errors[key];
                            errorFields.value.push(settingId);

                            // Add error message
                            errorMessages.push(errors[key]);

                            // Switch to the correct tab if needed
                            const group = findSettingGroup(settingId);
                            if (group && group !== activeTab.value) {
                                changeTab(group);

                                // Wait longer for tab change before scrolling
                                setTimeout(() => {
                                    scrollToFirstError();
                                }, 500);
                            } else {
                                // Scroll to the error field
                                setTimeout(() => {
                                    scrollToFirstError();
                                }, 200);
                            }

                            // Only handle the first error to avoid multiple scrolls
                            break;
                        }
                    }
                } else {
                    errorMessages.push(errors[key]);
                }
            }
        }
    });
};

const scrollToFirstError = () => {
    if (Object.keys(form.errors).length === 0) return;

    nextTick(() => {
        setTimeout(() => {
            // Make sure the panel has been rendered
            if (!settingsPanel.value) return;

            // Get references from SettingsPanel
            const panelSettingRefs = settingsPanel.value.settingRefs;
            if (!panelSettingRefs) return;

            // Find the first setting that has an error
            for (const settingId in fieldErrors.value) {
                if (panelSettingRefs[settingId]) {
                    // Call focus method on SettingItem
                    panelSettingRefs[settingId].focus();
                    break;
                }
            }
        }, 200); // Wait for DOM update
    });
};
</script>

<template>
    <Head title="System Settings" />

    <AdminLayout ref="layout">
        <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <!-- Mobile menu button -->
                    <button
                        @click="toggleMobileMenu"
                        class="md:hidden p-2 rounded-md text-gray-500 hover:text-gray-800 hover:bg-gray-100 focus:outline-none"
                    >
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </button>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        System Settings
                    </h2>
                </div>
                <SaveButton :processing="form.processing" @click="saveSettings" />
            </div>

        <div class="py-6 md:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Active Group indicator for mobile -->
                <div class="md:hidden mb-4 flex items-center justify-between bg-blue-50 p-3 rounded-lg shadow-sm">
                    <div class="font-medium text-blue-700">
                        {{ groupLabels[activeTab] || activeTab }}
                    </div>
                    <button
                        @click="toggleMobileMenu"
                        class="p-1 rounded-md text-blue-500 hover:bg-blue-100"
                    >
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile menu dropdown -->
                <div
                    v-if="mobileMenuOpen"
                    class="md:hidden mb-6 bg-white rounded-lg shadow-lg overflow-hidden transition-all duration-300 ease-in-out"
                >
                    <SettingsNav
                        :group-names="groupNames"
                        :group-labels="groupLabels"
                        :active-tab="activeTab"
                        :is-mobile="true"
                        @change-tab="changeTab"
                        class="border-b-0 rounded-t-lg"
                    />
                </div>

                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="p-4 md:p-6 text-gray-900">
                        <div class="flex flex-col md:flex-row md:gap-8">
                            <!-- Sidebar with setting groups - desktop only -->
                            <div class="hidden md:block md:w-1/4 lg:w-1/5 flex-shrink-0">
                                <SettingsNav
                                    :group-names="groupNames"
                                    :group-labels="groupLabels"
                                    :active-tab="activeTab"
                                    @change-tab="changeTab"
                                />
                            </div>

                            <!-- Panel showing settings for selected group -->
                            <div class="md:w-3/4 lg:w-4/5">
                                <SettingsPanel
                                    v-if="settings[activeTab]"
                                    :settings="settings[activeTab]"
                                    :group-name="activeTab"
                                    :group-label="groupLabels[activeTab]"
                                    :field-errors="fieldErrors"
                                    @update-setting="updateSettingValue"
                                    ref="settingsPanel"
                                />
                                <div v-else class="text-center p-6 text-gray-500">
                                    No settings available in this group.
                                </div>

                                <!-- Fixed bottom save button for mobile -->
                                <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t p-4 flex justify-center shadow-lg">
                                    <SaveButton
                                        :processing="form.processing"
                                        @click="saveSettings"
                                        custom-class="w-full flex justify-center"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
/* Add smooth transition for mobile menu */
.md\:hidden {
    transition: all 0.3s ease;
}
</style>
