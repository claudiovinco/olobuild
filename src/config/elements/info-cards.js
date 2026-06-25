import { t } from '@/i18n';
import { withHover } from './_shared';

const R = (n) => ({ tl: n, tr: n, br: n, bl: n, linked: true });
const R24 = R(24), R18 = R(18);

/**
 * Info Cards — griglia parametrica di card riusabile.
 * CONTENUTO: solo testi, dati item-level (incluso media per item).
 * STILE: colori, dimensioni, sfondi globali, hover, layout griglia.
 */
export default {
  type: 'info-cards',
  name: t('Info Cards'),
  icon: 'dashicons-grid-view',
  category: 'layout',

  defaults: {
    container_bg:                       { type: 'solid', color: 'var(--olo-color-dark, #16263d)' },
    container_radius:                   { ...R24 },
    container_radius_hover:             { ...R24 },
    container_radius_hover_duration:    400,
    container_padding:                  12,
    container_gap:                      0,

    columns:   3,
    items_gap: 0,

    items: [
      { counter: '01', counter_label: 'Carta',         title: 'Zero',    title_accent: '',   title_accent_italic: true,  description: 'Niente <strong>carta di credito</strong> per scaricare e provare. Niente trial scaduto, niente sblocchi nascosti.', icon: '', footer_dot_color: 'var(--olo-color-accent, #f4a23b)', footer_text: '', link_url: '', media_image: '', media_label: 'SCREENSHOT · 01' },
      { counter: '02', counter_label: 'Registrazione', title: 'Niente',  title_accent: '',   title_accent_italic: true,  description: 'Nessuna <strong>registrazione obbligatoria</strong>. Scarichi, installi, lavori. L\'account lo crei solo se vuoi.', icon: '', footer_dot_color: 'var(--olo-color-accent, #f4a23b)', footer_text: '', link_url: '', media_image: '', media_label: 'SCREENSHOT · 02' },
      { counter: '03', counter_label: 'Pro',           title: '30',      title_accent: 'gg', title_accent_italic: false, description: '<strong>Soddisfatti o rimborsati</strong> su OLObuild Pro. 30 giorni pieni, nessuna domanda, zero ostacoli.', icon: '', footer_dot_color: 'var(--olo-color-accent, #f4a23b)', footer_text: '', link_url: '', media_image: '', media_label: 'SCREENSHOT · 03' },
    ],

    card_bg:                       { type: 'solid', color: 'var(--olo-color-dark, #16263d)' },
    card_color:                    'var(--olo-color-surface-alt, #f6f7f9)',
    card_accent_color:             'var(--olo-color-primary, #e1474f)',
    card_radius:                   { ...R18 },
    card_radius_hover:             { ...R18 },
    card_radius_hover_duration:    400,
    card_padding:                  40,
    card_border:                   '',

    show_icon:           false,
    show_counter:        true,
    show_counter_label:  true,
    show_arrow:          true,
    show_footer:         false,
    show_link_text:      false,
    show_divider:        false,
    show_media:          false,
    media_aspect_ratio:  '4/3',
    object_position:               'center center',
    media_radius:                  { ...R18 },
    media_radius_hover:            { ...R18 },
    media_radius_hover_duration:   400,
    media_position:                'top',

    title_font_family: 'serif',
    title_size:        72,
    title_weight:      '500',
    title_italic:      true,
    counter_size:      11,
    description_size:  15,
    footer_size:       10,

    card_hover_effect: 'none',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Card items') },
    { key: 'items', label: t('Cards'), type: 'content-items',
      itemLabel: t('Card'),
      defaults: { counter: '00', counter_label: 'Label', title: 'Titolo', title_accent: '', title_accent_italic: false, description: 'Descrizione…', icon: '', footer_dot_color: '#10b981', footer_text: '', link_url: '', link_text: '', media_image: '', media_label: 'SCREENSHOT' },
      itemFields: [
        { type: 'separator', label: t('Media') },
        { key: 'media_image',         label: t('Immagine media'),          type: 'image' },
        { key: 'media_label',         label: t('Label placeholder media'), type: 'text' },
        { key: 'icon',                label: t('Icona'),                   type: 'icon' },
        { type: 'separator', label: t('Counter') },
        { key: 'counter',             label: t('Counter (es. 01)'),        type: 'text' },
        { key: 'counter_label',       label: t('Counter label'),           type: 'text' },
        { type: 'separator', label: t('Testi') },
        { key: 'title',               label: t('Titolo'),                  type: 'text' },
        { key: 'title_accent',        label: t('Suffisso titolo'),         type: 'text' },
        { key: 'title_accent_italic', label: t('Suffisso italico'),        type: 'toggle' },
        { key: 'description',         label: t('Descrizione'),             type: 'editor', mode: 'block' },
        { type: 'separator', label: t('Footer & Link') },
        { key: 'footer_text',         label: t('Footer testo'),            type: 'text' },
        { key: 'footer_dot_color',    label: t('Footer pallino'),          type: 'color' },
        { key: 'link_url',            label: t('Link (opzionale)'),        type: 'link' },
        { key: 'link_text',           label: t('Testo CTA (es. Learn more)'), type: 'text' },
      ],
    },

    { type: 'separator', label: t('Visibilità elementi') },
    { key: 'show_media',         label: t('Mostra media in alto'),                type: 'toggle' },
    { key: 'show_icon',          label: t('Mostra icona'),                        type: 'toggle' },
    { key: 'show_counter',       label: t('Mostra counter'),                      type: 'toggle' },
    { key: 'show_counter_label', label: t('Mostra counter label'),                type: 'toggle' },
    { key: 'show_arrow',         label: t('Mostra freccia in alto a destra'),    type: 'toggle' },
    { key: 'show_footer',        label: t('Mostra footer (pallino + tag)'),      type: 'toggle' },
    { key: 'show_link_text',     label: t('Mostra CTA testuale (Learn more →)'), type: 'toggle' },
    { key: 'show_divider',       label: t('Mostra separatore sotto descrizione'), type: 'toggle' },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Layout griglia') },
    { key: 'columns',   label: t('Numero colonne'),    type: 'range', min: 1, max: 6, step: 1, responsive: true },
    { key: 'items_gap', label: t('Gap tra card (px)'), type: 'range', min: 0, max: 60, step: 2, responsive: true },

    { type: 'separator', label: t('Sfondo container') },
    { key: 'container_bg',      label: t('Sfondo'),               type: 'background', showParallax: false },
    { key: 'container_padding', label: t('Padding interno (px)'), type: 'range', min: 0, max: 80, step: 1 },
    { key: 'container_gap',     label: t('Gap container-card (px)'), type: 'range', min: 0, max: 40, step: 1 },
    withHover({ key: 'container_radius', label: t('Border radius container'), type: 'border-radius' }, { hoverKey: 'container_radius_hover', hoverDurationKey: 'container_radius_hover_duration' }),

    { type: 'separator', label: t('Card stile') },
    { key: 'card_bg',           label: t('Sfondo card'),            type: 'background', showParallax: false },
    { key: 'card_color',        label: t('Colore testo'),           type: 'color' },
    { key: 'card_accent_color', label: t('Colore accent (titolo)'), type: 'color' },
    { key: 'card_padding',      label: t('Padding card (px)'),      type: 'range', min: 10, max: 80, step: 2 },
    { key: 'card_border',       label: t('Colore bordo (vuoto = nessuno)'), type: 'color' },
    withHover({ key: 'card_radius', label: t('Border radius card'), type: 'border-radius' }, { hoverKey: 'card_radius_hover', hoverDurationKey: 'card_radius_hover_duration' }),

    { type: 'separator', label: t('Media (immagine card)') },
    { key: 'media_aspect_ratio', label: t('Aspect ratio'), type: 'select', options: [
      { value: '16/9', label: '16 / 9' },
      { value: '4/3',  label: '4 / 3' },
      { value: '3/2',  label: '3 / 2' },
      { value: '1/1',  label: t('1 / 1 (quadrato)') },
      { value: '21/9', label: t('21 / 9 (ultra-wide)') },
    ], condition: { field: 'show_media', op: '=', value: true } },
    { key: 'object_position', label: t('Posizione contenuto'), type: 'object-position', reveal: true, contextKeys: { ratio: 'media_aspect_ratio' }, condition: { field: 'show_media', op: '=', value: true } },
    withHover({ key: 'media_radius', label: t('Border radius media'), type: 'border-radius' }, { hoverKey: 'media_radius_hover', hoverDurationKey: 'media_radius_hover_duration' }),

    { type: 'separator', label: t('Tipografia titolo') },
    { key: 'title_font_family', label: t('Famiglia'), type: 'font-family' },
    { key: 'title_size',   label: t('Dimensione (px)'), type: 'range', min: 18, max: 160, step: 2 },
    { key: 'title_weight', label: t('Peso'), type: 'select', options: [
      { value: '300', label: t('300 — Light') },
      { value: '400', label: t('400 — Regular') },
      { value: '500', label: t('500 — Medium') },
      { value: '600', label: t('600 — SemiBold') },
      { value: '700', label: t('700 — Bold') },
      { value: '800', label: t('800 — ExtraBold') },
      { value: '900', label: t('900 — Black') },
    ]},
    { key: 'title_italic', label: t('Titolo in italico'), type: 'toggle' },

    { type: 'separator', label: t('Dimensioni secondarie') },
    { key: 'counter_size',     label: t('Counter (px)'),     type: 'range', min: 9, max: 22, step: 1 },
    { key: 'description_size', label: t('Descrizione (px)'), type: 'range', min: 11, max: 22, step: 1 },
    { key: 'footer_size',      label: t('Footer (px)'),      type: 'range', min: 9, max: 16, step: 1 },

    { type: 'separator', label: t('Hover card') },
    { key: 'card_hover_effect', label: t('Effetto hover'), type: 'select', options: [
      { value: 'none',  label: t('Nessuno') },
      { value: 'lift',  label: t('Sollevamento') },
      { value: 'scale', label: t('Scala') },
      { value: 'glow',  label: t('Glow bordo accent') },
      { value: 'tilt',  label: t('Inclinazione 3D') },
    ]},
  ],
};
