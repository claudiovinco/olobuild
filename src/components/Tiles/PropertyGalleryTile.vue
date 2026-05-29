<template>
  <div class="mb-font-sans">
    <div :style="gridStyle">
      <div v-for="i in count" :key="i" :style="thumbStyle">
        <div :style="{ position:'absolute', inset:0, display:'flex', alignItems:'center', justifyContent:'center', color:'var(--olo-color-text-faint, #94a3b8)' }">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { columns: 3, gap: 8, height: 200, border_radius: 8 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const count = 6;
const gridStyle = computed(() => ({ display:'grid', gridTemplateColumns:'repeat('+s.value.columns+',1fr)', gap: s.value.gap+'px' }));
const thumbStyle = computed(() => ({ position:'relative', height: s.value.height+'px', background:'var(--olo-color-surface-alt, #f6f7f9)', borderRadius: s.value.border_radius+'px', overflow:'hidden', objectFit:'cover' }));
</script>
