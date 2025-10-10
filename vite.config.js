import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/it.css',   // ✅ ubah dari tiket.css ke it.css
                'resources/js/it.js',     // ✅ sudah bener pakai it.js
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
