// Ogni Slider Pro dei temi si costruisce DA ZERO coi soli comandi dell'editor? (1.4.1435)
//
// Utente, 24 nov 2026: «costruire da ZERO OGNI pro slider usandolo senza trucchi css». Per ognuno dei 150 slider si
// riparte da uno slider vuoto e si rifà ogni livello passando SOLO dai controlli dell'editor: ogni proprietà deve avere
// un controllo che la scrive (letto dal codice dell'editor, non da un elenco a mano) e il suo valore deve essere fra
// quelli che quel controllo sa dare (i menu, i tipi: numero, testo, sì/no, colore, liste). Quello che manca si stampa.
//
//   node scripts/slider-ricostruibile.mjs           → riepilogo
//   node scripts/slider-ricostruibile.mjs --list    → ogni caso
import fs from 'fs';
import path from 'path';
const ROOT = path.resolve(path.dirname(new URL(import.meta.url).pathname.replace(/^\/([A-Z]:)/, '$1')), '..');
const DIR = path.join(ROOT, 'src/components/ProSlider');
const leggi = (f) => (fs.existsSync(path.join(DIR, f)) ? fs.readFileSync(path.join(DIR, f), 'utf8') : '');
const LP = leggi('LayerProperties.vue');
const TUTTO = fs.readdirSync(DIR).filter((f) => /\.(vue|js)$/.test(f)).map(leggi).join('\n');

