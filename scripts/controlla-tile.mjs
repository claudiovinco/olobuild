#!/usr/bin/env node
/**
 * controlla-tile — prima passata automatica sul controllo di UNA tile (o di qualcuna), prima di un video di
 * «Una tile in un minuto» o di una rifinitura. Non sostituisce lo sguardo sulla tile vera: indica dove guardare.
 *
 *   node scripts/controlla-tile.mjs accordion form osmmap
 *
 * Per ogni campo dell'inspector (anche dentro i ripetitori) segnala:
 *   - SCELTA   un campo di testo che sceglie fra valori fissi (il renderer lo confronta con parole fisse, o
 *              l'etichetta/il valore dicono posizione, effetto, modo…): va un menu a tendina;
 *   - NUMERO   un campo di testo con un numero: va un numero o un cursore con l'unità;
 *   - COLORE   un campo di testo con un colore: va il selettore di colore (token);
 *   - LINK     un indirizzo in un campo di testo semplice: va il selettore di link;
 *   - FANTASMA una chiave che il renderer PHP della tile non nomina mai (controllo che forse non agisce);
 *   - HEX      colori scritti a mano nel renderer (fuori dai commenti).
 * Elenca anche le chiavi lette dal PHP che l'inspector non mostra (funzioni nascoste o resti).
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const HERE = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(HERE, '..');
const ELEMENTS = path.join(ROOT, 'src/config/elements');
const TILES = path.join(ROOT, 'includes/tiles');
const BS = String.fromCharCode(92);

function matchBrace(src, i) {
  let depth = 0, str = null;
  for (let j = i; j < src.length; j++) {
    const c = src[j];
    if (str) { if (c === BS) { j++; continue; } if (c === str) str = null; continue; }
    if (c === "'" || c === '"' || c === '`') { str = c; continue; }
    if (c === '/' && src[j + 1] === '/') { while (j < src.length && src[j] !== '\n') j++; continue; }
    if (c === '/' && src[j + 1] === '*') { j += 2; while (j < src.length && !(src[j] === '*' && src[j + 1] === '/')) j++; j++; continue; }
    if (c === '{') depth++;
    else if (c === '}') { depth--; if (depth === 0) return j; }
  }
  return -1;
}
function allObjects(src, acc = []) {
  let i = 0, str = null;
  while (i < src.length) {
    const c = src[i];
    if (str) { if (c === BS) { i += 2; continue; } if (c === str) str = null; i++; continue; }
    if (c === "'" || c === '"' || c === '`') { str = c; i++; continue; }
    if (c === '/' && src[i + 1] === '/') { while (i < src.length && src[i] !== '\n') i++; continue; }
    if (c === '/' && src[i + 1] === '*') { i += 2; while (i < src.length && !(src[i] === '*' && src[i + 1] === '/')) i++; i += 2; continue; }
    if (c === '{') {
      const j = matchBrace(src, i);
      if (j < 0) { i++; continue; }
      acc.push(src.slice(i, j + 1));
      allObjects(src.slice(i + 1, j), acc);
      i = j + 1;
      continue;
    }
    i++;
  }
  return acc;
}
function topProp(body, name) {
  const inner = body.slice(1, -1);
  let depth = 0, str = null;
  for (let j = 0; j < inner.length; j++) {
    const c = inner[j];
    if (str) { if (c === BS) { j++; continue; } if (c === str) str = null; continue; }
    if (c === "'" || c === '"' || c === '`') { str = c; continue; }
    if (c === '{' || c === '[' || c === '(') { depth++; continue; }
    if (c === '}' || c === ']' || c === ')') { depth--; continue; }
    if (depth !== 0) continue;
    const m = inner.slice(j).match(new RegExp('^' + name + String.raw`\s*:\s*`));
    if (m && (j === 0 || /[{,\s]/.test(inner[j - 1]))) {
      const rest = inner.slice(j + m[0].length);
      const q = rest[0];
      if (q === "'" || q === '"') return rest.slice(1, rest.indexOf(q, 1));
      const t = /^t\(\s*'((?:[^'\\]|\\.)*)'/.exec(rest);
      if (t) return t[1];
      const e = rest.search(/[,\n}]/);
      return rest.slice(0, e < 0 ? rest.length : e).trim();
    }
  }
  return null;
}
const senzaCommenti = (php) => php.replace(/\/\*[\s\S]*?\*\//g, '').replace(/(^|[^:'"])\/\/.*$/gm, '$1').replace(/^\s*#.*$/gm, '');

// i file PHP che, oltre alla classe della tile, leggono chiavi comuni a tutte
const COMUNI = ['class-tile-base.php', 'class-olox-base.php'].map((f) => path.join(TILES, f))
  .concat([path.join(ROOT, 'includes/class-tile-utils.php'), path.join(ROOT, 'includes/class-frontend-renderer.php')])
  .filter((f) => fs.existsSync(f)).map((f) => senzaCommenti(fs.readFileSync(f, 'utf8'))).join('\n');

const TESTO_TIPI = new Set(['text', 'input', 'string']);
const PAROLE_SCELTA = /posizion|allinea|direzion|effett|animaz|modalit|^tipo|unit[aà]|lato|ordine|formato|layout|variant|trigger|orientam|transizion|easing|andamento|disposizion|comportament|aspetto|stile\b/i;
const VALORI_SCELTA = /^(left|right|center|centre|top|bottom|middle|start|end|fade|slide|zoom|flip|none|auto|horizontal|vertical|row|column|asc|desc|cover|contain|fill|inline|block|grid|list|small|medium|large|sm|md|lg|xl|light|dark|solid|outline|ghost|soft|minimal|default|hover|click|scroll|load|always|never|linear|ease|ease-in|ease-out|ease-in-out|true|false|yes|no|sinistra|destra|centro|alto|basso)$/i;

function campiDi(file) {
  const src = fs.readFileSync(path.join(ELEMENTS, file + '.js'), 'utf8');
  const out = [];
  for (const body of allObjects(src)) {
    if (/(^|[{,\s])(fields|styleFields|contentFields)\s*:/.test(body) && !/(^|[{,\s])key\s*:/.test(body.slice(0, 200))) continue;
    const key = topProp(body, 'key');
    const type = topProp(body, 'type');
    if (!key || !type || /^['"]?[{[]/.test(key)) continue;
    out.push({ key, type, label: topProp(body, 'label') || '', def: topProp(body, 'default'), body, ripetitore: /itemFields/.test(src.slice(0, src.indexOf(body))) && false });
  }
  return { src, campi: out };
}

function phpDi(tipo) {
  for (const f of fs.readdirSync(TILES).filter((x) => x.endsWith('.php'))) {
    const s = fs.readFileSync(path.join(TILES, f), 'utf8');
    if (new RegExp(String.raw`protected\s+\$type\s*=\s*'` + tipo.replace(/[-]/g, '\\-') + "'").test(s)) return { file: f, php: senzaCommenti(s) };
  }
  return null;
}

const tipi = process.argv.slice(2);
if (!tipi.length) { console.log('uso: node scripts/controlla-tile.mjs <tipo> [tipo…]'); process.exit(1); }
for (const tipo of tipi) {
  const { src, campi } = campiDi(tipo);
  const php = phpDi(tipo);
  console.log('\n══ ' + tipo + (php ? '  (' + php.file + ', ' + php.php.split('\n').length + ' righe)' : '  (renderer PHP NON trovato)') + ' · ' + campi.length + ' campi');
  const visti = new Set();
  const segnala = (tag, c, perche) => console.log('  ' + tag.padEnd(8) + ' ' + c.key.padEnd(28) + ' ' + c.type.padEnd(12) + ' «' + c.label + '»' + (perche ? '  ← ' + perche : ''));
  for (const c of campi) {
    if (visti.has(c.key + '|' + c.type)) continue;
    visti.add(c.key + '|' + c.type);
    const nome = c.key.replace(/[.\-[\]]/g, '.');
    if (TESTO_TIPI.has(c.type)) {
      const d = (c.def || '').replace(/^['"]|['"]$/g, '');
      const confronti = php && new RegExp(String.raw`\[\s*'` + nome + String.raw`'\s*\]\s*(?:\?\?\s*'[^']*'\s*\)?\s*)?(?:===|!==|==|!=)\s*'|in_array\(\s*\$\w+\[\s*'` + nome + "'").test(php.php);
      if (confronti) segnala('SCELTA', c, 'il renderer lo confronta con valori fissi');
      else if (VALORI_SCELTA.test(d)) segnala('SCELTA', c, 'valore predefinito «' + d + '»');
      else if (PAROLE_SCELTA.test(c.label)) segnala('SCELTA?', c, 'l\'etichetta parla di una scelta');
      if (/^-?\d+(\.\d+)?(px|%|ms|s|em|rem|vh|vw|deg)?$/.test(d)) segnala('NUMERO', c, 'valore «' + d + '»');
      if (/^(#[0-9a-f]{3,8}|rgba?\(|hsla?\()/i.test(d)) segnala('COLORE', c, 'valore «' + d + '»');
      if (/(^|_)(url|link|href)$/i.test(c.key) || /^https?:/.test(d)) segnala('LINK', c, 'indirizzo in un campo di testo');
    }
    if (php && !/^(margin|padding|shadow|border|radius|hover|anim|entrance|bg_|background|css_|custom_|html_id|z_index|visib|hide_|show_on|width|height|max_width|min_height|opacity|filter|transform|display|position|overflow|class)/.test(c.key)) {
      const base = c.key.split(/[.[]/)[0];
      const inTile = php.php.includes("'" + base + "'") || php.php.includes('"' + base + '"') || php.php.includes("'" + base + '_');
      const inComuni = COMUNI.includes("'" + base + "'");
      // scrivono altre chiavi dal builder: preset (tilePresets), stile tipografico, ombra personalizzata (ponte legacy), geocodifica
      const scriveAltro = /^(typography_preset|preset)$/.test(base) || /_shadow_custom$/.test(base) || ['geocode', 'typography-preset'].includes(c.type);
      if (!inTile && !inComuni && !scriveAltro && !['separator', 'description', 'heading', 'info', 'tabs', 'group'].includes(c.type)) segnala('FANTASMA', c, 'il renderer non nomina «' + base + '»');
    }
  }
  const testi = campi.filter((c) => TESTO_TIPI.has(c.type)).map((c) => c.key);
  if (testi.length) console.log('  TESTO    ' + [...new Set(testi)].join(', '));
  if (php) {
    const hex = [...new Set((php.php.match(/#[0-9a-fA-F]{3}(?:[0-9a-fA-F]{3})?\b/g) || []))];
    if (hex.length) console.log('  HEX      ' + hex.slice(0, 14).join(' ') + (hex.length > 14 ? ' …(+' + (hex.length - 14) + ')' : ''));
    const lette = new Set([...php.php.matchAll(/\$(?:s|settings|set|c|cfg)\[\s*'([a-z0-9_]+)'\s*\]/g)].map((m) => m[1]));
    const chiavi = new Set(campi.map((c) => c.key.split(/[.[]/)[0]));
    // una chiave scritta da un controllo composito (keyMap della tipografia, legacyKeys del ponte) compare fra virgolette nel config
    const nascoste = [...lette].filter((k) => !chiavi.has(k) && !src.includes("'" + k + "'") && !src.includes('"' + k + '"')
      && !/^(margin|padding|shadow|border|radius|anim|hover|css|custom|id|class|items?|children|_builder|wow_)/.test(k));
    if (nascoste.length) console.log('  SENZA CONTROLLO (lette dal PHP, non nell\'inspector): ' + nascoste.join(', '));
  }
}
