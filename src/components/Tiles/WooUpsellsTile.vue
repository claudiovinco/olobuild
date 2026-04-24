<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon">{{ t('&#x1F6D2;') }}</span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else>
      <h2 v-if="s.show_heading" :style="headingStyle" data-olo-editable="title_text">{{ s.title_text }}</h2>
      <div :style="gridStyle">
        <div
          v-for="i in cardCount"
          :key="i"
          :style="cardStyle"
        >
          <!-- Image placeholder -->
          <div v-if="s.show_image" :style="imgStyle">
            <svg :style="{ width: '32px', height: '32px', opacity: 0.3 }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="3" y="3" width="18" height="18" rx="2" />
              <circle cx="8.5" cy="8.5" r="1.5" />
              <path d="M21 15l-5-5L5 21" />
            </svg>
          </div>
          <!-- Info -->
          <div style="padding: 12px 14px;">
            <div v-if="s.show_title" :style="{ color: s.title_color, fontWeight: 600, fontSize: '14px', marginBottom: '6px', lineHeight: '1.3' }">
              Prodotto suggerito {{ i }}
            </div>
            <div v-if="s.show_price" :style="{ color: s.price_color, fontWeight: 700, fontSize: '15px' }">
              &euro;{{ (24.90 + i * 15).toFixed(2) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  columns: '4',
  show_image: true,
  show_title: true,
  show_price: true,
  card_style: 'shadow',
  gap: '24',
  title_text: 'Ti potrebbe interessare',
  show_heading: true,
  title_color: 'var(--olo-color-text, #374151)',
  price_color: 'var(--olo-color-text, #374151)',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const wooActive = computed(() => true);

const cols = computed(() => Math.max(1, Math.min(6, parseInt(s.value.columns) || 4)));
const cardCount = computed(() => Math.min(cols.value, 4));

const headingStyle = computed(() => ({
  fontSize: '22px',
  fontWeight: '700',
  color: s.value.title_color,
  margin: '0 0 20px',
}));

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gap: (parseInt(s.value.gap) || 24) + 'px',
}));

const cardStyle = computed(() => {
  const st = {
    background: '#fff',
    borderRadius: '8px',
    overflow: 'hidden',
    transition: 'box-shadow 0.3s ease',
  };
  if (s.value.card_style === 'shadow') {
    st.boxShadow = '0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06)';
  } else if (s.value.card_style === 'border') {
    st.border = '1px solid #E5E7EB';
  }
  return st;
});

const imgStyle = computed(() => ({
  position: 'relative',
  width: '100%',
  paddingTop: '100%',
  background: '#F3F4F6',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
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
