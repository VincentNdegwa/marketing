<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import TabMenu from 'primevue/tabmenu';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import InputSwitch from 'primevue/inputswitch';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { TabMenuChangeEvent } from 'primevue/tabmenu';

const toast = useToast();
const activeTab = ref(0);

// Define props for the settings
defineProps({
  settings: {
    type: Object,
    default: () => ({})
  },
  groups: {
    type: Array,
    default: () => []
  }
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: 'dashboard',
  },
  {
    title: 'CMS',
    href: 'cms.index',
  },
  {
    title: 'Settings',
    href: 'cms.settings.index',
  },
];

// Tab menu items
const tabItems = computed(() => {
  return [
    { label: 'General', icon: 'pi pi-cog' },
    { label: 'SEO', icon: 'pi pi-search' },
    { label: 'Social Media', icon: 'pi pi-share-alt' },
    { label: 'Analytics', icon: 'pi pi-chart-line' },
    { label: 'Advanced', icon: 'pi pi-sliders-h' }
  ];
});

// Form for settings
const form = useForm({
  // General Settings
  site_name: '',
  site_description: '',
  site_logo: '',
  site_favicon: '',
  site_email: '',
  site_phone: '',
  site_address: '',
  
  // SEO Settings
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  google_site_verification: '',
  bing_site_verification: '',
  robots_txt: '',
  
  // Social Media Settings
  facebook_url: '',
  twitter_url: '',
  instagram_url: '',
  linkedin_url: '',
  youtube_url: '',
  
  // Analytics Settings
  google_analytics_id: '',
  facebook_pixel_id: '',
  
  // Advanced Settings
  custom_header_scripts: '',
  custom_footer_scripts: '',
  maintenance_mode: false,
  cache_enabled: true
});

// Load settings into form
// const loadSettings = () => {
//   if (props.settings) {
//     Object.keys(form).forEach(key => {
//       if (props.settings[key] !== undefined) {
//         form[key] = props.settings[key];
//       }
//     });
//   }
// };

// Save settings
const saveSettings = () => {
  form.post(route('cms.settings.update'), {
    onSuccess: () => {
      toast.add({
        severity: 'success',
        summary: 'Success',
        detail: 'Settings updated successfully',
        life: 3000
      });
    },
    onError: () => {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: 'Failed to update settings',
        life: 3000
      });
    }
  });
};

// Watch for active tab changes to load group-specific settings
const onTabChange = (event: TabMenuChangeEvent) => {
  activeTab.value = event.index;
  // If needed, you can load specific group settings here
  // router.visit(route('cms.settings.group', tabItems.value[activeTab.value].label.toLowerCase()));
};
</script>

