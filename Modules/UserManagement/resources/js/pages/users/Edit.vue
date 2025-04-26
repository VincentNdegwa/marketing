<script setup lang="ts">
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Card from 'primevue/card';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import InputSwitch from 'primevue/inputswitch';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Password from 'primevue/password';
import Textarea from 'primevue/textarea';
import { ref } from 'vue';
import { route } from 'ziggy-js';

interface Role {
  id: number;
  name: string;
  display_name: string;
}

interface User {
  id: number;
  name: string;
  email: string;
  type: string;
  mobile?: string;
  job_title?: string;
  bio?: string;
  address_line1?: string;
  address_line2?: string;
  city?: string;
  state?: string;
  postal_code?: string;
  country?: string;
  date_of_birth?: string;
  gender?: string;
  website?: string;
  social_links?: Record<string, any>;
  is_active: boolean;
  roles: Role[];
}

const props = defineProps<{
  user: User;
  roles: Role[];
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
        title: 'Edit User',
        href: 'usermanagement.edit',
    },
];

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    type: props.user.type,
    mobile: props.user.mobile || '',
    job_title: props.user.job_title || '',
    bio: props.user.bio || '',
    address_line1: props.user.address_line1 || '',
    address_line2: props.user.address_line2 || '',
    city: props.user.city || '',
    state: props.user.state || '',
    postal_code: props.user.postal_code || '',
    country: props.user.country || '',
    date_of_birth: props.user.date_of_birth ? new Date(props.user.date_of_birth) : null,
    gender: props.user.gender || null,
    website: props.user.website || '',
    social_links: props.user.social_links || {},
    roles: props.user.roles.map((r: Role) => r.id),
    is_active: props.user.is_active,
    business_id: props.current_business_id.toString(),
    _method: 'PUT'
});

const genderOptions = [
    { label: 'Male', value: 'male' },
    { label: 'Female', value: 'female' },
    { label: 'Other', value: 'other' },
];




const submitForm = () => {
    form.post(route('usermanagement.update', props.user.id), {
        onSuccess: () => {
            router.visit(route('usermanagement.index'));
        },
    });
};
const dialog = ref<{
  text: string;
  visible: boolean;
  header: string;
  action: ((payload: MouseEvent) => void) | undefined;
}>({
  text: "",
  visible: false,
  header: "",
  action: undefined
});

const confirmDelete = () => {
  dialog.value = {
    text: `Are you sure you want to delete this user? This action cannot be undone.`,
    visible: true,
    header: "Confirm Deletion",
    action: deleteUser
  };
};

const deleteUser = () => {
    router.delete(route('usermanagement.destroy', props.user.id), {
        onSuccess: () => {
            router.visit(route('usermanagement.index'));
        }
    });
};
</script>

