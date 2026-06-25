/**
 * Bandiere SVG inline per il tile Selettore lingua (e affini).
 *
 * PERCHÉ NON LE EMOJI: le flag-emoji (🇮🇹…) NON esistono su Windows (e su vari
 * browser/OS) → si vedono come "IT/GB" o quadratini. Inoltre le regole tile
 * vietano le emoji ("Icone dal set SVG, mai emoji"). Qui usiamo SVG inline.
 *
 * Ogni voce è il CONTENUTO interno dell'SVG (senza <svg> wrapper): viene avvolto
 * da flagSvg() con un viewBox per-bandiera e preserveAspectRatio="xMidYMid slice"
 * così riempie "cover" il contenitore (cerchio o rettangolo) deciso dal CSS.
 *
 * ⚠️ Mantenere ALLINEATO al gemello PHP Olo_Tile_Utils/flag_inner() per coerenza
 * builder↔frontend. Stemmi complessi (Saudi/Corea/Cina/USA) sono semplificati =
 * resa "icona" pulita e riconoscibile. Codici senza bandiera → badge col codice.
 */

// viewBox per-bandiera (rapporto reale della bandiera).
const VB = {
  it: '0 0 3 2', fr: '0 0 3 2', de: '0 0 5 3', es: '0 0 3 2', pt: '0 0 30 20',
  nl: '0 0 3 2', ru: '0 0 3 2', pl: '0 0 8 5', ja: '0 0 3 2', gb: '0 0 60 30',
  us: '0 0 60 32', zh: '0 0 30 20',
  ko: '0 0 36 24', ar: '0 0 36 24', cs: '0 0 36 24',
  sv: '0 0 16 10', da: '0 0 16 12', fi: '0 0 18 11', no: '0 0 22 16',
  hu: '0 0 3 2', ro: '0 0 3 2', bg: '0 0 3 2', uk: '0 0 3 2',
  el: '0 0 27 18', tr: '0 0 30 20', hr: '0 0 3 2', sk: '0 0 3 2', sl: '0 0 3 2',
};

