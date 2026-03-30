/**
 * ProSlider — Slider professionale con editor visuale dei livelli.
 *
 * Tutto il contenuto è gestito tramite un editor modale a schermo intero dedicato,
 * quindi l'array `fields` è intenzionalmente vuoto.
 */

function makeId() {
  return 'ps-' + Date.now().toString(36) + Math.random().toString(36).slice(2, 7);
}

function defaultLayer(type = 'text') {
  return {
    id: makeId(),
    type,
    // Contenuto (varia per tipo)
    content: type === 'text' ? 'Testo intestazione' : (type === 'button' ? 'Clicca qui' : ''),
    tag: 'h2',
    imageSrc: '',
    iconName: 'star',
    buttonUrl: '',
    buttonTarget: '_self',
    // Video layer
    videoSrc: '',
    videoAutoplay: true,
    videoMuted: true,
    videoLoop: true,
    // Posizione e dimensione (% del canvas) — auto si adatta al contenuto
    x: 10,
    y: 30,
    width: (type === 'shape' || type === 'image' || type === 'video') ? 30 : 'auto',
    height: (type === 'shape' || type === 'image' || type === 'video') ? 20 : 'auto',
    // Stile
    fontSize: type === 'text' ? 48 : (type === 'button' ? 18 : 24),
    fontWeight: '700',
    fontStyle: 'normal', // normal | italic
    color: '#ffffff',
    textAlign: 'left',
    bgColor: type === 'button' ? '#2563eb' : (type === 'shape' ? '#3b82f6' : ''),
    borderRadius: type === 'button' ? 6 : 0,
    borderRadiusLinked: true, // true = tutti gli angoli uguali
    borderRadiusTL: 0,
    borderRadiusTR: 0,
    borderRadiusBR: 0,
    borderRadiusBL: 0,
    padding: type === 'button' ? 16 : 0,
    paddingLinked: true, // true = tutti i lati uguali
    paddingTop: 0,
    paddingRight: 0,
    paddingBottom: 0,
    paddingLeft: 0,
    opacity: 100,
    // Tipografia avanzata (text/button)
    lineHeight: 1.2,
    letterSpacing: 0,
    textTransform: 'none',
    textDecoration: 'none',
    fontFamily: '',
    // Text stroke (text/button)
    textStrokeWidth: 0,
    textStrokeColor: '#000000',
    // Selectable text
    selectableText: false,
    // Bordo (tutti i tipi)
    borderWidth: 0,
    borderWidthLinked: true, // true = tutti i lati uguali
    borderWidthTop: 0,
    borderWidthRight: 0,
    borderWidthBottom: 0,
    borderWidthLeft: 0,
    borderStyle: 'solid',
    borderColor: '#ffffff',
    // Text shadow (text/button)
    textShadow: null, // { x: 2, y: 2, blur: 4, color: '#000000' }
    // Box shadow (tutti i tipi)
    boxShadow: null, // { x: 0, y: 4, blur: 10, spread: 0, color: 'rgba(0,0,0,0.3)' }
    // Immagine (image layer)
    objectFit: 'cover',
    objectPosition: 'center',
    // Filtri CSS (image/video)
    filterBrightness: 100,
    filterContrast: 100,
    filterSaturate: 100,
    filterGrayscale: 0,
    filterHueRotate: 0,
    filterBlur: 0,
    filterSepia: 0,
    filterInvert: 0,
    // Backdrop filter (glassmorphism - tutti i tipi)
    backdropBlur: 0,
    backdropBrightness: 100,
    backdropGrayscale: 0,
    // Shape gradient
    shapeGradient: null, // { from: '#3b82f6', to: '#8b5cf6', angle: 180 }
    // SFX block reveal (null = disabilitato)
    sfx: null, // { effect: 'blockRight'|'blockLeft'|'blockDown'|'blockUp', color: '#fff', duration: 800 }
    // Blend mode
    blendMode: 'normal',
    // Icon SVG controls
    iconStrokeColor: '',
    iconStrokeWidth: 0,
    iconStrokeDash: 0,
    iconFillColor: '',
    // Custom attributes
    customCSS: '',
    customClass: '',
    customId: '',
    // Cursor
    cursor: 'auto',
    // Audio layer
    audioSrc: '',
    audioAutoplay: false,
    audioLoop: false,
    // Animazione
    animIn: 'fadeInUp',
    animInDuration: 800,
    animInDelay: 200,
    animOut: 'fadeOutDown',
    animOutDuration: 600,
    animOutDelay: 0,
    animEasing: 'ease',
    // Character animation (solo per tipo text)
    charAnim: null,
    // Loop animation (continua dopo l'entrata)
    animLoop: 'none',
    animLoopDuration: 3000,
    animLoopEasing: 'ease-in-out',
    // Hover effects (null = disabilitato)
    // { scale, rotation, opacity, x, y, duration, easing, color, bgColor, borderColor, borderRadius, blur, brightness, grayscale, skewX, skewY, rotateX, rotateY, cursor }
    hover: null,
    // Responsive breakpoints (null = eredita dal valore principale)
    responsive: {
      notebook: null,  // { x, y, width, height, fontSize, visible }
      tablet: null,
      mobile: null,
    },
    // Timeline keyframe (null = usa animIn/animOut classico)
    timeline: null,
    // Layer actions (null = nessuna azione)
    action: null, // { type: 'none'|'goToSlide'|'nextSlide'|'prevSlide'|'scrollBelow'|'openUrl'|'toggleLayer', target, url, urlTarget }
    // Parallax depth (0 = nessun parallax, 1-10 profondita)
    parallaxDepth: 0,
    // Static layer: visibile su tutte le slide (solo se aggiunto alla sezione global)
    isGlobal: false,
    globalPosition: 'front', // 'front' | 'back'
  };
}

