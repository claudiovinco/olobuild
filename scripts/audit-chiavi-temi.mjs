#!/usr/bin/env node
/**
 * audit-chiavi-temi — le impostazioni dei temi che un utente non può vedere né cambiare dal pannello.
 *
 * PERCHÉ (utente, 23 nov 2026): «ripassare i siti gratuiti (i temi) alla ricerca di usi particolari delle tile che
 * un utente normale non riuscirebbe a comporre… se la tile mostra un video, questo deve essere collegato in una
 * sezione video e non apparire dal nulla». I temi sono scritti da script (scripts/temi/): possono mettere nelle
 * impostazioni chiavi che nessun controllo dell'inspector scrive, o media in campi che con quei valori restano
 * nascosti da una condizione.
 *
 * COME: carica con Vite i config VERI delle tile (src/config/elementRegistry.js) e il valutatore delle condizioni,
 * poi passa ogni tile di ogni pagina dei temi (di default solo quelli della Free) e segnala:
 *   - «senza controllo»: chiavi di settings che nessun campo (né le sue varianti: hover, dispositivi, legacy) scrive;
 *   - «media senza controllo» / «media nascosto»: un video o un'immagine in una chiave senza controllo, o in un campo
 *     che con le impostazioni del tema non si vede (anche dentro le voci dei ripetitori).
 * Lo Slider Pro ha un editor suo (slide e livelli): qui si guardano solo le sue impostazioni generali.
 *
 * Uso: node scripts/audit-chiavi-temi.mjs [--tutti] [--json file] [--tipo nome]
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { createServer } from 'vite';

const HERE = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(HERE, '..');
const argv = process.argv.slice(2);
const TUTTI = argv.includes('--tutti');
const JSON_OUT = argv.includes('--json') ? argv[argv.indexOf('--json') + 1] : '';
const SOLO_TIPO = argv.includes('--tipo') ? argv[argv.indexOf('--tipo') + 1] : '';

// I config girano nel browser: per caricarli qui basta un window minimo (niente tile esterne, niente traduzioni).
globalThis.window = globalThis.window || { oloData: {}, location: { href: '', search: '' }, matchMedia: () => ({ matches: false }) };
globalThis.document = globalThis.document || { documentElement: { lang: 'it' }, querySelector: () => null, createElement: () => ({ style: {} }) };

const server = await createServer({
  root: ROOT, configFile: path.join(ROOT, 'vite.config.js'), logLevel: 'error',
  server: { middlewareMode: true, hmr: false }, appType: 'custom', optimizeDeps: { noDiscovery: true, include: [] },
});
let reg, cond;
try {
  reg = await server.ssrLoadModule('/src/config/elementRegistry.js');
  cond = await server.ssrLoadModule('/src/utils/fieldCondition.js');
} catch (e) {
  console.error('non riesco a caricare i config:', e.message);
  await server.close();
  process.exit(1);
}

const VARIANTI = ['_hover', '_hover_duration', '_tablet', '_mobile', '_tablet_landscape', '_mobile_landscape', '_notebook', '_desktop', '_unit', '_linked', '_lock'];
// Chiavi che ogni tile può avere senza un campo suo (gestite dal builder o da gruppi comuni): lo Sfondo dell'elemento
// (bg) lo aggiunge l'inspector a tutte le tile, non i config.
// Chiavi scritte da un editor su misura e non da un campo con la loro `key`: la finestra dei dati della Tabella
// (FieldTabella → col_alignments), l'editor dello Slider Pro (altezza e sfondo comune a tutte le slide).
const GESTITE = { table: ['col_alignments'], proslider: ['globalBackground', 'height'] };
const COMUNI = new Set(['preset', 'typography_preset', 'stile', '_v', 'version', 'bg']);

// Un valore che cambia qualcosa: non vuoto, non spento, non «nessuno».
function significativo(v) {
  if (v === null || v === undefined || v === '' || v === false || v === 0 || v === '0') return false;
  if (Array.isArray(v)) return v.length > 0;
  if (typeof v === 'object') {
    if (v.type === 'none') return false;
    return Object.values(v).some((x) => significativo(x));
  }
  return true;
}

// I renderer PHP: per ogni tipo il file della sua classe (protected $type = '…'), per sapere se una chiave si legge.
const RENDERER = {};
for (const f of fs.readdirSync(path.join(ROOT, 'includes/tiles'))) {
  if (!f.endsWith('.php')) continue;
  const src = fs.readFileSync(path.join(ROOT, 'includes/tiles', f), 'utf8');
  const m = src.match(/protected\s+\$type\s*=\s*'([^']+)'/);
  if (m) RENDERER[m[1]] = src;
}
/** Il valore è quello che la tile avrebbe comunque (predefinito del config o del PHP): allora non cambia niente. */
function ugualePredefinito(tipo, def, key, v) {
  const norm = (x) => (x && typeof x === 'object') ? JSON.stringify(x) : String(x ?? '').trim().toLowerCase();
  if (def && def.defaults && key in def.defaults && norm(def.defaults[key]) === norm(v)) return true;
  const src = RENDERER[tipo];
  if (!src || (v && typeof v === 'object')) return false;
  const esc = key.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  const m = src.match(new RegExp(`['"]${esc}['"]\\s*=>\\s*('([^']*)'|"([^"]*)"|(-?\\d+(?:\\.\\d+)?)|true|false)`, 'i'));
  if (!m) return false;
  const pd = m[2] ?? m[3] ?? m[4] ?? m[1];
  return norm(pd) === norm(v) || (pd === 'true' && v === true) || (pd === 'false' && v === false);
}
function letta(tipo, key) {
  const src = RENDERER[tipo];
  if (!src) return null;
  return src.includes(`'${key}'`) || src.includes(`"${key}"`);
}

