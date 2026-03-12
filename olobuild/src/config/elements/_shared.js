/**
 * Shared field presets for element definitions.
 * Elements can spread these into their fields arrays.
 */

// ─── Link fields preset ───
export const linkFields = [
  { key: 'link_url', label: 'URL link', type: 'text' },
  { key: 'link_target', label: 'Apri in', type: 'select', options: [
    { value: '_self', label: 'Stessa finestra' },
    { value: '_blank', label: 'Nuova scheda' },
  ]},
];

export const targetField = {
  key: 'target', label: 'Apri in', type: 'select', options: [
    { value: '_self', label: 'Stessa finestra' },
    { value: '_blank', label: 'Nuova scheda' },
  ],
};

export const alignmentField = {
  key: 'alignment', label: 'Allineamento', type: 'select', options: [
    { value: 'left', label: 'Sinistra' },
    { value: 'center', label: 'Centro' },
    { value: 'right', label: 'Destra' },
  ],
};

export const columnWidthOptions = [
  { value: '', label: 'Auto' },
  { value: '1-1', label: '1/1' },
  { value: '1-2', label: '1/2' },
  { value: '1-3', label: '1/3' },
  { value: '2-3', label: '2/3' },
  { value: '1-4', label: '1/4' },
  { value: '3-4', label: '3/4' },
  { value: '1-5', label: '1/5' },
  { value: '2-5', label: '2/5' },
  { value: '3-5', label: '3/5' },
  { value: '4-5', label: '4/5' },
  { value: '1-6', label: '1/6' },
  { value: '5-6', label: '5/6' },
];

// ─── Shadow field — preset + custom ───
export const shadowField = [
  { key: 'shadow', label: 'Ombra', type: 'select', options: [
    { value: 'none', label: 'Nessuna' },
    { value: 'sm', label: 'Leggera' },
    { value: 'md', label: 'Media' },
    { value: 'lg', label: 'Forte' },
    { value: 'xl', label: 'Molto forte' },
    { value: 'custom', label: 'Personalizzata' },
  ]},
  { key: 'shadow_h', label: 'Offset H (px)', type: 'range', min: -50, max: 50, step: 1,
    condition: { field: 'shadow', op: 'eq', value: 'custom' } },
  { key: 'shadow_v', label: 'Offset V (px)', type: 'range', min: -50, max: 50, step: 1,
    condition: { field: 'shadow', op: 'eq', value: 'custom' } },
  { key: 'shadow_blur', label: 'Sfocatura (px)', type: 'range', min: 0, max: 100, step: 1,
    condition: { field: 'shadow', op: 'eq', value: 'custom' } },
  { key: 'shadow_spread', label: 'Espansione (px)', type: 'range', min: -50, max: 50, step: 1,
    condition: { field: 'shadow', op: 'eq', value: 'custom' } },
  { key: 'shadow_color', label: 'Colore ombra', type: 'color',
    condition: { field: 'shadow', op: 'eq', value: 'custom' } },
  { key: 'shadow_inset', label: 'Ombra interna', type: 'toggle',
    condition: { field: 'shadow', op: 'eq', value: 'custom' } },
];

export const shadowDefaults = {
  shadow: 'none',
  shadow_h: '0',
  shadow_v: '4',
  shadow_blur: '10',
  shadow_spread: '0',
  shadow_color: 'rgba(0,0,0,0.15)',
  shadow_inset: false,
};

// ─── CSS Filters shared fields ───
export const filterFields = [
  { type: 'separator', label: 'Filtri CSS' },
  { key: 'filter_blur', label: 'Sfocatura (px)', type: 'range', min: 0, max: 20, step: 1 },
  { key: 'filter_brightness', label: 'Luminosità (%)', type: 'range', min: 0, max: 200, step: 5 },
  { key: 'filter_contrast', label: 'Contrasto (%)', type: 'range', min: 0, max: 200, step: 5 },
  { key: 'filter_saturate', label: 'Saturazione (%)', type: 'range', min: 0, max: 200, step: 5 },
  { key: 'filter_grayscale', label: 'Scala di grigi (%)', type: 'range', min: 0, max: 100, step: 5 },
  { key: 'filter_sepia', label: 'Seppia (%)', type: 'range', min: 0, max: 100, step: 5 },
];

export const filterDefaults = {
  filter_blur: '0',
  filter_brightness: '100',
  filter_contrast: '100',
  filter_saturate: '100',
  filter_grayscale: '0',
  filter_sepia: '0',
};

