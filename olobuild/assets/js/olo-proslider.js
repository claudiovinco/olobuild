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
    fadeIn:        'mpsFadeIn',
    fadeInUp:      'mpsFadeInUp',
    fadeInDown:    'mpsFadeInDown',
    fadeInLeft:    'mpsFadeInLeft',
    fadeInRight:   'mpsFadeInRight',
    slideInLeft:   'mpsSlideInLeft',
    slideInRight:  'mpsSlideInRight',
    slideInUp:     'mpsSlideInUp',
    slideInDown:   'mpsSlideInDown',
    zoomIn:        'mpsZoomIn',
    bounceIn:      'mpsBounceIn',
    rotateIn:      'mpsRotateIn',
    fadeOut:        'mpsFadeOut',
    fadeOutUp:      'mpsFadeOutUp',
    fadeOutDown:    'mpsFadeOutDown',
    fadeOutLeft:    'mpsFadeOutLeft',
    fadeOutRight:   'mpsFadeOutRight',
    slideOutLeft:   'mpsSlideOutLeft',
    slideOutRight:  'mpsSlideOutRight',
    slideOutUp:     'mpsSlideOutUp',
    slideOutDown:   'mpsSlideOutDown',
    zoomOut:        'mpsZoomOut',
    bounceOut:      'mpsBounceOut',
    rotateOut:      'mpsRotateOut',
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

    /* ── Layer animations ────────────────────────────────── */
    function animateLayersIn(slide) {
      var layers = qsa(slide, '.olo-proslider-layer');
      layers.forEach(function (el) {
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

        el.style.animation = name + ' ' + dur + ' ' + easing + ' ' + delay + ' both';
        el.classList.add('mps-anim-visible');
      });
    }

    function animateLayersOut(slide, callback) {
      var layers = qsa(slide, '.olo-proslider-layer');
      if (!layers.length) { callback && callback(); return; }

      var maxEnd = 0;
      layers.forEach(function (el) {
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
        // Handle slide CSS transition
        if (transition === 'slide' && !newAlreadyVisible) {
          var goingForward = idx > current || (current === total - 1 && idx === 0);
          newSlide.style.transform = goingForward ? 'translateX(100%)' : 'translateX(-100%)';
          newSlide.classList.remove('mps-persistent-visible');
          newSlide.classList.add('mps-active');
          void newSlide.offsetWidth; // reflow
          newSlide.style.transform = 'translateX(0)';
          oldSlide.classList.add(goingForward ? 'mps-exit-left' : '');
          oldSlide.style.transform = goingForward ? 'translateX(-100%)' : 'translateX(100%)';
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

        // Pause/play video bg (skip for persistent — keep playing)
        if (!(oldPersistFor > 0)) pauseVideoBg(oldSlide);
        playVideoBg(newSlide);

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

    function scheduleAutoplay() {
      stopAutoplay();
      if (!cfg.autoplay) return;
      var delay = getSlideDelay(slides[current]);
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
      var img = qs(slide, '.olo-proslider-kenburns');
      if (img) {
        img.style.animation = 'none';
        void img.offsetWidth;
        img.style.animation = '';
      }
    }

    /* ── Initial state ───────────────────────────────────── */
    animateLayersIn(slides[0]);
    playVideoBg(slides[0]);
  }

  /* ── Bootstrap ─────────────────────────────────────────── */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
