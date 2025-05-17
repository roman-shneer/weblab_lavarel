import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.ts',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: true,
                },
            },
        }),
        /* basicSsl()*/
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            ziggy: path.resolve('vendor/tightenco/ziggy/dist'),
        },
    },
    server: {
        // cors: true, // Enable CORS
        //origin: 'https://weblab_lavarel.test:5173', // Set the origin      

    },
});
