<template>
    <AppLayout :title="pageTitle" :breadcrumbs="breadcrumbs">
        <template #page-title>
            {{ pageTitle }}
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
                                <Dropdown id="company_id" v-model="form.company_id" :options="companies" :disabled="company_id != null"
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
                        <div class="flex justify-end mt-6">
                            <Button label="Cancel" type="button" class="p-button-secondary mr-2"
                                @click="$inertia.visit(route('crm.contacts.index'))" />
                            <Button label="Create Contact" type="submit" icon="pi pi-check"
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
import { ref, watch } from 'vue';
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

interface Company {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
}

interface Props {
    companies: Company[];
    users: User[];
    pageTitle: string;
    company_id: number | null;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: 'dashboard',
    },
    {
        title: 'Contacts',
        href: 'crm.contacts.index',
    },
    {
        title: 'Create Contact',
        href: 'crm.contacts.create',
    },
];


const leadSourceOptions = ref([
    { name: 'Website', value: 'website' },
    { name: 'Phone Inquiry', value: 'phone' },
    { name: 'Referral', value: 'referral' },
    { name: 'Email', value: 'email' },
    { name: 'Social Media', value: 'social_media' },
    { name: 'Other', value: 'other' }
]);

const leadStatusOptions = ref([
    { name: 'New', value: 'new' },
    { name: 'Contacted', value: 'contacted' },
    { name: 'Qualified', value: 'qualified' },
    { name: 'Unqualified', value: 'unqualified' },
    { name: 'Customer', value: 'customer' }
]);

const statusOptions = ref([
    { name: 'Active', value: 'active' },
    { name: 'Inactive', value: 'inactive' }
]);

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    mobile: '',
    company_id: null as number | null,
    job_title: '',
    lead_source: '',
    lead_status: '',
    address: '',
    city: '',
    state: '',
    postal_code: '',
    country: '',
    website: '',
    notes: '',
    tags: [] as string[],
    assigned_to: null as number | null,
    status: 'active',
});


watch(
    () => props.company_id,
    (newCompanyId, oldCompanyId) => {
        console.log('Company ID changed:', oldCompanyId, '->', newCompanyId, typeof newCompanyId);
        if (newCompanyId) {
            form.company_id = Number(newCompanyId);
            console.log('Form company_id set to:', form.company_id);
        }
    },
    { immediate: true }
);
// Submit the form
const submitForm = () => {
    form.post(route('crm.contacts.store'));
};
</script>