import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Hero — Featured Story : hero editoriale "lead" a 2 colonne — grande immagine di copertina
 * su un lato + colonna editoriale sull'altro (kicker + headline serif + standfirst/deck +
 * byline/meta, CTA opzionali). Parametrizzata per riprodurre i blueprint OLOthemes
 * "gazette" (cream/serif Caslon/claret, media 4/3 a sinistra — DEFAULT) e "voyage"
 * (panel navy/serif Vollkorn/coral, standfirst italic, media radius 8). Render Vue == PHP
 * (FeaturedStoryTile.vue). Runtime: nessuno (pure CSS).
 */
export default {
  type: 'featuredstory',
  name: t('Hero — Featured Story'),
  icon: 'dashicons-media-document',
  category: 'marketing',

  defaults: {
    // Content
    kicker_text: 'The Essay · Cities',
    headline_text: 'The slow return of the city night market',
    headline_url: '#',
    standfirst: 'For a decade they were left for dead. Now, under the same lanterns, a new generation is rebuilding the night market — one stall, one recipe, one argument at a time.',
    byline_pre: 'By',
    byline_name: 'Elena Russo',
    byline_meta: '18 min read',
    cover_image: '',
    cover_url: '#',
    cover_label: 'cover — empty night market, lanterns, long exposure',
    // CTAs (optional)
    cta1_text: '',
    cta1_url: '#',
    cta2_text: '',
    cta2_url: '#',
    // Layout
    media_side: 'left',
    col_ratio: '1.15fr .85fr',
    cover_aspect: '4 / 3',
    media_radius: 0,
    standfirst_italic: false,
    placeholder_dark: true,
    // Spaziatura (override gated) — il padding verticale del root è responsivo (clamp),
    // quindi NON va sostituito: gated da pad_custom (default false → clamp invariato).
    pad_custom: false,
    content_padding: { top: 45, right: 0, bottom: 45, left: 0 },
    // Raggi (per-angolo, additivi) — default = raggio attuale → no-op.
    // cover_radius default {0,0,0,0}: se tutti 0 ricade su media_radius (anch'esso 0).
    cover_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
    cta_radius: { tl: 2, tr: 2, br: 2, bl: 2 },
    // Colors
    bg_color: '#f3f0e9',
    kicker_color: '#9a2b22',
    headline_color: '#16161a',
    accent_color: '',
    standfirst_color: '#2c2c30',
    byline_color: '#76746e',
    byline_name_color: '#16161a',
    media_bg: '#e9e4d8',
    cta_solid_bg: '#16161a',
    cta_solid_text: '#f3f0e9',
    // Fonts
    heading_font: "var(--olo-font-family-heading, 'Libre Caslon Display', Georgia, serif)",
    serif_font: "var(--olo-font-family-heading, 'Libre Caslon Text', Georgia, serif)",
    sans_font: "var(--olo-font-family, 'Mulish', -apple-system, sans-serif)",

    // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
    // Default no-op: bg 'none', shadow 'none', bordo 0 → render invariato.
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'kicker_text', label: t('Kicker (occhiello)'), type: 'text' },
    { key: 'headline_text', label: t('Titolo'), type: 'text' },
    { key: 'headline_url', label: t('Link del titolo'), type: 'link' },
    { key: 'standfirst', label: t('Standfirst (deck)'), type: 'textarea' },

    { type: 'separator', label: t('Firma / meta') },
    { key: 'byline_pre', label: t('Prefisso (es. By)'), type: 'text' },
    { key: 'byline_name', label: t('Autore'), type: 'text' },
    { key: 'byline_meta', label: t('Meta (es. 18 min read)'), type: 'text' },

    { type: 'separator', label: t('Copertina') },
    { key: 'cover_image', label: t('Immagine di copertina (vuoto = placeholder)'), type: 'image' },
    { key: 'cover_url', label: t('Link copertina'), type: 'link' },
    { key: 'cover_label', label: t('Etichetta placeholder'), type: 'text' },

    { type: 'separator', label: t('CTA (opzionali)') },
    { key: 'cta1_text', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'cta1_url', label: t('CTA 1 — link'), type: 'link' },
    { key: 'cta2_text', label: t('CTA 2 — testo'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — link'), type: 'link' },
  ],

  styleFields: [
    { type: 'separator', label: t('Layout') },
    { key: 'media_side', label: t('Lato immagine'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ] },
    { key: 'col_ratio', label: t('Rapporto colonne (media · testo)'), type: 'text',
      description: t('Es. "1.15fr .85fr". Invertito automaticamente se l\'immagine è a destra.') },
    { key: 'cover_aspect', label: t('Aspect ratio copertina'), type: 'text',
      description: t('Es. "4 / 3", "16 / 9", "4 / 5".') },
    { key: 'media_radius', label: t('Raggio copertina (px)'), type: 'range', min: 0, max: 40, step: 1 },
    { key: 'standfirst_italic', label: t('Standfirst in corsivo'), type: 'toggle' },
    { key: 'placeholder_dark', label: t('Placeholder scuro su chiaro'), type: 'toggle',
      description: t('Attivo = righe/etichetta scure (tema chiaro). Spegni per temi scuri (es. navy).') },

    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Sfondo sezione'), type: 'color' },
    { key: 'kicker_color', label: t('Colore kicker'), type: 'color' },
    { key: 'headline_color', label: t('Colore titolo'), type: 'color' },
    { key: 'accent_color', label: t('Accento (hover / focus)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'standfirst_color', label: t('Colore standfirst'), type: 'color' },
    { key: 'byline_color', label: t('Colore firma'), type: 'color' },
    { key: 'byline_name_color', label: t('Colore autore'), type: 'color' },
    { key: 'media_bg', label: t('Sfondo copertina (placeholder)'), type: 'color' },

    { type: 'separator', label: t('Colori CTA') },
    { key: 'cta_solid_bg', label: t('CTA piena — sfondo'), type: 'color' },
    { key: 'cta_solid_text', label: t('CTA piena — testo'), type: 'color' },

    { type: 'separator', label: t('Font') },
    { key: 'heading_font', label: t('Font titolo (display serif)'), type: 'font-family' },
    { key: 'serif_font', label: t('Font standfirst (serif)'), type: 'font-family' },
    { key: 'sans_font', label: t('Font sans (kicker/firma/CTA)'), type: 'font-family' },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'pad_custom', label: t('Padding sezione personalizzato'), type: 'toggle',
      description: t('Spento = padding verticale responsivo (clamp 34-56px). Acceso = usa i valori sotto.') },
    { key: 'content_padding', label: t('Padding sezione'), type: 'spacing',
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Raggio') },
    { key: 'cover_radius', label: t('Raggio copertina (per-angolo)'), type: 'border-radius',
      description: t('Se tutti gli angoli sono 0 vale "Raggio copertina (px)" sopra.') },
    { key: 'cta_radius', label: t('Raggio pulsanti CTA'), type: 'border-radius' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
