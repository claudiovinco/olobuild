import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import fs from 'fs';

// Versione con cui il bundle viene compilato, letta dalla stessa riga che fa fede
// a runtime (OLOBUILD_VERSION in olobuild.php). Serve a staleBundleGuard: se il
// server dichiara un'altra versione, il codice in esecuzione arriva da una cache.
const pluginVersion = (() => {
  try {
    const src = fs.readFileSync(path.resolve(__dirname, 'olobuild.php'), 'utf8');
    const m = src.match(/define\(\s*'OLOBUILD_VERSION',\s*'([^']+)'\s*\)/);
    return m ? m[1] : '';
  } catch {
    return '';
  }
})();

export default defineConfig({
  plugins: [vue()],
  // base relativa: i chunk ESM e gli import dinamici si risolvono rispetto all'URL
  // dell'entry (builder.js) via import.meta.url → portabile su qualsiasi path di install WP.
  base: './',
  define: {
    'process.env': {},
    __OLO_BUNDLE_VERSION__: JSON.stringify(pluginVersion),
  },
  build: {
    outDir: 'assets',
    emptyOutDir: false,
    assetsInlineLimit: 8192,
    chunkSizeWarningLimit: 1000,
    // Un solo file CSS (assets/css/builder.css) invece dell'iniezione inline dell'IIFE:
    // con il code-splitting ESM il CSS va estratto, e l'enqueue PHP condizionale lo carica.
    cssCodeSplit: false,
    rollupOptions: {
      input: path.resolve(__dirname, 'src/main.js'),
      output: {
        // ESM al posto di IIFE: abilita il code-splitting (vendor/tiptap/icons + chunk dinamici).
        format: 'es',
        entryFileNames: 'js/builder.js',
        chunkFileNames: 'js/chunks/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'css/builder.css';
          }
          return 'js/assets/[name]-[hash][extname]';
        },
        manualChunks(id) {
          if (id.includes('node_modules')) {
            if (id.includes('@tiptap') || id.includes('prosemirror')) return 'tiptap';
            if (id.includes('chart.js')) return 'chartjs';
            return 'vendor';
          }
          // Libreria icone (Lucide ~653KB + UIkit): chunk separato e cacheabile,
          // così non resta dentro il chunk principale dell'app.
          if (
            id.includes('lucideIconsSvg') ||
            id.includes('iconsLibrary') ||
            id.includes('uikitIconsRaw') ||
            id.includes('uikitIconsSvg')
          ) {
            return 'icons';
          }
        },
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
