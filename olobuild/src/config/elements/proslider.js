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
    // Posizione e dimensione (% del canvas)
    x: 10,
    y: 30,
    width: 80,
    height: 'auto',
    // Stile
    fontSize: type === 'text' ? 48 : (type === 'button' ? 18 : 24),
    fontWeight: '700',
    color: '#ffffff',
    textAlign: 'left',
    bgColor: type === 'button' ? '#2563eb' : '',
    borderRadius: type === 'button' ? 6 : 0,
    padding: type === 'button' ? 16 : 0,
    opacity: 100,
    // Animazione
    animIn: 'fadeInUp',
    animInDuration: 800,
    animInDelay: 200,
    animOut: 'fadeOutDown',
    animOutDuration: 600,
    animOutDelay: 0,
    animEasing: 'ease',
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
      overlay: '#000000',
      overlayOpacity: 0.3,
    },
    duration: 0,
    layers: [defaultLayer('text')],
  };
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
    },
    height: 600,
    autoplay: true,
    autoplaySpeed: 5000,
    pauseOnHover: true,
    loop: true,
    transition: 'fade',
    transitionDuration: 800,
    showArrows: true,
    showDots: true,
    keyboard: true,
    swipe: true,
  },

  fields: [],

  // Helper esportati per l'editor
  helpers: { defaultSlide, defaultLayer, makeId },
};
