<script setup>
import { onMounted, ref } from 'vue';

const textarea = ref(null);

defineProps({
    modelValue: String
});

defineEmits(['update:modelValue']);

onMounted(() => {
    if (textarea.value) {
        // Auto-resize textarea based on content
        textarea.value.style.height = 'auto';
        textarea.value.style.height = textarea.value.scrollHeight + 'px';
    }
});

const onInput = (e) => {
    // Auto-resize when typing
    e.target.style.height = 'auto';
    e.target.style.height = e.target.scrollHeight + 'px';
}
</script>

<template>
    <textarea
        ref="textarea"
        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm resize-none min-h-[120px]"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value); onInput($event)"
    ></textarea>
</template> 