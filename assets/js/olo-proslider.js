/**
 * Olobuilder ProSlider — Frontend runtime.
 *
 * Self-initialising IIFE that drives slide transitions,
 * layer CSS animations, navigation (arrows/dots/swipe/keyboard),
 * autoplay, Ken Burns and video backgrounds.
 */
(function () {
  'use strict';

  /* ── Animation name map (camelCase → CSS keyframe) ─────── */
  var ANIM = {
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
  var LOOP_ANIM = {
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
  function initAll() {
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
    var transition = cfg.transition || 'fade';

    if (total === 0) return;

    /* ── Proportional responsive scaling ─────────────────── */
    var designW = parseInt(container.getAttribute('data-design-width')) || 1200;
    var designH = parseInt(container.getAttribute('data-design-height')) || 600;
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

    function resolveHeightPx(mode, value, containerWidth) {
      switch (mode) {
        case 'vh':
          return Math.round((parseInt(value) / 100) * window.innerHeight);
        case 'ratio':
          var rp = String(value).split(':');
          var rw = parseFloat(rp[0]) || 16;
          var rh = parseFloat(rp[1]) || 9;
          return Math.round(containerWidth / rw * rh);
        default:
          return parseInt(value) || 600;
      }
    }

    function getResponsiveHeight(containerWidth) {
      var vw = window.innerWidth;
      for (var i = 0; i < respHeights.length; i++) {
        if (vw <= respHeights[i].maxW) {
          return resolveHeightPx(respHeights[i].mode, respHeights[i].value, containerWidth);
        }
      }
      return 0; // no override
    }

    function updateLayerScale() {
      var cw = container.offsetWidth;
      var scaleX = cw / designW;
      var respH = getResponsiveHeight(cw);
      var finalH;
      if (respH > 0) {
        // Responsive breakpoint: use resolved px directly
        finalH = respH;
      } else if (desktopHeightMode === 'vh') {
        finalH = resolveHeightPx('vh', desktopHeightValue, cw);
      } else if (desktopHeightMode === 'ratio') {
        finalH = resolveHeightPx('ratio', desktopHeightValue, cw);
      } else {
        // Desktop px: proportional scaling
        finalH = Math.round(designH * scaleX);
      }
      var layerDesignH = Math.round(finalH / scaleX);
      layerWraps.forEach(function (w) {
        w.style.width = designW + 'px';
        w.style.height = layerDesignH + 'px';
        w.style.transform = 'scale(' + scaleX + ')';
      });
      // Blend-mode layers fuori dal wrapper: scala font-size con CSS var
      var blendLayers = qsa(container, '.mps-blend-layer');
      blendLayers.forEach(function (bl) {
        bl.style.fontSize = 'calc(' + scaleX + ' * var(--mps-blend-fs, 24px))';
      });
      if (!container.classList.contains('mps-fullscreen')) {
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

        // Old slide: persistent → keep visible underneath; otherwise hide
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
        }, transition === 'fade' || transition === 'zoom' ? transDur * 0.3 : transDur * 0.5);
      }

      // Persistent slide → skip layer-out animation, proceed immediately
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
      document.addEventListener('keydown', function (e) {
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

        if (pType === 'mouse' || pType === 'both') {
          container.addEventListener('mousemove', function (e) {
            var rect = container.getBoundingClientRect();
            var mx = (e.clientX - rect.left) / rect.width - 0.5;
            var my = (e.clientY - rect.top) / rect.height - 0.5;
            parallaxLayers.forEach(function (pl) {
              var depth = parseInt(pl.getAttribute('data-parallax-depth'), 10) || 0;
              if (depth <= 0) return;
              var offsetX = mx * depth * pIntensity;
              var offsetY = my * depth * pIntensity;
              pl.style.transform = 'translate(' + offsetX + 'px, ' + offsetY + 'px)';
            });
          });
          container.addEventListener('mouseleave', function () {
            parallaxLayers.forEach(function (pl) {
              pl.style.transform = '';
              pl.style.transition = 'transform 0.5s ease-out';
              setTimeout(function () { pl.style.transition = ''; }, 500);
            });
          });
        }

        if (pType === 'scroll' || pType === 'both') {
          window.addEventListener('scroll', function () {
            var rect = container.getBoundingClientRect();
            var viewH = window.innerHeight;
            if (rect.bottom < 0 || rect.top > viewH) return;
            var progress = (viewH - rect.top) / (viewH + rect.height);
            var scrollOffset = (progress - 0.5) * 2;
            parallaxLayers.forEach(function (pl) {
              var depth = parseInt(pl.getAttribute('data-parallax-depth'), 10) || 0;
              if (depth <= 0) return;
              var offsetY = scrollOffset * depth * pIntensity;
              var current = pl.style.transform || '';
              if (current.indexOf('translateY') === -1) {
                pl.style.transform = 'translateY(' + offsetY + 'px)';
              }
            });
          }, { passive: true });
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
    if (cfg.carousel) {
      var track = qs(container, '.olo-proslider-track');
      if (track) {
        function updateCarouselTrack() {
          var cw = parseFloat(getComputedStyle(container).getPropertyValue('--mps-carousel-width')) || 80;
          var gap = parseFloat(getComputedStyle(container).getPropertyValue('--mps-carousel-gap')) || 10;
          var containerW = container.offsetWidth;
          var slideW = containerW * (cw / 100);
          var offset = (containerW - slideW) / 2 - current * (slideW + gap);
          track.style.transform = 'translateX(' + offset + 'px)';
          track.style.transition = 'transform 0.6s cubic-bezier(0.25,0.46,0.45,0.94)';
          slides.forEach(function (s, i) {
            s.classList.toggle('mps-carousel-active', i === current);
          });
        }
        // Patch goToSlide to also update carousel track
        var _origGoToSlide = goToSlide;
        goToSlide = function (idx) {
          _origGoToSlide(idx);
          setTimeout(updateCarouselTrack, 50);
        };
        updateCarouselTrack();
        window.addEventListener('resize', updateCarouselTrack);
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
      // Update active thumb on slide change
      var _origGoToSlideTh = goToSlide;
      goToSlide = function (idx) {
        _origGoToSlideTh(idx);
        setTimeout(function () {
          thumbs.forEach(function (t, i) { t.classList.toggle('mps-thumb-active', i === idx); });
        }, 50);
      };
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
      var _origGoToSlideTab = goToSlide;
      goToSlide = function (idx) {
        _origGoToSlideTab(idx);
        setTimeout(function () {
          tabs.forEach(function (t, i) { t.classList.toggle('mps-tab-active', i === idx); });
        }, 50);
      };
    }

    /* ── Scroll-Fixed Timeline ──────────────────────────── */
    if (cfg.scrollTimeline) {
      var wrapper = container.closest('.mps-scroll-fixed-wrapper');
      if (wrapper) {
        var scrollDist = cfg.scrollTimelineDistance || 2000;
        var scrollLast = -1;
        window.addEventListener('scroll', function () {
          var wr = wrapper.getBoundingClientRect();
          if (wr.bottom < 0 || wr.top > window.innerHeight) return;
          var progress = Math.max(0, Math.min(1, -wr.top / scrollDist));
          var targetIdx = Math.min(total - 1, Math.floor(progress * total));
          if (targetIdx !== scrollLast) {
            scrollLast = targetIdx;
            goToSlide(targetIdx);
          }
        }, { passive: true });
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
})();
