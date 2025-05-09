<template>
  <div class="grapesjs-editor-container">
    <div id="gjs" ref="editorContainer" class="h-full"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';

import createStudioEditor from '@grapesjs/studio-sdk';
import { listPagesComponent } from '@grapesjs/studio-sdk-plugins';
import '@grapesjs/studio-sdk/style';

// Define interface for project data
interface ProjectData {
  html?: string;
  css?: string;
  pages?: Array<{
    name: string;
    component: string;
  }>;
  [key: string]: any;
}

const props = defineProps({
  projectId: {
    type: String,
    default: null
  },
  projectData: {
    type: Object,
    default: () => ({})
  },
  readOnly: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['save', 'load', 'error']);
const editorContainer = ref(null);
let editor: any = null;

onMounted(async () => {
  try {
    const editorConfig = {
      root: '#gjs',
      licenseKey: props.projectId ? 'default-license-key' : 'no-license-key',
      project: {
        type: 'web' as const,
        id: props.projectId,
        data: props.projectData || {},
        default: {
          pages: [
            { name: 'Home', component: '<div class="section"><h1>Welcome to your new page</h1><p>Start editing to create your content</p></div>' }
          ]
        },
      },
      editor: {
        readOnly: props.readOnly,
      },
      storage: {
        type: 'self' as const,
        onSave: async ({ project }: { project: ProjectData }) => {
          emit('save', project);
        },
        onLoad: async () => {
          emit('load', { projectId: props.projectId });
          return { project: props.projectData || {} };
        },
      },
      plugins: [
        listPagesComponent.init({
        })
      ],
      canvas: {
        styles: [
          'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css'
        ]
      }

    };    
    editor = await createStudioEditor(editorConfig);
    
    
  } catch (error) {
    console.error('Error initializing GrapesJS editor:', error);
    emit('error', error);
  }
});

onBeforeUnmount(() => {
  if (editor) {
    editor.destroy();
  }
});
</script>

<style>
.grapesjs-editor-container {
  height: 100%;
  min-height: 70vh;
  width: 100%;
  position: relative;
}

</style>
