/**
 * Olobuild PDF Viewer — Runtime frontend
 * Depends on: pdfjsLib (pdf.min.js), St.PageFlip (page-flip.browser.js)
 */
(function () {
  'use strict';
  if (window._oloPdfViewer) return;
  window._oloPdfViewer = 1;

  /* CSS is now loaded externally via olo-pdfviewer.css (enqueued in PHP) */

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

  /* ═══════════════════════════════════════════════════
     OloPdfViewer class
     ═══════════════════════════════════════════════════ */
  function OloPdfViewer(container, config) {
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

    this._init();
  }

  OloPdfViewer.prototype._init = function () {
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

    // Setup PDF.js worker
    if (typeof pdfjsLib !== 'undefined') {
      var workerUrl = (window.oloPdfViewerData || {}).workerUrl || '';
      if (workerUrl) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = workerUrl;
      }
    }

    // Load PDF
    this._loadPdf().then(function () {
      self._hideLoading();
      self._initMode();
      self._bindKeys();
      self._buildThumbnails();
    }).catch(function (err) {
      self._hideLoading();
      self.root.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#999;font-size:14px;">Impossibile caricare il PDF</div>';
      console.error('OloPdfViewer:', err);
    });
  };

  /* ───── Load PDF ───── */
  OloPdfViewer.prototype._loadPdf = function () {
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
  OloPdfViewer.prototype._buildToolbar = function (theme) {
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
  OloPdfViewer.prototype._buildSearchBar = function (theme) {
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
  OloPdfViewer.prototype._initMode = function () {
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
  OloPdfViewer.prototype._calcInitialScale = function () {
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

  OloPdfViewer.prototype._resolveZoomIntent = function (viewport) {
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
  OloPdfViewer.prototype._initSinglePage = function () {
    var self = this;
    this.pagesEl = el('div', 'olo-pdfv-pages');
    this.canvasWrap.appendChild(this.pagesEl);

    this.mainCanvas = document.createElement('canvas');
    this.pagesEl.appendChild(this.mainCanvas);

    this._renderCurrentPage();
  };

  OloPdfViewer.prototype._renderCurrentPage = function () {
    var self = this;
    this.pdfDoc.getPage(this.currentPage).then(function (page) {
      var baseViewport = page.getViewport({ scale: 1 });

      // Resolve zoom intent on first render
      if (self._zoomIntent) {
        self._resolveZoomIntent(baseViewport);
      }

      renderHiDPI(page, self.mainCanvas, self.scale);
      self._updatePageInfo();
      self._updateZoomLabel();
    });
  };

  /* ───── Double page mode ───── */
  OloPdfViewer.prototype._initDoublePage = function () {
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

  OloPdfViewer.prototype._renderDoubleSpread = function () {
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

  OloPdfViewer.prototype._renderPageToCanvas = function (pageNum, canvas) {
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

      renderHiDPI(page, canvas, self.scale);
    });
  };

  /* ───── Scroll mode ───── */
  OloPdfViewer.prototype._initScroll = function () {
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
    });
  };

  OloPdfViewer.prototype._renderScrollPage = function (pageNum, placeholder) {
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

  OloPdfViewer.prototype._updateScrollPageNum = function () {
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
  OloPdfViewer.prototype._initFlipbook = function () {
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

  OloPdfViewer.prototype._setupFlipbook = function (firstPage) {
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
        useMouseEvents: true,
        swipeDistance: 30,
        showPageCorners: true,
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
      });

      // Navigate to start page
      if (this.currentPage > 1) {
        this.pageFlip.turnToPage(this.currentPage - 1 + this._flipBlankOffset);
      }

      // Render initial visible pages
      this._renderFlipbookVisiblePages();

      // Bind middle-mouse pan (once per flipWrap)
      if (!this._flipPanBound) {
        this._flipPanBound = true;
        this._bindFlipbookPan();
      } else {
        this._resetPan();
      }
    }
  };

  OloPdfViewer.prototype._renderFlipbookVisiblePages = function () {
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

  OloPdfViewer.prototype._renderFlipbookPage = function (pageNum) {
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

      pageDiv.innerHTML = '';
      pageDiv.appendChild(canvas);

      page.render({ canvasContext: ctx, viewport: viewport });
    });
  };

  /* ───── Navigation ───── */
  OloPdfViewer.prototype.goToPage = function (num) {
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
  };

  OloPdfViewer.prototype.nextPage = function () {
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

  OloPdfViewer.prototype.prevPage = function () {
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
  OloPdfViewer.prototype.zoomIn = function () {
    var idx = this._findZoomIndex();
    if (idx < ZOOM_LEVELS.length - 1) {
      this.setZoom(ZOOM_LEVELS[idx + 1]);
    }
  };

  OloPdfViewer.prototype.zoomOut = function () {
    var idx = this._findZoomIndex();
    if (idx > 0) {
      this.setZoom(ZOOM_LEVELS[idx - 1]);
    }
  };

  OloPdfViewer.prototype.setZoom = function (newScale) {
    this.scale = newScale;
    this._updateZoomLabel();
    this._rerender();
  };

  OloPdfViewer.prototype._findZoomIndex = function () {
    var s = this.scale;
    for (var i = 0; i < ZOOM_LEVELS.length; i++) {
      if (ZOOM_LEVELS[i] >= s - 0.01) return i;
    }
    return ZOOM_LEVELS.length - 1;
  };

  OloPdfViewer.prototype._rerender = function () {
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
  };

  /* ───── Fullscreen ───── */
  OloPdfViewer.prototype.toggleFullscreen = function () {
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
    var viewers = document.querySelectorAll('[data-olo-pdfviewer]');
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
  OloPdfViewer.prototype._cssFullscreen = function (enter) {
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

  OloPdfViewer.prototype._reinitFlipbookAfterResize = function () {
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
      }
    }, 100);
  };

  /* ───── Thumbnails ───── */
  OloPdfViewer.prototype.toggleThumbnails = function () {
    if (!this.thumbsSidebar) return;
    this.thumbsVisible = !this.thumbsVisible;
    this.thumbsSidebar.classList.toggle('olo-pdfv-visible', this.thumbsVisible);
    if (this.btnThumbs) this.btnThumbs.classList.toggle('olo-pdfv-active', this.thumbsVisible);
  };

  OloPdfViewer.prototype._buildThumbnails = function () {
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

  OloPdfViewer.prototype._updateThumbActive = function () {
    if (!this.thumbsSidebar) return;
    var thumbs = this.thumbsSidebar.querySelectorAll('.olo-pdfv-thumb');
    for (var i = 0; i < thumbs.length; i++) {
      var p = parseInt(thumbs[i].dataset.thumbPage, 10);
      thumbs[i].classList.toggle('olo-pdfv-active', p === this.currentPage);
    }
  };

  /* ───── Search toggle ───── */
  OloPdfViewer.prototype.toggleSearch = function () {
    if (!this.searchBar) return;
    this.searchVisible = !this.searchVisible;
    this.searchBar.classList.toggle('olo-pdfv-visible', this.searchVisible);
    if (this.btnSearch) this.btnSearch.classList.toggle('olo-pdfv-active', this.searchVisible);
    if (this.searchVisible && this.searchInput) {
      this.searchInput.focus();
    }
  };

  /* ───── Download ───── */
  OloPdfViewer.prototype.downloadPdf = function () {
    var a = document.createElement('a');
    a.href = this.config.url;
    a.download = this.config.url.split('/').pop().split('?')[0] || 'document.pdf';
    a.target = '_blank';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  };

  /* ───── Print ───── */
  OloPdfViewer.prototype.printPdf = function () {
    var w = window.open(this.config.url, '_blank');
    if (w) {
      w.addEventListener('load', function () {
        w.print();
      });
    }
  };

  /* ───── Keyboard shortcuts ───── */
  OloPdfViewer.prototype._bindKeys = function () {
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
          if (self.isFullscreen) {
            self.toggleFullscreen();
          } else if (self.searchVisible) {
            self.toggleSearch();
          }
          break;
      }
    });
  };

  /* ───── UI helpers ───── */
  OloPdfViewer.prototype._updatePageInfo = function () {
    if (this.pageInput) {
      this.pageInput.value = this.currentPage;
      this.pageInput.max = this.numPages;
    }
    if (this.pageTotal) {
      this.pageTotal.textContent = '/ ' + this.numPages;
    }
    this._updateThumbActive();
  };

  OloPdfViewer.prototype._updateZoomLabel = function () {
    if (this.zoomLabel) {
      this.zoomLabel.textContent = Math.round(this.scale * 100) + '%';
    }
  };

  OloPdfViewer.prototype._hideLoading = function () {
    if (this.loadingEl && this.loadingEl.parentNode) {
      this.loadingEl.parentNode.removeChild(this.loadingEl);
    }
  };

  /* ───── Touch gestures for single/double page ───── */
  OloPdfViewer.prototype._bindSwipe = function (target) {
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

  /* ───── Middle-mouse pan for zoomed flipbook ───── */
  OloPdfViewer.prototype._bindFlipbookPan = function () {
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
  OloPdfViewer.prototype._applyPan = function () {
    if (!this.flipContainer) return;
    if (this._panX === 0 && this._panY === 0) {
      this.flipContainer.style.transform = '';
    } else {
      this.flipContainer.style.transform = 'translate(' + this._panX + 'px,' + this._panY + 'px)';
    }
  };

  /* ───── Reset pan ───── */
  OloPdfViewer.prototype._resetPan = function () {
    this._panX = 0;
    this._panY = 0;
    this._applyPan();
  };

  /* ───── Auto-init ───── */
  function initAll() {
    var viewers = document.querySelectorAll('[data-olo-pdfviewer]');
    for (var i = 0; i < viewers.length; i++) {
      try {
        var config = JSON.parse(viewers[i].getAttribute('data-olo-pdfviewer'));
        var viewer = new OloPdfViewer(viewers[i], config);
        viewers[i]._oloPdfInst = viewer; // ref for fullscreenchange listener

        // Bind swipe on non-flipbook modes (flipbook has built-in touch)
        if (config.mode !== 'flipbook') {
          viewer._bindSwipe(viewers[i]);
        }
      } catch (err) {
        console.error('OloPdfViewer init error:', err);
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

})();
