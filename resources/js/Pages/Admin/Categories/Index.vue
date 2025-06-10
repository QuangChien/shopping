<template>
    <AdminLayout>
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Category List
            </h2>
            <Link
                :href="route('admin.categories.create')"
                class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105"
            >
                <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                Create New
            </Link>
        </div>

        <div class="py-6">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-md sm:rounded-lg border border-gray-200">
                    <!-- Filters Bar with Gradient Background -->
                    <div class="p-6 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-700 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            All Categories
                        </h3>

                        <!-- Filter and Search -->
                        <div class="grid gap-4 md:grid-cols-4">
                            <div class="col-span-2">
                                <label for="search" class="sr-only">Search Categories</label>
                                <div class="relative mt-1 rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <MagnifyingGlassIcon class="h-5 w-5 text-indigo-500" aria-hidden="true" />
                                    </div>
                                    <input
                                        type="text"
                                        name="search"
                                        id="search"
                                        v-model="searchInput"
                                        @focus="handleInputChange"
                                        class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="Search categories"
                                    />
                                </div>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select
                                    id="status"
                                    name="status"
                                    v-model="statusFilter"
                                    @change="handleInputChange"
                                    class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent</label>
                                <select
                                    id="parent_id"
                                    name="parent_id"
                                    v-model="parentFilter"
                                    @change="handleInputChange"
                                    class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">All</option>
                                    <option v-for="category in parentCategories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Categories Stats -->
                        <div class="mb-6 flex flex-wrap gap-4">
                            <div class="flex items-center bg-blue-50 p-3 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-blue-700">Total Categories</div>
                                    <div class="text-2xl font-bold text-blue-900">{{ categories.total }}</div>
                                </div>
                            </div>
                            <div class="flex items-center bg-green-50 p-3 rounded-lg shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-green-700">Active Categories</div>
                                    <div class="text-2xl font-bold text-green-900">
                                        {{ categories.data.filter(c => c.is_active).length }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Loading Indicator -->
                        <LoadingIndicator
                            v-if="isLoading"
                            overlay
                            text="Loading..."
                            color="indigo"
                        >
                            <!-- Categories Table (This will be overlaid with the loading indicator) -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border">
                                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                ID
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Name
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 hidden md:table-cell">
                                                Slug
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 hidden md:table-cell">
                                                Parent
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 hidden sm:table-cell">
                                                Sort Order
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        <tr v-for="(category, index) in categories.data" :key="category.id"
                                            class="transition-colors hover:bg-indigo-50/50"
                                            :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'"
                                        >
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-indigo-700 w-12">
                                                #{{ category.id }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 flex-shrink-0 bg-indigo-100 text-indigo-700 rounded-md flex items-center justify-center mr-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-semibold text-gray-900">{{ category.name }}</div>
                                                        <div v-if="category.description" class="text-xs text-gray-500 truncate max-w-xs">
                                                            {{ category.description }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
                                                <span class="bg-gray-100 px-2 py-1 rounded text-xs font-mono">{{ category.slug }}</span>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 hidden md:table-cell">
                                                <div v-if="category.parent" class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                                    </svg>
                                                    {{ category.parent.name }}
                                                </div>
                                                <div v-else class="text-gray-400">-</div>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                                <span
                                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                                    :class="category.is_active
                                                        ? 'bg-green-100 text-green-800 border border-green-200'
                                                        : 'bg-red-100 text-red-800 border border-red-200'"
                                                >
                                                    <span class="mr-1 h-1.5 w-1.5 rounded-full" :class="category.is_active ? 'bg-green-500' : 'bg-red-500'"></span>
                                                    {{ category.is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 hidden sm:table-cell">
                                                <span class="bg-gray-100 px-2 py-1 rounded">{{ category.sort_order || 0 }}</span>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <Link
                                                        :href="route('admin.categories.edit', category.id)"
                                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors flex items-center"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        Edit
                                                    </Link>
                                                    <button
                                                        @click="confirmDelete(category)"
                                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors flex items-center"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="categories.data.length === 0">
                                            <td colspan="7" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="text-gray-500 text-lg">No categories found</span>
                                                    <span class="text-gray-400 text-sm mt-1">Try changing your search criteria or create a new category</span>
                                                    <Link
                                                        :href="route('admin.categories.create')"
                                                        class="mt-4 inline-flex items-center rounded-md border border-transparent bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                    >
                                                        <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                                        Create Category
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </LoadingIndicator>

                        <!-- Table without loading -->
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            ID
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 hidden md:table-cell">
                                            Slug
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 hidden md:table-cell">
                                            Parent
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 hidden sm:table-cell">
                                            Sort Order
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="(category, index) in categories.data" :key="category.id"
                                        class="transition-colors hover:bg-indigo-50/50"
                                        :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'"
                                    >
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-indigo-700 w-12">
                                            #{{ category.id }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 flex-shrink-0 bg-indigo-100 text-indigo-700 rounded-md flex items-center justify-center mr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">{{ category.name }}</div>
                                                    <div v-if="category.description" class="text-xs text-gray-500 truncate max-w-xs">
                                                        {{ category.description }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
                                            <span class="bg-gray-100 px-2 py-1 rounded text-xs font-mono">{{ category.slug }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 hidden md:table-cell">
                                            <div v-if="category.parent" class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                                </svg>
                                                {{ category.parent.name }}
                                            </div>
                                            <div v-else class="text-gray-400">-</div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                                :class="category.is_active
                                                    ? 'bg-green-100 text-green-800 border border-green-200'
                                                    : 'bg-red-100 text-red-800 border border-red-200'"
                                            >
                                                <span class="mr-1 h-1.5 w-1.5 rounded-full" :class="category.is_active ? 'bg-green-500' : 'bg-red-500'"></span>
                                                {{ category.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 hidden sm:table-cell">
                                            <span class="bg-gray-100 px-2 py-1 rounded">{{ category.sort_order || 0 }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <Link
                                                    :href="route('admin.categories.edit', category.id)"
                                                    class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition-colors flex items-center"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </Link>
                                                <button
                                                    @click="confirmDelete(category)"
                                                    class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors flex items-center"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="categories.data.length === 0">
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-gray-500 text-lg">No categories found</span>
                                                <span class="text-gray-400 text-sm mt-1">Try changing your search criteria or create a new category</span>
                                                <Link
                                                    :href="route('admin.categories.create')"
                                                    class="mt-4 inline-flex items-center rounded-md border border-transparent bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                >
                                                    <PlusIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                                    Create Category
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <Pagination :links="categories.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="closeModal">
            <div class="p-6">
                <div class="bg-red-50 p-4 rounded-lg mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-red-800">
                                Delete Category
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>{{ selectedCategory ? `'${selectedCategory.name}'` : '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal" class="mr-2">
                        Cancel
                    </SecondaryButton>
                    <DangerButton
                        @click="deleteCategory"
                        :class="{ 'opacity-25': processing }"
                        :disabled="processing"
                    >
                        <span v-if="processing" class="mr-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        Delete
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { PlusIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/solid';
import AdminLayout from '@/Pages/Admin/Components/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import LoadingIndicator from '@/Components/LoadingIndicator.vue';

const props = defineProps({
    categories: Object,
    filters: Object,
    parentCategories: Array
});

// Filter and search state
const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.is_active || '');
const parentFilter = ref(props.filters.parent_id || '');
const processing = ref(false);
const isLoading = ref(false);

// Delete modal state
const showDeleteModal = ref(false);
const selectedCategory = ref(null);

// Set isLoading = true when the user starts interacting with the filter
const handleInputChange = () => {
    isLoading.value = true;
};

// Watch search input
const searchInput = ref(search.value);
watch(searchInput, debounce((value) => {
    isLoading.value = true;
    search.value = value;
    applyFilters();
}, 500));

// Apply filters when they change
watch([statusFilter, parentFilter], () => {
    isLoading.value = true;
    applyFilters();
});

// Filter apply function
const applyFilters = () => {
    router.get(route('admin.categories.index'), {
        search: search.value,
        is_active: statusFilter.value,
        parent_id: parentFilter.value
    }, {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            // Add setTimeout to ensure loading is visible long enough for users to see
            setTimeout(() => {
                isLoading.value = false;
            }, 800);
        },
        onError: () => {
            isLoading.value = false;
        }
    });
};

// Delete category confirmation
const confirmDelete = (category) => {
    selectedCategory.value = category;
    showDeleteModal.value = true;
};

// Close modal
const closeModal = () => {
    showDeleteModal.value = false;
    selectedCategory.value = null;
};

// Delete category
const deleteCategory = () => {
    if (!selectedCategory.value) return;

    processing.value = true;

    router.delete(route('admin.categories.destroy', selectedCategory.value.id), {
        onSuccess: () => {
            closeModal();
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        }
    });
};

// Debounce function for search
function debounce(fn, delay) {
    let timeoutID = null;
    return function() {
        clearTimeout(timeoutID);
        const args = arguments;
        const that = this;
        timeoutID = setTimeout(function() {
            fn.apply(that, args);
        }, delay);
    };
}
</script>
