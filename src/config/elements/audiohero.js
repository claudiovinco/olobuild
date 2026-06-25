import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Hero — Audio (Soundwave) : hero da musicista/band con SOUNDWAVE animato (equalizer
 * bars in puro CSS) come meccanica firma. Layout 2 colonne: sinistra tag+eq, H1 display
 * gigante, sottotitolo, 2 CTA (play / tour); destra cover art quadrata + mini-player
 * inline (play, track meta, waveform). Sfondo a doppio glow radiale (mint + pink).
 * Estratta PIXEL-PERFECT dal blueprint OLOthemes "Soundwave" (.sw-hero).
 * Render Vue == PHP (AudioHeroTile.vue). Nessun JS (animazioni solo CSS).
 */
export default {
  type: 'audiohero',
  name: t('Hero — Audio (Soundwave)'),
  icon: 'dashicons-format-audio',
  category: 'marketing',

  defaults: {
    tag_text: 'New album · out now',
    headline_text: 'Nightglass',
    subhead: 'Eleven tracks recorded between Berlin and a cabin with bad wifi. Late-night electronics for headphones and dancefloors alike.',
    cta1_text: 'Play album', cta1_url: '#listen',
    cta2_text: 'See tour dates', cta2_url: '#tour',
    cover_image: '', cover_label: 'album cover — Nightglass, neon on black',
    object_position: 'center center',
    player_track: 'Glasshouse', player_meta: 'Kova · Nightglass', show_player: true,
    bg_color: 'var(--olo-color-dark, #16263d)', panel_color: 'var(--olo-color-dark, #16263d)',
    accent: 'var(--olo-color-accent, #f4a23b)', accent_2: 'var(--olo-color-primary, #e1474f)', accent_on: 'var(--olo-color-dark, #16263d)',
    text_color: 'var(--olo-color-light, #f8f9fa)', sub_color: 'var(--olo-color-text-faint, #94a3b8)', meta_color: 'var(--olo-color-text-soft, #6b7280)',
    split_ratio: '1.1fr .9fr',

    // Spaziatura (override gated del padding interno responsivo) — default no-op.
    pad_custom: false,
    content_padding: { top: 72, right: 28, bottom: 72, left: 28 },

    // Forma — raggi additivi (default = raggi attuali hardcoded → no-op).
    cover_radius: { tl: 18, tr: 18, br: 18, bl: 18 },
    player_radius: { tl: 14, tr: 14, br: 14, bl: 14 },

    // KIT standard OLObuild — sfondo completo + ombra + bordo (default no-op)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'tag_text', label: t('Eyebrow (con barre eq)'), type: 'text' },
    { key: 'headline_text', label: t('Titolo'), type: 'text' },
    { key: 'subhead', label: t('Sottotitolo'), type: 'textarea' },

    { type: 'separator', label: t('CTA') },
    { key: 'cta1_text', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'cta1_url', label: t('CTA 1 — link'), type: 'link' },
    { key: 'cta2_text', label: t('CTA 2 — testo'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — link'), type: 'link' },

    { type: 'separator', label: t('Cover & player') },
    { key: 'cover_image', label: t('Cover album (vuoto = placeholder)'), type: 'image' },
    { key: 'object_position', label: t('Posizione contenuto'), type: 'object-position',
      contextKeys: { src: 'cover_image', ratio: '1/1', fit: 'cover' } },
    { key: 'cover_label', label: t('Etichetta placeholder cover'), type: 'text' },
    { key: 'show_player', label: t('Mostra mini-player'), type: 'toggle' },
    { key: 'player_track', label: t('Player — traccia'), type: 'text' },
    { key: 'player_meta', label: t('Player — meta (artista · album)'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Sfondo'), type: 'color' },
    { key: 'panel_color', label: t('Pannello (cover/player)'), type: 'color' },
    { key: 'accent', label: t('Accento (mint)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'accent_2', label: t('Accento 2 (glow secondario)'), type: 'color' },
    { key: 'accent_on', label: t('Testo su accento'), type: 'color' },
    { key: 'text_color', label: t('Colore titolo'), type: 'color' },
    { key: 'sub_color', label: t('Colore sottotitolo'), type: 'color' },
    { key: 'meta_color', label: t('Colore meta'), type: 'color' },

    { type: 'separator', label: t('Layout') },
    { key: 'split_ratio', label: t('Proporzione colonne'), type: 'text',
      description: t('Es. "1.1fr .9fr" (testo / cover).') },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'pad_custom', label: t('Padding interno personalizzato'), type: 'toggle',
      description: t('Disattivo = padding responsivo automatico (clamp).') },
    { key: 'content_padding', label: t('Padding interno (px)'), type: 'spacing', max: 160,
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Forma') },
    { key: 'cover_radius', label: t('Raggio cover'), type: 'border-radius' },
    { key: 'player_radius', label: t('Raggio mini-player'), type: 'border-radius' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
