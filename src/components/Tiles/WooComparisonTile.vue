<template>
  <div>
    <table :style="tableStyle">
      <thead>
        <tr>
          <th :style="headerCellStyle"></th>
          <th v-for="p in products" :key="p.name" :style="headerCellStyle">
            <div :style="{ width: '80px', height: '80px', background: TOKENS.surfaceAlt, borderRadius: '8px', margin: '0 auto 8px', display: 'flex', alignItems: 'center', justifyContent: 'center', color: TOKENS.textFaint }">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            </div>
            <div :style="{ fontWeight: 600, fontSize: '14px' }">{{ p.name }}</div>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="s.show_price !== false">
          <td :style="labelCellStyle">{{ t('Prezzo') }}</td>
          <td v-for="p in products" :key="'price-'+p.name" :style="valueCellStyle">&euro;{{ p.price }}</td>
        </tr>
        <tr v-if="s.show_rating !== false">
          <td :style="labelCellStyle">{{ t('Valutazione') }}</td>
          <td v-for="p in products" :key="'rating-'+p.name" :style="valueCellStyle">
            <svg v-for="n in 5" :key="n" width="14" height="14" viewBox="0 0 24 24" :fill="n <= p.rating ? TOKENS.accent : TOKENS.border" stroke="none" style="display:inline-block;vertical-align:middle;">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
          </td>
        </tr>
        <tr v-if="s.show_stock !== false">
          <td :style="labelCellStyle">{{ t('Disponibilita') }}</td>
          <td v-for="p in products" :key="'stock-'+p.name" :style="valueCellStyle">
            <span :style="{ color: p.inStock ? TOKENS.success.fg : TOKENS.error.fg }">{{ p.inStock ? 'Disponibile' : 'Esaurito' }}</span>
          </td>
        </tr>
        <tr v-if="s.show_add_to_cart !== false">
          <td :style="labelCellStyle"></td>
          <td v-for="p in products" :key="'cart-'+p.name" :style="valueCellStyle">
            <button :style="buttonStyle">{{ t('Aggiungi') }}</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => props.settings || {});
const products = [
  { name: 'Prodotto A', price: '29.90', rating: 4, inStock: true },
  { name: 'Prodotto B', price: '49.90', rating: 5, inStock: true },
  { name: 'Prodotto C', price: '39.90', rating: 3, inStock: false },
];
const headerBg = computed(() => resolveColor(s.value.header_bg, TOKENS.surfaceAlt));
const headerColor = computed(() => resolveColor(s.value.header_color, TOKENS.text));
const tableStyle = { width: '100%', borderCollapse: 'collapse', fontSize: '14px' };
const headerCellStyle = computed(() => ({
  background: headerBg.value,
  color: headerColor.value,
  padding: '16px',
  textAlign: 'center',
  borderBottom: '1px solid ' + TOKENS.border,
}));
const labelCellStyle = {
  padding: '12px 16px',
  fontWeight: 600,
  color: TOKENS.textSoft,
  borderBottom: '1px solid ' + TOKENS.surfaceAlt,
  fontSize: '13px',
};
const valueCellStyle = {
  padding: '12px 16px',
  textAlign: 'center',
  borderBottom: '1px solid ' + TOKENS.surfaceAlt,
};
const buttonStyle = {
  padding: '6px 16px',
  background: TOKENS.primary,
  color: TOKENS.onPrimary,
  border: 'none',
  borderRadius: '6px',
  fontSize: '12px',
  fontWeight: 600,
  cursor: 'pointer',
};
</script>