// ─── Entrance animations shared fields ───
export const entranceAnimationField = {
  key: 'entrance_animation', label: 'Animazione ingresso', type: 'select', options: [
    { value: 'none', label: 'Nessuna' },
    { value: 'fade', label: 'Dissolvenza' },
    { value: 'slide-up', label: 'Scorrimento dal basso' },
    { value: 'slide-left', label: 'Scorrimento da sinistra' },
    { value: 'slide-right', label: 'Scorrimento da destra' },
    { value: 'slide-down', label: 'Scorrimento dall\'alto' },
    { value: 'zoom-in', label: 'Zoom in' },
    { value: 'zoom-out', label: 'Zoom out' },
    { value: 'flip', label: 'Flip' },
    { value: 'rotate-in', label: 'Rotazione oraria' },
    { value: 'rotate-ccw', label: 'Rotazione antioraria' },
    { value: 'bounce', label: 'Rimbalzo' },
    { value: 'elastic', label: 'Elastico' },
    { value: 'blur-in', label: 'Sfocatura' },
    { value: 'swing', label: 'Oscillazione' },
    { value: 'rubber', label: 'Gomma' },
    { value: 'jello', label: 'Gelatina' },
    { value: 'back-in-left', label: 'Ritorno da sinistra' },
    { value: 'back-in-right', label: 'Ritorno da destra' },
    { value: 'typewriter', label: 'Macchina da scrivere' },
    { value: 'fade-up-big', label: 'Grande dissolvenza dal basso' },
    { value: 'fade-down-big', label: 'Grande dissolvenza dall\'alto' },
    { value: 'lightspeed-left', label: 'Velocità luce da sinistra' },
    { value: 'lightspeed-right', label: 'Velocità luce da destra' },
    { value: 'roll-in', label: 'Rotolamento in entrata' },
    { value: 'jack-in-box', label: 'Scatola sorpresa' },
    { value: 'hinge', label: 'Cardine che cade' },
    { value: 'flip-y', label: 'Capovolgimento asse Y' },
    { value: 'flip-x', label: 'Capovolgimento asse X' },
    { value: 'zoom-in-down', label: 'Zoom + discesa' },
    { value: 'zoom-in-up', label: 'Zoom + salita' },
    { value: 'bounce-left', label: 'Rimbalzo da sinistra' },
    { value: 'bounce-right', label: 'Rimbalzo da destra' },
    { value: 'skew-in', label: 'Distorsione in entrata' },
    { value: 'curtain-reveal', label: 'Effetto tendina' },
    { value: 'blur-zoom', label: 'Sfocatura + Zoom' },
  ],
};

export const entranceStaggerFields = [
  { key: 'entrance_stagger', label: 'Stagger figli', type: 'toggle',
    description: 'Anima i figli uno dopo l\'altro con ritardo incrementale',
    condition: { field: 'entrance_animation', operator: '!=', value: 'none' } },
  { key: 'entrance_stagger_delay', label: 'Ritardo stagger (ms)', type: 'range', min: 50, max: 500, step: 25,
    condition: { field: 'entrance_stagger', op: 'eq', value: true } },
];

export const entranceAnimationDefault = {
  entrance_animation: 'none',
  entrance_stagger: false,
  entrance_stagger_delay: '100',
};

// ─── Sticky fields preset ───
export const stickyFields = [
  { type: 'separator', label: 'Sticky' },
  { key: 'sticky', label: 'Sticky', type: 'toggle' },
  { key: 'sticky_position', label: 'Posizione', type: 'select', options: [
    { value: 'top', label: 'In alto' },
    { value: 'bottom', label: 'In basso' },
  ], condition: { field: 'sticky', op: 'eq', value: true } },
  { key: 'sticky_offset', label: 'Offset (px)', type: 'range', min: 0, max: 200, step: 5,
    condition: { field: 'sticky', op: 'eq', value: true } },
  { key: 'sticky_on_mobile', label: 'Sticky su mobile', type: 'toggle',
    condition: { field: 'sticky', op: 'eq', value: true } },
];

export const stickyDefaults = {
  sticky: false,
  sticky_position: 'top',
  sticky_offset: '0',
  sticky_on_mobile: true,
};

// ─── Conditional visibility fields preset ───
export const conditionFields = [
  { type: 'separator', label: 'Visibilità condizionale' },
  { key: 'cond_type', label: 'Condizione', type: 'select', options: [
    { value: '', label: 'Sempre visibile' },
    { value: 'logged_in', label: 'Solo utenti loggati' },
    { value: 'logged_out', label: 'Solo visitatori' },
    { value: 'role', label: 'Solo ruolo specifico' },
    { value: 'mobile', label: 'Solo mobile' },
    { value: 'desktop', label: 'Solo desktop' },
    { value: 'date_after', label: 'Dopo una data' },
    { value: 'date_before', label: 'Prima di una data' },
    { value: 'has_featured_image', label: 'Ha immagine in evidenza' },
    { value: 'is_front_page', label: 'Solo front page' },
    { value: 'is_single', label: 'Solo singolo post' },
    { value: 'is_page', label: 'Solo pagine' },
    { value: 'is_archive', label: 'Solo archivi' },
    { value: 'is_search', label: 'Solo pagina ricerca' },
    { value: 'is_404', label: 'Solo pagina 404' },
    { value: 'post_type', label: 'Solo post type specifico' },
    { value: 'has_children', label: 'Ha sotto-pagine' },
    { value: 'is_author', label: 'Solo pagina autore' },
    { value: 'url_contains', label: 'URL contiene...' },
  ] },
  { key: 'cond_role', label: 'Ruolo richiesto', type: 'text', placeholder: 'administrator',
    condition: { field: 'cond_type', op: 'eq', value: 'role' } },
  { key: 'cond_date', label: 'Data', type: 'date',
    condition: { field: 'cond_type', op: 'in', value: ['date_after', 'date_before'] } },
  { key: 'cond_post_type', label: 'Post type', type: 'text', placeholder: 'post',
    condition: { field: 'cond_type', op: 'eq', value: 'post_type' } },
  { key: 'cond_url_contains', label: 'Stringa nell\'URL', type: 'text', placeholder: '/blog/',
    condition: { field: 'cond_type', op: 'eq', value: 'url_contains' } },
];

