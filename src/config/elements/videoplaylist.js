
import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile Video Playlist — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → array videos (content-items), toggle show_duration, autoplay_next
 *   styleFields[] → preset, bg, typography_preset, textEffects, layout, dimensioni, colori, bordo
 *   defaults      → identico (nessuna modifica)
 */
export default {
  type: 'videoplaylist',
  name: t('Video Playlist'),
  icon: 'dashicons-playlist-video',
  category: 'media',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    videos: [
      { id: 'vp-1', url: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', title: t('Primo video'), duration: '3:32', thumbnail: '' },
      { id: 'vp-2', url: 'https://www.youtube.com/watch?v=9bZkp7q19f0', title: t('Secondo video'), duration: '4:12', thumbnail: '' },
      { id: 'vp-3', url: 'https://www.youtube.com/watch?v=kJQP7kiw5Fk', title: t('Terzo video'), duration: '5:01', thumbnail: '' },
    ],
    layout: 'sidebar-right',
    player_height: '360',
    sidebar_width: '280',
    sidebar_bg: '',
    text_color: '',
    active_color: '',
    show_duration: true,
    autoplay_next: false,
    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'videos', label: t('Video'), type: 'content-items',
      itemFields: [
        { key: 'url', label: t('URL video'), type: 'text', placeholder: t('YouTube, Vimeo o URL MP4') },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'duration', label: t('Durata'), type: 'text', placeholder: t('es. 3:45') },
        { key: 'thumbnail', label: t('Anteprima'), type: 'image' },
      ],
      newItemDefaults: { url: '', title: t('Nuovo video'), duration: '', thumbnail: '' },
      itemLabel: 'Video',
    },
    { key: 'show_duration', label: t('Mostra durata'), type: 'toggle' },
    { key: 'autoplay_next', label: t('Autoplay successivo'), type: 'toggle' },
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
    ...textEffectsFields([ { value: 'title', label: t('Solo Titolo') } ]),
    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Disposizione'), type: 'select', options: [
      { value: 'sidebar-right', label: t('Playlist a destra') },
      { value: 'sidebar-left', label: t('Playlist a sinistra') },
      { value: 'below', label: t('Playlist sotto') },
    ]},
    { key: 'player_height', label: t('Altezza player (px)'), type: 'range', min: 200, max: 600, step: 10 },
    { key: 'sidebar_width', label: t('Larghezza sidebar (px)'), type: 'range', min: 200, max: 400, step: 10 },
    { type: 'separator', label: t('Stile') },
    { key: 'sidebar_bg', label: t('Sfondo playlist'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },
    { key: 'active_color', label: t('Colore attivo'), type: 'color' },
    ...borderFields(),
  ],
};
