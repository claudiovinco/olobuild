<template>
  <div :style="wrapStyle">
    <h3 style="font-size:16px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 12px">Tariffa</h3>
    <div style="text-align:center;padding:10px 0">
      <div style="font-size:32px;font-weight:700;color:#1e87f0">&euro; 25,00</div>
      <div style="font-size:14px;color:#6b7280;margin-top:2px">/ ora</div>
    </div>
    <div style="font-size:12px;color:#9ca3af;margin-top:8px;padding-top:8px;border-top:1px solid #e5e7eb">
      Tariffa ridotta per enti pubblici. IVA inclusa.
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { style: 'card' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const wrapStyle = computed(() => s.value.style === 'card' ? { background: '#f9fafb', border: '1px solid #e5e7eb', borderRadius: '10px', padding: '16px' } : {});
</script>
