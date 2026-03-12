<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon">&#x1F6D2;</span>
      <span>WooCommerce richiesto</span>
    </div>
    <div v-else>
      <div :style="layoutStyle">
        <!-- Billing form -->
        <div>
          <h3 :style="sectionTitleStyle">Dettagli di fatturazione</h3>
          <div :style="formGridStyle">
            <div>
              <label :style="labelStyle">Nome *</label>
              <input type="text" placeholder="Mario" :style="inputStyle" readonly />
            </div>
            <div>
              <label :style="labelStyle">Cognome *</label>
              <input type="text" placeholder="Rossi" :style="inputStyle" readonly />
            </div>
          </div>
          <div style="margin-bottom:16px;">
            <label :style="labelStyle">Azienda (opzionale)</label>
            <input type="text" placeholder="Nome azienda" :style="inputStyle" readonly />
          </div>
          <div style="margin-bottom:16px;">
            <label :style="labelStyle">Paese/Regione *</label>
            <select :style="inputStyle" disabled>
              <option>Italia</option>
            </select>
          </div>
          <div style="margin-bottom:16px;">
            <label :style="labelStyle">Indirizzo *</label>
            <input type="text" placeholder="Via e numero civico" :style="inputStyle" readonly />
          </div>
          <div :style="formGridStyle">
            <div>
              <label :style="labelStyle">CAP *</label>
              <input type="text" placeholder="00100" :style="inputStyle" readonly />
            </div>
            <div>
              <label :style="labelStyle">Città *</label>
              <input type="text" placeholder="Roma" :style="inputStyle" readonly />
            </div>
          </div>
          <div style="margin-bottom:16px;">
            <label :style="labelStyle">Telefono *</label>
            <input type="text" placeholder="+39 333 1234567" :style="inputStyle" readonly />
          </div>
          <div style="margin-bottom:16px;">
            <label :style="labelStyle">Email *</label>
            <input type="text" placeholder="mario@email.com" :style="inputStyle" readonly />
          </div>
          <div v-if="s.show_order_notes" style="margin-bottom:16px;">
            <label :style="labelStyle">Note sull'ordine (opzionale)</label>
            <textarea :style="{ ...inputStyle, height: '80px', resize: 'vertical' }" placeholder="Note riguardo il tuo ordine, ad es. istruzioni per la consegna" readonly></textarea>
          </div>
        </div>

        <!-- Order summary -->
        <div>
          <h3 :style="sectionTitleStyle">Il tuo ordine</h3>
          <table :style="orderTableStyle">
            <thead>
              <tr>
                <th :style="orderThStyle">Prodotto</th>
                <th :style="{ ...orderThStyle, textAlign: 'right' }">Subtotale</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in orderItems" :key="item.name">
                <td :style="orderTdStyle">{{ item.name }} <strong>&times; {{ item.qty }}</strong></td>
                <td :style="{ ...orderTdStyle, textAlign: 'right' }">&euro;{{ (item.price * item.qty).toFixed(2) }}</td>
              </tr>
              <tr>
                <th :style="orderThTotalStyle">Subtotale</th>
                <td :style="{ ...orderTdStyle, textAlign: 'right', fontWeight: 600 }">&euro;{{ subtotal.toFixed(2) }}</td>
              </tr>
              <tr>
                <th :style="orderThTotalStyle">Spedizione</th>
                <td :style="{ ...orderTdStyle, textAlign: 'right' }">Gratuita</td>
              </tr>
              <tr>
                <th :style="{ ...orderThTotalStyle, fontSize: '16px' }">Totale</th>
                <td :style="{ ...orderTdStyle, textAlign: 'right', fontSize: '18px', fontWeight: 700, color: s.heading_color }">&euro;{{ subtotal.toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>

          <!-- Payment -->
          <div :style="paymentBoxStyle">
            <div :style="paymentOptionStyle">
              <input type="radio" checked readonly style="margin-right:8px;" />
              <label :style="{ fontWeight: 600, color: s.heading_color, fontSize: '14px' }">Bonifico bancario</label>
            </div>
            <div :style="{ ...paymentOptionStyle, borderBottom: 'none' }">
              <input type="radio" readonly style="margin-right:8px;" />
              <label :style="{ fontWeight: 600, color: s.heading_color, fontSize: '14px' }">PayPal</label>
            </div>
          </div>

          <button :style="placeOrderStyle">Effettua ordine</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  layout: 'two_columns',
  show_order_notes: true,
  accent_color: 'var(--olo-color-primary, #6366F1)',
  text_color: '#374151',
  form_style: 'modern',
  heading_color: 'var(--olo-color-text, #374151)',
  border_color: '#E5E7EB',
  button_color: '#FFFFFF',
  button_bg: 'var(--olo-color-primary, #6366F1)',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const wooActive = computed(() => true);

const orderItems = [
  { name: 'T-Shirt Premium Blu', price: 39.90, qty: 2 },
  { name: 'Felpa con Cappuccio Nera', price: 59.90, qty: 1 },
];

const subtotal = computed(() => orderItems.reduce((acc, item) => acc + item.price * item.qty, 0));

const borderRadius = computed(() => s.value.form_style === 'modern' ? '8px' : '4px');
const inputPadding = computed(() => s.value.form_style === 'modern' ? '12px 16px' : '8px 12px');

const layoutStyle = computed(() => {
  if (s.value.layout === 'two_columns') {
    return {
      display: 'grid',
      gridTemplateColumns: '1fr 1fr',
      gap: '32px',
    };
  }
  return { maxWidth: '640px' };
});

const sectionTitleStyle = computed(() => ({
  fontSize: '20px',
  fontWeight: '700',
  color: s.value.heading_color,
  margin: '0 0 20px',
  paddingBottom: '12px',
  borderBottom: `2px solid ${s.value.accent_color}`,
}));

const labelStyle = computed(() => ({
  display: 'block',
  fontSize: '13px',
  fontWeight: '600',
  color: s.value.heading_color,
  marginBottom: '6px',
}));

const inputStyle = computed(() => ({
  width: '100%',
  padding: inputPadding.value,
  border: `1px solid ${s.value.border_color}`,
  borderRadius: borderRadius.value,
  fontSize: '14px',
  color: s.value.text_color,
  boxSizing: 'border-box',
}));

const formGridStyle = {
  display: 'grid',
  gridTemplateColumns: '1fr 1fr',
  gap: '16px',
  marginBottom: '16px',
};

const orderTableStyle = computed(() => ({
  width: '100%',
  borderCollapse: 'collapse',
  border: `1px solid ${s.value.border_color}`,
  borderRadius: borderRadius.value,
  overflow: 'hidden',
  marginBottom: '20px',
}));

const orderThStyle = computed(() => ({
  background: '#F9FAFB',
  color: s.value.heading_color,
  fontWeight: '600',
  fontSize: '13px',
  padding: '12px 16px',
  textAlign: 'left',
  borderBottom: `1px solid ${s.value.border_color}`,
}));

const orderTdStyle = computed(() => ({
  padding: '12px 16px',
  borderBottom: `1px solid ${s.value.border_color}`,
  fontSize: '14px',
  color: s.value.text_color,
}));

const orderThTotalStyle = computed(() => ({
  padding: '12px 16px',
  borderBottom: `1px solid ${s.value.border_color}`,
  fontWeight: '600',
  color: s.value.heading_color,
  textAlign: 'left',
  fontSize: '14px',
}));

const paymentBoxStyle = computed(() => ({
  background: '#F9FAFB',
  border: `1px solid ${s.value.border_color}`,
  borderRadius: borderRadius.value,
  padding: '4px 16px',
  marginBottom: '16px',
}));

const paymentOptionStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  padding: '12px 0',
  borderBottom: `1px solid ${s.value.border_color}`,
}));

const placeOrderStyle = computed(() => ({
  display: 'block',
  width: '100%',
  padding: '14px 24px',
  background: s.value.button_bg,
  color: s.value.button_color,
  border: 'none',
  borderRadius: borderRadius.value,
  fontSize: '16px',
  fontWeight: '700',
  cursor: 'pointer',
  textAlign: 'center',
}));
</script>

<style scoped>
.olo-woo-notice {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 16px 20px;
  background: #FEF3C7;
  border: 1px solid #F59E0B;
  border-radius: 8px;
  color: #92400E;
  font-size: 14px;
  font-weight: 500;
}
.olo-woo-notice-icon {
  font-size: 20px;
}
</style>
