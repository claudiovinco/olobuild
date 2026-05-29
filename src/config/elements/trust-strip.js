import { t } from '@/i18n';

/**
 * Trust Strip — riga editoriale di "garanzie" intervallate da separatori.
 * CONTENUTO: solo testi e icone (item-level).
 * STILE: colori, dimensioni, allineamento, separator visivo.
 */
export default {
  type: 'trust-strip',
  name: t('Trust Strip'),
  icon: 'dashicons-yes-alt',
  category: 'layout',

  defaults: {
    items: [
      { icon: 'check', icon_color: '#10b981', text: 'Licenza <b>GPL-v3</b>' },
      { icon: 'check', icon_color: '#10b981', text: '<b>WCAG 2.2 AA</b>' },
      { icon: 'check', icon_color: '#10b981', text: 'Hosting <b>a scelta tua</b>' },
      { icon: 'check', icon_color: '#10b981', text: 'Export <b>HTML/JSON</b> totale' },
      { icon: 'check', icon_color: '#10b981', text: 'Trento, <b>Italia 🇮🇹</b>' },
    ],
    separator_char: '·',
    separator_color: '#9ca3af',
    text_color: '#374151',
    text_size:  14,
    font_family: 'sans-serif',
    align: 'center',
    flow:  'wrap',
    gap:   24,
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Items') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Voce'),
      defaults: { icon: 'check', icon_color: '#10b981', text: 'Nuova garanzia' },
      itemFields: [
        { key: 'icon',       label: t('Icona'),        type: 'icon' },
        { key: 'text',       label: t('Testo'), type: 'editor', mode: 'inline' },
        { key: 'icon_color', label: t('Colore icona'), type: 'color' },
      ],
    },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Tipografia') },
    { key: 'font_family', label: t('Famiglia'), type: 'select', options: [
      { value: 'sans-serif', label: t('Sans-serif (default)') },
      { value: 'serif',      label: t('Serif') },
      { value: 'mono',       label: t('Monospace') },
    ]},
    { key: 'text_color', label: t('Colore testo'),     type: 'color' },
    { key: 'text_size',  label: t('Dimensione (px)'),  type: 'range', min: 10, max: 24, step: 1 },

    { type: 'separator', label: t('Separatore') },
    { key: 'separator_char',  label: t('Carattere'), type: 'select', options: [
      { value: '·',  label: t('· (bullet)') },
      { value: '•',  label: t('• (filled bullet)') },
      { value: '|',  label: t('| (pipe)') },
      { value: '/',  label: '/' },
      { value: '—',  label: t('— (em-dash)') },
      { value: '',   label: t('Nessuno') },
    ]},
    { key: 'separator_color', label: t('Colore separatore'), type: 'color' },

    { type: 'separator', label: t('Layout') },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left',          label: t('Sinistra') },
      { value: 'center',        label: t('Centrato') },
      { value: 'right',         label: t('Destra') },
      { value: 'space-between', label: t('Distribuito (space-between)') },
    ]},
    { key: 'flow', label: t('Comportamento'), type: 'select', options: [
      { value: 'wrap',   label: t('A capo se serve') },
      { value: 'nowrap', label: t('Forza una riga') },
    ]},
    { key: 'gap', label: t('Gap tra items (px)'), type: 'range', min: 4, max: 80, step: 2 },
  ],
};