export const conditionDefaults = {
  cond_type: '',
  cond_role: '',
  cond_date: '',
  cond_post_type: '',
  cond_url_contains: '',
};

// ─── Mouse effects fields preset (3D tilt + cursor tracking) ───
export const mouseFields = [
  { type: 'separator', label: 'Effetti mouse' },
  { key: 'mouse_tilt', label: 'Tilt 3D al hover', type: 'toggle' },
  { key: 'mouse_tilt_intensity', label: 'Intensità tilt', type: 'range', min: 5, max: 30, step: 1,
    condition: { field: 'mouse_tilt', op: 'eq', value: true } },
  { key: 'mouse_track', label: 'Segui cursore', type: 'toggle' },
  { key: 'mouse_track_speed', label: 'Velocità tracking', type: 'range', min: 1, max: 10,
    condition: { field: 'mouse_track', op: 'eq', value: true } },
];

export const mouseDefaults = {
  mouse_tilt: false,
  mouse_tilt_intensity: '15',
  mouse_track: false,
  mouse_track_speed: '3',
};

// ─── Scroll-linked effects fields preset ───
export const scrollEffectFields = [
  { type: 'separator', label: 'Effetti su scroll' },
  { key: 'scroll_effect_opacity', label: 'Opacità su scroll', type: 'toggle' },
  { key: 'scroll_opacity_start', label: 'Opacità inizio', type: 'range', min: 0, max: 100,
    condition: { scroll_effect_opacity: [true] } },
  { key: 'scroll_opacity_end', label: 'Opacità fine', type: 'range', min: 0, max: 100,
    condition: { scroll_effect_opacity: [true] } },
  { key: 'scroll_effect_scale', label: 'Scala su scroll', type: 'toggle' },
  { key: 'scroll_scale_start', label: 'Scala inizio (%)', type: 'range', min: 50, max: 150,
    condition: { scroll_effect_scale: [true] } },
  { key: 'scroll_scale_end', label: 'Scala fine (%)', type: 'range', min: 50, max: 150,
    condition: { scroll_effect_scale: [true] } },
  { key: 'scroll_effect_rotate', label: 'Rotazione su scroll', type: 'toggle' },
  { key: 'scroll_rotate_start', label: 'Gradi inizio', type: 'range', min: -180, max: 180,
    condition: { scroll_effect_rotate: [true] } },
  { key: 'scroll_rotate_end', label: 'Gradi fine', type: 'range', min: -180, max: 180,
    condition: { scroll_effect_rotate: [true] } },
  { key: 'scroll_effect_translatex', label: 'Spostamento X su scroll', type: 'toggle' },
  { key: 'scroll_translatex_start', label: 'X inizio (px)', type: 'range', min: -200, max: 200,
    condition: { scroll_effect_translatex: [true] } },
  { key: 'scroll_translatex_end', label: 'X fine (px)', type: 'range', min: -200, max: 200,
    condition: { scroll_effect_translatex: [true] } },
];

export const scrollEffectDefaults = {
  scroll_effect_opacity: false,
  scroll_opacity_start: '0',
  scroll_opacity_end: '100',
  scroll_effect_scale: false,
  scroll_scale_start: '80',
  scroll_scale_end: '100',
  scroll_effect_rotate: false,
  scroll_rotate_start: '-15',
  scroll_rotate_end: '0',
  scroll_effect_translatex: false,
  scroll_translatex_start: '-50',
  scroll_translatex_end: '0',
};

// ─── Enhanced scroll effects (with blur + translateY) ───
export const scrollEffectFieldsEnhanced = [
  ...scrollEffectFields,
  { key: 'scroll_effect_blur', label: 'Sfocatura su scroll', type: 'toggle' },
  { key: 'scroll_blur_start', label: 'Sfocatura inizio (px)', type: 'range', min: 0, max: 20,
    condition: { scroll_effect_blur: [true] } },
  { key: 'scroll_blur_end', label: 'Sfocatura fine (px)', type: 'range', min: 0, max: 20,
    condition: { scroll_effect_blur: [true] } },
  { key: 'scroll_effect_translatey', label: 'Spostamento Y su scroll', type: 'toggle' },
  { key: 'scroll_translatey_start', label: 'Y inizio (px)', type: 'range', min: -300, max: 300,
    condition: { scroll_effect_translatey: [true] } },
  { key: 'scroll_translatey_end', label: 'Y fine (px)', type: 'range', min: -300, max: 300,
    condition: { scroll_effect_translatey: [true] } },
];

export const scrollEffectDefaultsEnhanced = {
  ...scrollEffectDefaults,
  scroll_effect_blur: false,
  scroll_blur_start: '0',
  scroll_blur_end: '10',
  scroll_effect_translatey: false,
  scroll_translatey_start: '-100',
  scroll_translatey_end: '0',
};

