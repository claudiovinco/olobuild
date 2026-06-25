import { t } from '@/i18n';
import { withHover } from './_shared.js';

/**
 * Hover List — lista a righe con pastiglia colore (swatch), nome e sotto-etichetta,
 * con indentazione al passaggio del mouse e un'anteprima flottante (peek) che segue il cursore.
 * Pensata per shade finder / liste curate. Token-first.
 *
 * Estensioni "sala di regia" (blueprint Clod Evoluzione):
 *   lead_mode 'number'  → numero progressivo mono (01, 02…) al posto della pastiglia,
 *                         riga a griglia 64px 1fr auto con descrizione a destra.
 *   peek_mode 'monitor' → il box che segue il cursore diventa un monitor di regia
 *                         (viewfinder + ● STILL + barra label) invece dell'immagine.
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

    lead_mode: 'swatch',
    swatch_size: 26,
    swatch_shape: 'circle',
    number_color: '',
    number_hover_color: '',

    name_font_family: 'heading',
    name_color: 'var(--olo-color-light, #f8f9fa)',
    name_size: 22,
    name_uppercase: false,

    sub_color: 'var(--olo-color-text-soft, #6b7280)',
    sub_size: 12,
    sub_uppercase: true,

    desc_color: '',
    desc_size: 14,

    row_padding_y: 20,
    hover_indent: 20,
    hover_bg: 'var(--olo-color-dark, #16263d)',
    line_color: 'rgba(246,233,236,.13)',

    peek: true,
    peek_mode: 'image',
    peek_width: 170,
    peek_ratio: '4/5',
    mono_font_family: '',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Righe') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Voce'),
      newItemDefaults: { color: '#e79aa6', name: 'Nuova voce', sub: 'Etichetta', desc: '', number: '', image: '', link_url: '', row_bg: { type: 'none' } },
      itemFields: [
        { key: 'color',    label: t('Colore pastiglia'), type: 'color' },
        { key: 'name',     label: t('Nome'),             type: 'text' },
        { key: 'sub',      label: t('Sotto-etichetta'),  type: 'text' },
        { key: 'desc',     label: t('Descrizione (colonna destra, layout numerato)'), type: 'text' },
        { key: 'number',   label: t('Numero (vuoto = automatico 01, 02…)'), type: 'text' },
        { key: 'image',    label: t('Immagine anteprima (peek)'), type: 'image' },
        { key: 'link_url', label: t('Link'),             type: 'link' },
        { key: 'row_bg',   label: t('Sfondo voce'),      type: 'background',
          description: t('Sfondo della riga: colore, gradiente o immagine. Vuoto = trasparente con evidenziazione standard al passaggio del mouse; una voce con sfondo proprio lo mantiene anche in hover.') },
      ],
    },

    { type: 'separator', label: t('Anteprima al hover') },
    { key: 'peek', label: t('Pannello "peek" che segue il cursore'), type: 'toggle' },
    { key: 'peek_mode', label: t('Contenuto anteprima'), type: 'select',
      condition: { field: 'peek', value: true }, options: [
        { value: 'image', label: t('Immagine') },
        { value: 'monitor', label: t('Monitor regia') },
      ]},
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
    { type: 'separator', label: t('Pastiglia / Numero') },
    { key: 'lead_mode', label: t('Elemento a sinistra'), type: 'select', options: [
      { value: 'swatch', label: t('Pastiglia') },
      { value: 'number', label: t('Numero') },
    ]},
    { key: 'swatch_size',  label: t('Dimensione (px)'), type: 'range', min: 14, max: 44, step: 1,
      condition: { field: 'lead_mode', value: 'swatch' } },
    { key: 'swatch_shape', label: t('Forma'), type: 'select', options: [
      { value: 'circle', label: t('Cerchio') },
      { value: 'square', label: t('Quadrato arrotondato') },
    ], condition: { field: 'lead_mode', value: 'swatch' } },
    withHover({ key: 'number_color', label: t('Colore numero'), type: 'color',
      condition: { field: 'lead_mode', value: 'number' } }, { hoverKey: 'number_hover_color' }),

    { type: 'separator', label: t('Nome') },
    { key: 'name_font_family', label: t('Famiglia'), type: 'font-family' },
    { key: 'name_color', label: t('Colore'),          type: 'color' },
    { key: 'name_size',  label: t('Dimensione (px)'), type: 'range', min: 14, max: 56, step: 1 },
    { key: 'name_uppercase', label: t('Maiuscolo'), type: 'toggle' },

    { type: 'separator', label: t('Sotto-etichetta') },
    { key: 'mono_font_family', label: t('Font (vuoto = mono del tema)'), type: 'font-family' },
    { key: 'sub_color',     label: t('Colore'),     type: 'color' },
    { key: 'sub_size',      label: t('Dimensione (px)'), type: 'range', min: 10, max: 18, step: 1 },
    { key: 'sub_uppercase', label: t('Maiuscolo'),  type: 'toggle' },

    { type: 'separator', label: t('Descrizione (colonna destra)') },
    { key: 'desc_color', label: t('Colore'), type: 'color',
      condition: { field: 'lead_mode', value: 'number' } },
    { key: 'desc_size',  label: t('Dimensione (px)'), type: 'number', min: 10, max: 24,
      condition: { field: 'lead_mode', value: 'number' } },

    { type: 'separator', label: t('Righe') },
    { key: 'row_padding_y', label: t('Padding verticale (px)'), type: 'range', min: 8, max: 40, step: 1 },
    { key: 'hover_indent',  label: t('Indentazione hover (px)'), type: 'range', min: 0, max: 40, step: 2 },
    { key: 'hover_bg',      label: t('Sfondo riga (hover)'),    type: 'color' },
    { key: 'line_color',    label: t('Colore linee'),          type: 'color' },
  ],
};
