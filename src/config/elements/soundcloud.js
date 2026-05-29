
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile SoundCloud — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → URL, toggle visual/autoplay, toggle visibilità (artwork/user)
 *   styleFields[] → preset, altezza, colore accento, allineamento, border-radius, bordo
 *   defaults      → identico (nessuna modifica)
 */
export default {
  type: 'soundcloud',
  name: t('SoundCloud'),
  icon: 'dashicons-format-audio',
  category: 'media',
  defaults: {
    preset: 'custom',
    url: '',
    auto_play: false,
    show_artwork: true,
    show_user: true,
    color: '',
    visual: true,
    height: '166',
    alignment: 'center',
    border_radius: '8',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'url', label: t('URL SoundCloud'), type: 'text', placeholder: t('https://soundcloud.com/...') },
    { key: 'visual', label: t('Player visuale (grande)'), type: 'toggle' },
    { type: 'separator', label: t('Riproduzione') },
    { key: 'auto_play', label: t('Riproduzione automatica'), type: 'toggle' },
    { key: 'show_artwork', label: t('Mostra copertina'), type: 'toggle' },
    { key: 'show_user', label: t('Mostra autore'), type: 'toggle' },
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
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 100, max: 600, step: 10 },
    { type: 'separator', label: t('Stile') },
    { key: 'color', label: t('Colore accento'), type: 'color' },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    withHover({ key: 'border_radius', label: t('Arrotondamento angoli (px)'), type: 'border-radius' }),
    ...borderFields(),
  ],
};
