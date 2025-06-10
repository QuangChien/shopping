<template>
    <Head title="Attributes Management" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Attributes Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters & Search -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="w-full md:w-1/4">
                                <InputLabel for="search" value="Search" />
                                <TextInput id="search" v-model="form.search" type="text" class="w-full" placeholder="Name or code" />
                            </div>

                            <div class="w-full md:w-1/4">
                                <InputLabel for="backend_type" value="Data Type" />
                                <select v-model="form.backend_type" id="backend_type" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All</option>
                                    <option v-for="type in backendTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                                </select>
                            </div>

                            <div class="w-full md:w-1/4">
                                <InputLabel for="frontend_input" value="Input Type" />
                                <select v-model="form.frontend_input" id="frontend_input" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All</option>
                                    <option v-for="input in frontendInputs" :key="input.id" :value="input.id">{{ input.name }}</option>
                                </select>
                            </div>

                            <div class="w-full md:w-1/4">
                                <InputLabel for="is_filterable" value="Filterable" />
                                <select v-model="form.is_filterable" id="is_filterable" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <PrimaryButton @click="search" class="mr-2">
                                <i class="fas fa-search mr-1"></i> Search
                            </PrimaryButton>
                            <SecondaryButton @click="resetFilters">
                                <i class="fas fa-times mr-1"></i> Clear Filters
                            </SecondaryButton>
                        </div>
                    </div>
                </div>

                <!-- Attributes Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between mb-4">
                            <h3 class="text-lg font-semibold">Attributes List</h3>
                            <Link :href="route('admin.attributes.create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                                <i class="fas fa-plus mr-1"></i> Add Attribute
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Code
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Label
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data Type
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Input Type
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Required
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Sort Order
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Options
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="attribute in attributes.data" :key="attribute.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ attribute.code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ attribute.frontend_label }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ getBackendTypeName(attribute.backend_type) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ getFrontendInputName(attribute.frontend_input) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span v-if="attribute.is_required" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <i class="fas fa-times"></i>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ attribute.sort_order }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <Link v-if="attribute.frontend_input === 'select' || attribute.frontend_input === 'multiselect'" 
                                                :href="route('admin.attributes.options', attribute.id)" 
                                                class="text-indigo-600 hover:text-indigo-900">
                                                Manage Options
                                            </Link>
                                            <span v-else>N/A</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('admin.attributes.edit', attribute.id)" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <button @click="confirmDelete(attribute)" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="attributes.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Confirm Delete Attribute
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Are you sure you want to delete this attribute? This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal" class="mr-2">
                        Cancel
                    </SecondaryButton>

                    <DangerButton @click="deleteAttribute" :class="{ 'opacity-25': processing }" :disabled="processing">
                        Delete Attribute
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import AdminLayout from '@/Pages/Admin/Components/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    attributes: Object,
    filters: Object,
    backendTypes: Array,
    frontendInputs: Array,
});

const form = useForm({
    search: props.filters.search || '',
    backend_type: props.filters.backend_type || '',
    frontend_input: props.filters.frontend_input || '',
    is_filterable: props.filters.is_filterable || '',
});

const confirmingDeletion = ref(false);
const processing = ref(false);
const attributeToDelete = ref(null);

const search = () => {
    form.get(route('admin.attributes.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.backend_type = '';
    form.frontend_input = '';
    form.is_filterable = '';
    search();
};

const getBackendTypeName = (type) => {
    const found = props.backendTypes.find(item => item.id === type);
    return found ? found.name : type;
};

const getFrontendInputName = (input) => {
    const found = props.frontendInputs.find(item => item.id === input);
    return found ? found.name : input;
};

const confirmDelete = (attribute) => {
    attributeToDelete.value = attribute;
    confirmingDeletion.value = true;
};

const closeModal = () => {
    confirmingDeletion.value = false;
    attributeToDelete.value = null;
};

const deleteAttribute = () => {
    if (attributeToDelete.value) {
        processing.value = true;
        
        useForm({}).delete(route('admin.attributes.destroy', attributeToDelete.value.id), {
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

// Auto-search when changing filters
watch([() => form.backend_type, () => form.frontend_input, () => form.is_filterable], () => {
    search();
});
</script> 