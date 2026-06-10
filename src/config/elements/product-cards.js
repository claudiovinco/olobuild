import { t } from '@/i18n';
import { withHover } from './_shared';

const R = (n) => ({ tl: n, tr: n, br: n, bl: n, linked: true });

/**
 * Product Cards — griglia di card prodotto stile editoriale.
 * Pattern: metà alta gradient + lettera-monogramma grande + label outline,
 * metà bassa con brand colored + badge pill opzionale + titolo serif con
 * suffisso italic + descrizione + CTA testuale.
 * Allineato agli standard Olobuild: border-radius 4 angoli + hover,
 * background creativo unificato, editor rich per descrizione.
 */
export default {
  type: 'product-cards',
  name: t('Product Cards'),
  icon: 'dashicons-portfolio',
  category: 'layout',

  defaults: {
    columns:  5,
    gap:      24,

    items: [
      { letter: 'C', letter_color: '#b3261e', top_bg: { type: 'gradient', gradient_from: '#fdf2f2', gradient_to: '#fbe1e1', gradient_angle: 180 }, screenshot_label: 'SCREENSHOT · EDITOR LIVE', brand_label: 'OLOBUILD',  brand_color: '#b3261e', show_badge: true,  badge_text: 'GRATIS', badge_bg: '#0f172a', badge_color: '#ffffff', title: 'Co',     title_accent: 'struisci',  title_accent_italic: true,  description: 'Page builder olonico. 187 tile, motion design, border-radius animato all\'hover. Alla pari dei top builder commerciali.', cta_text: 'SCOPRI OLOBUILD',  cta_url: '#' },
      { letter: 'T', letter_color: '#c2185b', top_bg: { type: 'gradient', gradient_from: '#fce4ec', gradient_to: '#f8bbd0', gradient_angle: 180 }, screenshot_label: 'SCREENSHOT · MULTILINGUA', brand_label: 'OLOLANG',   brand_color: '#c2185b', show_badge: false, badge_text: '',       badge_bg: '#0f172a', badge_color: '#ffffff', title: 'Tra',    title_accent: 'duci',      title_accent_italic: true,  description: 'Multilingua nativo. Traduzioni IA + editing umano. SEO per ogni lingua. 28 idiomi inclusi.',                                cta_text: 'SCOPRI OLOLANG',   cta_url: '#' },
      { letter: 'P', letter_color: '#1976d2', top_bg: { type: 'gradient', gradient_from: '#e3f2fd', gradient_to: '#bbdefb', gradient_angle: 180 }, screenshot_label: 'SCREENSHOT · CALENDARIO', brand_label: 'OLOBOOKING', brand_color: '#1976d2', show_badge: false, badge_text: '',       badge_bg: '#0f172a', badge_color: '#ffffff', title: 'Pre',    title_accent: 'nota',      title_accent_italic: true,  description: 'Motore di prenotazione multi-verticale. Strutture, appuntamenti, eventi, immobili, noleggi, ristoranti.',                   cta_text: 'SCOPRI OLOBOOKING', cta_url: '#' },
      { letter: 'M', letter_color: '#e65100', top_bg: { type: 'gradient', gradient_from: '#fff3e0', gradient_to: '#ffe0b2', gradient_angle: 180 }, screenshot_label: 'SCREENSHOT · TOUR 360°', brand_label: 'OLOTOUR',    brand_color: '#e65100', show_badge: false, badge_text: '',       badge_bg: '#0f172a', badge_color: '#ffffff', title: 'Mo',     title_accent: 'stra',      title_accent_italic: true,  description: 'Percorsi guidati a 360°. Foto e video sferici con hot-spot interattivi, multi-stanza, supporto VR.',                       cta_text: 'SCOPRI OLOTOUR',   cta_url: '#' },
      { letter: 'I', letter_color: '#2e7d32', top_bg: { type: 'gradient', gradient_from: '#e8f5e9', gradient_to: '#c8e6c9', gradient_angle: 180 }, screenshot_label: 'SCREENSHOT · AREA CORSI', brand_label: 'OLOTUTOR',   brand_color: '#2e7d32', show_badge: false, badge_text: '',       badge_bg: '#0f172a', badge_color: '#ffffff', title: 'In',     title_accent: 'segna',     title_accent_italic: true,  description: 'E-learning completo. Corsi, lezioni, esercizi, certificazioni, area allievi. WordPress-native.',                          cta_text: 'SCOPRI OLOTUTOR',  cta_url: '#' },
    ],

    // Card style
    card_bg:                       { type: 'solid', color: '#ffffff' },
    card_color:                    '#374151',
    card_radius:                   { ...R(24) },
    card_radius_hover:             { ...R(24) },
    card_radius_hover_duration:    400,
    card_shadow:                   'sm',
    card_padding:                  28,

    // Top section (lettera + screenshot label)
    top_aspect_ratio:        '3/4',
    top_padding:             24,
    letter_font_family:      'serif',
    letter_size:             140,
    letter_italic:           true,
    letter_align:            'center',
    logo_height:             52,
    show_screenshot_label:   true,
    screenshot_label_color:  '#9ca3af',

    // Bottom section
    brand_size:              13,
    brand_letter_spacing:    0.08,
    title_font_family:       'serif',
    title_size:              30,
    title_weight:            '500',
    description_size:        15,
    cta_size:                12,
    cta_arrow:               true,

    // Hover
    card_hover_effect: 'lift',
  },

  // ═══ CONTENUTO ═══════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Product cards') },
    { key: 'items', label: t('Cards'), type: 'content-items',
      itemLabel: t('Card'),
      defaults: { letter: 'X', letter_color: '#0f172a', logo_image: '', top_bg: { type: 'solid', color: '#f5f5f5' }, screenshot_label: 'SCREENSHOT', brand_label: 'BRAND', brand_color: '#0f172a', show_badge: false, badge_text: '', badge_bg: '#0f172a', badge_color: '#ffffff', title: 'Titolo', title_accent: '', title_accent_italic: false, description: 'Descrizione…', cta_text: 'SCOPRI', cta_url: '#' },
      itemFields: [
        { key: 'letter',              label: t('Lettera monogramma'), type: 'text' },
        { key: 'letter_color',        label: t('Colore lettera'),     type: 'color' },
        { key: 'logo_image',          label: t('Logo prodotto (sostituisce la lettera)'), type: 'image' },
        { key: 'top_bg',              label: t('Sfondo metà alta'),   type: 'background', showParallax: false },
        { key: 'screenshot_label',    label: t('Label outline (metà alta)'), type: 'text' },
        { key: 'brand_label',         label: t('Brand label'),        type: 'text' },
        { key: 'brand_color',         label: t('Colore brand'),       type: 'color' },
        { key: 'show_badge',          label: t('Mostra badge pill'),  type: 'toggle' },
        { key: 'badge_text',          label: t('Badge testo'),        type: 'text' },
        { key: 'badge_bg',            label: t('Badge sfondo'),       type: 'color' },
        { key: 'badge_color',         label: t('Badge testo colore'), type: 'color' },
        { key: 'title',               label: t('Titolo (base)'),      type: 'text' },
        { key: 'title_accent',        label: t('Suffisso titolo'),    type: 'text' },
        { key: 'title_accent_italic', label: t('Suffisso italico'),   type: 'toggle' },
        { key: 'description',         label: t('Descrizione'),        type: 'editor', mode: 'inline' },
        { key: 'cta_text',            label: t('CTA testo'),          type: 'text' },
        { key: 'cta_url',             label: t('CTA URL'),            type: 'link' },
      ],
    },

    { type: 'separator', label: t('Visibilità') },
    { key: 'show_screenshot_label', label: t('Mostra label outline (metà alta)'), type: 'toggle' },
    { key: 'cta_arrow',             label: t('Mostra freccia → al CTA'),         type: 'toggle' },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Griglia') },
    { key: 'columns', label: t('Numero colonne'), type: 'range', min: 1, max: 6, step: 1 },
    { key: 'gap',     label: t('Gap tra card (px)'), type: 'range', min: 0, max: 60, step: 2 },

    { type: 'separator', label: t('Card') },
    { key: 'card_bg',      label: t('Sfondo card'),     type: 'background', showParallax: false },
    { key: 'card_color',   label: t('Colore testo'),    type: 'color' },
    { key: 'card_padding', label: t('Padding card (px)'), type: 'range', min: 12, max: 60, step: 2 },
    withHover({ key: 'card_radius', label: t('Border radius card'), type: 'border-radius' }, { hoverKey: 'card_radius_hover', hoverDurationKey: 'card_radius_hover_duration' }),
    { key: 'card_shadow', label: t('Ombra card'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm',   label: t('Leggera') },
      { value: 'md',   label: t('Media') },
      { value: 'lg',   label: t('Forte') },
      { value: 'xl',   label: t('Molto forte') },
    ]},

    { type: 'separator', label: t('Metà alta (lettera)') },
    { key: 'top_aspect_ratio', label: t('Aspect ratio'), type: 'select', options: [
      { value: '1/1', label: t('1 / 1 (quadrato)') },
      { value: '4/5', label: '4 / 5' },
      { value: '3/4', label: '3 / 4' },
      { value: '2/3', label: '2 / 3' },
      { value: '3/2', label: '3 / 2' },
    ]},
    { key: 'top_padding',       label: t('Padding interno (px)'), type: 'range', min: 0, max: 60, step: 2 },
    { key: 'letter_font_family', label: t('Famiglia lettera'), type: 'font-family' },
    { key: 'letter_size',   label: t('Dimensione lettera (px)'), type: 'range', min: 40, max: 280, step: 4 },
    { key: 'logo_height',   label: t('Altezza logo (px)'),       type: 'range', min: 16, max: 160, step: 2 },
    { key: 'letter_italic', label: t('Lettera in italico'),      type: 'toggle' },
    { key: 'letter_align', label: t('Allineamento lettera'), type: 'select', options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centrato') },
      { value: 'right',  label: t('Destra') },
    ]},
    { key: 'screenshot_label_color', label: t('Colore label outline'), type: 'color' },

    { type: 'separator', label: t('Metà bassa') },
    { key: 'brand_size',           label: t('Brand label dimensione (px)'), type: 'range', min: 10, max: 22, step: 1 },
    { key: 'brand_letter_spacing', label: t('Brand letter-spacing (em)'),   type: 'range', min: 0, max: 0.3, step: 0.01 },
    { key: 'title_font_family', label: t('Titolo famiglia'), type: 'font-family' },
    { key: 'title_size',   label: t('Titolo dimensione (px)'), type: 'range', min: 16, max: 80, step: 2 },
    { key: 'title_weight', label: t('Titolo peso'), type: 'select', options: [
      { value: '300', label: t('300 — Light') },
      { value: '400', label: t('400 — Regular') },
      { value: '500', label: t('500 — Medium') },
      { value: '600', label: t('600 — SemiBold') },
      { value: '700', label: t('700 — Bold') },
    ]},
    { key: 'description_size', label: t('Descrizione dimensione (px)'), type: 'range', min: 11, max: 22, step: 1 },
    { key: 'cta_size',         label: t('CTA dimensione (px)'),         type: 'range', min: 9,  max: 16, step: 1 },

    { type: 'separator', label: t('Hover card') },
    { key: 'card_hover_effect', label: t('Effetto hover'), type: 'select', options: [
      { value: 'none',  label: t('Nessuno') },
      { value: 'lift',  label: t('Sollevamento') },
      { value: 'scale', label: t('Scala') },
      { value: 'tilt',  label: t('Inclinazione 3D') },
    ]},
  ],
};
