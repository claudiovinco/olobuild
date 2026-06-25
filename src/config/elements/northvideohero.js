import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

/**
 * Hero — North Video : hero SaaS scuro stile "Cohere North". Eyebrow MONO nudo + crest ORB
 * wireframe + H1 display gigante + mockup prodotto in cornice scura con VERO <video> nativo
 * (poster + controlli) o media placeholder. Dietro, immagine con background-attachment:fixed
 * mascherata (parallasse "sfondo fisso"). Reveal del mockup all'ingresso (reduced-motion safe).
 * Render Vue == PHP (NorthVideoHeroTile.vue).
 */
export default {
  type: 'northvideohero',
  name: t('Hero — North Video'),
  icon: 'dashicons-video-alt2',
  category: 'marketing',

  defaults: {
    eyebrow_text: 'NORTH',
    crest_on: true,
    headline_text: 'AI for business that turns complexity into clarity',
    accent_text: '',
    subhead: '',
    cta1_text: '', cta1_url: '#',
    cta2_text: '', cta2_url: '#',

    mock_mode: 'video',
    video_src: '',
    video_poster: '',
    show_controls: true,
    autoplay: false,
    muted: true,
    loop: false,
    media_label: 'product — North workspace',
    mock_reveal: true,

    bg_fixed_image: '',
    bg_fixed_from: 42,

    headline_max: 1100,
    frame_radius: { tl: 20, tr: 20, br: 20, bl: 20 },
    content_padding: { top: 160, right: 40, bottom: 96, left: 40 },

    bg_color: 'var(--olo-color-dark, #16263d)',
    text_color: 'var(--olo-color-light, #f8f9fa)',
    eyebrow_color: 'rgba(255,255,255,0.78)',
    sub_color: 'rgba(255,255,255,0.72)',
    accent: '',
    crest_color: 'rgba(255,255,255,0.5)',
    frame_bg: 'var(--olo-color-dark, #16263d)',
    frame_border: 'rgba(255,255,255,0.12)',

    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'eyebrow_text', label: t('Eyebrow (mono)'), type: 'text' },
    { key: 'crest_on', label: t('Mostra crest (orb)'), type: 'toggle' },
    { key: 'headline_text', label: t('Titolo'), type: 'textarea' },
    { key: 'accent_text', label: t('Parola accento (opzionale)'), type: 'text' },
    { key: 'subhead', label: t('Sottotitolo (opzionale)'), type: 'textarea' },

    { type: 'separator', label: t('CTA (opzionali)') },
    { key: 'cta1_text', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'cta1_url', label: t('CTA 1 — link'), type: 'link' },
    { key: 'cta2_text', label: t('CTA 2 — testo'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — link'), type: 'link' },

    { type: 'separator', label: t('Mockup prodotto') },
    { key: 'mock_mode', label: t('Tipo mockup'), type: 'select', options: [
      { value: 'video', label: t('Video') },
      { value: 'media', label: t('Placeholder') },
      { value: 'none', label: t('Nessuno') },
    ] },
    { key: 'video_src', label: t('File video (mp4/webm)'), type: 'media',
      condition: { field: 'mock_mode', op: 'eq', value: 'video' } },
    { key: 'video_poster', label: t('Poster / immagine'), type: 'media',
      condition: { field: 'mock_mode', op: 'eq', value: 'video' } },
    { key: 'show_controls', label: t('Mostra controlli player'), type: 'toggle',
      condition: { field: 'mock_mode', op: 'eq', value: 'video' } },
    { key: 'autoplay', label: t('Autoplay (muto)'), type: 'toggle',
      condition: { field: 'mock_mode', op: 'eq', value: 'video' } },
    { key: 'muted', label: t('Muto'), type: 'toggle',
      condition: { field: 'mock_mode', op: 'eq', value: 'video' } },
    { key: 'loop', label: t('Loop'), type: 'toggle',
      condition: { field: 'mock_mode', op: 'eq', value: 'video' } },
    { key: 'media_label', label: t('Etichetta placeholder'), type: 'text',
      condition: { field: 'mock_mode', op: 'eq', value: 'media' } },
    { key: 'mock_reveal', label: t('Reveal mockup all\'ingresso'), type: 'toggle' },
  ],

  styleFields: [
    { type: 'separator', label: t('Sfondo fisso (parallasse)') },
    { key: 'bg_fixed_image', label: t('Immagine sfondo fisso'), type: 'media',
      description: t('Layer con background-attachment:fixed mascherato in basso — es. erba aerea.') },
    { key: 'bg_fixed_from', label: t('Inizio comparsa (%)'), type: 'range', min: 0, max: 100, step: 1,
      condition: { field: 'bg_fixed_image', op: 'neq', value: '' } },

    { type: 'separator', label: t('Colori — superfici') },
    { key: 'bg_color', label: t('Sfondo sezione'), type: 'color' },
    { key: 'frame_bg', label: t('Sfondo cornice mockup'), type: 'color' },
    { key: 'frame_border', label: t('Bordo cornice mockup'), type: 'color' },

    { type: 'separator', label: t('Colori — testo') },
    { key: 'text_color', label: t('Colore titolo'), type: 'color' },
    { key: 'eyebrow_color', label: t('Colore eyebrow'), type: 'color' },
    { key: 'sub_color', label: t('Colore sottotitolo'), type: 'color' },
    { key: 'accent', label: t('Accento'), type: 'color', description: t('Vuoto = primario del tema.') },
    { key: 'crest_color', label: t('Colore crest'), type: 'color' },

    { type: 'separator', label: t('Layout') },
    { key: 'headline_max', label: t('Larghezza max titolo (px)'), type: 'range', min: 480, max: 1600, step: 20 },
    { key: 'content_padding', label: t('Padding contenuto'), type: 'spacing', max: 240 },
    { key: 'frame_radius', label: t('Angoli cornice mockup'), type: 'border-radius' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
