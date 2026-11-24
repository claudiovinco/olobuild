<template>
  <div class="mb-bg-gray-900 mb-border-t mb-border-gray-700">
    <!-- Add layer buttons -->
    <div class="mb-flex mb-items-center mb-gap-2 mb-px-3 mb-py-2 mb-border-b mb-border-gray-800">
      <span class="mb-text-[10px] mb-text-gray-500 mb-font-semibold mb-uppercase mb-mr-1">{{ t('Aggiungi:') }}</span>
      <button
        v-for="lt in layerTypes"
        :key="lt.type"
        @click="$emit('add-layer', lt.type)"
        class="mb-px-2 mb-py-1 mb-text-[11px] mb-bg-gray-700 mb-text-gray-300 mb-rounded hover:mb-bg-gray-600 mb-transition-colors"
      >
        <span class="mps-lb-ico" aria-hidden="true" v-html="svg(lt.ico)"></span>{{ lt.label }}
      </button>
    </div>

    <!-- La fila orizzontale dei livelli è diventata il pannello Livelli a gruppi nella colonna di sinistra
         (LayerTree.vue, 1.4.1424): qui restano i pulsanti per aggiungere un livello. -->
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import lucideIcons from './lucideIconsSvg.js';
import { nomeLivello, animazioniLivello } from './livelloUtils.js';

const props = defineProps({
  layers: { type: Array, default: () => [] },
  selectedId: { type: String, default: null },
  hiddenIds: { type: Set, default: () => new Set() },
  activeBreakpoint: { type: String, default: 'desktop' },
});

defineEmits(['add-layer', 'select', 'remove', 'toggle-visibility', 'move-up', 'move-down']);

function hasResponsiveOverride(layer) {
  if (props.activeBreakpoint === 'desktop') return false;
  const ov = layer.responsive?.[props.activeBreakpoint];
  return ov && Object.keys(ov).length > 0;
}

// Icone del set SVG (Lucide, già nel builder con la libreria delle icone): prima erano emoji e simboli di testo
const layerTypes = [
  { type: 'text', label: t('Testo'), ico: 'type' },
  { type: 'image', label: t('Immagine'), ico: 'image' },
  { type: 'video', label: t('Video'), ico: 'video' },
  { type: 'button', label: t('Pulsante'), ico: 'rectangle-horizontal' },
  { type: 'icon', label: t('Icona'), ico: 'star' },
  { type: 'shape', label: t('Forma'), ico: 'shapes' },
  { type: 'audio', label: t('Audio'), ico: 'music' },
];

const reversedLayers = computed(() => [...props.layers].reverse());

function typeIcon(tipo) {
  return layerTypes.find(lt => lt.type === tipo)?.ico || 'square';
}

function svg(chiave) {
  return lucideIcons[chiave] || '';
}

// Il nome che dice cos'è (1.4.1352): prima tutte le immagini erano «Immagine» e le forme «Forma».
function layerLabel(l) {
  return nomeLivello(l);
}
function animazioni(l) {
  return animazioniLivello(l);
}
</script>

<style scoped>
.mps-lb-ico {
  display: inline-flex;
  width: 13px;
  height: 13px;
  margin-right: 4px;
  vertical-align: -2px;
}
.mps-lb-ico :deep(svg) {
  width: 13px;
  height: 13px;
}
/* I bollini delle animazioni: piccoli, colorati per famiglia, la spiegazione nel suggerimento. */
.mps-lb-anim {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 15px;
  height: 15px;
  padding: 0 3px;
  border-radius: 4px;
  font-size: 9px;
  font-weight: 700;
  line-height: 1;
  color: #cbd5e1;
  background: #334155;
  flex-shrink: 0;
  cursor: help;
}
.mps-lb-anim--scroll { background: #1e3a5f; color: #93c5fd; }
.mps-lb-anim--timeline { background: #3b2f5f; color: #c4b5fd; }
.mps-lb-anim--stato, .mps-lb-anim--azione, .mps-lb-anim--pulisci { background: #4a3a14; color: #fcd34d; }
.mps-lb-anim--css { background: #4c1d1d; color: #fca5a5; }
.mps-lb-anim--formati, .mps-lb-anim--nascosto { background: #374151; color: #9ca3af; }
.mps-bar-btn {
  margin-left: 2px;
  font-size: 10px;
  color: #9ca3af;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0 1px;
  line-height: 1;
}
.mps-bar-btn:hover {
  color: #e5e7eb;
}
.mps-eye-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 1px 3px;
  border-radius: 3px;
}
.mps-eye-hidden {
  color: #ef4444;
  background: rgba(239, 68, 68, 0.12);
}
.mps-eye-hidden:hover {
  color: #f87171;
  background: rgba(239, 68, 68, 0.2);
}
.mps-move-btn {
  margin-left: 1px;
  font-size: 11px;
  color: #93c5fd;
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 3px;
  cursor: pointer;
  padding: 1px 4px;
  line-height: 1;
}
.mps-move-btn:hover {
  background: #334155;
  color: #bfdbfe;
}
.mps-move-disabled {
  opacity: 0.25;
  cursor: default;
}
</style>
