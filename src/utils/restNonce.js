// Nonce REST scaduto (scheda del builder aperta da ore): WordPress risponde 403
// rest_cookie_invalid_nonce PRIMA dell'handler, quindi il server non ha scritto
// niente e la richiesta si può ripetere con un nonce nuovo.
//
// Stessa logica di rinnovaNonce() in src/stores/builder.js (salvataggio delle
// zone): lì resta com'è finché le altre aree del lotto toccano quel file, poi
// può importare questa.

/**
 * Chiede un nonce nuovo a WordPress (admin-ajax.php?action=rest-nonce, endpoint
 * del core) e lo scrive DENTRO window.oloData, senza sostituire l'oggetto: store
 * e componenti ne tengono un riferimento. Una risposta che non è un nonce ('0',
 * '-1', 400) vuol dire sessione chiusa: false.
 */
export async function rinnovaNonce() {
  if (typeof window === 'undefined') return false;
  try {
    const base = window.ajaxurl || 'admin-ajax.php';
    const url = base + (base.indexOf('?') === -1 ? '?' : '&') + 'action=rest-nonce';
    const res = await fetch(url, { credentials: 'same-origin', cache: 'no-store' });
    if (!res.ok) return false;
    const nonce = String(await res.text()).trim();
    if (!/^[a-f0-9]{10}$/.test(nonce)) return false;
    if (window.oloData) window.oloData.nonce = nonce;
    if (window.oloThumbConfig) window.oloThumbConfig.nonce = nonce;
    return true;
  } catch (e) {
    return false;
  }
}

/** true se la risposta è il 403 del nonce scaduto (il corpo resta leggibile). */
export async function nonceScaduto(res) {
  if (!res || res.status !== 403) return false;
  try {
    const body = await res.clone().json();
    return !!body && body.code === 'rest_cookie_invalid_nonce';
  } catch (e) {
    return false;
  }
}
