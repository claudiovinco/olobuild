import { textEffectsFields, textEffectsDefaults } from './_shared';
import { shadowField } from './_shared.js';

export default {
  type: 'viewer360',
  name: 'Viewer 360°',
  icon: 'dashicons-admin-site-alt3',
  category: 'media',
  defaults: {
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
  },
  fields: [
    // ── SORGENTE ──
    { type: 'separator', label: 'Sorgente' },
    { key: 'source_type', label: 'Tipo', type: 'select', options: [
      { value: 'image', label: 'Foto 360° (equirectangular)' },
      { value: 'video', label: 'Video 360°' },
    ]},
    { key: 'image_url', label: 'Immagine panoramica 360°', type: 'image',
      condition: { field: 'source_type', value: 'image' } },
    { key: 'video_url', label: 'Video 360° (URL mp4/webm)', type: 'text', placeholder: 'https://...video-360.mp4',
      condition: { field: 'source_type', value: 'video' } },

    // ── NAVIGAZIONE ──
    { type: 'separator', label: 'Navigazione' },
    { key: 'autorotate', label: 'Rotazione automatica', type: 'toggle' },
    { key: 'autorotate_speed', label: 'Velocità rotazione (°/s)', type: 'range', min: 0.1, max: 5, step: 0.1,
      condition: { field: 'autorotate', value: true } },
    { key: 'mouse_drag', label: 'Trascinamento mouse', type: 'toggle' },
    { key: 'touch_drag', label: 'Trascinamento touch', type: 'toggle' },
    { key: 'scroll_zoom', label: 'Zoom con scroll', type: 'toggle' },
    { key: 'gyroscope', label: 'Giroscopio (mobile)', type: 'toggle' },

    // ── CONTROLLI ──
    { type: 'separator', label: 'Controlli UI' },
    { key: 'show_controls', label: 'Mostra barra controlli', type: 'toggle' },
    { key: 'show_fullscreen', label: 'Pulsante fullscreen', type: 'toggle',
      condition: { field: 'show_controls', value: true } },
    { key: 'show_zoom', label: 'Pulsanti zoom', type: 'toggle',
      condition: { field: 'show_controls', value: true } },
    { key: 'show_compass', label: 'Bussola', type: 'toggle',
      condition: { field: 'show_controls', value: true } },

    // ── VISTA INIZIALE ──
    { type: 'separator', label: 'Vista iniziale' },
    { key: 'default_yaw', label: 'Rotazione orizzontale (°)', type: 'range', min: -180, max: 180, step: 5 },
    { key: 'default_pitch', label: 'Inclinazione verticale (°)', type: 'range', min: -90, max: 90, step: 5 },
    { key: 'default_zoom', label: 'Zoom iniziale (%)', type: 'range', min: 10, max: 100, step: 5 },
    { key: 'min_zoom', label: 'Zoom minimo (%)', type: 'range', min: 10, max: 50, step: 5 },
    { key: 'max_zoom', label: 'Zoom massimo (%)', type: 'range', min: 50, max: 120, step: 5 },

    // ── LAYOUT ──
    { type: 'separator', label: 'Layout' },
    { key: 'height', label: 'Altezza (px)', type: 'range', min: 200, max: 800, step: 10 },
    { key: 'border_radius', label: 'Raggio bordi (px)', type: 'border-radius' },
    { key: 'border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'caption', label: 'Didascalia', type: 'text' },
    ...shadowField,
    ...textEffectsFields([ { value: 'caption', label: 'Solo Didascalia' } ]),
  ],
};
