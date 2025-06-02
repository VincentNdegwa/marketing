<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #page-title>
      {{ pageTitle }}
    </template>
    <template #page-actions>
      <ActionButton :href="route('crm.activities.create')">
        New Activity
      </ActionButton>
    </template>

    <div>
      <!-- Filters -->
      <Card class="mb-4">
        <template #content>
          <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <!-- Search -->
            <div class="md:col-span-2">
              <label for="search" class="block mb-2">Search</label>
              <InputText
                id="search"
                v-model="filters.search"
                class="w-full"
                placeholder="Search by title or description"
                @keyup.enter="getActivities"
              />
            </div>

            <!-- Type Filter -->
            <div>
              <label for="type" class="block mb-2">Type</label>
              <Dropdown
                id="type"
                v-model="filters.type"
                :options="typeOptions"
                optionLabel="name"
                optionValue="value"
                placeholder="All Types"
                class="w-full"
              />
            </div>

            <!-- Status Filter -->
            <div>
              <label for="status" class="block mb-2">Status</label>
              <Dropdown
                id="status"
                v-model="filters.status"
                :options="statusOptions"
                optionLabel="name"
                optionValue="value"
                placeholder="All Statuses"
                class="w-full"
              />
            </div>

            <!-- Date Range -->
            <div>
              <label for="date_range" class="block mb-2">Date Range</label>
              <Dropdown
                id="date_range"
                v-model="filters.date_range"
                :options="dateRangeOptions"
                optionLabel="name"
                optionValue="value"
                placeholder="Any Time"
                class="w-full"
              />
            </div>

            <!-- Filter Actions -->
            <div class="flex gap-2 items-center">
              <Button 
                label="Filter" 
                icon="pi pi-filter"
                @click="getActivities"
              />
              <Button 
                label="Reset" 
                icon="pi pi-times"
                severity="secondary"
                @click="resetFilters"
              />
            </div>
          </div>
        </template>
      </Card>

      <!-- Activities Table -->
      <Card>
        <template #content>
          <DataTable 
            :value="activities.data" 
            :paginator="activities.data.length > 0"
            :rows="10"
            :rowsPerPageOptions="[10, 25, 50]"
            :totalRecords="activities.total"
            :loading="loading"
            stripedRows
            responsiveLayout="scroll"
            class="p-datatable-sm"
            v-model:filters="dataTableFilters"
            filterDisplay="menu"
            :globalFilterFields="['title', 'description']"
          >
            <!-- Type Column -->
            <Column field="type" header="Type" sortable>
              <template #body="{ data }">
                <Tag 
                  :value="activityTypes[data.type]" 
                  :severity="getTypeSeverity(data.type)"
                  :icon="getTypeIcon(data.type)"
                />
              </template>
            </Column>

            <!-- Title Column -->
            <Column field="title" header="Title" sortable>
              <template #body="{ data }">
                <div>
                  <Link 
                    :href="route('crm.activities.show', data.id)"
                    class="text-blue-600 hover:underline font-medium"
                  >
                    {{ data.title }}
                  </Link>
                  <div v-if="data.description" class="text-sm text-gray-500 truncate max-w-xs">
                    {{ data.description }}
                  </div>
                </div>
              </template>
            </Column>

            <!-- Date Column -->
            <Column field="start_date" header="Date" sortable>
              <template #body="{ data }">
                <div>
                  <div>{{ formatDate(data.start_date) }}</div>
                  <div v-if="data.end_date && data.end_date !== data.start_date" class="text-sm text-gray-500">
                    to {{ formatDate(data.end_date) }}
                  </div>
                </div>
              </template>
            </Column>

            <!-- Status Column -->
            <Column field="status" header="Status" sortable>
              <template #body="{ data }">
                <Tag 
                  :value="activityStatuses[data.status]" 
                  :severity="getStatusSeverity(data.status)"
                />
              </template>
            </Column>

            <!-- Related To Column -->
            <Column field="related_type" header="Related To">
              <template #body="{ data }">
                <div v-if="data.related_type && data.related">
                  <Link 
                    :href="getRelatedLink(data)"
                    class="flex items-center text-blue-600 hover:underline"
                  >
                    <i :class="getRelatedTypeIconClass(data.related_type)" class="mr-2"></i>
                    <span>{{ getRelatedName(data) }}</span>
                  </Link>
                </div>
                <div v-else class="text-sm text-gray-500">Not related</div>
              </template>
            </Column>

            <!-- Assigned To Column -->
            <Column field="assigned_to_id" header="Assigned To">
              <template #body="{ data }">
                <div v-if="data.assigned_to" class="flex items-center">
                  <Avatar 
                    :image="data.assigned_to.profile_photo_url" 
                    size="small" 
                    shape="circle"
                    class="mr-2"
                  />
                  <span>{{ data.assigned_to.name }}</span>
                </div>
                <div v-else class="text-sm text-gray-500">Unassigned</div>
              </template>
            </Column>

            <!-- Actions Column -->
            <Column header="Actions" :exportable="false" style="min-width: 8rem">
              <template #body="{ data }">
                <div class="flex gap-2 justify-center">
                  <Button 
                    icon="pi pi-eye" 
                    severity="info" 
                    text 
                    rounded 
                    :pt="{ root: { class: 'p-1' } }"
                    @click="$inertia.visit(route('crm.activities.show', data.id))"
                    v-tooltip.top="'View'"
                  />
                  <Button 
                    icon="pi pi-pencil" 
                    severity="success" 
                    text 
                    rounded 
                    :pt="{ root: { class: 'p-1' } }"
                    @click="$inertia.visit(route('crm.activities.edit', data.id))"
                    v-tooltip.top="'Edit'"
                  />
                  <Button 
                    icon="pi pi-trash" 
                    severity="danger" 
                    text 
                    rounded 
                    :pt="{ root: { class: 'p-1' } }"
                    @click="confirmActivityDeletion(data)"
                    v-tooltip.top="'Delete'"
                  />
                </div>
              </template>
            </Column>

            <template #empty>
              <div class="text-center p-4">
                No activities found. Try adjusting your filters or <Link :href="route('crm.activities.create')" class="text-blue-600 hover:underline">create a new activity</Link>.
              </div>
            </template>
          </DataTable>

          <!-- Pagination -->
          <div v-if="activities.links && activities.links.length > 3" class="mt-4">
            <Paginator
              :rows="activities.per_page"
              :totalRecords="activities.total"
              :first="(activities.current_page - 1) * activities.per_page"
              @page="onPageChange($event)"
              :rowsPerPageOptions="[10, 25, 50]"
            />
          </div>
        </template>
      </Card>
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog 
      v-model:visible="confirmingActivityDeletion" 
      modal 
      header="Delete Activity" 
      :style="{ width: '450px' }"
    >
      <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
        <span>Are you sure you want to delete this activity?</span>
      </div>
      <template #footer>
        <Button 
          label="No" 
          icon="pi pi-times" 
          @click="closeModal" 
          severity="secondary"
          text
        />
        <Button 
          label="Yes" 
          icon="pi pi-check" 
          @click="deleteActivity" 
          severity="danger"
          :loading="form.processing"
        />
      </template>
    </Dialog>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Dialog from 'primevue/dialog';
