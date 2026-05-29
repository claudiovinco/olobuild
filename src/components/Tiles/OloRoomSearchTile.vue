<template>
  <div :style="{ background: TOKENS.surfaceAlt, border:'1px solid ' + TOKENS.border, borderRadius:'12px', padding:'16px' }">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
      <div :style="{ flex:'1', minWidth:'200px', background: TOKENS.surface, border:'1px solid ' + TOKENS.border, borderRadius:'8px', padding:'8px 12px', fontSize:'13px', color: TOKENS.textFaint }">{{ t('Cerca una sala...') }}</div>
      <div :style="{ background: TOKENS.surface, border:'1px solid ' + TOKENS.border, borderRadius:'8px', padding:'8px 12px', fontSize:'13px', color: TOKENS.textSoft, minWidth:'120px' }">{{ t('Tipo sala &#9662;') }}</div>
      <div :style="{ background: TOKENS.surface, border:'1px solid ' + TOKENS.border, borderRadius:'8px', padding:'8px 12px', fontSize:'13px', color: TOKENS.textSoft, minWidth:'120px' }">{{ t('Zona &#9662;') }}</div>
      <div :style="{ background: TOKENS.surface, border:'1px solid ' + TOKENS.border, borderRadius:'8px', padding:'8px 12px', fontSize:'13px', color: TOKENS.textSoft, minWidth:'100px' }">{{ t('Capienza &#9662;') }}</div>
      <button type="button" class="olo-search-btn" :style="{ background: TOKENS.primary, color: TOKENS.onPrimary, padding:'8px 20px', borderRadius:'8px', fontSize:'13px', fontWeight:'600', border:'none', cursor:'pointer' }">{{ t('Cerca') }}</button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
</script>

<style scoped>
.olo-search-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
