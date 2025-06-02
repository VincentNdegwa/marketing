<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #page-title>Deals</template>
    <template #page-actions>
      <ActionButton :href="route('crm.deals.create')">
        <Plus class="w-4 h-4 mr-2" />
        Add Deal
      </ActionButton>
    </template>

    <Card>
      <template #content>
        <!-- Filters -->
        <div class="mb-6 flex flex-wrap gap-4">
          <div class="flex-grow">
            <InputText v-model="filters.search" type="text" placeholder="Search deals..." class="w-full"
              @keyup.enter="getDeals" />
          </div>
          <div class="w-48">
            <Dropdown v-model="filters.stage" :options="stageOptions" optionLabel="label" optionValue="value"
              placeholder="All Stages" class="w-full" @change="getDeals" />
          </div>
          <div class="w-48">
            <Dropdown v-model="filters.status" :options="statusOptions" optionLabel="label" optionValue="value"
              placeholder="All Statuses" class="w-full" @change="getDeals" />
          </div>
          <div class="w-48">
            <Dropdown v-model="filters.user_id" :options="userOptions" optionLabel="label" optionValue="value"
              placeholder="All Owners" class="w-full" @change="getDeals" />
          </div>
          <Button severity="contrast" label="Filter" icon="pi pi-filter" @click="getDeals" />
          <Button severity="secondary" label="Reset" icon="pi pi-refresh" @click="resetFilters" />
        </div>

        <!-- Deals DataTable -->
        <DataTable :value="deals.data" :paginator="false" :rows="10" stripedRows responsiveLayout="scroll"
          class="p-datatable-sm" :loading="loading" v-model:sortField="filters.sort_field" v-model:sortOrder="sortOrder"
          @sort="onSort">
          <Column field="title" header="Title" sortable>
            <template #body="{ data }">
              <div>
                <Link :href="route('crm.deals.show', data.id)" class="text-primary hover:underline font-medium">
                {{ data.title }}
                </Link>
                <div class="text-sm text-gray-500">
                  {{ data.description ? data.description.substring(0, 50) + (data.description.length > 50 ? '...' : '')
                  : '' }}
                </div>
              </div>
            </template>
          </Column>

          <Column field="stage" header="Stage" sortable>
            <template #body="{ data }">
              <Tag :severity="getStageSeverity(data.stage)" :value="stages[data.stage]" />
            </template>
          </Column>

          <Column field="value" header="Value" sortable>
            <template #body="{ data }">
              {{ data.value ? formatCurrency(data.value, data.currency) : '-' }}
            </template>
          </Column>

          <Column field="expected_close_date" header="Expected Close" sortable>
            <template #body="{ data }">
              {{ data.expected_close_date ? formatDate(data.expected_close_date) : '-' }}
            </template>
          </Column>

          <Column field="company" header="Company">
            <template #body="{ data }">
              <div v-if="data.company">
                <Link :href="route('crm.companies.show', data.company.id)" class="text-primary hover:underline">
                {{ data.company.name }}
                </Link>
              </div>
              <div v-else class="text-sm text-gray-500">-</div>
            </template>
          </Column>

          <Column field="contact" header="Contact">
            <template #body="{ data }">
              <div v-if="data.contact">
                <Link :href="route('crm.contacts.show', data.contact.id)" class="text-primary hover:underline">
                {{ data.contact.name }}
                </Link>
              </div>
              <div v-else class="text-sm text-gray-500">-</div>
            </template>
          </Column>

          <Column field="user" header="Owner">
            <template #body="{ data }">
              <div v-if="data.user" class="text-sm">
                {{ data.user.name }}
              </div>
              <div v-else class="text-sm text-gray-500">-</div>
            </template>
          </Column>

          <Column header="Actions" :exportable="false" style="min-width: 8rem">
            <template #body="{ data }">
              <div class="flex gap-2">
                <Link :href="route('crm.deals.edit', data.id)"
                  class="p-button p-button-sm p-button-text p-button-primary">
                Edit
                </Link>
                <Button severity="danger" text size="small" @click="confirmDealDeletion(data)">
                  Delete
                </Button>
              </div>
            </template>
          </Column>

          <template #empty>
            <div class="text-center p-4 text-gray-500">
              No deals found.
              <Link :href="route('crm.deals.create')" class="text-primary hover:underline">Create one</Link>.
            </div>
          </template>
        </DataTable>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center">
          <Paginator v-if="deals.meta" :rows="filters.per_page" :totalRecords="deals.meta.total"
            :first="(deals.meta.current_page - 1) * filters.per_page" @page="onPageChange($event)"
            :rowsPerPageOptions="[10, 20, 50]"
            template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown" />
        </div>
      </template>
    </Card>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:visible="confirmingDealDeletion" :style="{ width: '450px' }" header="Delete Deal" :modal="true"
      :closable="false">
      <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
        <span>Are you sure you want to delete this deal? This action cannot be undone.</span>
      </div>
      <template #footer>
        <Button label="Cancel" icon="pi pi-times" @click="closeModal" class="p-button-text" />
        <Button label="Delete" icon="pi pi-trash" severity="danger" @click="deleteDeal"
          :loading="deleteForm.processing" />
      </template>
    </Dialog>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import ActionButton from '@/components/ui/action-button/ActionButton.vue';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Paginator from 'primevue/paginator';
