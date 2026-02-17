/**
 * Olobuilder – Post Grid: Filtering + Sorting
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

    initFilters(container);
    initSort(container);
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
      item.style.display = show ? '' : 'none';
      if (show) visibleCount++;
    });

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
    var field     = parts[0]; // price, date, title
    var direction = parts[1]; // asc, desc

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
  }

  function initAll() {
    document.querySelectorAll('.olo-postgrid').forEach(initPostGrid);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
