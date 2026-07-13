<template>
  <nav class="olo-coverdots-preview" :style="cssVars" aria-label="Sezioni della pagina">
    <button v-for="(d, i) in dots" :key="i" type="button" :title="d.label || undefined"
            :class="{ on: i === 0 }" :style="d.color ? { '--dc': d.color } : {}">
      <span></span>
    </button>
  </nav>
</template>

<script setup>
import { computed } from 'vue';
import def from '@/config/elements/coverdots.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...def.defaults, ...props.settings }));

// Nel canvas il gruppo cover-h non esiste: anteprima dagli items (o 3 segnaposto).
const dots = computed(() => {
  const items = Array.isArray(s.value.items) ? s.value.items : [];
  return items.length ? items : [{}, {}, {}];
});

const cssVars = computed(() => ({
  '--cd-size': `${s.value.dot_size || 34}px`,
  '--cd-gap': `${s.value.dot_gap ?? 4}px`,
  '--cd-inner': `${s.value.dot_inner || 9}px`,
  '--cd-border': s.value.border_color || 'var(--olo-color-border, rgba(128,128,128,.35))',
  '--cd-bg': s.value.dot_bg || 'transparent',
  '--cd-color': s.value.dot_color || 'var(--olo-color-primary, #e1474f)',
}));
</script>

<style scoped>
.olo-coverdots-preview{display:inline-flex;align-items:center;gap:var(--cd-gap);}
.olo-coverdots-preview button{width:var(--cd-size);height:var(--cd-size);border-radius:50%;
  border:1px solid var(--cd-border);background:var(--cd-bg);cursor:pointer;padding:0;
  display:flex;align-items:center;justify-content:center;transition:all .18s;}
.olo-coverdots-preview button span{width:var(--cd-inner);height:var(--cd-inner);border-radius:50%;
  background:var(--dc,var(--cd-color));display:block;opacity:.55;transition:all .18s;}
.olo-coverdots-preview button:hover span,.olo-coverdots-preview button.on span{opacity:1;}
.olo-coverdots-preview button:focus-visible{outline:2px solid var(--dc,var(--cd-color));outline-offset:2px;}
</style>
