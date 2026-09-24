#!/usr/bin/env node
/**
 * esporta-default-js.mjs — i default JS delle tile, per il censimento (passo 0.1).
 *
 * Il censimento gira su mosaic, dove c'è PHP ma non Node: lì legge i $defaults delle tile
 * con get_defaults(), ma i default del config JS (quelli che normalizeNodes fonde al
 * caricamento, src/stores/treeUtils.js) li può solo ricevere da fuori. Questo script li
 * esporta dai config VERI, caricati con Vite SSR, in un JSON da copiare accanto a
 * censimento.php (`js=<file>`): così le divergenze PHP↔JS si calcolano, non si elencano
 * a mano.
 *
 * Uso (in locale, dalla radice del repo):
 *   node scripts/golden/esporta-default-js.mjs --out <file.json fuori dal repo>
 *
 * Esce: { generato, versione, tipi: { <type>: { file, defaults, spazio_raggio: [chiavi] } } }
 * `spazio_raggio` = chiavi dei campi type 'spacing' e 'border-radius' (anche negli itemFields).
 */
import fs from 'fs';
import os from 'os';
import path from 'path';
import { fileURLToPath } from 'url';

const HERE = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(HERE, '..', '..');
const ELEMENTS = path.join(ROOT, 'src/config/elements');

const argv = process.argv.slice(2);
const opz = (nome) => { const i = argv.indexOf(nome); return i >= 0 ? argv[i + 1] : undefined; };
const out = opz('--out');
if (!out) {
  console.error('Uso: node scripts/golden/esporta-default-js.mjs --out <file.json fuori dal repo>');
  process.exit(2);
}

// Il minimo del browser che i config toccano all'import (i18n, oloData).
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

// Le chiavi dei campi spazio/raggio, anche dentro gli itemFields dei ripetitori.
function chiaviSpazio(campi, acc) {
  if (!Array.isArray(campi)) return acc;
  for (const c of campi) {
    if (!c || typeof c !== 'object') continue;
    if ((c.type === 'spacing' || c.type === 'border-radius') && c.key) acc.add(c.key);
    if (Array.isArray(c.itemFields)) chiaviSpazio(c.itemFields, acc);
    if (Array.isArray(c.fields)) chiaviSpazio(c.fields, acc);
  }
  return acc;
}

const versione = (fs.readFileSync(path.join(ROOT, 'olobuild.php'), 'utf8').match(/define\(\s*'OLOBUILD_VERSION',\s*'([^']+)'/) || [])[1] || '';
const server = await createServer({
  root: ROOT,
  configFile: path.join(ROOT, 'vite.config.js'),
  logLevel: 'error',
  // Niente cache né scansione delle dipendenze dentro il repo: serve solo caricare i config.
  cacheDir: path.join(os.tmpdir(), 'olo-golden-vite'),
  optimizeDeps: { noDiscovery: true, include: [] },
  server: { middlewareMode: true, hmr: false, watch: null },
  appType: 'custom',
});
const tipi = {};
const errori = [];
try {
  for (const f of fs.readdirSync(ELEMENTS).filter((x) => x.endsWith('.js') && !x.startsWith('_')).sort()) {
    try {
      const mod = await server.ssrLoadModule('/src/config/elements/' + f);
      const def = mod.default;
      if (!def || !def.type) continue;
      const spazio = chiaviSpazio(def.fields, new Set());
      chiaviSpazio(def.styleFields, spazio);
      chiaviSpazio(def.contentFields, spazio);
      tipi[def.type] = {
        file: f,
        defaults: JSON.parse(JSON.stringify(def.defaults || {})),
        spazio_raggio: [...spazio].sort(),
      };
    } catch (e) {
      errori.push(f + ': ' + (e && e.message ? e.message : String(e)));
    }
  }
} finally {
  await server.close();
}

fs.mkdirSync(path.dirname(path.resolve(out)), { recursive: true });
fs.writeFileSync(out, JSON.stringify({ generato: new Date().toISOString(), versione, tipi, errori }, null, 1) + '\n');
console.log(`default JS di ${Object.keys(tipi).length} tipi → ${out}` + (errori.length ? ` (${errori.length} config non caricati)` : ''));
for (const e of errori.slice(0, 10)) console.log('  ' + e);
process.exit(errori.length ? 1 : 0);
