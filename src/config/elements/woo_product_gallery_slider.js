
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
    max_width: '600',
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

  // Come nasce dalla palette (in una scheda prodotto): la foto principale su un fondo tenue con il
  // bollo dello sconto, frecce, zoom e lightbox, e le miniature della galleria sotto. Scioglie il
  // disaccordo sulla misura delle miniature fra PHP (72) e config (80).
  partenza: {
    thumbnail_size: '80',
    border_radius: '14',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // Il prodotto di cui mostrare la galleria: senza, la tile funzionava solo nella scheda prodotto.
    { type: 'separator', label: t('Prodotto') },
    { key: 'product_id', label: t('ID prodotto'), type: 'number', min: 0, step: 1,
      description: t('Il prodotto di cui mostrare le foto. Vuoto o 0 = il prodotto della pagina (nella scheda prodotto).') },

    { type: 'separator', label: t('Immagine principale') },
    { key: 'enable_zoom', label: t('Abilita zoom'), type: 'toggle' },
    { key: 'enable_lightbox', label: t('Abilita lightbox'), type: 'toggle' },

    { type: 'separator', label: t('Miniature') },
    { key: 'show_thumbnails', label: t('Mostra miniature'), type: 'toggle' },

    { type: 'separator', label: t('Slider') },
    { key: 'transition', label: t('Transizione'), type: 'select', options: [
      { value: 'slide', label: t('Slide') },
      { value: 'fade', label: t('Fade') },
    ]},
    { key: 'autoplay', label: t('Autoplay'), type: 'toggle' },
    { key: 'arrows', label: t('Mostra frecce'), type: 'toggle' },
    { key: 'dots', label: t('Mostra pallini'), type: 'toggle' },
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
    // Il PHP la limitava da sempre a 600 px (suo default) senza un controllo per cambiarla.
    { key: 'max_width', label: t('Larghezza massima'), type: 'range', min: 0, max: 1200, step: 10, unit: 'px',
      description: t('La larghezza oltre la quale la galleria non cresce. 0 = tutta la larghezza della cella.') },
    { key: 'main_height', label: t('Altezza principale'), type: 'text', placeholder: t('es. 500px, 60vh') },
    withHover({ key: 'border_radius', label: t('Raggio'), type: 'border-radius' }),

    { type: 'separator', label: t('Miniature') },
    { key: 'thumbnail_size', label: t('Dimensione miniature'), type: 'range', min: 40, max: 150, step: 5 },
    { key: 'thumbnail_gap', label: t('Gap miniature'), type: 'range', min: 0, max: 24, step: 2 },

    { key: 'thumbnail_position', label: t('Posizione miniature'), type: 'select', options: [
      { value: 'bottom', label: t('Sotto') },
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ]},
    { type: 'separator', label: t('Slider') },
    // Il tempo fra una foto e la successiva: senza autoplay non agisce, quindi si nasconde.
    { key: 'autoplay_speed', label: t('Intervallo autoplay'), type: 'range', min: 1000, max: 10000, step: 500, unit: 'ms',
      condition: { field: 'autoplay', value: true } },

    { type: 'separator', label: t('Colori') },
    { key: 'main_bg', label: t('Sfondo immagine'), type: 'color' },
    { key: 'thumbnail_border', label: t('Bordo miniature'), type: 'color' },
    { key: 'thumbnail_active_border', label: t('Bordo miniatura attiva'), type: 'color' },
    { key: 'arrow_color', label: t('Colore frecce'), type: 'color' },
    { key: 'arrow_bg', label: t('Sfondo frecce'), type: 'color' },
    { key: 'dot_color', label: t('Colore pallini'), type: 'color', condition: { field: 'dots', value: true } },
    { key: 'dot_active_color', label: t('Colore pallino attivo'), type: 'color', condition: { field: 'dots', value: true } },

    ...shadowField,
    ...borderFields(),
  ],
};
