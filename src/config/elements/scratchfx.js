import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile ScratchFX — "gratta e scopri" (famiglia C, bucket C).
 * Reference visivo: handoff-tile-speciali/temi/44-tema-tattoo.html + 45-tema-gelateria.html
 *
 *   fields[]      → contenuto SOTTO la copertura (immagine + testo premio + sottotitolo),
 *                   suggerimento (hint) e label del pulsante "Scopri" (alternativa tastiera)
 *   styleFields[] → aspetto della copertura (colore/gradiente/immagine/stampigliatura),
 *                   comportamento (brushSize, revealThreshold, resetOnLeave),
 *                   layout (altezza/aspect, raggio bordi), allineamento, shadow + bordi
 *
 * Contratto §2: ogni numero/colore/testo è un campo con default; nessun hardcode.
 * SSR: il contenuto sotto è già visibile (l'immagine/testo premio sono nel DOM).
 * Il canvas di copertura viene dipinto e gestito dal runtime inline (Pointer Events,
 * destination-out, campionamento alpha → auto-reveal oltre la soglia). Fallback no-JS /
 * reduced-motion → nessuna copertura, contenuto direttamente visibile + pulsante "Scopri".
 */
export default {
  type: 'scratchfx',
  name: t('Gratta e Scopri'),
  icon: 'dashicons-image-filter',
  category: 'interactive',
  defaults: {
    // ── Contenuto sotto la copertura ──
    image: '',
    object_position: 'center center',
    prize_eyebrow: t('Edizione limitata'),
    prize_title: t('Gusto a sorpresa'),
    prize_text: t('Gratta via la pellicola per scoprire la sorpresa.'),
    text_color: '',
    accent_color: '',
    under_bg: '',
    // Suggerimento + pulsante tastiera
    hint: t('Gratta con il dito o il mouse per scoprire'),
    show_button: true,
    reveal_label: t('Scopri'),

    // ── Aspetto copertura ──
    cover_type: 'gradient',
    cover_color: 'var(--olo-color-text-faint, #94a3b8)',
    cover_color2: 'var(--olo-color-text-faint, #94a3b8)',
    cover_angle: 135,
    cover_image: '',
    cover_text: '',
    cover_text_color: '',

    // ── Comportamento ──
    brush_size: 32,
    reveal_threshold: 60,
    reset_on_leave: false,

    // ── Layout ──
    aspect: '16/10',
    height_mode: 'aspect',
    height: 320,
    align: 'center',
    max_width: 520,
    border_radius: { tl: 24, tr: 24, br: 24, bl: 24 },

    // ── Stile ──
    shadow: 'lg',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Contenuto sotto la copertura') },
    { key: 'image', label: t('Immagine premio'), type: 'image',
      description: t('Mostrata sotto la pellicola da grattare. Opzionale: puoi usare solo testo.') },
    { key: 'object_position', label: t('Posizione contenuto'), type: 'object-position',
      contextKeys: { src: 'image', ratio: 'aspect' },
      condition: { field: 'image', op: 'neq', value: '' } },
    { key: 'prize_eyebrow', label: t('Sopra-titolo'), type: 'text' },
    { key: 'prize_title', label: t('Titolo premio'), type: 'text' },
    { key: 'prize_text', label: t('Descrizione'), type: 'textarea' },

    { type: 'separator', label: t('Suggerimento & accessibilità') },
    { key: 'hint', label: t('Testo suggerimento'), type: 'text',
      description: t('Scompare al primo tocco. Lascia vuoto per nasconderlo.') },
    { key: 'show_button', label: t('Pulsante "Scopri" (alternativa tastiera)'), type: 'toggle',
      description: t('Mostra un pulsante per rivelare tutto senza grattare. Sempre attivo come fallback no-JS.') },
    { key: 'reveal_label', label: t('Etichetta pulsante'), type: 'text',
      condition: { field: 'show_button', op: 'eq', value: true } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Aspetto copertura') },
    { key: 'cover_type', label: t('Tipo copertura'), type: 'select', options: [
      { value: 'solid',    label: t('Colore pieno') },
      { value: 'gradient', label: t('Gradiente') },
      { value: 'image',    label: t('Immagine') },
    ]},
    { key: 'cover_color', label: t('Colore copertura'), type: 'color',
      condition: { field: 'cover_type', op: 'in', value: ['solid', 'gradient'] } },
    { key: 'cover_color2', label: t('Colore copertura 2'), type: 'color',
      condition: { field: 'cover_type', op: 'eq', value: 'gradient' } },
    { key: 'cover_angle', label: t('Angolo gradiente (°)'), type: 'range', min: 0, max: 360, step: 5,
      condition: { field: 'cover_type', op: 'eq', value: 'gradient' } },
    { key: 'cover_image', label: t('Immagine copertura'), type: 'image',
      condition: { field: 'cover_type', op: 'eq', value: 'image' } },
    { key: 'cover_text', label: t('Testo stampigliato'), type: 'text',
      description: t('Ripetuto sulla copertura (es. "GRATTA"). Lascia vuoto per nessuno.') },
    { key: 'cover_text_color', label: t('Colore testo stampigliato'), type: 'color',
      condition: { field: 'cover_text', op: 'neq', value: '' } },

    { type: 'separator', label: t('Comportamento') },
    { key: 'brush_size', label: t('Dimensione pennello (px)'), type: 'range', min: 10, max: 80, step: 2 },
    { key: 'reveal_threshold', label: t('Soglia auto-reveal (%)'), type: 'range', min: 0, max: 100, step: 5,
      description: t('Quando la percentuale grattata supera questo valore, il resto si scopre da solo. 0 = disattiva auto-reveal.') },
    { key: 'reset_on_leave', label: t('Ricopri all\'uscita del mouse'), type: 'toggle',
      description: t('Ridipinge la copertura quando il puntatore lascia l\'area (se non già completamente rivelata).') },

    { type: 'separator', label: t('Colori contenuto') },
    { key: 'under_bg', label: t('Sfondo area premio'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },
    { key: 'accent_color', label: t('Colore accento (sopra-titolo)'), type: 'color' },

    { type: 'separator', label: t('Layout') },
    { key: 'height_mode', label: t('Modalità altezza'), type: 'select', options: [
      { value: 'aspect', label: t('Proporzione (aspect-ratio)') },
      { value: 'fixed',  label: t('Altezza fissa (px)') },
    ]},
    { key: 'aspect', label: t('Proporzione'), type: 'select', options: [
      { value: '16/10', label: '16:10' },
      { value: '16/9',  label: '16:9' },
      { value: '4/3',   label: '4:3' },
      { value: '3/2',   label: '3:2' },
      { value: '1/1',   label: '1:1' },
      { value: '2/1',   label: '2:1' },
    ], condition: { field: 'height_mode', op: 'eq', value: 'aspect' } },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 120, max: 700, step: 10,
      condition: { field: 'height_mode', op: 'eq', value: 'fixed' } },
    { key: 'max_width', label: t('Larghezza massima (px)'), type: 'range', min: 200, max: 1000, step: 10 },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right',  label: t('Destra') },
    ]},
    withHover({ key: 'border_radius', label: t('Raggio bordi (px)'), type: 'border-radius' }),

    ...shadowField,
    ...borderFields(),
  ],
};
