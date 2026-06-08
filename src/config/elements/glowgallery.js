import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

/**
 * Hero — Glow Gallery : hero EVENTI centrato su fondo scuro, con GLOW
 * radiale rosa sfocato dietro lo stack di testo (eyebrow, H1 serif con parola corsiva
 * accento, sub, fino a 2 CTA) e, SOTTO, una STRISCIA di tessere media verticali (3 di
 * default, ratio 3/4) con angoli superiori molto arrotondati e offset sfalsato (la 2a
 * traslata in su). Striscia = array content-items ripetibile (immagine + didascalia).
 * Render Vue == PHP (GlowGalleryTile.vue). Runtime: nessuno (pure CSS).
 * Estratta dal blueprint OLOthemes "Aurora" (Events).
 */
export default {
  type: 'glowgallery',
  name: t('Hero — Glow Gallery'),
  icon: 'dashicons-buddicons-activity',
  category: 'marketing',

  defaults: {
    eyebrow: 'Events studio · est. 2011',
    headline_text: 'Celebrations worth',
    accent_text: 'remembering.',
    subhead: 'We design and produce weddings, galas and private events — the kind your guests talk about for years. From the first spark of an idea to the last dance.',
    cta1_text: 'Start planning', cta1_url: '#', cta2_text: 'See our work', cta2_url: '#',
    items: [
      { image: '', caption: 'tablescape, candlelight' },
      { image: '', caption: 'ballroom, florals' },
      { image: '', caption: 'couple, golden hour' },
    ],
    strip_offset: 28, strip_radius: 200,
    bg_color: '#241430', accent: '', accent_on: '#170c1f',
    text_color: '#f3e9ef', sub_color: '#c8b3c6', eyebrow_color: '#e0afca', media_bg: '#33203f',
    glow_color: 'rgba(224,175,202,0.22)', glow_w: 760, glow_h: 520, glow_blur: 120, glow_y: -160,
    h_size_min: 48, h_size_vw: 8, h_size_max: 108, max_width: 880,

    // Spaziatura & Raggio (additivi, no-op coi default)
    content_padding: { top: 0, right: 30, bottom: 0, left: 30 },
    btn_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
    media_radius_custom: false,
    media_radius: { tl: 200, tr: 200, br: 8, bl: 8 },

    // KIT standard OLObuild — sfondo completo + ombra + bordo (no-op coi default)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'eyebrow', label: t('Eyebrow'), type: 'text' },
    { key: 'headline_text', label: t('Titolo'), type: 'text' },
    { key: 'accent_text', label: t('Parola accento (corsivo)'), type: 'text',
      description: t('Resa in corsivo con il colore accento.') },
    { key: 'subhead', label: t('Sottotitolo'), type: 'textarea' },

    { type: 'separator', label: t('CTA') },
    { key: 'cta1_text', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'cta1_url', label: t('CTA 1 — link'), type: 'link' },
    { key: 'cta2_text', label: t('CTA 2 — testo'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — link'), type: 'link' },

    { type: 'separator', label: t('Striscia gallery') },
    { key: 'items', label: t('Tessere media'), type: 'content-items',
      itemLabel: t('Tessera'),
      defaults: { image: '', caption: '' },
      itemFields: [
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'caption', label: t('Didascalia (opzionale)'), type: 'text' },
      ],
    },
    { key: 'strip_offset', label: t('Offset verticale 2a tessera (px)'), type: 'range', min: 0, max: 80, step: 2 },
    { key: 'strip_radius', label: t('Raggio angoli superiori (px)'), type: 'range', min: 0, max: 300, step: 10 },
  ],

  styleFields: [
    { type: 'separator', label: t('Layout') },
    { key: 'max_width', label: t('Larghezza max testo (px)'), type: 'range', min: 480, max: 1200, step: 20 },

    { type: 'separator', label: t('Titolo') },
    { key: 'h_size_min', label: t('Dimensione min (px)'), type: 'range', min: 20, max: 80, step: 1 },
    { key: 'h_size_vw', label: t('Dimensione fluida (vw)'), type: 'range', min: 4, max: 14, step: 0.5 },
    { key: 'h_size_max', label: t('Dimensione max (px)'), type: 'range', min: 60, max: 160, step: 2 },

    { type: 'separator', label: t('Glow radiale') },
    { key: 'glow_color', label: t('Colore glow'), type: 'color' },
    { key: 'glow_w', label: t('Larghezza glow (px)'), type: 'range', min: 200, max: 1400, step: 20 },
    { key: 'glow_h', label: t('Altezza glow (px)'), type: 'range', min: 200, max: 1000, step: 20 },
    { key: 'glow_blur', label: t('Sfocatura glow (px)'), type: 'range', min: 0, max: 200, step: 5 },
    { key: 'glow_y', label: t('Posizione Y glow (px)'), type: 'range', min: -300, max: 200, step: 10 },

    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Sfondo'), type: 'color' },
    { key: 'accent', label: t('Accento (titolo corsivo + CTA)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'accent_on', label: t('Testo su accento (CTA)'), type: 'color' },
    { key: 'text_color', label: t('Colore titolo'), type: 'color' },
    { key: 'sub_color', label: t('Colore sottotitolo'), type: 'color' },
    { key: 'eyebrow_color', label: t('Colore eyebrow'), type: 'color' },
    { key: 'media_bg', label: t('Sfondo tessere media'), type: 'color' },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'content_padding', label: t('Padding contenuto (px)'), type: 'spacing',
      description: t('Spazio interno dello stack di testo (default 0 / 30).') },

    { type: 'separator', label: t('Raggio') },
    { key: 'btn_radius', label: t('Raggio CTA (px)'), type: 'border-radius' },
    { key: 'media_radius_custom', label: t('Raggio tessere personalizzato'), type: 'toggle',
      description: t('Se attivo, sostituisce il raggio della striscia con i 4 angoli sotto.') },
    { key: 'media_radius', label: t('Raggio tessere media (px)'), type: 'border-radius',
      condition: { field: 'media_radius_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,
    ...borderFields(),
  ],
};
