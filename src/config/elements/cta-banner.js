import { t } from '@/i18n';
import { withHover } from './_shared';

const R = (n) => ({ tl: n, tr: n, br: n, bl: n, linked: true });

/**
 * CTA Banner — banner CTA editoriale a 3 colonne (headline | sottotitolo | bottone).
 * Standard Olobuild: i18n, separazione contenuto/stile, border-radius standard
 * + hover, sfondo creativo unificato, color picker con globe.
 */
export default {
  type: 'cta-banner',
  name: t('CTA Banner'),
  icon: 'dashicons-megaphone',
  category: 'layout',

  defaults: {
    headline:        'Il tuo primo sito OLObuild è online',
    headline_accent: 'oggi pomeriggio.',
    headline_accent_italic: true,
    subtitle:        'Trial gratuita, niente carta. Tre passi, una sigaretta a testa di pausa.',
    cta_text:        'Inizia ora →',
    cta_url:         '#',
    cta_target:      '_self',

    // Stile globale
    bg:              { type: 'solid', color: '#0f172a' },
    text_color:      '#ffffff',
    accent_color:    'var(--olo-color-primary, #e1474f)',
    subtitle_color:  '#9ca3af',

    // CTA pill
    cta_bg:                  'var(--olo-color-primary, #e1474f)',
    cta_bg_hover:            '',
    cta_color:               '#ffffff',
    cta_color_hover:         '#ffffff',
    cta_radius:              { ...R(999) },
    cta_radius_hover:        { ...R(999) },
    cta_radius_hover_duration: 300,
    cta_size:                15,
    cta_padding_y:           18,
    cta_padding_x:           32,

    // Tipografia
    headline_font_family: 'serif',
    headline_size:        36,
    headline_weight:      '400',
    subtitle_size:        14,

    // Layout
    layout:        'split-3',
    ratio:         '1.4fr 1fr auto',
    gap:           40,
    vertical_align: 'center',
    banner_radius:                  { ...R(20) },
    banner_radius_hover:            { ...R(20) },
    banner_radius_hover_duration:   400,
    banner_padding:                 40,
  },

  // ═══ CONTENUTO ═══════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Headline') },
    { key: 'headline',               label: t('Testo base'),          type: 'text' },
    { key: 'headline_accent',        label: t('Testo accent'),        type: 'text' },
    { key: 'headline_accent_italic', label: t('Accent in italico'),   type: 'toggle' },

    { type: 'separator', label: t('Sottotitolo') },
    { key: 'subtitle', label: t('Testo'), type: 'editor', mode: 'inline' },

    { type: 'separator', label: t('CTA') },
    { key: 'cta_text',   label: t('Testo CTA'), type: 'text' },
    { key: 'cta_url',    label: t('URL'),       type: 'link' },
    { key: 'cta_target', label: t('Apri in'),   type: 'select', options: [
      { value: '_self',  label: t('Stessa scheda') },
      { value: '_blank', label: t('Nuova scheda') },
    ]},
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Banner') },
    { key: 'bg',              label: t('Sfondo'),         type: 'background', showParallax: false },
    { key: 'banner_padding',  label: t('Padding (px)'),   type: 'range', min: 16, max: 120, step: 4 },
    withHover({ key: 'banner_radius', label: t('Border radius'), type: 'border-radius' }, { hoverKey: 'banner_radius_hover', hoverDurationKey: 'banner_radius_hover_duration' }),

    { type: 'separator', label: t('Headline stile') },
    { key: 'headline_font_family', label: t('Famiglia'), type: 'select', options: [
      { value: 'serif',      label: t('Serif (editoriale)') },
      { value: 'sans-serif', label: t('Sans-serif (moderno)') },
      { value: 'mono',       label: t('Monospace') },
    ]},
    { key: 'headline_size',   label: t('Dimensione (px)'), type: 'range', min: 18, max: 80, step: 2 },
    { key: 'headline_weight', label: t('Peso'), type: 'select', options: [
      { value: '300', label: t('300 — Light') },
      { value: '400', label: t('400 — Regular') },
      { value: '500', label: t('500 — Medium') },
      { value: '600', label: t('600 — SemiBold') },
      { value: '700', label: t('700 — Bold') },
    ]},
    { key: 'text_color',   label: t('Colore base'),   type: 'color' },
    { key: 'accent_color', label: t('Colore accent'), type: 'color' },

    { type: 'separator', label: t('Sottotitolo stile') },
    { key: 'subtitle_size',  label: t('Dimensione (px)'), type: 'range', min: 11, max: 22, step: 1 },
    { key: 'subtitle_color', label: t('Colore'),          type: 'color' },

    { type: 'separator', label: t('CTA stile') },
    withHover({ key: 'cta_bg',    label: t('Sfondo'),       type: 'color' }, { hoverKey: 'cta_bg_hover' }),
    withHover({ key: 'cta_color', label: t('Colore testo'), type: 'color' }, { hoverKey: 'cta_color_hover' }),
    { key: 'cta_size',      label: t('Dimensione testo (px)'), type: 'range', min: 12, max: 22, step: 1 },
    { key: 'cta_padding_y', label: t('Padding verticale (px)'), type: 'range', min: 10, max: 30, step: 1 },
    { key: 'cta_padding_x', label: t('Padding orizzontale (px)'), type: 'range', min: 16, max: 60, step: 2 },
    withHover({ key: 'cta_radius', label: t('Border radius CTA'), type: 'border-radius' }, { hoverKey: 'cta_radius_hover', hoverDurationKey: 'cta_radius_hover_duration' }),

    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Modalità'), type: 'select', options: [
      { value: 'split-3', label: t('3 colonne (headline | sottotitolo | CTA)') },
      { value: 'split-2', label: t('2 colonne (headline+sottotitolo | CTA)') },
      { value: 'stack',   label: t('Stack centrato (verticale)') },
    ]},
    { key: 'ratio', label: t('Proporzioni colonne (split-3)'), type: 'select', options: [
      { value: '1.4fr 1fr auto', label: '1.4 / 1 / auto' },
      { value: '1fr 1fr auto',   label: '1 / 1 / auto' },
      { value: '2fr 1fr auto',   label: '2 / 1 / auto' },
      { value: '1fr auto',       label: '1 / auto (2 colonne)' },
    ]},
    { key: 'vertical_align', label: t('Allineamento verticale'), type: 'select', options: [
      { value: 'start',  label: t('In alto') },
      { value: 'center', label: t('Centro') },
      { value: 'end',    label: t('In basso') },
    ]},
    { key: 'gap', label: t('Gap colonne (px)'), type: 'range', min: 0, max: 120, step: 4 },
  ],
};
