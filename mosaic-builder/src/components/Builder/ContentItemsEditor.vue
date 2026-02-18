<template>
  <div class="content-items-editor">
    <!-- Dynamic Query Panel -->
    <DynamicQueryPanel
      v-if="supportsDynamic"
      :query="tileQuery"
      :itemFields="itemFields"
      :itemMap="tileItemMap"
      @update:query="$emit('update:dynamic-query', $event)"
      @update:itemMap="$emit('update:dynamic-item-map', $event)"
    />

    <!-- Dynamic source active message -->
    <div v-if="isDynamicQueryActive" class="cie-dynamic-info">
      &#9889; Sorgente dinamica attiva — elementi generati dalla query
    </div>

    <!-- Static items list (hidden when query active) -->
    <draggable
      v-if="!isDynamicQueryActive"
      :list="localItems"
      item-key="id"
      handle=".cie-grip"
      ghost-class="cie-ghost"
      @end="emitUpdate"
      class="cie-list"
    >
      <template #item="{ element, index }">
        <div class="cie-item" :class="{ 'cie-item--open': expandedId === element.id }">
          <!-- Item header row -->
          <div class="cie-header" @click="toggleExpand(element.id)">
            <span class="cie-grip" title="Trascina per riordinare">&#10303;</span>
            <span v-if="thumbField && element[thumbField]" class="cie-thumb">
              <img :src="element[thumbField]" alt="" class="cie-thumb-img" />
            </span>
            <span class="cie-title">{{ getItemLabel(element) }}</span>
            <div class="cie-actions">
              <button
                type="button"
                class="cie-btn"
                title="Duplica"
                @click.stop="duplicateItem(index)"
              >&#10697;</button>
              <button
                type="button"
                class="cie-btn cie-btn--delete"
                title="Elimina"
                :disabled="localItems.length <= 1"
                @click.stop="removeItem(index)"
              >&times;</button>
              <span class="cie-chevron" :class="{ 'cie-chevron--open': expandedId === element.id }">&#9660;</span>
            </div>
          </div>

          <!-- Expanded editor -->
          <div v-if="expandedId === element.id" class="cie-body">
            <div v-for="field in itemFields" :key="field.key" class="cie-field">
              <label class="cie-label">{{ field.label }}</label>

              <!-- editor (RichTextEditor) -->
              <RichTextEditor
                v-if="field.type === 'editor'"
                :modelValue="element[field.key] || ''"
                :mode="field.mode || 'inline'"
                @update:modelValue="updateField(index, field.key, $event)"
              />

              <!-- image picker -->
              <div v-else-if="field.type === 'image'" class="cie-image-picker">
                <div v-if="element[field.key]" class="cie-image-preview">
                  <img :src="element[field.key]" alt="" />
                  <button @click="updateField(index, field.key, '')" class="cie-image-remove">&times;</button>
                </div>
                <button @click="pickImage(index, field.key)" class="cie-image-btn">
                  {{ element[field.key] ? 'Cambia immagine' : 'Seleziona immagine' }}
                </button>
              </div>

              <!-- media picker (image + video) -->
              <div v-else-if="field.type === 'media'" class="cie-image-picker">
                <div v-if="element[field.key]" class="cie-image-preview">
                  <img v-if="!/\.(mp4|webm|ogg)(\?.*)?$/i.test(element[field.key])" :src="element[field.key]" alt="" />
                  <div v-else class="cie-media-video-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                    <span class="cie-media-filename">{{ element[field.key].split('/').pop() }}</span>
                  </div>
                  <button @click="updateField(index, field.key, '')" class="cie-image-remove">&times;</button>
                </div>
                <button @click="pickMedia(index, field.key)" class="cie-image-btn">
                  {{ element[field.key] ? 'Cambia media' : 'Seleziona media' }}
                </button>
              </div>

              <!-- textarea -->
              <textarea
                v-else-if="field.type === 'textarea'"
                :value="element[field.key] || ''"
                @input="updateField(index, field.key, $event.target.value)"
                class="cie-input cie-textarea"
                rows="3"
              />

              <!-- range -->
              <div v-else-if="field.type === 'range'" class="cie-range-wrap">
                <input
                  type="range"
                  :value="element[field.key] ?? field.min ?? 0"
                  @input="updateField(index, field.key, parseFloat($event.target.value))"
                  :min="field.min ?? 0"
                  :max="field.max ?? 100"
                  :step="field.step ?? 1"
                  class="cie-range"
                />
                <span class="cie-range-val">{{ element[field.key] ?? field.min ?? 0 }}</span>
              </div>

              <!-- number -->
              <input
                v-else-if="field.type === 'number'"
                type="number"
                :value="element[field.key] ?? 0"
                @input="updateField(index, field.key, parseFloat($event.target.value))"
                :min="field.min"
                :max="field.max"
                :step="field.step"
                class="cie-input"
              />

              <!-- color -->
              <div v-else-if="field.type === 'color'" class="cie-color-wrap">
                <input
                  type="color"
                  :value="element[field.key] || '#000000'"
                  @input="updateField(index, field.key, $event.target.value)"
                  class="cie-color-swatch"
                />
                <input
                  type="text"
                  :value="element[field.key] || ''"
                  @change="updateField(index, field.key, $event.target.value)"
                  class="cie-input"
                  placeholder="#000000"
                />
              </div>

              <!-- select -->
              <select
                v-else-if="field.type === 'select'"
                :value="element[field.key] || ''"
                @change="updateField(index, field.key, $event.target.value)"
                class="cie-input"
              >
                <option v-for="opt in (field.options || [])" :key="opt.value" :value="opt.value">
                  {{ opt.label }}
                </option>
              </select>

              <!-- icon picker -->
              <div v-else-if="field.type === 'icon'" class="cie-icon-wrap">
                <span
                  v-if="element[field.key] && getIconSvg(element[field.key])"
                  class="cie-icon-preview"
                  v-html="getIconSvg(element[field.key])"
                ></span>
                <span v-else class="cie-icon-preview cie-icon-empty">?</span>
                <input
                  type="text"
                  :value="element[field.key] || ''"
                  @input="updateField(index, field.key, $event.target.value)"
                  class="cie-input cie-icon-input"
                  placeholder="es. star, home"
                />
                <button
                  type="button"
                  @click="openIconPicker(index, field.key)"
                  class="cie-icon-btn"
                >Sfoglia</button>
              </div>

              <!-- text (default) -->
              <input
                v-else
                type="text"
                :value="element[field.key] || ''"
                @input="updateField(index, field.key, $event.target.value)"
                class="cie-input"
                :placeholder="field.placeholder || ''"
              />
            </div>
          </div>
        </div>
      </template>
    </draggable>

    <button v-if="!isDynamicQueryActive" type="button" class="cie-add" @click="addItem">&#65291; Aggiungi {{ itemLabel }}</button>

    <!-- Icon picker modal -->
    <IconPicker
      v-if="iconPickerTarget"
      @select="onIconSelect"
      @close="iconPickerTarget = null"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import draggable from 'vuedraggable';
