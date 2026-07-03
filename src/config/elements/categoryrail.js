import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, focalField } from './_shared.js';

// Default no-op per i nuovi controlli additivi Spaziatura + Forma.
// Identici alla resa attuale: caption padding 16px 18px, card radius 14px.
const CAP_PADDING_DEFAULT = { top: 16, right: 18, bottom: 16, left: 18 };
const CARD_RADIUS_DEFAULT = { tl: 14, tr: 14, br: 14, bl: 14 };

/**
 * Category Rail — rail orizzontale drag-scroll di tessere categoria/prodotto
 * (immagine + overlay + titolo + sottotitolo, link). Estratta dai blueprint
 * OLOthemes (CategoryRail/ProductRail con data-hscroll: carrello, terra, field&co).
 * Render Vue == PHP (CategoryRailTile.vue). Runtime drag inline scoped (no '&&'/'<').
 */
export default {
  type: 'categoryrail',
  name: t('Category Rail (scroll)'),
  icon: 'dashicons-grid-view',
  category: 'media',

  defaults: {
    items: [
      { image: '', title: 'Ceramics', subtitle: '', link: '#' },
      { image: '', title: 'Art & prints', subtitle: '', link: '#' },
      { image: '', title: 'Jewellery', subtitle: '', link: '#' },
      { image: '', title: 'Homeware', subtitle: '', link: '#' },
      { image: '', title: 'Vintage', subtitle: '', link: '#' },
      { image: '', title: 'Stationery', subtitle: '', link: '#' },
    ],
    card_width: 260,
    card_aspect: '4/5',
    gap: 16,
    media_bg: '',
    overlay_color: 'rgba(16,16,21,0.5)',
    title_color: 'var(--olo-color-light, #f8f9fa)',
    subtitle_color: 'rgba(255,255,255,0.8)',
    radius: 14,
    object_position: 'center center',
    show_hint: true,
    hint_text: '← drag →',
    hint_color: '',

    // Controlli additivi (no-op): padding caption + raggio tessera. I default
    // riproducono ESATTAMENTE la resa attuale; usati come override solo se
    // modificati rispetto al default (gating nel render → byte-identici).
    cap_padding: { ...CAP_PADDING_DEFAULT },
    card_radius: { ...CARD_RADIUS_DEFAULT },

    // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
    // Default no-op: bg none / shadow none / border 0 → render invariato.
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Tessere') },
    { key: 'items', label: t('Categorie'), type: 'content-items',
      itemLabel: t('Tessera'),
      defaults: { image: '', title: 'Categoria', subtitle: '', link: '#' },
      itemFields: [
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'subtitle', label: t('Sottotitolo'), type: 'text' },
        { key: 'link', label: t('Link'), type: 'link' },
      ],
    },
    { type: 'separator', label: t('Suggerimento drag') },
    { key: 'show_hint', label: t('Mostra suggerimento'), type: 'toggle' },
    { key: 'hint_text', label: t('Testo suggerimento'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Tessere') },
    { key: 'card_width', label: t('Larghezza tessera (px)'), type: 'range', min: 180, max: 380, step: 10 },
    { key: 'card_aspect', label: t('Proporzioni'), type: 'select', options: [
      { value: '4/5', label: '4:5' },
      { value: '3/4', label: '3:4' },
      { value: '1/1', label: '1:1' },
      { value: '3/2', label: '3:2' },
    ]},
    { key: 'gap', label: t('Spazio tra tessere (px)'), type: 'range', min: 8, max: 32, step: 2 },
    { key: 'radius', label: t('Raggio bordo (px)'), type: 'range', min: 0, max: 32, step: 1 },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'cap_padding', label: t('Padding didascalia (px)'), type: 'spacing', max: 64 },

    { type: 'separator', label: t('Forma') },
    { key: 'card_radius', label: t('Raggio tessera (px)'), type: 'border-radius' },

    { type: 'separator', label: t('Colori') },
    { key: 'media_bg', label: t('Sfondo media'), type: 'color' },
    focalField('image', { key: 'object_position', src: '', reveal: true, label: t('Posizione — punto focale immagini') }),
    { key: 'overlay_color', label: t('Velo overlay'), type: 'color' },
    { key: 'title_color', label: t('Colore titolo'), type: 'color' },
    { key: 'subtitle_color', label: t('Colore sottotitolo'), type: 'color' },
    { key: 'hint_color', label: t('Colore suggerimento'), type: 'color' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
