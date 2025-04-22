<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { InputPassword } from '@/components/ui/input-password';
import { InputText } from '@/components/ui/input-text';
import { Label } from '@/components/ui/label';
import { ref, defineProps, defineEmits, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ToggleSwitch from 'primevue/toggleswitch';

interface ClientFormData {
    // Business details
    id: string;
    name: string;
    email: string;
    phone: string;
    website: string;
    address: string;
    city: string;
    state: string;
    country: string;
    zip_code: string;
    description: string;
    is_active: boolean;
    
    // Admin user details
    admin_user: {
        id: string;
        name: string;
        email: string;
        password: string;
        [key: string]: any;
    };
    
    // Index signature to satisfy FormDataType constraint
    [key: string]: any;
}

const props = defineProps<{
    open: boolean;
    editMode: boolean;
    userData: User | null;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'success'): void;
}>();

const currentData = ref<User | null>(null);

// Initialize the form
const form = useForm<ClientFormData>({
    // Business details
    id: '',
    name: '',
    email: '',
    phone: '',
    website: '',
    address: '',
    city: '',
    state: '',
    country: '',
    zip_code: '',
    description: '',
    is_active: true,
    
    // Admin user details
    admin_user: {
        id: '',
        name: '',
        email: '',
        password: ''
    }
});

watch(() => props.userData, (newValue) => {
    if (newValue) {
        currentData.value = newValue;
        form.admin_user.id = newValue.id.toString();
        form.admin_user.name = newValue.name;
        form.admin_user.email = newValue.email;
        form.admin_user.password = ''; 
        
        if (newValue.admin_businesses && newValue.admin_businesses.length > 0) {
            form.id = newValue.admin_businesses[0].id.toString();
            loadBusinessData(newValue.admin_businesses[0]);
        } else {
            resetBusinessFields();
        }
    }
}, { immediate: true });

// Watch for changes in open prop
watch(() => props.open, (newValue) => {
    if (!newValue) {
        // Reset form when dialog is closed
        form.reset();
        form.clearErrors();
    }
});

// Helper function to load business data into the form
function loadBusinessData(business: any) {
    form.id = business.id.toString();
    form.name = business.name;
    form.email = business.email;
    form.phone = business.phone || '';
    form.website = business.website || '';
    form.address = business.address || '';
    form.city = business.city || '';
    form.state = business.state || '';
    form.country = business.country || '';
    form.zip_code = business.zip_code || '';
    form.description = business.description || '';
    form.is_active = business.is_active === 1;
}

// Helper function to reset business fields
function resetBusinessFields() {
    form.id = '';
    form.name = '';
    form.email = '';
    form.phone = '';
    form.website = '';
    form.address = '';
    form.city = '';
    form.state = '';
    form.country = '';
    form.zip_code = '';
    form.description = '';
    form.is_active = true;
}

// Function to handle business selection change
function onBusinessChange() {
    if (!currentData.value || !currentData.value.admin_businesses) return;
    
    const selectedBusiness = currentData.value.admin_businesses.find(
        business => business.id.toString() === form.id
    );
    
    if (selectedBusiness) {
        loadBusinessData(selectedBusiness);
    }
}

// Handle form submission
function handleSubmit() {
    if (props.editMode) {
        form.put(route('clients.update', form.id), {
            onSuccess: () => {
                emit('update:open', false);
                emit('success');
            }
        });
    } else {
        form.post(route('clients.store'), {
            onSuccess: () => {
                emit('update:open', false);
                emit('success');
            }
        });
    }
}

// Close the dialog
function closeDialog() {
    emit('update:open', false);
}
</script>

