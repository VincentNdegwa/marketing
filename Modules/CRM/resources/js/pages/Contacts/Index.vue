<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #page-title>
      {{ pageTitle }}
    </template>
    <template #page-actions>
      <ActionButton :href="route('crm.contacts.create')">
        Add Contact
      </ActionButton>
    </template>

    <div>
      <Card>
        <template #content>
          <!-- Filters -->
          <div class="mb-6 flex flex-wrap gap-4">
            <div class="flex-grow">
              <InputText v-model="search" type="text" placeholder="Search contacts..." class="w-full"
                @keyup.enter="getContacts" />
            </div>
            <div class="w-48">
              <Dropdown v-model="filters.status" :options="statusOptions" optionLabel="name" optionValue="value"
                placeholder="All Statuses" class="w-full" @change="getContacts" />
            </div>
            <div class="w-48">
              <Dropdown v-model="filters.lead_status" :options="leadStatusOptions" optionLabel="name"
                optionValue="value" placeholder="All Lead Statuses" class="w-full" @change="getContacts" />
            </div>
            <div class="w-48">
              <Dropdown v-model="filters.company_id" :options="companyOptions" optionLabel="name" optionValue="value"
                placeholder="All Companies" class="w-full" :filter="true" @change="getContacts" />
            </div>
            <div class="w-48">
              <Dropdown v-model="filters.assigned_to" :options="userOptions" optionLabel="name" optionValue="value"
                placeholder="All Owners" class="w-full" @change="getContacts" />
            </div>
            <Button severity="contrast" label="Filter" @click="getContacts" />
            <Button severity="secondary" text icon="pi pi-refresh" label="Reset" @click="resetFilters" />
          </div>

          <!-- Contacts Table -->
          <DataTable :value="contacts.data" responsiveLayout="scroll" class="">
            <Column field="name" header="Name" sortable @sort="onSort('first_name', $event)">
              <template #body="{ data }">
                <div class="flex items-center">
                  <Avatar :label="data.first_name.charAt(0) + data.last_name.charAt(0)" shape="circle" />
                  <div class="ml-3">
                    <Link :href="route('crm.contacts.show', data.id)" class="font-medium">
                    {{ data.first_name }} {{ data.last_name }}
                    </Link>
                    <div class="text-sm">
                      {{ data.job_title || 'N/A' }}
                    </div>
                  </div>
                </div>
              </template>
            </Column>

            <Column field="email" header="Email" sortable @sort="onSort('email', $event)">
              <template #body="{ data }">
                {{ data.email || 'N/A' }}
              </template>
            </Column>

            <Column field="phone" header="Phone" sortable @sort="onSort('phone', $event)">
              <template #body="{ data }">
                {{ data.phone || data.mobile || 'N/A' }}
              </template>
            </Column>

            <Column field="company" header="Company">
              <template #body="{ data }">
                <template v-if="data.company">
                  <Link :href="route('crm.companies.show', data.company.id)">
                  {{ data.company.name }}
                  </Link>
                </template>
                <template v-else>
                  N/A
                </template>
              </template>
            </Column>

            <Column field="lead_status" header="Lead Status" sortable @sort="onSort('lead_status', $event)">
              <template #body="{ data }">
                <Tag v-if="data.lead_status" :value="data.lead_status"
                  :severity="getLeadStatusSeverity(data.lead_status)" />
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
                  @click="$inertia.visit(route('crm.contacts.edit', data.id))" />
                <Button icon="pi pi-trash" class="p-button-text p-button-rounded p-button-danger"
                  @click="confirmDelete(data)" />
              </template>
            </Column>

            <template #empty>
              <div class="text-center p-4">
                No contacts found.
                <Link :href="route('crm.contacts.create')">Create one</Link>.
              </div>
            </template>
          </DataTable>

          <div class="mt-4">
          <PrimePaginator 
            :data="props.contacts" 
            :preserveScroll="true"
          />
        </div>
        </template>
      </Card>
    </div>


    <!-- Delete Confirmation Modal -->
    <Dialog v-model:visible="showDeleteModal" header="Delete Contact" :style="{ width: '450px' }" :modal="true">
      <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem"></i>
        <span>Are you sure you want to delete this contact? This action cannot be undone.</span>
      </div>
      <template #footer>
        <Button label="Cancel" icon="pi pi-times" class="p-button-text" @click="closeModal" />
        <Button label="Delete" icon="pi pi-trash" class="p-button-danger" @click="deleteContact" :disabled="processing"
          :loading="processing" />
      </template>
    </Dialog>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
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
import PrimePaginator from '@/components/PrimePaginator.vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: 'dashboard',
  },
  {
    title: 'Contacts',
    href: 'crm.contacts.index',
  }
];
interface Company {
  id: number;
  name: string;
}

