<template>
  <Teleport to="body">
    <div class="mps-iconpicker-backdrop" @click.self="$emit('close')">
      <div class="mps-iconpicker">
        <div class="mps-iconpicker-header">
          <span class="mps-iconpicker-title">{{ t('Seleziona icona') }} ({{ filtered.length }}<span v-if="!search.trim() && totalCount() > filtered.length" class="mps-iconpicker-total"> / {{ totalCount() }}</span>)</span>
          <button @click="$emit('close')" class="mps-iconpicker-close">&times;</button>
        </div>
        <!-- Tabs -->
        <div class="mps-iconpicker-tabs">
          <button :class="['mps-tab', tab === 'builtin' && 'mps-tab--active']" @click="tab = 'builtin'">{{ t('Libreria') }}</button>
          <button :class="['mps-tab', tab === 'custom' && 'mps-tab--active']" @click="tab = 'custom'; loadCustomIcons()">{{ t('Le mie icone') }}</button>
        </div>
        <div class="mps-iconpicker-search">
          <input
            v-model="search"
            class="mps-iconpicker-input"
            :placeholder="t('Cerca icona...')"
            ref="searchInput"
          />
        </div>
        <!-- Upload (custom tab) -->
        <div v-if="tab === 'custom'" class="mps-iconpicker-upload">
          <label class="mps-upload-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            {{ t('Carica SVG') }}
            <input type="file" accept=".svg" multiple @change="uploadIcons" style="display:none" />
          </label>
          <span v-if="uploading" class="mps-upload-status">{{ t('Caricamento...') }}</span>
        </div>
        <div class="mps-iconpicker-grid" ref="gridEl" @scroll.passive="onScroll">
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
          <div v-if="!filtered.length" class="mps-iconpicker-empty">
            {{ tab === 'custom' ? t('Nessuna icona custom. Carica file SVG.') : t('Nessuna icona trovata') }}
          </div>
          <!-- Caricamento progressivo allo scroll (sentinel informativo) -->
          <div v-if="hasMore" class="mps-iconpicker-loading">+{{ totalCount() - filtered.length }}</div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import iconsSvg from './iconsLibrary.js';
import { t } from '@/i18n';

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

// Senza ricerca, paginazione "virtuale" semplice (mostra batch, espandibile)
// per evitare ~1800 SVG simultanei nel DOM al primo apri (renderizzazione lenta).
const VISIBLE_BATCH = 200;
const visibleCount = ref(VISIBLE_BATCH);

const filtered = computed(() => {
  const q = search.value.toLowerCase().trim();
  const source = tab.value === 'custom' ? customNames.value : iconNames;
  if (!q) return source.slice(0, visibleCount.value);
  return source.filter(i => i.includes(q));
});

const hasMore = computed(() => {
  if (search.value.trim()) return false;
  const source = tab.value === 'custom' ? customNames.value : iconNames;
  return visibleCount.value < source.length;
});

function loadMore() {
  visibleCount.value += VISIBLE_BATCH;
}

// Scroll infinito: carica il batch successivo avvicinandosi al fondo della griglia,
// senza pulsante manuale. Mantiene la performance (no ~1800 SVG insieme al primo apri).
const gridEl = ref(null);
function onScroll() {
  const el = gridEl.value;
  if (!el) return;
  if (el.scrollTop + el.clientHeight >= el.scrollHeight - 240 && hasMore.value) {
    loadMore();
  }
}

