<template>
  <div class="mb-font-sans">
    <div :style="videoStyle">
      <div :style="{ position:'absolute', inset:0, display:'flex', alignItems:'center', justifyContent:'center' }">
        <div :style="playBtnStyle">
          <div :style="{ width:0, height:0, borderTop:'14px solid transparent', borderBottom:'14px solid transparent', borderLeft:'22px solid #fff', marginLeft:'4px' }"></div>
        </div>
      </div>
      <div :style="{ position:'absolute', bottom:'12px', left:'12px', display:'flex', gap:'8px', alignItems:'center' }">
        <div :style="{ width:'40px', height:'4px', borderRadius:'2px', background:'rgba(255,255,255,0.3)' }">
          <div :style="{ width:'30%', height:'100%', borderRadius:'2px', background:'#fff' }"></div>
        </div>
        <span :style="{ fontSize:'12px', color:'rgba(255,255,255,0.7)' }">0:00 / 2:35</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { height: 360, border_radius: 12 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const videoStyle = computed(() => ({ position:'relative', height: s.value.height+'px', aspectRatio:'16 / 9', maxWidth:'100%', background:'var(--olo-color-dark, #16263d)', borderRadius: s.value.border_radius+'px', overflow:'hidden' }));
const playBtnStyle = { width:'64px', height:'64px', borderRadius:'50%', background:'var(--olo-color-primary, #e1474f)', display:'flex', alignItems:'center', justifyContent:'center', cursor:'pointer', boxShadow:'0 4px 16px rgba(0,0,0,0.3)' };
</script>
