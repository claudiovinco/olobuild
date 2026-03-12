export default {
  type: 'paymentbuttons',
  name: 'Pulsanti Pagamento',
  icon: 'dashicons-money-alt',
  category: 'marketing',
  defaults: {
    provider: 'stripe',
    amount: '29.99',
    currency: 'EUR',
    description: 'Pagamento',
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
  },
  fields: [
    { key: 'provider', label: 'Provider', type: 'select', options: [
      { value: 'stripe', label: 'Stripe' },
      { value: 'paypal', label: 'PayPal' },
      { value: 'both', label: 'Entrambi' },
    ]},
    { key: 'amount', label: 'Importo', type: 'text' },
    { key: 'currency', label: 'Valuta', type: 'select', options: [
      { value: 'EUR', label: 'EUR (€)' },
      { value: 'USD', label: 'USD ($)' },
      { value: 'GBP', label: 'GBP (£)' },
    ]},
    { key: 'description', label: 'Descrizione', type: 'text' },
    { key: 'button_text', label: 'Testo pulsante', type: 'text' },
    { key: 'success_url', label: 'URL successo', type: 'text', placeholder: '/grazie' },
    { key: 'cancel_url', label: 'URL annullamento', type: 'text', placeholder: '/annullato' },

    { type: 'separator', label: 'Stripe' },
    { key: 'stripe_key', label: 'Chiave pubblica Stripe', type: 'text',
      condition: { field: 'provider', op: 'in', value: ['stripe', 'both'] } },
    { key: 'stripe_price_id', label: 'Price ID Stripe', type: 'text',
      condition: { field: 'provider', op: 'in', value: ['stripe', 'both'] } },

    { type: 'separator', label: 'PayPal' },
    { key: 'paypal_client_id', label: 'Client ID PayPal', type: 'text',
      condition: { field: 'provider', op: 'in', value: ['paypal', 'both'] } },
    { key: 'paypal_style', label: 'Stile pulsante PayPal', type: 'select', options: [
      { value: 'rect', label: 'Rettangolare' },
      { value: 'pill', label: 'Pillola' },
    ], condition: { field: 'provider', op: 'in', value: ['paypal', 'both'] } },

    { type: 'separator', label: 'Stile' },
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'bg_color', label: 'Colore sfondo', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'border_radius', label: 'Raggio bordo', type: 'border-radius' },
    { key: 'font_size', label: 'Dim. carattere (px)', type: 'range', min: 12, max: 24, step: 1 },
    { key: 'full_width', label: 'Larghezza piena', type: 'toggle' },
  ],
};
