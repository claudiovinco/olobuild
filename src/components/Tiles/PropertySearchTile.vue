<template>
  <div class="mb-font-sans" :style="wrapStyle">
    <div :style="formStyle">
      <div :style="fieldStyle" v-for="field in fields" :key="field.label">
        <label :style="{ fontSize:'11px', fontWeight:'600', color:'var(--olo-color-text-muted, #6b7280)', textTransform:'uppercase', letterSpacing:'0.5px', marginBottom:'4px', display:'block' }">{{ field.label }}</label>
        <div class="olo-psearch-field" :style="selectStyle" tabindex="0">
          <span>{{ field.value }}</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--olo-color-text-faint, #94a3b8)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </div>
      </div>
      <div :style="{ display:'flex', alignItems: s.layout==='vertical' ? 'stretch' : 'flex-end' }">
        <button type="button" class="olo-psearch-btn" :style="btnStyle">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" style="margin-right:7px"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          {{ t('Cerca') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { layout:'horizontal', bg:'', border_radius:12, shadow:'md', padding:24, accent_color:'', btn_radius:8 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e1474f)');
const fields = [
  { label:'Tipo', value:'Vendita' }, { label:'Tipologia', value:'Appartamento' },
  { label:'Città', value:'Trento' }, { label:'Prezzo max', value:'€ 500.000' },
];
const shadowMap = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.1)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 8px 24px rgba(0,0,0,0.15)' };
const wrapStyle = computed(() => ({ background: s.value.bg || 'var(--olo-color-surface, #ffffff)', borderRadius: s.value.border_radius+'px', boxShadow: shadowMap[s.value.shadow]||shadowMap.md, padding: s.value.padding+'px' }));
const formStyle = computed(() => {
  if (s.value.layout === 'vertical') return { display:'flex', flexDirection:'column', gap:'16px' };
  return { display:'flex', gap:'16px', alignItems:'flex-end' };
});
const fieldStyle = computed(() => ({ flex:'1' }));
const selectStyle = computed(() => ({ display:'flex', justifyContent:'space-between', alignItems:'center', gap:'8px', padding:'10px 14px', border:'1px solid var(--olo-color-border, #e5e7eb)', borderRadius:'8px', fontSize:'14px', color:'var(--olo-color-text, #374151)', background:'var(--olo-color-surface-alt, #f6f7f9)', cursor:'pointer' }));
const btnStyle = computed(() => ({ display:'inline-flex', alignItems:'center', justifyContent:'center', padding:'10px 28px', border:'none', background: accent.value, color:'var(--olo-color-primary-contrast, #fff)', borderRadius: s.value.btn_radius+'px', fontSize:'14px', fontWeight:'700', cursor:'pointer', whiteSpace:'nowrap' }));
</script>

<style scoped>
/* a11y: bordo attivo primary sui campi (focus tastiera) + anello sul bottone */
.olo-psearch-field:focus,
.olo-psearch-field:focus-visible {
  outline: none;
  border-color: var(--olo-color-primary, #e1474f);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 18%, transparent);
}
.olo-psearch-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
