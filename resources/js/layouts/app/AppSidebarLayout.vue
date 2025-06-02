<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <div class="p-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">
                        <slot name="page-title"></slot>
                    </h1>

                    <div class=" flex justify-end items-center gap-1" >
                        <slot name="page-actions"></slot>
                    </div>
                </div>
            </div>
            <slot />
        </AppContent>
    </AppShell>
</template>
