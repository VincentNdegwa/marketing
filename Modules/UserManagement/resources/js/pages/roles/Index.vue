<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import Badge from 'primevue/badge';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import InputText from 'primevue/inputtext';
import { ref } from 'vue';
import Menu from 'primevue/menu';
import { route } from 'ziggy-js';




interface Role {
  id: number;
  name: string;
  display_name: string;
  description?: string;
  permissions: string[];
}
defineProps<{
    roles: any[];
    current_business_id: number;
}>();
const menu = ref();
const currentRole = ref();
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
const menu_item = ref([
  {
    label: 'Edit',
    icon: 'pi pi-pencil',
    command: () => {
      if (currentRole.value) {
        router.visit(route('user-role.edit', currentRole.value.id));
      }
    },
  },
  {
    label: 'Delete',
    icon: 'pi pi-trash',
    class: 'text-red-500',
    command: () => {
      if (currentRole.value && currentRole.value.name !== 'admin') {
        router.delete(route('user-role.destroy', currentRole.value.id), {
          onSuccess: () => {
            // Optional: Show success message or handle success case
          }
        });
      }
    },
  },
]);

const filters = ref({
    global: { value: null, matchMode: 'contains' },
});

const formatPermissions = (permissions:string[]) => {
    if (!permissions || permissions.length === 0) return 'No permissions assigned';
    return permissions.length > 3 ? `${permissions.slice(0, 3).join(', ')} +${permissions.length - 3} more` : permissions.join(', ');
};

const toggle = (event: Event, data: Role) => {
  currentRole.value = data
  menu.value.toggle(event);
}
</script>

<template>

  <Head title="Role Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Roles</h1>
        <Link :href="route('user-role.create')"
          class="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center rounded-sm px-3 py-2 shadow-xs"
          preserve-scroll>
        <span>Add Role</span>
        </Link>
      </div>

      <div class="mb-4">
        <div class="flex w-full flex-row items-center gap-1 md:w-60">
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
          <template #body="{ data }">
            <span>{{ data.description || 'No description' }}</span>
          </template>
        </Column>
        <Column field="permissions" header="Permissions" style="min-width: 16rem">
          <template #body="{ data }">
            <div>
              <span v-tooltip="data.permissions.join(', ')">{{ formatPermissions(data.permissions) }}</span>
            </div>
          </template>
        </Column>
        <Column field="users_count" header="Users" sortable style="min-width: 8rem">
          <template #body="{ data }">
            <Badge :value="data.users_count" severity="info" />
          </template>
        </Column>
        <Column header="Actions" style="min-width: 8rem">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Button type="button" severity="contrast" size="sm" @click="toggle($event, data)" aria-haspopup="true"
                aria-controls="overlay_menu">
                <i class="pi pi-ellipsis-v"></i>
              </Button>
            </div>
          </template>
        </Column>
        <Menu ref="menu" id="overlay_menu" :model="menu_item" :popup="true" />
      </DataTable>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Add any custom styles here */
</style>
