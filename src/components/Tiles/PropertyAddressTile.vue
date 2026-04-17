<template>
  <div class="mb-font-sans">
    <div :style="{ display:'flex', alignItems:'flex-start', gap:'10px', marginBottom: s.show_map ? '16px' : '0' }">
      <span :style="{ fontSize:'20px', flexShrink:0 }">&#128205;</span>
      <div>
        <div :style="{ fontSize:'15px', fontWeight:'600', color:'#1F2937', marginBottom:'2px' }">Via Nazionale 45</div>
        <div :style="{ fontSize:'14px', color:'#6B7280' }">Mattarello, Trento (TN) 38123</div>
      </div>
    </div>
    <div v-if="s.show_map" :style="mapStyle">
      <div :style="{ position:'absolute', inset:0, display:'flex', alignItems:'center', justifyContent:'center' }">
        <span :style="{ fontSize:'28px' }">&#128506;</span>
      </div>
      <div :style="{ position:'absolute', inset:0, opacity:0.06, backgroundImage:'linear-gradient(#374151 1px, transparent 1px), linear-gradient(90deg, #374151 1px, transparent 1px)', backgroundSize:'40px 40px' }"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { show_map: true, map_height: 200, border_radius: 10, map_bg: '#E8F0FE' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const mapStyle = computed(() => ({ position:'relative', height: s.value.map_height+'px', background: s.value.map_bg, borderRadius: s.value.border_radius+'px', overflow:'hidden' }));
</script>