// ─── Custom CSS per elemento ───
export const customCssField = {
  key: 'custom_css',
  label: 'CSS Personalizzato',
  type: 'code',
  language: 'css',
  placeholder: 'selector {\n  /* il tuo CSS */\n}',
  description: 'Usa "selector" come riferimento a questo elemento',
  section: 'Avanzato',
};

export const customCssDefault = {
  custom_css: '',
};


// ═══════════════════════════════════════════════════════════════════
//  NEW SHARED PRESETS
// ═══════════════════════════════════════════════════════════════════

// ─── 1. CSS Transform controls ───
export const transformFields = [
  { type: 'separator', label: 'Trasformazioni CSS' },
  { key: 'transform_rotate', label: 'Rotazione (deg)', type: 'range', min: -360, max: 360, step: 1 },
  { key: 'transform_rotateX', label: 'Rotazione X (deg)', type: 'range', min: -180, max: 180, step: 1 },
  { key: 'transform_rotateY', label: 'Rotazione Y (deg)', type: 'range', min: -180, max: 180, step: 1 },
  { key: 'transform_scale', label: 'Scala', type: 'range', min: 0.1, max: 3, step: 0.05 },
  { key: 'transform_translateX', label: 'Traslazione X (px)', type: 'range', min: -500, max: 500, step: 1 },
  { key: 'transform_translateY', label: 'Traslazione Y (px)', type: 'range', min: -500, max: 500, step: 1 },
  { key: 'transform_skewX', label: 'Inclinazione X (deg)', type: 'range', min: -45, max: 45, step: 1 },
  { key: 'transform_skewY', label: 'Inclinazione Y (deg)', type: 'range', min: -45, max: 45, step: 1 },
  { key: 'transform_origin', label: 'Punto di origine', type: 'select', options: [
    { value: 'center', label: 'Centro' },
    { value: 'top-left', label: 'Alto sinistra' },
    { value: 'top-center', label: 'Alto centro' },
    { value: 'top-right', label: 'Alto destra' },
    { value: 'center-left', label: 'Centro sinistra' },
    { value: 'center-right', label: 'Centro destra' },
    { value: 'bottom-left', label: 'Basso sinistra' },
    { value: 'bottom-center', label: 'Basso centro' },
    { value: 'bottom-right', label: 'Basso destra' },
  ]},
];

export const transformDefaults = {
  transform_rotate: '0',
  transform_rotateX: '0',
  transform_rotateY: '0',
  transform_scale: '1',
  transform_translateX: '0',
  transform_translateY: '0',
  transform_skewX: '0',
  transform_skewY: '0',
  transform_origin: 'center',
};

// ─── 2. Box Shadow — ora unificato in shadowField (vedi sopra) ───
// boxShadowFields e boxShadowDefaults rimossi — usare shadowField / shadowDefaults

// ─── 3. Text Shadow ───
export const textShadowFields = [
  { type: 'separator', label: 'Ombra testo' },
  { key: 'text_shadow_enabled', label: 'Ombra testo', type: 'toggle' },
  { key: 'text_shadow_h', label: 'Offset orizzontale (px)', type: 'range', min: -20, max: 20, step: 1,
    condition: { field: 'text_shadow_enabled', op: 'eq', value: true } },
  { key: 'text_shadow_v', label: 'Offset verticale (px)', type: 'range', min: -20, max: 20, step: 1,
    condition: { field: 'text_shadow_enabled', op: 'eq', value: true } },
  { key: 'text_shadow_blur', label: 'Sfocatura (px)', type: 'range', min: 0, max: 40, step: 1,
    condition: { field: 'text_shadow_enabled', op: 'eq', value: true } },
  { key: 'text_shadow_color', label: 'Colore ombra', type: 'color',
    condition: { field: 'text_shadow_enabled', op: 'eq', value: true } },
];

export const textShadowDefaults = {
  text_shadow_enabled: false,
  text_shadow_h: '1',
  text_shadow_v: '1',
  text_shadow_blur: '3',
  text_shadow_color: 'rgba(0,0,0,0.3)',
};

// ─── 4. Backdrop Filter ───
export const backdropFilterFields = [
  { type: 'separator', label: 'Filtro sfondo (backdrop)' },
  { key: 'backdrop_blur', label: 'Sfocatura sfondo (px)', type: 'range', min: 0, max: 30, step: 1 },
  { key: 'backdrop_brightness', label: 'Luminosità sfondo (%)', type: 'range', min: 0, max: 200, step: 5 },
  { key: 'backdrop_contrast', label: 'Contrasto sfondo (%)', type: 'range', min: 0, max: 200, step: 5 },
  { key: 'backdrop_saturate', label: 'Saturazione sfondo (%)', type: 'range', min: 0, max: 200, step: 5 },
];

export const backdropFilterDefaults = {
  backdrop_blur: '0',
  backdrop_brightness: '100',
  backdrop_contrast: '100',
  backdrop_saturate: '100',
};

