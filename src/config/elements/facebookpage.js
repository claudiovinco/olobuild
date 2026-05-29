
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Facebook Page — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → URL pagina, tab, toggle di visibilità (cover/facepile/header), lingua SDK, adapt_container
 *   styleFields[] → preset, typography_preset, dimensioni (width/height), allineamento, bordo
 *   defaults      → identico (nessuna modifica)
 */
export default {
  type: 'facebookpage',
  name: t('Facebook Page'),
  icon: 'dashicons-facebook',
  category: 'marketing',
  defaults: {
    preset: 'custom',
    typography_preset: '',
    page_url: '',
    width: '340',
    height: '500',
    tabs: 'timeline',
    show_cover: true,
    show_facepile: true,
    small_header: false,
    adapt_container: true,
    language: 'it_IT',
    alignment: 'center',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'page_url', label: t('URL pagina Facebook'), type: 'text', placeholder: t('https://www.facebook.com/...') },
    { key: 'tabs', label: t('Tab (timeline,events,messages)'), type: 'text', placeholder: t('timeline') },
    { key: 'show_cover', label: t('Mostra copertina'), type: 'toggle' },
    { key: 'show_facepile', label: t('Mostra facce amici'), type: 'toggle' },
    { key: 'small_header', label: t('Header compatto'), type: 'toggle' },
    { key: 'adapt_container', label: t('Adatta al contenitore'), type: 'toggle' },
    { key: 'language', label: t('Lingua SDK'), type: 'select', options: [
      { value: 'it_IT', label: t('Italiano') },
      { value: 'en_US', label: t('English') },
      { value: 'de_DE', label: t('Deutsch') },
      { value: 'fr_FR', label: t('Francais') },
      { value: 'es_ES', label: t('Espanol') },
    ]},
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
    { key: 'width', label: t('Larghezza (px)'), type: 'range', min: 180, max: 500, step: 10 },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 70, max: 1000, step: 10 },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    ...borderFields(),
  ],
};
