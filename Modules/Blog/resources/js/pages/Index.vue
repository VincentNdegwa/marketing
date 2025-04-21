<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: 'dashboard',
    },
    {
        title: 'Blog',
        href: 'blog.index',
    },
];
</script>

<template>
  <Head title="Blog" />
  
  <AppLayout :breadcrumbs="breadcrumbs" >
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h1 class="text-2xl font-bold mb-4">Welcome to the Blog Module</h1>
          <p>This is the index page of your new module.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
