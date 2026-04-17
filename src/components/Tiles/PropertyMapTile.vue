<template>
  <div class="mb-font-sans">
    <div :style="mapStyle">
      <div :style="{ position:'absolute', inset:0, display:'flex', flexDirection:'column', alignItems:'center', justifyContent:'center', gap:'12px' }">
        <div :style="{ fontSize:'36px' }">&#128205;</div>
        <span :style="{ background:'rgba(0,0,0,0.6)', color:'#fff', padding:'6px 16px', borderRadius:'20px', fontSize:'13px', fontWeight:'600' }">Mappa interattiva</span>
        <div :style="{ display:'flex', gap:'16px', marginTop:'8px' }">
          <div v-for="pin in pins" :key="pin.label" :style="pinStyle">
            <span :style="{ width:'8px', height:'8px', borderRadius:'50%', background: pin.color, display:'inline-block' }"></span>
            <span>{{ pin.label }}</span>
          </div>
        </div>
      </div>
      <div :style="{ position:'absolute', inset:0, opacity:0.08, backgroundImage:'linear-gradient(#374151 1px, transparent 1px), linear-gradient(90deg, #374151 1px, transparent 1px)', backgroundSize:'60px 60px' }"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { height: 400, border_radius: 12, bg_color: '#E8F0FE' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const pins = [ { label:'Vendita', color:'#6366F1' }, { label:'Affitto', color:'#10B981' }, { label:'Nuovo', color:'#F59E0B' } ];
const mapStyle = computed(() => ({ position:'relative', height: s.value.height+'px', background: s.value.bg_color, borderRadius: s.value.border_radius+'px', overflow:'hidden' }));
const pinStyle = { display:'flex', alignItems:'center', gap:'4px', fontSize:'12px', color:'#374151', fontWeight:'500' };
</script>
