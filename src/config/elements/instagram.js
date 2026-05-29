
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Instagram — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → URL, tipo embed, toggle didascalia
 *   styleFields[] → preset, typography_preset, larghezza, colore sfondo, border-radius, allineamento, bordo
 *   defaults      → identico (nessuna modifica)
 */
export default {
  type: 'instagram',
  name: t('Instagram'),
  icon: 'dashicons-instagram',
  category: 'marketing',
  defaults: {
    preset: 'custom',
    typography_preset: '',
    url: '',
    embed_type: 'post',
    width: '100%',
    caption: true,
    background_color: '',
    border_radius: '8',
    alignment: 'center',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'url', label: t('URL Instagram'), type: 'text', placeholder: t('https://www.instagram.com/p/...') },
    { key: 'embed_type', label: t('Tipo embed'), type: 'select', options: [
      { value: 'post', label: t('Post / Reel') },
      { value: 'profile', label: t('Profilo') },
    ]},
    { key: 'caption', label: t('Mostra didascalia'), type: 'toggle' },
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
    { key: 'width', label: t('Larghezza'), type: 'text', placeholder: '100%' },
    { key: 'background_color', label: t('Colore sfondo'), type: 'color' },
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    ...borderFields(),
  ],
};
