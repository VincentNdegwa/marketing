<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #page-title>
      {{ pageTitle }}
    </template>
    <template #page-actions>
      <ButtonGroup>
        <Button size="small" label="Edit" severity="contrast"
          @click="navigateTo(route('crm.deals.edit', deal.id))" />
        <Button size="small" label="Delete" severity="danger" @click="confirmDeletion" />
      </ButtonGroup>
    </template>

    <div>
      <!-- Deal Information Card -->
      <Card class="mb-4">
        <template #title>
          Deal Information
        </template>
        <template #content>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500">Title</h4>
                <p class="mt-1 text-sm text-gray-900">{{ deal.title }}</p>
              </div>

              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500">Description</h4>
                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ deal.description || 'No description provided' }}</p>
              </div>

              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500">Value</h4>
                <p class="mt-1 text-sm text-gray-900">
                  {{ formatCurrency(deal.value, deal.currency) }}
                </p>
              </div>

              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500">Probability</h4>
                <p class="mt-1 text-sm text-gray-900">
                  {{ deal.probability ? `${deal.probability}%` : 'Not set' }}
                </p>
              </div>
            </div>

            <div>
              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500">Stage</h4>
                <p class="mt-1">
                  <Tag :severity="getStageSeverity(deal.stage)" :value="deal.stage" />
                </p>
              </div>

              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500">Status</h4>
                <p class="mt-1">
                  <Tag :severity="getStatusSeverity(deal.status)" :value="deal.status" />
                </p>
              </div>

              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500">Expected Close Date</h4>
                <p class="mt-1 text-sm text-gray-900">
                  {{ deal.expected_close_date ? formatDate(deal.expected_close_date) : 'Not set' }}
                </p>
              </div>

              <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-500">Actual Close Date</h4>
                <p class="mt-1 text-sm text-gray-900">
                  {{ deal.actual_close_date ? formatDate(deal.actual_close_date) : 'Not set' }}
                </p>
              </div>
            </div>
          </div>
        </template>
      </Card>

      <!-- Related Information Card -->
      <Card class="mb-4">
        <template #title>
          Related Information
        </template>
        <template #content>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">Contact</h4>
              <div v-if="deal.contact" class="p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                  <Avatar :image="deal.contact.avatar" size="large" shape="circle" :pt="{
                      image: { class: 'w-10 h-10' }
                    }">
                    <template #icon>
                      <User class="w-6 h-6" />
                    </template>
                  </Avatar>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">{{ deal.contact.name }}</div>
                    <div class="text-sm text-gray-500">{{ deal.contact.email }}</div>
                  </div>
                </div>
                <div class="mt-3">
                  <Link :href="route('crm.contacts.show', deal.contact.id)"
                    class="text-sm text-indigo-600 hover:text-indigo-900">
                  View Contact
                  </Link>
                </div>
              </div>
              <p v-else class="text-sm text-gray-500">No contact associated</p>
            </div>

            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">Company</h4>
              <div v-if="deal.company" class="p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                  <Avatar :image="deal.company.logo" size="large" shape="circle" :pt="{
                      image: { class: 'w-10 h-10' }
                    }">
                    <template #icon>
                      <Building class="w-6 h-6" />
                    </template>
                  </Avatar>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">{{ deal.company.name }}</div>
                    <div class="text-sm text-gray-500">{{ deal.company.email }}</div>
                  </div>
                </div>
                <div class="mt-3">
                  <Link :href="route('crm.companies.show', deal.company.id)"
                    class="text-sm text-indigo-600 hover:text-indigo-900">
                  View Company
                  </Link>
                </div>
              </div>
              <p v-else class="text-sm text-gray-500">No company associated</p>
            </div>

            <div>
              <h4 class="text-sm font-medium text-gray-500 mb-2">Owner</h4>
              <div v-if="deal.user" class="p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                  <Avatar :image="deal.user.profile_photo_url" size="large" shape="circle" :pt="{
                      image: { class: 'w-10 h-10' }
                    }">
                    <template #icon>
                      <UserCircle class="w-6 h-6" />
                    </template>
                  </Avatar>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">{{ deal.user.name }}</div>
                    <div class="text-sm text-gray-500">{{ deal.user.email }}</div>
                  </div>
                </div>
              </div>
              <p v-else class="text-sm text-gray-500">No owner assigned</p>
            </div>
          </div>
        </template>
      </Card>

      <!-- Related Activities Card -->
      <Card class="mb-4">
        <template #title>
          <div class="flex justify-between items-center">
            <span>Related Activities</span>
            <Link :href="route('crm.activities.create', { related_type: 'deal', related_id: deal.id })"
              class="text-sm text-indigo-600 hover:text-indigo-900 flex items-center">
            <Plus class="w-4 h-4 mr-1" />
            Add Activity
            </Link>
          </div>
        </template>
        <template #content>
          <div v-if="activities && activities?.length > 0" class="space-y-4">
            <div v-for="activity in activities" :key="activity.id" class="p-3 bg-gray-50 rounded-lg">
              <div class="flex justify-between">
                <div>
                  <Link :href="route('crm.activities.show', activity.id)"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                  {{ activity.title }}
                  </Link>
                  <div class="mt-1 flex items-center space-x-2">
                    <Tag :severity="getActivityTypeSeverity(activity.type)" :value="activityTypes[activity.type]"
                      :icon="getActivityTypeIcon(activity.type)" />
                    <Tag :severity="getActivityStatusSeverity(activity.status)"
                      :value="activityStatuses[activity.status]" />
                  </div>
                  <div class="mt-2 text-sm text-gray-500">
                    {{ formatDate(activity.start_date) }}
                    <template v-if="activity.start_time">
                      at {{ activity.start_time }}
                    </template>
                  </div>
                </div>
                <div>
                  <Link :href="route('crm.activities.edit', activity.id)"
                    class="text-sm text-indigo-600 hover:text-indigo-900">
                  Edit
                  </Link>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-sm text-gray-500 py-4">
            No activities found for this deal.
          </div>
        </template>
      </Card>

      <!-- Related Tasks Card -->
      <Card class="mb-4">
        <template #title>
          <div class="flex justify-between items-center">
            <span>Related Tasks</span>
            <Link :href="route('crm.tasks.create', { related_type: 'deal', related_id: deal.id })"
              class="text-sm text-indigo-600 hover:text-indigo-900 flex items-center">
            <Plus class="w-4 h-4 mr-1" />
            Add Task
            </Link>
          </div>
        </template>
        <template #content>
          <div v-if=" tasks && tasks.length > 0" class="space-y-4">
            <div v-for="task in tasks" :key="task.id" class="p-3 bg-gray-50 rounded-lg">
              <div class="flex justify-between">
                <div>
                  <Link :href="route('crm.tasks.show', task.id)"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                  {{ task.title }}
                  </Link>
                  <div class="mt-1 flex items-center space-x-2">
                    <Tag :severity="getTaskPrioritySeverity(task.priority)" :value="task.priority" />
                    <Tag :severity="getTaskStatusSeverity(task.status)" :value="taskStatuses[task.status]" />
                  </div>
                  <div class="mt-2 text-sm text-gray-500">
                    Due: {{ formatDate(task.due_date) }}
                  </div>
                </div>
                <div>
                  <Link :href="route('crm.tasks.edit', task.id)" class="text-sm text-indigo-600 hover:text-indigo-900">
                  Edit
                  </Link>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-sm text-gray-500 py-4">
            No tasks found for this deal.
          </div>
        </template>
      </Card>

      <!-- Related Notes Card -->
      <Card>
        <template #title>
          <div class="flex justify-between items-center">

          </div>
        </template>

        <template #content>
          <div v-if="notes && notes.length > 0" class="space-y-4">
            <div v-for="note in notes" :key="note.id" class="bg-gray-50 p-4 rounded-lg">
              <div class="flex justify-between">
                <div>
                  <div class="text-sm text-gray-500 mb-2">
                    Added by {{ note.user ? note.user.name : 'Unknown' }} on {{ formatDate(note.created_at) }}
                  </div>
                  <div class="text-sm whitespace-pre-line">{{ note.content }}</div>
                </div>
                <div>
                  <Link :href="route('crm.notes.edit', note.id)" class="text-sm text-indigo-600 hover:text-indigo-900">
                  Edit
                  </Link>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-sm text-gray-500 py-4">
            No notes found for this deal.
          </div>
        </template>
      </Card>
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:visible="confirmingDeletion" modal header="Delete Deal" :style="{ width: '450px' }" :pt="{
        root: { class: 'border rounded-lg shadow-lg' },
        header: { class: 'border-b p-4' },
        content: { class: 'p-4' },
        footer: { class: 'border-t p-4 flex justify-end gap-2' }
      }">
      <div class="mb-4">
        Are you sure you want to delete this deal? This action cannot be undone.
      </div>
      <template #footer>
        <Button label="Cancel" @click="cancelDeletion" severity="secondary" outlined />
        <Button label="Delete" @click="deleteDeal" severity="danger" :loading="deleteForm.processing" />
      </template>
    </Dialog>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ActionButton from '@/components/ui/action-button/ActionButton.vue';
