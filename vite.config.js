import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

/**
 * Inside DDEV the dev server must listen on all interfaces and tell the
 * browser to load assets through the project's HTTPS URL on port 5173,
 * which `.ddev/config.yaml` exposes.
 */
const ddevServer = process.env.IS_DDEV_PROJECT === 'true'
    ? {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        origin: `${process.env.DDEV_PRIMARY_URL}:5173`,
        cors: { origin: /https?:\/\/([A-Za-z0-9\-.]+)?(\.ddev\.site)(?::\d+)?$/ },
    }
    : {};

export default defineConfig({
    server: ddevServer,
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
