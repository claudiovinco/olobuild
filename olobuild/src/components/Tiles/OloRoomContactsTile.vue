<template>
  <div :style="wrapStyle">
    <h3 :style="{fontSize:'16px',fontWeight:'700',color:'var(--olo-color-text, #374151)',margin:'0 0 10px'}" data-olo-editable="title">{{ s.title || 'Contatti' }}</h3>
    <div style="display:flex;flex-direction:column;gap:6px;font-size:13px;color:#374151">
      <div style="display:flex;align-items:center;gap:8px">
        <span style="color:#1e87f0;font-size:15px">&#9742;</span>
        <span>+39 0464 452 111</span>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <span style="color:#1e87f0;font-size:15px">&#9993;</span>
        <span>info@comune.rovereto.tn.it</span>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <span style="color:#1e87f0;font-size:15px">&#128438;</span>
        <span>+39 0464 452 199</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { style: 'card', title: 'Contatti' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const wrapStyle = computed(() => s.value.style === 'card' ? { background: '#f9fafb', border: '1px solid #e5e7eb', borderRadius: '10px', padding: '16px' } : {});
</script>
