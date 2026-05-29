
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';
/**
 * Tile Pro Slider — split CONTENUTO/STILE (regola universale Olobuild).
 *
 * NOTA: il contenuto principale (slide/livelli) è gestito da un editor modale dedicato
 * (`customEditor: 'proslider'`); qui sotto split delle sole opzioni globali.
 *
 *   fields[]      → modalità sizing, riproduzione (autoplay/loop/swipe/keyboard/mouseWheel),
 *                   tipo transizione, visibilità navigazione (frecce/dots/thumbs/tabs/progress),
 *                   parallax/carousel toggles, scroll timeline (toggle + distanza)
 *   styleFields[] → preset, sfondo, tipografia, durata transizione/animazioni, stile frecce/dots/thumbs,
 *                   colore/altezza barra progresso, intensità parallax, geometria carosello,
 *                   bordi
 *   AVANZATE      → meta tecnico (id/class/condizioni)
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
    // Stato iniziale: true = il layer parte nascosto (display:none) e può essere mostrato via action toggleLayer
    initiallyHidden: false,
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
  name: t('Pro Slider'),
  icon: 'dashicons-slides',
  category: 'media',
  customEditor: 'proslider',

  defaults: {
    typography_preset: '',
    bg: { type: 'none' },
    preset: 'custom',
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
    scrollTimelineDistance: 2000, // px of scroll for full timeline,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'sizingMode', label: t('Modalita'), type: 'select', options: [
      { value: 'auto', label: t('Auto') },
      { value: 'fullwidth', label: t('Full Width') },
      { value: 'fullscreen', label: t('Full Screen') },
    ]},

    { type: 'separator', label: t('Riproduzione') },
    { key: 'autoplay', label: t('Riproduzione automatica'), type: 'toggle' },
    { key: 'autoplaySpeed', label: t('Velocita (ms)'), type: 'number', min: 1000, max: 20000, step: 500,
      condition: { field: 'autoplay', value: true } },
    { key: 'pauseOnHover', label: t('Pausa al passaggio mouse'), type: 'toggle',
      condition: { field: 'autoplay', value: true } },
    { key: 'loop', label: t('Ripeti'), type: 'toggle' },

    { type: 'separator', label: t('Transizione') },
    { key: 'transition', label: t('Tipo transizione'), type: 'select', options: [
      { value: 'fade', label: t('Dissolvenza') },
      { value: 'slide', label: t('Scorrimento') },
      { value: 'zoom', label: t('Zoom') },
      { value: 'crossFade', label: t('Cross Fade') },
      { value: 'slideOver', label: t('Slide Over') },
      { value: 'fadeThroughDark', label: t('Fade Nero') },
      { value: 'blur', label: t('Blur') },
      { value: 'flipH', label: t('Flip Orizzontale') },
      { value: 'flipV', label: t('Flip Verticale') },
      { value: 'cubeH', label: t('Cubo Orizzontale') },
      { value: 'cubeV', label: t('Cubo Verticale') },
      { value: 'push', label: t('Push') },
      { value: 'pushDown', label: t('Push Giu') },
      { value: 'stack', label: t('Stack') },
      { value: 'paperCut', label: t('Paper Cut') },
      { value: 'zoomFade', label: t('Zoom Fade') },
      { value: 'rotateSlide', label: t('Rotate Slide') },
      { value: 'curtain3D', label: t('Curtain 3D') },
      { value: 'slideUp', label: t('Slide Up') },
      { value: 'slideDown', label: t('Slide Down') },
    ]},

    { type: 'separator', label: t('Navigazione') },
    { key: 'showArrows', label: t('Frecce'), type: 'toggle' },
    { key: 'showDots', label: t('Punti'), type: 'toggle' },
    { key: 'showProgressBar', label: t('Barra progresso'), type: 'toggle' },
    { key: 'keyboard', label: t('Navigazione tastiera'), type: 'toggle' },
    { key: 'swipe', label: t('Swipe touch'), type: 'toggle' },
    { key: 'mouseWheel', label: t('Rotella mouse'), type: 'toggle' },

    { type: 'separator', label: t('Miniature e Tab') },
    { key: 'showThumbs', label: t('Miniature'), type: 'toggle' },
    { key: 'showTabs', label: t('Tab'), type: 'toggle' },

    { type: 'separator', label: t('Parallax') },
    { key: 'parallax', label: t('Parallax'), type: 'toggle' },
    { key: 'parallaxType', label: t('Tipo parallax'), type: 'select', options: [
      { value: 'mouse', label: t('Mouse') },
      { value: 'scroll', label: t('Scroll') },
      { value: 'both', label: t('Entrambi') },
    ], condition: { field: 'parallax', value: true } },

    { type: 'separator', label: t('Carosello') },
    { key: 'carousel', label: t('Modalita carosello'), type: 'toggle' },
    { key: 'carousel3D', label: t('Rotazione 3D'), type: 'toggle',
      condition: { field: 'carousel', value: true } },

    { type: 'separator', label: t('Scroll Timeline') },
    { key: 'scrollTimeline', label: t('Scroll-fixed timeline'), type: 'toggle' },
    { key: 'scrollTimelineDistance', label: t('Distanza scroll (px)'), type: 'number', min: 500, max: 10000, step: 100,
      condition: { field: 'scrollTimeline', value: true } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'cinema-hero',     label: t('Cinema Hero') },
      { value: 'magazine-cover',  label: t('Magazine Cover') },
      { value: 'editorial-split', label: t('Editorial Split') },
      { value: 'minimal-clean',   label: t('Minimal Clean') },
      { value: 'product-showcase', label: t('Product Showcase') },
      { value: 'glass-overlay',   label: t('Glass Overlay') },
      { value: 'neon-tron',       label: t('Neon Tron') },
      { value: 'brutalist-mega',  label: t('Brutalist Mega') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-vhs',       label: t('Retro VHS') },
      { value: 'tilt-parallax',   label: t('Tilt Parallax') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Transizione — aspetto') },
    { key: 'transitionDuration', label: t('Durata (ms)'), type: 'number', min: 200, max: 3000, step: 100 },

    { type: 'separator', label: t('Navigazione — stile') },
    { key: 'arrowStyle', label: t('Stile frecce'), type: 'select', options: [
      { value: 'minimal', label: t('Minimal') },
      { value: 'rounded', label: t('Arrotondato') },
      { value: 'boxed', label: t('Box') },
      { value: 'outline', label: t('Outline') },
    ], condition: { field: 'showArrows', value: true } },
    { key: 'dotStyle', label: t('Stile punti'), type: 'select', options: [
      { value: 'circles', label: t('Cerchi') },
      { value: 'bars', label: t('Barre') },
      { value: 'numbers', label: t('Numeri') },
      { value: 'dash', label: t('Trattini') },
    ], condition: { field: 'showDots', value: true } },
    { key: 'progressBarColor', label: t('Colore barra'), type: 'color',
      condition: { field: 'showProgressBar', value: true } },
    { key: 'progressBarHeight', label: t('Altezza barra'), type: 'number', min: 1, max: 10,
      condition: { field: 'showProgressBar', value: true } },

    { type: 'separator', label: t('Miniature e Tab — posizione') },
    { key: 'thumbPosition', label: t('Posizione miniature'), type: 'select', options: [
      { value: 'bottom', label: t('Sotto') },
      { value: 'top', label: t('Sopra') },
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ], condition: { field: 'showThumbs', value: true } },
    { key: 'tabPosition', label: t('Posizione tab'), type: 'select', options: [
      { value: 'bottom', label: t('Sotto') },
      { value: 'top', label: t('Sopra') },
    ], condition: { field: 'showTabs', value: true } },

    { type: 'separator', label: t('Effetti') },
    { key: 'scrollEffect', label: t('Effetto scroll'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'fade', label: t('Fade') },
      { value: 'blur', label: t('Blur') },
      { value: 'fadeBlur', label: t('Fade + Blur') },
    ]},
    { key: 'parallaxIntensity', label: t('Intensita parallax'), type: 'number', min: 1, max: 20,
      condition: { field: 'parallax', value: true } },

    { type: 'separator', label: t('Carosello — dimensioni') },
    { key: 'carouselWidth', label: t('Larghezza slide %'), type: 'number', min: 40, max: 95,
      condition: { field: 'carousel', value: true } },
    { key: 'carouselGap', label: t('Gap (px)'), type: 'number', min: 0, max: 60,
      condition: { field: 'carousel', value: true } },
    { key: 'carouselSideScale', label: t('Scala laterali'), type: 'number', min: 0.5, max: 1, step: 0.05,
      condition: { field: 'carousel', value: true } },

    ...borderFields(),
  ],

  // Helper esportati per l'editor
  helpers: { defaultSlide, defaultLayer, makeId },
};
