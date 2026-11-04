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

  // L'Oggetto girevole non usa Pannellum e il suo contenuto (foto, frecce, angolo) arriva dal PHP:
  // gli avvisi d'errore della libreria e il riavvio del builder non devono svuotarlo.
  function isObject(el) {
    try { return (JSON.parse(el.getAttribute('data-olo-v360')) || {}).mode === 'object'; } catch(e) { return false; }
  }

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
        if (isObject(el)) return;
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
          if (isObject(el)) return;
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

    // Object-spin (turntable) — non usa Pannellum
    if (config.mode === 'object') { initObjectViewer(container, config); return; }

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

      // Riquadro verticale (telefono): in Pannellum il campo visivo è ORIZZONTALE, e 95° in un riquadro alto diventano
      // ~120° in verticale (travi e pareti che si piegano). Si stringe finché il verticale non supera 100° (1.4.976).
      var box = container.getBoundingClientRect();
      if (box.width && box.height && box.width < box.height * 1.2) {
        var maxH = 2 * Math.atan(Math.tan(50 * Math.PI / 180) * box.width / box.height) * 180 / Math.PI;
        opts.hfov = Math.max(opts.minHfov, Math.min(opts.hfov, maxH));
      }

      // Col dito (1.4.977): Pannellum prende ogni tocco e la pagina non scorre più sopra il panorama (un 360° alto come
      // lo schermo era una trappola). Sui touch il trascinamento è nostro e solo in orizzontale: in verticale scorre la pagina.
      var tattile = config.touch !== false && !!(window.matchMedia && window.matchMedia('(pointer: coarse)').matches);
      if (tattile) opts.draggable = false;

      // «Giroscopio (mobile)»: girando il telefono si gira la vista (Pannellum chiede il permesso dove serve).
      if (config.gyro) opts.orientationOnByDefault = true;

      if (config.autorotate) {
        opts.autoRotate = parseFloat(config.arSpeed) || 1;
        opts.autoRotateInactivityDelay = 2000;
      }

      if (!config.controls) {
        opts.showZoomCtrl = false;
        opts.showFullscreenCtrl = false;
      }

      if (config.type === 'video') {
        var prendi = initVideo360(container, config, opts);
        if (tattile && prendi) trascinaOrizzontale(container, prendi, config);
        return;
      }

      // Image 360
      opts.panorama = config.src;

      try {
        var viewerId = container.id || ('olo-v360-' + Math.random().toString(36).substr(2, 6));
        if (!container.id) container.id = viewerId;
        console.log('[olo-v360] init', viewerId, opts.panorama);
        var pano = pannellum.viewer(viewerId, opts);
        if (tattile) trascinaOrizzontale(container, function() { return pano; }, config);
      } catch(e) {
        console.error('[olo-v360] error', e);
        container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#EF4444;font-size:14px"><p>Errore: ' + (e.message || 'caricamento panorama 360°') + '</p></div>';
      }
    });
  }

  // ── Video 360° ──────────────────────────────────────────────────────────────────────────────
  // Fino alla 1.4.974 il video si vedeva piatto (object-fit:cover, stirato), sempre muto e senza tocco.
  // Ora Pannellum lo disegna sulla sfera: col panorama «dynamic» la texture si aggiorna a ogni fotogramma.
  // Prima del play si vede la copertina (una foto 360° ferma, già girevole); al primo play il visore
  // rinasce sul video con la stessa vista. Sul telefono si usa il video leggero, se c'è: molti telefoni non
  // disegnano una texture più larga di 4096 px e un 8K resta nero.
  var ICONE = {
    play:    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l11-6.5z" fill="currentColor"/></svg>',
    pausa:   '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 5h3.5v14H7zM13.5 5H17v14h-3.5z" fill="currentColor"/></svg>',
    audio:   '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9.5h3.5L12 5.5v13l-4.5-4H4z"/><path d="M15.5 9a4 4 0 0 1 0 6M18 6.5a7.5 7.5 0 0 1 0 11"/></svg>',
    muto:    '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9.5h3.5L12 5.5v13l-4.5-4H4z"/><path d="M16 9.5l5 5M21 9.5l-5 5"/></svg>',
    schermo: '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg>',
    esci:    '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4v5H4M15 4v5h5M9 20v-5H4M15 20v-5h5"/></svg>'
  };

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function(c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function schermoPiccolo() {
    try {
      return window.matchMedia('(pointer: coarse)').matches || Math.max(screen.width, screen.height) <= 1024;
    } catch (e) { return false; }
  }

  function minuti(s) {
    s = Math.max(0, Math.floor(s || 0));
    return Math.floor(s / 60) + ':' + ('0' + (s % 60)).slice(-2);
  }

  // Trascinamento col dito solo in orizzontale (touch-action: pan-y): il gesto si decide nei primi 8 px, verticale = la
  // pagina scorre, orizzontale = la vista gira (setYaw). La rotazione automatica si ferma al tocco e riparte dopo 2 s.
  function trascinaOrizzontale(el, prendi, config) {
    el.style.touchAction = 'pan-y';
    var x0 = null, y0 = 0, yaw0 = 0, verso = 0, riparti = null;
    el.addEventListener('touchstart', function(e) {
      x0 = null;
      if (e.touches.length !== 1) return;
      if (e.target.closest && e.target.closest('.olo-v360-ui, .olo-v360-avvia, .pnlm-controls-container')) return;
      var v = prendi();
      if (!v) return;
      x0 = e.touches[0].clientX; y0 = e.touches[0].clientY; verso = 0;
      try { yaw0 = v.getYaw(); } catch (er) { x0 = null; }
    }, { passive: true });
    el.addEventListener('touchmove', function(e) {
      if (x0 === null) return;
      var t = e.touches[0], dx = t.clientX - x0, dy = t.clientY - y0;
      if (!verso && (Math.abs(dx) > 8 || Math.abs(dy) > 8)) {
        verso = Math.abs(dx) > Math.abs(dy) ? 1 : -1;
        if (verso === 1) {
          var v0 = prendi();
          try { if (v0) { clearTimeout(riparti); v0.stopAutoRotate(); } } catch (er) {}
        }
      }
      if (verso !== 1) return;
      var v = prendi();
      if (!v) return;
      var larg = el.getBoundingClientRect().width || 1;
      try { v.setYaw(yaw0 - dx / larg * v.getHfov(), false); } catch (er) {}
      if (e.cancelable) e.preventDefault();
    }, { passive: false });
    el.addEventListener('touchend', function() {
      if (verso === 1 && config.autorotate) {
        clearTimeout(riparti);
        riparti = setTimeout(function() {
          var v = prendi();
          try { if (v) v.startAutoRotate(parseFloat(config.arSpeed) || 1); } catch (er) {}
        }, 2000);
      }
      x0 = null;
    }, { passive: true });
  }

  function initVideo360(container, config, opts) {
    var L = config.labels || {};
    var conAudio = config.audio !== false;
    var calmo = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var autoplay = !!config.autoplay && !calmo;

    container.innerHTML = '';
    container.classList.add('olo-v360-video');
    // Video che parte da solo e si ripete (un'apertura, uno sfondo): niente tempo né avanzamento, solo pausa, audio e
    // schermo intero, raccolti in basso a destra (1.4.977).
    if (config.autoplay && config.loop !== false) container.classList.add('is-ambiente');

    var stage = document.createElement('div');
    stage.className = 'olo-v360-pano';
    container.appendChild(stage);

    // Il video resta nel DOM (invisibile): su iOS un video staccato a volte non decodifica i fotogrammi.
    var video = document.createElement('video');
    video.className = 'olo-v360-sorgente';
    video.crossOrigin = 'anonymous';
    video.playsInline = true;
    video.setAttribute('playsinline', '');
    video.setAttribute('webkit-playsinline', '');
    video.muted = true;
    video.loop = config.loop !== false;
    video.preload = autoplay ? 'auto' : 'metadata';
    video.src = (config.srcMobile && schermoPiccolo()) ? config.srcMobile : config.src;
    container.appendChild(video);

    var fsOk = config.controls !== false && config.fullscreen !== false &&
      !!(container.requestFullscreen || container.webkitRequestFullscreen);

    var ui = document.createElement('div');
    ui.className = 'olo-v360-ui';
    ui.innerHTML =
      '<button type="button" class="olo-v360-btn olo-v360-pp" aria-label="' + esc(L.play) + '">' + ICONE.play + '</button>' +
      '<div class="olo-v360-barra" role="slider" tabindex="0" aria-label="' + esc(L.seek) + '" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">' +
        '<span class="olo-v360-fatto"></span></div>' +
      '<span class="olo-v360-tempo">0:00</span>' +
      (conAudio ? '<button type="button" class="olo-v360-btn olo-v360-vol" aria-pressed="false" aria-label="' + esc(L.unmute) + '">' + ICONE.muto + '</button>' : '') +
      (fsOk ? '<button type="button" class="olo-v360-btn olo-v360-fs" aria-label="' + esc(L.fullscreen) + '">' + ICONE.schermo + '</button>' : '');
    container.appendChild(ui);

    var avvia = document.createElement('button');
    avvia.type = 'button';
    avvia.className = 'olo-v360-avvia';
    avvia.innerHTML = '<span class="olo-v360-avvia-cerchio">' + ICONE.play + '</span><span class="olo-v360-avvia-testo">' + esc(L.start) + '</span>';
    container.appendChild(avvia);

    var pp = ui.querySelector('.olo-v360-pp');
    var barra = ui.querySelector('.olo-v360-barra');
    var fatto = ui.querySelector('.olo-v360-fatto');
    var tempoEl = ui.querySelector('.olo-v360-tempo');
    var vol = ui.querySelector('.olo-v360-vol');
    var fs = ui.querySelector('.olo-v360-fs');

    var viewer = null, sulVideo = false, n = 0, inVista = false, pausaDaScorrimento = false, partito = false;

    function vista() {
      var v = { yaw: opts.yaw, pitch: opts.pitch, hfov: opts.hfov };
      if (viewer) {
        try { v = { yaw: viewer.getYaw(), pitch: viewer.getPitch(), hfov: viewer.getHfov() }; } catch (e) {}
      }
      return v;
    }

    // conVideo = false → la copertina; true → il video sulla sfera (solo quando gira: Pannellum crea il
    // visore dinamico subito solo con dynamicUpdate acceso).
    function crea(conVideo) {
      if (!conVideo && !config.poster) return;
      var v = vista();
      if (viewer) { try { viewer.destroy(); } catch (e) {} viewer = null; }
      stage.innerHTML = '';
      var el = document.createElement('div');
      el.className = 'olo-v360-sfera';
      el.id = container.id + '-sfera-' + (++n);
      stage.appendChild(el);
      var o = Object.assign({}, opts, v, { showFullscreenCtrl: false });
      if (conVideo) { o.panorama = video; o.dynamic = true; o.dynamicUpdate = true; }
      else { o.panorama = config.poster; }
      try { viewer = pannellum.viewer(el.id, o); sulVideo = conVideo; }
      catch (e) { console.error('[olo-v360] video', e); }
    }

    function aggiornaPP() {
      var fermo = video.paused;
      pp.innerHTML = fermo ? ICONE.play : ICONE.pausa;
      pp.setAttribute('aria-label', fermo ? (L.play || '') : (L.pause || ''));
    }
    function aggiornaVol() {
      if (!vol) return;
      vol.innerHTML = video.muted ? ICONE.muto : ICONE.audio;
      vol.setAttribute('aria-pressed', video.muted ? 'false' : 'true');
      vol.setAttribute('aria-label', video.muted ? (L.unmute || '') : (L.mute || ''));
    }
    function aggiornaTempo() {
      var d = video.duration || 0, t = video.currentTime || 0;
      var p = d ? Math.min(100, t / d * 100) : 0;
      fatto.style.width = p + '%';
      barra.setAttribute('aria-valuenow', String(Math.round(p)));
      barra.setAttribute('aria-valuetext', minuti(t) + ' / ' + minuti(d));
      tempoEl.textContent = minuti(t) + (d ? ' / ' + minuti(d) : '');
    }

    function gioca(conSuono) {
      if (conSuono && conAudio) video.muted = false;
      pausaDaScorrimento = false;
      var pr = video.play();
      if (pr && pr.catch) {
        pr.catch(function() {
          // Il browser non lascia partire col suono senza un gesto: si riprova muto.
          if (!video.muted) { video.muted = true; video.play().catch(function() {}); }
        });
      }
    }

    avvia.addEventListener('click', function() { gioca(true); });
    pp.addEventListener('click', function() {
      if (video.paused) gioca(!partito);
      else video.pause();
    });
    if (vol) vol.addEventListener('click', function() {
      video.muted = !video.muted;
      if (video.paused && !video.muted) gioca(true);
    });

    function cerca(clientX) {
      var r = barra.getBoundingClientRect();
      if (!video.duration || !r.width) return;
      video.currentTime = Math.max(0, Math.min(1, (clientX - r.left) / r.width)) * video.duration;
      aggiornaTempo();
    }
    var trascina = false;
    barra.addEventListener('pointerdown', function(e) {
      trascina = true;
      try { barra.setPointerCapture(e.pointerId); } catch (er) {}
      cerca(e.clientX);
      e.stopPropagation();
    });
    barra.addEventListener('pointermove', function(e) { if (trascina) cerca(e.clientX); });
    barra.addEventListener('pointerup', function() { trascina = false; });
    barra.addEventListener('pointercancel', function() { trascina = false; });
    barra.addEventListener('keydown', function(e) {
      if (!video.duration) return;
      if (e.key === 'ArrowRight') { video.currentTime = Math.min(video.duration, video.currentTime + 5); e.preventDefault(); }
      else if (e.key === 'ArrowLeft') { video.currentTime = Math.max(0, video.currentTime - 5); e.preventDefault(); }
      else if (e.key === 'Home') { video.currentTime = 0; e.preventDefault(); }
      aggiornaTempo();
    });

    function aggiornaFs() {
      if (!fs) return;
      var dentro = (document.fullscreenElement || document.webkitFullscreenElement) === container;
      fs.innerHTML = dentro ? ICONE.esci : ICONE.schermo;
      fs.setAttribute('aria-label', dentro ? (L.exitFullscreen || '') : (L.fullscreen || ''));
      // Pannellum ridisegna la tela al ridimensionamento della finestra.
      setTimeout(function() { window.dispatchEvent(new Event('resize')); }, 60);
    }
    if (fs) {
      fs.addEventListener('click', function() {
        if (document.fullscreenElement || document.webkitFullscreenElement) {
          (document.exitFullscreen || document.webkitExitFullscreen).call(document);
        } else if (container.requestFullscreen) container.requestFullscreen();
        else container.webkitRequestFullscreen();
      });
      document.addEventListener('fullscreenchange', aggiornaFs);
      document.addEventListener('webkitfullscreenchange', aggiornaFs);
    }

    video.addEventListener('playing', function() {
      partito = true;
      container.classList.add('is-partito');
      if (!sulVideo) crea(true);
      else if (viewer) viewer.setUpdate(true);
      aggiornaPP();
    });
    video.addEventListener('pause', function() {
      if (viewer && sulVideo) viewer.setUpdate(false);
      aggiornaPP();
    });
    video.addEventListener('ended', aggiornaPP);
    video.addEventListener('timeupdate', aggiornaTempo);
    video.addEventListener('loadedmetadata', aggiornaTempo);
    video.addEventListener('volumechange', aggiornaVol);
    video.addEventListener('error', function() {
      container.classList.add('is-errore');
      avvia.querySelector('.olo-v360-avvia-testo').textContent = L.error || '';
    });

    // Copertina e lettore solo quando il riquadro si avvicina allo schermo; il video automatico gira
    // solo mentre si vede (fuori schermo si ferma e poi riparte da dov'era).
    var copertinaFatta = false;
    function quandoVisibile(vis) {
      inVista = vis;
      if (vis && !copertinaFatta) { copertinaFatta = true; if (!partito) crea(false); }
      if (vis) {
        if ((autoplay && !partito) || pausaDaScorrimento) gioca(false);
      } else if (!video.paused) {
        video.pause();
        pausaDaScorrimento = true;
      }
    }
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function(entries) {
        entries.forEach(function(en) { quandoVisibile(en.isIntersecting); });
      }, { rootMargin: '200px 0px', threshold: 0 }).observe(container);
    } else {
      quandoVisibile(true);
    }

    aggiornaPP();
    aggiornaVol();
    aggiornaTempo();
    return function() { return viewer; };
  }

  // Object-spin (turntable) viewer — drag-to-spin + inerzia, frames o pseudo-3D rotateY,
  // frecce tastiera, touch pan-y, reduced-motion → no auto-spin. (rif. 65-tema-ceramica.html)
  function initObjectViewer(container, config) {
    var img = container.querySelector('.olo-v360-frame');
    if (!img) return;
    container.__oloV360Img = img;
    var angleEl = container.querySelector('.olo-v360-angle');
    var prevBtn = container.querySelector('.olo-v360-prev');
    var nextBtn = container.querySelector('.olo-v360-next');

    var sub      = config.sub === 'frames' ? 'frames' : 'rotate';
    var frames   = Array.isArray(config.frames) ? config.frames : [];
    var dragSens = parseFloat(config.dragSens) || 0.55;
    var inertia  = parseFloat(config.inertia)  || 0.97;
    var arSpeed  = parseFloat(config.arSpeed)  || 1;
    var reduce   = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var spinFloor = (config.autospin && !reduce) ? Math.max(0.05, arSpeed * 0.25) : 0; // °/frame; reduced-motion → 0

    if (sub === 'frames' && frames.length > 1) {
      frames.forEach(function(u){ var p = new Image(); p.src = u; }); // preload
    }
    if (sub === 'rotate') { container.style.perspective = container.style.perspective || '1200px'; }

    var angle = 0, vel = 0, dragging = false, lastX = 0, running = false, rafId = null, inView = true;

    function frameIndex(a) {
      var n = frames.length; if (!n) return 0;
      var norm = ((a % 360) + 360) % 360;
      return Math.floor(norm / 360 * n) % n;
    }
    function paint() {
      var a = ((angle % 360) + 360) % 360;
      if (sub === 'frames' && frames.length) {
        var src = frames[frameIndex(angle)];
        if (img.getAttribute('src') !== src) img.setAttribute('src', src);
      } else {
        // Con una sola foto il retro è la foto stessa: oltre i 90° rotateY la mostrava a specchio
        // (a 180° scritte e loghi rovesciati). Di spalle si ribalta: il cambio cade di taglio, non si vede.
        var back = Math.cos(a * Math.PI / 180) < 0;
        img.style.transform = 'rotateY(' + angle + 'deg)' + (back ? ' scaleX(-1)' : '');
      }
      if (angleEl) angleEl.textContent = Math.round(a) + '°';
    }
    function loop() {
      if (!dragging) {
        angle += vel; vel *= inertia;
        if (spinFloor > 0) { if (Math.abs(vel) < spinFloor) vel = (vel < 0 ? -spinFloor : spinFloor); }
        else if (Math.abs(vel) < 0.02) { vel = 0; }
      }
      paint();
      if (!dragging && spinFloor === 0 && vel === 0) { running = false; rafId = null; return; } // perf: stop a regime
      if (inView) { rafId = requestAnimationFrame(loop); } else { running = false; rafId = null; }
    }
    function start() { if (!running) { running = true; rafId = requestAnimationFrame(loop); } }
    function stop()  { running = false; if (rafId) { cancelAnimationFrame(rafId); rafId = null; } }

    container.addEventListener('pointerdown', function(e){
      if (e.pointerType === 'touch' && config.touch === false) return;
      if (e.pointerType !== 'touch' && config.drag === false) return;
      dragging = true; lastX = e.clientX; vel = 0; container.style.cursor = 'grabbing';
      try { container.setPointerCapture(e.pointerId); } catch(_){}
      start();
    });
    container.addEventListener('pointermove', function(e){
      if (!dragging) return;
      var dx = e.clientX - lastX; lastX = e.clientX;
      angle += dx * dragSens; vel = dx * dragSens;
    });
    var endDrag = function(){ if (dragging) { dragging = false; container.style.cursor = 'grab'; start(); } };
    container.addEventListener('pointerup', endDrag);
    container.addEventListener('pointercancel', endDrag);

    // Tastiera: frecce ← → sul contenitore + pulsanti ‹ ›
    container.addEventListener('keydown', function(e){
      if (e.key === 'ArrowLeft')  { angle -= 15; vel = -2; start(); e.preventDefault(); }
      else if (e.key === 'ArrowRight') { angle += 15; vel = 2; start(); e.preventDefault(); }
    });
    if (prevBtn) prevBtn.addEventListener('click', function(){ angle -= 15; vel = -2; start(); });
    if (nextBtn) nextBtn.addEventListener('click', function(){ angle += 15; vel = 2; start(); });

    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function(entries){
        entries.forEach(function(en){ inView = en.isIntersecting; if (inView) start(); else stop(); });
      }, { threshold: 0 });
      io.observe(container);
    }

    paint();
    start();
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

  // Tile nata dopo il caricamento (contenuto differito sotto la piega): avvia le nuove istanze.
  document.addEventListener('olo:lazy-hydrated', initAll);

  // Expose for builder iframe reinit
  window.__oloV360Init = function() {
    document.querySelectorAll('[data-olo-v360]').forEach(function(el) {
      // Oggetto girevole: svuotarlo (come si fa col panorama) toglieva la foto resa dal PHP e il
      // riquadro restava nero dopo ogni render del builder. Si avvia solo se è nuovo o se la sua
      // foto è stata sostituita.
      if (isObject(el)) {
        if (!el.__oloV360Img || !el.contains(el.__oloV360Img)) { el.__oloV360 = false; initViewer(el); }
        return;
      }
      el.__oloV360 = false;
      el.innerHTML = '';
      initViewer(el);
    });
  };
})();
