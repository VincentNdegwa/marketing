<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #page-title>
      {{ pageTitle }}
    </template>
    <template #page-actions>
      <div class="flex gap-2">
        <ActionButton :href="route('crm.activities.show', activity.id)">
          View Activity
        </ActionButton>
        <ActionButton :href="route('crm.activities.index')">
          Back to Activities
        </ActionButton>
      </div>
    </template>

    <div>
      <Card>
        <template #content>
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <!-- Title -->
              <div class="col-span-1 md:col-span-2">
                <label for="title" class="block mb-2">Title</label>
                <InputText
                  id="title"
                  v-model="form.title"
                  class="w-full"
                  required
                  autofocus
                />
                <small v-if="form.errors.title" class="p-error">{{ form.errors.title }}</small>
              </div>

              <!-- Description -->
              <div class="col-span-1 md:col-span-2">
                <label for="description" class="block mb-2">Description</label>
                <Textarea
                  id="description"
                  v-model="form.description"
                  class="w-full"
                  rows="3"
                  autoResize
                />
                <small v-if="form.errors.description" class="p-error">{{ form.errors.description }}</small>
              </div>

              <!-- Type -->
              <div>
                <label for="type" class="block mb-2">Type</label>
                <Dropdown
                  id="type"
                  v-model="form.type"
                  :options="activityTypeOptions"
                  optionLabel="name"
                  optionValue="value"
                  placeholder="Select a type"
                  class="w-full"
                  required
                />
                <small v-if="form.errors.type" class="p-error">{{ form.errors.type }}</small>
              </div>

              <!-- Status -->
              <div>
                <label for="status" class="block mb-2">Status</label>
                <Dropdown
                  id="status"
                  v-model="form.status"
                  :options="activityStatusOptions"
                  optionLabel="name"
                  optionValue="value"
                  placeholder="Select a status"
                  class="w-full"
                  required
                />
                <small v-if="form.errors.status" class="p-error">{{ form.errors.status }}</small>
              </div>

              <!-- Start Date -->
              <div>
                <label for="start_date" class="block mb-2">Start Date</label>
                <Calendar
                  id="start_date"
                  v-model="form.start_date_obj"
                  dateFormat="yy-mm-dd"
                  class="w-full"
                  required
                  @update:modelValue="updateStartDate"
                />
                <small v-if="form.errors.start_date" class="p-error">{{ form.errors.start_date }}</small>
              </div>

              <!-- Start Time (conditional) -->
              <div v-if="showTimeFields">
                <label for="start_time" class="block mb-2">Start Time</label>
                <Calendar
                  id="start_time"
                  v-model="form.start_time_obj"
                  timeOnly
                  class="w-full"
                  @update:modelValue="updateStartTime"
                />
                <small v-if="form.errors.start_time" class="p-error">{{ form.errors.start_time }}</small>
              </div>

              <!-- End Date (conditional) -->
              <div v-if="showEndDateField">
                <label for="end_date" class="block mb-2">End Date</label>
                <Calendar
                  id="end_date"
                  v-model="form.end_date_obj"
                  dateFormat="yy-mm-dd"
                  class="w-full"
                  @update:modelValue="updateEndDate"
                />
                <small v-if="form.errors.end_date" class="p-error">{{ form.errors.end_date }}</small>
              </div>

              <!-- End Time (conditional) -->
              <div v-if="showTimeFields && showEndDateField">
                <label for="end_time" class="block mb-2">End Time</label>
                <Calendar
                  id="end_time"
                  v-model="form.end_time_obj"
                  timeOnly
                  class="w-full"
                  @update:modelValue="updateEndTime"
                />
                <small v-if="form.errors.end_time" class="p-error">{{ form.errors.end_time }}</small>
              </div>

              <!-- Duration (conditional) -->
              <div v-if="form.type === 'call'">
                <label for="duration" class="block mb-2">Duration (minutes)</label>
                <InputNumber
                  id="duration"
                  v-model="form.duration"
                  min="1"
                  class="w-full"
                />
                <small v-if="form.errors.duration" class="p-error">{{ form.errors.duration }}</small>
              </div>

              <!-- Location (conditional) -->
              <div v-if="form.type === 'meeting'">
                <label for="location" class="block mb-2">Location</label>
                <InputText
                  id="location"
                  v-model="form.location"
                  class="w-full"
                />
                <small v-if="form.errors.location" class="p-error">{{ form.errors.location }}</small>
              </div>

              <!-- Related Entity Type -->
              <div>
                <label for="related_type" class="block mb-2">Related To</label>
                <Dropdown
                  id="related_type"
                  v-model="form.related_type"
                  :options="relatedTypeOptions"
                  optionLabel="name"
                  optionValue="value"
                  placeholder="Select related type"
                  class="w-full"
                  @change="form.related_id = null"
                />
                <small v-if="form.errors.related_type" class="p-error">{{ form.errors.related_type }}</small>
              </div>

              <!-- Related Entity -->
              <div v-if="form.related_type">
                <label :for="form.related_type + '_id'" class="block mb-2">{{ relatedEntityLabel }}</label>
                <Dropdown
                  :id="form.related_type + '_id'"
                  v-model="form.related_id"
                  :options="relatedEntities"
                  optionLabel="displayName"
                  optionValue="id"
                  :placeholder="`Select ${relatedEntityLabel}`"
                  class="w-full"
                />
                <small v-if="form.errors.related_id" class="p-error">{{ form.errors.related_id }}</small>
              </div>

              <!-- User (Creator) -->
              <div>
                <label for="user_id" class="block mb-2">Created By</label>
                <Dropdown
                  id="user_id"
                  v-model="form.user_id"
                  :options="users"
                  optionLabel="name"
                  optionValue="id"
                  placeholder="Select user"
                  class="w-full"
                />
                <small v-if="form.errors.user_id" class="p-error">{{ form.errors.user_id }}</small>
              </div>

              <!-- Assigned To -->
              <div>
                <label for="assigned_to_id" class="block mb-2">Assigned To</label>
                <Dropdown
                  id="assigned_to_id"
                  v-model="form.assigned_to_id"
                  :options="assigneeOptions"
                  optionLabel="name"
                  optionValue="id"
                  placeholder="Select assignee"
                  class="w-full"
                />
                <small v-if="form.errors.assigned_to_id" class="p-error">{{ form.errors.assigned_to_id }}</small>
              </div>

              <!-- Outcome (conditional) -->
              <div v-if="form.status === 'completed'" class="col-span-1 md:col-span-2">
                <label for="outcome" class="block mb-2">Outcome</label>
                <Textarea
                  id="outcome"
                  v-model="form.outcome"
                  class="w-full"
                  rows="3"
                  autoResize
                />
                <small v-if="form.errors.outcome" class="p-error">{{ form.errors.outcome }}</small>
              </div>
            </div>

            <div class="flex justify-content-end mt-6">
              <Button 
                label="Cancel" 
                severity="secondary" 
                class="mr-2"
                type="button"
                @click="$inertia.visit(route('crm.activities.index'))" 
              />
              <Button 
                label="Update Activity" 
                type="submit" 
                icon="pi pi-check"
                :loading="form.processing" 
              />
            </div>
          </form>
        </template>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import ActionButton from '@/components/ui/action-button/ActionButton.vue';
