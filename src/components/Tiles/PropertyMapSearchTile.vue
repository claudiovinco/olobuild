<template>
  <div class="mb-font-sans" style="display:grid; grid-template-columns:1fr 1fr; gap:16px; min-height:400px">
    <div :style="{ background:'var(--olo-color-surface-alt, #f6f7f9)', borderRadius:'12px', display:'flex', alignItems:'center', justifyContent:'center', color:'var(--olo-color-text-faint, #94a3b8)', fontSize:'14px', flexDirection:'column', gap:'8px' }">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
      <span>{{ t('Mappa interattiva') }}</span>
    </div>
    <div style="display:flex; flex-direction:column; gap:12px">
      <div style="display:flex; gap:8px; flex-wrap:wrap">
        <span style="padding:6px 14px; background:var(--olo-color-surface-alt, #f6f7f9); border-radius:6px; font-size:12px; color:var(--olo-color-text-muted, #64748b)">{{ t('Tipo') }}</span>
        <span style="padding:6px 14px; background:var(--olo-color-surface-alt, #f6f7f9); border-radius:6px; font-size:12px; color:var(--olo-color-text-muted, #64748b)">{{ t('Prezzo') }}</span>
        <span style="padding:6px 14px; background:var(--olo-color-surface-alt, #f6f7f9); border-radius:6px; font-size:12px; color:var(--olo-color-text-muted, #64748b)">{{ t('Camere') }}</span>
      </div>
      <div v-for="i in 3" :key="i" :style="{ padding:'14px', background:'var(--olo-color-surface, #fafafa)', borderRadius:'8px', border:'1px solid var(--olo-color-border, #e5e7eb)' }">
        <div style="height:12px; width:60%; background:var(--olo-color-border, #e2e8f0); border-radius:4px; margin-bottom:6px"></div>
        <div style="height:10px; width:40%; background:var(--olo-color-surface-alt, #f1f5f9); border-radius:4px"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
defineProps({ settings: { type: Object, default: () => ({}) } });
</script>
