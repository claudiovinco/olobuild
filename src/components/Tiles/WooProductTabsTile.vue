<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><circle cx="7.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><circle cx="13.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><polyline points="0 2 3.2 4 5.3 12.5 16 12.5 18 6.5 8 6.5"/></svg></span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else>
      <!-- Tab nav -->
      <div :style="tabNavStyle">
        <button
          v-for="(tab, idx) in visibleTabs"
          :key="tab.key"
          :style="tabBtnStyle(idx === activeTab)"
          @click="activeTab = idx"
        >{{ tab.label }}</button>
      </div>
      <!-- Tab content -->
      <div :style="{ color: s.text_color, fontSize: '14px', lineHeight: '1.7', padding: '4px 0' }">
        <div v-if="visibleTabs[activeTab]?.key === 'description'">
          <p style="margin:0 0 12px;">{{ t('Questa è la descrizione completa del prodotto. Include tutti i dettagli, le specifiche tecniche e le informazioni che il cliente deve conoscere prima dell\'acquisto.') }}</p>
          <p style="margin:0;">{{ t('Il prodotto è realizzato con materiali di alta qualità e offre prestazioni eccellenti in ogni condizione d\'uso.') }}</p>
        </div>
        <div v-else-if="visibleTabs[activeTab]?.key === 'additional'">
          <table :style="tableStyle">
            <tr v-for="row in infoRows" :key="row.label">
              <th :style="thStyle">{{ row.label }}</th>
              <td :style="tdStyle">{{ row.value }}</td>
            </tr>
          </table>
        </div>
        <div v-else-if="visibleTabs[activeTab]?.key === 'reviews'">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div style="display:flex;gap:2px;">
              <svg v-for="star in 5" :key="star" width="16" height="16" viewBox="0 0 24 24" :fill="star <= 4 ? TOKENS.accent : TOKENS.border" stroke="none">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
              </svg>
            </div>
            <span :style="{ fontSize: '13px', color: TOKENS.textSoft }">{{ t('Basato su 12 recensioni') }}</span>
          </div>
          <div v-for="review in mockReviews" :key="review.name" :style="reviewStyle">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
              <strong :style="{ fontSize: '14px', color: s.text_color }">{{ review.name }}</strong>
              <div style="display:flex;gap:1px;">
                <svg v-for="star in 5" :key="star" width="12" height="12" viewBox="0 0 24 24" :fill="star <= review.rating ? TOKENS.accent : TOKENS.border" stroke="none">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
              </div>
            </div>
            <p :style="{ margin: 0, fontSize: '13px', color: TOKENS.textSoft }">{{ review.text }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { t } from '@/i18n';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  show_description: true,
  show_additional: true,
  show_reviews: true,
  tab_style: 'underline',
  active_color: 'var(--olo-color-primary, #e1474f)',
  text_color: '',           // '' ⇒ TOKENS.text
  border_color: '',         // '' ⇒ TOKENS.border
};
const sRaw = computed(() => ({ ...defaults, ...props.settings }));
const s = computed(() => ({
  ...sRaw.value,
  text_color: resolveColor(sRaw.value.text_color, TOKENS.text),
  border_color: resolveColor(sRaw.value.border_color, TOKENS.border),
  active_color: resolveColor(sRaw.value.active_color, TOKENS.primary),
}));

const wooActive = computed(() => true);
const activeTab = ref(0);

const allTabs = [
  { key: 'description', label: 'Descrizione' },
  { key: 'additional', label: 'Informazioni aggiuntive' },
  { key: 'reviews', label: 'Recensioni (12)' },
];

const visibleTabs = computed(() => {
  const tabs = [];
  if (s.value.show_description) tabs.push(allTabs[0]);
  if (s.value.show_additional) tabs.push(allTabs[1]);
  if (s.value.show_reviews) tabs.push(allTabs[2]);
  return tabs;
});

const infoRows = [
  { label: 'Peso', value: '0.5 kg' },
  { label: 'Dimensioni', value: '20 x 15 x 10 cm' },
  { label: 'Colore', value: 'Nero, Bianco, Grigio' },
  { label: 'Materiale', value: '100% Cotone' },
];

const mockReviews = [
  { name: 'Mario R.', rating: 5, text: 'Prodotto eccellente, qualità superiore alle aspettative.' },
  { name: 'Laura B.', rating: 4, text: 'Buon rapporto qualità-prezzo, spedizione veloce.' },
];

const tabNavStyle = computed(() => {
  const base = { display: 'flex', marginBottom: '24px' };
  if (s.value.tab_style === 'underline') {
    base.borderBottom = `2px solid ${s.value.border_color}`;
    base.gap = '0';
  } else if (s.value.tab_style === 'pills') {
    base.gap = '8px';
  } else if (s.value.tab_style === 'boxed') {
    base.border = `1px solid ${s.value.border_color}`;
    base.borderRadius = '8px';
    base.overflow = 'hidden';
    base.gap = '0';
  }
  return base;
});

const tabBtnStyle = (isActive) => {
  const base = {
    padding: '12px 20px',
    background: 'none',
    border: 'none',
    cursor: 'pointer',
    fontSize: '14px',
    fontWeight: '600',
    color: s.value.text_color,
    transition: 'all 0.2s ease',
    whiteSpace: 'nowrap',
  };

  if (s.value.tab_style === 'underline') {
    base.marginBottom = '-2px';
    base.borderBottom = `2px solid transparent`;
    if (isActive) {
      base.color = s.value.active_color;
      base.borderBottom = `2px solid ${s.value.active_color}`;
    }
  } else if (s.value.tab_style === 'pills') {
    base.borderRadius = '6px';
    if (isActive) {
      base.background = s.value.active_color;
      base.color = TOKENS.onPrimary;
    }
  } else if (s.value.tab_style === 'boxed') {
    base.flex = '1';
    base.textAlign = 'center';
    base.borderRight = `1px solid ${s.value.border_color}`;
    if (isActive) {
      base.background = s.value.active_color;
      base.color = '#fff';
    }
  }
  return base;
};

const tableStyle = { width: '100%', borderCollapse: 'collapse' };
const thStyle = computed(() => ({
  padding: '10px 12px',
  borderBottom: `1px solid ${s.value.border_color}`,
  textAlign: 'left',
  fontWeight: '600',
  fontSize: '14px',
  width: '30%',
  color: s.value.text_color,
}));
const tdStyle = computed(() => ({
  padding: '10px 12px',
  borderBottom: `1px solid ${s.value.border_color}`,
  fontSize: '14px',
  color: s.value.text_color,
}));
const reviewStyle = computed(() => ({
  padding: '14px 0',
  borderBottom: `1px solid ${s.value.border_color}`,
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
