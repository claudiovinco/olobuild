<template>
  <div class="mb-font-sans">
    <div v-for="group in groups" :key="group.title" :style="{ marginBottom:'20px' }">
      <h4 :style="{ fontSize:'14px', fontWeight:'700', color:'#1F2937', margin:'0 0 10px', textTransform:'uppercase', letterSpacing:'0.5px' }">{{ group.title }}</h4>
      <div :style="gridStyle">
        <div v-for="feat in group.items" :key="feat" :style="featStyle">
          <span :style="{ color: accent, fontSize:'14px', flexShrink:0 }">&#10003;</span>
          <span>{{ feat }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { columns: 3, accent_color: '' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');
const groups = [
  { title:'Ambienti', items:['Soggiorno open space', 'Cucina abitabile', 'Terrazzo panoramico', 'Ripostiglio', 'Cantina', 'Garage doppio'] },
  { title:'Comfort', items:['Riscaldamento a pavimento', 'Aria condizionata', 'Videocitofono', 'Ascensore', 'Porta blindata', 'Infissi triplo vetro'] },
];
const gridStyle = computed(() => ({ display:'grid', gridTemplateColumns:'repeat('+s.value.columns+',1fr)', gap:'6px 20px' }));
const featStyle = { display:'flex', alignItems:'center', gap:'8px', fontSize:'13px', color:'#374151', padding:'4px 0' };
</script>
