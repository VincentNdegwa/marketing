<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { ref } from 'vue';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';


defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            user_type: string;
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
}>();

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

function deleteUser(id: number) {
    if (confirm('Are you sure you want to delete this admin user?')) {
        useForm({}).delete(route('clients.destroy', id));
    }
}

</script>

<template>
    <Head title="Admin Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Admin Users</h1>
                <Button @click="openCreateDialog">Add Admin User</Button>
            </div>

            <div class="card">
                <DataTable 
                    :value="users.data" 
                    tableStyle="min-width: 50rem"
                    stripedRows
                    showGridlines
                    paginator
                    :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                    currentPageReportTemplate="{first} to {last} of {totalRecords}"
                >
                    <Column field="name" header="Name"></Column>
                    <Column field="email" header="Email" ></Column>
                    <Column field="user_type" header="Type" ></Column>
                    <Column header="Actions" :exportable="false">
                        <template #body="slotProps">
                            <div class="space-x-2">
                                <Button variant="outline" size="sm" @click="openEditDialog(slotProps.data)">
                                    <i class="pi pi-pencil mr-1"></i>
                                    Edit
                                </Button>
                                <Button 
                                    v-if="slotProps.data.user_type !== 'Super Admin'" 
                                    variant="destructive" 
                                    size="sm" 
                                    @click="deleteUser(slotProps.data.id)"
                                >
                                    <i class="pi pi-trash mr-1"></i>
                                    Delete
                                </Button>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog :open="showDialog" @update:open="showDialog = false">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ editMode ? 'Edit Admin User' : 'Add New Admin User' }}</DialogTitle>
                </DialogHeader>

                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" v-model="form.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required />
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" v-model="form.email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required />
                        <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password {{ editMode ? '(leave blank to keep current)' : '' }}</label>
                        <input type="password" v-model="form.password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" :required="!editMode" />
                        <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
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