/**
 * Olobuild Viewer 360° — lightweight equirectangular panorama viewer
 * Uses Pannellum (loaded from CDN) for WebGL rendering
 */
(function() {
  'use strict';

  // Resolve base URL from script src
  var _scripts = document.querySelectorAll('script[src*="olo-viewer360"]');
  var _baseUrl = '';
  if (_scripts.length) {
    _baseUrl = _scripts[_scripts.length - 1].src.replace(/assets\/js\/olo-viewer360\.js.*$/, '');
  }
  var PANNELLUM_CSS = _baseUrl + 'assets/vendor/pannellum/pannellum.css';
  var PANNELLUM_JS  = _baseUrl + 'assets/vendor/pannellum/pannellum.js';
  var loaded = false;
  var queue = [];

  function loadPannellum(callback) {
    if (loaded) { callback(); return; }
    if (window.pannellum) { loaded = true; callback(); return; }

    // Load CSS
    if (!document.querySelector('link[href*="pannellum"]')) {
      var css = document.createElement('link');
      css.rel = 'stylesheet';
      css.href = PANNELLUM_CSS;
      document.head.appendChild(css);
    }

    // Load JS
    queue.push(callback);
    if (queue.length > 1) return; // already loading

    var sc = document.createElement('script');
    sc.src = PANNELLUM_JS;
    sc.onload = function() {
      loaded = true;
      console.log('[olo-v360] Pannellum loaded');
      queue.forEach(function(cb) { cb(); });
      queue = [];
    };
    sc.onerror = function() {
      console.error('[olo-v360] Failed to load Pannellum from CDN');
      // Show error in all waiting containers
      document.querySelectorAll('[data-olo-v360]').forEach(function(el) {
        el.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#EF4444;font-size:14px;flex-direction:column;gap:8px"><p>Errore caricamento libreria 360°</p><p style="font-size:11px;color:#999">CDN non raggiungibile</p></div>';
      });
    };
    document.head.appendChild(sc);

    // Timeout fallback — if CDN doesn't respond in 10s
    setTimeout(function() {
      if (!loaded) {
        console.warn('[olo-v360] Pannellum load timeout');
        document.querySelectorAll('[data-olo-v360]').forEach(function(el) {
          if (el.querySelector('.pnlm-container')) return; // already initialized
          el.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#EF4444;font-size:14px"><p>Timeout caricamento 360° — ricarica la pagina</p></div>';
        });
      }
    }, 10000);
  }

  function initViewer(container) {
    if (container.__oloV360) return;
    container.__oloV360 = true;

    var config;
    try { config = JSON.parse(container.getAttribute('data-olo-v360')); } catch(e) { return; }
    if (!config.src) return;

    loadPannellum(function() {
      // Clear loading placeholder
      container.innerHTML = '';

      var opts = {
        type: config.type === 'video' ? 'equirectangular' : 'equirectangular',
        autoLoad: true,
        showZoomCtrl: config.zoomBtns !== false,
        showFullscreenCtrl: config.fullscreen !== false,
        compass: config.compass || false,
        mouseZoom: config.zoom !== false,
        draggable: config.drag !== false,
        touchPanSpeedCoeffFactor: config.touch !== false ? 1 : 0,
        hfov: config.fov || 50,
        minHfov: config.minFov || 20,
        maxHfov: config.maxFov || 80,
        yaw: config.yaw || 0,
        pitch: config.pitch || 0,
      };

      if (config.type === 'video') {
        // Video 360
        var video = document.createElement('video');
        video.src = config.src;
        video.crossOrigin = 'anonymous';
        video.loop = true;
        video.muted = true;
        video.playsInline = true;
        video.autoplay = true;
        opts.type = 'equirectangular';
        opts.panorama = config.src;
        // Pannellum doesn't natively support video, use image fallback
        // For video 360, we use a simpler canvas approach
        initVideoViewer(container, config, video);
        return;
      }

      // Image 360
      opts.panorama = config.src;

      if (config.autorotate) {
        opts.autoRotate = parseFloat(config.arSpeed) || 1;
        opts.autoRotateInactivityDelay = 2000;
      }

      if (!config.controls) {
        opts.showZoomCtrl = false;
        opts.showFullscreenCtrl = false;
      }

      try {
        var viewerId = container.id || ('olo-v360-' + Math.random().toString(36).substr(2, 6));
        if (!container.id) container.id = viewerId;
        console.log('[olo-v360] init', viewerId, opts.panorama);
        pannellum.viewer(viewerId, opts);
      } catch(e) {
        console.error('[olo-v360] error', e);
        container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#EF4444;font-size:14px"><p>Errore: ' + (e.message || 'caricamento panorama 360°') + '</p></div>';
      }
    });
  }

  // Simple video 360 viewer (canvas-based fallback)
  function initVideoViewer(container, config, video) {
    container.style.position = 'relative';
    container.style.overflow = 'hidden';
    container.style.cursor = 'grab';

    video.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block';
    container.appendChild(video);
    video.play().catch(function() {});

    // Simple drag-to-pan for video
    var startX = 0, startY = 0, offsetX = 0, offsetY = 0, dragging = false;

    container.addEventListener('mousedown', function(e) {
      dragging = true;
      startX = e.clientX - offsetX;
      startY = e.clientY - offsetY;
      container.style.cursor = 'grabbing';
    });
    document.addEventListener('mousemove', function(e) {
      if (!dragging) return;
      offsetX = e.clientX - startX;
      offsetY = e.clientY - startY;
      video.style.objectPosition = (50 - offsetX * 0.1) + '% ' + (50 - offsetY * 0.1) + '%';
    });
    document.addEventListener('mouseup', function() {
      dragging = false;
      container.style.cursor = 'grab';
    });

    // Fullscreen button
    if (config.fullscreen !== false) {
      var fsBtn = document.createElement('button');
      fsBtn.innerHTML = '⛶';
      fsBtn.style.cssText = 'position:absolute;bottom:10px;right:10px;background:rgba(0,0,0,0.6);color:#fff;border:none;border-radius:4px;width:32px;height:32px;cursor:pointer;font-size:18px;z-index:2';
      fsBtn.onclick = function() {
        if (container.requestFullscreen) container.requestFullscreen();
        else if (container.webkitRequestFullscreen) container.webkitRequestFullscreen();
      };
      container.appendChild(fsBtn);
    }
  }

  // Init all
  function initAll() {
    document.querySelectorAll('[data-olo-v360]').forEach(initViewer);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  // Expose for builder iframe reinit
  window.__oloV360Init = function() {
    document.querySelectorAll('[data-olo-v360]').forEach(function(el) {
      el.__oloV360 = false;
      el.innerHTML = '';
      initViewer(el);
    });
  };
})();