// ─── 6. Infinite (looping) animations ───
export const infiniteAnimationFields = [
  { type: 'separator', label: 'Animazione continua' },
  { key: 'infinite_animation', label: 'Animazione', type: 'select', options: [
    { value: 'none', label: 'Nessuna' },
    { value: 'float', label: 'Galleggiamento' },
    { value: 'pulse', label: 'Pulsazione' },
    { value: 'spin', label: 'Rotazione' },
    { value: 'wiggle', label: 'Dondolio' },
    { value: 'bounce', label: 'Rimbalzo' },
    { value: 'swing', label: 'Oscillazione' },
    { value: 'breathe', label: 'Respiro' },
  ]},
  { key: 'infinite_speed', label: 'Velocità (s)', type: 'range', min: 1, max: 10, step: 0.5,
    condition: { field: 'infinite_animation', operator: '!=', value: 'none' } },
  { key: 'infinite_direction', label: 'Direzione', type: 'select', options: [
    { value: 'normal', label: 'Normale' },
    { value: 'alternate', label: 'Alternata' },
    { value: 'reverse', label: 'Inversa' },
  ], condition: { field: 'infinite_animation', operator: '!=', value: 'none' } },
];

export const infiniteAnimationDefaults = {
  infinite_animation: 'none',
  infinite_speed: '3',
  infinite_direction: 'normal',
};

// ─── 7. CSS Mask / Clip-path ───
export const maskFields = [
  { type: 'separator', label: 'Maschera forma' },
  { key: 'mask_type', label: 'Tipo maschera', type: 'select', options: [
    { value: 'none', label: 'Nessuna' },
    { value: 'circle', label: 'Cerchio' },
    { value: 'ellipse', label: 'Ellisse' },
    { value: 'triangle', label: 'Triangolo' },
    { value: 'hexagon', label: 'Esagono' },
    { value: 'star', label: 'Stella' },
    { value: 'diamond', label: 'Diamante' },
    { value: 'blob', label: 'Blob' },
    { value: 'custom', label: 'Personalizzata' },
  ]},
  { key: 'mask_size', label: 'Dimensione maschera', type: 'select', options: [
    { value: 'contain', label: 'Contenuta' },
    { value: 'cover', label: 'Copertura' },
    { value: '50%', label: '50%' },
    { value: '75%', label: '75%' },
    { value: '100%', label: '100%' },
  ], condition: { field: 'mask_type', operator: '!=', value: 'none' } },
  { key: 'mask_position', label: 'Posizione maschera', type: 'select', options: [
    { value: 'center', label: 'Centro' },
    { value: 'top', label: 'Alto' },
    { value: 'bottom', label: 'Basso' },
    { value: 'left', label: 'Sinistra' },
    { value: 'right', label: 'Destra' },
    { value: 'top-left', label: 'Alto sinistra' },
    { value: 'top-right', label: 'Alto destra' },
    { value: 'bottom-left', label: 'Basso sinistra' },
    { value: 'bottom-right', label: 'Basso destra' },
  ], condition: { field: 'mask_type', operator: '!=', value: 'none' } },
  { key: 'mask_repeat', label: 'Ripeti maschera', type: 'select', options: [
    { value: 'no-repeat', label: 'Non ripetere' },
    { value: 'repeat', label: 'Ripeti' },
  ], condition: { field: 'mask_type', operator: '!=', value: 'none' } },
];

export const maskDefaults = {
  mask_type: 'none',
  mask_size: 'contain',
  mask_position: 'center',
  mask_repeat: 'no-repeat',
};

// ─── 8. Overflow ───
export const overflowField = {
  key: 'overflow', label: 'Overflow contenuto', type: 'select', options: [
    { value: 'visible', label: 'Visibile' },
    { value: 'hidden', label: 'Nascosto' },
    { value: 'auto', label: 'Automatico' },
    { value: 'scroll', label: 'Scroll' },
    { value: 'clip', label: 'Taglia' },
  ],
};

export const overflowDefault = {
  overflow: 'visible',
};

// ─── 10. Enhanced conditional visibility ───
const conditionTypeOptions = [
  { value: '', label: 'Sempre visibile' },
  { value: 'logged_in', label: 'Solo utenti loggati' },
  { value: 'logged_out', label: 'Solo visitatori' },
  { value: 'role', label: 'Solo ruolo specifico' },
  { value: 'mobile', label: 'Solo mobile' },
  { value: 'desktop', label: 'Solo desktop' },
  { value: 'date_after', label: 'Dopo una data' },
  { value: 'date_before', label: 'Prima di una data' },
  { value: 'has_featured_image', label: 'Ha immagine in evidenza' },
  { value: 'is_front_page', label: 'Solo front page' },
  { value: 'is_single', label: 'Solo singolo post' },
  { value: 'is_page', label: 'Solo pagine' },
  { value: 'is_archive', label: 'Solo archivi' },
  { value: 'is_search', label: 'Solo pagina ricerca' },
  { value: 'is_404', label: 'Solo pagina 404' },
  { value: 'post_type', label: 'Solo post type specifico' },
  { value: 'has_children', label: 'Ha sotto-pagine' },
  { value: 'is_author', label: 'Solo pagina autore' },
  { value: 'url_contains', label: 'URL contiene...' },
  { value: 'day_of_week', label: 'Giorno della settimana' },
  { value: 'time_range', label: 'Fascia oraria' },
  { value: 'referrer_url', label: 'Provenienza (referrer)' },
  { value: 'browser', label: 'Browser specifico' },
  { value: 'woo_cart_empty', label: 'Carrello WooCommerce vuoto' },
  { value: 'woo_cart_has_items', label: 'Carrello WooCommerce con prodotti' },
  { value: 'custom_field_equals', label: 'Campo personalizzato uguale a...' },
];

