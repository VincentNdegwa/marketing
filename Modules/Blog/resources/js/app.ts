import '@css/app.css';
import 'primeicons/primeicons.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue, route } from 'ziggy-js';
import { initializeTheme } from '@/composables/useAppearance';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import { resolveModulePage } from '@/utils/modulePageResolver';

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
  interface ImportMetaEnv {
    readonly VITE_APP_NAME: string;
    [key: string]: string | boolean | undefined;
  }

  interface ImportMeta {
    readonly env: ImportMetaEnv;
    readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
  }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  // For the Blog module, we need to make sure pages are correctly resolved
  resolve: (name) => {
    // If the page name is just 'Index', 'Show', etc., prepend 'blog/'
    // If it already has a prefix (like 'blog/Index'), leave it as is
    const pageName = name.includes('/') ? name : `blog/${name}`;
    console.log(`Blog module resolving: ${pageName}`);
    
    try {
      return resolveModulePage(pageName);
    } catch (error) {
      // Fallback: try resolving just the page name without the module prefix
      console.log(`Failed to resolve ${pageName} ${error}, trying fallback...`);
      return resolveModulePage(name);
    }
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) });
    
    // First initialize Ziggy to ensure routes are available
    app.use(plugin);
    app.use(ZiggyVue);
    
    // Then set the route function as a global property
    app.config.globalProperties.$route = route;
    
    app.use(PrimeVue, {
      theme: {
        preset: Aura,
        options: {
          prefix: 'p',
          darkModeSelector: '.dark',
          cssLayer: false
        }
      }
    });
    
    app.mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});

initializeTheme();
