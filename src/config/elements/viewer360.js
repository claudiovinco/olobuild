import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { shadowField } from './_shared.js';
import { demo } from '../demoMedia.js';
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
// I campi del video valgono solo nel panorama con «Video 360°» (array = tutte vere).
const conVideo = [{ field: 'mode', value: 'hdri' }, { field: 'source_type', value: 'video' }];

export default {
  type: 'viewer360',
  name: t('Viewer 360°'),
  icon: 'dashicons-admin-site-alt3',
  category: 'media',
  defaults: {
    typography_preset: '',
    preset: 'custom',
    mode: 'hdri',
    source_type: 'image',
    image_url: '',
    video_url: '',
    video_url_mobile: '',
    video_poster: '',
    video_autoplay: false,
    video_loop: true,
    video_audio: true,
    // Object-spin (turntable) — default OFF (mode=hdri): i Viewer360 esistenti restano invariati
    object_image: '',
    object_frames: [],
    spin_inertia: 0.97,
    drag_sensitivity: 0.55,
    show_angle: true,
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
    height_mobile: '',
    border_radius: 0,
    // Fondo dietro l'oggetto girevole: vuoto = il colore scuro della palette (com'era)
    stage_bg: '',
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

  // Appena trascinata: la modalità «Oggetto girevole» su un prodotto (le foto demo non sono
  // panorami equirettangolari: in «Panorama 360°» si vedrebbero stirate). Si ruota trascinando o
  // con le frecce, l'angolo è scritto in basso e la didascalia spiega il gesto.
  partenza: {
    mode: 'object-rotate',
    object_image: demo('ciotola'),
    autorotate: false,
    height: '420',
    border_radius: { tl: 16, tr: 16, br: 16, bl: 16 },
    caption: t('Ciotola in porcellana dipinta a mano · trascinala per farla girare'),
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // ── SORGENTE ──
    { type: 'separator', label: t('Sorgente') },
    { key: 'mode', label: t('Modalità'), type: 'select', options: [
      { value: 'hdri',          label: t('Panorama 360° (HDRI / equirect)') },
      { value: 'object-rotate', label: t('Oggetto girevole — 1 immagine (pseudo-3D)') },
      { value: 'object-frames', label: t('Oggetto girevole — sequenza di frame') },
    ], description: t('“Oggetto girevole”: trascina per ruotare un prodotto su sé stesso, con inerzia al rilascio. Pseudo-3D (una immagine ruotata) oppure sequenza di frame reale.') },

    { key: 'source_type', label: t('Tipo'), type: 'select',
      condition: { field: 'mode', value: 'hdri' }, options: [
      { value: 'image', label: t('Foto 360° (equirectangular)') },
      { value: 'video', label: t('Video 360°') },
    ]},
    { key: 'image_url', label: t('Immagine panoramica 360°'), type: 'image',
      condition: [{ field: 'mode', value: 'hdri' }, { field: 'source_type', op: 'neq', value: 'video' }] },
    // Dalla 1.4.975 il video si sceglie dalla Libreria media (la chiave salva sempre l'indirizzo, come prima).
    { key: 'video_url', label: t('Video 360°'), type: 'media', accept: 'video', condition: conVideo,
      description: t('Un video equirettangolare (2:1) in mp4 H.264. Per il computer bastano 3840×1920 a circa 10 Mbit/s: più grande non si vede meglio e molti schermi non lo reggono.') },
    { key: 'video_url_mobile', label: t('Video leggero per il telefono'), type: 'media', accept: 'video', condition: conVideo,
      description: t('Facoltativo: lo stesso video a 2560×1280, circa 5 Mbit/s. Su telefoni e tablet si usa questo: molti non riescono a disegnare un video più largo di 4096 px e resterebbe nero.') },
    { key: 'video_poster', label: t('Copertina (foto 360°)'), type: 'image', condition: conVideo,
      description: t('Un fotogramma del video, in 2:1: si vede e si gira già prima del play. Senza copertina il riquadro resta scuro finché il video non parte.') },
    { key: 'video_autoplay', label: t('Parte da solo, senza audio'), type: 'toggle', condition: conVideo,
      description: t('Parte quando arriva sullo schermo e si ferma quando esce. Chi guarda può attivare l’audio dalla barra. Spento: parte col pulsante, già con l’audio.') },
    { key: 'video_loop', label: t('Ricomincia alla fine'), type: 'toggle', condition: conVideo },
    { key: 'video_audio', label: t('Pulsante dell’audio'), type: 'toggle', condition: conVideo,
      description: t('Spento: il video resta sempre muto.') },
    { key: 'object_image', label: t('Immagine oggetto'), type: 'image',
      condition: { field: 'mode', value: 'object-rotate' } },
    { key: 'object_frames', label: t('Frame della rotazione (in ordine)'), type: 'gallery',
      condition: { field: 'mode', value: 'object-frames' } },

    // ── NAVIGAZIONE ──
    { type: 'separator', label: t('Navigazione') },
    { key: 'autorotate', label: t('Rotazione automatica'), type: 'toggle' },
    { key: 'mouse_drag', label: t('Trascinamento mouse'), type: 'toggle' },
    { key: 'touch_drag', label: t('Trascinamento touch'), type: 'toggle' },
    { key: 'scroll_zoom', label: t('Zoom con scroll'), type: 'toggle',
      condition: { field: 'mode', value: 'hdri' } },
    { key: 'gyroscope', label: t('Giroscopio (mobile)'), type: 'toggle',
      condition: { field: 'mode', value: 'hdri' } },
    { key: 'show_angle', label: t('Mostra angolo di rotazione'), type: 'toggle',
      condition: { field: 'mode', op: 'in', value: ['object-rotate', 'object-frames'] } },

    // ── CONTROLLI ──
    { type: 'separator', label: t('Controlli UI') },
    { key: 'show_controls', label: t('Mostra barra controlli'), type: 'toggle' },
    { key: 'show_fullscreen', label: t('Pulsante fullscreen'), type: 'toggle',
      condition: { field: 'show_controls', value: true } },
    { key: 'show_zoom', label: t('Pulsanti zoom'), type: 'toggle',
      condition: { field: 'show_controls', value: true } },
    { key: 'show_compass', label: t('Bussola'), type: 'toggle',
      condition: { field: 'show_controls', value: true } },

    // ── VISTA INIZIALE (solo panorama) ──
    { type: 'separator', label: t('Vista iniziale'), condition: { field: 'mode', value: 'hdri' } },
    { key: 'default_yaw', label: t('Rotazione orizzontale (°)'), type: 'range', min: -180, max: 180, step: 5,
      condition: { field: 'mode', value: 'hdri' } },
    { key: 'default_pitch', label: t('Inclinazione verticale (°)'), type: 'range', min: -90, max: 90, step: 5,
      condition: { field: 'mode', value: 'hdri' } },
    { key: 'default_zoom', label: t('Zoom iniziale'), type: 'range', min: 10, max: 100, step: 5,
      condition: { field: 'mode', value: 'hdri' } },
    { key: 'min_zoom', label: t('Zoom minimo'), type: 'range', min: 10, max: 50, step: 5,
      condition: { field: 'mode', value: 'hdri' } },
    { key: 'max_zoom', label: t('Zoom massimo'), type: 'range', min: 50, max: 120, step: 5,
      condition: { field: 'mode', value: 'hdri' } },

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
    { key: 'stage_bg', label: t('Sfondo del riquadro'), type: 'color',
      description: t('Vuoto = il colore scuro della palette. Per le foto prodotto su fondo bianco scegli il bianco, o il colore del fondo delle foto: il prodotto si fonde col riquadro.'),
      condition: { field: 'mode', op: 'in', value: ['object-rotate', 'object-frames'] } },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([ { value: 'caption', label: t('Solo Didascalia') } ]),

    // ── Navigazione (velocità rotazione) ──
    { type: 'separator', label: t('Animazione') },
    { key: 'autorotate_speed', label: t('Velocità rotazione (°/s)'), type: 'range', min: 0.1, max: 5, step: 0.1,
      condition: { field: 'autorotate', value: true } },
    { key: 'drag_sensitivity', label: t('Sensibilità trascinamento'), type: 'range', min: 0.1, max: 1.5, step: 0.05,
      condition: { field: 'mode', op: 'in', value: ['object-rotate', 'object-frames'] } },
    { key: 'spin_inertia', label: t('Inerzia al rilascio'), type: 'range', min: 0.8, max: 0.99, step: 0.01,
      condition: { field: 'mode', op: 'in', value: ['object-rotate', 'object-frames'] } },

    // ── LAYOUT ──
    { type: 'separator', label: t('Layout') },
    { key: 'height', label: t('Altezza'), type: 'range', min: 200, max: 800, step: 10 },
    { key: 'height_mobile', label: t('Altezza sul telefono'), type: 'number', min: 160, max: 1200, step: 10, unit: 'px',
      placeholder: t('Come sul computer') },
    withHover({ key: 'border_radius', label: t('Raggio'), type: 'border-radius' }),

    ...shadowField,
    ...borderFields(),
  ],
};
