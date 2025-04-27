<template>
  <Head title="CMS Pages" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Pages</h1>
        <Button v-permission="'cms.pages.create'" label="Create New Page" icon="pi pi-plus" severity="contrast" 
          @click="$inertia.visit(route('cms.pages.create'))" />
      </div>

      <Card class="bg-card dark:bg-card-dark">
        <template #content>
          <DataTable 
            :value="pages" 
            :paginator="true" 
            :rows="10"
            :loading="loading"
            stripedRows
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 10, 25, 50]"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} pages"
            responsiveLayout="scroll"
            v-model:filters="filters"
            filterDisplay="menu"
            :globalFilterFields="['title', 'slug', 'status']"
          >
            <template #header>
              <div class="flex justify-between">
                <Button type="button" icon="pi pi-filter-slash" label="Clear" severity="secondary" outlined @click="clearFilter()" />
                <span class="p-input-icon-left">
                  <i class="pi pi-search" />
                  <InputText v-model="filters['global'].value" placeholder="Search..." />
                </span>
              </div>
            </template>

            <Column field="title" header="Title" sortable>
              <template #body="{ data }">
                <div class="font-medium">{{ data.title }}</div>
                <div class="text-xs text-gray-500">{{ data.slug }}</div>
              </template>
              <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" @input="filterCallback()" placeholder="Search by title" class="p-column-filter" />
              </template>
            </Column>

            <Column field="status" header="Status" sortable style="width: 10rem">
              <template #body="{ data }">
                <Tag :severity="data.status === 'published' ? 'success' : 'warning'" :value="data.status" />
              </template>
              <template #filter="{ filterModel, filterCallback }">
                <Dropdown v-model="filterModel.value" @change="filterCallback()" :options="statuses" 
                  placeholder="Select Status" class="p-column-filter" style="min-width: 12rem">
                  <template #value="slotProps">
                    <Tag :severity="slotProps.value === 'published' ? 'success' : 'warning'" :value="slotProps.value" v-if="slotProps.value" />
                    <span v-else>{{ slotProps.placeholder }}</span>
                  </template>
                </Dropdown>
              </template>
            </Column>

            <Column field="updated_at" header="Last Updated" sortable style="width: 12rem">
              <template #body="{ data }">
                {{ formatDate(data.updated_at) }}
              </template>
            </Column>

            <Column header="Actions" style="width: 10rem">
              <template #body="{ data }">
                <div class="flex gap-2">
                  <Button v-permission="'cms.pages.edit'" icon="pi pi-pencil" severity="contrast" size="small" 
                    @click="editPage(data)" />
                  <Button v-permission="'cms.pages.view'" icon="pi pi-eye" severity="contrast" outlined size="small" 
                    @click="previewPage(data)" />
                  <Button v-permission="'cms.pages.delete'" icon="pi pi-trash" severity="danger" outlined size="small" 
                    @click="confirmDelete(data)" />
                </div>
              </template>
            </Column>
          </DataTable>
        </template>
      </Card>
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:visible="deleteDialog" header="Confirm Deletion" :style="{ width: '450px' }" :modal="true">
      <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
        <span>Are you sure you want to delete <strong>{{ pageToDelete?.title }}</strong>?</span>
      </div>
      <template #footer>
        <Button label="No" icon="pi pi-times" outlined @click="deleteDialog = false" />
        <Button label="Yes" icon="pi pi-check" severity="danger" @click="deletePage" />
      </template>
    </Dialog>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import Button from 'primevue/button';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import AppLayout from '@/Layouts/AppLayout.vue';
import { route } from 'ziggy-js';
import { usePermissions } from '@/composables/usePermissions';
import { BreadcrumbItem } from '@/types';

defineProps({
  pages: {
    type: Array,
    default: () => []
  }
});

const { hasPermission } = usePermissions();
const toast = useToast();
const loading = ref(false);
const deleteDialog = ref(false);
const pageToDelete = ref(null);
const statuses = ['draft', 'published'];

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


// Filters for the data table
const filters = ref({
  global: { value: null, matchMode: 'contains' },
  title: { value: null, matchMode: 'contains' },
  slug: { value: null, matchMode: 'contains' },
  status: { value: null, matchMode: 'equals' }
});

const clearFilter = () => {
  filters.value = {
    global: { value: null, matchMode: 'contains' },
    title: { value: null, matchMode: 'contains' },
    slug: { value: null, matchMode: 'contains' },
    status: { value: null, matchMode: 'equals' }
  };
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

const editPage = (page) => {
  router.visit(route('cms.pages.edit', page.id));
};

const previewPage = (page) => {
  window.open(route('cms.pages.preview', page.id), '_blank');
};

const confirmDelete = (page) => {
  pageToDelete.value = page;
  deleteDialog.value = true;
};

const deletePage = () => {
  if (!pageToDelete.value) return;
  
  router.delete(route('cms.pages.destroy', pageToDelete.value.id), {
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Success', detail: 'Page deleted successfully', life: 3000 });
      deleteDialog.value = false;
    },
    onError: () => {
      toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete page', life: 3000 });
    }
  });
};
</script>

<style scoped>
.confirmation-content {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
