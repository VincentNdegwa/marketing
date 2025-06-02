<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <template #page-title>Create Deal</template>
    <template #page-actions>
      <ActionButton :href="route('crm.deals.index')">
        <ArrowLeft class="w-4 h-4 mr-2" />
        Back to Deals
      </ActionButton>
    </template>

    <Card>
      <template #content>
        <form @submit.prevent="submit">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div class="col-span-1 md:col-span-2">
              <Label for="name" :required="true">Name</Label>
              <InputText
                id="name"
                v-model="form.name"
                class="w-full"
                :class="{ 'p-invalid': form.errors.name }"
                required
                autofocus
              />
              <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
            </div>

            <!-- Description -->
            <div class="col-span-1 md:col-span-2">
              <Label for="description">Description</Label>
              <Textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="w-full"
                :class="{ 'p-invalid': form.errors.description }"
              />
              <small v-if="form.errors.description" class="p-error">{{ form.errors.description }}</small>
            </div>

            <!-- Amount -->
            <div>
              <Label for="amount">Amount</Label>
              <InputNumber
                id="amount"
                v-model="form.amount"
                mode="currency"
                :currency="form.currency"
                locale="en-US"
                :minFractionDigits="2"
                class="w-full"
                :class="{ 'p-invalid': form.errors.amount }"
              />
              <small v-if="form.errors.amount" class="p-error">{{ form.errors.amount }}</small>
            </div>

            <!-- Currency -->
            <div>
              <Label for="currency">Currency</Label>
              <Dropdown
                id="currency"
                v-model="form.currency"
                :options="currencyOptions"
                optionLabel="label"
                optionValue="value"
                class="w-full"
                :class="{ 'p-invalid': form.errors.currency }"
              />
              <small v-if="form.errors.currency" class="p-error">{{ form.errors.currency }}</small>
            </div>

            <!-- Stage -->
            <div>
              <Label for="stage" :required="true">Stage</Label>
              <Dropdown
                id="stage"
                v-model="form.stage"
                :options="stageOptions"
                optionLabel="label"
                optionValue="value"
                class="w-full"
                :class="{ 'p-invalid': form.errors.stage }"
                required
              />
              <small v-if="form.errors.stage" class="p-error">{{ form.errors.stage }}</small>
            </div>

            <!-- Status -->
            <div>
              <Label for="status" :required="true">Status</Label>
              <Dropdown
                id="status"
                v-model="form.status"
                :options="statusOptions"
                optionLabel="label"
                optionValue="value"
                class="w-full"
                :class="{ 'p-invalid': form.errors.status }"
                required
              />
              <small v-if="form.errors.status" class="p-error">{{ form.errors.status }}</small>
            </div>

            <!-- Probability -->
            <div>
              <Label for="probability">Probability (%)</Label>
              <InputNumber
                id="probability"
                v-model="form.probability"
                suffix="%"
                :min="0"
                :max="100"
                class="w-full"
                :class="{ 'p-invalid': form.errors.probability }"
              />
              <small v-if="form.errors.probability" class="p-error">{{ form.errors.probability }}</small>
            </div>

            <!-- Expected Close Date -->
            <div>
              <Label for="expected_close_date">Expected Close Date</Label>
              <Calendar
                id="expected_close_date"
                v-model="form.expected_close_date"
                dateFormat="yy-mm-dd"
                class="w-full"
                :class="{ 'p-invalid': form.errors.expected_close_date }"
                :showIcon="true"
              />
              <small v-if="form.errors.expected_close_date" class="p-error">{{ form.errors.expected_close_date }}</small>
            </div>

            <!-- Source -->
            <div>
              <Label for="source">Source</Label>
              <InputText
                id="source"
                v-model="form.source"
                class="w-full"
                :class="{ 'p-invalid': form.errors.source }"
              />
              <small v-if="form.errors.source" class="p-error">{{ form.errors.source }}</small>
            </div>

            <!-- Contact -->
            <div>
              <Label for="contact_id">Contact</Label>
              <Dropdown
                id="contact_id"
                v-model="form.contact_id"
                :options="contactOptions"
                optionLabel="label"
                optionValue="value"
                class="w-full"
                :class="{ 'p-invalid': form.errors.contact_id }"
                :filter="true"
              />
              <small v-if="form.errors.contact_id" class="p-error">{{ form.errors.contact_id }}</small>
            </div>

            <!-- Company -->
            <div>
              <Label for="company_id">Company</Label>
              <Dropdown
                id="company_id"
                v-model="form.company_id"
                :options="companyOptions"
                optionLabel="label"
                optionValue="value"
                class="w-full"
                :class="{ 'p-invalid': form.errors.company_id }"
                :filter="true"
              />
              <small v-if="form.errors.company_id" class="p-error">{{ form.errors.company_id }}</small>
            </div>

            <!-- Assigned To -->
            <div>
              <Label for="assigned_to">Assigned To</Label>
              <Dropdown
                id="assigned_to"
                v-model="form.assigned_to"
                :options="userOptions"
                optionLabel="label"
                optionValue="value"
                class="w-full"
                :class="{ 'p-invalid': form.errors.assigned_to }"
                :filter="true"
              />
              <small v-if="form.errors.assigned_to" class="p-error">{{ form.errors.assigned_to }}</small>
            </div>

            <!-- Products -->
            <div class="col-span-1 md:col-span-2">
              <Label for="products">Products</Label>
              <Chips
                id="products"
                v-model="form.products"
                class="w-full"
                :class="{ 'p-invalid': form.errors.products }"
              />
              <small v-if="form.errors.products" class="p-error">{{ form.errors.products }}</small>
            </div>

            <!-- Tags -->
            <div class="col-span-1 md:col-span-2">
              <Label for="tags">Tags</Label>
              <Chips
                id="tags"
                v-model="form.tags"
                class="w-full"
                :class="{ 'p-invalid': form.errors.tags }"
              />
              <small v-if="form.errors.tags" class="p-error">{{ form.errors.tags }}</small>
            </div>
          </div>

          <div class="flex items-center justify-end mt-6">
            <Button
              type="button"
              label="Cancel"
              severity="secondary"
              text
              @click="navigateToIndex"
              class="mr-2"
            />
            <Button
              type="submit"
              label="Create Deal"
              :loading="form.processing"
            />
          </div>
        </form>
      </template>
    </Card>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import ActionButton from '@/components/ui/action-button/ActionButton.vue';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import InputNumber from 'primevue/inputnumber';
