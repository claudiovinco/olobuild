import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  plugins: [vue()],
  define: { 'process.env': {} },
  build: {
    outDir: 'assets',
    emptyOutDir: false,
    rollupOptions: {
      input: path.resolve(__dirname, 'src/admin-settings.js'),
      output: {
        format: 'iife',
        name: 'OloAdminSettings',
        entryFileNames: 'js/admin-settings.js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'css/admin-settings.css';
          }
          return 'js/[name].[ext]';
        },
        inlineDynamicImports: true,
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src'),
    },
  },
});
