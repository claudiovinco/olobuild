/**
 * Il sito si addormenta (Effetti di pagina → «Il sito si addormenta», OLObuild 1.4.957).
 *
 * Dopo data-secondi secondi senza un movimento (mouse, rotella, tasti, dito, scorrimento) il velo .olo-sonno scende
 * sulla pagina con le Z che salgono e la scritta; al primo movimento si risveglia (lo sbadiglio dura un attimo). Il velo
 * non riceve mai i click. Con meno movimento le Z restano ferme. Nella scheda nascosta si dorme già: niente da fare.
 */
(function () {
  'use strict';
  var velo = document.querySelector('.olo-sonno');
  if (!velo) { return; }
  // I token del tema stanno sul template: il velo li porta con sé quando va nel body (lì un fixed è davvero sullo
  // schermo, dentro il template un transform lo aggancerebbe al template).
  var tpl = velo.closest('.olo-template');
  if (tpl) {
    var cs = getComputedStyle(tpl);
    ['--olo-color-dark', '--olo-color-light', '--olo-font-family-heading', '--olo-font-family'].forEach(function (k) {
      var v = cs.getPropertyValue(k);
      if (v) { velo.style.setProperty(k, v.trim()); }
    });
  }
  document.body.appendChild(velo);
  velo.hidden = false;
  var attesa = Math.max(3, parseInt(velo.getAttribute('data-secondi'), 10) || 15) * 1000;
  var timer = 0, dorme = false;

  function dormi() {
    if (document.hidden) { return; }
    dorme = true;
    velo.classList.add('olo-sonno--dorme');
    document.documentElement.classList.add('olo-dorme');
  }
  function sveglia() {
    if (dorme) {
      dorme = false;
      velo.classList.remove('olo-sonno--dorme');
      document.documentElement.classList.remove('olo-dorme');
    }
    clearTimeout(timer);
    timer = setTimeout(dormi, attesa);
  }
  ['pointermove', 'pointerdown', 'wheel', 'keydown', 'touchstart', 'scroll'].forEach(function (ev) {
    window.addEventListener(ev, sveglia, { passive: true, capture: true });
  });
  document.addEventListener('visibilitychange', sveglia);
  sveglia();
})();
