import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/main.sass', 'resources/admin/sass/main.sass', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // Добавьте опцию для генерации manifest
    // build: {
    //     manifest: true,
    //     outDir: 'public/css/asdas',
    // }
});
