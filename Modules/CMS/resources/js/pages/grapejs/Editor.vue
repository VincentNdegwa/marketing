<template>
  <Head :title="isNew ? 'Create Page' : 'Edit Page'" />

  <div class="editor-fullscreen">
    <div class="editor-header bg-card dark:bg-card-dark shadow-sm">
      <div class="container mx-auto px-4 py-2">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div class="flex flex-1 items-center gap-4 min-w-[300px]">
            <div class="flex-1">
              <InputText v-model="form.title" class="w-full text-lg font-semibold" placeholder="Page Title" 
                :class="{ 'p-invalid': errors.title }" @input="generateSlug" />
              <small v-if="errors.title" class="p-error block">{{ errors.title }}</small>
            </div>
            <div class="w-48">
              <div class="flex items-center gap-1">
                <span class="text-xs text-gray-500">/page/</span>
                <InputText v-model="form.slug" class="w-full text-sm" placeholder="url-slug" 
                  :class="{ 'p-invalid': errors.slug }" />
              </div>
              <small v-if="errors.slug" class="p-error block">{{ errors.slug }}</small>
            </div>
          </div>
          
          <!-- Right side - Actions -->
          <div class="flex items-center gap-2">
            <Dropdown v-model="form.status" :options="['draft', 'published']" optionLabel="" class="w-32" 
              :class="{ 'p-invalid': errors.status }">
              <template #value="slotProps">
                <Tag :severity="slotProps.value === 'published' ? 'success' : 'warning'" :value="slotProps.value" />
              </template>
              <template #option="slotProps">
                <Tag :severity="slotProps.option === 'published' ? 'success' : 'warning'" :value="slotProps.option" />
              </template>
            </Dropdown>
            
            <Button v-if="!isNew" icon="pi pi-eye" severity="secondary" outlined @click="previewPage" 
              v-tooltip.bottom="'Preview Page'" />
            <Button icon="pi pi-cog" severity="secondary" outlined @click="showSettings = !showSettings" 
              v-tooltip.bottom="'Page Settings'" />
            <Button icon="pi pi-save" severity="contrast" :loading="saving" @click="savePage" 
              v-tooltip.bottom="'Save Page'" />
            <Button icon="pi pi-times" severity="secondary" outlined 
              @click="$inertia.visit(route('cms.pages.index'))" v-tooltip.bottom="'Exit Editor'" />
          </div>
        </div>
      </div>
    </div>
    
    <!-- Settings Panel -->
    <Sidebar v-model:visible="showSettings" position="right" class="p-sidebar-md" :modal="false">
      <template #header>
        <h3 class="text-xl font-semibold">Page Settings</h3>
      </template>
      
      <div class="p-4 space-y-4">
        <div class="space-y-2">
          <label for="description" class="block text-sm font-medium">Description</label>
          <Textarea id="description" v-model="form.description" rows="3" class="w-full" 
            :class="{ 'p-invalid': errors.description }" 
            placeholder="Brief description of this page (for SEO)" />
          <small v-if="errors.description" class="p-error">{{ errors.description }}</small>
        </div>
        
        <div class="space-y-2">
          <label for="meta_title" class="block text-sm font-medium">Meta Title</label>
          <InputText id="meta_title" v-model="form.meta_title" class="w-full" 
            :class="{ 'p-invalid': errors.meta_title }" 
            placeholder="SEO Title (defaults to page title if empty)" />
          <small v-if="errors.meta_title" class="p-error">{{ errors.meta_title }}</small>
        </div>
        
        <div class="space-y-2">
          <label for="meta_description" class="block text-sm font-medium">Meta Description</label>
          <Textarea id="meta_description" v-model="form.meta_description" rows="3" class="w-full" 
            :class="{ 'p-invalid': errors.meta_description }" 
            placeholder="SEO Description" />
          <small v-if="errors.meta_description" class="p-error">{{ errors.meta_description }}</small>
        </div>
        
        <div class="space-y-2">
          <label for="meta_keywords" class="block text-sm font-medium">Meta Keywords</label>
          <InputText id="meta_keywords" v-model="form.meta_keywords" class="w-full" 
            :class="{ 'p-invalid': errors.meta_keywords }" 
            placeholder="keyword1, keyword2, keyword3" />
          <small v-if="errors.meta_keywords" class="p-error">{{ errors.meta_keywords }}</small>
        </div>
      </div>
    </Sidebar>
    
    <!-- Main Editor -->
    <div class="editor-container">
      <GrapesJSEditor 
        :project-id="pageId" 
        :project-data="editorData" 
        @save="handleEditorSave" 
        @error="handleEditorError"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import Tag from 'primevue/tag';
