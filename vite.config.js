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
                'resources/css/dashboard-it.css',
                'resources/js/dashboard-it.js',
                'resources/css/manage-user.css',
                'resources/js/manage-user.js',
                'resources/css/feedbacks.css',
                'resources/js/feedbacks.js',
                'resources/css/it-ticket-index.css',
                'resources/css/it-ticket-history.css',
                'resources/js/it-ticket-history.js',
                'resources/css/change-password.css',
                'resources/js/change-password.js',
                'resources/js/auto-logout.js',
            ],
            refresh: true,
        }),
    ],
});
