<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #page-title>
      {{ pageTitle }}
    </template>
    <template #page-actions>
      <div class="flex gap-2">
        <ActionButton :href="route('crm.activities.edit', activity.id)">
          Edit Activity
        </ActionButton>
        <ActionButton :href="route('crm.activities.index')">
          Back to Activities
        </ActionButton>
        <Button 
          v-if="activity.status !== 'completed'"
          @click="markAsCompleted"
          severity="success"
          icon="pi pi-check"
          :loading="processing"
        >
          Mark as Completed
        </Button>
        <Button
          @click="confirmDeletion"
          severity="danger"
          icon="pi pi-trash"
        >
          Delete
        </Button>
      </div>
    </template>

    <div>
      <!-- Activity Information Card -->
      <Card class="mb-4">
        <template #content>
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-2xl font-bold text-gray-800">{{ activity.title }}</h3>
              <div class="mt-2 flex items-center gap-4">
                <Tag 
                  :severity="getTypeSeverity(activity.type)"
                  :value="activityTypes[activity.type]"
                  :icon="getTypeIcon(activity.type)"
                />
                <Tag 
                  :severity="getStatusSeverity(activity.status)"
                  :value="activityStatuses[activity.status]"
                />
              </div>
            </div>
          </div>
          
          <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="text-sm font-medium text-gray-500">Details</h4>
              <div class="mt-2 space-y-4">
                <!-- Description -->
                <div v-if="activity.description">
                  <h5 class="text-xs font-medium text-gray-500">Description</h5>
                  <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ activity.description }}</p>
                </div>
                
                <!-- Outcome (if completed) -->
                <div v-if="activity.status === 'completed' && activity.outcome">
                  <h5 class="text-xs font-medium text-gray-500">Outcome</h5>
                  <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ activity.outcome }}</p>
                </div>
                
                <!-- Date & Time -->
                <div>
                  <h5 class="text-xs font-medium text-gray-500">Date & Time</h5>
                  <div class="mt-1 text-sm text-gray-900">
                    <p>
                      <i class="pi pi-calendar mr-2"></i>
                      {{ formatDate(activity.start_date) }}
                      <template v-if="activity.end_date && activity.end_date !== activity.start_date">
                        - {{ formatDate(activity.end_date) }}
                      </template>
                    </p>
                    <p v-if="activity.start_time">
                      <i class="pi pi-clock mr-2"></i>
                      {{ formatTime(activity.start_time) }}
                      <template v-if="activity.end_time">
                        - {{ formatTime(activity.end_time) }}
                      </template>
                    </p>
                    <p v-if="activity.duration">
                      <i class="pi pi-stopwatch mr-2"></i>
                      Duration: {{ activity.duration }} minutes
                    </p>
                  </div>
                </div>
                
                <!-- Location (if meeting) -->
                <div v-if="activity.location">
                  <h5 class="text-xs font-medium text-gray-500">Location</h5>
                  <p class="mt-1 text-sm text-gray-900">{{ activity.location }}</p>
                </div>
              </div>
            </div>
            
            <div>
              <h4 class="text-sm font-medium text-gray-500">Relationships</h4>
              <div class="mt-2 space-y-4">
                <!-- Related Entity -->
                <div v-if="activity.related_type && activity.related">
                  <h5 class="text-xs font-medium text-gray-500">Related To</h5>
                  <div class="mt-1 text-sm text-gray-900">
                    <Link 
                      :href="getRelatedEntityRoute(activity.related_type, activity.related.id)"
                      class="flex items-center text-blue-600 hover:underline"
                    >
                      <i :class="getRelatedTypeIconClass(activity.related_type)" class="mr-2"></i>
                      <span>{{ getRelatedEntityName(activity.related_type, activity.related) }}</span>
                    </Link>
                  </div>
                </div>
                
                <!-- Created By -->
                <div v-if="activity.user">
                  <h5 class="text-xs font-medium text-gray-500">Created By</h5>
                  <div class="mt-1 text-sm text-gray-900 flex items-center">
                    <i class="pi pi-user mr-2"></i>
                    <span>{{ activity.user.name }}</span>
                  </div>
                </div>
                
                <!-- Assigned To -->
                <div v-if="activity.assigned_to">
                  <h5 class="text-xs font-medium text-gray-500">Assigned To</h5>
                  <div class="mt-1 text-sm text-gray-900 flex items-center">
                    <i class="pi pi-user mr-2"></i>
                    <span>{{ activity.assigned_to.name }}</span>
                  </div>
                </div>
                
                <!-- Timestamps -->
                <div>
                  <h5 class="text-xs font-medium text-gray-500">Timestamps</h5>
                  <div class="mt-1 text-sm text-gray-900">
                    <p>Created: {{ formatDateTime(activity.created_at) }}</p>
                    <p>Updated: {{ formatDateTime(activity.updated_at) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
      </Card>
      
      <!-- Complete Activity Form (if not completed) -->
      <Card v-if="showCompleteForm" class="mb-4">
        <template #title>
          Mark Activity as Completed
        </template>
        <template #content>
          <form @submit.prevent="submitCompletion">
            <div class="mb-4">
              <label for="outcome" class="block mb-2">Outcome</label>
              <Textarea
                id="outcome"
                v-model="completeForm.outcome"
                rows="3"
                class="w-full"
                autoResize
              />
              <small v-if="completeForm.errors.outcome" class="p-error">{{ completeForm.errors.outcome }}</small>
            </div>
            
            <div class="flex justify-end gap-2">
              <Button 
                type="button" 
                label="Cancel" 
                severity="secondary"
                @click="cancelCompletion"
              />
              <Button 
                type="submit" 
                label="Complete Activity" 
                icon="pi pi-check"
                :loading="completeForm.processing"
              />
            </div>
          </form>
        </template>
      </Card>
    </div>
    
    <!-- Delete Confirmation Dialog -->
    <Dialog 
      v-model:visible="confirmingDeletion" 
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
          @click="cancelDeletion" 
          severity="secondary"
          text
        />
        <Button 
          label="Yes" 
          icon="pi pi-check" 
          @click="deleteActivity" 
          severity="danger"
          :loading="deleteForm.processing"
        />
      </template>
    </Dialog>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from 'primevue/card';
import Button from 'primevue/button';
import Textarea from 'primevue/textarea';
import Dialog from 'primevue/dialog';
import Tag from 'primevue/tag';
import ActionButton from '@/components/ui/action-button/ActionButton.vue';
import type { BreadcrumbItem } from '@/types';

interface User {
  id: number;
  name: string;
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
  outcome: string | null;
  created_at: string;
  updated_at: string;
}

interface Props {
  activity: Activity;
  activityTypes: Record<string, string>;
  activityStatuses: Record<string, string>;
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
  },
  {
    title: 'Activity Details',
    href: 'crm.activities.show',
    params: { id: props.activity.id }
  }
];

// State
const processing = ref(false);
const showCompleteForm = ref(false);
const confirmingDeletion = ref(false);

// Forms
const completeForm = useForm({
  outcome: '',
});

const deleteForm = useForm({});

// Methods
const formatDate = (dateString: string | null): string => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString();
};

