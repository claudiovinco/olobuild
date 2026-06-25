import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

/**
 * Hero — Glow Statement : hero centrato a tutto schermo su fondo scuro con GLOW radiale
 * sfocato dietro un titolo editoriale gigante multi-riga. Ogni riga puo' essere normale,
 * accento-colore, OUTLINE (text-stroke, fill trasparente) o accento a gradiente. Eyebrow
 * con dot tra le parole, sub, fino a 2 CTA, scroll hint. Glow color/size/blur/position
 * configurabili. Estratta dai blueprint OLOthemes "Vela" (default) e "Prisma".
 * Render Vue == PHP (GlowHeroTile.vue). Runtime: nessuno (pure CSS).
 */
export default {
  type: 'glowhero',
  name: t('Hero — Glow Statement'),
  icon: 'dashicons-superhero',
  category: 'marketing',

  defaults: {
    eyebrow: 'Independent studio | Est. 2015 | Milan / everywhere',
    lines: [
      { text: 'Design with', mode: '' },
      { text: 'a point', mode: 'accent' },
      { text: 'of view.', mode: 'outline' },
    ],
    uppercase: true,
    eyebrow_dots: true,
    subhead: "We build brands, identities and digital products for people who'd rather stand out than blend in.",
    cta1_text: '',
    cta1_url: '#',
    cta2_text: '',
    cta2_url: '#',
    scroll_text: 'Scroll to see the work',
    show_scroll: true,
    bg_color: 'var(--olo-color-dark, #16263d)',
    accent: '',
    accent2: '',
    accent_on: 'var(--olo-color-dark, #16263d)',
    text_color: 'var(--olo-color-light, #f8f9fa)',
    sub_color: 'var(--olo-color-text-faint, #94a3b8)',
    eyebrow_color: 'var(--olo-color-text-faint, #94a3b8)',
    glow_color: 'rgba(244,162,59,0.18)',
    glow_w: 760,
    glow_h: 560,
    glow_blur: 100,
    glow_x: 50,
    glow_y: 20,
    h_size_min: 54,
    h_size_vw: 12,
    h_size_max: 180,
    h_line_height: 0.86,
    stroke_width: 2,
    align: 'left',
    max_width: 1240,
    min_height: 100,
    bottom_split: true,

    // Spaziatura — override GATED del padding responsive del contenitore (clamp). No-op coi default.
    pad_custom: false,
    content_padding: { top: 96, right: 0, bottom: 96, left: 0 },
    // Forma — raggio dei pulsanti CTA (pill). No-op coi default (999 = pill attuale).
    btn_radius: { tl: 999, tr: 999, br: 999, bl: 999 },

    // KIT standard OLObuild — sfondo completo / ombra / bordo (no-op coi default)
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'eyebrow', label: t('Eyebrow (parole separate da " | ")'), type: 'text',
      description: t('Ogni parola ha un dot accento davanti.') },
    { key: 'eyebrow_dots', label: t('Dot accento prima di ogni parola eyebrow'), type: 'toggle',
      description: t('Disattiva per un eyebrow a blocco singolo (stile Prisma).') },

    { type: 'separator', label: t('Titolo (righe)') },
    { key: 'lines', label: t('Righe titolo'), type: 'content-items',
      itemLabel: t('Riga'),
      defaults: { text: 'Riga titolo', mode: '' },
      itemFields: [
        { key: 'text', label: t('Testo'), type: 'text' },
        { key: 'mode', label: t('Stile'), type: 'select', options: [
          { value: '', label: t('Normale') },
          { value: 'accent', label: t('Accento (colore)') },
          { value: 'outline', label: t('Outline (contorno)') },
          { value: 'gradient', label: t('Gradiente accento') },
        ]},
      ],
    },
    { key: 'uppercase', label: t('Maiuscolo'), type: 'toggle' },
    { key: 'subhead', label: t('Sottotitolo'), type: 'textarea' },

    { type: 'separator', label: t('CTA') },
    { key: 'cta1_text', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'cta1_url', label: t('CTA 1 — link'), type: 'link' },
    { key: 'cta2_text', label: t('CTA 2 — testo'), type: 'text' },
    { key: 'cta2_url', label: t('CTA 2 — link'), type: 'link' },

    { type: 'separator', label: t('Scroll hint') },
    { key: 'show_scroll', label: t('Mostra scroll hint'), type: 'toggle' },
    { key: 'scroll_text', label: t('Testo scroll hint'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Layout') },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},
    { key: 'bottom_split', label: t('Riga inferiore: sub a sx · CTA a dx'), type: 'toggle' },
    { key: 'max_width', label: t('Larghezza max (px)'), type: 'range', min: 600, max: 1600, step: 20 },
    { key: 'min_height', label: t('Altezza minima (vh)'), type: 'range', min: 40, max: 100, step: 1 },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'pad_custom', label: t('Padding personalizzato'), type: 'toggle',
      description: t('Disattivo = padding fluido responsive (clamp). Attivo = usa i valori sotto.') },
    { key: 'content_padding', label: t('Padding contenuto (px)'), type: 'spacing',
      condition: { field: 'pad_custom', op: 'eq', value: true } },

    { type: 'separator', label: t('Forma') },
    { key: 'btn_radius', label: t('Raggio pulsanti CTA (px)'), type: 'border-radius' },

    { type: 'separator', label: t('Titolo') },
    { key: 'h_size_min', label: t('Dimensione min (px)'), type: 'range', min: 20, max: 90, step: 1 },
    { key: 'h_size_vw', label: t('Dimensione fluida (vw)'), type: 'range', min: 4, max: 16, step: 0.5 },
    { key: 'h_size_max', label: t('Dimensione max (px)'), type: 'range', min: 80, max: 240, step: 2 },
    { key: 'h_line_height', label: t('Interlinea'), type: 'range', min: 0.7, max: 1.2, step: 0.01 },
    { key: 'stroke_width', label: t('Spessore contorno outline (px)'), type: 'range', min: 0, max: 5, step: 0.5 },

    { type: 'separator', label: t('Glow radiale') },
    { key: 'glow_color', label: t('Colore glow'), type: 'color' },
    { key: 'glow_w', label: t('Larghezza glow (px)'), type: 'range', min: 200, max: 1400, step: 20 },
    { key: 'glow_h', label: t('Altezza glow (px)'), type: 'range', min: 200, max: 1000, step: 20 },
    { key: 'glow_blur', label: t('Sfocatura glow (px)'), type: 'range', min: 0, max: 200, step: 5 },
    { key: 'glow_x', label: t('Posizione X glow (%)'), type: 'range', min: 0, max: 100, step: 1 },
    { key: 'glow_y', label: t('Posizione Y glow (%)'), type: 'range', min: -50, max: 100, step: 1 },

    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Sfondo'), type: 'color' },
    { key: 'accent', label: t('Accento (titolo + dot + CTA)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'accent2', label: t('Accento 2 (fine gradiente)'), type: 'color',
      description: t('Vuoto = secondario del tema.') },
    { key: 'accent_on', label: t('Testo su accento (CTA)'), type: 'color' },
    { key: 'text_color', label: t('Colore titolo / contorno'), type: 'color' },
    { key: 'sub_color', label: t('Colore sottotitolo / scroll'), type: 'color' },
    { key: 'eyebrow_color', label: t('Colore eyebrow'), type: 'color' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,
    ...borderFields(),
  ],
};
