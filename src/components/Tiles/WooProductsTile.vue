<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><circle cx="7.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><circle cx="13.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><polyline points="0 2 3.2 4 5.3 12.5 16 12.5 18 6.5 8 6.5"/></svg></span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else>
      <div :style="gridStyle">
        <div
          v-for="i in cardCount"
          :key="i"
          :style="cardStyle"
        >
          <!-- Image placeholder -->
          <div :style="imgStyle">
            <svg :style="{ width: '32px', height: '32px', opacity: 0.3 }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="3" y="3" width="18" height="18" rx="2" />
              <circle cx="8.5" cy="8.5" r="1.5" />
              <path d="M21 15l-5-5L5 21" />
            </svg>
            <!-- Badge -->
            <div v-if="s.show_badge" :style="badgeStyle">-20%</div>
          </div>
          <!-- Info -->
          <div style="padding: 12px;">
            <div v-if="s.show_title" :style="{ color: s.title_color, fontWeight: 600, fontSize: '14px', marginBottom: '6px' }">
              Prodotto {{ i }}
            </div>
            <div v-if="s.show_rating" style="display:flex;gap:2px;margin-bottom:6px;">
              <svg v-for="star in 5" :key="star" width="14" height="14" viewBox="0 0 24 24" :fill="star <= 4 ? TOKENS.accent : TOKENS.border" stroke="none">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
              </svg>
            </div>
            <div v-if="s.show_price" style="display:flex;align-items:center;gap:6px;">
              <span :style="{ textDecoration: 'line-through', color: TOKENS.textFaint, fontSize: '12px' }">49,00</span>
              <span :style="{ color: saleColor, fontWeight: 700, fontSize: '15px' }">39,00</span>
            </div>
            <button
              v-if="s.show_add_to_cart"
              class="olo-woo-prod-btn"
              :style="btnStyle"
            >{{ t('Aggiungi') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  posts_per_page: '12',
  columns: '4',
  show_image: true,
  show_title: true,
  show_price: true,
  show_rating: false,
  show_add_to_cart: true,
  show_badge: true,
  image_ratio: '4-3',
  gap: '24',
  card_style: 'shadow',
  hover_effect: 'zoom',
  title_color: 'var(--olo-color-text, #374151)',
  price_color: 'var(--olo-color-text, #374151)',
  sale_color: '',           // '' ⇒ TOKENS.error (prezzo scontato = stato saldo)
  button_color: '',         // '' ⇒ TOKENS.onPrimary
  button_bg: 'var(--olo-color-primary, #e1474f)',
  badge_bg: '',             // '' ⇒ TOKENS.primary (badge = brand)
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const saleColor = computed(() => resolveColor(s.value.sale_color, TOKENS.error));

// WooCommerce is always "active" in the builder preview — real check is PHP-side
const wooActive = computed(() => true);

const cols = computed(() => Math.max(1, Math.min(6, parseInt(s.value.columns) || 4)));
const cardCount = computed(() => Math.min(parseInt(s.value.posts_per_page) || 4, cols.value * 2));

const ratioMap = {
  '1-1': '100%',
  '4-3': '75%',
  '3-4': '133%',
  '16-9': '56.25%',
  'auto': '75%',
};

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gap: (parseInt(s.value.gap) || 24) + 'px',
}));

const cardStyle = computed(() => {
  const st = {
    background: TOKENS.surface,
    borderRadius: '8px',
    overflow: 'hidden',
  };
  if (s.value.card_style === 'shadow') {
    st.boxShadow = '0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06)';
  } else if (s.value.card_style === 'border') {
    st.border = '1px solid ' + TOKENS.border;
  }
  return st;
});

const imgStyle = computed(() => ({
  position: 'relative',
  width: '100%',
  paddingTop: ratioMap[s.value.image_ratio] || '75%',
  background: TOKENS.surfaceAlt,
  color: TOKENS.textFaint,
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
}));

const badgeStyle = computed(() => ({
  position: 'absolute',
  top: '8px',
  left: '8px',
  background: resolveColor(s.value.badge_bg, TOKENS.primary),
  color: TOKENS.onPrimary,
  fontSize: '11px',
  fontWeight: 700,
  padding: '2px 8px',
  borderRadius: '4px',
}));

const btnStyle = computed(() => ({
  marginTop: '10px',
  width: '100%',
  padding: '8px 12px',
  background: resolveColor(s.value.button_bg, TOKENS.primary),
  color: resolveColor(s.value.button_color, TOKENS.onPrimary),
  border: 'none',
  borderRadius: '4px',
  fontSize: '13px',
  fontWeight: 600,
  cursor: 'pointer',
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
.olo-woo-prod-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
