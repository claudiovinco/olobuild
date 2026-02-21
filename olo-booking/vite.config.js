import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [vue()],
    build: {
        outDir: resolve(__dirname, 'assets'),
        emptyOutDir: false,
        rollupOptions: {
            input: resolve(__dirname, 'src/main.js'),
            output: {
                entryFileNames: 'js/manager-app.js',
                assetFileNames: (info) => {
                    if (info.name && info.name.endsWith('.css')) {
                        return 'css/manager-app.css';
                    }
                    return 'js/[name].[ext]';
                },
                manualChunks: undefined,
            },
        },
        cssCodeSplit: false,
        minify: 'esbuild',
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'src'),
        },
    },
});