import Sidebar from 'primevue/sidebar';
import GrapesJSEditor from '../../components/GrapesJSEditor.vue';
import { route } from 'ziggy-js';

const props = defineProps({
  page: {
    type: Object,
    default: null
  },
  errors: {
    type: Object,
    default: () => ({})
  }
});

interface ProjectData {
  html?: string;
  css?: string;
  pages?: Array<{
    name: string;
    component: string;
  }>;
  [key: string]: any;
}

const toast = useToast();

const pageId = ref(props.page?.id || null);
const isNew = computed(() => !pageId.value);
const saving = ref(false);
const editorData = ref(props.page?.content || {});
const showSettings = ref(false);

// Initialize form with page data or defaults
const form = ref({
  title: props.page?.title || '',
  slug: props.page?.slug || '',
  description: props.page?.description || '',
  status: props.page?.status || 'draft',
  content: props.page?.content || {},
  meta_title: props.page?.meta_title || '',
  meta_description: props.page?.meta_description || '',
  meta_keywords: props.page?.meta_keywords || ''
});

// Generate slug from title if slug is empty
const generateSlug = () => {
  if (!form.value.slug || form.value.slug === '') {
    form.value.slug = form.value.title
      .toLowerCase()
      .replace(/[^\w\s-]/g, '') // Remove special characters
      .replace(/\s+/g, '-')     // Replace spaces with hyphens
      .replace(/-+/g, '-')      // Replace multiple hyphens with a single one
      .trim();
  }
};

const handleEditorSave = (data: ProjectData) => {
  form.value.content = data;
};

const handleEditorError = (error: ProjectData) => {
  toast.add({ severity: 'error', summary: 'Editor Error', detail: 'There was a problem with the page editor', life: 3000 });
  console.error('Editor error:', error);
};

const savePage = () => {
  saving.value = true;
  
  // Make sure we have content data from the editor
  if (!form.value.content || Object.keys(form.value.content).length === 0) {
    form.value.content = { pages: [{ component: '<div class="section"><h1>' + form.value.title + '</h1><p>Content goes here</p></div>' }] };
  }
  
  try {
    if (isNew.value) {
      // Creating a new page
      router.post(route('cms.pages.store'), form.value, {
        onSuccess: () => {
          toast.add({ severity: 'success', summary: 'Success', detail: 'Page created successfully', life: 3000 });
          saving.value = false;
        },
        onError: (errors) => {
          console.error('Error saving page:', errors);
          toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save page', life: 3000 });
          saving.value = false;
        }
      });
    } else {
      // Updating an existing page
      router.put(route('cms.pages.update', pageId.value), form.value, {
        onSuccess: () => {
          toast.add({ severity: 'success', summary: 'Success', detail: 'Page updated successfully', life: 3000 });
          saving.value = false;
        },
        onError: (errors) => {
          console.error('Error updating page:', errors);
          toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to update page', life: 3000 });
          saving.value = false;
        }
      });
    }
  } catch (error) {
    console.error('Exception saving page:', error);
    toast.add({ severity: 'error', summary: 'Error', detail: 'An unexpected error occurred', life: 3000 });
    saving.value = false;
  }
};

const previewPage = () => {
  // Open preview in new tab
  if (pageId.value) {
    window.open(route('cms.pages.preview', pageId.value), '_blank');
  }
};
</script>

<style scoped>
.editor-fullscreen {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 100;
  background-color: white;
  display: flex;
  flex-direction: column;
}

.editor-header {
  flex: 0 0 auto;
  z-index: 10;
}

.editor-container {
  flex: 1;
  position: relative;
  overflow: hidden;
}

:deep(.p-sidebar-md) {
  width: 25rem;
}

:deep(.p-sidebar) {
  background-color: var(--surface-card);
}

:deep(.p-sidebar-header) {
  padding: 1rem;
  border-bottom: 1px solid var(--surface-border);
}

.dark .editor-fullscreen {
  background-color: var(--surface-ground);
}
</style>
