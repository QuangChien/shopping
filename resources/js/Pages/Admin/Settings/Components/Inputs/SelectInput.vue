<script setup>
defineProps({
    modelValue: [String, Number],
    options: {
        type: Array,
        default: () => []
    },
    placeholder: {
        type: String,
        default: 'Select an option'
    }
});

defineEmits(['update:modelValue']);
</script>

<template>
    <select
        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
    >
        <option value="" disabled>{{ placeholder }}</option>
        <option 
            v-for="option in options" 
            :key="option.value || option"
            :value="option.value || option"
        >
            {{ option.label || option }}
        </option>
    </select>
</template> 