<template>
    <div class="mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center border-b pb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Product Images
        </h3>

        <!-- Main Product Image -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Main Product Image
            </label>
            
            <!-- Image Preview -->
            <div v-if="mainImagePreview" class="flex items-start mb-4">
                <div class="relative w-32 h-32 border rounded-md overflow-hidden bg-gray-100">
                    <img :src="mainImagePreview" class="w-full h-full object-cover" />
                    <button 
                        type="button" 
                        @click="removeMainImage" 
                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="ml-4">
                    <div class="flex items-center">
                        <label for="alt_text_main" class="block text-sm font-medium text-gray-700 mr-2">
                            Alt Text:
                        </label>
                        <input
                            type="text"
                            id="alt_text_main"
                            v-model="form.media.main.alt_text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs"
                            placeholder="Alternative text for accessibility"
                        />
                    </div>
                    <div class="flex items-center mt-2">
                        <label for="title_main" class="block text-sm font-medium text-gray-700 mr-2">
                            Title:
                        </label>
                        <input
                            type="text"
                            id="title_main"
                            v-model="form.media.main.title"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs"
                            placeholder="Image title"
                        />
                    </div>
                </div>
            </div>
            
            <!-- Upload Form -->
            <div v-if="!mainImagePreview" class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600">
                        <label for="main_image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                            <span>Upload main image</span>
                            <input 
                                id="main_image" 
                                type="file" 
                                class="sr-only" 
                                accept="image/*"
                                @change="handleMainImageUpload"
                            />
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">
                        PNG, JPG, GIF up to 5MB
                    </p>
                </div>
            </div>
            
            <!-- Error message -->
            <div v-if="getMediaError('main')" class="mt-1 text-sm text-red-600">
                {{ getMediaError('main') }}
            </div>
        </div>

        <!-- Additional Product Images -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Additional Product Images
            </label>
            
            <!-- Gallery Preview -->
            <div v-if="form.media.gallery.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-4">
                <div v-for="(image, index) in form.media.gallery" :key="index" class="relative">
                    <div class="border rounded-md overflow-hidden bg-gray-100 aspect-square">
                        <img :src="getGalleryImagePreview(image)" class="w-full h-full object-cover" />
                        <button 
                            type="button" 
                            @click="removeGalleryImage(index)" 
                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <button 
                            type="button" 
                            @click="editGalleryImage(index)" 
                            class="absolute bottom-1 right-1 bg-blue-500 text-white rounded-full p-1 hover:bg-blue-600 focus:outline-none"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Upload Form -->
            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600">
                        <label for="gallery_images" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                            <span>Upload gallery images</span>
                            <input 
                                id="gallery_images" 
                                type="file" 
                                class="sr-only" 
                                accept="image/*"
                                multiple
                                @change="handleGalleryImageUpload"
                            />
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">
                        PNG, JPG, GIF up to 5MB
                    </p>
                </div>
            </div>
            
            <!-- Error message -->
            <div v-if="getMediaError('gallery')" class="mt-1 text-sm text-red-600">
                {{ getMediaError('gallery') }}
            </div>
        </div>

        <!-- Image Edit Modal -->
        <div v-if="editingImage !== null" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Image Information</h3>
                
                <div class="mb-4">
                    <label for="edit_alt_text" class="block text-sm font-medium text-gray-700">
                        Alt Text
                    </label>
                    <input
                        type="text"
                        id="edit_alt_text"
                        v-model="editingImageData.alt_text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Alternative text for accessibility"
                    />
                </div>
                
                <div class="mb-4">
                    <label for="edit_title" class="block text-sm font-medium text-gray-700">
                        Title
                    </label>
                    <input
                        type="text"
                        id="edit_title"
                        v-model="editingImageData.title"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Image title"
                    />
                </div>
                
                <div class="flex justify-end">
                    <button
                        type="button"
                        @click="cancelEditImage"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="saveEditImage"
                        class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    form: Object,
    errors: Object,
});

// Initialize media object if it doesn't exist
if (!props.form.media) {
    props.form.media = {
        main: {
            file: null,
            path: '',
            alt_text: '',
            title: '',
            is_main: true
        },
        gallery: []
    };
}

// Editing image state
const editingImage = ref(null);
const editingImageData = ref({
    alt_text: '',
    title: ''
});

// Main image preview
const mainImagePreview = computed(() => {
    if (props.form.media.main.file instanceof File) {
        return URL.createObjectURL(props.form.media.main.file);
    } else if (props.form.media.main.path) {
        return props.form.media.main.path.startsWith('http') 
            ? props.form.media.main.path 
            : `/storage/${props.form.media.main.path}`;
    }
    return null;
});

// Handle main image upload
const handleMainImageUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    props.form.media.main = {
        file: file,
        path: URL.createObjectURL(file),
        alt_text: props.form.media.main.alt_text || '',
        title: props.form.media.main.title || '',
        is_main: true
    };
};

// Remove main image
const removeMainImage = () => {
    props.form.media.main = {
        file: null,
        path: '',
        alt_text: '',
        title: '',
        is_main: true
    };
};

// Get gallery image preview
const getGalleryImagePreview = (image) => {
    if (image.file instanceof File) {
        return URL.createObjectURL(image.file);
    } else if (image.path) {
        return image.path.startsWith('http') 
            ? image.path 
            : `/storage/${image.path}`;
    }
    return null;
};

// Handle gallery image upload
const handleGalleryImageUpload = (event) => {
    const files = event.target.files;
    if (!files.length) return;
    
    Array.from(files).forEach(file => {
        props.form.media.gallery.push({
            file: file,
            path: URL.createObjectURL(file),
            alt_text: '',
            title: '',
            is_main: false
        });
    });
};

// Remove gallery image
const removeGalleryImage = (index) => {
    props.form.media.gallery.splice(index, 1);
};

// Edit gallery image
const editGalleryImage = (index) => {
    editingImage.value = index;
    editingImageData.value = {
        alt_text: props.form.media.gallery[index].alt_text || '',
        title: props.form.media.gallery[index].title || ''
    };
};

// Cancel edit image
const cancelEditImage = () => {
    editingImage.value = null;
};

// Save edit image
const saveEditImage = () => {
    if (editingImage.value !== null) {
        props.form.media.gallery[editingImage.value].alt_text = editingImageData.value.alt_text;
        props.form.media.gallery[editingImage.value].title = editingImageData.value.title;
        editingImage.value = null;
    }
};

// Helper to get errors for media fields
const getMediaError = (field) => {
    if (props.errors && props.errors.media && props.errors.media[field]) {
        return props.errors.media[field];
    }
    return null;
};

// Clean up object URLs when component is unmounted
watch(() => props.form.media.main.file, (newVal, oldVal) => {
    if (oldVal instanceof File && !newVal) {
        URL.revokeObjectURL(props.form.media.main.path);
    }
});
</script> 