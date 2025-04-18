import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from "@tailwindcss/vite";
import { resolve } from 'node:path';
import { defineConfig } from 'vite';
import getModulePaths from './vite-module-loader';


export default defineConfig(async () => {
    const modulePaths = await getModulePaths();
    console.log('Module paths:', modulePaths);

    return {
        plugins: [
            laravel({
                input: ['resources/js/app.ts', ...modulePaths],
                ssr: 'resources/js/ssr.ts',
                refresh: true,
            }),
            tailwindcss(),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/js'),
                'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
                '@modules': path.resolve(__dirname, './Modules'),
                '@blog': path.resolve(__dirname, './Modules/Blog/Resources/js'),
                '@css': path.resolve(__dirname, './resources/css'),
            },
        },
        // Make sure all modules can access the main application's routes and components
        optimizeDeps: {
            include: ['ziggy-js'],
        },
    };
});
