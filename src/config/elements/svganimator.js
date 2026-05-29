import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile SVG Animator — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → source_type, svg_url, svg_code, anim_type, anim_sequence, trigger,
 *                   comportamento (reverse, erase_on_leave, loop, replay_button + label),
 *                   alignment (allineamento layout)
 *   styleFields[] → typography_preset, durata/ritardi animazione (ms: duration, delay,
 *                   stagger_delay, loop_pause, easing + easing_custom),
 *                   stile tracciato (stroke_width, stroke_color, linecap, linejoin),
 *                   riempimento (show_fill, fill_color, fill_delay/duration ms),
 *                   max_width, shadow, borderFields
 */
export default {
  type: 'svganimator',
  name: t('SVG Animator'),
  icon: 'dashicons-art',
  category: 'media',
  defaults: {
    typography_preset: '',
    source_type: 'upload',
    svg_url: '',
    svg_code: '',
    anim_type: 'draw',
    anim_sequence: 'delayed',
    trigger: 'viewport',
    duration: 1500,
    delay: 0,
    easing: 'ease',
    easing_custom: '0.42, 0, 0.58, 1',
    stagger_delay: 100,
    stroke_width: '',
    stroke_color: '',
    stroke_linecap: '',
    stroke_linejoin: '',
    show_fill: true,
    fill_color: '',
    fill_delay: 300,
    fill_duration: 500,
    reverse: false,
    erase_on_leave: false,
    loop: false,
    loop_pause: 500,
    replay_button: false,
    replay_button_label: 'Replay',
    max_width: '',
    alignment: 'center',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // ── SORGENTE ──
    { type: 'separator', label: t('Sorgente') },
    { key: 'source_type', label: t('Tipo sorgente'), type: 'select', options: [
      { value: 'upload', label: t('Carica file SVG') },
      { value: 'code', label: t('Codice SVG') },
    ]},
    { key: 'svg_url', label: t('File SVG'), type: 'image', condition: { field: 'source_type', value: 'upload' } },
    { key: 'svg_code', label: t('Codice SVG'), type: 'textarea', rows: 8, placeholder: '<svg viewBox="0 0 100 100">...</svg>', condition: { field: 'source_type', value: 'code' } },

    // ── ANIMAZIONE (behavior) ──
    { type: 'separator', label: t('Animazione') },
    { key: 'anim_type', label: t('Tipo animazione'), type: 'select', options: [
      { value: 'draw', label: t('Disegna (stroke draw)') },
      { value: 'fill', label: t('Riempi dopo disegno') },
      { value: 'fade', label: t('Comparsa graduale (fade)') },
      { value: 'loop-draw', label: t('Disegno continuo (loop)') },
    ]},
    { key: 'anim_sequence', label: t('Sequenza'), type: 'select', options: [
      { value: 'sync', label: t('Tutti insieme') },
      { value: 'delayed', label: t('Con ritardo (stagger)') },
      { value: 'one-by-one', label: t('Uno alla volta') },
      { value: 'random', label: t('Ordine casuale') },
    ]},
    { key: 'trigger', label: t('Trigger'), type: 'select', options: [
      { value: 'auto', label: t('Automatico') },
      { value: 'viewport', label: t('Quando visibile') },
      { value: 'hover', label: t('Al passaggio mouse') },
      { value: 'click', label: t('Al click') },
    ]},

    // ── COMPORTAMENTO ──
    { type: 'separator', label: t('Comportamento') },
    { key: 'reverse', label: t('Direzione inversa'), type: 'toggle' },
    { key: 'erase_on_leave', label: t('Cancella quando esce dal viewport'), type: 'toggle' },
    { key: 'loop', label: t('Loop continuo'), type: 'toggle' },
    { key: 'replay_button', label: t('Pulsante replay'), type: 'toggle' },
    { key: 'replay_button_label', label: t('Testo pulsante'), type: 'text',
      condition: { field: 'replay_button', value: true } },

    // ── LAYOUT (allineamento) ──
    { type: 'separator', label: t('Allineamento') },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    // ── Tempistiche animazione (ms) ──
    { type: 'separator', label: t('Tempistiche animazione') },
    { key: 'duration', label: t('Durata (ms)'), type: 'range', min: 200, max: 5000, step: 100 },
    { key: 'delay', label: t('Ritardo iniziale (ms)'), type: 'range', min: 0, max: 3000, step: 100 },
    { key: 'stagger_delay', label: t('Ritardo tra elementi (ms)'), type: 'range', min: 0, max: 500, step: 10,
      condition: { field: 'anim_sequence', value: ['delayed', 'one-by-one', 'random'] } },
    { key: 'easing', label: t('Easing'), type: 'select', options: [
      { value: 'linear', label: t('Linear') },
      { value: 'ease', label: t('Ease') },
      { value: 'ease-in', label: t('Ease In') },
      { value: 'ease-out', label: t('Ease Out') },
      { value: 'ease-in-out', label: t('Ease In Out') },
      { value: 'custom', label: t('Custom (cubic-bezier)') },
    ]},
    { key: 'easing_custom', label: t('Cubic Bezier'), type: 'text', placeholder: t('0.42, 0, 0.58, 1'),
      condition: { field: 'easing', value: 'custom' } },
    { key: 'loop_pause', label: t('Pausa tra cicli (ms)'), type: 'range', min: 0, max: 3000, step: 100,
      condition: { field: 'loop', value: true } },

    // ── STILE TRACCIATO ──
    { type: 'separator', label: t('Stile tracciato') },
    { key: 'stroke_width', label: t('Spessore linea (px)'), type: 'range', min: 0, max: 20, step: 0.5 },
    { key: 'stroke_color', label: t('Colore linea'), type: 'color' },
    { key: 'stroke_linecap', label: t('Terminazione linea'), type: 'select', options: [
      { value: '', label: t('Default SVG') },
      { value: 'butt', label: t('Butt (taglio netto)') },
      { value: 'round', label: t('Round (arrotondato)') },
      { value: 'square', label: t('Square (quadrato)') },
    ]},
    { key: 'stroke_linejoin', label: t('Giunzione linee'), type: 'select', options: [
      { value: '', label: t('Default SVG') },
      { value: 'miter', label: t('Miter (angolo)') },
      { value: 'round', label: t('Round (arrotondato)') },
      { value: 'bevel', label: t('Bevel (smussato)') },
    ]},

    // ── RIEMPIMENTO ──
    { type: 'separator', label: t('Riempimento') },
    { key: 'show_fill', label: t('Mostra riempimento'), type: 'toggle' },
    { key: 'fill_color', label: t('Colore riempimento'), type: 'color',
      condition: { field: 'show_fill', value: true } },
    { key: 'fill_delay', label: t('Ritardo fill dopo disegno (ms)'), type: 'range', min: 0, max: 2000, step: 50,
      condition: { field: 'show_fill', value: true } },
    { key: 'fill_duration', label: t('Durata transizione fill (ms)'), type: 'range', min: 100, max: 2000, step: 50,
      condition: { field: 'show_fill', value: true } },

    // ── LAYOUT (dimensione) ──
    { type: 'separator', label: t('Layout') },
    { key: 'max_width', label: t('Larghezza max (px, vuoto = 100%)'), type: 'text' },

    ...shadowField,
    ...borderFields(),
  ],
};
