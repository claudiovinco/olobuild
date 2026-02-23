/**
 * Olobuilder – Leaflet Locations Map
 * Initializes interactive maps with markers, clustering, and taxonomy filters.
 */
(function () {
  'use strict';

  // Tile layer definitions (free, no API key)
  var TILE_LAYERS = {
    osm: {
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    },
    positron: {
      url: 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/">CARTO</a>',
    },
    dark: {
      url: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/">CARTO</a>',
    },
  };

  // Fix Leaflet default icon paths (vendor folder)
  function fixIconPaths() {
    if (!L || !L.Icon || !L.Icon.Default) return;
    // Reset any cached imagePath so Leaflet re-detects from CSS
    // (leaflet.css and images/ are co-located in the same vendor dir)
    L.Icon.Default.imagePath = null;
  }

  /**
   * Build popup HTML for a location.
   */
  function buildPopup(loc, config) {
    // Service-specific popup
    if (config.mode === 'services') {
      return buildServicePopup(loc, config);
    }

    var html = '<div class="olo-map-popup">';
    if (config.popupImage && loc.image) {
      html += '<img src="' + loc.image + '" alt="' + escHtml(loc.title) + '" class="olo-map-popup-img" />';
    }
    html += '<strong class="olo-map-popup-title">' + escHtml(loc.title) + '</strong>';
    // Rating + Price
    var meta = '';
    if (loc.rating) {
      meta += '<span class="olo-map-popup-rating">' + buildStars(loc.rating) + '</span>';
    }
    if (loc.price) {
      meta += '<span class="olo-map-popup-price">' + escHtml(loc.price) + '</span>';
    }
    if (meta) {
      html += '<div class="olo-map-popup-meta">' + meta + '</div>';
    }
    if (loc.address) {
      html += '<span class="olo-map-popup-address">' + escHtml(loc.address) + '</span>';
    }
    if (config.popupExcerpt && loc.excerpt) {
      html += '<p class="olo-map-popup-excerpt">' + loc.excerpt + '</p>';
    }
    // Gallery strip
    if (loc.gallery && loc.gallery.length) {
      html += '<div class="olo-map-popup-gallery">';
      loc.gallery.forEach(function(item) {
        html += '<a href="' + item.full + '" target="_blank" class="olo-map-popup-gallery-link">'
              + '<img src="' + item.thumb + '" alt="" class="olo-map-popup-gallery-thumb" />'
              + '</a>';
      });
      if (loc.gallery_count > loc.gallery.length) {
        html += '<span class="olo-map-popup-gallery-more">+' + (loc.gallery_count - loc.gallery.length) + '</span>';
      }
      html += '</div>';
    }
    // Detail fields
    var details = '';
    if (loc.phone) {
      details += '<span class="olo-map-popup-detail"><strong>&#9742;</strong> <a href="tel:' + escHtml(loc.phone) + '">' + escHtml(loc.phone) + '</a></span>';
    }
    if (loc.email) {
      details += '<span class="olo-map-popup-detail"><strong>&#9993;</strong> <a href="mailto:' + escHtml(loc.email) + '">' + escHtml(loc.email) + '</a></span>';
    }
    if (loc.hours) {
      details += '<span class="olo-map-popup-detail"><strong>&#128339;</strong> ' + loc.hours + '</span>';
    }
    if (loc.website) {
      details += '<span class="olo-map-popup-detail"><strong>&#127760;</strong> <a href="' + loc.website + '" target="_blank" rel="noopener">Website</a></span>';
    }
    if (details) {
      html += '<div class="olo-map-popup-details">' + details + '</div>';
    }
    // Accessibility badges
    if (loc.accessibility && loc.accessibility.length) {
      var badges = { wheelchair: '♿ Wheelchair', parking: '🅿 Parking', elevator: '🛗 Elevator', audio_guide: '🎧 Audio', pet_friendly: '🐾 Pets OK' };
      html += '<div class="olo-map-popup-badges">';
      loc.accessibility.forEach(function(a) { if (badges[a]) html += '<span class="olo-map-popup-badge">' + badges[a] + '</span>'; });
      html += '</div>';
    }
    // Rental property info
    if (loc.price_night || loc.bedrooms || loc.guests) {
      html += '<div class="olo-map-popup-rental">';
      if (loc.price_night) {
        html += '<span class="olo-map-popup-rental-price">&euro;' + loc.price_night + '<small>/night</small></span>';
      }
      var specs = [];
      if (loc.bedrooms !== undefined) specs.push(loc.bedrooms + (loc.bedrooms == 1 ? ' bed' : ' beds'));
      if (loc.bathrooms !== undefined) specs.push(loc.bathrooms + ' bath');
      if (loc.guests) specs.push(loc.guests + ' guests');
      if (loc.sqm) specs.push(loc.sqm + ' m&sup2;');
      if (specs.length) {
        html += '<span class="olo-map-popup-rental-specs">' + specs.join(' &middot; ') + '</span>';
      }
      if (loc.availability && loc.availability !== 'available') {
        var statusLabel = loc.availability === 'booked' ? 'Currently Booked' : 'Under Maintenance';
        html += '<span class="olo-map-popup-rental-status olo-map-popup-rental-' + loc.availability + '">' + statusLabel + '</span>';
      }
      html += '</div>';
    }

    // Action buttons
    var actions = '';
    if (loc.booking_url) {
      actions += '<a href="' + loc.booking_url + '" target="_blank" rel="noopener" class="olo-map-popup-btn">Book now</a>';
    } else if (loc.ticket_url) {
      actions += '<a href="' + loc.ticket_url + '" target="_blank" rel="noopener" class="olo-map-popup-btn">Book tickets</a>';
    }
    if (config.popupLink && loc.url) {
      actions += '<a href="' + loc.url + '" class="olo-map-popup-link">View details &rarr;</a>';
    }
    if (actions) {
      html += '<div class="olo-map-popup-actions">' + actions + '</div>';
    }
    html += '</div>';
    return html;
  }

  /**
   * Amenity emoji map for popup badges.
   */
  var AMENITY_EMOJI = {
    wifi: '📶', fireplace: '🔥', parking: '🅿️', kitchen: '🍳', tv: '📺',
    bbq: '🍖', terrace: '☀️', heating: '♨️', sauna: '♨️', hottub: '🛁',
    ski: '⛷️', garden: '🌿', pets: '🐾', washer: '🧺', highchair: '👶',
    aircon: '❄️', dishwasher: '🍽️', linens: '🛏️', towels: '🛁',
    pool: '🏊', hiking: '🥾', bikes: '🚲'
  };

  /**
   * Build popup HTML for a service (baita/accommodation).
   */
  function buildServicePopup(loc, config) {
    var bg = config.popupBg || '#ffffff';
    var color = config.popupColor || '#333333';
    var radius = config.popupRadius !== undefined ? config.popupRadius : 8;
    var maxW = config.popupMaxWidth || 280;
    var imgH = config.popupImgHeight || 180;
    var btnColor = config.popupBtnColor || '#3b82f6';
    var btnText = config.popupBtnText || 'Scopri e Prenota';

    var html = '<div class="olo-map-popup olo-map-popup-service"'
      + ' style="background:' + bg + ';color:' + color + ';border-radius:' + radius + 'px;max-width:' + maxW + 'px">';

    if (config.popupImage && loc.image) {
      html += '<img src="' + loc.image + '" alt="' + escHtml(loc.title) + '" class="olo-map-popup-img"'
        + ' style="height:' + imgH + 'px;border-radius:' + radius + 'px ' + radius + 'px 0 0" />';
    }

    html += '<div class="olo-map-popup-svc-body" style="padding:10px 12px">';
    html += '<strong class="olo-map-popup-title" style="color:' + color + '">' + escHtml(loc.title) + '</strong>';

    // Locality + Altitude line
    var subline = '';
    if (config.popupShowValley !== false && loc.valley) {
      subline += '<span class="olo-map-popup-locality">' + escHtml(loc.valley) + '</span>';
    }
    if (config.popupAltitude && loc.altitude !== undefined) {
      subline += '<span class="olo-map-popup-altitude">' + loc.altitude.toLocaleString('it-IT') + 'm s.l.m.</span>';
    }
    if (subline) {
      html += '<div class="olo-map-popup-subline">' + subline + '</div>';
    }

    // Price
    if (config.popupPrice && loc.price) {
      html += '<div class="olo-map-popup-svc-price">';
      html += '<span class="olo-map-popup-svc-price-main">&euro; ' + loc.price + '<small>/notte</small></span>';
      if (loc.price_weekend) {
        html += '<span class="olo-map-popup-svc-price-we">&euro; ' + loc.price_weekend + '<small>/notte weekend</small></span>';
      }
      html += '</div>';
    }

    // Specs row
    if (config.popupShowSpecs !== false) {
      var specs = [];
      if (loc.bedrooms !== undefined) specs.push(loc.bedrooms + (loc.bedrooms == 1 ? ' camera' : ' camere'));
      if (loc.bathrooms !== undefined) specs.push(loc.bathrooms + (loc.bathrooms == 1 ? ' bagno' : ' bagni'));
      if (loc.capacity) specs.push('max ' + loc.capacity + ' ospiti');
      if (loc.sqm) specs.push(loc.sqm + ' m&sup2;');
      if (specs.length) {
        html += '<div class="olo-map-popup-svc-specs">' + specs.join(' &middot; ') + '</div>';
      }
    }

    // Amenities badges
    if (config.popupShowAmenities && loc.amenities_labeled && loc.amenities_labeled.length) {
      html += '<div class="olo-map-popup-amenities">';
      loc.amenities_labeled.forEach(function(a) {
        var emoji = AMENITY_EMOJI[a.slug] || '';
        html += '<span class="olo-map-popup-amenity-badge">'
          + (emoji ? '<span class="olo-map-popup-amenity-emoji">' + emoji + '</span>' : '')
          + escHtml(a.label) + '</span>';
      });
      html += '</div>';
    }

    // Gallery mini-strip
    if (config.popupShowGallery && loc.gallery && loc.gallery.length) {
      html += '<div class="olo-map-popup-gallery">';
      loc.gallery.forEach(function(item) {
        html += '<a href="' + item.full + '" target="_blank" class="olo-map-popup-gallery-link">'
              + '<img src="' + item.thumb + '" alt="" class="olo-map-popup-gallery-thumb" />'
              + '</a>';
      });
      if (loc.gallery_count > loc.gallery.length) {
        html += '<span class="olo-map-popup-gallery-more">+' + (loc.gallery_count - loc.gallery.length) + '</span>';
      }
      html += '</div>';
    }

    // Excerpt
    if (config.popupExcerpt && loc.excerpt) {
      html += '<p class="olo-map-popup-excerpt">' + loc.excerpt + '</p>';
    }

    // Action: link to service page
    if (loc.url) {
      html += '<div class="olo-map-popup-actions">';
      html += '<a href="' + loc.url + '" class="olo-map-popup-btn" style="background:' + btnColor + '">'
        + escHtml(btnText) + '</a>';
      html += '</div>';
    }

    html += '</div></div>';
    return html;
  }

  function buildStars(rating) {
    var full = Math.floor(rating);
    var half = (rating % 1) >= 0.5 ? 1 : 0;
    var empty = 5 - full - half;
    var s = '';
    for (var i = 0; i < full; i++) s += '&#9733;';
    if (half) s += '&#189;';
    for (var i = 0; i < empty; i++) s += '&#9734;';
    return s;
  }

  function escHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str || ''));
    return div.innerHTML;
  }

  /**
   * Create a colored marker icon (SVG pin).
   */
  function getColorIcon(color, cache) {
    if (cache[color]) return cache[color];
    var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="41" viewBox="0 0 25 41">'
      + '<path d="M12.5 0C5.6 0 0 5.6 0 12.5C0 22 12.5 41 12.5 41S25 22 25 12.5C25 5.6 19.4 0 12.5 0z" fill="' + color + '" stroke="#fff" stroke-width="1.5"/>'
      + '<circle cx="12.5" cy="12.5" r="5.5" fill="#fff"/>'
      + '</svg>';
    var icon = L.divIcon({
      html: svg,
      className: 'olo-map-marker',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
    });
    cache[color] = icon;
    return icon;
  }

  /**
   * Initialize a single map canvas.
   */
  function initMap(canvas) {
    var configStr = canvas.getAttribute('data-map-config');
    if (!configStr) return;

    var config;
    try {
      config = JSON.parse(configStr);
    } catch (e) {
      return;
    }

    var map = L.map(canvas, {
      center: config.center || [41.9028, 12.4964],
      zoom: config.zoom || 13,
      scrollWheelZoom: false,
    });

    // Enable scroll zoom on first click
    map.once('click', function () {
      map.scrollWheelZoom.enable();
    });

    // Tile layer
    var layer = TILE_LAYERS[config.tileLayer] || TILE_LAYERS.osm;
    L.tileLayer(layer.url, {
      attribution: layer.attribution,
      maxZoom: 19,
    }).addTo(map);

    // Create markers
    var markers = [];
    var iconCache = {};

    (config.locations || []).forEach(function (loc) {
      var opts = {};
      if (loc.color) {
        opts.icon = getColorIcon(loc.color, iconCache);
      }
      var marker = L.marker([loc.lat, loc.lng], opts);
      var popupMaxW = (config.mode === 'services' && config.popupMaxWidth) ? config.popupMaxWidth + 20 : 300;
      marker.bindPopup(buildPopup(loc, config), { maxWidth: popupMaxW });
      marker._oloTerms = loc.terms || [];
      marker._oloId = loc.id;
      if (loc.altitude !== undefined) {
        marker._oloAltitude = loc.altitude;
      }
      // Service filter data
      marker._oloLocality = loc.valley || '';
      marker._oloBedrooms = loc.bedrooms !== undefined ? parseInt(loc.bedrooms, 10) : null;
      marker._oloGuests = loc.capacity !== undefined ? parseInt(loc.capacity, 10) : null;
      marker._oloPriceNight = loc.price !== undefined ? parseFloat(loc.price) : null;
      marker._oloAmenities = loc.amenities || [];
      markers.push(marker);
    });

    // Cluster or direct add
    var markerGroup;
    if (config.cluster && typeof L.markerClusterGroup === 'function') {
      markerGroup = L.markerClusterGroup();
      markers.forEach(function (m) {
        markerGroup.addLayer(m);
      });
      map.addLayer(markerGroup);
    } else {
      markerGroup = L.featureGroup(markers);
      markerGroup.addTo(map);
    }

    // Fit bounds
    if (config.fitBounds && markers.length > 0) {
      var bounds = L.featureGroup(markers).getBounds();
      if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [40, 40], maxZoom: 16 });
      }
    }

    // Store reference for filters
    canvas._oloMap = map;
    canvas._oloMarkers = markers;
    canvas._oloGroup = markerGroup;
    canvas._oloConfig = config;

    // Initialize filters for this map
    initFilters(canvas);
  }

  /**
   * Initialize filter controls linked to a map.
   */
  function initFilters(canvas) {
    var mapId = canvas.id;
    if (!mapId) return;

    var config = canvas._oloConfig;

    // Service filters (new combined system)
    var svcFilterGroups = document.querySelectorAll('.olo-map-svc-filter-group[data-map-target="' + mapId + '"]');
    if (svcFilterGroups.length > 0) {
      // Centralized filter state
      canvas._oloActiveFilters = {
        altitude: '',
        valley: '',
        guests: '',
        price: '',
        bedrooms: '',
        amenities: []
      };

      svcFilterGroups.forEach(function (group) {
        var filterType = group.getAttribute('data-filter-type');
        var isAmenities = filterType === 'amenities';
        var pills = group.querySelectorAll('.olo-map-filter-pill');

        pills.forEach(function (pill) {
          pill.addEventListener('click', function () {
            var val = pill.getAttribute('data-svc-filter') || '';

            if (isAmenities) {
              // Multi-toggle: add/remove from array
              pill.classList.toggle('olo-map-filter-active');
              var arr = canvas._oloActiveFilters.amenities;
              var idx = arr.indexOf(val);
              if (idx === -1) {
                arr.push(val);
              } else {
                arr.splice(idx, 1);
              }
            } else {
              // Single-select: deactivate siblings, activate this
              pills.forEach(function (p) { p.classList.remove('olo-map-filter-active'); });
              pill.classList.add('olo-map-filter-active');
              canvas._oloActiveFilters[filterType] = val;
            }

            applyAllServiceFilters(canvas);
          });
        });
      });

      return; // Service mode uses combined filters only
    }

    // Compact dropdown filters
    var compactSelects = document.querySelectorAll('.olo-map-compact-select[data-map-target="' + mapId + '"]');
    if (compactSelects.length > 0) {
      canvas._oloActiveFilters = {
        altitude: '',
        valley: '',
        guests: '',
        price: '',
        bedrooms: '',
        amenities: []
      };

      compactSelects.forEach(function (sel) {
        var filterType = sel.getAttribute('data-filter-type');
        sel.addEventListener('change', function () {
          canvas._oloActiveFilters[filterType] = sel.value;
          applyAllServiceFilters(canvas);
        });
      });

      return;
    }

    // Location mode filters (taxonomy pills / dropdown — legacy)
    var allContainers = document.querySelectorAll('.olo-map-filters[data-map-target="' + mapId + '"]');
    allContainers.forEach(function (filtersContainer) {
      var pills = filtersContainer.querySelectorAll('.olo-map-filter-pill');
      pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
          pills.forEach(function (p) { p.classList.remove('olo-map-filter-active'); });
          pill.classList.add('olo-map-filter-active');
          applyFilter(canvas, pill.getAttribute('data-filter') || '');
        });
      });

      var select = filtersContainer.querySelector('[data-filter-select]');
      if (select) {
        select.addEventListener('change', function () {
          applyFilter(canvas, select.value);
        });
      }
    });
  }

  /**
   * Apply taxonomy filter: show/hide markers.
   */
  function applyFilter(canvas, termSlug) {
    var map = canvas._oloMap;
    var markers = canvas._oloMarkers;
    var group = canvas._oloGroup;
    var config = canvas._oloConfig;

    if (!map || !markers || !group) return;

    // Remove all markers from group
    if (typeof group.clearLayers === 'function') {
      group.clearLayers();
    }

    var visibleMarkers = [];

    markers.forEach(function (m) {
      if (!termSlug || m._oloTerms.indexOf(termSlug) !== -1) {
        group.addLayer(m);
        visibleMarkers.push(m);
      }
    });

    // Re-fit bounds to visible markers
    if (config.fitBounds && visibleMarkers.length > 0) {
      var bounds = L.featureGroup(visibleMarkers).getBounds();
      if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [40, 40], maxZoom: 16 });
      }
    }
  }

  /**
   * Apply all service filters combined (AND logic).
   */
  function applyAllServiceFilters(canvas) {
    var map = canvas._oloMap;
    var markers = canvas._oloMarkers;
    var group = canvas._oloGroup;
    var config = canvas._oloConfig;
    var filters = canvas._oloActiveFilters;

    if (!map || !markers || !group || !filters) return;

    if (typeof group.clearLayers === 'function') {
      group.clearLayers();
    }

    var visibleMarkers = [];

    markers.forEach(function (m) {
      // Altitude range
      if (filters.altitude) {
        var parts = filters.altitude.split('-');
        var aMin = parseInt(parts[0], 10) || 0;
        var aMax = parseInt(parts[1], 10) || 99999;
        var alt = m._oloAltitude;
        if (alt === undefined || alt < aMin || alt >= aMax) return;
      }

      // Valley exact match
      if (filters.valley) {
        if (m._oloLocality !== filters.valley) return;
      }

      // Guests range
      if (filters.guests) {
        var gParts = filters.guests.split('-');
        var gMin = parseInt(gParts[0], 10) || 0;
        var gMax = parseInt(gParts[1], 10) || 99999;
        var guests = m._oloGuests;
        if (guests === null || guests < gMin || guests > gMax) return;
      }

      // Price range
      if (filters.price) {
        var pParts = filters.price.split('-');
        var pMin = parseInt(pParts[0], 10) || 0;
        var pMax = parseInt(pParts[1], 10) || 99999;
        var price = m._oloPriceNight;
        if (price === null || price < pMin || price > pMax) return;
      }

      // Bedrooms exact match ("4+" means >= 4)
      if (filters.bedrooms) {
        var beds = m._oloBedrooms;
        if (beds === null) return;
        if (filters.bedrooms === '4+') {
          if (beds < 4) return;
        } else {
          if (beds !== parseInt(filters.bedrooms, 10)) return;
        }
      }

      // Amenities: marker must have ALL selected (AND)
      if (filters.amenities.length > 0) {
        var markerAmenities = m._oloAmenities;
        for (var i = 0; i < filters.amenities.length; i++) {
          if (markerAmenities.indexOf(filters.amenities[i]) === -1) return;
        }
      }

      // Passed all filters
      group.addLayer(m);
      visibleMarkers.push(m);
    });

    // Re-fit bounds
    if (config.fitBounds && visibleMarkers.length > 0) {
      var bounds = L.featureGroup(visibleMarkers).getBounds();
      if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [40, 40], maxZoom: 16 });
      }
    }

    // Update counter
    var mapId = canvas.id;
    var counter = document.querySelector('.olo-map-svc-filter-counter[data-map-target="' + mapId + '"] [data-svc-count]');
    if (counter) {
      counter.textContent = visibleMarkers.length;
    }
  }

  /**
   * Simple lightbox for gallery thumbnails inside Leaflet popups.
   */
  function initLightbox() {
    // Create overlay element once
    var overlay = document.createElement('div');
    overlay.className = 'olo-map-lightbox';
    overlay.innerHTML = '<img class="olo-map-lightbox-img" />';
    overlay.style.display = 'none';
    document.body.appendChild(overlay);

    var img = overlay.querySelector('img');

    overlay.addEventListener('click', function () {
      overlay.style.display = 'none';
    });

    // Delegate click on gallery links inside popups
    document.addEventListener('click', function (e) {
      var link = e.target.closest('.olo-map-popup-gallery-link');
      if (!link) return;
      e.preventDefault();
      img.src = link.href;
      overlay.style.display = 'flex';
    });
  }

  /**
   * Init all maps on the page.
   */
  function initAll() {
    fixIconPaths();
    var canvases = document.querySelectorAll('.olo-map-canvas[data-map-config]');
    canvases.forEach(initMap);
    if (canvases.length) {
      initLightbox();
    }
  }

  // Run on DOMContentLoaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
