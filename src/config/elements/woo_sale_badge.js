
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Woo Sale Badge — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → testo badge, testo custom, forma, posizione
 *   styleFields[] → preset, sfondo, tipografia, dimensioni, peso, colori, bordo
 */
export default {
  type: 'woo_sale_badge',
  name: t('Badge Offerta'),
  icon: 'dashicons-awards',
  category: 'woocommerce',
  placeholder: t('Badge sconto personalizzabile per prodotto'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    badge_text: 'auto',
    custom_text: 'Offerta!',
    badge_bg: '',
    badge_color: '',
    badge_shape: 'pill',
    position: 'top-left',
    font_size: '14',
    font_weight: '700',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'badge_text', label: t('Testo badge'), type: 'select', options: [
      { value: 'auto', label: t('Automatico (%)') },
      { value: '%', label: t('Solo percentuale') },
      { value: 'custom', label: t('Testo personalizzato') },
    ]},
    { key: 'custom_text', label: t('Testo personalizzato'), type: 'text', condition: { field: 'badge_text', value: 'custom' } },
    { key: 'badge_shape', label: t('Forma'), type: 'select', options: [
      { value: 'circle', label: t('Cerchio') },
      { value: 'pill', label: t('Pillola') },
      { value: 'rectangle', label: t('Rettangolo') },
    ]},
    { key: 'position', label: t('Posizione'), type: 'select', options: [
      { value: 'top-left', label: t('Alto sinistra') },
      { value: 'top-right', label: t('Alto destra') },
      { value: 'bottom-left', label: t('Basso sinistra') },
      { value: 'bottom-right', label: t('Basso destra') },
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

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Badge'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'font_size',
        weight: 'font_weight',
        color:  'badge_color',
      },
      sizeMin: 10, sizeMax: 32,
    },

    { type: 'separator', label: t('Colori') },
    { key: 'badge_bg', label: t('Sfondo badge'), type: 'color' },
    ...borderFields(),
  ],
};
