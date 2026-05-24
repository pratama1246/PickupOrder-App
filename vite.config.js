import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    // Load env variables
    const env = loadEnv(mode, process.cwd(), '');
    const isTunnel = env.APP_URL && env.APP_URL.includes('mytamakikii.web.id');

    return {
        plugins: [
            tailwindcss(),
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
        server: isTunnel ? {
            host: '0.0.0.0',
            port: 5173,
            cors: true,
            headers: {
                'Access-Control-Allow-Origin': '*',
            },
            hmr: {
                host: 'vite.mytamakikii.web.id',
                protocol: 'wss',
                clientPort: 443,
            },
            allowedHosts: ['vite.mytamakikii.web.id'],
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        } : {
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});