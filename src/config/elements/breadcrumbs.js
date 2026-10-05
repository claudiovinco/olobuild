import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Breadcrumbs — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → separatore, etichetta home, visibilità (Home, pagina corrente)
 *   styleFields[] → preset, sfondo, typography preset, ombra, bordo
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'breadcrumbs',
  name: t('Breadcrumbs'),
  icon: 'dashicons-arrow-right-alt2',
  category: 'navigation',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    separator: '/',
    home_label: 'Home',
    show_home: true,
    show_current: true,
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'separator', label: t('Separatore'), type: 'text' },
    { key: 'home_label', label: t('Etichetta Home'), type: 'text' },
    { key: 'show_home', label: t('Mostra Home'), type: 'toggle' },
    { key: 'show_current', label: t('Mostra pagina corrente'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'pill-rounded',    label: t('Pill Rounded') },
      { value: 'arrow-style',     label: t('Arrow Style') },
      { value: 'glass-pill',      label: t('Glass Pill') },
      { value: 'neon-trail',      label: t('Neon Trail') },
      { value: 'brutalist-mono',  label: t('Brutalist Mono') },
      { value: 'gradient-link',   label: t('Gradient Link') },
      { value: 'sticker-tags',    label: t('Sticker Tags') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-pills',      label: t('Tilt Pills') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...shadowField,
    ...borderFields(),
  ],
};