interface Contact {
  id: number;
  first_name: string;
  last_name: string;
  email: string | null;
  phone: string | null;
  mobile: string | null;
  job_title: string | null;
  lead_status: string | null;
  created_at: string;
  company: Company | null;
}

interface Filters {
  search?: string;
  status?: string;
  lead_status?: string;
  company_id?: number | string;
  assigned_to?: number | string;
  sort_field?: string;
  sort_direction?: 'asc' | 'desc';
}


interface ContactsData {
  data: Contact[];
  current_page: number;
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: { url: string | null; label: string; active: boolean }[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}

// Define props
interface Props {
  contacts: ContactsData;
  filters: Filters;
  pageTitle: string;
  companies?: { id: number; name: string }[];
  users?: { id: number; name: string }[];
}

const props = defineProps<Props>();

// Reactive state
const search = ref<string>(props.filters.search || '');
const filters = ref<Filters>({ ...props.filters });
const showDeleteModal = ref<boolean>(false);
const processing = ref<boolean>(false);
const contactToDelete = ref<Contact | null>(null);

// Dropdown options
const statusOptions = ref([
  { name: 'All Statuses', value: '' },
  { name: 'Active', value: 'active' },
  { name: 'Inactive', value: 'inactive' }
]);

const leadStatusOptions = ref([
  { name: 'All Lead Statuses', value: '' },
  { name: 'New', value: 'new' },
  { name: 'Contacted', value: 'contacted' },
  { name: 'Qualified', value: 'qualified' },
  { name: 'Unqualified', value: 'unqualified' },
  { name: 'Customer', value: 'customer' }
]);

const companyOptions = computed(() => {
  const options = [{ name: 'All Companies', value: '' }];

  if (props.contacts.data && props.contacts.data.length > 0) {
    props.contacts.data.forEach(data => {
      if (data.company) {
        options.push({ name: data.company?.name, value: data.company?.id.toString() });
      }
    });
  }

  return options;
});

const userOptions = computed(() => {
  const options = [{ name: 'All Owners', value: '' }];

  if (props.users) {
    props.users.forEach(user => {
      options.push({ name: user.name, value: user.id.toString() });
    });
  }

  return options;
});

const resetFilters = (): void => {
  search.value = '';
  filters.value = {
    search: '',
    status: '',
    lead_status: '',
    company_id: '',
    assigned_to: '',
    sort_field: undefined,
    sort_direction: undefined
  };
  getContacts();
};


// Watch for search changes
watch(search, (value) => {
  filters.value.search = value;
});

// Methods
const getContacts = (): void => {
  router.get(route('crm.contacts.index'), {
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
  getContacts();
};

const getLeadStatusSeverity = (status: string): string => {
  switch (status) {
    case 'customer': return 'success';
    case 'qualified': return 'info';
    case 'contacted': return 'warning';
    case 'unqualified': return 'danger';
    default: return 'secondary';
  }
};

const confirmDelete = (contact: Contact): void => {
  contactToDelete.value = contact;
  showDeleteModal.value = true;
};

const closeModal = (): void => {
  showDeleteModal.value = false;
  contactToDelete.value = null;
};

const deleteContact = (): void => {
  if (!contactToDelete.value) return;

  processing.value = true;
  router.delete(route('crm.contacts.destroy', contactToDelete.value.id), {
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