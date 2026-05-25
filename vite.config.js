import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '')
    const isTunnel = env.APP_URL?.includes('mytamakikii.web.id')

    return {
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
            strictPort: true,
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
            ...(isTunnel
                ? {
                      allowedHosts: [
                          'pickuporder.mytamakikii.web.id',
                          'vite.mytamakikii.web.id',
                      ],
                      cors: true,
                      hmr: {
                          host: 'vite.mytamakikii.web.id',
                          protocol: 'wss',
                          clientPort: 443,
                      },
                  }
                : {}),
        },
    }
})