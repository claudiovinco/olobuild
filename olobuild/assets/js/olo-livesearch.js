/**
 * Olobuild LiveSearch — frontend vanilla JS.
 * Modes: expanded, compact, modal.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var widgets = document.querySelectorAll('.olo-livesearch[data-livesearch]');
    widgets.forEach(initWidget);
  });

  function initWidget(el) {
    var raw = el.getAttribute('data-livesearch');
    if (!raw) return;
    var cfg;
    try { cfg = JSON.parse(atob(raw)); } catch (e) { return; }

    var input     = el.querySelector('.olo-ls-input');
    var clearBtn  = el.querySelector('.olo-ls-clear');
    var dropdown  = el.querySelector('.olo-ls-dropdown');
    var resultsEl = el.querySelector('.olo-ls-results');
    var footerEl  = el.querySelector('.olo-ls-footer');
    var emptyEl   = el.querySelector('.olo-ls-empty');
    var trigger   = el.querySelector('.olo-ls-trigger');
    var fieldWrap = el.querySelector('.olo-ls-field');

    // Modal elements
    var overlay   = el.querySelector('.olo-ls-overlay');
    var backdrop  = el.querySelector('.olo-ls-backdrop');

    if (!input || !dropdown || !resultsEl) return;

    var debounceTimer = null;
    var controller    = null;
    var activeIndex   = -1;
    var isOpen        = false;
    var isExpanded    = cfg.mode !== 'compact';

    // ═══ MODAL MODE ═══
    var modalIsOpen = false;

    if (cfg.mode === 'modal' && overlay && trigger) {
      // Sposta overlay in <body> per evitare clip da parent overflow/z-index
      document.body.appendChild(overlay);

      // Re-query gli elementi perché ora sono in <body>
      input     = overlay.querySelector('.olo-ls-input');
      clearBtn  = overlay.querySelector('.olo-ls-clear');
      dropdown  = overlay.querySelector('.olo-ls-dropdown');
      resultsEl = overlay.querySelector('.olo-ls-results');
      footerEl  = overlay.querySelector('.olo-ls-footer');
      emptyEl   = overlay.querySelector('.olo-ls-empty');
      backdrop  = overlay.querySelector('.olo-ls-backdrop');

      trigger.addEventListener('click', function () {
        openModal();
      });

      backdrop.addEventListener('click', function () {
        closeModal();
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalIsOpen) {
          closeModal();
        }
      });
    }

    function openModal() {
      modalIsOpen = true;
      document.body.style.overflow = 'hidden';
      // Frame 1: rende visibile (visibility), frame 2: anima (opacity/transform)
      requestAnimationFrame(function () {
        overlay.classList.add('olo-ls-overlay--open');
        setTimeout(function () { input.focus(); }, 200);
      });
    }

    function closeModal() {
      overlay.classList.remove('olo-ls-overlay--open');
      document.body.style.overflow = '';
      modalIsOpen = false;
      isOpen = false;
      activeIndex = -1;
      setTimeout(function () {
        input.value = '';
        toggleClear(false);
        dropdown.hidden = true;
        dropdown.classList.remove('olo-ls-dropdown--open');
        resultsEl.innerHTML = '';
      }, 350);
    }

    // ═══ COMPACT MODE ═══
    if (cfg.mode === 'compact' && trigger && fieldWrap) {
      trigger.addEventListener('click', function () {
        isExpanded = true;
        fieldWrap.classList.remove('olo-ls-field--hidden');
        trigger.hidden = true;
        input.focus();
      });

      input.addEventListener('blur', function () {
        if (cfg.mode === 'compact' && input.value.trim() === '' && !isOpen) {
          setTimeout(function () {
            if (!isOpen && input.value.trim() === '') {
              isExpanded = false;
              fieldWrap.classList.add('olo-ls-field--hidden');
              trigger.hidden = false;
            }
          }, 200);
        }
      });
    }

    // ─── Input events ───
    input.addEventListener('input', function () {
      var val = input.value.trim();
      toggleClear(val.length > 0);

      if (val.length < (cfg.minChars || 2)) {
        closeDropdown();
        return;
      }

      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function () { doSearch(val); }, cfg.debounce || 300);
    });

    // ─── Clear ───
    if (clearBtn) {
      clearBtn.addEventListener('click', function (e) {
        e.preventDefault();
        input.value = '';
        toggleClear(false);
        closeDropdown();
        input.focus();
      });
    }

    // ─── Keyboard navigation ───
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        if (cfg.mode === 'modal') {
          closeModal();
        } else {
          closeDropdown();
        }
        return;
      }
      if (!isOpen) return;
      var items = resultsEl.querySelectorAll('.olo-ls-item');
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeIndex = Math.min(activeIndex + 1, items.length - 1);
        highlightItem(items);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeIndex = Math.max(activeIndex - 1, 0);
        highlightItem(items);
      } else if (e.key === 'Enter') {
        e.preventDefault();
        if (activeIndex >= 0 && items[activeIndex]) {
          var link = items[activeIndex].querySelector('a');
          if (link) window.location.href = link.href;
        }
      }
    });

    // ─── Click outside (non-modal) ───
    if (cfg.mode !== 'modal') {
      document.addEventListener('click', function (e) {
        if (!el.contains(e.target)) {
          closeDropdown();
        }
      });
    }

    // ─── Search ───
    function doSearch(q) {
      if (controller) controller.abort();
      controller = new AbortController();

      var url = cfg.restUrl +
        '?q=' + encodeURIComponent(q) +
        '&post_types=' + encodeURIComponent(cfg.postTypes || 'post,page') +
        '&max=' + (cfg.maxResults || 10) +
        '&title_only=' + (cfg.titleOnly ? 1 : 0) +
        '&thumb=' + (cfg.showThumb ? 1 : 0);

      if (cfg.taxonomy) url += '&taxonomy=' + encodeURIComponent(cfg.taxonomy);
      if (cfg.terms)    url += '&terms=' + encodeURIComponent(cfg.terms);
      if (cfg.exclude)  url += '&exclude=' + encodeURIComponent(cfg.exclude);

      fetch(url, { signal: controller.signal })
        .then(function (r) { return r.json(); })
        .then(function (data) { renderResults(data, q); })
        .catch(function (err) {
          if (err.name !== 'AbortError') console.error('LiveSearch:', err);
        });
    }

    // ─── Render ───
    var isGrid = (cfg.columns || 1) > 1;

    function renderResults(data, query) {
      resultsEl.innerHTML = '';
      activeIndex = -1;

      // Toggle grid mode class
      resultsEl.classList.toggle('olo-ls-results--grid', isGrid);

      var results = data.results || [];
      var total   = data.total || 0;

      if (results.length === 0) {
        if (emptyEl) { emptyEl.hidden = false; }
        if (footerEl) { footerEl.hidden = true; }
        openDropdown();
        return;
      }

      if (emptyEl) { emptyEl.hidden = true; }

      results.forEach(function (item, idx) {
        var div = document.createElement('div');
        div.className = 'olo-ls-item';
        div.setAttribute('role', 'option');
        div.setAttribute('data-index', idx);

        var linkClass = isGrid ? 'olo-ls-item-link olo-ls-item-link--card' : 'olo-ls-item-link';
        var html = '<a href="' + escHtml(item.url) + '" class="' + linkClass + '">';

        if (cfg.showThumb && item.thumbnail) {
          html += '<img class="olo-ls-thumb" src="' + escHtml(item.thumbnail) + '" alt="" loading="lazy">';
        } else if (cfg.showThumb) {
          html += '<span class="olo-ls-thumb olo-ls-thumb--empty"></span>';
        }

        html += '<span class="olo-ls-item-text">';
        html += '<span class="olo-ls-item-title">' + highlightMatch(item.title, query) + '</span>';
        if (item.excerpt) {
          html += '<span class="olo-ls-item-excerpt">' + escHtml(item.excerpt) + '</span>';
        }
        html += '</span></a>';

        div.innerHTML = html;
        resultsEl.appendChild(div);
      });

      if (footerEl && cfg.showAllUrl && total > results.length) {
        var allUrl = cfg.showAllUrl;
        if (allUrl.indexOf('?') === -1) {
          allUrl += '?s=' + encodeURIComponent(query);
        } else {
          allUrl += '&s=' + encodeURIComponent(query);
        }
        footerEl.innerHTML = '<a href="' + escHtml(allUrl) + '">' +
          escHtml(cfg.showAllText || 'Vedi tutti i risultati') +
          ' <span class="olo-ls-total">(' + total + ')</span></a>';
        footerEl.hidden = false;
      } else if (footerEl) {
        footerEl.hidden = true;
      }

      openDropdown();
    }

    // ─── Helpers ───
    function openDropdown() {
      dropdown.hidden = false;
      isOpen = true;
      void dropdown.offsetHeight;
      dropdown.classList.add('olo-ls-dropdown--open');
    }

    function closeDropdown() {
      dropdown.classList.remove('olo-ls-dropdown--open');
      isOpen = false;
      activeIndex = -1;
      setTimeout(function () {
        if (!isOpen) dropdown.hidden = true;
      }, 200);
    }

    function toggleClear(show) {
      if (clearBtn) clearBtn.hidden = !show;
    }

    function highlightItem(items) {
      items.forEach(function (it, i) {
        it.classList.toggle('olo-ls-item--active', i === activeIndex);
      });
      if (items[activeIndex]) {
        items[activeIndex].scrollIntoView({ block: 'nearest' });
      }
    }

    function highlightMatch(text, query) {
      if (!query) return escHtml(text);
      var safe  = escHtml(text);
      var regex = new RegExp('(' + escRegex(query) + ')', 'gi');
      return safe.replace(regex, '<mark>$1</mark>');
    }

    function escHtml(str) {
      if (!str) return '';
      var div = document.createElement('div');
      div.appendChild(document.createTextNode(str));
      return div.innerHTML;
    }

    function escRegex(str) {
      return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
  }
})();
