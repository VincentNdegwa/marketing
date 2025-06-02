<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #page-title>
      {{ pageTitle }}
    </template>
    <template #page-actions>
      <ActionButton :href="route('crm.companies.create')">
      Add Company
      </ActionButton>
    </template>

    <div>
      <Card>
        <template #content>
          <!-- Filters -->
          <div class="mb-6 flex flex-wrap gap-4">
            <div class="flex-grow">
              <InputText v-model="search" type="text" placeholder="Search companies..." class="w-full"
                @keyup.enter="getCompanies" />
            </div>
            <div class="w-48">
              <Dropdown v-model="filters.industry" :options="industryOptions" optionLabel="name" optionValue="value"
                placeholder="All Industries" class="w-full" @change="getCompanies" />
            </div>
            <div class="w-48">
              <Dropdown v-model="filters.status" :options="statusOptions" optionLabel="name"
                optionValue="value" placeholder="All Statuses" class="w-full" @change="getCompanies" />
            </div>
            <Button severity="contrast" label="Filter" @click="getCompanies" />
          </div>

          <!-- Companies Table -->
          <DataTable :value="companies.data" responsiveLayout="scroll" class="">
            <Column field="name" header="Company Name" sortable @sort="onSort('name', $event)">
              <template #body="{ data }">
                <div class="flex items-center">
                  <Avatar :label="data.name.charAt(0)" shape="square" />
                  <div class="ml-3">
                    <Link :href="route('crm.companies.show', data.id)" class="font-medium">
                    {{ data.name }}
                    </Link>
                    <div class="text-sm">
                      {{ data.industry || 'N/A' }}
                    </div>
                  </div>
                </div>
              </template>
            </Column>

            <Column field="website" header="Website" sortable @sort="onSort('website', $event)">
              <template #body="{ data }">
                <a v-if="data.website" :href="data.website" target="_blank" class="text-blue-500 hover:underline">
                  {{ data.website }}
                </a>
                <span v-else>N/A</span>
              </template>
            </Column>

            <Column field="phone" header="Phone" sortable @sort="onSort('phone', $event)">
              <template #body="{ data }">
                {{ data.phone || 'N/A' }}
              </template>
            </Column>

            <Column field="contacts_count" header="Contacts">
              <template #body="{ data }">
                <Link v-if="data.contacts_count > 0" :href="route('crm.contacts.index', { company_id: data.id })">
                  {{ data.contacts_count }} {{ data.contacts_count === 1 ? 'contact' : 'contacts' }}
                </Link>
                <span v-else>No contacts</span>
              </template>
            </Column>

            <Column field="deals_count" header="Deals">
              <template #body="{ data }">
                <Link v-if="data.deals_count > 0" :href="route('crm.deals.index', { company_id: data.id })">
                  {{ data.deals_count }} {{ data.deals_count === 1 ? 'deal' : 'deals' }}
                </Link>
                <span v-else>No deals</span>
              </template>
            </Column>

            <Column field="status" header="Status" sortable @sort="onSort('status', $event)">
              <template #body="{ data }">
                <Tag v-if="data.status" :value="data.status"
                  :severity="getStatusSeverity(data.status)" />
                <span v-else>N/A</span>
              </template>
            </Column>

            <Column field="created_at" header="Created" sortable @sort="onSort('created_at', $event)">
              <template #body="{ data }">
                {{ new Date(data.created_at).toLocaleDateString() }}
              </template>
            </Column>

            <Column header="Actions" class="text-right">
              <template #body="{ data }">
                <Button icon="pi pi-pencil" class="p-button-text p-button-rounded"
                  @click="$inertia.visit(route('crm.companies.edit', data.id))" />
                <Button icon="pi pi-trash" class="p-button-text p-button-rounded p-button-danger"
                  @click="confirmDelete(data)" />
              </template>
            </Column>

            <template #empty>
              <div class="text-center p-4">
                No companies found.
                <Link :href="route('crm.companies.create')">Create one</Link>.
              </div>
            </template>
          </DataTable>
        </template>
      </Card>
    </div>


    <!-- Delete Confirmation Modal -->
    <Dialog v-model:visible="showDeleteModal" header="Delete Company" :style="{ width: '450px' }" :modal="true">
      <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
        <span v-if="companyToDelete">
          Are you sure you want to delete <strong>{{ companyToDelete.name }}</strong>?
          <p class="mt-2 text-red-500">This will also delete all associated records and cannot be undone.</p>
        </span>
      </div>
      <template #footer>
        <Button label="Cancel" icon="pi pi-times" class="p-button-text" @click="closeModal" />
        <Button label="Delete" icon="pi pi-trash" class="p-button-danger" @click="deleteCompany" :disabled="processing"
          :loading="processing" />
      </template>
    </Dialog>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { route } from 'ziggy-js';

