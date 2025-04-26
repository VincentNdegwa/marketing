<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Tag from 'primevue/tag';

defineProps<{
  users: any[];
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
];

const filters = ref({
  global: { value: null, matchMode: 'contains' },
});

const getSeverity = (status) => {
  return status ? 'success' : 'danger';
};

const formatDate = (date) => {
  if (!date) return 'Never';
  return new Date(date).toLocaleString();
};

const formatRoles = (roles) => {
  return roles.join(', ') || 'No roles assigned';
};
</script>

<template>
  <Head title="User Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Users</h1>
        <Button label="Add User" icon="pi pi-plus" class="p-button-sm" />
      </div>
      
      <div class="mb-4">
        <div class="flex flex-row gap-1 md:w-60 w-full items-center">
          <InputText v-model="filters.global.value" placeholder="Search users..." class="w-full" />
        </div>
      </div>
      
      <DataTable 
        :value="users" 
        :paginator="true" 
        :rows="10"
        :rowsPerPageOptions="[5, 10, 25, 50]"
        :filters="filters"
        filterDisplay="menu"
        responsiveLayout="scroll"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} users"
        class="p-datatable-sm">
        
        <Column field="name" header="Name" sortable style="min-width: 12rem"></Column>
        <Column field="email" header="Email" sortable style="min-width: 14rem"></Column>
        <Column field="job_title" header="Job Title" sortable style="min-width: 10rem">
          <template #body="{data}">
            <span>{{ data.job_title || 'Not specified' }}</span>
          </template>
        </Column>
        <Column field="roles" header="Roles" style="min-width: 14rem">
          <template #body="{data}">
            <span>{{ formatRoles(data.roles) }}</span>
          </template>
        </Column>
        <Column field="is_active" header="Status" sortable style="min-width: 8rem">
          <template #body="{data}">
            <Tag :value="data.is_active ? 'Active' : 'Inactive'" :severity="getSeverity(data.is_active)" />
          </template>
        </Column>
        <Column field="last_login_at" header="Last Login" sortable style="min-width: 10rem">
          <template #body="{data}">
            <span>{{ formatDate(data.last_login_at) }}</span>
          </template>
        </Column>
        <Column header="Actions" style="min-width: 8rem">
          <template #body="{}">
            <div class="flex gap-2">
              <Button icon="pi pi-pencil" class="p-button-text p-button-sm" />
              <Button icon="pi pi-trash" class="p-button-text p-button-danger p-button-sm" />
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </AppLayout>
</template>
