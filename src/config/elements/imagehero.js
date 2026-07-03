import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, focalField, focalDefault } from './_shared.js';
import { t } from '@/i18n';

/**
 * Hero — Image Overlay : hero full-bleed con immagine di sfondo, velo a gradiente scuro
 * (verticale + opzionale laterale) e blocco testo sovrapposto (eyebrow+dot, titolo con
 * parola-accento, sottotitolo, fino a 2 CTA, riga meta/data opzionale, hint di scroll).
 * Una sola proprietà `text_position` riproduce i diversi blueprint OLOthemes:
 *   center (fiori) · bottom-left (loft) · left (atelier/saffron/linea) · center-right.
 * Default fedeli al blueprint Atelier (an-hero). Render Vue == PHP (ImageHeroTile.vue).
 * Cablata in: atelier, fieldco, fiori, linea, loft, saffron, vinea, vows, fjordline,
 * pasaje, wander. Nessun runtime JS.
 */
export default {
  type: 'imagehero',
  name: t('Hero — Image Overlay'),
  icon: 'dashicons-format-image',
  category: 'marketing',

  defaults: {
    eyebrow_text: "Autumn / Winter '26",
    eyebrow_dot: false,
    headline_text: 'The',
    accent_text: 'Nocturne',
    headline_tail: 'Collection',
    accent_italic: true,
    stack_lines: false,
    subhead: 'Tailoring for the hours after dark. Cut in wool crêpe and silk, finished by hand in our Paris atelier.',
    cta1_text: 'Shop the collection',
    cta1_url: '#',
    cta2_text: 'View lookbook',
    cta2_url: '#',
    meta_text: '',
    scroll_hint: '',
    bg_image: '',
    ...focalDefault('bg_image'),
    media_bg: { type: 'none' },
    bg_color: 'var(--olo-color-dark, #16263d)',
    media_label: 'campaign — figure in black tailoring, gold light, full bleed',
    text_position: 'left',
    text_align: 'left',
    content_width: 600,
    aspect_ratio: '21/10',
    min_height: 520,
    heading_font: 'serif',
    overlay_color: 'var(--olo-color-dark, #16263d)',
    overlay_top: 0.2,
    overlay_bottom: 0.75,
    overlay_sides: true,
    accent: '',
    accent_on: 'var(--olo-color-dark, #16263d)',
    text_color: 'var(--olo-color-light, #f8f9fa)',
    sub_color: 'var(--olo-color-light, #f8f9fa)',
    eyebrow_color: '',

    // Spaziatura / Forma (additivi, default no-op) — padding contenuto è responsivo
    // (clamp/vh) quindi override GATED; raggi = valori hardcoded attuali.
    pad_custom: false,                                   // false → mantiene clamp(40px,7vh,80px) 32px
    content_padding: { top: 60, right: 32, bottom: 60, left: 32 },
    cta_radius: { tl: 2, tr: 2, br: 2, bl: 2 },          // = border-radius:2px attuale dei bottoni
    wrap_radius: { tl: 0, tr: 0, br: 0, bl: 0 },         // hero full-bleed: 0 = nessun angolo (no-op)

    // Kit standard OLObuild — sfondo completo + ombra + bordo (default no-op)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'eyebrow_text', label: t('Eyebrow (sopratitolo)'), type: 'text' },
    { key: 'eyebrow_dot', label: t('Dot prima dell\'eyebrow'), type: 'toggle' },
    { key: 'headline_text', label: t('Titolo — inizio'), type: 'text' },
    { key: 'accent_text', label: t('Parola accento (colore)'), type: 'text' },
    { key: 'headline_tail', label: t('Titolo — coda (dopo accento)'), type: 'text' },
    { key: 'subhead', label: t('Sottotitolo'), type: 'textarea' },
    { key: 'meta_text', label: t('Riga meta / data (opzionale)'), type: 'text',
      description: t('Es. "12 September 2026 · Lago di Como". Vuoto = nascosta.') },
    { key: 'scroll_hint', label: t('Hint di scroll (opzionale)'), type: 'text',
      description: t('Es. "Scroll". Vuoto = nascosto.') },

    { type: 'separator', label: t('CTA') },
    { key: 'cta1_text', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'cta1_url', label: t('CTA 1 — link'), type: 'link' },
    { key: 'cta2_text', label: t('CTA 2 — testo'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — link'), type: 'link' },

    { type: 'separator', label: t('Sfondo / media') },
    { key: 'bg_image', label: t('Immagine di sfondo (vuoto = placeholder)'), type: 'image' },
    focalField('bg_image'),
    { key: 'media_bg', label: t('Sfondo / media (ogni tipo)'), type: 'background', showParallax: false },
    { key: 'media_label', label: t('Etichetta placeholder'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Layout') },
    { key: 'text_position', label: t('Posizione del testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra (centrato in verticale) — Atelier/Linea') },
      { value: 'center', label: t('Centro (stack pieno) — Fiori') },
      { value: 'bottom-left', label: t('In basso a sinistra — Loft/Saffron') },
      { value: 'center-right', label: t('Destra (centrato in verticale)') },
    ]},
    { key: 'text_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'content_width', label: t('Larghezza max contenuto (px)'), type: 'range', min: 280, max: 1200, step: 10 },
    { key: 'aspect_ratio', label: t('Aspect ratio immagine (es. 21/10)'), type: 'text' },
    { key: 'min_height', label: t('Altezza minima (≤100 = vh, altrimenti px)'), type: 'range', min: 50, max: 900, step: 1 },
    { key: 'heading_font', label: t('Font titolo'), type: 'font-family' },
    { key: 'accent_italic', label: t('Parola accento in corsivo'), type: 'toggle' },
    { key: 'stack_lines', label: t('Titolo su righe separate (stack)'), type: 'toggle',
      description: t('Manda inizio / accento / coda su tre righe (stile Atelier/Saffron).') },

    { type: 'separator', label: t('Velo (overlay)') },
    { key: 'overlay_color', label: t('Colore velo'), type: 'color' },
    { key: 'overlay_top', label: t('Intensità alto'), type: 'range', min: 0, max: 1, step: 0.05 },
    { key: 'overlay_bottom', label: t('Intensità basso'), type: 'range', min: 0, max: 1, step: 0.05 },
    { key: 'overlay_sides', label: t('Velo laterale (stile Atelier)'), type: 'toggle' },

    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Accento (parola + CTA + dot)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'accent_on', label: t('Testo su accento (CTA solid)'), type: 'color' },
    { key: 'text_color', label: t('Colore titolo'), type: 'color' },
    { key: 'sub_color', label: t('Colore sottotitolo / meta'), type: 'color' },
    { key: 'eyebrow_color', label: t('Colore eyebrow (vuoto = accento)'), type: 'color' },
    { key: 'bg_color', label: t('Colore di fondo (dietro immagine)'), type: 'color' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'pad_custom', label: t('Padding contenuto personalizzato'), type: 'toggle',
      description: t('Default: padding responsivo clamp(40px,7vh,80px) 32px. Attiva per impostare valori fissi.') },
    { key: 'content_padding', label: t('Padding contenuto'), type: 'spacing',
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Forma / Raggio') },
    { key: 'cta_radius', label: t('Raggio CTA (bottoni)'), type: 'border-radius' },
    { key: 'wrap_radius', label: t('Raggio contenitore'), type: 'border-radius',
      description: t('Hero full-bleed: lascia 0. Utile se la tile non è a tutto schermo.') },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
