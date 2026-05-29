<template>
  <div class="mb-font-sans" :style="{ textAlign: s.alignment }">
    <div :style="{ fontSize: s.size+'px', fontWeight:'800', color: s.price_color || accent, lineHeight:'1.2' }">{{ t('&euro; 325.000') }}</div>
    <div v-if="s.show_second_price" :style="{ fontSize:'16px', color:'var(--olo-color-text-faint, #94a3b8)', textDecoration:'line-through', marginTop:'4px' }">{{ t('&euro; 350.000') }}</div>
    <div :style="{ marginTop:'10px', display:'inline-flex', gap:'12px', alignItems:'center', flexWrap:'wrap' }">
      <span :style="badgeStyle">{{ t('Prezzo trattabile') }}</span>
      <span :style="{ fontSize:'14px', color:'var(--olo-color-text-muted, #6b7280)' }">{{ t('Spese cond. &euro; 120/mese') }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { alignment: 'left', size: 32, price_color: '', accent_color: '', show_second_price: true };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e1474f)');
const badgeStyle = computed(() => ({ display:'inline-block', padding:'4px 14px', background: `color-mix(in srgb, ${accent.value} 12%, #fff)`, color: accent.value, borderRadius:'20px', fontSize:'12px', fontWeight:'600' }));
</script>