const cacheChiavi = new Map();
function campi(def) {
  return [...(def.fields || []), ...(def.styleFields || [])];
}
function chiaviControllate(def) {
  if (cacheChiavi.has(def.type)) return cacheChiavi.get(def.type);
  const set = new Set([...COMUNI, ...(GESTITE[def.type] || [])]);
  const add = (f) => {
    if (!f || typeof f !== 'object') return;
    if (typeof f.key === 'string' && f.key) {
      set.add(f.key);
      VARIANTI.forEach((v) => set.add(f.key + v));
    }
    if (f.keys && typeof f.keys === 'object') Object.values(f.keys).forEach((k) => { if (typeof k === 'string') { set.add(k); VARIANTI.forEach((v) => set.add(k + v)); } });
    // le chiavi accessorie dei campi: hoverKey/hoverDurationKey di withHover(), e ogni altra proprietà *Key
    for (const [prop, val] of Object.entries(f)) if (/Key$/.test(prop) && typeof val === 'string' && val) set.add(val);
    if (f.legacyDeviceKeys && typeof f.legacyDeviceKeys === 'object') Object.values(f.legacyDeviceKeys).forEach((k) => typeof k === 'string' && set.add(k));
    const leg = f.legacyKeys;
    if (Array.isArray(leg)) leg.forEach((k) => typeof k === 'string' && set.add(k));
    else if (leg && typeof leg === 'object') Object.values(leg).flat().forEach((k) => typeof k === 'string' && set.add(k));
    for (const sub of ['fields', 'children', 'controls']) if (Array.isArray(f[sub])) f[sub].forEach(add);
  };
  campi(def).forEach(add);
  // le chiavi vecchie che bgMigrate porta nel pannello dello sfondo al caricamento: le governa quel pannello
  const mig = def.bgMigrate ? (Array.isArray(def.bgMigrate) ? def.bgMigrate : [def.bgMigrate]) : [];
  for (const m of mig) for (const [prop, val] of Object.entries(m)) if (prop !== 'target' && typeof val === 'string' && val) set.add(val);
  cacheChiavi.set(def.type, set);
  return set;
}

