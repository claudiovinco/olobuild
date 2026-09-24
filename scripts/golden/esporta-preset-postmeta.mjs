#!/usr/bin/env node
/**
 * esporta-preset-postmeta.mjs — i casi di prova-postmeta-preset.php (scheda
 * `tile-dinamici-postmeta-chip-fatale`, ondata O0).
 *
 * Dai sorgenti VERI, caricati con Vite SSR: i 12 preset di TILE_PRESETS.postmeta come li salva il
 * builder su una tile appena inserita (default del config + `preset` + valori del preset) e 7 casi
 * limite del «Padding chip» (chip_padding a 4 lati). Ogni caso è reso anche dal gemello Vue
 * (PostmetaTile.vue con vue/server-renderer): il padding dei suoi chip è l'atteso che il PHP deve
 * dare su mosaic. Così i preset non si ricopiano a mano e il confronto è PHP ↔ Vue.
 *
 * Uso (in locale, dalla radice del repo):
 *   node scripts/golden/esporta-preset-postmeta.mjs --out <file.json fuori dal repo>
 *
 * Esce: { generato, versione, casi: { <id>: { chip_style, padding, settings } } }
 * `padding`: '-' = nessun chip; '' = chip senza padding; altrimenti lo shorthand a 4 lati.
 * Uscita 1 se mancano preset, se il Vue non rende una voce o se i chip di un caso hanno padding
 * diversi fra loro; 2 = uso.
 */
import fs from 'fs';
import os from 'os';
import path from 'path';
import { fileURLToPath } from 'url';

const HERE = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(HERE, '..', '..');

const argv = process.argv.slice(2);
const iOut = argv.indexOf('--out');
const out = iOut >= 0 ? argv[iOut + 1] : undefined;
if (!out) {
  console.error('Uso: node scripts/golden/esporta-preset-postmeta.mjs --out <file.json fuori dal repo>');
  process.exit(2);
}

// Il minimo del browser che i moduli toccano all'import (i18n, oloData).
const noop = () => {};
const memoria = new Map();
globalThis.window = globalThis;
globalThis.oloData = globalThis.oloData || { restUrl: '/wp-json/olobuild/v1/', nonce: '', locale: 'it_IT' };
globalThis.localStorage = globalThis.localStorage || { getItem: (k) => memoria.get(k) ?? null, setItem: (k, v) => memoria.set(k, String(v)), removeItem: (k) => memoria.delete(k) };
const nodo = () => ({ style: {}, classList: { add: noop, remove: noop, contains: () => false, toggle: noop }, setAttribute: noop, getAttribute: () => null, appendChild: noop, addEventListener: noop, removeEventListener: noop, querySelector: () => null, querySelectorAll: () => [] });
globalThis.document = globalThis.document || { documentElement: nodo(), body: nodo(), head: nodo(), createElement: nodo, getElementById: () => null, querySelector: () => null, querySelectorAll: () => [], addEventListener: noop, removeEventListener: noop, cookie: '' };
globalThis.matchMedia = globalThis.matchMedia || (() => ({ matches: false, addEventListener: noop, removeEventListener: noop, addListener: noop, removeListener: noop }));

let createServer;
try {
  ({ createServer } = await import('vite'));
} catch {
  console.error('Vite non trovato: lancia lo script dalla radice del repo, dove c\'è node_modules.');
  process.exit(2);
}

// Casi limite del «Padding chip»: [id, preset di partenza, chiavi scritte sopra].
const TUTTE = { show_date: true, show_author: true, show_categories: true, show_tags: true, show_comments_count: true, show_reading_time: true };
const LIMITE = [
  ['x-asimmetrico', 'tag-pills', { chip_padding: { top: 4, right: 12, bottom: 8, left: 12, linked: false } }],
  ['x-numero', 'tag-pills', { chip_padding: 9 }],
  ['x-vuoto', 'tag-pills', { chip_padding: {} }], // oggetto senza lati = non impostato: valgono chip_padding_y/x
  ['x-negativo', 'brutalist-stamp', { chip_padding: { top: -3, right: 5, bottom: -1, left: 5 } }],
  ['x-zeri', 'sticker-scrap', { chip_padding: { top: 0, right: 0, bottom: 0, left: 0, linked: true } }],
  ['x-tutto-chip-impilato', 'tilt-3d', { ...TUTTE, layout: 'stacked' }],
  ['x-tutto-none', 'editorial-classic', { ...TUTTE, icon_style: 'before' }],
];

