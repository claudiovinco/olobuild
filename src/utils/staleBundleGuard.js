/**
 * staleBundleGuard — il builder non deve MAI girare con un bundle vecchio.
 *
 * PROBLEMA REALE (ripetuto): gli asset escono con `Cache-Control: max-age=604800`.
 * Dopo un deploy l'HTML della pagina builder chiede `builder.js?ver=<versione>.<mtime>`
 * (URL nuovo → download nuovo), ma una scheda rimasta aperta continua a eseguire il
 * bundle che aveva già in memoria. Risultato visto dall'utente: le funzioni appena
 * aggiunte "spariscono" — non sono mai arrivate al suo browser.
 *
 * SOLUZIONE: il bundle conosce la versione con cui è stato compilato
 * (`__OLO_BUNDLE_VERSION__`, iniettata da Vite leggendo olobuild.php). Se il server
 * dichiara un'altra versione (`oloData.version`), il codice in esecuzione è vecchio:
 *   1º incontro → ricarica una volta sola (l'HTML è no-store, quindi torna con l'URL nuovo);
 *   se dopo la ricarica la versione è ancora diversa → niente loop: banner esplicito,
 *   perché a quel punto la copia vecchia arriva da una cache che il reload non buca.
 *
 * La guardia sta in sessionStorage ed è per-versione: un deploy successivo riabilita
 * il giro. Qualsiasi errore di storage non deve impedire l'avvio del builder.
 */

const KEY_PREFIX = 'olo_stale_bundle_';

function safeSessionGet(key) {
  try {
    return window.sessionStorage.getItem(key);
  } catch {
    return null;
  }
}

function safeSessionSet(key, value) {
  try {
    window.sessionStorage.setItem(key, value);
    return true;
  } catch {
    return false;
  }
}

function showBanner(bundleVersion, serverVersion) {
  const el = document.createElement('div');
  el.setAttribute('role', 'alert');
  el.style.cssText = [
    'position:fixed', 'top:0', 'left:0', 'right:0', 'z-index:2147483647',
    'background:#e1474f', 'color:#fff', 'padding:10px 16px',
    'font:600 13px/1.45 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif',
    'display:flex', 'align-items:center', 'gap:12px', 'justify-content:center',
    'box-shadow:0 2px 8px rgba(0,0,0,.25)',
  ].join(';');
  el.textContent =
    `Questo editor sta girando con una versione vecchia (${bundleVersion}); sul server c'è la ${serverVersion}. ` +
    'Ricarica con Ctrl+Shift+R (Cmd+Shift+R su Mac): le funzioni nuove non sono ancora arrivate al browser.';
  const close = document.createElement('button');
  close.type = 'button';
  close.textContent = '✕';
  close.style.cssText = 'appearance:none;border:0;background:transparent;color:#fff;font-size:15px;cursor:pointer;padding:0 4px';
  close.addEventListener('click', () => el.remove());
  el.appendChild(close);
  document.body.appendChild(el);
}

/**
 * @returns {boolean} true se sta ricaricando (il chiamante può saltare il mount).
 */
export function guardStaleBundle() {
  const bundleVersion = typeof __OLO_BUNDLE_VERSION__ === 'string' ? __OLO_BUNDLE_VERSION__ : '';
  const serverVersion = (window.oloData && window.oloData.version) || '';

  if (!bundleVersion || !serverVersion || bundleVersion === serverVersion) {
    return false;
  }

  const key = KEY_PREFIX + serverVersion;
  if (safeSessionGet(key)) {
    // Ricarica già tentata per questa versione: la copia vecchia arriva da una
    // cache che il reload normale non buca → lo diciamo, invece di riprovare.
    if (document.body) showBanner(bundleVersion, serverVersion);
    else document.addEventListener('DOMContentLoaded', () => showBanner(bundleVersion, serverVersion), { once: true });
    return false;
  }

  // Se sessionStorage non è scrivibile non ricarichiamo: senza guardia si rischia
  // un ciclo infinito di reload, che è peggio del bundle vecchio.
  if (!safeSessionSet(key, '1')) {
    if (document.body) showBanner(bundleVersion, serverVersion);
    return false;
  }

  window.location.reload();
  return true;
}
