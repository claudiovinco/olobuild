/**
 * Olobuilder – Post Grid: Filtering + Sorting + Pagination
 */
(function () {
  'use strict';

  function initPostGrid(container) {
    var items = container.querySelectorAll('.olo-postgrid-item');
    var grid  = container.querySelector('.olo-postgrid-grid');
    var empty = container.querySelector('.olo-postgrid-empty');

    if (!grid || !items.length) return;

    container._pgItems = Array.from(items);
    container._pgGrid  = grid;
    container._pgEmpty = empty;
    container._pgActiveFilter = '';
    container._pgCurrentPage = 1;
    container._pgLoadmorePages = 1;

    // Parse config
    var configStr = container.getAttribute('data-postgrid-config');
    var config = {};
    try { config = JSON.parse(configStr); } catch (e) { console.warn('PostGrid config parse error:', e); }
    container._pgConfig = config;

    initFilters(container);
    initSort(container);
    if (config.paginationEnabled) {
      initPagination(container);
    }
  }

  function initFilters(container) {
    var gridId = container.id;
    if (!gridId) return;

    var filtersContainer = container.querySelector('.olo-postgrid-filters[data-postgrid-target="' + gridId + '"]');
    if (!filtersContainer) return;

    // Pills
    var pills = filtersContainer.querySelectorAll('.olo-postgrid-pill');
    pills.forEach(function (pill) {
      pill.addEventListener('click', function () {
        pills.forEach(function (p) { p.classList.remove('olo-postgrid-pill-active'); });
        pill.classList.add('olo-postgrid-pill-active');
        container._pgActiveFilter = pill.getAttribute('data-filter') || '';
        applyFilter(container);
      });
    });

    // Dropdown
    var select = filtersContainer.querySelector('[data-postgrid-filter-select]');
    if (select) {
      select.addEventListener('change', function () {
        container._pgActiveFilter = select.value;
        applyFilter(container);
      });
    }
  }

  function applyFilter(container) {
    var termSlug     = container._pgActiveFilter;
    var visibleCount = 0;

    container._pgItems.forEach(function (item) {
      var terms = (item.getAttribute('data-terms') || '').split(',').filter(Boolean);
      var show  = !termSlug || terms.indexOf(termSlug) !== -1;
      item._pgFiltered = !show;
      if (show) visibleCount++;
    });

    // Reset pagination on filter change
    container._pgCurrentPage = 1;
    container._pgLoadmorePages = 1;

    if (container._pgConfig.paginationEnabled) {
      applyPagination(container);
    } else {
      // No pagination — just show/hide
      container._pgItems.forEach(function (item) {
        item.style.display = item._pgFiltered ? 'none' : '';
      });
    }

    if (container._pgEmpty) {
      container._pgEmpty.style.display = visibleCount === 0 ? '' : 'none';
    }
  }

  function initSort(container) {
    var select = container.querySelector('[data-postgrid-sort]');
    if (!select) return;

    select.addEventListener('change', function () {
      applySort(container, select.value);
    });
  }

  function applySort(container, sortKey) {
    if (sortKey === 'default') return;

    var parts     = sortKey.split('-');
    var field     = parts[0];
    var direction = parts[1];

    var items = container._pgItems.slice();

    items.sort(function (a, b) {
      var aVal, bVal;

      if (field === 'price') {
        aVal = parseFloat(a.getAttribute('data-price')) || 0;
        bVal = parseFloat(b.getAttribute('data-price')) || 0;
      } else if (field === 'date') {
        aVal = a.getAttribute('data-date') || '';
        bVal = b.getAttribute('data-date') || '';
      } else if (field === 'title') {
        aVal = (a.getAttribute('data-title') || '').toLowerCase();
        bVal = (b.getAttribute('data-title') || '').toLowerCase();
      } else {
        return 0;
      }

      var cmp;
      if (typeof aVal === 'number' && typeof bVal === 'number') {
        cmp = aVal - bVal;
      } else {
        cmp = aVal < bVal ? -1 : (aVal > bVal ? 1 : 0);
      }

      return direction === 'desc' ? -cmp : cmp;
    });

    // Re-append sorted items to grid
    var grid = container._pgGrid;
    items.forEach(function (item) {
      grid.appendChild(item);
    });

    // Update internal items list to reflect new order
    container._pgItems = items;

    // Reset pagination on sort
    container._pgCurrentPage = 1;
    container._pgLoadmorePages = 1;

    if (container._pgConfig.paginationEnabled) {
      applyPagination(container);
    }
  }

  /* ── Pagination ── */

  function initPagination(container) {
    var pag = container.querySelector('.olo-pg-pagination');
    if (!pag) return;
    container._pgPagEl = pag;

    var style = container._pgConfig.paginationStyle || 'dots';

    if (style === 'arrows') {
      var prevBtn = pag.querySelector('.olo-pg-prev');
      var nextBtn = pag.querySelector('.olo-pg-next');
      if (prevBtn) prevBtn.addEventListener('click', function () {
        if (container._pgCurrentPage > 1) {
          container._pgCurrentPage--;
          applyPagination(container);
        }
      });
      if (nextBtn) nextBtn.addEventListener('click', function () {
        var maxPage = getMaxPage(container);
        if (container._pgCurrentPage < maxPage) {
          container._pgCurrentPage++;
          applyPagination(container);
        }
      });
    } else if (style === 'loadmore') {
      var loadBtn = pag.querySelector('.olo-pg-loadmore');
      if (loadBtn) loadBtn.addEventListener('click', function () {
        container._pgLoadmorePages++;
        container._pgCurrentPage = container._pgLoadmorePages;
        applyPagination(container);
      });
    }

    // Initial pagination
    applyPagination(container);
  }

  function getVisibleItems(container) {
    return container._pgItems.filter(function (item) {
      return !item._pgFiltered;
    });
  }

  function getMaxPage(container) {
    var visible = getVisibleItems(container);
    var perPage = container._pgConfig.itemsPerPage || 6;
    return Math.max(1, Math.ceil(visible.length / perPage));
  }

  function applyPagination(container) {
    var config   = container._pgConfig;
    var perPage  = config.itemsPerPage || 6;
    var style    = config.paginationStyle || 'dots';
    var page     = container._pgCurrentPage;
    var visible  = getVisibleItems(container);
    var maxPage  = Math.max(1, Math.ceil(visible.length / perPage));

    // Clamp page
    if (page > maxPage) {
      page = maxPage;
      container._pgCurrentPage = page;
    }

    var isLoadmore = (style === 'loadmore');
    var startIdx   = isLoadmore ? 0 : (page - 1) * perPage;
    var endIdx     = isLoadmore ? (container._pgLoadmorePages * perPage) : (page * perPage);

    // Hide all items first
    container._pgItems.forEach(function (item) {
      item.style.display = 'none';
    });

    // Show visible items within page range
    var visIdx = 0;
    for (var i = 0; i < visible.length; i++) {
      if (visIdx >= startIdx && visIdx < endIdx) {
        visible[i].style.display = '';
      }
      visIdx++;
    }

    updatePaginationUI(container, page, maxPage);

    // Trigger UIkit update for masonry
    if (typeof UIkit !== 'undefined') {
      try { UIkit.update(); } catch (e) { console.warn('UIkit update error:', e); }
    }
  }

  function updatePaginationUI(container, page, maxPage) {
    var pag   = container._pgPagEl;
    if (!pag) return;
    var style = container._pgConfig.paginationStyle || 'dots';

    if (style === 'arrows') {
      var prevBtn = pag.querySelector('.olo-pg-prev');
      var nextBtn = pag.querySelector('.olo-pg-next');
      var info    = pag.querySelector('.olo-pg-page-info');
      if (prevBtn) prevBtn.disabled = (page <= 1);
      if (nextBtn) nextBtn.disabled = (page >= maxPage);
      if (info) info.textContent = page + ' / ' + maxPage;

    } else if (style === 'loadmore') {
      var loadBtn = pag.querySelector('.olo-pg-loadmore');
      if (loadBtn) {
        loadBtn.style.display = (container._pgLoadmorePages >= maxPage) ? 'none' : '';
      }

    } else {
      // dots or numbers — generate dynamically
      // Remove old generated elements
      var old = pag.querySelectorAll('.olo-pg-page-dot, .olo-pg-page-num');
      old.forEach(function (el) { el.remove(); });

      for (var i = 1; i <= maxPage; i++) {
        var el = document.createElement('button');
        if (style === 'numbers') {
          el.className = 'olo-pg-page-num' + (i === page ? ' olo-pg-page-active' : '');
          el.textContent = i;
        } else {
          el.className = 'olo-pg-page-dot' + (i === page ? ' olo-pg-page-active' : '');
        }
        el.setAttribute('data-page', i);
        el.addEventListener('click', (function (pg) {
          return function () {
            container._pgCurrentPage = pg;
            applyPagination(container);
          };
        })(i));
        pag.appendChild(el);
      }
    }
  }

  function initAll() {
    document.querySelectorAll('.olo-postgrid').forEach(function(pg) {
      if (!pg._pgItems) initPostGrid(pg);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  // Re-init when builder iframe injects new HTML
  document.addEventListener('olo:iframe-render', initAll);
})();