// Contenuto SVG (geometria) per codice lingua.
const INNER = {
  // Italia — verde/bianco/rosso verticale
  it: '<rect width="3" height="2" fill="#fff"/><rect width="1" height="2" fill="#009246"/><rect x="2" width="1" height="2" fill="#ce2b37"/>',
  // Francia — blu/bianco/rosso verticale
  fr: '<rect width="3" height="2" fill="#fff"/><rect width="1" height="2" fill="#002654"/><rect x="2" width="1" height="2" fill="#ce1126"/>',
  // Germania — nero/rosso/oro orizzontale
  de: '<rect width="5" height="1" fill="#000"/><rect width="5" height="1" y="1" fill="#d00"/><rect width="5" height="1" y="2" fill="#ffce00"/>',
  // Spagna — rosso/giallo(metà)/rosso (stemma omesso)
  es: '<rect width="3" height="2" fill="#c60b1e"/><rect width="3" height="1" y="0.5" fill="#ffc400"/>',
  // Portogallo — verde 2/5 + rosso 3/5 (sfera gialla stilizzata)
  pt: '<rect width="30" height="20" fill="#da291c"/><rect width="12" height="20" fill="#046a38"/><circle cx="12" cy="10" r="3.2" fill="none" stroke="#ffe600" stroke-width="1.1"/>',
  // Paesi Bassi — rosso/bianco/blu orizzontale
  nl: '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" fill="#ae1c28"/><rect width="3" height="0.667" y="1.333" fill="#21468b"/>',
  // Russia — bianco/blu/rosso orizzontale
  ru: '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" y="0.667" fill="#0039a6"/><rect width="3" height="0.667" y="1.333" fill="#d52b1e"/>',
  // Polonia — bianco/rosso orizzontale
  pl: '<rect width="8" height="5" fill="#fff"/><rect width="8" height="2.5" y="2.5" fill="#dc143c"/>',
  // Giappone — bianco + disco rosso
  ja: '<rect width="3" height="2" fill="#fff"/><circle cx="1.5" cy="1" r="0.6" fill="#bc002d"/>',
  // Regno Unito (per "English") — Union Jack (versione canonica compatta)
  gb: '<clipPath id="uks"><path d="M0,0 v30 h60 v-30 z"/></clipPath><clipPath id="ukt"><path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z"/></clipPath><g clip-path="url(#uks)"><path d="M0,0 v30 h60 v-30 z" fill="#012169"/><path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6"/><path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#ukt)" stroke="#C8102E" stroke-width="4"/><path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10"/><path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"/></g>',
  // USA — 13 strisce (7 rosse) + cantone blu con stelle (semplificate a puntini)
  us: '<rect width="60" height="32" fill="#fff"/><g fill="#b22234"><rect width="60" height="2.46"/><rect y="4.92" width="60" height="2.46"/><rect y="9.84" width="60" height="2.46"/><rect y="14.76" width="60" height="2.46"/><rect y="19.68" width="60" height="2.46"/><rect y="24.6" width="60" height="2.46"/><rect y="29.52" width="60" height="2.46"/></g><rect width="24" height="17.22" fill="#3c3b6e"/><g fill="#fff"><circle cx="3" cy="2.4" r="0.7"/><circle cx="7.2" cy="2.4" r="0.7"/><circle cx="11.4" cy="2.4" r="0.7"/><circle cx="15.6" cy="2.4" r="0.7"/><circle cx="19.8" cy="2.4" r="0.7"/><circle cx="3" cy="6.7" r="0.7"/><circle cx="7.2" cy="6.7" r="0.7"/><circle cx="11.4" cy="6.7" r="0.7"/><circle cx="15.6" cy="6.7" r="0.7"/><circle cx="19.8" cy="6.7" r="0.7"/><circle cx="3" cy="11" r="0.7"/><circle cx="7.2" cy="11" r="0.7"/><circle cx="11.4" cy="11" r="0.7"/><circle cx="15.6" cy="11" r="0.7"/><circle cx="19.8" cy="11" r="0.7"/><circle cx="3" cy="15.3" r="0.7"/><circle cx="7.2" cy="15.3" r="0.7"/><circle cx="11.4" cy="15.3" r="0.7"/><circle cx="15.6" cy="15.3" r="0.7"/><circle cx="19.8" cy="15.3" r="0.7"/></g>',
  // Cina — rosso + stella grande + 4 stelline (semplificate a puntini)
  zh: '<rect width="30" height="20" fill="#de2910"/><path d="M5,2.2 6.18,5.83 10,5.83 6.9,8.07 8.09,11.7 5,9.46 1.91,11.7 3.1,8.07 0,5.83 3.82,5.83 z" fill="#ffde00"/><g fill="#ffde00"><circle cx="10" cy="2" r="0.9"/><circle cx="12" cy="4" r="0.9"/><circle cx="12" cy="7" r="0.9"/><circle cx="10" cy="9" r="0.9"/></g>',
  // Corea del Sud — bianco + taegeuk + 4 trigrammi (semplificati)
  ko: '<rect width="36" height="24" fill="#fff"/><circle cx="18" cy="12" r="5" fill="#cd2e3a"/><path d="M18,7 a2.5,2.5 0 0,1 0,5 a2.5,2.5 0 0,0 0,5 a5,5 0 0,1 0,-10" fill="#0047a0"/><g fill="#000"><g transform="translate(5,5)"><rect width="5" height="0.7"/><rect y="1.3" width="5" height="0.7"/><rect y="2.6" width="5" height="0.7"/></g><g transform="translate(26,5)"><rect width="5" height="0.7"/><rect y="1.3" width="2.1" height="0.7"/><rect x="2.9" y="1.3" width="2.1" height="0.7"/><rect y="2.6" width="5" height="0.7"/></g><g transform="translate(5,15.7)"><rect width="2.1" height="0.7"/><rect x="2.9" width="2.1" height="0.7"/><rect y="1.3" width="5" height="0.7"/><rect y="2.6" width="2.1" height="0.7"/><rect x="2.9" y="2.6" width="2.1" height="0.7"/></g><g transform="translate(26,15.7)"><rect width="2.1" height="0.7"/><rect x="2.9" width="2.1" height="0.7"/><rect y="1.3" width="2.1" height="0.7"/><rect x="2.9" y="1.3" width="2.1" height="0.7"/><rect y="2.6" width="2.1" height="0.7"/><rect x="2.9" y="2.6" width="2.1" height="0.7"/></g></g>',
  // Arabia Saudita (per "ar") — verde + spada bianca (testo shahada omesso)
  ar: '<rect width="36" height="24" fill="#006c35"/><rect x="7" y="15.4" width="22" height="1.4" rx="0.7" fill="#fff"/><path d="M7,16.1 l-2.6,-1.3 0,2.6 z" fill="#fff"/>',
  // Repubblica Ceca — bianco/rosso + triangolo blu
  cs: '<rect width="36" height="12" fill="#fff"/><rect y="12" width="36" height="12" fill="#d7141a"/><path d="M0,0 L18,12 L0,24 z" fill="#11457e"/>',
  // Svezia — blu + croce scandinava gialla
  sv: '<rect width="16" height="10" fill="#006aa7"/><rect x="5" width="2" height="10" fill="#fecc00"/><rect y="4" width="16" height="2" fill="#fecc00"/>',
  // Danimarca — rosso + croce bianca
  da: '<rect width="16" height="12" fill="#c8102e"/><rect x="5" width="2" height="12" fill="#fff"/><rect y="5" width="16" height="2" fill="#fff"/>',
  // Finlandia — bianco + croce blu
  fi: '<rect width="18" height="11" fill="#fff"/><rect x="5" width="3" height="11" fill="#003580"/><rect y="4" width="18" height="3" fill="#003580"/>',
  // Norvegia — rosso + croce bianca + croce blu
  no: '<rect width="22" height="16" fill="#ef2b2d"/><rect x="6" width="4" height="16" fill="#fff"/><rect y="6" width="22" height="4" fill="#fff"/><rect x="7" width="2" height="16" fill="#002868"/><rect y="7" width="22" height="2" fill="#002868"/>',
  // Ungheria — rosso/bianco/verde orizzontale
  hu: '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" fill="#ce2939"/><rect width="3" height="0.667" y="1.333" fill="#477050"/>',
  // Romania — blu/giallo/rosso verticale
  ro: '<rect width="3" height="2" fill="#fcd116"/><rect width="1" height="2" fill="#002b7f"/><rect x="2" width="1" height="2" fill="#ce1126"/>',
  // Bulgaria — bianco/verde/rosso orizzontale
  bg: '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" y="0.667" fill="#00966e"/><rect width="3" height="0.667" y="1.333" fill="#d62612"/>',
  // Ucraina — blu/giallo orizzontale
  uk: '<rect width="3" height="2" fill="#ffd500"/><rect width="3" height="1" fill="#005bbb"/>',
  // Grecia — strisce blu/bianche + cantone con croce
  el: '<rect width="27" height="18" fill="#0d5eaf"/><g fill="#fff"><rect y="2" width="27" height="2"/><rect y="6" width="27" height="2"/><rect y="10" width="27" height="2"/><rect y="14" width="27" height="2"/></g><rect width="10" height="10" fill="#0d5eaf"/><rect x="4" width="2" height="10" fill="#fff"/><rect y="4" width="10" height="2" fill="#fff"/>',
  // Turchia — rosso + falce e stella bianche
  tr: '<rect width="30" height="20" fill="#e30a17"/><circle cx="11" cy="10" r="5" fill="#fff"/><circle cx="12.5" cy="10" r="4" fill="#e30a17"/><path d="M18,7.6 l0.9,2 2.2,0.2 -1.7,1.4 0.5,2.1 -1.9,-1.1 -1.9,1.1 0.5,-2.1 -1.7,-1.4 2.2,-0.2 z" fill="#fff"/>',
  // Croazia — rosso/bianco/blu orizzontale (stemma omesso)
  hr: '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" fill="#ff0000"/><rect width="3" height="0.667" y="1.333" fill="#171796"/>',
  // Slovacchia — bianco/blu/rosso orizzontale (stemma omesso)
  sk: '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" y="0.667" fill="#0b4ea2"/><rect width="3" height="0.667" y="1.333" fill="#ee1c25"/>',
  // Slovenia — bianco/blu/rosso orizzontale (stemma omesso)
  sl: '<rect width="3" height="2" fill="#fff"/><rect width="3" height="0.667" y="0.667" fill="#0000c6"/><rect width="3" height="0.667" y="1.333" fill="#ed1c24"/>',
};

// Alias: codici lingua → bandiera (es. 'en' usa la bandiera UK).
const ALIAS = { en: 'gb', 'en-gb': 'gb', 'en-us': 'us', 'pt-br': 'pt', cn: 'zh', jp: 'ja', ua: 'uk' };

function resolve(code) {
  const c = String(code || '').toLowerCase();
  return ALIAS[c] || c;
}

export function hasFlag(code) {
  return !!INNER[resolve(code)];
}

/**
 * Ritorna l'SVG (stringa) della bandiera, già "cover", oppure '' se non disponibile.
 * @param {string} code  codice lingua (it, en, de, …)
 * @param {string} [aria] label accessibile (es. "Italiano"); se assente → aria-hidden
 */
export function flagSvg(code, aria) {
  const key = resolve(code);
  const inner = INNER[key];
  if (!inner) return '';
  const vb = VB[key] || '0 0 3 2';
  const a11y = aria ? `role="img" aria-label="${aria}"` : 'aria-hidden="true"';
  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="${vb}" preserveAspectRatio="xMidYMid slice" ${a11y} focusable="false" style="width:100%;height:100%;display:block">${inner}</svg>`;
}

export default flagSvg;
