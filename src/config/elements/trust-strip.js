import { t } from '@/i18n';

/**
 * Trust Strip — riga editoriale di "garanzie" intervallate da separatori.
 * CONTENUTO: solo testi e icone (item-level).
 * STILE: colori, dimensioni, allineamento, separator visivo.
 */
export default {
  type: 'trust-strip',
  name: t('Striscia garanzie'),
  icon: 'dashicons-yes-alt',
  category: 'layout',

  // Allineati alla fonte unica (PHP get_defaults via REST): colori vuoti =
  // token di sistema via resolveColor/safe_color_css (success, text, text-faint).
  defaults: {
    typography_preset: '',
    items: [
      { icon: 'check', icon_color: '', text: 'Licenza <b>GPL-v3</b>' },
      { icon: 'check', icon_color: '', text: '<b>WCAG 2.2 AA</b>' },
      { icon: 'check', icon_color: '', text: 'Hosting <b>a scelta tua</b>' },
      { icon: 'check', icon_color: '', text: 'Export <b>HTML/JSON</b> totale' },
      { icon: 'check', icon_color: '', text: 'Trento, <b>Italia</b>' },
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
    pill_bg: 'color-mix(in srgb, currentColor 6%, transparent)',
    pill_border: 'color-mix(in srgb, currentColor 16%, transparent)',
    pill_text_color: '',
    badge_bg: 'var(--olo-color-surface-alt, #f6f7f9)',
    badge_color: 'var(--olo-color-dark, #16263d)',
    icon_size: 18,
  },

  // Appena nata: la riga delle garanzie di un negozio o di un'attività, cinque voci con
  // un'icona diversa per ciascuna (nel colore primario), il punto chiave in grassetto e i separatori.
  partenza: {
    items: [
      { icon: 'truck', icon_color: 'var(--olo-color-primary, #e1474f)', text: t('Spedizione <b>gratuita</b> da 49 €') },
      { icon: 'rotate-ccw', icon_color: 'var(--olo-color-primary, #e1474f)', text: t('Reso <b>entro 30 giorni</b>') },
      { icon: 'lock', icon_color: 'var(--olo-color-primary, #e1474f)', text: t('Pagamenti <b>sicuri</b>') },
      { icon: 'headphones', icon_color: 'var(--olo-color-primary, #e1474f)', text: t('Assistenza <b>in italiano</b>') },
      { icon: 'badge-check', icon_color: 'var(--olo-color-primary, #e1474f)', text: t('Garanzia <b>di 2 anni</b>') },
    ],
    badge_bg: 'var(--olo-color-surface-alt, #f6f7f9)',
    badge_color: 'var(--olo-color-dark, #16263d)',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  // 16 nov 2026 (episodio 30): nomi in italiano («Items», «Pill», «bullet», «em-dash», «space-between» via), spiegazioni
  // nelle (i) invece che tra parentesi nelle etichette, logo e badge solo con le pillole (con «In riga» non agiscono),
  // «Voce» nuova coi suoi valori (la chiave era `defaults`, che il ripetitore non legge) e lo Stile nelle zone.
  fields: [
    { type: 'separator', label: t('Voci') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Voce'), etichettaDa: 'text', miniaturaDa: 'logo',
      newItemDefaults: { icon: 'check', icon_color: 'var(--olo-color-primary)', text: t('Nuova garanzia') },
      itemFields: [
        { key: 'icon',  label: t('Icona'), type: 'icon' },
        { key: 'logo',  label: t('Logo'), type: 'image', condizioneTile: { field: 'variant', op: 'eq', value: 'pill' },
          description: t('Con le pillole, al posto dell’icona: il marchio di un pagamento, di un corriere, di una certificazione.') },
        { key: 'text',  label: t('Testo'), type: 'editor', mode: 'inline' },
        { key: 'badge', label: t('Badge'), type: 'text', condizioneTile: { field: 'variant', op: 'eq', value: 'pill' },
          description: t('Con le pillole, una piccola etichetta a destra del testo: «Nuovo», «Gratis».') },
      ],
    },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  // Zone: Aspetto · Pillole (solo con le pillole) · Separatore (solo in riga) · Testo · Disposizione · Ogni voce.
  // NB: condizioni «in riga» con op 'neq' pill (non eq inline): le tile salvate prima della variante non hanno `variant`
  // nei settings ma il render fa ripiego su inline — devono vedere i controlli in riga.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'variant', label: t('Stile'), type: 'select', options: [
      { value: 'inline', label: t('In riga') },
      { value: 'pill',   label: t('Pillole') },
    ], description: t('In riga: icona e testo, con un separatore fra le voci. Pillole: ogni voce in una capsula di vetro, col logo al posto dell’icona e un badge.') },
    { key: 'icon_size', label: t('Dimensione icone'), type: 'range', min: 10, max: 48, step: 1, unit: 'px' },

    { type: 'separator', label: t('Pillole'), condition: { field: 'variant', op: 'eq', value: 'pill' } },
    { key: 'pill_bg',         label: t('Sfondo pillola'),  type: 'color' },
    { key: 'pill_border',     label: t('Colore bordo'),    type: 'color' },
    { key: 'pill_text_color', label: t('Colore testo'),    type: 'color' },
    { key: 'logo_height',     label: t('Altezza logo'),    type: 'range', min: 10, max: 64, step: 1, unit: 'px' },
    { key: 'badge_bg',        label: t('Sfondo badge'),    type: 'color' },
    { key: 'badge_color',     label: t('Colore testo badge'), type: 'color' },

    { type: 'separator', label: t('Separatore'), condition: { field: 'variant', op: 'neq', value: 'pill' } },
    { key: 'separator_char',  label: t('Separatore'), type: 'select', options: [
      { value: '·',  label: t('· Punto') },
      { value: '•',  label: t('• Pallino') },
      { value: '|',  label: t('| Barra') },
      { value: '/',  label: '/' },
      { value: '—',  label: t('— Lineetta') },
      { value: '',   label: t('Nessuno') },
    ]},
    { key: 'separator_color', label: t('Colore separatore'), type: 'color' },

    // Un controllo unico. Due dichiarazioni perché nella variante «pill» il
    // colore del testo è `pill_text_color`, che vive nella zona Pillole:
    // così non resta mai un campo colore orfano fuori dal controllo.
    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Testo'), responsiveKeys: [],
      keys: { family: 'font_family', size: 'text_size', color: 'text_color' },
      sizeMin: 10, sizeMax: 24,
      condition: { field: 'variant', op: 'neq', value: 'pill' } },
    { type: 'typography', label: t('Testo'), responsiveKeys: [],
      keys: { family: 'font_family', size: 'text_size' },
      sizeMin: 10, sizeMax: 24,
      condition: { field: 'variant', op: 'eq', value: 'pill' } },

    { type: 'separator', label: t('Disposizione') },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left',          label: t('Sinistra') },
      { value: 'center',        label: t('Centrato') },
      { value: 'right',         label: t('Destra') },
      { value: 'space-between', label: t('Distribuito') },
    ]},
    { key: 'flow', label: t('Su più righe'), type: 'select', options: [
      { value: 'wrap',   label: t('A capo se serve') },
      { value: 'nowrap', label: t('Forza una riga') },
    ], description: t('Forza una riga: le voci non vanno mai a capo, e se non ci stanno la striscia scorre di lato.') },
    { key: 'gap', label: t('Gap'), type: 'range', min: 4, max: 80, step: 2, unit: 'px' },

    { type: 'separator', label: t('Ogni voce') },
    { key: 'items', type: 'content-items', label: t('Voci'), itemLabel: t('Voce'), etichettaDa: 'text', miniaturaDa: 'logo', itemFields: [
        { key: 'icon_color', label: t('Colore icona'), type: 'color' },
    ] },
  ],
};
