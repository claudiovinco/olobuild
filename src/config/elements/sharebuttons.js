
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile ShareButtons — split CONTENUTO/STILE.
 *   fields[]      → buttons (platform + custom_label)
 *   styleFields[] → preset, bg, typo, stile (icon-only/text/etc.), size, gap, alignment, colore icona+hover, sfondo, border
 */
export default {
  type: 'sharebuttons',
  name: t('Condivisione'),
  icon: 'dashicons-share-alt',
  category: 'marketing',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    buttons: [
      { id: 'sh-1', platform: 'facebook',  custom_label: '' },
      { id: 'sh-2', platform: 'twitter',   custom_label: '' },
      { id: 'sh-3', platform: 'whatsapp',  custom_label: '' },
      { id: 'sh-4', platform: 'linkedin',  custom_label: '' },
      { id: 'sh-5', platform: 'email',     custom_label: '' },
    ],
    style: 'icon-only',
    size: '36',
    gap: '10',
    alignment: 'center',
    icon_color: '',
    icon_hover_color: '',
    bg_color: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'buttons', label: t('Pulsanti'), type: 'content-items',
      itemFields: [
        { key: 'platform', label: t('Piattaforma'), type: 'select', options: [
          { value: 'facebook',  label: t('Facebook') },
          { value: 'twitter',   label: t('X / Twitter') },
          { value: 'whatsapp',  label: t('WhatsApp') },
          { value: 'linkedin',  label: t('LinkedIn') },
          { value: 'email',     label: t('Email') },
          { value: 'copylink',  label: t('Copia link') },
          { value: 'telegram',  label: t('Telegram') },
          { value: 'pinterest', label: t('Pinterest') },
        ]},
        { key: 'custom_label', label: t('Etichetta personalizzata'), type: 'text' },
      ],
    },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-pills',    label: t('Modern Pills') },
      { value: 'icon-only',       label: t('Icon Only') },
      { value: 'minimal-line',    label: t('Minimal Line') },
      { value: 'circle-icons',    label: t('Circle Icons') },
      { value: 'magazine-row',    label: t('Magazine Row') },
      { value: 'glass-pills',     label: t('Glass Pills') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-pills',  label: t('Gradient Pills') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-vhs',       label: t('Retro VHS') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Aspetto pulsanti') },
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'icon-only',  label: t('Solo icona') },
      { value: 'icon-text',  label: t('Icona + testo') },
      { value: 'text-only',  label: t('Solo testo') },
    ]},
    { key: 'size', label: t('Dimensione (px)'), type: 'range', min: 24, max: 64, step: 2 },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 4, max: 24, step: 2 },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right',  label: t('Destra') },
    ]},

    { type: 'separator', label: t('Colori') },
    withHover({ key: 'icon_color', label: t('Colore icona'), type: 'color' }, { hoverKey: 'icon_hover_color' }),
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },

    ...borderFields(),
  ],
};
