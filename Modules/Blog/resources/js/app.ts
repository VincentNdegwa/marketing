// Import CSS if needed
// import '../css/module.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { route } from 'ziggy-js';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';

// Import the Blog component for direct use in Blade templates
import BlogComponent from './components/BlogComponent.vue';

// Extend ImportMeta interface for Vite
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

// Register components for direct use in Blade templates
const app = createApp({});
app.component('blog-component', BlogComponent);

// Mount the app if there's a specific element for this module (for Blade usage)
const blogElement = document.getElementById('blog-module');
if (blogElement) {
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
  app.mount(blogElement);
}

// Setup for Inertia pages
const appName = import.meta.env.VITE_APP_NAME || 'Blog Module';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) });
    app.config.globalProperties.$route = route;
    app.use(plugin)
      .use(ZiggyVue)
      .use(PrimeVue, {
        theme: {
          preset: Aura,
          options: {
            prefix: 'p',
            darkModeSelector: '.dark',
            cssLayer: false
          }
        }
      })
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});

// Export components for use in other parts of the application
export { BlogComponent };
