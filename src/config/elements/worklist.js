import { t } from '@/i18n';

/**
 * Work List — indice di progetti/voci a righe (numero · titolo · categoria · anno · freccia)
 * con indentazione e shift al passaggio del mouse. Stile editoriale "hover-list".
 * Token-first: font e colori dai settings; il display eredita il font del tema.
 */
export default {
  type: 'worklist',
  name: t('Work List'),
  icon: 'dashicons-menu-alt',
  category: 'layout',

  defaults: {
    items: [
      { number: '01', title: 'Marisol',      category: 'Brand identity', year: '2026', link_url: '' },
      { number: '02', title: 'Atlas Press',   category: 'Editorial · web', year: '2025', link_url: '' },
      { number: '03', title: 'Field Museum',  category: 'Wayfinding',     year: '2025', link_url: '' },
      { number: '04', title: 'Cobalt',        category: 'Identity · product', year: '2024', link_url: '' },
    ],

    divider_color: '#d7d1c2',
    row_padding_y: 26,
    row_hover_bg:  '#e8e3d7',
    hover_indent:  24,

    number_color: '#8d8a82',
    number_size:  13,

    title_font_family: 'heading',
    title_color:  '#18181a',
    title_size:   40,
    title_weight: '500',

    show_category:  true,
    category_color: '#8d8a82',
    category_size:  12,

    show_year:  true,
    year_color: '#18181a',
    year_size:  13,

    show_arrow:  true,
    arrow_color: '#18181a',

    mono_font_family: '',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Voci') },
    { key: 'items', label: t('Righe'), type: 'content-items',
      itemLabel: t('Voce'),
      defaults: { number: '00', title: 'Nuovo progetto', category: 'Categoria', year: '2026', link_url: '' },
      itemFields: [
        { key: 'number',   label: t('Numero (es. 01)'), type: 'text' },
        { key: 'title',    label: t('Titolo'),          type: 'text' },
        { key: 'category', label: t('Categoria'),       type: 'text' },
        { key: 'year',     label: t('Anno'),            type: 'text' },
        { key: 'link_url', label: t('Link'),            type: 'link' },
      ],
    },

    { type: 'separator', label: t('Visibilità') },
    { key: 'show_category', label: t('Mostra categoria'), type: 'toggle' },
    { key: 'show_year',     label: t('Mostra anno'),      type: 'toggle' },
    { key: 'show_arrow',    label: t('Mostra freccia'),   type: 'toggle' },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Righe') },
    { key: 'divider_color', label: t('Colore linee'),         type: 'color' },
    { key: 'row_padding_y', label: t('Padding verticale (px)'), type: 'range', min: 8, max: 60, step: 1 },
    { key: 'row_hover_bg',  label: t('Sfondo riga (hover)'),  type: 'color' },
    { key: 'hover_indent',  label: t('Indentazione hover (px)'), type: 'range', min: 0, max: 48, step: 2 },

    { type: 'separator', label: t('Titolo') },
    { key: 'title_font_family', label: t('Famiglia titolo'), type: 'font-family' },
    { key: 'title_color',  label: t('Colore'), type: 'color' },
    { key: 'title_size',   label: t('Dimensione (px)'), type: 'range', min: 20, max: 72, step: 1, responsive: true },
    { key: 'title_weight', label: t('Peso'), type: 'select', options: [
      { value: '400', label: t('400 — Regular') },
      { value: '500', label: t('500 — Medium') },
      { value: '600', label: t('600 — SemiBold') },
      { value: '700', label: t('700 — Bold') },
    ]},

    { type: 'separator', label: t('Meta (numero · categoria · anno)') },
    { key: 'mono_font_family', label: t('Font meta (vuoto = mono del tema)'), type: 'font-family' },
    { key: 'number_color',   label: t('Colore numero'),    type: 'color' },
    { key: 'number_size',    label: t('Numero (px)'),      type: 'range', min: 10, max: 20, step: 1 },
    { key: 'category_color', label: t('Colore categoria'), type: 'color' },
    { key: 'category_size',  label: t('Categoria (px)'),   type: 'range', min: 10, max: 18, step: 1 },
    { key: 'year_color',     label: t('Colore anno'),      type: 'color' },
    { key: 'year_size',      label: t('Anno (px)'),        type: 'range', min: 10, max: 20, step: 1 },
    { key: 'arrow_color',    label: t('Colore freccia'),   type: 'color' },
  ],
};
