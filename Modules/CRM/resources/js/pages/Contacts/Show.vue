<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <template #page-title>
            {{ pageTitle }}
        </template>

        <template #page-actions>
            <ButtonGroup>

                <Button size="small" label="Edit" severity="contrast"
                    @click="navigateTo(route('crm.contacts.edit', contact.id))" />
                <Button size="small" label="Deal" severity="contrast" @click="createDeal" />
                <Button size="small" label="Activity" severity="contrast" @click="scheduleActivity" />
                <Button size="small" label="Task" severity="contrast" @click="createTask" />
                <Button size="small" label="Delete" severity="danger" @click="confirmDelete" />

            </ButtonGroup>
        </template>

        <div class="space-y-6">
            <!-- Contact Information Card -->
            <Card>
                <template #title>Contact Information</template>
                <template #content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Name</h3>
                            <p>{{ contact.first_name }} {{ contact.last_name }}</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Email</h3>
                            <p>{{ contact.email || 'Not provided' }}</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Phone</h3>
                            <p>{{ contact.phone || 'Not provided' }}</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Mobile</h3>
                            <p>{{ contact.mobile || 'Not provided' }}</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Job Title</h3>
                            <p>{{ contact.job_title || 'Not provided' }}</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Company</h3>
                            <p v-if="contact.company">
                                <Link :href="route('crm.companies.show', contact.company.id)">
                                {{ contact.company.name }}
                                </Link>
                            </p>
                            <p v-else>Not associated with a company</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Lead Status</h3>
                            <Tag v-if="contact.lead_status" :value="contact.lead_status"
                                :severity="getLeadStatusSeverity(contact.lead_status)" />
                            <p v-else>Not set</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Lead Source</h3>
                            <p>{{ contact.lead_source || 'Not set' }}</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Assigned To</h3>
                            <p>{{ contact.assigned_user?.name || 'Not assigned' }}</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Status</h3>
                            <Tag :value="contact.status || 'active'"
                                :severity="contact.status === 'inactive' ? 'danger' : 'success'" />
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Website</h3>
                            <p v-if="contact.website">
                                <a :href="contact.website" target="_blank" rel="noopener noreferrer"
                                    class="text-primary">
                                    {{ contact.website }}
                                </a>
                            </p>
                            <p v-else>Not provided</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Created At</h3>
                            <p>{{ formatDate(contact.created_at) }}</p>
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Address Information Card -->
            <Card>
                <template #title>Address Information</template>
                <template #content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Street Address</h3>
                            <p>{{ contact.address || 'Not provided' }}</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">City</h3>
                            <p>{{ contact.city || 'Not provided' }}</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">State/Province</h3>
                            <p>{{ contact.state || 'Not provided' }}</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">ZIP/Postal Code</h3>
                            <p>{{ contact.postal_code || 'Not provided' }}</p>
                        </div>

                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Country</h3>
                            <p>{{ contact.country || 'Not provided' }}</p>
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Additional Information -->
            <Card v-if="contact.notes || (contact.tags && contact.tags.length)">
                <template #title>Additional Information</template>
                <template #content>
                    <div class="space-y-4">
                        <div v-if="contact.tags && contact.tags.length" class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Tags</h3>
                            <div class="flex flex-wrap gap-1">
                                <Chip v-for="tag in contact.tags" :key="tag" :label="tag" class="mr-2" />
                            </div>
                        </div>

                        <div v-if="contact.notes" class="flex flex-col gap-1">
                            <h3 class="font-semibold text-sm">Notes</h3>
                            <p class="whitespace-pre-wrap">{{ contact.notes }}</p>
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Activity, Deals, Tasks tabs -->
            <div class="card">
                <Tabs value="0" :modelValue="activeTab" @update:modelValue="activeTab = $event">
                    <TabList>
                        <Tab value="activities">Activities</Tab>
                        <Tab value="deals">Deals</Tab>
                        <Tab value="tasks">Tasks</Tab>
                        <Tab value="notes">Notes</Tab>
                    </TabList>
                    <TabPanels>
                        <!-- Activities Panel -->
                        <TabPanel value="activities">
                            <div class="flex justify-between mb-3">
                                <h3 class="text-lg font-medium">Recent Activities</h3>
                                <Button severity="contrast" size="small" @click="scheduleActivity">
                                    Schedule Activity
                                </Button>
                            </div>
                            <div v-if="contact.activities && contact.activities.length" class="space-y-3">
                                <div v-for="activity in contact.activities" :key="activity.id" class="border-b pb-3">
                                    <h4 class="font-medium">{{ activity.type }}</h4>
                                    <p class="text-sm">{{ activity.description }}</p>
                                    <div class="text-xs mt-1">{{ formatDate(activity.created_at) }}</div>
                                </div>
                            </div>
                            <div v-else class="text-center py-4">
                                No activities recorded
                            </div>
                        </TabPanel>

                        <!-- Deals Panel -->
                        <TabPanel value="deals">
                            <div class="flex justify-between mb-3">
                                <h3 class="text-lg font-medium">Related Deals</h3>
                                <Button severity="contrast" size="small" @click="createDeal">
                                    Create Deal
                                </Button>
                            </div>
                            <div v-if="contact.deals && contact.deals.length" class="space-y-3">
                                <div v-for="deal in contact.deals" :key="deal.id" class="border-b pb-3">
                                    <h4 class="font-medium">
                                        <Link :href="route('crm.deals.show', deal.id)">
                                        {{ deal.name }}
                                        </Link>
                                    </h4>
                                    <div class="flex justify-between text-sm mt-1">
                                        <span>{{ formatCurrency(deal.value) }}</span>
                                        <Tag :value="deal.stage" :severity="getDealStageSeverity(deal.stage)" />
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-4">
                                No deals associated
                            </div>
                        </TabPanel>

                        <!-- Tasks Panel -->
                        <TabPanel value="tasks">
                            <div class="flex justify-between mb-3">
                                <h3 class="text-lg font-medium">Assigned Tasks</h3>
                                <Button severity="contrast" size="small" @click="createTask">
                                    Create Task
                                </Button>
                            </div>
                            <div v-if="contact.tasks && contact.tasks.length" class="space-y-3">
                                <div v-for="task in contact.tasks" :key="task.id" class="border-b pb-3">
                                    <h4 class="font-medium">{{ task.title }}</h4>
                                    <p class="text-sm">{{ task.description }}</p>
                                    <div class="flex justify-between text-xs mt-1">
                                        <span>Due: {{ formatDate(task.due_date) }}</span>
                                        <Tag :value="task.status" :severity="getTaskStatusSeverity(task.status)" />
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-4">
                                No tasks assigned
                            </div>
                        </TabPanel>

                        <!-- Notes Panel -->
                        <TabPanel value="notes">
                            <div class="flex justify-between mb-3">
                                <h3 class="text-lg font-medium">Contact Notes</h3>
                                <Button severity="contrast" size="small" @click="addNote">
                                    Add Note
                                </Button>
                            </div>
                            <div v-if="contact.notes && contact.notes.length" class="space-y-3">
                                <div v-for="note in contact.notes" :key="note.id" class="border-b pb-3">
                                    <p class="whitespace-pre-wrap">{{ note.content }}</p>
                                    <div class="text-xs mt-1">Added {{ formatDate(note.created_at) }}</div>
                                </div>
                            </div>
                            <div v-else class="text-center py-4">
                                No notes added
                            </div>
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>
        </div>
    </AppLayout>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:visible="deleteDialog" header="Delete Contact" :style="{ width: '450px' }" :modal="true">
        <div class="confirmation-content">
            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
            <span>Are you sure you want to delete this contact? This action cannot be undone.</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" @click="deleteDialog = false" class="p-button-text" />
            <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteContact" />
        </template>
    </Dialog>

    <!-- Activity Dialog -->
    <Dialog v-model:visible="activityDialog" header="Schedule Activity" :style="{ width: '500px' }" :modal="true">
        <div class="p-fluid">
            <div class="field mb-4">
                <label for="activity-type">Activity Type</label>
                <Dropdown id="activity-type" v-model="newActivity.type" :options="activityTypes" optionLabel="label"
                    optionValue="value" placeholder="Select Type" class="w-full" />
            </div>
            <div class="field mb-4">
                <label for="activity-date">Date</label>
                <Calendar id="activity-date" v-model="newActivity.date" dateFormat="yy-mm-dd" placeholder="Select Date"
                    class="w-full" />
            </div>
            <div class="field mb-4">
                <label for="activity-description">Description</label>
                <Textarea id="activity-description" v-model="newActivity.description" rows="3" />
            </div>
        </div>
        <template #footer>
            <Button label="Cancel" icon="pi pi-times" @click="activityDialog = false" class="p-button-text" />
            <Button label="Schedule" icon="pi pi-check" severity="contrast" @click="saveActivity" />
        </template>
    </Dialog>

    <!-- Note Dialog -->
    <Dialog v-model:visible="noteDialog" header="Add Note" :style="{ width: '500px' }" :modal="true">
        <div class="p-fluid">
            <div class="field mb-4">
                <label for="note-content">Note</label>
                <Textarea id="note-content" v-model="newNote.content" rows="5" />
            </div>
        </div>
        <template #footer>
            <Button label="Cancel" icon="pi pi-times" @click="noteDialog = false" class="p-button-text" />
            <Button label="Save" icon="pi pi-check" severity="contrast" @click="saveNote" />
        </template>
    </Dialog>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from 'primevue/card';
