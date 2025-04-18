<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { computed } from 'vue';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Blog',
        href: 'blog.index',
    },
    {
        title: 'View Post',
        href: 'blog.show',
    },
];

// Sample post data (in a real application, this would come from the backend)
const post = {
    id: 1,
    title: 'Getting Started with Laravel Modules',
    content: `
        <p>Laravel Modules is a package by Nicolas Widart that allows you to organize your Laravel application into modules. This makes it easier to maintain and scale your application as it grows.</p>
        
        <h2>Why Use Laravel Modules?</h2>
        <p>As your Laravel application grows, it can become difficult to maintain. By organizing your code into modules, you can:</p>
        <ul>
            <li>Keep related code together</li>
            <li>Make your codebase more maintainable</li>
            <li>Reuse modules across different projects</li>
            <li>Develop and test modules independently</li>
        </ul>
        
        <h2>Getting Started</h2>
        <p>To install Laravel Modules, you can use Composer:</p>
        <pre><code>composer require nwidart/laravel-modules</code></pre>
        
        <p>After installation, you'll need to publish the package's configuration:</p>
        <pre><code>php artisan vendor:publish --provider="Nwidart\\Modules\\LaravelModulesServiceProvider"</code></pre>
        
        <p>You'll also need to update your composer.json file to autoload the modules:</p>
        <pre><code>{
  "autoload": {
    "psr-4": {
      "App\\\\": "app/",
      "Modules\\\\": "Modules/"
    }
  }
}</code></pre>
        
        <p>Don't forget to run <code>composer dump-autoload</code> after making these changes.</p>
    `,
    excerpt: 'Learn how to organize your Laravel application using modules...',
    image: null,
    created_at: '2025-04-15T14:30:00',
    updated_at: '2025-04-15T14:30:00'
};

const formattedDate = computed(() => {
    const date = new Date(post.created_at);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
});
</script>

<template>
    <Head :title="post.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ post.title }}</h1>
                <div class="flex space-x-2">
                    <a :href="`/blog/${post.id}/edit`" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-white uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Edit
                    </a>
                    <a href="/blog" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Back to Blog
                    </a>
                </div>
            </div>

            <div class="border-sidebar-border/70 dark:border-sidebar-border relative rounded-xl border overflow-hidden">
                <div v-if="post.image" class="aspect-video">
                    <img :src="post.image" alt="Blog post image" class="w-full h-full object-cover" />
                </div>
                <div v-else class="aspect-video relative">
                    <PlaceholderPattern />
                </div>
                
                <div class="p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Published on {{ formattedDate }}
                    </div>
                    
                    <div class="prose dark:prose-invert max-w-none" v-html="post.content"></div>
                    
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Last updated: {{ formattedDate }}
                            </div>
                            <div class="flex space-x-4">
                                <button class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                    Share
                                </button>
                                <button class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                    Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
