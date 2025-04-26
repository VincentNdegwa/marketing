<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Business {
  id: number;
  name: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  state: string;
  country: string;
  zip_code: string;
  website: string;
  description: string;
  is_active: boolean;
  slug: string;
}

interface Props {
  business: Business;
}

const props = defineProps<Props>();

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
        title: props.business.name,
        href: route('business.show', { business: props.business.id }),
    },
];

// Format address parts that exist
const formatAddress = () => {
  const parts = [];
  if (props.business.address) parts.push(props.business.address);
  
  const cityState = [];
  if (props.business.city) cityState.push(props.business.city);
  if (props.business.state) cityState.push(props.business.state);
  if (cityState.length > 0) parts.push(cityState.join(', '));
  
  if (props.business.zip_code) parts.push(props.business.zip_code);
  if (props.business.country) parts.push(props.business.country);
  
  return parts.join(', ');
};
</script>

<template>
  <Head :title="business.name" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between items-center mb-6">
            <div>
              <h1 class="text-2xl font-bold">{{ business.name }}</h1>
              <p class="text-gray-500 mt-1">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                  :class="business.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                >
                  {{ business.is_active ? 'Active' : 'Inactive' }}
                </span>
              </p>
            </div>
            <div class="flex space-x-3">
              <Link 
                :href="route('business.switch', { business: business.id })"
                method="post"
                as="button"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
              >
                Switch to this Business
              </Link>
              <Link 
                :href="route('business.edit', { business: business.id })"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
              >
                Edit Business
              </Link>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Business Details -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
              <h2 class="text-lg font-medium text-gray-900 mb-4">Business Details</h2>
              
              <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Email</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <a :href="`mailto:${business.email}`" class="text-blue-600 hover:underline">
                      {{ business.email }}
                    </a>
                  </dd>
                </div>

                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Phone</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <a v-if="business.phone" :href="`tel:${business.phone}`" class="text-blue-600 hover:underline">
                      {{ business.phone }}
                    </a>
                    <span v-else class="text-gray-400">Not provided</span>
                  </dd>
                </div>

                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Website</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <a v-if="business.website" :href="business.website" target="_blank" class="text-blue-600 hover:underline">
                      {{ business.website }}
                    </a>
                    <span v-else class="text-gray-400">Not provided</span>
                  </dd>
                </div>

                <div class="sm:col-span-2">
                  <dt class="text-sm font-medium text-gray-500">Address</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <span v-if="formatAddress()">{{ formatAddress() }}</span>
                    <span v-else class="text-gray-400">Not provided</span>
                  </dd>
                </div>

                <div class="sm:col-span-2">
                  <dt class="text-sm font-medium text-gray-500">Description</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <p v-if="business.description">{{ business.description }}</p>
                    <span v-else class="text-gray-400">No description provided</span>
                  </dd>
                </div>
              </dl>
            </div>

            <!-- Business Stats -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
              <h2 class="text-lg font-medium text-gray-900 mb-4">Business Statistics</h2>
              
              <!-- Placeholder for future statistics -->
              <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                  <h3 class="text-sm font-medium text-gray-500">Users</h3>
                  <p class="mt-1 text-2xl font-semibold text-gray-900">0</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                  <h3 class="text-sm font-medium text-gray-500">Roles</h3>
                  <p class="mt-1 text-2xl font-semibold text-gray-900">0</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                  <h3 class="text-sm font-medium text-gray-500">Projects</h3>
                  <p class="mt-1 text-2xl font-semibold text-gray-900">0</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                  <h3 class="text-sm font-medium text-gray-500">Tasks</h3>
                  <p class="mt-1 text-2xl font-semibold text-gray-900">0</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