const formatTime = (timeString: string | null): string => {
  if (!timeString) return '';
  return timeString;
};

const formatDateTime = (dateTimeString: string | null): string => {
  if (!dateTimeString) return '';
  const date = new Date(dateTimeString);
  return date.toLocaleString();
};

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

const getStatusSeverity = (status: string): string => {
  const severities: Record<string, string> = {
    'planned': 'secondary',
    'in_progress': 'info',
    'completed': 'success',
    'cancelled': 'danger',
  };
  return severities[status] || 'secondary';
};

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

const getRelatedTypeIconClass = (type: string | null): string => {
  if (!type) return 'pi pi-briefcase';
  
  const icons: Record<string, string> = {
    'contact': 'pi pi-user',
    'company': 'pi pi-building',
    'deal': 'pi pi-dollar',
  };
  return icons[type] || 'pi pi-briefcase';
};

const getRelatedEntityName = (type: string | null, entity: RelatedEntity | null): string => {
  if (!entity || !type) return '';
  
  switch (type) {
    case 'contact':
      return entity.name || '';
    case 'company':
      return entity.name || '';
    case 'deal':
      return entity.title || '';
    default:
      return '';
  }
};

const getRelatedEntityRoute = (type: string | null, id: number): string => {
  if (!type || !id) return '#';
  
  switch (type) {
    case 'contact':
      return route('crm.contacts.show', id);
    case 'company':
      return route('crm.companies.show', id);
    case 'deal':
      return route('crm.deals.show', id);
    default:
      return '#';
  }
};

const markAsCompleted = (): void => {
  showCompleteForm.value = true;
};

const cancelCompletion = (): void => {
  showCompleteForm.value = false;
  completeForm.reset();
};

const submitCompletion = (): void => {
  completeForm.put(route('crm.activities.complete', props.activity.id), {
    onSuccess: () => {
      showCompleteForm.value = false;
      completeForm.reset();
    },
  });
};

const confirmDeletion = (): void => {
  confirmingDeletion.value = true;
};

const cancelDeletion = (): void => {
  confirmingDeletion.value = false;
};

const deleteActivity = (): void => {
  deleteForm.delete(route('crm.activities.destroy', props.activity.id), {
    onSuccess: () => {
      confirmingDeletion.value = false;
    },
  });
};
</script>
