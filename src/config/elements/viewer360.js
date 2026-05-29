import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Viewer360 — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → source_type, image_url, video_url, navigazione (autorotate, mouse_drag,
 *                   touch_drag, scroll_zoom, gyroscope), toggle controlli (show_controls,
 *                   show_fullscreen, show_zoom, show_compass), vista iniziale (yaw, pitch,
 *                   zoom, min_zoom, max_zoom), didascalia (testo)
 *   styleFields[] → preset, typography_preset, textEffectsFields, autorotate_speed (°/s),
 *                   height (px), border_radius, shadow + borderFields
 */
export default {
  type: 'viewer360',
  name: t('Viewer 360°'),
  icon: 'dashicons-admin-site-alt3',
  category: 'media',
  defaults: {
    typography_preset: '',
    preset: 'custom',
    source_type: 'image',
    image_url: '',
    video_url: '',
    // Navigation
    autorotate: true,
    autorotate_speed: '1',
    mouse_drag: true,
    touch_drag: true,
    scroll_zoom: true,
    gyroscope: false,
    // UI
    show_controls: true,
    show_fullscreen: true,
    show_zoom: true,
    show_compass: false,
    // Initial view
    default_yaw: '0',
    default_pitch: '0',
    default_zoom: '50',
    min_zoom: '20',
    max_zoom: '80',
    // Layout
    height: '400',
    border_radius: 0,
    // Overlay
    caption: '',
    caption_position: 'bottom',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'caption',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // ── SORGENTE ──
    { type: 'separator', label: t('Sorgente') },
    { key: 'source_type', label: t('Tipo'), type: 'select', options: [
      { value: 'image', label: t('Foto 360° (equirectangular)') },
      { value: 'video', label: t('Video 360°') },
    ]},
    { key: 'image_url', label: t('Immagine panoramica 360°'), type: 'image',
      condition: { field: 'source_type', value: 'image' } },
    { key: 'video_url', label: t('Video 360° (URL mp4/webm)'), type: 'text', placeholder: t('https://...video-360.mp4'),
      condition: { field: 'source_type', value: 'video' } },

    // ── NAVIGAZIONE ──
    { type: 'separator', label: t('Navigazione') },
    { key: 'autorotate', label: t('Rotazione automatica'), type: 'toggle' },
    { key: 'mouse_drag', label: t('Trascinamento mouse'), type: 'toggle' },
    { key: 'touch_drag', label: t('Trascinamento touch'), type: 'toggle' },
    { key: 'scroll_zoom', label: t('Zoom con scroll'), type: 'toggle' },
    { key: 'gyroscope', label: t('Giroscopio (mobile)'), type: 'toggle' },

    // ── CONTROLLI ──
    { type: 'separator', label: t('Controlli UI') },
    { key: 'show_controls', label: t('Mostra barra controlli'), type: 'toggle' },
    { key: 'show_fullscreen', label: t('Pulsante fullscreen'), type: 'toggle',
      condition: { field: 'show_controls', value: true } },
    { key: 'show_zoom', label: t('Pulsanti zoom'), type: 'toggle',
      condition: { field: 'show_controls', value: true } },
    { key: 'show_compass', label: t('Bussola'), type: 'toggle',
      condition: { field: 'show_controls', value: true } },

    // ── VISTA INIZIALE ──
    { type: 'separator', label: t('Vista iniziale') },
    { key: 'default_yaw', label: t('Rotazione orizzontale (°)'), type: 'range', min: -180, max: 180, step: 5 },
    { key: 'default_pitch', label: t('Inclinazione verticale (°)'), type: 'range', min: -90, max: 90, step: 5 },
    { key: 'default_zoom', label: t('Zoom iniziale (%)'), type: 'range', min: 10, max: 100, step: 5 },
    { key: 'min_zoom', label: t('Zoom minimo (%)'), type: 'range', min: 10, max: 50, step: 5 },
    { key: 'max_zoom', label: t('Zoom massimo (%)'), type: 'range', min: 50, max: 120, step: 5 },

    // ── Didascalia (testo) ──
    { type: 'separator', label: t('Didascalia') },
    { key: 'caption', label: t('Didascalia'), type: 'text' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-frame',   label: t('Minimal Frame') },
      { value: 'cinema-wide',     label: t('Cinema Wide') },
      { value: 'showcase-bold',   label: t('Showcase Bold') },
      { value: 'product-display', label: t('Product Display') },
      { value: 'glass-frame',     label: t('Glass Frame') },
      { value: 'neon-frame',      label: t('Neon Frame') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-glow',   label: t('Gradient Glow') },
      { value: 'sticker-frame',   label: t('Sticker Frame') },
      { value: 'retro-monitor',   label: t('Retro Monitor') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([ { value: 'caption', label: t('Solo Didascalia') } ]),

    // ── Navigazione (velocità rotazione) ──
    { type: 'separator', label: t('Animazione') },
    { key: 'autorotate_speed', label: t('Velocità rotazione (°/s)'), type: 'range', min: 0.1, max: 5, step: 0.1,
      condition: { field: 'autorotate', value: true } },

    // ── LAYOUT ──
    { type: 'separator', label: t('Layout') },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 200, max: 800, step: 10 },
    withHover({ key: 'border_radius', label: t('Raggio bordi (px)'), type: 'border-radius' }),

    ...shadowField,
    ...borderFields(),
  ],
};
