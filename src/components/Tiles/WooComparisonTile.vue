<template>
  <div>
    <table :style="tableStyle">
      <thead>
        <tr>
          <th :style="headerCellStyle"></th>
          <th v-for="p in products" :key="p.name" :style="headerCellStyle">
            <div :style="{ width: '80px', height: '80px', background: '#F3F4F6', borderRadius: '8px', margin: '0 auto 8px', display: 'flex', alignItems: 'center', justifyContent: 'center' }">
              <span style="font-size:28px;opacity:.3;">&#x1F4E6;</span>
            </div>
            <div :style="{ fontWeight: 600, fontSize: '14px' }">{{ p.name }}</div>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="s.show_price !== false">
          <td :style="labelCellStyle">Prezzo</td>
          <td v-for="p in products" :key="'price-'+p.name" :style="valueCellStyle">&euro;{{ p.price }}</td>
        </tr>
        <tr v-if="s.show_rating !== false">
          <td :style="labelCellStyle">Valutazione</td>
          <td v-for="p in products" :key="'rating-'+p.name" :style="valueCellStyle">{{ '★'.repeat(p.rating) }}{{ '☆'.repeat(5-p.rating) }}</td>
        </tr>
        <tr v-if="s.show_stock !== false">
          <td :style="labelCellStyle">Disponibilita</td>
          <td v-for="p in products" :key="'stock-'+p.name" :style="valueCellStyle">
            <span :style="{ color: p.inStock ? '#059669' : '#DC2626' }">{{ p.inStock ? 'Disponibile' : 'Esaurito' }}</span>
          </td>
        </tr>
        <tr v-if="s.show_add_to_cart !== false">
          <td :style="labelCellStyle"></td>
          <td v-for="p in products" :key="'cart-'+p.name" :style="valueCellStyle">
            <button :style="buttonStyle">Aggiungi</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => props.settings || {});
const products = [
  { name: 'Prodotto A', price: '29.90', rating: 4, inStock: true },
  { name: 'Prodotto B', price: '49.90', rating: 5, inStock: true },
  { name: 'Prodotto C', price: '39.90', rating: 3, inStock: false },
];
const headerBg = computed(() => s.value.header_bg || '#F9FAFB');
const headerColor = computed(() => s.value.header_color || 'var(--olo-color-text, #374151)');
const tableStyle = { width: '100%', borderCollapse: 'collapse', fontSize: '14px' };
const headerCellStyle = computed(() => ({
  background: headerBg.value,
  color: headerColor.value,
  padding: '16px',
  textAlign: 'center',
  borderBottom: '1px solid #E5E7EB',
}));
const labelCellStyle = {
  padding: '12px 16px',
  fontWeight: 600,
  color: '#6B7280',
  borderBottom: '1px solid #F3F4F6',
  fontSize: '13px',
};
const valueCellStyle = {
  padding: '12px 16px',
  textAlign: 'center',
  borderBottom: '1px solid #F3F4F6',
};
const buttonStyle = {
  padding: '6px 16px',
  background: 'var(--olo-color-primary, #6366F1)',
  color: '#fff',
  border: 'none',
  borderRadius: '6px',
  fontSize: '12px',
  fontWeight: 600,
  cursor: 'pointer',
};
</script>