export const conditionFieldsEnhanced = [
  { type: 'separator', label: 'Visibilità condizionale' },
  { key: 'cond_logic', label: 'Logica condizioni', type: 'select', options: [
    { value: 'and', label: 'Tutte le condizioni (AND)' },
    { value: 'or', label: 'Almeno una condizione (OR)' },
  ]},
  { key: 'cond_type', label: 'Condizione 1', type: 'select', options: conditionTypeOptions },
  { key: 'cond_role', label: 'Ruolo richiesto', type: 'text', placeholder: 'administrator',
    condition: { field: 'cond_type', op: 'eq', value: 'role' } },
  { key: 'cond_date', label: 'Data', type: 'date',
    condition: { field: 'cond_type', op: 'in', value: ['date_after', 'date_before'] } },
  { key: 'cond_post_type', label: 'Post type', type: 'text', placeholder: 'post',
    condition: { field: 'cond_type', op: 'eq', value: 'post_type' } },
  { key: 'cond_url_contains', label: 'Stringa nell\'URL', type: 'text', placeholder: '/blog/',
    condition: { field: 'cond_type', op: 'eq', value: 'url_contains' } },
  { key: 'cond_day', label: 'Giorno', type: 'select', options: [
    { value: 'monday', label: 'Lunedì' },
    { value: 'tuesday', label: 'Martedì' },
    { value: 'wednesday', label: 'Mercoledì' },
    { value: 'thursday', label: 'Giovedì' },
    { value: 'friday', label: 'Venerdì' },
    { value: 'saturday', label: 'Sabato' },
    { value: 'sunday', label: 'Domenica' },
  ], condition: { field: 'cond_type', op: 'eq', value: 'day_of_week' } },
  { key: 'cond_time_from', label: 'Ora inizio', type: 'time',
    condition: { field: 'cond_type', op: 'eq', value: 'time_range' } },
  { key: 'cond_time_to', label: 'Ora fine', type: 'time',
    condition: { field: 'cond_type', op: 'eq', value: 'time_range' } },
  { key: 'cond_referrer', label: 'URL provenienza', type: 'text', placeholder: 'google.com',
    condition: { field: 'cond_type', op: 'eq', value: 'referrer_url' } },
  { key: 'cond_browser', label: 'Browser', type: 'select', options: [
    { value: 'chrome', label: 'Chrome' },
    { value: 'firefox', label: 'Firefox' },
    { value: 'safari', label: 'Safari' },
    { value: 'edge', label: 'Edge' },
    { value: 'opera', label: 'Opera' },
  ], condition: { field: 'cond_type', op: 'eq', value: 'browser' } },
  { key: 'cond_custom_field_key', label: 'Nome campo', type: 'text', placeholder: 'meta_key',
    condition: { field: 'cond_type', op: 'eq', value: 'custom_field_equals' } },
  { key: 'cond_custom_field_value', label: 'Valore campo', type: 'text', placeholder: 'valore',
    condition: { field: 'cond_type', op: 'eq', value: 'custom_field_equals' } },
  { key: 'cond_2_type', label: 'Condizione 2', type: 'select', options: conditionTypeOptions },
  { key: 'cond_2_role', label: 'Ruolo richiesto (cond. 2)', type: 'text', placeholder: 'administrator',
    condition: { field: 'cond_2_type', op: 'eq', value: 'role' } },
  { key: 'cond_2_date', label: 'Data (cond. 2)', type: 'date',
    condition: { field: 'cond_2_type', op: 'in', value: ['date_after', 'date_before'] } },
  { key: 'cond_2_post_type', label: 'Post type (cond. 2)', type: 'text', placeholder: 'post',
    condition: { field: 'cond_2_type', op: 'eq', value: 'post_type' } },
  { key: 'cond_2_url_contains', label: 'Stringa nell\'URL (cond. 2)', type: 'text', placeholder: '/blog/',
    condition: { field: 'cond_2_type', op: 'eq', value: 'url_contains' } },
  { key: 'cond_2_day', label: 'Giorno (cond. 2)', type: 'select', options: [
    { value: 'monday', label: 'Lunedì' },
    { value: 'tuesday', label: 'Martedì' },
    { value: 'wednesday', label: 'Mercoledì' },
    { value: 'thursday', label: 'Giovedì' },
    { value: 'friday', label: 'Venerdì' },
    { value: 'saturday', label: 'Sabato' },
    { value: 'sunday', label: 'Domenica' },
  ], condition: { field: 'cond_2_type', op: 'eq', value: 'day_of_week' } },
  { key: 'cond_2_time_from', label: 'Ora inizio (cond. 2)', type: 'time',
    condition: { field: 'cond_2_type', op: 'eq', value: 'time_range' } },
  { key: 'cond_2_time_to', label: 'Ora fine (cond. 2)', type: 'time',
    condition: { field: 'cond_2_type', op: 'eq', value: 'time_range' } },
  { key: 'cond_2_referrer', label: 'URL provenienza (cond. 2)', type: 'text', placeholder: 'google.com',
    condition: { field: 'cond_2_type', op: 'eq', value: 'referrer_url' } },
  { key: 'cond_2_browser', label: 'Browser (cond. 2)', type: 'select', options: [
    { value: 'chrome', label: 'Chrome' },
    { value: 'firefox', label: 'Firefox' },
    { value: 'safari', label: 'Safari' },
    { value: 'edge', label: 'Edge' },
    { value: 'opera', label: 'Opera' },
  ], condition: { field: 'cond_2_type', op: 'eq', value: 'browser' } },
  { key: 'cond_2_custom_field_key', label: 'Nome campo (cond. 2)', type: 'text', placeholder: 'meta_key',
    condition: { field: 'cond_2_type', op: 'eq', value: 'custom_field_equals' } },
  { key: 'cond_2_custom_field_value', label: 'Valore campo (cond. 2)', type: 'text', placeholder: 'valore',
    condition: { field: 'cond_2_type', op: 'eq', value: 'custom_field_equals' } },
];

