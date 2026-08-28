import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Hero — Search (Carrello) : hero e-commerce centrato con glow radiale superiore,
 * eyebrow coral, H1 gigante con parola-accento, sub, BARRA DI RICERCA (input + button)
 * come meccanica-firma e riga di chip-categoria. Estratta pixel-perfect dal blueprint
 * OLOthemes "Carrello" (.ca-hero). Render Vue == PHP (SearchHeroTile.vue). Runtime: nessuno.
 */
export default {
  type: 'searchhero',
  name: t('Hero — Search (Carrello)'),
  icon: 'dashicons-search',
  category: 'marketing',
  // Ritirata dalla palette (unificazione hero, Fase 2): assorbita da `hero`
  // (scena glow + modulo "barra di ricerca + chip"). I template salvati continuano a renderizzare.
  hidden: true,

  defaults: {
    eyebrow_text: 'Marketplace for independent makers',
    headline_text: 'Everything good,',
    headline_line2: 'from',
    accent_text: 'small shops.',
    subhead: 'Thousands of independent sellers, one cart, one checkout. Find the thing — and support the person who made it.',
    search_placeholder: 'Search 90,000+ handmade things…',
    search_button: 'Search',
    search_url: '#',
    chips: 'Ceramics, Prints, Jewellery, Homeware, Vintage, Gifts',
    bg_color: 'var(--olo-color-dark, #16263d)',
    panel_color: 'var(--olo-color-dark, #16263d)',
    accent: 'var(--olo-color-primary, #e1474f)',
    accent_on: 'var(--olo-color-light, #f8f9fa)',
    glow_color: 'rgba(255,90,95,0.22)',
    text_color: 'var(--olo-color-light, #f8f9fa)',
    sub_color: 'var(--olo-color-text-soft, #6b7280)',
    chip_color: 'var(--olo-color-text-faint, #94a3b8)',
    border_color: 'rgba(255,255,255,0.09)',
    search_border: 'rgba(255,90,95,0.4)',
    min_height: 0,

    // Spaziatura (additivo, no-op): override GATED del padding responsive del contenitore.
    // pad_custom=false → resta clamp(52px,7vw,92px) 0 (responsivo, invariato).
    pad_custom: false,
    content_padding: { top: 52, right: 0, bottom: 52, left: 0 },

    // Raggio (additivo, no-op): raggio della barra di ricerca (firma) — default 14px invariato.
    search_radius: { tl: 14, tr: 14, br: 14, bl: 14 },

    // KIT standard OLObuild (additivo, no-op coi default) — contenitore principale
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'eyebrow_text', label: t('Eyebrow'), type: 'text' },
    { key: 'headline_text', label: t('Titolo — riga 1'), type: 'text' },
    { key: 'headline_line2', label: t('Titolo — riga 2 (prefisso)'), type: 'text' },
    { key: 'accent_text', label: t('Parola accento (colore)'), type: 'text' },
    { key: 'subhead', label: t('Sottotitolo'), type: 'textarea' },

    { type: 'separator', label: t('Barra di ricerca') },
    { key: 'search_placeholder', label: t('Placeholder ricerca'), type: 'text' },
    { key: 'search_button', label: t('Testo pulsante'), type: 'text' },
    { key: 'search_url', label: t('Link ricerca / chip'), type: 'link' },

    { type: 'separator', label: t('Chip categorie') },
    { key: 'chips', label: t('Chip (separate da virgola)'), type: 'text',
      description: t('Es. Ceramics, Prints, Jewellery') },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Sfondo'), type: 'color' },
    { key: 'panel_color', label: t('Sfondo barra ricerca'), type: 'color' },
    { key: 'accent', label: t('Accento (eyebrow + parola + button)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'accent_on', label: t('Testo su accento'), type: 'color' },
    { key: 'glow_color', label: t('Colore glow'), type: 'color' },
    { key: 'text_color', label: t('Colore titolo'), type: 'color' },
    { key: 'sub_color', label: t('Colore sottotitolo'), type: 'color' },
    { key: 'chip_color', label: t('Colore chip'), type: 'color' },
    { key: 'border_color', label: t('Bordo chip'), type: 'color' },
    { key: 'search_border', label: t('Bordo barra ricerca'), type: 'color' },

    { type: 'separator', label: t('Forma') },
    { key: 'min_height', label: t('Altezza minima (vh, 0 = auto)'), type: 'range', min: 0, max: 100, step: 1 },
    { key: 'search_radius', label: t('Raggio barra ricerca'), type: 'border-radius' },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'pad_custom', label: t('Padding personalizzato'), type: 'toggle',
      description: t('Off = padding responsivo predefinito. On = usa i valori sotto.') },
    { key: 'content_padding', label: t('Padding contenitore (px)'), type: 'spacing', max: 200,
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,
    ...borderFields(),
  ],
};
