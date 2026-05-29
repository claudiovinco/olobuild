// Canale standalone per richieste di "scrolla all'tile X nel canvas iframe".
// Usato da StructureTree (click su nodo) → useIframeBridge (postMessage olo:scroll-to).
// Non passa dallo store Pinia (memoria: scroll-flash deve restare fuori da builder.js).

var EVT = 'olo:request-scroll-to-tile';

export function requestScrollToTile(tileId) {
  if (!tileId) return;
  try {
    window.dispatchEvent(new CustomEvent(EVT, { detail: { tileId: tileId } }));
  } catch (e) { /* ignore */ }
}

export function onScrollToTileRequest(handler) {
  function wrapped(e) { handler(e && e.detail && e.detail.tileId); }
  window.addEventListener(EVT, wrapped);
  return function unsubscribe() { window.removeEventListener(EVT, wrapped); };
}