import Paginator from 'primevue/paginator';
import Avatar from 'primevue/avatar';
import ActionButton from '@/components/ui/action-button/ActionButton.vue';
import type { BreadcrumbItem } from '@/types';

interface User {
  id: number;
  name: string;
  profile_photo_url?: string;
}

interface RelatedEntity {
  id: number;
  name?: string;
  title?: string;
  [key: string]: any;
}

interface Activity {
  id: number;
  title: string;
  description: string | null;
  type: string;
  status: string;
  start_date: string;
  start_time: string | null;
  end_date: string | null;
  end_time: string | null;
  duration: number | null;
  location: string | null;
  related_type: string | null;
  related_id: number | null;
  related: RelatedEntity | null;
  user_id: number;
  user: User;
  assigned_to_id: number | null;
  assigned_to: User | null;
  created_at: string;
  updated_at: string;
}

interface Pagination {
  data: Activity[];
  links: any[];
  current_page: number;
  from: number;
  last_page: number;
  per_page: number;
  to: number;
  total: number;
}

interface Filters {
  search: string;
  type: string;
  status: string;
  date_range: string;
  related_type: string;
  assigned_to: string;
  sort?: string;
  direction?: string;
}

interface Props {
  activities: Pagination;
  activityTypes: Record<string, string>;
  activityStatuses: Record<string, string>;
  filters: Filters;
  pageTitle: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: 'dashboard',
  },
  {
    title: 'Activities',
    href: 'crm.activities.index',
  }
];

// Table columns
const columns = [
  { key: 'type', label: 'Type', sortable: true },
  { key: 'title', label: 'Title', sortable: true },
  { key: 'start_date', label: 'Date', sortable: true },
  { key: 'status', label: 'Status', sortable: true },
  { key: 'related_type', label: 'Related To', sortable: false },
  { key: 'assigned_to_id', label: 'Assigned To', sortable: false },
];

// Convert activity types object to array for dropdown
const typeOptions = computed(() => {
  return [
    { name: 'All Types', value: '' },
    ...Object.entries(props.activityTypes).map(([value, name]) => ({
      name,
      value
    }))
  ];
});

// Convert activity statuses object to array for dropdown
const statusOptions = computed(() => {
  return [
    { name: 'All Statuses', value: '' },
    ...Object.entries(props.activityStatuses).map(([value, name]) => ({
      name,
      value
    }))
  ];
});

// Date range options
const dateRangeOptions = ref([
  { name: 'Any Time', value: '' },
  { name: 'Today', value: 'today' },
  { name: 'Tomorrow', value: 'tomorrow' },
  { name: 'This Week', value: 'this_week' },
  { name: 'Next Week', value: 'next_week' },
  { name: 'This Month', value: 'this_month' },
  { name: 'Overdue', value: 'overdue' },
]);

