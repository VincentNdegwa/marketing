<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Badge from 'primevue/badge';

defineProps<{
  roles: any[];
  current_business_id: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: 'dashboard',
  },
  {
    title: 'User Management',
    href: 'usermanagement.index',
  },
  {
    title: 'Roles',
    href: 'user-role.index',
  },
];

const filters = ref({
  global: { value: null, matchMode: 'contains' },
});

const formatPermissions = (permissions) => {
  if (!permissions || permissions.length === 0) return 'No permissions assigned';
  return permissions.length > 3 
    ? `${permissions.slice(0, 3).join(', ')} +${permissions.length - 3} more`
    : permissions.join(', ');
};
</script>

<template>

  <Head title="Role Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Roles</h1>
        <Button label="Add Role" icon="pi pi-plus" class="p-button-sm" />
      </div>

      <div class="mb-4">
        <div class="flex flex-row gap-1 md:w-60 w-full items-center">
          <InputText v-model="filters.global.value" placeholder="Search roles..." class="w-full" />
        </div>
      </div>

      <DataTable :value="roles" :paginator="true" :rows="10" :rowsPerPageOptions="[5, 10, 25, 50]" :filters="filters"
        filterDisplay="menu" responsiveLayout="scroll"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} roles" class="p-datatable-sm">

        <Column field="display_name" header="Role Name" sortable style="min-width: 12rem"></Column>
        <Column field="name" header="System Name" sortable style="min-width: 10rem"></Column>
        <Column field="description" header="Description" style="min-width: 14rem">
          <template #body="{data}">
            <span>{{ data.description || 'No description' }}</span>
          </template>
        </Column>
        <Column field="permissions" header="Permissions" style="min-width: 16rem">
          <template #body="{data}">
            <div>
              <span v-tooltip="data.permissions.join(', ')">{{ formatPermissions(data.permissions) }}</span>
            </div>
          </template>
        </Column>
        <Column field="users_count" header="Users" sortable style="min-width: 8rem">
          <template #body="{data}">
            <Badge :value="data.users_count" severity="info" />
          </template>
        </Column>
        <Column header="Actions" style="min-width: 8rem">
          <template #body="{data}">
            <div class="flex gap-2">
              <Button icon="pi pi-pencil" class="p-button-text p-button-sm" />
              <Button icon="pi pi-trash" class="p-button-text p-button-danger p-button-sm"
                :disabled="data.name === 'admin'" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Add any custom styles here */
</style>
