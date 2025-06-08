<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'info',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    message: {
        type: String,
        required: true
    },
    dismissible: {
        type: Boolean,
        default: true
    },
    duration: {
        type: Number,
        default: 5000 // 5 seconds
    },
    autoClose: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

const visible = ref(true);
const animating = ref(false);
const progress = ref(100);
let autoCloseTimeout = null;
let progressInterval = null;

// Style classes based on alert type
const alertClasses = computed(() => {
    const baseClasses = 'p-4 rounded-md flex items-start justify-between';
    
    const typeClasses = {
        success: 'bg-green-50 text-green-800 border border-green-200',
        error: 'bg-red-50 text-red-800 border border-red-200',
        warning: 'bg-yellow-50 text-yellow-800 border border-yellow-200',
        info: 'bg-blue-50 text-blue-800 border border-blue-200'
    };
    
    // Add special animation based on alert type
    const animationClass = animating.value ? 
        (props.type === 'success' ? 'alert-pulse' : 
        props.type === 'error' ? 'alert-shake' : 
        'animate-alert-in') : '';
    
    // Add extra padding at the bottom if we have a progress bar
    const paddingClass = props.autoClose ? 'pb-5' : '';
    
    return `${baseClasses} ${typeClasses[props.type]} ${animationClass} ${paddingClass}`;
});

// Progress bar color based on alert type
const progressBarColor = computed(() => {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    return colors[props.type];
});

// Icon based on alert type
const iconComponent = computed(() => {
    const icons = {
        success: {
            path: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z',
            className: 'text-green-500'
        },
        error: {
            path: 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z',
            className: 'text-red-500'
        },
        warning: {
            path: 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z',
            className: 'text-yellow-500'
        },
        info: {
            path: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z',
            className: 'text-blue-500'
        }
    };
    
    return icons[props.type];
});

// Start progress bar for auto close
const startProgressBar = () => {
    if (props.autoClose && props.duration > 0) {
        // Set 50ms interval for smooth animation
        const intervalTime = 50;
        const steps = props.duration / intervalTime;
        const decrement = 100 / steps;
        
        progressInterval = setInterval(() => {
            progress.value -= decrement;
            if (progress.value <= 0) {
                clearInterval(progressInterval);
            }
        }, intervalTime);
    }
};

// Close the alert
const close = () => {
    animating.value = true;
    // Add a slight delay to let the animation complete
    setTimeout(() => {
        visible.value = false;
        emit('close');
    }, 300);
};

// Auto close functionality
onMounted(() => {
    // Start with a special animation based on alert type
    animating.value = true;
    setTimeout(() => {
        animating.value = false;
    }, 800); // Longer duration for animations to complete
    
    if (props.autoClose && props.duration > 0) {
        startProgressBar();
        autoCloseTimeout = setTimeout(() => {
            close();
        }, props.duration);
    }
});

onBeforeUnmount(() => {
    if (autoCloseTimeout) {
        clearTimeout(autoCloseTimeout);
    }
    if (progressInterval) {
        clearInterval(progressInterval);
    }
});
</script>

<template>
    <transition 
        enter-active-class="alert-enter-active"
        enter-from-class="alert-enter"
        leave-active-class="alert-exit-active"
        leave-from-class="alert-exit"
    >
        <div v-if="visible" 
            :class="alertClasses" 
            role="alert"
            style="transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); position: relative;"
        >
            <div class="flex w-full">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5" :class="iconComponent.className" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" :d="iconComponent.path" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-grow">
                    <p class="text-sm font-medium">{{ message }}</p>
                </div>
                <div v-if="dismissible" class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button
                            type="button"
                            class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="{
                                'text-green-500 hover:bg-green-100 focus:ring-green-600 focus:ring-offset-green-50': type === 'success',
                                'text-red-500 hover:bg-red-100 focus:ring-red-600 focus:ring-offset-red-50': type === 'error',
                                'text-yellow-500 hover:bg-yellow-100 focus:ring-yellow-600 focus:ring-offset-yellow-50': type === 'warning',
                                'text-blue-500 hover:bg-blue-100 focus:ring-blue-600 focus:ring-offset-blue-50': type === 'info',
                            }"
                            @click="close"
                        >
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Progress bar for auto-close -->
            <div v-if="autoClose" class="absolute bottom-0 left-0 right-0 h-1 rounded-b-md overflow-hidden">
                <div 
                    :class="progressBarColor" 
                    class="h-full transition-all ease-linear"
                    :style="`width: ${progress}%`"
                ></div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.animate-alert-in {
    animation: alert-bounce-in 0.5s ease-out;
}

@keyframes alert-bounce-in {
    0% {
        opacity: 0;
        transform: scale(0.95) translateY(-10px);
    }
    50% {
        opacity: 1;
        transform: scale(1.02) translateY(0);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}
</style> 