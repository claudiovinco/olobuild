<template>
  <div class="mb-bg-gray-900 mb-border-t mb-border-gray-700">
    <!-- Add layer buttons -->
    <div class="mb-flex mb-items-center mb-gap-2 mb-px-3 mb-py-2 mb-border-b mb-border-gray-800">
      <span class="mb-text-[10px] mb-text-gray-500 mb-font-semibold mb-uppercase mb-mr-1">Aggiungi:</span>
      <button
        v-for="lt in layerTypes"
        :key="lt.type"
        @click="$emit('add-layer', lt.type)"
        class="mb-px-2 mb-py-1 mb-text-[11px] mb-bg-gray-700 mb-text-gray-300 mb-rounded hover:mb-bg-gray-600 mb-transition-colors"
      >
        {{ lt.icon }} {{ lt.label }}
      </button>
    </div>

    <!-- Layer list (reversed: top of stack = left) -->
    <div class="mb-flex mb-items-center mb-gap-1 mb-px-3 mb-py-2 mb-overflow-x-auto">
      <span v-if="!layers.length" class="mb-text-[10px] mb-text-gray-500 mb-italic">Nessun livello in questa slide</span>
      <div
        v-for="(layer, idx) in reversedLayers"
        :key="layer.id"
        @click="$emit('select', layer.id)"
        :class="[
          'mb-flex mb-items-center mb-gap-1 mb-px-2 mb-py-1 mb-rounded mb-text-[11px] mb-cursor-pointer mb-transition-colors mb-shrink-0 mb-border',
          layer.id === selectedId
            ? 'mb-border-primary-400 mb-bg-primary-900/30 mb-text-primary-300'
            : 'mb-border-gray-700 mb-bg-gray-800 mb-text-gray-400 hover:mb-border-gray-500'
        ]"
      >
        <span class="mb-opacity-60">{{ typeIcon(layer.type) }}</span>
        <span class="mb-max-w-24 mb-truncate">{{ layerLabel(layer) }}</span>

        <!-- Move up (toward front / higher z-index) -->
        <button
          @click.stop="$emit('move-up', layer.id)"
          :disabled="idx === 0"
          :class="['mps-move-btn', idx === 0 ? 'mps-move-disabled' : '']"
          title="Porta avanti"
        >&#9650;</button>

        <!-- Move down (toward back / lower z-index) -->
        <button
          @click.stop="$emit('move-down', layer.id)"
          :disabled="idx === reversedLayers.length - 1"
          :class="['mps-move-btn', idx === reversedLayers.length - 1 ? 'mps-move-disabled' : '']"
          title="Manda indietro"
        >&#9660;</button>

        <!-- Visibility toggle -->
        <button
          @click.stop="$emit('toggle-visibility', layer.id)"
          :class="['mps-bar-btn', hiddenIds.has(layer.id) ? 'mb-text-gray-600' : '']"
          :title="hiddenIds.has(layer.id) ? 'Mostra' : 'Nascondi'"
        >{{ hiddenIds.has(layer.id) ? '&#x1F441;&#xFE0F;' : '&#x1F441;' }}</button>

        <!-- Delete -->
        <button @click.stop="$emit('remove', layer.id)" class="mps-bar-btn hover:mb-text-red-400" title="Elimina">&times;</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  layers: { type: Array, default: () => [] },
  selectedId: { type: String, default: null },
  hiddenIds: { type: Set, default: () => new Set() },
});

defineEmits(['add-layer', 'select', 'remove', 'toggle-visibility', 'move-up', 'move-down']);

const layerTypes = [
  { type: 'text', label: 'Testo', icon: 'T' },
  { type: 'image', label: 'Immagine', icon: '🖼' },
  { type: 'video', label: 'Video', icon: '▶' },
  { type: 'button', label: 'Pulsante', icon: '▢' },
  { type: 'icon', label: 'Icona', icon: '★' },
  { type: 'shape', label: 'Forma', icon: '◆' },
];

const reversedLayers = computed(() => [...props.layers].reverse());

function typeIcon(t) {
  return layerTypes.find(lt => lt.type === t)?.icon || '?';
}

function layerLabel(l) {
  if (l.type === 'text' || l.type === 'button') return l.content || l.type;
  if (l.type === 'icon') return l.iconName || 'icona';
  if (l.type === 'image') return 'Immagine';
  if (l.type === 'video') return 'Video';
  return l.type;
}
</script>

<style scoped>
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
