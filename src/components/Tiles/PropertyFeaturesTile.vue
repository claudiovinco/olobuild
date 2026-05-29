<template>
  <div class="mb-font-sans">
    <div v-for="group in groups" :key="group.title" :style="{ marginBottom:'20px' }">
      <h4 :style="{ fontSize:'14px', fontWeight:'700', color:'var(--olo-color-text, #1f2937)', margin:'0 0 10px', textTransform:'uppercase', letterSpacing:'0.5px' }">{{ group.title }}</h4>
      <div :style="gridStyle">
        <div v-for="feat in group.items" :key="feat" :style="featStyle">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" :stroke="accent" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M20 6 9 17l-5-5"/></svg>
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
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e1474f)');
const groups = [
  { title:'Ambienti', items:['Soggiorno open space', 'Cucina abitabile', 'Terrazzo panoramico', 'Ripostiglio', 'Cantina', 'Garage doppio'] },
  { title:'Comfort', items:['Riscaldamento a pavimento', 'Aria condizionata', 'Videocitofono', 'Ascensore', 'Porta blindata', 'Infissi triplo vetro'] },
];
const gridStyle = computed(() => ({ display:'grid', gridTemplateColumns:'repeat('+s.value.columns+',1fr)', gap:'6px 20px' }));
const featStyle = { display:'flex', alignItems:'center', gap:'8px', fontSize:'13px', color:'var(--olo-color-text, #374151)', padding:'4px 0' };
</script>
