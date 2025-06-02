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
import { usePermissions } from '@/composables/usePermissions';

const {
  hasPermission,
} = usePermissions();

interface Business {
  id: number;
  name: string;
  email: string;
  phone: string;
  address: string;
  description: string;
  is_active: boolean;
  is_current: boolean;
  slug: string;
}

interface Props {
  businesses: Business[];
  defaultBusinessId: number | null;
}

const props = defineProps<Props>();
const menu = ref();
const currentBusiness = ref<Business | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: 'dashboard',
    },
    {
        title: 'Businesses',
        href: 'business.index',
    },
];

const filters = ref({
  global: { value: null, matchMode: 'contains' },
});

const getSeverity = (status: boolean) => {
  return status ? 'success' : 'danger';
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

const confirmDelete = (business: Business) => {
  dialog.value = {
    text: `Are you sure you want to delete ${business.name}? This action cannot be undone.`,
    visible: true,
    header: "Confirm Deletion",
    action: () => deleteBusiness(business)
  };
};

const deleteBusiness = (business: Business) => {
  router.delete(route('business.destroy', { business: business.id }), {
    onSuccess: () => {
      dialog.value.visible = false;
    }
  });
};

const isDefault = (businessId: number) => {
  return props.defaultBusinessId === businessId;
};

const setAsDefault = (business: Business) => {
  router.post(route('business.set-default', { business: business.id }), {});
};

const switchToBusiness = (business: Business) => {
  router.post(route('business.switch', { business: business.id }), {});
};

const menu_items = ref([
  {
    label: 'Switch to Business',
    icon: 'pi pi-sync',
    command: () => {
      if (currentBusiness.value) {
        switchToBusiness(currentBusiness.value);
      }
    },
    visible: () => !!currentBusiness.value && !currentBusiness.value.is_current,
  },
  {
    label: 'Set as Default',
    icon: 'pi pi-star',
    command: () => {
      if (currentBusiness.value && !isDefault(currentBusiness.value.id)) {
        setAsDefault(currentBusiness.value);
      }
    },
    visible: () => (!!currentBusiness.value && !isDefault(currentBusiness.value.id) && hasPermission('business.manage')),
  },
  {
    separator: true
  },
  {
    label: 'Edit',
    icon: 'pi pi-pencil',
    command: () => {
      if (currentBusiness.value) {
        router.visit(route('business.edit', { business: currentBusiness.value.id }));
      }
    },
    visible: () => hasPermission('business.edit')
  },
  {
    label: 'Delete',
    icon: 'pi pi-trash',
    class: 'text-red-500',
    command: () => {
      if (currentBusiness.value) {
        confirmDelete(currentBusiness.value);
      }
    },
    visible: () => hasPermission('business.delete')
  },
]);

const toggle = (event: Event, data: Business) => {
  currentBusiness.value = data;  
  menu.value.toggle(event);
};


</script>

<template>

  <Head title="Businesses" />

  <AppLayout :breadcrumbs="breadcrumbs">

    <template #page-title>
      Businesses
     </template>

    <template #page-actions>

      <Link v-permission="'business.create'" :href="route('business.create')"
        class="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center rounded-sm px-3 py-2 shadow-xs">
      <span>Add Business</span>
      </Link>

    </template>

    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl">

      <div class="mb-4">
        <div class="flex flex-row gap-1 md:w-60 w-full items-center">
          <InputText v-model="filters.global.value" placeholder="Search businesses..." class="w-full" />
        </div>
      </div>

      <DataTable :value="businesses" :paginator="true" :rows="10" :rowsPerPageOptions="[5, 10, 25, 50]"
        :filters="filters" filterDisplay="menu" responsiveLayout="scroll"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} businesses" class="p-datatable-sm"
        v-if="businesses.length > 0">
        <Column field="name" header="Name" sortable style="min-width: 12rem"></Column>
        <Column field="email" header="Email" sortable style="min-width: 14rem"></Column>
        <Column field="phone" header="Phone" sortable style="min-width: 10rem">
          <template #body="{data}">
            <span>{{ data.phone || 'Not specified' }}</span>
          </template>
        </Column>
        <Column field="is_active" header="Status" sortable style="min-width: 8rem">
          <template #body="{data}">
            <Tag :value="data.is_active ? 'Active' : 'Inactive'" :severity="getSeverity(data.is_active)" />
          </template>
        </Column>
        <Column header="Default" style="min-width: 8rem">
          <template #body="{data}">
            <Chip v-if="isDefault(data.id)" label="Default" class="bg-green-100 text-green-800" />
          </template>
        </Column>
        <Column header="Actions" style="min-width: 8rem">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Button v-can="['business.edit', 'business.delete', 'business.manage']" type="button" severity="contrast"
                size="sm" @click="toggle($event, data)" aria-haspopup="true" aria-controls="overlay_menu">
                <i class="pi pi-ellipsis-v"></i>
              </Button>
            </div>
          </template>
        </Column>
      </DataTable>

      <div v-if="businesses.length === 0" class="text-center py-8 rounded-lg shadow-sm">
        <p class="text-gray-500">No businesses found. Create your first business to get started.</p>
      </div>

      <Menu ref="menu" id="overlay_menu" :model="menu_items" :popup="true" />

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
    </div>
  </AppLayout>
</template>
