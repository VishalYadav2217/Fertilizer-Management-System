import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            // Map 'jspdf' to the correct UMD file in node_modules
            'jspdf': 'jspdf/dist/jspdf.umd.min.js',
        },
    },
});