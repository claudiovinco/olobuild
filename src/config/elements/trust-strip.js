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

  // Allineati alla fonte unica (PHP get_defaults via REST): colori vuoti =
  // token di sistema via resolveColor/safe_color_css (success, text, text-faint).
  defaults: {
    items: [
      { icon: 'check', icon_color: '', text: 'Licenza <b>GPL-v3</b>' },
      { icon: 'check', icon_color: '', text: '<b>WCAG 2.2 AA</b>' },
      { icon: 'check', icon_color: '', text: 'Hosting <b>a scelta tua</b>' },
      { icon: 'check', icon_color: '', text: 'Export <b>HTML/JSON</b> totale' },
      { icon: 'check', icon_color: '', text: 'Trento, <b>Italia 🇮🇹</b>' },
    ],
    separator_char: '·',
    separator_color: '',
    text_color: '',
    text_size:  14,
    font_family: 'sans-serif',
    align: 'center',
    flow:  'wrap',
    gap:   24,
    variant: 'inline',
    logo_height: 18,
    pill_bg: 'rgba(255,255,255,0.05)',
    pill_border: 'rgba(255,255,255,0.12)',
    pill_text_color: '',
    badge_bg: 'var(--olo-color-surface-alt, #f6f7f9)',
    badge_color: 'var(--olo-color-dark, #16263d)',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Items') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Voce'),
      defaults: { icon: 'check', icon_color: '', text: 'Nuova garanzia' },
      itemFields: [
        { key: 'icon',       label: t('Icona'),        type: 'icon' },
        { key: 'logo',       label: t('Logo (immagine — variante Pill)'), type: 'image' },
        { key: 'text',       label: t('Testo'), type: 'editor', mode: 'inline' },
        { key: 'badge',      label: t('Badge (variante Pill)'), type: 'text' },
        { key: 'icon_color', label: t('Colore icona'), type: 'color' },
      ],
    },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  // Un solo controllo per ogni proprietà visiva, raggruppato per variante:
  // - sezioni "Pill"/"Badge" appaiono SOLO in variante pill
  // - sezione "Separatore" e "Colore testo" SOLO in variante inline (in pill i
  //   separatori non sono renderizzati e il testo si colora con "Colore testo"
  //   della sezione Pill — prima erano due controlli sullo stesso elemento)
  // NB: condition inline-only via operator '!=' pill (non value:'inline'): i tile
  // salvati prima della variante non hanno `variant` nei settings ma il render
  // fa fallback a inline — devono vedere i controlli inline.
  styleFields: [
    { type: 'separator', label: t('Variante') },
    { key: 'variant', label: t('Stile'), type: 'select', options: [
      { value: 'inline', label: t('Inline (icona + testo, separatori)') },
      { value: 'pill',   label: t('Pill (box glass: logo + testo + badge)') },
    ]},

    { type: 'separator', label: t('Pill') },
    { key: 'pill_bg',         label: t('Sfondo'),             type: 'color', condition: { field: 'variant', value: 'pill' } },
    { key: 'pill_border',     label: t('Colore bordo'),       type: 'color', condition: { field: 'variant', value: 'pill' } },
    { key: 'pill_text_color', label: t('Colore testo'),       type: 'color', condition: { field: 'variant', value: 'pill' } },
    { key: 'logo_height',     label: t('Altezza logo (px)'),  type: 'range', min: 10, max: 64, step: 1, condition: { field: 'variant', value: 'pill' } },

    { type: 'separator', label: t('Badge (pill)') },
    { key: 'badge_bg',    label: t('Sfondo'), type: 'color', condition: { field: 'variant', value: 'pill' } },
    { key: 'badge_color', label: t('Testo'),  type: 'color', condition: { field: 'variant', value: 'pill' } },

    { type: 'separator', label: t('Separatore') },
    { key: 'separator_char',  label: t('Carattere'), type: 'select', condition: { field: 'variant', operator: '!=', value: 'pill' }, options: [
      { value: '·',  label: t('· (bullet)') },
      { value: '•',  label: t('• (filled bullet)') },
      { value: '|',  label: t('| (pipe)') },
      { value: '/',  label: '/' },
      { value: '—',  label: t('— (em-dash)') },
      { value: '',   label: t('Nessuno') },
    ]},
    { key: 'separator_color', label: t('Colore separatore'), type: 'color', condition: { field: 'variant', operator: '!=', value: 'pill' } },

    { type: 'separator', label: t('Tipografia') },
    { key: 'font_family', label: t('Famiglia'), type: 'font-family' },
    { key: 'text_size',  label: t('Dimensione (px)'),  type: 'range', min: 10, max: 24, step: 1 },
    { key: 'text_color', label: t('Colore testo'),     type: 'color', condition: { field: 'variant', operator: '!=', value: 'pill' } },

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
