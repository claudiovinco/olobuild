/**
 * Origine dei messaggi tra il builder e l'anteprima (iframe .olo-live-iframe).
 *
 * Il builder accetta un messaggio solo se arriva dalla FINESTRA dell'iframe e da
 * un'origine ammessa: quella del builder e quelle da cui il PHP serve l'anteprima
 * (home_url e i permalink a cui l'iframe viene reindirizzato in modalità inline).
 * Manda solo all'origine che l'anteprima ha usato, mai a '*': se l'iframe esce
 * verso un altro sito (form con action esterna, meta refresh in una tile HTML),
 * quel sito non riceve la bozza e non può pilotare il builder.
 * Gemello di fromBuilder()/post() in assets/js/iframe-bridge.js.
 */

let origineNota = null; // origine dell'anteprima, dall'ultimo messaggio valido
let avvisato = false;

function origineDi(url) {
  if (!url) return '';
  try {
    const o = new URL(String(url), window.location.href).origin;
    return o && o !== 'null' ? o : '';
  } catch (e) {
    return '';
  }
}

/** Origini da cui l'anteprima può parlare al builder. */
export function previewOrigins() {
  const d = window.oloData || {};
  const out = [];
  [window.location.origin, d.siteInfo && d.siteInfo.home_url, d.home_url, d.postPermalink, d.linkedPostPermalink]
    .forEach((u) => {
      const o = origineDi(u);
      if (o && out.indexOf(o) === -1) out.push(o);
    });
  return out;
}

/**
 * true se il messaggio viene dall'anteprima: stessa finestra dell'iframe e
 * origine ammessa. Si confronta la FINESTRA e non l'URL, perché l'iframe può
 * essere reindirizzato al permalink (modalità inline) e resta la stessa.
 */
export function isFromPreview(event, iframe) {
  if (!iframe || !iframe.contentWindow || event.source !== iframe.contentWindow) return false;
  const ammesse = previewOrigins();
  if (ammesse.indexOf(event.origin) === -1) {
    // Un'origine calcolata male spegnerebbe il canvas in silenzio: lo si dice.
    const d = event.data;
    if (!avvisato && d && typeof d.type === 'string' && d.type.indexOf('olo:') === 0) {
      avvisato = true;
      console.warn('[bridge] messaggio dell\'anteprima scartato: origine', event.origin, '- ammesse:', ammesse.join(', '));
    }
    return false;
  }
  origineNota = event.origin;
  return true;
}

/**
 * Destinazione dei messaggi verso l'anteprima: l'origine che ha usato (nota dal
 * primo messaggio valido, olo:ready); prima di allora quella dell'URL dell'iframe
 * se è ammessa, altrimenti quella del builder. Mai '*'.
 */
export function previewTargetOrigin(iframe) {
  if (origineNota) return origineNota;
  const dallaSrc = iframe ? origineDi(iframe.src) : '';
  return dallaSrc && previewOrigins().indexOf(dallaSrc) !== -1 ? dallaSrc : window.location.origin;
}

export function postToPreview(iframe, msg) {
  if (!iframe || !iframe.contentWindow) return;
  iframe.contentWindow.postMessage(msg, previewTargetOrigin(iframe));
}

/** All'uscita dal canvas: la prossima anteprima riparte da capo. */
export function resetPreviewOrigin() {
  origineNota = null;
}
