<template>
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">Create Blog Category</h1>
        <p class="mb-4">This is the blog-category/Create.vue component in the Blog module.</p>
        
        <div class="mb-4">
            <inertia-link :href="route('blogs-category.index')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Back to Categories
            </inertia-link>
        </div>
        
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Category Name</label>
                <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            </div>
            
            <div>
                <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Save Category
                </button>
            </div>
        </form>
        
        <div class="mt-6 bg-gray-100 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-2">Page Resolution Information:</h2>
            <pre class="bg-gray-800 text-white p-3 rounded overflow-x-auto">
                Path: blog-category/Create
                Component: Modules/Blog/Resources/js/pages/blog-category/Create.vue
            </pre>
        </div>
    </div>
</template>

<script setup>
import { Link as InertiaLink } from '@inertiajs/vue3';
</script>