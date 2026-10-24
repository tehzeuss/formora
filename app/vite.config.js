import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 'resources/js/app.js',
                'resources/css/paginas/formularios/listar.css',
                'resources/css/paginas/formularios/editor.css',
                'resources/css/paginas/formularios/visualizar.css',
                'resources/css/paginas/formularios/compartilhamento.css',
                'resources/css/paginas/formularios/respostas.css',
                'resources/css/paginas/formularios/excluir.css',
                'resources/css/paginas/administracao.css',
                'resources/js/paginas/formularios/editor.js',
                'resources/js/paginas/formularios/visualizar.js',
                'resources/js/paginas/formularios/respostas.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
