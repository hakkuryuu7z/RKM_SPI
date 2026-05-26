import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // --- TAMBAHIN BLOK SERVER INI BOLO ---
    server: {
        host: '0.0.0.0',
        cors: true,
        hmr: {
            host: '10.48.232.187', // ⚠️ GANTI pake IP address laptop lu yang didapet dari ipconfig tadi!
        },
    },
});