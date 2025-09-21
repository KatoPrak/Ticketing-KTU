import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                    'resources/css/tiket.css',
                    'resources/js/tiket.js',
                    'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
