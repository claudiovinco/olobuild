<template>
  <div class="gtp-root">
    <div class="gtp-header">
      <h3 class="gtp-title">{{ t('Tipografia Globale') }}</h3>
      <p class="gtp-desc">{{ t('Definisci set tipografici riutilizzabili tramite variabili CSS.') }}</p>
    </div>

    <div class="gtp-list">
      <div
        v-for="(set, index) in localSets"
        :key="set.id + '-' + index"
        class="gtp-item"
      >
        <div class="gtp-item-header">
          <input
            type="text"
            :value="set.label"
            @input="updateField(index, 'label', $event.target.value)"
            :placeholder="t('Nome set')"
            class="gtp-input gtp-input--label"
          />
          <button
            class="gtp-remove"
            @click="removeSet(index)"
            :title="t('Rimuovi set')"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        <div class="gtp-fields">
          <!-- Font Family -->
          <div class="gtp-field">
            <label class="gtp-field-label">{{ t('Font family') }}</label>
            <FieldFontFamily
              :modelValue="set.family"
              @update:modelValue="updateField(index, 'family', $event)"
            />
          </div>

          <!-- Weight -->
          <div class="gtp-field">
            <label class="gtp-field-label">{{ t('Peso') }}</label>
            <select
              :value="set.weight"
              @change="updateField(index, 'weight', $event.target.value)"
              class="gtp-select"
            >
              <option value="100">{{ t('100 - Thin') }}</option>
              <option value="200">{{ t('200 - Extra Light') }}</option>
              <option value="300">{{ t('300 - Light') }}</option>
              <option value="400">{{ t('400 - Regular') }}</option>
              <option value="500">{{ t('500 - Medium') }}</option>
              <option value="600">{{ t('600 - Semi Bold') }}</option>
              <option value="700">{{ t('700 - Bold') }}</option>
              <option value="800">{{ t('800 - Extra Bold') }}</option>
              <option value="900">{{ t('900 - Black') }}</option>
            </select>
          </div>

          <!-- Transform -->
          <div class="gtp-field">
            <label class="gtp-field-label">{{ t('Trasformazione') }}</label>
            <select
              :value="set.transform"
              @change="updateField(index, 'transform', $event.target.value)"
              class="gtp-select"
            >
              <option value="none">{{ t('Nessuna') }}</option>
              <option value="uppercase">{{ t('MAIUSCOLO') }}</option>
              <option value="lowercase">minuscolo</option>
              <option value="capitalize">{{ t('Capitalizza') }}</option>
            </select>
          </div>

          <!-- Line Height -->
          <div class="gtp-field">
            <label class="gtp-field-label">{{ t('Line height') }}</label>
            <input
              type="text"
              :value="set.line_height"
              @change="updateField(index, 'line_height', $event.target.value)"
              class="gtp-input gtp-input--small"
            />
          </div>

          <!-- Letter Spacing -->
          <div class="gtp-field">
            <label class="gtp-field-label">{{ t('Letter spacing (px)') }}</label>
            <input
              type="text"
              :value="set.letter_spacing"
              @change="updateField(index, 'letter_spacing', $event.target.value)"
              class="gtp-input gtp-input--small"
            />
          </div>
        </div>

        <div class="gtp-vars">
          <span class="gtp-var" v-if="set.family">var(--olo-font-{{ set.id }}-family)</span>
          <span class="gtp-var">var(--olo-font-{{ set.id }}-weight)</span>
        </div>

        <!-- Preview -->
        <div
          class="gtp-preview"
          :style="{
            fontFamily: set.family ? (set.family + ', sans-serif') : 'inherit',
            fontWeight: set.weight,
            textTransform: set.transform === 'none' ? 'initial' : set.transform,
            lineHeight: set.line_height,
            letterSpacing: set.letter_spacing + 'px',
          }"
        >
          {{ t('Anteprima del testo - Abc 123') }}
        </div>
      </div>
    </div>

    <button class="gtp-add" @click="addSet">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      Aggiungi set tipografico
    </button>

    <div class="gtp-actions">
      <button
        class="gtp-save"
        :disabled="!isDirty || isSaving"
        @click="save"
      >
        {{ isSaving ? 'Salvataggio...' : 'Salva tipografia' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, watch, computed } from 'vue';
import { useStylesStore } from '@/stores/styles';
import { useToast } from '@/composables/useToast.js';
import FieldFontFamily from './fields/FieldFontFamily.vue';

const stylesStore = useStylesStore();
const toast = useToast();

// Local copy for editing
const localSets = ref(JSON.parse(JSON.stringify(stylesStore.globalTypography || [])));

// If the store has no sets yet, provide defaults
if (localSets.value.length === 0) {
  localSets.value = [
    { id: 'heading', label: 'Titoli', family: 'Montserrat', weight: '700', transform: 'none', line_height: '1.3', letter_spacing: '0' },
    { id: 'subheading', label: 'Sottotitoli', family: 'Montserrat', weight: '500', transform: 'none', line_height: '1.4', letter_spacing: '0.5' },
    { id: 'body', label: 'Corpo testo', family: 'Open Sans', weight: '400', transform: 'none', line_height: '1.6', letter_spacing: '0' },
    { id: 'small', label: 'Testo piccolo', family: 'Open Sans', weight: '400', transform: 'none', line_height: '1.5', letter_spacing: '0' },
  ];
}

const isDirty = ref(false);
const isSaving = computed(() => stylesStore.isSaving);

function generateId(label) {
  return label
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '')
    || 'set-' + Date.now();
}

