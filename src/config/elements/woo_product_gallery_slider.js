
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Gallery Slider — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle thumbnails/zoom/lightbox, posizione thumb, autoplay/loop behavior,
 *                   frecce/dots (comportamento navigazione), tipo transizione
 *   styleFields[] → preset, sfondo, tipografia, altezza/dimensioni, gap, raggio bordi, autoplay speed,
 *                   colori (sfondo, bordo miniature, frecce, dots), ombra, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_product_gallery_slider',
  name: t('WC Gallery Slider'),
  icon: 'dashicons-images-alt2',
  category: 'woocommerce',
  placeholder: t('Gallery slider prodotto WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    show_thumbnails: true,
    thumbnail_position: 'bottom',
    enable_zoom: true,
    enable_lightbox: true,
    main_height: '500px',
    thumbnail_size: '80',
    thumbnail_gap: '8',
    autoplay: false,
    autoplay_speed: '3000',
    transition: 'slide',
    arrows: true,
    dots: false,
    border_radius: '8',
    main_bg: '',
    thumbnail_border: '',
    thumbnail_active_border: '',
    arrow_color: '',
    arrow_bg: '',
    dot_color: '',
    dot_active_color: '',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Immagine principale') },
    { key: 'enable_zoom', label: t('Abilita zoom'), type: 'toggle' },
    { key: 'enable_lightbox', label: t('Abilita lightbox'), type: 'toggle' },

    { type: 'separator', label: t('Miniature') },
    { key: 'show_thumbnails', label: t('Mostra miniature'), type: 'toggle' },
    { key: 'thumbnail_position', label: t('Posizione miniature'), type: 'select', options: [
      { value: 'bottom', label: t('Sotto') },
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Slider') },
    { key: 'transition', label: t('Transizione'), type: 'select', options: [
      { value: 'slide', label: t('Slide') },
      { value: 'fade', label: t('Fade') },
    ]},
    { key: 'autoplay', label: t('Autoplay'), type: 'toggle' },
    { key: 'arrows', label: t('Mostra frecce'), type: 'toggle' },
    { key: 'dots', label: t('Mostra dots'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'editorial-serif', label: t('Editorial Serif') },
      { value: 'compact-inline',  label: t('Compact Inline') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ] },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Immagine principale') },
    { key: 'main_height', label: t('Altezza principale'), type: 'text', placeholder: t('es. 500px, 60vh') },
    withHover({ key: 'border_radius', label: t('Raggio bordi (px)'), type: 'border-radius' }),

    { type: 'separator', label: t('Miniature') },
    { key: 'thumbnail_size', label: t('Dimensione miniature (px)'), type: 'range', min: 40, max: 150, step: 5 },
    { key: 'thumbnail_gap', label: t('Gap miniature (px)'), type: 'range', min: 0, max: 24, step: 2 },

    { type: 'separator', label: t('Slider') },
    { key: 'autoplay_speed', label: t('Velocita autoplay (ms)'), type: 'range', min: 1000, max: 10000, step: 500 },

    { type: 'separator', label: t('Colori') },
    { key: 'main_bg', label: t('Sfondo immagine'), type: 'color' },
    { key: 'thumbnail_border', label: t('Bordo miniature'), type: 'color' },
    { key: 'thumbnail_active_border', label: t('Bordo miniatura attiva'), type: 'color' },
    { key: 'arrow_color', label: t('Colore frecce'), type: 'color' },
    { key: 'arrow_bg', label: t('Sfondo frecce'), type: 'color' },
    { key: 'dot_color', label: t('Colore dots'), type: 'color' },
    { key: 'dot_active_color', label: t('Colore dot attivo'), type: 'color' },

    ...shadowField,
    ...borderFields(),
  ],
};
