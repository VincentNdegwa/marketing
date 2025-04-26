<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Chip from 'primevue/chip';
import { route } from 'ziggy-js';
import Menu from 'primevue/menu';
import Dialog from 'primevue/dialog';

export interface UserData {
  id: number;
  name: string;
  email: string;
  job_title: string | null;
  is_active: boolean;
  last_login_at: string | null;
  roles: string[];
  is_admin: boolean;
}

defineProps<{
  users: UserData[];
  current_business_id: number;
}>();
const menu = ref();
const currentUser = ref();
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

const getSeverity = (status:string) => {
  return status ? 'success' : 'danger';
};

const formatDate = (date: number|string|Date) => {
  if (!date) return 'Never';
  return new Date(date).toLocaleString();
};

const dialog = ref<{
  text: string;
  visible: boolean;
  header: string;
  action: ((payload: MouseEvent) => void) | undefined;
}>({
  text: "",
  visible: false,
  header: "",
  action: undefined
});

const confirmDelete = (user: UserData) => {
  dialog.value = {
    text: `Are you sure you want to delete ${user.name}? This action cannot be undone.`,
    visible: true,
    header: "Confirm Deletion",
    action: () => deleteUser(user)
  };
};

const deleteUser = (user: UserData) => {
  router.delete(route('usermanagement.destroy', { id: user.id }), {
    onSuccess: () => {
    }
  });
};

const menu_item = ref([
  {
    label: 'Edit',
    icon: 'pi pi-pencil',
    command: () => {
      if (currentUser.value) {
        router.visit(route('usermanagement.edit', { id: currentUser.value.id }));
      }
    },
  },
  {
    label: 'Delete',
    icon: 'pi pi-trash',
    class: 'text-red-500',
    command: () => {
      if (currentUser.value) {
        confirmDelete(currentUser.value);
      }
    },
  },
]);

const toggle = (event: Event, data: UserData) => {
  currentUser.value = data;  
  menu.value.toggle(event);
}

</script>

<template>

  <Head title="User Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Users</h1>
        <Link :href="route('usermanagement.create')"
          class="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center rounded-sm px-3 py-2 shadow-xs"
          preserve-scroll>
        <span>Add User</span>
        </Link>
      </div>

      <div class="mb-4">
        <div class="flex flex-row gap-1 md:w-60 w-full items-center">
          <InputText v-model="filters.global.value" placeholder="Search users..." class="w-full" />
        </div>
      </div>

      <DataTable :value="users" :paginator="true" :rows="10" :rowsPerPageOptions="[5, 10, 25, 50]" :filters="filters"
        filterDisplay="menu" responsiveLayout="scroll"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} users" class="p-datatable-sm">

        <Column field="name" header="Name" sortable style="min-width: 12rem"></Column>
        <Column field="email" header="Email" sortable style="min-width: 14rem"></Column>
        <Column field="job_title" header="Job Title" sortable style="min-width: 10rem">
          <template #body="{data}">
            <span>{{ data.job_title || 'Not specified' }}</span>
          </template>
        </Column>
        <Column field="roles" header="Roles" style="min-width: 14rem">
          <template #body="{data}">
            <div v-for="(item, index) in data.roles" :key="index">
              <Chip :label="item" />
            </div>
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
          <template #body="{ data }">
            <div class="flex gap-2">
              <Button v-if="!data.is_admin" type="button" severity="contrast" size="sm" @click="toggle($event, data)" aria-haspopup="true"
                aria-controls="overlay_menu">
                <i class="pi pi-ellipsis-v"></i>
              </Button>
            </div>
          </template>
        </Column>
        <Menu ref="menu" id="overlay_menu" :model="menu_item" :popup="true" />
      </DataTable>
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:visible="dialog.visible" modal :header="dialog.header" :style="{ width: '450px' }">
      <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
        <span>{{ dialog.text }}</span>
      </div>
      <template #footer>
        <Button label="No" icon="pi pi-times" class="p-button-text" @click="dialog.visible = false" />
        <Button label="Yes" icon="pi pi-check" class="p-button-danger" @click="dialog.action" />
      </template>
    </Dialog>
  </AppLayout>
</template>
