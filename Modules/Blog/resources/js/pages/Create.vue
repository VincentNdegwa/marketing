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
        title: 'Create Post',
        href: 'blog.create',
    },
];

const title = ref('');
const content = ref('');
const excerpt = ref('');
const image = ref(null);

const submitForm = () => {
    // In a real application, this would submit the form using Inertia or an API call
    console.log('Form submitted', { title: title.value, content: content.value, excerpt: excerpt.value });
};
</script>

<template>
    <Head title="Create Blog Post" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Blog Post</h1>
                <a href="/blog" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-white uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancel
                </a>
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
                            rows="8"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        ></textarea>
                    </div>
                    
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Featured Image</label>
                        <input 
                            type="file" 
                            id="image" 
                            class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                dark:file:bg-blue-900 dark:file:text-blue-200
                                hover:file:bg-blue-100 dark:hover:file:bg-blue-800"
                        />
                    </div>
                    
                    <div class="flex justify-end">
                        <button 
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            Create Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
