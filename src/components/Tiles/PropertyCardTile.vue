<template>
  <a class="olo-pcard mb-font-sans" href="#" @click.prevent :style="cardStyle">
    <div :style="imageStyle">
      <div :style="{ position:'absolute', inset:0, display:'flex', alignItems:'center', justifyContent:'center', color:'var(--olo-color-text-faint, #94a3b8)' }">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
      </div>
      <span :style="badgeStyle">{{ t('Vendita') }}</span>
    </div>
    <div :style="{ padding: s.padding+'px' }">
      <div :style="{ fontSize: s.title_size+'px', fontWeight:'700', color:'var(--olo-color-text, #1f2937)', marginBottom:'4px' }">{{ t('Villa Panoramica') }}</div>
      <div :style="{ fontSize:'13px', color:'var(--olo-color-text-muted, #6b7280)', marginBottom:'12px' }">{{ t('Pergine Valsugana, Lago') }}</div>
      <div :style="{ display:'flex', gap:'16px', fontSize:'13px', color:'var(--olo-color-text-muted, #6b7280)', marginBottom:'14px' }">
        <span>{{ t('220 m&sup2;') }}</span><span>{{ t('8 locali') }}</span><span>{{ t('4 camere') }}</span><span>{{ t('3 bagni') }}</span>
      </div>
      <div :style="{ display:'flex', justifyContent:'space-between', alignItems:'center' }">
        <span :style="{ fontSize:'20px', fontWeight:'700', color: accent }">{{ t('&euro; 520.000') }}</span>
        <span :style="btnStyle">{{ t('Scopri') }}</span>
      </div>
    </div>
  </a>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { SHADOW } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { max_width: 380, border_radius: 12, shadow: 'md', padding: 20, title_size: 18, accent_color: '', btn_radius: 8 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e1474f)');
const shadowMap = SHADOW;
const cardStyle = computed(() => ({ display:'block', textDecoration:'none', maxWidth: s.value.max_width+'px', background:'var(--olo-color-surface, #fff)', borderRadius: s.value.border_radius+'px', boxShadow: shadowMap[s.value.shadow]||shadowMap.md, overflow:'hidden', border:'1px solid var(--olo-color-border, #e5e7eb)' }));
const imageStyle = computed(() => ({ position:'relative', aspectRatio:'3 / 2', background:'var(--olo-color-surface-alt, #f6f7f9)', borderRadius: s.value.border_radius+'px '+s.value.border_radius+'px 0 0', overflow:'hidden' }));
const badgeStyle = computed(() => ({ position:'absolute', top:'12px', left:'12px', background: accent.value, color:'var(--olo-color-primary-contrast, #fff)', padding:'4px 12px', borderRadius:'6px', fontSize:'11px', fontWeight:'700', textTransform:'uppercase' }));
const btnStyle = computed(() => ({ padding:'8px 18px', background: accent.value, color:'var(--olo-color-primary-contrast, #fff)', borderRadius: s.value.btn_radius+'px', fontSize:'13px', fontWeight:'600', cursor:'pointer' }));
</script>

<style scoped>
.olo-pcard:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 35%, transparent);
}
</style>