import type { BreadcrumbItem } from '@/types';

interface User {
  id: number;
  name: string;
}

interface Contact {
  id: number;
  name: string;
  email?: string;
  displayName?: string;
}

interface Company {
  id: number;
  name: string;
  displayName?: string;
}

interface Deal {
  id: number;
  title: string;
  displayName?: string;
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
  user_id: number;
  assigned_to_id: number | null;
  outcome: string | null;
}

interface Props {
  activity: Activity;
  contacts: Contact[];
  companies: Company[];
  deals: Deal[];
  users: User[];
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
    title: 'Edit Activity',
    href: 'crm.activities.edit',
    params: { id: props.activity.id }
  }
];

// Convert activity types object to array for dropdown
const activityTypeOptions = computed(() => {
  return Object.entries(props.activityTypes).map(([value, name]) => ({
    name,
    value
  }));
});

// Convert activity statuses object to array for dropdown
const activityStatusOptions = computed(() => {
  return Object.entries(props.activityStatuses).map(([value, name]) => ({
    name,
    value
  }));
});

// Related type options
const relatedTypeOptions = ref([
  { name: 'Not Related', value: '' },
  { name: 'Contact', value: 'contact' },
  { name: 'Company', value: 'company' },
  { name: 'Deal', value: 'deal' }
]);

// Prepare assignee options with "Not Assigned" option
const assigneeOptions = computed(() => {
  return [
    { id: null, name: 'Not Assigned' },
    ...props.users
  ];
});

// Helper function to parse date string to Date object
const parseDate = (dateStr: string | null): Date | null => {
  if (!dateStr) return null;
  return new Date(dateStr);
};

