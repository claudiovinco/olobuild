import { t } from '@/i18n';

/**
 * Lookbook Mixer — zona interattiva "componi la tua routine": più slot, ognuno con
 * prev/next che scorre le opzioni (nome · prezzo · colore); una card somma il totale live.
 * Le voci sono piatte con un campo "step" (gruppo): il render le raggruppa per step.
 */
export default {
  type: 'lookbookmixer',
  name: t('Lookbook Mixer'),
  icon: 'dashicons-randomize',
  category: 'layout',

  defaults: {
    items: [
      { step: 'Cleanse', name: 'Rosewater Gel',   price: '24', color: '#f4c9d4' },
      { step: 'Cleanse', name: 'Clay Melt Balm',  price: '29', color: '#e3b778' },
      { step: 'Cleanse', name: 'Milk Cleanser',   price: '22', color: 'var(--olo-color-light, #f8f9fa)' },
      { step: 'Treat',   name: 'Vitamin C Drops', price: '38', color: '#e3b778' },
      { step: 'Treat',   name: 'Niacinamide 10%', price: '32', color: 'var(--olo-color-primary, #e1474f)' },
      { step: 'Treat',   name: 'Retinal Night Oil', price: '46', color: '#d98aa1' },
      { step: 'Hydrate', name: 'Ceramide Cream',  price: '34', color: '#f4c9d4' },
      { step: 'Hydrate', name: 'Gel-Water Lotion', price: '28', color: '#cfeaf0' },
      { step: 'Protect', name: 'Sheer SPF 50',    price: '30', color: 'var(--olo-color-light, #f8f9fa)' },
      { step: 'Protect', name: 'Tinted SPF 30',   price: '33', color: '#e3b778' },
    ],

    currency: '€',
    card_title: 'Your routine',
    card_steps_label: 'steps',
    card_sub: 'Built in four taps. Swap any step until it’s yours.',
    cta_text: 'Add routine to bag',
    cta_url: '#',

    panel_bg: 'var(--olo-color-dark, #16263d)',
    slot_bg: 'var(--olo-color-dark, #16263d)',
    accent: 'var(--olo-color-primary, #e1474f)',
    accent_ink: 'var(--olo-color-dark, #16263d)',
    name_color: 'var(--olo-color-light, #f8f9fa)',
    price_color: 'var(--olo-color-text-soft, #6b7280)',
    line_color: 'rgba(246,233,236,.13)',
    name_font_family: 'heading',
    mono_font_family: '',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Opzioni (campo "step" = gruppo/slot)') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Opzione'),
      defaults: { step: 'Step', name: 'Opzione', price: '0', color: '#e7a0b4' },
      itemFields: [
        { key: 'step',  label: t('Step (gruppo)'), type: 'text' },
        { key: 'name',  label: t('Nome'),          type: 'text' },
        { key: 'price', label: t('Prezzo (numero)'), type: 'number', min: 0, step: 0.01 },
        { key: 'color', label: t('Colore pastiglia'), type: 'color' },
      ],
    },

    { type: 'separator', label: t('Card totale') },
    { key: 'card_title',       label: t('Titolo'),        type: 'text' },
    { key: 'card_steps_label', label: t('Etichetta "steps"'), type: 'text' },
    { key: 'card_sub',         label: t('Sottotitolo'),   type: 'text' },
    { key: 'cta_text',         label: t('Testo bottone'), type: 'text' },
    { key: 'cta_url',          label: t('Link bottone'),  type: 'link' },
    { key: 'currency',         label: t('Simbolo valuta'), type: 'text' },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'panel_bg',    label: t('Sfondo pannello'), type: 'color' },
    { key: 'slot_bg',     label: t('Sfondo slot'),     type: 'color' },
    { key: 'accent',      label: t('Accento'),         type: 'color' },
    { key: 'accent_ink',  label: t('Testo su accento'), type: 'color' },
    { key: 'name_color',  label: t('Colore nome/totale'), type: 'color' },
    { key: 'price_color', label: t('Colore prezzo/sub'), type: 'color' },
    { key: 'line_color',  label: t('Colore bordi'),    type: 'color' },

    { type: 'separator', label: t('Tipografia') },
    { key: 'name_font_family', label: t('Famiglia nome/totale'), type: 'font-family' },
    { key: 'mono_font_family', label: t('Font etichette (vuoto = mono del tema)'), type: 'font-family' },
  ],
};
