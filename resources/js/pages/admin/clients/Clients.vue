<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
// import { Input } from '@/components/ui/input';
import { InputPassword } from '@/components/ui/input-password';
import {InputText} from '@/components/ui/input-text';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

import { ref, defineAsyncComponent } from 'vue';

import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Menu from 'primevue/menu';
import Tag from 'primevue/tag';
import DynamicDialog from 'primevue/dynamicdialog';
import { useDialog } from 'primevue/usedialog';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';


defineProps<{
    users: {
        current_page: number;
        data: User[];
        first_page_url: string;
        from: number;
        last_page: number;
        last_page_url: string;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        next_page_url: string | null;
        path: string;
        per_page: number;
        prev_page_url: string | null;
        to: number;
        total: number;
    };
}>();

const BusinessDetailsComponent = defineAsyncComponent(()=>import('./BusinessDetails.vue'))
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: 'dashboard.admin',
    },
    {
        title: 'Admin Users',
        href: 'clients.index',
    },
];

const showDialog = ref(false);
const editMode = ref(false);
const menu = ref();
const currentData = ref<User | null>(null);
const dialog = useDialog();
const toast = useToast();

const form = useForm({
    id: '',
    name: '',
    email: '',
    password: '',
});

function openCreateDialog() {
    editMode.value = false;
    form.reset();
    showDialog.value = true;
}
const menu_item = [
    {
        label: 'View',
        icon: 'pi pi-eye',
        command: () => showBusinessDetails(),
    },
    {
        label: 'Edit',
        icon: 'pi pi-pencil',
        command: () => openEditDialog(currentData.value),
    },
    {
        label: 'Delete',
        icon: 'pi pi-trash',
        class: 'text-red-500',
        command: () => deleteUser(currentData.value?.id),
    },
];

function openEditDialog(user: any) {
    editMode.value = true;
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.password = ''; // Clear password field for editing
    showDialog.value = true;
}

function handleSubmit() {
    if (editMode.value) {
        form.put(route('clients.update', form.id), {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route('clients.store'), {
            onSuccess: () => {
                showDialog.value = false;
                form.reset();
            },
        });
    }
}

function deleteUser(id: number | undefined) {
    if (id) {
        
        if (confirm('Are you sure you want to delete this admin user?')) {
            useForm({}).delete(route('clients.destroy', id));
        }
    }
}

const toggle = (event: Event, data: User) => {
    currentData.value = data;
    menu.value.toggle(event);
    console.log(data);
};

const showBusinessDetails = () => {
    // Make sure we have data to display
    if (!currentData.value || !currentData.value.businesses) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No business data available', life: 3000 });
        return;
    }
    
    console.log('Businesses data to pass:', currentData.value.businesses);
    
    dialog.open(BusinessDetailsComponent, {
        props: {
            header: `Business Details for ${currentData.value.name}`,
            style: {
                width: '70vw',
            },
            breakpoints: {
                '960px': '75vw',
                '640px': '90vw'
            },
            modal: true
        },
        data: {
            business: currentData.value.businesses
        },
        onClose: (options) => {
            if (options?.data) {
                toast.add({ severity: 'info', summary: 'Business Details', detail: `Viewed details for ${currentData.value?.name}`, life: 3000 });
            }
        }
    });
};

</script>

<template>
    <Head title="Admin Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Admin Users</h1>
                <Button @click="openCreateDialog">Add Admin User</Button>
            </div>

            <div class="card">
                <DataTable
                    :value="users.data"
                    paginator
                    :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                    currentPageReportTemplate="{first} to {last} of {totalRecords}"
                >
                    <Column field="name" header="Name"></Column>
                    <Column field="email" header="Email"></Column>
                    <Column field="user_type" header="Type"></Column>
                    <Column header="Businesses" :exportable="false">
                        <template #body="slotProps">
                            <div class="flex flex-wrap gap-1">
                                <Tag
                                    v-for="business in slotProps.data.admin_businesses"
                                    :key="business.id"
                                    :severity="business.id === slotProps.data.default_business_id ? 'success' : 'info'"
                                    :value="business.name"
                                    class="cursor-pointer"
                                >
                                    <template #icon v-if="business.id === slotProps.data.default_business_id">
                                        <i class="pi pi-star-fill mr-1"></i>
                                    </template>
                                </Tag>
                                <div v-if="slotProps.data.admin_businesses.length === 0" class="text-gray-500 italic">No businesses</div>
                            </div>
                        </template>
                    </Column>
                    <Column header="Actions" :exportable="false" style="width: 5rem">
                        <template #body="slotProps">
                            <div class="flex justify-center">
                                <Button
                                    type="button"
                                    size="sm"
                                    @click="toggle($event, slotProps.data)"
                                    aria-haspopup="true"
                                    aria-controls="overlay_menu"
                                >
                                    <i class="pi pi-ellipsis-v"></i>
                                </Button>
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <Menu ref="menu" id="overlay_menu" :model="menu_item" :popup="true" />
            </div>
        </div>
        
        <Toast />
        <DynamicDialog />

        <!-- Create/Edit Dialog -->
        <Dialog :open="showDialog" @update:open="showDialog = false">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ editMode ? 'Edit Admin User' : 'Add New Admin User' }}</DialogTitle>
                </DialogHeader>

                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div class="space-y-2">
                        <Label>Name</Label>
                        <InputText v-model="form.name" required />
                        <small v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</small>
                    </div>

                    <div class="space-y-2">
                        <Label>Email</Label>
                        <InputText v-model="form.email" required type="email" />
                        <small v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</small>
                    </div>

                    <div class="space-y-2">
                        <Label>Password {{ editMode ? '(leave blank to keep current)' : '' }}</Label>
                        <InputPassword type="password" v-model="form.password" :required="!editMode" :toggleMask="true" :fluid="true" aria-label="Password" aria-labelledby="password-label" />
                        <small v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</small>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="showDialog = false">Cancel</Button>
                        <Button type="submit" :disabled="form.processing">{{ editMode ? 'Update' : 'Create' }}</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