import RichTextEditor from './RichTextEditor.vue';
import DynamicQueryPanel from './DynamicQueryPanel.vue';
import IconPicker from '../ProSlider/IconPicker.vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { useMediaPicker } from '@/composables/useMediaPicker';

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  itemFields: { type: Array, default: () => [] },
  newItemDefaults: { type: Object, default: () => ({}) },
  itemLabel: { type: String, default: 'Item' },
  tileId: { type: String, default: '' },
  supportsDynamic: { type: Boolean, default: false },
  dynamic: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['update:modelValue', 'update:dynamic-query', 'update:dynamic-item-map']);

const tileQuery = computed(() => props.dynamic?._query || {});
const tileItemMap = computed(() => props.dynamic?._itemMap || {});
const isDynamicQueryActive = computed(() => !!tileQuery.value?.enabled);
const { openSingleImage } = useMediaPicker();

const localItems = ref(JSON.parse(JSON.stringify(props.modelValue)));
const expandedId = ref(null);
const iconPickerTarget = ref(null);

// Find first image field key for thumbnail preview in header
const thumbField = computed(() => {
  const f = props.itemFields.find(f => f.type === 'image');
  return f ? f.key : null;
});

// Find first text-like field key for header label
const labelField = computed(() => {
  const f = props.itemFields.find(f => ['text', 'editor'].includes(f.type));
  return f ? f.key : null;
});