import Calendar from 'primevue/calendar';
import Button from 'primevue/button';
import Chips from 'primevue/chips';
import { Label } from '@/components/ui/label';
import { route } from 'ziggy-js';
import type { BreadcrumbItem } from '@/types';

// Interfaces
interface Contact {
  id: number;
  name: string;
  email: string;
}

interface Company {
  id: number;
  name: string;
}

interface User {
  id: number;
  name: string;
  email: string;
}

interface DropdownOption {
  label: string;
  value: string | number;
}

interface Props {
  contacts: Contact[];
  companies: Company[];
  users: User[];
  stages: Record<string, string>;
  statuses: Record<string, string>;
}

// Props
const props = defineProps<Props>();

// Computed
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  {
    title: 'Dashboard',
    href: 'dashboard',
  },
  {
    title: 'Deals',
    href: 'crm.deals.index',
  },
  {
    title: 'Create Deal',
    href: '',
  }
]);

const currencyOptions = computed<DropdownOption[]>(() => [
  { label: 'USD - US Dollar', value: 'USD' },
  { label: 'EUR - Euro', value: 'EUR' },
  { label: 'GBP - British Pound', value: 'GBP' },
  { label: 'CAD - Canadian Dollar', value: 'CAD' },
  { label: 'AUD - Australian Dollar', value: 'AUD' },
  { label: 'JPY - Japanese Yen', value: 'JPY' },
]);

const stageOptions = computed<DropdownOption[]>(() => [
  { label: 'Prospecting', value: 'prospecting' },
  { label: 'Qualification', value: 'qualification' },
  { label: 'Proposal', value: 'proposal' },
  { label: 'Negotiation', value: 'negotiation' },
  { label: 'Closed Won', value: 'closed_won' },
  { label: 'Closed Lost', value: 'closed_lost' },
]);

const statusOptions = computed<DropdownOption[]>(() => [
  { label: 'Active', value: 'active' },
  { label: 'Inactive', value: 'inactive' },
  { label: 'Won', value: 'won' },
  { label: 'Lost', value: 'lost' },
]);

const contactOptions = computed<DropdownOption[]>(() => {
  const options: DropdownOption[] = [{ label: 'No Contact', value: '' }];
  
  props.contacts.forEach(contact => {
    options.push({ 
      label: `${contact.name} (${contact.email})`, 
      value: contact.id 
    });
  });
  
  return options;
});

const companyOptions = computed<DropdownOption[]>(() => {
  const options: DropdownOption[] = [{ label: 'No Company', value: '' }];
  
  props.companies.forEach(company => {
    options.push({ label: company.name, value: company.id });
  });
  
  return options;
});

const userOptions = computed<DropdownOption[]>(() => {
  const options: DropdownOption[] = [{ label: 'No Owner', value: '' }];
  
  props.users.forEach(user => {
    options.push({ label: user.name, value: user.id });
  });
  
  return options;
});

// Form
const form = useForm({
  name: '',
  description: '',
  amount: null as number | null,
  currency: 'USD',
  stage: 'prospecting',
  status: 'active',
  probability: null as number | null,
  expected_close_date: null as Date | Date | null,
  contact_id: '',
  company_id: '',
  assigned_to: '',
  source: '',
  products: [] as string[],
  tags: [] as string[],
});

// Methods
const submit = (): void => {
  form.post(route('crm.deals.store'), {
    onSuccess: () => {
      form.reset();
    },
  });
};

const navigateToIndex = (): void => {
  window.location.href = route('crm.deals.index');
};
</script>
