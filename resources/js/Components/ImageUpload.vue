<template>
    <div class="relative">
        <div class="space-y-2">
            <label v-if="label" :for="inputId" class="block text-sm font-medium text-gray-700 flex items-center">
                <svg v-if="!hideIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ label }} <span v-if="required" class="text-red-500 ml-1">*</span>
            </label>

            <!-- File uploaded or previewing -->
            <div v-if="modelValue || previewSrc" class="relative">
                <div class="mb-2 relative border border-gray-200 rounded-lg overflow-hidden bg-gray-50 shadow-sm flex justify-center items-center group">
                    <img
                        :src="previewSrc || modelValue"
                        alt="Uploaded image"
                        class="max-h-48 object-contain"
                    />
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <button
                            type="button"
                            class="p-2 bg-red-600 text-white rounded-full shadow-md hover:bg-red-700 transition-colors"
                            @click.prevent="removeImage"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center text-xs text-gray-500 mb-1">
                    <span class="truncate max-w-xs">{{ previewFilename || extractFilename(modelValue) }}</span>
                </div>

                <input type="hidden" :name="name" :value="modelValue" />
            </div>

            <!-- New upload -->
            <div v-else>
                <div
                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md relative"
                    :class="[
                        dragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 hover:border-indigo-400',
                        uploadError || error ? 'border-red-300 bg-red-50' : ''
                    ]"
                    @dragenter.prevent="dragging = true"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="handleDrop"
                >
                    <div class="space-y-1 text-center">
                        <svg
                            class="mx-auto h-12 w-12"
                            :class="uploadError || error ? 'text-red-400' : 'text-gray-400'"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 48 48"
                            aria-hidden="true"
                        >
                            <path
                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                        <div class="flex justify-center text-sm" :class="uploadError || error ? 'text-red-500' : 'text-gray-600'">
                            <label
                                :for="inputId"
                                class="relative cursor-pointer rounded-md font-medium text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:text-indigo-500"
                            >
                                <span>{{ uploadText }}</span>
                                <input
                                    :id="inputId"
                                    :name="name"
                                    type="file"
                                    class="sr-only"
                                    :accept="accept"
                                    @change="handleFileChange"
                                    :required="required && !modelValue"
                                />
                            </label>
                            <p class="pl-1">{{ dragText }}</p>
                        </div>
                        <p class="text-xs" :class="uploadError || error ? 'text-red-500' : 'text-gray-500'">
                            {{ helperText }}
                        </p>
                    </div>
                </div>

                <div v-if="uploadError || error" class="mt-1 text-sm text-red-600 flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ uploadError || error }}
                </div>
            </div>

            <div v-if="isUploading" class="mt-2">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-indigo-600 rounded-full transition-all duration-200"
                        :style="{ width: `${uploadProgress}%` }"
                    ></div>
                </div>
                <div class="text-xs text-gray-500 mt-1 text-center">{{ uploadProgress }}% đã tải lên</div>
            </div>

            <!-- Debug Info (delete in production) -->
            <div v-if="false" class="mt-4 p-2 bg-gray-100 rounded text-xs">
                <div><strong>modelValue:</strong> {{ modelValue }}</div>
                <div><strong>previewSrc:</strong> {{ previewSrc }}</div>
                <div><strong>previewFilename:</strong> {{ previewFilename }}</div>
                <div><strong>uploadedPath:</strong> {{ uploadedPath }}</div>
                <div><strong>Error:</strong> {{ uploadError || error }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    name: {
        type: String,
        required: true
    },
    label: {
        type: String,
        default: 'Image'
    },
    folder: {
        type: String,
        default: 'images'
    },
    accept: {
        type: String,
        default: 'image/*'
    },
    required: {
        type: Boolean,
        default: false
    },
    uploadText: {
        type: String,
        default: 'Upload file'
    },
    dragText: {
        type: String,
        default: 'or drag and drop anywhere'
    },
    helperText: {
        type: String,
        default: 'PNG, JPG, WEBP tối đa 2MB'
    },
    error: {
        type: String,
        default: ''
    },
    hideIcon: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue', 'change', 'uploaded', 'error', 'deleted']);

const inputId = computed(() => `file-upload-${Math.random().toString(36).substring(2, 10)}`);
const dragging = ref(false);
const isUploading = ref(false);
const uploadProgress = ref(0);
const previewSrc = ref(null);
const previewFilename = ref(null);
const uploadedPath = ref('');
const uploadError = ref('');

// Load existing image when component is mounted
onMounted(() => {
    if (props.modelValue) {
        // If we already have an image URL, set it as preview
        previewSrc.value = props.modelValue;
        previewFilename.value = extractFilename(props.modelValue);
    }
});

// Watch for changes in the model value
watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        previewSrc.value = newValue;
        previewFilename.value = extractFilename(newValue);
    } else {
        previewSrc.value = null;
        previewFilename.value = null;
    }
});