function totalCount() {
  return (tab.value === 'custom' ? customNames.value : iconNames).length;
}

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
  background: rgba(0,0,0,0.25);
  z-index: 9999999;
  display: flex;
  align-items: center;
  justify-content: center;
}
.mps-iconpicker {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  width: 640px;
  max-width: 92vw;
  max-height: 600px;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,0.15), 0 4px 12px rgba(0,0,0,0.08);
}
.mps-iconpicker-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  border-bottom: 1px solid #e5e7eb;
}
.mps-iconpicker-title {
  font-size: 13px;
  font-weight: 600;
  color: #1f2937;
}
.mps-iconpicker-close {
  background: none;
  border: none;
  color: #9ca3af;
  font-size: 20px;
  cursor: pointer;
  padding: 0 4px;
  line-height: 1;
}
.mps-iconpicker-close:hover { color: #374151; }

.mps-iconpicker-search {
  padding: 10px 16px;
  border-bottom: 1px solid #e5e7eb;
}
.mps-iconpicker-input {
  width: 100%;
  background: #f9fafb;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 8px 12px;
  font-size: 13px;
  color: #1f2937;
  outline: none;
}
.mps-iconpicker-input::placeholder { color: #9ca3af; }
.mps-iconpicker-input:focus {
  border-color: var(--olo-ui-accent, #e8622a);
  box-shadow: 0 0 0 2px rgba(232, 98, 42, 0.12);
}

.mps-iconpicker-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(76px, 1fr));
  gap: 4px;
  padding: 10px;
  overflow-y: auto;
  flex: 1;
  align-content: start;
}
.mps-iconpicker-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  padding: 10px 4px 6px;
  border: none;
  background: transparent;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.15s;
}
.mps-iconpicker-item:hover {
  background: #f3f4f6;
}
.mps-iconpicker-svg {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
}
/* Icon colorization — `currentColor` strategy:
 *   1. Set `color` on <svg> → both fill and stroke pick it up via currentColor.
 *   2. UIkit icons (no fill/stroke on root) need an explicit `fill: currentColor`
 *      override; their default would be implicit `black`.
 *   3. Lucide icons (`<svg fill="none" stroke="currentColor">`) must keep
 *      `fill: none` — the attribute selector preserves it without leaking the
 *      fill onto the path geometry (which would solid-fill line icons).
 *   4. Inner elements with explicit `fill="none"` (UIkit composite icons)
 *      must also be respected. */
.mps-iconpicker-svg :deep(svg) {
  width: 24px;
  height: 24px;
  color: #4b5563;
  stroke: currentColor;
}
.mps-iconpicker-svg :deep(svg:not([fill="none"])) {
  fill: currentColor;
}
.mps-iconpicker-svg :deep(svg [fill="none"]) {
  fill: none;
}
.mps-iconpicker-item:hover .mps-iconpicker-svg :deep(svg) {
  color: #1f2937;
}
.mps-iconpicker-name {
  font-size: 9px;
  color: #6b7280;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 100%;
}
.mps-iconpicker-empty {
  grid-column: 1 / -1;
  text-align: center;
  font-size: 13px;
  color: #9ca3af;
  padding: 24px;
}
.mps-iconpicker-total {
  color: #9ca3af;
  font-weight: 400;
}
.mps-iconpicker-loading {
  grid-column: 1 / -1;
  text-align: center;
  font-size: 11px;
  font-weight: 600;
  color: #9ca3af;
  padding: 14px;
}

/* Tabs */
.mps-iconpicker-tabs {
  display: flex;
  border-bottom: 1px solid #e5e7eb;
}
.mps-tab {
  flex: 1;
  padding: 10px;
  background: transparent;
  border: none;
  color: #6b7280;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: color 0.15s, border-color 0.15s;
  border-bottom: 2px solid transparent;
}
.mps-tab--active {
  color: #1f2937;
  border-bottom-color: var(--olo-ui-accent, #e8622a);
}
.mps-tab:hover { color: #374151; }

/* Upload */
.mps-iconpicker-upload {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-bottom: 1px solid #e5e7eb;
}
.mps-upload-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 5px 12px;
  background: #f3f4f6;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  color: #374151;
  font-size: 12px;
  cursor: pointer;
  transition: background 0.15s;
}
.mps-upload-btn:hover { background: #e5e7eb; }
.mps-upload-status {
  font-size: 12px;
  color: #9ca3af;
}

/* Scrollbar */
.mps-iconpicker-grid::-webkit-scrollbar { width: 6px; }
.mps-iconpicker-grid::-webkit-scrollbar-track { background: transparent; }
.mps-iconpicker-grid::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
.mps-iconpicker-grid::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>
