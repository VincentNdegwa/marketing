<script setup lang="ts">
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Card from 'primevue/card';
import Dropdown from 'primevue/dropdown';
import InputSwitch from 'primevue/inputswitch';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Password from 'primevue/password';
import Textarea from 'primevue/textarea';
import { route } from 'ziggy-js';

const props = defineProps<{
    roles: any[];
    current_business_id: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: 'dashboard',
    },
    {
        title: 'User Management',
        href: 'usermanagement.index',
    },
    {
        title: 'Create User',
        href: 'usermanagement.create',
    },
];

const form = useForm({
    name: '',
    email: '',
    password: '',
    type: 'subuser', // Default to subuser
    mobile: '',
    job_title: '',
    bio: '',
    address_line1: '',
    address_line2: '',
    city: '',
    state: '',
    postal_code: '',
    country: '',
    date_of_birth: null,
    gender: null,
    website: '',
    social_links: {},
    roles: [],
    is_active: true,
    business_id: props.current_business_id.toString(),
});

const genderOptions = [
    { label: 'Male', value: 'male' },
    { label: 'Female', value: 'female' },
    { label: 'Other', value: 'other' },
];

const submitForm = () => {
    form.post(route('usermanagement.store'), {
        onSuccess: () => {
            // Redirect to users list on success
            router.visit(route('usermanagement.index'));
        },
    });
};
</script>