// ── 1. i controlli, dal codice ────────────────────────────────────────────────
const scritte = new Set(); // percorsi che un controllo scrive: 'fontSize', 'hover.scale', 'sfondoForma[].tappe[].colore'…
for (const m of TUTTO.matchAll(/\bup\('([\w]+)'/g)) scritte.add(m[1]);
for (const m of TUTTO.matchAll(/\brv\('([\w]+)'\)/g)) scritte.add(m[1]);
// funzioni «upX(k, v)» che scrivono dentro un oggetto del livello: function upHover(key, val) { … up('hover', …
const setter = {};
for (const m of LP.matchAll(/function (\w+)\((\w+)(?:,\s*\w+)*\)\s*\{([\s\S]*?)\n\}/g)) {
  const genitore = m[3].match(/\bup\('(\w+)'/);
  if (genitore && /^(up|set)[A-Z]/.test(m[1])) setter[m[1]] = genitore[1];
}
for (const [fn, gen] of Object.entries(setter)) for (const m of TUTTO.matchAll(new RegExp('\\b' + fn + "\\('(\\w+)'", 'g'))) scritte.add(gen + '.' + m[1]);
// i campi annidati scritti con «{ ...layer.X, k: v }» o con :modelValue="layer.X.k" nei componenti
for (const m of TUTTO.matchAll(/up\('(\w+)',\s*\{\s*\.\.\.(?:props\.)?layer\.\1,\s*(\w+):/g)) scritte.add(m[1] + '.' + m[2]);
for (const m of TUTTO.matchAll(/layer\.(\w+)\??\.(\w+)/g)) scritte.add(m[1] + '.' + m[2]);
// i componenti che scrivono una lista intera: le loro chiavi
const LISTE = {
  sfondoForma: ['tipo', 'ripeti', 'angolo', 'forma', 'dimensione', 'posizione', 'tappe'], 'sfondoForma[].tappe': ['colore', 'da', 'a'],
  ombre: ['x', 'y', 'blur', 'spread', 'color', 'inset'], ritaglio: [], coloriDisegno: null,
};
for (const [lista, chiavi] of Object.entries(LISTE)) { scritte.add(lista); for (const k of chiavi || []) scritte.add(lista.replace(/\[\]/g, '[]') + '[].' + k); }
// linea del tempo: le proprietà animabili (ANIMATABLE_PROPS) coi loro limiti — upProp() li TAGLIA (clampVal)
const TU = leggi('timelineUtils.js');
const LIMITI_KF = {};
for (const m of TU.matchAll(/^\s+(\w+):\s*\{\s*label:[^}]*?min:\s*(-?[\d.]+),\s*max:\s*(-?[\d.]+)/gm)) { LIMITI_KF[m[1]] = [+m[2], +m[3]]; scritte.add('timeline.keyframes[].props.' + m[1]); }
for (const k of ['id', 'time', 'easing', 'props']) scritte.add('timeline.keyframes[].' + k); // id: lo dà l'editor
for (const m of (TUTTO.match(/tloop:\s*null/) ? ['tloop'] : [])) scritte.add('timeline.' + m);
for (const m of TUTTO.matchAll(/update-timeline-prop',\s*'(\w+)'/g)) scritte.add('timeline.' + m[1]);
// movimento con lo scorrimento: le chiavi SM_CHIAVI in ogni tappa e negli stati «da»/«a»
const SM = (TUTTO.match(/SM_CHIAVI\s*=\s*\[([^\]]*)\]/) || [, ''])[1];
for (const m of SM.matchAll(/'(\w+)'/g)) for (const dove of ['tappe[]', 'da', 'a']) scritte.add(`scrollMove.${dove}.${m[1]}`);
scritte.add('scrollMove.tappe[].p');
// lati e angoli scritti con «'padding' + side» (Top/Right/Bottom/Left) o con gli angoli TL/TR/BL/BR
for (const m of TUTTO.matchAll(/up\('(\w+)'\s*\+\s*side/g)) for (const s of ['Top', 'Right', 'Bottom', 'Left']) scritte.add(m[1] + s);
for (const m of TUTTO.matchAll(/up\('(borderRadius(?:TL|TR|BL|BR))'/g)) scritte.add(m[1]);
// i colori che possono seguire uno stato: upColore('color', …) / upStatoColore('bgColor', …)
for (const m of TUTTO.matchAll(/\bup(?:Stato)?Colore\('(\w+)'/g)) scritte.add(m[1]);
// le ombre: una lista sola (FieldOmbre) che scrive «boxShadow» per un'ombra semplice e «ombre» per le altre
for (const k of ['x', 'y', 'blur', 'spread', 'color']) scritte.add('boxShadow.' + k);
// valori per schermo: le chiavi che l'editor salva per tablet e telefono
const PER_SCHERMO = new Set(((TUTTO.match(/const responsiveKeys = \[([^\]]*)\]/) || [, ''])[1].match(/'(\w+)'/g) || []).map((s) => s.slice(1, -1)));
// chiavi che l'editor scrive da sé (non da un campo): identità, tipo, gruppo, ordine sulla tela
for (const k of ['id', 'type', 'name', 'x', 'y', 'width', 'height', 'responsive', 'isGlobal', 'gruppo', 'etichetta', 'daParte', 'content']) scritte.add(k);

// ── 2. i menu: valori ammessi ─────────────────────────────────────────────────
function blocco(src, nome) {
  const i = src.search(new RegExp('const\\s+' + nome + '\\s*='));
  if (i < 0) return '';
  let j = src.indexOf('=', i) + 1, prof = 0, inizio = -1;
  for (; j < src.length; j++) {
    const c = src[j];
    if ('[{('.includes(c)) { if (inizio < 0) inizio = j; prof++; } else if (']})'.includes(c)) { prof--; if (prof === 0) return src.slice(inizio, src.indexOf('\n', j)); }
  }
  return '';
}
function valori(src, nome) {
  const b = blocco(src, nome);
  const out = new Set([...b.matchAll(/value:\s*'([^']*)'/g)].map((m) => m[1]));
  const arr = b.match(/^\[([^\]]*)\]\.map/); if (arr) for (const m of arr[1].matchAll(/'([^']*)'/g)) out.add(m[1]);
  for (const m of b.matchAll(/\.\.\.([A-Za-z_]+)/g)) for (const v of valori(src, m[1])) out.add(v);
  const ref = b.match(/^([a-zA-Z]+)\.map/); if (ref) for (const m of blocco(src, ref[1]).matchAll(/'([^']*)'/g)) out.add(m[1]);
  return out;
}
const MENU = {};
// i menu che si riempiono da soli si controllano a parte (sotto): il livello bersaglio, i valori degli stati
const DINAMICI = new Set(['targetLayerOptions', 'opzioniSeguiClic', 'opzioniQuando']);
for (const m of LP.matchAll(/<FieldSelect[^>]*?:model-value="(?:props\.)?layer\.([\w.?]+)[^"]*"[^>]*?:options="(\w+)"/g)) if (!DINAMICI.has(m[2])) MENU[m[1].replace(/\?/g, '')] = valori(LP + leggi('timelineUtils.js'), m[2]);

// ── 3. ricostruzione di ogni slider ───────────────────────────────────────────
const casi = [];
let livelliSlide = [];
// FieldColor: vuoto, trasparente, un #hex o rgba() scritto, un token della palette (anche con la riserva), un token o
// un #hex con la trasparenza della sua barra (color-mix(in srgb, X N%, transparent)). Non sa mescolare due colori.
const CHIAVE_COLORE = /(colou?r|colore|^bgColor$|Color$|^from$|^to$|^segno$)/i;
const TOKEN = String.raw`var\(\s*--olo-color-[a-z0-9_-]+\s*(?:,\s*#[0-9a-f]{3,8}\s*)?\)`;
const HEXRGB = String.raw`#[0-9a-f]{3,8}|rgba?\([\d\s.,%]+\)`;
// … e dalla 1.4.1435 «Mescola con»: color-mix(in srgb, A P%, B) con A e B token della palette (o #hex)
const PARTE = String.raw`(?:${TOKEN}|#[0-9a-f]{3,8})`;
const UN_COLORE = String.raw`(?:transparent|currentColor|${HEXRGB}|${TOKEN}|color-mix\(in srgb,\s*${PARTE}\s+\d+%,\s*(?:transparent|${PARTE})\))`;
// … e «Segue il colore di uno stato» (1.4.1435): var(--mps-st-<stato>, <un colore del selettore>). Le percentuali sono
// intere: le barre Alfa e «Quanto» vanno di 1 in 1.
const COLORE_DEL_SELETTORE = new RegExp(String.raw`^(|${UN_COLORE}|var\(\s*--mps-st-[a-z0-9_-]+\s*,\s*${UN_COLORE}\s*\))$`, 'i');
function livello(l, tema, file) {
  const chi = `${tema} · ${l.name || l.id}`;
  // i menu dinamici: il livello da mostrare o nascondere deve esistere nella slide; «Nasce dove si clicca» deve
  // nominare un valore che un livello cliccabile della slide dà a uno stato
  if (l.action?.type === 'toggleLayer' && l.action.target && !livelliSlide.some((x) => x.id === l.action.target)) casi.push({ chi, file, cosa: `action.target = ${l.action.target}`, perche: 'il livello bersaglio non è nella slide' });
  if (l.seguiClic) {
    const valoriClic = new Set(livelliSlide.flatMap((x) => (x.action?.type === 'setState' ? [x.action.value] : x.action?.type === 'cycleState' ? String(x.action.values || '').split(',') : [])).map((v) => String(v || '').toLowerCase().replace(/[^a-z0-9_-]/g, '')));
    if (!valoriClic.has(String(l.seguiClic))) casi.push({ chi, file, cosa: `seguiClic = ${l.seguiClic}`, perche: 'nessun livello della slide dà quel valore' });
  }
  for (const kf of l.timeline?.keyframes || []) for (const [k, v] of Object.entries(kf.props || {})) {
    const lim = LIMITI_KF[k];
    if (lim && typeof v === 'number' && (v < lim[0] || v > lim[1])) casi.push({ chi, file, cosa: `timeline.props.${k} = ${v}`, perche: `la linea del tempo lo taglia a [${lim[0]}, ${lim[1]}]` });
  }
  const visita = (o, via) => {
    for (const [k, v] of Object.entries(o)) {
      const p = via ? via + '.' + k : k;
      const norm = p.replace(/\.\d+(?=\.|$)/g, '[]');
      if (k === 'responsive' && !via) {
        for (const [bp, ov] of Object.entries(v || {})) for (const kk of Object.keys(ov || {})) if (!PER_SCHERMO.has(kk)) casi.push({ chi, file, cosa: `responsive.${bp}.${kk}`, perche: 'per schermo: l\'editor non lo salva' });
        continue;
      }
      if (norm === 'coloriDisegno' || norm.startsWith('coloriDisegno.')) continue; // un colore per colore del file
      if (norm.startsWith('ritaglio')) continue; // lista di punti [x, y]
      const scritta = scritte.has(norm) || scritte.has(norm.replace(/\[\]/g, ''));
      if (!scritta && !(v && typeof v === 'object')) { casi.push({ chi, file, cosa: norm, perche: 'nessun controllo lo scrive' }); continue; }
      if (MENU[norm] && MENU[norm].size && v !== '' && v !== null && !MENU[norm].has(String(v))) casi.push({ chi, file, cosa: `${norm} = ${v}`, perche: 'il menu non lo offre' });
      // i colori: quello che il selettore colore (FieldColor) sa dare
      if (typeof v === 'string' && CHIAVE_COLORE.test(k) && !COLORE_DEL_SELETTORE.test(v.trim())) casi.push({ chi, file, cosa: `${norm} = ${v}`, perche: 'il selettore colore non lo sa dare' });
      if (Array.isArray(v)) v.forEach((x, i) => { if (x && typeof x === 'object' && !Array.isArray(x)) visita(x, `${norm}[]`.replace(/\[\]\[\]$/, '[]')); });
      else if (v && typeof v === 'object') visita(v, norm);
    }
  };
  visita(l, '');
}
const temi = path.join(ROOT, 'assets/data/themes');
let nSlider = 0, nLiv = 0;
for (const t of fs.readdirSync(temi)) {
  const p = path.join(temi, t);
  if (!fs.statSync(p).isDirectory()) continue;
  for (const f of fs.readdirSync(p).filter((x) => x.endsWith('.json'))) {
    let d; try { d = JSON.parse(fs.readFileSync(path.join(p, f), 'utf8')); } catch { continue; }
    const cerca = (o) => {
      if (Array.isArray(o)) return o.forEach(cerca);
      if (!o || typeof o !== 'object') return;
      if (o.type === 'proslider' && o.settings) {
        nSlider++;
        for (const s of o.settings.slides || []) { livelliSlide = [...(s.layers || []), ...(o.settings.globalLayers || [])]; for (const l of s.layers || []) { nLiv++; livello(l, t, f); } }
        livelliSlide = o.settings.globalLayers || [];
        for (const l of o.settings.globalLayers || []) { nLiv++; livello(l, t, f); }
        return;
      }
      Object.values(o).forEach(cerca);
    };
    cerca(d);
  }
}
const perCosa = {};
for (const c of casi) { const k = c.cosa.replace(/ = .*/, '') + ' — ' + c.perche; (perCosa[k] ||= []).push(c); }
console.log(`slider ${nSlider}, livelli ${nLiv}, controlli letti dal codice ${scritte.size}, menu ${Object.keys(MENU).length}`);
for (const [k, l] of Object.entries(perCosa).sort((a, b) => b[1].length - a[1].length)) {
  console.log(String(l.length).padStart(5), k, ' es.', l.slice(0, 2).map((c) => c.chi + (c.cosa.includes(' = ') ? ' (' + c.cosa.split(' = ')[1] + ')' : '')).join(' | '));
  if (process.argv.includes('--list')) for (const c of l) console.log('        ', c.chi, c.cosa);
}
console.log(`\nda costruire coi comandi: ${casi.length ? casi.length + ' casi NON coperti' : 'TUTTO coperto'}`);
process.exitCode = casi.length ? 1 : 0;
