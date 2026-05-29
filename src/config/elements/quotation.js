import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Quotation — split CONTENUTO/STILE.
 *   fields[]      → content, author
 *   styleFields[] → preset, bg, typo, style/alignment, text-effects, shadow, border
 */
export default {
  type: 'quotation',
  name: t('Citazione'),
  icon: 'dashicons-format-quote',
  category: 'text',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    content: 'La vita è quello che ti succede mentre sei impegnato a fare altri progetti.',
    author: 'John Lennon',
    style: 'default',
    alignment: 'left',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'content',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'content', label: t('Citazione'), type: 'textarea' },
    { key: 'author', label: t('Autore'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'editorial-classic', label: t('Editorial Classic') },
      { value: 'magazine-pull',     label: t('Magazine Pull') },
      { value: 'minimal-line',      label: t('Minimal Line') },
      { value: 'big-mark',          label: t('Big Mark') },
      { value: 'centered-script',   label: t('Centered Script') },
      { value: 'glass-frosted',     label: t('Glass Frosted') },
      { value: 'neon-quote',        label: t('Neon Quote') },
      { value: 'brutalist-stamp',   label: t('Brutalist Stamp') },
      { value: 'gradient-text',     label: t('Gradient Text') },
      { value: 'sticky-note',       label: t('Sticky Note') },
      { value: 'retro-typewriter',  label: t('Retro Typewriter') },
      { value: 'tilt-card',         label: t('Tilt Card') },
      { value: 'custom',            label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Layout') },
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'default', label: t('Predefinito') },
      { value: 'footer', label: t('Citazione a piè di pagina') },
    ]},
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    ...textEffectsFields([
      { value: 'content', label: t('Solo Contenuto') },
      { value: 'author', label: t('Solo Autore') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    ...shadowField,
    ...borderFields(),
  ],
};
