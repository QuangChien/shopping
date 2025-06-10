<template>
    <div>
        <div class="flex items-center">
            <input
                type="text"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                placeholder="#RRGGBB"
            />
            <div 
                class="w-10 h-10 ml-2 border border-gray-300 cursor-pointer" 
                :style="{ backgroundColor: modelValue }"
                @click="showPicker = true"
            ></div>
        </div>

        <!-- Color Picker Popup -->
        <div v-if="showPicker" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="showPicker = false">
            <div class="bg-white p-4 rounded-md shadow-lg">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold">Select a Color</h3>
                </div>
                
                <!-- Color Palette -->
                <div class="grid grid-cols-8 gap-2 mb-4">
                    <div 
                        v-for="color in colors" 
                        :key="color"
                        class="w-8 h-8 rounded-sm cursor-pointer border border-gray-300"
                        :style="{ backgroundColor: color }"
                        :class="{ 'ring-2 ring-indigo-500': modelValue === color }"
                        @click="selectColor(color)"
                    ></div>
                </div>
                
                <!-- Custom Color Input -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Custom Color</label>
                    <div class="flex mt-1">
                        <input 
                            type="text" 
                            v-model="customColor" 
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                            placeholder="#RRGGBB"
                        />
                        <div 
                            class="w-8 h-8 ml-2 border border-gray-300" 
                            :style="{ backgroundColor: customColor }"
                        ></div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button 
                        type="button" 
                        class="bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none border border-gray-300 rounded-md mr-2"
                        @click="showPicker = false"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        class="bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none rounded-md"
                        @click="applyCustomColor"
                    >
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:modelValue']);

const showPicker = ref(false);
const customColor = ref(props.modelValue);

// Basic color palette
const colors = [
    '#F44336', '#E91E63', '#9C27B0', '#673AB7', '#3F51B5', '#2196F3', '#03A9F4', '#00BCD4',
    '#009688', '#4CAF50', '#8BC34A', '#CDDC39', '#FFEB3B', '#FFC107', '#FF9800', '#FF5722',
    '#795548', '#9E9E9E', '#607D8B', '#000000', '#FFFFFF', '#FF0000', '#00FF00', '#0000FF',
    '#FFFF00', '#00FFFF', '#FF00FF', '#C0C0C0', '#808080', '#800000', '#808000', '#008000',
    '#800080', '#008080', '#000080', '#5D4037', '#EEEEEE', '#BDBDBD', '#757575', '#616161'
];

// When props change, update the custom color
watch(() => props.modelValue, (newValue) => {
    customColor.value = newValue;
});

// Select a color from the palette
const selectColor = (color) => {
    customColor.value = color;
    emit('update:modelValue', color);
    showPicker.value = false;
};

// Apply the custom color
const applyCustomColor = () => {
    emit('update:modelValue', customColor.value);
    showPicker.value = false;
};
</script> 