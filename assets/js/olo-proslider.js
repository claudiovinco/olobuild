/**
 * Olobuilder ProSlider — Frontend runtime.
 * ES module format.
 *
 * Self-initialising module that drives slide transitions,
 * layer CSS animations, navigation (arrows/dots/swipe/keyboard),
 * autoplay, Ken Burns and video backgrounds.
 */

/* ── Animation name map (camelCase -> CSS keyframe) ─────── */
const ANIM = {
  // Entrances — Base/Fade
  fadeIn:        'mpsFadeIn',
  fadeInUp:      'mpsFadeInUp',
  fadeInDown:    'mpsFadeInDown',
  fadeInLeft:    'mpsFadeInLeft',
  fadeInRight:   'mpsFadeInRight',
  // Entrances — Slide
  slideInLeft:   'mpsSlideInLeft',
  slideInRight:  'mpsSlideInRight',
  slideInUp:     'mpsSlideInUp',
  slideInDown:   'mpsSlideInDown',
  slideShortFromTop:     'mpsSlideShortFromTop',
  slideShortFromBottom:  'mpsSlideShortFromBottom',
  slideShortFromLeft:    'mpsSlideShortFromLeft',
  slideShortFromRight:   'mpsSlideShortFromRight',
  smoothSlideFromBottom: 'mpsSmoothSlideFromBottom',
  smoothSlideFromTop:    'mpsSmoothSlideFromTop',
  smoothSlideFromLeft:   'mpsSmoothSlideFromLeft',
  smoothSlideFromRight:  'mpsSmoothSlideFromRight',
  // Entrances — Skew
  skewFromLeft:       'mpsSkewFromLeft',
  skewFromRight:      'mpsSkewFromRight',
  skewShortFromLeft:  'mpsSkewShortFromLeft',
  skewShortFromRight: 'mpsSkewShortFromRight',
  // Entrances — Flip 3D
  flipFromTop:    'mpsFlipFromTop',
  flipFromBottom: 'mpsFlipFromBottom',
  flipFromLeft:   'mpsFlipFromLeft',
  flipFromRight:  'mpsFlipFromRight',
  // Entrances — Rotate
  rotateIn:             'mpsRotateIn',
  rotateInFromBottom:   'mpsRotateInFromBottom',
  rotate3D:             'mpsRotate3D',
  rotateInFromLeft:     'mpsRotateInFromLeft',
  rotateInFromRight:    'mpsRotateInFromRight',
  // Entrances — Pop/Bounce
  zoomIn:        'mpsZoomIn',
  bounceIn:      'mpsBounceIn',
  popUpSmooth:   'mpsPopUpSmooth',
  popUpBack:     'mpsPopUpBack',
  bounceInUp:    'mpsBounceInUp',
  bounceInDown:  'mpsBounceInDown',
  // Entrances — Mask/Reveal
  maskFromLeft:   'mpsMaskFromLeft',
  maskFromRight:  'mpsMaskFromRight',
  maskFromTop:    'mpsMaskFromTop',
  maskFromBottom: 'mpsMaskFromBottom',
  maskZoomOut:    'mpsMaskZoomOut',
  maskCenter:     'mpsMaskCenter',
  // Exits — Base/Fade
  fadeOut:        'mpsFadeOut',
  fadeOutUp:      'mpsFadeOutUp',
  fadeOutDown:    'mpsFadeOutDown',
  fadeOutLeft:    'mpsFadeOutLeft',
  fadeOutRight:   'mpsFadeOutRight',
  // Exits — Slide
  slideOutLeft:   'mpsSlideOutLeft',
  slideOutRight:  'mpsSlideOutRight',
  slideOutUp:     'mpsSlideOutUp',
  slideOutDown:   'mpsSlideOutDown',
  slideShortOutTop:      'mpsSlideShortOutTop',
  slideShortOutBottom:   'mpsSlideShortOutBottom',
  slideShortOutLeft:     'mpsSlideShortOutLeft',
  slideShortOutRight:    'mpsSlideShortOutRight',
  smoothSlideOutBottom:  'mpsSmoothSlideOutBottom',
  smoothSlideOutTop:     'mpsSmoothSlideOutTop',
  smoothSlideOutLeft:    'mpsSmoothSlideOutLeft',
  smoothSlideOutRight:   'mpsSmoothSlideOutRight',
  // Exits — Skew
  skewOutLeft:       'mpsSkewOutLeft',
  skewOutRight:      'mpsSkewOutRight',
  skewShortOutLeft:  'mpsSkewShortOutLeft',
  skewShortOutRight: 'mpsSkewShortOutRight',
  // Exits — Flip 3D
  flipOutTop:    'mpsFlipOutTop',
  flipOutBottom: 'mpsFlipOutBottom',
  flipOutLeft:   'mpsFlipOutLeft',
  flipOutRight:  'mpsFlipOutRight',
  // Exits — Rotate
  rotateOut:           'mpsRotateOut',
  rotateOutToBottom:   'mpsRotateOutToBottom',
  rotateOut3D:         'mpsRotateOut3D',
  rotateOutToLeft:     'mpsRotateOutToLeft',
  rotateOutToRight:    'mpsRotateOutToRight',
  // Exits — Pop/Bounce
  zoomOut:        'mpsZoomOut',
  bounceOut:      'mpsBounceOut',
  popOutSmooth:   'mpsPopOutSmooth',
  popOutBack:     'mpsPopOutBack',
  bounceOutUp:    'mpsBounceOutUp',
  bounceOutDown:  'mpsBounceOutDown',
  // Exits — Mask
  maskOutLeft:    'mpsMaskOutLeft',
  maskOutRight:   'mpsMaskOutRight',
  maskOutTop:     'mpsMaskOutTop',
  maskOutBottom:  'mpsMaskOutBottom',
  maskZoomIn:     'mpsMaskZoomIn',
  maskOutCenter:  'mpsMaskOutCenter',
};

