<template>
    <div class="mb-8" v-if="showVariants">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center border-b pb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            Product Variants
        </h3>

        <!-- Variant Attributes Selection -->
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Configurable Attributes</h4>
            <div class="space-y-3">
                <div v-for="attribute in variantAttributes" :key="attribute.code" class="flex items-center">
                    <input
                        type="checkbox"
                        :id="`variant-attr-${attribute.code}`"
                        v-model="selectedVariantAttributes"
                        :value="attribute.code"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    />
                    <label :for="`variant-attr-${attribute.code}`" class="ml-2 block text-sm text-gray-900">
                        {{ attribute.label }}
                    </label>
                </div>
            </div>
            <div v-if="selectedVariantAttributes.length === 0" class="mt-2 text-sm text-amber-600">
                Select at least one attribute to create variants
            </div>
        </div>

        <!-- Generated Variants -->
        <div v-if="selectedVariantAttributes.length > 0">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Generated Variants</h4>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                SKU
                            </th>
                            <th 
                                v-for="attrCode in selectedVariantAttributes" 
                                :key="`header-${attrCode}`"
                                scope="col" 
                                class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                {{ getAttributeLabel(attrCode) }}
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Default
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(variant, index) in form.variants" :key="`variant-${index}`">
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                <input
                                    type="text"
                                    v-model="variant.sku"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500 ring-red-500': getVariantError(index, 'sku') }"
                                />
                                <div v-if="getVariantError(index, 'sku')" class="mt-1 text-xs text-red-600">
                                    {{ getVariantError(index, 'sku') }}
                                </div>
                            </td>
                            <td 
                                v-for="attrCode in selectedVariantAttributes" 
                                :key="`variant-${index}-${attrCode}`"
                                class="px-3 py-2 whitespace-nowrap text-sm text-gray-900"
                            >
                                <select
                                    v-model="variant.attributes[attrCode]"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500 ring-red-500': getVariantAttributeError(index, attrCode) }"
                                >
                                    <option value="">Select {{ getAttributeLabel(attrCode) }}</option>
                                    <option 
                                        v-for="option in getAttributeOptions(attrCode)" 
                                        :key="`option-${option.value}`" 
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                                <div v-if="getVariantAttributeError(index, attrCode)" class="mt-1 text-xs text-red-600">
                                    {{ getVariantAttributeError(index, attrCode) }}
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                <input
                                    type="number"
                                    v-model="variant.price"
                                    min="0"
                                    step="0.01"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500 ring-red-500': getVariantError(index, 'price') }"
                                />
                                <div v-if="getVariantError(index, 'price')" class="mt-1 text-xs text-red-600">
                                    {{ getVariantError(index, 'price') }}
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                <input
                                    type="number"
                                    v-model="variant.quantity"
                                    min="0"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-500 ring-red-500': getVariantError(index, 'quantity') }"
                                />
                                <div v-if="getVariantError(index, 'quantity')" class="mt-1 text-xs text-red-600">
                                    {{ getVariantError(index, 'quantity') }}
                                </div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                <input
                                    type="radio"
                                    :name="'default_variant'"
                                    :value="index"
                                    v-model="defaultVariantIndex"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-between">
                <button
                    type="button"
                    @click="generateVariants"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Generate Variants
                </button>
                <button
                    type="button"
                    @click="addVariant"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Variant
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    form: Object,
    errors: Object,
    attributes: {
        type: Array,
        default: () => [],
    },
});

// Track selected variant attributes
const selectedVariantAttributes = ref([]);

// Track default variant
const defaultVariantIndex = ref(0);

// Show variants section only for configurable products
const showVariants = computed(() => {
    return props.form.type_id === 'configurable';
});

// Get all attributes that can be used for variants (select/multiselect with options)
const variantAttributes = computed(() => {
    return props.attributes.filter(attr => 
        ['select', 'multiselect'].includes(attr.input) && 
        attr.options && 
        attr.options.length > 0
    );
});

// Get attribute label by code
const getAttributeLabel = (code) => {
    const attr = props.attributes.find(a => a.code === code);
    return attr ? attr.label : code;
};

// Get attribute options by code
const getAttributeOptions = (code) => {
    const attr = props.attributes.find(a => a.code === code);
    return attr && attr.options ? attr.options : [];
};

// Get variant error
const getVariantError = (index, field) => {
    if (props.errors && props.errors.variants && props.errors.variants[index] && props.errors.variants[index][field]) {
        return props.errors.variants[index][field];
    }
    return null;
};

// Get variant attribute error
const getVariantAttributeError = (index, attrCode) => {
    if (props.errors && props.errors.variants && props.errors.variants[index] && 
        props.errors.variants[index].attributes && props.errors.variants[index].attributes[attrCode]) {
        return props.errors.variants[index].attributes[attrCode];
    }
    return null;
};

// Add a new empty variant
const addVariant = () => {
    const variant = {
        sku: `${props.form.sku}-${props.form.variants.length + 1}`,
        price: props.form.attributes.price || 0,
        quantity: 0,
        attributes: {}
    };
    
    // Initialize attribute values
    selectedVariantAttributes.value.forEach(attrCode => {
        variant.attributes[attrCode] = '';
    });
    
    props.form.variants.push(variant);
};

// Generate all possible variants based on selected attributes
const generateVariants = () => {
    if (selectedVariantAttributes.value.length === 0) return;
    
    // Get all attribute combinations
    const combinations = generateAttributeCombinations();
    
    // Create variants from combinations
    props.form.variants = combinations.map((combo, index) => {
        return {
            sku: `${props.form.sku}-${index + 1}`,
            price: props.form.attributes.price || 0,
            quantity: 0,
            attributes: combo,
            is_default: index === 0
        };
    });
};

// Generate all possible attribute combinations
const generateAttributeCombinations = () => {
    const attributesWithOptions = selectedVariantAttributes.value.map(attrCode => {
        return {
            code: attrCode,
            options: getAttributeOptions(attrCode).map(opt => ({ 
                code: attrCode, 
                value: opt.value 
            }))
        };
    });
    
    return cartesianProduct(attributesWithOptions);
};

// Helper function to generate Cartesian product of attribute options
const cartesianProduct = (attributesWithOptions) => {
    if (attributesWithOptions.length === 0) return [{}];
    
    const result = [];
    const allCombosExceptFirst = cartesianProduct(attributesWithOptions.slice(1));
    
    for (const opt of attributesWithOptions[0].options) {
        for (const combo of allCombosExceptFirst) {
            const newCombo = { ...combo };
            newCombo[opt.code] = opt.value;
            result.push(newCombo);
        }
    }
    
    return result;
};

// Initialize form.variants if it doesn't exist
if (!props.form.variants) {
    props.form.variants = [];
}

// When default variant changes, update is_default property on all variants
watch(defaultVariantIndex, (newIndex) => {
    props.form.variants.forEach((variant, idx) => {
        variant.is_default = idx === newIndex;
    });
});

// Watch for product type changes and reset variants if changing away from configurable
watch(() => props.form.type_id, (newType) => {
    if (newType !== 'configurable') {
        props.form.variants = [];
        selectedVariantAttributes.value = [];
    }
});

// Watch for selected variant attributes changes
watch(selectedVariantAttributes, () => {
    // Update existing variants to have all selected attributes
    props.form.variants.forEach(variant => {
        selectedVariantAttributes.value.forEach(attrCode => {
            if (variant.attributes[attrCode] === undefined) {
                variant.attributes[attrCode] = '';
            }
        });
    });
});
</script> 