export const conditionDefaultsEnhanced = {
  ...conditionDefaults,
  cond_logic: 'and',
  cond_day: '',
  cond_time_from: '',
  cond_time_to: '',
  cond_referrer: '',
  cond_browser: '',
  cond_custom_field_key: '',
  cond_custom_field_value: '',
  cond_2_type: '',
  cond_2_role: '',
  cond_2_date: '',
  cond_2_post_type: '',
  cond_2_url_contains: '',
  cond_2_day: '',
  cond_2_time_from: '',
  cond_2_time_to: '',
  cond_2_referrer: '',
  cond_2_browser: '',
  cond_2_custom_field_key: '',
  cond_2_custom_field_value: '',
};

// ─── 11. Responsive field helper ───
/**
 * Takes a single field definition and returns an array of 3 fields:
 * desktop (original), tablet (key_tablet), mobile (key_mobile).
 * @param {Object} field - Field definition with at least { key, label, type }
 * @returns {Array} Array of 3 field definitions
 */
export function makeResponsive(field) {
  const { key, label, ...rest } = field;
  return [
    { key, label: `${label} (desktop)`, ...rest },
    { key: `${key}_widescreen`, label: `${label} (widescreen)`, ...rest },
    { key: `${key}_tablet_landscape`, label: `${label} (tablet landscape)`, ...rest },
    { key: `${key}_tablet`, label: `${label} (tablet)`, ...rest },
    { key: `${key}_mobile_landscape`, label: `${label} (mobile landscape)`, ...rest },
    { key: `${key}_mobile`, label: `${label} (mobile)`, ...rest },
  ];
}

// ─── 12. Popup trigger controls ───
export const popupTriggerFields = [
  { type: 'separator', label: 'Trigger popup' },
  { key: 'popup_trigger', label: 'Attivazione', type: 'select', options: [
    { value: 'click', label: 'Click' },
    { value: 'page_load', label: 'Caricamento pagina' },
    { value: 'scroll_percent', label: 'Percentuale scroll' },
    { value: 'exit_intent', label: 'Intenzione di uscita' },
    { value: 'time_delay', label: 'Ritardo temporale' },
    { value: 'inactivity', label: 'Inattività utente' },
  ]},
  { key: 'popup_delay', label: 'Ritardo (secondi)', type: 'range', min: 0, max: 30, step: 1,
    condition: { field: 'popup_trigger', op: 'in', value: ['page_load', 'time_delay'] } },
  { key: 'popup_scroll_percent', label: 'Percentuale scroll (%)', type: 'range', min: 10, max: 100, step: 10,
    condition: { field: 'popup_trigger', op: 'eq', value: 'scroll_percent' } },
  { key: 'popup_frequency', label: 'Frequenza visualizzazione', type: 'select', options: [
    { value: 'always', label: 'Sempre' },
    { value: 'once_session', label: 'Una volta per sessione' },
    { value: 'once_day', label: 'Una volta al giorno' },
    { value: 'once_week', label: 'Una volta a settimana' },
    { value: 'once_ever', label: 'Una sola volta' },
  ]},
  { key: 'popup_close_on_overlay', label: 'Chiudi al click su overlay', type: 'toggle' },
  { key: 'popup_animation', label: 'Animazione apertura', type: 'select', options: [
    { value: 'fade', label: 'Dissolvenza' },
    { value: 'slide-up', label: 'Scorrimento dal basso' },
    { value: 'slide-down', label: 'Scorrimento dall\'alto' },
    { value: 'slide-left', label: 'Scorrimento da sinistra' },
    { value: 'slide-right', label: 'Scorrimento da destra' },
    { value: 'zoom', label: 'Zoom' },
    { value: 'flip', label: 'Flip' },
  ]},
];

