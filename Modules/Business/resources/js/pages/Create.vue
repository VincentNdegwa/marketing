<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { route } from 'ziggy-js';
import Button from 'primevue/button';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: 'dashboard',
    },
    {
        title: 'Businesses',
        href: 'business.index',
    },
    {
        title: 'Create Business',
        href: 'business.create',
    },
];

const form = useForm({
    name: '',
    email: '',
    description: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    country: '',
    zip_code: '',
    website: '',
});

const submit = () => {
    form.post(route('business.store'));
};
</script>

<template>

  <Head title="Create Business" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Create New Business</h1>
      </div>

      <Card class="dark:bg-dark dark:text-white">
        <template #content>

          <form @submit.prevent="submit" class="space-y-6">
            <!-- Required Fields Section -->
            <div class="dark:border-0 p-4 rounded-lg border border-gray-200">
              <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Required Information</h2>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Business Name -->
                <div class="field">
                  <label for="name" class="font-medium block mb-2 dark:text-gray-200">Business Name <span
                      class="text-red-500">*</span></label>
                  <InputText id="name" v-model="form.name" class="w-full" :class="{ 'p-invalid': form.errors.name }"
                    required autofocus />
                  <small v-if="form.errors.name" class="p-error block mt-1">{{ form.errors.name }}</small>
                </div>

                <!-- Business Email -->
                <div class="field">
                  <label for="email" class="font-medium block mb-2 dark:text-gray-200">Business Email <span
                      class="text-red-500">*</span></label>
                  <InputText id="email" v-model="form.email" type="email" class="w-full"
                    :class="{ 'p-invalid': form.errors.email }" required />
                  <small v-if="form.errors.email" class="p-error block mt-1">{{ form.errors.email }}</small>
                </div>
              </div>
            </div>

            <!-- Optional Fields Section -->
            <div class="dark:border-0 p-4 rounded-lg border border-gray-200">
              <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Additional Information</h2>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Phone -->
                <div class="field">
                  <label for="phone" class="font-medium block mb-2 dark:text-gray-200">Phone Number</label>
                  <InputText id="phone" v-model="form.phone" class="w-full"
                    :class="{ 'p-invalid': form.errors.phone }" />
                  <small v-if="form.errors.phone" class="p-error block mt-1">{{ form.errors.phone }}</small>
                </div>

                <!-- Website -->
                <div class="field">
                  <label for="website" class="font-medium block mb-2 dark:text-gray-200">Website</label>
                  <InputText id="website" v-model="form.website" class="w-full"
                    :class="{ 'p-invalid': form.errors.website }" placeholder="https://example.com" />
                  <small v-if="form.errors.website" class="p-error block mt-1">{{ form.errors.website }}</small>
                </div>

                <!-- Address -->
                <div class="field md:col-span-2">
                  <label for="address" class="font-medium block mb-2 dark:text-gray-200">Address</label>
                  <InputText id="address" v-model="form.address" class="w-full"
                    :class="{ 'p-invalid': form.errors.address }" />
                  <small v-if="form.errors.address" class="p-error block mt-1">{{ form.errors.address }}</small>
                </div>

                <!-- City, State, Zip in a row -->
                <div class="field">
                  <label for="city" class="font-medium block mb-2 dark:text-gray-200">City</label>
                  <InputText id="city" v-model="form.city" class="w-full" :class="{ 'p-invalid': form.errors.city }" />
                  <small v-if="form.errors.city" class="p-error block mt-1">{{ form.errors.city }}</small>
                </div>

                <div class="field">
                  <label for="state" class="font-medium block mb-2 dark:text-gray-200">State/Province</label>
                  <InputText id="state" v-model="form.state" class="w-full"
                    :class="{ 'p-invalid': form.errors.state }" />
                  <small v-if="form.errors.state" class="p-error block mt-1">{{ form.errors.state }}</small>
                </div>

                <div class="field">
                  <label for="country" class="font-medium block mb-2 dark:text-gray-200">Country</label>
                  <InputText id="country" v-model="form.country" class="w-full"
                    :class="{ 'p-invalid': form.errors.country }" />
                  <small v-if="form.errors.country" class="p-error block mt-1">{{ form.errors.country }}</small>
                </div>

                <div class="field">
                  <label for="zip_code" class="font-medium block mb-2 dark:text-gray-200">Postal/ZIP Code</label>
                  <InputText id="zip_code" v-model="form.zip_code" class="w-full"
                    :class="{ 'p-invalid': form.errors.zip_code }" />
                  <small v-if="form.errors.zip_code" class="p-error block mt-1">{{ form.errors.zip_code }}</small>
                </div>

                <!-- Description -->
                <div class="field md:col-span-2">
                  <label for="description" class="font-medium block mb-2 dark:text-gray-200">Description</label>
                  <Textarea id="description" v-model="form.description" class="w-full"
                    :class="{ 'p-invalid': form.errors.description }" rows="4" autoResize />
                  <small v-if="form.errors.description" class="p-error block mt-1">{{ form.errors.description }}</small>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3">
              <Button type="button" label="Cancel" severity="secondary" outlined
                @click="$inertia.visit(route('business.index'))" />
              <Button type="submit" label="Create Business" :loading="form.processing" />
            </div>
          </form>
        </template>
      </Card>
    </div>
  </AppLayout>
</template>
