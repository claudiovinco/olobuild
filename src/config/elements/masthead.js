import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Hero — Masthead (Dispatch) : testata di giornale stampato. Riga edizione/data ·
 * nameplate serif gigante centrato · azioni a destra, tra righe sottili (hairline rules).
 * Sotto, lead-story: kicker, titolo serif (riga normale + riga corsiva), standfirst, firma.
 * Meccanismo firma = la testata stampata. Render Vue == PHP (MastheadTile.vue). Nessun JS.
 */
export default {
  type: 'masthead',
  name: t('Hero — Masthead (Dispatch)'),
  icon: 'dashicons-text-page',
  category: 'marketing',

  defaults: {
    edition_text: 'Friday, 6 March 2026 · Milan',
    nameplate_text: 'The Dispatch',
    action1_text: 'Sign in',
    action1_url: '#',
    action2_text: 'Subscribe',
    action2_url: '#newsletter',
    kicker_text: 'Politics · Analysis',
    headline_text: 'Inside the budget deal',
    headline_italic_text: 'that almost didn\'t happen',
    subhead: 'For seventy-two hours the talks looked dead. Then a late-night compromise on housing rewrote the maths — and the coalition with it. We reconstruct the week.',
    byline_text: 'By Elena Marchetti · 12 min read',
    bg_color: 'var(--olo-color-light, #f8f9fa)',
    ink_color: 'var(--olo-color-text, #1f2937)',
    ink_soft_color: 'var(--olo-color-text, #1f2937)',
    ink_faint_color: 'var(--olo-color-text-soft, #6b7280)',
    accent: 'var(--olo-color-primary, #e1474f)',
    rule_color: 'var(--olo-color-border, #e5e7eb)',
    nameplate_size: 52,
    headline_size: 54,

    // Spaziatura (additivo, no-op coi default).
    // Il blocco lead ha padding RESPONSIVO clamp() → override GATED (default off = clamp invariato).
    pad_custom: false,
    lead_padding: { top: 52, right: 0, bottom: 60, left: 0 },

    // Forma — raggio del bottone "Subscribe" (default 2px = invariato).
    btn_radius: { tl: 2, tr: 2, br: 2, bl: 2 },

    // KIT standard OLObuild (additivo, no-op coi default)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Testata') },
    { key: 'edition_text', label: t('Edizione / data'), type: 'text' },
    { key: 'nameplate_text', label: t('Nameplate (testata)'), type: 'text' },
    { key: 'action1_text', label: t('Azione 1 — testo'), type: 'text' },
    { key: 'action1_url', label: t('Azione 1 — link'), type: 'link' },
    { key: 'action2_text', label: t('Azione 2 (bottone) — testo'), type: 'text' },
    { key: 'action2_url', label: t('Azione 2 (bottone) — link'), type: 'link' },

    { type: 'separator', label: t('Articolo di apertura') },
    { key: 'kicker_text', label: t('Occhiello (kicker)'), type: 'text' },
    { key: 'headline_text', label: t('Titolo'), type: 'text' },
    { key: 'headline_italic_text', label: t('Titolo — seconda riga (corsivo)'), type: 'text' },
    { key: 'subhead', label: t('Standfirst (sottotitolo)'), type: 'textarea' },
    { key: 'byline_text', label: t('Firma (byline)'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Sfondo (carta)'), type: 'color' },
    { key: 'ink_color', label: t('Inchiostro (titoli)'), type: 'color' },
    { key: 'ink_soft_color', label: t('Inchiostro tenue (testo)'), type: 'color' },
    { key: 'ink_faint_color', label: t('Inchiostro chiaro (meta)'), type: 'color' },
    { key: 'accent', label: t('Accento (kicker + bottone)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'rule_color', label: t('Colore righe sottili'), type: 'color' },

    { type: 'separator', label: t('Tipografia') },
    { key: 'nameplate_size', label: t('Dimensione nameplate (px)'), type: 'range', min: 24, max: 96, step: 1 },
    { key: 'headline_size', label: t('Dimensione titolo (px)'), type: 'range', min: 24, max: 96, step: 1 },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'pad_custom', label: t('Padding personalizzato (blocco articolo)'), type: 'toggle',
      description: t('Off = spaziatura responsive automatica. On = usa i valori sotto (px).') },
    { key: 'lead_padding', label: t('Padding articolo (px)'), type: 'spacing', max: 160,
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Forma') },
    { key: 'btn_radius', label: t('Raggio bottone (px)'), type: 'border-radius' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,
    ...borderFields(),
  ],
};