export const popupTriggerDefaults = {
  popup_trigger: 'click',
  popup_delay: '3',
  popup_scroll_percent: '50',
  popup_frequency: 'always',
  popup_close_on_overlay: true,
  popup_animation: 'fade',
};

// ─── 13. Multi-step form fields ───
export const formStepFields = [
  { type: 'separator', label: 'Form multi-step' },
  { key: 'form_multi_step', label: 'Form multi-step', type: 'toggle' },
  { key: 'form_step_style', label: 'Stile indicatore step', type: 'select', options: [
    { value: 'numbered', label: 'Numerato' },
    { value: 'progress_bar', label: 'Barra di progresso' },
    { value: 'dots', label: 'Pallini' },
    { value: 'none', label: 'Nessuno' },
  ], condition: { field: 'form_multi_step', op: 'eq', value: true } },
  { key: 'form_step_color', label: 'Colore step attivo', type: 'color',
    condition: { field: 'form_multi_step', op: 'eq', value: true } },
];

export const formStepDefaults = {
  form_multi_step: false,
  form_step_style: 'numbered',
  form_step_color: '',
};

// ─── 14. Flex container controls ───
export const flexContainerFields = [
  { type: 'separator', label: 'Layout Flex' },
  { key: 'flex_direction', label: 'Direzione', type: 'select', options: [
    { value: 'row', label: 'Riga' },
    { value: 'column', label: 'Colonna' },
    { value: 'row-reverse', label: 'Riga inversa' },
    { value: 'column-reverse', label: 'Colonna inversa' },
  ]},
  { key: 'flex_justify', label: 'Giustificazione', type: 'select', options: [
    { value: 'flex-start', label: 'Inizio' },
    { value: 'center', label: 'Centro' },
    { value: 'flex-end', label: 'Fine' },
    { value: 'space-between', label: 'Spazio tra' },
    { value: 'space-around', label: 'Spazio attorno' },
    { value: 'space-evenly', label: 'Spazio uniforme' },
  ]},
  { key: 'flex_align', label: 'Allineamento verticale', type: 'select', options: [
    { value: 'stretch', label: 'Estendi' },
    { value: 'flex-start', label: 'Inizio' },
    { value: 'center', label: 'Centro' },
    { value: 'flex-end', label: 'Fine' },
    { value: 'baseline', label: 'Baseline' },
  ]},
  { key: 'flex_wrap', label: 'A capo', type: 'select', options: [
    { value: 'nowrap', label: 'No' },
    { value: 'wrap', label: 'Sì' },
    { value: 'wrap-reverse', label: 'Sì (inverso)' },
  ]},
  { key: 'flex_gap', label: 'Spazio tra elementi (px)', type: 'range', min: 0, max: 100, step: 1 },
];

export const flexContainerDefaults = {
  flex_direction: 'row',
  flex_justify: 'flex-start',
  flex_align: 'stretch',
  flex_wrap: 'nowrap',
  flex_gap: '0',
};

// ─── 15. CSS Grid controls ───
export const cssGridFields = [
  { type: 'separator', label: 'Layout CSS Grid' },
  { key: 'layout_mode', label: 'Modalità layout', type: 'select', options: [
    { value: 'flex', label: 'Flex (predefinito)' },
    { value: 'grid', label: 'CSS Grid' },
  ]},
  { key: 'grid_columns', label: 'Colonne griglia', type: 'text', placeholder: 'es. repeat(3, 1fr)',
    condition: { field: 'layout_mode', value: 'grid' } },
  { key: 'grid_rows', label: 'Righe griglia', type: 'text', placeholder: 'es. auto',
    condition: { field: 'layout_mode', value: 'grid' } },
  { key: 'grid_gap', label: 'Gap (px)', type: 'range', min: 0, max: 80, step: 2,
    condition: { field: 'layout_mode', value: 'grid' } },
  { key: 'grid_column_gap', label: 'Gap colonne (px)', type: 'range', min: 0, max: 80, step: 2,
    condition: { field: 'layout_mode', value: 'grid' } },
  { key: 'grid_row_gap', label: 'Gap righe (px)', type: 'range', min: 0, max: 80, step: 2,
    condition: { field: 'layout_mode', value: 'grid' } },
  { key: 'grid_align_items', label: 'Allineamento elementi', type: 'select', options: [
    { value: 'stretch', label: 'Estendi' },
    { value: 'start', label: 'Inizio' },
    { value: 'center', label: 'Centro' },
    { value: 'end', label: 'Fine' },
  ], condition: { field: 'layout_mode', value: 'grid' } },
  { key: 'grid_justify_items', label: 'Giustificazione elementi', type: 'select', options: [
    { value: 'stretch', label: 'Estendi' },
    { value: 'start', label: 'Inizio' },
    { value: 'center', label: 'Centro' },
    { value: 'end', label: 'Fine' },
  ], condition: { field: 'layout_mode', value: 'grid' } },
];

export const cssGridDefaults = {
  layout_mode: 'flex',
  grid_columns: 'repeat(3, 1fr)',
  grid_rows: 'auto',
  grid_gap: '24',
  grid_column_gap: '0',
  grid_row_gap: '0',
  grid_align_items: 'stretch',
  grid_justify_items: 'stretch',
};
