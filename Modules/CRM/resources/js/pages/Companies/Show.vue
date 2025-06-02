<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #page-title>
      {{ company.name }}
    </template>
    <template #page-actions>
      <div class="flex gap-2">
        <ActionButton :href="route('crm.companies.edit', company.id)" >
          Edit
        </ActionButton>
      </div>
    </template>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Company Information -->
      <div class="lg:col-span-2">
        <Card>
          <template #title>
            <div class="flex items-center">
              <i class="pi pi-building mr-2"></i>
              <span>Company Information</span>
            </div>
          </template>
          <template #content>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h4 class="text-lg font-medium mb-4">Basic Details</h4>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">Company Name</div>
                  <div>{{ company.name }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">Industry</div>
                  <div>{{ company.industry || 'N/A' }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">Website</div>
                  <div>
                    <a v-if="company.website" :href="company.website" target="_blank"
                      class="text-blue-500 hover:underline">
                      {{ company.website }}
                    </a>
                    <span v-else>N/A</span>
                  </div>
                </div>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">Phone</div>
                  <div>{{ company.phone || 'N/A' }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">Status</div>
                  <div>
                    <Tag :value="company.status" :severity="getStatusSeverity(company.status)" />
                  </div>
                </div>
              </div>

              <div>
                <h4 class="text-lg font-medium mb-4">Address</h4>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">Street Address</div>
                  <div>{{ company.address || 'N/A' }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">City</div>
                  <div>{{ company.city || 'N/A' }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">State/Province</div>
                  <div>{{ company.state || 'N/A' }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">Postal Code</div>
                  <div>{{ company.postal_code || 'N/A' }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">Country</div>
                  <div>{{ company.country || 'N/A' }}</div>
                </div>
              </div>
            </div>

            <div class="mt-6">
              <h4 class="text-lg font-medium mb-4">Description</h4>
              <div class="mb-3">
                <div>{{ company.description || 'No description available.' }}</div>
              </div>
            </div>

            <div class="mt-6">
              <h4 class="text-lg font-medium mb-4">Additional Information</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-3">
                  <div class="text-sm text-gray-500">Assigned To</div>
                  <div>{{ company.assigned_to ? company.assigned_to.name : 'N/A' }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">Created By</div>
                  <div>{{ company.creator ? company.creator.name : 'N/A' }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">Created Date</div>
                  <div>{{ new Date(company.created_at).toLocaleDateString() }}</div>
                </div>
                <div class="mb-3">
                  <div class="text-sm text-gray-500">Last Updated</div>
                  <div>{{ new Date(company.updated_at).toLocaleDateString() }}</div>
                </div>
              </div>
            </div>
          </template>
        </Card>

        <!-- Deals -->
        <Card class="mt-6">
          <template #title>
            <div class="flex items-center justify-between w-full">
              <div class="flex items-center">
                <i class="pi pi-dollar mr-2"></i>
                <span>Deals</span>
              </div>
              <Button icon="pi pi-plus" label="Add Deal" size="small" severity="contrast"
                @click="$inertia.visit(route('crm.deals.create', { company_id: company.id }))" />
            </div>
          </template>
          <template #content>
            <DataTable :value="company.deals" responsiveLayout="scroll" class="p-datatable-sm" :rows="5">
              <Column field="name" header="Deal Name">
                <template #body="{ data }">
                  <Link :href="route('crm.deals.show', data.id)" class="text-blue-500 hover:underline">
                  {{ data.name }}
                  </Link>
                </template>
              </Column>
              <Column field="amount" header="Amount">
                <template #body="{ data }">
                  {{ formatCurrency(data.amount) }}
                </template>
              </Column>
              <Column field="stage" header="Stage">
                <template #body="{ data }">
                  <Tag :value="data.stage" :severity="getDealStageSeverity(data.stage)" />
                </template>
              </Column>
              <Column field="expected_close_date" header="Expected Close">
                <template #body="{ data }">
                  {{ data.expected_close_date ? new Date(data.expected_close_date).toLocaleDateString() : 'N/A' }}
                </template>
              </Column>
              <template #empty>
                <div class="text-center p-4">
                  No deals found for this company.
                  <Link :href="route('crm.deals.create', { company_id: company.id })">Create one</Link>.
                </div>
              </template>
            </DataTable>
            <div v-if="company.deals && company.deals.length > 0" class="mt-4 text-right">
              <Link :href="route('crm.deals.index', { company_id: company.id })">View all deals</Link>
            </div>
          </template>
        </Card>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <!-- Contacts -->
        <Card>
          <template #title>
            <div class="flex items-center justify-between w-full">
              <div class="flex items-center">
                <i class="pi pi-users mr-2"></i>
                <span>Contacts</span>
              </div>
              <Button icon="pi pi-plus" label="Add Contact" size="small" severity="contrast"
                @click="$inertia.visit(route('crm.contacts.create', { company_id: company.id }))" />
            </div>
          </template>
          <template #content>
            <div v-if="company.contacts && company.contacts.length > 0" class="space-y-4">
              <div v-for="contact in company.contacts" :key="contact.id" class="p-3 border-b last:border-b-0">
                <div class="flex items-center">
                  <Avatar :label="contact.first_name.charAt(0) + contact.last_name.charAt(0)" shape="circle"
                    class="mr-3" />
                  <div>
                    <Link :href="route('crm.contacts.show', contact.id)" class="font-medium hover:underline">
                    {{ contact.first_name }} {{ contact.last_name }}
                    </Link>
                    <div class="text-sm text-gray-500">{{ contact.job_title || 'N/A' }}</div>
                    <div class="text-sm">{{ contact.email }}</div>
                    <div class="text-sm">{{ contact.phone || 'No phone' }}</div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center p-4">
              No contacts found for this company.
              <Link :href="route('crm.contacts.create', { company_id: company.id })">Create one</Link>.
            </div>
            <div v-if="company.contacts && company.contacts.length > 0" class="mt-4 text-right">
              <Link :href="route('crm.contacts.index', { company_id: company.id })">View all contacts</Link>
            </div>
          </template>
        </Card>

        <!-- Activities -->
        <Card class="mt-6">
          <template #title>
            <div class="flex items-center justify-between w-full">
              <div class="flex items-center">
                <i class="pi pi-calendar mr-2"></i>
                <span>Recent Activities</span>
              </div>
              <Button icon="pi pi-plus" label="Add Activity" size="small" severity="contrast"
                @click="$inertia.visit(route('crm.activities.create', { company_id: company.id }))" />
            </div>
          </template>
          <template #content>
            <div v-if="company.activities && company.activities.length > 0" class="space-y-4">
              <div v-for="activity in company.activities" :key="activity.id" class="p-3 border-b last:border-b-0">
                <div class="flex items-start">
                  <i :class="getActivityIcon(activity.type)" class="mt-1 mr-3 text-lg"></i>
                  <div>
                    <Link :href="route('crm.activities.show', activity.id)" class="font-medium hover:underline">
                    {{ activity.title }}
                    </Link>
                    <div class="text-sm text-gray-500">{{ activity.type }}</div>
                    <div class="text-sm">{{ new Date(activity.due_date).toLocaleDateString() }}</div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center p-4">
              No activities found for this company.
              <Link :href="route('crm.activities.create', { company_id: company.id })">Create one</Link>.
            </div>
            <div v-if="company.activities && company.activities.length > 0" class="mt-4 text-right">
              <Link :href="route('crm.activities.index', { related_to: 'company', related_id: company.id })">View all
              activities</Link>
            </div>
          </template>
        </Card>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Dialog v-model:visible="showDeleteModal" header="Delete Company" :style="{ width: '450px' }" :modal="true">
      <div class="confirmation-content">
        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
        <span>
          Are you sure you want to delete <strong>{{ company.name }}</strong>?
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
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { route } from 'ziggy-js';

import Card from 'primevue/card';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Tag from 'primevue/tag';
import Avatar from 'primevue/avatar';
import { BreadcrumbItem } from '@/types';
import ActionButton from '@/components/ui/action-button/ActionButton.vue';

interface Contact {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone: string | null;
  job_title: string | null;
}

interface Deal {
  id: number;
  name: string;
  amount: number;
  stage: string;
  expected_close_date: string | null;
}

interface Activity {
  id: number;
  title: string;
  type: string;
  due_date: string;
}

interface User {
  id: number;
  name: string;
}

interface Company {
  id: number;
  name: string;
  industry: string | null;
  website: string | null;
  phone: string | null;
  status: string;
  address: string | null;
  city: string | null;
  state: string | null;
  postal_code: string | null;
  country: string | null;
  description: string | null;
  assigned_to: User | null;
  creator: User | null;
  created_at: string;
  updated_at: string;
  contacts: Contact[];
  deals: Deal[];
  activities: Activity[];
}

interface Props {
  company: Company;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: 'dashboard',
  },
  {
    title: 'Companies',
    href: 'crm.companies.index',
  },
  {
    title: props.company.name,
    href: 'crm.companies.show',
    // params: { id: props.company.id }
  }
];

// State
const showDeleteModal = ref<boolean>(false);
const processing = ref<boolean>(false);

// Methods
const getStatusSeverity = (status: string): string => {
  switch (status) {
    case 'customer': return 'success';
    case 'prospect': return 'info';
    case 'partner': return 'warning';
    case 'inactive': return 'danger';
    default: return 'secondary';
  }
};

const getDealStageSeverity = (stage: string): string => {
  switch (stage) {
    case 'won': return 'success';
    case 'lost': return 'danger';
    case 'negotiation': return 'warning';
    case 'proposal': return 'info';
    case 'qualification': return 'secondary';
    default: return 'secondary';
  }
};

const getActivityIcon = (type: string): string => {
  switch (type.toLowerCase()) {
    case 'call': return 'pi pi-phone';
    case 'meeting': return 'pi pi-users';
    case 'email': return 'pi pi-envelope';
    case 'task': return 'pi pi-check-square';
    case 'deadline': return 'pi pi-calendar';
    default: return 'pi pi-calendar';
  }
};

const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
};

const confirmDelete = (): void => {
  showDeleteModal.value = true;
};

const closeModal = (): void => {
  showDeleteModal.value = false;
};

const deleteCompany = (): void => {
  processing.value = true;
  router.delete(route('crm.companies.destroy', props.company.id), {
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
