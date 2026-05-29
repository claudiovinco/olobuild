<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><circle cx="7.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><circle cx="13.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><polyline points="0 2 3.2 4 5.3 12.5 16 12.5 18 6.5 8 6.5"/></svg></span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else>
      <!-- Cart table -->
      <table :style="tableStyle">
        <thead>
          <tr>
            <th :style="thStyle" v-if="s.show_thumbnail" style="width:80px;"></th>
            <th :style="thStyle">{{ t('Prodotto') }}</th>
            <th :style="thStyle">{{ t('Prezzo') }}</th>
            <th :style="thStyle" style="width:100px;">{{ t('Quantità') }}</th>
            <th :style="thStyle">{{ t('Subtotale') }}</th>
            <th :style="thStyle" style="width:40px;"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in cartItems" :key="item.name">
            <td :style="tdStyle" v-if="s.show_thumbnail">
              <div :style="thumbStyle">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-text-faint, #94a3b8)" stroke-width="1.5">
                  <rect x="3" y="3" width="18" height="18" rx="2"/>
                  <circle cx="8.5" cy="8.5" r="1.5"/>
                  <path d="M21 15l-5-5L5 21"/>
                </svg>
              </div>
            </td>
            <td :style="tdStyle">
              <span :style="{ color: s.heading_color, fontWeight: 600, fontSize: '14px' }">{{ item.name }}</span>
            </td>
            <td :style="tdStyle">&euro;{{ item.price.toFixed(2) }}</td>
            <td :style="tdStyle">
              <input
                type="number"
                :value="item.qty"
                readonly
                :style="qtyStyle"
              />
            </td>
            <td :style="{ ...tdStyle, fontWeight: 600 }">&euro;{{ (item.price * item.qty).toFixed(2) }}</td>
            <td :style="tdStyle">
              <span style="color:var(--olo-color-error, #b42318);cursor:pointer;font-size:18px;">{{ t('&times;') }}</span>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Coupon + Update -->
      <div v-if="s.show_coupon" :style="actionsStyle">
        <div style="display:flex;gap:8px;align-items:center;">
          <input
            type="text"
            :placeholder="t('Codice coupon')"
            :style="couponInputStyle"
            readonly
          />
          <button :style="couponBtnStyle">{{ t('Applica coupon') }}</button>
        </div>
        <button :style="updateBtnStyle">{{ t('Aggiorna carrello') }}</button>
      </div>

      <!-- Totals -->
      <div v-if="s.show_totals" :style="totalsWrapStyle">
        <h2 :style="totalsTitleStyle">{{ t('Totali carrello') }}</h2>
        <table :style="{ width: '100%', borderCollapse: 'collapse' }">
          <tr>
            <th :style="totalsThStyle">{{ t('Subtotale') }}</th>
            <td :style="totalsTdStyle">&euro;{{ subtotal.toFixed(2) }}</td>
          </tr>
          <tr>
            <th :style="totalsThStyle">{{ t('Spedizione') }}</th>
            <td :style="totalsTdStyle">{{ t('Spedizione gratuita') }}</td>
          </tr>
          <tr>
            <th :style="{ ...totalsThStyle, fontSize: '16px' }">{{ t('Totale') }}</th>
            <td :style="{ ...totalsTdStyle, fontSize: '18px', fontWeight: 700, color: s.heading_color }">&euro;{{ subtotal.toFixed(2) }}</td>
          </tr>
        </table>
        <button :style="checkoutBtnStyle">{{ t('Procedi al checkout') }}</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  show_thumbnail: true,
  show_coupon: true,
  show_totals: true,
  button_color: '',         // '' ⇒ TOKENS.onPrimary
  button_bg: 'var(--olo-color-primary, #e1474f)',
  text_color: '',           // '' ⇒ TOKENS.text
  heading_color: 'var(--olo-color-text, #374151)',
  border_color: '',         // '' ⇒ TOKENS.border
};
const sRaw = computed(() => ({ ...defaults, ...props.settings }));
// Vista risolta token-first: colori vuoti ⇒ token del tema (niente grigi nudi).
const s = computed(() => ({
  ...sRaw.value,
  text_color: resolveColor(sRaw.value.text_color, TOKENS.text),
  heading_color: resolveColor(sRaw.value.heading_color, TOKENS.text),
  border_color: resolveColor(sRaw.value.border_color, TOKENS.border),
  button_color: resolveColor(sRaw.value.button_color, TOKENS.onPrimary),
  button_bg: resolveColor(sRaw.value.button_bg, TOKENS.primary),
}));

