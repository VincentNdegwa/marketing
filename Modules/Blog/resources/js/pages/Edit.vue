<script setup lang="ts">
import AppLayout from '../../../../resources/js/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '../types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Blog',
        href: 'blog.index',
    },
    {
        title: 'Edit Post',
        href: 'blog.edit',
    },
];

// Sample post data (in a real application, this would come from the backend)
const post = {
    id: 1,
    title: 'Getting Started with Laravel Modules',
    content: `Laravel Modules is a package by Nicolas Widart that allows you to organize your Laravel application into modules. This makes it easier to maintain and scale your application as it grows.

Why Use Laravel Modules?
As your Laravel application grows, it can become difficult to maintain. By organizing your code into modules, you can:
- Keep related code together
- Make your codebase more maintainable
- Reuse modules across different projects
- Develop and test modules independently

Getting Started
To install Laravel Modules, you can use Composer:
composer require nwidart/laravel-modules

After installation, you'll need to publish the package's configuration:
php artisan vendor:publish --provider="Nwidart\\Modules\\LaravelModulesServiceProvider"`,
    excerpt: 'Learn how to organize your Laravel application using modules...',
    image: null
};

const title = ref(post.title);
const content = ref(post.content);
const excerpt = ref(post.excerpt);

const submitForm = () => {
    // In a real application, this would submit the form using Inertia or an API call
    console.log('Form submitted', { id: post.id, title: title.value, content: content.value, excerpt: excerpt.value });
};
</script>

<template>
    <Head title="Edit Blog Post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Blog Post</h1>
                <div class="flex space-x-2">
                    <a :href="`/blog/${post.id}`" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-white uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button 
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Delete
                    </button>
                </div>
            </div>

            <div class="border-sidebar-border/70 dark:border-sidebar-border relative rounded-xl border p-6">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input 
                            type="text" 
                            id="title" 
                            v-model="title" 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        />
                    </div>
                    
                    <div>
                        <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Excerpt</label>
                        <textarea 
                            id="excerpt" 
                            v-model="excerpt" 
                            rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>
                    
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
                        <textarea 
                            id="content" 
                            v-model="content" 
                            rows="12"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        ></textarea>
                    </div>
                    
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Featured Image</label>
                        <div class="mt-1 flex items-center">
                            <div v-if="post.image" class="relative h-20 w-20 mr-4 rounded-md overflow-hidden">
                                <img :src="post.image" alt="Current featured image" class="h-full w-full object-cover" />
                                <button type="button" class="absolute top-0 right-0 bg-red-600 text-white rounded-bl-md p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <input 
                                type="file" 
                                id="image" 
                                class="text-sm text-gray-500 dark:text-gray-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    dark:file:bg-blue-900 dark:file:text-blue-200
                                    hover:file:bg-blue-100 dark:hover:file:bg-blue-800"
                            />
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button 
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            Update Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
