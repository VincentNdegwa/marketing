<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import DataTable from 'primevue/datatable';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Menu from 'primevue/menu';
import FileUpload from 'primevue/fileupload';
import ConfirmDialog from 'primevue/confirmdialog';
import Textarea from 'primevue/textarea';
// import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { usePermissions } from '@/composables/usePermissions';
import { route } from 'ziggy-js';
import axios from 'axios';

// Define types for our media and folder objects
interface MediaFile {
  id: number;
  name: string;
  file_name: string;
  file_path: string;
  mime_type: string;
  size: number;
  folder: string;
  alt_text?: string;
  caption?: string;
  url: string;
  is_image: boolean;
  is_video?: boolean;
  is_document?: boolean;
  metadata?: Record<string, any>;
}

interface Folder {
  name: string;
  path: string;
  count: number;
}

const { hasPermission } = usePermissions();
const toast = useToast();
const menu = ref<InstanceType<typeof Menu> | null>(null);
const currentMedia = ref<MediaFile | null>(null);

// State
const mediaFiles = ref<MediaFile[]>([]);
const folders = ref<Folder[]>([]);
const currentFolder = ref('/');
const totalRecords = ref(0);
const perPage = ref(20);
const first = ref(0);
const showUploadDialog = ref(false);
const showCreateFolderDialog = ref(false);
const showEditDialog = ref(false);
const uploadFolder = ref('');
const newFolderName = ref('');

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
    title: 'Media',
    href: 'cms.media.index',
  },
];

const filters = ref({
  global: { value: null, matchMode: 'contains' },
});

const menu_items = ref([
  {
    label: 'Edit Details',
    icon: 'pi pi-pencil',
    command: () => {
      if (currentMedia.value) {
        editMedia(currentMedia.value);
      }
    },
    visible: () => hasPermission('cms.media.edit')
  },
  {
    label: 'Download',
    icon: 'pi pi-download',
    command: () => {
      if (currentMedia.value) {
        window.open(currentMedia.value.url, '_blank');
      }
    }
  },
  {
    label: 'Delete',
    icon: 'pi pi-trash',
    class: 'text-red-500',
    command: () => {
      if (currentMedia.value) {
        confirmDelete(currentMedia.value);
      }
    },
    visible: () => hasPermission('cms.media.delete')
  },
]);

// Methods
const loadMedia = async (page = 1) => {
  try {
    const response = await axios.get(route('cms.media.index'), {
      params: {
        folder: currentFolder.value,
        page: page,
        per_page: perPage.value
      }
    });
    
    mediaFiles.value = response.data.files;
    folders.value = response.data.folders;
    totalRecords.value = response.data.total;
    
  } catch {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to load media files',
      life: 3000
    });
  }
};

const navigateToFolder = (folder: string) => {
  currentFolder.value = folder;
  first.value = 0;
  loadMedia(1);
};

const navigateUp = () => {
  if (currentFolder.value === '/') return;
  
  const parts = currentFolder.value.split('/').filter(Boolean);
  parts.pop();
  currentFolder.value = parts.length ? '/' + parts.join('/') : '/';
  
  loadMedia(1);
};

const onPageChange = (event: { page: number }) => {
  loadMedia(event.page + 1);
};

const formatFileSize = (bytes: number) => {
  if (bytes === 0) return '0 Bytes';
  
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const onUpload = () => {
  showUploadDialog.value = false;
  loadMedia();
  toast.add({
    severity: 'success',
    summary: 'Success',
    detail: 'Files uploaded successfully',
    life: 3000
  });
};

const createFolder = async () => {
  if (!newFolderName.value) return;
  
  try {
    await axios.post(route('cms.media.folder.create'), {
      name: newFolderName.value,
      parent: currentFolder.value
    });
    
    showCreateFolderDialog.value = false;
    newFolderName.value = '';
    loadMedia();
    
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: 'Folder created successfully',
      life: 3000
    });
  } catch {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to create folder',
      life: 3000
    });
  }
};

const editMedia = (file: MediaFile) => {
  currentMedia.value = file;
  showEditDialog.value = true;
};

const toggle = (event: Event, file: MediaFile) => {
  currentMedia.value = file;
  menu.value?.toggle(event);
};

const confirmDelete = (file: MediaFile) => {
  deleteMedia(file);
};

const deleteMedia = async (file: MediaFile) => {
  try {
    await axios.delete(route('cms.media.destroy', { media: file.id }));
    
    loadMedia();
    
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: 'Media deleted successfully',
      life: 3000
    });
  } catch{
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to delete media',
      life: 3000
    });
  }
};