import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Tag from 'primevue/tag';
import { BreadcrumbItem } from '@/types';
import Avatar from 'primevue/avatar';
import ActionButton from '@/components/ui/action-button/ActionButton.vue'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: 'dashboard',
  },
  {
    title: 'Companies',
    href: 'crm.companies.index',
  }
];

interface Company {
  id: number;
  name: string;
  industry: string | null;
  website: string | null;
  phone: string | null;
  status: string;
  contacts_count: number;
  deals_count: number;
  created_at: string;
}

interface Filters {
  search?: string;
  industry?: string;
  status?: string;
  sort_field?: string;
  sort_direction?: 'asc' | 'desc';
}

interface CompaniesData {
  data: Company[];
}

// Define props
interface Props {
  companies: CompaniesData;
  filters: Filters;
  pageTitle: string;
}

const props = defineProps<Props>();

// Reactive state
const search = ref<string>(props.filters.search || '');
const filters = ref<Filters>({ ...props.filters });
const showDeleteModal = ref<boolean>(false);
const processing = ref<boolean>(false);
const companyToDelete = ref<Company | null>(null);

// Dropdown options
const statusOptions = ref([
  { name: 'All Statuses', value: '' },
  { name: 'Active', value: 'active' },
  { name: 'Inactive', value: 'inactive' },
  { name: 'Prospect', value: 'prospect' },
  { name: 'Customer', value: 'customer' },
  { name: 'Partner', value: 'partner' }
]);

const industryOptions = ref([
  { name: 'All Industries', value: '' },
  { name: 'Technology', value: 'technology' },
  { name: 'Finance', value: 'finance' },
  { name: 'Healthcare', value: 'healthcare' },
  { name: 'Education', value: 'education' },
  { name: 'Manufacturing', value: 'manufacturing' },
  { name: 'Retail', value: 'retail' },
  { name: 'Other', value: 'other' }
]);

// Watch for search changes
watch(search, (value) => {
  filters.value.search = value;
});

// Methods
const getCompanies = (): void => {
  router.get(route('crm.companies.index'), {
    ...filters.value,
    search: search.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const onSort = (field: string, event: any): void => {
  const order = event.order === 1 ? 'asc' : 'desc';
  filters.value.sort_field = field;
  filters.value.sort_direction = order;
  getCompanies();
};

const getStatusSeverity = (status: string): string => {
  switch (status) {
    case 'customer': return 'success';
    case 'prospect': return 'info';
    case 'partner': return 'warning';
    case 'inactive': return 'danger';
    default: return 'secondary';
  }
};

const confirmDelete = (company: Company): void => {
  companyToDelete.value = company;
  showDeleteModal.value = true;
};

const closeModal = (): void => {
  showDeleteModal.value = false;
  companyToDelete.value = null;
};

const deleteCompany = (): void => {
  if (!companyToDelete.value) return;

  processing.value = true;
  router.delete(route('crm.companies.destroy', companyToDelete.value.id), {
    onSuccess: () => {
      closeModal();
      processing.value = false;
    },
    onError: () => {
      processing.value = false;
    },
  });
};
</script>
