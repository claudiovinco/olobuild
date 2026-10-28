import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

// Quarto bundle: la Redazione (Olobuild_Redazione, menu «Redazione» di WordPress).
// Output: assets/js/redazione.js col suo CSS dentro — NON svuota outDir (convive con builder.js e gli altri).
export default defineConfig({
  plugins: [vue()],
  define: {
    'process.env': {},
  },
  build: {
    outDir: 'assets',
    emptyOutDir: false,
    assetsInlineLimit: 8192,
    rollupOptions: {
      input: path.resolve(__dirname, 'src/redazione.js'),
      output: {
        format: 'iife',
        name: 'OloRedazione',
        entryFileNames: 'js/redazione.js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'css/redazione.css';
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
