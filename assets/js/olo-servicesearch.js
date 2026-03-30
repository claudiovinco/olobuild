(function () {
  'use strict';

  function initSearchBar(container) {
    var raw = container.getAttribute('data-svsearch-config') || '';
    var config;
    try { config = JSON.parse(atob(raw)); } catch(e) { try { config = JSON.parse(raw); } catch(e2) { config = {}; } }
    var resultsUrl = config.resultsUrl || '/risultati/';

    var btn = container.querySelector('.olo-svsearch-btn');
    if (!btn) return;

    btn.addEventListener('click', function () {
      var params = new URLSearchParams();

      // Collect select/input values
      var selects = container.querySelectorAll('.olo-svsearch-select');
      selects.forEach(function (sel) {
        var param = sel.getAttribute('data-param');
        var val = sel.value;
        if (param && val) {
          params.set(param, val);
        }
      });

      // Collect date inputs
      var inputs = container.querySelectorAll('.olo-svsearch-input');
      inputs.forEach(function (inp) {
        var param = inp.getAttribute('data-param');
        var val = inp.value;
        if (param && val) {
          params.set(param, val);
        }
      });

      // Collect checked amenities
      var amenityCheckboxes = container.querySelectorAll('input[data-param="amenities"]:checked');
      if (amenityCheckboxes.length > 0) {
        var slugs = [];
        amenityCheckboxes.forEach(function (cb) {
          slugs.push(cb.value);
        });
        params.set('amenities', slugs.join(','));
      }

      // Build URL and redirect
      var qs = params.toString();
      var url = resultsUrl + (qs ? '?' + qs : '');
      window.location.href = url;
    });
  }

  function initAll() {
    var containers = document.querySelectorAll('.olo-svsearch[data-svsearch-config]');
    containers.forEach(initSearchBar);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
