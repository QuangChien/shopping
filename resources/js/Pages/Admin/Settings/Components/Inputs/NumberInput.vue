<script setup>
defineProps({
    modelValue: [Number, String],
    min: {
        type: [Number, String],
        default: null
    },
    max: {
        type: [Number, String],
        default: null
    },
    step: {
        type: [Number, String],
        default: 1
    }
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="flex w-full relative">
        <input
            type="number"
            class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full"
            :value="modelValue"
            :min="min"
            :max="max"
            :step="step"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <div class="text-gray-400">
                <!-- Slot for measurement units if needed -->
                <slot></slot>
            </div>
        </div>
    </div>
</template> 