import Tag from 'primevue/tag';
import Chip from 'primevue/chip';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import Textarea from 'primevue/textarea';
import Calendar from 'primevue/calendar';
import type { BreadcrumbItem } from '@/types';
import ButtonGroup from 'primevue/buttongroup';
// import ActionButton from '@/components/ui/action-button/ActionButton.vue';

interface Company {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
}

interface Activity {
    id: number;
    type: string;
    description: string;
    created_at: string;
}

interface Deal {
    id: number;
    name: string;
    value: number;
    stage: string;
}

interface Task {
    id: number;
    title: string;
    description: string;
    due_date: string;
    status: string;
}

interface Note {
    id: number;
    content: string;
    created_at: string;
}

interface Contact {
    id: number;
    first_name: string;
    last_name: string;
    email: string | null;
    phone: string | null;
    mobile: string | null;
    job_title: string | null;
    company_id: number | null;
    lead_source: string | null;
    lead_status: string | null;
    address: string | null;
    city: string | null;
    state: string | null;
    postal_code: string | null;
    country: string | null;
    website: string | null;
    tags: string[] | null;
    assigned_to: number | null;
    assigned_user: User | null;
    status: string | null;
    company: Company | null;
    activities: Activity[] | null;
    deals: Deal[] | null;
    tasks: Task[] | null;
    notes: Note[] | null;
    created_at: string;
    updated_at: string;
    full_name: string;
}