<template>
  <Head title="CMS Settings" />
  
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Settings</h1>
        <Button 
          label="Save Changes" 
          icon="pi pi-save" 
          severity="primary" 
          @click="saveSettings" 
          :loading="form.processing" 
        />
      </div>

      <TabMenu :model="tabItems" :activeIndex="activeTab" @tab-change="onTabChange" />

      <Card class="mt-4">
        <template #content>
          <!-- General Settings -->
          <div v-if="activeTab === 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="field">
              <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
              <InputText id="site_name" v-model="form.site_name" class="w-full" />
              <small class="text-red-500" v-if="form.errors.site_name">{{ form.errors.site_name }}</small>
            </div>
            
            <div class="field">
              <label for="site_email" class="block text-sm font-medium text-gray-700 mb-1">Site Email</label>
              <InputText id="site_email" v-model="form.site_email" class="w-full" />
              <small class="text-red-500" v-if="form.errors.site_email">{{ form.errors.site_email }}</small>
            </div>
            
            <div class="field md:col-span-2">
              <label for="site_description" class="block text-sm font-medium text-gray-700 mb-1">Site Description</label>
              <Textarea id="site_description" v-model="form.site_description" rows="3" class="w-full" />
              <small class="text-red-500" v-if="form.errors.site_description">{{ form.errors.site_description }}</small>
            </div>
            
            <div class="field">
              <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-1">Site Logo URL</label>
              <InputText id="site_logo" v-model="form.site_logo" class="w-full" />
              <small class="text-red-500" v-if="form.errors.site_logo">{{ form.errors.site_logo }}</small>
            </div>
            
            <div class="field">
              <label for="site_favicon" class="block text-sm font-medium text-gray-700 mb-1">Site Favicon URL</label>
              <InputText id="site_favicon" v-model="form.site_favicon" class="w-full" />
              <small class="text-red-500" v-if="form.errors.site_favicon">{{ form.errors.site_favicon }}</small>
            </div>
            
            <div class="field">
              <label for="site_phone" class="block text-sm font-medium text-gray-700 mb-1">Site Phone</label>
              <InputText id="site_phone" v-model="form.site_phone" class="w-full" />
              <small class="text-red-500" v-if="form.errors.site_phone">{{ form.errors.site_phone }}</small>
            </div>
            
            <div class="field md:col-span-2">
              <label for="site_address" class="block text-sm font-medium text-gray-700 mb-1">Site Address</label>
              <Textarea id="site_address" v-model="form.site_address" rows="2" class="w-full" />
              <small class="text-red-500" v-if="form.errors.site_address">{{ form.errors.site_address }}</small>
            </div>
          </div>
          
          <!-- SEO Settings -->
          <div v-if="activeTab === 1" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="field">
              <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Default Meta Title</label>
              <InputText id="meta_title" v-model="form.meta_title" class="w-full" />
              <small class="text-red-500" v-if="form.errors.meta_title">{{ form.errors.meta_title }}</small>
            </div>
            
            <div class="field md:col-span-2">
              <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Default Meta Description</label>
              <Textarea id="meta_description" v-model="form.meta_description" rows="3" class="w-full" />
              <small class="text-red-500" v-if="form.errors.meta_description">{{ form.errors.meta_description }}</small>
            </div>
            
            <div class="field md:col-span-2">
              <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">Default Meta Keywords</label>
              <InputText id="meta_keywords" v-model="form.meta_keywords" class="w-full" />
              <small class="text-gray-500">Separate keywords with commas</small>
              <small class="text-red-500" v-if="form.errors.meta_keywords">{{ form.errors.meta_keywords }}</small>
            </div>
            
            <div class="field">
              <label for="google_site_verification" class="block text-sm font-medium text-gray-700 mb-1">Google Site Verification</label>
              <InputText id="google_site_verification" v-model="form.google_site_verification" class="w-full" />
              <small class="text-red-500" v-if="form.errors.google_site_verification">{{ form.errors.google_site_verification }}</small>
            </div>
            
            <div class="field">
              <label for="bing_site_verification" class="block text-sm font-medium text-gray-700 mb-1">Bing Site Verification</label>
              <InputText id="bing_site_verification" v-model="form.bing_site_verification" class="w-full" />
              <small class="text-red-500" v-if="form.errors.bing_site_verification">{{ form.errors.bing_site_verification }}</small>
            </div>
            
            <div class="field md:col-span-2">
              <label for="robots_txt" class="block text-sm font-medium text-gray-700 mb-1">Robots.txt Content</label>
              <Textarea id="robots_txt" v-model="form.robots_txt" rows="5" class="w-full font-mono text-sm" />
              <small class="text-red-500" v-if="form.errors.robots_txt">{{ form.errors.robots_txt }}</small>
            </div>
          </div>
          
          <!-- Social Media Settings -->
          <div v-if="activeTab === 2" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="field">
              <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-1">Facebook URL</label>
              <InputText id="facebook_url" v-model="form.facebook_url" class="w-full" />
              <small class="text-red-500" v-if="form.errors.facebook_url">{{ form.errors.facebook_url }}</small>
            </div>
            
            <div class="field">
              <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-1">Twitter URL</label>
              <InputText id="twitter_url" v-model="form.twitter_url" class="w-full" />
              <small class="text-red-500" v-if="form.errors.twitter_url">{{ form.errors.twitter_url }}</small>
            </div>
            
            <div class="field">
              <label for="instagram_url" class="block text-sm font-medium text-gray-700 mb-1">Instagram URL</label>
              <InputText id="instagram_url" v-model="form.instagram_url" class="w-full" />
              <small class="text-red-500" v-if="form.errors.instagram_url">{{ form.errors.instagram_url }}</small>
            </div>
            
            <div class="field">
              <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-1">LinkedIn URL</label>
              <InputText id="linkedin_url" v-model="form.linkedin_url" class="w-full" />
              <small class="text-red-500" v-if="form.errors.linkedin_url">{{ form.errors.linkedin_url }}</small>
            </div>
            
            <div class="field">
              <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-1">YouTube URL</label>
              <InputText id="youtube_url" v-model="form.youtube_url" class="w-full" />
              <small class="text-red-500" v-if="form.errors.youtube_url">{{ form.errors.youtube_url }}</small>
            </div>
          </div>
          
          <!-- Analytics Settings -->
          <div v-if="activeTab === 3" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="field">
              <label for="google_analytics_id" class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID</label>
              <InputText id="google_analytics_id" v-model="form.google_analytics_id" class="w-full" placeholder="UA-XXXXXXXXX-X or G-XXXXXXXXXX" />
              <small class="text-red-500" v-if="form.errors.google_analytics_id">{{ form.errors.google_analytics_id }}</small>
            </div>
            
            <div class="field">
              <label for="facebook_pixel_id" class="block text-sm font-medium text-gray-700 mb-1">Facebook Pixel ID</label>
              <InputText id="facebook_pixel_id" v-model="form.facebook_pixel_id" class="w-full" />
              <small class="text-red-500" v-if="form.errors.facebook_pixel_id">{{ form.errors.facebook_pixel_id }}</small>
            </div>
          </div>
          
          <!-- Advanced Settings -->
          <div v-if="activeTab === 4" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="field md:col-span-2">
              <label for="custom_header_scripts" class="block text-sm font-medium text-gray-700 mb-1">Custom Header Scripts</label>
              <Textarea id="custom_header_scripts" v-model="form.custom_header_scripts" rows="5" class="w-full font-mono text-sm" />
              <small class="text-gray-500">These scripts will be added to the <head> section of all pages. </head> </small>
              <small class="text-red-500" v-if="form.errors.custom_header_scripts">{{ form.errors.custom_header_scripts }}</small>
            </div>
            
            <div class="field md:col-span-2">
              <label for="custom_footer_scripts" class="block text-sm font-medium text-gray-700 mb-1">Custom Footer Scripts</label>
              <Textarea id="custom_footer_scripts" v-model="form.custom_footer_scripts" rows="5" class="w-full font-mono text-sm" />
                <small class="text-gray-500"> <body> These scripts will be added just before the closing </body> tag of all pages. </small>
              <small class="text-red-500" v-if="form.errors.custom_footer_scripts">{{ form.errors.custom_footer_scripts }}</small>
            </div>
            
            <div class="field flex items-center gap-2">
              <InputSwitch v-model="form.maintenance_mode" />
              <label for="maintenance_mode" class="text-sm font-medium text-gray-700">Maintenance Mode</label>
              <small class="text-red-500" v-if="form.errors.maintenance_mode">{{ form.errors.maintenance_mode }}</small>
            </div>
            
            <div class="field flex items-center gap-2">
              <InputSwitch v-model="form.cache_enabled" />
              <label for="cache_enabled" class="text-sm font-medium text-gray-700">Enable Page Cache</label>
              <small class="text-red-500" v-if="form.errors.cache_enabled">{{ form.errors.cache_enabled }}</small>
            </div>
          </div>
        </template>
      </Card>
    </div>
  </AppLayout>
</template>
