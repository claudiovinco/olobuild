// Foto demo delle IMPOSTAZIONI DI PARTENZA delle tile (`partenza` nei config degli elementi).
//
// Il pacchetto sta in assets/img/demo/: 18 foto nel pubblico dominio (CC0), WebP larghe al più
// 1200 px, ≈ 1 MB in tutto. Fonti e autori in assets/img/demo/CREDITS.txt.
//
// Una tile appena trascinata nel canvas deve già mostrare a cosa serve (utente, 5 ott 2026: «deve
// già mostrare un utilizzo, anche se generico, della potenza della tile»): le sue foto vengono da qui.
// L'URL è quello del plugin sul sito in cui si costruisce, come il segnaposto grigio
// (`placeholderImageUrl()` in src/utils/contentPlaceholders.js).

/** Le foto del pacchetto: nome del file (senza .webp) → testo alternativo. */
export const DEMO_FOTO = {
  'alba-nuvole': 'Il sole che sorge sopra un mare di nuvole',
  architettura: 'Facciata di un edificio moderno con balconi a sbalzo',
  'bosco-luce': 'Un sentiero nel bosco attraversato dai raggi del sole',
  caffe: 'Una tazza di cappuccino su un tavolo di legno',
  ciotola: 'Una ciotola di porcellana blu decorata con pesci',
  'citta-tetti': 'I tetti rossi di un centro storico visti dall\'alto',
  'facciata-vetro': 'Grattacieli di vetro visti dal basso',
  foglie: 'Grandi foglie verdi con gocce di pioggia',
  insalata: 'Un piatto di insalata con carote a nastro',
  'mare-costa': 'Una baia con acqua turchese e una collina verde',
  'montagna-lago': 'Una montagna innevata riflessa in un lago',
  'piatti-alto': 'Una tavola imbandita vista dall\'alto',
  scrivania: 'Un portatile su una scrivania di legno',
  'spiaggia-alto': 'Onde su una spiaggia con scogli viste dall\'alto',
  'strada-colline': 'Una strada che attraversa una valle verde',
  tavola: 'Due piatti e un calice su una tavola di legno',
  ufficio: 'Una scrivania davanti a una vetrata sulla città',
  vaso: 'Un vaso di ceramica bianca',
};

function base() {
  const u = (typeof window !== 'undefined' && window.oloData && window.oloData.pluginUrl)
    ? window.oloData.pluginUrl
    : '/wp-content/plugins/olobuild/';
  return u.replace(/\/$/, '') + '/assets/img/demo/';
}

/** URL della foto demo. Un nome che non è nel pacchetto è un errore di chi scrive il config. */
export function demo(nome) {
  if (!Object.prototype.hasOwnProperty.call(DEMO_FOTO, nome)) throw new Error('foto demo inesistente: ' + nome);
  return base() + nome + '.webp';
}

/** Testo alternativo della foto demo. */
export const demoAlt = (nome) => DEMO_FOTO[nome] || '';

/** La foto demo come voce di galleria { url, alt, caption } (senza id: non è nella Libreria media). */
export function demoObj(nome, caption = '') {
  return { url: demo(nome), alt: demoAlt(nome), caption };
}

// ── Dati del sito in cui si costruisce (oloData del builder) ──────────────────────────────────
// Le tile di navigazione nascono col logo e il menu DEL SITO, non con un logo di serie: un header
// appena trascinato deve già essere quello del sito. Fuori dal builder (catalogo) restano vuoti.
const datiBuilder = () => (typeof window !== 'undefined' && window.oloData) || {};

/** URL del logo del sito (Personalizza → Identità del sito), '' se non c'è. */
export const logoDelSito = () => (datiBuilder().siteInfo || {}).logo_url || '';

/** Nome del sito. */
export const nomeDelSito = () => (datiBuilder().siteInfo || {}).name || '';

/** Il primo menu di WordPress che ha delle voci (id), 0 se il sito non ne ha. */
export function primoMenu() {
  const m = (datiBuilder().wpMenus || []).find((x) => x && Array.isArray(x.items) && x.items.length);
  return m ? m.id : 0;
}