// Handling when selecting file from input
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Show instant preview
        showPreview(file);
        // Then upload the file
        uploadFile(file);
    }
};

// Handling when dragging and dropping files
const handleDrop = (event) => {
    dragging.value = false;
    const file = event.dataTransfer.files[0];
    if (file) {
        // Show instant preview
        showPreview(file);
        // Then upload the file
        uploadFile(file);
    }
};

// Show image preview
const showPreview = (file) => {
    if (!file || !file.type.startsWith('image/')) {
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        previewSrc.value = e.target.result;
        previewFilename.value = file.name;

        // Emit change event for preview
        emit('change', {
            preview: true,
            src: e.target.result,
            filename: file.name
        });
    };
    reader.readAsDataURL(file);
};

// Upload file to server
const uploadFile = async (file) => {
    // Reset error
    uploadError.value = '';

    // Check file type
    if (!file.type.startsWith('image/')) {
        const error = 'Chỉ hỗ trợ file hình ảnh';
        uploadError.value = error;
        emit('error', error);
        return;
    }

    // Check file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        const error = 'Kích thước file không được vượt quá 2MB';
        uploadError.value = error;
        emit('error', error);
        return;
    }

    // Create FormData to upload
    const formData = new FormData();
    formData.append('image', file);
    formData.append('folder', props.folder);

    isUploading.value = true;
    uploadProgress.value = 0;

    try {
        // Send request to upload file
        const xhr = new XMLHttpRequest();

        xhr.open('POST', route('admin.upload.image'));

        // Add CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            xhr.setRequestHeader('X-CSRF-TOKEN', token);
        }

        // Track upload progress
        xhr.upload.addEventListener('progress', (event) => {
            if (event.lengthComputable) {
                uploadProgress.value = Math.round((event.loaded * 100) / event.total);
            }
        });

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Update value when upload successful
                    uploadedPath.value = response.path;
                    emit('update:modelValue', response.url);
                    emit('uploaded', {
                        url: response.url,
                        path: response.path,
                        filename: response.filename
                    });
                } else {
                    const error = response.message || 'Upload failed';
                    uploadError.value = error;
                    emit('error', error);
                }
            } else {
                const error = 'An error occurred while uploading.';
                uploadError.value = error;
                emit('error', error);
            }
            isUploading.value = false;
        };

        xhr.onerror = function() {
            const error = 'Kết nối thất bại';
            uploadError.value = error;
            emit('error', error);
            isUploading.value = false;
        };

        xhr.send(formData);
    } catch (error) {
        const errorMessage = error.message || 'An error occurred while uploading.';
        uploadError.value = errorMessage;
        emit('error', errorMessage);
        isUploading.value = false;
    }
};

// Xóa ảnh đã upload
const removeImage = async () => {
    if (confirm('Are you sure you want to delete this photo?')) {
        try {
            // If there is a path to the uploaded file, send a deletion request.
            if (uploadedPath.value) {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                await fetch(route('admin.upload.image.delete'), {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ path: uploadedPath.value })
                });
            }

            // Reset value
            previewSrc.value = null;
            previewFilename.value = null;
            uploadedPath.value = '';
            emit('update:modelValue', '');
            emit('deleted');
            emit('change', '');
        } catch (error) {
            console.error('Error deleting image:', error);
        }
    }
};

// Extract file name from URL
const extractFilename = (url) => {
    if (!url) return '';
    const parts = url.split('/');
    return parts[parts.length - 1];
};
</script>