const RE_VIDEO = /\.(mp4|webm|mov|m4v)(\?|$)|youtube\.com|youtu\.be|vimeo\.com/i;
const RE_IMG = /\.(jpe?g|png|webp|gif|avif|svg)(\?|$)/i;
function media(v, acc = []) {
  if (typeof v === 'string') {
    if (RE_VIDEO.test(v)) acc.push(['video', v]);
    else if (RE_IMG.test(v) && /\/|\{\{/.test(v)) acc.push(['immagine', v]);
  } else if (Array.isArray(v)) v.forEach((x) => media(x, acc));
  else if (v && typeof v === 'object') Object.values(v).forEach((x) => media(x, acc));
  return acc;
}

/** Il campo di una chiave e se si vede con queste impostazioni (anche la condizione del separatore che lo precede). */
function campoVisibile(def, key, settings, lista) {
  let sep = null;
  for (const f of lista || campi(def)) {
    if (!f) continue;
    if (f.type === 'separator') { sep = f; continue; }
    const tocca = f.key === key || (f.keys && Object.values(f.keys).includes(key))
      || (Array.isArray(f.legacyKeys) && f.legacyKeys.includes(key))
      || VARIANTI.some((v) => f.key && f.key + v === key);
    if (!tocca) continue;
    const ok = (!sep || cond.isFieldVisible(sep, settings)) && cond.isFieldVisible(f, settings);
    return { campo: f, visibile: ok };
  }
  return null;
}

const free = new Set(JSON.parse(fs.readFileSync(path.join(ROOT, 'assets/data/temi-free.json'), 'utf8')).free);
const TEMI = path.join(ROOT, 'assets/data/themes');
const perTipo = {};
const problemi = [];
let nTile = 0;

function giro(nodi, tema, pagina) {
  for (const x of Array.isArray(nodi) ? nodi : []) {
    if (!x || typeof x !== 'object') continue;
    const tipo = x.type;
    if (tipo && !['section', 'row', 'column', 'container'].includes(tipo) && (!SOLO_TIPO || SOLO_TIPO === tipo)) {
      nTile++;
      const def = reg.getElementDef(tipo);
      const s = x.settings || {};
      const pt = perTipo[tipo] || (perTipo[tipo] = { tile: 0, temi: new Set(), senza: {}, senzaDef: !def });
      pt.tile++; pt.temi.add(tema);
      if (def) {
        const ctrl = chiaviControllate(def);
        for (const [k, v] of Object.entries(s)) {
          const m = media(v);
          if (tipo === 'proslider' && (k === 'slides' || k === 'globalLayers')) continue;
          if (!ctrl.has(k)) {
            const e = pt.senza[k] || (pt.senza[k] = { n: 0, temi: new Set(), esempio: v, pieni: 0, temiPieni: new Set(), esempioPieno: undefined });
            e.n++; e.temi.add(tema);
            if (significativo(v) && !ugualePredefinito(tipo, def, k, v)) { e.pieni++; e.temiPieni.add(tema); if (e.esempioPieno === undefined) e.esempioPieno = v; }
            m.forEach(([genere, url]) => problemi.push({ tema, pagina, tipo, id: x.id, chiave: k, genere, url, motivo: 'senza controllo' }));
            continue;
          }
          if (m.length) {
            const c = campoVisibile(def, k, { ...(def.defaults || {}), ...s });
            if (c && !c.visibile) m.forEach(([genere, url]) => problemi.push({ tema, pagina, tipo, id: x.id, chiave: k, genere, url, motivo: 'campo nascosto (' + (c.campo.label || c.campo.key) + ')' }));
            // voci dei ripetitori
            if (c && c.campo.type === 'content-items' && Array.isArray(v)) {
              const itemKeys = new Set();
              (c.campo.itemFields || []).forEach((f) => { if (f.key) { itemKeys.add(f.key); VARIANTI.forEach((vv) => itemKeys.add(f.key + vv)); } if (f.keys) Object.values(f.keys).forEach((kk) => itemKeys.add(kk)); });
              v.forEach((voce, i) => {
                if (!voce || typeof voce !== 'object') return;
                for (const [ik, iv] of Object.entries(voce)) {
                  const mm = media(iv);
                  if (!mm.length) continue;
                  if (!itemKeys.has(ik)) mm.forEach(([genere, url]) => problemi.push({ tema, pagina, tipo, id: x.id, chiave: k + '[' + i + '].' + ik, genere, url, motivo: 'voce: senza controllo' }));
                  else {
                    const cc = campoVisibile(def, ik, { ...(c.campo.newItemDefaults || {}), ...voce }, c.campo.itemFields);
                    if (cc && !cc.visibile) mm.forEach(([genere, url]) => problemi.push({ tema, pagina, tipo, id: x.id, chiave: k + '[' + i + '].' + ik, genere, url, motivo: 'voce: campo nascosto (' + (cc.campo.label || cc.campo.key) + ')' }));
                  }
                }
              });
            }
          }
        }
      }
    }
    giro(x.children, tema, pagina);
  }
}

for (const tema of fs.readdirSync(TEMI).sort()) {
  if (!TUTTI && !free.has(tema)) continue;
  const dir = path.join(TEMI, tema);
  if (!fs.statSync(dir).isDirectory()) continue;
  for (const f of fs.readdirSync(dir)) {
    if (!f.endsWith('.json') || f === 'theme.json') continue;
    let j;
    try { j = JSON.parse(fs.readFileSync(path.join(dir, f), 'utf8')); } catch (e) { continue; }
    giro(Array.isArray(j) ? j : (j.content || j.sections || []), tema, f.replace(/\.json$/, ''));
  }
}

await server.close();

const righe = [];
righe.push(`Tile passate: ${nTile} (${TUTTI ? 'tutti i temi' : 'temi della Free'})`);
const tipi = Object.entries(perTipo).sort((a, b) => b[1].tile - a[1].tile);
const senzaDef = tipi.filter(([, p]) => p.senzaDef).map(([t]) => t);
if (senzaDef.length) righe.push('Tipi senza config: ' + senzaDef.join(', '));
// Contano le chiavi che CAMBIANO la pagina: valore pieno in qualche tile e lette dal renderer PHP. Le altre sono resti
// innocui (vuote o mai lette) e finiscono solo nel conteggio.
righe.push('\n== Impostazioni senza controllo che cambiano la pagina (valore pieno e diverso dal predefinito, lette dal PHP) ==');
let innocue = 0;
for (const [tipo, p] of tipi) {
  const ks = Object.entries(p.senza).filter(([k, e]) => {
    const ok = e.pieni > 0 && letta(tipo, k) !== false;
    if (!ok) innocue++;
    return ok;
  }).sort((a, b) => b[1].pieni - a[1].pieni);
  if (!ks.length) continue;
  righe.push(`${tipo} (${p.tile} tile in ${p.temi.size} temi)${RENDERER[tipo] ? '' : ' [renderer non trovato]'}:`);
  for (const [k, e] of ks) righe.push(`   ${k.padEnd(30)} ${String(e.pieni).padStart(4)} tile in ${String(e.temiPieni.size).padStart(3)} temi · ${JSON.stringify(e.esempioPieno).slice(0, 90)}`);
}
righe.push(`(chiavi senza controllo ma innocue, vuote o mai lette: ${innocue})`);
righe.push('\n== Media senza controllo o nascosti ==');
const perMotivo = {};
for (const pr of problemi) {
  const k = `${pr.tipo} · ${pr.chiave.replace(/\[\d+\]/, '[]')} · ${pr.genere} · ${pr.motivo}`;
  (perMotivo[k] = perMotivo[k] || []).push(pr);
}
for (const [k, l] of Object.entries(perMotivo).sort((a, b) => b[1].length - a[1].length)) {
  righe.push(`${String(l.length).padStart(4)}  ${k}  (es. ${l[0].tema}/${l[0].pagina})`);
}
console.log(righe.join('\n'));
if (JSON_OUT) {
  const dump = { tipi: Object.fromEntries(tipi.map(([t, p]) => [t, { tile: p.tile, temi: [...p.temi], senza: Object.fromEntries(Object.entries(p.senza).map(([k, e]) => [k, { n: e.n, temi: [...e.temi], esempio: e.esempio }])) }])), problemi };
  fs.writeFileSync(JSON_OUT, JSON.stringify(dump, null, 1));
}
