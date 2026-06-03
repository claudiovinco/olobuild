import { t } from '@/i18n';
/**
 * Shared field presets for element definitions.
 * Elements can spread these into their fields arrays.
 */

// ─── Link fields preset ───
export const linkFields = [
  { key: 'link_url', label: t('URL link'), type: 'link' },
  { key: 'link_target', label: t('Apri in'), type: 'select', options: [
    { value: '_self', label: t('Stessa finestra') },
    { value: '_blank', label: t('Nuova scheda') },
  ]},
];

export const targetField = {
  key: 'target', label: t('Apri in'), type: 'select', options: [
    { value: '_self', label: t('Stessa finestra') },
    { value: '_blank', label: t('Nuova scheda') },
  ],
};

export const alignmentField = {
  key: 'alignment', label: t('Allineamento'), type: 'select', options: [
    { value: 'left', label: t('Sinistra') },
    { value: 'center', label: t('Centro') },
    { value: 'right', label: t('Destra') },
  ],
};

export const columnWidthOptions = [
  { value: '', label: t('Auto') },
  { value: '1-1', label: t('1/1') },
  { value: '1-2', label: t('1/2') },
  { value: '1-3', label: t('1/3') },
  { value: '2-3', label: t('2/3') },
  { value: '1-4', label: t('1/4') },
  { value: '3-4', label: t('3/4') },
  { value: '1-5', label: t('1/5') },
  { value: '2-5', label: t('2/5') },
  { value: '3-5', label: t('3/5') },
  { value: '4-5', label: t('4/5') },
  { value: '1-6', label: t('1/6') },
  { value: '5-6', label: t('5/6') },
];

