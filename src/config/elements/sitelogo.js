
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Site Logo — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → asset (logo image, dark, retina, svg), origine, link, motto toggle
 *   styleFields[] → preset, bg, typo preset, dimensioni, allineamento, effetti hover, bordo
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'sitelogo',
  name: t('Logo sito'),
  icon: 'dashicons-admin-home',
  category: 'navigation',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    source: 'custom_image',
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

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'source', label: t('Origine'), type: 'select',
      description: t('Auto = usa il logo configurato in Aspetto → Personalizza → Identità Sito.'),
      options: [
        { value: 'custom_image', label: t('Immagine personalizzata') },
        { value: 'svg', label: t('SVG personalizzato') },
        { value: 'auto', label: t('Auto (logo del tema WordPress)') },
      ]},
    { key: 'custom_image', label: t('Logo principale'), type: 'image',
      condition: { field: 'source', value: 'custom_image' } },
    { key: 'retina_image', label: t('Logo Retina (2x)'), type: 'image',
      condition: { field: 'source', value: 'custom_image' } },
    { key: 'svg_logo', label: t('Logo SVG'), type: 'image',
      condition: { field: 'source', value: 'svg' } },

    { type: 'separator', label: t('Variante scura') },
    { key: 'dark_mode', label: t('Logo per sfondo scuro'), type: 'select', options: [
      { value: 'none', label: t('Non usare') },
      { value: 'auto', label: t('Automatico (prefers-color-scheme)') },
      { value: 'class', label: t('Con classe .olo-dark') },
      { value: 'sticky', label: t('Quando header diventa sticky') },
    ]},
    { key: 'dark_image', label: t('Logo versione chiara (per sfondo scuro)'), type: 'image',
      condition: { field: 'dark_mode', operator: '!=', value: 'none' } },

    { type: 'separator', label: t('Link') },
    { key: 'link_home', label: t('Link alla homepage'), type: 'toggle' },
    { key: 'link_url', label: t('URL personalizzato (override)'), type: 'link', placeholder: t('https://...'),
      condition: { field: 'link_home', value: true } },

    { type: 'separator', label: t('Motto / Tagline') },
    { key: 'show_tagline', label: t('Mostra motto del sito'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean', label: t('Modern Clean') },
      { value: 'minimal-mono', label: t('Minimal Mono') },
      { value: 'magazine-bold', label: t('Magazine Bold') },
      { value: 'centered-large', label: t('Centered Large') },
      { value: 'compact-inline', label: t('Compact Inline') },
      { value: 'glass-frame', label: t('Glass Frame') },
      { value: 'neon-glow', label: t('Neon Glow') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-soft', label: t('Gradient Soft') },
      { value: 'sticker-fun', label: t('Sticker Fun') },
      { value: 'retro-vhs', label: t('Retro VHS') },
      { value: 'tilt-3d', label: t('3D Tilt') },
      { value: 'custom', label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Dimensioni') },
    { key: 'max_height', label: t('Altezza massima (px)'), type: 'range', min: 16, max: 200, step: 2 },
    { key: 'max_height_sticky', label: t('Altezza in sticky (px, vuoto = uguale)'), type: 'number', min: 0, placeholder: t('es. 36') },
    { key: 'max_width', label: t('Larghezza massima (px, vuoto = auto)'), type: 'number', min: 0 },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Tagline') },
    { key: 'tagline_color', label: t('Colore motto'), type: 'color',
      condition: { field: 'show_tagline', value: true } },
    { key: 'tagline_size', label: t('Dimensione motto (px)'), type: 'range', min: 10, max: 24, step: 1,
      condition: { field: 'show_tagline', value: true } },

    { type: 'separator', label: t('Effetti') },
    { key: 'hover_opacity', label: t('Opacità hover (%)'), type: 'range', min: 20, max: 100, step: 5 },
    { key: 'transition_duration', label: t('Durata transizione (s)'), type: 'range', min: 0, max: 1, step: 0.05 },

    ...borderFields(),
  ],
};