import Card from 'primevue/card';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Avatar from 'primevue/avatar';
import Dialog from 'primevue/dialog';
import { 
  User, 
  Building, 
  UserCircle,
  Plus
} from 'lucide-vue-next';
import { route } from 'ziggy-js';

// Types
interface BreadcrumbItem {
  title: string;
  href: string;
  params?: Record<string, any>;
}

interface User {
  id: number;
  name: string;
  email: string;
  profile_photo_url?: string;
}

interface Contact {
  id: number;
  name: string;
  email: string;
  phone?: string;
  avatar?: string;
}

interface Company {
  id: number;
  name: string;
  email?: string;
  phone?: string;
  logo?: string;
}

interface Activity {
  id: number;
  title: string;
  type: string;
  status: string;
  start_date: string;
  start_time?: string;
  end_date?: string;
  end_time?: string;
}

interface Task {
  id: number;
  title: string;
  status: string;
  priority: string;
  due_date: string;
}

interface Note {
  id: number;
  content: string;
  created_at: string;
  user_id: number;
  user?: User;
}

interface Deal {
  id: number;
  title: string;
  description?: string;
  value?: number;
  currency?: string;
  probability?: number;
  stage: string;
  status: string;
  expected_close_date?: string;
  actual_close_date?: string;
  contact_id?: number;
  contact?: Contact;
  company_id?: number;
  company?: Company;
  user_id: number;
  user?: User;
  created_at: string;
  updated_at: string;
}

