#!/usr/bin/env node
/**
 * audit-ui-standard — verifica che i controlli dell'inspector siano UNIFORMI.
 *
 * PERCHÉ
 * ------
 * Con 250 tile è facile che un padding torni a essere due slider, che un bordo
 * torni a essere tre campi separati, o che un controllo scriva una chiave che
 * nessun renderer legge. Questo script rilegge tutti i config di
 * `src/config/elements/` e fallisce quando il numero di violazioni SUPERA la
 * soglia registrata in `scripts/ui-standard-baseline.json`.
 *
 * Uso:
 *   node scripts/audit-ui-standard.mjs            # confronto con la baseline
 *   node scripts/audit-ui-standard.mjs --list     # elenca le violazioni
 *   node scripts/audit-ui-standard.mjs --update   # riscrive la baseline
 *
 * Le soglie NON vanno alzate: se il numero sale, è una regressione.
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const HERE = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(HERE, '..');
const ELEMENTS = path.join(ROOT, 'src/config/elements');
const BASELINE = path.join(HERE, 'ui-standard-baseline.json');
const BS = String.fromCharCode(92);

// ─── estrazione dei field dai config (parser a graffe bilanciate) ────────────
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
      const e = rest.search(/[,\n}]/);
      return rest.slice(0, e < 0 ? rest.length : e).trim();
    }
  }
  return null;
}

const fields = [];
for (const f of fs.readdirSync(ELEMENTS).filter((x) => x.endsWith('.js') && !x.startsWith('_'))) {
  const src = fs.readFileSync(path.join(ELEMENTS, f), 'utf8');
  for (const body of allObjects(src)) {
    if (/(^|[{,\s])(fields|styleFields|contentFields|itemFields)\s*:/.test(body)) continue;
    const key = topProp(body, 'key');
    const type = topProp(body, 'type');
    if (!key || !type) continue;
    fields.push({ file: f.replace(/\.js$/, ''), key, type, label: topProp(body, 'label') || '' });
  }
}

// ─── le regole: una famiglia = un solo tipo di controllo ────────────────────
const IGNORE = /pad_custom|_custom$|radius_default|point_radius|point_hover_radius|spotlight_radius|trigger_radius|show_radius|media_radius_custom|strip_radius|media_radius_top|overlay_padding|frame_padding|pad_y|anim_border/;
// Tile con un parametro omonimo ma di significato diverso (raggio fisico, preset).
const IGNORE_TILE = { physicsbin: /^radius$/, section: /^padding$/, progallery: /anim_border/ };
const skip = (r) => IGNORE.test(r.key) || (IGNORE_TILE[r.file] && IGNORE_TILE[r.file].test(r.key));

// Le etichette nei config sono avvolte in t('…'): qui serve il testo dentro.
const etichetta = (raw) => {
  const m = /^t\(\s*'([\s\S]*)'\s*\)$/.exec(String(raw || ''));
  return (m ? m[1] : String(raw || '')).trim();
};

// Unità che un controllo numerico sa già mostrare accanto al valore.
const UNITA_IN_CODA = /\s*\((px|%|ms|s|vh|vw|vmin|vmax|em|rem|ch|deg|fr|pt)\)\s*$/i;

// Il nome canonico per famiglia — vince la forma già più diffusa, non la mia
// preferenza. Cambia solo il testo mostrato: le chiavi salvate non si toccano.
const GLOSSARIO = [
  { nome: 'Raggio',  key: /radius/i },
  { nome: 'Padding', key: /padding/i },
  { nome: 'Gap',     key: /(^|_)gap$/i },
  { nome: 'Ombra',   key: /(^|_)shadow$/i },
  { nome: 'Durata',  key: /duration$/i },
];

const RULES = [
  {
    id: 'padding-spacing',
    titolo: 'Il padding si gestisce SEMPRE col controllo a 4 lati (type:\'spacing\')',
    match: (r) => /padding/i.test(r.key) && !skip(r),
    ok: (r) => r.type === 'spacing' || r.type === 'toggle',
  },
  {
    id: 'margin-spacing',
    titolo: 'Il margine si gestisce SEMPRE col controllo a 4 lati',
    match: (r) => /(^|_)margin/i.test(r.key),
    ok: (r) => r.type === 'spacing' || r.type === 'toggle',
  },
  {
    id: 'radius-4-angoli',
    titolo: 'Il raggio si gestisce SEMPRE col controllo a 4 angoli (type:\'border-radius\')',
    match: (r) => /radius/i.test(r.key) && !skip(r) && !/(^|_)(km|zoom)/.test(r.key),
    ok: (r) => r.type === 'border-radius' || r.type === 'toggle',
  },
  {
    id: 'bordo-completo',
    titolo: 'Spessore+colore di un bordo = UN controllo type:\'border\'',
    match: (r) => /border_(width|thickness|size)$/i.test(r.key) && !skip(r),
    ok: () => false,
  },
  {
    id: 'tipografia-unica',
    titolo: 'Le proprietà tipografiche stanno nel pannello type:\'typography\'',
    match: (r) => /(font_size|font_weight|text_transform)$/i.test(r.key) && !/fallback/.test(r.key),
    ok: () => false,
  },
  {
    id: 'font-family',
    titolo: 'La famiglia di caratteri usa type:\'font-family\'',
    match: (r) => /font_family$/i.test(r.key),
    ok: (r) => r.type === 'font-family',
  },
  {
    id: 'ombra-unica',
    titolo: 'L\'ombra si compone in UN controllo (type:\'box-shadow\'), non in sotto-campi sparsi',
    // shadow_h / shadow_v / shadow_blur / shadow_spread / shadow_inset dichiarati
    // come campi a sé sono la vecchia forma: stanno DENTRO FieldBoxShadow, che li
    // mostra insieme con l'anteprima. Le chiavi salvate restano quelle (ponte legacy).
    match: (r) => /^([a-z0-9_]*_)?shadow_(h|v|blur|spread|inset)$/.test(r.key),
    ok: () => false,
  },
  {
    id: 'glossario',
    titolo: 'La stessa cosa si chiama con lo STESSO nome in tutte le tile',
    // Il nome canonico è quello già più diffuso (censimento dei 5.922 campi):
    // Raggio (109 contro Arrotondamento 49 e Border Radius 23), Padding (117),
    // Gap (50 contro Spazio 23, Distanza 9, Spaziatura 6).
    // Un toggle non nomina mai una misura: «Mostra durata» accende una cosa,
    // non è il campo in cui si scrive una durata.
    match: (r) => !!r.label && r.type !== 'toggle' && !skip(r) && GLOSSARIO.some((g) => g.key.test(r.key)),
    ok: (r) => {
      const g = GLOSSARIO.find((x) => x.key.test(r.key));
      return !g || new RegExp('^' + g.nome, 'i').test(etichetta(r.label));
    },
  },
  {
    id: 'unita-nel-controllo',
    titolo: 'L\'unità di misura la mostra il controllo, non l\'etichetta',
    // range e number hanno la valbox di NumberScrubber, che scrive l'unità accanto
    // al numero: ripeterla nel nome del campo la fa scrivere in modi diversi tile
    // per tile ed è la stessa informazione due volte.
    match: (r) => ['range', 'number', 'spacing', 'border-radius'].includes(r.type) && UNITA_IN_CODA.test(etichetta(r.label)),
    ok: () => false,
  },
  {
    id: 'icona-picker',
    titolo: 'Le icone usano il picker (type:\'icon\'), mai testo libero',
    // I toggle 'mostra icona' non scelgono un'icona: non rientrano nella regola.
    match: (r) => /^icon$|(^|_)icon$/i.test(r.key) && r.type !== 'toggle',
    ok: (r) => r.type === 'icon' || r.type === 'icon-select' || r.type === 'select',
  },
];

const violazioni = {};
for (const rule of RULES) {
  violazioni[rule.id] = fields.filter((r) => rule.match(r) && !rule.ok(r));
}

// ─── coerenza dell'elenco «ombra sul wrapper» ───────────────────────────────
// Le tile che montano il controllo Ombra condiviso senza disegnarlo nel proprio
// renderer ricevono l'ombra sul wrapper. L'elenco è scritto due volte — in PHP e
// in JS — perché il sito e il canvas devono decidere allo stesso modo. Qui lo si
// RICALCOLA dal codice e si controlla che le due copie combacino: il giorno in cui
// una tile impara a rendersi l'ombra da sé, l'elenco va accorciato in entrambe.
function elencoDa(file, marcatore) {
  try {
    const src = fs.readFileSync(path.join(ROOT, file), 'utf8');
    const i = src.indexOf(marcatore);
    if (i < 0) return null;
    const blocco = src.slice(i, src.indexOf(']', i));
    return new Set([...blocco.matchAll(/'([a-z0-9_]+)'/g)].map((m) => m[1]));
  } catch { return null; }
}
const atteso = new Set();
for (const f of fs.readdirSync(ELEMENTS).filter((x) => x.endsWith('.js') && !x.startsWith('_'))) {
  const tipo = f.replace(/\.js$/, '');
  if (!/shadowField/.test(fs.readFileSync(path.join(ELEMENTS, f), 'utf8'))) continue;
  const php = path.join(ROOT, 'includes/tiles', `class-${tipo}-tile.php`);
  let rende = false;
  try { rende = /shadow_value|'shadow'/.test(fs.readFileSync(php, 'utf8')); } catch { rende = false; }
  if (!rende) atteso.add(tipo);
}
const inPhp = elencoDa('includes/class-frontend-renderer.php', 'static $elenco = [');
const inJs = elencoDa('src/components/Grid/GridCell.vue', 'const OMBRA_SUL_WRAPPER = new Set([');
const diff = (a, b) => (!a || !b ? ['(elenco non trovato)'] : [...new Set([...a].filter((x) => !b.has(x)).concat([...b].filter((x) => !a.has(x))))]);
const scartiPhp = diff(atteso, inPhp);
const scartiJs = diff(inPhp, inJs);
violazioni['ombra-wrapper-elenco'] = [...scartiPhp, ...scartiJs].map((x) => ({ file: 'elenco', type: '', key: String(x), label: '' }));
RULES.push({ id: 'ombra-wrapper-elenco', titolo: 'L\'elenco «ombra sul wrapper» combacia fra PHP, JS e stato reale del codice' });

// ─── confronto con la baseline ──────────────────────────────────────────────
const args = process.argv.slice(2);
const conteggi = Object.fromEntries(Object.entries(violazioni).map(([k, v]) => [k, v.length]));

if (args.includes('--update')) {
  fs.writeFileSync(BASELINE, JSON.stringify({ aggiornato: new Date().toISOString().slice(0, 10), soglie: conteggi }, null, 2) + '\n');
  console.log('baseline aggiornata:', JSON.stringify(conteggi));
  process.exit(0);
}

if (args.includes('--list')) {
  for (const rule of RULES) {
    const v = violazioni[rule.id];
    console.log(`\n── ${rule.id} — ${rule.titolo}  [${v.length}]`);
    for (const r of v) console.log('   ' + r.file.padEnd(24) + r.type.padEnd(10) + r.key);
  }
}

let baseline = { soglie: {} };
try { baseline = JSON.parse(fs.readFileSync(BASELINE, 'utf8')); } catch { /* prima esecuzione */ }

let regressioni = 0;
console.log(`\ncampi analizzati: ${fields.length} in ${new Set(fields.map((f) => f.file)).size} tile\n`);
for (const rule of RULES) {
  const n = conteggi[rule.id];
  const atteso = baseline.soglie[rule.id];
  const stato = atteso === undefined ? 'NUOVO' : n > atteso ? 'REGRESSIONE' : n < atteso ? 'MIGLIORATO' : 'ok';
  if (stato === 'REGRESSIONE') regressioni++;
  const marca = stato === 'REGRESSIONE' ? '✗' : stato === 'MIGLIORATO' ? '↓' : '·';
  console.log(`${marca} ${rule.id.padEnd(20)} ${String(n).padStart(4)}${atteso !== undefined ? ` (atteso ≤ ${atteso})` : ''}  ${stato}`);
}

if (regressioni) {
  console.log(`\n${regressioni} regressioni: un controllo è tornato fuori standard. Dettagli con --list.`);
  process.exit(1);
}
console.log('\nNessuna regressione.');
