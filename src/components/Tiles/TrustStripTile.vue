<template>
  <div class="olo-tstrip" :style="rowStyle">
    <template v-for="(it, idx) in items" :key="idx">
      <span v-if="it.text || it.icon" class="olo-tstrip__item" style="display:inline-flex;align-items:center;gap:8px">
        <span v-if="it.icon" class="olo-tstrip__icon" :style="{ color: it.icon_color || '#10b981' }" v-html="resolveIcon(it.icon)"></span>
        <span v-if="it.text" v-html="it.text"></span>
      </span>
      <span v-if="s.separator_char && idx < items.length - 1" class="olo-tstrip__sep" :style="{ color: s.separator_color || '#9ca3af', opacity: .65 }">{{ s.separator_char }}</span>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/iconsLibrary.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { icon: 'check', icon_color: '#10b981', text: 'Licenza <b>GPL-v3</b>' },
    { icon: 'check', icon_color: '#10b981', text: '<b>WCAG 2.2 AA</b>' },
    { icon: 'check', icon_color: '#10b981', text: 'Hosting <b>a scelta tua</b>' },
    { icon: 'check', icon_color: '#10b981', text: 'Export <b>HTML/JSON</b> totale' },
    { icon: 'check', icon_color: '#10b981', text: 'Trento, <b>Italia 🇮🇹</b>' },
  ],
  separator_char: '·',
  separator_color: '#9ca3af',
  text_color: '#374151',
  text_size: 14,
  font_family: 'sans-serif',
  align: 'center',
  flow: 'wrap',
  gap: 24,
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);

const SERIF = "'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif";
const SANS  = "'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";
const MONO  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
const fmap  = { serif: SERIF, 'sans-serif': SANS, mono: MONO };

const alignMap = { left: 'flex-start', center: 'center', right: 'flex-end', 'space-between': 'space-between' };

const rowStyle = computed(() => ({
  display: 'flex',
  flexWrap: s.value.flow === 'nowrap' ? 'nowrap' : 'wrap',
  alignItems: 'center',
  justifyContent: alignMap[s.value.align] || 'center',
  gap: (s.value.gap || 24) + 'px',
  fontFamily: fmap[s.value.font_family] || SANS,
  fontSize: (s.value.text_size || 14) + 'px',
  color: s.value.text_color || '#374151',
  lineHeight: 1.5,
}));

function resolveIcon(name) {
  if (!name) return '';
  return iconsSvg[name] || '';
}
</script>

<style scoped>
.olo-tstrip__icon { display: inline-flex; align-items: center; }
.olo-tstrip__icon :deep(svg) { width: 0.9em; height: 0.9em; }
</style>
