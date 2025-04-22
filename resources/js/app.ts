import '@css/app.css';
import 'primeicons/primeicons.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue, route } from 'ziggy-js';
import { initializeTheme } from '@/composables/useAppearance';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import { resolveModulePage } from '@/utils/modulePageResolver';
import DialogService from 'primevue/dialogservice';
import ToastService from 'primevue/toastservice';

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
    resolve: (name) => resolveModulePage(name),
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
        
        // Add DialogService and ToastService
        app.use(DialogService);
        app.use(ToastService);

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

initializeTheme();
