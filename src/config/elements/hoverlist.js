import { t } from '@/i18n';

/**
 * Hover List — lista a righe con pastiglia colore (swatch), nome e sotto-etichetta,
 * con indentazione al passaggio del mouse e un'anteprima flottante (peek) che segue il cursore.
 * Pensata per shade finder / liste curate. Token-first.
 */
export default {
  type: 'hoverlist',
  name: t('Hover List'),
  icon: 'dashicons-art',
  category: 'layout',

  defaults: {
    items: [
      { color: '#9a3b52', name: 'Rosewood',   sub: 'Cool · matte',  link_url: '' },
      { color: '#c77a6a', name: 'Terracotta',  sub: 'Warm · matte',  link_url: '' },
      { color: '#e79aa6', name: 'Peony',       sub: 'Cool · blush',  link_url: '' },
      { color: '#e6a17e', name: 'Apricot',     sub: 'Warm · blush',  link_url: '' },
      { color: '#7d2e3e', name: 'Merlot',      sub: 'Deep · matte',  link_url: '' },
    ],

    swatch_size: 26,
    swatch_shape: 'circle',

    name_font_family: 'heading',
    name_color: '#f6e9ec',
    name_size: 22,

    sub_color: '#9c7e8c',
    sub_size: 12,
    sub_uppercase: true,

    row_padding_y: 20,
    hover_indent: 20,
    hover_bg: '#4d2f40',
    line_color: 'rgba(246,233,236,.13)',

    peek: true,
    peek_width: 170,
    peek_ratio: '4/5',
    mono_font_family: '',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Righe') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Voce'),
      defaults: { color: '#e79aa6', name: 'Nuova voce', sub: 'Etichetta', image: '', link_url: '' },
      itemFields: [
        { key: 'color',    label: t('Colore pastiglia'), type: 'color' },
        { key: 'name',     label: t('Nome'),             type: 'text' },
        { key: 'sub',      label: t('Sotto-etichetta'),  type: 'text' },
        { key: 'image',    label: t('Immagine anteprima (peek)'), type: 'image' },
        { key: 'link_url', label: t('Link'),             type: 'link' },
      ],
    },

    { type: 'separator', label: t('Anteprima al hover') },
    { key: 'peek', label: t('Pannello "peek" immagine che segue il cursore'), type: 'toggle' },
    { key: 'peek_width', label: t('Larghezza anteprima (px)'), type: 'range', min: 100, max: 320, step: 10,
      condition: { field: 'peek', value: true } },
    { key: 'peek_ratio', label: t('Proporzioni anteprima'), type: 'select',
      condition: { field: 'peek', value: true }, options: [
        { value: '4/5', label: '4:5' },
        { value: '1/1', label: '1:1' },
        { value: '3/4', label: '3:4' },
        { value: '16/11', label: '16:11' },
      ]},
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Pastiglia') },
    { key: 'swatch_size',  label: t('Dimensione (px)'), type: 'range', min: 14, max: 44, step: 1 },
    { key: 'swatch_shape', label: t('Forma'), type: 'select', options: [
      { value: 'circle', label: t('Cerchio') },
      { value: 'square', label: t('Quadrato arrotondato') },
    ]},

    { type: 'separator', label: t('Nome') },
    { key: 'name_font_family', label: t('Famiglia'), type: 'font-family' },
    { key: 'name_color', label: t('Colore'),          type: 'color' },
    { key: 'name_size',  label: t('Dimensione (px)'), type: 'range', min: 14, max: 36, step: 1 },

    { type: 'separator', label: t('Sotto-etichetta') },
    { key: 'mono_font_family', label: t('Font (vuoto = mono del tema)'), type: 'font-family' },
    { key: 'sub_color',     label: t('Colore'),     type: 'color' },
    { key: 'sub_size',      label: t('Dimensione (px)'), type: 'range', min: 10, max: 18, step: 1 },
    { key: 'sub_uppercase', label: t('Maiuscolo'),  type: 'toggle' },

    { type: 'separator', label: t('Righe') },
    { key: 'row_padding_y', label: t('Padding verticale (px)'), type: 'range', min: 8, max: 40, step: 1 },
    { key: 'hover_indent',  label: t('Indentazione hover (px)'), type: 'range', min: 0, max: 40, step: 2 },
    { key: 'hover_bg',      label: t('Sfondo riga (hover)'),    type: 'color' },
    { key: 'line_color',    label: t('Colore linee'),          type: 'color' },
  ],
};
