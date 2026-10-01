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
    fields.push({ file: f.replace(/\.js$/, ''), key, type, label: topProp(body, 'label') || '', body });
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

// Le chiavi che nominano la maschera di ritaglio di un'immagine. Ogni tile se l'è
// chiamata a modo suo: aspect, media_aspect, thumb_ratio, card_aspect, peek_ratio…
const RAPPORTO = /(^|_)(ratio|aspect)(_ratio)?$/i;

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

/**
 * Una chiave è una proprietà TIPOGRAFICA quando ha un suffisso tipografico e il
 * prefisso nomina un elemento di testo. `title_size` sì, `icon_size` no.
 *
 * I nomi degli elementi di testo sono quelli che le 250 tile usano davvero:
 * l'elenco nasce dalla ricognizione, non dall'immaginazione. Chi ne aggiunge uno
 * nuovo lo mette qui, così l'audit continua a vederlo.
 */
const TESTO = [
  'title', 'titolo', 'heading', 'headline', 'subhead', 'subheading', 'subtitle', 'sub',
  'text', 'label', 'quote', 'name', 'nameplate', 'role', 'desc', 'description', 'excerpt',
  'kicker', 'eyebrow', 'caption', 'tagline', 'lead', 'standfirst', 'counter', 'number',
  'value', 'price', 'currency', 'day', 'time', 'date', 'note', 'footer',
  'position', 'brand', 'letter', 'cta', 'cta1', 'cta2', 'more', 'tag', 'meta', 'body',
  'author', 'question', 'answer', 'stat', 'word', 'char', 'accent', 'serif',
  'sans', 'mono', 'h', 'paragraph', 'p',
];
// ⚠️ Fuori di proposito: `handle` è la maniglia del confronto immagini e `step`
// il passo di un asse — hanno una dimensione, non un carattere.
// Suffissi che nominano una proprietà del CARATTERE (non la geometria di un box).
const SUFFISSO_TIPO = /_(size|weight|transform|line_height|lineheight|letter_spacing|letterspacing|tracking|font)$/i;

