<template>
  <div class="olo-typo-tab">
    <div class="olo-card">
      <div class="olo-card-head">
        <div class="olo-card-icon" style="background:#1a1a1a;color:#fff">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/></svg>
        </div>
        <div>
          <h3>Tipografia Globale</h3>
          <p>Definisci set tipografici riutilizzabili tramite variabili CSS</p>
        </div>
      </div>
      <div class="olo-card-body">
        <div class="olo-typosets-list">
          <div v-for="(set, index) in localSets" :key="index" class="olo-typoset">
            <!-- Header set -->
            <div class="olo-typoset-header">
              <input
                type="text"
                :value="set.label"
                @input="updateField(index, 'label', $event.target.value)"
                placeholder="Nome set"
                class="olo-field-input olo-typoset-name"
              />
              <button class="olo-typoset-remove" @click="removeSet(index)" title="Rimuovi set">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>

            <!-- Fields grid -->
            <div class="olo-typoset-fields">
              <div class="olo-typoset-field">
                <label>Font family</label>
                <FieldFontFamily
                  :modelValue="set.family"
                  @update:modelValue="updateField(index, 'family', $event)"
                />
              </div>
              <div class="olo-typoset-field">
                <label>Peso</label>
                <select :value="set.weight" @change="updateField(index, 'weight', $event.target.value)" class="olo-field-input olo-select">
                  <option value="100">100 - Thin</option>
                  <option value="200">200 - Extra Light</option>
                  <option value="300">300 - Light</option>
                  <option value="400">400 - Regular</option>
                  <option value="500">500 - Medium</option>
                  <option value="600">600 - Semi Bold</option>
                  <option value="700">700 - Bold</option>
                  <option value="800">800 - Extra Bold</option>
                  <option value="900">900 - Black</option>
                </select>
              </div>
              <div class="olo-typoset-field">
                <label>Trasformazione</label>
                <select :value="set.transform" @change="updateField(index, 'transform', $event.target.value)" class="olo-field-input olo-select">
                  <option value="none">Nessuna</option>
                  <option value="uppercase">MAIUSCOLO</option>
                  <option value="lowercase">minuscolo</option>
                  <option value="capitalize">Capitalizza</option>
                </select>
              </div>
              <div class="olo-typoset-field">
                <label>Line height</label>
                <input type="text" :value="set.line_height" @change="updateField(index, 'line_height', $event.target.value)" class="olo-field-input olo-input-sm" />
              </div>
              <div class="olo-typoset-field">
                <label>Letter spacing (px)</label>
                <input type="text" :value="set.letter_spacing" @change="updateField(index, 'letter_spacing', $event.target.value)" class="olo-field-input olo-input-sm" />
              </div>
            </div>

            <!-- CSS vars -->
            <div class="olo-typoset-vars">
              <span v-if="set.family" class="olo-var-tag">var(--olo-font-{{ set.id }}-family)</span>
              <span class="olo-var-tag">var(--olo-font-{{ set.id }}-weight)</span>
            </div>

            <!-- Preview -->
            <div
              class="olo-typoset-preview"
              :style="{
                fontFamily: set.family ? (set.family + ', sans-serif') : 'inherit',
                fontWeight: set.weight,
                textTransform: set.transform === 'none' ? 'initial' : set.transform,
                lineHeight: set.line_height,
                letterSpacing: set.letter_spacing + 'px',
              }"
            >
              Anteprima del testo - Abc 123
            </div>
          </div>
        </div>

        <button class="olo-typoset-add" @click="addSet">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Aggiungi set tipografico
        </button>
      </div>
    </div>

    <div class="olo-actions">
      <button @click="save" :disabled="!isDirty || isSaving" class="olo-btn-save" :class="{ disabled: !isDirty }">
        <svg v-if="!isSaving" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <span v-if="isSaving" class="olo-spinner"></span>
        {{ isSaving ? 'Salvataggio...' : 'Salva tipografia' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, inject } from 'vue';
import { useStylesStore } from '@/stores/styles';
import FieldFontFamily from '../Builder/fields/FieldFontFamily.vue';

const stylesStore = useStylesStore();
const showToast = inject('showToast', () => {});

const localSets = ref(JSON.parse(JSON.stringify(stylesStore.globalTypography || [])));

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
  return label.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') || 'set-' + Date.now();
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
    id: 'set-' + n, label: 'Set ' + n, family: '', weight: '400',
    transform: 'none', line_height: '1.5', letter_spacing: '0',
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
  showToast('Tipografia globale salvata');
}

watch(() => stylesStore.globalTypography, (newVal) => {
  if (!isDirty.value) {
    localSets.value = JSON.parse(JSON.stringify(newVal || []));
  }
}, { deep: true });
</script>

<style scoped>
.olo-typosets-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin-bottom: 16px;
}
.olo-typoset {
  background: #f9fafb;
  border: 1px solid #f3f4f6;
  border-radius: 10px;
  padding: 16px;
  transition: border-color 0.15s;
}
.olo-typoset:hover {
  border-color: #e5e7eb;
}
.olo-typoset-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
}
.olo-typoset-name {
  flex: 1;
  font-weight: 600 !important;
  font-size: 14px !important;
}
.olo-typoset-remove {
  background: none;
  border: none;
  color: #d1d5db;
  cursor: pointer;
  padding: 6px;
  border-radius: 6px;
  display: flex;
  transition: all 0.15s;
}
.olo-typoset-remove:hover {
  color: #ef4444;
  background: #fef2f2;
}
.olo-typoset-fields {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}
.olo-typoset-field label {
  display: block;
  font-size: 11px;
  font-weight: 500;
  color: #9ca3af;
  margin-bottom: 4px;
}
.olo-select {
  cursor: pointer;
  appearance: auto;
}
.olo-input-sm {
  width: 80px !important;
}
.olo-typoset-vars {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 12px;
}
.olo-var-tag {
  font-size: 11px;
  color: #9ca3af;
  font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;
  background: #f3f4f6;
  padding: 2px 8px;
  border-radius: 4px;
}
.olo-typoset-preview {
  margin-top: 10px;
  padding: 10px 14px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  color: #374151;
  font-size: 16px;
}
.olo-typoset-add {
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
.olo-typoset-add:hover {
  border-color: #6366f1;
  color: #6366f1;
  background: #fafafe;
}
</style>
