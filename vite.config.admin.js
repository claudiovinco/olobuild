import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import { execFileSync } from 'child_process';

// Rigenera l'indice di ricerca dei campi Configurazione prima di ogni build
// admin, così src/config/settingsSearchIndex.js non deriva mai dai tab reali.
const settingsSearchIndex = {
  name: 'olo-settings-search-index',
  buildStart() {
    execFileSync(process.execPath, [path.resolve(__dirname, 'scripts/build-settings-search-index.cjs')], { stdio: 'inherit' });
  },
};

export default defineConfig({
  plugins: [settingsSearchIndex, vue()],
  define: {
    'process.env': {},
  },
  build: {
    outDir: 'assets',
    emptyOutDir: false,
    assetsInlineLimit: 8192,
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
