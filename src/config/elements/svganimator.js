import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

// Il disegno della partenza: un paesaggio a linea (cime con neve, sole, sentiero con la bandierina
// d'arrivo e due uccelli), tutto a tratto senza riempimento, così l'animazione lo traccia pezzo per pezzo.
const SVG_PARTENZA = `<svg viewBox="0 0 640 260" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
<circle cx="512" cy="62" r="28"/>
<polyline points="0,214 86,130 132,170 226,66 296,150 346,108 432,196 512,138 640,206"/>
<polyline points="204,92 226,66 250,96 238,90 226,102 214,90"/>
<polyline points="330,124 346,108 362,126"/>
<path d="M36 248 C116 228 168 244 228 220 S338 194 380 208 S470 198 548 150"/>
<line x1="548" y1="150" x2="548" y2="104"/><polygon points="548,104 576,115 548,126"/>
<circle cx="36" cy="248" r="5"/>
<path d="M380 66 q9 -9 18 0 q9 -9 18 0"/><path d="M424 46 q7 -7 14 0 q7 -7 14 0"/>
</svg>`;

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

  // Appena trascinata: un paesaggio a linea scritto nel campo «Codice SVG», che il colore primario
  // traccia un pezzo dopo l'altro quando entra in vista (tratto spesso, terminazioni tonde).
  partenza: {
    source_type: 'code',
    svg_code: SVG_PARTENZA,
    duration: 1800,
    stagger_delay: 180,
    easing: 'ease-in-out',
    stroke_color: 'var(--olo-color-primary)',
    stroke_width: '3',
    stroke_linecap: 'round',
    stroke_linejoin: 'round',
    show_fill: false,
    max_width: '640px',
    replay_button_label: t('Rivedi'),
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

  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    // ── Tempistiche animazione (ms) ──
    { type: 'separator', label: t('Tempistiche animazione') },
    { key: 'duration', label: t('Durata'), type: 'range', min: 200, max: 5000, step: 100 },
    { key: 'delay', label: t('Ritardo iniziale'), type: 'range', min: 0, max: 3000, step: 100 },
    { key: 'stagger_delay', label: t('Ritardo tra elementi'), type: 'range', min: 0, max: 500, step: 10,
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
    { key: 'loop_pause', label: t('Pausa tra cicli'), type: 'range', min: 0, max: 3000, step: 100,
      condition: { field: 'loop', value: true } },

    // ── STILE TRACCIATO ──
    { type: 'separator', label: t('Stile tracciato') },
    { key: 'stroke_width', label: t('Spessore linea'), type: 'range', min: 0, max: 20, step: 0.5 },
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
    { key: 'fill_delay', label: t('Ritardo fill dopo disegno'), type: 'range', min: 0, max: 2000, step: 50,
      condition: { field: 'show_fill', value: true } },
    { key: 'fill_duration', label: t('Durata transizione fill'), type: 'range', min: 100, max: 2000, step: 50,
      condition: { field: 'show_fill', value: true } },

    // ── LAYOUT (dimensione) ──
    { type: 'separator', label: t('Layout') },
    { key: 'max_width', label: t('Larghezza max (vuoto = 100%)'), type: 'unit', units: ['px', '%'], min: 0 },

    ...shadowField,
    ...borderFields(),
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
  ],
};
