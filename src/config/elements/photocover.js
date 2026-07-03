import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, focalField, focalDefault } from './_shared.js';

/**
 * Hero — Photo Cover (Frame) : hero editoriale full-bleed con grande foto di copertina
 * incorniciata e overlay tipografico (kicker mono + titolo gigante uppercase + riga meta/
 * byline mono) ancorato in basso a sinistra. Signature: la cornice fotografica precisa +
 * gradiente di scurimento alto→basso che lascia parlare l'immagine mantenendo leggibile
 * la didascalia. Estratta pixel-perfect dal blueprint OLOthemes "Frame" (.fr-cover).
 * Render Vue == PHP (PhotoCoverTile.vue). Runtime: nessuno.
 */
export default {
  type: 'photocover',
  name: t('Hero — Photo Cover'),
  icon: 'dashicons-format-image',
  category: 'marketing',

  defaults: {
    kicker_text: 'Photo Essay · Issue 41',
    headline_text: 'The City After Rain',
    uppercase: true,
    meta_items: [
      { text: 'Photographs · Yuki Mori' },
      { text: '28 frames' },
      { text: '12 min' },
    ],
    bg_image: '',
    ...focalDefault('bg_image'),
    media_label: 'cover photograph — rain-soaked city street, single figure',
    aspect_ratio: '16/9',
    min_height: 560,
    overlay_top: 0.3,
    overlay_bottom: 0.85,
    frame_padding: 28,
    media_bg: 'var(--olo-color-dark, #16263d)',
    kicker_color: '',
    headline_color: 'var(--olo-color-light, #f8f9fa)',
    meta_color: 'var(--olo-color-light, #f8f9fa)',

    // SPAZIATURA — override gated del padding del contenuto (.pc-in).
    // Il padding di default è responsivo (clamp(...,5vw,...)) e NON va sostituito:
    // resta attivo finché pad_custom = false (default → render invariato).
    pad_custom: false,
    content_padding: { top: 28, right: 28, bottom: 28, left: 28 },

    // FORMA — raggio della foto di copertina (.pc-media). Default 0 = no-op.
    media_radius: { tl: 0, tr: 0, br: 0, bl: 0 },

    // KIT standard OLObuild (sfondo + ombra + bordo) sul contenitore principale.
    // Default no-op: con questi valori il render resta identico a prima.
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'kicker_text', label: t('Kicker (occhiello)'), type: 'text' },
    { key: 'headline_text', label: t('Titolo'), type: 'text' },
    { key: 'uppercase', label: t('Maiuscolo'), type: 'toggle' },

    { type: 'separator', label: t('Meta / byline') },
    { key: 'meta_items', label: t('Voci meta'), type: 'content-items',
      itemLabel: t('Voce'),
      newItemDefaults: { text: 'Nuova voce' },
      itemFields: [
        { key: 'text', label: t('Testo'), type: 'text' },
      ],
    },

    { type: 'separator', label: t('Foto di copertina') },
    { key: 'bg_image', label: t('Immagine di copertina (vuoto = placeholder)'), type: 'image' },
    focalField('bg_image', { ratio: 'aspect_ratio' }),
    { key: 'media_label', label: t('Etichetta placeholder'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Cornice / media') },
    { key: 'aspect_ratio', label: t('Proporzioni'), type: 'select', options: [
      { value: '16/9', label: '16:9' },
      { value: '21/9', label: '21:9' },
      { value: '3/2', label: '3:2' },
      { value: '4/3', label: '4:3' },
      { value: '1/1', label: '1:1' },
    ] },
    { key: 'min_height', label: t('Altezza minima (px)'), type: 'range', min: 0, max: 1000, step: 10 },
    { key: 'frame_padding', label: t('Padding cornice (px base)'), type: 'range', min: 0, max: 120, step: 2 },
    { key: 'media_bg', label: t('Sfondo media'), type: 'color' },

    { type: 'separator', label: t('Raggio') },
    { key: 'media_radius', label: t('Raggio foto'), type: 'border-radius' },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'pad_custom', label: t('Padding contenuto personalizzato'), type: 'toggle',
      description: t('Disattivo = padding responsivo automatico (cornice). Attivo = usa i valori sotto.') },
    { key: 'content_padding', label: t('Padding contenuto'), type: 'spacing',
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Velo / overlay') },
    { key: 'overlay_top', label: t('Velo in alto'), type: 'range', min: 0, max: 1, step: 0.05 },
    { key: 'overlay_bottom', label: t('Velo in basso'), type: 'range', min: 0, max: 1, step: 0.05 },

    { type: 'separator', label: t('Colori testo') },
    { key: 'kicker_color', label: t('Colore kicker'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'headline_color', label: t('Colore titolo'), type: 'color' },
    { key: 'meta_color', label: t('Colore meta'), type: 'color' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
