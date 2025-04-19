<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 bg-white rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Blog Categories</h1>
                <Link 
                    :href="route('blogs-category.create')" 
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Create New Category
                </Link>
            </div>
            
            <!-- Categories list would go here -->
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg text-center">
                <p class="text-gray-500 dark:text-gray-400">No categories found. Create your first category to get started.</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { route } from 'ziggy-js';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Blog Categories',
        href: 'blogs-category.index',
    },
];
</script>