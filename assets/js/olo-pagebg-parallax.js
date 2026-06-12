/**
 * Olobuild Page Background Parallax
 *
 * Parallasse del layer sfondo-pagina (div.olo-tile-bg dentro .olo-template).
 * Sostituisce uk-parallax su questo layer: essendo alto quanto l'INTERA pagina,
 * UIkit ne diluiva il progresso su tutto lo scroll (movimento impercettibile).
 * Qui il progresso 0→1 si completa scorrendo ~1.2 schermate, dove l'immagine
 * di sfondo è effettivamente visibile.
 *
 * Config: attributo data-olo-pagebg-parallax = JSON
 *   { bgx|bgy|scale|opacity|blur: [{value, position(0-100)}, …], nomobile: bool }
 * Stesso formato stops del ParallaxEditor (e di build_parallax_attr_from_object).
 */
(function () {
  'use strict';

  // Interpolazione lineare multi-stop. Uno stop solo = "muovi di X" (X → 0),
  // come la resa storica uk-parallax con valore singolo.
  function interp(stops, p) {
    if (!Array.isArray(stops) || !stops.length) return null;
    var pts = stops.map(function (s) {
      return { v: parseFloat(s.value) || 0, p: (parseFloat(s.position) || 0) / 100 };
    }).sort(function (a, b) { return a.p - b.p; });
    if (pts.length === 1) pts.push({ v: 0, p: 1 });
    if (p <= pts[0].p) return pts[0].v;
    for (var i = 1; i < pts.length; i++) {
      if (p <= pts[i].p) {
        var a = pts[i - 1], b = pts[i];
        var t = (p - a.p) / ((b.p - a.p) || 1);
        return a.v + (b.v - a.v) * t;
      }
    }
    return pts[pts.length - 1].v;
  }

  function setup(el) {
    var cfg;
    try { cfg = JSON.parse(el.getAttribute('data-olo-pagebg-parallax')); } catch (e) { return; }
    if (!cfg || typeof cfg !== 'object') return;

    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)');
    var mq = cfg.nomobile === false ? null : window.matchMedia('(min-width: 960px)');
    var basePos = null;
    var ticking = false;

    function ensureBase() {
      if (basePos) return;
      // Base = background-position configurata (es. "top center" → "50% 0%").
      var bp = getComputedStyle(el).backgroundPosition.split(' ');
      basePos = [bp[0] || '50%', bp[1] || '0%'];
    }

    function reset() {
      el.style.backgroundPosition = '';
      el.style.transform = '';
      el.style.opacity = '';
      el.style.filter = '';
    }

    function apply() {
      ticking = false;
      if (reduce.matches || (mq && !mq.matches)) { reset(); return; }
      var root = document.scrollingElement || document.documentElement;
      var vh = window.innerHeight || 1;
      var maxScroll = Math.max(1, root.scrollHeight - vh);
      var dist = Math.min(vh * 1.2, maxScroll);
      var p = Math.min(1, Math.max(0, (window.pageYOffset || root.scrollTop || 0) / dist));

      var x = interp(cfg.bgx, p);
      var y = interp(cfg.bgy, p);
      if (x !== null || y !== null) {
        ensureBase();
        el.style.backgroundPosition =
          'calc(' + basePos[0] + ' + ' + (x || 0).toFixed(2) + 'px) ' +
          'calc(' + basePos[1] + ' + ' + (y || 0).toFixed(2) + 'px)';
      }
      var s = interp(cfg.scale, p);
      if (s !== null) el.style.transform = (Math.abs(s - 1) > 0.001) ? 'scale(' + s.toFixed(4) + ')' : '';
      var o = interp(cfg.opacity, p);
      if (o !== null) el.style.opacity = String(Math.min(1, Math.max(0, o)));
      var bl = interp(cfg.blur, p);
      if (bl !== null) el.style.filter = bl > 0.01 ? 'blur(' + bl.toFixed(2) + 'px)' : '';
    }

    function schedule() {
      if (!ticking) { ticking = true; requestAnimationFrame(apply); }
    }

    window.addEventListener('scroll', schedule, { passive: true });
    window.addEventListener('resize', schedule, { passive: true });
    if (mq && mq.addEventListener) mq.addEventListener('change', schedule);
    apply();
  }

  function init() {
    document.querySelectorAll('[data-olo-pagebg-parallax]').forEach(setup);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