watch(() => props.modelValue, (newVal) => {
  const incoming = JSON.stringify(newVal);
  if (incoming !== JSON.stringify(localItems.value)) {
    localItems.value = JSON.parse(incoming);
  }
}, { deep: true });

function generateId() {
  return 'ci-' + Date.now() + '-' + Math.random().toString(36).substr(2, 6);
}

function stripHtml(html) {
  const tmp = document.createElement('div');
  tmp.innerHTML = html || '';
  return tmp.textContent || tmp.innerText || '';
}

function getItemLabel(element) {
  if (!labelField.value) return props.itemLabel;
  const val = element[labelField.value];
  if (!val) return 'Senza titolo';
  return stripHtml(String(val)) || 'Senza titolo';
}

function emitUpdate() {
  emit('update:modelValue', JSON.parse(JSON.stringify(localItems.value)));
}

function toggleExpand(id) {
  expandedId.value = expandedId.value === id ? null : id;
}

function updateField(index, field, value) {
  localItems.value[index][field] = value;
  emitUpdate();
}

function pickImage(index, fieldKey) {
  openSingleImage(({ url }) => {
    updateField(index, fieldKey, url);
  });
}

function pickMedia(index, fieldKey) {
  if (!window.wp || !window.wp.media) return;
  const frame = wp.media({
    title: 'Seleziona media',
    button: { text: 'Usa questo media' },
    multiple: false,
  });
  frame.on('select', () => {
    const attachment = frame.state().get('selection').first().toJSON();
    updateField(index, fieldKey, attachment.url);
  });
  frame.open();
}

function getIconSvg(name) {
  return iconsSvg[name] || '';
}

function openIconPicker(index, fieldKey) {
  iconPickerTarget.value = { index, fieldKey };
}

function onIconSelect(name) {
  if (iconPickerTarget.value) {
    updateField(iconPickerTarget.value.index, iconPickerTarget.value.fieldKey, name);
  }
  iconPickerTarget.value = null;
}

function addItem() {
  const newItem = { id: generateId(), ...JSON.parse(JSON.stringify(props.newItemDefaults)) };
  localItems.value.push(newItem);
  expandedId.value = newItem.id;
  emitUpdate();
}

function duplicateItem(index) {
  const source = localItems.value[index];
  const clone = { ...JSON.parse(JSON.stringify(source)), id: generateId() };
  localItems.value.splice(index + 1, 0, clone);
  expandedId.value = clone.id;
  emitUpdate();
}

function removeItem(index) {
  if (localItems.value.length <= 1) return;
  const removed = localItems.value[index];
  if (expandedId.value === removed.id) expandedId.value = null;
  localItems.value.splice(index, 1);
  emitUpdate();
}
</script>

