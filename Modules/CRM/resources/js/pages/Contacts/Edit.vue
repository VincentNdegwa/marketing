<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <template #page-title>
            {{ pageTitle }}
        </template>

        <template #page-actions>
            <div class="flex gap-2">
                <ActionButton :href="route('crm.contacts.show', contact.id)" >
                Cancel
                </ActionButton>
            </div>
        </template>

        <div>
            <Card>
                <template #content>
                    <form @submit.prevent="submitForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- First Name -->
                            <div>
                                <Label for="first_name" :required="true">First Name</Label>
                                <InputText id="first_name" v-model="form.first_name" class="w-full" autofocus />
                                <small v-if="form.errors.first_name" class="p-error">{{ form.errors.first_name
                                    }}</small>
                            </div>

                            <!-- Last Name -->
                            <div>
                                <Label for="last_name" :required="true">Last Name</Label>
                                <InputText id="last_name" v-model="form.last_name" class="w-full" />
                                <small v-if="form.errors.last_name" class="p-error">{{ form.errors.last_name }}</small>
                            </div>

                            <!-- Email -->
                            <div>
                                <Label for="email">Email</Label>
                                <InputText id="email" v-model="form.email" type="email" class="w-full" />
                                <small v-if="form.errors.email" class="p-error">{{ form.errors.email }}</small>
                            </div>

                            <!-- Phone -->
                            <div>
                                <Label for="phone">Phone</Label>
                                <InputText id="phone" v-model="form.phone" class="w-full" />
                                <small v-if="form.errors.phone" class="p-error">{{ form.errors.phone }}</small>
                            </div>

                            <!-- Mobile -->
                            <div>
                                <Label for="mobile">Mobile</Label>
                                <InputText id="mobile" v-model="form.mobile" class="w-full" />
                                <small v-if="form.errors.mobile" class="p-error">{{ form.errors.mobile }}</small>
                            </div>

                            <!-- Job Title -->
                            <div>
                                <Label for="job_title">Job Title</Label>
                                <InputText id="job_title" v-model="form.job_title" class="w-full" />
                                <small v-if="form.errors.job_title" class="p-error">{{ form.errors.job_title }}</small>
                            </div>

                            <!-- Company -->
                            <div>
                                <Label for="company_id">Company</Label>
                                <Dropdown id="company_id" v-model="form.company_id" :options="companies"
                                    optionLabel="name" optionValue="id" placeholder="Select Company" class="w-full" />
                                <small v-if="form.errors.company_id" class="p-error">{{ form.errors.company_id
                                    }}</small>
                            </div>

                            <!-- Lead Source -->
                            <div>
                                <Label for="lead_source">Lead Source</Label>
                                <Dropdown id="lead_source" v-model="form.lead_source" :options="leadSourceOptions"
                                    optionLabel="name" optionValue="value" placeholder="Select Source" class="w-full" />
                                <small v-if="form.errors.lead_source" class="p-error">{{ form.errors.lead_source
                                    }}</small>
                            </div>

                            <!-- Lead Status -->
                            <div>
                                <Label for="lead_status">Lead Status</Label>
                                <Dropdown id="lead_status" v-model="form.lead_status" :options="leadStatusOptions"
                                    optionLabel="name" optionValue="value" placeholder="Select Status" class="w-full" />
                                <small v-if="form.errors.lead_status" class="p-error">{{ form.errors.lead_status
                                    }}</small>
                            </div>

                            <!-- Status -->
                            <div>
                                <Label for="status">Status</Label>
                                <Dropdown id="status" v-model="form.status" :options="statusOptions" optionLabel="name"
                                    optionValue="value" placeholder="Select Status" class="w-full" />
                                <small v-if="form.errors.status" class="p-error">{{ form.errors.status }}</small>
                            </div>

                            <!-- Assigned To -->
                            <div>
                                <Label for="assigned_to">Assigned To</Label>
                                <Dropdown id="assigned_to" v-model="form.assigned_to" :options="users"
                                    optionLabel="name" optionValue="id" placeholder="Select User" class="w-full" />
                                <small v-if="form.errors.assigned_to" class="p-error">{{ form.errors.assigned_to
                                    }}</small>
                            </div>

                            <!-- Website -->
                            <div>
                                <Label for="website">Website</Label>
                                <InputText id="website" v-model="form.website" class="w-full" />
                                <small v-if="form.errors.website" class="p-error">{{ form.errors.website }}</small>
                            </div>
                        </div>

                        <!-- Address Section -->
                        <div class="mt-6 mb-4">
                            <h3 class="text-lg font-medium mb-4">Contact Address</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Street Address -->
                                <div>
                                    <Label for="address">Street Address</Label>
                                    <InputText id="address" v-model="form.address" class="w-full" />
                                    <small v-if="form.errors.address" class="p-error">{{ form.errors.address }}</small>
                                </div>

                                <!-- City -->
                                <div>
                                    <Label for="city">City</Label>
                                    <InputText id="city" v-model="form.city" class="w-full" />
                                    <small v-if="form.errors.city" class="p-error">{{ form.errors.city }}</small>
                                </div>

                                <!-- State/Province -->
                                <div>
                                    <Label for="state">State/Province</Label>
                                    <InputText id="state" v-model="form.state" class="w-full" />
                                    <small v-if="form.errors.state" class="p-error">{{ form.errors.state }}</small>
                                </div>

                                <!-- ZIP/Postal Code -->
                                <div>
                                    <Label for="postal_code">ZIP/Postal Code</Label>
                                    <InputText id="postal_code" v-model="form.postal_code" class="w-full" />
                                    <small v-if="form.errors.postal_code" class="p-error">{{ form.errors.postal_code
                                        }}</small>
                                </div>

                                <!-- Country -->
                                <div>
                                    <Label for="country">Country</Label>
                                    <InputText id="country" v-model="form.country" class="w-full" />
                                    <small v-if="form.errors.country" class="p-error">{{ form.errors.country }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="mt-6 mb-4">
                            <h3 class="text-lg font-medium mb-4">Additional Information</h3>
                            <div class="grid gap-4">
                                <!-- Tags -->
                                <div>
                                    <Label for="tags">Tags</Label>
                                    <Chips id="tags" v-model="form.tags" class="w-full" />
                                    <small v-if="form.errors.tags" class="p-error">{{ form.errors.tags }}</small>
                                </div>

                                <!-- Notes -->
                                <div>
                                    <Label for="notes">Notes</Label>
                                    <Textarea id="notes" v-model="form.notes" class="w-full" rows="4" autoResize />
                                    <small v-if="form.errors.notes" class="p-error">{{ form.errors.notes }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-content-end mt-6">
                            <Button label="Cancel" type="button" class="p-button-secondary mr-2"
                                @click="$inertia.visit(route('crm.contacts.show', contact.id))" />
                            <Button label="Update Contact" type="submit" icon="pi pi-check"
                                :loading="form.processing" />
                        </div>
                    </form>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';
import Chips from 'primevue/chips';
import { route } from 'ziggy-js';
import { Label } from '@/components/ui/label';
import type { BreadcrumbItem } from '@/types';
import ActionButton from '@/components/ui/action-button/ActionButton.vue';

interface Company {
    id: number;
    name: string;
}

interface User {
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
    company_id: number | null;
    lead_source: string | null;
    lead_status: string | null;
    address: string | null;
    city: string | null;
    state: string | null;
    postal_code: string | null;
    country: string | null;
    website: string | null;
    notes: string | null;
    tags: string[] | null;
    assigned_to: number | null;
    status: string | null;
    created_at: string;
    updated_at: string;
    full_name: string;
}

interface Props {
    contact: Contact;
    companies: Company[];
    users?: User[];
    pageTitle: string;
}

const props = defineProps<Props>();

// Initialize users array if not provided
const users = props.users || [];

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Dashboard',
        href: 'dashboard',
    },
    {
        title: 'Contacts',
        href: 'crm.contacts.index',
    },
    props.contact && {
        title: props.contact?.full_name || 'View Contact',
        href: 'crm.contacts.show',
        params: { id: props.contact?.id || 0 }
    },
    {
        title: 'Edit Contacts',
        href: 'crm.contacts.edit',
    }
]);

