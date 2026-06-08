<template>
  <div class="mb-font-sans">
    <div v-if="s.filter_bar" :style="{ display:'flex', gap:'8px', marginBottom: s.gap+'px', flexWrap:'wrap' }">
      <button v-for="cat in categories" :key="cat.label" type="button" class="olo-pgrid-chip"
            :style="{ padding:'8px 18px', borderRadius:'20px', fontSize:'13px', fontWeight:'600', cursor:'pointer', border:'none', fontFamily:'inherit',
                       background: cat.active ? accent : 'var(--olo-color-surface-alt, #f6f7f9)', color: cat.active ? 'var(--olo-color-primary-contrast, #fff)' : 'var(--olo-color-text, #374151)' }">
        {{ cat.label }}
      </button>
    </div>
    <div :style="gridStyle">
      <div v-for="(p, i) in properties" :key="i" :style="cardStyle">
        <div :style="{ position:'relative', aspectRatio:'16 / 10', background:'var(--olo-color-surface-alt, #f6f7f9)', borderRadius: s.card_radius+'px '+s.card_radius+'px 0 0', overflow:'hidden' }">
          <div :style="{ position:'absolute', inset:0, display:'flex', alignItems:'center', justifyContent:'center', color:'var(--olo-color-text-faint, #94a3b8)' }">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
          </div>
          <span :style="badgeStyle(p.badge)">{{ p.badge }}</span>
        </div>
        <div :style="{ padding:'16px' }">
          <div :style="{ fontSize:'16px', fontWeight:'700', color:'var(--olo-color-text, #1f2937)', marginBottom:'4px' }">{{ p.title }}</div>
          <div :style="{ fontSize:'13px', color:'var(--olo-color-text-muted, #6b7280)', marginBottom:'10px' }">{{ p.location }}</div>
          <div :style="{ display:'flex', gap:'12px', fontSize:'12px', color:'var(--olo-color-text-muted, #6b7280)', marginBottom:'12px' }">
            <span>{{ p.mq }} m&sup2;</span><span>{{ p.locali }} locali</span><span>{{ p.camere }} cam</span><span>{{ p.bagni }} bagni</span>
          </div>
          <div :style="{ display:'flex', justifyContent:'space-between', alignItems:'center' }">
            <span :style="{ fontSize:'18px', fontWeight:'700', color: accent }">{{ p.price }}</span>
            <span :style="btnStyle">{{ t('Dettagli') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { SHADOW } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { columns: 3, gap: 24, card_radius: 12, card_shadow: 'sm', filter_bar: true, accent_color: '', btn_bg: '', btn_color: '', btn_radius: 8 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e1474f)');
const shadowMap = SHADOW;
const categories = [ { label:'Tutti', active:true }, { label:'Appartamenti', active:false }, { label:'Ville', active:false }, { label:'Commerciale', active:false } ];
const properties = [
  { title:'Trilocale Centro Storico', location:'Trento, Centro', mq:95, locali:4, camere:2, bagni:1, price:'€ 325.000', badge:'Vendita' },
  { title:'Bilocale con Terrazzo', location:'Rovereto, San Giorgio', mq:65, locali:3, camere:1, bagni:1, price:'€ 850/mese', badge:'Affitto' },
  { title:'Villa Panoramica', location:'Pergine, Lago', mq:220, locali:8, camere:4, bagni:3, price:'€ 520.000', badge:'Vendita' },
];
const gridStyle = computed(() => ({ display:'grid', gridTemplateColumns:'repeat('+s.value.columns+',1fr)', gap: s.value.gap+'px' }));
const cardStyle = computed(() => ({ background:'var(--olo-color-surface, #fff)', borderRadius: s.value.card_radius+'px', border:'1px solid var(--olo-color-border, #e5e7eb)', boxShadow: shadowMap[s.value.card_shadow]||shadowMap.sm, overflow:'hidden' }));
function badgeStyle(label) {
  const bg = label === 'Affitto' ? 'var(--olo-color-success, #15803d)' : 'var(--olo-color-primary, #e1474f)';
  return { position:'absolute', top:'12px', left:'12px', background: bg, color:'var(--olo-color-primary-contrast, #fff)', padding:'4px 12px', borderRadius:'6px', fontSize:'11px', fontWeight:'700', textTransform:'uppercase' };
}
const btnStyle = computed(() => ({ padding:'8px 16px', background: s.value.btn_bg || accent.value, color: s.value.btn_color || 'var(--olo-color-primary-contrast, #fff)', borderRadius: s.value.btn_radius+'px', fontSize:'13px', fontWeight:'600', cursor:'pointer' }));
</script>

<style scoped>
.olo-pgrid-chip:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
