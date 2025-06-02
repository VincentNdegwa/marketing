<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #page-title>
      {{ pageTitle }}
    </template>

    <div>
      <Card>
        <template #content>
          <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Basic Information Section -->
              <div class="col-span-2">
                <h3 class="text-lg font-medium mb-4">Basic Information</h3>
              </div>

              <!-- Company Name -->
              <div class="col-span-2 md:col-span-1">
                <div class="field">
                  <label for="name" class="block mb-1">Company Name <span class="text-red-500">*</span></label>
                  <InputText id="name" v-model="form.name" class="w-full" :class="{ 'p-invalid': form.errors.name }" />
                  <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
                </div>
              </div>

              <!-- Industry -->
              <div class="col-span-2 md:col-span-1">
                <div class="field">
                  <label for="industry" class="block mb-1">Industry</label>
                  <Dropdown id="industry" v-model="form.industry" :options="industryOptions" optionLabel="name" 
                    optionValue="value" placeholder="Select Industry" class="w-full" />
                  <small v-if="form.errors.industry" class="p-error">{{ form.errors.industry }}</small>
                </div>
              </div>

              <!-- Website -->
              <div class="col-span-2 md:col-span-1">
                <div class="field">
                  <label for="website" class="block mb-1">Website</label>
                  <InputText id="website" v-model="form.website" class="w-full" placeholder="https://example.com" />
                  <small v-if="form.errors.website" class="p-error">{{ form.errors.website }}</small>
                </div>
              </div>

              <!-- Phone -->
              <div class="col-span-2 md:col-span-1">
                <div class="field">
                  <label for="phone" class="block mb-1">Phone</label>
                  <InputText id="phone" v-model="form.phone" class="w-full" />
                  <small v-if="form.errors.phone" class="p-error">{{ form.errors.phone }}</small>
                </div>
              </div>

              <!-- Status -->
              <div class="col-span-2 md:col-span-1">
                <div class="field">
                  <label for="status" class="block mb-1">Status <span class="text-red-500">*</span></label>
                  <Dropdown id="status" v-model="form.status" :options="statusOptions" optionLabel="name" 
                    optionValue="value" placeholder="Select Status" class="w-full" :class="{ 'p-invalid': form.errors.status }" />
                  <small v-if="form.errors.status" class="p-error">{{ form.errors.status }}</small>
                </div>
              </div>

              <!-- Assigned To -->
              <div class="col-span-2 md:col-span-1">
                <div class="field">
                  <label for="assigned_to" class="block mb-1">Assigned To</label>
                  <Dropdown id="assigned_to" v-model="form.assigned_to" :options="userOptions" optionLabel="name" 
                    optionValue="id" placeholder="Select User" class="w-full" />
                  <small v-if="form.errors.assigned_to" class="p-error">{{ form.errors.assigned_to }}</small>
                </div>
              </div>

              <!-- Address Section -->
              <div class="col-span-2">
                <h3 class="text-lg font-medium mb-4 mt-4">Address Information</h3>
              </div>

              <!-- Address -->
              <div class="col-span-2">
                <div class="field">
                  <label for="address" class="block mb-1">Street Address</label>
                  <Textarea id="address" v-model="form.address" rows="2" class="w-full" />
                  <small v-if="form.errors.address" class="p-error">{{ form.errors.address }}</small>
                </div>
              </div>

              <!-- City -->
              <div class="col-span-2 md:col-span-1">
                <div class="field">
                  <label for="city" class="block mb-1">City</label>
                  <InputText id="city" v-model="form.city" class="w-full" />
                  <small v-if="form.errors.city" class="p-error">{{ form.errors.city }}</small>
                </div>
              </div>

              <!-- State/Province -->
              <div class="col-span-2 md:col-span-1">
                <div class="field">
                  <label for="state" class="block mb-1">State/Province</label>
                  <InputText id="state" v-model="form.state" class="w-full" />
                  <small v-if="form.errors.state" class="p-error">{{ form.errors.state }}</small>
                </div>
              </div>

              <!-- Postal Code -->
              <div class="col-span-2 md:col-span-1">
                <div class="field">
                  <label for="postal_code" class="block mb-1">Postal Code</label>
                  <InputText id="postal_code" v-model="form.postal_code" class="w-full" />
                  <small v-if="form.errors.postal_code" class="p-error">{{ form.errors.postal_code }}</small>
                </div>
              </div>

              <!-- Country -->
              <div class="col-span-2 md:col-span-1">
                <div class="field">
                  <label for="country" class="block mb-1">Country</label>
                  <InputText id="country" v-model="form.country" class="w-full" />
                  <small v-if="form.errors.country" class="p-error">{{ form.errors.country }}</small>
                </div>
              </div>

              <!-- Additional Information Section -->
              <div class="col-span-2">
                <h3 class="text-lg font-medium mb-4 mt-4">Additional Information</h3>
              </div>

              <!-- Description -->
              <div class="col-span-2">
                <div class="field">
                  <label for="description" class="block mb-1">Description</label>
                  <Textarea id="description" v-model="form.description" rows="3" class="w-full" />
                  <small v-if="form.errors.description" class="p-error">{{ form.errors.description }}</small>
                </div>
              </div>

              <!-- Form Buttons -->
              <div class="col-span-2 flex justify-end gap-2 mt-4">
                <Button type="button" label="Cancel" severity="contrast" variant="outlined"  @click="cancel" />
                <Button type="submit" severity="contrast" label="Create Company" :loading="processing" />
              </div>
            </div>
          </form>
        </template>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { route } from 'ziggy-js';

import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import { BreadcrumbItem } from '@/types';

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
    title: 'Create',
    href: 'crm.companies.create',
  }
];

interface Props {
  pageTitle: string;
  users: Array<{
    id: number;
    name: string;
  }>;
}

const props = defineProps<Props>();

// Form state
const form = useForm({
  name: '',
  industry: null,
  website: '',
  phone: '',
  status: 'prospect',
  address: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
  description: '',
  assigned_to: null,
  custom_fields: {}
});

const processing = ref<boolean>(false);

// Dropdown options
const statusOptions = ref([
  { name: 'Prospect', value: 'prospect' },
  { name: 'Customer', value: 'customer' },
  { name: 'Partner', value: 'partner' },
  { name: 'Inactive', value: 'inactive' }
]);

const industryOptions = ref([
  { name: 'Technology', value: 'technology' },
  { name: 'Finance', value: 'finance' },
  { name: 'Healthcare', value: 'healthcare' },
  { name: 'Education', value: 'education' },
  { name: 'Manufacturing', value: 'manufacturing' },
  { name: 'Retail', value: 'retail' },
  { name: 'Other', value: 'other' }
]);

// Convert users to dropdown options
const userOptions = ref(props.users || []);

// Methods
const submitForm = () => {
  processing.value = true;
  form.post(route('crm.companies.store'), {
    onSuccess: () => {
      processing.value = false;
    },
    onError: () => {
      processing.value = false;
    }
  });
};

const cancel = () => {
  window.history.back();
};
</script>
