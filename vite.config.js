import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  plugins: [vue()],
  define: {
    'process.env': {},
  },
  build: {
    outDir: 'assets',
    emptyOutDir: false,
    assetsInlineLimit: 8192,
    chunkSizeWarningLimit: 1000,
    rollupOptions: {
      input: path.resolve(__dirname, 'src/main.js'),
      output: {
        format: 'iife',
        name: 'OlobuildApp',
        entryFileNames: 'js/builder.js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'css/builder.css';
          }
          return 'js/[name].[ext]';
        },
        inlineDynamicImports: true,
      },
    },
    // Drop console.log in production (keep console.error/warn)
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: false,
        pure_funcs: ['console.log'],
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src'),
    },
  },
});