// Loading state
const loading = ref(false);

// DataTable filters
const dataTableFilters = ref({});

// Filters state
const filters = ref({
  search: props.filters.search || '',
  type: props.filters.type || '',
  status: props.filters.status || '',
  date_range: props.filters.date_range || '',
  related_type: props.filters.related_type || '',
  assigned_to: props.filters.assigned_to || '',
});

// Sorting state
const sortColumn = ref(props.filters.sort || 'start_date');
const sortDirection = ref(props.filters.direction || 'asc');

// Delete confirmation
const confirmingActivityDeletion = ref(false);
const activityBeingDeleted = ref<Activity | null>(null);

// Form for deletion
const form = useForm({});

// Format date
const formatDate = (dateString: string | null): string => {
  if (!dateString) return '';
  
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }).format(date);
};

// Get type severity for Tag component
const getTypeSeverity = (type: string): string => {
  const severities: Record<string, string> = {
    'call': 'info',
    'meeting': 'warning',
    'email': 'success',
    'task': 'primary',
    'deadline': 'danger',
  };
  return severities[type] || 'secondary';
};

// Get status severity for Tag component
const getStatusSeverity = (status: string): string => {
  const severities: Record<string, string> = {
    'planned': 'secondary',
    'in_progress': 'info',
    'completed': 'success',
    'cancelled': 'danger',
  };
  return severities[status] || 'secondary';
};

// Get type icon for Tag component
const getTypeIcon = (type: string): string => {
  const icons: Record<string, string> = {
    'call': 'pi pi-phone',
    'meeting': 'pi pi-video',
    'email': 'pi pi-envelope',
    'task': 'pi pi-check-square',
    'deadline': 'pi pi-calendar',
  };
  return icons[type] || 'pi pi-calendar';
};

// Get related type icon class
const getRelatedTypeIconClass = (type: string | null): string => {
  if (!type) return 'pi pi-briefcase';
  
  const icons: Record<string, string> = {
    'contact': 'pi pi-user',
    'company': 'pi pi-building',
    'deal': 'pi pi-dollar',
  };
  return icons[type] || 'pi pi-briefcase';
};

// Get related entity name
const getRelatedName = (activity: Activity): string => {
  if (!activity.related) return '';
  
  switch (activity.related_type) {
    case 'contact':
      return activity.related.name || '';
    case 'company':
      return activity.related.name || '';
    case 'deal':
      return activity.related.title || '';
    default:
      return 'Unknown';
  }
};

// Get related entity link
const getRelatedLink = (activity: Activity): string => {
  if (!activity.related || !activity.related.id) return '#';
  
  switch (activity.related_type) {
    case 'contact':
      return route('crm.contacts.show', activity.related.id);
    case 'company':
      return route('crm.companies.show', activity.related.id);
    case 'deal':
      return route('crm.deals.show', activity.related.id);
    default:
      return '#';
  }
};

// Handle pagination
const onPageChange = (event: any) => {
  const page = Math.floor(event.first / event.rows) + 1;
  router.get(route('crm.activities.index'), {
    ...filters.value,
    page,
  }, {
    preserveState: true,
    replace: true,
  });
};

// Get activities with filters and sorting
const getActivities = () => {
  loading.value = true;
  router.get(route('crm.activities.index'), {
    search: filters.value.search,
    type: filters.value.type,
    status: filters.value.status,
    date_range: filters.value.date_range,
    related_type: filters.value.related_type,
    assigned_to: filters.value.assigned_to,
    sort: sortColumn.value,
    direction: sortDirection.value,
  }, {
    preserveState: true,
    replace: true,
    onSuccess: () => {
      loading.value = false;
    },
    onError: () => {
      loading.value = false;
    }
  });
};

// Reset filters
const resetFilters = () => {
  filters.value = {
    search: '',
    type: '',
    status: '',
    date_range: '',
    related_type: '',
    assigned_to: '',
  };
  
  getActivities();
};

// Confirm activity deletion
const confirmActivityDeletion = (activity: Activity) => {
  activityBeingDeleted.value = activity;
  confirmingActivityDeletion.value = true;
};

// Close modal
const closeModal = () => {
  confirmingActivityDeletion.value = false;
  setTimeout(() => {
    activityBeingDeleted.value = null;
  }, 300);
};

// Delete activity
const deleteActivity = () => {
  if (!activityBeingDeleted.value) return;
  
  form.delete(route('crm.activities.destroy', activityBeingDeleted.value.id), {
    onSuccess: () => {
      closeModal();
    },
  });
};

// Watch for filter changes
watch(filters, (newFilters, oldFilters) => {
  // Only auto-refresh on dropdown changes, not on search input
  if (
    newFilters.type !== oldFilters.type ||
    newFilters.status !== oldFilters.status ||
    newFilters.date_range !== oldFilters.date_range ||
    newFilters.related_type !== oldFilters.related_type ||
    newFilters.assigned_to !== oldFilters.assigned_to
  ) {
    getActivities();
  }
}, { deep: true });
</script>