<template>
    <Dialog :open="open" @update:open="closeDialog" class="overflow-y-auto max-h-screen">
        <DialogContent class="max-h-[90vh] overflow-y-auto max-w-full min-w-full md:min-w-1/2">
            <DialogHeader>
                <DialogTitle>{{ editMode ? 'Edit Business' : 'Add New Business' }}</DialogTitle>
            </DialogHeader>

            <form @submit.prevent="handleSubmit" class="space-y-4">

                <!-- Business Selector (only visible in edit mode) -->
                <div v-if="editMode && currentData?.admin_businesses && currentData.admin_businesses.length > 0"
                    class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Select Business to Edit</h3>
                    <div class="space-y-2">
                        <Label :required="true">Business</Label>
                        <select v-model="form.id" @change="onBusinessChange"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                            <option v-for="business in currentData.admin_businesses" :key="business.id"
                                :value="business.id.toString()">
                                {{ business.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Admin User Section -->
                <div class="mb-4 pt-4 border-t border-gray-200">
                    <h3 class="text-lg font-medium mb-2">Admin User Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label :required="true">Admin Name</Label>
                            <InputText v-model="form.admin_user.name" required />
                            <small v-if="form.errors['admin_user.name']" class="mt-1 text-sm text-red-500">{{
                                form.errors['admin_user.name'] }}</small>
                        </div>

                        <div class="space-y-2">
                            <Label :required="true">Admin Email</Label>
                            <InputText v-model="form.admin_user.email" required type="email" />
                            <small v-if="form.errors['admin_user.email']" class="mt-1 text-sm text-red-500">{{
                                form.errors['admin_user.email'] }}</small>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <Label :required="true">Password {{ editMode ? '(leave blank to keep current)' : ''
                                }}</Label>
                            <InputPassword v-model="form.admin_user.password" :required="!editMode" :toggleMask="true"
                                :fluid="true" aria-label="Password" aria-labelledby="password-label" />
                            <small v-if="form.errors['admin_user.password']" class="mt-1 text-sm text-red-500">{{
                                form.errors['admin_user.password'] }}</small>
                        </div>
                    </div>
                </div>

                <!-- Business Information Section -->
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Business Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label :required="true">Business Name</Label>
                            <InputText v-model="form.name" required />
                            <small v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name
                                }}</small>
                        </div>

                        <div class="space-y-2">
                            <Label :required="true" >Business Email</Label>
                            <InputText v-model="form.email" required type="email" />
                            <small v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email
                                }}</small>
                        </div>

                        <div class="space-y-2">
                            <Label>Phone</Label>
                            <InputText v-model="form.phone" />
                            <small v-if="form.errors.phone" class="mt-1 text-sm text-red-500">{{ form.errors.phone
                                }}</small>
                        </div>

                        <div class="space-y-2">
                            <Label>Website</Label>
                            <InputText v-model="form.website" />
                            <small v-if="form.errors.website" class="mt-1 text-sm text-red-500">{{
                                form.errors.website }}</small>
                        </div>
                    </div>
                </div>

                <!-- Address Section -->
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Address Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Address</Label>
                            <InputText v-model="form.address" />
                            <small v-if="form.errors.address" class="mt-1 text-sm text-red-500">{{
                                form.errors.address }}</small>
                        </div>

                        <div class="space-y-2">
                            <Label>City</Label>
                            <InputText v-model="form.city" />
                            <small v-if="form.errors.city" class="mt-1 text-sm text-red-500">{{ form.errors.city
                                }}</small>
                        </div>

                        <div class="space-y-2">
                            <Label>State/Province</Label>
                            <InputText v-model="form.state" />
                            <small v-if="form.errors.state" class="mt-1 text-sm text-red-500">{{ form.errors.state
                                }}</small>
                        </div>

                        <div class="space-y-2">
                            <Label>Country</Label>
                            <InputText v-model="form.country" />
                            <small v-if="form.errors.country" class="mt-1 text-sm text-red-500">{{
                                form.errors.country }}</small>
                        </div>

                        <div class="space-y-2">
                            <Label>Zip/Postal Code</Label>
                            <InputText v-model="form.zip_code" />
                            <small v-if="form.errors.zip_code" class="mt-1 text-sm text-red-500">{{
                                form.errors.zip_code }}</small>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="space-y-2">
                    <Label>Description</Label>
                    <textarea v-model="form.description" rows="3"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"></textarea>
                    <small v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{
                        form.errors.description }}</small>
                </div>

                <!-- Status Section -->
                <div class="space-y-2">
                    <Label>Status</Label>
                    <div class="flex items-center space-x-2">
                        <ToggleSwitch v-model="form.is_active" />
                        <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                    </div>
                    <small v-if="form.errors.is_active" class="mt-1 text-sm text-red-500">{{ form.errors.is_active
                        }}</small>
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="closeDialog">Cancel</Button>
                    <Button type="submit" :disabled="form.processing">{{ editMode ? 'Update' : 'Create' }}</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
