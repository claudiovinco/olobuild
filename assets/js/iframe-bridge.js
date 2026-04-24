/**
 * Olobuild iframe bridge — runs inside the builder preview iframe.
 * Handles postMessage communication with the parent builder app.
 */
(function() {
  'use strict';

  var root = document.getElementById('olo-iframe-root');
  var selectedId = null;
  var hoveredEl = null;
  var previewMode = false;

  // ── Helpers ──

  function post(type, data) {
    if (window.parent !== window) {
      window.parent.postMessage(Object.assign({ type: type }, data || {}), '*');
    }
  }

  function findTileId(el) {
    while (el && el !== root) {
      if (el.dataset && el.dataset.oloTileId) return el.dataset.oloTileId;
      el = el.parentElement;
    }
    return null;
  }

  function findTileEl(tileId) {
    return root.querySelector('[data-olo-tile-id="' + tileId + '"]');
  }

  // ── Selection ──

  function selectTile(tileId) {
    // Deselect previous
    if (selectedId) {
      var prev = findTileEl(selectedId);
      if (prev) prev.classList.remove('olo-builder-selected');
    }
    selectedId = tileId;
    if (tileId) {
      var el = findTileEl(tileId);
      if (el) el.classList.add('olo-builder-selected');
    }
  }

  // ── Hover ──

  function onMouseOver(e) {
    if (previewMode) return;
    if (e.target.closest('.olo-iframe-add-btn, .olo-iframe-toolbar')) {
      clearTimeout(hoverToolbarTimeout);
      return;
    }
    var tileId = findTileId(e.target);
    if (!tileId) return;
    var el = findTileEl(tileId);
    if (el === hoveredEl) return;
    if (hoveredEl) hoveredEl.classList.remove('olo-builder-hover');
    hoveredEl = el;
    if (el && tileId !== selectedId) {
      el.classList.add('olo-builder-hover');
      showHoverToolbar(el, tileId);
    }
  }

  function onMouseOut(e) {
    // Don't hide if moving to toolbar or staying within the same tile
    var related = e.relatedTarget;
    if (related && (related.closest('.olo-iframe-toolbar') || (hoveredEl && hoveredEl.contains(related)))) {
      clearTimeout(hoverToolbarTimeout);
      return;
    }
    if (hoveredEl) {
      hoveredEl.classList.remove('olo-builder-hover');
      hoveredEl = null;
      hideHoverToolbar();
    }
  }

  // ── Click ──

  function onClick(e) {
    if (previewMode) return;
    // Don't intercept clicks on builder controls
    if (e.target.closest('.olo-iframe-add-btn, .olo-iframe-toolbar, .olo-iframe-add-circle')) return;
    e.preventDefault();
    e.stopPropagation();
    var tileId = findTileId(e.target);
    if (tileId) {
      selectTile(tileId);
      post('olo:tile-click', { tileId: tileId });
    } else {
      selectTile(null);
      post('olo:canvas-click');
    }
  }

  // ── Double-click (inline edit placeholder) ──

  var activeEditEl = null;
  var editToolbar = null;

  function onDblClick(e) {
    if (previewMode) return;

    // Find nearest editable or text element
    var editEl = e.target.closest('[data-olo-editable]');
    if (!editEl) {
      // Auto-detect: double-click on heading, paragraph, button text, span inside tile
      var textEl = e.target.closest('h1,h2,h3,h4,h5,h6,p,.olo-headline *,.olo-btn-text,button span,.olo-nl-title,.olo-nl-sub,.olo-ib-title,.olo-ib-desc,.olo-testimonial-quote');
      if (textEl) {
        var tileEl = textEl.closest('[data-olo-tile-id]');
        if (tileEl) {
          editEl = textEl;
          // Auto-assign editable attribute based on tag
          var tag = textEl.tagName.toLowerCase();
          if (tag.match(/^h[1-6]$/)) editEl.setAttribute('data-olo-editable', 'heading');
          else if (textEl.classList.contains('olo-btn-text') || textEl.closest('.olo-btn')) editEl.setAttribute('data-olo-editable', 'text');
          else editEl.setAttribute('data-olo-editable', 'content');
        }
      }
    }

    if (!editEl) return;
    var tileId = findTileId(editEl);
    var field = editEl.dataset.oloEditable;
    if (!tileId || !field) return;

    e.preventDefault();
    e.stopPropagation();
    startInlineEdit(editEl, tileId, field);
  }

  function startInlineEdit(el, tileId, field) {
    // End previous edit
    if (activeEditEl) endInlineEdit();

    activeEditEl = el;
    el.setAttribute('contenteditable', 'true');
    el.style.outline = '2px solid #3B82F6';
    el.style.outlineOffset = '2px';
    el.style.borderRadius = '2px';
    el.style.minHeight = '1em';
    el.style.cursor = 'text';
    el.focus();

    // Select all text
    var range = document.createRange();
    range.selectNodeContents(el);
    var sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);

    // Show edit toolbar
    showEditToolbar(el);

    // Save on blur
    function onBlur(e) {
      // Don't end if clicking toolbar
      if (editToolbar && editToolbar.contains(e.relatedTarget)) return;
      setTimeout(function() {
        if (editToolbar && editToolbar.contains(document.activeElement)) return;
        endInlineEdit();
      }, 150);
    }
    el._oloBlur = onBlur;
    el.addEventListener('blur', onBlur);

    // Save on Enter (for single-line fields like headings, buttons)
    el.addEventListener('keydown', function onKey(e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        if (field === 'heading' || field === 'text') {
          e.preventDefault();
          endInlineEdit();
        }
      }
      if (e.key === 'Escape') {
        e.preventDefault();
        endInlineEdit();
      }
    });

    // Hide hover toolbar while editing
    if (hoverToolbar) hoverToolbar.style.display = 'none';
  }

  function endInlineEdit() {
    if (!activeEditEl) return;
    var el = activeEditEl;
    var tileId = findTileId(el);
    var field = el.dataset.oloEditable;

    // Get edited content
    var newValue = field === 'content' ? el.innerHTML : el.textContent.trim();

    el.removeAttribute('contenteditable');
    el.style.outline = '';
    el.style.outlineOffset = '';
    el.style.cursor = '';
    if (el._oloBlur) el.removeEventListener('blur', el._oloBlur);

    // Send updated value to parent
    if (tileId && field) {
      post('olo:inline-edit', { tileId: tileId, field: field, value: newValue });
    }

    // Remove toolbar
    if (editToolbar) {
      editToolbar.remove();
      editToolbar = null;
    }

    activeEditEl = null;
  }

  function showEditToolbar(el) {
    if (editToolbar) editToolbar.remove();

    editToolbar = document.createElement('div');
    editToolbar.style.cssText = 'position:fixed;z-index:999999;display:flex;gap:2px;padding:4px 6px;background:#1F2937;border:1px solid #374151;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.3);font-size:12px';

    var buttons = [
      { cmd: 'bold', icon: 'B', style: 'font-weight:bold' },
      { cmd: 'italic', icon: 'I', style: 'font-style:italic' },
      { cmd: 'underline', icon: 'U', style: 'text-decoration:underline' },
    ];

    buttons.forEach(function(b) {
      var btn = document.createElement('button');
      btn.innerHTML = b.icon;
      btn.style.cssText = 'width:28px;height:28px;border:none;background:transparent;color:#D1D5DB;cursor:pointer;border-radius:4px;display:flex;align-items:center;justify-content:center;' + (b.style || '');
      btn.onmousedown = function(e) {
        e.preventDefault();
        document.execCommand(b.cmd, false, null);
      };
      btn.onmouseover = function() { btn.style.background = '#374151'; };
      btn.onmouseout = function() { btn.style.background = 'transparent'; };
      editToolbar.appendChild(btn);
    });

    // Done button
    var done = document.createElement('button');
    done.textContent = '✓';
    done.style.cssText = 'width:28px;height:28px;border:none;background:#22C55E;color:#fff;cursor:pointer;border-radius:4px;margin-left:4px;font-size:14px;display:flex;align-items:center;justify-content:center';
    done.onmousedown = function(e) { e.preventDefault(); endInlineEdit(); };
    editToolbar.appendChild(done);

    document.body.appendChild(editToolbar);

    // Position above element
    var rect = el.getBoundingClientRect();
    editToolbar.style.left = Math.max(4, rect.left) + 'px';
    editToolbar.style.top = Math.max(4, rect.top - 44) + 'px';
  }

  // ── Context menu ──

  function onContextMenu(e) {
    if (previewMode) return;
    var tileId = findTileId(e.target);
    if (!tileId) return;
    e.preventDefault();
    selectTile(tileId);
    post('olo:tile-click', { tileId: tileId });
    post('olo:tile-contextmenu', {
      tileId: tileId,
      x: e.clientX,
      y: e.clientY
    });
  }

  // ── Drag over (from parent sidebar) ──

  var dropIndicator = null;

  function getDropIndicator() {
    if (!dropIndicator) {
      dropIndicator = document.createElement('div');
      dropIndicator.className = 'olo-drop-indicator';
      document.body.appendChild(dropIndicator);
    }
    return dropIndicator;
  }

  function hideDropIndicator() {
    if (dropIndicator) dropIndicator.style.display = 'none';
  }

  /**
   * Send a snapshot of section/column bounding boxes to the parent.
   * The parent uses this for synchronous hit-testing during drag.
   */
  function sendLayoutSnapshot() {
    var grid = root.querySelector('[data-olo-zone="body"] .olo-frontend-grid') || root.querySelector('.olo-frontend-grid');
    if (!grid) { post('olo:layout-snapshot', { sections: [], columns: [] }); return; }

    var sectionEls = grid.querySelectorAll(':scope > [data-olo-tile-id]');
    var sects = [];
    for (var i = 0; i < sectionEls.length; i++) {
      var r = sectionEls[i].getBoundingClientRect();
      sects.push({ id: sectionEls[i].getAttribute('data-olo-tile-id'), top: r.top, bottom: r.bottom, left: r.left, right: r.right, index: i });
    }

    var columnEls = root.querySelectorAll('[data-olo-tile-type="column"]');
    var cols = [];
    for (var j = 0; j < columnEls.length; j++) {
      var cr = columnEls[j].getBoundingClientRect();
      cols.push({ id: columnEls[j].getAttribute('data-olo-tile-id'), top: cr.top, bottom: cr.bottom, left: cr.left, right: cr.right });
    }

    post('olo:layout-snapshot', { sections: sects, columns: cols });
  }

  // ── Height observer ──

  var resizeObserver = null;
  var lastHeight = 0;

  function observeHeight() {
    if (typeof ResizeObserver === 'undefined') return;
    resizeObserver = new ResizeObserver(function() {
      var h = root.scrollHeight;
      if (h !== lastHeight) {
        lastHeight = h;
        post('olo:height', { height: h });
      }
    });
    resizeObserver.observe(root);
  }

  // ── Prevent link navigation ──

  function blockLinks(e) {
    var a = e.target.closest('a[href]');
    if (a && a.getAttribute('href') !== '#' && a.getAttribute('href') !== 'javascript:void(0)') {
      e.preventDefault();
    }
  }

  // ── Reinit UIkit + execute inline scripts after DOM update ──

  function reinitUIkit() {
    if (typeof UIkit === 'undefined') return;
    try {
      // UIkit auto-initializes via MutationObserver, but innerHTML replacement
      // can miss it. Force UIkit to re-scan all elements with uk-* attributes.

      // 1. Find all UIkit component elements
      var selectors = ['slider','slideshow','lightbox','grid','scrollspy','accordion','tab','switcher','countdown','filter','parallax','sticky','navbar','drop','dropdown'];
      selectors.forEach(function(name) {
        var els = root.querySelectorAll('[uk-' + name + '],[data-uk-' + name + ']');
        els.forEach(function(el) {
          try {
            // Use UIkit's component constructor to (re)init
            if (UIkit[name]) {
              UIkit[name](el);
            }
          } catch(e) {}
        });
      });

      // 2. General update for everything else
      UIkit.update(document.body);
    } catch(ex) {
      console.warn('[bridge] UIkit reinit error:', ex);
    }
  }

  function reinitTileScripts() {
    document.dispatchEvent(new CustomEvent('olo:iframe-render'));

    // Re-init booking widgets
    var uninitWidgets = root.querySelectorAll('.olob-widget:not([data-olob-init])');
    uninitWidgets.forEach(function(w) {
      w.setAttribute('data-olob-init', '1');
      if (typeof window.__olobInitWidget === 'function') {
        window.__olobInitWidget(w);
      }
    });

    // Re-init restaurant booking widgets (delay to ensure DOM settled)
    setTimeout(function() {
      if (typeof window.__oloRestInit === 'function') {
        window.__oloRestInit();
      }
    }, 100);

    // Re-init SVG Animator
    if (typeof window.__oloSvgaInit === 'function') {
      window.__oloSvgaInit();
    }

    // Re-init Viewer 360
    if (typeof window.__oloV360Init === 'function') {
      window.__oloV360Init();
    }

    // Re-init virtual tour viewer
    if (typeof window.__oloVtourBoot === 'function') {
      var vtourContainers = root.querySelectorAll('.olo-vtour-container[data-vtour-config]:not([data-vtour-init])');
      if (vtourContainers.length) {
        vtourContainers.forEach(function(c) { c.setAttribute('data-vtour-init', '1'); });
        window.__oloVtourBoot();
      }
    }
  }

  // Reset all "run-once" guard flags before re-executing inline scripts
  function resetScriptGuards() {
    var keys = Object.keys(window);
    for (var i = 0; i < keys.length; i++) {
      if (keys[i].indexOf('_olo') === 0) {
        delete window[keys[i]];
      }
    }
    // Destroy existing Leaflet map instances to prevent "already initialized" errors
    root.querySelectorAll('.olo-map-canvas, [id^="olo-map-"]').forEach(function(el) {
      if (el._leaflet_id) {
        try { el._leaflet = null; delete el._leaflet_id; } catch(e) {}
      }
    });
  }

  // ── Overlay controls: "+" add buttons between sections ──

  function injectAddButtons() {
    // Remove old ones
    root.querySelectorAll('.olo-iframe-add-btn').forEach(function(b) { b.remove(); });
    if (previewMode) return;

    // Find all zones
    var zones = root.querySelectorAll('[data-olo-zone]');
    zones.forEach(function(zone) {
      var grid = zone.querySelector('.olo-frontend-grid');
      if (!grid) return;
      var sections = grid.querySelectorAll(':scope > section[data-olo-tile-id], :scope > [data-olo-tile-id]');
      var zoneName = zone.getAttribute('data-olo-zone');

      // "+" between each section
      for (var i = 0; i <= sections.length; i++) {
        var btn = createAddButton(zoneName, i);
        if (i < sections.length) {
          sections[i].parentNode.insertBefore(btn, sections[i]);
        } else {
          grid.appendChild(btn);
        }
      }
    });

    // If no zones (body-only), add to the main grid
    if (!zones.length) {
      var grid = root.querySelector('.olo-frontend-grid');
      if (grid) {
        var sections = grid.querySelectorAll(':scope > section[data-olo-tile-id]');
        for (var i = 0; i <= sections.length; i++) {
          var btn = createAddButton('body', i);
          if (i < sections.length) {
            sections[i].parentNode.insertBefore(btn, sections[i]);
          } else {
            grid.appendChild(btn);
          }
        }
      }
    }
  }

  function createAddButton(zone, index) {
    var wrap = document.createElement('div');
    wrap.className = 'olo-iframe-add-btn';
    wrap.setAttribute('data-zone', zone);
    wrap.setAttribute('data-index', index);
    wrap.innerHTML = '<button class="olo-iframe-add-circle" title="Aggiungi sezione">' +
      '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2"><line x1="7" y1="2" x2="7" y2="12"/><line x1="2" y1="7" x2="12" y2="7"/></svg>' +
      '</button>';
    wrap.querySelector('button').addEventListener('click', function(e) {
      e.stopPropagation();
      post('olo:add-section', { zone: zone, index: index });
    });
    return wrap;
  }

  // ── Floating tile toolbar on hover ──

  var hoverToolbar = null;
  var hoverToolbarTimeout = null;

  function getHoverToolbar() {
    if (!hoverToolbar) {
      hoverToolbar = document.createElement('div');
      hoverToolbar.className = 'olo-iframe-toolbar';
      hoverToolbar.innerHTML =
        '<span class="olo-iframe-tb-grip" title="Trascina per spostare"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 3h-2v4h4v-2l3 3-3 3v-2h-4v4h2l-3 3-3-3h2v-4H8v2l-3-3 3-3v2h4V5H10l2-3z"/></svg></span>' +
        '<span class="olo-iframe-toolbar-label"></span>' +
        '<button class="olo-iframe-tb-btn olo-tb-move" data-action="moveleft" title="Colonna precedente ←"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg></button>' +
        '<button class="olo-iframe-tb-btn olo-tb-move" data-action="moveup" title="Sposta su ↑"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 15l-6-6-6 6"/></svg></button>' +
        '<button class="olo-iframe-tb-btn olo-tb-move" data-action="movedown" title="Sposta giù ↓"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg></button>' +
        '<button class="olo-iframe-tb-btn olo-tb-move" data-action="moveright" title="Colonna successiva →"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg></button>' +
        '<span class="olo-iframe-tb-sep"></span>' +
        '<button class="olo-iframe-tb-btn" data-action="settings" title="Impostazioni"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg></button>' +
        '<button class="olo-iframe-tb-btn" data-action="duplicate" title="Duplica"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg></button>' +
        '<button class="olo-iframe-tb-btn" data-action="delete" title="Elimina"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4h8v2M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/></svg></button>' +
        '<span class="olo-iframe-tb-sep"></span>' +
        '<button class="olo-iframe-tb-btn olo-tb-add" data-action="addafter" title="Aggiungi elemento dopo"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg></button>' +
        '<button class="olo-iframe-tb-btn olo-tb-addcol" data-action="addcolumn" title="Aggiungi colonna"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="18" rx="1"/><rect x="14" y="3" width="7" height="18" rx="1" opacity="0.4"/><path d="M17.5 9v6M14.5 12h6" stroke-width="2"/></svg></button>';
      document.body.appendChild(hoverToolbar);

      hoverToolbar.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-action]');
        if (!btn) return;
        e.stopPropagation();
        var tileId = hoverToolbar.getAttribute('data-for-tile');
        if (!tileId) return;
        var action = btn.dataset.action;
        if (action === 'settings') {
          post('olo:tile-click', { tileId: tileId });
        } else if (action === 'addafter') {
          post('olo:add-tile-after', { tileId: tileId });
        } else if (action === 'addcolumn') {
          post('olo:add-column', { tileId: tileId });
        } else {
          post('olo:tile-action', { tileId: tileId, action: action });
        }
      });

      hoverToolbar.addEventListener('mouseenter', function() {
        clearTimeout(hoverToolbarTimeout);
      });
      hoverToolbar.addEventListener('mouseleave', function() {
        if (!gripDragging) hideHoverToolbar();
      });

      // ── Grip drag system (mouse-based, not native drag) ──
      var gripDragging = false;
      var gripDragId = null;
      var gripGhost = null;
      var gripDropTarget = null;
      var gripDropBefore = true;
      var gripIndicator = null;

      var grip = hoverToolbar.querySelector('.olo-iframe-tb-grip');
      if (grip) {
        grip.addEventListener('mousedown', function(e) {
          var tileId = hoverToolbar.getAttribute('data-for-tile');
          var el = tileId ? findTileEl(tileId) : null;
          if (!el) return;
          e.preventDefault();
          e.stopPropagation();

          gripDragging = true;
          gripDragId = tileId;

          // Create ghost
          gripGhost = document.createElement('div');
          gripGhost.className = 'olo-grip-ghost';
          var rect = el.getBoundingClientRect();
          gripGhost.style.width = Math.min(rect.width, 300) + 'px';
          gripGhost.style.height = Math.min(rect.height, 60) + 'px';
          gripGhost.textContent = hoverToolbar.querySelector('.olo-iframe-toolbar-label').textContent;
          document.body.appendChild(gripGhost);

          // Create drop indicator
          if (!gripIndicator) {
            gripIndicator = document.createElement('div');
            gripIndicator.className = 'olo-grip-indicator';
            document.body.appendChild(gripIndicator);
          }

          // Dim source element
          el.style.opacity = '0.3';
          el.style.transition = 'opacity 0.2s';

          // Hide the toolbar during drag
          hoverToolbar.style.display = 'none';
        });
      }

      document.addEventListener('mousemove', function(e) {
        if (!gripDragging || !gripGhost) return;

        // Move ghost
        gripGhost.style.left = (e.clientX + 12) + 'px';
        gripGhost.style.top = (e.clientY - 20) + 'px';

        // Find drop target
        gripGhost.style.pointerEvents = 'none';
        if (gripIndicator) gripIndicator.style.pointerEvents = 'none';
        var elUnder = document.elementFromPoint(e.clientX, e.clientY);
        gripGhost.style.pointerEvents = '';
        if (gripIndicator) gripIndicator.style.pointerEvents = '';

        var targetEl = elUnder ? elUnder.closest('[data-olo-tile-id]') : null;
        if (targetEl && targetEl.getAttribute('data-olo-tile-id') === gripDragId) {
          targetEl = null; // Can't drop on self
        }

        if (targetEl) {
          var tRect = targetEl.getBoundingClientRect();
          var midY = tRect.top + tRect.height / 2;
          gripDropBefore = e.clientY < midY;
          gripDropTarget = targetEl.getAttribute('data-olo-tile-id');

          // Show indicator
          if (gripIndicator) {
            gripIndicator.style.display = 'block';
            gripIndicator.style.top = (gripDropBefore ? tRect.top : tRect.bottom) + 'px';
            gripIndicator.style.left = tRect.left + 'px';
            gripIndicator.style.width = tRect.width + 'px';
          }
        } else {
          gripDropTarget = null;
          if (gripIndicator) gripIndicator.style.display = 'none';
        }
      });

      document.addEventListener('mouseup', function(e) {
        if (!gripDragging) return;

        // Restore source element
        var srcEl = gripDragId ? findTileEl(gripDragId) : null;
        if (srcEl) {
          srcEl.style.opacity = '';
          srcEl.style.transition = '';
        }

        // Remove ghost and indicator
        if (gripGhost) { gripGhost.remove(); gripGhost = null; }
        if (gripIndicator) gripIndicator.style.display = 'none';

        // Execute reorder
        if (gripDropTarget && gripDragId && gripDropTarget !== gripDragId) {
          post('olo:reorder', {
            sourceId: gripDragId,
            targetId: gripDropTarget,
            before: gripDropBefore
          });
        }

        // Reset
        gripDragging = false;
        gripDragId = null;
        gripDropTarget = null;
        hoverToolbar.style.display = '';
        hideHoverToolbar();
      });
    }
    return hoverToolbar;
  }

  // ── Section drag reorder in iframe ──

  var dragSrcId = null;
  var dragIndicatorEl = null;

  function initSectionDrag() {
    // Make all tile elements accept drag reorder
    root.querySelectorAll('[data-olo-tile-id]').forEach(function(el) {
      el.addEventListener('dragover', function(e) {
        if (!dragSrcId || dragSrcId === el.dataset.oloTileId) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var rect = el.getBoundingClientRect();
        var indicator = getDropIndicator();
        indicator.style.display = 'block';
        indicator.style.top = (e.clientY < rect.top + rect.height / 2 ? rect.top : rect.bottom) + 'px';
      });
      el.addEventListener('drop', function(e) {
        if (!dragSrcId || dragSrcId === el.dataset.oloTileId) return;
        e.preventDefault();
        e.stopPropagation();
        var rect = el.getBoundingClientRect();
        var before = e.clientY < rect.top + rect.height / 2;
        post('olo:reorder', { sourceId: dragSrcId, targetId: el.dataset.oloTileId, before: before });
        dragSrcId = null;
        hideDropIndicator();
      });
      el.addEventListener('dragstart', function(e) {
        if (previewMode) return;
        dragSrcId = el.dataset.oloTileId;
        e.dataTransfer.effectAllowed = 'move';
        el.style.opacity = '0.4';
      });
      el.addEventListener('dragend', function() {
        el.style.opacity = '';
        dragSrcId = null;
        hideDropIndicator();
      });
    });
  }

  function showHoverToolbar(el, tileId) {
    if (previewMode) return;
    clearTimeout(hoverToolbarTimeout);
    var tb = getHoverToolbar();
    var rect = el.getBoundingClientRect();
    var tileType = el.getAttribute('data-olo-tile-type') || '';
    var tileNames = {
      row: 'Riga', headline: 'Titolo', text: 'Testo', image: 'Immagine', button: 'Pulsante',
      video: 'Video', icon: 'Icona', iconbox: 'Iconbox', iconlist: 'Lista icone', list: 'Lista',
      desclist: 'Desc List', grid: 'Griglia', panel: 'Pannello', form: 'Form', map: 'Mappa',
      megamenu: 'Megamenu', navmenu: 'NavMenu', search: 'Ricerca', sitelogo: 'Logo',
      postgrid: 'Post Grid', proslider: 'Slider', progallery: 'Galleria', lightbox: 'Lightbox',
      popover: 'Popover', hotspot: 'Hotspot', booking: 'Booking', bookingpicker: 'Booking Picker',
      servicehero: 'Service Hero', serviceinfo: 'Service Info', servicelist: 'Service List',
      serviceprices: 'Prezzi', servicegallery: 'Galleria Servizi', servicesearch: 'Ricerca Servizi',
      serviceresults: 'Risultati', servicerelated: 'Correlati', servicevideo: 'Video Servizio',
      hostcard: 'Host Card', floatingpanel: 'Floating Panel', mobilebar: 'Mobile Bar',
      newsletter: 'Newsletter', svganimator: 'SVG Animator', viewer360: '360 Viewer',
      textmask: 'Testo Mask', overlaygrid: 'Overlay Grid', separator: 'Separatore',
      spacer: 'Spaziatore', counter: 'Contatore', countdown: 'Countdown', tabs: 'Tab',
      accordion: 'Accordion', popup: 'Popup', subnav: 'Subnav', breadcrumb: 'Breadcrumb',
      sitemap: 'Sitemap', social: 'Social', testimonial: 'Testimonial', pricing: 'Prezzi',
      timeline: 'Timeline', table: 'Tabella', alert: 'Alert', badge: 'Badge',
      code: 'Codice', html: 'HTML', lottie: 'Lottie', pdfviewer: 'PDF Viewer'
    };
    var label = el.tagName === 'SECTION' ? 'Sezione' : (tileNames[tileType] || tileType || 'Elemento');
    tb.querySelector('.olo-iframe-toolbar-label').textContent = label;
    tb.setAttribute('data-for-tile', tileId);
    // Position toolbar inside the element (top-left corner with padding)
    var topPos = rect.top + window.scrollY + 4;
    var leftPos = rect.left + window.scrollX + 4;
    // Clamp to viewport
    if (topPos < window.scrollY + 2) topPos = window.scrollY + 2;
    tb.style.top = topPos + 'px';
    tb.style.left = leftPos + 'px';
    tb.style.display = 'flex';
    tb._forEl = el;
  }

  function hideHoverToolbar() {
    hoverToolbarTimeout = setTimeout(function() {
      if (hoverToolbar) hoverToolbar.style.display = 'none';
    }, 400);
  }

  function executeInlineScripts(container) {
    var scripts = container.querySelectorAll('script:not([data-olo-executed])');
    for (var i = 0; i < scripts.length; i++) {
      var oldScript = scripts[i];
      // Skip non-JS scripts (JSON-LD, application/json, etc.)
      var scriptType = (oldScript.getAttribute('type') || '').toLowerCase();
      if (scriptType && scriptType !== 'text/javascript' && scriptType !== 'module') {
        continue;
      }
      var newScript = document.createElement('script');
      newScript.setAttribute('data-olo-executed', '1');
      if (oldScript.src) {
        newScript.src = oldScript.src;
      } else {
        newScript.textContent = oldScript.textContent;
      }
      try {
        oldScript.parentNode.replaceChild(newScript, oldScript);
      } catch (e) {
        // Silently skip scripts that fail to execute
      }
    }
  }

  // ── Message handler ──

  function onMessage(e) {
    var d = e.data;
    if (!d || typeof d.type !== 'string' || d.type.indexOf('olo:') !== 0) return;

    switch (d.type) {
      case 'olo:render':
        console.log('[bridge] olo:render received, html length:', (d.html||'').length);
        root.innerHTML = d.html || '';
        if (d.css) {
          var existingStyle = document.getElementById('olo-builder-dynamic-css');
          if (existingStyle) existingStyle.remove();
          var style = document.createElement('style');
          style.id = 'olo-builder-dynamic-css';
          style.textContent = d.css;
          document.head.appendChild(style);
        }
        // Reset run-once guards then re-execute inline <script> tags
        resetScriptGuards();
        // Use double-rAF to wait for browser layout/paint, then execute scripts
        // This ensures offsetLeft/offsetWidth are computed (needed by coverflow, masonry, etc.)
        requestAnimationFrame(function() {
          requestAnimationFrame(function() {
            executeInlineScripts(root);
            // Also re-run scripts after images load (for coverflow scroll positioning)
            var imgs = root.querySelectorAll('img:not([data-olo-loaded])');
            var pending = 0;
            imgs.forEach(function(img) {
              if (!img.complete) {
                pending++;
                img.addEventListener('load', function onLoad() {
                  img.removeEventListener('load', onLoad);
                  img.setAttribute('data-olo-loaded', '1');
                  pending--;
                  if (pending === 0) {
                    // All images loaded — re-init filmstrip/coverflow for correct positioning
                    resetScriptGuards();
                    executeInlineScripts(root);
                  }
                });
              }
            });
          });
        });
        // Delay reinit so DOM is fully settled before UIkit scans
        setTimeout(function() {
          reinitUIkit();
          reinitTileScripts();
          injectAddButtons();
          initSectionDrag();
          if (selectedId) {
            var el = findTileEl(selectedId);
            if (el) el.classList.add('olo-builder-selected');
          }
        }, 50);
        // Report height + layout snapshot for drag-and-drop hit-testing
        setTimeout(function() {
          post('olo:height', { height: root.scrollHeight });
          sendLayoutSnapshot();
        }, 150);
        break;

      case 'olo:patch':
        if (d.tileId && d.html) {
          var target = findTileEl(d.tileId);
          if (target) {
            var temp = document.createElement('div');
            temp.innerHTML = d.html.trim();
            var newEl = temp.firstElementChild;
            if (newEl) {
              target.replaceWith(newEl);
              executeInlineScripts(newEl);
              reinitUIkit();
              reinitTileScripts();
              if (d.tileId === selectedId) {
                newEl.classList.add('olo-builder-selected');
              }
            } else {
              // HTML didn't produce an element — request full render
              console.warn('[bridge] patch: no element from html, requesting full render');
              post('olo:request-full-render', {});
            }
          } else {
            // Target not found — request full render
            post('olo:request-full-render', {});
          }
        }
        break;

      case 'olo:select':
        selectTile(d.tileId || null);
        break;

      case 'olo:deselect':
        selectTile(null);
        break;

      case 'olo:scroll-to':
        if (d.tileId) {
          var scrollEl = findTileEl(d.tileId);
          if (scrollEl) scrollEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        break;

      case 'olo:viewport':
        // Viewport width handled by parent resizing the iframe
        break;

      case 'olo:preview-mode':
        previewMode = !!d.enabled;
        document.body.classList.toggle('olo-preview-mode', previewMode);
        if (previewMode) {
          selectTile(null);
          if (hoveredEl) { hoveredEl.classList.remove('olo-builder-hover'); hoveredEl = null; }
        }
        break;

      case 'olo:wireframe-mode':
        document.body.classList.toggle('olo-wireframe-mode', !!d.enabled);
        var wfId = 'olo-wireframe-css';
        var existing = document.getElementById(wfId);
        if (d.enabled) {
          if (!existing) {
            var st = document.createElement('style');
            st.id = wfId;
            st.textContent =
              '.olo-wireframe-mode .olo-frontend-grid > section { outline: 2px dashed rgba(245,158,11,0.35) !important; outline-offset: -2px; position: relative; }' +
              '.olo-wireframe-mode .olo-frontend-grid > section::after { content: "SEZIONE"; position: absolute; top: 4px; right: 8px; font-size: 9px; font-weight: 700; letter-spacing: 1px; color: rgba(245,158,11,0.5); pointer-events: none; z-index: 10; }' +
              '.olo-wireframe-mode [data-olo-tile-id][class*="olo-row"], .olo-wireframe-mode .uk-grid { outline: 1px dashed rgba(99,102,241,0.35) !important; outline-offset: -1px; }' +
              '.olo-wireframe-mode [data-olo-tile-id] { outline: 1px solid rgba(59,130,246,0.18) !important; outline-offset: -1px; position: relative; }' +
              '.olo-wireframe-mode [data-olo-tile-id]:hover { outline-color: rgba(59,130,246,0.5) !important; }';
            document.head.appendChild(st);
          }
        } else if (existing) {
          existing.remove();
        }
        break;

      case 'olo:drag-over':
        // Visual feedback only — hit-testing is done parent-side via layout snapshot
        var ind = getDropIndicator();
        if (d.y !== undefined && d.colRect) {
          // Highlight target column
          var cr = d.colRect;
          var w = (cr.right || 0) - (cr.left || 0);
          var h = (cr.bottom || 0) - (cr.top || 0);
          ind.style.cssText = 'display:block;position:fixed;z-index:999999;pointer-events:none;' +
            'top:' + cr.top + 'px;left:' + cr.left + 'px;width:' + w + 'px;height:' + h + 'px;' +
            'border:2px dashed rgba(37,99,235,0.6);border-radius:6px;background:rgba(37,99,235,0.06);' +
            'transition:top .15s ease,left .15s ease,width .15s ease,height .15s ease;';
        } else if (d.y !== undefined && typeof d.lineY === 'number') {
          // Show insertion line between sections
          ind.style.cssText = 'display:block;position:fixed;z-index:999999;pointer-events:none;' +
            'top:' + (d.lineY - 2) + 'px;left:5%;width:90%;height:4px;' +
            'background:linear-gradient(90deg,transparent,#2563EB 10%,#2563EB 90%,transparent);' +
            'border-radius:2px;box-shadow:0 0 12px rgba(37,99,235,0.4);transition:top .15s ease;';
        }
        break;

      case 'olo:request-layout':
        sendLayoutSnapshot();
        break;

      case 'olo:drag-leave':
        hideDropIndicator();
        break;

      case 'olo:auto-scroll':
        // Parent invia delta positivo=down, negativo=up. Applichiamo immediatamente.
        if (typeof d.delta === 'number') {
          window.scrollBy({ top: d.delta, behavior: 'auto' });
        }
        break;

      case 'olo:auto-scroll-stop':
        // Nulla da fermare perché usiamo scroll istantaneo per-tick invece di RAF.
        break;

      case 'olo:bezier-preview':
        if (d.tileId) {
          var bpEl = findTileEl(d.tileId);
          if (bpEl) {
            if (d.reset) {
              bpEl.style.transform = '';
              bpEl.style.opacity = '';
              bpEl.style.filter = '';
            } else {
              var parts = [];
              if (d.x !== undefined && d.x !== 0) parts.push('translateX(' + d.x + 'px)');
              if (d.y !== undefined && d.y !== 0) parts.push('translateY(' + d.y + 'px)');
              if (d.scale !== undefined && d.scale !== 1) parts.push('scale(' + d.scale + ')');
              if (d.rotate !== undefined && d.rotate !== 0) parts.push('rotate(' + d.rotate + 'deg)');
              bpEl.style.transform = parts.length ? parts.join(' ') : '';
              if (d.opacity !== undefined && d.opacity !== 1) {
                bpEl.style.opacity = d.opacity;
              } else {
                bpEl.style.opacity = '';
              }
              if (d.blur !== undefined && d.blur > 0) {
                bpEl.style.filter = 'blur(' + d.blur + 'px)';
              } else {
                bpEl.style.filter = '';
              }
            }
          }
        }
        break;
    }
  }

  // ── Init ──

  root.addEventListener('click', onClick, true);
  root.addEventListener('dblclick', onDblClick, true);
  root.addEventListener('contextmenu', onContextMenu, true);
  root.addEventListener('mouseover', onMouseOver, true);
  root.addEventListener('mouseout', onMouseOut, true);
  document.addEventListener('click', blockLinks, true);
  window.addEventListener('message', onMessage, false);

  observeHeight();

    // Forward key events to parent so shortcuts (Delete, Ctrl+C/V) work
  document.addEventListener('keydown', function(e) {
    // Skip if editing text
    var tag = e.target.tagName;
    if (tag === 'INPUT' || tag === 'TEXTAREA' || e.target.isContentEditable) return;
    // Forward Delete, Backspace, Ctrl+C, Ctrl+V, Ctrl+Alt+C, Ctrl+Alt+V
    if (e.key === 'Delete' || e.key === 'Backspace' || (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 'z' || e.key === 's' || e.code === 'KeyC' || e.code === 'KeyV'))) {
      e.preventDefault();
      parent.postMessage({ type: 'olo:keydown', key: e.key, code: e.code, ctrlKey: e.ctrlKey, shiftKey: e.shiftKey, altKey: e.altKey, metaKey: e.metaKey }, '*');
    }
  });

  // Signal ready to parent
  post('olo:ready');

})();
