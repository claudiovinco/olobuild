
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
// Site Logo — logo del sito.
// D1 (text effects) N/A: nessun testo proprio (solo immagine + alt).
// D2 (filter sull'immagine) N/A: i loghi del brand non vanno filtrati/sfocati.
export default {
  type: 'sitelogo',
  name: 'Logo sito',
  icon: 'dashicons-admin-home',
  category: 'navigation',
  defaults: {
    source: 'auto',
    custom_image: '',
    dark_image: '',
    dark_mode: 'none',
    svg_logo: '',
    max_height: 50,
    max_height_sticky: '',
    max_width: '',
    link_home: true,
    link_url: '',
    show_tagline: false,
    tagline_color: '',
    tagline_size: '14',
    alignment: 'left',
    retina_image: '',
    hover_opacity: '',
    transition_duration: '0.3',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    // ── LOGO ──
    { type: 'separator', label: 'Logo' },
    { key: 'source', label: 'Origine', type: 'select', options: [
      { value: 'auto', label: 'Logo / Titolo WordPress' },
      { value: 'custom_image', label: 'Immagine personalizzata' },
      { value: 'svg', label: 'SVG personalizzato' },
    ]},
    { key: 'custom_image', label: 'Logo principale', type: 'image',
      condition: { field: 'source', value: 'custom_image' } },
    { key: 'retina_image', label: 'Logo Retina (2x)', type: 'image',
      condition: { field: 'source', value: 'custom_image' } },
    { key: 'svg_logo', label: 'Logo SVG', type: 'image',
      condition: { field: 'source', value: 'svg' } },

    // ── VARIANTE DARK ──
    { type: 'separator', label: 'Variante scura' },
    { key: 'dark_mode', label: 'Logo per sfondo scuro', type: 'select', options: [
      { value: 'none', label: 'Non usare' },
      { value: 'auto', label: 'Automatico (prefers-color-scheme)' },
      { value: 'class', label: 'Con classe .olo-dark' },
      { value: 'sticky', label: 'Quando header diventa sticky' },
    ]},
    { key: 'dark_image', label: 'Logo versione chiara (per sfondo scuro)', type: 'image',
      condition: { field: 'dark_mode', operator: '!=', value: 'none' } },

    // ── DIMENSIONI ──
    { type: 'separator', label: 'Dimensioni' },
    { key: 'max_height', label: 'Altezza massima (px)', type: 'range', min: 16, max: 200, step: 2 },
    { key: 'max_height_sticky', label: 'Altezza in sticky (px, vuoto = uguale)', type: 'text', placeholder: 'es. 36' },
    { key: 'max_width', label: 'Larghezza massima (px, vuoto = auto)', type: 'text' },
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},

    // ── LINK ──
    { type: 'separator', label: 'Link' },
    { key: 'link_home', label: 'Link alla homepage', type: 'toggle' },
    { key: 'link_url', label: 'URL personalizzato (override)', type: 'text', placeholder: 'https://...',
      condition: { field: 'link_home', value: true } },

    // ── MOTTO ──
    { type: 'separator', label: 'Motto / Tagline' },
    { key: 'show_tagline', label: 'Mostra motto del sito', type: 'toggle' },
    { key: 'tagline_color', label: 'Colore motto', type: 'color',
      condition: { field: 'show_tagline', value: true } },
    { key: 'tagline_size', label: 'Dimensione motto (px)', type: 'range', min: 10, max: 24, step: 1,
      condition: { field: 'show_tagline', value: true } },

    // ── EFFETTI ──
    { type: 'separator', label: 'Effetti' },
    { key: 'hover_opacity', label: 'Opacità hover (%)', type: 'range', min: 20, max: 100, step: 5 },
    { key: 'transition_duration', label: 'Durata transizione (s)', type: 'range', min: 0, max: 1, step: 0.05 },
    ...borderFields(),
  ],
};
