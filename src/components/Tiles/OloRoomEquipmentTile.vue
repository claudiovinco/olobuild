<template>
  <div>
    <h3 :style="{fontSize:'16px',fontWeight:'700',color:'var(--olo-color-text, #374151)',margin:'0 0 10px'}">Dotazioni</h3>
    <div :style="gridStyle">
      <div v-for="item in equipment" :key="item" :style="itemStyle">
        <span style="color:#1e87f0;font-size:14px;flex-shrink:0">&#10003;</span>
        <span>{{ item }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { style: 'card', columns: 3 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const equipment = ['Videoproiettore', 'Microfono', 'Wi-Fi', 'Lavagna', 'Condizionatore', 'Prese elettriche'];
const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: `repeat(${parseInt(s.value.columns)||3}, 1fr)`, gap: '8px 16px' }));
const itemStyle = computed(() => ({ display: 'flex', alignItems: 'center', gap: '6px', fontSize: '13px', color: '#374151', padding: s.value.style === 'card' ? '6px 10px' : '3px 0', background: s.value.style === 'card' ? '#f3f4f6' : 'transparent', borderRadius: '6px' }));
</script>
