/**
 * Indice argomenti (tile «argomenti», Olobuild_Argomenti_Tile): apri e chiudi i rami, la ricerca fra i titoli, il
 * pulsante che apre l'indice sul telefono. Senza questo file l'indice resta leggibile: il ramo aperto lo decide il PHP.
 */
(function () {
  'use strict';

  function avvia(nav) {
    if (nav.dataset.oloArgPronto) return;
    nav.dataset.oloArgPronto = '1';

    var apri = nav.querySelector('.olo-arg-apri');
    if (apri) {
      apri.addEventListener('click', function () {
        var aperto = nav.classList.toggle('is-aperto');
        apri.setAttribute('aria-expanded', aperto ? 'true' : 'false');
      });
    }

    nav.addEventListener('click', function (e) {
      var freccia = e.target.closest('.olo-arg-freccia');
      if (!freccia || !nav.contains(freccia)) return;
      var voce = freccia.closest('.olo-arg-voce');
      var aperta = voce.classList.toggle('is-aperta');
      freccia.setAttribute('aria-expanded', aperta ? 'true' : 'false');
    });

    var campo = nav.querySelector('.olo-arg-cerca input');
    if (!campo) return;
    var voci = Array.prototype.slice.call(nav.querySelectorAll('.olo-arg-voce'));
    var nessuno = nav.querySelector('.olo-arg-nessuno');
    // Come si erano aperti i rami prima di cercare: tornano così quando la casella si svuota.
    var primaAperte = null;

    function normale(s) {
      return String(s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    }

    campo.addEventListener('input', function () {
      var q = normale(campo.value.trim());
      if (!q) {
        voci.forEach(function (v) { v.classList.remove('is-nascosta'); });
        if (primaAperte) {
          voci.forEach(function (v) { v.classList.toggle('is-aperta', primaAperte.indexOf(v) !== -1); });
          primaAperte = null;
        }
        if (nessuno) nessuno.hidden = true;
        return;
      }
      if (!primaAperte) primaAperte = voci.filter(function (v) { return v.classList.contains('is-aperta'); });
      var trovate = 0;
      voci.forEach(function (v) { v.classList.add('is-nascosta'); v.classList.remove('is-aperta'); });
      voci.forEach(function (v) {
        var a = v.querySelector(':scope > .olo-arg-riga a');
        if (!a || normale(a.textContent).indexOf(q) === -1) return;
        trovate += 1;
        v.classList.remove('is-nascosta');
        // Si vedono anche gli argomenti che la contengono, aperti.
        var su = v.parentElement ? v.parentElement.closest('.olo-arg-voce') : null;
        while (su) {
          su.classList.remove('is-nascosta');
          su.classList.add('is-aperta');
          su = su.parentElement ? su.parentElement.closest('.olo-arg-voce') : null;
        }
      });
      if (nessuno) nessuno.hidden = trovate > 0;
    });
  }

  function tutti() {
    Array.prototype.forEach.call(document.querySelectorAll('[data-olo-argomenti]'), avvia);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', tutti);
  else tutti();
  // Le tile disegnate dopo (anteprima del builder, contenuti caricati dopo).
  document.addEventListener('olo:tile-rendered', tutti);
})();
