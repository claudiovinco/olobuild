<template>
  <div class="mb-font-sans">
    <div :style="gridStyle">
      <div v-for="(grad, i) in gradients" :key="i" :style="thumbStyle(grad)">
        <div :style="{ position:'absolute', inset:0, display:'flex', alignItems:'center', justifyContent:'center', color:'rgba(255,255,255,0.5)', fontSize:'24px' }">&#128247;</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { columns: 3, gap: 8, height: 200, border_radius: 8 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const gradients = [
  'linear-gradient(135deg,#6366F140,#6366F190)',
  'linear-gradient(135deg,#10B98140,#10B98190)',
  'linear-gradient(135deg,#F5920040,#F5920090)',
  'linear-gradient(135deg,#EC489940,#EC489990)',
  'linear-gradient(135deg,#0891B240,#0891B290)',
  'linear-gradient(135deg,#8B5CF640,#8B5CF690)',
];
const gridStyle = computed(() => ({ display:'grid', gridTemplateColumns:'repeat('+s.value.columns+',1fr)', gap: s.value.gap+'px' }));
function thumbStyle(grad) {
  return { position:'relative', height: s.value.height+'px', background: grad, borderRadius: s.value.border_radius+'px', overflow:'hidden' };
}
</script>
