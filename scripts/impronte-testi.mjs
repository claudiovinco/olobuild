// Impronte dei testi «di partenza» per il controllo della pagina allegata ai moduli (Olobuild_Page_Check,
// includes/class-page-check.php): chi si candida con una pagina fatta di tile lasciate come arrivano non passa.
//
// Testi raccolti: la libreria (blocchi e pagine), i modelli di partenza, i 52 temi, i valori iniziali delle tile
// (stringhe dei config in src/config/elements, con le traduzioni dei .po del sito: nella sandbox la tile appena
// trascinata parla la lingua di chi la usa) e la pagina iniziale della sandbox (scripts/testi-sandbox.json).
// Ogni testo di almeno due parole → coppia o terne di parole consecutive → crc32 → elenco ordinato e senza doppioni
// in assets/data/impronte-testi.bin (uint32 little-endian). La normalizzazione è la STESSA di
// Olobuild_Page_Check::parole()/terne(): se cambia qui, cambia là.
//
// Gira da sola a ogni build (plugin in vite.config.js) o a mano: node scripts/impronte-testi.mjs
import fs from 'node:fs';
import path from 'node:path';
import zlib from 'node:zlib';
import { fileURLToPath } from 'node:url';

export function parole(testo) {
  const s = String(testo)
    .replace(/<[^>]*>/g, ' ')
    .replace(/&[#a-z0-9]+;/gi, ' ')
    .toLowerCase()
    .normalize('NFC')
    .replace(/[^\p{L}\p{N}]+/gu, ' ')
    .trim();
  return s === '' ? [] : s.split(' ');
}

export function terne(w) {
  if (w.length < 2) return [];
  if (w.length === 2) return [w[0] + ' ' + w[1]];
  const out = [];
  for (let i = 0; i + 2 < w.length; i++) out.push(w[i] + ' ' + w[i + 1] + ' ' + w[i + 2]);
  return out;
}

// Tutte le stringhe di un valore JSON; quelle che sono a loro volta JSON (content salvato come testo) si aprono.
function stringheJson(v, out) {
  if (typeof v === 'string') {
    const t = v.trim();
    if ((t.startsWith('[') || t.startsWith('{')) && t.length > 20) {
      try { stringheJson(JSON.parse(t), out); return; } catch { /* testo normale */ }
    }
    out.push(v);
  } else if (Array.isArray(v)) {
    for (const x of v) stringheJson(x, out);
  } else if (v && typeof v === 'object') {
    for (const x of Object.values(v)) stringheJson(x, out);
  }
}

// Le stringhe letterali di un sorgente JS (config delle tile): defaults, partenza, voci dei ripetitori.
function stringheJs(src, out) {
  const re = /'((?:\\.|[^'\\\n])*)'|"((?:\\.|[^"\\\n])*)"|`((?:\\.|[^`\\$])*)`/g;
  let m;
  while ((m = re.exec(src))) {
    const s = m[1] ?? m[2] ?? m[3] ?? '';
    out.push(s.replace(/\\n/g, ' ').replace(/\\(.)/g, '$1'));
  }
}

// msgid e msgstr di un .po (anche su più righe).
function stringhePo(src, out) {
  let cur = null;
  const chiudi = () => { if (cur !== null) out.push(cur); cur = null; };
  for (const riga of src.split(/\r?\n/)) {
    const a = riga.match(/^(?:msgid|msgstr(?:\[\d+\])?)\s+"(.*)"\s*$/);
    const c = riga.match(/^"(.*)"\s*$/);
    if (a) { chiudi(); cur = a[1]; } else if (c && cur !== null) { cur += c[1]; } else { chiudi(); }
  }
  chiudi();
  for (let i = 0; i < out.length; i++) out[i] = out[i].replace(/\\n/g, ' ').replace(/\\(.)/g, '$1');
}

export function generaImpronte(radice, { log = console.log } = {}) {
  const t0 = Date.now();
  const testi = [];
  const fonti = {};
  const conta = (nome, prima) => { fonti[nome] = (fonti[nome] || 0) + (testi.length - prima); };
  const leggiJson = (file, nome) => {
    if (!fs.existsSync(file)) return;
    const prima = testi.length;
    try { stringheJson(JSON.parse(fs.readFileSync(file, 'utf8')), testi); } catch { /* file non JSON: saltato */ }
    conta(nome, prima);
  };

  const dati = path.join(radice, 'assets', 'data');
  for (const f of ['template-library.json', 'template-library-pagine.json', 'starter-templates.json', 'tpl-cover.json', 'tpl-homepage.json', 'tpl-landing.json']) {
    leggiJson(path.join(dati, f), 'libreria e modelli');
  }
  const temi = path.join(dati, 'themes');
  if (fs.existsSync(temi)) {
    for (const tema of fs.readdirSync(temi)) {
      const dir = path.join(temi, tema);
      if (!fs.statSync(dir).isDirectory()) continue;
      for (const f of fs.readdirSync(dir)) if (f.endsWith('.json')) leggiJson(path.join(dir, f), 'temi');
    }
  }
  const elementi = path.join(radice, 'src', 'config', 'elements');
  if (fs.existsSync(elementi)) {
    const prima = testi.length;
    for (const f of fs.readdirSync(elementi)) if (f.endsWith('.js')) stringheJs(fs.readFileSync(path.join(elementi, f), 'utf8'), testi);
    conta('tile (config)', prima);
  }
  for (const loc of ['en_US', 'en_GB', 'de_DE', 'fr_FR', 'es_ES']) {
    const f = path.join(radice, 'languages', `olobuild-${loc}.po`);
    if (!fs.existsSync(f)) continue;
    const prima = testi.length;
    stringhePo(fs.readFileSync(f, 'utf8'), testi);
    conta('traduzioni (.po)', prima);
  }
  leggiJson(path.join(radice, 'scripts', 'testi-sandbox.json'), 'pagina iniziale della sandbox');

  const insieme = new Set();
  for (const t of testi) {
    const w = parole(t);
    if (w.length < 2) continue;
    for (const x of terne(w)) insieme.add(zlib.crc32(Buffer.from(x, 'utf8')) >>> 0);
  }
  const elenco = Uint32Array.from(insieme).sort();
  const buf = Buffer.alloc(elenco.length * 4);
  elenco.forEach((v, i) => buf.writeUInt32LE(v, i * 4));
  const out = path.join(dati, 'impronte-testi.bin');
  const prima = fs.existsSync(out) ? fs.readFileSync(out) : null;
  if (!prima || !prima.equals(buf)) fs.writeFileSync(out, buf);
  log(`impronte-testi: ${elenco.length} impronte da ${testi.length} stringhe (${Object.entries(fonti).map(([k, v]) => `${k} ${v}`).join(', ')}), ${(buf.length / 1024).toFixed(0)} KB, ${Date.now() - t0} ms${prima && prima.equals(buf) ? ', invariate' : ''}`);
  return elenco.length;
}

if (process.argv[1] && fileURLToPath(import.meta.url) === path.resolve(process.argv[1])) {
  generaImpronte(path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..'));
}
