<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import Card from 'primevue/card';
import Button from 'primevue/button';
import { route } from 'ziggy-js';
import { usePermissions } from '@/composables/usePermissions';

const { hasPermission } = usePermissions();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: 'dashboard',
    },
    {
        title: 'CMS',
        href: 'cms.index',
    },
];

const cmsFeatures = [
  {
    title: 'Pages',
    description: 'Create and manage website pages with our drag-and-drop editor',
    icon: 'pi pi-file',
    route: 'cms.pages.index',
    permission: 'cms.pages.view'
  },
  {
    title: 'Templates',
    description: 'Create reusable templates for your pages',
    icon: 'pi pi-copy',
    route: 'cms.templates.index',
    permission: 'cms.templates.view'
  },
  {
    title: 'Media Library',
    description: 'Upload and manage images and other media files',
    icon: 'pi pi-images',
    route: 'cms.media.index',
    permission: 'cms.media.view'
  },
  {
    title: 'Settings',
    description: 'Configure CMS settings and options',
    icon: 'pi pi-cog',
    route: 'cms.settings.index',
    permission: 'cms.settings.view'
  }
];
</script>

<template>
  <Head title="CMS" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Content Management System</h1>
        <Button v-if="hasPermission('cms.pages.create')" label="Create New Page" icon="pi pi-plus" severity="contrast" 
          @click="$inertia.visit(route('cms.pages.create'))" />
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card v-for="feature in cmsFeatures" :key="feature.title" class="bg-card dark:bg-card-dark h-full">
          <template #header>
            <div class="flex items-center justify-center h-16 bg-primary bg-opacity-10 dark:bg-opacity-20">
              <i :class="[feature.icon, 'text-4xl text-primary']" />
            </div>
          </template>
          <template #title>
            <h3 class="text-lg font-semibold">{{ feature.title }}</h3>
          </template>
          <template #content>
            <p class="mb-4">{{ feature.description }}</p>
            <div class="flex justify-end">
              <Button v-if="!feature.permission || hasPermission(feature.permission)" 
                label="Manage" icon="pi pi-arrow-right" outlined severity="contrast" 
                @click="$inertia.visit(route(feature.route))" />
            </div>
          </template>
        </Card>
      </div>
      
      <Card class="mt-4 bg-card dark:bg-card-dark">
        <template #title>
          <h2 class="text-xl font-bold">Getting Started with GrapesJS</h2>
        </template>
        <template #content>
          <p class="mb-4">
            The CMS module uses GrapesJS Studio SDK to provide a powerful drag-and-drop page builder experience. 
            You can create beautiful, responsive pages without writing any code.
          </p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div class="border rounded-lg p-4">
              <h3 class="text-lg font-semibold mb-2">Key Features</h3>
              <ul class="list-disc pl-5 space-y-1">
                <li>Drag-and-drop interface</li>
                <li>Responsive design tools</li>
                <li>Rich text editing</li>
                <li>Image and media management</li>
                <li>Reusable templates</li>
              </ul>
            </div>
            <div class="border rounded-lg p-4">
              <h3 class="text-lg font-semibold mb-2">Quick Links</h3>
              <div class="space-y-2">
                <Button label="Try the Editor" icon="pi pi-pencil" class="w-full justify-content-start" 
                  @click="$inertia.visit(route('cms.pages.create'))" />
                <!-- <Button label="View Documentation" icon="pi pi-book" class="w-full justify-content-start" 
                  @click="window.open('https://grapesjs.com/docs/', '_blank')" /> -->
              </div>
            </div>
          </div>
        </template>
      </Card>
    </div>
  </AppLayout>
</template>
