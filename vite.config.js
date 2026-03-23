import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    build: {
        manifest: 'manifest.json',
        outDir: 'public/build',
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/typography.css',
                'resources/css/agenda.css',
                'resources/css/sidebar.css',
                'resources/css/sidebar-enhanced.css',
                'resources/css/dashboard.css',
                'resources/js/app.js',
                'resources/js/agenda.js',
                'resources/js/sidebar.js',
                'resources/js/vendor-alerts.js',
                'resources/js/vendor-reporting.js',
            ],
            refresh: true,
        }),
    ],
});
