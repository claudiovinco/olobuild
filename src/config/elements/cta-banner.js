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
    typography_preset: '',
    headline:        'Il tuo primo sito OLObuild è online',
    headline_accent: 'oggi pomeriggio.',
    headline_accent_italic: true,
    subtitle:        'Trial gratuita, niente carta. Tre passi, una sigaretta a testa di pausa.',
    cta_text:        'Inizia ora →',
    cta_url:         '#',
    cta_target:      '_self',

    // CTA secondaria (opzionale — vuota = nascosta)
    cta2_text:       '',
    cta2_url:        '#',
    cta2_target:     '_self',
    cta2_bg:         'transparent',
    cta2_color:      'var(--olo-color-light, #f8f9fa)',
    cta2_border:     'rgba(255,255,255,.28)',

    // Stile globale
    bg:              { type: 'solid', color: 'var(--olo-color-dark, #16263d)' },
    text_color:      'var(--olo-color-light, #f8f9fa)',
    accent_color:    'var(--olo-color-primary, #e1474f)',
    subtitle_color:  'var(--olo-color-text-faint, #94a3b8)',

    // CTA pill
    cta_bg:                  'var(--olo-color-primary, #e1474f)',
    cta_bg_hover:            '',
    cta_color:               'var(--olo-color-light, #f8f9fa)',
    // Vuoto = in hover il testo resta il suo colore, come il fondo (cta_bg_hover ''): fisso chiaro, un
    // pulsante chiaro col testo scuro diventava chiaro su chiaro (catalogo, 6 ott 2026).
    cta_color_hover:         '',
    cta_radius:              { ...R(999) },
    cta_radius_hover:        { ...R(999) },
    cta_radius_hover_duration: 300,
    cta_size:                15,
    cta_padding:             { top: 18, right: 32, bottom: 18, left: 32 },

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
    banner_padding:                 { top: 40, right: 40, bottom: 40, left: 40 },
  },

  // Appena nata: il banner d'invito all'azione di un'attività qualunque, scuro del tema, con
  // la parola accento in corsivo, sottotitolo leggibile e due pulsanti (pieno nel primario e contorno).
  partenza: {
    headline: t('Hai un progetto in mente?'),
    headline_accent: t('Parliamone.'),
    subtitle: t('Raccontaci di cosa hai bisogno: ti rispondiamo entro un giorno lavorativo con una proposta su misura, senza impegno.'),
    cta_text: t('Richiedi un preventivo'),
    cta2_text: t('Chiamaci'),
    cta2_color: 'var(--olo-color-light, #f8f9fa)',
    cta2_border: 'color-mix(in srgb, var(--olo-color-light, #f8f9fa) 35%, transparent)',
    text_color: 'var(--olo-color-light, #f8f9fa)',
    subtitle_color: 'color-mix(in srgb, var(--olo-color-light, #f8f9fa) 72%, transparent)',
    cta_color: 'var(--olo-color-primary-contrast, #ffffff)',
    cta_color_hover: 'var(--olo-color-primary-contrast, #ffffff)',
    subtitle_size: 15,
  },

  // ═══ CONTENUTO ═══════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Headline') },
    { key: 'headline',               label: t('Testo base'),          type: 'text' },
    { key: 'headline_accent',        label: t('Testo accent'),        type: 'text' },

    { type: 'separator', label: t('Sottotitolo') },
    { key: 'subtitle', label: t('Testo'), type: 'editor', mode: 'inline' },

    { type: 'separator', label: t('CTA') },
    { key: 'cta_text',   label: t('Testo CTA'), type: 'text' },
    { key: 'cta_url',    label: t('URL'),       type: 'link' },
    { key: 'cta_target', label: t('Apri in'),   type: 'select', options: [
      { value: '_self',  label: t('Stessa scheda') },
      { value: '_blank', label: t('Nuova scheda') },
    ]},

    { type: 'separator', label: t('CTA secondaria (opzionale)') },
    { key: 'cta2_text',   label: t('Testo CTA 2 (vuoto = nascosto)'), type: 'text' },
    { key: 'cta2_url',    label: t('URL'),     type: 'link' },
    { key: 'cta2_target', label: t('Apri in'), type: 'select', options: [
      { value: '_self',  label: t('Stessa scheda') },
      { value: '_blank', label: t('Nuova scheda') },
    ]},
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Banner') },
    { key: 'bg',              label: t('Sfondo'),         type: 'background', showParallax: false },
    { key: 'banner_padding',  label: t('Padding'), type: 'spacing', min: 0, max: 160 },
    withHover({ key: 'banner_radius', label: t('Raggio'), type: 'border-radius' }, { hoverKey: 'banner_radius_hover', hoverDurationKey: 'banner_radius_hover_duration' }),

    { type: 'separator', label: t('Headline stile') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'typography', label: t('Titolo'), responsiveKeys: [], keys: { size: 'headline_size', weight: 'headline_weight', family: 'headline_font_family' }, sizeMin: 18, sizeMax: 80, sizeStep: 2 },

    { key: 'text_color',   label: t('Colore base'),   type: 'color' },
    { key: 'accent_color', label: t('Colore accent'), type: 'color' },

    { key: 'headline_accent_italic', label: t('Accent in italico'),   type: 'toggle' },
    { type: 'separator', label: t('Sottotitolo stile') },
    { type: 'typography', label: t('Sottotitolo'), responsiveKeys: [], keys: { size: 'subtitle_size', color: 'subtitle_color' }, sizeMin: 11, sizeMax: 22 },

    { type: 'separator', label: t('CTA stile') },
    withHover({ key: 'cta_bg',    label: t('Sfondo'),       type: 'color' }, { hoverKey: 'cta_bg_hover', defaultDuration: 200 }),
    withHover({ key: 'cta_color', label: t('Colore testo'), type: 'color' }, { hoverKey: 'cta_color_hover', defaultDuration: 200 }),
    { type: 'typography', label: t('Pulsante'), responsiveKeys: [], keys: { size: 'cta_size' }, sizeMin: 12, sizeMax: 22 },
    { key: 'cta_padding',   label: t('Padding bottoni'), type: 'spacing', min: 0, max: 80 },
    withHover({ key: 'cta_radius', label: t('Raggio CTA'), type: 'border-radius' }, { hoverKey: 'cta_radius_hover', hoverDurationKey: 'cta_radius_hover_duration' }),

    { type: 'separator', label: t('CTA 2 stile') },
    { key: 'cta2_bg',     label: t('Sfondo CTA 2'),   type: 'color' },
    { key: 'cta2_color',  label: t('Colore testo CTA 2'), type: 'color' },
    { key: 'cta2_border', label: t('Bordo CTA 2'),    type: 'color' },

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
    { key: 'gap', label: t('Gap colonne'), type: 'range', min: 0, max: 120, step: 4 },
  ],
};
