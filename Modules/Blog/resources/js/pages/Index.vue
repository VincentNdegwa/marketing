<script setup lang="ts">
import AppLayout from '../../../../resources/js/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '../types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Blog',
        href: 'blog.index',
    },
];

// Sample blog posts data (in a real application, this would come from the backend)
const posts = [
    {
        id: 1,
        title: 'Getting Started with Laravel Modules',
        excerpt: 'Learn how to organize your Laravel application using modules...',
        image: null
    },
    {
        id: 2,
        title: 'Integrating Vue.js with Laravel Modules',
        excerpt: 'A comprehensive guide to using Vue.js in your modular Laravel app...',
        image: null
    },
    {
        id: 3,
        title: 'Building Scalable Applications with Laravel',
        excerpt: 'Best practices for creating maintainable and scalable Laravel applications...',
        image: null
    }
];
</script>

<template>
    <Head title="Blog" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Blog Posts</h1>
                <a href="/blog/create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    New Post
                </a>
            </div>

            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div v-for="post in posts" :key="post.id" class="border-sidebar-border/70 dark:border-sidebar-border relative overflow-hidden rounded-xl border">
                    <div class="aspect-video relative">
                        <PlaceholderPattern v-if="!post.image" />
                        <img v-else :src="post.image" alt="Blog post image" class="w-full h-full object-cover" />
                    </div>
                    <div class="p-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ post.title }}</h2>
                        <p class="text-gray-600 dark:text-gray-300 mt-2">{{ post.excerpt }}</p>
                        <div class="mt-4">
                            <a :href="`/blog/${post.id}`" class="text-blue-600 dark:text-blue-400 hover:underline">Read more</a>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="posts.length === 0" class="border-sidebar-border/70 dark:border-sidebar-border relative min-h-[50vh] flex-1 rounded-xl border flex items-center justify-center">
                <div class="text-center">
                    <p class="text-gray-500 dark:text-gray-400 mb-4">No blog posts found</p>
                    <a href="/blog/create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Create Your First Post
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
