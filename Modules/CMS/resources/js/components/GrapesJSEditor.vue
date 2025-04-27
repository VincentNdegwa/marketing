<template>
  <div class="grapesjs-editor-container">
    <div id="gjs" ref="editorContainer" class="h-full"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';

import createStudioEditor from '@grapesjs/studio-sdk';
import '@grapesjs/studio-sdk/style';

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
    editor = await createStudioEditor({
      container: '#gjs',
      height: '100%',
      root: editorContainer.value,
      project: {
        type: 'web',
        id: props.projectId,
        data: props.projectData || {},
        // The default project to use for new projects
        default: {
          pages: [
            { name: 'Home', component: '<div class="section"><h1>Welcome to your new page</h1><p>Start editing to create your content</p></div>' }
          ]
        },
      },
      // Configure editor options
      editor: {
        readOnly: props.readOnly,
      },
      // Add custom blocks and components
      // Configure toolbar
      toolbar: {
        // Customize toolbar options
      },
      // Configure storage
      storage: {
        // By default, the editor will use local storage
        // You can override this to use your own storage solution
        onStore: async (data: any) => {
          // Emit save event with project data
          emit('save', data);
          return { success: true };
        },
        onLoad: async (props: { projectId: string | null; projectData: Record<string, any> }) => {
          // Emit load event
          emit('load', { projectId: props.projectId });
          // Return project data from props
          return props.projectData;
        }
      }
    });
    
    // Additional editor setup if needed
    
  } catch (error) {
    console.error('Error initializing GrapesJS editor:', error);
    emit('error', error);
  }
});

onBeforeUnmount(() => {
  // Clean up editor instance
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
}
</style>