// Helper function to parse time string to Date object
const parseTime = (timeStr: string | null): Date | null => {
  if (!timeStr) return null;
  const [hours, minutes] = timeStr.split(':').map(Number);
  const date = new Date();
  date.setHours(hours, minutes, 0, 0);
  return date;
};

// Form with date objects for Calendar component
const form = useForm({
  title: props.activity.title,
  description: props.activity.description || '',
  type: props.activity.type,
  status: props.activity.status,
  start_date: props.activity.start_date,
  start_date_obj: parseDate(props.activity.start_date),
  start_time: props.activity.start_time || '',
  start_time_obj: parseTime(props.activity.start_time),
  end_date: props.activity.end_date || '',
  end_date_obj: parseDate(props.activity.end_date),
  end_time: props.activity.end_time || '',
  end_time_obj: parseTime(props.activity.end_time),
  duration: props.activity.duration,
  location: props.activity.location || '',
  related_type: props.activity.related_type || '',
  related_id: props.activity.related_id,
  user_id: props.activity.user_id,
  assigned_to_id: props.activity.assigned_to_id,
  outcome: props.activity.outcome || '',
});

// Computed properties
const showTimeFields = computed(() => {
  return ['call', 'meeting'].includes(form.type);
});

const showEndDateField = computed(() => {
  return ['meeting'].includes(form.type);
});

const relatedEntityLabel = computed(() => {
  switch (form.related_type) {
    case 'contact':
      return 'Contact';
    case 'company':
      return 'Company';
    case 'deal':
      return 'Deal';
    default:
      return '';
  }
});

// Process related entities to add displayName property
const processRelatedEntities = (entities: any[], type: string) => {
  return entities.map(entity => {
    let displayName = '';
    switch (type) {
      case 'contact':
        displayName = `${entity.name || entity.first_name + ' ' + entity.last_name} ${entity.email ? `(${entity.email})` : ''}`;
        break;
      case 'company':
        displayName = entity.name;
        break;
      case 'deal':
        displayName = entity.title;
        break;
    }
    return { ...entity, displayName };
  });
};

// Get related entities based on type
const relatedEntities = computed(() => {
  switch (form.related_type) {
    case 'contact':
      return processRelatedEntities(props.contacts, 'contact');
    case 'company':
      return processRelatedEntities(props.companies, 'company');
    case 'deal':
      return processRelatedEntities(props.deals, 'deal');
    default:
      return [];
  }
});

// Methods to update date/time string values from Date objects
const updateStartDate = () => {
  if (form.start_date_obj) {
    const year = form.start_date_obj.getFullYear();
    const month = (form.start_date_obj.getMonth() + 1).toString().padStart(2, '0');
    const day = form.start_date_obj.getDate().toString().padStart(2, '0');
    form.start_date = `${year}-${month}-${day}`;
  }
};

const updateStartTime = () => {
  if (form.start_time_obj) {
    const hours = form.start_time_obj.getHours().toString().padStart(2, '0');
    const minutes = form.start_time_obj.getMinutes().toString().padStart(2, '0');
    form.start_time = `${hours}:${minutes}`;
  } else {
    form.start_time = '';
  }
};

const updateEndDate = () => {
  if (form.end_date_obj) {
    const year = form.end_date_obj.getFullYear();
    const month = (form.end_date_obj.getMonth() + 1).toString().padStart(2, '0');
    const day = form.end_date_obj.getDate().toString().padStart(2, '0');
    form.end_date = `${year}-${month}-${day}`;
  } else {
    form.end_date = '';
  }
};

const updateEndTime = () => {
  if (form.end_time_obj) {
    const hours = form.end_time_obj.getHours().toString().padStart(2, '0');
    const minutes = form.end_time_obj.getMinutes().toString().padStart(2, '0');
    form.end_time = `${hours}:${minutes}`;
  } else {
    form.end_time = '';
  }
};

// Submit form
const submit = () => {
  form.put(route('crm.activities.update', props.activity.id), {
    onSuccess: () => {
      // Success handling if needed
    },
  });
};

// Watch for type changes to reset conditional fields
watch(() => form.type, (newType) => {
  if (!['call', 'meeting'].includes(newType)) {
    form.start_time = '';
    form.start_time_obj = null;
    form.end_time = '';
    form.end_time_obj = null;
  }
  
  if (newType !== 'meeting') {
    form.end_date = '';
    form.end_date_obj = null;
    form.location = '';
  }
  
  if (newType !== 'call') {
    form.duration = null;
  }
});

// Watch for status changes to reset outcome
watch(() => form.status, (newStatus) => {
  if (newStatus !== 'completed') {
    form.outcome = '';
  }
});
</script>
