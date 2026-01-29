import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/user.css',
                'resources/css/list-tiket.css',
                'resources/css/it.css',
                'resources/css/app.css',
                'resources/css/admin.css',

                'resources/js/user.js',
                'resources/js/ticket-detail-handler.js',
                'resources/js/staff.js',
                'resources/js/list-tiket.js',
                'resources/js/it.js', 
                'resources/js/bootstrap.js',
                'resources/js/app.js',
                'resources/js/admin.js',
            ],
            refresh: true,
        }),
    ],
});
