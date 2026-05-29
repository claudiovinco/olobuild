import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Social — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → links (piattaforme + URL), toggle show_labels, toggle use_brand_colors
 *   styleFields[] → preset, bg, typography_preset, layout (size, gap, alignment), stile (filled/outline/minimal),
 *                   icon_color, hover_effect, shadow, borderFields
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'social',
  name: t('Link social'),
  icon: 'dashicons-share',
  category: 'marketing',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    links: [
      { id: 's-1', platform: 'facebook',  url: 'https://facebook.com' },
      { id: 's-2', platform: 'twitter',   url: 'https://x.com' },
      { id: 's-3', platform: 'instagram', url: 'https://instagram.com' },
      { id: 's-4', platform: 'linkedin',  url: 'https://linkedin.com' },
    ],
    size: '32',
    alignment: 'center',
    gap: '12',
    shadow: 'none',
    style: 'filled',
    show_labels: false,
    icon_color: '',
    hover_effect: 'lift',
    use_brand_colors: true,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Piattaforme') },
    { key: 'links', label: t('Piattaforme'), type: 'content-items',
      itemFields: [
        { key: 'platform', label: t('Piattaforma'), type: 'select', options: [
          { value: 'facebook',  label: t('Facebook') },
          { value: 'twitter',   label: t('X / Twitter') },
          { value: 'instagram', label: t('Instagram') },
          { value: 'linkedin',  label: t('LinkedIn') },
          { value: 'youtube',   label: t('YouTube') },
          { value: 'tiktok',    label: t('TikTok') },
          { value: 'whatsapp',  label: t('WhatsApp') },
          { value: 'telegram',  label: t('Telegram') },
          { value: 'pinterest', label: t('Pinterest') },
          { value: 'discord',   label: t('Discord') },
          { value: 'twitch',    label: t('Twitch') },
          { value: 'spotify',   label: t('Spotify') },
          { value: 'snapchat',  label: t('Snapchat') },
          { value: 'github',    label: t('GitHub') },
          { value: 'email',     label: t('Email') },
          { value: 'website',   label: t('Sito web') },
        ]},
        { key: 'url', label: t('URL'), type: 'text' },
      ],
    },
    { key: 'show_labels', label: t('Mostra nomi piattaforma'), type: 'toggle' },
    { key: 'use_brand_colors', label: t('Colori brand'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-pills', label: t('Modern Pills') },
      { value: 'minimal-line', label: t('Minimal Line') },
      { value: 'magazine-bold', label: t('Magazine Bold') },
      { value: 'circle-icons', label: t('Circle Icons') },
      { value: 'compact-row', label: t('Compact Row') },
      { value: 'glass-pills', label: t('Glass Pills') },
      { value: 'neon-glow', label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-pills', label: t('Gradient Pills') },
      { value: 'sticker-fun', label: t('Sticker Fun') },
      { value: 'retro-vhs', label: t('Retro VHS') },
      { value: 'tilt-3d', label: t('3D Tilt') },
      { value: 'custom', label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Layout') },
    { key: 'size', label: t('Dim. icona (px)'), type: 'range', min: 20, max: 60, step: 4 },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 48, step: 4 },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Stile') },
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'filled', label: t('Pieno (sfondo colorato)') },
      { value: 'outline', label: t('Contorno') },
      { value: 'minimal', label: t('Minimale') },
    ]},
    { key: 'icon_color', label: t('Colore personalizzato'), type: 'color',
      condition: { field: 'use_brand_colors', operator: '!=', value: true } },

    { type: 'separator', label: t('Hover') },
    { key: 'hover_effect', label: t('Effetto hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'lift', label: t('Solleva') },
      { value: 'grow', label: t('Ingrandisci') },
      { value: 'glow', label: t('Bagliore') },
    ]},

    { type: 'separator', label: t('Aspetto') },
    ...shadowField,
    ...borderFields(),
  ],
};