interface Props {
  deal: Deal;
  activities: Activity[];
  tasks: Task[];
  notes: Note[];
  stages: Record<string, string>;
  statuses: Record<string, string>;
  activityTypes?: Record<string, string>;
  activityStatuses?: Record<string, string>;
  taskStatuses?: Record<string, string>;
  pageTitle?: string;
}

const props = withDefaults(defineProps<Props>(), {
  activityTypes: () => ({
    'call': 'Call',
    'meeting': 'Meeting',
    'email': 'Email',
    'task': 'Task',
    'deadline': 'Deadline'
  }),
  activityStatuses: () => ({
    'planned': 'Planned',
    'in_progress': 'In Progress',
    'completed': 'Completed',
    'cancelled': 'Cancelled'
  }),
  taskStatuses: () => ({
    'pending': 'Pending',
    'in_progress': 'In Progress',
    'completed': 'Completed',
    'cancelled': 'Cancelled'
  }),
  pageTitle: ''
});

// Computed
const pageTitle = props.pageTitle || `Deal: ${props.deal.title}`;

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: 'dashboard',
  },
  {
    title: 'Deals',
    href: 'crm.deals.index',
  },
  {
    title: 'Deal Details',
    href: 'crm.deals.show',
    params: { id: props.deal.id }
  }
];

// State
const confirmingDeletion = ref(false);
const deleteForm = useForm({});
const navigateTo = (path: string) => {
  router.visit(path);
};
// Methods
const formatCurrency = (value?: number, currency: string = 'USD'): string => {
  if (value === undefined || value === null) return 'Not set';
  
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: currency || 'USD',
  }).format(value);
};

const formatDate = (dateString?: string): string => {
  if (!dateString) return '';
  
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }).format(date);
};

const getStageSeverity = (stage: string): string => {
  const severities: Record<string, string> = {
    'lead': 'info',
    'qualified': 'info',
    'proposal': 'warning',
    'negotiation': 'warning',
    'closed_won': 'success',
    'closed_lost': 'danger',
  };
  return severities[stage] || 'secondary';
};

const getStatusSeverity = (status: string): string => {
  const severities: Record<string, string> = {
    'open': 'success',
    'closed': 'secondary',
    'pending': 'warning',
  };
  return severities[status] || 'secondary';
};

const getActivityTypeSeverity = (type: string): string => {
  const severities: Record<string, string> = {
    'call': 'info',
    'meeting': 'warning',
    'email': 'success',
    'task': 'primary',
    'deadline': 'danger',
  };
  return severities[type] || 'secondary';
};

const getActivityStatusSeverity = (status: string): string => {
  const severities: Record<string, string> = {
    'planned': 'secondary',
    'in_progress': 'info',
    'completed': 'success',
    'cancelled': 'danger',
  };
  return severities[status] || 'secondary';
};

const getActivityTypeIcon = (type: string): string => {
  const icons: Record<string, string> = {
    'call': 'pi pi-phone',
    'meeting': 'pi pi-video',
    'email': 'pi pi-envelope',
    'task': 'pi pi-check-square',
    'deadline': 'pi pi-calendar',
  };
  return icons[type] || 'pi pi-calendar';
};

const getTaskPrioritySeverity = (priority: string): string => {
  const severities: Record<string, string> = {
    'low': 'info',
    'medium': 'warning',
    'high': 'danger',
  };
  return severities[priority] || 'secondary';
};

const getTaskStatusSeverity = (status: string): string => {
  const severities: Record<string, string> = {
    'pending': 'warning',
    'in_progress': 'info',
    'completed': 'success',
    'cancelled': 'danger',
  };
  return severities[status] || 'secondary';
};

const confirmDeletion = (): void => {
  confirmingDeletion.value = true;
};

const cancelDeletion = (): void => {
  confirmingDeletion.value = false;
};

const deleteDeal = (): void => {
  deleteForm.delete(route('crm.deals.destroy', props.deal.id), {
    onSuccess: () => {
      confirmingDeletion.value = false;
    },
  });
};
</script>