// Un modulo virtuale: componente, vue e vue/server-renderer dallo stesso grafo di Vite.
const VIRTUALE = 'virtual:olo-esporta-postmeta';
const CODICE = `
import { createSSRApp, h } from 'vue';
import { renderToString } from 'vue/server-renderer';
import PostmetaTile from '@/components/Tiles/PostmetaTile.vue';
export { TILE_PRESETS } from '@/config/tilePresets.js';
export { default as config } from '@/config/elements/postmeta.js';
export const rendi = (settings) => renderToString(createSSRApp({ render: () => h(PostmetaTile, { settings }) }));
`;

// Il padding dei chip nell'HTML del Vue: '-' senza chip, '' chip senza padding.
function paddingVue(html) {
  // Vue scrive le classi nell'ordine che vuole («is-chip olo-postmeta-item»): si leggono come insieme.
  const voci = [...html.matchAll(/<span class="([^"]*)" style="([^"]*)"/g)]
    .map((m) => ({ classi: m[1].split(/\s+/), stile: m[2] }))
    .filter((v) => v.classi.includes('olo-postmeta-item'));
  if (!voci.length) throw new Error('il Vue non ha reso nessuna voce');
  const chip = voci.filter((v) => v.classi.includes('is-chip'));
  if (!chip.length) return '-';
  if (chip.length !== voci.length) throw new Error(`chip ${chip.length} su ${voci.length} voci`);
  const pad = [...new Set(chip.map((v) => ((v.stile.match(/(?:^|;)padding:([^;]*)/) || [])[1] || '').trim()))];
  if (pad.length !== 1) throw new Error('padding diversi fra i chip: ' + pad.join(' | '));
  return pad[0];
}

const versione = (fs.readFileSync(path.join(ROOT, 'olobuild.php'), 'utf8').match(/define\(\s*'OLOBUILD_VERSION',\s*'([^']+)'/) || [])[1] || '';
const server = await createServer({
  root: ROOT,
  configFile: path.join(ROOT, 'vite.config.js'),
  logLevel: 'error',
  // Niente cache né scansione delle dipendenze dentro il repo.
  cacheDir: path.join(os.tmpdir(), 'olo-golden-vite'),
  optimizeDeps: { noDiscovery: true, include: [] },
  // ws: false, se no Vite apre comunque il WebSocket sulla 24678 (anche con hmr: false) e, se la
  // porta è occupata, stampa in rosso un errore che non ferma niente.
  server: { middlewareMode: true, hmr: false, ws: false, watch: null },
  appType: 'custom',
  plugins: [{
    name: 'olo-esporta-postmeta',
    resolveId: (id) => (id === VIRTUALE ? '\0' + VIRTUALE : null),
    load: (id) => (id === '\0' + VIRTUALE ? CODICE : null),
  }],
});
const casi = {};
const errori = [];
try {
  const { TILE_PRESETS, config, rendi } = await server.ssrLoadModule(VIRTUALE);
  const preset = TILE_PRESETS.postmeta || {};
  // Come applyTilePresetTheme su una tile nuova: default del config, poi `preset` e i suoi valori.
  const salvato = (id, sopra = {}) => JSON.parse(JSON.stringify({ ...config.defaults, preset: id, ...preset[id], ...sopra }));
  const elenco = Object.keys(preset).map((id) => [id, salvato(id)]);
  for (const [id, base, sopra] of LIMITE) {
    if (!preset[base]) errori.push(`${id}: preset di partenza ${base} assente`);
    else elenco.push([id, salvato(base, sopra)]);
  }
  if (Object.keys(preset).length !== 12) errori.push(`preset di postmeta: ${Object.keys(preset).length} invece di 12`);
  for (const [id, settings] of elenco) {
    try {
      casi[id] = { chip_style: settings.chip_style || 'none', padding: paddingVue(await rendi(settings)), settings };
    } catch (e) {
      errori.push(`${id}: ${e && e.message ? e.message : String(e)}`);
    }
  }
} finally {
  await server.close();
}

fs.mkdirSync(path.dirname(path.resolve(out)), { recursive: true });
fs.writeFileSync(out, JSON.stringify({ generato: new Date().toISOString(), versione, casi }, null, 1) + '\n');
for (const [id, c] of Object.entries(casi)) console.log(`${id.padEnd(24)} ${c.chip_style.padEnd(8)} ${c.padding === '' ? '(chip senza padding)' : c.padding}`);
console.log(`${Object.keys(casi).length} casi → ${out}` + (errori.length ? ` · ${errori.length} errori` : ''));
for (const e of errori) console.log('  ' + e);
process.exit(errori.length ? 1 : 0);
