import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/site.js',
                'resources/css/site.css'
            ],
            refresh: true,
        }),
    ],
});