import Dialog from 'primevue/dialog';
import Tag from 'primevue/tag';
import type { BreadcrumbItem } from '@/types';
import { route } from 'ziggy-js';

// Interfaces
interface User {
  id: number;
  name: string;
  email: string;
}

interface Contact {
  id: number;
  name: string;
  email: string;
}

interface Company {
  id: number;
  name: string;
}

interface Deal {
  id: number;
  title: string;
  description: string | null;
  value: number | null;
  currency: string;
  stage: string;
  status: string;
  probability: number | null;
  expected_close_date: string | null;
  actual_close_date: string | null;
  contact_id: number | null;
  contact: Contact | null;
  company_id: number | null;
  company: Company | null;
  user_id: number | null;
  user: User | null;
  created_at: string;
  updated_at: string;
}

interface PaginationMeta {
  current_page: number;
  from: number;
  last_page: number;
  path: string;
  per_page: number;
  to: number;
  total: number;
}

interface PaginatedData<T> {
  data: T[];
  meta: PaginationMeta;
  links: Record<string, string | null>;
}

interface FilterOptions {
  search: string;
  stage: string;
  status: string;
  user_id: string | number;
  sort_field: string;
  sort_direction: string;
  per_page: number;
}

interface DropdownOption {
  label: string;
  value: string | number;
}

interface Props {
  deals: PaginatedData<Deal>;
  filters: FilterOptions;
  stages: Record<string, string>;
  statuses: Record<string, string>;
  users: User[];
}

// Props
const props = defineProps<Props>();

// Reactive state
const loading = ref(false);
const filters = reactive<FilterOptions>({
  search: props.filters.search || '',
  stage: props.filters.stage || '',
  status: props.filters.status || '',
  user_id: props.filters.user_id || '',
  sort_field: props.filters.sort_field || 'created_at',
  sort_direction: props.filters.sort_direction || 'desc',
  per_page: props.filters.per_page || 10,
});
const sortOrder = ref(filters.sort_direction === 'asc' ? 1 : -1);

const confirmingDealDeletion = ref(false);
const dealToDelete = ref<Deal | null>(null);
const deleteForm = useForm({});

// Computed
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  {
    title: 'Dashboard',
    href: 'dashboard',
  },
  {
    title: 'Deals',
    href: 'crm.deals.index',
  }
]);

const stageOptions = computed<DropdownOption[]>(() => {
  const options = [{ label: 'All Stages', value: '' }];
  
  Object.entries(props.stages).forEach(([value, label]) => {
    options.push({ label, value });
  });
  
  return options;
});

const statusOptions = computed<DropdownOption[]>(() => {
  const options = [{ label: 'All Statuses', value: '' }];
  
  Object.entries(props.statuses).forEach(([value, label]) => {
    options.push({ label, value });
  });
  
  return options;
});

const userOptions = computed<DropdownOption[]>(() => {
  const options = [{ label: 'All Owners', value: '' }];
  
  props.users.forEach(user => {
    options.push({ label: user.name, value: user.id.toString() });
  });
  
  return options;
});

// Methods
const getDeals = (): void => {
  loading.value = true;
  router.get(
    route('crm.deals.index'),
    filters,
    {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        loading.value = false;
      },
      onError: () => {
        loading.value = false;
      }
    }
  );
};
const onSort = (event: any): void => {
  filters.sort_field = event.sortField;
  filters.sort_direction = event.sortOrder === 1 ? 'asc' : 'desc';
  sortOrder.value = event.sortOrder;
  getDeals();
};

const onPageChange = (event: any): void => {
  const page = Math.floor(event.first / event.rows) + 1;
  filters.per_page = event.rows;
  
  router.get(
    route('crm.deals.index', { page }),
    filters,
    {
      preserveState: true,
      preserveScroll: true,
      replace: true
    }
  );
};

const resetFilters = (): void => {
  filters.search = '';
  filters.stage = '';
  filters.status = '';
  filters.user_id = '';
  filters.sort_field = 'created_at';
  filters.sort_direction = 'desc';
  getDeals();
};

const confirmDealDeletion = (deal: Deal): void => {
  dealToDelete.value = deal;
  confirmingDealDeletion.value = true;
};

const closeModal = (): void => {
  confirmingDealDeletion.value = false;
  setTimeout(() => {
    dealToDelete.value = null;
  }, 300);
};

const deleteDeal = (): void => {
  if (dealToDelete.value) {
    deleteForm.delete(route('crm.deals.destroy', dealToDelete.value.id), {
      onSuccess: () => closeModal(),
    });
  }
};

const formatCurrency = (value: number | null, currency: string = 'USD'): string => {
  if (value === null || value === undefined) return '-';
  
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: currency || 'USD',
  }).format(value);
};

const formatDate = (dateString: string | null): string => {
  if (!dateString) return '-';
  
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const getStageSeverity = (stage: string): string => {
  const severities: Record<string, string> = {
    'lead': 'info',
    'qualification': 'info',
    'proposal': 'warning',
    'negotiation': 'warning',
    'closed_won': 'success',
    'closed_lost': 'danger',
  };
  return severities[stage] || 'secondary';
};
</script>