interface Props {
    contact: Contact;
    pageTitle: string;
}

const props = defineProps<Props>();
const activeTab = ref('activities');
const deleteDialog = ref(false);
const activityDialog = ref(false);
const noteDialog = ref(false);

// New activity form
const newActivity = ref({
    type: '',
    date: null,
    description: ''
});

// New note form
const newNote = ref({
    content: ''
});

const activityTypes = [
    { label: 'Call', value: 'call' },
    { label: 'Meeting', value: 'meeting' },
    { label: 'Email', value: 'email' },
    { label: 'Follow-up', value: 'follow_up' }
];

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Dashboard',
        href: 'dashboard',
    },
    {
        title: 'Contacts',
        href: 'crm.contacts.index',
    },
    {
        title: props.contact.first_name + ' ' + props.contact.last_name,
        href: `crm.contacts.show`,
        active: true
    },
]);

const navigateTo = (path: string) => {
    router.visit(path);
};

const confirmDelete = () => {
    deleteDialog.value = true;
};

const deleteContact = () => {
    router.delete(route('crm.contacts.destroy', props.contact.id), {
        onSuccess: () => {
            // Redirect will be handled by the controller
            navigateTo(route('crm.contacts.index'));
        }
    });
};

// Create a new deal with this contact pre-selected
const createDeal = () => {
    navigateTo(route('crm.deals.create', { contact_id: props.contact.id }));
};

// Schedule an activity for this contact
const scheduleActivity = () => {
    activityDialog.value = true;
};

const saveActivity = () => {
    router.post(route('crm.activities.store'), {
        contact_id: props.contact.id,
        type: newActivity.value.type,
        scheduled_date: newActivity.value.date,
        description: newActivity.value.description
    }, {
        onSuccess: () => {
            activityDialog.value = false;
            // Reset form
            newActivity.value = {
                type: '',
                date: null,
                description: ''
            };
        }
    });
};

// Create a task associated with this contact
const createTask = () => {
    navigateTo(route('crm.tasks.create', { contact_id: props.contact.id }));
};

// Add a note to this contact
const addNote = () => {
    noteDialog.value = true;
};

const saveNote = () => {
    router.post(route('crm.notes.store'), {
        contact_id: props.contact.id,
        content: newNote.value.content
    }, {
        onSuccess: () => {
            noteDialog.value = false;
            // Reset form
            newNote.value = {
                content: ''
            };
        }
    });
};

const getLeadStatusSeverity = (status: string): string => {
    switch (status) {
        case 'customer': return 'success';
        case 'qualified': return 'info';
        case 'contacted': return 'warning';
        case 'unqualified': return 'danger';
        default: return 'secondary';
    }
};

const getDealStageSeverity = (stage: string): string => {
    switch (stage) {
        case 'won': return 'success';
        case 'negotiation': return 'warning';
        case 'proposal': return 'info';
        case 'qualified': return 'info';
        case 'lost': return 'danger';
        default: return 'secondary';
    }
};

const getTaskStatusSeverity = (status: string): string => {
    switch (status) {
        case 'completed': return 'success';
        case 'in_progress': return 'warning';
        case 'not_started': return 'secondary';
        case 'delayed': return 'danger';
        default: return 'secondary';
    }
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
};
</script>