const wooActive = computed(() => true);

const cartItems = [
  { name: 'T-Shirt Premium Blu', price: 39.90, qty: 2 },
  { name: 'Felpa con Cappuccio Nera', price: 59.90, qty: 1 },
];

const subtotal = computed(() => cartItems.reduce((acc, item) => acc + item.price * item.qty, 0));

const tableStyle = computed(() => ({
  width: '100%',
  borderCollapse: 'collapse',
  border: `1px solid ${s.value.border_color}`,
  borderRadius: '8px',
  overflow: 'hidden',
}));

const thStyle = computed(() => ({
  background: TOKENS.surfaceAlt,
  color: s.value.heading_color,
  fontWeight: '600',
  fontSize: '13px',
  textTransform: 'uppercase',
  letterSpacing: '0.05em',
  padding: '12px 16px',
  borderBottom: `1px solid ${s.value.border_color}`,
  textAlign: 'left',
}));

const tdStyle = computed(() => ({
  padding: '16px',
  borderBottom: `1px solid ${s.value.border_color}`,
  verticalAlign: 'middle',
  fontSize: '14px',
  color: s.value.text_color,
}));

const thumbStyle = {
  width: '60px',
  height: '60px',
  background: TOKENS.surfaceAlt,
  borderRadius: '6px',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
};

const qtyStyle = computed(() => ({
  width: '60px',
  padding: '6px 8px',
  border: `1px solid ${s.value.border_color}`,
  borderRadius: '4px',
  fontSize: '14px',
  textAlign: 'center',
}));

const actionsStyle = computed(() => ({
  display: 'flex',
  justifyContent: 'space-between',
  alignItems: 'center',
  padding: '16px 0',
  borderBottom: `1px solid ${s.value.border_color}`,
  flexWrap: 'wrap',
  gap: '12px',
}));

const couponInputStyle = computed(() => ({
  padding: '8px 12px',
  border: `1px solid ${s.value.border_color}`,
  borderRadius: '4px',
  fontSize: '14px',
  width: '180px',
}));

const couponBtnStyle = computed(() => ({
  padding: '8px 16px',
  background: 'transparent',
  color: s.value.text_color,
  border: `1px solid ${s.value.border_color}`,
  borderRadius: '4px',
  fontSize: '13px',
  fontWeight: '600',
  cursor: 'pointer',
}));

const updateBtnStyle = computed(() => ({
  padding: '8px 16px',
  background: 'transparent',
  color: s.value.text_color,
  border: `1px solid ${s.value.border_color}`,
  borderRadius: '4px',
  fontSize: '13px',
  fontWeight: '600',
  cursor: 'pointer',
}));

const totalsWrapStyle = computed(() => ({
  marginTop: '24px',
  maxWidth: '400px',
  marginLeft: 'auto',
}));

const totalsTitleStyle = computed(() => ({
  fontSize: '18px',
  fontWeight: '700',
  color: s.value.heading_color,
  margin: '0 0 16px',
}));

const totalsThStyle = computed(() => ({
  padding: '10px 0',
  borderBottom: `1px solid ${s.value.border_color}`,
  fontWeight: '600',
  color: s.value.heading_color,
  textAlign: 'left',
  width: '40%',
  fontSize: '14px',
}));

const totalsTdStyle = computed(() => ({
  padding: '10px 0',
  borderBottom: `1px solid ${s.value.border_color}`,
  fontSize: '14px',
  color: s.value.text_color,
  textAlign: 'right',
}));

const checkoutBtnStyle = computed(() => ({
  display: 'block',
  width: '100%',
  padding: '12px 24px',
  marginTop: '16px',
  background: s.value.button_bg,
  color: s.value.button_color,
  border: 'none',
  borderRadius: '6px',
  fontSize: '14px',
  fontWeight: '600',
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
  background: color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);
  border: 1px solid var(--olo-color-warning, #b45309);
  border-radius: 8px;
  color: var(--olo-color-warning, #b45309);
  font-size: 14px;
  font-weight: 500;
}
.olo-woo-notice-icon {
  width: 20px;
  height: 20px;
  display: inline-flex;
  flex-shrink: 0;
}
.olo-woo-notice-icon :deep(svg) {
  width: 100%;
  height: 100%;
}
</style>