/* ── Loop animation name map ─────────────────────────────── */
const LOOP_ANIM = {
  pendulum:       'mpsPendulum',
  pendulumBelow:  'mpsPendulumBelow',
  pendulumAbove:  'mpsPendulumAbove',
  pendulumLeft:   'mpsPendulumLeft',
  pendulumRight:  'mpsPendulumRight',
  waveSmallLeft:  'mpsWaveSmallLeft',
  waveSmallRight: 'mpsWaveSmallRight',
  waveBigLeft:    'mpsWaveBigLeft',
  waveBigRight:   'mpsWaveBigRight',
  wiggleY:        'mpsWiggleY',
  wiggleX:        'mpsWiggleX',
  wiggle3D:       'mpsWiggle3D',
  crazyWiggle:    'mpsCrazyWiggle',
  spinCW:         'mpsSpinCW',
  spinCCW:        'mpsSpinCCW',
  blinkLoop:      'mpsBlinkLoop',
  floatLoop:      'mpsFloatLoop',
  pulseLoop:      'mpsPulseLoop',
  breathLoop:     'mpsBreathLoop',
  slideHLoop:     'mpsSlideHLoop',
  hoverLoop:      'mpsHoverLoop',
};

/* ── Helpers ───────────────────────────────────────────── */
function qsa(parent, sel) { return Array.from(parent.querySelectorAll(sel)); }
function qs(parent, sel)  { return parent.querySelector(sel); }

/* ── Init all sliders on the page ─────────────────────── */
export function initAll() {
  qsa(document, '.olo-proslider').forEach(initSlider);
}

