<template>
  <div class="mps-iconpicker-backdrop" @click.self="$emit('close')">
    <div class="mps-iconpicker">
      <div class="mps-iconpicker-header">
        <span class="mb-text-xs mb-font-semibold mb-text-gray-200">Seleziona icona ({{ filtered.length }})</span>
        <button @click="$emit('close')" class="mb-text-gray-400 hover:mb-text-gray-200 mb-text-lg">&times;</button>
      </div>
      <div class="mps-iconpicker-search">
        <input
          v-model="search"
          class="mps-iconpicker-input"
          placeholder="Cerca icona..."
          ref="searchInput"
        />
      </div>
      <div class="mps-iconpicker-grid">
        <button
          v-for="name in filtered"
          :key="name"
          @click="$emit('select', name)"
          class="mps-iconpicker-item"
          :title="name"
        >
          <span class="mps-iconpicker-svg" v-html="getSvg(name)"></span>
          <span class="mps-iconpicker-name">{{ name }}</span>
        </button>
        <div v-if="!filtered.length" class="mps-iconpicker-empty">
          Nessuna icona trovata
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import iconsSvg from './uikitIconsSvg.js';

defineEmits(['select', 'close']);

const search = ref('');
const searchInput = ref(null);

onMounted(() => {
  nextTick(() => searchInput.value?.focus());
});

// Use only icons that have SVG data
const iconNames = Object.keys(iconsSvg).sort();

function getSvg(name) {
  return iconsSvg[name] || '';
}

const filtered = computed(() => {
  const q = search.value.toLowerCase().trim();
  if (!q) return iconNames;
  return iconNames.filter(i => i.includes(q));
});
</script>

<style scoped>
.mps-iconpicker-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: center;
}
.mps-iconpicker {
  background: #1f2937;
  border: 1px solid #374151;
  border-radius: 8px;
  width: 480px;
  max-height: 520px;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 40px rgba(0,0,0,0.5);
}
.mps-iconpicker-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  border-bottom: 1px solid #374151;
}
.mps-iconpicker-search {
  padding: 8px 14px;
  border-bottom: 1px solid #374151;
}
.mps-iconpicker-input {
  width: 100%;
  background: #111827;
  border: 1px solid #4b5563;
  border-radius: 6px;
  padding: 6px 10px;
  font-size: 12px;
  color: #e5e7eb;
  outline: none;
}
.mps-iconpicker-input:focus {
  border-color: var(--olo-color-primary, #6366f1);
}
.mps-iconpicker-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 2px;
  padding: 8px;
  overflow-y: auto;
  flex: 1;
}
.mps-iconpicker-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 3px;
  padding: 8px 2px;
  border: none;
  background: transparent;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.15s;
}
.mps-iconpicker-item:hover {
  background: #374151;
}
.mps-iconpicker-svg {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
}
.mps-iconpicker-svg :deep(svg) {
  width: 24px;
  height: 24px;
  fill: #d1d5db;
  stroke: #d1d5db;
}
.mps-iconpicker-item:hover .mps-iconpicker-svg :deep(svg) {
  fill: #fff;
  stroke: #fff;
}
.mps-iconpicker-name {
  font-size: 8px;
  color: #9ca3af;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 100%;
}
.mps-iconpicker-empty {
  grid-column: 1 / -1;
  text-align: center;
  font-size: 12px;
  color: #6b7280;
  padding: 16px;
}
</style>
