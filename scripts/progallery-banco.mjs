#!/usr/bin/env node
/**
 * Banco di prova della Pro Gallery: scrive un JSON con i valori predefiniti della tile, i suoi stili predefiniti
 * (tilePresets.progallery) e le foto di partenza, così uno script PHP può costruire una pagina con una galleria per
 * ogni stile e provarla nel browser (23 nov 2026, verifica degli stili predefiniti chiesta dall'utente).
 *
 * Uso: node scripts/progallery-banco.mjs <uscita.json>
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { createServer } from 'vite';

const HERE = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(HERE, '..');
const USCITA = process.argv[2] || path.join(ROOT, 'progallery-banco.json');

globalThis.window = globalThis.window || { oloData: { pluginUrl: '{{OLOBUILD_URL}}' }, location: { href: '', search: '' }, matchMedia: () => ({ matches: false }) };
globalThis.document = globalThis.document || { documentElement: { lang: 'it' }, querySelector: () => null, createElement: () => ({ style: {} }) };

const server = await createServer({
  root: ROOT, configFile: path.join(ROOT, 'vite.config.js'), logLevel: 'error',
  server: { middlewareMode: true, hmr: false }, appType: 'custom', optimizeDeps: { noDiscovery: true, include: [] },
});
try {
  const def = (await server.ssrLoadModule('/src/config/elements/progallery.js')).default;
  const pre = (await server.ssrLoadModule('/src/config/tilePresets.js'));
  const presets = (pre.TILE_PRESETS || pre.tilePresets || pre.default || {}).progallery || {};
  const etichette = {};
  for (const f of def.styleFields) if (f.key === 'preset') for (const o of f.options) etichette[o.value] = o.label;
  fs.writeFileSync(USCITA, JSON.stringify({ defaults: def.defaults, partenza: def.partenza, presets, etichette }, null, 1));
  console.log('stili:', Object.keys(presets).length, '→', USCITA);
} finally {
  await server.close();
}
