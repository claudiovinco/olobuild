<template>
  <div class="mb-font-sans" :style="{ textAlign: s.alignment }">
    <div :style="{ fontSize: s.size+'px', fontWeight:'800', color: s.price_color || accent, lineHeight:'1.2' }">&euro; 325.000</div>
    <div v-if="s.show_second_price" :style="{ fontSize:'16px', color:'#9CA3AF', textDecoration:'line-through', marginTop:'4px' }">&euro; 350.000</div>
    <div :style="{ marginTop:'10px', display:'inline-flex', gap:'12px', alignItems:'center', flexWrap:'wrap' }">
      <span :style="badgeStyle">Prezzo trattabile</span>
      <span :style="{ fontSize:'14px', color:'#6B7280' }">Spese cond. &euro; 120/mese</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { alignment: 'left', size: 32, price_color: '', accent_color: '', show_second_price: true };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');
const badgeStyle = computed(() => ({ display:'inline-block', padding:'4px 14px', background: accent.value + '18', color: accent.value, borderRadius:'20px', fontSize:'12px', fontWeight:'600' }));
</script>
