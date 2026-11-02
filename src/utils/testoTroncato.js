/**
 * Scritte tagliate dei pannelli («Sini…», un nome lungo nella struttura, un valore nel menu a tendina): passandoci
 * sopra compare la scritta intera in un popup, subito, senza aspettare il suggerimento del browser (utente, 2 nov
 * 2026: «lascia il testo solo in un popup automatico… cerca ovunque questo problema»). Un solo ascoltatore per pagina,
 * vale per ogni elemento con `text-overflow: ellipsis` (o `-webkit-line-clamp`) che non ci sta, in qualunque
 * componente: nessuno deve ricordarsi di aggiungerlo. Il `title` nativo, se c'è, si mette da parte finché il popup è
 * aperto (se no comparivano due suggerimenti).
 */
let installato = false;

export function installaTestoTroncato() {
  if (installato || typeof document === 'undefined') return;
  installato = true;

  let tip = null;
  let corrente = null;
  let titoloMesso = null;

  const troncato = (el) => {
    if (!(el instanceof HTMLElement) || el.closest('input, textarea, select, [contenteditable="true"]')) return false;
    const cs = getComputedStyle(el);
    if (cs.textOverflow === 'ellipsis' && el.scrollWidth > el.clientWidth + 1) return true;
    const clamp = cs.webkitLineClamp || cs.getPropertyValue('-webkit-line-clamp');
    return !!clamp && clamp !== 'none' && el.scrollHeight > el.clientHeight + 1;
  };
  // Il primo, risalendo di poco dal punto sotto il mouse, che è tagliato.
  const trova = (t) => {
    let el = t;
    for (let n = 0; el && n < 4; n++, el = el.parentElement) {
      if (troncato(el)) return el;
    }
    return null;
  };

  function nascondi() {
    if (tip) tip.style.display = 'none';
    if (corrente && titoloMesso !== null) corrente.setAttribute('title', titoloMesso);
    corrente = null;
    titoloMesso = null;
  }

  function mostra(el) {
    const testo = (el.innerText || el.textContent || '').replace(/\s+/g, ' ').trim();
    if (!testo) return;
    if (!tip) {
      tip = document.createElement('div');
      tip.className = 'olo-tip-troncato';
      tip.setAttribute('role', 'tooltip');
      tip.style.cssText = 'position:fixed;z-index:2147483000;max-width:320px;padding:5px 9px;border-radius:6px;'
        + 'background:#111827;color:#fff;font:500 12px/1.35 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;'
        + 'box-shadow:0 6px 16px rgba(16,24,40,.22);pointer-events:none;white-space:normal;overflow-wrap:anywhere;display:none';
      document.body.appendChild(tip);
    }
    corrente = el;
    titoloMesso = el.hasAttribute('title') ? el.getAttribute('title') : null;
    if (titoloMesso !== null) el.removeAttribute('title');
    tip.textContent = testo;
    tip.style.display = 'block';
    const r = el.getBoundingClientRect();
    const w = tip.offsetWidth;
    const h = tip.offsetHeight;
    const x = Math.max(6, Math.min(r.left + r.width / 2 - w / 2, window.innerWidth - w - 6));
    const y = r.top - h - 6 >= 4 ? r.top - h - 6 : r.bottom + 6;
    tip.style.left = Math.round(x) + 'px';
    tip.style.top = Math.round(y) + 'px';
  }

  document.addEventListener('mouseover', (e) => {
    const el = trova(e.target);
    if (el === corrente) return;
    nascondi();
    if (el) mostra(el);
  }, true);
  document.addEventListener('mouseout', (e) => {
    if (corrente && (!e.relatedTarget || !corrente.contains(e.relatedTarget))) nascondi();
  }, true);
  document.addEventListener('mousedown', nascondi, true);
  window.addEventListener('scroll', nascondi, true);
  window.addEventListener('blur', nascondi);
}