function defaultSlide() {
  return {
    id: makeId(),
    persistFor: 0,
    background: {
      type: 'color',
      image: '',
      video: '',
      color: '#1e293b',
      gradientFrom: '#1e293b',
      gradientTo: '#0f172a',
      gradientAngle: 180,
      kenBurns: false,
      kenBurnsScale: 1.2,
      kenBurnsDuration: 8000,
      kenBurnsDirection: 'in',
      kenBurnsBlurStart: 0,
      kenBurnsBlurEnd: 0,
      kenBurnsPanX: 0,
      kenBurnsPanY: 0,
      overlay: '#000000',
      overlayOpacity: 0.3,
    },
    duration: 0,
    tabLabel: '',
    layers: [defaultLayer('text')],
  };
}

/**
 * Normalizza un valore altezza nel formato { mode, value }.
 * Backward compat: numeri semplici → { mode: 'px', value: N }
 */
export function normalizeHeight(val) {
  if (val && typeof val === 'object' && val.mode) return val;
  if (typeof val === 'number' || (typeof val === 'string' && !isNaN(val))) {
    return { mode: 'px', value: parseInt(val) || 600 };
  }
  return null;
}

/**
 * Risolve un oggetto altezza in px per l'editor canvas.
 * @param {object} h - { mode: 'px'|'vh'|'ratio', value }
 * @param {number} canvasWidth - larghezza del canvas in px
 * @returns {number} altezza in px
 */
export function resolveHeightPx(h, canvasWidth) {
  if (!h) return null;
  const nh = normalizeHeight(h);
  if (!nh) return null;
  switch (nh.mode) {
    case 'vh':
      return Math.round((nh.value / 100) * 800); // simulato 800px viewport nell'editor
    case 'ratio': {
      const parts = String(nh.value).split(':');
      if (parts.length === 2) {
        const w = parseFloat(parts[0]) || 16;
        const hh = parseFloat(parts[1]) || 9;
        return Math.round(canvasWidth / w * hh);
      }
      return 600;
    }
    default:
      return nh.value || 600;
  }
}