function proprietaTipografica(key) {
  const k = String(key || '').toLowerCase();
  const m = SUFFISSO_TIPO.exec(k);
  if (!m) {
    // Le due forme senza prefisso: `size_min`/`size_max` di una scala fluida.
    return /^size_(min|max)$/.test(k);
  }
  const prefisso = k.slice(0, m.index);
  if (!prefisso) return false;
  // Il prefisso può essere composto (`footer_label_size`, `title_accent_size`):
  // basta che l'ULTIMA parola, o la prima, nomini un elemento di testo.
  const parti = prefisso.split('_').filter(Boolean);
  return TESTO.includes(parti[parti.length - 1]) || TESTO.includes(parti[0]);
}

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
    // La rete aveva le maglie larghe: catturava solo le chiavi che FINISCONO in
    // font_size/font_weight/text_transform, e lasciava passare `title_size`,
    // `quote_size`, `name_weight`, `size_min`… cioè la forma più diffusa.
    // Ora il match è: suffisso tipografico + prefisso che nomina un ELEMENTO DI
    // TESTO. Il prefisso conta davvero: `icon_size`, `avatar_size`, `dot_size`
    // sono misure di oggetti, non tipografia, e non devono finire nel pannello.
    match: (r) => proprietaTipografica(r.key) && !/fallback/.test(r.key) && !skip(r),
    // Unica forma ammessa fuori dal pannello: un `*_size` che è una SCALA
    // simbolica (Piccolo/Medio/Grande) e non una misura — lì non c'è nessun
    // numero da scrubbare, è un preset del tema (content, overlaygrid, overlayslider).
    ok: (r) => /_size$/.test(r.key) && r.type === 'select' && !/value:\s*'?\d/.test(r.body),
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
  {
    id: 'proporzioni-canoniche',
    titolo: 'Le proporzioni di un\'immagine si scelgono dall\'elenco canonico (ratioOptions)',
    // Prima dell'uniformazione c'erano 29 selettori di proporzione e 29 elenchi
    // diversi: chi offriva 4 voci, chi 9, e nessuno le stesse. Il valore salvato non
    // si puo' cambiare (tre formati storici convivono: '16/9', '16:9', '16-9'), ma
    // l'elenco, l'ordine e i nomi si': li genera ratioOptions() di _imageFrame.js,
    // che il separatore giusto lo riceve come parametro.
    // Restano fuori i rapporti che non sono maschere di ritaglio: le proporzioni di
    // colonna, che si esprimono in `fr` e sono un layout, non un'immagine.
    match: (r) => r.type === 'select' && RAPPORTO.test(r.key) && /value:\s*'\d+[/:-]\d/.test(r.body) && !/'[\d.]+fr/.test(r.body),
    ok: (r) => /ratioOptions\s*\(/.test(r.body),
  },
  {
    id: 'proporzioni-nome',
    titolo: 'Il selettore di ritaglio si chiama «Proporzioni» in tutte le tile',
    match: (r) => r.type === 'select' && RAPPORTO.test(r.key) && /ratioOptions\s*\(/.test(r.body) && !!r.label,
    ok: (r) => /^Proporzioni/i.test(etichetta(r.label)),
  },
  {
    id: 'adattamento-nome',
    titolo: 'Il selettore di adattamento si chiama «Adattamento», mai «Object fit»',
    match: (r) => r.type === 'select' && /(^|_)(object_)?fit$/i.test(r.key) && !!r.label,
    ok: (r) => /^Adattamento/i.test(etichetta(r.label)),
  },
  {
    id: 'focale-nome',
    titolo: 'Il punto focale si chiama «Punto focale», non «Posizione contenuto»',
    // Lo stesso controllo si chiamava in cinque modi: Posizione contenuto (15),
    // Posizione — punto focale (9), Punto focale (7), Punto focale immagine,
    // Posizione immagine. «Posizione» da sola non dice cosa fa: quel controllo
    // sceglie QUALE PARTE della foto resta inquadrata quando viene ritagliata.
    match: (r) => r.type === 'object-position' && !!r.label,
    ok: (r) => /^Punto focale/i.test(etichetta(r.label)),
  },
  {
    id: 'focale-grafico',
    titolo: 'Il punto focale usa il picker grafico (type:\'object-position\'), mai un select a 9 voci',
    // FieldObjectPosition permette anche la posizione LIBERA in percentuale; il
    // select a nove voci no, e salva comunque la stessa identica stringa CSS.
    match: (r) => /object_position$/i.test(r.key),
    ok: (r) => r.type === 'object-position',
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

// ─── il selettore «per dispositivo» della tipografia deve essere letto ───────
// Le chiavi logiche in `responsiveKeys` fanno comparire, accanto alla proprietà,
// il selettore desktop/tablet/telefono, che salva `<chiave>_tablet` e
// `<chiave>_mobile`. Se il renderer PHP della tile quelle chiavi non le legge, il
// selettore è un controllo che non fa niente: erano 128 su 131. Qui lo si
// ricalcola dal codice. Senza `responsiveKeys` il selettore non compare.
const phpPerTipo = {};
for (const f of fs.readdirSync(path.join(ROOT, 'includes/tiles')).filter((x) => x.endsWith('.php'))) {
  const src = fs.readFileSync(path.join(ROOT, 'includes/tiles', f), 'utf8');
  const m = src.match(/protected\s+\$type\s*=\s*'([^']+)'/);
  if (m) phpPerTipo[m[1]] = src;
}
const leggePerDispositivo = (php, k) => php.includes(k + '_tablet') || php.includes(k + '_mobile')
  || php.includes("'" + k + "_' .") || php.includes("'" + k + "_'.");
const dispositivoFantasma = [];
for (const f of fs.readdirSync(ELEMENTS).filter((x) => x.endsWith('.js') && !x.startsWith('_'))) {
  const src = fs.readFileSync(path.join(ELEMENTS, f), 'utf8');
  const tipo = (src.match(/type:\s*'([^']+)'/) || [])[1];
  if (!tipo) continue;
  const php = phpPerTipo[tipo] || '';
  const re = /type:\s*'typography'/g;
  let m;
  while ((m = re.exec(src))) {
    const inizio = src.lastIndexOf('{', m.index);
    let prof = 0, fine = inizio;
    for (let i = inizio; i < src.length; i++) {
      if (src[i] === '{') prof++;
      else if (src[i] === '}') { prof--; if (prof === 0) { fine = i; break; } }
    }
    const blocco = src.slice(inizio, fine + 1);
    const rk = blocco.match(/responsiveKeys:\s*\[([^\]]*)\]/);
    if (!rk) continue;
    for (const [, logica] of rk[1].matchAll(/'(\w+)'/g)) {
      const mk = blocco.match(new RegExp('(?:^|[\\s{,])' + logica + ":\\s*'([^']+)'"));
      if (!mk) continue; // chiave logica non mappata: il selettore non compare
      if (!leggePerDispositivo(php, mk[1])) {
        dispositivoFantasma.push({ file: f.replace(/\.js$/, ''), type: 'typography', key: mk[1] + ' (' + logica + ')', label: '' });
      }
    }
  }
}
violazioni['dispositivo-letto'] = dispositivoFantasma;
RULES.push({ id: 'dispositivo-letto', titolo: 'Il selettore tablet/telefono della tipografia compare solo dove il renderer ne legge i valori' });

// ─── «stile tipografico applicato dalla tile»: PHP e JS d'accordo ───────────
// Le tile dell'elenco non ricevono la classe olo-typo-* sul wrapper: lo stile
// lo applicano loro a un elemento preciso. L'elenco è scritto in PHP e in JS
// (sito e canvas devono decidere allo stesso modo), e ogni tile che vi compare
// deve leggere davvero `typography_preset`, altrimenti lo stile non farebbe niente.
function elencoTipi(file, marcatore) {
  try {
    const src = fs.readFileSync(path.join(ROOT, file), 'utf8');
    const i = src.indexOf(marcatore);
    if (i < 0) return null;
    const blocco = src.slice(i, src.indexOf(']', i));
    return new Set([...blocco.matchAll(/'([a-z0-9_-]+)'/g)].map((x) => x[1]));
  } catch { return null; }
}
const stilePhp = elencoTipi('includes/class-frontend-renderer.php', 'function tile_stile_tipografico_proprio');
const stileJs = elencoTipi('src/components/Grid/GridCell.vue', 'const STILE_TIPOGRAFICO_PROPRIO = new Set([');
const stileScarti = diff(stilePhp, stileJs);
for (const tipo of stilePhp || []) {
  if (!/typography_preset/.test(phpPerTipo[tipo] || '')) stileScarti.push(tipo + ' (non legge typography_preset)');
}
violazioni['stile-proprio-elenco'] = stileScarti.map((x) => ({ file: 'elenco', type: '', key: String(x), label: '' }));
RULES.push({ id: 'stile-proprio-elenco', titolo: 'Le tile che applicano da sé lo stile tipografico: elenco PHP = JS, e lo leggono davvero' });

// ─── tile atomiche: un solo elenco, uguale in PHP e in JS ────────────────────
// Badge, pulsante, icona, divisore, spaziatore, interruttore: contenitore sempre
// trasparente e senza cornice, e nell'inspector niente sfondo/raggio/bordo/ombra
// del contenitore. Il renderer PHP e il builder devono concordare sull'elenco, e
// nel JS l'elenco vive in UN posto (useBackgroundStyle.js): c'era una copia a
// cinque, senza il badge, dentro BuilderInspector.
const atomPhp = elencoTipi('includes/class-frontend-renderer.php', '$ATOMIC_TILES = [');
const atomJs = elencoTipi('src/composables/useBackgroundStyle.js', 'export const ATOMIC_TILE_TYPES = new Set([');
const atomScarti = diff(atomPhp, atomJs);
(function copieLocali(dir) {
  for (const f of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, f.name);
    if (f.isDirectory()) { copieLocali(p); continue; }
    if (!/\.(js|vue)$/.test(f.name) || p.endsWith('useBackgroundStyle.js')) continue;
    if (/ATOMIC_TILES?\s*=\s*new Set\(\[/.test(fs.readFileSync(p, 'utf8'))) atomScarti.push('copia locale: ' + path.relative(ROOT, p));
  }
})(path.join(ROOT, 'src'));
// Lo Sfondo è parte comune di tutte le tile: sulle atomiche lo disegna l'elemento,
// quindi ogni renderer atomico deve leggerlo (sfondo_elemento() o style['bg']).
for (const tipo of atomPhp || []) {
  const php = phpPerTipo[tipo] || '';
  if (!/sfondo_elemento\(|\$style\['bg'\]/.test(php)) atomScarti.push(tipo + ' (non disegna lo Sfondo sull\'elemento)');
}
violazioni['atomiche-elenco'] = atomScarti.map((x) => ({ file: 'elenco', type: '', key: String(x), label: '' }));
RULES.push({ id: 'atomiche-elenco', titolo: 'Tile atomiche: elenco PHP = JS, un solo elenco JS, e ognuna disegna lo Sfondo sull\'elemento' });

// ─── controlli fantasma: offerti dall'inspector, mai letti dal renderer ─────
// «Un controllo che non fa niente è peggio di un controllo che manca.» Cinque
// famiglie ricorrenti, ricalcolate dal codice. Il renderer di riferimento è il
// PHP: è lui che disegna sia il sito sia il canvas del builder (iframe).
// Le soglie di partenza sono il debito censito il giorno in cui la regola è nata:
// possono solo scendere, tile per tile.
function matchParen(src, i) {
  let depth = 0, str = null;
  for (let j = i; j < src.length; j++) {
    const c = src[j];
    if (str) { if (c === BS) { j++; continue; } if (c === str) str = null; continue; }
    if (c === "'" || c === '"' || c === '`') { str = c; continue; }
    if (c === '(') depth++;
    else if (c === ')') { depth--; if (depth === 0) return j; }
  }
  return -1;
}
// Il tipo della tile è il `type` di primo livello di `export default { … }`: il primo
// «type:» del file può essere quello di un campo o di un commento (proslider, revealbox,
// spacer), e la tile verrebbe saltata in silenzio.
const tipoConfig = (src) => {
  const i = src.search(/export\s+default\s*\{/);
  if (i < 0) return null;
  const a = src.indexOf('{', i);
  const b = matchBrace(src, a);
  return b > a ? topProp(src.slice(a, b + 1), 'type') : null;
};
const leggeChiave = (php, k) => new RegExp("\\[\\s*'" + k + "'\\s*\\]").test(php);
const presetSrc = fs.readFileSync(path.join(ROOT, 'src/config/tilePresets.js'), 'utf8');
const presetBody = presetSrc.slice(presetSrc.indexOf('export const TILE_PRESETS = {'));
const tipiConPreset = new Set([...presetBody.matchAll(/\n {2}['"]?([a-z0-9_-]+)['"]?\s*:\s*[{A-Z]/g)].map((x) => x[1]));
const fantasmi = { preset: [], hover: [], dispositivo: [], bordo: [], effettiTesto: [] };
for (const f of fs.readdirSync(ELEMENTS).filter((x) => x.endsWith('.js') && !x.startsWith('_'))) {
  const src = fs.readFileSync(path.join(ELEMENTS, f), 'utf8');
  const tipo = (src.match(/type:\s*'([^']+)'/) || [])[1];
  const php = phpPerTipo[tipo];
  if (!tipo || php === undefined) continue;
  const nome = f.replace(/\.js$/, '');
  // 1. «Stile» (preset): serve una voce in TILE_PRESETS o un renderer che lo legga
  if (/key:\s*'preset'/.test(src) && !tipiConPreset.has(tipo) && !leggeChiave(php, 'preset')) {
    fantasmi.preset.push({ file: nome, type: 'select', key: 'preset', label: '' });
  }
  // 2. toggle Normale/Hover: la chiave hover deve arrivare al renderer
  for (let i = src.indexOf('withHover('); i >= 0; i = src.indexOf('withHover(', i + 1)) {
    const fine = matchParen(src, i + 'withHover'.length);
    const call = src.slice(i, fine + 1);
    const key = (call.match(/key:\s*'([a-z0-9_]+)'/) || [])[1];
    if (!key) continue;
    const hk = (call.match(/hoverKey:\s*'([a-z0-9_]+)'/) || [])[1] || key + '_hover';
    const inMappa = /build_hover_css/.test(php) && new RegExp("'" + key + "'\\s*=>").test(php);
    if (!php.includes(hk) && !inMappa) fantasmi.hover.push({ file: nome, type: 'hover', key: hk, label: '' });
  }
  // 3. selettore del dispositivo (`responsive: true`): i valori per tablet e
  //    telefono devono essere letti (a mano o con css_per_dispositivo())
  for (const obj of allObjects(src)) {
    const km = obj.match(/^\{\s*key:\s*'([a-z0-9_]+)'/);
    if (!km || !/responsive:\s*true/.test(obj)) continue;
    const k = km[1];
    const letto = leggePerDispositivo(php, k) || new RegExp("css_per_dispositivo\\(\\s*\\$\\w+\\s*,\\s*'" + k + "'").test(php);
    if (!letto) fantasmi.dispositivo.push({ file: nome, type: 'disp.', key: k, label: '' });
  }
  // 4. «Bordo» condiviso: la chiave del bordo deve essere letta
  for (let i = src.indexOf('borderFields('); i >= 0; i = src.indexOf('borderFields(', i + 1)) {
    if (/function\s+$/.test(src.slice(Math.max(0, i - 9), i))) continue;
    const call = src.slice(i, matchParen(src, i + 'borderFields'.length) + 1);
    const k = (call.match(/key:\s*'([a-z0-9_]+)'/) || [])[1] || 'border';
    if (!leggeChiave(php, k)) fantasmi.bordo.push({ file: nome, type: 'border', key: k, label: '' });
  }
  // 5. «Effetti testo» condivisi: il renderer deve usare Olobuild_Text_Effects
  if (/textEffectsFields\(/.test(src) && !/Olobuild_Text_Effects|tfx_/.test(php)) {
    fantasmi.effettiTesto.push({ file: nome, type: 'text-fx', key: 'text_effect', label: '' });
  }
}
violazioni['fantasma-preset'] = fantasmi.preset;
RULES.push({ id: 'fantasma-preset', titolo: 'Il menu «Stile» (preset) ha preset registrati o un renderer che lo legge' });
violazioni['fantasma-hover'] = fantasmi.hover;
RULES.push({ id: 'fantasma-hover', titolo: 'Il toggle Normale/Hover di un campo scrive una chiave che il renderer legge' });
violazioni['fantasma-dispositivo'] = fantasmi.dispositivo;
RULES.push({ id: 'fantasma-dispositivo', titolo: 'Un campo con selettore del dispositivo è letto per tablet e telefono' });
violazioni['fantasma-bordo'] = fantasmi.bordo;
RULES.push({ id: 'fantasma-bordo', titolo: 'Il controllo «Bordo» condiviso è disegnato dal renderer' });
violazioni['fantasma-effetti-testo'] = fantasmi.effettiTesto;
RULES.push({ id: 'fantasma-effetti-testo', titolo: 'Gli «Effetti testo» condivisi sono resi dal renderer' });

// ─── effetti bordo dell'elemento: visibili dove il renderer li disegna, e solo lì ───
// borderFields() monta sotto il Bordo dell'elemento gli effetti (neon, gradiente…) salvati in
// settings.border_effect*. Da giugno a settembre 2026 un filtro di StyleFieldsRenderer li toglieva
// a tutte le tile come doppione di quelli del Contenitore (style.border_effect*, un altro oggetto):
// una funzione viva senza controllo, che sulle atomiche non si raggiungeva in nessun modo.
//  a) StyleFieldsRenderer non toglie di nuovo quei campi;
//  b) una tile che li offre ha un PHP che li disegna sul bordo della stessa chiave;
//  c) una tile che li spegne (borderFields({ effetti: false })) ha un PHP che NON li disegna;
//  d) il verso opposto: un PHP che li disegna sul bordo di una chiave ha nel config un
//     borderFields() con quella chiave e con gli effetti accesi (salvo EFFETTI_SENZA_CONTROLLO).
// «Li disegna» vuol dire che la regola colpisce un elemento: il selettore passato a
// build_border_effect_css deve esistere nel markup (effettiDisegnati, sotto). In 23 tile (nav,
// subnav, tagcloud, chart…) il selettore è di classe, '.{$uid}', ma nel markup quel valore è solo
// un id (o un data-): la regola non trova nulla, né sul sito né nel builder, che è lo stesso PHP.
// Lì gli effetti sono spenti; correggere il selettore cambierebbe la resa dei template salvati, e
// va fatto insieme al loro Bordo, fantasma per la stessa ragione (debito a parte).
// Eccezioni motivate di d): tile il cui PHP disegna ancora bordo ed effetti salvati, ma che di
// proposito non offrono il Bordo.
const EFFETTI_SENZA_CONTROLLO = new Set([
  // decoratore a zero dimensioni dalla 1.2.96 (sul sito l'elemento è alto 0): il config non monta
  // né il Bordo né gli effetti; il PHP ridisegna solo un bordo salvato prima di allora.
  'goo:border',
]);
// Argomenti di una chiamata PHP, separati alle virgole di primo livello (fuori da stringhe e parentesi).
function argomentiPhp(s) {
  const a = [];
  let prof = 0, cur = '', str = null;
  for (let i = 0; i < s.length; i++) {
    const c = s[i];
    if (str) { cur += c; if (c === BS) { cur += s[++i] ?? ''; continue; } if (c === str) str = null; continue; }
    if (c === '"' || c === "'") { str = c; cur += c; continue; }
    if ('([{'.includes(c)) prof++;
    else if (')]}'.includes(c)) prof--;
    if (c === ',' && prof === 0) { a.push(cur.trim()); cur = ''; continue; }
    cur += c;
  }
  if (cur.trim()) a.push(cur.trim());
  return a;
}
// Variabile PHP ($uid o $this->_uid) in una RegExp.
const PHPVAR = String.raw`\$(?:this->)?\w+`;
const reVarPhp = (n) => n.replace(/\$/g, BS + '$');
// Primo selettore semplice di un'espressione PHP: ".{$uid} .x", "#{$uid}", '.' . $uid, "…$uid…", o
// una variabile ($card_sel) assegnata a una di queste prima della chiamata. → { sigillo, v } | null
// (null: '.$uid' fra apici semplici, un letterale, una forma non riconosciuta = non colpisce nulla).
function selettorePhp(expr, php, finoA) {
  let e = expr.trim();
  const v = e.match(new RegExp('^(' + PHPVAR + ')$'));
  if (v) {
    let ultima = null;
    for (const m of php.matchAll(new RegExp(reVarPhp(v[1]) + String.raw`\s*=(?!=)\s*([^;]+);`, 'g'))) {
      if (m.index < finoA) ultima = m[1].trim();
    }
    if (!ultima) return null;
    e = ultima;
  }
  const m = e.match(new RegExp(String.raw`^"([.#])\{(` + PHPVAR + String.raw`)\}[^"]*"$`))
    || e.match(new RegExp(String.raw`^'([.#])'\s*\.\s*(` + PHPVAR + ')'))
    || e.match(new RegExp(String.raw`^"([.#])(` + PHPVAR + ')[^"]*"$'));
  return m ? { sigillo: m[1], v: m[2] } : null;
}
// Il valore v compare nel markup come classe (sigillo '.') o come id ('#')? Si segue anche dove
// finisce: $cls = 'x ' . $uid, $classi[] = $uid, $uid = $this->_uid (fino a tre passaggi). Per ogni
// occorrenza conta l'ultimo attributo aperto prima di lei (class="…, id="…, 'class' =>), se il suo
// valore non si è già chiuso: class="olo-x <?php echo esc_attr( $uid ); ?>" sì, id="$uid" no.
function nelMarkup(php, v, sigillo) {
  const nomi = new Set([v]);
  for (let giro = 0; giro < 3; giro++) {
    for (const n of [...nomi]) {
      // $Y = … n …;  $Y .= … n …;  $Y[] = n;  (non i confronti ==, ===, !=, <=, >=, né =>)
      const re = new RegExp('(' + PHPVAR + String.raw`)(?:\[\])?\s*(?<![=!<>])\.?=(?![=>])[^;]*` + reVarPhp(n) + String.raw`(?![\w\[>-])[^;]*;`, 'g');
      for (const m of php.matchAll(re)) nomi.add(m[1]);
      const alias = new RegExp(reVarPhp(n) + String.raw`\s*=(?!=)\s*(\$this->\w+)\s*(?:\?\?[^;]*)?;`, 'g');
      for (const m of php.matchAll(alias)) nomi.add(m[1]);
    }
  }
  const voluto = sigillo === '.' ? 'class' : 'id';
  for (const n of nomi) {
    for (const m of php.matchAll(new RegExp(reVarPhp(n) + String.raw`(?![\w\[>-])`, 'g'))) {
      const prima = php.slice(Math.max(0, m.index - 400), m.index);
      const attr = [...prima.matchAll(/\b([a-zA-Z][\w-]*)\s*=\s*\\?(["'])|'(class|id)'\s*=>/g)].pop();
      if (!attr) continue;
      const tra = prima.slice(attr.index + attr[0].length);
      if (attr[3]) { if (attr[3] === voluto && !/[\n;]/.test(tra)) return true; continue; }
      if (attr[1] === voluto && !tra.split(BS + attr[2]).join('').includes(attr[2])) return true;
    }
  }
  return false;
}
// Chiavi di bordo della tile ($s['k']) su cui il PHP chiama build_border_effect_css: vivi = con un
// selettore che trova l'elemento, morti = solo su un selettore che nel markup non c'è.
function effettiDisegnati(php) {
  // senza commenti: i phpcs:ignore dentro <?php … ?> nominano $uid e allontanano l'attributo
  const src = php.replace(/^\s*\/\*[\s\S]*?\*\//gm, '').replace(/(^|[\s;{}])\/\/.*?(?=\?>|$)/gm, '$1');
  const vivi = new Set(), morti = new Set();
  for (let i = src.indexOf('build_border_effect_css('); i >= 0; i = src.indexOf('build_border_effect_css(', i + 1)) {
    if (/function\s+$/.test(src.slice(Math.max(0, i - 20), i))) continue;
    const p = i + 'build_border_effect_css'.length;
    const args = argomentiPhp(src.slice(p + 1, matchParen(src, p)));
    const k = ((args[1] || '').match(/^\$\w+\[\s*'([a-z0-9_]+)'\s*\]/) || [])[1];
    if (!k) continue;
    const sel = selettorePhp(args[0], src, i);
    (sel && nelMarkup(src, sel.v, sel.sigillo) ? vivi : morti).add(k);
  }
  for (const k of vivi) morti.delete(k);
  return { vivi, morti };
}
{
  const trovate = [];
  const sfr = fs.readFileSync(path.join(ROOT, 'src/components/Builder/StyleFieldsRenderer.vue'), 'utf8');
  if (/border_effect|'Effetti bordo'/.test(sfr)) trovate.push({ file: 'StyleFieldsRenderer', type: 'filtro', key: 'border_effect', label: '' });
  const disegnatiPer = {};
  for (const [tipo, php] of Object.entries(phpPerTipo)) disegnatiPer[tipo] = effettiDisegnati(php);
  const offerti = {}; // tipo → chiavi di bordo con gli effetti offerti nel config
  for (const f of fs.readdirSync(ELEMENTS).filter((x) => x.endsWith('.js') && !x.startsWith('_'))) {
    const src = fs.readFileSync(path.join(ELEMENTS, f), 'utf8');
    const tipo = tipoConfig(src);
    for (let i = src.indexOf('...borderFields('); i >= 0; i = src.indexOf('...borderFields(', i + 1)) {
      const call = src.slice(i, matchParen(src, i + '...borderFields'.length) + 1);
      const k = (call.match(/key:\s*'([a-z0-9_]+)'/) || [])[1] || 'border';
      const spenti = /effetti:\s*false/.test(call);
      const d = disegnatiPer[tipo];
      const disegnati = !!d?.vivi.has(k);
      if (!spenti) (offerti[tipo] ||= new Set()).add(k);
      // 'selettore': il PHP li scrive, ma su un selettore che nel markup non c'è
      if (!spenti && !disegnati) trovate.push({ file: f.replace(/\.js$/, ''), type: d?.morti.has(k) ? 'selettore' : 'effetti', key: k, label: '' });
      if (spenti && disegnati) trovate.push({ file: f.replace(/\.js$/, ''), type: 'nascosti', key: k, label: '' });
    }
  }
  for (const [tipo, d] of Object.entries(disegnatiPer)) {
    for (const k of d.vivi) {
      if (offerti[tipo]?.has(k) || EFFETTI_SENZA_CONTROLLO.has(tipo + ':' + k)) continue;
      trovate.push({ file: tipo, type: 'no-ctrl', key: k, label: '' });
    }
  }
  violazioni['effetti-bordo-elemento'] = trovate;
  RULES.push({ id: 'effetti-bordo-elemento', titolo: 'Gli Effetti bordo dell\'elemento si vedono dove il renderer li disegna, e solo lì' });
}

// ─── stile nel Contenuto ─────────────────────────────────────────────────────
// «Ogni cosa che tocca stile e colore sta nel tab Stile» (utente, 23 set 2026): il
// corsivo e i colori stavano a volte nel Contenuto e a volte nello Stile. Nel Contenuto
// (`fields`, anche nelle voci dei ripetitori) nessun controllo di stile: le voci hanno
// il loro specchio nello Stile (content-items a struttura fissa, stesse chiavi).
// Contenuto = cosa la tile dice e mostra (testi, media principali, link, voci, dati,
// comportamento, visibilità). Stile = come appare (colori, tipografia e corsivo, sfondi,
// bordi, raggi, ombre, spazi, dimensioni, disposizione, varianti).
const TIPI_STILE_C = new Set(['color', 'typography', 'font-family', 'border', 'border-radius', 'box-shadow',
  'text-shadow', 'background', 'gradient', 'css-filter', 'backdrop-filter', 'object-position', 'text-effects',
  'spacing', 'shadow']);
// chiavi che SEMBRANO stile ma sono contenuto o comportamento
const ESCLUSI_C = new Set(['file_max_size', 'radius_default', 'fit_bounds', 'start_position', 'currency_position',
  'image_size', 'thumbnail_size', 'img_size', 'thumb_size', 'text_effect_cursor_char', 'password_require_uppercase',
  'y_step_size', 'direction']);
// per tile: il valore È il contenuto (tipo di effetto, font in mostra, colonne della riga)
const ESCLUSI_TILE_C = new Set(['particlefx:preset', 'variablespecimen:font_family', 'row:layout',
  'inner-columns:layout', 'section:layout', 'column:layout']);
const RE_STILE_C = [
  /(^|_)(italic|bold|uppercase|underline|font_size|font_weight|letter_spacing|line_height|text_transform|tracking)(_|$)/,
  /(^|_)(color|colour|colors|bg|tint|palette)(_|$)/,
  /(^|_)(opacity|blur|glow|shadow|radius|border|gradient|parallax)(_|$)/,
  /(^|_)(columns|cols|gap|align|alignment|justify|aspect_ratio|aspect|ratio|object_fit|height|width|max_width|min_height|equal_height|full_width)(_|$)/,
  /(^|_)(size|size_min|size_max)$/,
  /(^|_)(style|variant|skin|theme|preset|hover_effect|orientation|shape)$/,
];
const TIPI_PER_CHIAVE_C = new Set(['toggle', 'select', 'range', 'number', 'unit', 'text', 'segmented', 'radio', 'icon-select', '?']);
function eStile(key, tipo, tile, etichette, labelCampo) {
  if (ESCLUSI_C.has(key) || ESCLUSI_TILE_C.has(tile + ':' + key)) return false;
  if (/comportament/i.test(labelCampo)) return false;
  // «Sfondo voce», «Sfondo card» → stile; «Sfondo / media», «Copertina» → media principale
  if (tipo === 'background') return /sfondo/i.test(etichette) && !/media/i.test(labelCampo);
  if (TIPI_STILE_C.has(tipo)) return true;
  if (!key) return false;
  if (key === 'layout') return tipo === 'select' || tipo === 'segmented';
  if (!TIPI_PER_CHIAVE_C.has(tipo)) return false;
  if (/^(show|mostra|enable|use|has|hide|disable)_/.test(key)) return false;
  if (/_text$|_label$|_title$|_url$|_link$/.test(key)) return false;
  if (/position$/.test(key)) return tipo === 'select' || tipo === '?' || /object_position/.test(key);
  if (/(^|_)fit$/.test(key)) return tipo === 'select';
  return RE_STILE_C.some((re) => re.test(key));
}
function chiudiC(src, j) { // j su ( [ { → indice della chiusa corrispondente
  let d = 0, str = null;
  for (let k = j; k < src.length; k++) {
    const c = src[k];
    if (str) { if (c === BS) { k++; continue; } if (c === str) str = null; continue; }
    if (c === "'" || c === '"' || c === '`') { str = c; continue; }
    if (c === '/' && src[k + 1] === '/') { const e = src.indexOf('\n', k); k = e < 0 ? src.length : e; continue; }
    if (c === '/' && src[k + 1] === '*') { const e = src.indexOf('*/', k + 2); k = e < 0 ? src.length : e + 1; continue; }
    if ('([{'.includes(c)) d++;
    else if (')]}'.includes(c)) { d--; if (d === 0) return k; }
  }
  return -1;
}
function elementiC(src, a, b) { // elementi di primo livello dell'array [a..b]
  const out = [];
  let j = a + 1;
  while (j < b) {
    const c = src[j];
    if (/\s|,/.test(c)) { j++; continue; }
    if (c === '/' && src[j + 1] === '/') { const e = src.indexOf('\n', j); j = e < 0 ? b : e + 1; continue; }
    if (c === '/' && src[j + 1] === '*') { j = src.indexOf('*/', j + 2) + 2; continue; }
    let e;
    if (c === '{') e = chiudiC(src, j);
    else {
      let k = j, d = 0, str = null;
      for (; k < b; k++) {
        const ch = src[k];
        if (str) { if (ch === BS) { k++; continue; } if (ch === str) str = null; continue; }
        if (ch === "'" || ch === '"' || ch === '`') { str = ch; continue; }
        if ('([{'.includes(ch)) d++; else if (')]}'.includes(ch)) d--; else if (ch === ',' && d === 0) break;
      }
      e = k - 1;
    }
    out.push({ s: j, corpo: src.slice(j, e + 1) });
    j = e + 1;
  }
  return out;
}
function descriviC(corpo) {
  const t = corpo.trim();
  const obj = t.startsWith('{') ? t : (t.match(/^[A-Za-z_$][\w$]*\(\s*(\{[\s\S]*\})\s*(?:,[\s\S]*)?\)$/) || [])[1];
  if (!obj) return null;
  const lab = (obj.match(/[{,]\s*label:\s*(?:t\()?'((?:[^'\\]|\\.)*)'/) || [])[1] || '';
  return { key: (obj.match(/[{,]\s*key:\s*'([^']+)'/) || [])[1] || '', tipo: (obj.match(/[{,]\s*type:\s*'([^']+)'/) || [])[1] || '?', lab, obj };
}
const stileNelContenuto = [];
for (const f of fs.readdirSync(ELEMENTS).filter((x) => x.endsWith('.js') && !x.startsWith('_'))) {
  const src = fs.readFileSync(path.join(ELEMENTS, f), 'utf8');
  const tile = f.replace(/\.js$/, '');
  const i = src.search(/\n {2}fields:\s*\[/);
  if (i < 0) continue;
  const a = src.indexOf('[', i), b = chiudiC(src, a);
  let sezione = '';
  for (const el of elementiC(src, a, b)) {
    if (/^\.\.\.(shadowField|borderFields|borderEffectFields|textEffectsFields|typographyFields?|focalFields?|objectPositionField)\b/.test(el.corpo.trim())) {
      stileNelContenuto.push({ file: tile, type: 'spread', key: el.corpo.trim().slice(3, 40), label: '' });
      continue;
    }
    const d = descriviC(el.corpo);
    if (!d) continue;
    if (d.tipo === 'separator') { sezione = d.lab; continue; }
    if (d.tipo === 'content-items') {
      const m = d.obj.match(/itemFields:\s*\[/);
      if (!m) continue;
      const base = el.s + el.corpo.indexOf(d.obj) + m.index + m[0].length - 1;
      for (const v of elementiC(src, base, chiudiC(src, base))) {
        const dv = descriviC(v.corpo);
        if (dv && dv.tipo !== 'separator' && eStile(dv.key, dv.tipo, tile, dv.lab, dv.lab)) {
          stileNelContenuto.push({ file: tile, type: 'voce', key: d.key + '.' + dv.key, label: dv.lab });
        }
      }
      continue;
    }
    if (eStile(d.key, d.tipo, tile, d.lab + ' ' + sezione, d.lab)) stileNelContenuto.push({ file: tile, type: d.tipo, key: d.key || '(' + d.tipo + ')', label: d.lab });
  }
}
violazioni['stile-nel-contenuto'] = stileNelContenuto;
RULES.push({ id: 'stile-nel-contenuto', titolo: 'Nessun controllo di stile o colore nel tab Contenuto (anche nelle voci dei ripetitori)' });

// ─── compatto 0.1: le regole dei tre piani (chrome-*, css-*, densita-*) ─────
// Il sistema compatto (audit_results/MIGLIORIE_OLOBUILD_2026-09.md, passo 0.1) tocca
// tre piani: il chrome dell'inspector, il CSS di resa, la densità del contenuto.
// Qui entrano SOLO le regole che oggi si definiscono senza ambiguità. La soglia è il
// conteggio del 24 set 2026: quelle a 0 restano 0, le altre possono solo scendere.
// Il resto arriva col passo che crea ciò che la regola controlla (motivi e passi in
// scripts/golden/README.md):
//   chrome-token, chrome-accento → A1 (i token e le esclusioni non esistono ancora)
//   chrome-segmentato-unico → A1/D1, chrome-popover-motore → A1/D3 (oggi sono elenchi a mano)
//   css-important-tile → dopo B1 (B1 aggiunge la sua costante: fissata prima, B1 la alzerebbe)
//   densita-default-gemelli → 0.3 (oggi non è 0; l'elenco lo calcola il censimento)
//   densita-scala-unica, densita-gemelli, densita-token-consumati → C1
//   densita-fisse-elenco → C2/E3 · densita-auto-gemelli, densita-auto-renderer → E1
// Sono conteggi di testo: i commenti /* … */ non contano (le righe restano al loro posto).
const relC = (p) => path.relative(ROOT, p).split(path.sep).join('/');
function fileC(dir, re, ricorsivo = false) {
  const out = [];
  if (!fs.existsSync(dir)) return out;
  for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, e.name);
    if (e.isDirectory()) { if (ricorsivo) out.push(...fileC(p, re, true)); continue; }
    if (re.test(e.name)) out.push(p);
  }
  return out.sort();
}
const senzaCommentiC = (s) => s.replace(/\/\*[\s\S]*?\*\//g, (m) => m.replace(/[^\n]/g, ' '));
const sorgentiC = new Map();
const leggiC = (f) => {
  if (!sorgentiC.has(f)) {
    const src = senzaCommentiC(fs.readFileSync(f, 'utf8'));
    const righe = [0];
    for (let i = 0; i < src.length; i++) if (src[i] === '\n') righe.push(i + 1);
    sorgentiC.set(f, { src, righe });
  }
  return sorgentiC.get(f);
};
const rigaC = (righe, idx) => { let lo = 0, hi = righe.length - 1; while (lo < hi) { const m = (lo + hi + 1) >> 1; if (righe[m] <= idx) lo = m; else hi = m - 1; } return lo + 1; };
function occorrenzeC(files, re) {
  const out = [];
  for (const f of files) {
    const { src, righe } = leggiC(f);
    for (const m of src.matchAll(re)) {
      out.push({ file: relC(f), type: ':' + rigaC(righe, m.index), key: m[0].replace(/\s+/g, ' ').slice(0, 90), label: '' });
    }
  }
  return out;
}
// I gruppi di file. T = renderer PHP delle tile, V = tile del canvas Vue, C = config
// dell'inspector, F = frontend.css, R = CSS di resa + renderer di pagina + Style System.
const GC = {
  T: fileC(path.join(ROOT, 'includes/tiles'), /\.php$/),
  V: fileC(path.join(ROOT, 'src/components/Tiles'), /\.vue$/, true),
  C: fileC(ELEMENTS, /\.js$/),
  F: [path.join(ROOT, 'assets/css/frontend.css')],
  R: [
    ...['frontend', 'olo-proslider', 'olo-livesearch', 'olo-pdfviewer', 'olo-svganimator', 'olox', 'timeline-super', 'location-single']
      .map((n) => path.join(ROOT, 'assets/css', n + '.css')),
    path.join(ROOT, 'includes/class-frontend-renderer.php'),
    ...fileC(path.join(ROOT, 'includes/traits'), /^trait-olobuild-renderer-.*\.php$/),
    path.join(ROOT, 'includes/class-style-system.php'),
  ].filter((f) => fs.existsSync(f)),
};
const TVR = [...GC.T, ...GC.V, ...GC.R];
const nuoveRegole = [];
const regolaC = (id, titolo, trovate) => { violazioni[id] = trovate; nuoveRegole.push({ id, titolo }); };

// PIANO 1 — chrome
// I token del chrome (--olo-ui-*) sono dell'editor: una tile che li legge cambierebbe
// aspetto sul sito quando cambia la densità dell'EDITOR. (--olo-ui-accent è a parte:
// lo usano i segnaposto del builder, e l'accento lo governa chrome-accento in A1.)
regolaC('chrome-nelle-tile', 'Le tile (PHP, Vue, config, frontend.css) non leggono i token del chrome --olo-ui-*',
  occorrenzeC([...GC.T, ...GC.V, ...GC.C, ...GC.F], /--olo-ui-(ctl|fs|lh|s[1-6]|row|inline|section|panel|rail|toolbar|sb|switch|swatch|label|status)/g));

// Una `description` su un campo reso IN LINEA (renderInline di InspectorField: tipi
// INLINE_COMPACT e INLINE_FILL, salvo layout:'block', hoverable, aiGenerate:'alt',
// geocode) non viene mostrata. Gli elenchi si leggono da InspectorField.vue, così la
// regola segue il componente. Debito del giorno, va a 0 con A3 (FieldRow).
// Nelle voci dei ripetitori (itemFields) i tipi di CIE_NATIVE li rende ContentItemsEditor
// da sé e la description la mostra (cie-desc): lì conta solo chi delega a InspectorField
// (unit, date, time, datetime). CIE_NATIVE si legge da ContentItemsEditor.vue.
{
  const ifSrc = fs.readFileSync(path.join(ROOT, 'src/components/Builder/InspectorField.vue'), 'utf8');
  const elenco = (nome) => { const m = ifSrc.match(new RegExp('const ' + nome + '\\s*=\\s*\\[([^\\]]*)\\]')); return m ? [...m[1].matchAll(/'([^']+)'/g)].map((x) => x[1]) : null; };
  const compatti = elenco('INLINE_COMPACT'), riempiti = elenco('INLINE_FILL');
  const trovate = [];
  if (!compatti || !riempiti) trovate.push({ file: 'InspectorField', type: '', key: '(INLINE_COMPACT/INLINE_FILL non trovati)', label: '' });
  const inLinea = new Set([...(compatti || []), ...(riempiti || [])]);
  const cieM = fs.readFileSync(path.join(ROOT, 'src/components/Builder/ContentItemsEditor.vue'), 'utf8').match(/const CIE_NATIVE\s*=\s*new Set\(\[([^\]]*)\]/);
  const cieNative = cieM ? new Set([...cieM[1].matchAll(/'([^']*)'/g)].map((x) => x[1])) : null;
  if (!cieNative) trovate.push({ file: 'ContentItemsEditor', type: '', key: '(CIE_NATIVE non trovato)', label: '' });
  // Per file, i corpi dei campi scritti dentro un `itemFields: [ … ]` (multinsieme: un corpo
  // identico può comparire più volte). Gli array annidati stanno già in quello esterno.
  const fineArray = (src, i) => {
    let depth = 0, str = null;
    for (let j = i; j < src.length; j++) {
      const c = src[j];
      if (str) { if (c === BS) { j++; continue; } if (c === str) str = null; continue; }
      if (c === "'" || c === '"' || c === '`') { str = c; continue; }
      if (c === '/' && src[j + 1] === '/') { while (j < src.length && src[j] !== '\n') j++; continue; }
      if (c === '/' && src[j + 1] === '*') { j += 2; while (j < src.length && !(src[j] === '*' && src[j + 1] === '/')) j++; j++; continue; }
      if (c === '[') depth++;
      else if (c === ']') { depth--; if (depth === 0) return j; }
    }
    return -1;
  };
  const nelleVoci = new Map();
  for (const f of fs.readdirSync(ELEMENTS).filter((x) => x.endsWith('.js') && !x.startsWith('_'))) {
    const src = fs.readFileSync(path.join(ELEMENTS, f), 'utf8');
    const conta = new Map();
    let fine = -1;
    for (const m of src.matchAll(/\bitemFields\s*:\s*\[/g)) {
      if (m.index < fine) continue;
      const a = m.index + m[0].length - 1;
      const b = fineArray(src, a);
      if (b < 0) continue;
      fine = b;
      for (const body of allObjects(src.slice(a + 1, b))) conta.set(body, (conta.get(body) || 0) + 1);
    }
    nelleVoci.set(f.replace(/\.js$/, ''), conta);
  }
  // I campi avvolti in withHover(...) diventano hoverable: restano a tutta riga.
  const avvolti = new Set();
  for (const f of fs.readdirSync(ELEMENTS).filter((x) => x.endsWith('.js') && !x.startsWith('_'))) {
    const src = fs.readFileSync(path.join(ELEMENTS, f), 'utf8');
    for (let i = src.indexOf('withHover('); i >= 0; i = src.indexOf('withHover(', i + 1)) {
      const a = src.indexOf('{', i);
      const fineCall = matchParen(src, i + 'withHover'.length);
      if (a < 0 || a > fineCall) continue;
      const b = matchBrace(src, a);
      if (b > 0) avvolti.add(src.slice(a, b + 1));
    }
  }
  for (const r of fields) {
    if (!inLinea.has(r.type) || !topProp(r.body, 'description')) continue;
    if (topProp(r.body, 'layout') === 'block' || topProp(r.body, 'aiGenerate') === 'alt' || r.type === 'geocode') continue;
    if (/(^|[{,\s])hoverable\s*:\s*true/.test(r.body) || avvolti.has(r.body)) continue;
    const voci = nelleVoci.get(r.file);
    const n = voci ? voci.get(r.body) || 0 : 0;
    if (n > 0) {
      voci.set(r.body, n - 1);
      if (cieNative && cieNative.has(r.type)) continue; // voce di ripetitore resa da ContentItemsEditor
    }
    trovate.push({ file: r.file, type: r.type, key: r.key, label: '' });
  }
  regolaC('chrome-descrizione-invisibile', 'Nessuna description su un campo reso in linea (lì InspectorField non la mostra)', trovate);
}

// «Durata» del toggle Normale/Hover: withHover() offre sempre il campo `{key}_hover_duration`
// (o hoverDurationKey), salvo `noDuration: true` (InspectorField non mostra la riga). Agisce
// solo se il renderer PHP della tile lo legge: per nome (anche con
// Olobuild_Tile_Utils::durata_hover( $s, '<chiave>', … )), con build_hover_css() (la mappa lo
// deriva dalla chiave: conta solo una chiave scritta DENTRO gli argomenti della chiamata, non
// nei $defaults) o con radius_hover*() (lo deriva dalla chiave hover). Debito del giorno: può
// solo scendere.
{
  const trovate = [];
  for (const f of fs.readdirSync(ELEMENTS).filter((x) => x.endsWith('.js') && !x.startsWith('_'))) {
    const src = fs.readFileSync(path.join(ELEMENTS, f), 'utf8');
    const tipo = tipoConfig(src);
    const php = phpPerTipo[tipo];
    if (!tipo || php === undefined) continue; // renderer fuori da includes/tiles (offcanvas, olo_room_* di OLObooking)
    const mappeHover = [];
    for (let j = php.indexOf('build_hover_css('); j >= 0; j = php.indexOf('build_hover_css(', j + 1)) {
      mappeHover.push(php.slice(j, matchParen(php, j + 'build_hover_css'.length) + 1));
    }
    const argomentiHover = mappeHover.join('\n');
    for (let i = src.indexOf('withHover('); i >= 0; i = src.indexOf('withHover(', i + 1)) {
      const call = src.slice(i, matchParen(src, i + 'withHover'.length) + 1);
      const key = (call.match(/key:\s*'([a-z0-9_]+)'/) || [])[1];
      if (!key) continue;
      if (/noDuration:\s*true/.test(call)) continue; // nessuna «Durata» offerta
      const hk = (call.match(/hoverKey:\s*'([a-z0-9_]+)'/) || [])[1] || key + '_hover';
      const dk = (call.match(/hoverDurationKey:\s*'([a-z0-9_]+)'/) || [])[1] || key + '_hover_duration';
      // letta = usata come indice ($s['…']), passata come dur_key o a durata_hover(): stare nei
      // $defaults non basta
      const perNome = leggeChiave(php, dk) || new RegExp("'dur_key'\\s*=>\\s*'" + dk + "'").test(php)
        || new RegExp("durata_hover\\(\\s*\\$\\w+\\s*,\\s*'" + dk + "'").test(php);
      const daMappa = dk === key + '_hover_duration' && new RegExp("'" + key + "'\\s*=>").test(argomentiHover);
      const daRaggio = dk === hk + '_duration' && /radius_hover(_rules)?\s*\(/.test(php) && php.includes("'" + hk + "'");
      if (!perNome && !daMappa && !daRaggio) trovate.push({ file: f.replace(/\.js$/, ''), type: 'durata', key: dk, label: '' });
    }
  }
  regolaC('fantasma-durata', 'La «Durata» dell\'hover è letta dal renderer PHP della tile', trovate);
}

// PIANO 2 — CSS di resa
regolaC('css-layer-tile', 'Nessun @layer nel CSS delle tile e di resa (la cascata la decide B1, non un livello)',
  occorrenzeC(TVR, /@layer\b/g));
regolaC('css-container-senza-nome', 'Ogni @container nomina il suo contenitore (mai la query anonima)',
  occorrenzeC(TVR, /@container\s*\(/g));
// Le unità cq* hanno senso solo dentro @container olo-cell (la cella della griglia).
{
  const trovate = [];
  for (const f of TVR) {
    const { src, righe } = leggiC(f);
    const blocchi = [];
    for (const m of src.matchAll(/@container\s+olo-cell\b[^{]*\{/g)) {
      let d = 0, j = m.index + m[0].length - 1;
      for (; j < src.length; j++) { if (src[j] === '{') d++; else if (src[j] === '}') { d--; if (d === 0) break; } }
      blocchi.push([m.index, j]);
    }
    for (const m of src.matchAll(/\d(cqi|cqw|cqh|cqb|cqmin|cqmax)\b/g)) {
      if (blocchi.some(([a, b]) => m.index > a && m.index < b)) continue;
      trovate.push({ file: relC(f), type: ':' + rigaC(righe, m.index), key: m[0], label: '' });
    }
  }
  regolaC('css-cq-fuori-container', 'Le unità cq* stanno solo dentro @container olo-cell', trovate);
}
// L'uid delle tile nasce da wp_rand(10000, 99999): l'HTML cambia a ogni vista e il banco
// deve renderlo deterministico (scripts/golden/golden-stub.php). Scende con B/E3.
regolaC('css-uid-casuale', 'Uid di tile da wp_rand(10000, 99999) nei renderer PHP', occorrenzeC(GC.T, /\bwp_rand\s*\(\s*10000\s*,\s*99999\s*\)/g));
// «16pxpx»: gli helper che restituiscono GIÀ l'unità (spacing_css, sides_css, border_radius,
// build_border_radius_css, radius_force_css, css_len) seguiti da `. 'px'` o, in una stringa,
// da `}px`. La chiamata si chiude sulle sue parentesi (una regex ne scavalcherebbe una e prenderebbe il
// `. 'px'` di un intval() più avanti). spacing_sides() no: restituisce numeri. Controprova
// sull'HTML reso: la colonna pxpx di _summary.tsv del banco (anche per un valore passato
// da una variabile, che qui non si vede).
{
  const trovate = [];
  for (const f of fileC(path.join(ROOT, 'includes'), /\.php$/, true)) {
    const { src, righe } = leggiC(f);
    for (const m of src.matchAll(/\b(?:spacing_css|sides_css|border_radius|build_border_radius_css|radius_force_css|css_len)\s*\(/g)) {
      const b = matchParen(src, m.index + m[0].length - 1);
      if (b < 0) continue;
      const dopo = src.slice(b + 1, b + 24);
      if (/^\s*\.\s*['"]px/.test(dopo) || /^\s*\}px/.test(dopo)) {
        trovate.push({ file: relC(f), type: ':' + rigaC(righe, m.index), key: src.slice(m.index, b + 1).replace(/\s+/g, ' ').slice(0, 90), label: '' });
      }
    }
  }
  regolaC('css-pxpx', 'Nessun helper che restituisce già l\'unità seguito da \'px\' nei renderer PHP (16pxpx)', trovate);
}
// Le soglie di @media scritte a mano fuori dal contratto dei 5 breakpoint (1400, 1200,
// 960, 640, 480 e i loro −1). Una per ogni min-/max-width letterale in px.
{
  const contratto = new Set([1400, 1200, 960, 640, 480, 1399, 1199, 959, 639, 479]);
  const trovate = [];
  for (const f of [...GC.T, ...GC.R]) {
    const { src, righe } = leggiC(f);
    for (const q of src.matchAll(/@media\b[^{]*/g)) {
      for (const m of q[0].matchAll(/\((?:max|min)-width\s*:\s*(\d+(?:\.\d+)?)px/g)) {
        if (contratto.has(Number(m[1]))) continue;
        trovate.push({ file: relC(f), type: ':' + rigaC(righe, q.index), key: m[0] + ')', label: '' });
      }
    }
  }
  regolaC('css-soglie-fuori-contratto', 'Le @media usano solo le soglie del contratto (1400/1200/960/640/480 e −1)', trovate);
}

// PIANO 3 — densità del contenuto
regolaC('densita-doppia', 'Mai var(--olo-density|ds|dr) moltiplicato per var(--olo-space-*|radius-*) nello stesso calc()',
  occorrenzeC(TVR, /calc\([^;{}]*?var\(\s*--olo-(?:density|ds|dr)\b[^;{}]*?var\(\s*--olo-(?:space|radius)-|calc\([^;{}]*?var\(\s*--olo-(?:space|radius)-[^;{}]*?var\(\s*--olo-(?:density|ds|dr)\b/g));
regolaC('densita-ds-senza-ripiego', 'Ogni var(--olo-ds|dr|density) ha il ripiego ", 1"',
  occorrenzeC([...TVR, ...GC.C, ...fileC(path.join(ROOT, 'src'), /\.(js|vue|scss|css)$/, true).filter((f) => !f.startsWith(path.join(ROOT, 'src/components/Tiles')) && !f.startsWith(ELEMENTS))],
    /var\(\s*--olo-(?:ds|dr|density)\s*(?:\)|,(?!\s*1\s*\)))/g));
// I tipi presenti al 24 set 2026 non leggono i token effettivi --olo-space-*/--olo-radius-*
// (li converte E3, famiglia per famiglia, con la moltiplicazione per la densità). Una
// tile NATA DOPO che li legge va messa qui sotto per nome di file.
const TILE_NATE_DOPO_0_1 = new Set([]);
regolaC('densita-token-in-tile-esistenti', 'Le tile esistenti non leggono --olo-space-*/--olo-radius-*',
  occorrenzeC([...GC.T, ...GC.V, ...GC.F].filter((f) => !TILE_NATE_DOPO_0_1.has(path.basename(f))), /var\(\s*--olo-(?:space|radius)-/g));
// I px scritti a mano per spazi e raggi nei renderer PHP: un intero ≥ 3 in una
// dichiarazione padding/margin/gap/border-radius (e lati). Regex fissata qui: il
// conteggio di partenza dipende da lei (il report diceva 1.896 con una regex non scritta).
regolaC('densita-letterali', 'px di spazio e raggio scritti a mano in includes/tiles (scendono con E3)',
  occorrenzeC(GC.T, /\b(?:padding|margin|gap|row-gap|column-gap|border-radius)(?:-(?:top|right|bottom|left|inline|block)(?:-start|-end)?)?\s*:\s*[^;{}"'<>]*?\b(?:[3-9]|\d{2,})px/g));

for (const r of nuoveRegole) RULES.push(r);

// ─── componenti orfani dell'area inspector ──────────────────────────────────
// Un componente che nessuno importa non gira mai, ma va comunque mantenuto e al
// prossimo intervento si rischia di aggiornare lui invece di quello vivo (StylePanel,
// ResponsiveFieldWrap: 400 righe tolte nel 2026-09). Gli import si risolvono sul
// percorso ('./', '../', '@/'), non sul solo nome del file. ESCLUSI: orfani che
// un'altra scheda del report deve ancora riusare o togliere — chi lo fa, li leva da qui.
const ESCLUSI_ORFANI = new Set([
  'AISettingsPanel.vue', 'GlobalColorsPanel.vue', // globali-orfani
  'DesignPresets.vue',                            // inserimento-designpresets-orfano
  'DeviceSwitch.vue',                             // canvas-desktop-non-desktop
]);
function fileDi(dir, ok, acc = []) {
  for (const f of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, f.name);
    if (f.isDirectory()) fileDi(p, ok, acc); else if (ok(f.name)) acc.push(p);
  }
  return acc;
}
const SRC = path.join(ROOT, 'src');
const importati = new Set();
for (const f of fileDi(SRC, (n) => /\.(vue|js|ts|mjs)$/.test(n))) {
  for (const m of fs.readFileSync(f, 'utf8').matchAll(/(?:\bfrom\s*|\bimport\s*\(?\s*)['"]([^'"]+\.vue)['"]/g)) {
    const spec = m[1];
    const abs = spec.startsWith('@/') ? path.join(SRC, spec.slice(2)) : spec.startsWith('.') ? path.resolve(path.dirname(f), spec) : null;
    if (abs && path.normalize(abs) !== path.normalize(f)) importati.add(path.normalize(abs));
  }
}
violazioni['componente-orfano'] = fileDi(path.join(SRC, 'components/Builder'), (n) => n.endsWith('.vue'))
  .filter((c) => !importati.has(path.normalize(c)) && !ESCLUSI_ORFANI.has(path.basename(c)))
  .map((c) => ({ file: 'Builder', type: '', key: path.relative(SRC, c).split(path.sep).join('/'), label: '' }));
RULES.push({ id: 'componente-orfano', titolo: 'Ogni componente di src/components/Builder è importato da un altro file' });

// ─── controlli orfani: un ramo dei dispatcher che nessun campo raggiunge ─────
// FieldBorderLegacy, FieldTransform e FieldBackdropFilter restavano montabili da
// InspectorField per tipi che nessun config dichiarava più (dalla v1.2.50), con la
// vecchia interfaccia pronta a ricomparire. Due controlli: (a) ogni componente di
// fields/ è importato E usato fuori dalla riga di import; (b) ogni tipo gestito dai
// dispatcher (InspectorField, StyleFieldsRenderer, ContentItemsEditor) è dichiarato
// da un campo (`type: 'x'`) in un altro file di src/, config `_*.js` compresi.
const campoOrfano = [];
const sorgentiSrc = fileDi(SRC, (n) => /\.(vue|js|ts|mjs)$/.test(n)).map((p) => ({ p: path.normalize(p), s: fs.readFileSync(p, 'utf8') }));
const kebab = (x) => x.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();
for (const c of fileDi(path.join(SRC, 'components/Builder/fields'), (n) => n.endsWith('.vue'))) {
  const abs = path.normalize(c);
  const usato = sorgentiSrc.some(({ p, s }) => p !== abs && [...s.matchAll(/^\s*import\s+([A-Za-z_$][\w$]*)\s+from\s*['"]([^'"]+\.vue)['"];?\s*$/gm)].some((m) => {
    const spec = m[2];
    const dest = spec.startsWith('@/') ? path.join(SRC, spec.slice(2)) : spec.startsWith('.') ? path.resolve(path.dirname(p), spec) : null;
    if (!dest || path.normalize(dest) !== abs) return false;
    const resto = s.replace(m[0], '');
    return new RegExp('\\b' + m[1] + '\\b').test(resto) || resto.includes('<' + kebab(m[1]));
  }));
  if (!usato) campoOrfano.push({ file: 'fields', type: 'mai usato', key: path.basename(c), label: '' });
}
const DISPATCHER = ['InspectorField.vue', 'StyleFieldsRenderer.vue', 'ContentItemsEditor.vue'].map((f) => path.normalize(path.join(SRC, 'components/Builder', f)));
const tipiDispatcher = new Set();
for (const d of DISPATCHER) {
  const s = fs.readFileSync(d, 'utf8');
  // Solo confronti sul tipo del CAMPO e i case che restituiscono un componente: i case
  // di optionsSource ('taxonomies', 'templates'…) non sono tipi di campo.
  for (const m of s.matchAll(/\b(?:props\.field|field|f|fieldDef)\.type\s*===\s*'([a-z0-9_-]+)'/g)) tipiDispatcher.add(m[1]);
  for (const m of s.matchAll(/\bcase\s+'([a-z0-9_-]+)'\s*:\s*return\s+[A-Z]\w*/g)) tipiDispatcher.add(m[1]);
}
const dichiarazioni = sorgentiSrc.filter(({ p }) => !DISPATCHER.includes(p)).map(({ s }) => s).join('\n');
for (const ti of [...tipiDispatcher].sort()) {
  if (!new RegExp('\\btype\\s*:\\s*[\'"]' + ti + '[\'"]').test(dichiarazioni)) campoOrfano.push({ file: 'dispatcher', type: 'tipo', key: ti, label: '' });
}
violazioni['campo-orfano'] = campoOrfano;
RULES.push({ id: 'campo-orfano', titolo: 'Ogni controllo di fields/ è usato e ogni tipo dei dispatcher è dichiarato da un campo' });

// ─── FieldBox: ogni cella scrive l'angolo (o il lato) che disegna, dove sta ──
// Fino alla 1.4.491 la griglia del Raggio si riempiva nell'ordine dei dati
// (tl, tr, br, bl) e l'icona di base disegnava l'angolo in alto a DESTRA: la cella in
// basso a sinistra scriveva br, e ogni icona mostrava l'angolo accanto. Qui si
// controlla che per ogni cella chiave = icona (base + rotazione) = posizione, e che il
// template collochi le celle per posizione e non per ordine dei dati.
const fieldBoxCelle = [];
{
  const fb = fs.readFileSync(path.join(SRC, 'components/Builder/fields/FieldBox.vue'), 'utf8');
  const ATTESE = {
    tl: [0, 1, 1], tr: [90, 1, 2], br: [180, 2, 2], bl: [270, 2, 1],
    top: [0, 1, 1], right: [90, 1, 2], bottom: [180, 1, 3], left: [270, 1, 4],
  };
  const viste = new Set();
  for (const m of fb.matchAll(/\{\s*k:\s*'(\w+)',\s*r:\s*'(\d+)deg',\s*row:\s*(\d+),\s*col:\s*(\d+)/g)) {
    const [, k, r, row, col] = m;
    viste.add(k);
    const a = ATTESE[k];
    if (!a) { fieldBoxCelle.push({ file: 'FieldBox', type: 'chiave', key: k, label: '' }); continue; }
    if (+r !== a[0]) fieldBoxCelle.push({ file: 'FieldBox', type: 'icona', key: `${k} ruota ${r}°, atteso ${a[0]}°`, label: '' });
    if (+row !== a[1] || +col !== a[2]) fieldBoxCelle.push({ file: 'FieldBox', type: 'posto', key: `${k} in ${row},${col}, atteso ${a[1]},${a[2]}`, label: '' });
  }
  for (const k of Object.keys(ATTESE)) if (!viste.has(k)) fieldBoxCelle.push({ file: 'FieldBox', type: 'manca', key: k, label: '' });
  // Le rotazioni valgono solo se l'icona di base disegna l'angolo in ALTO A SINISTRA
  // (parte in basso a sinistra, sale e curva verso destra) e il lato ALTO.
  if (!/const CORNER_SVG = '[^']*<path d="M4 20v-7a9 9 0 0 1 9-9h7"\/>/.test(fb)) fieldBoxCelle.push({ file: 'FieldBox', type: 'icona', key: 'CORNER_SVG non è l\'angolo in alto a sinistra', label: '' });
  if (!/const EDGE_SVG = '[^']*<line x1="4" y1="4" x2="20" y2="4"/.test(fb)) fieldBoxCelle.push({ file: 'FieldBox', type: 'icona', key: 'EDGE_SVG non è il lato alto', label: '' });
  if (!/v-for="c in celle"/.test(fb) || !/gridRow:\s*c\.row/.test(fb) || !/gridColumn:\s*c\.col/.test(fb)) {
    fieldBoxCelle.push({ file: 'FieldBox', type: 'griglia', key: 'le celle non sono collocate per riga/colonna', label: '' });
  }
}
violazioni['fieldbox-celle'] = fieldBoxCelle;
RULES.push({ id: 'fieldbox-celle', titolo: 'FieldBox: ogni cella scrive l\'angolo/lato che disegna, nella sua posizione' });

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
