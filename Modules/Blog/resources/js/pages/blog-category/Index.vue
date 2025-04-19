<template>
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4">Blog Categories</h1>
        <p class="mb-4">This is the blog-category/Index.vue component in the Blog module.</p>
        
        <div class="mb-4">
            <inertia-link :href="route('blogs-category.create')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Create New Category
            </inertia-link>
        </div>
        
        <div class="bg-gray-100 p-4 rounded-lg">
            <h2 class="text-lg font-semibold mb-2">Page Resolution Information:</h2>
            <pre class="bg-gray-800 text-white p-3 rounded overflow-x-auto">
                Path: blog-category/Index
                Component: Modules/Blog/Resources/js/pages/blog-category/Index.vue
            </pre>
        </div>
    </div>
</template>

<script setup>
import { Link as InertiaLink } from '@inertiajs/vue3';
</script>