const updateMedia = async () => {
  if (!currentMedia.value) return;
  
  try {
    await axios.put(
      route('cms.media.update', { media: currentMedia.value.id }),
      {
        name: currentMedia.value.name,
        alt_text: currentMedia.value.alt_text,
        caption: currentMedia.value.caption,
        metadata: currentMedia.value.metadata || {}
      }
    );
    
    showEditDialog.value = false;
    loadMedia();
    
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: 'Media updated successfully',
      life: 3000
    });
  } catch {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to update media',
      life: 3000
    });
  }
};

// Lifecycle
onMounted(() => {
  loadMedia();
  uploadFolder.value = currentFolder.value;
});
</script>

<template>
  <Head title="Media Library" />
  
  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Media Library</h1>
        <div class="flex gap-2">
          <Button 
            v-permission="'cms.media.create.folder'" 
            label="New Folder" 
            icon="pi pi-folder" 
            severity="secondary" 
            outlined
            @click="showCreateFolderDialog = true" 
          />
          <Button 
            v-permission="'cms.media.upload'" 
            label="Upload Files" 
            icon="pi pi-upload" 
            severity="primary" 
            @click="showUploadDialog = true" 
          />
        </div>
      </div>
      
      <div class="mb-4">
        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
          <div class="flex flex-row gap-1 md:w-60 w-full items-center">
            <InputText v-model="filters.global.value" placeholder="Search media..." class="w-full" />
          </div>
          
          <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-md">
            <span class="font-medium text-sm">Current Folder:</span>
            <Button 
              v-if="currentFolder !== '/'" 
              icon="pi pi-arrow-up" 
              @click="navigateUp" 
              class="p-button-text p-button-sm"
              tooltip="Up one level"
            />
          </div>
        </div>
      </div>

      <!-- Folders and Files Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-4">
        <!-- Folders -->
        <div 
          v-for="folder in folders" 
          :key="folder.path" 
          @click="navigateToFolder(folder.path)"
          class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 flex items-center shadow-sm"
        >
          <i class="pi pi-folder text-yellow-500 text-2xl mr-3"></i>
          <div>
            <div class="font-medium">{{ folder.name }}</div>
            <div class="text-xs text-gray-500">{{ folder.count }} items</div>
          </div>
        </div>

        <!-- Media Files -->
        <div 
          v-for="file in mediaFiles" 
          :key="file.id" 
          class="border rounded-lg p-2 hover:bg-gray-50 shadow-sm relative group"
        >
          <div class="aspect-w-16 aspect-h-9 mb-2 bg-gray-100 rounded overflow-hidden">
            <img 
              v-if="file.is_image" 
              :src="file.url" 
              :alt="file.alt_text || file.name" 
              class="object-cover w-full h-full"
            />
            <div 
              v-else 
              class="flex items-center justify-center h-full"
            >
              <i 
                :class="{
                  'pi pi-file-pdf': file.mime_type === 'application/pdf',
                  'pi pi-file-excel': file.mime_type.includes('spreadsheet'),
                  'pi pi-file-word': file.mime_type.includes('document'),
                  'pi pi-file': !file.mime_type.includes('pdf') && !file.mime_type.includes('spreadsheet') && !file.mime_type.includes('document')
                }"
                class="text-4xl text-gray-400"
              ></i>
            </div>
          </div>
          <div class="px-2">
            <div class="font-medium truncate">{{ file.name }}</div>
            <div class="text-xs text-gray-500">{{ formatFileSize(file.size) }}</div>
          </div>
          <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <Button 
              v-can="['cms.media.edit', 'cms.media.delete', 'cms.media.download']" 
              type="button" 
              severity="contrast" 
              size="sm" 
              @click="toggle($event, file)" 
              aria-haspopup="true"
              aria-controls="media_menu"
            >
              <i class="pi pi-ellipsis-v"></i>
            </Button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <DataTable 
        :value="[]" 
        :paginator="true" 
        :rows="perPage" 
        :total-records="totalRecords"
        v-model:first="first" 
        @page="onPageChange"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[10, 20, 50, 100]"
        currentPageReportTemplate="Showing {first} to {last} of {totalRecords} items"
        class="p-datatable-sm"
      >
      </DataTable>
    </div>

    <!-- Media Actions Menu -->
    <Menu ref="menu" id="media_menu" :model="menu_items" :popup="true" />

    <!-- Upload Dialog -->
    <Dialog 
      v-model:visible="showUploadDialog" 
      header="Upload Media" 
      :style="{ width: '450px' }"
      modal
    >
      <div class="p-fluid">
        <div class="field mb-4">
          <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Files</label>
          <FileUpload
            name="file"
            url="/cms/media/upload"
            :multiple="true"
            accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.*"
            :max-file-size="10000000"
            @upload="onUpload"
            :headers="{ 'X-CSRF-TOKEN': $page.props.csrf_token }"
            class="w-full"
          >
            <template #empty>
              <p class="text-center py-4 text-gray-500">Drag and drop files here or click to browse.</p>
            </template>
          </FileUpload>
        </div>
        <div class="field">
          <label for="folder" class="block text-sm font-medium text-gray-700 mb-1">Destination Folder</label>
          <InputText id="folder" v-model="uploadFolder" placeholder="Current folder" class="w-full" />
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end gap-2">
          <Button label="Cancel" icon="pi pi-times" @click="showUploadDialog = false" outlined />
          <Button label="Upload" icon="pi pi-check" @click="onUpload" severity="primary" />
        </div>
      </template>
    </Dialog>

    <!-- Create Folder Dialog -->
    <Dialog 
      v-model:visible="showCreateFolderDialog" 
      header="Create New Folder" 
      :style="{ width: '350px' }"
      modal
    >
      <div class="p-fluid">
        <div class="field">
          <label for="folderName" class="block text-sm font-medium text-gray-700 mb-1">Folder Name</label>
          <InputText id="folderName" v-model="newFolderName" class="w-full" placeholder="Enter folder name" />
          <small class="text-gray-500">The folder will be created in the current location.</small>
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end gap-2">
          <Button label="Cancel" icon="pi pi-times" @click="showCreateFolderDialog = false" outlined />
          <Button label="Create" icon="pi pi-check" @click="createFolder" severity="primary" />
        </div>
      </template>
    </Dialog>

    <!-- Delete Confirmation Dialog -->
    <ConfirmDialog>
      <template #message="{ message }">
        <div class="flex items-start gap-4">
          <i class="pi pi-exclamation-triangle text-red-500 text-2xl mt-1"></i>
          <div>
            <p class="font-medium">{{ message }}</p>
            <p class="text-sm text-gray-500 mt-1">This action cannot be undone.</p>
          </div>
        </div>
      </template>
    </ConfirmDialog>
    
    <!-- Edit Media Dialog -->
    <Dialog 
      v-model:visible="showEditDialog" 
      header="Edit Media Details" 
      :style="{ width: '500px' }"
      modal
    >
      <div class="p-fluid" v-if="currentMedia">
        <div class="field mb-4">
          <label for="mediaName" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
          <InputText id="mediaName" v-model="currentMedia.name" class="w-full" />
        </div>
        
        <div class="field mb-4">
          <label for="mediaAltText" class="block text-sm font-medium text-gray-700 mb-1">Alt Text</label>
          <InputText id="mediaAltText" v-model="currentMedia.alt_text" class="w-full" placeholder="Alternative text for accessibility" />
        </div>
        
        <div class="field mb-4">
          <label for="mediaCaption" class="block text-sm font-medium text-gray-700 mb-1">Caption</label>
          <Textarea id="mediaCaption" v-model="currentMedia.caption" class="w-full" rows="3" placeholder="Caption or description" />
        </div>
        
        <div class="field mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">File Information</label>
          <div class="bg-gray-50 p-3 rounded text-sm">
            <p><span class="font-medium">File name:</span> {{ currentMedia.file_name }}</p>
            <p><span class="font-medium">Type:</span> {{ currentMedia.mime_type }}</p>
            <p><span class="font-medium">Size:</span> {{ formatFileSize(currentMedia.size) }}</p>
            <p><span class="font-medium">Location:</span> {{ currentMedia.folder }}</p>
          </div>
        </div>
        
        <div class="field" v-if="currentMedia.is_image">
          <label class="block text-sm font-medium text-gray-700 mb-1">Preview</label>
          <img :src="currentMedia.url" class="max-w-full h-auto rounded border" :alt="currentMedia.alt_text || currentMedia.name" />
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end gap-2">
          <Button label="Cancel" icon="pi pi-times" @click="showEditDialog = false" outlined />
          <Button label="Save" icon="pi pi-check" @click="updateMedia" severity="primary" />
        </div>
      </template>
    </Dialog>
  </AppLayout>
</template>

