import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { t } from '@/i18n';

/**
 * Tile PaymentButtons — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → provider, importo, valuta, descrizione, testo pulsante, URL success/cancel,
 *                   chiavi Stripe/PayPal (sorgente dati esterna)
 *   styleFields[] → sfondo creativo, typography preset, allineamento, colori, raggio, font size,
 *                   larghezza piena, stile PayPal, effetti testo, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'paymentbuttons',
  name: t('Pulsanti Pagamento'),
  icon: 'dashicons-money-alt',
  category: 'marketing',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    provider: 'stripe',
    amount: '29.99',
    currency: 'EUR',
    description: t('Pagamento'),
    button_text: 'Paga ora',
    success_url: '',
    cancel_url: '',
    // Style
    alignment: 'center',
    bg_color: '',
    text_color: '',
    border_radius: '8',
    font_size: '16',
    full_width: false,
    icon_position: 'before',
    // PayPal
    paypal_client_id: '',
    paypal_style: 'rect',
    // Stripe
    stripe_key: '',
    stripe_price_id: '',
    ...textEffectsDefaults,
    text_effect_target: 'description',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'provider', label: t('Provider'), type: 'select', options: [
      { value: 'stripe', label: t('Stripe') },
      { value: 'paypal', label: t('PayPal') },
      { value: 'both', label: t('Entrambi') },
    ]},
    { key: 'amount', label: t('Importo'), type: 'number', min: 0, step: 0.01 },
    { key: 'currency', label: t('Valuta'), type: 'select', options: [
      { value: 'EUR', label: t('EUR (€)') },
      { value: 'USD', label: t('USD ($)') },
      { value: 'GBP', label: t('GBP (£)') },
    ]},
    { key: 'description', label: t('Descrizione'), type: 'text' },
    { key: 'button_text', label: t('Testo pulsante'), type: 'text' },
    { key: 'success_url', label: t('URL successo'), type: 'link', placeholder: t('/grazie') },
    { key: 'cancel_url', label: t('URL annullamento'), type: 'link', placeholder: t('/annullato') },

    { type: 'separator', label: t('Stripe') },
    { key: 'stripe_key', label: t('Chiave pubblica Stripe'), type: 'text',
      condition: { field: 'provider', op: 'in', value: ['stripe', 'both'] } },
    { key: 'stripe_price_id', label: t('Price ID Stripe'), type: 'text',
      condition: { field: 'provider', op: 'in', value: ['stripe', 'both'] } },

    { type: 'separator', label: t('PayPal') },
    { key: 'paypal_client_id', label: t('Client ID PayPal'), type: 'text',
      condition: { field: 'provider', op: 'in', value: ['paypal', 'both'] } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([ { value: 'description', label: t('Solo Descrizione') } ]),

    { type: 'separator', label: t('PayPal — stile') },
    { key: 'paypal_style', label: t('Stile pulsante PayPal'), type: 'select', options: [
      { value: 'rect', label: t('Rettangolare') },
      { value: 'pill', label: t('Pillola') },
    ], condition: { field: 'provider', op: 'in', value: ['paypal', 'both'] } },

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'font_size',
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 24,
    },

    { type: 'separator', label: t('Stile') },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },
    withHover({ key: 'border_radius', label: t('Raggio bordo'), type: 'border-radius' }),
    { key: 'full_width', label: t('Larghezza piena'), type: 'toggle' },
    ...borderFields(),
  ],
};