export default {
  type: 'proslider',
  name: 'Pro Slider',
  icon: 'dashicons-slides',
  category: 'media',
  customEditor: 'proslider',

  defaults: {
    slides: [defaultSlide()],
    globalBackground: {
      type: 'color',
      image: '',
      video: '',
      color: '#1e293b',
      gradientFrom: '#1e293b',
      gradientTo: '#0f172a',
      gradientAngle: 180,
      kenBurns: false,
      kenBurnsScale: 1.2,
      kenBurnsDuration: 8000,
      kenBurnsDirection: 'in',
    },
    height: { mode: 'px', value: 600 },
    heightNotebook: null,
    heightTablet: null,
    heightMobile: null,
    autoplay: true,
    autoplaySpeed: 5000,
    pauseOnHover: true,
    loop: true,
    transition: 'fade',
    transitionDuration: 800,
    showArrows: true,
    arrowStyle: 'minimal',
    showDots: true,
    dotStyle: 'circles',
    showProgressBar: false,
    progressBarColor: '#3b82f6',
    progressBarHeight: 3,
    keyboard: true,
    swipe: true,
    mouseWheel: false,
    // Sizing mode: 'auto' | 'fullwidth' | 'fullscreen'
    sizingMode: 'auto',
    // Global layers (visibili su tutte le slide)
    globalLayers: [],
    // Parallax settings
    parallax: false,
    parallaxType: 'mouse', // 'mouse' | 'scroll' | 'both'
    parallaxIntensity: 5,
    // Scroll effects on slider visibility
    scrollEffect: 'none', // 'none' | 'fade' | 'blur' | 'fadeBlur'
    // Carousel mode
    carousel: false,
    carouselWidth: 80, // % of container per slide
    carouselGap: 10,   // px
    carouselSideScale: 0.85,
    carousel3D: false,
    // Navigation: thumbnails
    showThumbs: false,
    thumbPosition: 'bottom', // 'bottom' | 'top' | 'left' | 'right'
    // Navigation: tabs
    showTabs: false,
    tabPosition: 'bottom', // 'bottom' | 'top'
    // Scroll-fixed timeline (pin slider, animate on scroll)
    scrollTimeline: false,
    scrollTimelineDistance: 2000, // px of scroll for full timeline
  },

  fields: [
    { key: 'sizingMode', label: 'Modalita', type: 'select', options: [
      { value: 'auto', label: 'Auto' },
      { value: 'fullwidth', label: 'Full Width' },
      { value: 'fullscreen', label: 'Full Screen' },
    ]},

    { type: 'separator', label: 'Riproduzione' },
    { key: 'autoplay', label: 'Riproduzione automatica', type: 'toggle' },
    { key: 'autoplaySpeed', label: 'Velocita (ms)', type: 'number', min: 1000, max: 20000, step: 500,
      condition: { field: 'autoplay', value: true } },
    { key: 'pauseOnHover', label: 'Pausa al passaggio mouse', type: 'toggle',
      condition: { field: 'autoplay', value: true } },
    { key: 'loop', label: 'Ripeti', type: 'toggle' },

    { type: 'separator', label: 'Transizione' },
    { key: 'transition', label: 'Tipo transizione', type: 'select', options: [
      { value: 'fade', label: 'Dissolvenza' },
      { value: 'slide', label: 'Scorrimento' },
      { value: 'zoom', label: 'Zoom' },
      { value: 'crossFade', label: 'Cross Fade' },
      { value: 'slideOver', label: 'Slide Over' },
      { value: 'fadeThroughDark', label: 'Fade Nero' },
      { value: 'blur', label: 'Blur' },
      { value: 'flipH', label: 'Flip Orizzontale' },
      { value: 'flipV', label: 'Flip Verticale' },
      { value: 'cubeH', label: 'Cubo Orizzontale' },
      { value: 'cubeV', label: 'Cubo Verticale' },
      { value: 'push', label: 'Push' },
      { value: 'pushDown', label: 'Push Giu' },
      { value: 'stack', label: 'Stack' },
      { value: 'paperCut', label: 'Paper Cut' },
      { value: 'zoomFade', label: 'Zoom Fade' },
      { value: 'rotateSlide', label: 'Rotate Slide' },
      { value: 'curtain3D', label: 'Curtain 3D' },
      { value: 'slideUp', label: 'Slide Up' },
      { value: 'slideDown', label: 'Slide Down' },
    ]},
    { key: 'transitionDuration', label: 'Durata (ms)', type: 'number', min: 200, max: 3000, step: 100 },

    { type: 'separator', label: 'Navigazione' },
    { key: 'showArrows', label: 'Frecce', type: 'toggle' },
    { key: 'arrowStyle', label: 'Stile frecce', type: 'select', options: [
      { value: 'minimal', label: 'Minimal' },
      { value: 'rounded', label: 'Arrotondato' },
      { value: 'boxed', label: 'Box' },
      { value: 'outline', label: 'Outline' },
    ], condition: { field: 'showArrows', value: true } },
    { key: 'showDots', label: 'Punti', type: 'toggle' },
    { key: 'dotStyle', label: 'Stile punti', type: 'select', options: [
      { value: 'circles', label: 'Cerchi' },
      { value: 'bars', label: 'Barre' },
      { value: 'numbers', label: 'Numeri' },
      { value: 'dash', label: 'Trattini' },
    ], condition: { field: 'showDots', value: true } },
    { key: 'showProgressBar', label: 'Barra progresso', type: 'toggle' },
    { key: 'progressBarColor', label: 'Colore barra', type: 'color',
      condition: { field: 'showProgressBar', value: true } },
    { key: 'progressBarHeight', label: 'Altezza barra', type: 'number', min: 1, max: 10,
      condition: { field: 'showProgressBar', value: true } },
    { key: 'keyboard', label: 'Navigazione tastiera', type: 'toggle' },
    { key: 'swipe', label: 'Swipe touch', type: 'toggle' },
    { key: 'mouseWheel', label: 'Rotella mouse', type: 'toggle' },

    { type: 'separator', label: 'Miniature e Tab' },
    { key: 'showThumbs', label: 'Miniature', type: 'toggle' },
    { key: 'thumbPosition', label: 'Posizione miniature', type: 'select', options: [
      { value: 'bottom', label: 'Sotto' },
      { value: 'top', label: 'Sopra' },
      { value: 'left', label: 'Sinistra' },
      { value: 'right', label: 'Destra' },
    ], condition: { field: 'showThumbs', value: true } },
    { key: 'showTabs', label: 'Tab', type: 'toggle' },
    { key: 'tabPosition', label: 'Posizione tab', type: 'select', options: [
      { value: 'bottom', label: 'Sotto' },
      { value: 'top', label: 'Sopra' },
    ], condition: { field: 'showTabs', value: true } },

    { type: 'separator', label: 'Effetti' },
    { key: 'scrollEffect', label: 'Effetto scroll', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'fade', label: 'Fade' },
      { value: 'blur', label: 'Blur' },
      { value: 'fadeBlur', label: 'Fade + Blur' },
    ]},
    { key: 'parallax', label: 'Parallax', type: 'toggle' },
    { key: 'parallaxType', label: 'Tipo parallax', type: 'select', options: [
      { value: 'mouse', label: 'Mouse' },
      { value: 'scroll', label: 'Scroll' },
      { value: 'both', label: 'Entrambi' },
    ], condition: { field: 'parallax', value: true } },
    { key: 'parallaxIntensity', label: 'Intensita parallax', type: 'number', min: 1, max: 20,
      condition: { field: 'parallax', value: true } },

    { type: 'separator', label: 'Carosello' },
    { key: 'carousel', label: 'Modalita carosello', type: 'toggle' },
    { key: 'carouselWidth', label: 'Larghezza slide %', type: 'number', min: 40, max: 95,
      condition: { field: 'carousel', value: true } },
    { key: 'carouselGap', label: 'Gap (px)', type: 'number', min: 0, max: 60,
      condition: { field: 'carousel', value: true } },
    { key: 'carouselSideScale', label: 'Scala laterali', type: 'number', min: 0.5, max: 1, step: 0.05,
      condition: { field: 'carousel', value: true } },
    { key: 'carousel3D', label: 'Rotazione 3D', type: 'toggle',
      condition: { field: 'carousel', value: true } },

    { type: 'separator', label: 'Scroll Timeline' },
    { key: 'scrollTimeline', label: 'Scroll-fixed timeline', type: 'toggle' },
    { key: 'scrollTimelineDistance', label: 'Distanza scroll (px)', type: 'number', min: 500, max: 10000, step: 100,
      condition: { field: 'scrollTimeline', value: true } },
  ],

  // Helper esportati per l'editor
  helpers: { defaultSlide, defaultLayer, makeId },
};
