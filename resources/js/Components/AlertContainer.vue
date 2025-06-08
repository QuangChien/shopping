<script setup>
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Alert from './Alert.vue';

// Unique ID generator for alerts
const uniqueId = () => {
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
};

// Default props
const props = defineProps({
    position: {
        type: String,
        default: 'top-right',
        validator: (value) => ['top-right', 'top-left', 'bottom-right', 'bottom-left', 'top-center', 'bottom-center'].includes(value)
    },
    maxAlerts: {
        type: Number,
        default: 5
    }
});

// Alerts array
const alerts = ref([]);

// Track processed flash messages to avoid duplicates
const processedFlashIds = ref(new Set());

// Position styles
const positionClasses = computed(() => {
    const positions = {
        'top-right': 'fixed top-4 right-4',
        'top-left': 'fixed top-4 left-4',
        'bottom-right': 'fixed bottom-4 right-4',
        'bottom-left': 'fixed bottom-4 left-4',
        'top-center': 'fixed top-4 left-1/2 transform -translate-x-1/2',
        'bottom-center': 'fixed bottom-4 left-1/2 transform -translate-x-1/2'
    };
    
    return positions[props.position];
});

// Get flash messages from Inertia
const page = usePage();
watch(() => page.props.flash, (newFlash) => {
    if (newFlash) {
        // Create a unique ID for this set of flash messages to avoid duplicates
        const flashId = JSON.stringify(newFlash);
        
        // Only process if we haven't seen this exact flash message before
        if (!processedFlashIds.value.has(flashId)) {
            processedFlashIds.value.add(flashId);
            
            // Process different types of flash messages
            if (newFlash.success) {
                addAlert('success', newFlash.success);
            }
            if (newFlash.error) {
                addAlert('error', newFlash.error);
            }
            if (newFlash.warning) {
                addAlert('warning', newFlash.warning);
            }
            if (newFlash.info) {
                addAlert('info', newFlash.info);
            }
            
            // Limit the size of our set to avoid memory leaks
            if (processedFlashIds.value.size > 10) {
                processedFlashIds.value.clear();
            }
        }
    }
}, { immediate: true, deep: true });

// Add new alert
const addAlert = (type, message, options = {}) => {
    const id = uniqueId();
    const alert = {
        id,
        type,
        message,
        dismissible: options.dismissible !== undefined ? options.dismissible : true,
        duration: options.duration || 5000,
        autoClose: options.autoClose !== undefined ? options.autoClose : true,
    };
    
    // Check for duplicates before adding
    const isDuplicate = alerts.value.some(existingAlert => 
        existingAlert.type === type && existingAlert.message === message
    );
    
    if (!isDuplicate) {
        // Add to alerts array, limiting to maxAlerts
        alerts.value.push(alert);
        if (alerts.value.length > props.maxAlerts) {
            alerts.value.shift();
        }
    }
    
    return id;
};

// Remove alert by ID
const removeAlert = (id) => {
    const index = alerts.value.findIndex(alert => alert.id === id);
    if (index !== -1) {
        alerts.value.splice(index, 1);
    }
};

// Expose methods for external use
defineExpose({
    addAlert,
    removeAlert
});
</script>

<template>
    <div :class="positionClasses" class="w-80 z-50 space-y-2">
        <Alert
            v-for="alert in alerts"
            :key="alert.id"
            :type="alert.type"
            :message="alert.message"
            :dismissible="alert.dismissible"
            :duration="alert.duration"
            :auto-close="alert.autoClose"
            @close="removeAlert(alert.id)"
            class="shadow-lg"
        />
    </div>
</template> 