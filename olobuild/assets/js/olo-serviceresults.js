(function () {
  'use strict';

  var TILE_LAYERS = {
    osm: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    positron: 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
    dark: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
  };

  var TILE_ATTR = '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>';

  // =========================================================================
  // Init
  // =========================================================================

  function initResults(container) {
    var raw = container.getAttribute('data-svresults-config') || '';
    var config;
    try { config = JSON.parse(atob(raw)); } catch(e) { try { config = JSON.parse(raw); } catch(e2) { config = {}; } }
    var services = config.services || [];

    // Shuffle se ordine casuale
    if (config.randomOrder) {
      for (var i = services.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var tmp = services[i]; services[i] = services[j]; services[j] = tmp;
      }
    }

    // State
    container._svAll = services;
    container._svFiltered = services.slice();
    container._svPage = 1;
    container._svLoadmorePages = 1;
    container._svConfig = config;
    container._svActiveAmenities = [];

    // Map
    container._svMap = null;
    container._svMarkerGroup = null;
    container._svMarkers = [];

    // Parse URL params and prefill filters
    prefillFromUrl(container);

    // Init filters
    initFilters(container);

    // Apply initial filter
    applyFilters(container);

    // Init map
    if (config.layout !== 'cards-only') {
      initMap(container);
    }
  }

  // =========================================================================
  // URL prefill
  // =========================================================================

  function prefillFromUrl(container) {
    var params = new URLSearchParams(window.location.search);

    // Select filters
    var selects = container.querySelectorAll('.olo-svresults-select');
    selects.forEach(function (sel) {
      var filterType = sel.getAttribute('data-filter');
      var val = params.get(filterType);
      if (val) {
        sel.value = val;
      }
    });

    // Amenities
    var amenStr = params.get('amenities');
    if (amenStr) {
      var slugs = amenStr.split(',');
      container._svActiveAmenities = slugs;
      var pills = container.querySelectorAll('.olo-svresults-amenity-pill');
      pills.forEach(function (pill) {
        if (slugs.indexOf(pill.getAttribute('data-amenity')) >= 0) {
          pill.classList.add('olo-svresults-amenity-active');
        }
      });
    }
  }

  // =========================================================================
  // Filters
  // =========================================================================

  function initFilters(container) {
    // Select changes
    var selects = container.querySelectorAll('.olo-svresults-select');
    selects.forEach(function (sel) {
      sel.addEventListener('change', function () {
        container._svPage = 1;
        container._svLoadmorePages = 1;
        applyFilters(container);
      });
    });

    // Amenity pills toggle
    var pills = container.querySelectorAll('.olo-svresults-amenity-pill');
    pills.forEach(function (pill) {
      pill.addEventListener('click', function () {
        var slug = pill.getAttribute('data-amenity');
        pill.classList.toggle('olo-svresults-amenity-active');
        var idx = container._svActiveAmenities.indexOf(slug);
        if (idx >= 0) {
          container._svActiveAmenities.splice(idx, 1);
        } else {
          container._svActiveAmenities.push(slug);
        }
        container._svPage = 1;
        container._svLoadmorePages = 1;
        applyFilters(container);
      });
    });
  }

  function getFilterValues(container) {
    var filters = {};
    var selects = container.querySelectorAll('.olo-svresults-select');
    selects.forEach(function (sel) {
      var key = sel.getAttribute('data-filter');
      if (sel.value) {
        filters[key] = sel.value;
      }
    });
    filters.amenities = container._svActiveAmenities.slice();
    return filters;
  }

  function matchesFilters(svc, filters) {
    // Valley
    if (filters.valley && (svc.valley || '') !== filters.valley) {
      return false;
    }

    // Altitude (range string "min-max")
    if (filters.altitude) {
      var parts = filters.altitude.split('-');
      if (parts.length === 2) {
        var minA = parseInt(parts[0], 10);
        var maxA = parseInt(parts[1], 10);
        var alt = svc.altitude || 0;
        if (alt < minA || alt >= maxA) return false;
      }
    }

    // Guests (range string "min-max")
    if (filters.guests) {
      var gParts = filters.guests.split('-');
      if (gParts.length === 2) {
        var minG = parseInt(gParts[0], 10);
        var maxG = parseInt(gParts[1], 10);
        var cap = svc.capacity || 0;
        if (cap < minG || (maxG < 99 && cap > maxG)) return false;
      }
    }

    // Bedrooms
    if (filters.bedrooms) {
      var beds = svc.bedrooms || 0;
      if (filters.bedrooms === '4+') {
        if (beds < 4) return false;
      } else {
        if (beds !== parseInt(filters.bedrooms, 10)) return false;
      }
    }

    // Type
    if (filters.type && (svc.service_type || '') !== filters.type) {
      return false;
    }

    // Club di Prodotto
    if (filters.club) {
      var clubCats = svc.club_categories || [];
      if (clubCats.indexOf(filters.club) < 0) return false;
    }

    // Amenities (AND logic)
    if (filters.amenities && filters.amenities.length > 0) {
      var svcAmenities = svc.amenities || [];
      for (var i = 0; i < filters.amenities.length; i++) {
        if (svcAmenities.indexOf(filters.amenities[i]) < 0) {
          return false;
        }
      }
    }

    return true;
  }

  function applyFilters(container) {
    var filters = getFilterValues(container);
    var all = container._svAll;
    var filtered = [];

    for (var i = 0; i < all.length; i++) {
      if (matchesFilters(all[i], filters)) {
        filtered.push(all[i]);
      }
    }

    container._svFiltered = filtered;

    // Update counter
    var counterEl = container.querySelector('[data-results-count]');
    if (counterEl) {
      counterEl.textContent = filtered.length;
    }

    // Render cards + pagination
    renderCards(container);
    renderPagination(container);

    // Update map markers
    if (container._svMap) {
      updateMapMarkers(container);
    }

    // Sync URL
    syncUrl(container, filters);
  }

  // =========================================================================
  // URL sync
  // =========================================================================

  function syncUrl(container, filters) {
    var params = new URLSearchParams();
    if (filters.valley) params.set('valley', filters.valley);
    if (filters.altitude) params.set('altitude', filters.altitude);
    if (filters.guests) params.set('guests', filters.guests);
    if (filters.bedrooms) params.set('bedrooms', filters.bedrooms);
    if (filters.type) params.set('type', filters.type);
    if (filters.club) params.set('club', filters.club);
    if (filters.amenities && filters.amenities.length > 0) {
      params.set('amenities', filters.amenities.join(','));
    }
    var qs = params.toString();
    var newUrl = window.location.pathname + (qs ? '?' + qs : '');
    history.replaceState(null, '', newUrl);
  }

  // =========================================================================
  // Card rendering
  // =========================================================================

  function renderCards(container) {
    var config = container._svConfig;
    var filtered = container._svFiltered;
    var perPage = config.itemsPerPage || 8;
    var style = config.paginationStyle || 'numbers';
    var page = container._svPage;

    var grid = container.querySelector('.olo-svresults-grid');
    var empty = container.querySelector('.olo-svresults-empty');
    if (!grid) return;

    // Determine visible items
    var startIdx, endIdx;
    if (style === 'loadmore') {
      startIdx = 0;
      endIdx = container._svLoadmorePages * perPage;
    } else {
      startIdx = (page - 1) * perPage;
      endIdx = startIdx + perPage;
    }

    var visible = filtered.slice(startIdx, endIdx);

    // Gap
    var gapMap = { collapse: '0', small: '8px', medium: '16px', large: '24px' };
    var gap = gapMap[config.gap] || '16px';
    grid.style.display = 'grid';
    grid.style.gridTemplateColumns = 'repeat(' + (config.columns || 2) + ', 1fr)';
    grid.style.gap = gap;

    // Empty state
    if (filtered.length === 0) {
      grid.style.display = 'none';
      if (empty) empty.style.display = '';
      return;
    } else {
      grid.style.display = '';
      if (empty) empty.style.display = 'none';
    }

    // Build HTML
    var html = '';
    for (var i = 0; i < visible.length; i++) {
      html += buildCard(visible[i], config);
    }
    grid.innerHTML = html;

    // Card hover → marker interaction
    var cards = grid.querySelectorAll('.olo-svresults-card');
    cards.forEach(function (card, idx) {
      var svc = visible[idx];
      if (!svc) return;
      card.addEventListener('mouseenter', function () {
        highlightMarker(container, svc.id, true);
      });
      card.addEventListener('mouseleave', function () {
        highlightMarker(container, svc.id, false);
      });
    });
  }

  function buildCard(svc, config) {
    var radius = config.cardRadius || '8px';
    var imgH = config.imageHeight || 180;
    var imgR = config.imageRadius || '0px';
    var cardClass = 'olo-svresults-card';
    if (config.cardStyle === 'shadow') cardClass += ' olo-svresults-card--shadow';
    if (config.cardStyle === 'minimal') cardClass += ' olo-svresults-card--minimal';
    if (config.cardStyle === 'primary') cardClass += ' olo-svresults-card--primary';
    if (config.matchHeight) cardClass += ' olo-svresults-card--matchh';

    var hoverClass = (config.hoverEffect && config.hoverEffect !== 'none')
      ? ' olo-svr-hover-' + config.hoverEffect : '';
    var kbClass = config.fxKenburns ? ' olo-svr-kenburns' : '';

    var isCardLink = config.linkStyle === 'card';

    var html = '<div class="' + cardClass + '" data-svc-id="' + svc.id + '" style="border-radius:' + radius + ';overflow:hidden;">';

    // ── Media section ──
    if (svc.image) {
      var mediaStyle = 'position:relative;overflow:hidden;height:' + imgH + 'px;' + (imgR && imgR !== '0px' ? 'border-radius:' + imgR + ';' : '');
      html += '<div class="olo-svresults-card-media" style="' + mediaStyle + '">';

      var imgTag = '<img src="' + oloUtils.escHtml(svc.image) + '" alt="' + oloUtils.escHtml(svc.title) + '" class="olo-svresults-card-img' + hoverClass + kbClass + '" loading="lazy">';

      if (isCardLink) {
        html += imgTag;
      } else {
        html += '<a href="' + oloUtils.escHtml(svc.url) + '">' + imgTag + '</a>';
      }

      // Overlay gradient
      if (config.overlayGradient && config.overlayColor) {
        var oc = config.overlayColor;
        var rp=parseInt(oc.substring(1,3),16), r=isNaN(rp)?0:rp, gp=parseInt(oc.substring(3,5),16), g=isNaN(gp)?0:gp, bp=parseInt(oc.substring(5,7),16), b=isNaN(bp)?0:bp;
        var a = (config.overlayOpacity||50)/100;
        var dir = config.overlayDirection||'bottom';
        var cssDir = {bottom:'to top',top:'to bottom',left:'to right',right:'to left'}[dir]||'to top';
        var pos = {bottom:'bottom:0;left:0;right:0;',top:'top:0;left:0;right:0;',left:'top:0;left:0;bottom:0;',right:'top:0;right:0;bottom:0;'}[dir]||'bottom:0;left:0;right:0;';
        var dim = (dir==='left'||dir==='right') ? 'width:'+(config.overlayHeight||50)+'%;height:100%;' : 'width:100%;height:'+(config.overlayHeight||50)+'%;';
        html += '<div style="position:absolute;'+pos+dim+'pointer-events:none;z-index:1;background:linear-gradient('+cssDir+',rgba('+r+','+g+','+b+','+a+'),transparent);"></div>';
      }

      // Ribbon
      if (svc.ribbon) {
        html += '<span class="olo-svr-ribbon olo-svr-ribbon--' + (config.ribbonPosition||'top-right') + '" style="background:' + (config.ribbonBg||'#e11d48') + ';color:' + (config.ribbonColor||'#fff') + ';">' + oloUtils.escHtml(svc.ribbon) + '</span>';
      }

      // Opening badge
      if (config.showServiceOpening && svc.service_opening) {
        var opBg = (svc.service_opening.toLowerCase().indexOf('stagional') >= 0)
          ? (config.openingBgSeasonal || '#d97706') : (config.openingBgAnnual || '#059669');
        html += '<span class="olo-svr-opening" style="background:' + opBg + ';font-size:' + (config.openingSize||11) + 'px;">' + oloUtils.escHtml(svc.service_opening) + '</span>';
      }

      html += '</div>';
    }

    // ── Body section ──
    var bodyStyle = '';
    if (config.bodyPadding) bodyStyle += 'padding:' + config.bodyPadding + 'px;';
    if (config.bodyBg) {
      var bg = config.bodyBg;
      var tp=parseInt(bg.substring(1,3),16), br2=isNaN(tp)?0:tp, tp2=parseInt(bg.substring(3,5),16), bg2=isNaN(tp2)?0:tp2, tp3=parseInt(bg.substring(5,7),16), bb=isNaN(tp3)?0:tp3;
      var ba = (config.bodyBgOpacity != null ? config.bodyBgOpacity : 100) / 100;
      bodyStyle += 'background:rgba(' + br2 + ',' + bg2 + ',' + bb + ',' + ba + ');';
    }

    html += '<div class="olo-svresults-card-body" style="' + bodyStyle + '">';

    // Title
    var titleStyle = '';
    if (config.titleSize) titleStyle += 'font-size:' + config.titleSize + 'em;';
    if (config.titleColor) titleStyle += 'color:' + config.titleColor + ';';
    html += '<h3 class="olo-svresults-card-title" style="' + titleStyle + '">';
    if (isCardLink) {
      html += oloUtils.escHtml(svc.title);
    } else {
      html += '<a href="' + oloUtils.escHtml(svc.url) + '">' + oloUtils.escHtml(svc.title) + '</a>';
    }
    html += '</h3>';

    // Stats
    if (config.showStats) {
      var stats = [];
      var L = config.labels || {};
      if (svc.capacity) stats.push(svc.capacity + ' ' + (L.ospiti || 'ospiti'));
      if (svc.bedrooms) stats.push(svc.bedrooms + ' ' + (L.cam || 'cam.'));
      if (svc.bathrooms) stats.push(svc.bathrooms + ' ' + (L.bagni || 'bagni'));
      if (svc.altitude) stats.push(numberFormat(svc.altitude) + 'm');
      if (stats.length) {
        html += '<div class="olo-svresults-card-stats">' + stats.join(' &middot; ') + '</div>';
      }
    }

    // Excerpt
    if (config.showExcerpt && svc.excerpt) {
      var exStyle = '';
      if (config.excerptSize) exStyle += 'font-size:' + config.excerptSize + 'em;';
      if (config.excerptColor) exStyle += 'color:' + config.excerptColor + ';';
      html += '<p class="olo-svresults-card-excerpt" style="' + exStyle + '">' + svc.excerpt + '</p>';
    }

    // Footer
    html += '<div class="olo-svresults-card-footer">';
    if (config.showPrice && svc.price) {
      html += '<span class="olo-svresults-card-price">' + (config.pricePrefix || '') + svc.price + (config.priceSuffix || '') + '</span>';
    }
    if (config.linkStyle === 'button') {
      var defLink = (config.labels && config.labels.dettagli) || 'Dettagli';
      html += '<a href="' + oloUtils.escHtml(svc.url) + '" class="olo-svresults-card-btn">' + oloUtils.escHtml(config.linkText || defLink) + '</a>';
    } else if (!isCardLink) {
      var defLink2 = (config.labels && config.labels.dettagli) || 'Dettagli';
      html += '<a href="' + oloUtils.escHtml(svc.url) + '" class="olo-svresults-card-link">' + oloUtils.escHtml(config.linkText || defLink2) + ' &rarr;</a>';
    }
    html += '</div>';

    html += '</div></div>';

    // Wrap card in link if card-clickable
    if (isCardLink) {
      html = '<a href="' + oloUtils.escHtml(svc.url) + '" class="olo-svresults-card-wrap" style="text-decoration:none;color:inherit;display:block;">' + html + '</a>';
    }

    return html;
  }

  // =========================================================================
  // Pagination
  // =========================================================================

  function renderPagination(container) {
    var config = container._svConfig;
    var filtered = container._svFiltered;
    var perPage = config.itemsPerPage || 8;
    var style = config.paginationStyle || 'numbers';
    var pagEl = container.querySelector('.olo-svresults-pagination');
    if (!pagEl) return;

    var totalPages = Math.ceil(filtered.length / perPage);
    if (totalPages <= 1) {
      pagEl.innerHTML = '';
      return;
    }

    var page = container._svPage;
    var html = '';

    if (style === 'dots') {
      for (var d = 1; d <= totalPages; d++) {
        html += '<button class="olo-svresults-pg-dot' + (d === page ? ' olo-svresults-pg-active' : '') + '" data-page="' + d + '"></button>';
      }
    } else if (style === 'arrows') {
      html += '<button class="olo-svresults-pg-arrow" data-page="' + Math.max(1, page - 1) + '"' + (page === 1 ? ' disabled' : '') + '>&larr;</button>';
      html += '<span class="olo-svresults-pg-info">' + page + ' / ' + totalPages + '</span>';
      html += '<button class="olo-svresults-pg-arrow" data-page="' + Math.min(totalPages, page + 1) + '"' + (page === totalPages ? ' disabled' : '') + '>&rarr;</button>';
    } else if (style === 'loadmore') {
      var shown = container._svLoadmorePages * perPage;
      if (shown < filtered.length) {
        var lmLabel = (config.labels && config.labels.caricaAltro) || 'Carica altro';
        html += '<button class="olo-svresults-pg-loadmore">' + oloUtils.escHtml(lmLabel) + '</button>';
      }
    } else {
      // numbers
      for (var n = 1; n <= totalPages; n++) {
        html += '<button class="olo-svresults-pg-num' + (n === page ? ' olo-svresults-pg-active' : '') + '" data-page="' + n + '">' + n + '</button>';
      }
    }

    pagEl.innerHTML = html;

    // Events
    if (style === 'loadmore') {
      var lmBtn = pagEl.querySelector('.olo-svresults-pg-loadmore');
      if (lmBtn) {
        lmBtn.addEventListener('click', function () {
          container._svLoadmorePages++;
          renderCards(container);
          renderPagination(container);
        });
      }
    } else {
      var btns = pagEl.querySelectorAll('[data-page]');
      btns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var p = parseInt(btn.getAttribute('data-page'), 10);
          if (p && p !== container._svPage) {
            container._svPage = p;
            renderCards(container);
            renderPagination(container);
            // Scroll to top of cards
            var wrap = container.querySelector('.olo-svresults-cards-wrap');
            if (wrap) wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        });
      });
    }
  }

  // =========================================================================
  // Leaflet Map
  // =========================================================================

  function initMap(container) {
    var config = container._svConfig;
    var mapEl = container.querySelector('.olo-svresults-map');
    if (!mapEl || typeof L === 'undefined') return;

    // Fix Leaflet default icon paths
    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
      iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
      iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
      shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    });

    var zoom = config.defaultZoom || 10;
    var map = L.map(mapEl, {
      scrollWheelZoom: false,
    }).setView([46.4, 11.3], zoom);

    var tileUrl = TILE_LAYERS[config.tileLayer] || TILE_LAYERS.positron;
    L.tileLayer(tileUrl, { attribution: TILE_ATTR, maxZoom: 19 }).addTo(map);

    var markerGroup = L.markerClusterGroup ? L.markerClusterGroup() : L.layerGroup();
    map.addLayer(markerGroup);

    container._svMap = map;
    container._svMarkerGroup = markerGroup;

    // Build all markers
    var iconCache = {};
    var services = container._svAll;
    for (var i = 0; i < services.length; i++) {
      var svc = services[i];
      if (!svc.lat || !svc.lng) continue;

      var marker = L.marker([svc.lat, svc.lng], {
        icon: getColorIcon(config.markerColor || '#e11d48', iconCache),
      });

      marker._oloId = svc.id;
      marker._oloSvc = svc;

      // Popup
      marker.bindPopup(buildPopup(svc, config));

      // Click marker → scroll to card
      (function (svcId) {
        marker.on('click', function () {
          var card = container.querySelector('.olo-svresults-card[data-svc-id="' + svcId + '"]');
          if (card) {
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            card.classList.add('olo-svresults-card--highlight');
            setTimeout(function () { card.classList.remove('olo-svresults-card--highlight'); }, 1500);
          }
        });
      })(svc.id);

      container._svMarkers.push(marker);
    }

    updateMapMarkers(container);

    // Invalidate size after a tick (container might not be visible yet)
    setTimeout(function () { map.invalidateSize(); }, 200);
  }

  function updateMapMarkers(container) {
    var group = container._svMarkerGroup;
    if (!group) return;

    group.clearLayers();

    var filtered = container._svFiltered;
    var filteredIds = {};
    for (var i = 0; i < filtered.length; i++) {
      filteredIds[filtered[i].id] = true;
    }

    var bounds = [];
    var markers = container._svMarkers;
    for (var j = 0; j < markers.length; j++) {
      var m = markers[j];
      if (filteredIds[m._oloId]) {
        group.addLayer(m);
        bounds.push(m.getLatLng());
      }
    }

    // Fit bounds
    var config = container._svConfig;
    if (config.fitBounds && bounds.length > 0) {
      var map = container._svMap;
      if (bounds.length === 1) {
        map.setView(bounds[0], config.defaultZoom || 12);
      } else {
        map.fitBounds(L.latLngBounds(bounds), { padding: [30, 30], maxZoom: 15 });
      }
    }
  }

  function buildPopup(svc, config) {
    var html = '<div class="olo-svresults-popup">';
    if (svc.image) {
      html += '<img class="olo-svresults-popup-img" src="' + oloUtils.escHtml(svc.image) + '" alt="' + oloUtils.escHtml(svc.title) + '">';
    }
    html += '<div class="olo-svresults-popup-body">';
    html += '<strong><a href="' + oloUtils.escHtml(svc.url) + '">' + oloUtils.escHtml(svc.title) + '</a></strong>';

    var stats = [];
    var L2 = config.labels || {};
    if (svc.capacity) stats.push(svc.capacity + ' ' + (L2.ospiti || 'ospiti'));
    if (svc.bedrooms) stats.push(svc.bedrooms + ' ' + (L2.cam || 'cam.'));
    if (svc.altitude) stats.push(numberFormat(svc.altitude) + 'm');
    if (stats.length) {
      html += '<div class="olo-svresults-popup-stats">' + stats.join(' &middot; ') + '</div>';
    }

    if (config.showPrice && svc.price) {
      html += '<div class="olo-svresults-popup-price">' + (config.pricePrefix || '') + svc.price + (config.priceSuffix || '') + '</div>';
    }

    html += '</div></div>';
    return html;
  }

  function highlightMarker(container, svcId, on) {
    var markers = container._svMarkers;
    for (var i = 0; i < markers.length; i++) {
      if (markers[i]._oloId === svcId) {
        if (on) {
          markers[i]._icon && markers[i]._icon.classList.add('olo-svresults-marker-bounce');
        } else {
          markers[i]._icon && markers[i]._icon.classList.remove('olo-svresults-marker-bounce');
        }
        break;
      }
    }
  }

  // =========================================================================
  // Color marker icon (SVG)
  // =========================================================================

  function getColorIcon(color, cache) {
    if (cache[color]) return cache[color];

    var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="41" viewBox="0 0 25 41">'
      + '<path d="M12.5 0C5.6 0 0 5.6 0 12.5C0 21.9 12.5 41 12.5 41S25 21.9 25 12.5C25 5.6 19.4 0 12.5 0z" fill="' + color + '"/>'
      + '<circle cx="12.5" cy="12.5" r="5" fill="#fff"/>'
      + '</svg>';

    var icon = L.icon({
      iconUrl: 'data:image/svg+xml;base64,' + btoa(svg),
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
    });
    cache[color] = icon;
    return icon;
  }

  // =========================================================================
  // Utils
  // =========================================================================

  function numberFormat(n) {
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  // =========================================================================
  // Boot
  // =========================================================================

  function initAll() {
    var containers = document.querySelectorAll('.olo-svresults[data-svresults-config]');
    containers.forEach(initResults);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
