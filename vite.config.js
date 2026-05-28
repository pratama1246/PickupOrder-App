import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

const resolveDevServerHost = (env) => {
    if (env.VITE_DEV_SERVER_HOST) {
        return env.VITE_DEV_SERVER_HOST
    }

    if (env.APP_URL) {
        try {
            return new URL(env.APP_URL).hostname
        } catch {
            return '127.0.0.1'
        }
    }

    return '127.0.0.1'
}

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '')
    const devServerHost = resolveDevServerHost(env)

    return {
        plugins: [
            tailwindcss(),
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                ],
                refresh: true,
            }),
        ],

        server: {
            host: '0.0.0.0',
            port: 5173,
            strictPort: true,
            hmr: {
                host: devServerHost,
            },

            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    }
})