function updateField(index, field, value) {
  localSets.value[index][field] = value;
  if (field === 'label') {
    localSets.value[index].id = generateId(value);
  }
  isDirty.value = true;
}

function addSet() {
  const n = localSets.value.length + 1;
  localSets.value.push({
    id: 'set-' + n,
    label: 'Set ' + n,
    family: '',
    weight: '400',
    transform: 'none',
    line_height: '1.5',
    letter_spacing: '0',
  });
  isDirty.value = true;
}

function removeSet(index) {
  localSets.value.splice(index, 1);
  isDirty.value = true;
}

async function save() {
  stylesStore.setGlobalTypography(JSON.parse(JSON.stringify(localSets.value)));
  await stylesStore.saveGlobalTypography();
  isDirty.value = false;
  toast.success(t('Tipografia globale salvata'));
}

// Sync from store if it changes externally
watch(() => stylesStore.globalTypography, (newVal) => {
  if (!isDirty.value) {
    localSets.value = JSON.parse(JSON.stringify(newVal || []));
  }
}, { deep: true });
</script>

<style scoped>
.gtp-root {
  padding: 12px;
}

.gtp-header {
  margin-bottom: 12px;
}

.gtp-title {
  font-size: 13px;
  font-weight: 600;
  color: #e5e7eb;
  margin: 0 0 4px 0;
}

.gtp-desc {
  font-size: 11px;
  color: #9ca3af;
  margin: 0;
  line-height: 1.4;
}

.gtp-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 12px;
}

.gtp-item {
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid #374151;
  border-radius: 6px;
  padding: 10px;
}

.gtp-item-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.gtp-fields {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.gtp-field {
  display: flex;
  align-items: center;
  gap: 8px;
}

.gtp-field-label {
  font-size: 10px;
  color: #9ca3af;
  width: 90px;
  flex-shrink: 0;
}

.gtp-input {
  width: 100%;
  background: #111827;
  border: 1px solid #374151;
  border-radius: 4px;
  padding: 4px 6px;
  font-size: 11px;
  color: #e5e7eb;
  outline: none;
  font-family: inherit;
}

.gtp-input:focus {
  border-color: var(--olo-color-primary, #6366f1);
}

.gtp-input--label {
  flex: 1;
  font-weight: 600;
  font-size: 12px;
}

.gtp-input--small {
  width: 60px;
  flex: none;
}

.gtp-select {
  flex: 1;
  background: #111827;
  border: 1px solid #374151;
  border-radius: 4px;
  padding: 4px 6px;
  font-size: 11px;
  color: #e5e7eb;
  outline: none;
  font-family: inherit;
  cursor: pointer;
}

.gtp-select:focus {
  border-color: var(--olo-color-primary, #6366f1);
}

.gtp-vars {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-top: 6px;
}

.gtp-var {
  font-size: 9px;
  color: #6b7280;
  font-family: monospace;
  background: rgba(255, 255, 255, 0.04);
  padding: 1px 5px;
  border-radius: 3px;
}

.gtp-preview {
  margin-top: 6px;
  padding: 6px 8px;
  background: #1f2937;
  border-radius: 4px;
  color: #e5e7eb;
  font-size: 14px;
}

.gtp-remove {
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

.gtp-remove:hover {
  color: #ef4444;
  background: rgba(239, 68, 68, 0.1);
}

.gtp-add {
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

.gtp-add:hover {
  border-color: var(--olo-color-primary, #6366f1);
  color: #e5e7eb;
}

.gtp-actions {
  margin-top: 12px;
  display: flex;
  justify-content: flex-end;
}

.gtp-save {
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

.gtp-save:hover {
  opacity: 0.9;
}

.gtp-save:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
