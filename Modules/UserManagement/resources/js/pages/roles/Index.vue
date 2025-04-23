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
    title: 'UserManagement',
    href: 'usermanagement.index',
  },
];
</script>

<template>

  <Head title="UserManagement" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div >
        <h1 class="text-2xl font-bold mb-4">Welcome to the Role</h1>
        <p>This is the index page of your new module.</p>
      </div>
    </div>
  </AppLayout>
</template>
