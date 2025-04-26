<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import MultiSelect from 'primevue/multiselect';
import Dropdown from 'primevue/dropdown';
import InputSwitch from 'primevue/inputswitch';
import Dialog from 'primevue/dialog';

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
  job_title?: string;
  bio?: string;
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
  password_confirmation: '',
  type: props.user.type,
  job_title: props.user.job_title,
  bio: props.user.bio,
  roles: props.user.roles.map((r: Role) => r.id),
  is_active: props.user.is_active,
  business_id: props.current_business_id,
  _method: 'PUT'
});

const userTypes = [
  { label: 'Client', value: 'client' },
  { label: 'Staff', value: 'staff' },
  { label: 'Partner', value: 'partner' },
];

const deleteDialogVisible = ref(false);

// Get the route function from the page props
const page = usePage();
const route = (name: string, params?: any) => page.props.route(name, params);

const submitForm = () => {
  form.post(route('usermanagement.update', props.user.id), {
    onSuccess: () => {
      // Redirect to users list on success
      router.visit(route('usermanagement.index'));
    },
  });
};

const confirmDelete = () => {
  deleteDialogVisible.value = true;
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
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Edit User: {{ user.name }}</h1>
        <Button label="Delete User" icon="pi pi-trash" class="p-button-danger" @click="confirmDelete" />
      </div>

      <Card>
        <template #content>
          <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <InputText 
                  id="name" 
                  v-model="form.name" 
                  class="w-full" 
                  :class="{ 'p-invalid': form.errors.name }"
                  placeholder="Enter full name"
                />
                <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
              </div>

              <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <InputText 
                  id="email" 
                  v-model="form.email" 
                  class="w-full" 
                  :class="{ 'p-invalid': form.errors.email }"
                  placeholder="Enter email address"
                  type="email"
                />
                <small v-if="form.errors.email" class="p-error">{{ form.errors.email }}</small>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-gray-700">Password (leave blank to keep current)</label>
                <Password 
                  id="password" 
                  v-model="form.password" 
                  class="w-full" 
                  :class="{ 'p-invalid': form.errors.password }"
                  placeholder="Enter new password"
                  toggleMask
                  :feedback="true"
                />
                <small v-if="form.errors.password" class="p-error">{{ form.errors.password }}</small>
              </div>

              <div class="space-y-2">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <Password 
                  id="password_confirmation" 
                  v-model="form.password_confirmation" 
                  class="w-full" 
                  :class="{ 'p-invalid': form.errors.password_confirmation }"
                  placeholder="Confirm new password"
                  toggleMask
                />
                <small v-if="form.errors.password_confirmation" class="p-error">{{ form.errors.password_confirmation }}</small>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label for="type" class="block text-sm font-medium text-gray-700">User Type</label>
                <Dropdown
                  id="type"
                  v-model="form.type"
                  :options="userTypes"
                  optionLabel="label"
                  optionValue="value"
                  placeholder="Select user type"
                  class="w-full"
                  :class="{ 'p-invalid': form.errors.type }"
                />
                <small v-if="form.errors.type" class="p-error">{{ form.errors.type }}</small>
              </div>

              <div class="space-y-2">
                <label for="job_title" class="block text-sm font-medium text-gray-700">Job Title</label>
                <InputText 
                  id="job_title" 
                  v-model="form.job_title" 
                  class="w-full" 
                  :class="{ 'p-invalid': form.errors.job_title }"
                  placeholder="Enter job title"
                />
                <small v-if="form.errors.job_title" class="p-error">{{ form.errors.job_title }}</small>
              </div>
            </div>

            <div class="space-y-2">
              <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
              <Textarea 
                id="bio" 
                v-model="form.bio" 
                rows="3" 
                class="w-full" 
                :class="{ 'p-invalid': form.errors.bio }"
                placeholder="Brief description about the user"
              />
              <small v-if="form.errors.bio" class="p-error">{{ form.errors.bio }}</small>
            </div>

            <div class="space-y-2">
              <label for="roles" class="block text-sm font-medium text-gray-700">Roles</label>
              <MultiSelect
                id="roles"
                v-model="form.roles"
                :options="roles"
                optionLabel="display_name"
                optionValue="id"
                placeholder="Select roles"
                class="w-full"
                :class="{ 'p-invalid': form.errors.roles }"
                display="chip"
              />
              <small v-if="form.errors.roles" class="p-error">{{ form.errors.roles }}</small>
            </div>

            <div class="flex items-center space-x-2">
              <InputSwitch v-model="form.is_active" />
              <label class="text-sm font-medium text-gray-700">Active Account</label>
              <small v-if="form.errors.is_active" class="p-error">{{ form.errors.is_active }}</small>
            </div>

            <div class="flex justify-end space-x-3">
              <Button 
                type="button" 
                label="Cancel" 
                class="p-button-outlined" 
                @click="router.visit(route('usermanagement.index'))" 
              />
              <Button 
                type="submit" 
                label="Update User" 
                icon="pi pi-check" 
                :loading="form.processing" 
              />
            </div>
          </form>
        </template>
      </Card>
    </div>
  </AppLayout>

  <!-- Delete Confirmation Dialog -->
  <Dialog v-model:visible="deleteDialogVisible" modal header="Confirm Deletion" :style="{ width: '450px' }">
    <div class="confirmation-content">
      <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
      <span>Are you sure you want to delete this user? This action cannot be undone.</span>
    </div>
    <template #footer>
      <Button label="No" icon="pi pi-times" class="p-button-text" @click="deleteDialogVisible = false" />
      <Button label="Yes" icon="pi pi-check" class="p-button-danger" @click="deleteUser" />
    </template>
  </Dialog>
</template>
