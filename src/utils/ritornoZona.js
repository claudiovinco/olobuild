/**
 * Ritorno alla pagina di partenza dopo «Apri il template» del chip di zona.
 *
 * Prima di aprire l'header o il footer dal chip (useIframeBridge.js) il builder
 * annota da dove si parte; il builder di quel template, e solo di quello, mostra,
 * in una riga sua sotto la barra, «Torna a «…»» (BuilderToolbar.vue). sessionStorage
 * è per scheda e sopravvive alla navigazione nella stessa scheda: lo stesso header
 * aperto dall'elenco in un'altra scheda non vede niente. Nella stessa scheda decide
 * il referrer: il pulsante c'è solo se al builder si è arrivati da quella pagina, o
 * se lo si è ricaricato dopo esserci arrivati così, comprese le ricariche che
 * avvengono prima che la barra esista (bundle vecchio, nuovo tentativo di App.vue
 * quando il template non si carica): la voce la controlla main.js appena parte,
 * prima di ognuna di quelle, e la barra riceve lo stesso esito. Nessun dato salvato
 * cambia. Senza storage (finestra privata, dati bloccati) o senza referrer tutto
 * funziona come prima, solo senza il pulsante.
 */

const CHIAVE = 'olobuild_ritorno_zona';

function scarta() {
  try { window.sessionStorage.removeItem(CHIAVE); } catch (e) { /* niente storage */ }
}

// Il referrer non porta mai il frammento, location.href sì (#olo-canvas di «Salta al contenuto»).
const senzaHash = (s) => String(s || '').split('#')[0];

/**
 * Solo un indirizzo dello stesso sito che apre il builder di un altro template:
 * un valore rimasto nello storage non deve portare fuori né a sé stesso.
 */
function urlDelBuilder(url, idAperto) {
  try {
    const u = new URL(String(url), window.location.href);
    if (u.origin !== window.location.origin) return '';
    if (!/\/admin\.php$/.test(u.pathname)) return '';
    if (u.searchParams.get('page') !== 'olobuilder-templates') return '';
    if (parseInt(u.searchParams.get('template_id'), 10) === idAperto) return '';
    return u.href;
  } catch (e) {
    return '';
  }
}

/**
 * @param {{ verso: number, url: string, titolo: string, zona: 'header'|'footer' }} voce
 *   verso = id del template di zona che si sta per aprire, url = builder di partenza.
 */
export function ricordaRitorno(voce) {
  try {
    window.sessionStorage.setItem(CHIAVE, JSON.stringify(voce));
  } catch (e) { /* niente storage: niente pulsante */ }
}

// { id, ritorno } del primo controllo di questo caricamento della pagina.
let esito = null;

/**
 * Al caricamento del builder: la voce vale solo per il template annotato e solo se
 * qui si è arrivati dalla pagina annotata, o se ci si è ricaricati da sé dopo che
 * la voce era già stata accettata a questo indirizzo (lo annota `qui`). Una voce
 * per un altro template (si è tornati alla pagina, o si è andati altrove),
 * arrivando da un'altra parte o non valida è vecchia e si toglie.
 *
 * Decide la prima chiamata di ogni caricamento, in main.js prima di ogni ricarica
 * (dopo una ricarica il referrer è la pagina stessa, e vale solo una voce già
 * accettata qui); le chiamate seguenti per lo stesso template, come quella di
 * BuilderToolbar.vue, ricevono lo stesso esito senza rileggere lo storage.
 * @param {number} idAperto  id del template aperto nel builder
 * @returns {null|{ url: string, titolo: string, zona: 'header'|'footer' }}
 */
export function ritornoPer(idAperto) {
  const id = parseInt(idAperto, 10) || 0;
  if (!esito || esito.id !== id) esito = { id, ritorno: controlla(id) };
  return esito.ritorno;
}

function controlla(id) {
  let voce;
  try {
    const raw = window.sessionStorage.getItem(CHIAVE);
    if (!raw) return null;
    voce = JSON.parse(raw);
  } catch (e) {
    voce = null;
  }
  const url = voce && id > 0 && parseInt(voce.verso, 10) === id ? urlDelBuilder(voce.url, id) : '';
  // Dall'elenco dei template, dalla bacheca o dalla barra di amministrazione il referrer
  // è un altro: la voce è rimasta da un giro precedente (o da un «Resta» nella conferma
  // di uscita). F5 e Indietro/Avanti tengono il referrer dell'arrivo; location.reload()
  // (bundle vecchio, nuovo tentativo di App.vue, import di un tema, «Ripristina» dello
  // stile, ritorno dalla bfcache in builder-page.php) mette in Chrome la pagina stessa,
  // e da lì anche F5: vale solo se la voce era già stata accettata qui, non per una
  // rimasta da un «Resta» o da altrove.
  const qui = senzaHash(window.location.href);
  let ref = '';
  try { ref = senzaHash(document.referrer); } catch (e) { /* niente */ }
  const daLi = !!url && (ref === senzaHash(url) || (voce.qui === qui && ref === qui));
  if (!daLi || (voce.zona !== 'header' && voce.zona !== 'footer')) {
    scarta();
    return null;
  }
  if (voce.qui !== qui) {
    try { window.sessionStorage.setItem(CHIAVE, JSON.stringify({ ...voce, qui })); } catch (e) { /* niente storage */ }
  }
  const titolo = typeof voce.titolo === 'string' ? voce.titolo.trim() : '';
  return { url, titolo, zona: voce.zona };
}