// ─── Shadow field — preset + custom ───
export const shadowField = [
  { key: 'shadow', label: t('Ombra'), type: 'select', options: [
    { value: 'none', label: t('Nessuna') },
    { value: 'sm', label: t('Leggera') },
    { value: 'md', label: t('Media') },
    { value: 'lg', label: t('Forte') },
    { value: 'xl', label: t('Molto forte') },
    { value: 'custom', label: t('Personalizzata') },
  ]},
  { key: 'shadow_h', label: t('Offset H (px)'), type: 'range', min: -50, max: 50, step: 1,
    condition: { field: 'shadow', op: 'eq', value: 'custom' } },
  { key: 'shadow_v', label: t('Offset V (px)'), type: 'range', min: -50, max: 50, step: 1,
    condition: { field: 'shadow', op: 'eq', value: 'custom' } },
  { key: 'shadow_blur', label: t('Sfocatura (px)'), type: 'range', min: 0, max: 100, step: 1,
    condition: { field: 'shadow', op: 'eq', value: 'custom' } },
  { key: 'shadow_spread', label: t('Espansione (px)'), type: 'range', min: -50, max: 50, step: 1,
    condition: { field: 'shadow', op: 'eq', value: 'custom' } },
  { key: 'shadow_color', label: t('Colore ombra'), type: 'color',
    condition: { field: 'shadow', op: 'eq', value: 'custom' } },
  { key: 'shadow_inset', label: t('Ombra interna'), type: 'toggle',
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
  { type: 'separator', label: t('Filtri CSS') },
  { key: 'filter_blur', label: t('Sfocatura (px)'), type: 'range', min: 0, max: 20, step: 1 },
  { key: 'filter_brightness', label: t('Luminosità (%)'), type: 'range', min: 0, max: 200, step: 5 },
  { key: 'filter_contrast', label: t('Contrasto (%)'), type: 'range', min: 0, max: 200, step: 5 },
  { key: 'filter_saturate', label: t('Saturazione (%)'), type: 'range', min: 0, max: 200, step: 5 },
  { key: 'filter_grayscale', label: t('Scala di grigi (%)'), type: 'range', min: 0, max: 100, step: 5 },
  { key: 'filter_sepia', label: t('Seppia (%)'), type: 'range', min: 0, max: 100, step: 5 },
];

export const filterDefaults = {
  filter_blur: '0',
  filter_brightness: '100',
  filter_contrast: '100',
  filter_saturate: '100',
  filter_grayscale: '0',
  filter_sepia: '0',
};

// ─── Wow Effects helper (v1.0.72+) ───
// Rende personalizzabili gli "effetti audaci" dei preset (liquid-glass, neon-cyber,
// brutalist, retro-terminal, 3d-tilt, sticker, magnetic-liquid) tramite controlli
// nell'inspector. Sostituisce il CSS hardcoded con !important che esisteva in
// get_preset_extra_css. Ogni tile che lo include espone gli stessi 8 controlli
// — backdrop-filter, border-style, font-family, rotation, perspective+tilt, glow
// pulse, title glow, scanlines. Più un toggle "Disattiva effetto wow" che spegne
// il blocco extra_css quando l'utente vuole solo i colori del preset.
//
// Uso: spread `...wowEffectsFields()` negli styleFields, spread `...wowEffectsDefaults`
// nei defaults. I valori di default sono neutri (zero/false/inherit/solid), così
// nei preset non-audaci il blocco è invisibile/no-op.

export const wowEffectsFields = () => [
  { type: 'separator', label: t('Effetti avanzati (preset wow)') },
  { key: 'wow_disable',         label: t('Disattiva effetti preset wow'), type: 'toggle' },
  { key: 'wow_backdrop_blur',   label: t('Sfocatura sfondo (blur)'),      type: 'range',  min: 0,    max: 40,   step: 1 },
  { key: 'wow_backdrop_saturate', label: t('Saturazione sfondo (%)'),     type: 'range',  min: 100,  max: 200,  step: 5 },
  { key: 'wow_border_style',    label: t('Stile bordo'),                  type: 'select', options: [
    { value: 'solid',  label: t('Continuo') },
    { value: 'dashed', label: t('Tratteggiato') },
    { value: 'dotted', label: t('Puntinato') },
    { value: 'double', label: t('Doppio') },
  ]},
  { key: 'wow_font_family',     label: t('Font speciale'),                type: 'select', options: [
    { value: 'inherit',   label: t('Default (eredita)') },
    { value: 'monospace', label: t('Monospaziato') },
    { value: 'serif',     label: t('Serif') },
    { value: 'sans',      label: t('Sans-serif') },
  ]},
  { key: 'wow_rotation',        label: t('Rotazione (gradi)'),            type: 'range',  min: -10,  max: 10,   step: 0.5 },
  { key: 'wow_perspective',     label: t('Prospettiva 3D (px)'),          type: 'range',  min: 0,    max: 2000, step: 100 },
  { key: 'wow_tilt_x',          label: t('Tilt asse X (gradi)'),          type: 'range',  min: -10,  max: 10,   step: 0.5 },
  { key: 'wow_glow_pulse',      label: t('Glow pulsante (bordo animato)'),type: 'toggle' },
  { key: 'wow_title_glow',      label: t('Glow su titolo/heading'),       type: 'toggle' },
  { key: 'wow_scanlines',       label: t('Effetto scanlines (CRT)'),      type: 'toggle' },
  { key: 'wow_terminal_prompt', label: t('Prompt terminale (> + cursore)'),type: 'toggle' },
];

export const wowEffectsDefaults = {
  wow_disable: false,
  wow_backdrop_blur: 0,
  wow_backdrop_saturate: 100,
  wow_border_style: 'solid',
  wow_font_family: 'inherit',
  wow_rotation: 0,
  wow_perspective: 0,
  wow_tilt_x: 0,
  wow_glow_pulse: false,
  wow_title_glow: false,
  wow_scanlines: false,
  wow_terminal_prompt: false,
};

// ─── Entrance animations shared fields ───
export const entranceAnimationField = {
  key: 'entrance_animation', label: t('Animazione ingresso'), type: 'select', options: [
    { value: 'none', label: t('Nessuna') },
    { value: 'fade', label: t('Dissolvenza') },
    { value: 'slide-up', label: t('Scorrimento dal basso') },
    { value: 'slide-left', label: t('Scorrimento da sinistra') },
    { value: 'slide-right', label: t('Scorrimento da destra') },
    { value: 'slide-down', label: t('Scorrimento dall\'alto') },
    { value: 'zoom-in', label: t('Zoom in') },
    { value: 'zoom-out', label: t('Zoom out') },
    { value: 'flip', label: t('Flip') },
    { value: 'rotate-in', label: t('Rotazione oraria') },
    { value: 'rotate-ccw', label: t('Rotazione antioraria') },
    { value: 'bounce', label: t('Rimbalzo') },
    { value: 'elastic', label: t('Elastico') },
    { value: 'blur-in', label: t('Sfocatura') },
    { value: 'swing', label: t('Oscillazione') },
    { value: 'rubber', label: t('Gomma') },
    { value: 'jello', label: t('Gelatina') },
    { value: 'back-in-left', label: t('Ritorno da sinistra') },
    { value: 'back-in-right', label: t('Ritorno da destra') },
    { value: 'typewriter', label: t('Macchina da scrivere') },
    { value: 'fade-up-big', label: t('Grande dissolvenza dal basso') },
    { value: 'fade-down-big', label: t('Grande dissolvenza dall\'alto') },
    { value: 'lightspeed-left', label: t('Velocità luce da sinistra') },
    { value: 'lightspeed-right', label: t('Velocità luce da destra') },
    { value: 'roll-in', label: t('Rotolamento in entrata') },
    { value: 'jack-in-box', label: t('Scatola sorpresa') },
    { value: 'hinge', label: t('Cardine che cade') },
    { value: 'flip-y', label: t('Capovolgimento asse Y') },
    { value: 'flip-x', label: t('Capovolgimento asse X') },
    { value: 'zoom-in-down', label: t('Zoom + discesa') },
    { value: 'zoom-in-up', label: t('Zoom + salita') },
    { value: 'bounce-left', label: t('Rimbalzo da sinistra') },
    { value: 'bounce-right', label: t('Rimbalzo da destra') },
    { value: 'skew-in', label: t('Distorsione in entrata') },
    { value: 'curtain-reveal', label: t('Effetto tendina') },
    { value: 'blur-zoom', label: t('Sfocatura + Zoom') },
  ],
};

export const entranceStaggerFields = [
  { key: 'entrance_stagger', label: t('Stagger figli'), type: 'toggle',
    description: t('Anima i figli uno dopo l\'altro con ritardo incrementale'),
    condition: { field: 'entrance_animation', operator: '!=', value: 'none' } },
  { key: 'entrance_stagger_delay', label: t('Ritardo stagger (ms)'), type: 'range', min: 50, max: 500, step: 25,
    condition: { field: 'entrance_stagger', op: 'eq', value: true } },
];

export const entranceAnimationDefault = {
  entrance_animation: 'none',
  entrance_stagger: false,
  entrance_stagger_delay: '100',
};

// ─── Sticky fields preset ───
export const stickyFields = [
  { type: 'separator', label: t('Sticky') },
  { key: 'sticky', label: t('Sticky'), type: 'toggle' },
  { key: 'sticky_position', label: t('Posizione'), type: 'select', options: [
    { value: 'top', label: t('In alto') },
    { value: 'bottom', label: t('In basso') },
  ], condition: { field: 'sticky', op: 'eq', value: true } },
  { key: 'sticky_offset', label: t('Offset (px)'), type: 'range', min: 0, max: 200, step: 5,
    condition: { field: 'sticky', op: 'eq', value: true } },
  { key: 'sticky_on_mobile', label: t('Sticky su mobile'), type: 'toggle',
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
  { type: 'separator', label: t('Visibilità condizionale') },
  { key: 'cond_type', label: t('Condizione'), type: 'select', options: [
    { value: '', label: t('Sempre visibile') },
    { value: 'logged_in', label: t('Solo utenti loggati') },
    { value: 'logged_out', label: t('Solo visitatori') },
    { value: 'role', label: t('Solo ruolo specifico') },
    { value: 'mobile', label: t('Solo mobile') },
    { value: 'desktop', label: t('Solo desktop') },
    { value: 'date_after', label: t('Dopo una data') },
    { value: 'date_before', label: t('Prima di una data') },
    { value: 'has_featured_image', label: t('Ha immagine in evidenza') },
    { value: 'is_front_page', label: t('Solo front page') },
    { value: 'is_single', label: t('Solo singolo post') },
    { value: 'is_page', label: t('Solo pagine') },
    { value: 'is_archive', label: t('Solo archivi') },
    { value: 'is_search', label: t('Solo pagina ricerca') },
    { value: 'is_404', label: t('Solo pagina 404') },
    { value: 'post_type', label: t('Solo post type specifico') },
    { value: 'has_children', label: t('Ha sotto-pagine') },
    { value: 'is_author', label: t('Solo pagina autore') },
    { value: 'url_contains', label: t('URL contiene...') },
  ] },
  { key: 'cond_role', label: t('Ruolo richiesto'), type: 'text', placeholder: t('administrator'),
    condition: { field: 'cond_type', op: 'eq', value: 'role' } },
  { key: 'cond_date', label: t('Data'), type: 'date',
    condition: { field: 'cond_type', op: 'in', value: ['date_after', 'date_before'] } },
  { key: 'cond_post_type', label: t('Post type'), type: 'text', placeholder: t('post'),
    condition: { field: 'cond_type', op: 'eq', value: 'post_type' } },
  { key: 'cond_url_contains', label: t('Stringa nell\'URL'), type: 'text', placeholder: t('/blog/'),
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
  { type: 'separator', label: t('Effetti mouse') },
  { key: 'mouse_tilt', label: t('Tilt 3D al hover'), type: 'toggle' },
  { key: 'mouse_tilt_intensity', label: t('Intensità tilt'), type: 'range', min: 5, max: 30, step: 1,
    condition: { field: 'mouse_tilt', op: 'eq', value: true } },
  { key: 'mouse_track', label: t('Segui cursore'), type: 'toggle' },
  { key: 'mouse_track_speed', label: t('Velocità tracking'), type: 'range', min: 1, max: 10,
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
  { type: 'separator', label: t('Effetti su scroll') },
  { key: 'scroll_effect_opacity', label: t('Opacità su scroll'), type: 'toggle' },
  { key: 'scroll_opacity_start', label: t('Opacità inizio'), type: 'range', min: 0, max: 100,
    condition: { scroll_effect_opacity: [true] } },
  { key: 'scroll_opacity_end', label: t('Opacità fine'), type: 'range', min: 0, max: 100,
    condition: { scroll_effect_opacity: [true] } },
  { key: 'scroll_effect_scale', label: t('Scala su scroll'), type: 'toggle' },
  { key: 'scroll_scale_start', label: t('Scala inizio (%)'), type: 'range', min: 50, max: 150,
    condition: { scroll_effect_scale: [true] } },
  { key: 'scroll_scale_end', label: t('Scala fine (%)'), type: 'range', min: 50, max: 150,
    condition: { scroll_effect_scale: [true] } },
  { key: 'scroll_effect_rotate', label: t('Rotazione su scroll'), type: 'toggle' },
  { key: 'scroll_rotate_start', label: t('Gradi inizio'), type: 'range', min: -180, max: 180,
    condition: { scroll_effect_rotate: [true] } },
  { key: 'scroll_rotate_end', label: t('Gradi fine'), type: 'range', min: -180, max: 180,
    condition: { scroll_effect_rotate: [true] } },
  { key: 'scroll_effect_translatex', label: t('Spostamento X su scroll'), type: 'toggle' },
  { key: 'scroll_translatex_start', label: t('X inizio (px)'), type: 'range', min: -200, max: 200,
    condition: { scroll_effect_translatex: [true] } },
  { key: 'scroll_translatex_end', label: t('X fine (px)'), type: 'range', min: -200, max: 200,
    condition: { scroll_effect_translatex: [true] } },
  { key: 'scroll_effect_fill', label: t('Riempimento su scroll (altezza %)'), type: 'toggle',
    description: t('Anima la height da inizio→fine col progresso di scroll (preset FillFX — barra/livello che si riempie).') },
  { key: 'scroll_fill_start', label: t('Riempimento inizio (%)'), type: 'range', min: 0, max: 100,
    condition: { scroll_effect_fill: [true] } },
  { key: 'scroll_fill_end', label: t('Riempimento fine (%)'), type: 'range', min: 0, max: 100,
    condition: { scroll_effect_fill: [true] } },
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
  scroll_effect_fill: false,
  scroll_fill_start: '0',
  scroll_fill_end: '100',
};

// ─── Enhanced scroll effects (with blur + translateY) ───
export const scrollEffectFieldsEnhanced = [
  ...scrollEffectFields,
  { key: 'scroll_effect_blur', label: t('Sfocatura su scroll'), type: 'toggle' },
  { key: 'scroll_blur_start', label: t('Sfocatura inizio (px)'), type: 'range', min: 0, max: 20,
    condition: { scroll_effect_blur: [true] } },
  { key: 'scroll_blur_end', label: t('Sfocatura fine (px)'), type: 'range', min: 0, max: 20,
    condition: { scroll_effect_blur: [true] } },
  { key: 'scroll_effect_translatey', label: t('Spostamento Y su scroll'), type: 'toggle' },
  { key: 'scroll_translatey_start', label: t('Y inizio (px)'), type: 'range', min: -300, max: 300,
    condition: { scroll_effect_translatey: [true] } },
  { key: 'scroll_translatey_end', label: t('Y fine (px)'), type: 'range', min: -300, max: 300,
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
  label: t('CSS Personalizzato'),
  type: 'code',
  language: 'css',
  placeholder: t('selector {\n  /* il tuo CSS */\n}'),
  description: 'Usa "selector" come riferimento a questo elemento',
  section: 'Avanzato',
};

export const customCssDefault = {
  custom_css: '',
};


// (rimosso: imageRadiusField/imageRadiusDefault — codice morto, mai importato.
//  Il pattern corretto per i radius hoverable è withHover() sul field 'border_radius'.)

// ─── Text effects shared fields ───
// Returns the canonical "Effetti testo" inspector group, parametrized by available targets.
// Each tile decides which semantic targets it has (heading/text/subtitle/…); pass them via
// `targetOptions` (e.g. [{value:'heading',label: t('Solo titolo')}, {value:'text',label: t('Solo testo')}, {value:'both',label: t('Titolo e testo')}]).
export const textEffectsFields = (targetOptions = [
  { value: 'heading', label: t('Solo titolo') },
  { value: 'text', label: t('Solo testo') },
  { value: 'both', label: t('Titolo e testo') },
]) => [
  { type: 'separator', label: t('Effetti testo') },
  { key: 'text_effect', label: t('Effetto'), type: 'select', options: [
    { value: 'none', label: t('Nessuno') },
    { value: 'typewriter', label: t('Macchina da scrivere') },
    { value: 'typewriter-loop', label: t('Macchina da scrivere — loop frasi') },
    { value: 'reveal-letter', label: t('Reveal lettera per lettera') },
    { value: 'reveal-word', label: t('Reveal parola per parola') },
    { value: 'gradient-anim', label: t('Gradient animato') },
    { value: 'glitch', label: t('Glitch RGB') },
    { value: 'wave', label: t('Wave (lettere ondulanti)') },
    { value: 'underline-grow', label: t('Underline grow') },
    { value: 'highlight-grow', label: t('Highlight grow') },
    { value: 'scramble', label: t('Split scramble') },
  ]},
  { key: 'text_effect_target', label: t('Applica a'), type: 'select', options: targetOptions,
    condition: { field: 'text_effect', op: 'neq', value: 'none' } },
  { key: 'text_effect_speed', label: t('Velocità (ms per char/parola)'), type: 'range', min: 10, max: 300, step: 5,
    condition: { field: 'text_effect', op: 'neq', value: 'none' } },
  { key: 'text_effect_delay', label: t('Ritardo iniziale (ms)'), type: 'range', min: 0, max: 3000, step: 100,
    condition: { field: 'text_effect', op: 'neq', value: 'none' } },
  { key: 'text_effect_cursor', label: t('Mostra cursore lampeggiante'), type: 'toggle',
    condition: { field: 'text_effect', op: 'eq', value: 'typewriter' } },
  { key: 'text_effect_cursor_char', label: t('Carattere cursore'), type: 'text',
    condition: { field: 'text_effect_cursor', op: 'eq', value: true } },
  { key: 'text_effect_phrases', label: t('Frasi (una per riga)'), type: 'textarea',
    description: t('Mostrate in loop. Se vuoto, vengono usate le righe del testo del tile.'),
    condition: { field: 'text_effect', op: 'eq', value: 'typewriter-loop' } },
  { key: 'text_effect_pause', label: t('Pausa tra frasi (ms)'), type: 'range', min: 500, max: 5000, step: 100,
    condition: { field: 'text_effect', op: 'eq', value: 'typewriter-loop' } },
  { key: 'text_effect_color', label: t('Colore primario'), type: 'color',
    description: t('Per gradient/highlight/underline'),
    condition: { field: 'text_effect', value: ['gradient-anim', 'highlight-grow', 'underline-grow'] } },
  { key: 'text_effect_color_to', label: t('Colore secondario (gradient)'), type: 'color',
    condition: { field: 'text_effect', op: 'eq', value: 'gradient-anim' } },
  { key: 'text_effect_loop', label: t('Riproduci in loop'), type: 'toggle',
    condition: { field: 'text_effect', value: ['typewriter', 'reveal-letter', 'reveal-word', 'wave', 'glitch', 'scramble'] } },
];

export const textEffectsDefaults = {
  text_effect: 'none',
  text_effect_target: 'heading',
  text_effect_speed: '50',
  text_effect_delay: '0',
  text_effect_loop: false,
  text_effect_cursor: true,
  text_effect_cursor_char: '|',
  text_effect_color: '',
  text_effect_color_to: '',
  text_effect_phrases: '',
  text_effect_pause: '1500',
};

// ═══════════════════════════════════════════════════════════════════
//  NEW SHARED PRESETS
// ═══════════════════════════════════════════════════════════════════

// ─── 1. CSS Transform controls ───
export const transformFields = [
  { type: 'separator', label: t('Trasformazioni CSS') },
  { key: 'transform_rotate', label: t('Rotazione (deg)'), type: 'range', min: -360, max: 360, step: 1 },
  { key: 'transform_rotateX', label: t('Rotazione X (deg)'), type: 'range', min: -180, max: 180, step: 1 },
  { key: 'transform_rotateY', label: t('Rotazione Y (deg)'), type: 'range', min: -180, max: 180, step: 1 },
  { key: 'transform_scale', label: t('Scala'), type: 'range', min: 0.1, max: 3, step: 0.05 },
  { key: 'transform_translateX', label: t('Traslazione X (px)'), type: 'range', min: -500, max: 500, step: 1 },
  { key: 'transform_translateY', label: t('Traslazione Y (px)'), type: 'range', min: -500, max: 500, step: 1 },
  { key: 'transform_skewX', label: t('Inclinazione X (deg)'), type: 'range', min: -45, max: 45, step: 1 },
  { key: 'transform_skewY', label: t('Inclinazione Y (deg)'), type: 'range', min: -45, max: 45, step: 1 },
  { key: 'transform_origin', label: t('Punto di origine'), type: 'select', options: [
    { value: 'center', label: t('Centro') },
    { value: 'top-left', label: t('Alto sinistra') },
    { value: 'top-center', label: t('Alto centro') },
    { value: 'top-right', label: t('Alto destra') },
    { value: 'center-left', label: t('Centro sinistra') },
    { value: 'center-right', label: t('Centro destra') },
    { value: 'bottom-left', label: t('Basso sinistra') },
    { value: 'bottom-center', label: t('Basso centro') },
    { value: 'bottom-right', label: t('Basso destra') },
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
  { type: 'separator', label: t('Ombra testo') },
  { key: 'text_shadow_enabled', label: t('Ombra testo'), type: 'toggle' },
  { key: 'text_shadow_h', label: t('Offset orizzontale (px)'), type: 'range', min: -20, max: 20, step: 1,
    condition: { field: 'text_shadow_enabled', op: 'eq', value: true } },
  { key: 'text_shadow_v', label: t('Offset verticale (px)'), type: 'range', min: -20, max: 20, step: 1,
    condition: { field: 'text_shadow_enabled', op: 'eq', value: true } },
  { key: 'text_shadow_blur', label: t('Sfocatura (px)'), type: 'range', min: 0, max: 40, step: 1,
    condition: { field: 'text_shadow_enabled', op: 'eq', value: true } },
  { key: 'text_shadow_color', label: t('Colore ombra'), type: 'color',
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
  { type: 'separator', label: t('Filtro sfondo (backdrop)') },
  { key: 'backdrop_blur', label: t('Sfocatura sfondo (px)'), type: 'range', min: 0, max: 30, step: 1 },
  { key: 'backdrop_brightness', label: t('Luminosità sfondo (%)'), type: 'range', min: 0, max: 200, step: 5 },
  { key: 'backdrop_contrast', label: t('Contrasto sfondo (%)'), type: 'range', min: 0, max: 200, step: 5 },
  { key: 'backdrop_saturate', label: t('Saturazione sfondo (%)'), type: 'range', min: 0, max: 200, step: 5 },
];

export const backdropFilterDefaults = {
  backdrop_blur: '0',
  backdrop_brightness: '100',
  backdrop_contrast: '100',
  backdrop_saturate: '100',
};

// ─── 6. Infinite (looping) animations ───
export const infiniteAnimationFields = [
  { type: 'separator', label: t('Animazione continua') },
  { key: 'infinite_animation', label: t('Animazione'), type: 'select', options: [
    { value: 'none', label: t('Nessuna') },
    { value: 'float', label: t('Galleggiamento') },
    { value: 'float-rot', label: t('Galleggiamento + rotazione') },
    { value: 'pulse', label: t('Pulsazione') },
    { value: 'spin', label: t('Rotazione') },
    { value: 'wiggle', label: t('Dondolio') },
    { value: 'bounce', label: t('Rimbalzo') },
    { value: 'swing', label: t('Oscillazione') },
    { value: 'breathe', label: t('Respiro') },
  ]},
  { key: 'infinite_speed', label: t('Velocità (s)'), type: 'range', min: 1, max: 10, step: 0.5,
    condition: { field: 'infinite_animation', operator: '!=', value: 'none' } },
  { key: 'infinite_amplitude', label: t('Ampiezza (px)'), type: 'range', min: 2, max: 60, step: 1,
    condition: { field: 'infinite_animation', op: 'in', value: ['float', 'float-rot', 'bounce'] } },
  { key: 'infinite_delay', label: t('Ritardo (ms)'), type: 'range', min: 0, max: 3000, step: 100,
    condition: { field: 'infinite_animation', operator: '!=', value: 'none' } },
  { key: 'infinite_direction', label: t('Direzione'), type: 'select', options: [
    { value: 'normal', label: t('Normale') },
    { value: 'alternate', label: t('Alternata') },
    { value: 'reverse', label: t('Inversa') },
  ], condition: { field: 'infinite_animation', operator: '!=', value: 'none' } },
];

export const infiniteAnimationDefaults = {
  infinite_animation: 'none',
  infinite_speed: '3',
  infinite_amplitude: '',
  infinite_delay: '0',
  infinite_direction: 'normal',
};

// ─── 7. CSS Mask / Clip-path ───
export const maskFields = [
  { type: 'separator', label: t('Maschera forma') },
  { key: 'mask_type', label: t('Tipo maschera'), type: 'select', options: [
    { value: 'none', label: t('Nessuna') },
    { value: 'circle', label: t('Cerchio') },
    { value: 'ellipse', label: t('Ellisse') },
    { value: 'triangle', label: t('Triangolo') },
    { value: 'hexagon', label: t('Esagono') },
    { value: 'star', label: t('Stella') },
    { value: 'diamond', label: t('Diamante') },
    { value: 'blob', label: t('Blob') },
    { value: 'custom', label: t('Personalizzata') },
  ]},
  { key: 'mask_size', label: t('Dimensione maschera'), type: 'select', options: [
    { value: 'contain', label: t('Contenuta') },
    { value: 'cover', label: t('Copertura') },
    { value: '50%', label: '50%' },
    { value: '75%', label: '75%' },
    { value: '100%', label: '100%' },
  ], condition: { field: 'mask_type', operator: '!=', value: 'none' } },
  { key: 'mask_position', label: t('Posizione maschera'), type: 'select', options: [
    { value: 'center', label: t('Centro') },
    { value: 'top', label: t('Alto') },
    { value: 'bottom', label: t('Basso') },
    { value: 'left', label: t('Sinistra') },
    { value: 'right', label: t('Destra') },
    { value: 'top-left', label: t('Alto sinistra') },
    { value: 'top-right', label: t('Alto destra') },
    { value: 'bottom-left', label: t('Basso sinistra') },
    { value: 'bottom-right', label: t('Basso destra') },
  ], condition: { field: 'mask_type', operator: '!=', value: 'none' } },
  { key: 'mask_repeat', label: t('Ripeti maschera'), type: 'select', options: [
    { value: 'no-repeat', label: t('Non ripetere') },
    { value: 'repeat', label: t('Ripeti') },
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
  key: 'overflow', label: t('Overflow contenuto'), type: 'select', options: [
    { value: 'visible', label: t('Visibile') },
    { value: 'hidden', label: t('Nascosto') },
    { value: 'auto', label: t('Automatico') },
    { value: 'scroll', label: t('Scroll') },
    { value: 'clip', label: t('Taglia') },
  ],
};

export const overflowDefault = {
  overflow: 'visible',
};

// ─── 10. Enhanced conditional visibility ───
const conditionTypeOptions = [
  { value: '', label: t('Sempre visibile') },
  { value: 'logged_in', label: t('Solo utenti loggati') },
  { value: 'logged_out', label: t('Solo visitatori') },
  { value: 'role', label: t('Solo ruolo specifico') },
  { value: 'mobile', label: t('Solo mobile') },
  { value: 'desktop', label: t('Solo desktop') },
  { value: 'date_after', label: t('Dopo una data') },
  { value: 'date_before', label: t('Prima di una data') },
  { value: 'has_featured_image', label: t('Ha immagine in evidenza') },
  { value: 'is_front_page', label: t('Solo front page') },
  { value: 'is_single', label: t('Solo singolo post') },
  { value: 'is_page', label: t('Solo pagine') },
  { value: 'is_archive', label: t('Solo archivi') },
  { value: 'is_search', label: t('Solo pagina ricerca') },
  { value: 'is_404', label: t('Solo pagina 404') },
  { value: 'post_type', label: t('Solo post type specifico') },
  { value: 'has_children', label: t('Ha sotto-pagine') },
  { value: 'is_author', label: t('Solo pagina autore') },
  { value: 'url_contains', label: t('URL contiene...') },
  { value: 'day_of_week', label: t('Giorno della settimana') },
  { value: 'time_range', label: t('Fascia oraria') },
  { value: 'referrer_url', label: t('Provenienza (referrer)') },
  { value: 'browser', label: t('Browser specifico') },
  { value: 'woo_cart_empty', label: t('Carrello WooCommerce vuoto') },
  { value: 'woo_cart_has_items', label: t('Carrello WooCommerce con prodotti') },
  { value: 'custom_field_equals', label: t('Campo personalizzato uguale a...') },
];

export const conditionFieldsEnhanced = [
  { type: 'separator', label: t('Visibilità condizionale') },
  { key: 'cond_logic', label: t('Logica condizioni'), type: 'select', options: [
    { value: 'and', label: t('Tutte le condizioni (AND)') },
    { value: 'or', label: t('Almeno una condizione (OR)') },
  ]},
  { key: 'cond_type', label: t('Condizione 1'), type: 'select', options: conditionTypeOptions },
  { key: 'cond_role', label: t('Ruolo richiesto'), type: 'text', placeholder: t('administrator'),
    condition: { field: 'cond_type', op: 'eq', value: 'role' } },
  { key: 'cond_date', label: t('Data'), type: 'date',
    condition: { field: 'cond_type', op: 'in', value: ['date_after', 'date_before'] } },
  { key: 'cond_post_type', label: t('Post type'), type: 'text', placeholder: t('post'),
    condition: { field: 'cond_type', op: 'eq', value: 'post_type' } },
  { key: 'cond_url_contains', label: t('Stringa nell\'URL'), type: 'text', placeholder: t('/blog/'),
    condition: { field: 'cond_type', op: 'eq', value: 'url_contains' } },
  { key: 'cond_day', label: t('Giorno'), type: 'select', options: [
    { value: 'monday', label: t('Lunedì') },
    { value: 'tuesday', label: t('Martedì') },
    { value: 'wednesday', label: t('Mercoledì') },
    { value: 'thursday', label: t('Giovedì') },
    { value: 'friday', label: t('Venerdì') },
    { value: 'saturday', label: t('Sabato') },
    { value: 'sunday', label: t('Domenica') },
  ], condition: { field: 'cond_type', op: 'eq', value: 'day_of_week' } },
  { key: 'cond_time_from', label: t('Ora inizio'), type: 'time',
    condition: { field: 'cond_type', op: 'eq', value: 'time_range' } },
  { key: 'cond_time_to', label: t('Ora fine'), type: 'time',
    condition: { field: 'cond_type', op: 'eq', value: 'time_range' } },
  { key: 'cond_referrer', label: t('URL provenienza'), type: 'text', placeholder: t('google.com'),
    condition: { field: 'cond_type', op: 'eq', value: 'referrer_url' } },
  { key: 'cond_browser', label: t('Browser'), type: 'select', options: [
    { value: 'chrome', label: t('Chrome') },
    { value: 'firefox', label: t('Firefox') },
    { value: 'safari', label: t('Safari') },
    { value: 'edge', label: t('Edge') },
    { value: 'opera', label: t('Opera') },
  ], condition: { field: 'cond_type', op: 'eq', value: 'browser' } },
  { key: 'cond_custom_field_key', label: t('Nome campo'), type: 'text', placeholder: t('meta_key'),
    condition: { field: 'cond_type', op: 'eq', value: 'custom_field_equals' } },
  { key: 'cond_custom_field_value', label: t('Valore campo'), type: 'text', placeholder: t('valore'),
    condition: { field: 'cond_type', op: 'eq', value: 'custom_field_equals' } },
  { key: 'cond_2_type', label: t('Condizione 2'), type: 'select', options: conditionTypeOptions },
  { key: 'cond_2_role', label: t('Ruolo richiesto (cond. 2)'), type: 'text', placeholder: t('administrator'),
    condition: { field: 'cond_2_type', op: 'eq', value: 'role' } },
  { key: 'cond_2_date', label: t('Data (cond. 2)'), type: 'date',
    condition: { field: 'cond_2_type', op: 'in', value: ['date_after', 'date_before'] } },
  { key: 'cond_2_post_type', label: t('Post type (cond. 2)'), type: 'text', placeholder: t('post'),
    condition: { field: 'cond_2_type', op: 'eq', value: 'post_type' } },
  { key: 'cond_2_url_contains', label: t('Stringa nell\'URL (cond. 2)'), type: 'text', placeholder: t('/blog/'),
    condition: { field: 'cond_2_type', op: 'eq', value: 'url_contains' } },
  { key: 'cond_2_day', label: t('Giorno (cond. 2)'), type: 'select', options: [
    { value: 'monday', label: t('Lunedì') },
    { value: 'tuesday', label: t('Martedì') },
    { value: 'wednesday', label: t('Mercoledì') },
    { value: 'thursday', label: t('Giovedì') },
    { value: 'friday', label: t('Venerdì') },
    { value: 'saturday', label: t('Sabato') },
    { value: 'sunday', label: t('Domenica') },
  ], condition: { field: 'cond_2_type', op: 'eq', value: 'day_of_week' } },
  { key: 'cond_2_time_from', label: t('Ora inizio (cond. 2)'), type: 'time',
    condition: { field: 'cond_2_type', op: 'eq', value: 'time_range' } },
  { key: 'cond_2_time_to', label: t('Ora fine (cond. 2)'), type: 'time',
    condition: { field: 'cond_2_type', op: 'eq', value: 'time_range' } },
  { key: 'cond_2_referrer', label: t('URL provenienza (cond. 2)'), type: 'text', placeholder: t('google.com'),
    condition: { field: 'cond_2_type', op: 'eq', value: 'referrer_url' } },
  { key: 'cond_2_browser', label: t('Browser (cond. 2)'), type: 'select', options: [
    { value: 'chrome', label: t('Chrome') },
    { value: 'firefox', label: t('Firefox') },
    { value: 'safari', label: t('Safari') },
    { value: 'edge', label: t('Edge') },
    { value: 'opera', label: t('Opera') },
  ], condition: { field: 'cond_2_type', op: 'eq', value: 'browser' } },
  { key: 'cond_2_custom_field_key', label: t('Nome campo (cond. 2)'), type: 'text', placeholder: t('meta_key'),
    condition: { field: 'cond_2_type', op: 'eq', value: 'custom_field_equals' } },
  { key: 'cond_2_custom_field_value', label: t('Valore campo (cond. 2)'), type: 'text', placeholder: t('valore'),
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
  { type: 'separator', label: t('Trigger popup') },
  { key: 'popup_trigger', label: t('Attivazione'), type: 'select', options: [
    { value: 'click', label: t('Click') },
    { value: 'page_load', label: t('Caricamento pagina') },
    { value: 'scroll_percent', label: t('Percentuale scroll') },
    { value: 'exit_intent', label: t('Intenzione di uscita') },
    { value: 'time_delay', label: t('Ritardo temporale') },
    { value: 'inactivity', label: t('Inattività utente') },
  ]},
  { key: 'popup_delay', label: t('Ritardo (secondi)'), type: 'range', min: 0, max: 30, step: 1,
    condition: { field: 'popup_trigger', op: 'in', value: ['page_load', 'time_delay'] } },
  { key: 'popup_scroll_percent', label: t('Percentuale scroll (%)'), type: 'range', min: 10, max: 100, step: 10,
    condition: { field: 'popup_trigger', op: 'eq', value: 'scroll_percent' } },
  { key: 'popup_frequency', label: t('Frequenza visualizzazione'), type: 'select', options: [
    { value: 'always', label: t('Sempre') },
    { value: 'once_session', label: t('Una volta per sessione') },
    { value: 'once_day', label: t('Una volta al giorno') },
    { value: 'once_week', label: t('Una volta a settimana') },
    { value: 'once_ever', label: t('Una sola volta') },
  ]},
  { key: 'popup_close_on_overlay', label: t('Chiudi al click su overlay'), type: 'toggle' },
  { key: 'popup_animation', label: t('Animazione apertura'), type: 'select', options: [
    { value: 'fade', label: t('Dissolvenza') },
    { value: 'slide-up', label: t('Scorrimento dal basso') },
    { value: 'slide-down', label: t('Scorrimento dall\'alto') },
    { value: 'slide-left', label: t('Scorrimento da sinistra') },
    { value: 'slide-right', label: t('Scorrimento da destra') },
    { value: 'zoom', label: t('Zoom') },
    { value: 'flip', label: t('Flip') },
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
  { type: 'separator', label: t('Form multi-step') },
  { key: 'form_multi_step', label: t('Form multi-step'), type: 'toggle' },
  { key: 'form_step_style', label: t('Stile indicatore step'), type: 'select', options: [
    { value: 'numbered', label: t('Numerato') },
    { value: 'progress_bar', label: t('Barra di progresso') },
    { value: 'dots', label: t('Pallini') },
    { value: 'none', label: t('Nessuno') },
  ], condition: { field: 'form_multi_step', op: 'eq', value: true } },
  { key: 'form_step_color', label: t('Colore step attivo'), type: 'color',
    condition: { field: 'form_multi_step', op: 'eq', value: true } },
];

export const formStepDefaults = {
  form_multi_step: false,
  form_step_style: 'numbered',
  form_step_color: '',
};

// ─── 14. Flex container controls ───
export const flexContainerFields = [
  { type: 'separator', label: t('Layout Flex') },
  { key: 'flex_direction', label: t('Direzione'), type: 'select', options: [
    { value: 'row', label: t('Riga') },
    { value: 'column', label: t('Colonna') },
    { value: 'row-reverse', label: t('Riga inversa') },
    { value: 'column-reverse', label: t('Colonna inversa') },
  ]},
  { key: 'flex_justify', label: t('Giustificazione'), type: 'select', options: [
    { value: 'flex-start', label: t('Inizio') },
    { value: 'center', label: t('Centro') },
    { value: 'flex-end', label: t('Fine') },
    { value: 'space-between', label: t('Spazio tra') },
    { value: 'space-around', label: t('Spazio attorno') },
    { value: 'space-evenly', label: t('Spazio uniforme') },
  ]},
  { key: 'flex_align', label: t('Allineamento verticale'), type: 'select', options: [
    { value: 'stretch', label: t('Estendi') },
    { value: 'flex-start', label: t('Inizio') },
    { value: 'center', label: t('Centro') },
    { value: 'flex-end', label: t('Fine') },
    { value: 'baseline', label: t('Baseline') },
  ]},
  { key: 'flex_wrap', label: t('A capo'), type: 'select', options: [
    { value: 'nowrap', label: t('No') },
    { value: 'wrap', label: t('Sì') },
    { value: 'wrap-reverse', label: t('Sì (inverso)') },
  ]},
  { key: 'flex_column_gap', label: t('Gap orizzontale (px)'), type: 'range', min: 0, max: 100, step: 1 },
  { key: 'flex_row_gap', label: t('Gap verticale (px)'), type: 'range', min: 0, max: 100, step: 1 },
];

export const flexContainerDefaults = {
  flex_direction: 'row',
  flex_justify: 'flex-start',
  flex_align: 'stretch',
  flex_wrap: 'nowrap',
  flex_column_gap: '0',
  flex_row_gap: '0',
};

// ─── 15. CSS Grid controls ───
export const cssGridFields = [
  { type: 'separator', label: t('Layout CSS Grid') },
  { key: 'layout_mode', label: t('Modalità layout'), type: 'select', options: [
    { value: 'flex', label: t('Flex (predefinito)') },
    { value: 'grid', label: t('CSS Grid') },
  ]},
  { key: 'grid_columns', label: t('Colonne griglia'), type: 'text', placeholder: t('es. repeat(3, 1fr)'),
    condition: { field: 'layout_mode', value: 'grid' } },
  { key: 'grid_rows', label: t('Righe griglia'), type: 'text', placeholder: t('es. auto'),
    condition: { field: 'layout_mode', value: 'grid' } },
  { key: 'grid_gap', label: t('Gap (px)'), type: 'range', min: 0, max: 80, step: 2,
    condition: { field: 'layout_mode', value: 'grid' } },
  { key: 'grid_column_gap', label: t('Gap colonne (px)'), type: 'range', min: 0, max: 80, step: 2,
    condition: { field: 'layout_mode', value: 'grid' } },
  { key: 'grid_row_gap', label: t('Gap righe (px)'), type: 'range', min: 0, max: 80, step: 2,
    condition: { field: 'layout_mode', value: 'grid' } },
  { key: 'grid_align_items', label: t('Allineamento elementi'), type: 'select', options: [
    { value: 'stretch', label: t('Estendi') },
    { value: 'start', label: t('Inizio') },
    { value: 'center', label: t('Centro') },
    { value: 'end', label: t('Fine') },
  ], condition: { field: 'layout_mode', value: 'grid' } },
  { key: 'grid_justify_items', label: t('Giustificazione elementi'), type: 'select', options: [
    { value: 'stretch', label: t('Estendi') },
    { value: 'start', label: t('Inizio') },
    { value: 'center', label: t('Centro') },
    { value: 'end', label: t('Fine') },
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

// ─── Hover wrapper ─────────────────────────────────────────────────────────

/**
 * Marca un field come "hoverable": l'inspector mostra un piccolo toggle "occhio"
 * accanto al label; click → espande inline un secondo controllo identico legato
 * alla chiave `${key}_hover` (override con opts.hoverKey) + un mini input
 * "Durata transizione (ms)" legato a `${key}_hover_duration`.
 *
 * Le chiavi salvate NON cambiano — è puro zucchero UI sul lato editor.
 *
 * @param {Object} field - Definizione field standard ({ key, label, type, ... })
 * @param {Object} [opts]
 * @param {string} [opts.hoverKey]         - Chiave hover custom (default: `${key}_hover`)
 * @param {string} [opts.hoverDurationKey] - Chiave durata custom (default: `${key}_hover_duration`)
 * @param {number} [opts.defaultDuration]  - Durata di fallback (default: 300)
 */
export function withHover(field, opts = {}) {
  return {
    ...field,
    hoverable: true,
    hoverKey: opts.hoverKey || `${field.key}_hover`,
    hoverDurationKey: opts.hoverDurationKey || `${field.key}_hover_duration`,
    hoverDefaultDuration: opts.defaultDuration ?? 300,
  };
}

// ─── Border system ─────────────────────────────────────────────────────────

export const borderDefault = {
  top: 0, right: 0, bottom: 0, left: 0,
  linked: true, style: 'solid', color: '',
};

export const borderHoverDefault = {
  top: 0, right: 0, bottom: 0, left: 0,
  linked: true, style: '', color: '',
};

export const borderEffectDefaults = {
  border_effect:           'none',
  border_effect_intensity: 'medium',
  border_effect_color2:    '',
  border_effect_angle:     135,
  border_effect_speed:     4,
};

/**
 * Restituisce l'array di fields per la sezione Bordo dell'inspector.
 * @param {Object} opts  { key, hoverKey, durationKey } — override chiavi default
 */
/**
 * Solo la sezione "Effetti bordo" (neon/gradiente…). Estratta da borderFields()
 * così il tab Stile del wrapper può montare il CONTROLLO bordo dentro il pannello
 * "Spazi & Bordi" (StyleBoxStack) e tenere gli effetti come sezione separata,
 * senza duplicare le definizioni. Opera sulle chiavi border_effect_*.
 */
export function borderEffectFields() {
  return [
    { type: 'separator', label: t('Effetti bordo') },
    { key: 'border_effect', label: t('Effetto'), type: 'select', options: [
      { value: 'none',          label: t('Nessuno') },
      { value: 'neon',          label: t('Neon glow') },
      { value: 'neon-pulse',    label: t('Neon pulsante') },
      { value: 'gradient',      label: t('Gradiente statico') },
      { value: 'gradient-spin', label: t('Gradiente rotante') },
    ]},
    { key: 'border_effect_intensity', label: t('Intensità glow'), type: 'select',
      options: [
        { value: 'subtle',  label: t('Sottile') },
        { value: 'medium',  label: t('Media') },
        { value: 'intense', label: t('Intensa') },
      ],
      condition: { field: 'border_effect', op: 'in', value: ['neon', 'neon-pulse'] } },
    { key: 'border_effect_color2', label: t('Colore 2'), type: 'color',
      condition: { field: 'border_effect', op: 'in', value: ['gradient', 'gradient-spin'] } },
    { key: 'border_effect_angle', label: t('Angolo gradiente (°)'), type: 'range',
      min: 0, max: 360, step: 5,
      condition: { field: 'border_effect', op: '=', value: 'gradient' } },
    { key: 'border_effect_speed', label: t('Velocità rotazione (s)'), type: 'range',
      min: 1, max: 20, step: 1,
      condition: { field: 'border_effect', op: '=', value: 'gradient-spin' } },
  ];
}

export function borderFields(opts = {}) {
  const key      = opts.key          ?? 'border';
  const hoverKey = opts.hoverKey     ?? 'border_hover';
  const durKey   = opts.durationKey  ?? 'border_hover_duration';

  return [
    { type: 'separator', label: t('Bordo') },
    withHover(
      { key, label: t('Bordo'), type: 'border' },
      { hoverKey, hoverDurationKey: durKey }
    ),
    ...borderEffectFields(),
  ];
}

// ─── Widget template field — embed a widget template inside a container item ───
// Riusabile come PRIMO sub-field nei tile container (accordion, tab, slider...)
// per consentire all'utente di inserire un template type='widget' come contenuto
// della scheda. Il widget viene renderizzato server-side PRIMA del content inline.
export const widgetTemplateField = {
  key: 'widget_template_id',
  label: t('Widget collegato (opzionale)'),
  type: 'select',
  optionsSource: 'widgetTemplates',
  description: t('Seleziona un template di tipo Widget per inserirlo prima del contenuto della scheda. Crea i widget in Gestione Template → tab Widget.'),
};