<style scoped>
.cie-list {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.cie-item {
  border: 1px solid #374151;
  border-radius: 6px;
  background: #1f2937;
  overflow: hidden;
}

.cie-item--open {
  border-color: var(--olo-color-primary, #6366f1);
}

.cie-header {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 8px;
  cursor: pointer;
  user-select: none;
}

.cie-header:hover {
  background: rgba(255, 255, 255, 0.03);
}

.cie-grip {
  cursor: grab;
  color: #6b7280;
  font-size: 14px;
  flex-shrink: 0;
}

.cie-grip:active {
  cursor: grabbing;
}

.cie-thumb {
  width: 28px;
  height: 20px;
  border-radius: 3px;
  overflow: hidden;
  flex-shrink: 0;
  background: #374151;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cie-thumb-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.cie-title {
  flex: 1;
  font-size: 12px;
  color: #d1d5db;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 0;
}

.cie-actions {
  display: flex;
  align-items: center;
  gap: 2px;
  flex-shrink: 0;
}

.cie-btn {
  width: 22px;
  height: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 4px;
  background: transparent;
  color: #9ca3af;
  cursor: pointer;
  font-size: 14px;
  padding: 0;
}

.cie-btn:hover {
  background: #374151;
  color: #fff;
}

.cie-btn--delete:hover {
  background: #dc2626;
  color: #fff;
}

.cie-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.cie-btn:disabled:hover {
  background: transparent;
  color: #9ca3af;
}

.cie-chevron {
  font-size: 8px;
  color: #6b7280;
  transition: transform 0.2s;
  margin-left: 2px;
}

.cie-chevron--open {
  transform: rotate(180deg);
}

.cie-body {
  padding: 8px;
  border-top: 1px solid #374151;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.cie-field {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.cie-label {
  font-size: 10px;
  font-weight: 600;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.cie-input {
  width: 100%;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  padding: 6px 8px;
  font-size: 12px;
  color: #111827;
}

.cie-input:focus {
  outline: none;
  border-color: var(--olo-color-primary, #6366f1);
}

.cie-textarea {
  resize: vertical;
  min-height: 48px;
  font-family: inherit;
}

.cie-range-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
}

.cie-range {
  flex: 1;
}

.cie-range-val {
  font-size: 11px;
  color: #9ca3af;
  min-width: 28px;
  text-align: right;
}

.cie-color-wrap {
  display: flex;
  gap: 6px;
  align-items: center;
}

.cie-color-swatch {
  width: 28px;
  height: 28px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  flex-shrink: 0;
}

.cie-image-picker {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.cie-image-preview {
  position: relative;
  border-radius: 4px;
  overflow: hidden;
}

.cie-image-preview img {
  width: 100%;
  height: 60px;
  object-fit: cover;
  display: block;
}

.cie-image-remove {
  position: absolute;
  top: 2px;
  right: 2px;
  width: 18px;
  height: 18px;
  background: #dc2626;
  color: #fff;
  border: none;
  border-radius: 50%;
  font-size: 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cie-image-btn {
  width: 100%;
  padding: 5px;
  background: #374151;
  border: 1px solid #4b5563;
  border-radius: 4px;
  color: #d1d5db;
  font-size: 11px;
  cursor: pointer;
}

.cie-image-btn:hover {
  background: #4b5563;
}

.cie-media-video-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px;
  background: #1f2937;
  color: #9ca3af;
  height: 60px;
}
.cie-media-filename {
  font-size: 10px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.cie-add {
  width: 100%;
  margin-top: 6px;
  padding: 7px 0;
  border: 1px dashed #4b5563;
  border-radius: 6px;
  background: transparent;
  color: #9ca3af;
  font-size: 12px;
  cursor: pointer;
  transition: border-color 0.15s, color 0.15s;
}

.cie-add:hover {
  border-color: var(--olo-color-primary, #6366f1);
  color: #c7d2fe;
}

.cie-ghost {
  opacity: 0.4;
  border: 1px dashed var(--olo-color-primary, #6366f1) !important;
}

.cie-dynamic-info {
  padding: 10px 12px;
  background: rgb(var(--olo-primary-rgb, 99 102 241) / 0.1);
  border: 1px solid rgb(var(--olo-primary-rgb, 99 102 241) / 0.25);
  border-radius: 6px;
  font-size: 12px;
  color: #c7d2fe;
  text-align: center;
}

.cie-icon-wrap {
  display: flex;
  align-items: center;
  gap: 6px;
}
.cie-icon-preview {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  background: #374151;
  border-radius: 4px;
  flex-shrink: 0;
}
.cie-icon-preview :deep(svg) {
  width: 18px;
  height: 18px;
  fill: #d1d5db;
  stroke: #d1d5db;
}
.cie-icon-empty {
  color: #6b7280;
  font-size: 11px;
}
.cie-icon-input {
  flex: 1;
  min-width: 0;
}
.cie-icon-btn {
  padding: 5px 8px;
  background: #374151;
  border: 1px solid #4b5563;
  border-radius: 4px;
  color: #d1d5db;
  font-size: 10px;
  cursor: pointer;
  white-space: nowrap;
  flex-shrink: 0;
}
.cie-icon-btn:hover {
  background: #4b5563;
}
</style>
