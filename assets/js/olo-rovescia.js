/**
 * Scorrimento alla rovescia (Effetti di pagina → «Scorrimento alla rovescia», OLObuild 1.4.957).
 *
 * Il PHP mette sull'html le classi olo-rovescia (le sezioni dal basso verso l'alto, la testata in fondo, il piè di
 * pagina in cima) e olo-rovescia-attesa (la pagina resta invisibile finché non è portata in fondo; senza questo file
 * si mostra da sola dopo 1,6 s). Qui:
 *   - la pagina parte dal fondo e ci resta mentre foto e tile pigre la allungano, finché chi legge non si muove;
 *   - la rotella e i tasti vanno al contrario (data-rovescia-rotella="1"): in giù si sale, con un moto morbido;
 *     dentro un riquadro che scorre da sé (un pannello, una lista) la rotella resta quella di sempre;
 *   - il pulsante «torna in cima» riporta all'inizio, cioè in fondo, e ne prende il nome.
 * Sul telefono il dito trascina la pagina come sempre: per salire si trascina in giù.
 */
(function () {
  'use strict';
  var html = document.documentElement;
  if (!html.classList.contains('olo-rovescia')) { return; }
  var rotella = html.getAttribute('data-rovescia-rotella') !== '0';
  var meno = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var mosso = false;

  if ('scrollRestoration' in history) { history.scrollRestoration = 'manual'; }

  function fondo() { return Math.max(0, html.scrollHeight - window.innerHeight); }
  function vai(y) { window.scrollTo({ top: y, left: 0, behavior: 'instant' }); }

  // ── la partenza dal fondo ──
  function inFondo() {
    if (mosso || location.hash.length > 1) { return; }
    vai(fondo());
  }
  function mostra() { html.classList.remove('olo-rovescia-attesa'); }
  function parti() {
    inFondo();
    requestAnimationFrame(function () { inFondo(); mostra(); });
  }
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', parti); } else { parti(); }
  window.addEventListener('load', function () { inFondo(); setTimeout(inFondo, 400); setTimeout(inFondo, 1200); });
  if (window.ResizeObserver) {
    var ro = new ResizeObserver(function () { inFondo(); });
    ro.observe(document.body);
    setTimeout(function () { ro.disconnect(); }, 6000);
  }
  ['wheel', 'touchstart', 'keydown', 'mousedown'].forEach(function (ev) {
    window.addEventListener(ev, function () { mosso = true; }, { passive: true, capture: true });
  });

  // ── il torna in cima che torna all'inizio ──
  var inizio = html.getAttribute('data-rovescia-inizio') || '';
  function rinomina() {
    document.querySelectorAll('.olo-totop-btn').forEach(function (b) { if (inizio) { b.setAttribute('aria-label', inizio); } });
  }
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', rinomina); } else { rinomina(); }
  document.addEventListener('click', function (e) {
    var b = e.target.closest ? e.target.closest('.olo-totop-btn') : null;
    if (!b) { return; }
    e.preventDefault();
    e.stopImmediatePropagation();
    window.scrollTo({ top: fondo(), left: 0, behavior: meno ? 'instant' : 'smooth' });
  }, true);

  if (!rotella) { return; }

  // ── la rotella al contrario ──
  var meta = null, giro = 0;
  function passo() {
    var y = window.pageYOffset, d = meta - y;
    if (Math.abs(d) < 0.6) { vai(meta); giro = 0; meta = null; return; }
    vai(y + d * 0.22);
    giro = requestAnimationFrame(passo);
  }
  function muovi(dy) {
    if (meta === null) { meta = window.pageYOffset; }
    meta = Math.max(0, Math.min(fondo(), meta + dy));
    if (meno) { vai(meta); meta = null; return; }
    if (!giro) { giro = requestAnimationFrame(passo); }
  }
  // l'utente trascina la barra o il dito: la meta riparte da dove si trova
  window.addEventListener('scroll', function () { if (!giro) { meta = null; } }, { passive: true });

  function scorreDaSe(el, dy) {
    for (; el && el !== document.body && el !== html; el = el.parentElement) {
      var s = getComputedStyle(el), o = s.overflowY;
      if ((o === 'auto' || o === 'scroll') && el.scrollHeight > el.clientHeight + 1) {
        if (dy < 0 ? el.scrollTop > 0 : el.scrollTop + el.clientHeight < el.scrollHeight - 1) { return true; }
      }
    }
    return false;
  }
  window.addEventListener('wheel', function (e) {
    if (e.ctrlKey || e.defaultPrevented || Math.abs(e.deltaX) > Math.abs(e.deltaY)) { return; }
    if (scorreDaSe(e.target, e.deltaY)) { return; }
    e.preventDefault();
    var k = e.deltaMode === 1 ? 40 : (e.deltaMode === 2 ? window.innerHeight : 1);
    muovi(-e.deltaY * k);
  }, { passive: false });

  window.addEventListener('keydown', function (e) {
    if (e.defaultPrevented || e.altKey || e.ctrlKey || e.metaKey) { return; }
    var t = e.target;
    if (t && (t.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(t.tagName))) { return; }
    var h = window.innerHeight * 0.85, dy = null;
    switch (e.key) {
      case 'ArrowDown': dy = -70; break;
      case 'ArrowUp': dy = 70; break;
      case 'PageDown': dy = -h; break;
      case 'PageUp': dy = h; break;
      case ' ':
        if (t && t !== document.body && t !== html) { return; }
        dy = e.shiftKey ? h : -h; break;
      case 'Home': dy = fondo(); break;
      case 'End': dy = -fondo(); break;
      default: return;
    }
    e.preventDefault();
    muovi(dy);
  });
})();
