<template>
  <div class="olo-colors-tab">
    <div class="olo-card">
      <div class="olo-card-head">
        <div class="olo-card-icon" style="background:#e8622a;color:#fff">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/></svg>
        </div>
        <div>
          <h3>Palette Colori Globale</h3>
          <p>Definisci colori riutilizzabili ovunque tramite variabili CSS</p>
        </div>
      </div>
      <div class="olo-card-body">
        <div class="olo-gcolors-list">
          <div v-for="(color, index) in localColors" :key="index" class="olo-gcolor-item">
            <div class="olo-gcolor-row">
              <input
                type="text"
                :value="color.label"
                @input="updateColorLabel(index, $event.target.value)"
                placeholder="Nome colore"
                class="olo-field-input olo-gcolor-name"
              />
              <div class="olo-gcolor-hex-wrap">
                <input
                  type="color"
                  :value="color.value"
                  @input="updateColorValue(index, $event.target.value)"
                  class="olo-gcolor-swatch-inline"
                />
                <input
                  type="text"
                  :value="color.value"
                  @change="updateColorValue(index, $event.target.value)"
                  class="olo-field-input olo-gcolor-hex"
                />
              </div>
              <code class="olo-gcolor-var">--olo-color-{{ color.id }}</code>
              <button class="olo-gcolor-remove" @click="removeColor(index)" title="Rimuovi colore">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>
          </div>
        </div>

        <button class="olo-gcolor-add" @click="addColor">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Aggiungi colore
        </button>
      </div>
    </div>

    <div class="olo-actions">
      <button @click="save" :disabled="!isDirty || isSaving" class="olo-btn-save" :class="{ disabled: !isDirty }">
        <svg v-if="!isSaving" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <span v-if="isSaving" class="olo-spinner"></span>
        {{ isSaving ? 'Salvataggio...' : 'Salva palette' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, inject } from 'vue';
import { useStylesStore } from '@/stores/styles';

const stylesStore = useStylesStore();
const showToast = inject('showToast', () => {});

const localColors = ref(JSON.parse(JSON.stringify(stylesStore.globalColors || [])));

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
  return label.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'color-' + Date.now();
}

function updateColorValue(index, value) {
  localColors.value[index].value = value;
  isDirty.value = true;
}

function updateColorLabel(index, label) {
  localColors.value[index].label = label;
  localColors.value[index].id = generateId(label);
  isDirty.value = true;
}

function addColor() {
  const n = localColors.value.length + 1;
  localColors.value.push({ id: 'colore-' + n, label: 'Colore ' + n, value: '#888888' });
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
  showToast('Palette colori salvata');
}

watch(() => stylesStore.globalColors, (newVal) => {
  if (!isDirty.value) {
    localColors.value = JSON.parse(JSON.stringify(newVal || []));
  }
}, { deep: true });
</script>

<style scoped>
.olo-gcolors-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 16px;
}
.olo-gcolor-item {
  background: #f9fafb;
  border: 1px solid #f3f4f6;
  border-radius: 10px;
  padding: 10px 14px;
  transition: border-color 0.15s;
}
.olo-gcolor-item:hover {
  border-color: #e5e7eb;
}
.olo-gcolor-row {
  display: flex;
  align-items: center;
  gap: 10px;
}
.olo-gcolor-name {
  flex: 1;
  min-width: 0;
  font-weight: 500 !important;
}
.olo-gcolor-hex-wrap {
  display: flex;
  align-items: center;
  flex-shrink: 0;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  overflow: hidden;
  background: #fff;
}
.olo-gcolor-swatch-inline {
  width: 32px;
  height: 32px;
  border: none;
  cursor: pointer;
  padding: 0;
  background: none;
  flex-shrink: 0;
}
.olo-gcolor-swatch-inline::-webkit-color-swatch-wrapper {
  padding: 2px;
}
.olo-gcolor-swatch-inline::-webkit-color-swatch {
  border: none;
  border-radius: 3px;
}
.olo-gcolor-hex {
  width: 80px !important;
  font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace !important;
  font-size: 12px !important;
  color: #6b7280 !important;
  flex: none !important;
  border: none !important;
  border-radius: 0 !important;
  background: transparent !important;
  padding: 4px 8px 4px 0 !important;
}
.olo-gcolor-hex:focus {
  outline: none;
}
.olo-gcolor-var {
  font-size: 10px;
  color: #b0b0b0;
  font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;
  background: none;
  flex-shrink: 0;
  white-space: nowrap;
}
.olo-gcolor-remove {
  background: none;
  border: none;
  color: #d1d5db;
  cursor: pointer;
  padding: 4px;
  border-radius: 6px;
  flex-shrink: 0;
  display: flex;
  transition: all 0.15s;
}
.olo-gcolor-remove:hover {
  color: #ef4444;
  background: #fef2f2;
}
.olo-gcolor-add {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 12px 0;
  background: none;
  border: 2px dashed #e5e7eb;
  border-radius: 10px;
  color: #9ca3af;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.15s;
}
.olo-gcolor-add:hover {
  border-color: #e8622a;
  color: #e8622a;
  background: #fdf5f0;
}
</style>
