import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/responsive.css',
                'resources/js/app.js',
                'resources/js/charts.js',
                'resources/js/resume-preview.js'
            ],
            refresh: true,
        }),
    ],
});
