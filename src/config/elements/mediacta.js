import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Media CTA — call-to-action full-bleed con media/video di sfondo (slot, strisce se vuoto)
 * + velo gradiente + contenuto centrato (eyebrow + titolo gigante con accento + sub + CTA).
 * Estratta dal blueprint OLOthemes (.vd-cta membership). Render Vue == PHP. Nessun JS.
 */
export default {
  type: 'mediacta',
  name: t('Media CTA (sfondo full-bleed)'),
  icon: 'dashicons-megaphone',
  category: 'marketing',

  defaults: {
    eyebrow: 'Membership',
    eyebrow_color: '',
    headline: 'Become a member of our',
    accent_text: 'club',
    uppercase: true,
    headline_color: '#ffffff',
    subhead: '',
    subhead_color: 'rgba(255,255,255,0.78)',
    cta1_text: 'Go to membership',
    cta1_url: '#',
    cta2_text: '',
    cta2_url: '',
    bg_image: '',
    media_label: 'membership — supporters in the stands · background video',
    overlay_color: '#0a2a1e',
    overlay_top: 0.78,
    overlay_bottom: 0.9,
    accent: '',
    accent_on: '#0a2a1e',
    text_color: '#ffffff',
    align: 'center',
    pad_y: 160,

    // SPAZIATURA (additivo, no-op coi default) —
    // .omc-in ha padding fisso '0 28px' → field diretto (default = invariato).
    content_padding: { top: 0, right: 28, bottom: 0, left: 28 },
    // Il padding verticale del root è responsive (clamp(64px,12vw,pad_y px) 0):
    // override GATED per non perdere la responsività (default pad_custom=false = invariato).
    pad_custom: false,
    root_padding: { top: 64, right: 0, bottom: 64, left: 0 },

    // FORMA — raggio pill dei pulsanti (default 999 = invariato).
    btn_radius: { tl: 999, tr: 999, br: 999, bl: 999 },

    // KIT standard OLObuild (additivo, no-op coi default)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'headline', label: t('Titolo'), type: 'text' },
    { key: 'accent_text', label: t('Parola accento'), type: 'text' },
    { key: 'uppercase', label: t('Maiuscolo'), type: 'toggle' },
    { key: 'subhead', label: t('Sottotitolo (opzionale)'), type: 'textarea' },

    { type: 'separator', label: t('CTA') },
    { key: 'cta1_text', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'cta1_url', label: t('CTA 1 — link'), type: 'link' },
    { key: 'cta2_text', label: t('CTA 2 — testo (opzionale)'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — link'), type: 'link' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg_image', label: t('Immagine/poster (vuoto = placeholder)'), type: 'image' },
    { key: 'media_label', label: t('Etichetta placeholder'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Accento (parola + CTA + occhiello)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'accent_on', label: t('Testo su accento'), type: 'color' },
    { key: 'text_color', label: t('Colore titolo'), type: 'color' },
    { key: 'eyebrow_color', label: t('Occhiello (vuoto = accento)'), type: 'color' },
    { key: 'subhead_color', label: t('Sottotitolo'), type: 'color' },
    { key: 'overlay_color', label: t('Colore velo'), type: 'color' },
    { key: 'overlay_top', label: t('Velo alto (opacità)'), type: 'range', min: 0, max: 1, step: 0.02 },
    { key: 'overlay_bottom', label: t('Velo basso (opacità)'), type: 'range', min: 0, max: 1, step: 0.02 },

    { type: 'separator', label: t('Layout') },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'center', label: t('Centro') },
      { value: 'left', label: t('Sinistra') },
    ]},
    { key: 'pad_y', label: t('Padding verticale (px)'), type: 'range', min: 60, max: 220, step: 4 },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'content_padding', label: t('Padding contenuto'), type: 'spacing',
      description: t('Spazio interno del blocco testo/CTA.') },
    { key: 'pad_custom', label: t('Padding verticale custom'), type: 'toggle',
      description: t('Attiva per sostituire il padding verticale responsive con valori fissi.') },
    { key: 'root_padding', label: t('Padding sezione'), type: 'spacing',
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Forma') },
    { key: 'btn_radius', label: t('Raggio pulsanti'), type: 'border-radius' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },
    { type: 'separator', label: t('Ombra') },
    ...shadowField,
    ...borderFields(),
  ],
};