/* ── Single slider init ───────────────────────────────── */
function initSlider(container) {
  if (container._mpsInit) return;
  container._mpsInit = true;

  var cfg;
  try { cfg = JSON.parse(container.getAttribute('data-proslider') || '{}'); } catch (e) { cfg = {}; }

  var slides     = qsa(container, '.olo-proslider-slide');
  var dots       = qsa(container, '.olo-proslider-dot');
  var prevBtn    = qs(container, '.olo-proslider-prev');
  var nextBtn    = qs(container, '.olo-proslider-next');
  var total      = slides.length;
  var current    = 0;
  var animating  = false;
  var autoTimer  = null;
  // Nel carosello il cambio È lo scorrimento delle card: la transizione scelta non agisce (niente
  // transform in linea sulle slide, che toglievano scala e rotazione alle card).
  var transition = cfg.carousel ? 'carousel' : (cfg.transition || 'fade');
  // Chi segue il cambio di slide (carosello, miniature, schede, timeline di scorrimento) si iscrive qui:
  // alla PARTENZA riceve la slide scelta, all'ARRIVO sa che il cambio è chiuso. Prima ognuno avvolgeva
  // goToSlide e leggeva `current` 50 ms dopo, quando era ancora la slide vecchia (si aggiorna a fine
  // transizione): il carosello restava indietro di una card e le miniature si accendevano anche sui
  // cambi rifiutati.
  var allaPartenza = [];
  var allArrivo = [];

  if (total === 0) return;

  /* ── Proportional responsive scaling ─────────────────── */
  // La tela di progetto è quella dell'editor per ogni formato (ProSliderEditor: computer 1440, notebook 1240,
  // tablet 1024, telefono 640): i livelli si posano lì e la tela si scala sulla larghezza vera dello slider.
  // Prima era 1200 per tutti: il computer non somigliava all'editor e sul telefono lo slider diventava una
  // striscia bassa col testo di un terzo. Le soglie sono quelle delle regole per formato del PHP
  // (generate_responsive_css, viewport).
  var designW = parseInt(container.getAttribute('data-design-width')) || 1440;
  var designH = parseInt(container.getAttribute('data-design-height')) || 600;
  var FORMATI = [
    { key: 'mobile', max: 640, w: 640 },
    { key: 'tablet', max: 1024, w: 1024 },
    { key: 'notebook', max: 1240, w: 1240 },
  ];
  function formatoCorrente() {
    var vw = window.innerWidth;
    for (var i = 0; i < FORMATI.length; i++) {
      if (vw <= FORMATI[i].max) return FORMATI[i];
    }
    return { key: 'desktop', max: Infinity, w: designW };
  }
  var layerWraps = qsa(container, '.mps-layers-wrap');

  // Desktop height mode (vh, ratio, px)
  var desktopHeightMode = container.getAttribute('data-height-mode') || 'px';
  var desktopHeightValue = container.getAttribute('data-height-value') || designH;

  // Parse responsive heights from data attribute (format: "1240:mode:value,...")
  var respHeights = [];
  var rhAttr = container.getAttribute('data-responsive-heights');
  if (rhAttr) {
    rhAttr.split(',').forEach(function (pair) {
      var parts = pair.split(':');
      if (parts.length >= 3) {
        // format: maxW:mode:value (ratio has 4 parts: maxW:ratio:W:H)
        var maxW = parseInt(parts[0]);
        var mode = parts[1];
        var value = parts.slice(2).join(':'); // handles ratio "16:9"
        respHeights.push({ maxW: maxW, mode: mode, value: value });
      } else if (parts.length === 2) {
        // Backward compat: maxW:pxValue
        respHeights.push({ maxW: parseInt(parts[0]), mode: 'px', value: parts[1] });
      }
    });
    respHeights.sort(function (a, b) { return a.maxW - b.maxW; });
  }

  function resolveHeightPx(mode, value, containerWidth, formatW) {
    switch (mode) {
      case 'vh':
        return Math.round((parseInt(value) / 100) * window.innerHeight);
      case 'ratio':
        var rp = String(value).split(':');
        var rw = parseFloat(rp[0]) || 16;
        var rh = parseFloat(rp[1]) || 9;
        return Math.round(containerWidth / rw * rh);
      default:
        // px = altezza sulla tela del formato, che si scala con la larghezza come nell'editor. Prima
        // l'altezza di un formato era fissa e quella del computer scalava: le due regole non tornavano.
        return Math.round((parseInt(value) || 600) * containerWidth / (formatW || designW));
    }
  }

  // L'altezza di un formato è la sua, se impostata, se no quella del computer: come nell'editor
  // (data[heightX] || data.height), senza ereditare dal formato più largo.
  function altezzaFormato(f) {
    for (var i = 0; i < respHeights.length; i++) {
      if (respHeights[i].maxW === f.max) return respHeights[i];
    }
    return { mode: desktopHeightMode, value: desktopHeightMode === 'px' ? designH : desktopHeightValue };
  }

  function updateLayerScale() {
    var cw = container.offsetWidth;
    var f = formatoCorrente();
    var scaleX = cw / f.w;
    var h = altezzaFormato(f);
    var schermoPieno = container.classList.contains('mps-fullscreen');
    // A schermo pieno l'altezza è quella della finestra (100vh), non quella impostata: la tela dei livelli
    // la segue, se no un livello al 90% finiva sotto il bordo o a metà schermo.
    var finalH = schermoPieno ? (container.offsetHeight || resolveHeightPx(h.mode, h.value, cw, f.w)) : resolveHeightPx(h.mode, h.value, cw, f.w);
    var layerDesignH = Math.round(finalH / scaleX);
    // Nel carosello la tela di una slide è larga quanto la card (la stessa frazione della tela del formato):
    // i livelli restano alla scala dello slider (un carattere di 48 resta 48 sulla tela) e si posano sulla
    // card, come nell'editor, che col carosello mostra la tela stretta. Prima la tela era larga quanto
    // tutto lo slider: un livello al centro finiva sul bordo destro della card, o fuori.
    var cardW = cfg.carousel && slides[0] ? slides[0].offsetWidth : 0;
    var slideDesignW = cardW && cw ? Math.round(f.w * cardW / cw) : f.w;
    layerWraps.forEach(function (w) {
      var globale = w.classList.contains('olo-proslider-global-layers');
      w.style.width = (globale ? f.w : slideDesignW) + 'px';
      w.style.height = layerDesignH + 'px';
      w.style.transform = 'scale(' + scaleX + ')';
    });
    // I livelli con fusione stanno fuori dalla tela scalata (il transform isolerebbe la fusione): carattere,
    // spaziatura e padding si moltiplicano per --mps-k, che dentro la tela vale 1 (CSS). Prima il carattere
    // restava quello di progetto e sul telefono un titolo di 120 usciva dallo slider.
    container.style.setProperty('--mps-k', String(scaleX));
    if (!schermoPieno) {
      container.style.height = finalH + 'px';
    }
  }

  updateLayerScale();
  var _mpsResizeTimer;
  window.addEventListener('resize', function () {
    clearTimeout(_mpsResizeTimer);
    _mpsResizeTimer = setTimeout(updateLayerScale, 30);
  });

  /* ── Layer animations ────────────────────────────────── */
  function animateLayersIn(slide) {
    var layers = qsa(slide, '.olo-proslider-layer');
    layers.forEach(function (el) {
      // Timeline keyframe layer
      var tlName = el.getAttribute('data-timeline-name');
      if (tlName) {
        el.style.animation = 'none';
        el.classList.remove('mps-anim-visible');
        void el.offsetWidth;

        var tlDur   = el.getAttribute('data-timeline-dur') || '3000ms';
        var tlDelay = el.getAttribute('data-timeline-delay') || '0ms';
        var tlLoop  = el.getAttribute('data-timeline-loop') === '1' ? 'infinite' : '1';

        el.style.animation = tlName + ' ' + tlDur + ' ease ' + tlDelay + ' ' + tlLoop + ' both';
        el.classList.add('mps-anim-visible');
        return;
      }

      // Classic animation
      var animKey = el.getAttribute('data-anim-in') || 'fadeIn';

      el.style.animation = 'none';
      el.classList.remove('mps-anim-visible');
      // Force reflow
      void el.offsetWidth;

      if (animKey === 'none') {
        // No animation — show immediately
        el.style.animation = '';
        el.classList.add('mps-anim-visible');
        return;
      }

      var name   = ANIM[animKey] || 'mpsFadeIn';
      var dur    = (parseInt(el.getAttribute('data-anim-in-duration'), 10) || 800) + 'ms';
      var delay  = (parseInt(el.getAttribute('data-anim-in-delay'), 10)    || 0)   + 'ms';
      var easing = el.getAttribute('data-anim-easing') || 'ease';

      // Character animation: animate individual chars/words
      if (el.getAttribute('data-char-anim') === '1') {
        var chars = qsa(el, '.mps-char');
        if (chars.length > 0) {
          el.style.animation = '';
          el.classList.add('mps-anim-visible');
          chars.forEach(function (ch) {
            ch.style.opacity = '0';
            ch.style.animation = 'none';
            void ch.offsetWidth;
            // animation-delay is set inline by PHP
            ch.style.animation = name + ' ' + dur + ' ' + easing + ' both';
          });
          return;
        }
      }

      el.style.animation = name + ' ' + dur + ' ' + easing + ' ' + delay + ' both';
      el.classList.add('mps-anim-visible');

      // Loop animation — starts after entrance completes
      var loopKey = el.getAttribute('data-anim-loop');
      if (!(loopKey && loopKey !== 'none' && LOOP_ANIM[loopKey])) {
        // Finito l'ingresso, l'animazione si toglie (il livello resta visibile per mps-anim-visible): la sua posa
        // finale, tenuta da «both», copriva il transform in linea della parallasse del livello (mouse e scorrimento).
        var entrataFine = (parseInt(delay, 10) || 0) + (parseInt(dur, 10) || 800) + 60;
        setTimeout(function () {
          if (el.style.animation.indexOf(name + ' ') === 0) { el.style.animation = 'none'; }
        }, entrataFine);
      }
      if (loopKey && loopKey !== 'none' && LOOP_ANIM[loopKey]) {
        var loopName  = LOOP_ANIM[loopKey];
        var loopDur   = (parseInt(el.getAttribute('data-anim-loop-dur'), 10) || 3000) + 'ms';
        var loopEase  = el.getAttribute('data-anim-loop-easing') || 'ease-in-out';
        var entrDelay = parseInt(delay, 10) || 0;
        var entrDur   = parseInt(dur, 10) || 800;
        setTimeout(function () {
          el.style.animation = loopName + ' ' + loopDur + ' ' + loopEase + ' infinite';
        }, entrDelay + entrDur);
      }
    });
  }

  function animateLayersOut(slide, callback) {
    var layers = qsa(slide, '.olo-proslider-layer');
    if (!layers.length) { callback && callback(); return; }

    var maxEnd = 0;
    layers.forEach(function (el) {
      // Timeline layer: quick fadeout
      if (el.getAttribute('data-timeline-name')) {
        el.style.animation = 'mpsFadeOut 400ms ease both';
        el.classList.remove('mps-anim-visible');
        if (400 > maxEnd) maxEnd = 400;
        return;
      }

      var animKey = el.getAttribute('data-anim-out') || 'fadeOut';

      if (animKey === 'none') {
        // No animation — hide immediately
        el.style.animation = '';
        el.classList.remove('mps-anim-visible');
        return;
      }

      var name   = ANIM[animKey] || 'mpsFadeOut';
      var dur    = parseInt(el.getAttribute('data-anim-out-duration'), 10) || 600;
      var delay  = parseInt(el.getAttribute('data-anim-out-delay'), 10)    || 0;
      var easing = el.getAttribute('data-anim-easing') || 'ease';

      el.style.animation = name + ' ' + dur + 'ms ' + easing + ' ' + delay + 'ms both';
      el.classList.remove('mps-anim-visible');

      var end = dur + delay;
      if (end > maxEnd) maxEnd = end;
    });

    setTimeout(function () { callback && callback(); }, maxEnd);
  }

  /* ── Persistence tracker: { slideIndex: remainingSlides } */
  var persistTracker = {};

  /* ── Slide transition ────────────────────────────────── */
  function goToSlide(idx) {
    if (animating || idx === current || total <= 1) return;
    animating = true;
    // Cancel pending autoplay timer (will be re-scheduled after transition)
    if (autoTimer) { clearTimeout(autoTimer); autoTimer = -1; }
    allaPartenza.forEach(function (fn) { fn(idx); });

    var oldSlide = slides[current];
    var newSlide = slides[idx];
    var oldPersistFor = parseInt(oldSlide.getAttribute('data-persist-for') || '0', 10);
    var newAlreadyVisible = newSlide.classList.contains('mps-persistent-visible');

    // Expire persistent slides whose counter has run out
    var expiredKeys = [];
    for (var k in persistTracker) {
      if (!persistTracker.hasOwnProperty(k)) continue;
      var ki = parseInt(k, 10);
      // If we're navigating TO this persistent slide, don't expire it
      if (ki === idx) continue;
      persistTracker[k]--;
      if (persistTracker[k] <= 0) {
        expiredKeys.push(ki);
      }
    }
    expiredKeys.forEach(function (si) {
      delete persistTracker[si];
      var s = slides[si];
      animateLayersOut(s, function () {
        s.classList.remove('mps-persistent-visible');
      });
    });

    // If navigating back to a persistent-visible slide, remove from tracker
    if (newAlreadyVisible && persistTracker[idx] !== undefined) {
      delete persistTracker[idx];
    }

    function proceed() {
      var needs3D = ['slide', 'flipH', 'flipV', 'cubeH', 'cubeV', 'slideOver'].indexOf(transition) !== -1;

      if (needs3D && !newAlreadyVisible) {
        var goingForward = idx > current || (current === total - 1 && idx === 0);
        // Set initial off-screen position for new slide (CSS handles this via default styles)
        newSlide.classList.remove('mps-persistent-visible');
        newSlide.classList.add('mps-active');
        void newSlide.offsetWidth; // reflow
        // For 'slide' transition, set inline transform
        if (transition === 'slide') {
          newSlide.style.transform = goingForward ? 'translateX(100%)' : 'translateX(-100%)';
          void newSlide.offsetWidth;
          newSlide.style.transform = 'translateX(0)';
          oldSlide.style.transform = goingForward ? 'translateX(-100%)' : 'translateX(100%)';
        }
        // Add exit class for CSS-driven 3D transitions
        oldSlide.classList.add('mps-exit-left');
      } else {
        newSlide.classList.remove('mps-persistent-visible');
        newSlide.classList.add('mps-active');
      }

      // Old slide: persistent -> keep visible underneath; otherwise hide
      if (oldPersistFor > 0) {
        oldSlide.classList.remove('mps-active');
        oldSlide.classList.add('mps-persistent-visible');
        persistTracker[current] = oldPersistFor;
      } else {
        oldSlide.classList.remove('mps-active');
        oldSlide.classList.remove('mps-exit-left');
      }

      // Update dots
      dots.forEach(function (d, i) {
        d.classList.toggle('mps-dot-active', i === idx);
      });

      // Pause/play video bg + audio (skip for persistent — keep playing)
      if (!(oldPersistFor > 0)) { pauseVideoBg(oldSlide); handleAudioLayers(oldSlide, false); }
      playVideoBg(newSlide);
      handleAudioLayers(newSlide, true);

      // Ken Burns reset
      resetKenBurns(newSlide);

      // Animate in new layers after slide transition
      var transDur = cfg.transDuration || 800;
      setTimeout(function () {
        // If the new slide was already persistent-visible, layers are already shown
        if (!newAlreadyVisible) {
          animateLayersIn(newSlide);
        }
        // Clean old slide transform
        if (transition === 'slide' && !(oldPersistFor > 0)) {
          oldSlide.style.transform = '';
        }
        current = idx;
        animating = false;
        // Re-schedule autoplay based on new current slide's duration
        if (cfg.autoplay && autoTimer !== null) scheduleAutoplay();
        allArrivo.forEach(function (fn) { fn(idx); });
      }, transition === 'fade' || transition === 'zoom' ? transDur * 0.3 : transDur * 0.5);
    }

    // Persistent slide -> skip layer-out animation, proceed immediately
    if (oldPersistFor > 0) {
      proceed();
    } else {
      animateLayersOut(oldSlide, proceed);
    }
  }

  function next() {
    var idx = current + 1;
    if (idx >= total) idx = cfg.loop ? 0 : current;
    goToSlide(idx);
  }

  function prev() {
    var idx = current - 1;
    if (idx < 0) idx = cfg.loop ? total - 1 : 0;
    goToSlide(idx);
  }

  /* ── Navigation ──────────────────────────────────────── */
  if (prevBtn) prevBtn.addEventListener('click', function (e) { e.preventDefault(); prev(); });
  if (nextBtn) nextBtn.addEventListener('click', function (e) { e.preventDefault(); next(); });

  dots.forEach(function (dot) {
    dot.addEventListener('click', function () {
      var idx = parseInt(dot.getAttribute('data-slide'), 10);
      if (!isNaN(idx)) goToSlide(idx);
    });
  });

  /* ── Keyboard ────────────────────────────────────────── */
  if (cfg.keyboard) {
    // Le frecce muovono lo slider solo se è lui ad avere il fuoco o se è quello più visibile nella
    // finestra, e mai mentre si scrive in un campo. Prima ascoltava tutto il documento: con più slider
    // nella pagina una freccia li faceva scorrere tutti, e scorreva anche scrivendo in un modulo.
    var visibile = function (el) {
      var r = el.getBoundingClientRect();
      var vh = window.innerHeight || document.documentElement.clientHeight;
      return Math.max(0, Math.min(r.bottom, vh) - Math.max(r.top, 0)) / Math.max(1, Math.min(r.height, vh));
    };
    document.addEventListener('keydown', function (e) {
      if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') return;
      if (e.defaultPrevented || e.altKey || e.ctrlKey || e.metaKey) return;
      var t = e.target;
      if (t && (t.isContentEditable || /^(INPUT|TEXTAREA|SELECT)$/.test(t.tagName))) return;
      var dentro = container.contains(document.activeElement) && document.activeElement !== document.body;
      if (!dentro) {
        var mia = visibile(container);
        if (mia < 0.5) return;
        var tutti = qsa(document, '.olo-proslider');
        for (var i = 0; i < tutti.length; i++) {
          if (tutti[i] !== container && visibile(tutti[i]) > mia) return;
        }
      }
      if (e.key === 'ArrowLeft')  prev();
      if (e.key === 'ArrowRight') next();
    });
  }

  /* ── Swipe ───────────────────────────────────────────── */
  if (cfg.swipe) {
    var touchStartX = 0;
    var touchStartY = 0;
    container.addEventListener('touchstart', function (e) {
      touchStartX = e.touches[0].clientX;
      touchStartY = e.touches[0].clientY;
    }, { passive: true });
    container.addEventListener('touchend', function (e) {
      var dx = e.changedTouches[0].clientX - touchStartX;
      var dy = e.changedTouches[0].clientY - touchStartY;
      if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
        if (dx < 0) next(); else prev();
      }
    }, { passive: true });
  }

  /* ── Autoplay (per-slide duration) ────────────────────── */
  function getSlideDelay(slideEl) {
    var d = parseInt(slideEl.getAttribute('data-duration') || '0', 10);
    return d > 0 ? d : (cfg.autoplaySpeed || 5000);
  }

  /* ── Progress bar ────────────────────────────────────── */
  var progressBar = qs(container, '.olo-proslider-progressbar-fill');
  var progressRAF = null;

  function startProgressBar(duration) {
    if (!progressBar) return;
    progressBar.style.transition = 'none';
    progressBar.style.width = '0%';
    void progressBar.offsetWidth;
    progressBar.style.transition = 'width ' + duration + 'ms linear';
    progressBar.style.width = '100%';
  }
  function resetProgressBar() {
    if (!progressBar) return;
    progressBar.style.transition = 'none';
    progressBar.style.width = '0%';
  }

  function scheduleAutoplay() {
    stopAutoplay();
    if (!cfg.autoplay) return;
    var delay = getSlideDelay(slides[current]);
    startProgressBar(delay);
    autoTimer = setTimeout(function () {
      next();
      // Re-schedule happens inside goToSlide after transition completes
    }, delay);
  }
  function startAutoplay() {
    scheduleAutoplay();
  }
  function stopAutoplay() {
    if (autoTimer) { clearTimeout(autoTimer); autoTimer = null; }
    resetProgressBar();
  }

  if (cfg.autoplay) {
    startAutoplay();
    if (cfg.pauseOnHover) {
      container.addEventListener('mouseenter', stopAutoplay);
      container.addEventListener('mouseleave', startAutoplay);
    }
  }

  /* ── Video background ────────────────────────────────── */
  function playVideoBg(slide) {
    var v = qs(slide, '.olo-proslider-bg video');
    if (v) { try { v.play(); } catch (e) {} }
  }
  function pauseVideoBg(slide) {
    var v = qs(slide, '.olo-proslider-bg video');
    if (v) { try { v.pause(); } catch (e) {} }
  }

  /* ── Ken Burns reset ─────────────────────────────────── */
  function resetKenBurns(slide) {
    var img = qs(slide, '.olo-proslider-kenburns, .olo-proslider-kenburns-out, .olo-proslider-kenburns-adv');
    if (img) {
      img.style.animation = 'none';
      void img.offsetWidth;
      img.style.animation = '';
    }
  }

  /* ── Layer Actions ────────────────────────────────────── */
  qsa(container, '.olo-proslider-layer[data-action]').forEach(function (el) {
    el.style.cursor = 'pointer';
    el.addEventListener('click', function (e) {
      var action;
      try { action = JSON.parse(el.getAttribute('data-action')); } catch (_) { return; }
      if (!action) return;
      switch (action.type) {
        case 'nextSlide':  next(); break;
        case 'prevSlide':  prev(); break;
        case 'goToSlide':
          var si = parseInt(action.target, 10);
          if (!isNaN(si)) { goToSlide(si); }
          break;
        case 'scrollBelow':
          var rect = container.getBoundingClientRect();
          window.scrollTo({ top: window.scrollY + rect.bottom, behavior: 'smooth' });
          break;
        case 'openUrl':
          if (action.url) { window.open(action.url, action.urlTarget || '_self'); }
          break;
        case 'toggleLayer':
          if (action.target) {
            var tgt = qs(container, '.mps-layer-' + action.target);
            if (tgt) { tgt.style.display = tgt.style.display === 'none' ? '' : 'none'; }
          }
          break;
      }
    });
  });

  /* ── Mouse Wheel Navigation ──────────────────────────── */
  if (cfg.mouseWheel) {
    var wheelCooldown = false;
    container.addEventListener('wheel', function (e) {
      if (wheelCooldown) return;
      e.preventDefault();
      wheelCooldown = true;
      if (e.deltaY > 0) { next(); } else { prev(); }
      setTimeout(function () { wheelCooldown = false; }, 800);
    }, { passive: false });
  }

  /* ── Parallax (mouse move) ───────────────────────────── */
  if (cfg.parallax) {
    var parallaxLayers = qsa(container, '[data-parallax-depth]');
    if (parallaxLayers.length > 0) {
      var pType = cfg.parallaxType || 'mouse';
      var pIntensity = cfg.parallaxIntensity || 5;
      // Mouse e scorrimento scrivono lo stesso transform: ogni livello tiene le due parti e le somma.
      // Prima lo scorrimento scriveva solo la prima volta (poi trovava già un translateY e si fermava)
      // e con «Entrambi» il mouse e lo scorrimento si cancellavano a vicenda.
      var pStato = parallaxLayers.map(function () { return { mx: 0, my: 0, sy: 0 }; });
      function scriviLivello(i) {
        var st = pStato[i];
        var x = st.mx, y = st.my + st.sy;
        parallaxLayers[i].style.transform = (x || y) ? 'translate(' + x.toFixed(1) + 'px, ' + y.toFixed(1) + 'px)' : '';
      }

      if (pType === 'mouse' || pType === 'both') {
        container.addEventListener('mousemove', function (e) {
          var rect = container.getBoundingClientRect();
          var mx = (e.clientX - rect.left) / rect.width - 0.5;
          var my = (e.clientY - rect.top) / rect.height - 0.5;
          parallaxLayers.forEach(function (pl, i) {
            var depth = parseInt(pl.getAttribute('data-parallax-depth'), 10) || 0;
            if (depth <= 0) return;
            pStato[i].mx = mx * depth * pIntensity;
            pStato[i].my = my * depth * pIntensity;
            scriviLivello(i);
          });
        });
        container.addEventListener('mouseleave', function () {
          parallaxLayers.forEach(function (pl, i) {
            pStato[i].mx = 0;
            pStato[i].my = 0;
            pl.style.transition = 'transform 0.5s ease-out';
            scriviLivello(i);
            setTimeout(function () { pl.style.transition = ''; }, 500);
          });
        });
      }

      if (pType === 'scroll' || pType === 'both') {
        var aggiornaScorrimento = function () {
          var rect = container.getBoundingClientRect();
          var viewH = window.innerHeight;
          if (rect.bottom < 0 || rect.top > viewH) return;
          var progress = (viewH - rect.top) / (viewH + rect.height);
          var scrollOffset = (progress - 0.5) * 2;
          parallaxLayers.forEach(function (pl, i) {
            var depth = parseInt(pl.getAttribute('data-parallax-depth'), 10) || 0;
            if (depth <= 0) return;
            // scendendo nella pagina, più alta è la profondità del livello più in fretta sale: sembra più vicino, davanti allo sfondo
            pStato[i].sy = -scrollOffset * depth * pIntensity;
            scriviLivello(i);
          });
        };
        window.addEventListener('scroll', aggiornaScorrimento, { passive: true });
        aggiornaScorrimento();
      }
    }
  }

  /* ── Scroll Effects (fade/blur on page scroll) ────────── */
  if (cfg.scrollEffect && cfg.scrollEffect !== 'none') {
    window.addEventListener('scroll', function () {
      var rect = container.getBoundingClientRect();
      var viewH = window.innerHeight;
      if (rect.top > viewH || rect.bottom < 0) return;
      var visible = Math.max(0, Math.min(1, (viewH - rect.top) / (viewH * 0.5)));
      if (cfg.scrollEffect === 'fade' || cfg.scrollEffect === 'fadeBlur') {
        container.style.opacity = visible;
      }
      if (cfg.scrollEffect === 'blur' || cfg.scrollEffect === 'fadeBlur') {
        var blurPx = (1 - visible) * 10;
        container.style.filter = blurPx > 0.1 ? 'blur(' + blurPx.toFixed(1) + 'px)' : 'none';
      }
    }, { passive: true });
  }

  /* ── Carousel Mode ──────────────────────────────────── */
  // Le card stanno in fila sul binario (flex) e il binario scorre per portare al centro quella scelta. La
  // posizione si misura sulla card vera (offsetLeft, margini compresi): il conto di prima ignorava metà del
  // gap e la card scivolava fuori centro di un gap in più a ogni posizione. Card al centro, card prima e
  // card dopo (che nel 3D guardano il centro da lati opposti) si decidono alla PARTENZA del cambio.
  if (cfg.carousel) {
    var track = qs(container, '.olo-proslider-track');
    if (track) {
      var cardScelta = 0;
      var centraCard = function (idx, subito) {
        var s = slides[idx];
        if (!s) return;
        // prospettiva del 3D proporzionale alla card: lo stesso scorcio sul computer e sul telefono
        container.style.setProperty('--mps-prosp', Math.round(s.offsetWidth * 1.6) + 'px');
        var centro = s.offsetLeft + s.offsetWidth / 2;
        if (s.offsetParent !== track) centro -= track.offsetLeft;
        if (subito) track.style.transition = 'none';
        track.style.transform = 'translateX(' + (container.clientWidth / 2 - centro).toFixed(1) + 'px)';
        if (subito) { void track.offsetWidth; track.style.transition = ''; }
        slides.forEach(function (sl, i) {
          sl.classList.toggle('mps-carousel-active', i === idx);
          sl.classList.toggle('mps-carousel-prima', i < idx);
          sl.classList.toggle('mps-carousel-dopo', i > idx);
          // distanza dalla card al centro: le card di lato, più piccole, si avvicinano di quanto si sono
          // ristrette (CSS), così fra una card e l'altra resta il Gap impostato
          sl.style.setProperty('--mps-d', String(Math.abs(i - idx)));
        });
      };
      allaPartenza.push(function (idx) { cardScelta = idx; centraCard(idx); });
      centraCard(0, true);
      var _mpsCaroselloTimer;
      window.addEventListener('resize', function () {
        clearTimeout(_mpsCaroselloTimer);
        _mpsCaroselloTimer = setTimeout(function () { centraCard(cardScelta, true); }, 40);
      });
      // Una card di lato si sceglie col clic: i suoi livelli sono spenti e non ricevono il clic (CSS).
      slides.forEach(function (sl, i) {
        sl.addEventListener('click', function (e) {
          if (i === cardScelta) return;
          e.preventDefault();
          goToSlide(i);
        });
      });
    }
  }

  /* ── Thumbnail Navigation ───────────────────────────── */
  var thumbContainer = document.querySelector('[data-thumbs-for="' + container.id + '"]');
  if (thumbContainer) {
    var thumbs = qsa(thumbContainer, '.olo-proslider-thumb');
    thumbs.forEach(function (thumb) {
      thumb.addEventListener('click', function () {
        var si = parseInt(thumb.getAttribute('data-slide'), 10);
        if (!isNaN(si)) goToSlide(si);
      });
    });
    allaPartenza.push(function (idx) {
      thumbs.forEach(function (t, i) { t.classList.toggle('mps-thumb-active', i === idx); });
    });
  }

  /* ── Tab Navigation ─────────────────────────────────── */
  var tabContainer = document.querySelector('[data-tabs-for="' + container.id + '"]');
  if (tabContainer) {
    var tabs = qsa(tabContainer, '.olo-proslider-tab');
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var si = parseInt(tab.getAttribute('data-slide'), 10);
        if (!isNaN(si)) goToSlide(si);
      });
    });
    allaPartenza.push(function (idx) {
      tabs.forEach(function (t, i) { t.classList.toggle('mps-tab-active', i === idx); });
    });
  }

  /* ── Scroll-Fixed Timeline ──────────────────────────── */
  // Un cambio chiesto dallo scorrimento mentre un altro era in corso veniva rifiutato ma segnato come fatto:
  // lo slider restava sulla slide sbagliata finché non si scorreva fino alla successiva. Ora resta in
  // sospeso e si rifà all'arrivo del cambio in corso.
  if (cfg.scrollTimeline) {
    var wrapper = container.closest('.mps-scroll-fixed-wrapper');
    if (wrapper) {
      var scrollDist = cfg.scrollTimelineDistance || 2000;
      var bersaglioScorrimento = 0;
      var inSospeso = false;
      var seguiScorrimento = function () {
        var wr = wrapper.getBoundingClientRect();
        if (wr.bottom < 0 || wr.top > window.innerHeight) return;
        var progress = Math.max(0, Math.min(1, -wr.top / scrollDist));
        var targetIdx = Math.min(total - 1, Math.floor(progress * total));
        if (targetIdx === bersaglioScorrimento && !inSospeso) return;
        bersaglioScorrimento = targetIdx;
        if (animating) { inSospeso = true; return; }
        inSospeso = false;
        if (targetIdx !== current) goToSlide(targetIdx);
      };
      window.addEventListener('scroll', seguiScorrimento, { passive: true });
      allArrivo.push(function () { if (inSospeso) seguiScorrimento(); });
    }
  }

  /* ── SFX Block Reveal trigger ───────────────────────── */
  qsa(container, '.mps-sfx-block').forEach(function (el) {
    var effect = el.getAttribute('data-sfx-effect') || 'blockRight';
    var effectMap = {
      blockRight: 'mpsSfxBlockRight',
      blockLeft:  'mpsSfxBlockLeft',
      blockDown:  'mpsSfxBlockDown',
      blockUp:    'mpsSfxBlockUp',
    };
    var animName = effectMap[effect] || 'mpsSfxBlockRight';
    var dur = getComputedStyle(el).getPropertyValue('--mps-sfx-dur') || '800ms';
    el.style.setProperty('--mps-sfx-anim', animName);
  });

  /* ── Audio Layer autoplay ───────────────────────────── */
  function handleAudioLayers(slide, play) {
    qsa(slide, 'audio').forEach(function (a) {
      if (play) { try { a.play(); } catch (_) {} }
      else { try { a.pause(); a.currentTime = 0; } catch (_) {} }
    });
  }

  /* ── Initial state ───────────────────────────────────── */
  animateLayersIn(slides[0]);
  playVideoBg(slides[0]);
  handleAudioLayers(slides[0], true);
}

/* ── Bootstrap ─────────────────────────────────────────── */
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  initAll();
}

// Re-init when builder iframe injects new HTML
document.addEventListener('olo:iframe-render', initAll);
// Tile nata dopo il caricamento (contenuto differito sotto la piega): avvia le nuove istanze.
document.addEventListener('olo:lazy-hydrated', initAll);
