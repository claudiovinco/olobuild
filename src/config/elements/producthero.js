import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

/**
 * Hero — Product (SaaS) : hero centrato tech/SaaS. Eyebrow PILL + headline centrata con
 * parola-accento (gradiente) + subhead + 2 CTA, su RADIAL GLOW + griglia faint mascherata.
 * Sotto, mockup PRODUCT in cornice browser (barra con 3 pallini + URL opzionale):
 * media placeholder (modo "media") o dashboard con KPI cards (label/value/delta) + chart a
 * barre inline (modo "dashboard"). Tutto puro CSS/SVG, nessun JS.
 *
 * Parametrizzato per riprodurre 1:1 i blueprint OLOthemes Circuit (indigo glow + grid +
 * cornice browser) e DataFold (teal glow + KPI + bar chart). Default fedeli a Circuit.
 * Render Vue == PHP (ProductHeroTile.vue).
 */
export default {
  type: 'producthero',
  name: t('Hero — Product (SaaS)'),
  icon: 'dashicons-desktop',
  category: 'marketing',

  defaults: {
    pill_pre: 'New',
    pill_text: 'Circuit 3.0 — now with live workflows',
    headline_text: 'Ship reliable software,',
    accent_text: 'without the busywork.',
    subhead: 'Circuit connects the tools your team already uses, automates the hand-offs, and gives everyone one honest view of every release.',
    cta1_text: 'Start free — no card', cta1_url: '#',
    cta2_text: 'See how it works', cta2_url: '#',
    glow_on: true, glow_color: '#6c8cff',
    grid_on: true, grid_color: 'rgba(255,255,255,0.04)', grid_size: 48,
    mock_mode: 'dashboard',
    mock_url: 'app.circuit.io / workspace / releases',
    mock_label: 'product — workflow board &amp; live status dashboard',
    kpis: [
      { label: 'Net revenue', value: '$4.82M', delta: '▲ 18.4% MoM', down: '' },
      { label: 'Active accounts', value: '12,408', delta: '▲ 6.1% MoM', down: '' },
      { label: 'Churn', value: '1.9%', delta: '▼ 0.3 pts', down: '1' },
    ],
    chart_title: 'Revenue by week',
    chart_meta: 'last 12 weeks · live',
    bars: [
      { h: 38, label: 'w1', alt: '' }, { h: 46, label: 'w2', alt: '' }, { h: 41, label: 'w3', alt: '' },
      { h: 54, label: 'w4', alt: '' }, { h: 60, label: 'w5', alt: '' }, { h: 52, label: 'w6', alt: '' },
      { h: 68, label: 'w7', alt: '' }, { h: 74, label: 'w8', alt: '' }, { h: 66, label: 'w9', alt: '1' },
      { h: 82, label: 'w10', alt: '' }, { h: 90, label: 'w11', alt: '' }, { h: 100, label: 'w12', alt: '1' },
    ],
    bg_color: '#0b0d18', panel_color: '#141a2e', panel2_color: '#1b2238', cell_color: '#11142270',
    accent: '#6c8cff', accent2: '#b08bff', accent_on: '#ffffff', down_color: '',
    text_color: '#ffffff', sub_color: '#8a90a8', pill_text_color: '#c9cde0',
    pill_bg: 'rgba(255,255,255,0.05)',
    line_color: 'rgba(255,255,255,0.09)',
    pill_mono: false, mono_meta: true,

    // Spaziatura + Raggio (additivi, no-op coi default).
    // content_padding = padding dell'inner .oph-in (oggi '0 28px' fisso) → invariato.
    content_padding: { top: 0, right: 28, bottom: 0, left: 28 },
    // frame_radius = angoli cornice browser .oph-frame (oggi '16px 16px 0 0') → invariato.
    frame_radius: { tl: 16, tr: 16, br: 0, bl: 0 },
    // kpi_radius = angoli card KPI .oph-kpi (oggi '11px') → invariato.
    kpi_radius: { tl: 11, tr: 11, br: 11, bl: 11 },

    // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
    // Default no-op: bg none / shadow none / border 0 → render invariato.
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'pill_pre', label: t('Pill — prefisso (accento, in grassetto)'), type: 'text' },
    { key: 'pill_text', label: t('Pill — testo (eyebrow)'), type: 'text' },
    { key: 'headline_text', label: t('Titolo'), type: 'text' },
    { key: 'accent_text', label: t('Riga accento (gradiente, va a capo)'), type: 'text' },
    { key: 'subhead', label: t('Sottotitolo'), type: 'textarea' },

    { type: 'separator', label: t('CTA') },
    { key: 'cta1_text', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'cta1_url', label: t('CTA 1 — link'), type: 'link' },
    { key: 'cta2_text', label: t('CTA 2 — testo'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — link'), type: 'link' },

    { type: 'separator', label: t('Mockup prodotto') },
    { key: 'mock_mode', label: t('Tipo mockup'), type: 'select', options: [
      { value: 'dashboard', label: t('Dashboard (KPI + grafico)') },
      { value: 'media', label: t('Media placeholder') },
    ] },
    { key: 'mock_url', label: t('URL barra browser (solo dashboard)'), type: 'text' },
    { key: 'mock_label', label: t('Etichetta media placeholder (solo media)'), type: 'text' },

    { type: 'separator', label: t('KPI cards (dashboard)') },
    { key: 'kpis', label: t('KPI'), type: 'content-items',
      itemLabel: t('KPI'),
      defaults: { label: 'Metric', value: '0', delta: '', down: '' },
      itemFields: [
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'value', label: t('Valore'), type: 'text' },
        { key: 'delta', label: t('Variazione (es. ▲ 18.4%)'), type: 'text' },
        { key: 'down', label: t('Variazione negativa (colore alt)'), type: 'toggle' },
      ],
    },

    { type: 'separator', label: t('Grafico a barre (dashboard)') },
    { key: 'chart_title', label: t('Titolo grafico'), type: 'text' },
    { key: 'chart_meta', label: t('Sottotitolo grafico'), type: 'text' },
    { key: 'bars', label: t('Barre'), type: 'content-items',
      itemLabel: t('Barra'),
      defaults: { h: 50, label: '', alt: '' },
      itemFields: [
        { key: 'h', label: t('Altezza (%)'), type: 'range', min: 0, max: 100, step: 1 },
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'alt', label: t('Colore alternativo (accento 2)'), type: 'toggle' },
      ],
    },
  ],

  styleFields: [
    { type: 'separator', label: t('Sfondo backdrop') },
    { key: 'glow_on', label: t('Radial glow'), type: 'toggle' },
    { key: 'glow_color', label: t('Colore glow'), type: 'color' },
    { key: 'grid_on', label: t('Griglia faint'), type: 'toggle' },
    { key: 'grid_color', label: t('Colore griglia'), type: 'color' },
    { key: 'grid_size', label: t('Passo griglia (px)'), type: 'range', min: 16, max: 120, step: 2 },

    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Accento (pill/CTA/barre)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'accent2', label: t('Accento 2 (gradiente headline / barre alt)'), type: 'color' },
    { key: 'accent_on', label: t('Testo su CTA solida'), type: 'color' },
    { key: 'down_color', label: t('Colore variazione negativa'), type: 'color',
      description: t('Vuoto = usa accento 2.') },
    { key: 'text_color', label: t('Colore titolo / valori'), type: 'color' },
    { key: 'sub_color', label: t('Colore testo secondario'), type: 'color' },
    { key: 'pill_text_color', label: t('Colore testo pill'), type: 'color' },
    { key: 'pill_bg', label: t('Sfondo pill'), type: 'color' },
    { key: 'bg_color', label: t('Sfondo sezione'), type: 'color' },
    { key: 'panel_color', label: t('Sfondo cornice'), type: 'color' },
    { key: 'panel2_color', label: t('Sfondo barra browser'), type: 'color' },
    { key: 'cell_color', label: t('Sfondo celle KPI / grafico'), type: 'color' },
    { key: 'line_color', label: t('Colore bordi'), type: 'color' },

    { type: 'separator', label: t('Tipografia') },
    { key: 'pill_mono', label: t('Pill in monospace'), type: 'toggle' },
    { key: 'mono_meta', label: t('Meta grafico in monospace'), type: 'toggle' },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'content_padding', label: t('Padding contenuto (inner)'), type: 'spacing', max: 120 },

    { type: 'separator', label: t('Raggio') },
    { key: 'frame_radius', label: t('Angoli cornice browser'), type: 'border-radius' },
    { key: 'kpi_radius', label: t('Angoli card KPI'), type: 'border-radius' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
