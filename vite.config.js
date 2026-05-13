import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'vite.mytamakikii.web.id',
            protocol: 'wss',
            clientPort: 443,
        },
        allowedHosts: ['vite.mytamakikii.web.id'],
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});