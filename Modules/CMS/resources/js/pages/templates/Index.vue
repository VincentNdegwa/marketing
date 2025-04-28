<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// Define Template interface
interface Template {
  id: number;
  name: string;
  description?: string;
  content?: any;
  thumbnail?: string;
  category?: string;
  user_id: number;
  created_at: string;
  updated_at: string;
  pages_count?: number;
  user?: {
    id: number;
    name: string;
  };
}
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Menu from 'primevue/menu';
import { useToast } from 'primevue/usetoast';
import { usePermissions } from '@/composables/usePermissions';
import { route } from 'ziggy-js';

const { hasPermission } = usePermissions();
const toast = useToast();
const menu = ref<InstanceType<typeof Menu> | null>(null);
const currentTemplate = ref<Template | null>(null);
const deleteDialog = ref(false);
const templateToDelete = ref<Template | null>(null);

defineProps({
  templates: {
    type: Array as () => Template[],
    default: () => []
  }
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: 'dashboard',
  },
  {
    title: 'CMS',
    href: 'cms.index',
  },
  {
    title: 'Templates',
    href: 'cms.templates.index',
  },
];

const filters = ref({
  global: { value: null, matchMode: 'contains' },
  name: { value: null, matchMode: 'contains' },
  category: { value: null, matchMode: 'contains' }
});

const menu_items = ref([
  {
    label: 'Edit',
    icon: 'pi pi-pencil',
    command: () => {
      if (currentTemplate.value) {
        router.visit(route('cms.templates.edit', { template: currentTemplate.value.id }));
      }
    },
    visible: () => hasPermission('cms.templates.edit')
  },
  {
    label: 'Duplicate',
    icon: 'pi pi-copy',
    command: () => {
      if (currentTemplate.value) {
        duplicateTemplate(currentTemplate.value);
      }
    },
    visible: () => hasPermission('cms.templates.create')
  },
  {
    label: 'Delete',
    icon: 'pi pi-trash',
    class: 'text-red-500',
    command: () => {
      if (currentTemplate.value) {
        confirmDelete(currentTemplate.value);
      }
    },
    visible: () => hasPermission('cms.templates.delete')
  },
]);

const toggle = (event: Event, template: Template) => {
  currentTemplate.value = template;  
  menu.value?.toggle(event);
};

const confirmDelete = (template: Template) => {
  templateToDelete.value = template;
  deleteDialog.value = true;
};

const deleteTemplate = () => {
  if (!templateToDelete.value) return;
  
  router.delete(route('cms.templates.destroy', { template: templateToDelete.value.id }), {
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Success', detail: 'Template deleted successfully', life: 3000 });
      deleteDialog.value = false;
    },
    onError: () => {
      toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete template', life: 3000 });
    }
  });
};

const duplicateTemplate = (template: Template) => {
  router.post(route('cms.templates.duplicate', { template: template.id }), {}, {
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Success', detail: 'Template duplicated successfully', life: 3000 });
    },
    onError: () => {
      toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to duplicate template', life: 3000 });
    }
  });
};

const formatDate = (dateString: string) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: '2-digit',
  }).format(date);
};
</script>

<template>
  <Head title="CMS Templates" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Templates</h1>
        <Button 
          v-permission="'cms.templates.create'" 
          label="Create Template" 
          icon="pi pi-plus" 
          severity="primary" 
          @click="router.visit(route('cms.templates.create'))" 
        />
      </div>

      <div class="mb-4">
        <div class="flex flex-row gap-1 md:w-60 w-full items-center">
          <InputText v-model="filters.global.value" placeholder="Search templates..." class="w-full" />
        </div>
      </div>

      <DataTable 
        :value="templates" 
        :paginator="true" 
        :rows="10" 
        :rowsPerPageOptions="[5, 10, 25, 50]" 
        :filters="filters"
        filterDisplay="menu" 
        responsiveLayout="scroll"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} templates" 
        class="p-datatable-sm"
      >
        <Column field="name" header="Name" sortable style="min-width: 12rem">
          <template #body="{ data }">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-gray-100 rounded-md overflow-hidden mr-3">
                <img v-if="data.thumbnail" :src="data.thumbnail" :alt="data.name" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                  <i class="pi pi-file-o text-2xl"></i>
                </div>
              </div>
              <div>
                <div class="font-medium">{{ data.name }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ data.category || 'Uncategorized' }}</div>
              </div>
            </div>
          </template>
        </Column>
        
        <Column field="description" header="Description" sortable style="min-width: 14rem">
          <template #body="{ data }">
            <div class="text-sm">{{ data.description || 'No description' }}</div>
          </template>
        </Column>
        
        <Column field="created_at" header="Created" sortable style="min-width: 8rem">
          <template #body="{ data }">
            <div class="text-sm">{{ formatDate(data.created_at) }}</div>
          </template>
        </Column>
        
        <Column header="Pages" style="min-width: 6rem">
          <template #body="{ data }">
            <Tag :value="data.pages_count || '0'" severity="info" />
          </template>
        </Column>
        
        <Column header="Actions" style="min-width: 6rem">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Button 
                v-can="['cms.templates.edit', 'cms.templates.delete']" 
                type="button" 
                severity="contrast" 
                size="sm" 
                @click="toggle($event, data)" 
                aria-haspopup="true"
                aria-controls="template_menu"
              >
                <i class="pi pi-ellipsis-v"></i>
              </Button>
            </div>
          </template>
        </Column>
      </DataTable>


      <Menu ref="menu" id="template_menu" :model="menu_items" :popup="true" />

      <!-- Delete Confirmation Dialog -->
      <Dialog v-model:visible="deleteDialog" modal header="Confirm Deletion" :style="{ width: '450px' }">
        <div class="flex items-start gap-4">
          <i class="pi pi-exclamation-triangle text-red-500 text-2xl mt-1"></i>
          <div>
            <p class="font-medium">Are you sure you want to delete "{{ templateToDelete?.name }}"?</p>
            <p class="text-sm text-gray-500 mt-1">This action cannot be undone. Pages using this template will not be affected.</p>
          </div>
        </div>
        <template #footer>
          <div class="flex justify-end gap-2">
            <Button label="Cancel" icon="pi pi-times" @click="deleteDialog = false" outlined />
            <Button label="Delete" icon="pi pi-trash" @click="deleteTemplate" severity="danger" />
          </div>
        </template>
      </Dialog>
    </div>
  </AppLayout>
</template>
