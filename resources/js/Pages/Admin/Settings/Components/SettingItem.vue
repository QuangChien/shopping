<script setup>
import { ref, computed, onMounted } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import TextareaInput from './Inputs/TextareaInput.vue';
import SelectInput from './Inputs/SelectInput.vue';
import NumberInput from './Inputs/NumberInput.vue';

const props = defineProps({
    setting: Object,
    error: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['update']);

const value = ref(props.setting.value);
const focused = ref(false);
const changed = ref(false);
const inputRef = ref(null);

// Track when value changes to emit event
const updateValue = (newValue) => {
    value.value = newValue;
    changed.value = props.setting.value !== newValue;
    emit('update', newValue);
};

// Display different inputs based on setting type
const isCheckbox = computed(() => props.setting.type === 'boolean');
const isTextarea = computed(() => props.setting.type === 'text');
const isNumber = computed(() => props.setting.type === 'number' || props.setting.type === 'integer');
const isSelect = computed(() => props.setting.type === 'select');

// Format key from snake_case to title
const formatLabel = computed(() => {
    return props.setting.key
        .replace(/_/g, ' ')
        .replace(/\b\w/g, char => char.toUpperCase());
});

// Get labels for boolean values
const getBooleanLabel = (isEnabled) => {
    return isEnabled ? 'Enabled' : 'Disabled';
};

// Convert boolean value to checkbox state
const checkboxValue = computed({
    get: () => value.value === 'true' || value.value === true,
    set: (val) => updateValue(val ? 'true' : 'false')
});

// CSS classes for the setting container
const settingClasses = computed(() => {
    return {
        'border-blue-200 bg-blue-50': focused.value && !props.error,
        'border-green-200 bg-green-50': changed.value && !focused.value && !props.error,
        'border-red-300 bg-red-50': props.error,
    };
});

// Handle focus state
const setFocused = (state) => {
    focused.value = state;
};

// Method to focus the input element
const focus = () => {
    if (inputRef.value) {
        // Cố gắng truy cập phần tử input thực tế
        const actualInput = inputRef.value.$el || inputRef.value;
        if (actualInput && typeof actualInput.focus === 'function') {
            actualInput.focus();
            // Thêm cuộn vào phần tử có lỗi
            setTimeout(() => {
                actualInput.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }, 100);
        }
    }
};

// Expose methods to parent components
defineExpose({
    focus,
    inputRef
});

onMounted(() => {
    // Handle default values
    if (props.setting.type === 'boolean') {
        value.value = props.setting.value === 'true' || props.setting.value === true 
            ? 'true' 
            : 'false';
    }
    
    // Auto focus if there's an error
    if (props.error) {
        setTimeout(() => {
            focus();
        }, 100);
    }
});
</script>

<template>
    <div 
        :id="`setting-${setting.id}`"
        class="setting-item p-4 border rounded-lg transition-all duration-200"
        :class="settingClasses"
        :data-setting-id="`setting-${setting.id}`"
    >
        <div class="flex flex-col md:flex-row md:items-start md:gap-6">
            <!-- Label and description section -->
            <div class="md:w-1/3 mb-3 md:mb-0">
                <div class="flex items-center">
                    <InputLabel :for="setting.key" :value="formatLabel" class="text-gray-700 font-medium" />
                    <span v-if="setting.required" class="ml-1 text-red-500">*</span>
                </div>
                
                <!-- Description -->
                <p v-if="setting.description" class="mt-1 text-sm text-gray-600">
                    {{ setting.description }}
                </p>
            </div>
            
            <!-- Input controls section -->
            <div class="md:w-2/3">
                <!-- Checkbox for boolean -->
                <div v-if="isCheckbox" class="mt-1">
                    <label class="flex items-center">
                        <Checkbox 
                            ref="inputRef"
                            :id="setting.key"
                            v-model:checked="checkboxValue"
                            :name="setting.key"
                            @focus="setFocused(true)"
                            @blur="setFocused(false)"
                            class="transition-all duration-200 rounded"
                            :class="{ 'border-red-500 ring-red-500': error }"
                        />
                        <span class="ml-2 text-sm text-gray-700">
                            {{ getBooleanLabel(checkboxValue) }}
                        </span>
                    </label>
                </div>
                
                <!-- Textarea for text -->
                <div v-else-if="isTextarea" class="mt-1">
                    <TextareaInput 
                        ref="inputRef"
                        :id="setting.key"
                        v-model="value"
                        :name="setting.key"
                        class="mt-1 block w-full transition-all duration-200 focus:ring-blue-500 focus:border-blue-500"
                        :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': error }"
                        @input="updateValue($event.target.value)"
                        @focus="setFocused(true)"
                        @blur="setFocused(false)"
                    />
                </div>
                
                <!-- Number input for numeric values -->
                <div v-else-if="isNumber" class="mt-1">
                    <NumberInput 
                        ref="inputRef"
                        :id="setting.key"
                        v-model="value"
                        :name="setting.key"
                        class="mt-1 block w-full transition-all duration-200 focus:ring-blue-500 focus:border-blue-500"
                        :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': error }"
                        @input="updateValue($event.target.value)"
                        @focus="setFocused(true)"
                        @blur="setFocused(false)"
                    />
                </div>
                
                <!-- Select for options -->
                <div v-else-if="isSelect" class="mt-1">
                    <SelectInput 
                        ref="inputRef"
                        :id="setting.key"
                        v-model="value"
                        :name="setting.key"
                        :options="setting.options || []"
                        placeholder="Select an option"
                        class="mt-1 block w-full transition-all duration-200 focus:ring-blue-500 focus:border-blue-500"
                        :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': error }"
                        @change="updateValue($event.target.value)"
                        @focus="setFocused(true)"
                        @blur="setFocused(false)"
                    />
                </div>
                
                <!-- Text input for other types -->
                <div v-else class="mt-1">
                    <TextInput 
                        ref="inputRef"
                        :id="setting.key"
                        v-model="value"
                        type="text"
                        :name="setting.key"
                        class="mt-1 block w-full transition-all duration-200 focus:ring-blue-500 focus:border-blue-500"
                        :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': error }"
                        @input="updateValue($event.target.value)"
                        @focus="setFocused(true)"
                        @blur="setFocused(false)"
                    />
                </div>
                
                <!-- Error message -->
                <div v-if="error" class="mt-2 text-sm text-red-600">
                    {{ error }}
                </div>
                
                <!-- Changed indicator -->
                <div v-else-if="changed" class="mt-2 text-xs text-green-600 flex items-center">
                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Changed
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.setting-item {
    border-color: #e5e7eb;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

/* Animation when value changes */
@keyframes highlight {
    0% { background-color: rgba(59, 130, 246, 0.1); }
    100% { background-color: transparent; }
}

.setting-item.changed {
    animation: highlight 1s ease-out;
}
</style> 