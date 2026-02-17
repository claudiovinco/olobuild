<template>
  <div class="dbp-panel">
    <div class="dbp-header">
      <span class="dbp-title">Collegamento dinamico</span>
      <button type="button" class="dbp-close" @click="$emit('close')">&times;</button>
    </div>

    <!-- Step 1: Select source -->
    <div class="dbp-section">
      <label class="dbp-label">Sorgente</label>
      <select v-model="selectedSource" class="dbp-select" @change="selectedField = ''">
        <option value="">Seleziona sorgente...</option>
        <option v-for="s in bindingSources" :key="s.value" :value="s.value">{{ s.label }}</option>
      </select>
    </div>

    <!-- Step 2: Select field -->
    <div v-if="selectedSource" class="dbp-section">
      <label class="dbp-label">Campo</label>

      <!-- Manual input for custom_field -->
      <template v-if="fieldsForSource === 'manual'">
        <input
          v-model="selectedField"
          type="text"
          class="dbp-input"
          placeholder="meta_key"
        />
      </template>

      <!-- ACF grouped fields -->
      <template v-else-if="selectedSource === 'acf' && isGroupedFields">
        <select v-model="selectedField" class="dbp-select">
          <option value="">Seleziona campo...</option>
          <optgroup v-for="group in fieldsForSource" :key="group.group_label" :label="group.group_label">
            <option v-for="f in group.fields" :key="f.key" :value="f.key">{{ f.label }}</option>
          </optgroup>
        </select>
      </template>

      <!-- Standard flat fields -->
      <template v-else-if="Array.isArray(fieldsForSource)">
        <select v-model="selectedField" class="dbp-select">
          <option value="">Seleziona campo...</option>
          <option v-for="f in fieldsForSource" :key="f.key" :value="f.key">{{ f.label }}</option>
        </select>
      </template>
    </div>

    <!-- Preview -->
    <div v-if="previewValue !== null" class="dbp-preview">
      <label class="dbp-label">Anteprima</label>
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
      >Applica collegamento</button>
      <button
        v-if="binding"
        type="button"
        class="dbp-btn dbp-btn--remove"
        @click="$emit('select', null)"
      >Rimuovi</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useDynamicContent } from '@/composables/useDynamicContent';

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

const fieldsForSource = computed(() => {
  if (!selectedSource.value) return [];
  return getFieldsForSource(selectedSource.value);
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

.dbp-select,
.dbp-input {
  width: 100%;
  background: #111827;
  border: 1px solid #374151;
  border-radius: 4px;
  padding: 5px 8px;
  font-size: 12px;
  color: #e5e7eb;
}

.dbp-select:focus,
.dbp-input:focus {
  outline: none;
  border-color: var(--olo-color-primary, #6366f1);
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
  background: var(--olo-color-primary, #6366F1);
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
