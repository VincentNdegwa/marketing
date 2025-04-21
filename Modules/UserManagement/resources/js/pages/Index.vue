<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

</script>

<template>
  <Head title="UserManagement" />
  
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        UserManagement
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h1 class="text-2xl font-bold mb-4">Welcome to the UserManagement Module</h1>
          <p>This is the index page of your new module.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
