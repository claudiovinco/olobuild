<template>
  <div class="mps-iconpicker-backdrop" @click.self="$emit('close')">
    <div class="mps-iconpicker">
      <div class="mps-iconpicker-header">
        <span class="mb-text-xs mb-font-semibold mb-text-gray-200">Seleziona icona ({{ filtered.length }})</span>
        <button @click="$emit('close')" class="mb-text-gray-400 hover:mb-text-gray-200 mb-text-lg">&times;</button>
      </div>
      <!-- Tabs -->
      <div class="mps-iconpicker-tabs">
        <button :class="['mps-tab', tab === 'builtin' && 'mps-tab--active']" @click="tab = 'builtin'">Libreria</button>
        <button :class="['mps-tab', tab === 'custom' && 'mps-tab--active']" @click="tab = 'custom'; loadCustomIcons()">Le mie icone</button>
      </div>
      <div class="mps-iconpicker-search">
        <input
          v-model="search"
          class="mps-iconpicker-input"
          placeholder="Cerca icona..."
          ref="searchInput"
        />
      </div>
      <!-- Upload (custom tab) -->
      <div v-if="tab === 'custom'" class="mps-iconpicker-upload">
        <label class="mps-upload-btn">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Carica SVG
          <input type="file" accept=".svg" multiple @change="uploadIcons" style="display:none" />
        </label>
        <span v-if="uploading" class="mb-text-xs mb-text-gray-400">Caricamento...</span>
      </div>
      <div class="mps-iconpicker-grid">
        <button
          v-for="name in filtered"
          :key="name"
          @click="$emit('select', tab === 'custom' ? 'custom:' + name : name)"
          class="mps-iconpicker-item"
          :title="name"
        >
          <span class="mps-iconpicker-svg" v-html="getSvg(name)"></span>
          <span class="mps-iconpicker-name">{{ name }}</span>
        </button>
        <!-- Delete button for custom icons -->
        <div v-if="!filtered.length" class="mps-iconpicker-empty">
          {{ tab === 'custom' ? 'Nessuna icona custom. Carica file SVG.' : 'Nessuna icona trovata' }}
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
const tab = ref('builtin');
const customIcons = ref({});
const customLoaded = ref(false);
const uploading = ref(false);

onMounted(() => {
  nextTick(() => searchInput.value?.focus());
});

const iconNames = Object.keys(iconsSvg).sort();
const customNames = computed(() => Object.keys(customIcons.value).sort());

function getSvg(name) {
  if (tab.value === 'custom') return customIcons.value[name] || '';
  return iconsSvg[name] || '';
}

const filtered = computed(() => {
  const q = search.value.toLowerCase().trim();
  const source = tab.value === 'custom' ? customNames.value : iconNames;
  if (!q) return source;
  return source.filter(i => i.includes(q));
});

async function loadCustomIcons() {
  if (customLoaded.value) return;
  try {
    const res = await fetch(`${window.oloData?.restUrl || '/wp-json/'}olo/v1/custom-icons`, {
      headers: { 'X-WP-Nonce': window.oloData?.nonce || '' },
    });
    if (res.ok) {
      customIcons.value = await res.json();
      customLoaded.value = true;
    }
  } catch (e) { /* silently fail */ }
}

async function uploadIcons(event) {
  const files = event.target.files;
  if (!files.length) return;
  uploading.value = true;
  for (const file of files) {
    if (!file.name.endsWith('.svg')) continue;
    const formData = new FormData();
    formData.append('svg_file', file);
    formData.append('name', file.name.replace('.svg', ''));
    try {
      const res = await fetch(`${window.oloData?.restUrl || '/wp-json/'}olo/v1/custom-icons`, {
        method: 'POST',
        headers: { 'X-WP-Nonce': window.oloData?.nonce || '' },
        body: formData,
      });
      if (res.ok) {
        const data = await res.json();
        customIcons.value = { ...customIcons.value, [data.name]: data.svg };
      }
    } catch (e) { /* silently fail */ }
  }
  uploading.value = false;
  event.target.value = '';
}
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
.mps-iconpicker-tabs {
  display: flex;
  border-bottom: 1px solid #374151;
}
.mps-tab {
  flex: 1;
  padding: 8px;
  background: transparent;
  border: none;
  color: #9ca3af;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  transition: color 0.15s, border-color 0.15s;
  border-bottom: 2px solid transparent;
}
.mps-tab--active {
  color: #e5e7eb;
  border-bottom-color: var(--olo-color-primary, #6366f1);
}
.mps-tab:hover { color: #d1d5db; }
.mps-iconpicker-upload {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  border-bottom: 1px solid #374151;
}
.mps-upload-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  background: #374151;
  border: 1px solid #4b5563;
  border-radius: 4px;
  color: #d1d5db;
  font-size: 11px;
  cursor: pointer;
  transition: background 0.15s;
}
.mps-upload-btn:hover { background: #4b5563; }
</style>
