<template>
    <Head title="Modules" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 bg-white dark:bg-[#0a0a0a]">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">System Modules</h1>
                <div class="flex space-x-2">
                    <Link 
                        :href="route('admin.modules')" 
                        class="px-3 py-2 bg-primary text-primary-foreground shadow-xs hover:bg-primary/90 flex items-center rounded-sm"
                        preserve-scroll
                    >
                        <RefreshCw class="h-4 w-4 mr-2" />
                        Refresh
                    </Link>
                </div>
            </div>
            
            <div v-if="modules.length === 0" class="bg-gray-50 p-8 rounded-lg text-center">
                <PackageX class="h-12 w-12 mx-auto text-gray-400 mb-4" />
                <p class="text-gray-500 dark:text-gray-400 text-lg">No modules found in the system.</p>
            </div>
            
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="module in modules" :key="module.name" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center">
                            <div v-if="module.icon" class="mr-2 w-6 h-6">
                                <img v-if="module.icon" :src="module.icon" alt="Module icon" class="w-full h-full object-contain" />
                                <Package v-else class="w-5 h-5 text-gray-400" />
                            </div>
                            <h3 class="text-lg font-semibold">{{ module.name }}</h3>
                        </div>
                        <div class="flex space-x-2">
                            <span 
                                v-if="module.is_common"
                                class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200"
                                title="Common module used by multiple features"
                            >
                                Common
                            </span>
                            <span 
                                :class="{
                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': module.enabled,
                                    'bg-gray-100 text-gray-800 dark:text-gray-300': !module.enabled
                                }"
                                class="px-2 py-1 text-xs rounded-full"
                            >
                                {{ module.enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                    
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ module.description || 'No description available' }}</p>
                    
                    <div class="flex flex-col space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Version:</span>
                            <span>{{ module.version || 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Path:</span>
                            <span class="text-gray-700 dark:text-gray-300 truncate max-w-[180px]" :title="module.path">{{ module.path }}</span>
                        </div>
                        <div v-if="module.depends_on && module.depends_on.length > 0" class="flex flex-col">
                            <span class="text-gray-500 dark:text-gray-400 mb-1">Dependencies:</span>
                            <div class="flex flex-wrap gap-1">
                                <span 
                                    v-for="dep in module.depends_on" 
                                    :key="dep"
                                    class="px-2 py-0.5 bg-blue-50 text-blue-700 dark:bg-blue-900 dark:text-blue-200 text-xs rounded"
                                >
                                    {{ dep }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-end space-x-2">
                        <Link 
                            v-if="module.enabled"
                            :href="route('admin.modules.disable', { name: module.alias })"
                            method="post"
                            as="button"
                            class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600 text-sm"
                            preserve-scroll
                        >
                            Disable
                        </Link>
                        <Link 
                            v-else
                            :href="route('admin.modules.enable', { name: module.alias })"
                            method="post"
                            as="button"
                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm"
                            preserve-scroll
                        >
                            Enable
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { RefreshCw, PackageX, Package } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import { route } from 'ziggy-js';
import {Head} from '@inertiajs/vue3';

interface Module {
    name: string;
    alias: string;
    description?: string;
    version?: string;
    enabled: boolean;
    path: string;
    is_common: number;
    icon?: string;
    depends_on: string[];
}

interface Props {
    modules: Module[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: 'dashboard.admin',
    },
    {
        title: 'Modules',
        href: 'admin.modules',
    },
];
</script>