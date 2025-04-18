<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

import { Head } from '@inertiajs/vue3';
import { BlogPost } from '../../types'

defineProps<{
  posts: BlogPost[]
}>();
</script>

<template>
  <div>
    <Head title="Blog" />
    <AppLayout :breadcrumbs="[]" >
      
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-lg font-medium text-gray-900">Blog Posts</h2>
              <a href="/blog/create" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150">
                Create Post
              </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="post in posts" :key="post.id" class="border border-gray-200 overflow-hidden rounded-lg">
                <div class="p-4">
                  <h2 class="text-lg font-semibold text-gray-900">{{ post.title }}</h2>
                  <p class="text-gray-600 mt-2">{{ post.excerpt }}</p>
                  <div class="mt-4">
                    <a :href="`/blog/${post.id}`" class="text-blue-600 hover:underline">Read more</a>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-if="posts.length === 0" class="border border-gray-200 min-h-[50vh] flex-1 rounded-lg flex items-center justify-center">
              <div class="text-center">
                <p class="text-gray-500 mb-4">No blog posts found</p>
                <a href="/blog/create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none transition ease-in-out duration-150">
                  Create Your First Post
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>

  </div>
</template>
