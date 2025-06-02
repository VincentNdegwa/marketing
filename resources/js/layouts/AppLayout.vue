<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { useToast } from 'primevue/usetoast';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}
interface Flash {
    success: string|null,
    error: string|null,
    warning: string|null,
    info: string|null
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

let toast: any = null;
let toastAvailable = false;

try {
    toast = useToast();
    toastAvailable = true;
    console.log('Toast service initialized successfully');
} catch (error) {
    console.error('Toast service not available:', error);
    toastAvailable = false;
}

const page = usePage();

watch(
    () => page.props.flash as Flash,
    (flash: Flash) => {
        if (!flash) return;
        
        try {
            if (toastAvailable && toast) {
                if (flash.success) {
                    console.log('Showing success toast:', flash.success);
                    toast.add({ severity: 'success', summary: 'Success', detail: flash.success, life: 5000 });
                }
                
                if (flash.error) {
                    console.log('Showing error toast:', flash.error);
                    toast.add({ severity: 'error', summary: 'Error', detail: flash.error, life: 5000 });
                }
                
                if (flash.warning) {
                    console.log('Showing warning toast:', flash.warning);
                    toast.add({ severity: 'warn', summary: 'Warning', detail: flash.warning, life: 5000 });
                }
                
                if (flash.info) {
                    console.log('Showing info toast:', flash.info);
                    toast.add({ severity: 'info', summary: 'Information', detail: flash.info, life: 5000 });
                }
            } else {
                // Fallback to console logs if toast service isn't available
                if (flash.success) console.log('Success message (toast unavailable):', flash.success);
                if (flash.error) console.log('Error message (toast unavailable):', flash.error);
                if (flash.warning) console.log('Warning message (toast unavailable):', flash.warning);
                if (flash.info) console.log('Info message (toast unavailable):', flash.info);
            }
        } catch (error) {
            console.error('Error showing toast:', error);
        }
    },
    { deep: true, immediate: true }
);
</script>

<template>
    <div>
        <Toast :baseZIndex="9999" />

        <AppLayout :breadcrumbs="breadcrumbs">
            <template #page-title>
                <slot name="page-title"></slot>
            </template>

            <template #page-actions>
                <slot name="page-actions"></slot>
            </template>

            <div class=" p-4">
                <slot />
            </div>
        </AppLayout>
    </div>
</template>

<style>

</style>
