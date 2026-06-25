import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

/**
 * Hero — Smear (Canvas) : hero da galleria d'artista. Sfondo caldo scuro con glow
 * radiale ambra, eyebrow uppercase a tracking largo, H1 gigante serif (Gilda Display)
 * con parola-accento in corsivo, paragrafo cream, hint in basso. Meccanica firma: il
 * "paint smear" — muovendo il cursore sulla hero si depositano pennellate di pigmento
 * colorato che sfumano (palette configurabile). Render Vue == PHP (SmearHeroTile.vue).
 * Estratta dal blueprint OLOthemes "Canvas — Jonah Veld".
 */
export default {
  type: 'smearhero',
  name: t('Hero — Smear (Canvas)'),
  icon: 'dashicons-art',
  category: 'marketing',

  defaults: {
    eyebrow_text: 'Painter · oil & pigment',
    headline_text: 'Jonah',
    accent_text: 'Veld',
    subhead: 'Large-scale abstracts about colour, weather and memory. Move your cursor — leave a mark.',
    hint_text: '↑ drag the colour around',
    bg_color: 'var(--olo-color-dark, #16263d)',
    glow_color: 'rgba(224,177,58,0.08)',
    eyebrow_color: 'var(--olo-color-accent, #f4a23b)',
    text_color: 'var(--olo-color-light, #f8f9fa)',
    accent_color: '',
    sub_color: 'var(--olo-color-light, #f8f9fa)',
    hint_color: 'var(--olo-color-text-soft, #6b7280)',
    smear_palette: '#e0b13a,#cc5b3f,#5b86b8,#f0ebe1',
    smear_enabled: true,
    min_height: 72,

    // Spaziatura (gated): il padding di base è responsivo clamp(48px,9vh,90px) 30px.
    // Override attivo SOLO se pad_custom=true → no-op coi default.
    pad_custom: false,
    content_padding: { top: 90, right: 30, bottom: 90, left: 30 },

    // Forma: raggio del contenitore hero (full-bleed) — default 0 = nessun arrotondamento (no-op).
    container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },

    // KIT standard OLObuild — additivi, no-op coi default (sfondo none, ombra none, bordo 0)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'eyebrow_text', label: t('Eyebrow'), type: 'text' },
    { key: 'headline_text', label: t('Titolo'), type: 'text' },
    { key: 'accent_text', label: t('Parola accento (corsivo)'), type: 'text' },
    { key: 'subhead', label: t('Sottotitolo'), type: 'textarea' },
    { key: 'hint_text', label: t('Hint in basso'), type: 'text' },

    { type: 'separator', label: t('Effetto pennellata') },
    { key: 'smear_enabled', label: t('Attiva il paint smear (cursore)'), type: 'toggle' },
    { key: 'smear_palette', label: t('Palette pigmenti (hex separati da virgola)'), type: 'text',
      description: t('Colori delle pennellate lasciate dal cursore.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Sfondo'), type: 'color' },
    { key: 'glow_color', label: t('Glow radiale'), type: 'color' },
    { key: 'eyebrow_color', label: t('Colore eyebrow'), type: 'color' },
    { key: 'text_color', label: t('Colore titolo'), type: 'color' },
    { key: 'accent_color', label: t('Accento (parola corsivo)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'sub_color', label: t('Colore sottotitolo'), type: 'color' },
    { key: 'hint_color', label: t('Colore hint'), type: 'color' },

    { type: 'separator', label: t('Dimensioni') },
    { key: 'min_height', label: t('Altezza minima (vh)'), type: 'range', min: 40, max: 100, step: 1 },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'pad_custom', label: t('Padding personalizzato'), type: 'toggle',
      description: t('Off = padding responsivo predefinito. On = usa i valori sotto.') },
    { key: 'content_padding', label: t('Padding contenuto (px)'), type: 'spacing',
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Forma') },
    { key: 'container_radius', label: t('Raggio contenitore (px)'), type: 'border-radius',
      description: t('Arrotonda gli angoli della hero.') },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
