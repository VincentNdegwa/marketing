<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import { route } from 'ziggy-js';
// import MultiSelect from 'primevue/multiselect';

interface Permission {
  id: number;
  name: string;
  display_name: string;
  description?: string;
  module?: string;
}

const props = defineProps<{
  permissions: Permission[];
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
    title: 'Roles',
    href: 'user-role.index',
  },
  {
    title: 'Create Role',
    href: 'user-role.create',
  },
];

const form = useForm({
  name: '',
  display_name: '',
  description: '',
  permissions: [],
  business_id: props.current_business_id,
});

const groupedPermissions = ref<Record<string, Record<string, Permission[]>>>({});

onMounted(() => {
  // Group permissions by module
  props.permissions.forEach((permission) => {
    const module = permission.module;
    const part = permission.name.split('.');
    const sub_module = part[0];

    if (module) {

      if (!groupedPermissions.value[module]) {
        groupedPermissions.value[module] = {};
      }
      if (!groupedPermissions.value[module][sub_module]) {
        groupedPermissions.value[module][sub_module] = [];
      }
      
      groupedPermissions.value[module][sub_module].push(permission);
      
    }
    
  });
});


const submitForm = () => {
  form.post(route('user-role.store'), {
    onSuccess: () => {
      // Redirect to roles list on success
      router.visit(route('user-role.index'));
    },
  });
};
</script>

<template>

  <Head title="Create Role" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Create New Role</h1>
      </div>

      <Card>
        <template #content>
          <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label for="name" class="block text-sm font-medium">System Name</label>
                <InputText id="name" v-model="form.name" class="w-full" :class="{ 'p-invalid': form.errors.name }"
                  placeholder="e.g. manager (lowercase, no spaces)" />
                <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
                <small class="text-gray-500">Unique identifier used by the system (lowercase, no spaces)</small>
              </div>

              <div class="space-y-2">
                <label for="display_name" class="block text-sm font-medium">Display Name</label>
                <InputText id="display_name" v-model="form.display_name" class="w-full"
                  :class="{ 'p-invalid': form.errors.display_name }" placeholder="e.g. Business Manager" />
                <small v-if="form.errors.display_name" class="p-error">{{ form.errors.display_name }}</small>
                <small class="text-gray-500">Human-readable name shown in the interface</small>
              </div>
            </div>

            <div class="space-y-2">
              <label for="description" class="block text-sm font-medium">Description</label>
              <Textarea id="description" v-model="form.description" rows="3" class="w-full"
                :class="{ 'p-invalid': form.errors.description }"
                placeholder="Describe the purpose and responsibilities of this role" />
              <small v-if="form.errors.description" class="p-error">{{ form.errors.description }}</small>
            </div>

            <div class="space-y-4">
              <label class="block text-sm font-medium">Permissions</label>

              <div v-for="(permissions, module) in groupedPermissions" :key="module" class="border rounded-lg p-4">
                <h3 class="text-lg font-semibold capitalize mb-3">{{ module }} Module</h3>

                <div v-for="(permission, sub_module) in permissions" :key="sub_module" class="border rounded-ms p-2">
                  <h4 class="text-md capitalize mb-2">{{ sub_module }}</h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div v-for="perm in permission" :key="perm.id" class="flex items-start">
                      <div class="flex items-center h-5">
                        <input :id="`permission-${perm.id}`" type="checkbox" :value="perm.id" v-model="form.permissions"
                          class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                      </div>
                      <div class="ml-3 text-sm">
                        <label :for="`permission-${perm.id}`" class="font-medium">{{
                          perm.display_name }}</label>
                        <p v-if="perm.description" class="text-gray-500">{{ perm.description }}</p>
                      </div>
                    </div>
                  </div>
                </div>

              </div>

              <small v-if="form.errors.permissions" class="p-error">{{ form.errors.permissions }}</small>
            </div>

            <div class="flex justify-end space-x-3">
              <Button type="button" label="Cancel" severity="contrast" class="p-button-outlined"
                @click="router.visit(route('user-role.index'))" />
              <Button type="submit" label="Create Role" severity="contrast" icon="pi pi-check"
                :loading="form.processing" />
            </div>
          </form>
        </template>
      </Card>
    </div>
  </AppLayout>
</template>
