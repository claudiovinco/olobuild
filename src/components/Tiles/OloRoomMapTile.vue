<template>
  <div :style="wrapStyle">
    <div :style="mapStyle">
      <div style="position:absolute;inset:0;background:linear-gradient(135deg,#e0f2fe,#bae6fd 40%,#7dd3fc 70%,#38bdf8);opacity:.6"></div>
      <div style="position:absolute;inset:0;opacity:.15;background:repeating-linear-gradient(0deg,transparent,transparent 20px,#0284c7 20px,#0284c7 21px),repeating-linear-gradient(90deg,transparent,transparent 20px,#0284c7 20px,#0284c7 21px)"></div>
      <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-100%);z-index:2">
        <svg width="32" height="42" viewBox="0 0 32 42"><path d="M16 0C7.2 0 0 7.2 0 16c0 12 16 26 16 26s16-14 16-26C32 7.2 24.8 0 16 0z" :fill="markerColor"/><circle cx="16" cy="14" r="6" fill="#fff"/></svg>
      </div>
      <div :style="{ position:'absolute', bottom:'8px', left:'8px', background:'rgba(255,255,255,.9)', padding:'3px 8px', borderRadius:'4px', fontSize:'10px', color: TOKENS.text, zIndex:2 }">Zoom: {{ s.zoom || 16 }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { height: 280, zoom: 16 };
const s = computed(() => ({ ...defaults, ...props.settings }));
// Pin token-first: usa il primario del brand se l'utente non sceglie un colore.
const markerColor = computed(() => resolveColor(s.value.marker_color, TOKENS.primary));
const wrapStyle = computed(() => ({ borderRadius: '10px', overflow: 'hidden' }));
const mapStyle = computed(() => ({ position: 'relative', height: Math.min(parseInt(s.value.height) || 280, 180) + 'px', background: '#dbeafe' }));
</script>
