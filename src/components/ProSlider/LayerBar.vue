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
        {{ lt.icon }} {{ lt.label }}
      </button>
    </div>

    <!-- Layer list (reversed: top of stack = left) -->
    <div class="mb-flex mb-items-center mb-gap-1 mb-px-3 mb-py-2 mb-overflow-x-auto">
      <span v-if="!layers.length" class="mb-text-[10px] mb-text-gray-500 mb-italic">{{ t('Nessun livello in questa slide') }}</span>
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
        <span v-if="hasResponsiveOverride(layer)" class="mb-w-1.5 mb-h-1.5 mb-rounded-full mb-bg-yellow-400 mb-shrink-0" :title="t('Override responsive')"></span>

        <!-- Move up (toward front / higher z-index) -->
        <button
          @click.stop="$emit('move-up', layer.id)"
          :disabled="idx === 0"
          :class="['mps-move-btn', idx === 0 ? 'mps-move-disabled' : '']"
          :title="t('Porta avanti')"
        >&#9650;</button>

        <!-- Move down (toward back / lower z-index) -->
        <button
          @click.stop="$emit('move-down', layer.id)"
          :disabled="idx === reversedLayers.length - 1"
          :class="['mps-move-btn', idx === reversedLayers.length - 1 ? 'mps-move-disabled' : '']"
          :title="t('Manda indietro')"
        >&#9660;</button>

        <!-- Visibility toggle -->
        <button
          @click.stop="$emit('toggle-visibility', layer.id)"
          :class="['mps-bar-btn mps-eye-btn', hiddenIds.has(layer.id) ? 'mps-eye-hidden' : '']"
          :title="hiddenIds.has(layer.id) ? t('Mostra') : t('Nascondi')"
        >
          <svg v-if="!hiddenIds.has(layer.id)" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
          </svg>
          <svg v-else width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/>
          </svg>
        </button>

        <!-- Delete -->
        <button @click.stop="$emit('remove', layer.id)" class="mps-bar-btn hover:mb-text-red-400" :title="t('Elimina')">&times;</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

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

const layerTypes = [
  { type: 'text', label: 'Testo', icon: 'T' },
  { type: 'image', label: 'Immagine', icon: '🖼' },
  { type: 'video', label: 'Video', icon: '▶' },
  { type: 'button', label: 'Pulsante', icon: '▢' },
  { type: 'icon', label: 'Icona', icon: '★' },
  { type: 'shape', label: 'Forma', icon: '◆' },
  { type: 'audio', label: 'Audio', icon: '♫' },
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
