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
  windLoop:       'mpsWindLoop',
  flickerLoop:    'mpsFlickerLoop',
  wanderLoop:     'mpsWanderLoop',
  driftLoop:      'mpsDriftLoop',
};

/* ── Helpers ───────────────────────────────────────────── */
function qsa(parent, sel) { return Array.from(parent.querySelectorAll(sel)); }
function qs(parent, sel)  { return parent.querySelector(sel); }

// I fotogrammi di un'animazione del foglio di stile (@keyframes mpsFadeOutDown…), letti una volta: servono all'uscita dei
// livelli con la linea del tempo, che si compone col transform del momento (1.4.1472). null se il foglio non si legge.
var _mpsFotogrammi = {};
function fotogrammiCss(nome) {
  if (Object.prototype.hasOwnProperty.call(_mpsFotogrammi, nome)) return _mpsFotogrammi[nome];
  var regola = null;
  var fogli = document.styleSheets || [];
  for (var i = 0; i < fogli.length && !regola; i++) {
    var regole;
    try { regole = fogli[i].cssRules; } catch (_) { continue; } // foglio di un altro dominio
    if (!regole) continue;
    for (var j = 0; j < regole.length; j++) {
      if (regole[j].type === 7 && regole[j].name === nome) { regola = regole[j]; break; }
    }
  }
  var out = null;
  if (regola) {
    out = [];
    for (var k = 0; k < regola.cssRules.length; k++) {
      var kf = regola.cssRules[k];
      kf.keyText.split(',').forEach(function (p) {
        p = p.trim();
        out.push({
          offset: p === 'from' ? 0 : (p === 'to' ? 1 : parseFloat(p) / 100),
          transform: kf.style.transform || '', opacity: kf.style.opacity || '',
          clipPath: kf.style.clipPath || '', filter: kf.style.filter || '',
        });
      });
    }
    out = out.filter(function (f) { return f.offset >= 0 && f.offset <= 1; }).sort(function (a, b) { return a.offset - b.offset; });
    if (out.length < 2) out = null;
  }
  _mpsFotogrammi[nome] = out;
  return out;
}

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

  // Fermo in cima mentre la pagina scorre (timeline a scorrimento) lo slider è la scena che resta davanti per tutto il
  // tratto: è alto almeno quanto lo schermo. In 16:9 su un tablet in verticale occupava 461 px dei 1180 e sotto restava
  // il vuoto finché le diapositive non erano passate tutte (2 nov 2026). La composizione del formato sta al centro
  // dell'altezza in più; i livelli che la coprono tutta (fondi, teli, foto a piena slide) si allungano fino ai bordi.
  var scenaFissa = cfg.scrollTimeline && !cfg.carousel ? container.closest('.mps-scroll-fixed-wrapper') : null;
  var sondaSchermo = null;
  function altezzaSchermo() {
    // 100lvh, la finestra con le barre del browser nascoste: mentre compaiono e spariscono lo slider resta com'è.
    if (!sondaSchermo) {
      sondaSchermo = document.createElement('div');
      sondaSchermo.setAttribute('aria-hidden', 'true');
      sondaSchermo.style.cssText = 'position:fixed;top:0;left:0;width:0;height:100vh;height:100lvh;visibility:hidden;pointer-events:none;';
      document.body.appendChild(sondaSchermo);
    }
    return Math.max(sondaSchermo.offsetHeight, window.innerHeight || 0);
  }
  var stiliDiPartenza = new Map(); // livello → top e height in linea di prima che la scena li allungasse o spostasse
  function frazione(v, rif) { // top/height calcolati: in px se il livello è disegnato, se no la percentuale scritta
    var n = parseFloat(v) || 0;
    return /%$/.test(v) ? n / 100 : n / Math.max(1, rif);
  }
  function scenaAPieno(altezza, finalH, scarto, scaleX) {
    stiliDiPartenza.forEach(function (st, el) { el.style.top = st.top; el.style.height = st.height; });
    stiliDiPartenza.clear();
    if (altezza <= finalH) return;
    var h0 = finalH / scaleX, H = altezza / scaleX, s0 = scarto / scaleX;
    var segna = function (el) { stiliDiPartenza.set(el, { top: el.style.top, height: el.style.height }); };
    // Dove sta il livello rispetto ai bordi della tela: 'pieno' li tocca tutti e due; 'su' e 'giu' sono i fili e le
    // fasce sottili su un bordo (il bastone della tenda resta in cima). Quello che è disegno, anche se tocca un bordo
    // (il vaso di Clorofilla), resta con la composizione, se no si staccava dalla pianta.
    var bordo = function (t, h) {
      if (t <= 0.02 && t + h >= 0.98) return 'pieno';
      if (h > 0.06) return '';
      // Un livello che parte fuori dalla tela (sopra o sotto) non è un filo sul bordo: è un pezzo che entra con lo
      // scorrimento e resta con la composizione. I pulsanti sullo scontrino ancora dentro la stampante (Scontrino) si
      // staccavano dalla carta e finivano sulle righe, a 320 e 360 px dove lo schermo è più alto dello slider (20 nov 2026).
      if (t < -0.03 || t > 1.02) return '';
      if (t <= 0.02) return 'su';
      return t + h >= 0.98 ? 'giu' : '';
    };
    // Un velo (un livello vuoto che è solo una sfumatura o un colore trasparente) attaccato al bordo alto o basso si
    // allunga fino al bordo della scena. Il velo in cima che fa leggere il menu (Elastan) partiva dalla tela, centrata
    // nell'altezza in più: a una finestra più alta del 16:9 restava sopra una fascia chiara alta quanto lo scarto, con
    // la testata sopra il video senza velo (18 nov 2026).
    // (Un livello forma ha dentro un div vuoto: conta che non abbia testo né media.)
    var velo = function (el, cs) {
      if ((el.textContent || '').trim() || el.querySelector('img, video, svg, canvas, picture, iframe, object')) return false;
      var dentro = el.children.length === 1 ? getComputedStyle(el.children[0]) : null;
      var fondo = function (s) {
        if (/gradient\(/.test(s.backgroundImage)) return true;
        var m = /rgba\([^)]*,\s*([\d.]+)\)/.exec(s.backgroundColor);
        return !!m && parseFloat(m[1]) > 0 && parseFloat(m[1]) < 1;
      };
      // Una forma con gli angoli tondi è disegno, non velo (il vaso di Clorofilla perdeva il fondo arrotondato).
      var tondo = function (s) {
        return ['borderTopLeftRadius', 'borderTopRightRadius', 'borderBottomLeftRadius', 'borderBottomRightRadius']
          .some(function (k) { return parseFloat(s[k]) > 0; });
      };
      if (tondo(cs) || (dentro && tondo(dentro))) return false;
      return fondo(cs) || (!!dentro && fondo(dentro));
    };
    // Un cerchio (angoli tondi almeno quanto metà del lato più corto) che copre la tela resta un cerchio: allungato
    // nell'altezza in più diventava un ovale (le lenti di Vetrino, più grandi della tela). Non si allunga: resta al
    // centro con la composizione, e la scena in più la copre lui stesso, o quello che c'è sotto (18 nov 2026).
    var cerchio = function (cs) {
      var r = cs.borderTopLeftRadius || '';
      if (/%/.test(r)) return parseFloat(r) >= 50;
      return parseFloat(r) > 0 && parseFloat(r) >= Math.min(parseFloat(cs.width) || 0, parseFloat(cs.height) || 0) / 2 - 1;
    };
    layerWraps.forEach(function (w) {
      qsa(w, ':scope > .olo-proslider-layer, :scope > .mps-stato > .olo-proslider-layer').forEach(function (el) {
        if (el.hasAttribute('data-timeline-name')) return; // in alto a sinistra per regola: lo posa la sua timeline
        var cs = getComputedStyle(el), t = frazione(cs.top, h0), hh = frazione(cs.height, h0), b = bordo(t, hh);
        if (b === 'pieno' && cerchio(cs)) return;
        if (!b && velo(el, cs)) b = t <= 0.02 ? 'velo-su' : (t + hh >= 0.98 ? 'velo-giu' : '');
        if (!b) return;
        segna(el);
        if (b === 'velo-su') {
          el.style.setProperty('top', (t * h0 - s0).toFixed(1) + 'px', 'important');
          el.style.setProperty('height', (hh * h0 + s0).toFixed(1) + 'px', 'important');
        } else if (b === 'velo-giu') {
          el.style.setProperty('height', (hh * h0 + s0).toFixed(1) + 'px', 'important');
        } else if (b === 'pieno') {
          el.style.setProperty('top', (t * H - s0).toFixed(1) + 'px', 'important');
          el.style.setProperty('height', (hh * H).toFixed(1) + 'px', 'important');
        } else {
          el.style.setProperty('top', (t * h0 + (b === 'su' ? -s0 : s0)).toFixed(1) + 'px', 'important');
        }
      });
    });
    // I livelli con fusione stanno nella slide, fuori dalla tela, in percentuale della slide: quelli che la coprono si
    // allungano da soli, gli altri tornano dove li mette la composizione (in cima, in fondo o al centro).
    var rif = container.offsetHeight;
    qsa(container, '.mps-blend-layer').forEach(function (el) {
      if (el.hasAttribute('data-timeline-name')) return;
      var cs = getComputedStyle(el), t = frazione(cs.top, rif), hh = frazione(cs.height, rif), b = bordo(t, hh);
      if (b === 'pieno') return;
      segna(el);
      el.style.setProperty('top', (t * finalH + (b === 'su' ? 0 : b === 'giu' ? 2 * scarto : scarto)).toFixed(1) + 'px', 'important');
      if (/%$/.test(el.style.height)) el.style.setProperty('height', (parseFloat(el.style.height) / 100 * finalH).toFixed(1) + 'px', 'important');
    });
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
    var altezza = scenaFissa && !schermoPieno ? Math.max(finalH, altezzaSchermo()) : finalH;
    var scarto = Math.round((altezza - finalH) / 2);
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
      w.style.top = scarto + 'px';
      w.style.transform = 'scale(' + scaleX + ')';
    });
    if (scenaFissa) scenaAPieno(altezza, finalH, scarto, scaleX);
    // I livelli con fusione stanno fuori dalla tela scalata (il transform isolerebbe la fusione): carattere,
    // spaziatura e padding si moltiplicano per --mps-k, che dentro la tela vale 1 (CSS). Prima il carattere
    // restava quello di progetto e sul telefono un titolo di 120 usciva dallo slider.
    container.style.setProperty('--mps-k', String(scaleX));
    // La scala anche per il carattere minimo leggibile (olo-proslider.css --mps-den, PHP carattere_leggibile):
    // --mps-k dentro la tela torna 1, questa no.
    container.style.setProperty('--mps-scala', String(scaleX));
    if (!schermoPieno) {
      // Più alta del formato vince sulle regole per formato del PHP (aspect-ratio e height:auto con !important).
      if (altezza > finalH) {
        container.style.setProperty('height', altezza + 'px', 'important');
      } else {
        container.style.removeProperty('height');
        container.style.height = finalH + 'px';
      }
    }
    // Il tratto in cui lo slider resta fermo è la distanza impostata su ogni schermo: il contenitore che scorre era alto
    // quanto lo slider del computer più la distanza, e dove lo slider era più basso restava fermo più a lungo.
    if (scenaFissa) scenaFissa.style.height = (container.offsetHeight + (cfg.scrollTimelineDistance || 2000)) + 'px';
    if (cubo) profonditaCubo();
    distanziaPulsanti(scaleX);
  }

  // Pulsanti e link di una slide mai attaccati né accavallati (24 nov 2026, Torchio: il riquadro rosso di «La stamperia»
  // copriva il bordo di «Sfoglia il campionario»). Stanno a percentuale ma sono larghi quanto la scritta, e la scritta
  // cambia: il carattere del tema, la traduzione (in tedesco è più lunga), il carattere minimo leggibile di tablet e
  // telefono. Sulla tela già composta, quello a destra (in una fila) o sotto (in una pila) si sposta quanto basta a
  // lasciare uno spazio; a ogni ricomposizione si riparte dalla posizione del progetto. Solo livelli con un link: due
  // scritte o due forme sovrapposte di proposito restano come sono.
  var _mpsScalaPulsanti = 1;
  // Un livello con un link, figlio della tela o di uno «stato» della tela (gruppi di livelli che compaiono a comando).
  function pulsanteDellaTela(el, w) {
    var p = el.parentElement;
    return (p === w || (p && p.parentElement === w && p.classList.contains('mps-stato'))) &&
      !el.hasAttribute('data-timeline-name') && !!el.querySelector(':scope > a, :scope > .mps-mp > a');
  }
  // Ogni coppia troppo vicina si separa dove costa meno: a destra (in fila) o in basso (in pila). Mai più di quanto è
  // grande il pulsante (oltre, la sovrapposizione è voluta); se a destra uscirebbe dalla tela, va indietro l'altro.
  function separaGruppo(ls, tela, spazio) {
    for (var giro = 0; giro < 3; giro++) {
      var mosso = false;
      for (var i = 0; i < ls.length; i++) {
        for (var j = 0; j < ls.length; j++) {
          if (i === j) continue;
          var a = ls[i], b = ls[j];
          var aL = a.x + a.dx, aT = a.y + a.dy, bL = b.x + b.dx, bT = b.y + b.dy;
          var vx = Math.min(aL + a.w, bL + b.w) - Math.max(aL, bL);
          var vy = Math.min(aT + a.h, bT + b.h) - Math.max(aT, bT);
          if (vx <= 0.5 - spazio || vy <= 0.5 - spazio || (vx <= 0 && vy <= 0)) continue;
          var nx = vy > 0 && bL >= aL ? aL + a.w + spazio - bL : Infinity;
          var ny = vx > 0 && bT >= aT ? aT + a.h + spazio - bT : Infinity;
          if (nx === Infinity && ny === Infinity) continue;
          if (nx <= ny) {
            if (nx <= 0.5 || nx > Math.min(a.w, b.w)) continue;
            if (bL + nx + b.w > tela - 8) {
              if (aL - nx < 8) continue;
              a.dx -= nx;
            } else {
              b.dx += nx;
            }
          } else {
            if (ny <= 0.5 || ny > Math.min(a.h, b.h) + spazio) continue;
            b.dy += ny;
          }
          mosso = true;
        }
      }
      if (!mosso) break;
    }
  }
  function distanziaPulsanti(scala) {
    if (scala) _mpsScalaPulsanti = scala;
    var spazio = Math.max(16, 12 / (_mpsScalaPulsanti || 1));
    layerWraps.forEach(function (w) {
      var tela = w.offsetWidth;
      var gruppi = [], fermi = [];
      // I pulsanti hanno la transizione dell'effetto al passaggio (spesso «all»): animerebbe anche il margine, e la
      // misura cadrebbe a metà strada. Ferma durante misura e spostamento, poi torna com'era.
      qsa(w, '.olo-proslider-layer').forEach(function (el) {
        var candidato = pulsanteDellaTela(el, w);
        if (!candidato && !el.hasAttribute('data-mps-sp')) return;
        fermi.push([el, el.style.transition]);
        el.style.transition = 'none';
        if (el.hasAttribute('data-mps-sp')) { el.style.marginLeft = ''; el.style.marginTop = ''; el.removeAttribute('data-mps-sp'); }
        if (!candidato || el.offsetWidth < 2 || el.offsetHeight < 2) return;
        // Un gruppo per ciò che si vede insieme: i livelli della tela fra loro, quelli dello stesso stato (nome e valore:
        // ogni livello ha il suo involucro) fra loro.
        var p = el.parentElement;
        var chiave = p === w ? '' : (p.getAttribute('data-stato') || '') + '=' + (p.getAttribute('data-stato-vale') || '');
        var g = gruppi.filter(function (x) { return x.k === chiave; })[0];
        if (!g) { g = { k: chiave, ls: [] }; gruppi.push(g); }
        g.ls.push({ el: el, x: el.offsetLeft, y: el.offsetTop, w: el.offsetWidth, h: el.offsetHeight, dx: 0, dy: 0 });
      });
      gruppi.forEach(function (g) {
        if (g.ls.length < 2) return;
        separaGruppo(g.ls, tela, spazio);
        g.ls.forEach(function (l) {
          if (!l.dx && !l.dy) return;
          l.el.setAttribute('data-mps-sp', '1');
          if (l.dx) l.el.style.marginLeft = Math.round(l.dx) + 'px';
          if (l.dy) l.el.style.marginTop = Math.round(l.dy) + 'px';
        });
      });
      if (fermi.length) {
        void w.offsetWidth;
        fermi.forEach(function (f) { f[0].style.transition = f[1]; });
      }
    });
  }
  // I caratteri del tema arrivano dopo e le scritte si allargano: si rifà quando un pulsante cambia misura (lo
  // spostamento è un margine, non cambia la misura: niente giri a vuoto), oltre che a caratteri pronti e a pagina caricata.
  // (un timer, non requestAnimationFrame: in una scheda in secondo piano il fotogramma non arriva e lo spostamento
  // restava quello calcolato col carattere di riserva, più stretto)
  if ('ResizeObserver' in window) {
    var _mpsSpT = null;
    var _mpsSpRo = new ResizeObserver(function () {
      if (_mpsSpT) return;
      _mpsSpT = setTimeout(function () { _mpsSpT = null; distanziaPulsanti(); }, 40);
    });
    layerWraps.forEach(function (w) {
      qsa(w, '.olo-proslider-layer').forEach(function (el) {
        if (pulsanteDellaTela(el, w)) _mpsSpRo.observe(el);
      });
    });
  }
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(function () { distanziaPulsanti(); });
  window.addEventListener('load', function () { distanziaPulsanti(); });

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
  // 1.4.1473: «Dimensione del percorso» (k, 1 = 100%) e «Segue la larghezza dello slider» (a: lo spostamento si
  // allarga e si stringe con la tela del formato rispetto a quella del Computer, 1440) moltiplicano lo spostamento;
  // «Ritardo» proprio del percorso (w, ms) si somma a quello dell'ingresso. Gemello: percorso.js dell'editor.
  function fattorePercorso(c) {
    var k = Number(c.k) > 0 ? Number(c.k) : 1;
    return c.a ? k * (formatoCorrente().w || 1440) / 1440 : k;
  }
  function puntiPercorso(d, ruota, fattore) {
    fattore = fattore || 1;
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
          var t = 'translate(' + ((p.x - p0.x) * fattore).toFixed(2) + 'px,' + ((p.y - p0.y) * fattore).toFixed(2) + 'px)';
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
      var fattore = fattorePercorso(c);
      if (!mp._mpsFrames || mp._mpsFattore !== fattore) { mp._mpsFrames = puntiPercorso(c.p || '', !!c.r, fattore); mp._mpsFattore = fattore; }
      if (mp._mpsFrames.length < 2) return;
      mp._mpsAnim = mp.animate(mp._mpsFrames, {
        duration: c.d || 3000,
        iterations: c.l ? Infinity : 1,
        easing: c.e || 'linear',
        delay: (parseInt(delay, 10) || 0) + (parseInt(c.w, 10) || 0),
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
        if (el._mpsUscita) { try { el._mpsUscita.cancel(); } catch (_) {} el._mpsUscita = null; }
        el.style.animationPlayState = '';
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
            esceTimeline(el);
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

  // L'uscita di un livello con la linea del tempo (1.4.1472). Con la sua «Uscita» (data-anim-out: effetto, durata, ritardo,
  // andamento) esce con quella; senza, con la dissolvenza rapida di sempre. La linea del tempo si ferma dov'è (in pausa
  // tiene posizione, scala, filtri, maschera) e l'uscita parte da lì: i fotogrammi dell'effetto si compongono col transform
  // del momento, se no un «Dissolvenza verso il basso» riporterebbe il livello in alto a sinistra (la sua posizione sta
  // nell'animazione della linea del tempo). Restituisce quanto dura.
  function esceTimeline(el) {
    var chiave = el.getAttribute('data-anim-out');
    if (!chiave) {
      el.style.animation = 'mpsFadeOut 400ms ease both';
      el.classList.remove('mps-anim-visible');
      return 400;
    }
    if (chiave === 'none') {
      el.style.animation = 'none';
      el.classList.remove('mps-anim-visible');
      return 0;
    }
    var dur = parseInt(el.getAttribute('data-anim-out-duration'), 10);
    if (isNaN(dur)) dur = 600;
    var rit = parseInt(el.getAttribute('data-anim-out-delay'), 10) || 0;
    var curva = el.getAttribute('data-anim-easing') || 'ease';
    var fot = el.animate ? fotogrammiCss(ANIM[chiave] || 'mpsFadeOut') : null;
    if (!fot) {
      el.style.animation = 'mpsFadeOut ' + dur + 'ms ' + curva + ' ' + rit + 'ms both';
      el.classList.remove('mps-anim-visible');
      return dur + rit;
    }
    var cs = getComputedStyle(el);
    var base = cs.transform && cs.transform !== 'none' ? cs.transform + ' ' : '';
    var op = parseFloat(cs.opacity);
    if (isNaN(op)) op = 1;
    el.style.animationPlayState = 'paused';
    var frames = fot.map(function (f) {
      var o = { offset: f.offset, transform: (base + f.transform).trim() || 'none', opacity: f.opacity !== '' ? op * parseFloat(f.opacity) : op };
      if (f.clipPath) o.clipPath = f.clipPath;
      if (f.filter) o.filter = f.filter;
      return o;
    });
    if (el._mpsUscita) { try { el._mpsUscita.cancel(); } catch (_) {} }
    try {
      el._mpsUscita = el.animate(frames, { duration: dur, delay: rit, easing: curva, fill: 'both' });
    } catch (_) {
      el._mpsUscita = null;
      el.style.animationPlayState = '';
      el.style.animation = 'mpsFadeOut ' + dur + 'ms ease ' + rit + 'ms both';
    }
    el.classList.remove('mps-anim-visible');
    return dur + rit;
  }

  function animateLayersOut(slide, callback) {
    var layers = qsa(slide, '.olo-proslider-layer');
    // il percorso si ferma dov'è mentre il livello esce (riparte da capo al prossimo ingresso)
    fermaPercorso(slide);
    layers.forEach(function (el) { if (el._mpsFine) { clearTimeout(el._mpsFine); el._mpsFine = null; } });
    if (!layers.length) { callback && callback(); return; }

    var maxEnd = 0;
    layers.forEach(function (el) {
      // Timeline layer: la sua Uscita o la dissolvenza rapida (esceTimeline)
      if (el.getAttribute('data-timeline-name')) {
        var fineTl = esceTimeline(el);
        if (fineTl > maxEnd) maxEnd = fineTl;
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
    // il video che segue lo scorrimento non parte da solo
    if (v && !v.hasAttribute('data-video-scorre')) { try { v.play(); } catch (e) {} }
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

  /* ── Stati (interazioni al clic, 1.4.981) ─────────────── */
  // Uno stato è un nome con un valore (luce = accesa). Le azioni «Imposta uno stato» e «Stato successivo» lo cambiano; i
  // livelli con «Visibile solo in uno stato» stanno in un involucro (.mps-stato) che si mostra quando il valore è fra i
  // loro (o, con «!», quando non lo è). Il valore sta anche sul contenitore (data-st-nome) e, se l'azione porta un
  // colore, nella variabile --mps-st-nome, che i colori dei livelli possono usare (var(--mps-st-tinta, #fff)).
  function valoreStato(nome) { return container.getAttribute('data-st-' + nome) || ''; }
  function statoVale(cond, v) {
    cond = String(cond || '').trim();
    var neg = cond.charAt(0) === '!';
    var lista = (neg ? cond.slice(1) : cond).split(',').map(function (x) { return x.trim(); });
    var dentro = lista.indexOf(String(v || '')) !== -1;
    return neg ? !dentro : dentro;
  }
  function rianima(w) {
    // quello che si mostra riparte dall'inizio: i disegni SVG animati, i video
    qsa(w, 'img').forEach(function (im) {
      if (/\.svg(#|$)/i.test(im.getAttribute('src') || '')) im.src = im.getAttribute('src').replace(/#.*$/, '') + '#r' + Date.now();
    });
    qsa(w, 'video').forEach(function (v) { try { v.currentTime = 0; v.play(); } catch (_) {} });
  }
  function impostaStato(nome, valore, colore) {
    nome = String(nome || '').toLowerCase().replace(/[^a-z0-9_-]/g, '');
    if (!nome) return;
    valore = String(valore == null ? '' : valore);
    container.setAttribute('data-st-' + nome, valore);
    if (colore) container.style.setProperty('--mps-st-' + nome, colore);
    qsa(container, '.mps-stato[data-stato="' + nome + '"]').forEach(function (w) {
      var ok = statoVale(w.getAttribute('data-stato-vale'), valore);
      var era = !w.classList.contains('mps-stato--via');
      w.classList.toggle('mps-stato--via', !ok);
      if (ok) w.removeAttribute('aria-hidden'); else w.setAttribute('aria-hidden', 'true');
      if (ok && !era) rianima(w);
    });
    // i pulsanti che impostano questo stato: acceso quello del valore in vigore
    qsa(container, '.olo-proslider-layer[data-action]').forEach(function (el) {
      var a; try { a = JSON.parse(el.getAttribute('data-action')); } catch (_) { return; }
      if (!a || a.type !== 'setState' || String(a.target || '').toLowerCase() !== nome) return;
      var on = String(a.value == null ? '' : a.value) === valore;
      el.classList.toggle('mps-stato-attivo', on);
      el.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
    container.dispatchEvent(new CustomEvent('olo:stato', { detail: { nome: nome, valore: valore } }));
  }
  container.oloStato = impostaStato;

  /* ── Livello da pulire col puntatore (1.4.981) ─────────── */
  // La tela copre il livello con la sua immagine (o il colore) e il puntatore la cancella passandoci sopra; sul telefono
  // il dito, anche mentre la pagina scorre. Una griglia di 48 × 27 caselle conta quanto è pulito: alla soglia il resto
  // svanisce e lo stato scritto («nome=valore») cambia. I tratti si ricordano (in frazioni del livello) e si rifanno
  // quando la tela cambia misura.
  qsa(container, 'canvas.mps-pulisci-tela').forEach(function (cv) {
    var o; try { o = JSON.parse(cv.getAttribute('data-pulisci')); } catch (_) { return; }
    var strato = cv.closest('.olo-proslider-layer') || cv;
    var g = cv.getContext('2d');
    if (!g) return;
    var GC = 48, GR = 27, celle = new Uint8Array(GC * GR), pulite = 0, finito = false;
    var tratti = [], ultimo = null, img = null, pronto = false, dpr = Math.min(2, window.devicePixelRatio || 1);
    function raggio() {
      // il pennello è in px della tela del computer; sul telefono non scende sotto un dito
      var w = Math.max(container.offsetWidth || 1440, 1000);
      return o.p * w / 1440 * dpr * ((window.innerWidth || 1440) < 700 ? 1.25 : 1);
    }
    // (1.4.1448) la tela non capisce var() né color-mix(): con un colore della palette («Primario», come in Fuliggine)
    // fillStyle restava nero. Il colore si fa leggere al browser su un elemento dentro lo slider, dove i token valgono.
    function coloreVero(c) {
      if (!c || !/var\(|color-mix|currentcolor/i.test(c)) return c || '#111';
      var s = document.createElement('span');
      s.style.cssText = 'display:none;color:' + c;
      strato.appendChild(s);
      var v = getComputedStyle(s).color;
      strato.removeChild(s);
      return v || '#111';
    }
    function copri() {
      g.globalCompositeOperation = 'source-over';
      g.clearRect(0, 0, cv.width, cv.height);
      g.fillStyle = coloreVero(o.c);
      g.fillRect(0, 0, cv.width, cv.height);
      if (img && img.naturalWidth) {
        // come object-fit: cover, col punto scelto
        var s = Math.max(cv.width / img.naturalWidth, cv.height / img.naturalHeight);
        var w = img.naturalWidth * s, h = img.naturalHeight * s;
        var pos = String(o.pos || 'center').split(/\s+/);
        var px = pos[0] === 'left' ? 0 : pos[0] === 'right' ? 1 : (/%$/.test(pos[0]) ? parseFloat(pos[0]) / 100 : 0.5);
        var py = pos[1] === 'top' ? 0 : pos[1] === 'bottom' ? 1 : (/%$/.test(pos[1] || '') ? parseFloat(pos[1]) / 100 : 0.5);
        g.drawImage(img, (cv.width - w) * px, (cv.height - h) * py, w, h);
      }
    }
    function punto(nx, ny) {
      var r = raggio(), x = nx * cv.width, y = ny * cv.height;
      var grad = g.createRadialGradient(x, y, 0, x, y, r);
      grad.addColorStop(0, 'rgba(0,0,0,1)');
      grad.addColorStop(0.55, 'rgba(0,0,0,0.85)');
      grad.addColorStop(1, 'rgba(0,0,0,0)');
      g.globalCompositeOperation = 'destination-out';
      g.fillStyle = grad;
      g.beginPath(); g.arc(x, y, r, 0, Math.PI * 2); g.fill();
      // le caselle dentro il pennello (un po' più strette del bordo sfumato)
      var rc = r * 0.7 / cv.width * GC, ry = r * 0.7 / cv.height * GR, cx = nx * GC, cy = ny * GR;
      for (var j = Math.max(0, Math.floor(cy - ry)); j <= Math.min(GR - 1, Math.floor(cy + ry)); j++) {
        for (var i = Math.max(0, Math.floor(cx - rc)); i <= Math.min(GC - 1, Math.floor(cx + rc)); i++) {
          var dx = (i + 0.5 - cx) / Math.max(0.001, rc), dy = (j + 0.5 - cy) / Math.max(0.001, ry);
          if (dx * dx + dy * dy <= 1 && !celle[j * GC + i]) { celle[j * GC + i] = 1; pulite++; }
        }
      }
    }
    function tratto(a, b) {
      // fra due posizioni del puntatore, un punto ogni mezzo pennello: la riga resta continua anche coi movimenti veloci
      var r = raggio(), d = Math.hypot((b[0] - a[0]) * cv.width, (b[1] - a[1]) * cv.height), n = Math.max(1, Math.ceil(d / (r * 0.45)));
      for (var k = 1; k <= n; k++) punto(a[0] + (b[0] - a[0]) * k / n, a[1] + (b[1] - a[1]) * k / n);
    }
    function misura() {
      var r = strato.getBoundingClientRect();
      if (!r.width || !r.height) return false;
      cv.width = Math.max(2, Math.round(r.width * dpr));
      cv.height = Math.max(2, Math.round(r.height * dpr));
      copri();
      tratti.forEach(function (t) { if (t.length === 1) punto(t[0][0], t[0][1]); for (var k = 1; k < t.length; k++) tratto(t[k - 1], t[k]); });
      return true;
    }
    function fine() {
      if (finito) return;
      finito = true;
      cv.style.transition = 'opacity 900ms ease';
      cv.style.opacity = '0';
      strato.style.pointerEvents = 'none';
      var st = String(o.st || '').split('=');
      if (st[0]) impostaStato(st[0].trim(), (st[1] || '').trim());
    }
    function passa(cx, cy, nuovo) {
      if (finito || !pronto) return;
      var r = strato.getBoundingClientRect();
      if (!r.width) return;
      var nx = (cx - r.left) / r.width, ny = (cy - r.top) / r.height;
      if (nx < -0.05 || nx > 1.05 || ny < -0.05 || ny > 1.05) { ultimo = null; return; }
      var q = [nx, ny];
      if (nuovo || !ultimo) { tratti.push([q]); punto(nx, ny); } else { tratti[tratti.length - 1].push(q); tratto(ultimo, q); }
      ultimo = q;
      if (pulite / (GC * GR) * 100 >= o.s) fine();
    }
    strato.addEventListener('pointermove', function (e) { if (e.pointerType !== 'touch') passa(e.clientX, e.clientY, false); });
    strato.addEventListener('pointerleave', function () { ultimo = null; });
    strato.addEventListener('touchstart', function (e) { var t = e.touches[0]; if (t) passa(t.clientX, t.clientY, true); }, { passive: true });
    strato.addEventListener('touchmove', function (e) { var t = e.touches[0]; if (t) passa(t.clientX, t.clientY, false); }, { passive: true });
    strato.addEventListener('touchend', function () { ultimo = null; }, { passive: true });
    // se lo stato cambia da un'altra parte (il pulsante «Pulisci tutto»), la tela se ne va
    container.addEventListener('olo:stato', function (e) {
      var st = String(o.st || '').split('=');
      if (st[0] && e.detail && e.detail.nome === st[0].trim() && e.detail.valore === (st[1] || '').trim() && !finito) {
        finito = true; cv.style.transition = 'opacity 900ms ease'; cv.style.opacity = '0'; strato.style.pointerEvents = 'none';
      }
    });
    function parti() { pronto = misura(); }
    if (o.img) {
      img = new Image();
      img.onload = parti;
      img.onerror = function () { img = null; parti(); };
      img.src = o.img;
    } else {
      parti();
    }
    var tMis = null;
    window.addEventListener('resize', function () { clearTimeout(tMis); tMis = setTimeout(function () { if (!finito) pronto = misura(); }, 120); });
    if ('ResizeObserver' in window) {
      new ResizeObserver(function () { if (!finito && !pronto) pronto = misura(); }).observe(strato);
    }
  });

  /* ── Layer Actions ────────────────────────────────────── */
  qsa(container, '.olo-proslider-layer[data-action]').forEach(function (el) {
    el.style.cursor = 'pointer';
    // i livelli che agiscono e non sono pulsanti hanno role=button (PHP): Invio e Spazio li premono
    if (el.getAttribute('role') === 'button') {
      el.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); el.click(); }
      });
    }
    // Il punto del clic: chi cambia uno stato lascia sullo slider dove lo si è toccato, in px dal centro, per quel valore
    // (--mps-clic-x-<valore>, --mps-clic-y-<valore>). Un livello di quello stato ci si sposta (translate nel suo CSS):
    // la crepa nasce dove si colpisce il vetro (Scheggia, 15 nov 2026). Da tastiera il punto è il centro.
    function segnaPunto(e, valore) {
      var v = String(valore || '').toLowerCase().replace(/[^a-z0-9_-]/g, '');
      if (!v) return;
      var x = 0, y = 0;
      if (e && e.detail !== 0 && typeof e.clientX === 'number') {
        var rc = container.getBoundingClientRect();
        x = Math.round(e.clientX - rc.left - rc.width / 2);
        y = Math.round(e.clientY - rc.top - rc.height / 2);
      }
      container.style.setProperty('--mps-clic-x-' + v, x + 'px');
      container.style.setProperty('--mps-clic-y-' + v, y + 'px');
    }
    function esegui(e) {
      var action;
      try { action = JSON.parse(el.getAttribute('data-action')); } catch (_) { return; }
      if (!action) return;
      switch (action.type) {
        case 'setState':
          // un pulsante che cambia uno stato non porta altrove (il suo link «#» farebbe saltare la pagina in cima)
          if (e && e.preventDefault) e.preventDefault();
          segnaPunto(e, action.value);
          impostaStato(action.target, action.value, action.color);
          break;
        case 'cycleState':
          if (e && e.preventDefault) e.preventDefault();
          // il valore dopo quello in vigore, nell'ordine scritto; dall'ultimo si ricomincia, o ci si ferma
          var nomeSt = String(action.target || '').toLowerCase();
          var valori = String(action.values || '').split(',').map(function (x) { return x.trim(); });
          var ora = valori.indexOf(valoreStato(nomeSt));
          if (ora === valori.length - 1 && action.loop === false) break;
          segnaPunto(e, valori[(ora + 1) % valori.length]);
          impostaStato(nomeSt, valori[(ora + 1) % valori.length]);
          break;
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
            if (tgt) mostraNascondi(tgt, action.effetto === 'fade' ? (parseInt(action.durata, 10) || 400) : 0);
          }
          break;
      }
    }
    el.addEventListener('click', esegui);
    // Al passaggio del mouse (1.4.1428, come le azioni «mouse enter / leave» di Slider Revolution): l'azione parte
    // quando il mouse entra; col tocco e con la tastiera resta il clic. «ritorna»: uscendo, lo stato torna com'era.
    var az0; try { az0 = JSON.parse(el.getAttribute('data-action')); } catch (_) { az0 = null; }
    if (az0 && az0.trigger === 'hover') {
      // L'uscita si misura sulla ZONA del livello presa all'entrata, non sul livello: una spia che, scelta, si nasconde
      // (Cric) farebbe «uscire» il mouse da sola, lo stato tornerebbe indietro, la spia riapparirebbe… e lampeggerebbe
      // (prova su olo03 del 24 nov). Anche uscendo dallo slider lo stato torna com'era.
      var nomeH = String(az0.target || '').toLowerCase().replace(/[^a-z0-9_-]/g, ''), primaH = null, zonaH = null, coloreH = '';
      var torna = function () {
        document.removeEventListener('pointermove', controllaUscita);
        container.removeEventListener('pointerleave', torna);
        zonaH = null;
        if (primaH !== null) {
          impostaStato(nomeH, primaH);
          // 1.4.1477: torna anche il colore dello stato (--mps-st-<stato>), se no restava quello dell'ultima spia passata
          // e il display mostrava il testo di prima col colore di un'altra scelta
          if (coloreH) container.style.setProperty('--mps-st-' + nomeH, coloreH);
          else container.style.removeProperty('--mps-st-' + nomeH);
        }
      };
      var controllaUscita = function (ev) {
        var r = zonaH;
        if (r && (ev.clientX < r.left - 2 || ev.clientX > r.right + 2 || ev.clientY < r.top - 2 || ev.clientY > r.bottom + 2)) torna();
      };
      el.addEventListener('pointerenter', function (e) {
        if ((e.pointerType && e.pointerType !== 'mouse') || zonaH) return;
        primaH = (az0.type === 'setState' || az0.type === 'cycleState') ? valoreStato(nomeH) : null;
        coloreH = container.style.getPropertyValue('--mps-st-' + nomeH);
        esegui(e);
        if (az0.ritorna && primaH !== null) {
          zonaH = el.getBoundingClientRect();
          document.addEventListener('pointermove', controllaUscita, { passive: true });
          container.addEventListener('pointerleave', torna);
        }
      });
    }
  });

  // «Mostra o nasconde un livello» (1.4.1475): di colpo come prima o, con «Come compare: Dissolvenza», sfumando. Il livello
  // va e viene come sempre con display; la dissolvenza va dall'opacità che ha (la sua entrata, se riparte) a zero e
  // ritorno, senza toccare le sue animazioni (un fotogramma solo: l'altro capo è il valore del momento).
  function mostraNascondi(el, durata) {
    var nascosto = el.style.display === 'none';
    if (el._mpsMn) { try { el._mpsMn.cancel(); } catch (_) {} el._mpsMn = null; }
    if (!durata || !el.animate || menoMoto) { el.style.display = nascosto ? '' : 'none'; return; }
    try {
      if (nascosto) {
        el.style.display = '';
        el._mpsMn = el.animate([{ opacity: 0, offset: 0 }], { duration: durata, easing: 'ease' });
      } else {
        var a = el.animate([{ opacity: 0, offset: 1 }], { duration: durata, easing: 'ease', fill: 'forwards' });
        el._mpsMn = a;
        a.onfinish = function () { el.style.display = 'none'; try { a.cancel(); } catch (_) {} if (el._mpsMn === a) el._mpsMn = null; };
      }
    } catch (_) { el.style.display = nascosto ? '' : 'none'; }
  }

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
    var conTl = pl.hasAttribute('data-timeline-name');
    if (og) {
      pLivelli.push({ el: pl, tl: conTl, tipo: og.t || 'mouse', prof: og.d || 5, k: 4, tilt: !!og.r, vel: og.s || 0.5, mx: 0, my: 0, sy: 0, rx: 0, ry: 0 });
    } else if (cfg.parallax) {
      var prof = parseInt(pl.getAttribute('data-parallax-depth'), 10) || 0;
      if (prof > 0) pLivelli.push({ el: pl, tl: conTl, tipo: cfg.parallaxType || 'mouse', prof: prof, k: cfg.parallaxIntensity || 5, tilt: false, vel: 1, mx: 0, my: 0, sy: 0, rx: 0, ry: 0 });
    }
  });
  if (pLivelli.length) {
    // Coi livelli con la linea del tempo il transform in linea non si vedrebbe (lo tiene la loro animazione): la
    // parallasse si SOMMA all'animazione (Web Animations, composite «add»). 1.4.1472: prima lì non agiva.
    var parallasseSopra = function (st, t, durata) {
      if (!st.el.animate) return;
      var da = st.ultimo || 'none';
      st.ultimo = t || 'none';
      try {
        if (st.anim && !durata) { st.anim.effect.setKeyframes([{ transform: st.ultimo }]); return; }
        if (st.anim) st.anim.cancel();
        st.anim = st.el.animate(durata ? [{ transform: da }, { transform: st.ultimo }] : [{ transform: st.ultimo }],
          { duration: durata || 1, easing: 'ease-out', fill: 'forwards', composite: 'add' });
      } catch (_) { st.anim = null; }
    };
    var scriviLivello = function (st, durata) {
      var x = st.mx, y = st.my + st.sy;
      var t = (x || y) ? 'translate(' + x.toFixed(1) + 'px, ' + y.toFixed(1) + 'px)' : '';
      if (st.tilt && (st.rx || st.ry)) t += ' perspective(900px) rotateX(' + st.rx.toFixed(2) + 'deg) rotateY(' + st.ry.toFixed(2) + 'deg)';
      if (st.tl) { parallasseSopra(st, t, durata); return; }
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
          if (st.tl) { scriviLivello(st, 500); return; }
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
  //
  // Fluidità (Ti Consiglia, 13 nov 2026: su telefono e tablet lo zoom scattava, sul computer no):
  // - dove il browser sa legare un'animazione allo scorrimento (ViewTimeline: Chrome 115, Safari 26) le tappe diventano
  //   un'animazione del browser che va col compositore, in pari col dito, senza passare dal JavaScript a ogni fotogramma;
  //   altrove si calcola a ogni fotogramma come prima, ma leggendo le misure tutte prima di scrivere;
  // - il filtro dell'opacità c'è solo mentre il livello sfuma: a opacità piena il livello torna al filtro suo (ogni
  //   filtro è una superficie in più da ridisegnare a ogni fotogramma), e lontano dai tratti in cui si vede il livello è
  //   nascosto (visibility) e il suo video in pausa.
  (function () {
    var mobili = qsa(container, '.olo-proslider-layer[data-scroll-move]').map(function (el) {
      try { return { el: el, d: JSON.parse(el.getAttribute('data-scroll-move')) }; } catch (e) { return null; }
    }).filter(function (m) { return m && m.d && m.d.t && m.d.t.length > 1; });
    if (!mobili.length) return;
    function usa(m, chiavi) { return m.d.t.some(function (t) { return chiavi.some(function (c) { return t.v[c] != null; }); }); }
    mobili.forEach(function (m) {
      m.tela = m.el.parentElement;
      // la maschera (mt, mr, mb, ml) solo sui livelli che la usano: gli altri restano senza clip-path
      m.maschera = usa(m, ['mt', 'mr', 'mb', 'ml']);
      m.conO = usa(m, ['o']);
      m.conXY = usa(m, ['x', 'y']);
      m.conS = usa(m, ['s', 'sx', 'sy']);
      m.conR = usa(m, ['r', 'rx', 'ry']);
      // gli assi del carattere variabile (larghezza e peso veri del disegno), nell'ordine fisso: le tappe si fondono
      m.assi = ['wdth', 'wght'].filter(function (c) { return usa(m, [c]); });
      // il filtro del livello (Avanzate → CSS: la luminosità di un video) resta sotto l'opacità
      m.filtro = m.el.style.filter || '';
      m.video = m.el.querySelector('video');
      m.scritto = {};
      // la prospettiva per le rotazioni 3D sta sulla tela del livello
      if (m.tela && m.d.t.some(function (t) { return t.v.rx || t.v.ry; })) m.tela.style.perspective = '1600px';
    });
    var avvolto = container.closest('.mps-scroll-fixed-wrapper');
    // Andamento «morbido» (di serie): fra una tappa e l'altra si parte e si arriva piano. «Costante» (c): si va a velocità
    // uniforme e la scala cresce in proporzione (da 1 a 4 come da 4 a 16), così uno zoom che attraversa più tappe non
    // rallenta a ogni tappa (le potenze di dieci di Ti Consiglia, 10 nov 2026).
    function valore(t, k, base, costante) {
      var u = 0;
      for (var i = 0; i < t.length - 1; i++) { if (t[i + 1].p >= k) { u = i; break; } u = i; }
      var a = t[u], b = t[Math.min(u + 1, t.length - 1)];
      var span = Math.max(0.0001, b.p - a.p);
      var f = Math.max(0, Math.min(1, (k - a.p) / span));
      if (!costante) f = f * f * (3 - 2 * f);
      var va = a.v[base], vb = b.v[base];
      var scala = base === 's' || base === 'sx' || base === 'sy';
      var def = scala ? 1 : (base === 'o' || base === 'wdth' ? 100 : (base === 'wght' ? 400 : 0));
      va = va == null ? def : va; vb = vb == null ? def : vb;
      if (costante && scala && va > 0 && vb > 0 && va !== vb) return va * Math.pow(vb / va, f);
      return va + (vb - va) * f;
    }
    // dall'avanzamento della scena (0–100) a quello del livello nel suo tratto, e viceversa
    function delLivello(d, p) {
      return d.f > d.i ? Math.max(0, Math.min(100, (p - d.i) / (d.f - d.i) * 100)) : (p >= d.i ? 100 : 0);
    }
    function dellaScena(d, k) { return d.i + k * (d.f - d.i) / 100; }
    // i valori di un livello al punto k del suo tratto (t: le tappe da cui leggerli)
    function stile(m, t, k, fattore) {
      var c = m.d.c, s = {};
      if (m.conXY) s.translate = (valore(t, k, 'x', c) * fattore).toFixed(1) + 'px ' + (valore(t, k, 'y', c) * fattore).toFixed(1) + 'px';
      if (m.conS) {
        // sx e sy allungano in orizzontale e in verticale, moltiplicando la scala
        var sc = valore(t, k, 's', c), sx = valore(t, k, 'sx', c), sy = valore(t, k, 'sy', c);
        s.scale = sx !== 1 || sy !== 1 ? (sc * sx).toFixed(4) + ' ' + (sc * sy).toFixed(4) : sc.toFixed(4);
      }
      if (m.conR) {
        var rx = valore(t, k, 'rx', c), ry = valore(t, k, 'ry', c);
        s.rotate = rx ? 'x ' + rx.toFixed(2) + 'deg' : (ry ? 'y ' + ry.toFixed(2) + 'deg' : valore(t, k, 'r', c).toFixed(2) + 'deg');
      }
      if (m.assi.length) {
        s.fontVariationSettings = m.assi.map(function (a) { return '"' + a + '" ' + valore(t, k, a, c).toFixed(1); }).join(', ');
      }
      if (m.conO) {
        var o = Math.max(0, Math.min(100, valore(t, k, 'o', c)));
        s.filter = o >= 99.95 ? (m.filtro || 'none') : (m.filtro ? m.filtro + ' ' : '') + 'opacity(' + (o / 100).toFixed(3) + ')';
      }
      return s;
    }
    // La maschera: quanto del livello resta coperto da ogni lato (in % del livello, che si allunga con la scena): un
    // video che si scopre sotto una riga che scende (Coro Paganella, 3 nov 2026), una fascia che la segue.
    function maschera(m, t, k) {
      return 'inset(' + ['mt', 'mr', 'mb', 'ml'].map(function (c) {
        return Math.max(0, Math.min(100, valore(t, k, c, m.d.c))).toFixed(2) + '%';
      }).join(' ') + ')';
    }
    function scrivi(m, prop, v) {
      if (m.scritto[prop] === v) return;
      m.scritto[prop] = v;
      m.el.style[prop] = v;
    }
    // I tratti della scena (0–100) in cui il livello si vede, allargati di MARGINE punti per lato: fuori il livello è
    // nascosto e il suo video fermo. Il margine copre il ritardo del JavaScript quando lo zoom va col compositore.
    var MARGINE = 4;
    mobili.forEach(function (m) {
      if (!m.conO) return;
      var d = m.d, punti = [0, 100];
      d.t.forEach(function (t) { punti.push(dellaScena(d, t.p)); });
      punti = punti.filter(function (p) { return p >= 0 && p <= 100; }).sort(function (a, b) { return a - b; });
      var tratti = [];
      for (var i = 0; i < punti.length - 1; i++) {
        var a = punti[i], b = punti[i + 1];
        if (b - a < 1e-6) continue;
        var vede = [a + 1e-4, (a + b) / 2, b - 1e-4].some(function (p) { return valore(d.t, delLivello(d, p), 'o', d.c) > 0.05; });
        if (!vede) continue;
        var ult = tratti[tratti.length - 1];
        if (ult && a - MARGINE <= ult[1]) ult[1] = b + MARGINE; else tratti.push([a - MARGINE, b + MARGINE]);
      }
      m.tratti = tratti;
    });
    function visibile(m, p) {
      if (!m.tratti) return;
      var si = m.tratti.some(function (t) { return p >= t[0] && p <= t[1]; });
      if (m.via === !si) return;
      m.via = !si;
      // '' lascia decidere alla diapositiva (le diapositive non attive nascondono i loro livelli)
      m.el.style.visibility = si ? '' : 'hidden';
      if (!m.video) return;
      if (!si) {
        if (!m.video.paused) { m.video.pause(); m.fermato = true; }
      } else if (m.fermato) {
        m.fermato = false;
        var pr = m.video.play();
        if (pr && pr.catch) pr.catch(function () {});
      }
    }
    function avanzamento() {
      var r = (avvolto || container).getBoundingClientRect();
      var vh = window.innerHeight || 800;
      if (r.bottom < -vh || r.top > vh * 2) return null;
      var p = avvolto ? -r.top / Math.max(1, avvolto.offsetHeight - container.offsetHeight) : -r.top / Math.max(1, r.height);
      return menoMoto ? 0 : Math.max(0, Math.min(1, p)) * 100;
    }

    // ── col compositore: un'animazione del browser per livello, legata allo scorrimento ──
    // Il soggetto è il contenitore che scorre (o lo slider): «contain» va da quando la sua cima tocca la cima della
    // finestra a quando il suo fondo tocca il fondo (il tratto in cui lo slider resta fermo), «exit-crossing» da quando
    // la cima dello slider tocca la cima della finestra a quando la tocca il fondo. Solo se fra il soggetto e la pagina
    // non c'è un altro contenitore che scorre (overflow hidden/auto/scroll): la timeline si legherebbe a quello.
    function scorreLaPagina(el) {
      var radice = document.scrollingElement || document.documentElement;
      for (var n = el.parentElement; n && n !== radice && n !== document.body; n = n.parentElement) {
        var cs = getComputedStyle(n);
        if (/(auto|scroll|hidden)/.test(cs.overflowX + ' ' + cs.overflowY)) return false;
      }
      return true;
    }
    var soggetto = avvolto || container;
    var conTimeline = !menoMoto && typeof window.ViewTimeline === 'function' && window.Animation &&
      'rangeStart' in window.Animation.prototype && !window.oloProSliderSenzaTimeline && scorreLaPagina(soggetto);
    var animazioni = [];
    // Le tappe di un livello come fotogrammi chiave sull'avanzamento della scena: uno per tappa (col valore suo, anche
    // quando due tappe stanno allo stesso punto), con l'andamento morbido come curva (cubic-bezier(1/3, 0, 2/3, 1) è
    // esattamente 3f² − 2f³); dove la scala cresce in proporzione (andamento costante) otto punti intermedi, perché il
    // browser la interpolerebbe in linea retta.
    function fotogrammi(m, fattore, soloMaschera) {
      var d = m.d, t = d.t, kf = [];
      var curva = d.c ? 'linear' : 'cubic-bezier(0.333, 0, 0.667, 1)';
      function aggiungi(p, tappe, k) {
        var f = soloMaschera ? { clipPath: maschera(m, tappe, k) } : stile(m, tappe, k, fattore);
        f.offset = Math.max(0, Math.min(1, p / 100));
        f.easing = curva;
        kf.push(f);
      }
      if (dellaScena(d, t[0].p) > 0) aggiungi(0, t, 0);
      for (var i = 0; i < t.length; i++) {
        var p = dellaScena(d, t[i].p);
        if (p < 0 || p > 100) continue;
        aggiungi(p, [t[i], t[i]], 0);
        var nx = t[i + 1];
        if (!soloMaschera && nx && d.c && m.conS && nx.p > t[i].p &&
            ['s', 'sx', 'sy'].some(function (c) { return (t[i].v[c] == null ? 1 : t[i].v[c]) !== (nx.v[c] == null ? 1 : nx.v[c]); })) {
          for (var j = 1; j < 8; j++) {
            var k = t[i].p + (nx.p - t[i].p) * j / 8;
            aggiungi(dellaScena(d, k), t, k);
          }
        }
      }
      if (dellaScena(d, t[t.length - 1].p) < 100) aggiungi(100, t, 100);
      // stabile: a parità di punto resta l'ordine delle tappe
      kf.forEach(function (f, i) { f._n = i; });
      kf.sort(function (a, b) { return a.offset - b.offset || a._n - b._n; });
      kf.forEach(function (f) { delete f._n; });
      return kf;
    }
    function fattoreDi(m) { return m.tela && m.tela.offsetWidth ? m.tela.offsetWidth / 1440 : 1; }
    // Il tratto in cui lo slider resta fermo, in px dalla cima come lo misura avanzamento(): «contain 100%» finirebbe
    // quando il fondo del contenitore tocca il fondo della finestra, e sul telefono lo slider può essere un po' più alto
    // della finestra (la tela di 640 × 1400 a 390 px è alta 853 px).
    function fineTratto() {
      return avvolto ? 'contain ' + Math.max(1, avvolto.offsetHeight - container.offsetHeight) + 'px' : 'exit-crossing 100%';
    }
    if (conTimeline) {
      try {
        var tl = new window.ViewTimeline({ subject: soggetto, axis: 'block' });
        var opz = { timeline: tl, rangeStart: avvolto ? 'contain 0px' : 'exit-crossing 0%', rangeEnd: fineTratto(),
                    fill: 'both', duration: 'auto' };
        mobili.forEach(function (m) {
          m.fattore = fattoreDi(m);
          if (m.conXY || m.conS || m.conR || m.conO || m.assi.length) {
            m.anim = m.el.animate(fotogrammi(m, m.fattore), opz);
            animazioni.push(m.anim);
            // un browser che accettasse l'animazione ignorando il tratto la legherebbe a tutto il passaggio nella finestra
            var rs = m.anim.rangeStart;
            if (String(rs && rs.rangeName || rs).indexOf(avvolto ? 'contain' : 'exit-crossing') !== 0) throw new Error('tratto');
          }
          // la maschera a parte: clip-path non va col compositore, e terrebbe fuori anche il resto
          if (m.maschera) animazioni.push(m.el.animate(fotogrammi(m, m.fattore, true), opz));
        });
      } catch (e) {
        animazioni.forEach(function (a) { try { a.cancel(); } catch (e2) {} });
        animazioni = [];
        conTimeline = false;
      }
    }
    if (conTimeline) {
      container.setAttribute('data-mps-scorre', 'browser');
      // gli spostamenti in px seguono la larghezza della tela; nascondere i livelli e fermare i video resta al
      // JavaScript, che scrive solo quando un livello entra o esce dai suoi tratti
      var fine = fineTratto();
      var rifai = function () {
        var f = fineTratto();
        if (f !== fine) {
          fine = f;
          animazioni.forEach(function (a) { a.rangeEnd = f; });
        }
        mobili.forEach(function (m) {
          if (!m.anim || !m.conXY) return;
          var fatt = fattoreDi(m);
          if (Math.abs(fatt - m.fattore) < 0.0005) return;
          m.fattore = fatt;
          m.anim.effect.setKeyframes(fotogrammi(m, fatt));
        });
      };
      var giroV = null;
      var segui = function () {
        giroV = null;
        var p = avanzamento();
        if (p == null) return;
        mobili.forEach(function (m) { visibile(m, p); });
      };
      var chiediV = function () { if (!giroV) giroV = requestAnimationFrame(segui); };
      window.addEventListener('scroll', chiediV, { passive: true });
      window.addEventListener('resize', function () { rifai(); chiediV(); });
      if ('ResizeObserver' in window) {
        var ro = new ResizeObserver(function () { rifai(); });
        ro.observe(container);
        if (avvolto) ro.observe(avvolto);
      }
      segui();
      return;
    }

    // ── senza: a ogni fotogramma, prima le misure (una per tela) e poi le scritture, solo dei valori cambiati ──
    container.setAttribute('data-mps-scorre', 'js');
    function applica() {
      giro = null;
      var p = avanzamento();
      if (p == null) return;
      var larghe = new Map();
      mobili.forEach(function (m) {
        if (m.tela && !larghe.has(m.tela)) larghe.set(m.tela, m.tela.offsetWidth);
      });
      mobili.forEach(function (m) {
        var k = delLivello(m.d, p);
        var lw = m.tela ? larghe.get(m.tela) : 0;
        var s = stile(m, m.d.t, k, lw ? lw / 1440 : 1);
        for (var prop in s) scrivi(m, prop, s[prop]);
        if (m.maschera) scrivi(m, 'clipPath', maschera(m, m.d.t, k));
        visibile(m, p);
      });
    }
    var giro = null;
    function chiedi() { if (!giro) giro = requestAnimationFrame(applica); }
    window.addEventListener('scroll', chiedi, { passive: true });
    window.addEventListener('resize', chiedi);
    applica();
  })();

  /* ── Video di sfondo che segue lo scorrimento (videoScroll) ── */
  // Il tempo del video è l'avanzamento dello scorrimento (lo stesso dei livelli che si muovono: il tratto in cui lo slider
  // resta fermo in cima, o la sua uscita dalla finestra); il video ci arriva con una piccola rincorsa, così le tacche
  // della rotella non fanno scatti. Risalendo torna indietro.
  (function () {
    var video = qsa(container, '.olo-proslider-bg video[data-video-scorre]');
    if (!video.length) return;
    var avvolto = container.closest('.mps-scroll-fixed-wrapper');
    var stato = video.map(function (v) { return { v: v, t: 0 }; });
    var giro = null;
    function avanzamento() {
      var r = (avvolto || container).getBoundingClientRect();
      var p = avvolto ? -r.top / Math.max(1, avvolto.offsetHeight - container.offsetHeight) : -r.top / Math.max(1, r.height);
      return Math.max(0, Math.min(1, p));
    }
    function passo() {
      giro = null;
      var p = avanzamento(), ancora = false;
      stato.forEach(function (s) {
        var d = s.v.duration;
        if (!d || isNaN(d)) return;
        var meta = p * (d - 0.05);
        s.t += (meta - s.t) * 0.22;
        if (Math.abs(meta - s.t) < 0.01) s.t = meta; else ancora = true;
        if (Math.abs(s.v.currentTime - s.t) > 0.02) { try { s.v.currentTime = s.t; } catch (e) {} }
      });
      if (ancora) giro = requestAnimationFrame(passo);
    }
    function chiedi() { if (!giro) giro = requestAnimationFrame(passo); }
    stato.forEach(function (s) {
      s.v.pause();
      s.v.addEventListener('loadedmetadata', chiedi);
    });
    window.addEventListener('scroll', chiedi, { passive: true });
    window.addEventListener('resize', chiedi);
    chiedi();
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
