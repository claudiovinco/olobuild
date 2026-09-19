/**
 * La cornice di un'immagine deve esistere in TRE posti, o non esiste.
 *
 * Il config dichiara il controllo, il renderer PHP lo disegna sul sito, il
 * componente Vue lo disegna nel canvas del builder. Se ne manca uno il controllo
 * compare nell'inspector e non fa niente — oppure, peggio, fa cose diverse nel
 * builder e sul sito, e l'utente scopre la differenza dopo aver pubblicato.
 *
 * Questo controllo è statico e volutamente grossolano: cerca la CHIAVE nei due
 * renderer. Non prova che il CSS prodotto sia giusto (per quello c'è la prova di
 * render sul server, che confronta l'HTML di ogni opzione), ma becca subito il
 * caso più comune, cioè il renderer che non è stato toccato affatto.
 *
 *   node scripts/audit-cornice-immagine.mjs          riepilogo
 *   node scripts/audit-cornice-immagine.mjs --list   il dettaglio
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const ELEMENTS = path.join(ROOT, 'src/config/elements');
const TILES_PHP = path.join(ROOT, 'includes/tiles');
const TILES_VUE = path.join(ROOT, 'src/components/Tiles');

// Le chiavi della cornice: la maschera di ritaglio, l'adattamento, il focale.
const CORNICE = /(^|_)((object_)?fit|ratio|aspect|aspect_ratio|object_position)$/i;

// Il nome del file non segue una regola sola: 'info-cards' sta in
// class-info-cards-tile.php e in InfoCardsTile.vue, 'olo_room_grid' in
// class-olo-room-grid-tile.php. Si provano le forme note.
const phpCandidati = (t) => [
  `class-${t}-tile.php`,
  `class-${t.replace(/_/g, '-')}-tile.php`,
];
const vueCandidati = (t) => {
  const camel = t.replace(/[-_](.)/g, (_, c) => c.toUpperCase());
  const pascal = camel.charAt(0).toUpperCase() + camel.slice(1);
  return [`${pascal}Tile.vue`, `${pascal.replace(/Olo/, 'Olo')}Tile.vue`];
};
const leggi = (dir, nomi) => {
  for (const n of nomi) {
    const p = path.join(dir, n);
    if (fs.existsSync(p)) return { file: n, src: fs.readFileSync(p, 'utf8') };
  }
  return null;
};

// Il default che conta NON è quello del config JS: quando si trascina una tile
// nuova, il builder la semina con i default che arrivano dal REST, cioè da
// get_defaults() del PHP (class-rest-api.php → useDragDrop.js). Se i due non
// coincidono, l'inspector mostra selezionato un valore e il sito ne rende un altro.
// È lo stesso meccanismo che teneva i colori del brand fuori dalle tile nuove.
const defaultDa = (src, apertura) => {
  const i = src.indexOf(apertura);
  if (i < 0) return null;
  const out = {};
  // Ci si ferma alla prima riga che chiude l'array al livello di partenza.
  // Il blocco è `defaults: {` in JS e `return [` (o `= [`) in PHP: si parte dalla
  // prima parentesi utile dopo l'ancora, qualunque delle due sia.
  const g = src.indexOf('{', i), h = src.indexOf('[', i);
  let dep = 0, j = g < 0 ? h : h < 0 ? g : Math.min(g, h);
  if (j < 0) return null;
  const inizio = j;
  for (let k = j; k < src.length; k++) {
    const c = src[k];
    if (c === '{' || c === '[') dep++;
    else if (c === '}' || c === ']') { dep--; if (dep === 0) { j = k; break; } }
  }
  const corpo = src.slice(inizio, j);
  // In JS le chiavi sono nude (media_aspect_ratio: '4/3'), in PHP sono quotate
  // ('media_aspect_ratio' => '4/3'): la virgoletta è facoltativa da entrambe le parti.
  for (const m of corpo.matchAll(/['"]?([A-Za-z0-9_]+)['"]?\s*(?::|=>)\s*'([^']*)'/g)) out[m[1]] = m[2];
  return out;
};

// Le eccezioni DICHIARATE, con il motivo. Non sono buchi: sono posti dove il
// canvas del builder non disegna affatto quell'immagine, quindi non c'e' niente da
// allineare. Vanno tenute corte e motivate: se una tile finisce qui senza un motivo
// verificabile, la regola smette di servire a qualcosa.
const ECCEZIONI = {
  // Il rapporto e l'adattamento stanno dentro un HOTSPOT del PDF, che il canvas non
  // disegna affatto (PdfproTile.vue rasterizza le pagine e non apre i pannelli):
  // li rende il runtime del frontend, assets/js/olo-pdfpro.js:1581-1583.
  'pdfpro.image_ratio': 'immagine di hotspot, resa dal runtime frontend',
  'pdfpro.image_fit': 'immagine di hotspot, resa dal runtime frontend',
};

const buchi = [];
const discordi = [];
let esaminate = 0, chiavi = 0;

for (const f of fs.readdirSync(ELEMENTS).filter((x) => x.endsWith('.js') && !x.startsWith('_'))) {
  const tipo = f.replace(/\.js$/, '');
  const src = fs.readFileSync(path.join(ELEMENTS, f), 'utf8');
  const trovate = new Set();
  for (const m of src.matchAll(/key:\s*'([A-Za-z0-9_]+)'/g)) {
    if (CORNICE.test(m[1])) trovate.add(m[1]);
  }
  // Le chiavi montate da imageFrameFields() non compaiono come letterali.
  for (const m of src.matchAll(/imageFrameFields\(\s*'([A-Za-z0-9_]+)'/g)) {
    trovate.add(`${m[1]}_ratio`); trovate.add(`${m[1]}_fit`); trovate.add(`${m[1]}_object_position`);
  }
  if (!trovate.size) continue;
  esaminate++;
  const php = leggi(TILES_PHP, phpCandidati(tipo));
  const vue = leggi(TILES_VUE, vueCandidati(tipo));

  const defJs = defaultDa(src, 'defaults:');
  const defPhp = php ? (defaultDa(php.src, 'protected $defaults') || defaultDa(php.src, 'get_defaults')) : null;
  if (defJs && defPhp) {
    for (const k of trovate) {
      const a = defJs[k], b = defPhp[k];
      if (a !== undefined && b !== undefined && a !== b) discordi.push({ tipo, chiave: k, js: a, php: b });
      else if (a !== undefined && b === undefined) discordi.push({ tipo, chiave: k, js: a, php: '(assente)' });
    }
  }
  // Un renderer può leggere la cornice SENZA nominare la chiave: gli helper
  // `image_frame($s, 'img')` e `focal_pos($s, 'img')` compongono il nome da un
  // PREFISSO. Cercare il letterale darebbe un falso allarme proprio sulle tile
  // che hanno fatto la cosa giusta, cioè delegare all'helper condiviso.
  const prefissi = (src2) => {
    const out = new Set();
    const re = /(?:image_frame|imageFrame|focal_pos|focalPos)\s*\(\s*[^,]+,\s*'([A-Za-z0-9_]*)'/g;
    for (const m of src2.matchAll(re)) out.add(m[1]);
    return out;
  };
  const prePhp = php ? prefissi(php.src) : new Set();
  const preVue = vue ? prefissi(vue.src) : new Set();
  const viaHelper = (pre, k) => {
    for (const p of pre) {
      if (p === '') continue;
      if (k === `${p}_ratio` || k === `${p}_ratio_custom` || k === `${p}_fit` || k === `${p}_object_position`) return true;
    }
    return false;
  };

  for (const k of trovate) {
    chiavi++;
    const inPhp = php ? php.src.includes(`'${k}'`) || php.src.includes(`"${k}"`) || viaHelper(prePhp, k) : null;
    const inVue = vue ? vue.src.includes(k) || viaHelper(preVue, k) : null;
    if ((inPhp === false || inVue === false) && !ECCEZIONI[`${tipo}.${k}`]) {
      buchi.push({ tipo, chiave: k, php: php ? (inPhp ? 'sì' : 'NO') : '(file assente)', vue: vue ? (inVue ? 'sì' : 'NO') : '(file assente)' });
    }
  }
}

console.log(`\ncornici esaminate: ${chiavi} chiavi in ${esaminate} tile`);
if (buchi.length) {
  console.log(`\n${buchi.length} chiavi che un renderer non legge:\n`);
  if (process.argv.includes('--list')) {
    for (const b of buchi) console.log(`   ${(b.tipo + '.' + b.chiave).padEnd(44)} php:${b.php.padEnd(14)} vue:${b.vue}`);
  } else {
    console.log('   (dettaglio con --list)');
  }
  process.exit(1);
}
console.log(`
Ogni chiave della cornice è letta da entrambi i renderer (${Object.keys(ECCEZIONI).length} eccezioni dichiarate).`);
