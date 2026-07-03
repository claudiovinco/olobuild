/**
 * Olobuild iframe bridge — runs inside the builder preview iframe.
 * Handles postMessage communication with the parent builder app.
 */
(function() {
  'use strict';

  // v3.55.29 — blocca HTML5 drag SUBITO al caricamento dell'iframe-bridge
  // (NON dentro init()). Le tile contengono <img>/<a> che sono draggable di
  // default → senza questo blocco, il browser fa partire HTML5 drag in parallelo
  // al drag custom (Edge-hotzone) e mostra cursor "no-drop" (cerchio barrato).
  document.addEventListener('dragstart', function(e) { e.preventDefault(); }, true);
  document.addEventListener('drag',      function(e) { e.preventDefault(); }, true);

  var root = document.getElementById('olo-iframe-root');
  var selectedId = null;       // selezione primaria (guida la toolbar/inspector)
  var selectedIds = [];        // multi-selezione (ctrl-click)
  var hoveredEl = null;
  var previewMode = false;

  // Grip-drag state — declared in outer scope so edge-handlers (line ~145+) can read it.
  // Set/cleared by the grip drag system inside getHoverToolbar() (line ~700+).
  var gripDragging = false;
  var gripDragId = null;
  var gripGhost = null;
  var gripDropTarget = null;
  var gripDropBefore = true;
  var gripIndicator = null;
  var gripPointerId = null;     // pointer che ha iniziato il grip drag (filtro multi-pointer)

  // ── Auto-scroll della pagina durante il grip drag interno ──
  // Hot zone ai bordi del viewport iframe: RAF loop che continua anche a
  // puntatore fermo (lo scroll qui è same-document, niente postMessage).
  var GRIP_SCROLL_ZONE = 60;
  var gripScrollRaf = null;
  var gripScrollDelta = 0;
  var gripScrollLastTs = 0;

  function updateGripAutoScroll(clientY) {
    var vh = window.innerHeight || document.documentElement.clientHeight;
    var delta = 0;
    if (clientY < GRIP_SCROLL_ZONE) {
      delta = -(4 + ((GRIP_SCROLL_ZONE - clientY) / GRIP_SCROLL_ZONE) * 18);
    } else if (vh - clientY < GRIP_SCROLL_ZONE) {
      delta = 4 + ((GRIP_SCROLL_ZONE - (vh - clientY)) / GRIP_SCROLL_ZONE) * 18;
    }
    gripScrollDelta = delta;
    if (delta === 0) { stopGripAutoScroll(); return; }
    if (gripScrollRaf) return;
    gripScrollLastTs = 0;
    gripScrollRaf = requestAnimationFrame(gripScrollTick);
  }

  function gripScrollTick(ts) {
    if (!gripDragging || gripScrollDelta === 0) { stopGripAutoScroll(); return; }
    var dt = gripScrollLastTs ? Math.min(ts - gripScrollLastTs, 50) : 16.7;
    gripScrollLastTs = ts;
    window.scrollBy({ top: gripScrollDelta * (dt / 16.7), behavior: 'auto' });
    gripScrollRaf = requestAnimationFrame(gripScrollTick);
  }

  function stopGripAutoScroll() {
    if (gripScrollRaf) { cancelAnimationFrame(gripScrollRaf); gripScrollRaf = null; }
    gripScrollDelta = 0;
  }

  /**
   * Chiude il grip drag. applyReorder=false → annullo (Esc/pointercancel):
   * ripristina tutto senza postare il reorder al parent.
   */
  function endGripDrag(applyReorder) {
    if (!gripDragging) return;

    var srcEl = gripDragId ? findTileEl(gripDragId) : null;
    if (srcEl) {
      srcEl.style.opacity = '';
      srcEl.style.transition = '';
    }
    if (gripGhost) { gripGhost.remove(); gripGhost = null; }
    if (gripIndicator) gripIndicator.style.display = 'none';
    stopGripAutoScroll();
    document.body.classList.remove('olo-dragging');

    if (applyReorder && gripDropTarget && gripDragId && gripDropTarget !== gripDragId) {
      post('olo:reorder', {
        sourceId: gripDragId,
        targetId: gripDropTarget,
        before: gripDropBefore
      });
    }

    gripDragging = false;
    gripDragId = null;
    gripDropTarget = null;
    gripPointerId = null;
    if (hoverToolbar) hoverToolbar.style.display = '';
    hideHoverToolbar();
  }

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

  // ── Scroll-flash: evidenzia la tile raggiunta dal click sulla Struttura ──
  // Le preferenze (olo_scroll_flash) viaggiano nel messaggio 'olo:scroll-to':
  // vivono nel localStorage del documento builder, non in quello dell'iframe.
  // Animazioni CSS olo-sf-flash/olo-sf-pulse in iframe-builder.css.
  function hexToRgba(hex, alpha) {
    var m = /^#?([0-9a-f]{6})$/i.exec(String(hex || '').trim());
    if (!m) return null;
    var n = parseInt(m[1], 16);
    return 'rgba(' + ((n >> 16) & 255) + ',' + ((n >> 8) & 255) + ',' + (n & 255) + ',' + alpha + ')';
  }
  var sfTimer = null;
  function applyScrollFlash(el, sf, delayMs) {
    var color = hexToRgba(sf.color, 1) || 'rgba(232,98,42,1)';
    var soft = hexToRgba(sf.color, 0.7) || 'rgba(232,98,42,0.7)';
    var isPulse = sf.effect === 'pulse';
    var cls = isPulse ? 'olo-tile-pulse' : 'olo-tile-flash';
    var duration = parseInt(sf.duration, 10) || 1000;
    var count = Math.max(1, parseInt(sf.pulse_count, 10) || 2);
    clearTimeout(sfTimer);
    // Click rapidi su più nodi: spegni il flash precedente ovunque sia.
    var prev = root.querySelector('.olo-tile-flash, .olo-tile-pulse');
    if (prev) prev.classList.remove('olo-tile-flash', 'olo-tile-pulse');
    // Il flash parte quando lo scroll smooth è (circa) arrivato.
    sfTimer = setTimeout(function () {
      el.style.setProperty('--sf-color', color);
      el.style.setProperty('--sf-color-soft', soft);
      el.style.setProperty('--sf-size', (parseInt(sf.size, 10) || 6) + 'px');
      if (isPulse) {
        // "Durata effetto" = totale; un ciclo = durata/ripetizioni.
        el.style.setProperty('--sf-cycle', Math.round(duration / count) + 'ms');
        el.style.setProperty('--sf-count', String(count));
      } else {
        el.style.setProperty('--sf-dur', duration + 'ms');
      }
      el.classList.add(cls);
      el.addEventListener('animationend', function onEnd() {
        el.removeEventListener('animationend', onEnd);
        el.classList.remove(cls);
      });
    }, delayMs);
  }

  // ── Force-hover preview ──
  // L'utente attiva il toggle "modifica hover" nell'inspector e si aspetta di
  // vedere la tile come fosse :hover. CSS :hover non è forzabile da JS, quindi
  // cloniamo runtime tutte le regole CSS che contengono :hover sostituendolo
  // con [data-olo-force-hover] e applichiamo l'attributo alla tile + descendant.
  var forceHoverState = { enabled: false, tileId: null };
  function applyForceHover(enabled, tileId) {
    forceHoverState.enabled = !!enabled;
    forceHoverState.tileId = enabled ? tileId : null;
    // Cleanup: rimuovi attributo da tutti gli elementi e rimuovi style precedente
    var prev = document.querySelectorAll('[data-olo-force-hover]');
    for (var i = 0; i < prev.length; i++) prev[i].removeAttribute('data-olo-force-hover');
    var oldStyle = document.getElementById('olo-force-hover-css');
    if (oldStyle) oldStyle.remove();

    if (!enabled || !tileId) return;
    var tileEl = findTileEl(tileId);
    if (!tileEl) return;

    // Applica data-attr a wrapper + tutti i discendenti (così le rules nested
    // tipo `.tile .child:hover` si attivano se il :hover era sul figlio).
    tileEl.setAttribute('data-olo-force-hover', '1');
    var descendants = tileEl.querySelectorAll('*');
    for (var j = 0; j < descendants.length; j++) {
      descendants[j].setAttribute('data-olo-force-hover', '1');
    }

    // Clona regole CSS contenenti :hover sostituendolo con [data-olo-force-hover]
    var clones = [];
    for (var s = 0; s < document.styleSheets.length; s++) {
      try {
        var sheet = document.styleSheets[s];
        if (!sheet.cssRules) continue;
        for (var r = 0; r < sheet.cssRules.length; r++) {
          var rule = sheet.cssRules[r];
          if (!rule.cssText || rule.cssText.indexOf(':hover') === -1) continue;
          // Replace solo l'occorrenza del pseudo-class :hover, non sottostringhe accidentali.
          // Il pattern "(?<!\w):hover\b" non funziona ovunque, usiamo una regex più semplice:
          // sostituisce :hover quando NON è preceduto da '\\' (per evitare CSS escaped — raro).
          clones.push(rule.cssText.replace(/:hover\b/g, '[data-olo-force-hover]'));
        }
      } catch (e) { /* CORS / SecurityError su stylesheets cross-origin: skip */ }
    }
    if (clones.length) {
      var st = document.createElement('style');
      st.id = 'olo-force-hover-css';
      st.textContent = clones.join('\n');
      document.head.appendChild(st);
    }
  }

  // ── Selection ──

  // Applica un set di selezione: evidenzia TUTTE le tile del set, toolbar sulla
  // primaria (l'ultima). Sostituisce la vecchia selezione singola.
  function applySelectionSet(ids) {
    ids = Array.isArray(ids) ? ids.filter(Boolean) : [];
    var prevSel = root ? root.querySelectorAll('.olo-builder-selected') : [];
    for (var i = 0; i < prevSel.length; i++) prevSel[i].classList.remove('olo-builder-selected');
    selectedIds = ids.slice();
    selectedId = ids.length ? ids[ids.length - 1] : null;
    for (var j = 0; j < ids.length; j++) {
      var sel = findTileEl(ids[j]);
      if (sel) sel.classList.add('olo-builder-selected');
    }
    if (selectedId) {
      var pe = findTileEl(selectedId);
      if (pe) showHoverToolbar(pe, selectedId);
    } else {
      hideHoverToolbar();
    }
  }

  // Selezione singola (retro-compatibile): rimpiazza l'intero set con una tile.
  function selectTile(tileId) {
    applySelectionSet(tileId ? [tileId] : []);
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
    // Suppress click that follows an edge-drag gesture
    if (suppressNextClick) {
      suppressNextClick = false;
      e.preventDefault();
      e.stopPropagation();
      return;
    }

    // V3.22.5 — Detect clicks on interactive widgets (accordion / tabs /
    // switcher / etc.). For these we *don't* call preventDefault, AND we
    // explicitly invoke the UIkit component method since the bridge is in
    // capture phase and the native handler doesn't always fire reliably.
    // Add `data-olo-interactive` to any custom element to opt-in.
    var INTERACTIVE_SEL = '.uk-accordion-title, .uk-tab a, .uk-tab > * > a, .uk-switcher-nav a, .uk-subnav a, .uk-slidenav, .uk-dotnav a, [data-uk-toggle], [uk-toggle], [data-olo-interactive], .oit-tab, [role="tab"], [data-idx][role="button"], .olo-carousel-arrow, .olo-carousel-dot, .olo-car-arrow, .olo-car-dot';
    var isInteractive = e.target.closest && e.target.closest(INTERACTIVE_SEL);

    if (!isInteractive) {
      e.preventDefault();
      e.stopPropagation();
    } else {
      // For .uk-accordion-title (and similar <a href="#">) we only block the
      // browser default navigation (jump-to-top). We leave propagation alone
      // so the native UIkit handler in the bubble phase performs the toggle.
      // Calling toggle() manually here would cause a double-toggle (bridge +
      // UIkit), netting zero state change.
      var anchor = e.target.closest('a[href="#"]');
      if (anchor) e.preventDefault();
    }
    // Floating panel empty placeholder: open finder to insert into the panel
    var emptyEl = e.target.closest && e.target.closest('[data-olo-fp-empty]');
    if (emptyEl) {
      var fpId = emptyEl.getAttribute('data-olo-fp-empty');
      if (fpId) {
        post('olo:open-finder-for', { tileId: fpId });
        return;
      }
    }
    // Empty canvas placeholder buttons: open InsertPanel on the right tab
    var emptyActionEl = e.target.closest && e.target.closest('[data-olo-empty-action]');
    if (emptyActionEl) {
      e.preventDefault();
      e.stopPropagation();
      var action = emptyActionEl.getAttribute('data-olo-empty-action');
      post('olo:empty-action', { action: action });
      return;
    }
    var tileId = findTileId(e.target);
    if (tileId) {
      var additive = !!(e.ctrlKey || e.metaKey);
      if (additive) {
        // Toggle locale ottimistico; il parent riconcilia con olo:select-set.
        var pos = selectedIds.indexOf(tileId);
        if (pos === -1) applySelectionSet(selectedIds.concat([tileId]));
        else applySelectionSet(selectedIds.filter(function (x) { return x !== tileId; }));
      } else {
        selectTile(tileId);
      }
      post('olo:tile-click', { tileId: tileId, additive: additive });
    } else if (!isInteractive) {
      selectTile(null);
      post('olo:canvas-click');
    }
  }

  // ── Edge-hotzone drag (afferra la tile dai bordi, no overlay che blocca click) ──

  var EDGE_ZONE = 12;
  var DRAG_THRESHOLD = 5;
  var edgeDownState = null;
  var cursorAppliedEl = null;
  var suppressNextClick = false;
  var startTileDrag = null; // assegnato in getHoverToolbar

  function isBuilderChrome(target) {
    return !!(target.closest && target.closest('.olo-iframe-add-btn, .olo-iframe-toolbar, .olo-iframe-add-circle, .olo-grip-ghost, .olo-grip-indicator'));
  }

  function nearBorderOf(el, clientX, clientY) {
    var rect = el.getBoundingClientRect();
    return (clientY - rect.top) < EDGE_ZONE
        || (rect.bottom - clientY) < EDGE_ZONE
        || (clientX - rect.left) < EDGE_ZONE
        || (rect.right - clientX) < EDGE_ZONE;
  }

  function onEdgeMouseDown(e) {
    if (previewMode || gripDragging) return;
    if (e.button !== 0 && e.pointerType === 'mouse') return;
    if (isBuilderChrome(e.target)) return;
    // Skip editable text regions
    if (e.target.closest('[data-olo-editable], [contenteditable="true"]')) return;
    var tileId = findTileId(e.target);
    if (!tileId) return;
    var el = findTileEl(tileId);
    if (!el) return;
    // Il drag parte da qualunque punto del tile. DRAG_THRESHOLD (movimento > 5px)
    // distingue click (selezione) da drag — il check nearBorderOf era una restrizione
    // di troppo che impediva di afferrare tile larghi (es. headline centrato) dal centro.
    edgeDownState = { tileId: tileId, el: el, startX: e.clientX, startY: e.clientY, pointerId: e.pointerId };
  }

  function onEdgeMouseMove(e) {
    // 1. Se abbiamo un pointerdown pending, verifica threshold per avviare drag
    if (edgeDownState && !gripDragging) {
      if (edgeDownState.pointerId != null && e.pointerId !== edgeDownState.pointerId) return;
      var dx = e.clientX - edgeDownState.startX;
      var dy = e.clientY - edgeDownState.startY;
      if ((dx * dx + dy * dy) >= (DRAG_THRESHOLD * DRAG_THRESHOLD)) {
        var s = edgeDownState;
        edgeDownState = null;
        suppressNextClick = true;
        if (!hoverToolbar) getHoverToolbar();
        gripPointerId = e.pointerId;
        if (typeof startTileDrag === 'function') startTileDrag(s.tileId, s.el);
      }
      return;
    }
    if (gripDragging || previewMode) return;
    // 2. Cursor feedback: near-border → grab
    if (isBuilderChrome(e.target)) return;
    var tileId = findTileId(e.target);
    var el = tileId ? findTileEl(tileId) : null;
    if (!el || !nearBorderOf(el, e.clientX, e.clientY)) {
      if (cursorAppliedEl) { cursorAppliedEl.style.cursor = ''; cursorAppliedEl = null; }
      return;
    }
    if (cursorAppliedEl !== el) {
      if (cursorAppliedEl) cursorAppliedEl.style.cursor = '';
      el.style.cursor = 'grab';
      cursorAppliedEl = el;
    }
  }

  function onEdgeMouseUp() {
    edgeDownState = null;
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
          // Auto-assign editable attribute based on tag.
          // Marca anche come "auto" così endInlineEdit() può ripulirlo al termine —
          // altrimenti l'attributo resta appiccicato sul DOM e il check di
          // onEdgeMouseDown (skip editable text regions) blocca il drag per sempre
          // su quel tile. Bug visibile soprattutto su tile headline che vengono
          // doppio-cliccati per editare e poi non si trascinano più.
          var tag = textEl.tagName.toLowerCase();
          if (tag.match(/^h[1-6]$/)) editEl.setAttribute('data-olo-editable', 'heading');
          else if (textEl.classList.contains('olo-btn-text') || textEl.closest('.olo-btn')) editEl.setAttribute('data-olo-editable', 'text');
          else editEl.setAttribute('data-olo-editable', 'content');
          editEl.dataset.oloEditableAuto = '1';
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
    // Se l'attributo data-olo-editable era stato auto-assegnato dal doppio-click
    // (non viene dal PHP renderer), lo rimuoviamo — altrimenti resta sul DOM e
    // impedisce per sempre il drag del tile (vedi check in onEdgeMouseDown).
    if (el.dataset.oloEditableAuto === '1') {
      el.removeAttribute('data-olo-editable');
      delete el.dataset.oloEditableAuto;
    }
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
   * Coordinates are DOCUMENT-RELATIVE (include scrollY) so il parent può
   * fare hit-test corretto anche dopo scroll dell'iframe.
   */
  function sendLayoutSnapshot() {
    var grid = root.querySelector('[data-olo-zone="body"] .olo-frontend-grid') || root.querySelector('.olo-frontend-grid');
    var scrollX = window.pageXOffset || document.documentElement.scrollLeft || 0;
    var scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
    if (!grid) { post('olo:layout-snapshot', { sections: [], columns: [], containers: [], scrollX: scrollX, scrollY: scrollY }); return; }

    var sectionEls = grid.querySelectorAll(':scope > [data-olo-tile-id]');
    var sects = [];
    for (var i = 0; i < sectionEls.length; i++) {
      var r = sectionEls[i].getBoundingClientRect();
      sects.push({
        id: sectionEls[i].getAttribute('data-olo-tile-id'),
        top: r.top + scrollY, bottom: r.bottom + scrollY,
        left: r.left + scrollX, right: r.right + scrollX,
        index: i,
      });
    }

    var columnEls = root.querySelectorAll('[data-olo-tile-type="column"]');
    var cols = [];
    for (var j = 0; j < columnEls.length; j++) {
      var cr = columnEls[j].getBoundingClientRect();
      cols.push({
        id: columnEls[j].getAttribute('data-olo-tile-id'),
        top: cr.top + scrollY, bottom: cr.bottom + scrollY,
        left: cr.left + scrollX, right: cr.right + scrollX,
      });
    }

    // Container tiles (floatingpanel, ecc.) — drop target più specifico delle colonne
    var containerEls = root.querySelectorAll('[data-olo-tile-type="floatingpanel"]');
    var containers = [];
    for (var k = 0; k < containerEls.length; k++) {
      var cnr = containerEls[k].getBoundingClientRect();
      containers.push({
        id: containerEls[k].getAttribute('data-olo-tile-id'),
        type: containerEls[k].getAttribute('data-olo-tile-type'),
        top: cnr.top + scrollY, bottom: cnr.bottom + scrollY,
        left: cnr.left + scrollX, right: cnr.right + scrollX,
      });
    }

    // Elementi figli DIRETTI di ogni colonna, in ordine d'albero, con l'indice
    // nel column.children: consente il drop element-level (dropline precisa "tra
    // due tile" invece del solo append in fondo). Un discendente conta come figlio
    // diretto se il suo contenitore-antenato più vicino (con data-olo-tile-type)
    // è QUESTA colonna → salta gli elementi annidati in inner-columns/floatingpanel.
    var STRUCT = { column: 1, 'inner-column': 1, 'inner-columns': 1, floatingpanel: 1, row: 1, section: 1 };
    var elements = [];
    for (var c = 0; c < columnEls.length; c++) {
      var colEl = columnEls[c];
      var colId = colEl.getAttribute('data-olo-tile-id');
      var descendants = colEl.querySelectorAll('[data-olo-tile-id]');
      var childIdx = 0;
      for (var e = 0; e < descendants.length; e++) {
        var d = descendants[e];
        var owner = d.parentElement ? d.parentElement.closest('[data-olo-tile-type]') : null;
        if (owner !== colEl) continue; // non è figlio diretto (albero) di questa colonna
        var er = d.getBoundingClientRect();
        elements.push({
          columnId: colId,
          index: childIdx,
          top: er.top + scrollY, bottom: er.bottom + scrollY,
          left: er.left + scrollX, right: er.right + scrollX,
        });
        childIdx++;
      }
    }

    post('olo:layout-snapshot', { sections: sects, columns: cols, containers: containers, elements: elements, scrollX: scrollX, scrollY: scrollY });
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

    // Re-init PDF viewers (runtime presenti nel builder iframe)
    if (window.OloPdfViewer && typeof window.OloPdfViewer.initAll === 'function') {
      try { window.OloPdfViewer.initAll(); } catch(e) {}
    }
    if (window.OloPdfPro && typeof window.OloPdfPro.initAll === 'function') {
      try { window.OloPdfPro.initAll(); } catch(e) {}
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

      // "+" between each section
      for (var i = 0; i <= sections.length; i++) {
        var btn = createAddSectionButton(i);
        if (i < sections.length) {
          sections[i].parentNode.insertBefore(btn, sections[i]);
        } else {
          grid.appendChild(btn);
        }
      }

      // "+" between rows inside each section
      sections.forEach(injectRowButtons);
    });

    // If no zones (body-only), add to the main grid
    if (!zones.length) {
      var grid = root.querySelector('.olo-frontend-grid');
      if (grid) {
        var sections = grid.querySelectorAll(':scope > section[data-olo-tile-id]');
        for (var i = 0; i <= sections.length; i++) {
          var btn = createAddSectionButton(i);
          if (i < sections.length) {
            sections[i].parentNode.insertBefore(btn, sections[i]);
          } else {
            grid.appendChild(btn);
          }
        }
        sections.forEach(injectRowButtons);
      }
    }
  }

  /**
   * Insert "+" buttons between top-level rows inside a given section element.
   * Top-level: rows that are NOT nested inside another row/column/inner-columns.
   */
  function injectRowButtons(sectionEl) {
    var sectionId = sectionEl.getAttribute('data-olo-tile-id');
    if (!sectionId) return;
    var allRows = sectionEl.querySelectorAll('[data-olo-tile-type="row"]');
    var topRows = [];
    allRows.forEach(function(row) {
      var p = row.parentElement;
      var nested = false;
      while (p && p !== sectionEl) {
        var pt = p.getAttribute && p.getAttribute('data-olo-tile-type');
        if (pt === 'row' || pt === 'column' || pt === 'inner-columns' || pt === 'inner-column') { nested = true; break; }
        p = p.parentElement;
      }
      if (!nested) topRows.push(row);
    });

    for (var i = 0; i <= topRows.length; i++) {
      var btn = createAddRowButton(sectionId, i);
      if (i < topRows.length) {
        topRows[i].parentNode.insertBefore(btn, topRows[i]);
      } else if (topRows.length > 0) {
        // dopo l'ultima riga, prima dei sibling controls (presets, ecc.)
        topRows[topRows.length - 1].parentNode.appendChild(btn);
      }
    }
  }

  function createAddSectionButton(index) {
    var wrap = document.createElement('div');
    wrap.className = 'olo-iframe-add-btn olo-iframe-add-btn--section';
    wrap.setAttribute('data-index', index);
    wrap.innerHTML = '<button class="olo-iframe-add-circle" title="Aggiungi sezione">' +
      '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2"><line x1="7" y1="2" x2="7" y2="12"/><line x1="2" y1="7" x2="12" y2="7"/></svg>' +
      '</button>';
    wrap.querySelector('button').addEventListener('click', function(e) {
      e.stopPropagation();
      post('olo:add-section', { index: index });
    });
    return wrap;
  }

  function createAddRowButton(sectionId, rowIndex) {
    var wrap = document.createElement('div');
    wrap.className = 'olo-iframe-add-btn olo-iframe-add-btn--row';
    wrap.setAttribute('data-section-id', sectionId);
    wrap.setAttribute('data-row-index', rowIndex);
    wrap.innerHTML = '<button class="olo-iframe-add-circle olo-iframe-add-circle--row" title="Aggiungi riga">' +
      '<svg width="12" height="12" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2"><line x1="7" y1="2" x2="7" y2="12"/><line x1="2" y1="7" x2="12" y2="7"/></svg>' +
      '</button>';
    wrap.querySelector('button').addEventListener('click', function(e) {
      e.stopPropagation();
      post('olo:add-row', { sectionId: sectionId, rowIndex: rowIndex });
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
        '<span class="olo-iframe-tb-grip" title="Trascina per spostare"><svg width="10" height="16" viewBox="0 0 10 16" fill="currentColor" aria-hidden="true"><circle cx="2" cy="2" r="1.4"/><circle cx="8" cy="2" r="1.4"/><circle cx="2" cy="8" r="1.4"/><circle cx="8" cy="8" r="1.4"/><circle cx="2" cy="14" r="1.4"/><circle cx="8" cy="14" r="1.4"/></svg></span>' +
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
      // Variables declared at outer IIFE scope so edge-handlers can read them.
      gripDragging = false;
      gripDragId = null;
      gripGhost = null;
      gripDropTarget = null;
      gripDropBefore = true;
      gripIndicator = null;

      // Reusable tile-drag starter — used by grip and by edge-hotzone drag.
      startTileDrag = function(tileId, el, labelText) {
        gripDragging = true;
        gripDragId = tileId;

        gripGhost = document.createElement('div');
        gripGhost.className = 'olo-grip-ghost';
        var rect = el.getBoundingClientRect();
        gripGhost.style.width = Math.min(rect.width, 300) + 'px';
        gripGhost.style.height = Math.min(rect.height, 60) + 'px';
        var label = labelText;
        if (!label && hoverToolbar) {
          var lbl = hoverToolbar.querySelector('.olo-iframe-toolbar-label');
          label = lbl ? lbl.textContent : 'Sposta';
        }
        gripGhost.textContent = label || 'Sposta';
        document.body.appendChild(gripGhost);

        if (!gripIndicator) {
          gripIndicator = document.createElement('div');
          gripIndicator.className = 'olo-grip-indicator';
          document.body.appendChild(gripIndicator);
        }

        el.style.opacity = '0.3';
        el.style.transition = 'opacity 0.2s';

        if (hoverToolbar) hoverToolbar.style.display = 'none';

        // v3.55.30 — attiva la classe globale olo-dragging così il CSS forza il
        // cursore "mano chiusa" (var(--olo-cursor-grabbing)) ovunque durante il
        // drag interno tile→tile. Senza questa, il cursor durante movimento
        // restava la "manina puntata" del SO Windows perché nessuna classe
        // attivava l'override CSS.
        document.body.classList.add('olo-dragging');
      };

      // Pointer events (non mouse events): funzionano anche con touch/pen.
      // Il grip ha touch-action: none (iframe-builder.css) così il browser non
      // interpreta il gesto col dito come scroll (pointercancel = drag morto).
      var grip = hoverToolbar.querySelector('.olo-iframe-tb-grip');
      if (grip) {
        grip.addEventListener('pointerdown', function(e) {
          if (e.button !== 0 && e.pointerType === 'mouse') return;
          var tileId = hoverToolbar.getAttribute('data-for-tile');
          var el = tileId ? findTileEl(tileId) : null;
          if (!el) return;
          e.preventDefault();
          e.stopPropagation();
          gripPointerId = e.pointerId;
          startTileDrag(tileId, el);
        });
      }

      document.addEventListener('pointermove', function(e) {
        if (!gripDragging || !gripGhost) return;
        if (gripPointerId !== null && e.pointerId !== gripPointerId) return;

        // Move ghost
        gripGhost.style.left = (e.clientX + 12) + 'px';
        gripGhost.style.top = (e.clientY - 20) + 'px';

        // Auto-scroll quando il pointer è vicino ai bordi del viewport
        updateGripAutoScroll(e.clientY);

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

      document.addEventListener('pointerup', function(e) {
        if (!gripDragging) return;
        if (gripPointerId !== null && e.pointerId !== gripPointerId) return;
        endGripDrag(true);
      });

      // pointercancel (es. pan del browser, palm rejection): annulla senza reorder.
      document.addEventListener('pointercancel', function(e) {
        if (!gripDragging) return;
        if (gripPointerId !== null && e.pointerId !== gripPointerId) return;
        endGripDrag(false);
      });
    }
    return hoverToolbar;
  }

  // ── Section drag reorder in iframe ──
  // v3.55.24 — RIMOSSO `initSectionDrag()` HTML5 (dragstart/dragover/drop).
  // Era dead code: nessuna tile ha `draggable="true"` quindi gli eventi non si
  // firavano mai. Il drag reorder dentro l'iframe è già implementato in modo
  // custom via pointer events (Edge-hotzone drag + grip mousedown sopra → vedi
  // riga ~795 startTileDrag). Il vantaggio del custom: cursor: var(--olo-cursor-*)
  // funziona, niente "manina pixelata" di Windows del drag HTML5 nativo.

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
          // initSectionDrag() rimosso v3.55.24 — drag custom basato su pointer events
          // (vedi onEdgeMouseDown / startTileDrag) gestisce già il reorder.
          for (var si = 0; si < selectedIds.length; si++) {
            var selEl = findTileEl(selectedIds[si]);
            if (selEl) selEl.classList.add('olo-builder-selected');
          }
          // Riapplica force-hover se era attivo prima del re-render
          if (forceHoverState.enabled && forceHoverState.tileId) {
            applyForceHover(true, forceHoverState.tileId);
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
              if (selectedIds.indexOf(d.tileId) !== -1) {
                newEl.classList.add('olo-builder-selected');
              }

              // Scoped CSS (hover + responsive) — rimpiazza eventuale <style data-tile-id="X">
              // precedente per evitare accumulo di rules stale dopo molte patch sullo stesso tile.
              var STYLE_ID = 'olo-tile-style-' + d.tileId.replace(/[^A-Za-z0-9_-]/g, '');
              var oldStyle = document.getElementById(STYLE_ID);
              if (oldStyle) oldStyle.remove();
              if (d.scoped_css) {
                var styleEl = document.createElement('style');
                styleEl.id = STYLE_ID;
                styleEl.setAttribute('data-tile-id', d.tileId);
                styleEl.textContent = d.scoped_css;
                document.head.appendChild(styleEl);
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

      case 'olo:render-zone': {
        // Re-render live di una zona (header/footer) in modalità INLINE: l'header reale è
        // renderizzato dal tema e vive FUORI da #olo-iframe-root, quindi il full render lo
        // salta. Qui sostituiamo SOLO il CONTENUTO della zona, preservandone il wrapper
        // (<header class="olo-site-header ...">) così overlay/sticky restano intatti, e
        // rieseguiamo gli <script> inline del tile (es. runtime megamenu).
        if (d.zone && d.html) {
          var zoneEl = document.querySelector('[data-olo-zone="' + d.zone + '"]')
                    || document.querySelector(d.zone === 'footer' ? 'footer.olo-site-footer' : 'header.olo-site-header');
          if (zoneEl) {
            var tmpZ = document.createElement('div');
            tmpZ.innerHTML = String(d.html).trim();
            var srcZone = tmpZ.querySelector('[data-olo-zone="' + d.zone + '"]') || tmpZ.firstElementChild;
            if (srcZone) {
              zoneEl.innerHTML = srcZone.innerHTML;
              // Doppio rAF: layout pronto prima di eseguire gli script (sticky/posPanel
              // del megamenu usano getBoundingClientRect).
              requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                  executeInlineScripts(zoneEl);
                  setTimeout(function() { reinitUIkit(); reinitTileScripts(); }, 30);
                });
              });
            }
          }
        }
        break;
      }

      case 'olo:select':
        selectTile(d.tileId || null);
        break;

      case 'olo:select-set':
        applySelectionSet(d.ids || []);
        break;

      case 'olo:deselect':
        selectTile(null);
        break;

      case 'olo:scroll-to':
        if (d.tileId) {
          // Retry: dopo un inserimento il re-render del body è asincrono
          // (~300ms debounce + fetch REST), quindi l'elemento può non esistere
          // ancora al primo tentativo. Riprova per ~1.2s poi rinuncia.
          (function(tileId, flash) {
            var attempts = 0;
            var MAX = 24; // 24 × 50ms ≈ 1.2s
            var tryScroll = function() {
              var scrollEl = findTileEl(tileId);
              if (scrollEl) {
                var sf = flash || {};
                // scroll_ms: 0 = salto istantaneo, altrimenti smooth + attesa prima del flash.
                var scrollMs = (sf.scroll_ms === undefined) ? 500 : (parseInt(sf.scroll_ms, 10) || 0);
                scrollEl.scrollIntoView({ behavior: scrollMs === 0 ? 'auto' : 'smooth', block: 'center' });
                applyScrollFlash(scrollEl, sf, scrollMs);
                return;
              }
              if (++attempts < MAX) setTimeout(tryScroll, 50);
            };
            tryScroll();
          })(d.tileId, d.flash);
        }
        break;

      case 'olo:set-page-bg':
        // Applica il bg pagina IMMEDIATAMENTE via inline style su html+body, senza
        // aspettare il render REST completo. Update live mentre l'utente cambia colori.
        // Il render REST farà il merge definitivo del style; questo è solo per UX immediata.
        try {
          var bg = d.page_bg || {};
          var hasBg = bg.type && bg.type !== 'none';
          // Marker sul body: dice ai reset CSS del template di togliersi di mezzo.
          if (hasBg) {
            document.body.setAttribute('data-olo-pagebg', '1');
          } else {
            document.body.removeAttribute('data-olo-pagebg');
          }
          var styleEl = document.getElementById('olo-builder-live-page-bg');
          if (!styleEl) {
            styleEl = document.createElement('style');
            styleEl.id = 'olo-builder-live-page-bg';
            document.head.appendChild(styleEl);
          }
          var css = '';
          // !important per battere reset CSS del template, style-system o tema.
          if (bg.type === 'solid' && bg.color) {
            css = 'background-color: ' + bg.color + ' !important; background-image: none !important;';
          } else if (bg.type === 'gradient') {
            var ga = parseInt(bg.gradient_angle) || 180;
            css = 'background: linear-gradient(' + ga + 'deg, ' + (bg.gradient_from || '#fff') + ', ' + (bg.gradient_to || '#000') + ') !important;';
          } else if (bg.type === 'image' && bg.image_url) {
            css = 'background-image: url(' + bg.image_url + ') !important;background-size:' + (bg.image_size || 'cover') + ' !important;background-position:' + (bg.image_position || 'center center') + ' !important;background-repeat:no-repeat !important;';
          }
          // Applichiamo a TUTTI i container possibili: html, body, root wrapper.
          // Sufficienti per coprire i casi in cui un parent ha bg suo che maschera body.
          styleEl.textContent = css ? ('html, body, body > #olo-iframe-root { ' + css + ' }') : '';
          console.log('[bridge] olo:set-page-bg applied:', bg.type, bg.color || bg.gradient_from || bg.image_url || '');
        } catch (e) { console.warn('[bridge] olo:set-page-bg error', e); }
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
              // Color-coding livelli = stessa lingua dei bottoni add del canvas:
              // sezione arancio chrome, row verde, tile blu.
              '.olo-wireframe-mode .olo-frontend-grid > section { outline: 2px dashed rgba(232,98,42,0.35) !important; outline-offset: -2px; position: relative; }' +
              '.olo-wireframe-mode .olo-frontend-grid > section::after { content: "SEZIONE"; position: absolute; top: 4px; right: 8px; font-size: 9px; font-weight: 700; letter-spacing: 1px; color: rgba(232,98,42,0.55); pointer-events: none; z-index: 10; }' +
              '.olo-wireframe-mode [data-olo-tile-id][class*="olo-row"], .olo-wireframe-mode .uk-grid { outline: 1px dashed rgba(16,185,129,0.35) !important; outline-offset: -1px; }' +
              '.olo-wireframe-mode [data-olo-tile-id] { outline: 1px solid rgba(59,130,246,0.18) !important; outline-offset: -1px; position: relative; }' +
              '.olo-wireframe-mode [data-olo-tile-id]:hover { outline-color: rgba(59,130,246,0.5) !important; }';
            document.head.appendChild(st);
          }
        } else if (existing) {
          existing.remove();
        }
        break;

      case 'olo:force-hover':
        applyForceHover(!!d.enabled, d.tileId || null);
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
  // Edge-hotzone drag: pointerdown su una tile + movimento > 5px = drag.
  // Pointer events (non mouse) così funziona anche con pen; con touch resta
  // soggetto al touch-action della tile (di default il pan/scroll vince — il
  // reorder touch passa dal grip della toolbar, che ha touch-action: none).
  root.addEventListener('pointerdown', onEdgeMouseDown, true);
  document.addEventListener('pointermove', onEdgeMouseMove);
  document.addEventListener('pointerup', onEdgeMouseUp, true);
  document.addEventListener('pointercancel', onEdgeMouseUp, true);
  document.addEventListener('click', blockLinks, true);
  // (HTML5 drag già bloccato al top del modulo, vedi commento v3.55.29.)
  window.addEventListener('message', onMessage, false);

  observeHeight();

    // Forward key events to parent so shortcuts (Delete, Ctrl+C/V) work
  document.addEventListener('keydown', function(e) {
    // Esc durante un grip drag interno: annulla senza reorder (il safety net
    // del parent non vede i keydown di questo document).
    if (e.key === 'Escape' && gripDragging) {
      e.preventDefault();
      e.stopPropagation();
      endGripDrag(false);
      return;
    }
    // Skip if editing text
    var tag = e.target.tagName;
    if (tag === 'INPUT' || tag === 'TEXTAREA' || e.target.isContentEditable) return;
    // Forward Delete, Backspace, Ctrl+C/V/Z/S/D, Ctrl+Alt+C/V, Alt+frecce (nudge).
    // Ctrl+D incluso anche per fare preventDefault (altrimenti Chrome apre "Aggiungi preferito").
    if (e.key === 'Delete' || e.key === 'Backspace' || (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 'z' || e.key === 's' || e.key === 'd' || e.code === 'KeyC' || e.code === 'KeyV' || e.code === 'KeyD')) || (e.altKey && !e.ctrlKey && (e.key === 'ArrowUp' || e.key === 'ArrowDown'))) {
      e.preventDefault();
      parent.postMessage({ type: 'olo:keydown', key: e.key, code: e.code, ctrlKey: e.ctrlKey, shiftKey: e.shiftKey, altKey: e.altKey, metaKey: e.metaKey }, '*');
    }
  });

  // Signal ready to parent. mode = 'inline' (WP renderizza header/footer reali,
  // il bridge aggiorna SOLO il body) | 'standalone' (template HTML standalone).
  post('olo:ready', { mode: (typeof window !== 'undefined' && window.OLO_IFRAME_MODE) ? window.OLO_IFRAME_MODE : 'standalone' });

})();