<template>

    <Head title="Create User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Create New User</h1>
            </div>

            <Card class="dark:bg-dark dark:text-white">
                <template #content>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- Required Fields Section -->
                        <div class="mb-6 rounded-lg p-4">
                            <h3 class="mb-4 text-lg font-medium">Required Information</h3>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="name" :required="true">Full Name</Label>
                                    <InputText id="name" v-model="form.name" class="w-full"
                                        :class="{ 'p-invalid': form.errors.name }" placeholder="Enter full name" />
                                    <small v-if="form.errors.name" class="text-red-900">{{ form.errors.name }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="email" :required="true">Email Address</Label>
                                    <InputText id="email" v-model="form.email" class="w-full"
                                        :class="{ 'p-invalid': form.errors.email }" placeholder="Enter email address"
                                        type="email" />
                                    <small v-if="form.errors.email" class="text-red-900">{{ form.errors.email }}</small>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="password" :required="true">Password</Label>
                                    <Password id="password" v-model="form.password" class="w-full"
                                        :class="{ 'p-invalid': form.errors.password }" placeholder="Enter password"
                                        toggleMask inputClass="!w-full !flex-1" :feedback="true" />
                                    <small v-if="form.errors.password" class="text-red-900">{{ form.errors.password
                                        }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="roles" :required="true">Roles</Label>
                                    <MultiSelect id="roles" v-model="form.roles" :options="roles"
                                        optionLabel="display_name" optionValue="id" placeholder="Select roles"
                                        class="w-full" :class="{ 'p-invalid': form.errors.roles }" display="chip" />
                                    <small v-if="form.errors.roles" class="text-red-900">{{ form.errors.roles }}</small>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <!-- User type is hidden and set to 'subuser' by default -->
                                    <input type="hidden" id="type" v-model="form.type" />
                                </div>

                                <div class="space-y-2">
                                    <InputText id="business_id" :hidden="true" v-model="form.business_id" class="w-full"
                                        :class="{ 'p-invalid': form.errors.business_id }" disabled />
                                    <!-- <small v-if="form.errors.business_id" class="text-red-900">{{ form.errors.business_id }}</small> -->
                                </div>
                            </div>
                        </div>

                        <!-- Optional Fields Section -->
                        <div class="rounded-lg p-4">
                            <h3 class="mb-4 text-lg font-medium">Additional Information</h3>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="mobile">Mobile Number</Label>
                                    <InputText id="mobile" v-model="form.mobile" class="w-full"
                                        :class="{ 'p-invalid': form.errors.mobile }"
                                        placeholder="Enter mobile number" />
                                    <small v-if="form.errors.mobile" class="text-red-900">{{ form.errors.mobile
                                        }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="job_title">Job Title</Label>
                                    <InputText id="job_title" v-model="form.job_title" class="w-full"
                                        :class="{ 'p-invalid': form.errors.job_title }" placeholder="Enter job title" />
                                    <small v-if="form.errors.job_title" class="text-red-900">{{ form.errors.job_title
                                        }}</small>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="gender">Gender</Label>
                                    <Dropdown id="gender" v-model="form.gender" :options="genderOptions"
                                        optionLabel="label" optionValue="value" placeholder="Select gender"
                                        class="w-full" :class="{ 'p-invalid': form.errors.gender }" />
                                    <small v-if="form.errors.gender" class="text-red-900">{{ form.errors.gender
                                        }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="date_of_birth">Date of Birth</Label>
                                    <Calendar id="date_of_birth" v-model="form.date_of_birth" dateFormat="yy-mm-dd"
                                        class="w-full" :class="{ 'p-invalid': form.errors.date_of_birth }"
                                        placeholder="Select date of birth" />
                                    <small v-if="form.errors.date_of_birth" class="text-red-900">{{
                                        form.errors.date_of_birth }}</small>
                                </div>
                            </div>

                            <div class="mt-4 space-y-2">
                                <Label for="bio">Bio</Label>
                                <Textarea id="bio" v-model="form.bio" rows="3" class="w-full"
                                    :class="{ 'p-invalid': form.errors.bio }"
                                    placeholder="Brief description about the user" />
                                <small v-if="form.errors.bio" class="text-red-900">{{ form.errors.bio }}</small>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="website">Website</Label>
                                    <InputText id="website" v-model="form.website" class="w-full"
                                        :class="{ 'p-invalid': form.errors.website }" placeholder="Enter website URL" />
                                    <small v-if="form.errors.website" class="text-red-900">{{ form.errors.website
                                        }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="country">Country</Label>
                                    <InputText id="country" v-model="form.country" class="w-full"
                                        :class="{ 'p-invalid': form.errors.country }" placeholder="Enter country" />
                                    <small v-if="form.errors.country" class="text-red-900">{{ form.errors.country
                                        }}</small>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="address_line1">Address Line 1</Label>
                                    <InputText id="address_line1" v-model="form.address_line1" class="w-full"
                                        :class="{ 'p-invalid': form.errors.address_line1 }"
                                        placeholder="Enter address line 1" />
                                    <small v-if="form.errors.address_line1" class="text-red-900">{{
                                        form.errors.address_line1 }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="address_line2">Address Line 2</Label>
                                    <InputText id="address_line2" v-model="form.address_line2" class="w-full"
                                        :class="{ 'p-invalid': form.errors.address_line2 }"
                                        placeholder="Enter address line 2" />
                                    <small v-if="form.errors.address_line2" class="text-red-900">{{
                                        form.errors.address_line2 }}</small>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-3">
                                <div class="space-y-2">
                                    <Label for="city">City</Label>
                                    <InputText id="city" v-model="form.city" class="w-full"
                                        :class="{ 'p-invalid': form.errors.city }" placeholder="Enter city" />
                                    <small v-if="form.errors.city" class="text-red-900">{{ form.errors.city }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="state">State/Province</Label>
                                    <InputText id="state" v-model="form.state" class="w-full"
                                        :class="{ 'p-invalid': form.errors.state }"
                                        placeholder="Enter state or province" />
                                    <small v-if="form.errors.state" class="text-red-900">{{ form.errors.state }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="postal_code">Postal Code</Label>
                                    <InputText id="postal_code" v-model="form.postal_code" class="w-full"
                                        :class="{ 'p-invalid': form.errors.postal_code }"
                                        placeholder="Enter postal code" />
                                    <small v-if="form.errors.postal_code" class="text-red-900">{{
                                        form.errors.postal_code }}</small>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center space-x-2">
                                <InputSwitch v-model="form.is_active" />
                                <Label>Active Account</Label>
                                <small v-if="form.errors.is_active" class="text-red-900">{{ form.errors.is_active
                                    }}</small>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Button type="button" label="Cancel" severity="contrast" outlined
                                @click="router.visit(route('usermanagement.index'))" />
                            <Button type="submit" label="Create User" severity="contrast" icon="pi pi-check"
                                :loading="form.processing" />
                        </div>
                    </form>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>
