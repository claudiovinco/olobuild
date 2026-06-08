import { defineConfig } from 'vite';
import path from 'path';

// Secondo bundle: il selettore temi condiviso come libreria IIFE leggera
// (window.OloThemePicker), riusabile fuori dal builder (es. setup wizard PHP).
// Output: assets/js/theme-picker.js — NON svuota outDir (convive con builder.js).
export default defineConfig({
  define: { 'process.env': {} },
  build: {
    outDir: 'assets',
    emptyOutDir: false,
    lib: {
      entry: path.resolve(__dirname, 'src/theme-picker/standalone.js'),
      name: 'OloThemePicker',
      formats: ['iife'],
      fileName: () => 'js/theme-picker.js',
    },
    minify: 'terser',
    terserOptions: { compress: { pure_funcs: ['console.log'] } },
  },
});
