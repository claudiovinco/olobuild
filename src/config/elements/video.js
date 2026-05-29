import { textEffectsFields, textEffectsDefaults, shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Video — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → sorgente (YouTube/Vimeo/file), URL, poster, didascalia, overlay testo, opzioni privacy/playback (autoplay/mute/loop/controls/start/end/facade)
 *   styleFields[] → preset, typo, display_mode + height, border-radius, text-effects, play icon aspect, overlay aspect, ombra, bordo
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'video',
  name: t('Video'),
  icon: 'dashicons-video-alt3',
  category: 'essential',
  defaults: {
    typography_preset: '',
    preset: 'custom',
    source_type: 'embed',
    video_url: '',
    file_url: '',
    display_mode: '16:9',
    cover_height: '500',
    border_radius: 0,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    facade: true,
    autoplay: false,
    muted: false,
    loop: false,
    controls: true,
    start_time: '',
    end_time: '',
    poster_image: '',
    privacy_mode: false,
    show_play_icon: true,
    play_icon_size: '80',
    play_icon_color: '',
    overlay_text: '',
    overlay_color: '#000000',
    overlay_opacity: '0',
    overlay_text_size: '32',
    overlay_text_color: '#ffffff',
    overlay_text_weight: '700',
    overlay_text_align: 'center',
    caption: '',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'all',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'source_type', label: t('Sorgente'), type: 'select', options: [
      { value: 'embed', label: t('YouTube / Vimeo') },
      { value: 'file', label: t('File (MP4 / WebM)') },
    ]},
    { key: 'video_url', label: t('URL video'), type: 'text' },
    { key: 'file_url', label: t('File video'), type: 'media', accept: 'video' },
    { key: 'poster_image', label: t('Immagine poster'), type: 'image' },
    { key: 'overlay_text', label: t('Testo overlay'), type: 'textarea' },
    { key: 'caption', label: t('Didascalia'), type: 'text' },

    { type: 'separator', label: t('Riproduzione') },
    { key: 'autoplay', label: t('Riproduzione automatica'), type: 'toggle' },
    { key: 'muted', label: t('Silenziato'), type: 'toggle' },
    { key: 'loop', label: t('Ripeti'), type: 'toggle' },
    { key: 'controls', label: t('Mostra controlli'), type: 'toggle' },
    { key: 'start_time', label: t('Inizio (secondi)'), type: 'text' },
    { key: 'end_time', label: t('Fine (secondi)'), type: 'text' },
    { key: 'facade', label: t('Lazy Load (Facade)'), type: 'toggle' },
    { key: 'show_play_icon', label: t('Mostra icona play'), type: 'toggle' },

    { type: 'separator', label: t('Privacy') },
    { key: 'privacy_mode', label: t('Modo privacy (no-cookie)'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean', label: t('Modern Clean') },
      { value: 'minimal-frame', label: t('Minimal Frame') },
      { value: 'cinema-wide', label: t('Cinema Wide') },
      { value: 'magazine-bold', label: t('Magazine Bold') },
      { value: 'centered-large', label: t('Centered Large') },
      { value: 'glass-frame', label: t('Glass Frame') },
      { value: 'neon-glow', label: t('Neon Glow') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-border', label: t('Gradient Border') },
      { value: 'sticker-tape', label: t('Sticker Tape') },
      { value: 'retro-vhs', label: t('Retro VHS') },
      { value: 'tilt-3d', label: t('3D Tilt') },
      { value: 'custom', label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Dimensioni') },
    { key: 'display_mode', label: t('Visualizzazione'), type: 'select', options: [
      { value: '16:9', label: '16:9' },
      { value: '4:3', label: '4:3' },
      { value: '1:1', label: '1:1' },
      { value: 'cover', label: t('Cover (altezza fissa)') },
    ]},
    { key: 'cover_height', label: t('Altezza (px)'), type: 'range', min: 100, max: 1200, step: 10 },
    withHover({ key: 'border_radius', label: t('Raggio bordi (px)'), type: 'border-radius' }),

    ...textEffectsFields([
      { value: 'overlay_text', label: t('Solo Testo overlay') },
      { value: 'caption', label: t('Solo Didascalia') },
      { value: 'all', label: t('Entrambi') },
    ]),

    { type: 'separator', label: t('Icona play — Aspetto') },
    { key: 'play_icon_size', label: t('Dimensione icona play (px)'), type: 'range', min: 40, max: 160, step: 10 },
    { key: 'play_icon_color', label: t('Colore icona play'), type: 'color' },

    { type: 'separator', label: t('Overlay — Aspetto') },
    { key: 'overlay_color', label: t('Colore overlay'), type: 'color' },
    { key: 'overlay_opacity', label: t('Opacità overlay (%)'), type: 'range', min: 0, max: 100, step: 5 },
    { key: 'overlay_text_size', label: t('Dimensione testo (px)'), type: 'range', min: 12, max: 120, step: 1,
      condition: { field: 'overlay_text', op: 'notEmpty' } },
    { key: 'overlay_text_weight', label: t('Peso testo'), type: 'select', options: [
      { value: '300', label: t('Light (300)') },
      { value: '400', label: t('Normale (400)') },
      { value: '500', label: t('Medio (500)') },
      { value: '600', label: t('Semi-bold (600)') },
      { value: '700', label: t('Bold (700)') },
      { value: '800', label: t('Extra bold (800)') },
      { value: '900', label: t('Black (900)') },
    ], condition: { field: 'overlay_text', op: 'notEmpty' } },
    { key: 'overlay_text_color', label: t('Colore testo'), type: 'color',
      condition: { field: 'overlay_text', op: 'notEmpty' } },
    { key: 'overlay_text_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ], condition: { field: 'overlay_text', op: 'notEmpty' } },

    ...shadowField,
    ...borderFields(),
  ],
};