<template>

    <Head title="Edit User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Edit User: {{ user.name }}</h1>
                <Button label="Delete User" icon="pi pi-trash" class="p-button-danger" @click="confirmDelete" />
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
                                    <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="email" :required="true">Email Address</Label>
                                    <InputText id="email" v-model="form.email" class="w-full"
                                        :class="{ 'p-invalid': form.errors.email }" placeholder="Enter email address"
                                        type="email" />
                                    <small v-if="form.errors.email" class="p-error">{{ form.errors.email }}</small>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="password">Password (leave blank to keep current)</Label>
                                    <Password id="password" v-model="form.password" class="w-full"
                                        :class="{ 'p-invalid': form.errors.password }" placeholder="Enter new password"
                                        toggleMask inputClass="!w-full !flex-1" :feedback="true" />
                                    <small v-if="form.errors.password" class="p-error">{{ form.errors.password
                                        }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="roles" :required="true">Roles</Label>
                                    <MultiSelect id="roles" v-model="form.roles" :options="roles"
                                        optionLabel="display_name" optionValue="id" placeholder="Select roles"
                                        class="w-full" :class="{ 'p-invalid': form.errors.roles }" display="chip" />
                                    <small v-if="form.errors.roles" class="p-error">{{ form.errors.roles }}</small>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <!-- User type is hidden -->
                                    <input type="hidden" id="type" v-model="form.type" />
                                </div>

                                <div class="space-y-2">
                                    <InputText id="business_id" :hidden="true" v-model="form.business_id" class="w-full"
                                        :class="{ 'p-invalid': form.errors.business_id }" disabled />
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
                                    <small v-if="form.errors.mobile" class="p-error">{{ form.errors.mobile }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="job_title">Job Title</Label>
                                    <InputText id="job_title" v-model="form.job_title" class="w-full"
                                        :class="{ 'p-invalid': form.errors.job_title }" placeholder="Enter job title" />
                                    <small v-if="form.errors.job_title" class="p-error">{{ form.errors.job_title
                                        }}</small>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="gender">Gender</Label>
                                    <Dropdown id="gender" v-model="form.gender" :options="genderOptions"
                                        optionLabel="label" optionValue="value" placeholder="Select gender"
                                        class="w-full" :class="{ 'p-invalid': form.errors.gender }" />
                                    <small v-if="form.errors.gender" class="p-error">{{ form.errors.gender }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="date_of_birth">Date of Birth</Label>
                                    <Calendar id="date_of_birth" v-model="form.date_of_birth" dateFormat="yy-mm-dd"
                                        class="w-full" :class="{ 'p-invalid': form.errors.date_of_birth }"
                                        placeholder="Select date of birth" />
                                    <small v-if="form.errors.date_of_birth" class="p-error">{{ form.errors.date_of_birth
                                        }}</small>
                                </div>
                            </div>

                            <div class="mt-4 space-y-2">
                                <Label for="bio">Bio</Label>
                                <Textarea id="bio" v-model="form.bio" rows="3" class="w-full"
                                    :class="{ 'p-invalid': form.errors.bio }"
                                    placeholder="Brief description about the user" />
                                <small v-if="form.errors.bio" class="p-error">{{ form.errors.bio }}</small>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="website">Website</Label>
                                    <InputText id="website" v-model="form.website" class="w-full"
                                        :class="{ 'p-invalid': form.errors.website }" placeholder="Enter website URL" />
                                    <small v-if="form.errors.website" class="p-error">{{ form.errors.website }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="country">Country</Label>
                                    <InputText id="country" v-model="form.country" class="w-full"
                                        :class="{ 'p-invalid': form.errors.country }" placeholder="Enter country" />
                                    <small v-if="form.errors.country" class="p-error">{{ form.errors.country }}</small>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="address_line1">Address Line 1</Label>
                                    <InputText id="address_line1" v-model="form.address_line1" class="w-full"
                                        :class="{ 'p-invalid': form.errors.address_line1 }"
                                        placeholder="Enter address line 1" />
                                    <small v-if="form.errors.address_line1" class="p-error">{{ form.errors.address_line1
                                        }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="address_line2">Address Line 2</Label>
                                    <InputText id="address_line2" v-model="form.address_line2" class="w-full"
                                        :class="{ 'p-invalid': form.errors.address_line2 }"
                                        placeholder="Enter address line 2" />
                                    <small v-if="form.errors.address_line2" class="p-error">{{ form.errors.address_line2
                                        }}</small>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-3">
                                <div class="space-y-2">
                                    <Label for="city">City</Label>
                                    <InputText id="city" v-model="form.city" class="w-full"
                                        :class="{ 'p-invalid': form.errors.city }" placeholder="Enter city" />
                                    <small v-if="form.errors.city" class="p-error">{{ form.errors.city }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="state">State/Province</Label>
                                    <InputText id="state" v-model="form.state" class="w-full"
                                        :class="{ 'p-invalid': form.errors.state }"
                                        placeholder="Enter state or province" />
                                    <small v-if="form.errors.state" class="p-error">{{ form.errors.state }}</small>
                                </div>

                                <div class="space-y-2">
                                    <Label for="postal_code">Postal Code</Label>
                                    <InputText id="postal_code" v-model="form.postal_code" class="w-full"
                                        :class="{ 'p-invalid': form.errors.postal_code }"
                                        placeholder="Enter postal code" />
                                    <small v-if="form.errors.postal_code" class="p-error">{{ form.errors.postal_code
                                        }}</small>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center space-x-2">
                                <InputSwitch v-model="form.is_active" />
                                <Label>Active Account</Label>
                                <small v-if="form.errors.is_active" class="p-error">{{ form.errors.is_active }}</small>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Button type="button" label="Cancel" severity="contrast" outlined
                                @click="router.visit(route('usermanagement.index'))" />
                            <Button type="submit" label="Update User" severity="contrast" icon="pi pi-check"
                                :loading="form.processing" />
                        </div>
                    </form>
                </template>
            </Card>
        </div>
    </AppLayout>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:visible="dialog.visible" modal :header="dialog.header" :style="{ width: '450px' }">
        <div class="confirmation-content">
            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
            <span>{{ dialog.text }}</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" class="p-button-text" @click="dialog.visible = false" />
            <Button label="Yes" icon="pi pi-check" class="p-button-danger" @click="deleteUser" />
        </template>
    </Dialog>
</template>
