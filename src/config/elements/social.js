import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Social — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → links (piattaforme + URL), toggle show_labels, toggle use_brand_colors
 *   styleFields[] → preset, bg, typography_preset, layout (size, gap, alignment), stile (filled/outline/minimal),
 *                   icon_color, hover_effect, shadow, borderFields
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */

// Le piattaforme che il renderer sa disegnare (icon_paths() in class-social-tile.php).
// Elenco unico: lo usa anche la riga di icone del Link in Bio.
export const PIATTAFORME_SOCIAL = [
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
];

// Stili delle icone social nei menu (Mega Menu, social del menu mobile del Menu di navigazione): un solo
// elenco, resi da Olobuild_Social_Tile::css_menu(). I primi tre sono quelli storici del Mega Menu.
export const STILI_SOCIAL_MENU = [
  { value: 'plain',   label: t('Solo icona') },
  { value: 'circle',  label: t('Cerchio') },
  { value: 'rounded', label: t('Rettangolo arrotondato') },
  { value: 'outline', label: t('Contorno') },
  { value: 'square',  label: t('Quadrato') },
  { value: 'brand',   label: t('Colori dei marchi') },
  { value: 'pill',    label: t('Pillola con il nome') },
];

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
    tile_padding: { top: 16, right: 16, bottom: 16, left: 16 },
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Appena nata: i quattro social più usati da un'attività come cerchi pieni nel colore
  // primario del sito (non nei colori dei marchi), che si sollevano al passaggio.
  partenza: {
    links: [
      { id: 's-1', platform: 'instagram', url: 'https://www.instagram.com/' },
      { id: 's-2', platform: 'facebook',  url: 'https://www.facebook.com/' },
      { id: 's-3', platform: 'linkedin',  url: 'https://www.linkedin.com/' },
      { id: 's-4', platform: 'youtube',   url: 'https://www.youtube.com/' },
    ],
    size: '44',
    use_brand_colors: false,
    icon_color: 'var(--olo-color-primary)',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Piattaforme') },
    { key: 'links', label: t('Piattaforme'), type: 'content-items',
      itemFields: [
        { key: 'platform', label: t('Piattaforma'), type: 'select', options: PIATTAFORME_SOCIAL },
        { key: 'url', label: t('URL'), type: 'link', placeholder: t('https://...') },
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
    { key: 'size', label: t('Dim. icona'), type: 'range', min: 20, max: 60, step: 4 },
    { key: 'gap', label: t('Gap'), type: 'range', min: 0, max: 48, step: 4 },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'tile_padding', label: t('Padding'), type: 'spacing', max: 100 },

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
