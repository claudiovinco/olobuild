/**
 * Olobuild — Row Loop frontend pagination handler.
 *
 * Listener delegato sul `<body>` per i click sui bottoni "Carica altri" generati
 * da `Olo_Frontend_Renderer::render_row_loop_pagination()`. Chiama il REST endpoint
 * `olobuild/v1/row-loop/page` e appende l'HTML restituito al container della Row.
 */
(function() {
  'use strict';

  if (window.__oloRowLoopInit) return;
  window.__oloRowLoopInit = true;

  function getRestBase() {
    return (window.oloFrontendData && window.oloFrontendData.restUrl)
      || (window.wpApiSettings && window.wpApiSettings.root + 'olobuild/v1')
      || '/wp-json/olobuild/v1';
  }

  function findContainer(rowId) {
    return document.querySelector('[data-olo-loop-row-container="' + rowId + '"]');
  }

  function setLoading(btn, on) {
    if (on) {
      btn.dataset.oloLoopLoading = '1';
      btn.style.opacity = '0.6';
      btn.style.pointerEvents = 'none';
    } else {
      delete btn.dataset.oloLoopLoading;
      btn.style.opacity = '';
      btn.style.pointerEvents = '';
    }
  }

  document.addEventListener('click', function(e) {
    var btn = e.target.closest('.olo-loop-load-more');
    if (!btn) return;
    e.preventDefault();
    if (btn.dataset.oloLoopLoading === '1') return;

    var rowId      = btn.getAttribute('data-olo-loop-row');
    var currentPage = parseInt(btn.getAttribute('data-olo-loop-page') || '1', 10);
    var maxPage    = parseInt(btn.getAttribute('data-olo-loop-max') || '1', 10);
    if (!rowId || currentPage >= maxPage) return;

    var container = findContainer(rowId);
    if (!container) {
      console.warn('[olo-row-loop] container non trovato per row', rowId);
      return;
    }

    var templateId = parseInt(container.getAttribute('data-olo-loop-template-id') || '0', 10);
    if (!templateId) {
      console.warn('[olo-row-loop] template_id mancante sul container');
      return;
    }

    var nextPage = currentPage + 1;
    setLoading(btn, true);

    fetch(getRestBase() + '/row-loop/page', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ template_id: templateId, row_id: rowId, page: nextPage }),
    })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (!data || typeof data.html !== 'string') {
          throw new Error('Risposta non valida');
        }
        // Append children — usa un wrapper temporaneo per parserizzare l'HTML
        var tmp = document.createElement('div');
        tmp.innerHTML = data.html;
        // Sposta tutti i nodi (elementi + text) al container reale
        while (tmp.firstChild) {
          container.appendChild(tmp.firstChild);
        }

        // Aggiorna stato bottone
        btn.setAttribute('data-olo-loop-page', String(nextPage));
        if (!data.has_more || nextPage >= maxPage) {
          // Nasconde il bottone quando non ci sono più pagine
          var wrapper = btn.closest('.olo-loop-pagination');
          if (wrapper) wrapper.remove();
          else btn.remove();
        }

        // Re-init di tile dinamici (slider, gallery, ecc.) sui nuovi nodi.
        // UIkit fa l'auto-discovery via MutationObserver, quindi nella maggior parte
        // dei casi non serve nulla. Questi sono i fallback per i tile custom.
        if (window.UIkit && typeof window.UIkit.update === 'function') {
          try { window.UIkit.update(container); } catch (e) {}
        }
        // ProSlider, PostGrid: hanno init globali idempotenti. Li ri-triggeriamo.
        if (typeof window.oloProSliderInit === 'function') {
          try { window.oloProSliderInit(container); } catch (e) {}
        }
        if (typeof window.oloPostGridInit === 'function') {
          try { window.oloPostGridInit(container); } catch (e) {}
        }
      })
      .catch(function(err) {
        console.error('[olo-row-loop] errore caricamento pagina', err);
      })
      .finally(function() {
        setLoading(btn, false);
      });
  });
})();
