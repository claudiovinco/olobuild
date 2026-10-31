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
  // Anche quando cambia solo la larghezza del contenitore (una colonna che si allarga al passaggio): la tela
  // segue fotogramma per fotogramma.
  if ('ResizeObserver' in window) {
    var _mpsLargo = container.offsetWidth, _mpsRo = null;
    new ResizeObserver(function () {
      if (container.offsetWidth === _mpsLargo || _mpsRo) return;
      _mpsRo = requestAnimationFrame(function () { _mpsRo = null; _mpsLargo = container.offsetWidth; updateLayerScale(); });
    }).observe(container);
  }
  var _mpsResizeTimer;
  window.addEventListener('resize', function () {
    clearTimeout(_mpsResizeTimer);
    _mpsResizeTimer = setTimeout(updateLayerScale, 30);
  });

  /* ── Layer animations ────────────────────────────────── */
  var menoMoto = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Maschera d'ingresso (data-mask): il clip-path va dal riquadro impostato (--mps-m) a tutto il livello, insieme
  // all'ingresso e con la sua durata, ritardo e curva.
  function mascheraIngresso(el, dur, easing, delay) {
    return el.getAttribute('data-mask') === '1' ? 'mpsMaskReveal ' + dur + ' ' + easing + ' ' + delay + ' both' : '';
  }

  // Percorso di movimento (data-mp sul contenitore .mps-mp): il tracciato SVG si campiona in 64 punti che
  // diventano keyframe di translate dal primo punto (il livello parte da dove è posato), con la rotazione lungo
  // il percorso se chiesta. Web Animations sul contenitore interno: l'ingresso resta sul livello.
  function puntiPercorso(d, ruota) {
    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    svg.setAttribute('width', '0');
    svg.setAttribute('height', '0');
    svg.style.cssText = 'position:absolute;width:0;height:0;overflow:hidden';
    path.setAttribute('d', d);
    svg.appendChild(path);
    document.body.appendChild(svg);
    var frames = [];
    try {
      var len = path.getTotalLength();
      if (len > 0) {
        var p0 = path.getPointAtLength(0);
        var N = 64;
        for (var i = 0; i <= N; i++) {
          var l = len * i / N;
          var p = path.getPointAtLength(l);
          var t = 'translate(' + (p.x - p0.x).toFixed(2) + 'px,' + (p.y - p0.y).toFixed(2) + 'px)';
          if (ruota) {
            var a = path.getPointAtLength(Math.max(0, l - 0.5));
            var b = path.getPointAtLength(Math.min(len, l + 0.5));
            t += ' rotate(' + (Math.atan2(b.y - a.y, b.x - a.x) * 180 / Math.PI).toFixed(1) + 'deg)';
          }
          frames.push({ transform: t, offset: i / N });
        }
      }
    } catch (_) { frames = []; }
    svg.remove();
    return frames;
  }
  function avviaPercorso(el, delay) {
    if (menoMoto) return;
    qsa(el, '.mps-mp').forEach(function (mp) {
      if (mp._mpsAnim) { try { mp._mpsAnim.cancel(); } catch (_) {} mp._mpsAnim = null; }
      if (!mp.animate) return;
      var c;
      try { c = JSON.parse(mp.getAttribute('data-mp') || '{}'); } catch (_) { return; }
      if (!mp._mpsFrames) mp._mpsFrames = puntiPercorso(c.p || '', !!c.r);
      if (mp._mpsFrames.length < 2) return;
      mp._mpsAnim = mp.animate(mp._mpsFrames, {
        duration: c.d || 3000,
        iterations: c.l ? Infinity : 1,
        easing: c.e || 'linear',
        delay: parseInt(delay, 10) || 0,
        fill: 'both',
      });
    });
  }
  function fermaPercorso(slide) {
    qsa(slide, '.mps-mp').forEach(function (mp) {
      if (mp._mpsAnim) { try { mp._mpsAnim.pause(); } catch (_) {} }
    });
  }

  // Ken Burns del livello immagine: riparte a ogni ingresso della slide
  function riavviaKenBurnsLivello(el) {
    qsa(el, '.mps-lkb').forEach(function (img) {
      img.style.animation = 'none';
      void img.offsetWidth;
      img.style.animation = '';
    });
  }

  function animateLayersIn(slide) {
    var layers = qsa(slide, '.olo-proslider-layer');
    layers.forEach(function (el) {
      riavviaKenBurnsLivello(el);
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
        // «Fine con la slide» spento: finita la timeline il livello esce, senza aspettare la fine della slide
        if (el._mpsFine) { clearTimeout(el._mpsFine); el._mpsFine = null; }
        if (el.getAttribute('data-timeline-end') === '0' && tlLoop !== 'infinite') {
          el._mpsFine = setTimeout(function () {
            el.style.animation = 'mpsFadeOut 400ms ease both';
            el.classList.remove('mps-anim-visible');
          }, (parseInt(tlDur, 10) || 3000) + (parseInt(tlDelay, 10) || 0));
        }
        avviaPercorso(el, 0);
        return;
      }

      // Classic animation
      var animKey = el.getAttribute('data-anim-in') || 'fadeIn';

      el.style.animation = 'none';
      el.classList.remove('mps-anim-visible');
      // Force reflow
      void el.offsetWidth;

      if (animKey === 'none') {
        // No animation — show immediately (la maschera d'ingresso, se c'è, agisce lo stesso)
        el.style.animation = mascheraIngresso(el, (parseInt(el.getAttribute('data-anim-in-duration'), 10) || 800) + 'ms', el.getAttribute('data-anim-easing') || 'ease', (parseInt(el.getAttribute('data-anim-in-delay'), 10) || 0) + 'ms');
        el.classList.add('mps-anim-visible');
        avviaPercorso(el, 0);
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
          el.style.animation = mascheraIngresso(el, dur, easing, delay);
          el.classList.add('mps-anim-visible');
          // Partenza personalizzata (mpsCharCustom, misure dal livello) o l'ingresso del livello; yoyo: andata e
          // ritorno senza fine. Il ritardo di ogni lettera (scritto dal PHP) si legge UNA volta e si somma a
          // quello del livello: lo shorthand animation lo azzerava e le lettere entravano tutte insieme.
          var nomeLettere = el.getAttribute('data-char-custom') === '1' ? 'mpsCharCustom' : name;
          var giro = el.getAttribute('data-char-yoyo') === '1' ? ' infinite alternate' : '';
          var base = parseInt(delay, 10) || 0;
          chars.forEach(function (ch) {
            if (ch._mpsD === undefined) ch._mpsD = parseInt(ch.style.animationDelay, 10) || 0;
            ch.style.opacity = '0';
            ch.style.animation = 'none';
            void ch.offsetWidth;
            ch.style.animation = nomeLettere + ' ' + dur + ' ' + easing + ' ' + (base + ch._mpsD) + 'ms' + giro + ' both';
          });
          avviaPercorso(el, delay);
          return;
        }
      }

      var maschera = mascheraIngresso(el, dur, easing, delay);
      el.style.animation = name + ' ' + dur + ' ' + easing + ' ' + delay + ' both' + (maschera ? ', ' + maschera : '');
      el.classList.add('mps-anim-visible');
      avviaPercorso(el, delay);

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
    // il percorso si ferma dov'è mentre il livello esce (riparte da capo al prossimo ingresso)
    fermaPercorso(slide);
    layers.forEach(function (el) { if (el._mpsFine) { clearTimeout(el._mpsFine); el._mpsFine = null; } });
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
  // Il cubo gira per tutta la durata: un altro cambio a metà rotazione partirebbe da una faccia storta.
  var cuboInGiro = false;
  var allaFineCubo = [];
  var verso = 0;
  var cubo = transition === 'cubeH' || transition === 'cubeV';
  // Il cubo che segue lo scorrimento (scrollScrub, 31 ott 2026): niente transizioni a tempo, l'angolo lo dà la pagina.
  var cuboAScorrimento = false;
  // La timeline a scorrimento a passi: un cambio chiesto da punti, linguette o frecce porta la pagina alla diapositiva.
  var scorriAPosto = null;
  var daScorrimento = false;
  var scorriAFaccia = function () {};
  // Scorrimenti: asse, verso andando avanti (1 = la nuova entra da destra o dal basso), la vecchia esce spinta.
  var SCORRIMENTI = {
    slide: ['X', 1, true], push: ['X', 1, true], pushRight: ['X', -1, true], slideOver: ['X', 1, false],
    pushDown: ['Y', -1, true], slideDown: ['Y', -1, true], slideUp: ['Y', 1, true],
  };
  function profonditaCubo() {
    var tr = qs(container, '.olo-proslider-track') || container;
    var lato = transition === 'cubeV' ? tr.offsetHeight : tr.offsetWidth;
    if (lato > 0) container.style.setProperty('--mps-cube-d', (lato / 2) + 'px');
  }
  if (cubo) profonditaCubo();

  function goToSlide(idx) {
    if (cuboAScorrimento) { scorriAFaccia(idx); return; }
    if (scorriAPosto && !daScorrimento) { scorriAPosto(idx); return; }
    if (animating || cuboInGiro || idx === current || total <= 1) return;
    animating = true;
    var versoChiesto = verso;
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
      var needs3D = ['flipH', 'flipV', 'cubeH', 'cubeV', 'curtain3D'].indexOf(transition) !== -1;
      var durata = cfg.transDuration || 800;

      // Il verso viene da chi ha chiesto il cambio (frecce, swipe, autoplay): dall'ultima alla prima col
      // loop è un passo avanti, dalla prima all'ultima un passo indietro. Schede, miniature e punti: conta
      // la posizione.
      var goingForward = versoChiesto !== 0 ? versoChiesto > 0 : idx > current;
      // Scorrimenti (31 ott 2026): la nuova entra dal lato giusto e spinge via la vecchia dal lato opposto,
      // al contrario tornando indietro; a fine corsa la vecchia torna al suo posto fuori campo senza
      // transizione. Prima la vecchia perdeva subito la classe d'uscita (spariva o tornava indietro
      // incrociando la nuova) e con «slide» riattraversava lo schermo a fine corsa.
      var sc = SCORRIMENTI[transition];
      if (sc && !newAlreadyVisible && !(oldPersistFor > 0)) {
        var segno = sc[1] * (goingForward ? 1 : -1);
        var tr = function (v) { return 'translate' + sc[0] + '(' + v + '%)'; };
        newSlide.classList.remove('mps-persistent-visible');
        newSlide.classList.add('mps-no-trans');
        newSlide.style.transform = tr(100 * segno);
        newSlide.classList.add('mps-active');
        void newSlide.offsetWidth;
        newSlide.classList.remove('mps-no-trans');
        newSlide.style.transform = tr(0);
        oldSlide.style.transform = sc[2] ? tr(-100 * segno) : tr(0);
        oldSlide.classList.remove('mps-active');
        oldSlide.classList.add('mps-scorre-via');
        setTimeout(function () {
          oldSlide.classList.add('mps-no-trans');
          oldSlide.classList.remove('mps-scorre-via');
          oldSlide.style.transform = '';
          newSlide.style.transform = '';
          void oldSlide.offsetWidth;
          oldSlide.classList.remove('mps-no-trans');
        }, durata + 40);
      } else if (needs3D && !newAlreadyVisible) {
        // Set initial off-screen position for new slide (CSS handles this via default styles)
        newSlide.classList.remove('mps-persistent-visible');
        if (cubo) {
          // La faccia nuova parte dal fianco giusto (destra andando avanti, sinistra tornando indietro) senza
          // transizione, poi gira in posizione.
          profonditaCubo();
          newSlide.classList.remove('mps-exit-left', 'mps-exit-right');
          newSlide.classList.add('mps-no-trans', goingForward ? 'mps-from-next' : 'mps-from-prev');
          void newSlide.offsetWidth;
          newSlide.classList.remove('mps-no-trans', 'mps-from-next', 'mps-from-prev');
        }
        newSlide.classList.add('mps-active');
        void newSlide.offsetWidth; // reflow
        // Add exit class for CSS-driven 3D transitions
        oldSlide.classList.add(cubo && !goingForward ? 'mps-exit-right' : 'mps-exit-left');
      } else {
        newSlide.classList.remove('mps-persistent-visible');
        newSlide.classList.add('mps-active');
      }

      // Old slide: persistent -> keep visible underneath; otherwise hide
      if (oldPersistFor > 0) {
        oldSlide.classList.remove('mps-active');
        oldSlide.classList.add('mps-persistent-visible');
        persistTracker[current] = oldPersistFor;
      } else if (cubo && !newAlreadyVisible) {
        // La faccia uscente resta visibile sul fianco finché il cubo non ha finito di girare, poi torna
        // nascosta senza transizione (altrimenti rigirerebbe dietro a quella nuova).
        oldSlide.classList.remove('mps-active');
        cuboInGiro = true;
        setTimeout(function () {
          cuboInGiro = false;
          allaFineCubo.forEach(function (fn) { fn(); });
          if (oldSlide.classList.contains('mps-active')) return;
          oldSlide.classList.add('mps-no-trans');
          oldSlide.classList.remove('mps-exit-left', 'mps-exit-right');
          void oldSlide.offsetWidth;
          oldSlide.classList.remove('mps-no-trans');
        }, (cfg.transDuration || 800) + 40);
      } else {
        oldSlide.classList.remove('mps-active');
        if (needs3D && !newAlreadyVisible) {
          // Flip e sipario: la faccia uscente finisce di girare con la classe d'uscita, poi torna al suo posto
          // senza transizione. Prima la classe se ne andava nello stesso istante in cui arrivava.
          setTimeout(function () {
            if (oldSlide.classList.contains('mps-active')) return;
            oldSlide.classList.add('mps-no-trans');
            oldSlide.classList.remove('mps-exit-left');
            void oldSlide.offsetWidth;
            oldSlide.classList.remove('mps-no-trans');
          }, durata + 40);
        } else {
          oldSlide.classList.remove('mps-exit-left');
        }
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
    verso = 1;
    goToSlide(idx);
    verso = 0;
  }

  function prev() {
    var idx = current - 1;
    if (idx < 0) idx = cfg.loop ? total - 1 : 0;
    verso = -1;
    goToSlide(idx);
    verso = 0;
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

  // La riproduzione automatica corre solo con lo slider nella finestra (e non col puntatore sopra, se chiesto):
  // prima partiva al caricamento e chi scorreva fino a uno slider più in basso lo trovava già avanti di qualche
  // slide. L'avvio vero è in fondo (primoIngresso, all'ingresso nella finestra).
  var inVista = false;
  var sopra = false;
  function riprendiAutoplay() {
    if (cfg.autoplay && inVista && !(cfg.pauseOnHover && sopra)) startAutoplay();
  }
  if (cfg.autoplay && cfg.pauseOnHover) {
    container.addEventListener('mouseenter', function () { sopra = true; stopAutoplay(); });
    container.addEventListener('mouseleave', function () { sopra = false; riprendiAutoplay(); });
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

  /* ── Parallax (mouse move / scroll) ──────────────────── */
  // Due sorgenti: la profondità numerica del livello (agisce con la parallasse dello slider accesa, col tipo e
  // l'intensità dello slider) e la parallasse del livello (oggetto dell'editor: tipo, profondità 1-20,
  // inclinazione 3D, velocità di scorrimento), che agisce da sola. Prima l'oggetto si salvava e nessuno lo
  // leggeva. Mouse e scorrimento scrivono lo stesso transform: ogni livello tiene le parti e le somma.
  var pLivelli = [];
  qsa(container, '.olo-proslider-layer').forEach(function (pl) {
    var og = null;
    var grezzo = pl.getAttribute('data-lpx');
    if (grezzo) { try { og = JSON.parse(grezzo); } catch (_) { og = null; } }
    if (og) {
      pLivelli.push({ el: pl, tipo: og.t || 'mouse', prof: og.d || 5, k: 4, tilt: !!og.r, vel: og.s || 0.5, mx: 0, my: 0, sy: 0, rx: 0, ry: 0 });
    } else if (cfg.parallax) {
      var prof = parseInt(pl.getAttribute('data-parallax-depth'), 10) || 0;
      if (prof > 0) pLivelli.push({ el: pl, tipo: cfg.parallaxType || 'mouse', prof: prof, k: cfg.parallaxIntensity || 5, tilt: false, vel: 1, mx: 0, my: 0, sy: 0, rx: 0, ry: 0 });
    }
  });
  if (pLivelli.length) {
    var scriviLivello = function (st) {
      var x = st.mx, y = st.my + st.sy;
      var t = (x || y) ? 'translate(' + x.toFixed(1) + 'px, ' + y.toFixed(1) + 'px)' : '';
      if (st.tilt && (st.rx || st.ry)) t += ' perspective(900px) rotateX(' + st.rx.toFixed(2) + 'deg) rotateY(' + st.ry.toFixed(2) + 'deg)';
      st.el.style.transform = t;
    };
    var colMouse = pLivelli.filter(function (st) { return st.tipo === 'mouse' || st.tipo === 'both'; });
    var colScorrimento = pLivelli.filter(function (st) { return st.tipo === 'scroll' || st.tipo === 'both'; });

    if (colMouse.length) {
      container.addEventListener('mousemove', function (e) {
        var rect = container.getBoundingClientRect();
        var mx = (e.clientX - rect.left) / rect.width - 0.5;
        var my = (e.clientY - rect.top) / rect.height - 0.5;
        colMouse.forEach(function (st) {
          st.mx = mx * st.prof * st.k;
          st.my = my * st.prof * st.k;
          if (st.tilt) {
            // il livello si inclina verso il puntatore, di più quanto più è profondo
            st.ry = mx * st.prof * 2.4;
            st.rx = -my * st.prof * 2.4;
          }
          scriviLivello(st);
        });
      });
      container.addEventListener('mouseleave', function () {
        colMouse.forEach(function (st) {
          st.mx = 0; st.my = 0; st.rx = 0; st.ry = 0;
          st.el.style.transition = 'transform 0.5s ease-out';
          scriviLivello(st);
          setTimeout(function () { st.el.style.transition = ''; }, 500);
        });
      });
    }

    if (colScorrimento.length) {
      var aggiornaScorrimento = function () {
        var rect = container.getBoundingClientRect();
        var viewH = window.innerHeight;
        if (rect.bottom < 0 || rect.top > viewH) return;
        var progress = (viewH - rect.top) / (viewH + rect.height);
        var scrollOffset = (progress - 0.5) * 2;
        colScorrimento.forEach(function (st) {
          // scendendo nella pagina, più alta è la profondità del livello più in fretta sale: sembra più vicino, davanti allo sfondo
          st.sy = -scrollOffset * st.prof * st.k * st.vel;
          scriviLivello(st);
        });
      };
      window.addEventListener('scroll', aggiornaScorrimento, { passive: true });
      aggiornaScorrimento();
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
      var cardScelta = 0, cardAlCentro = 0;
      // Con «Ripeti» e almeno tre card il carosello gira in tondo: la card scelta sta in mezzo al binario e le altre
      // attorno (order), metà prima e metà dopo. Prima era una fila: alla prima card la sinistra restava vuota e
      // dall'ultima alla prima il binario tornava indietro attraverso tutte le card.
      var giro = !!cfg.loop && slides.length >= 3;
      var posizione = function (i, idx) {
        if (!giro) return i - idx;
        var n = slides.length, d = ((i - idx) % n + n) % n;
        return d > Math.floor((n - 1) / 2) ? d - n : d;
      };
      var centraCard = function (idx, subito) {
        var s = slides[idx];
        if (!s) return;
        // prospettiva del 3D proporzionale alla card: lo stesso scorcio sul computer e sul telefono
        container.style.setProperty('--mps-prosp', Math.round(s.offsetWidth * 1.6) + 'px');
        if (giro) {
          // Il riordino sposta le card sul binario: il binario si sposta al contrario di quanto si è mossa la card
          // che era al centro, senza transizione, così niente salta; poi scorre verso la card scelta.
          var ancora = slides[cardAlCentro], primaA = ancora.offsetLeft;
          slides.forEach(function (sl, i) { sl.style.order = String(posizione(i, idx) + slides.length); });
          var spostata = ancora.offsetLeft - primaA;
          if (spostata && !subito) {
            var ora = new DOMMatrixReadOnly(getComputedStyle(track).transform).m41;
            track.style.transition = 'none';
            track.style.transform = 'translateX(' + (ora - spostata).toFixed(1) + 'px)';
            void track.offsetWidth;
            track.style.transition = '';
          }
        }
        cardAlCentro = idx;
        var centro = s.offsetLeft + s.offsetWidth / 2;
        if (s.offsetParent !== track) centro -= track.offsetLeft;
        if (subito) track.style.transition = 'none';
        track.style.transform = 'translateX(' + (container.clientWidth / 2 - centro).toFixed(1) + 'px)';
        if (subito) { void track.offsetWidth; track.style.transition = ''; }
        slides.forEach(function (sl, i) {
          var p = posizione(i, idx);
          sl.classList.toggle('mps-carousel-active', p === 0);
          sl.classList.toggle('mps-carousel-prima', p < 0);
          sl.classList.toggle('mps-carousel-dopo', p > 0);
          // distanza dalla card al centro: le card di lato, più piccole, si avvicinano di quanto si sono
          // ristrette (CSS), così fra una card e l'altra resta il Gap impostato
          sl.style.setProperty('--mps-d', String(Math.abs(p)));
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
    // Il cubo che segue lo scorrimento: la faccia i esce di lato e la i+1 entra dal lato opposto, ruotate della
    // frazione di pagina scorsa fra le due (come le classi del cubo a tempo: avanti la nuova arriva da destra, o dal
    // basso); il lato in ombra si scurisce. La faccia più vicina è quella «attiva»: i suoi livelli entrano, il
    // suo video va, linguette e punti la segnano. Frecce, linguette e punti scorrono la pagina fino a lei.
    if (wrapper && cfg.scrollScrub && cubo && total > 1 && !menoMoto) {
      cuboAScorrimento = true;
      cfg.autoplay = false;
      container.classList.add('mps-cubo-scorre');
      // Lo scorrimento che fa girare il cubo è il tratto in cui lo slider resta fermo in cima (altezza del contenitore
      // meno quella dello slider): con un'altezza in vh la distanza impostata non torna col contenitore.
      var distCubo = function () { return Math.max(1, wrapper.offsetHeight - container.offsetHeight) || cfg.scrollTimelineDistance || 2000; };
      var asseV = transition === 'cubeV';
      var gira = function (s, gradi, ombra) {
        s.style.transform = asseV ? 'rotateX(' + (-gradi) + 'deg)' : 'rotateY(' + gradi + 'deg)';
        s.style.setProperty('--mps-faccia-ombra', ombra.toFixed(3));
      };
      var arriva = function (idx) {
        var vecchia = slides[current], nuova = slides[idx];
        allaPartenza.forEach(function (fn) { fn(idx); });
        animateLayersOut(vecchia, function () {});
        vecchia.classList.remove('mps-active');
        nuova.classList.add('mps-active');
        handleAudioLayers(vecchia, false);
        handleAudioLayers(nuova, true);
        dots.forEach(function (d, i) { d.classList.toggle('mps-dot-active', i === idx); });
        current = idx;
        animateLayersIn(nuova);
        allArrivo.forEach(function (fn) { fn(idx); });
      };
      var giro = null;
      var segui = function () {
        giro = null;
        var wr = wrapper.getBoundingClientRect();
        if (wr.bottom < 0 || wr.top > window.innerHeight) return;
        var p = Math.max(0, Math.min(1, -wr.top / distCubo())) * (total - 1);
        var i = Math.min(total - 2, Math.floor(p));
        var f = p - i;
        slides.forEach(function (s, k) {
          var su = k === i || k === i + 1;
          s.classList.toggle('mps-faccia', su);
          if (su) { playVideoBg(s); } else { s.style.transform = ''; pauseVideoBg(s); }
        });
        gira(slides[i], -90 * f, 0.55 * f);
        gira(slides[i + 1], 90 * (1 - f), 0.55 * (1 - f));
        var vicina = Math.round(p);
        if (vicina !== current) arriva(vicina);
      };
      var chiedi = function () { if (!giro) giro = requestAnimationFrame(segui); };
      scorriAFaccia = function (idx) {
        idx = Math.max(0, Math.min(total - 1, idx));
        var y = wrapper.getBoundingClientRect().top + window.pageYOffset + distCubo() * idx / (total - 1);
        window.scrollTo({ top: Math.round(y), behavior: 'smooth' });
      };
      window.addEventListener('scroll', chiedi, { passive: true });
      window.addEventListener('resize', function () { profonditaCubo(); chiedi(); });
      chiedi();
    } else if (wrapper) {
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
        if (animating || cuboInGiro) { inSospeso = true; return; }
        inSospeso = false;
        if (targetIdx !== current) { daScorrimento = true; goToSlide(targetIdx); daScorrimento = false; }
      };
      window.addEventListener('scroll', seguiScorrimento, { passive: true });
      allArrivo.push(function () { if (inSospeso) seguiScorrimento(); });
      allaFineCubo.push(function () { if (inSospeso) seguiScorrimento(); });

      // Una diapositiva per gesto (31 ott 2026): con la rotella o il trackpad 600 px passano in un attimo e si saltavano
      // diapositive. Mentre lo slider è fermo in cima un gesto porta alla diapositiva dopo o prima, l'inerzia dello stesso
      // gesto non conta (un gesto nuovo comincia dopo una pausa); alla prima e all'ultima la pagina scorre. Fermandosi
      // (anche col dito) la pagina si posa al centro del tratto della diapositiva.
      if (cfg.scrollStep !== false && total > 1) {
        var cima = function () { return wrapper.getBoundingClientRect().top + window.pageYOffset; };
        var posizione = function (i) { return Math.round(cima() + scrollDist * (i + 0.5) / total); };
        var fermo = function () { var wr = wrapper.getBoundingClientRect(); return wr.top <= 1 && wr.bottom >= window.innerHeight - 1; };
        var mio = false;
        scorriAPosto = function (i) {
          mio = true;
          window.scrollTo({ top: posizione(Math.max(0, Math.min(total - 1, i))), behavior: menoMoto ? 'auto' : 'smooth' });
          setTimeout(function () { mio = false; }, 900);
        };
        var ultimaRuota = 0, gestoUsato = false, bloccoFino = 0;
        window.addEventListener('wheel', function (e) {
          if (e.ctrlKey || !fermo()) return;
          var ora = performance.now();
          if (ora - ultimaRuota > 220) gestoUsato = false;
          ultimaRuota = ora;
          if (gestoUsato) { e.preventDefault(); return; }
          if (Math.abs(e.deltaY) < 2) return;
          var i = bersaglioScorrimento + (e.deltaY > 0 ? 1 : -1);
          if (i < 0 || i > total - 1) return;
          e.preventDefault();
          if (ora < bloccoFino) return;
          gestoUsato = true;
          bloccoFino = ora + (cfg.transDuration || 800) + 200;
          mio = true;
          window.scrollTo({ top: posizione(i), behavior: 'auto' });
          setTimeout(function () { mio = false; }, 200);
        }, { passive: false });
        // Col dito: un trascinamento, una diapositiva; lo slancio non c'è perché il trascinamento è nostro.
        var tocco = null;
        window.addEventListener('touchstart', function (e) {
          tocco = fermo() && e.touches.length === 1 ? { y: e.touches[0].clientY, verso: 0, fatto: false } : null;
        }, { passive: true });
        window.addEventListener('touchmove', function (e) {
          if (!tocco) return;
          var dy = tocco.y - e.touches[0].clientY;
          if (!tocco.verso) {
            if (Math.abs(dy) < 8) return;
            tocco.verso = dy > 0 ? 1 : -1;
            var j = bersaglioScorrimento + tocco.verso;
            if (j < 0 || j > total - 1) { tocco = null; return; }
            tocco.indice = j;
          }
          if (e.cancelable) e.preventDefault();
          if (!tocco.fatto && Math.abs(dy) > 40) {
            tocco.fatto = true;
            mio = true;
            window.scrollTo({ top: posizione(tocco.indice), behavior: 'auto' });
            setTimeout(function () { mio = false; }, 250);
          }
        }, { passive: false });
        window.addEventListener('touchend', function () { tocco = null; }, { passive: true });
        window.addEventListener('touchcancel', function () { tocco = null; }, { passive: true });
        // Entrando nel tratto fermo con lo slancio (non col dito sopra, non per un nostro scorrimento): la pagina si posa
        // sulla diapositiva del bordo da cui si entra, e lo slancio finisce lì.
        var eraFermo = fermo();
        window.addEventListener('scroll', function () {
          var f = fermo();
          if (f && !eraFermo && !tocco && !mio) {
            var j = window.pageYOffset > cima() + scrollDist / 2 ? total - 1 : 0;
            mio = true;
            gestoUsato = true;
            window.scrollTo({ top: posizione(j), behavior: 'auto' });
            setTimeout(function () { mio = false; }, 250);
          }
          eraFermo = f;
        }, { passive: true });
        var posa = null;
        window.addEventListener('scroll', function () {
          clearTimeout(posa);
          posa = setTimeout(function () {
            if (mio || !fermo()) return;
            var y = posizione(bersaglioScorrimento);
            if (Math.abs(window.pageYOffset - y) > 4) {
              mio = true;
              window.scrollTo({ top: y, behavior: menoMoto ? 'auto' : 'smooth' });
              setTimeout(function () { mio = false; }, 700);
            }
          }, 180);
        }, { passive: true });
      }
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

  /* ── Livelli che si muovono con lo scorrimento (scrollMove) ── */
  // L'avanzamento va da 0 a 1: nello slider fermo in cima (scorrimento fissato) è il tratto in cui resta fermo, se no è
  // l'uscita dello slider dalla finestra. Ogni livello lo legge nel suo tratto (inizio, fine) e passa da una tappa
  // all'altra; i valori vanno sulle proprietà singole translate, scale e rotate, che si sommano al transform delle
  // animazioni d'ingresso senza toccarlo, e l'opacità su un filtro. Gli spostamenti sono in px della tela del computer
  // (1440): sulle tele più strette si riducono in proporzione. Con «riduci il movimento» i livelli restano alla prima tappa.
  (function () {
    var mobili = qsa(container, '.olo-proslider-layer[data-scroll-move]').map(function (el) {
      try { return { el: el, d: JSON.parse(el.getAttribute('data-scroll-move')) }; } catch (e) { return null; }
    }).filter(function (m) { return m && m.d && m.d.t && m.d.t.length > 1; });
    if (!mobili.length) return;
    // la prospettiva per le rotazioni 3D sta sulla tela del livello
    mobili.forEach(function (m) {
      if (m.d.t.some(function (t) { return t.v.rx || t.v.ry; })) {
        var tela = m.el.parentElement;
        if (tela) tela.style.perspective = '1600px';
      }
    });
    var avvolto = container.closest('.mps-scroll-fixed-wrapper');
    function valore(t, k, base) {
      var u = 0;
      for (var i = 0; i < t.length - 1; i++) { if (t[i + 1].p >= k) { u = i; break; } u = i; }
      var a = t[u], b = t[Math.min(u + 1, t.length - 1)];
      var span = Math.max(0.0001, b.p - a.p);
      var f = Math.max(0, Math.min(1, (k - a.p) / span));
      f = f * f * (3 - 2 * f);
      var va = a.v[base], vb = b.v[base];
      var def = base === 's' ? 1 : (base === 'o' ? 100 : 0);
      va = va == null ? def : va; vb = vb == null ? def : vb;
      return va + (vb - va) * f;
    }
    function applica() {
      giro = null;
      var r = (avvolto || container).getBoundingClientRect();
      var vh = window.innerHeight || 800;
      var p;
      if (avvolto) {
        var dist = Math.max(1, avvolto.offsetHeight - container.offsetHeight);
        p = -r.top / dist;
      } else {
        p = -r.top / Math.max(1, r.height);
      }
      if (r.bottom < -vh || r.top > vh * 2) return;
      p = menoMoto ? 0 : Math.max(0, Math.min(1, p)) * 100;
      mobili.forEach(function (m) {
        var d = m.d, k = d.f > d.i ? Math.max(0, Math.min(100, (p - d.i) / (d.f - d.i) * 100)) : (p >= d.i ? 100 : 0);
        var tela = m.el.parentElement;
        var fattore = tela && tela.offsetWidth ? tela.offsetWidth / 1440 : 1;
        var x = valore(d.t, k, 'x') * fattore, y = valore(d.t, k, 'y') * fattore;
        var s = valore(d.t, k, 's'), rz = valore(d.t, k, 'r'), rx = valore(d.t, k, 'rx'), ry = valore(d.t, k, 'ry');
        var o = valore(d.t, k, 'o');
        m.el.style.translate = x.toFixed(1) + 'px ' + y.toFixed(1) + 'px';
        m.el.style.scale = s.toFixed(4);
        m.el.style.rotate = rx ? 'x ' + rx.toFixed(2) + 'deg' : (ry ? 'y ' + ry.toFixed(2) + 'deg' : rz.toFixed(2) + 'deg');
        if (d.t.some(function (t) { return t.v.o != null; })) m.el.style.filter = 'opacity(' + (o / 100).toFixed(3) + ')';
      });
    }
    var giro = null;
    function chiedi() { if (!giro) giro = requestAnimationFrame(applica); }
    window.addEventListener('scroll', chiedi, { passive: true });
    window.addEventListener('resize', chiedi);
    applica();
  })();

  /* ── Initial state ───────────────────────────────────── */
  // I livelli della prima slide entrano quando lo slider arriva nella finestra (almeno un quinto, o mezza
  // finestra se è più alto): prima entravano al caricamento e, per uno slider sotto la piega, l'ingresso
  // finiva prima che lo si vedesse. Fuori dalla finestra si fermano riproduzione automatica e video.
  var avviato = false;
  function primoIngresso() {
    if (avviato) return;
    avviato = true;
    animateLayersIn(slides[current]);
    // I livelli globali (fermi su tutte le slide) entrano una volta sola, con la prima slide: prima nessuno li
    // faceva entrare e restavano invisibili (opacità 0 di ogni livello).
    qsa(container, '.olo-proslider-global-layers').forEach(function (g) { animateLayersIn(g); });
    playVideoBg(slides[current]);
    handleAudioLayers(slides[current], true);
  }
  if ('IntersectionObserver' in window) {
    new IntersectionObserver(function (voci) {
      voci.forEach(function (v) {
        var ora = v.isIntersecting && (v.intersectionRatio >= 0.2 || v.intersectionRect.height >= window.innerHeight * 0.5);
        if (ora === inVista) return;
        inVista = ora;
        if (inVista) {
          if (avviato) { playVideoBg(slides[current]); } else { primoIngresso(); }
          riprendiAutoplay();
        } else if (avviato) {
          stopAutoplay();
          pauseVideoBg(slides[current]);
        }
      });
    }, { threshold: [0, 0.2, 0.5] }).observe(container);
  } else {
    inVista = true;
    primoIngresso();
    riprendiAutoplay();
  }
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
