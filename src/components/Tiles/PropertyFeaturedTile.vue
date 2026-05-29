<template>
  <div class="mb-font-sans" :style="{ position:'relative' }">
    <div :style="scrollStyle">
      <div v-for="(p, i) in properties" :key="i" :style="cardStyle">
        <div :style="{ position:'relative', aspectRatio:'16 / 10', background:'var(--olo-color-surface-alt, #f6f7f9)', borderRadius: s.card_radius+'px '+s.card_radius+'px 0 0', overflow:'hidden' }">
          <div :style="{ position:'absolute', inset:0, display:'flex', alignItems:'center', justifyContent:'center', color:'var(--olo-color-text-faint, #94a3b8)' }">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
          </div>
          <span :style="badgeStyle">{{ p.badge }}</span>
        </div>
        <div :style="{ padding:'16px' }">
          <div :style="{ fontSize:'15px', fontWeight:'700', color:'var(--olo-color-text, #1f2937)', marginBottom:'4px' }">{{ p.title }}</div>
          <div :style="{ fontSize:'12px', color:'var(--olo-color-text-muted, #6b7280)', marginBottom:'8px' }">{{ p.location }}</div>
          <div :style="{ fontSize:'17px', fontWeight:'700', color: accent }">{{ p.price }}</div>
        </div>
      </div>
    </div>
    <button type="button" class="olo-pfeat-arrow" :style="arrowStyle('-16px', null)" aria-label="Precedente">&#8249;</button>
    <button type="button" class="olo-pfeat-arrow" :style="arrowStyle(null, '-16px')" aria-label="Successivo">&#8250;</button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { card_radius: 12, card_width: 300, gap: 20, accent_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e1474f)');
const properties = [
  { title:'Attico Luminoso', location:'Trento, Centro', price:'€ 410.000', badge:'Vendita' },
  { title:'Bilocale Arredato', location:'Rovereto, Borgo', price:'€ 750/mese', badge:'Affitto' },
  { title:'Villetta a Schiera', location:'Lavis, Residenziale', price:'€ 290.000', badge:'Vendita' },
];
const scrollStyle = computed(() => ({ display:'flex', gap: s.value.gap+'px', overflow:'hidden' }));
const cardStyle = computed(() => ({ minWidth: s.value.card_width+'px', flex:'0 0 '+s.value.card_width+'px', background:'var(--olo-color-surface, #fff)', borderRadius: s.value.card_radius+'px', border:'1px solid var(--olo-color-border, #e5e7eb)', boxShadow:'0 2px 8px rgba(0,0,0,0.08)', overflow:'hidden' }));
const badgeStyle = { position:'absolute', top:'10px', left:'10px', background:'rgba(22,38,61,0.75)', color:'#fff', padding:'4px 10px', borderRadius:'6px', fontSize:'11px', fontWeight:'600' };
function arrowStyle(left, right) {
  const pos = {};
  if (left) pos.left = left;
  if (right) pos.right = right;
  return { ...pos, position:'absolute', top:'50%', transform:'translateY(-50%)', width:'36px', height:'36px', borderRadius:'50%', border:'none', background:'var(--olo-color-surface, #fff)', boxShadow:'0 2px 8px rgba(0,0,0,0.15)', display:'flex', alignItems:'center', justifyContent:'center', fontSize:'20px', color:'var(--olo-color-text, #374151)', cursor:'pointer', zIndex:2 };
}
</script>

<style scoped>
.olo-pfeat-arrow:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 35%, transparent);
}
</style>
