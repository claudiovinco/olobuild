import { focalDefault } from './_shared.js';
import { t } from '@/i18n';

/**
 * Hero — Masked Video : hero full-bleed con pannello mascherato (bordo inferiore ad
 * arco via radial-mask), media/video di sfondo (slot, placeholder a strisce se vuoto),
 * watermark ghost gigante, pill con dot, H1 gigante uppercase con parola-accento, riga
 * sub + 2 CTA. Estratta dal blueprint OLOthemes "MaskedVideoHero" (verdano/pulse/fjordline).
 * Render Vue == PHP (MaskedVideoHeroTile.vue). Runtime: nessuno (parallax opzionale CSS).
 */
export default {
  type: 'maskedvideohero',
  name: t('Hero — Masked Video'),
  icon: 'dashicons-format-video',
  category: 'marketing',

  // Unificazione sfondo: il campo legacy bg_image (immagine/video poster) confluisce
  // nel pannello unico media_bg (immagine/video/gradiente/colore…). Non distruttivo:
  // la chiave vecchia resta come fallback nei renderer, la maschera resta su .mvh-media.
  bgMigrate: { imageKey: 'bg_image', imagePosKey: 'bg_image_object_position' },

  defaults: {
    media_bg: { type: 'none' },
    tag_text: 'Next home game · Sat 14 Mar · 15:00',
    tag_dot_color: '',
    headline_text: 'Forged on the',
    accent_text: 'pitch.',
    uppercase: true,
    subhead: 'Eight teams, one badge. Verdano FC has played, fought and grown in this city for fifty years — and we’re only getting started.',
    cta1_text: 'View fixtures',
    cta1_url: '#',
    cta2_text: 'Become a member',
    cta2_url: '#',
    bg_color: 'var(--olo-color-dark, #16263d)',
    bg_image: '',
    ...focalDefault('bg_image'),
    media_label: 'home hero — match footage · background video',
    overlay_color: 'var(--olo-color-dark, #16263d)',
    overlay_strength: 0.55,
    watermark_text: 'VFC',
    watermark_color: 'rgba(255,255,255,0.055)',
    accent: '',
    accent_on: 'var(--olo-color-dark, #16263d)',
    text_color: 'var(--olo-color-light, #f8f9fa)',
    sub_color: 'rgba(255,255,255,0.72)',
    arch: true,
    transparent_bg: false,
    min_height: 84,
  },

  fields: [
    { key: 'tag_text', label: t('Pill (eyebrow)'), type: 'text' },
    { key: 'headline_text', label: t('Titolo'), type: 'text' },
    { key: 'accent_text', label: t('Parola accento (colore)'), type: 'text' },
    { key: 'uppercase', label: t('Maiuscolo'), type: 'toggle' },
    { key: 'subhead', label: t('Sottotitolo'), type: 'textarea' },

    { type: 'separator', label: t('CTA') },
    { key: 'cta1_text', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'cta1_url', label: t('CTA 1 — link'), type: 'link' },
    { key: 'cta2_text', label: t('CTA 2 — testo'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — link'), type: 'link' },

    { type: 'separator', label: t('Sfondo / media') },
    { key: 'transparent_bg', label: t('Sfondo trasparente (no segnaposto)'), type: 'toggle',
      description: t('Niente colore pannello né striscia segnaposto: si vede lo sfondo della sezione.') },
    { key: 'media_bg', label: t('Sfondo / media (immagine, video, gradiente, colore…)'), type: 'background', showParallax: false },
    { key: 'media_label', label: t('Etichetta placeholder'), type: 'text' },
    { key: 'watermark_text', label: t('Watermark (ghost)'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Colore pannello'), type: 'color' },
    { key: 'accent', label: t('Accento (parola + CTA)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'accent_on', label: t('Testo su accento'), type: 'color' },
    { key: 'text_color', label: t('Colore titolo'), type: 'color' },
    { key: 'sub_color', label: t('Colore sottotitolo'), type: 'color' },
    { key: 'tag_dot_color', label: t('Dot pill (vuoto = accento)'), type: 'color' },
    { key: 'watermark_color', label: t('Colore watermark'), type: 'color' },
    { key: 'overlay_color', label: t('Colore velo'), type: 'color' },
    { key: 'overlay_strength', label: t('Intensità velo'), type: 'range', min: 0, max: 1, step: 0.05 },

    { type: 'separator', label: t('Forma') },
    { key: 'arch', label: t('Bordo inferiore ad arco (maschera)'), type: 'toggle' },
    { key: 'min_height', label: t('Altezza minima (vh)'), type: 'range', min: 50, max: 100, step: 1 },
  ],
};
