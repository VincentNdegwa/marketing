<script lang="ts" setup>
import { inject, onMounted, ref } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Chip from 'primevue/chip';

interface DialogRef {
    value: {
        data: {
            business: Business[];
        };
    };
}

const dialogRef = inject<DialogRef>('dialogRef');
const businesses = ref<Business[] | null>(null);
const expandedRows = ref({});

onMounted(() => {
    if (dialogRef) {
        businesses.value = dialogRef.value.data.business;
        if (businesses.value && businesses.value.length > 0) {
            collapseAll();
        }
    }
});

const expandAll = () => {
    if (businesses.value) {
        expandedRows.value = businesses.value.reduce((acc: any, business: any) => {
            acc[business.id] = true;
            return acc;
        }, {});
    }
};

const collapseAll = () => {
    expandedRows.value = {};
};

// Helper functions for UI display
const getStatusSeverity = (active: boolean | number) => {
    return active ? 'success' : 'danger';
};
</script>

<template>
    <div class="card p-4">
        <div v-if="businesses?.length && businesses.length > 0">
            <DataTable v-model:expandedRows="expandedRows" :value="businesses" dataKey="id"
                tableStyle="min-width: 60rem" stripedRows class="p-datatable-sm">
                <template #header>
                    <div class="flex flex-wrap justify-between gap-2">
                        <h3 class="text-xl font-bold">Business List</h3>
                        <div>
                            <Button text icon="pi pi-plus" label="Expand All" @click="expandAll" />
                            <Button text icon="pi pi-minus" label="Collapse All" @click="collapseAll" />
                        </div>
                    </div>
                </template>

                <Column expander style="width: 3rem" />
                <Column field="name" header="Business Name" sortable />
                <Column field="email" header="Email" sortable />
                <Column field="phone" header="Phone" sortable>
                    <template #body="slotProps">
                        {{ slotProps.data.phone || 'N/A' }}
                    </template>
                </Column>
                <Column field="website" header="Website" sortable>
                    <template #body="slotProps">
                        <a v-if="slotProps.data.website" :href="slotProps.data.website" target="_blank"
                            class="text-blue-500 hover:underline">
                            {{ slotProps.data.website }}
                        </a>
                        <span v-else>N/A</span>
                    </template>
                </Column>
                <Column field="address" header="Location" sortable>
                    <template #body="slotProps">
                        <div v-if="slotProps.data.city || slotProps.data.country">
                            {{ [slotProps.data.city, slotProps.data.country].filter(Boolean).join(', ') }}
                        </div>
                        <span v-else>N/A</span>
                    </template>
                </Column>
                <Column field="is_active" header="Status" sortable style="width: 8rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.is_active ? 'Active' : 'Inactive'"
                            :severity="getStatusSeverity(slotProps.data.is_active)" />
                    </template>
                </Column>
                <Column field="has_roles" header="Has Roles?" style="width: 8rem">

                </Column>
                <Column field="users" header="Users" style="width: 8rem">
                    <template #body="slotProps">
                        <Tag :value="slotProps.data.users?.length || 0" severity="info" />
                    </template>
                </Column>
                <!-- <Column header="Actions" style="width: 10rem">
                    <template #body="slotProps">
                        <div class="flex gap-1">
                            <Button icon="pi pi-eye" rounded text severity="info" aria-label="View" />
                            <Button icon="pi pi-pencil" rounded text severity="success" aria-label="Edit" />
                            <Button icon="pi pi-trash" rounded text severity="danger" aria-label="Delete" />
                        </div>
                    </template>
                </Column> -->

                <template #expansion="slotProps">
                    <div class="p-3">
                        <h5 class="text-lg font-semibold mb-2">Users for {{ slotProps.data.name }}</h5>
                        <DataTable :value="slotProps.data.users"
                            v-if="slotProps.data.users && slotProps.data.users.length > 0" stripedRows
                            class="p-datatable-sm">
                            <Column field="name" header="Name" sortable />
                            <Column field="email" header="Email" sortable />
                            <Column header="Default" style="width: 8rem">
                                <template #body="userSlot">
                                    <Tag v-if="userSlot.data.pivot && userSlot.data.pivot.is_default" value="Default"
                                        severity="success" icon="pi pi-check" />
                                    <span v-else>-</span>
                                </template>
                            </Column>
                            <Column field="is_active" header="Status" sortable style="width: 8rem">
                                <template #body="userSlot">
                                    <Tag :value="userSlot.data.is_active ? 'Active' : 'Inactive'"
                                        :severity="getStatusSeverity(userSlot.data.is_active)" />
                                </template>
                            </Column>
                            <Column field="roles" header="Roles">
                                <template #body="userSlot">
                                    <div class="flex" v-for="(item, index) in userSlot.data.roles" :key="index">
                                        <Chip :label="item.display_name" />
                                    </div>
                                </template>
                            </Column>
                            <!-- <Column header="Actions" style="width: 10rem">
                                <template #body>
                                    <div class="flex gap-1">
                                        <Button icon="pi pi-eye" rounded text severity="info" aria-label="View" />
                                        <Button icon="pi pi-pencil" rounded text severity="success" aria-label="Edit" />
                                        <Button icon="pi pi-trash" rounded text severity="danger" aria-label="Delete" />
                                    </div>
                                </template>
                            </Column> -->
                        </DataTable>
                        <div v-else class="text-gray-500 italic">No users associated with this business</div>
                    </div>
                </template>
            </DataTable>
        </div>
        <div v-else class="p-4 text-center text-gray-500">Loading business details...</div>
    </div>
</template>
