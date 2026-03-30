<template>
  <div class="gcp-root">
    <div class="gcp-header">
      <h3 class="gcp-title">Palette Colori Globale</h3>
      <p class="gcp-desc">Definisci colori riutilizzabili ovunque tramite variabili CSS.</p>
    </div>

    <div class="gcp-list">
      <div
        v-for="(color, index) in localColors"
        :key="color.id + '-' + index"
        class="gcp-item"
      >
        <div class="gcp-item-row">
          <input
            type="color"
            :value="color.value"
            @input="updateColorValue(index, $event.target.value)"
            class="gcp-swatch"
          />
          <div class="gcp-item-fields">
            <input
              type="text"
              :value="color.label"
              @input="updateColorLabel(index, $event.target.value)"
              placeholder="Nome colore"
              class="gcp-input gcp-input--label"
            />
            <input
              type="text"
              :value="color.value"
              @change="updateColorValue(index, $event.target.value)"
              class="gcp-input gcp-input--value"
            />
          </div>
          <button
            class="gcp-remove"
            @click="removeColor(index)"
            title="Rimuovi colore"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <div class="gcp-var-name">var(--olo-color-{{ color.id }})</div>
      </div>
    </div>

    <button class="gcp-add" @click="addColor">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      Aggiungi colore
    </button>

    <div class="gcp-actions">
      <button
        class="gcp-save"
        :disabled="!isDirty || isSaving"
        @click="save"
      >
        {{ isSaving ? 'Salvataggio...' : 'Salva palette' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useStylesStore } from '@/stores/styles';
import { useToast } from '@/composables/useToast.js';

const stylesStore = useStylesStore();
const toast = useToast();

// Local copy for editing
const localColors = ref(JSON.parse(JSON.stringify(stylesStore.globalColors || [])));

// If the store has no colors yet, provide defaults
if (localColors.value.length === 0) {
  localColors.value = [
    { id: 'primary', label: 'Primario', value: '#1e87f0' },
    { id: 'secondary', label: 'Secondario', value: '#32d296' },
    { id: 'accent', label: 'Accento', value: '#faa05a' },
    { id: 'dark', label: 'Scuro', value: '#1a1a2e' },
    { id: 'light', label: 'Chiaro', value: '#f8f9fa' },
    { id: 'text', label: 'Testo', value: '#333333' },
  ];
}

const isDirty = ref(false);
const isSaving = computed(() => stylesStore.isSaving);

function generateId(label) {
  return label
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '')
    || 'color-' + Date.now();
}

function updateColorValue(index, value) {
  localColors.value[index].value = value;
  isDirty.value = true;
}

function updateColorLabel(index, label) {
  const oldId = localColors.value[index].id;
  localColors.value[index].label = label;
  // Auto-generate ID only if the id was auto-generated (matches old label pattern)
  const expectedOldId = generateId(localColors.value[index].label);
  // Always regenerate id from label for new / user-editable colors
  localColors.value[index].id = generateId(label);
  isDirty.value = true;
}

function addColor() {
  const n = localColors.value.length + 1;
  localColors.value.push({
    id: 'colore-' + n,
    label: 'Colore ' + n,
    value: '#888888',
  });
  isDirty.value = true;
}

function removeColor(index) {
  localColors.value.splice(index, 1);
  isDirty.value = true;
}

async function save() {
  stylesStore.setGlobalColors(JSON.parse(JSON.stringify(localColors.value)));
  await stylesStore.saveGlobalColors();
  isDirty.value = false;
  toast.success('Palette colori salvata');
}

// Sync from store if it changes externally
watch(() => stylesStore.globalColors, (newVal) => {
  if (!isDirty.value) {
    localColors.value = JSON.parse(JSON.stringify(newVal || []));
  }
}, { deep: true });
</script>

<style scoped>
.gcp-root {
  padding: 12px;
}

.gcp-header {
  margin-bottom: 12px;
}

.gcp-title {
  font-size: 13px;
  font-weight: 600;
  color: #e5e7eb;
  margin: 0 0 4px 0;
}

.gcp-desc {
  font-size: 11px;
  color: #9ca3af;
  margin: 0;
  line-height: 1.4;
}

.gcp-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 12px;
}

.gcp-item {
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid #374151;
  border-radius: 6px;
  padding: 8px;
}

.gcp-item-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.gcp-swatch {
  width: 32px;
  height: 32px;
  border: 1px solid #4b5563;
  border-radius: 6px;
  cursor: pointer;
  flex-shrink: 0;
  padding: 0;
  background: none;
}

.gcp-item-fields {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.gcp-input {
  width: 100%;
  background: #111827;
  border: 1px solid #374151;
  border-radius: 4px;
  padding: 3px 6px;
  font-size: 11px;
  color: #e5e7eb;
  outline: none;
  font-family: inherit;
}

.gcp-input:focus {
  border-color: var(--olo-color-primary, #6366f1);
}

.gcp-input--label {
  font-weight: 500;
}

.gcp-input--value {
  font-family: monospace;
  font-size: 10px;
  color: #9ca3af;
}

.gcp-var-name {
  font-size: 9px;
  color: #6b7280;
  font-family: monospace;
  margin-top: 4px;
  padding-left: 40px;
}

.gcp-remove {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.gcp-remove:hover {
  color: #ef4444;
  background: rgba(239, 68, 68, 0.1);
}

.gcp-add {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  width: 100%;
  padding: 6px 0;
  background: none;
  border: 1px dashed #4b5563;
  border-radius: 6px;
  color: #9ca3af;
  font-size: 11px;
  cursor: pointer;
  font-family: inherit;
  transition: border-color 0.15s, color 0.15s;
}

.gcp-add:hover {
  border-color: var(--olo-color-primary, #6366f1);
  color: #e5e7eb;
}

.gcp-actions {
  margin-top: 12px;
  display: flex;
  justify-content: flex-end;
}

.gcp-save {
  background: var(--olo-color-primary, #6366f1);
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 6px 16px;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  transition: opacity 0.15s;
}

.gcp-save:hover {
  opacity: 0.9;
}

.gcp-save:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
