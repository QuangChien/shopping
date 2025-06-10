<template>
    <Head title="Create Attribute" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Attribute
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between mb-6">
                    <Link :href="route('admin.attributes.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Attributes
                    </Link>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Information -->
                                <div>
                                    <h3 class="text-lg font-semibold mb-4">Basic Information</h3>

                                    <div class="mb-4">
                                        <InputLabel for="code" value="Attribute Code" />
                                        <TextInput
                                            id="code"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.code"
                                            required
                                            autocomplete="off"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">Use lowercase letters, numbers and underscore only. Must start with a letter.</p>
                                        <InputError :message="form.errors.code" class="mt-2" />
                                    </div>

                                    <div class="mb-4">
                                        <InputLabel for="frontend_label" value="Attribute Label" />
                                        <TextInput
                                            id="frontend_label"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.frontend_label"
                                            required
                                            autocomplete="off"
                                        />
                                        <InputError :message="form.errors.frontend_label" class="mt-2" />
                                    </div>

                                    <div class="mb-4">
                                        <InputLabel for="backend_type" value="Data Type" />
                                        <select
                                            id="backend_type"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            v-model="form.backend_type"
                                            required
                                        >
                                            <option value="">Select Data Type</option>
                                            <option v-for="type in backendTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                                        </select>
                                        <InputError :message="form.errors.backend_type" class="mt-2" />
                                    </div>

                                    <div class="mb-4">
                                        <InputLabel for="frontend_input" value="Input Type" />
                                        <select
                                            id="frontend_input"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            v-model="form.frontend_input"
                                            required
                                        >
                                            <option value="">Select Input Type</option>
                                            <option v-for="input in frontendInputs" :key="input.id" :value="input.id">{{ input.name }}</option>
                                        </select>
                                        <InputError :message="form.errors.frontend_input" class="mt-2" />
                                    </div>

                                    <div class="mb-4">
                                        <InputLabel for="default_value" value="Default Value (optional)" />
                                        <TextInput
                                            id="default_value"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.default_value"
                                            autocomplete="off"
                                        />
                                        <InputError :message="form.errors.default_value" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Advanced Settings -->
                                <div>
                                    <h3 class="text-lg font-semibold mb-4">Advanced Settings</h3>

                                    <div class="mb-4">
                                        <InputLabel for="sort_order" value="Sort Order" />
                                        <TextInput
                                            id="sort_order"
                                            type="number"
                                            class="mt-1 block w-full"
                                            v-model="form.sort_order"
                                            min="0"
                                        />
                                        <InputError :message="form.errors.sort_order" class="mt-2" />
                                    </div>

                                    <div class="mb-4">
                                        <InputLabel for="frontend_class" value="CSS Class (optional)" />
                                        <TextInput
                                            id="frontend_class"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.frontend_class"
                                            autocomplete="off"
                                        />
                                        <InputError :message="form.errors.frontend_class" class="mt-2" />
                                    </div>

                                    <div class="mb-4">
                                        <InputLabel for="note" value="Admin Note (optional)" />
                                        <TextArea
                                            id="note"
                                            class="mt-1 block w-full"
                                            v-model="form.note"
                                            rows="3"
                                        />
                                        <InputError :message="form.errors.note" class="mt-2" />
                                    </div>

                                    <div class="mb-4">
                                        <div class="flex items-center">
                                            <input
                                                id="is_required"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                v-model="form.is_required"
                                            />
                                            <label for="is_required" class="ml-2 text-sm text-gray-600">Required</label>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="flex items-center">
                                            <input
                                                id="is_unique"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                v-model="form.is_unique"
                                            />
                                            <label for="is_unique" class="ml-2 text-sm text-gray-600">Unique Value</label>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="flex items-center">
                                            <input
                                                id="is_filterable"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                v-model="form.is_filterable"
                                            />
                                            <label for="is_filterable" class="ml-2 text-sm text-gray-600">Filterable</label>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="flex items-center">
                                            <input
                                                id="is_searchable"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                v-model="form.is_searchable"
                                            />
                                            <label for="is_searchable" class="ml-2 text-sm text-gray-600">Searchable</label>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="flex items-center">
                                            <input
                                                id="is_comparable"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                v-model="form.is_comparable"
                                            />
                                            <label for="is_comparable" class="ml-2 text-sm text-gray-600">Comparable</label>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="flex items-center">
                                            <input
                                                id="is_visible_on_front"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                v-model="form.is_visible_on_front"
                                            />
                                            <label for="is_visible_on_front" class="ml-2 text-sm text-gray-600">Visible on Product Page</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link
                                    :href="route('admin.attributes.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition mr-2"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Create Attribute
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Pages/Admin/Components/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    backendTypes: Array,
    frontendInputs: Array,
});

const form = useForm({
    code: '',
    backend_type: '',
    frontend_input: '',
    frontend_label: '',
    is_required: false,
    is_unique: false,
    is_filterable: false,
    is_searchable: false,
    is_comparable: false,
    is_visible_on_front: false,
    sort_order: 0,
    default_value: '',
    frontend_class: '',
    note: '',
});

const submit = () => {
    form.post(route('admin.attributes.store'), {
        onSuccess: () => form.reset(),
    });
};
</script> 