import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        // Bind to all interfaces inside the container
        host: '0.0.0.0',
        // Ensure the HMR client uses a routable host for the browser
        hmr: {
            host: 'localhost',
            protocol: 'http',
            port: Number(process.env.VITE_PORT) || 5173,
        },
    },
});
