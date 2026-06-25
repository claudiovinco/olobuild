<template>
  <div class="dbp-panel">
    <div class="dbp-header">
      <span class="dbp-title">{{ t('Collegamento dinamico') }}</span>
      <button type="button" class="dbp-close" @click="$emit('close')">{{ t('&times;') }}</button>
    </div>

    <!-- Step 1: Select source -->
    <div class="dbp-section">
      <label class="dbp-label">{{ t('Sorgente') }}</label>
      <FieldSelect ui="dropdown" theme="dark" :model-value="selectedSource" :options="sourceOpts" @update:model-value="onSourceChange" />
    </div>

    <!-- Step 2: Select field -->
    <div v-if="selectedSource" class="dbp-section">
      <label class="dbp-label">{{ t('Campo') }}</label>

      <!-- Manual input for custom_field -->
      <template v-if="fieldsForSource === 'manual'">
        <input
          v-model="selectedField"
          type="text"
          class="dbp-input"
          :placeholder="t('meta_key')"
        />
      </template>

      <!-- ACF grouped fields -->
      <template v-else-if="selectedSource === 'acf' && isGroupedFields">
        <FieldSelect ui="dropdown" theme="dark" :model-value="selectedField" :options="groupedFieldOpts" @update:model-value="selectedField = $event" />
      </template>

      <!-- Standard flat fields -->
      <template v-else-if="Array.isArray(fieldsForSource)">
        <FieldSelect ui="dropdown" theme="dark" :model-value="selectedField" :options="flatFieldOpts" @update:model-value="selectedField = $event" />
      </template>
    </div>

    <!-- Preview -->
    <div v-if="previewValue !== null" class="dbp-preview">
      <label class="dbp-label">{{ t('Anteprima') }}</label>
      <div class="dbp-preview-value">
        <img v-if="previewIsImage" :src="previewValue" alt="" class="dbp-preview-img" />
        <span v-else>{{ previewDisplayValue }}</span>
      </div>
    </div>

    <!-- Actions -->
    <div class="dbp-actions">
      <button
        type="button"
        class="dbp-btn dbp-btn--apply"
        :disabled="!selectedSource || !selectedField"
        @click="applyBinding"
      >{{ t('Applica collegamento') }}</button>
      <button
        v-if="binding"
        type="button"
        class="dbp-btn dbp-btn--remove"
        @click="$emit('select', null)"
      >{{ t('Rimuovi') }}</button>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, watch, onMounted } from 'vue';
import { useDynamicContent } from '@/composables/useDynamicContent';
import FieldSelect from './fields/FieldSelect.vue';

const props = defineProps({
  binding: { type: Object, default: null },
  fieldType: { type: String, default: 'text' },
});

const emit = defineEmits(['select', 'close']);

const { fetchSources, getFieldsForSource, getBindingSources, previewBinding } = useDynamicContent();

const selectedSource = ref(props.binding?.source || '');
const selectedField = ref(props.binding?.field || '');
const previewValue = ref(null);
const previewLoading = ref(false);

const bindingSources = computed(() => getBindingSources());

// Opzioni FieldSelect (label RAW: t() la applica FieldSelect internamente;
// le label dinamiche passano per t() come fallback identità).
const sourceOpts = computed(() => [
  { value: '', label: 'Seleziona sorgente...' },
  ...bindingSources.value.map(s => ({ value: s.value, label: s.label })),
]);

// Cambio sorgente: stessa semantica del vecchio @change (reset del campo solo
// se la sorgente cambia davvero).
function onSourceChange(value) {
  if (value === selectedSource.value) return;
  selectedSource.value = value;
  selectedField.value = '';
}

const fieldsForSource = computed(() => {
  if (!selectedSource.value) return [];
  return getFieldsForSource(selectedSource.value);
});

const flatFieldOpts = computed(() => {
  const f = fieldsForSource.value;
  if (!Array.isArray(f)) return [];
  return [
    { value: '', label: 'Seleziona campo...' },
    ...f.map(x => ({ value: x.key, label: x.label })),
  ];
});

// Campi ACF raggruppati: stessi value (f.key) del vecchio select nativo.
const groupedFieldOpts = computed(() => {
  const f = fieldsForSource.value;
  if (!Array.isArray(f)) return [];
  return [
    { value: '', label: 'Seleziona campo...' },
    ...f.map(g => ({
      group: g.group_label,
      options: (g.fields || []).map(x => ({ value: x.key, label: x.label })),
    })),
  ];
});

const isGroupedFields = computed(() => {
  const f = fieldsForSource.value;
  return Array.isArray(f) && f.length > 0 && f[0]?.group_label;
});

const previewIsImage = computed(() => {
  if (!previewValue.value || typeof previewValue.value !== 'string') return false;
  return /\.(jpg|jpeg|png|gif|webp|svg)(\?|$)/i.test(previewValue.value)
    || previewValue.value.startsWith('http');
});

const previewDisplayValue = computed(() => {
  const val = previewValue.value;
  if (val === null || val === undefined) return '';
  const str = String(val);
  return str.length > 120 ? str.substring(0, 120) + '...' : str;
});

onMounted(() => {
  fetchSources();
});

// Auto-preview when source+field change
watch([selectedSource, selectedField], async () => {
  if (!selectedSource.value || !selectedField.value) {
    previewValue.value = null;
    return;
  }
  previewLoading.value = true;
  const result = await previewBinding(selectedSource.value, selectedField.value);
  previewValue.value = result?.value ?? null;
  previewLoading.value = false;
});

function applyBinding() {
  if (!selectedSource.value || !selectedField.value) return;
  emit('select', {
    source: selectedSource.value,
    field: selectedField.value,
  });
}
</script>

<style scoped>
.dbp-panel {
  margin-top: 6px;
  padding: 10px;
  background: #1e293b;
  border: 1px solid #475569;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dbp-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.dbp-title {
  font-size: 11px;
  font-weight: 600;
  color: #c7d2fe;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.dbp-close {
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 3px;
  background: transparent;
  color: #9ca3af;
  font-size: 14px;
  cursor: pointer;
}

.dbp-close:hover {
  background: #374151;
  color: #fff;
}

.dbp-section {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.dbp-label {
  font-size: 10px;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.dbp-input {
  width: 100%;
  background: #111827;
  border: 1px solid #374151;
  border-radius: 4px;
  padding: 5px 8px;
  font-size: 12px;
  color: #e5e7eb;
}

.dbp-input:focus {
  outline: none;
  border-color: var(--olo-ui-accent, #e8622a);
}

.dbp-preview {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.dbp-preview-value {
  padding: 6px 8px;
  background: #111827;
  border-radius: 4px;
  font-size: 11px;
  color: #d1d5db;
  word-break: break-all;
  max-height: 80px;
  overflow: auto;
}

.dbp-preview-img {
  max-width: 100%;
  max-height: 60px;
  object-fit: cover;
  border-radius: 3px;
}

.dbp-actions {
  display: flex;
  gap: 6px;
}

.dbp-btn {
  flex: 1;
  padding: 6px 0;
  border: none;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
}

.dbp-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.dbp-btn--apply {
  background: var(--olo-ui-accent, #e8622a);
  color: #fff;
}

.dbp-btn--apply:hover:not(:disabled) {
  filter: brightness(0.88);
}

.dbp-btn--remove {
  background: #dc2626;
  color: #fff;
}

.dbp-btn--remove:hover {
  background: #b91c1c;
}
</style>
