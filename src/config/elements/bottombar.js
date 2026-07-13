import { t } from '@/i18n';

/**
 * Tile BottomBar — barra fissa in fondo alla viewport (credits, note legali,
 * contatti sempre visibili). Contenuto HTML centrabile, colori e bordo
 * superiore configurabili. Nel canvas resta in flusso (fixed solo frontend).
 *   fields[]      → contenuto HTML, allineamento, nascondi su mobile
 *   styleFields[] → colori, tipografia, padding, bordo superiore, z-index
 */
export default {
  type: 'bottombar',
  name: t('Barra fissa in basso'),
  icon: 'dashicons-minus',
  category: 'content',
  defaults: {
    content_html: '',
    align: 'center',
    hide_mobile: false,
    bg_color: '',
    text_color: '',
    link_color: '',
    font_size: 11,
    letter_spacing: 2,
    uppercase: true,
    font_preset: '',
    padding_y: 14,
    border_top: false,
    border_color: '',
    z_index: 92,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'content_html', label: t('Contenuto (HTML)'), type: 'textarea',
      hint: t('Testo e link della barra, es. credits del sito.') },
    { key: 'align', label: t('Allineamento'), type: 'segmented', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ] },
    { key: 'hide_mobile', label: t('Nascondi su mobile'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'bg_color', label: t('Sfondo'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },
    { key: 'link_color', label: t('Colore link'), type: 'color' },
    { key: 'font_preset', label: t('Set tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { key: 'font_size', label: t('Dimensione testo (px)'), type: 'range', min: 9, max: 16, step: 1 },
    { key: 'letter_spacing', label: t('Spaziatura lettere (px)'), type: 'range', min: 0, max: 6, step: 0.5 },
    { key: 'uppercase', label: t('Maiuscolo'), type: 'toggle' },
    { key: 'padding_y', label: t('Padding verticale (px)'), type: 'range', min: 6, max: 28, step: 1 },
    { key: 'border_top', label: t('Bordo superiore'), type: 'toggle' },
    { key: 'border_color', label: t('Colore bordo'), type: 'color',
      condition: { field: 'border_top', operator: '==', value: true } },
    { key: 'z_index', label: t('Z-index'), type: 'range', min: 10, max: 9999, step: 1 },
  ],
};