const leadSourceOptions = [
    { name: 'Website', value: 'website' },
    { name: 'Phone Inquiry', value: 'phone' },
    { name: 'Referral', value: 'referral' },
    { name: 'Email', value: 'email' },
    { name: 'Social Media', value: 'social_media' },
    { name: 'Other', value: 'other' }
];

const leadStatusOptions = [
    { name: 'New', value: 'new' },
    { name: 'Contacted', value: 'contacted' },
    { name: 'Qualified', value: 'qualified' },
    { name: 'Unqualified', value: 'unqualified' },
    { name: 'Customer', value: 'customer' }
];

const statusOptions = [
    { name: 'Active', value: 'active' },
    { name: 'Inactive', value: 'inactive' }
];

// Set up form with existing contact data
const form = useForm({
    first_name: props.contact.first_name,
    last_name: props.contact.last_name,
    email: props.contact.email || '',
    phone: props.contact.phone || '',
    mobile: props.contact.mobile || '',
    company_id: props.contact.company_id,
    job_title: props.contact.job_title || '',
    lead_source: props.contact.lead_source || '',
    lead_status: props.contact.lead_status || '',
    address: props.contact.address || '',
    city: props.contact.city || '',
    state: props.contact.state || '',
    postal_code: props.contact.postal_code || '',
    country: props.contact.country || '',
    website: props.contact.website || '',
    notes: props.contact.notes || '',
    tags: props.contact.tags || [],
    assigned_to: props.contact.assigned_to,
    status: props.contact.status || 'active',
});

// Submit the form to update contact
const submitForm = () => {
    form.put(route('crm.contacts.update', props.contact.id));
};
</script>