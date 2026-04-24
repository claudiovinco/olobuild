/**
 * Olobuild PDF Viewer — Runtime frontend
 * Depends on: pdfjsLib (pdf.min.js), St.PageFlip (page-flip.browser.js)
 */
(function () {
  'use strict';
  if (window._oloPdfPro) return;
  window._oloPdfPro = 1;

  /* ───── CSS inject ───── */
  var CSS = [
    /* Container */
    '.olo-pdfv { display:flex; flex-direction:column; width:100%; height:100%; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; }',

    /* Toolbar */
    '.olo-pdfv-toolbar { display:flex; align-items:center; gap:6px; padding:6px 10px; flex-shrink:0; border-bottom:1px solid rgba(0,0,0,.1); user-select:none; z-index:5; }',
    '.olo-pdfv-toolbar.olo-pdfv-dark { background:#1e1e1e; color:#e0e0e0; border-color:rgba(255,255,255,.1); }',
    '.olo-pdfv-toolbar.olo-pdfv-light { background:#ffffff; color:#333; }',
    '.olo-pdfv-toolbar button { background:none; border:1px solid transparent; border-radius:4px; padding:4px 7px; cursor:pointer; color:inherit; font-size:14px; line-height:1; display:inline-flex; align-items:center; justify-content:center; min-width:28px; height:28px; }',
    '.olo-pdfv-toolbar button:hover { background:rgba(128,128,128,.15); }',
    '.olo-pdfv-toolbar button:active { background:rgba(128,128,128,.25); }',
    '.olo-pdfv-toolbar button.olo-pdfv-active { background:rgba(99,102,241,.15); color:#6366f1; }',
    '.olo-pdfv-toolbar .olo-pdfv-sep { width:1px; height:20px; background:rgba(128,128,128,.25); flex-shrink:0; }',
    '.olo-pdfv-toolbar .olo-pdfv-page-info { font-size:12px; white-space:nowrap; }',
    '.olo-pdfv-toolbar .olo-pdfv-page-input { width:40px; text-align:center; border:1px solid rgba(128,128,128,.3); border-radius:3px; padding:2px 4px; font-size:12px; background:transparent; color:inherit; }',
    '.olo-pdfv-toolbar .olo-pdfv-spacer { flex:1; }',

    /* Search bar */
    '.olo-pdfv-search { display:none; align-items:center; gap:6px; padding:4px 10px; border-bottom:1px solid rgba(0,0,0,.1); flex-shrink:0; }',
    '.olo-pdfv-search.olo-pdfv-visible { display:flex; }',
    '.olo-pdfv-search.olo-pdfv-dark { background:#2a2a2a; color:#e0e0e0; }',
    '.olo-pdfv-search.olo-pdfv-light { background:#f9f9f9; color:#333; }',
    '.olo-pdfv-search input { flex:1; max-width:250px; border:1px solid rgba(128,128,128,.3); border-radius:3px; padding:4px 8px; font-size:13px; background:transparent; color:inherit; }',
    '.olo-pdfv-search .olo-pdfv-search-count { font-size:11px; opacity:.7; white-space:nowrap; }',

    /* Viewer area */
    '.olo-pdfv-body { flex:1; overflow:hidden; position:relative; display:flex; }',

    /* Thumbnails sidebar */
    '.olo-pdfv-thumbs { width:120px; flex-shrink:0; overflow-y:auto; padding:8px; border-right:1px solid rgba(0,0,0,.1); display:none; }',
    '.olo-pdfv-thumbs.olo-pdfv-visible { display:block; }',
    '.olo-pdfv-thumbs.olo-pdfv-dark { background:#1a1a1a; }',
    '.olo-pdfv-thumbs.olo-pdfv-light { background:#f0f0f0; }',
    '.olo-pdfv-thumb { cursor:pointer; margin-bottom:8px; border:2px solid transparent; border-radius:3px; overflow:hidden; }',
    '.olo-pdfv-thumb.olo-pdfv-active { border-color:#6366f1; }',
    '.olo-pdfv-thumb canvas { display:block; width:100%; height:auto; }',
    '.olo-pdfv-thumb-num { text-align:center; font-size:10px; opacity:.6; margin-top:2px; }',

    /* Canvas area */
    '.olo-pdfv-canvas-wrap { flex:1; overflow:auto; display:flex; justify-content:center; align-items:stretch; position:relative; }',

    /* Single / double page */
    '.olo-pdfv-pages { display:flex; justify-content:center; align-items:center; width:100%; }',
    '.olo-pdfv-pages canvas { display:block; box-shadow:0 2px 8px rgba(0,0,0,.15); }',
    '.olo-pdfv-pages.olo-pdfv-double { gap:4px; }',

    /* Scroll mode */
    '.olo-pdfv-scroll { display:flex; flex-direction:column; align-items:center; gap:8px; padding:16px 0; }',
    '.olo-pdfv-scroll canvas { display:block; box-shadow:0 1px 4px rgba(0,0,0,.1); }',
    '.olo-pdfv-scroll .olo-pdfv-page-placeholder { background:rgba(128,128,128,.08); display:flex; align-items:center; justify-content:center; color:rgba(128,128,128,.4); font-size:12px; }',

    /* Flipbook */
    '.olo-pdfv-flipbook-wrap { flex:1; display:flex; justify-content:center; align-items:center; overflow:hidden; width:100%; height:100%; }',
    '.olo-pdfv-flipbook-wrap.olo-pdfv-panning { cursor:grabbing!important; }',
    '.olo-pdfv-flipbook-container { position:relative; transition:transform 0s; }',
    '.olo-pdfv-flipbook-page { background:#fff; }',
    '.olo-pdfv-flipbook-page canvas { display:block; width:100%; height:100%; }',

    /* Loading */
    '.olo-pdfv-loading { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; z-index:3; }',
    '.olo-pdfv-spinner { width:36px; height:36px; border:3px solid rgba(128,128,128,.2); border-top-color:#6366f1; border-radius:50%; animation:olo-pdfv-spin .8s linear infinite; }',
    '@keyframes olo-pdfv-spin { to { transform:rotate(360deg); } }',

    /* Fullscreen */
    '.olo-pdfv-fullscreen { width:100vw!important; height:100vh!important; border-radius:0!important; border:none!important; background:#f5f5f5!important; }',
    '.olo-pdfv-fullscreen:not(:fullscreen) { position:fixed!important; inset:0!important; z-index:2147483647!important; }',

    /* Hotspot layer */
    '.olo-pdfv-hotspot-layer { position:absolute; inset:0; pointer-events:none; z-index:3; }',
    '.olo-pdfv-hotspot { position:absolute; border-radius:50%; cursor:pointer; pointer-events:auto; transform:translate(-50%,-50%); z-index:4; box-shadow:0 0 0 2px rgba(255,255,255,.8),0 2px 6px rgba(0,0,0,.3); transition:transform .15s ease; display:flex; align-items:center; justify-content:center; overflow:hidden; }',
    '.olo-pdfv-hotspot:hover { transform:translate(-50%,-50%) scale(1.3); }',
    '.olo-pdfv-hotspot-pulse { position:absolute; inset:-4px; border-radius:50%; border:2px solid currentColor; opacity:0; animation:olo-pdfv-pulse 2s ease-out infinite; pointer-events:none; }',
    '@keyframes olo-pdfv-pulse { 0%{transform:scale(.8);opacity:.6} 100%{transform:scale(1.8);opacity:0} }',

    /* Page wrap for double mode */
    '.olo-pdfv-page-wrap { position:relative; display:inline-block; }',

    /* Popup */
    '.olo-pdfv-popup { position:absolute; z-index:10; width:280px; max-width:90vw; background:#fff; border-radius:10px; box-shadow:0 8px 30px rgba(0,0,0,.25); overflow:hidden; pointer-events:auto; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; animation:olo-pdfv-popup-in .2s ease; }',
    '@keyframes olo-pdfv-popup-in { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }',
    '.olo-pdfv-popup-arrow { position:absolute; width:12px; height:12px; background:#fff; transform:rotate(45deg); box-shadow:-2px -2px 4px rgba(0,0,0,.08); z-index:-1; }',
    '.olo-pdfv-popup-close { position:absolute; top:6px; right:6px; width:24px; height:24px; border:none; background:rgba(0,0,0,.06); border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#666; font-size:14px; line-height:1; z-index:2; padding:0; }',
    '.olo-pdfv-popup-close:hover { background:rgba(0,0,0,.12); }',
    '.olo-pdfv-popup-img { display:block; width:100%; height:auto; }',
    '.olo-pdfv-popup-video { position:relative; width:100%; padding-bottom:56.25%; background:#000; }',
    '.olo-pdfv-popup-video iframe,.olo-pdfv-popup-video video { position:absolute; inset:0; width:100%; height:100%; border:none; }',
    '.olo-pdfv-popup-body { padding:12px 14px; }',
    '.olo-pdfv-popup-body h4 { margin:0 0 4px; font-size:14px; font-weight:600; color:#1a1a1a; line-height:1.3; }',
    '.olo-pdfv-popup-body p { margin:0 0 8px; font-size:13px; color:#555; line-height:1.5; }',
    '.olo-pdfv-popup-btn { display:inline-block; padding:6px 14px; background:#4f46e5; color:#fff; text-decoration:none; border-radius:6px; font-size:13px; font-weight:500; line-height:1.4; transition:filter .15s; cursor:pointer; }',
    '.olo-pdfv-popup-btn:hover { text-decoration:none; }',

    /* Hide hotspots during flip animation */
    '.olo-pdfv-flipping .olo-pdfv-hotspot-layer { visibility:hidden; }',

    /* ── Bottom progress bar (page nav slider + zoom + fullscreen) ── */
    '.olo-pdfv-bottombar { display:flex; align-items:center; gap:12px; padding:10px 14px; flex-shrink:0; background:#1e1e1e; color:#f0f0f0; border-radius:10px; margin:8px; user-select:none; }',
    '.olo-pdfv-bottombar .olo-pdfv-bb-info { font-size:13px; font-variant-numeric:tabular-nums; white-space:nowrap; min-width:60px; }',
    '.olo-pdfv-bottombar .olo-pdfv-bb-range { flex:1; appearance:none; -webkit-appearance:none; background:transparent; height:24px; cursor:pointer; padding:0; margin:0; }',
    '.olo-pdfv-bottombar .olo-pdfv-bb-range::-webkit-slider-runnable-track { height:3px; background:rgba(255,255,255,.25); border-radius:2px; }',
    '.olo-pdfv-bottombar .olo-pdfv-bb-range::-moz-range-track { height:3px; background:rgba(255,255,255,.25); border-radius:2px; }',
    '.olo-pdfv-bottombar .olo-pdfv-bb-range::-webkit-slider-thumb { -webkit-appearance:none; appearance:none; width:14px; height:14px; border-radius:50%; background:#fff; margin-top:-5.5px; cursor:pointer; box-shadow:0 1px 3px rgba(0,0,0,.4); }',
    '.olo-pdfv-bottombar .olo-pdfv-bb-range::-moz-range-thumb { width:14px; height:14px; border-radius:50%; background:#fff; border:none; cursor:pointer; box-shadow:0 1px 3px rgba(0,0,0,.4); }',
    '.olo-pdfv-bottombar .olo-pdfv-bb-btn { background:none; border:none; color:#f0f0f0; cursor:pointer; padding:4px; display:inline-flex; align-items:center; justify-content:center; border-radius:4px; opacity:.85; transition:opacity .12s, background .12s; }',
    '.olo-pdfv-bottombar .olo-pdfv-bb-btn:hover { opacity:1; background:rgba(255,255,255,.1); }',
    '.olo-pdfv-bottombar .olo-pdfv-bb-btn:active { background:rgba(255,255,255,.18); }',
  ].join('\n');

  var styleEl = document.createElement('style');
  styleEl.textContent = CSS;
  document.head.appendChild(styleEl);

  /* ───── SVG Icons ───── */
  var ICONS = {
    prev: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>',
    next: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 6 15 12 9 18"/></svg>',
    zoomIn: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>',
    zoomOut: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>',
    fullscreen: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>',
    exitFullscreen: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 14 10 14 10 20"/><polyline points="20 10 14 10 14 4"/><line x1="14" y1="10" x2="21" y2="3"/><line x1="3" y1="21" x2="10" y2="14"/></svg>',
    download: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
    print: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>',
    search: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
    thumbs: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>',
    close: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
  };

  /* ───── Helper: create element ───── */
  function el(tag, cls, html) {
    var e = document.createElement(tag);
    if (cls) e.className = cls;
    if (html) e.innerHTML = html;
    return e;
  }

  function btn(icon, title) {
    var b = el('button', '', ICONS[icon] || icon);
    b.title = title || '';
    b.type = 'button';
    return b;
  }

  /* ───── ZOOM LEVELS ───── */
  var ZOOM_LEVELS = [0.25, 0.33, 0.5, 0.67, 0.75, 1, 1.25, 1.5, 2, 3, 4];

  /* ───── DPR helper for crisp rendering ───── */
  function getDPR() { return window.devicePixelRatio || 1; }

  /** Render a PDF page to a canvas with HiDPI support.
   *  Sets canvas pixel buffer to scale*dpr, CSS size to scale*1. */
  function renderHiDPI(page, canvas, scale) {
    var dpr = getDPR();
    var viewport = page.getViewport({ scale: scale * dpr });
    var cssViewport = page.getViewport({ scale: scale });
    var ctx = canvas.getContext('2d');
    canvas.width = Math.round(viewport.width);
    canvas.height = Math.round(viewport.height);
    canvas.style.width = Math.round(cssViewport.width) + 'px';
    canvas.style.height = Math.round(cssViewport.height) + 'px';
    return page.render({ canvasContext: ctx, viewport: viewport }).promise;
  }

  /* ───── Video URL parser ───── */
  function parseVideoUrl(url) {
    if (!url) return null;
    var m;
    // YouTube
    m = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w-]{11})/);
    if (m) return { type: 'iframe', src: 'https://www.youtube.com/embed/' + m[1] + '?autoplay=1&rel=0' };
    // Vimeo
    m = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
    if (m) return { type: 'iframe', src: 'https://player.vimeo.com/video/' + m[1] + '?autoplay=1' };
    // Direct video file
    if (/\.(mp4|webm|ogg)(\?|$)/i.test(url)) return { type: 'video', src: url };
    return null;
  }

  /* ═══════════════════════════════════════════════════
     OloPdfPro class
     ═══════════════════════════════════════════════════ */
  function OloPdfPro(container, config) {
    this.container = container;
    this.config = config;
    this.pdfDoc = null;
    this.numPages = 0;
    this.currentPage = Math.max(1, config.startPage || 1);
    this.scale = 1;
    this.isFullscreen = false;
    this.thumbsVisible = false;
    this.searchVisible = false;
    this.pageFlip = null;
    this.renderedPages = {};
    this.pageCanvases = {};
    this._observer = null;
    this._destroyed = false;
    this._activePopup = null;
    this._activePopupHs = null;
    this._hotspotsByPage = {};

    this._init();
  }

  OloPdfPro.prototype._init = function () {
    var self = this;
    var c = this.config;
    var theme = c.theme || 'light';

    // Root wrapper
    this.root = el('div', 'olo-pdfv');
    this.container.appendChild(this.root);

    // Loading indicator
    this.loadingEl = el('div', 'olo-pdfv-loading', '<div class="olo-pdfv-spinner"></div>');
    this.root.appendChild(this.loadingEl);

    // Build toolbar
    if (c.toolbar.enabled) {
      this._buildToolbar(theme);
    }

    // Search bar removed — not applicable for canvas rendering

    // Body
    this.body = el('div', 'olo-pdfv-body');
    this.root.appendChild(this.body);

    // Thumbnails sidebar
    if (c.toolbar.thumbnails) {
      this.thumbsSidebar = el('div', 'olo-pdfv-thumbs olo-pdfv-' + theme);
      this.body.appendChild(this.thumbsSidebar);
    }

    // Canvas area
    this.canvasWrap = el('div', 'olo-pdfv-canvas-wrap');
    this.body.appendChild(this.canvasWrap);

    // Bottom progress bar (page slider + zoom + fullscreen)
    this._buildBottomBar();

    // Setup PDF.js worker
    if (typeof pdfjsLib !== 'undefined') {
      var workerUrl = (window.oloPdfProData || {}).workerUrl || '';
      if (workerUrl) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = workerUrl;
      }
    }

    // Load PDF
    this._loadPdf().then(function () {
      self._hideLoading();
      self._initMode();
      self._initHotspots();
      var nav = (self.config && self.config.nav) || {};
      if (nav.keyboard !== false) self._bindKeys();
      self._buildThumbnails();
    }).catch(function (err) {
      self._hideLoading();
      self.root.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#999;font-size:14px;">Impossibile caricare il PDF</div>';
      console.error('OloPdfPro:', err);
    });
  };

  /* ───── Load PDF ───── */
  OloPdfPro.prototype._loadPdf = function () {
    var self = this;
    if (typeof pdfjsLib === 'undefined') {
      return Promise.reject(new Error('pdfjsLib not loaded'));
    }
    var loadingTask = pdfjsLib.getDocument(this.config.url);
    return loadingTask.promise.then(function (pdf) {
      self.pdfDoc = pdf;
      self.numPages = pdf.numPages;
      if (self.currentPage > self.numPages) self.currentPage = 1;
      self._updatePageInfo();
    });
  };

  /* ───── Toolbar ───── */
  OloPdfPro.prototype._buildToolbar = function (theme) {
    var self = this;
    var tb = this.config.toolbar;
    this.toolbar = el('div', 'olo-pdfv-toolbar olo-pdfv-' + theme);

    // Page navigation
    if (tb.pageNav) {
      this.btnPrev = btn('prev', 'Pagina precedente');
      this.btnNext = btn('next', 'Pagina successiva');
      this.pageInput = el('input', 'olo-pdfv-page-input');
      this.pageInput.type = 'number';
      this.pageInput.min = 1;
      this.pageInput.value = this.currentPage;
      this.pageTotal = el('span', 'olo-pdfv-page-info', '/ -');

      this.toolbar.appendChild(this.btnPrev);
      this.toolbar.appendChild(this.pageInput);
      this.toolbar.appendChild(this.pageTotal);
      this.toolbar.appendChild(this.btnNext);

      this.btnPrev.addEventListener('click', function () { self.prevPage(); });
      this.btnNext.addEventListener('click', function () { self.nextPage(); });
      this.pageInput.addEventListener('change', function () {
        var p = parseInt(this.value, 10);
        if (p >= 1 && p <= self.numPages) {
          self.goToPage(p);
        }
      });
      this.pageInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          this.blur();
        }
      });

      this.toolbar.appendChild(el('div', 'olo-pdfv-sep'));
    }

    // Zoom
    if (tb.zoom) {
      this.btnZoomOut = btn('zoomOut', 'Riduci');
      this.btnZoomIn = btn('zoomIn', 'Ingrandisci');
      this.zoomLabel = el('span', 'olo-pdfv-page-info', '100%');

      this.toolbar.appendChild(this.btnZoomOut);
      this.toolbar.appendChild(this.zoomLabel);
      this.toolbar.appendChild(this.btnZoomIn);

      this.btnZoomOut.addEventListener('click', function () { self.zoomOut(); });
      this.btnZoomIn.addEventListener('click', function () { self.zoomIn(); });

      this.toolbar.appendChild(el('div', 'olo-pdfv-sep'));
    }

    this.toolbar.appendChild(el('div', 'olo-pdfv-spacer'));

    // Search removed — PDF rendered as canvas images, text search not possible

    // Thumbnails toggle
    if (tb.thumbnails) {
      this.btnThumbs = btn('thumbs', 'Miniature');
      this.toolbar.appendChild(this.btnThumbs);
      this.btnThumbs.addEventListener('click', function () { self.toggleThumbnails(); });
    }

    // Download
    if (tb.download) {
      this.btnDownload = btn('download', 'Scarica');
      this.toolbar.appendChild(this.btnDownload);
      this.btnDownload.addEventListener('click', function () { self.downloadPdf(); });
    }

    // Print
    if (tb.print) {
      this.btnPrint = btn('print', 'Stampa');
      this.toolbar.appendChild(this.btnPrint);
      this.btnPrint.addEventListener('click', function () { self.printPdf(); });
    }

    // Fullscreen
    if (tb.fullscreen) {
      this.btnFullscreen = btn('fullscreen', 'Schermo intero');
      this.toolbar.appendChild(this.btnFullscreen);
      this.btnFullscreen.addEventListener('click', function () { self.toggleFullscreen(); });
    }

    this.root.appendChild(this.toolbar);
  };

  /* ───── Search bar ───── */
  OloPdfPro.prototype._buildSearchBar = function (theme) {
    var self = this;
    this.searchBar = el('div', 'olo-pdfv-search olo-pdfv-' + theme);

    this.searchInput = el('input', '');
    this.searchInput.type = 'text';
    this.searchInput.placeholder = 'Cerca nel documento...';
    this.searchCount = el('span', 'olo-pdfv-search-count', '');
    var closeBtn = btn('close', 'Chiudi');

    this.searchBar.appendChild(this.searchInput);
    this.searchBar.appendChild(this.searchCount);
    this.searchBar.appendChild(closeBtn);

    closeBtn.addEventListener('click', function () { self.toggleSearch(); });
    this.searchInput.addEventListener('input', function () {
      // Simple text search - placeholder for future enhancement
      self.searchCount.textContent = '';
    });

    this.root.appendChild(this.searchBar);
  };

  /* ───── Init viewing mode ───── */
  OloPdfPro.prototype._initMode = function () {
    var mode = this.config.mode;
    // Calculate initial scale
    this._calcInitialScale();

    if (mode === 'flipbook') {
      this._initFlipbook();
    } else if (mode === 'scroll') {
      this._initScroll();
    } else if (mode === 'double') {
      this._initDoublePage();
    } else {
      this._initSinglePage();
    }
  };

  /* ───── Calculate initial scale ───── */
  OloPdfPro.prototype._calcInitialScale = function () {
    var self = this;
    var zoom = this.config.zoom;
    var num = parseFloat(zoom);

    if (!isNaN(num) && num > 0) {
      this.scale = num / 100;
      return;
    }

    // fit-width / fit-page calculated after first page
    // store intent, resolve lazily
    this._zoomIntent = zoom;
    this.scale = 1;
  };

  OloPdfPro.prototype._resolveZoomIntent = function (viewport) {
    if (!this._zoomIntent) return;
    var areaW = this.canvasWrap.clientWidth;
    var areaH = this.canvasWrap.clientHeight;
    if (areaW <= 0) return;

    if (this._zoomIntent === 'fit-width') {
      this.scale = (areaW - 32) / viewport.width;
    } else if (this._zoomIntent === 'fit-page') {
      var sw = (areaW - 32) / viewport.width;
      var sh = (areaH - 16) / viewport.height;
      this.scale = Math.min(sw, sh);
    }
    this._zoomIntent = null;
    this._updateZoomLabel();
  };

  /* ───── Single page mode ───── */
  OloPdfPro.prototype._initSinglePage = function () {
    var self = this;
    this.pagesEl = el('div', 'olo-pdfv-pages');
    this.canvasWrap.appendChild(this.pagesEl);

    this.mainCanvas = document.createElement('canvas');
    this.pagesEl.appendChild(this.mainCanvas);

    this._renderCurrentPage();
  };

  OloPdfPro.prototype._renderCurrentPage = function () {
    var self = this;
    this.pdfDoc.getPage(this.currentPage).then(function (page) {
      var baseViewport = page.getViewport({ scale: 1 });

      // Resolve zoom intent on first render
      if (self._zoomIntent) {
        self._resolveZoomIntent(baseViewport);
      }

      renderHiDPI(page, self.mainCanvas, self.scale).then(function () {
        self._renderHotspotsForCurrentView();
      });
      self._updatePageInfo();
      self._updateZoomLabel();
    });
  };

  /* ───── Double page mode ───── */
  OloPdfPro.prototype._initDoublePage = function () {
    var self = this;
    this.pagesEl = el('div', 'olo-pdfv-pages olo-pdfv-double');
    this.canvasWrap.appendChild(this.pagesEl);

    this.leftCanvas = document.createElement('canvas');
    this.rightCanvas = document.createElement('canvas');
    this.pagesEl.appendChild(this.leftCanvas);
    this.pagesEl.appendChild(this.rightCanvas);

    // Show even-odd spread
    if (this.currentPage % 2 === 0) this.currentPage = Math.max(1, this.currentPage - 1);

    this._renderDoubleSpread();
  };

  OloPdfPro.prototype._renderDoubleSpread = function () {
    var self = this;
    var left = this.currentPage;
    var right = left + 1;

    this._renderPageToCanvas(left, this.leftCanvas);
    if (right <= this.numPages) {
      this.rightCanvas.style.display = 'block';
      this._renderPageToCanvas(right, this.rightCanvas);
    } else {
      this.rightCanvas.style.display = 'none';
    }
    this._updatePageInfo();
  };

  OloPdfPro.prototype._renderPageToCanvas = function (pageNum, canvas) {
    var self = this;
    this.pdfDoc.getPage(pageNum).then(function (page) {
      var baseViewport = page.getViewport({ scale: 1 });

      if (self._zoomIntent) {
        // For double mode, fit width considers both pages
        var areaW = self.canvasWrap.clientWidth;
        var areaH = self.canvasWrap.clientHeight;
        if (self._zoomIntent === 'fit-width') {
          self.scale = (areaW - 40) / (baseViewport.width * 2 + 4);
        } else if (self._zoomIntent === 'fit-page') {
          var sw = (areaW - 40) / (baseViewport.width * 2 + 4);
          var sh = (areaH - 16) / baseViewport.height;
          self.scale = Math.min(sw, sh);
        }
        self._zoomIntent = null;
        self._updateZoomLabel();
      }

      renderHiDPI(page, canvas, self.scale).then(function () {
        self._renderHotspotsForCurrentView();
      });
    });
  };

  /* ───── Scroll mode ───── */
  OloPdfPro.prototype._initScroll = function () {
    var self = this;
    this.scrollEl = el('div', 'olo-pdfv-scroll');
    this.canvasWrap.appendChild(this.scrollEl);

    // Create placeholders for all pages
    this.scrollPages = [];

    this.pdfDoc.getPage(1).then(function (firstPage) {
      var baseViewport = firstPage.getViewport({ scale: 1 });

      if (self._zoomIntent) {
        self._resolveZoomIntent(baseViewport);
      }

      var viewport = firstPage.getViewport({ scale: self.scale });
      var pw = Math.round(viewport.width);
      var ph = Math.round(viewport.height);

      for (var i = 0; i < self.numPages; i++) {
        var placeholder = el('div', 'olo-pdfv-page-placeholder', 'Pagina ' + (i + 1));
        placeholder.style.width = pw + 'px';
        placeholder.style.height = ph + 'px';
        placeholder.dataset.pageNum = i + 1;
        self.scrollEl.appendChild(placeholder);
        self.scrollPages.push(placeholder);
      }

      // Lazy render with IntersectionObserver
      self._observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            var num = parseInt(entry.target.dataset.pageNum, 10);
            if (!self.renderedPages[num]) {
              self.renderedPages[num] = true;
              self._renderScrollPage(num, entry.target);
            }
          }
        });
      }, {
        root: self.canvasWrap,
        rootMargin: '200px 0px',
        threshold: 0.01,
      });

      self.scrollPages.forEach(function (p) {
        self._observer.observe(p);
      });

      // Track current page on scroll
      self.canvasWrap.addEventListener('scroll', function () {
        self._updateScrollPageNum();
      });

      // Scroll to start page
      if (self.currentPage > 1 && self.scrollPages[self.currentPage - 1]) {
        self.scrollPages[self.currentPage - 1].scrollIntoView();
      }

      self._renderHotspotsForCurrentView();
    });
  };

  OloPdfPro.prototype._renderScrollPage = function (pageNum, placeholder) {
    var self = this;
    this.pdfDoc.getPage(pageNum).then(function (page) {
      var canvas = document.createElement('canvas');

      placeholder.textContent = '';
      placeholder.style.width = '';
      placeholder.style.height = '';
      placeholder.className = '';
      placeholder.appendChild(canvas);

      renderHiDPI(page, canvas, self.scale);
      self.pageCanvases[pageNum] = canvas;
    });
  };

  OloPdfPro.prototype._updateScrollPageNum = function () {
    var wrap = this.canvasWrap;
    var top = wrap.scrollTop + wrap.clientHeight / 3;
    for (var i = 0; i < this.scrollPages.length; i++) {
      var p = this.scrollPages[i];
      if (p.offsetTop + p.offsetHeight > top) {
        var newPage = i + 1;
        if (newPage !== this.currentPage) {
          this.currentPage = newPage;
          this._updatePageInfo();
        }
        break;
      }
    }
  };

  /* ───── Flipbook mode ───── */
  OloPdfPro.prototype._initFlipbook = function () {
    var self = this;
    this.flipWrap = el('div', 'olo-pdfv-flipbook-wrap');
    this.canvasWrap.appendChild(this.flipWrap);

    this.flipContainer = el('div', 'olo-pdfv-flipbook-container');
    this.flipWrap.appendChild(this.flipContainer);

    // We need to know page dimensions first
    this.pdfDoc.getPage(1).then(function (firstPage) {
      // Use requestAnimationFrame to ensure layout is computed
      requestAnimationFrame(function () { self._setupFlipbook(firstPage); });
    });
  };

  OloPdfPro.prototype._setupFlipbook = function (firstPage) {
    var self = this;
    var baseVp = firstPage.getViewport({ scale: 1 });

    // Get container dimensions — use parent container if wrap has no size
    var areaW = this.flipWrap.clientWidth || this.container.clientWidth || 800;
    var areaH = this.flipWrap.clientHeight || this.container.clientHeight - 50 || 500;

    // Calculate base fit scale (fills available area)
    var pageRatio = baseVp.width / baseVp.height;
    var targetH = Math.max(200, areaH - 20);
    var targetW = targetH * pageRatio;

    // If two pages would be wider than container, shrink
    if (targetW * 2 > areaW - 40) {
      targetW = Math.max(100, (areaW - 40) / 2);
      targetH = targetW / pageRatio;
    }

    // Store base fit scale for zoom reference
    if (!this._flipBaseScale) {
      this._flipBaseScale = targetH / baseVp.height;
      this.scale = this._flipBaseScale;
      this._updateZoomLabel();
    }

    // Apply zoom: ratio of current scale to base fit scale
    var zoomFactor = this.scale / this._flipBaseScale;
    targetW = targetW * zoomFactor;
    targetH = targetH * zoomFactor;

    // Clamp to reasonable bounds
    targetW = Math.max(80, targetW);
    targetH = Math.max(100, targetH);

    var flipW = Math.round(targetW);
    var flipH = Math.round(targetH);
    this._flipPageW = flipW;
    this._flipPageH = flipH;
    this._flipScale = flipH / baseVp.height;

    // Build page array with blank spacers for soft-bend cover layout.
    // StPageFlip forces "hard" density on pages adjacent to covers,
    // so we use blank pages + showCover:false to keep ALL flips soft.
    var allPages = [];
    this._flipPages = []; // only real PDF page divs
    this._flipBlankOffset = 1; // blank pages before first real page

    // Blank front spacer (inside front cover)
    var blankFront = el('div', 'olo-pdfv-flipbook-page');
    blankFront.style.width = flipW + 'px';
    blankFront.style.height = flipH + 'px';
    blankFront.dataset.density = 'soft';
    allPages.push(blankFront);
    this.flipContainer.appendChild(blankFront);

    // Real PDF pages — all soft
    for (var i = 0; i < this.numPages; i++) {
      var pageDiv = el('div', 'olo-pdfv-flipbook-page');
      pageDiv.style.width = flipW + 'px';
      pageDiv.style.height = flipH + 'px';
      pageDiv.dataset.pageNum = i + 1;
      pageDiv.dataset.density = 'soft';
      allPages.push(pageDiv);
      this._flipPages.push(pageDiv);
      this.flipContainer.appendChild(pageDiv);
    }

    // Blank back spacer if needed to make total even
    if (allPages.length % 2 !== 0) {
      var blankBack = el('div', 'olo-pdfv-flipbook-page');
      blankBack.style.width = flipW + 'px';
      blankBack.style.height = flipH + 'px';
      blankBack.dataset.density = 'soft';
      allPages.push(blankBack);
      this.flipContainer.appendChild(blankBack);
    }

    // Init PageFlip — showCover:false, blanks handle cover layout
    if (typeof St !== 'undefined' && St.PageFlip) {
      var self = this;
      var _nav = (this.config && this.config.nav) || {};
      this.pageFlip = new St.PageFlip(this.flipContainer, {
        width: flipW,
        height: flipH,
        size: 'fixed',
        drawShadow: true,
        flippingTime: 800,
        usePortrait: false,
        startZIndex: 0,
        autoSize: false,
        maxShadowOpacity: 0.5,
        showCover: false,
        mobileScrollSupport: false,
        clickEventForward: false,
        useMouseEvents: _nav.click !== false,
        swipeDistance: _nav.swipe !== false ? 30 : 10000,
        showPageCorners: _nav.click !== false,
      });

      this.pageFlip.loadFromHTML(allPages);

      this.pageFlip.on('flip', function (e) {
        // Convert PageFlip index to real PDF page number
        var pfIndex = e.data;
        var pdfPage = pfIndex - self._flipBlankOffset + 1;
        if (pdfPage < 1) pdfPage = 1;
        if (pdfPage > self.numPages) pdfPage = self.numPages;
        self.currentPage = pdfPage;
        self._updatePageInfo();
        self._renderFlipbookVisiblePages();
        self._renderHotspotsForCurrentView();
      });

      this.pageFlip.on('changeState', function (e) {
        var state = e.data;
        if (state === 'flipping' || state === 'user_fold') {
          self.flipContainer.classList.add('olo-pdfv-flipping');
          self._closePopup();
        } else {
          self.flipContainer.classList.remove('olo-pdfv-flipping');
        }
      });

      // Navigate to start page
      if (this.currentPage > 1) {
        this.pageFlip.turnToPage(this.currentPage - 1 + this._flipBlankOffset);
      }

      // Render initial visible pages
      this._renderFlipbookVisiblePages();
      this._renderHotspotsForCurrentView();

      // Bind middle-mouse pan (once per flipWrap)
      if (!this._flipPanBound) {
        this._flipPanBound = true;
        this._bindFlipbookPan();
      } else {
        this._resetPan();
      }
    }
  };

  OloPdfPro.prototype._renderFlipbookVisiblePages = function () {
    var self = this;
    // Render current page and neighbors
    var center = this.currentPage;
    var start = Math.max(1, center - 2);
    var end = Math.min(this.numPages, center + 3);

    for (var i = start; i <= end; i++) {
      if (!this.renderedPages[i]) {
        this.renderedPages[i] = true;
        this._renderFlipbookPage(i);
      }
    }
  };

  OloPdfPro.prototype._renderFlipbookPage = function (pageNum) {
    var self = this;
    var pageDiv = this._flipPages[pageNum - 1];
    if (!pageDiv) return;

    this.pdfDoc.getPage(pageNum).then(function (page) {
      var dpr = getDPR();
      var viewport = page.getViewport({ scale: self._flipScale * dpr });
      var canvas = document.createElement('canvas');
      var ctx = canvas.getContext('2d');
      canvas.width = Math.round(self._flipPageW * dpr);
      canvas.height = Math.round(self._flipPageH * dpr);
      canvas.style.width = '100%';
      canvas.style.height = '100%';

      // Remove only old canvas, preserve hotspot layers
      var oldCanvas = pageDiv.querySelector('canvas');
      if (oldCanvas) pageDiv.removeChild(oldCanvas);
      pageDiv.insertBefore(canvas, pageDiv.firstChild);

      page.render({ canvasContext: ctx, viewport: viewport });
    });
  };

  /* ───── Navigation ───── */
  OloPdfPro.prototype.goToPage = function (num) {
    if (num < 1 || num > this.numPages) return;
    this.currentPage = num;
    var mode = this.config.mode;

    if (mode === 'flipbook' && this.pageFlip) {
      this.pageFlip.turnToPage(num - 1 + (this._flipBlankOffset || 0));
    } else if (mode === 'scroll') {
      if (this.scrollPages[num - 1]) {
        this.scrollPages[num - 1].scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    } else if (mode === 'double') {
      if (num % 2 === 0) num = num - 1;
      this.currentPage = num;
      this._renderDoubleSpread();
    } else {
      this._renderCurrentPage();
    }
    this._updatePageInfo();
    this._updateThumbActive();
    this._renderHotspotsForCurrentView();
  };

  OloPdfPro.prototype.nextPage = function () {
    var mode = this.config.mode;
    if (mode === 'flipbook' && this.pageFlip) {
      this.pageFlip.flipNext();
    } else if (mode === 'double') {
      var next = this.currentPage + 2;
      if (next <= this.numPages) this.goToPage(next);
    } else {
      if (this.currentPage < this.numPages) this.goToPage(this.currentPage + 1);
    }
  };

  OloPdfPro.prototype.prevPage = function () {
    var mode = this.config.mode;
    if (mode === 'flipbook' && this.pageFlip) {
      this.pageFlip.flipPrev();
    } else if (mode === 'double') {
      var prev = this.currentPage - 2;
      if (prev >= 1) this.goToPage(prev);
    } else {
      if (this.currentPage > 1) this.goToPage(this.currentPage - 1);
    }
  };

  /* ───── Zoom ───── */
  OloPdfPro.prototype.zoomIn = function () {
    var idx = this._findZoomIndex();
    if (idx < ZOOM_LEVELS.length - 1) {
      this.setZoom(ZOOM_LEVELS[idx + 1]);
    }
  };

  OloPdfPro.prototype.zoomOut = function () {
    var idx = this._findZoomIndex();
    if (idx > 0) {
      this.setZoom(ZOOM_LEVELS[idx - 1]);
    }
  };

  OloPdfPro.prototype.setZoom = function (newScale) {
    this.scale = newScale;
    this._updateZoomLabel();
    this._rerender();
  };

  OloPdfPro.prototype._findZoomIndex = function () {
    var s = this.scale;
    for (var i = 0; i < ZOOM_LEVELS.length; i++) {
      if (ZOOM_LEVELS[i] >= s - 0.01) return i;
    }
    return ZOOM_LEVELS.length - 1;
  };

  OloPdfPro.prototype._rerender = function () {
    var mode = this.config.mode;
    if (mode === 'single') {
      this._renderCurrentPage();
    } else if (mode === 'double') {
      this._renderDoubleSpread();
    } else if (mode === 'scroll') {
      // Re-render all visible pages
      this.renderedPages = {};
      while (this.scrollEl.firstChild) this.scrollEl.removeChild(this.scrollEl.firstChild);
      this.scrollPages = [];
      if (this._observer) this._observer.disconnect();
      this._initScroll();
    }
    else if (mode === 'flipbook') {
      // Reinit flipbook at new scale — remove old flipWrap entirely
      this.renderedPages = {};
      this._flipPanBound = false; // rebind pan on new flipWrap
      if (this.pageFlip) {
        this.pageFlip.destroy();
        this.pageFlip = null;
      }
      if (this.flipWrap && this.flipWrap.parentNode) {
        this.flipWrap.parentNode.removeChild(this.flipWrap);
      }
      this._initFlipbook();
    }
    // Hotspots re-rendered inside each mode's async callback
  };

  /* ───── Fullscreen ───── */
  OloPdfPro.prototype.toggleFullscreen = function () {
    var self = this;
    var fsEl = document.fullscreenElement || document.webkitFullscreenElement;

    if (!fsEl) {
      // Enter fullscreen via native API
      var target = this.container;
      var req = target.requestFullscreen || target.webkitRequestFullscreen;
      if (req) {
        req.call(target).then(function () {
          self.isFullscreen = true;
          self.container.classList.add('olo-pdfv-fullscreen');
          if (self.btnFullscreen) self.btnFullscreen.innerHTML = ICONS.exitFullscreen;
          self._reinitFlipbookAfterResize();
        }).catch(function () {
          // Fallback: CSS-only fullscreen
          self._cssFullscreen(true);
        });
      } else {
        // No API support: CSS fallback
        self._cssFullscreen(true);
      }
    } else {
      // Exit fullscreen
      var exitFn = document.exitFullscreen || document.webkitExitFullscreen;
      if (exitFn) exitFn.call(document);
    }
  };

  // Listen for native fullscreen change (handles Esc key exit too)
  document.addEventListener('fullscreenchange', handleFsChange);
  document.addEventListener('webkitfullscreenchange', handleFsChange);
  function handleFsChange() {
    var fsEl = document.fullscreenElement || document.webkitFullscreenElement;
    // Find viewer that was fullscreened
    var viewers = document.querySelectorAll('[data-olo-pdfpro]');
    for (var i = 0; i < viewers.length; i++) {
      var inst = viewers[i]._oloPdfInst;
      if (!inst) continue;
      if (!fsEl && inst.isFullscreen) {
        // Exited fullscreen
        inst.isFullscreen = false;
        inst.container.classList.remove('olo-pdfv-fullscreen');
        if (inst.btnFullscreen) inst.btnFullscreen.innerHTML = ICONS.fullscreen;
        inst._reinitFlipbookAfterResize();
      }
    }
  }

  // CSS fallback for browsers without Fullscreen API
  OloPdfPro.prototype._cssFullscreen = function (enter) {
    this.isFullscreen = enter;
    if (enter) {
      this.container.classList.add('olo-pdfv-fullscreen');
      if (this.btnFullscreen) this.btnFullscreen.innerHTML = ICONS.exitFullscreen;
    } else {
      this.container.classList.remove('olo-pdfv-fullscreen');
      if (this.btnFullscreen) this.btnFullscreen.innerHTML = ICONS.fullscreen;
    }
    this._reinitFlipbookAfterResize();
  };

  OloPdfPro.prototype._reinitFlipbookAfterResize = function () {
    var self = this;
    setTimeout(function () {
      if (self.config.mode === 'flipbook') {
        self.renderedPages = {};
        self._flipBaseScale = null;
        self._flipPanBound = false;
        if (self.pageFlip) {
          self.pageFlip.destroy();
          self.pageFlip = null;
        }
        if (self.flipWrap && self.flipWrap.parentNode) {
          self.flipWrap.parentNode.removeChild(self.flipWrap);
        }
        self._initFlipbook();
        // Hotspots rendered inside _setupFlipbook callback
      }
    }, 100);
  };

  /* ───── Thumbnails ───── */
  OloPdfPro.prototype.toggleThumbnails = function () {
    if (!this.thumbsSidebar) return;
    this.thumbsVisible = !this.thumbsVisible;
    this.thumbsSidebar.classList.toggle('olo-pdfv-visible', this.thumbsVisible);
    if (this.btnThumbs) this.btnThumbs.classList.toggle('olo-pdfv-active', this.thumbsVisible);
  };

  OloPdfPro.prototype._buildThumbnails = function () {
    if (!this.thumbsSidebar) return;
    var self = this;
    var thumbScale = 0.15;

    for (var i = 1; i <= Math.min(this.numPages, 100); i++) {
      (function (num) {
        var wrap = el('div', 'olo-pdfv-thumb' + (num === self.currentPage ? ' olo-pdfv-active' : ''));
        wrap.dataset.thumbPage = num;
        var numLabel = el('div', 'olo-pdfv-thumb-num', '' + num);
        var canvas = document.createElement('canvas');
        wrap.appendChild(canvas);
        wrap.appendChild(numLabel);
        self.thumbsSidebar.appendChild(wrap);

        wrap.addEventListener('click', function () {
          self.goToPage(num);
        });

        // Render thumbnail
        self.pdfDoc.getPage(num).then(function (page) {
          var vp = page.getViewport({ scale: thumbScale });
          canvas.width = vp.width;
          canvas.height = vp.height;
          var ctx = canvas.getContext('2d');
          page.render({ canvasContext: ctx, viewport: vp });
        });
      })(i);
    }
  };

  OloPdfPro.prototype._updateThumbActive = function () {
    if (!this.thumbsSidebar) return;
    var thumbs = this.thumbsSidebar.querySelectorAll('.olo-pdfv-thumb');
    for (var i = 0; i < thumbs.length; i++) {
      var p = parseInt(thumbs[i].dataset.thumbPage, 10);
      thumbs[i].classList.toggle('olo-pdfv-active', p === this.currentPage);
    }
  };

  /* ───── Search toggle ───── */
  OloPdfPro.prototype.toggleSearch = function () {
    if (!this.searchBar) return;
    this.searchVisible = !this.searchVisible;
    this.searchBar.classList.toggle('olo-pdfv-visible', this.searchVisible);
    if (this.btnSearch) this.btnSearch.classList.toggle('olo-pdfv-active', this.searchVisible);
    if (this.searchVisible && this.searchInput) {
      this.searchInput.focus();
    }
  };

  /* ───── Download ───── */
  OloPdfPro.prototype.downloadPdf = function () {
    var a = document.createElement('a');
    a.href = this.config.url;
    a.download = this.config.url.split('/').pop().split('?')[0] || 'document.pdf';
    a.target = '_blank';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  };

  /* ───── Print ───── */
  OloPdfPro.prototype.printPdf = function () {
    var w = window.open(this.config.url, '_blank');
    if (w) {
      w.addEventListener('load', function () {
        w.print();
      });
    }
  };

  /* ───── Keyboard shortcuts ───── */
  OloPdfPro.prototype._bindKeys = function () {
    var self = this;
    this.container.setAttribute('tabindex', '0');

    this.container.addEventListener('keydown', function (e) {
      if (e.target.tagName === 'INPUT') return;

      switch (e.key) {
        case 'ArrowLeft':
          e.preventDefault();
          self.prevPage();
          break;
        case 'ArrowRight':
          e.preventDefault();
          self.nextPage();
          break;
        case '+':
        case '=':
          e.preventDefault();
          self.zoomIn();
          break;
        case '-':
          e.preventDefault();
          self.zoomOut();
          break;
        case 'f':
        case 'F':
          if (!e.ctrlKey && !e.metaKey) {
            e.preventDefault();
            self.toggleFullscreen();
          }
          break;
        case 'Escape':
          if (self._activePopup) {
            self._closePopup();
          } else if (self.isFullscreen) {
            self.toggleFullscreen();
          } else if (self.searchVisible) {
            self.toggleSearch();
          }
          break;
      }
    });
  };

  /* ───── UI helpers ───── */
  OloPdfPro.prototype._updatePageInfo = function () {
    if (this.pageInput) {
      this.pageInput.value = this.currentPage;
      this.pageInput.max = this.numPages;
    }
    if (this.pageTotal) {
      this.pageTotal.textContent = '/ ' + this.numPages;
    }
    if (this.bbInfo && this.numPages) {
      this.bbInfo.textContent = this.currentPage + ' / ' + this.numPages;
    }
    if (this.bbRange && this.numPages) {
      this.bbRange.max = this.numPages;
      this.bbRange.value = this.currentPage;
    }
    this._updateThumbActive();
  };

  OloPdfPro.prototype._updateZoomLabel = function () {
    if (this.zoomLabel) {
      this.zoomLabel.textContent = Math.round(this.scale * 100) + '%';
    }
  };

  /* ───── Bottom progress bar ───── */
  OloPdfPro.prototype._buildBottomBar = function () {
    var self = this;
    var bb = this.config.bottombar || { enabled: true, pages: true, zoom: false, fullscreen: false };
    if (!bb.enabled) return;
    // Se nessun controllo è abilitato, non costruire la barra
    if (!bb.pages && !bb.zoom && !bb.fullscreen) return;

    this.bottomBar = el('div', 'olo-pdfv-bottombar');

    if (bb.pages) {
      this.bbInfo = el('span', 'olo-pdfv-bb-info', '1 / -');
      this.bbRange = document.createElement('input');
      this.bbRange.type = 'range';
      this.bbRange.className = 'olo-pdfv-bb-range';
      this.bbRange.min = '1';
      this.bbRange.max = String(this.numPages || 1);
      this.bbRange.value = String(this.currentPage || 1);
      this.bbRange.step = '1';
      this.bottomBar.appendChild(this.bbInfo);
      this.bottomBar.appendChild(this.bbRange);
      this.bbRange.addEventListener('input', function () {
        var p = parseInt(this.value, 10);
        if (p >= 1 && p <= self.numPages) self.goToPage(p);
      });
    }

    if (bb.zoom) {
      this.bbZoomOut = document.createElement('button');
      this.bbZoomOut.className = 'olo-pdfv-bb-btn';
      this.bbZoomOut.title = 'Riduci';
      this.bbZoomOut.innerHTML = ICONS.zoomOut;
      this.bbZoomIn = document.createElement('button');
      this.bbZoomIn.className = 'olo-pdfv-bb-btn';
      this.bbZoomIn.title = 'Ingrandisci';
      this.bbZoomIn.innerHTML = ICONS.zoomIn;
      this.bottomBar.appendChild(this.bbZoomOut);
      this.bottomBar.appendChild(this.bbZoomIn);
      this.bbZoomOut.addEventListener('click', function () { self.zoomOut(); });
      this.bbZoomIn.addEventListener('click', function () { self.zoomIn(); });
    }

    if (bb.fullscreen) {
      this.bbFullscreen = document.createElement('button');
      this.bbFullscreen.className = 'olo-pdfv-bb-btn';
      this.bbFullscreen.title = 'Schermo intero';
      this.bbFullscreen.innerHTML = ICONS.fullscreen;
      this.bottomBar.appendChild(this.bbFullscreen);
      this.bbFullscreen.addEventListener('click', function () { self.toggleFullscreen(); });
    }

    this.root.appendChild(this.bottomBar);
  };

  OloPdfPro.prototype._hideLoading = function () {
    if (this.loadingEl && this.loadingEl.parentNode) {
      this.loadingEl.parentNode.removeChild(this.loadingEl);
    }
  };

  /* ───── Touch gestures for single/double page ───── */
  OloPdfPro.prototype._bindSwipe = function (target) {
    var self = this;
    var startX = 0;
    var startY = 0;
    var threshold = 50;

    target.addEventListener('touchstart', function (e) {
      startX = e.touches[0].clientX;
      startY = e.touches[0].clientY;
    }, { passive: true });

    target.addEventListener('touchend', function (e) {
      var dx = e.changedTouches[0].clientX - startX;
      var dy = e.changedTouches[0].clientY - startY;
      if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > threshold) {
        if (dx < 0) { self.nextPage(); }
        else { self.prevPage(); }
      }
    }, { passive: true });
  };

  /* ───── Click-to-turn for single/double page ───── */
  OloPdfPro.prototype._bindClickNav = function () {
    var self = this;
    if (!this.canvasWrap) return;
    this.canvasWrap.style.cursor = 'pointer';
    this.canvasWrap.addEventListener('click', function (e) {
      var t = e.target;
      if (t && t.closest && t.closest('input, button, a, [role="button"], .olo-pdfv-hotspot, .olo-pdfv-thumbnails')) return;
      var rect = self.canvasWrap.getBoundingClientRect();
      var x = e.clientX - rect.left;
      if (x < rect.width / 2) self.prevPage();
      else self.nextPage();
    });
  };

  /* ───── Middle-mouse pan for zoomed flipbook ───── */
  OloPdfPro.prototype._bindFlipbookPan = function () {
    var wrap = this.flipWrap;
    if (!wrap) return;
    var self = this;
    var panning = false;
    var startX, startY, origPanX, origPanY;

    // Init pan offsets
    this._panX = 0;
    this._panY = 0;

    // CAPTURE phase: intercept middle mouse BEFORE StPageFlip sees it
    wrap.addEventListener('mousedown', function (e) {
      if (e.button !== 1) return;
      e.preventDefault();
      e.stopImmediatePropagation();
      panning = true;
      startX = e.clientX;
      startY = e.clientY;
      origPanX = self._panX;
      origPanY = self._panY;
      wrap.classList.add('olo-pdfv-panning');
    }, true); // capture phase

    document.addEventListener('mousemove', function (e) {
      if (!panning) return;
      e.preventDefault();
      self._panX = origPanX + (e.clientX - startX);
      self._panY = origPanY + (e.clientY - startY);
      self._applyPan();
    });

    document.addEventListener('mouseup', function (e) {
      if (!panning) return;
      panning = false;
      wrap.classList.remove('olo-pdfv-panning');
    });

    // Block middle-click auto-scroll and auxclick
    wrap.addEventListener('auxclick', function (e) {
      if (e.button === 1) { e.preventDefault(); e.stopImmediatePropagation(); }
    }, true);
  };

  /* ───── Apply pan transform ───── */
  OloPdfPro.prototype._applyPan = function () {
    if (!this.flipContainer) return;
    if (this._panX === 0 && this._panY === 0) {
      this.flipContainer.style.transform = '';
    } else {
      this.flipContainer.style.transform = 'translate(' + this._panX + 'px,' + this._panY + 'px)';
    }
  };

  /* ───── Reset pan ───── */
  OloPdfPro.prototype._resetPan = function () {
    this._panX = 0;
    this._panY = 0;
    this._applyPan();
  };

  /* ═══════════════════════════════════════════════════
     Hotspot system
     ═══════════════════════════════════════════════════ */

  OloPdfPro.prototype._initHotspots = function () {
    var self = this;
    var hotspots = this.config.hotspots;
    if (!hotspots || !hotspots.length) return;

    // Group hotspots by page number
    this._hotspotsByPage = {};
    for (var i = 0; i < hotspots.length; i++) {
      var pg = hotspots[i].page || 1;
      if (!this._hotspotsByPage[pg]) this._hotspotsByPage[pg] = [];
      this._hotspotsByPage[pg].push(hotspots[i]);
    }

    // Click outside to close popup
    document.addEventListener('mousedown', function (e) {
      if (!self._activePopup) return;
      if (self._activePopup.contains(e.target)) return;
      // Check if click is on a hotspot dot
      if (e.target.classList.contains('olo-pdfv-hotspot')) return;
      self._closePopup();
    });

    // Initial render after a short delay (modes may still be setting up)
    setTimeout(function () {
      self._renderHotspotsForCurrentView();
    }, 300);
  };

  OloPdfPro.prototype._renderHotspotsForCurrentView = function () {
    if (!this.config.hotspots || !this.config.hotspots.length) return;
    this._closePopup();
    this._clearHotspotLayers();

    var mode = this.config.mode;
    if (mode === 'single') {
      this._renderHotspotsSingle();
    } else if (mode === 'double') {
      this._renderHotspotsDouble();
    } else if (mode === 'scroll') {
      this._renderHotspotsScroll();
    } else if (mode === 'flipbook') {
      this._renderHotspotsFlipbook();
    }
    // Trigger UIkit to process new uk-icon elements
    if (window.UIkit) { UIkit.update(this.container); }
  };

  OloPdfPro.prototype._clearHotspotLayers = function () {
    var layers = this.container.querySelectorAll('.olo-pdfv-hotspot-layer');
    for (var i = 0; i < layers.length; i++) {
      layers[i].parentNode.removeChild(layers[i]);
    }
  };

  OloPdfPro.prototype._createHotspotLayer = function (parent) {
    var layer = el('div', 'olo-pdfv-hotspot-layer');
    parent.appendChild(layer);
    return layer;
  };

  OloPdfPro.prototype._addHotspotDot = function (layer, hs) {
    var self = this;
    var c = this.config;
    var size = c.hotspotSize || 14;
    var color = c.hotspotColor || '#EF4444';

    var dotColor = (hs.color && hs.color !== '') ? hs.color : color;

    var hasIcon = hs.icon && hs.icon !== '';
    var dotSize = hasIcon ? Math.max(size, 24) : size;

    var dot = el('div', 'olo-pdfv-hotspot');
    dot.style.left = hs.x + '%';
    dot.style.top = hs.y + '%';
    dot.style.width = dotSize + 'px';
    dot.style.height = dotSize + 'px';
    dot.style.backgroundColor = dotColor;
    dot.title = hs.title || '';

    if (hasIcon) {
      var iconRatio = Math.round((dotSize * 0.6) / 20 * 100) / 100;
      var iconSpan = document.createElement('span');
      iconSpan.setAttribute('uk-icon', 'icon: ' + hs.icon + '; ratio: ' + iconRatio);
      iconSpan.style.cssText = 'color:#fff;display:flex;align-items:center;justify-content:center;width:100%;height:100%;line-height:1;';
      dot.appendChild(iconSpan);
    }

    if (c.hotspotPulse) {
      var pulse = el('div', 'olo-pdfv-hotspot-pulse');
      pulse.style.color = dotColor;
      dot.appendChild(pulse);
    }

    // Block mousedown/touchstart so StPageFlip doesn't start a flip
    dot.addEventListener('mousedown', function (e) {
      e.stopPropagation();
      e.stopImmediatePropagation();
    }, true);
    dot.addEventListener('touchstart', function (e) {
      e.stopPropagation();
      e.stopImmediatePropagation();
    }, true);
    dot.addEventListener('click', function (e) {
      e.stopPropagation();
      e.stopImmediatePropagation();
      self._togglePopup(hs, dot, layer);
    });

    layer.appendChild(dot);
    return dot;
  };

  /* ── Single mode hotspots ── */
  OloPdfPro.prototype._renderHotspotsSingle = function () {
    if (!this.pagesEl) return;
    // pagesEl contains mainCanvas — make pagesEl position:relative
    this.pagesEl.style.position = 'relative';
    var hsArr = this._hotspotsByPage[this.currentPage];
    if (!hsArr || !hsArr.length) return;

    var layer = this._createHotspotLayer(this.pagesEl);
    // Size layer to match the canvas
    layer.style.width = this.mainCanvas.style.width;
    layer.style.height = this.mainCanvas.style.height;
    layer.style.position = 'absolute';
    layer.style.left = this.mainCanvas.offsetLeft + 'px';
    layer.style.top = this.mainCanvas.offsetTop + 'px';

    for (var i = 0; i < hsArr.length; i++) {
      this._addHotspotDot(layer, hsArr[i]);
    }
  };

  /* ── Double mode hotspots ── */
  OloPdfPro.prototype._renderHotspotsDouble = function () {
    if (!this.pagesEl) return;

    var canvases = [
      { canvas: this.leftCanvas, page: this.currentPage },
      { canvas: this.rightCanvas, page: this.currentPage + 1 },
    ];

    for (var c = 0; c < canvases.length; c++) {
      var info = canvases[c];
      if (info.page > this.numPages) continue;
      if (info.canvas.style.display === 'none') continue;
      var hsArr = this._hotspotsByPage[info.page];
      if (!hsArr || !hsArr.length) continue;

      // Wrap canvas in position:relative container if not already
      var wrap = info.canvas.parentNode;
      if (!wrap.classList.contains('olo-pdfv-page-wrap')) {
        wrap = el('div', 'olo-pdfv-page-wrap');
        info.canvas.parentNode.insertBefore(wrap, info.canvas);
        wrap.appendChild(info.canvas);
      }

      var layer = this._createHotspotLayer(wrap);
      for (var i = 0; i < hsArr.length; i++) {
        this._addHotspotDot(layer, hsArr[i]);
      }
    }
  };

  /* ── Scroll mode hotspots ── */
  OloPdfPro.prototype._renderHotspotsScroll = function () {
    if (!this.scrollPages) return;

    for (var p = 0; p < this.scrollPages.length; p++) {
      var pageNum = p + 1;
      var hsArr = this._hotspotsByPage[pageNum];
      if (!hsArr || !hsArr.length) continue;

      var container = this.scrollPages[p];
      container.style.position = 'relative';

      var layer = this._createHotspotLayer(container);
      for (var i = 0; i < hsArr.length; i++) {
        this._addHotspotDot(layer, hsArr[i]);
      }
    }
  };

  /* ── Flipbook mode hotspots ── */
  OloPdfPro.prototype._renderHotspotsFlipbook = function () {
    if (!this._flipPages) return;

    for (var p = 0; p < this._flipPages.length; p++) {
      var pageNum = p + 1;
      var hsArr = this._hotspotsByPage[pageNum];
      if (!hsArr || !hsArr.length) continue;

      var pageDiv = this._flipPages[p];
      pageDiv.style.position = 'relative';

      var layer = this._createHotspotLayer(pageDiv);
      for (var i = 0; i < hsArr.length; i++) {
        this._addHotspotDot(layer, hsArr[i]);
      }
    }
  };

  /* ── Popup methods ── */
  OloPdfPro.prototype._togglePopup = function (hs, dot, layer) {
    if (this._activePopupHs === hs) {
      this._closePopup();
    } else {
      this._closePopup();
      this._showPopup(hs, dot, layer);
    }
  };

  OloPdfPro.prototype._showPopup = function (hs, dot, layer) {
    var self = this;

    var popup = el('div', 'olo-pdfv-popup');
    var arrow = el('div', 'olo-pdfv-popup-arrow');
    popup.appendChild(arrow);

    // Close button
    var closeBtn = el('button', 'olo-pdfv-popup-close', '\u00d7');
    closeBtn.type = 'button';
    closeBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      self._closePopup();
    });
    popup.appendChild(closeBtn);

    // Image
    if (hs.image_url) {
      var img = document.createElement('img');
      img.className = 'olo-pdfv-popup-img';
      img.src = hs.image_url;
      img.alt = hs.title || '';
      popup.appendChild(img);
    }

    // Video
    var videoInfo = parseVideoUrl(hs.video_url);
    if (videoInfo) {
      var videoWrap = el('div', 'olo-pdfv-popup-video');
      if (videoInfo.type === 'iframe') {
        var iframe = document.createElement('iframe');
        iframe.src = videoInfo.src;
        iframe.setAttribute('allowfullscreen', '');
        iframe.setAttribute('allow', 'autoplay; encrypted-media');
        videoWrap.appendChild(iframe);
      } else {
        var video = document.createElement('video');
        video.src = videoInfo.src;
        video.controls = true;
        video.autoplay = true;
        video.muted = true;
        videoWrap.appendChild(video);
      }
      popup.appendChild(videoWrap);
    }

    // Body: title + description + button
    var body = el('div', 'olo-pdfv-popup-body');

    if (hs.title) {
      var title = el('h4', '', '');
      if (hs.icon) {
        var popupIcon = document.createElement('span');
        popupIcon.setAttribute('uk-icon', 'icon: ' + hs.icon + '; ratio: 0.9');
        popupIcon.style.cssText = 'margin-right:6px;vertical-align:-2px;display:inline-block;';
        title.appendChild(popupIcon);
      }
      title.appendChild(document.createTextNode(hs.title));
      body.appendChild(title);
    }

    if (hs.description) {
      var desc = el('p', '');
      desc.innerHTML = hs.description;
      body.appendChild(desc);
    }

    if (hs.btn_label && hs.btn_url) {
      var btnWrap = el('div', '');
      var isStretch = hs.btn_align === 'stretch';
      if (hs.btn_align === 'center') btnWrap.style.textAlign = 'center';
      else if (hs.btn_align === 'right') btnWrap.style.textAlign = 'right';

      var link = document.createElement('a');
      link.className = 'olo-pdfv-popup-btn';
      link.href = hs.btn_url;
      link.textContent = hs.btn_label;
      if (hs.btn_target) link.target = '_blank';
      link.rel = 'noopener';

      // Tipografia
      if (hs.btn_font_size) link.style.fontSize = hs.btn_font_size + 'px';
      if (hs.btn_font_weight) link.style.fontWeight = hs.btn_font_weight;
      if (hs.btn_letter_spacing) link.style.letterSpacing = hs.btn_letter_spacing + 'px';
      if (hs.btn_text_transform) link.style.textTransform = hs.btn_text_transform;

      // Colori
      var bgColor = hs.btn_bg || '#4f46e5';
      link.style.background = bgColor;
      if (hs.btn_color) link.style.color = hs.btn_color;

      // Padding
      var pv = hs.btn_padding_v || 6;
      var ph = hs.btn_padding_h || 14;
      link.style.padding = pv + 'px ' + ph + 'px';

      // Bordo arrotondato
      if (hs.btn_radius && hs.btn_radius !== '0px') {
        link.style.borderRadius = hs.btn_radius;
      }

      // Bordo
      var bw = hs.btn_border_width || 0;
      if (bw > 0) {
        var bs = hs.btn_border_style || 'solid';
        var bc = hs.btn_border_color || '#000000';
        link.style.border = bw + 'px ' + bs + ' ' + bc;
      } else {
        link.style.border = 'none';
      }

      // Larghezza piena
      if (isStretch) {
        link.style.display = 'block';
        link.style.textAlign = 'center';
        link.style.width = '100%';
        link.style.boxSizing = 'border-box';
      }

      // Hover scurisce sfondo
      (function (l, bg) {
        l.addEventListener('mouseenter', function () {
          l.style.filter = 'brightness(0.88)';
        });
        l.addEventListener('mouseleave', function () {
          l.style.filter = '';
        });
      })(link, bgColor);

      btnWrap.appendChild(link);
      body.appendChild(btnWrap);
    }

    popup.appendChild(body);

    // Append popup to canvasWrap (stays inside fullscreen container)
    this.canvasWrap.style.position = 'relative';
    this.canvasWrap.appendChild(popup);

    this._activePopup = popup;
    this._activePopupHs = hs;

    // Position popup relative to canvasWrap
    this._positionPopup(popup, arrow, dot);
  };

  OloPdfPro.prototype._positionPopup = function (popup, arrow, dot) {
    var wrapRect = this.canvasWrap.getBoundingClientRect();
    var dotRect = dot.getBoundingClientRect();
    var popupW = popup.offsetWidth;
    var popupH = popup.offsetHeight;

    // Dot center relative to canvasWrap + scroll offset
    var dotCX = dotRect.left + dotRect.width / 2 - wrapRect.left + this.canvasWrap.scrollLeft;
    var dotTY = dotRect.top - wrapRect.top + this.canvasWrap.scrollTop;
    var dotBY = dotTY + dotRect.height;

    var gap = 10;
    var arrowSize = 6;

    // Try positioning above
    var top = dotTY - popupH - gap - arrowSize;
    var above = true;
    if (top < this.canvasWrap.scrollTop + 10) {
      // Not enough space above, position below
      top = dotBY + gap + arrowSize;
      above = false;
    }

    // Horizontal: center on dot, clamp to canvasWrap bounds
    var left = dotCX - popupW / 2;
    var minLeft = this.canvasWrap.scrollLeft + 4;
    var maxLeft = this.canvasWrap.scrollLeft + wrapRect.width - popupW - 4;
    if (left < minLeft) left = minLeft;
    if (left > maxLeft) left = maxLeft;

    popup.style.position = 'absolute';
    popup.style.top = Math.round(top) + 'px';
    popup.style.left = Math.round(left) + 'px';

    // Arrow position
    var arrowLeft = dotCX - left - arrowSize;
    arrowLeft = Math.max(12, Math.min(popupW - 24, arrowLeft));
    arrow.style.left = Math.round(arrowLeft) + 'px';

    if (above) {
      arrow.style.bottom = '-6px';
      arrow.style.top = '';
      arrow.style.boxShadow = '2px 2px 4px rgba(0,0,0,.08)';
    } else {
      arrow.style.top = '-6px';
      arrow.style.bottom = '';
      arrow.style.boxShadow = '-2px -2px 4px rgba(0,0,0,.08)';
    }
  };

  OloPdfPro.prototype._closePopup = function () {
    if (this._activePopup) {
      // Stop video/iframe
      var iframes = this._activePopup.querySelectorAll('iframe');
      for (var i = 0; i < iframes.length; i++) {
        iframes[i].src = '';
      }
      var videos = this._activePopup.querySelectorAll('video');
      for (var v = 0; v < videos.length; v++) {
        videos[v].pause();
        videos[v].src = '';
      }
      if (this._activePopup.parentNode) {
        this._activePopup.parentNode.removeChild(this._activePopup);
      }
      this._activePopup = null;
      this._activePopupHs = null;
    }
  };

  /* ───── Auto-init ───── */
  function initAll() {
    var viewers = document.querySelectorAll('[data-olo-pdfpro]');
    for (var i = 0; i < viewers.length; i++) {
      if (viewers[i]._oloPdfInst) continue; // già inizializzato
      try {
        var config = JSON.parse(viewers[i].getAttribute('data-olo-pdfpro'));
        var viewer = new OloPdfPro(viewers[i], config);
        viewers[i]._oloPdfInst = viewer;

        var nav = config.nav || {};
        if (nav.swipe !== false && config.mode !== 'flipbook') {
          viewer._bindSwipe(viewers[i]);
        }
        if (nav.click !== false && config.mode !== 'flipbook' && config.mode !== 'scroll') {
          viewer._bindClickNav();
        }
      } catch (err) {
        console.error('OloPdfPro init error:', err);
      }
    }
  }

  // Esporta per chiamate manuali (es. re-init dopo render dinamico nell'iframe builder)
  window.OloPdfPro = { initAll: initAll };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

})();
