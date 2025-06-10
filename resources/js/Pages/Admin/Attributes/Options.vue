<template>
    <Head :title="`Options for ${attribute.frontend_label}`" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Options for "{{ attribute.frontend_label }}" attribute
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between mb-6">
                    <Link :href="route('admin.attributes.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </Link>
                </div>

                <!-- Add New Option Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Add New Option</h3>

                        <form @submit.prevent="submitOption">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div>
                                    <InputLabel for="value" value="Value" />
                                    <TextInput
                                        id="value"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="optionForm.value"
                                        required
                                    />
                                    <InputError :message="optionForm.errors.value" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="label" value="Label (optional)" />
                                    <TextInput
                                        id="label"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="optionForm.label"
                                    />
                                    <InputError :message="optionForm.errors.label" class="mt-2" />
                                </div>

                                <div v-if="isColorAttribute">
                                    <InputLabel for="swatch" value="Color code (optional)" />
                                    <ColorPicker
                                        id="swatch"
                                        v-model="optionForm.swatch"
                                        class="mt-1"
                                    />
                                    <InputError :message="optionForm.errors.swatch" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="sort_order" value="Sort Order" />
                                    <TextInput
                                        id="sort_order"
                                        type="number"
                                        class="mt-1 block w-full"
                                        v-model="optionForm.sort_order"
                                        min="0"
                                    />
                                    <InputError :message="optionForm.errors.sort_order" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex justify-end mt-4">
                                <PrimaryButton :class="{ 'opacity-25': optionForm.processing }" :disabled="optionForm.processing">
                                    <i class="fas fa-plus mr-1"></i> Add Option
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Options Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Options List</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Value
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Label
                                        </th>
                                        <th v-if="isColorAttribute" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Color Code
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Sort Order
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(option, index) in attribute.options" :key="option.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <span v-if="editingIndex === index">
                                                <TextInput
                                                    type="text"
                                                    class="w-full"
                                                    v-model="editOptionForm.value"
                                                    required
                                                />
                                            </span>
                                            <span v-else>{{ option.value }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span v-if="editingIndex === index">
                                                <TextInput
                                                    type="text"
                                                    class="w-full"
                                                    v-model="editOptionForm.label"
                                                />
                                            </span>
                                            <span v-else>{{ option.label }}</span>
                                        </td>
                                        <td v-if="isColorAttribute" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span v-if="editingIndex === index">
                                                <ColorPicker
                                                    v-model="editOptionForm.swatch"
                                                />
                                            </span>
                                            <span v-else class="flex items-center">
                                                {{ option.swatch }}
                                                <div 
                                                    v-if="option.swatch" 
                                                    class="w-6 h-6 ml-2 border border-gray-300" 
                                                    :style="{ backgroundColor: option.swatch }"
                                                ></div>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span v-if="editingIndex === index">
                                                <TextInput
                                                    type="number"
                                                    class="w-full"
                                                    v-model="editOptionForm.sort_order"
                                                    min="0"
                                                />
                                            </span>
                                            <span v-else>{{ option.sort_order }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div v-if="editingIndex === index">
                                                <PrimaryButton @click="updateOption(option.id)" class="mr-1" :disabled="editOptionForm.processing">
                                                    <i class="fas fa-save"></i>
                                                </PrimaryButton>
                                                <SecondaryButton @click="cancelEdit">
                                                    <i class="fas fa-times"></i>
                                                </SecondaryButton>
                                            </div>
                                            <div v-else>
                                                <button @click="startEdit(option, index)" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button @click="confirmDelete(option)" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Confirm Delete Option
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Are you sure you want to delete this option? This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal" class="mr-2">
                        Cancel
                    </SecondaryButton>

                    <DangerButton @click="deleteOption" :class="{ 'opacity-25': processing }" :disabled="processing">
                        Delete Option
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Pages/Admin/Components/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import ColorPicker from '@/Components/ColorPicker.vue';

const props = defineProps({
    attribute: Object,
});

const isColorAttribute = computed(() => {
    return props.attribute.code === 'color';
});

const optionForm = useForm({
    value: '',
    label: '',
    swatch: '',
    sort_order: 0,
});

const editOptionForm = useForm({
    value: '',
    label: '',
    swatch: '',
    sort_order: 0,
});

const confirmingDeletion = ref(false);
const processing = ref(false);
const optionToDelete = ref(null);
const editingIndex = ref(-1);

const submitOption = () => {
    optionForm.post(route('admin.attributes.options.store', props.attribute.id), {
        onSuccess: () => {
            optionForm.reset();
        },
    });
};

const startEdit = (option, index) => {
    editOptionForm.value = option.value;
    editOptionForm.label = option.label;
    editOptionForm.swatch = option.swatch;
    editOptionForm.sort_order = option.sort_order;
    editingIndex.value = index;
};

const cancelEdit = () => {
    editingIndex.value = -1;
    editOptionForm.reset();
};

const updateOption = (optionId) => {
    editOptionForm.put(route('admin.attributes.options.update', [props.attribute.id, optionId]), {
        onSuccess: () => {
            editingIndex.value = -1;
        },
    });
};

const confirmDelete = (option) => {
    optionToDelete.value = option;
    confirmingDeletion.value = true;
};

const closeModal = () => {
    confirmingDeletion.value = false;
    optionToDelete.value = null;
};

const deleteOption = () => {
    if (optionToDelete.value) {
        processing.value = true;
        
        useForm({}).delete(route('admin.attributes.options.destroy', [props.attribute.id, optionToDelete.value.id]), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                processing.value = false;
            },
            onError: () => {
                processing.value = false;
            },
        });
    }
};
</script> 