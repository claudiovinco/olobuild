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
            <div v-for="field in itemFields" :key="field.key" v-show="isFieldVisible(field, element)" class="cie-field">
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
                  <button @click="updateField(index, field.key, ''); updateField(index, field.key + '_id', 0)" class="cie-image-remove">&times;</button>
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
                  <button @click="updateField(index, field.key, ''); updateField(index, field.key + '_id', 0)" class="cie-image-remove">&times;</button>
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

              <!-- hotspot-position (visual placer) -->
              <button
                v-else-if="field.type === 'hotspot-position'"
                type="button"
                class="cie-placer-btn"
                @click="openPlacer(index)"
              >&#9678; Posiziona su PDF</button>

              <!-- toggle (checkbox) -->
              <label
                v-else-if="field.type === 'toggle'"
                class="cie-toggle"
              >
                <input
                  type="checkbox"
                  :checked="!!element[field.key]"
                  @change="updateField(index, field.key, $event.target.checked)"
                />
                <span class="cie-toggle-label">{{ element[field.key] ? 'Sì' : 'No' }}</span>
              </label>

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

    <!-- Hotspot Placer modal -->
    <Teleport to="body">
      <div v-if="placerOpen" class="cie-placer-overlay" @click.self="closePlacer">
        <div class="cie-placer-modal">
          <div class="cie-placer-header">
            <span class="cie-placer-title">Posiziona hotspot — Pagina {{ placerPage }}</span>
            <div class="cie-placer-nav">
              <button type="button" :disabled="placerPage <= 1" @click="placerGoPage(placerPage - 1)">&laquo; Prec</button>
              <span>{{ placerPage }} / {{ placerTotalPages }}</span>
              <button type="button" :disabled="placerPage >= placerTotalPages" @click="placerGoPage(placerPage + 1)">Succ &raquo;</button>
            </div>
            <button type="button" class="cie-placer-close" @click="closePlacer">&times;</button>
          </div>
          <div class="cie-placer-canvas-area" ref="placerCanvasArea">
            <canvas ref="placerCanvas"></canvas>
            <div
              class="cie-placer-dot-layer"
              ref="placerDotLayer"
              @click="onPlacerClick"
            >
              <div
                v-for="(hs, hi) in placerPageHotspots"
                :key="hi"
                class="cie-placer-dot"
                :class="{ 'cie-placer-dot--active': hs._index === placerItemIndex }"
                :style="{
                  left: hs.x + '%',
                  top: hs.y + '%',
                  backgroundColor: hs.color || tileSettings.hotspot_color || '#EF4444',
                }"
                :title="hs.title || 'Hotspot ' + (hs._index + 1)"
                @click.stop="selectPlacerItem(hs._index)"
              >
                <span class="cie-placer-dot-num">{{ hs._index + 1 }}</span>
              </div>
            </div>
          </div>
          <div class="cie-placer-footer">
            <span class="cie-placer-coords">
              X: {{ placerItem?.x?.toFixed(1) ?? '—' }}% &nbsp; Y: {{ placerItem?.y?.toFixed(1) ?? '—' }}%
            </span>
            <span class="cie-placer-hint">Clicca sulla pagina per riposizionare</span>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import draggable from 'vuedraggable';
import RichTextEditor from './RichTextEditor.vue';
import DynamicQueryPanel from './DynamicQueryPanel.vue';
import IconPicker from '../ProSlider/IconPicker.vue';
import { useToast } from '@/composables/useToast';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { useMediaPicker } from '@/composables/useMediaPicker';

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  itemFields: { type: Array, default: () => [] },
  newItemDefaults: { type: Object, default: () => ({}) },
  itemLabel: { type: String, default: 'Item' },
  tileId: { type: String, default: '' },
  tileSettings: { type: Object, default: () => ({}) },
  supportsDynamic: { type: Boolean, default: false },
  dynamic: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['update:modelValue', 'update:dynamic-query', 'update:dynamic-item-map']);

const tileQuery = computed(() => props.dynamic?._query || {});
const tileItemMap = computed(() => props.dynamic?._itemMap || {});
const isDynamicQueryActive = computed(() => !!tileQuery.value?.enabled);
const { openSingleImage } = useMediaPicker();

const ensureArray = (v) => Array.isArray(v) ? v : [];
const localItems = ref(JSON.parse(JSON.stringify(ensureArray(props.modelValue))));
const expandedId = ref(null);
const iconPickerTarget = ref(null);

// Find first image field key for thumbnail preview in header
const thumbField = computed(() => {
  const f = props.itemFields.find(f => f.type === 'image');
  return f ? f.key : null;
});

// Find first text-like field key for header label
const labelField = computed(() => {
  const f = props.itemFields.find(f => ['text', 'editor', 'select'].includes(f.type));
  return f ? f.key : null;
});

watch(() => props.modelValue, (newVal) => {
  const safe = ensureArray(newVal);
  const incoming = JSON.stringify(safe);
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
  // Per campi select: mostra la label dell'opzione, non il value
  const fieldDef = props.itemFields.find(f => f.key === labelField.value);
  if (fieldDef && fieldDef.type === 'select' && fieldDef.options) {
    const opt = fieldDef.options.find(o => o.value === val);
    if (opt) return opt.label;
  }
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
  openSingleImage(({ url, id }) => {
    updateField(index, fieldKey, url);
    updateField(index, fieldKey + '_id', id);
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
    updateField(index, fieldKey + '_id', attachment.id);
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

/* ─── Hotspot Placer ─── */
const placerOpen = ref(false);
const placerItemIndex = ref(-1);
const placerPage = ref(1);
const placerTotalPages = ref(0);
const placerCanvas = ref(null);
const placerCanvasArea = ref(null);
const placerDotLayer = ref(null);
let _placerPdf = null;

const placerItem = computed(() => {
  if (placerItemIndex.value < 0) return null;
  return localItems.value[placerItemIndex.value] || null;
});

const placerPageHotspots = computed(() => {
  const result = [];
  for (let i = 0; i < localItems.value.length; i++) {
    const hs = localItems.value[i];
    if ((hs.page || 1) === placerPage.value) {
      result.push({ ...hs, _index: i });
    }
  }
  return result;
});

function loadPdfJs() {
  return new Promise((resolve) => {
    if (window.pdfjsLib) return resolve();
    const base = (window.oloData || {}).pluginUrl || '';
    const s = document.createElement('script');
    s.src = base + 'assets/vendor/pdfjs/pdf.min.js';
    s.onload = () => {
      if (window.pdfjsLib) {
        window.pdfjsLib.GlobalWorkerOptions.workerSrc =
          base + 'assets/vendor/pdfjs/pdf.worker.min.js';
      }
      resolve();
    };
    document.head.appendChild(s);
  });
}

async function openPlacer(index) {
  const pdfUrl = props.tileSettings.pdf_url;
  const toast = useToast();
  if (!pdfUrl) { toast.warning('Seleziona prima un file PDF'); return; }
  placerItemIndex.value = index;
  placerPage.value = localItems.value[index]?.page || 1;
  placerOpen.value = true;
  await loadPdfJs();
  if (!window.pdfjsLib) return;
  try {
    _placerPdf = await window.pdfjsLib.getDocument(pdfUrl).promise;
    placerTotalPages.value = _placerPdf.numPages;
    if (placerPage.value > placerTotalPages.value) placerPage.value = 1;
    await renderPlacerPage();
  } catch (e) {
    console.error('PDF load error:', e);
  }
}

async function renderPlacerPage() {
  if (!_placerPdf || !placerCanvas.value) return;
  const page = await _placerPdf.getPage(placerPage.value);
  const area = placerCanvasArea.value;
  if (!area) return;
  const areaW = area.clientWidth - 32;
  const areaH = area.clientHeight - 32;
  const baseVp = page.getViewport({ scale: 1 });
  const scaleW = areaW / baseVp.width;
  const scaleH = areaH / baseVp.height;
  const scale = Math.min(scaleW, scaleH, 2);
  const dpr = window.devicePixelRatio || 1;
  const vp = page.getViewport({ scale: scale * dpr });
  const cssVp = page.getViewport({ scale });
  const canvas = placerCanvas.value;
  canvas.width = Math.round(vp.width);
  canvas.height = Math.round(vp.height);
  canvas.style.width = Math.round(cssVp.width) + 'px';
  canvas.style.height = Math.round(cssVp.height) + 'px';
  const ctx = canvas.getContext('2d');
  await page.render({ canvasContext: ctx, viewport: vp }).promise;
  // Size dot layer to match canvas
  if (placerDotLayer.value) {
    placerDotLayer.value.style.width = canvas.style.width;
    placerDotLayer.value.style.height = canvas.style.height;
  }
}

async function placerGoPage(p) {
  placerPage.value = p;
  await renderPlacerPage();
}

function onPlacerClick(e) {
  if (placerItemIndex.value < 0) return;
  const layer = placerDotLayer.value;
  if (!layer) return;
  const rect = layer.getBoundingClientRect();
  const x = ((e.clientX - rect.left) / rect.width) * 100;
  const y = ((e.clientY - rect.top) / rect.height) * 100;
  const clampedX = Math.max(0, Math.min(100, parseFloat(x.toFixed(1))));
  const clampedY = Math.max(0, Math.min(100, parseFloat(y.toFixed(1))));
  updateField(placerItemIndex.value, 'x', clampedX);
  updateField(placerItemIndex.value, 'y', clampedY);
  updateField(placerItemIndex.value, 'page', placerPage.value);
}

function selectPlacerItem(idx) {
  placerItemIndex.value = idx;
  const item = localItems.value[idx];
  if (item && (item.page || 1) !== placerPage.value) {
    placerGoPage(item.page || 1);
  }
}

function closePlacer() {
  placerOpen.value = false;
  _placerPdf = null;
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

function isFieldVisible(field, element) {
  if (!field.condition) return true;
  const { field: condField, op, value } = field.condition;
  const val = element[condField];
  if (op === 'in') return Array.isArray(value) && value.includes(val);
  if (op === 'eq') return val === value;
  if (op === 'notEmpty') return !!val;
  return val === value;
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
  color: #9CA3AF;
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
  color: #9CA3AF;
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
  color: #9CA3AF;
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

.cie-toggle {
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
}

.cie-toggle input[type="checkbox"] {
  width: 16px;
  height: 16px;
  accent-color: var(--olo-color-primary, #6366f1);
  cursor: pointer;
}

.cie-toggle-label {
  font-size: 11px;
  color: #9ca3af;
}

/* Placer button */
.cie-placer-btn {
  width: 100%;
  padding: 6px 10px;
  background: #4f46e5;
  border: none;
  border-radius: 5px;
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
}
.cie-placer-btn:hover { background: #4338ca; }

/* Placer modal */
.cie-placer-overlay {
  position: fixed;
  inset: 0;
  z-index: 100000;
  background: rgba(0,0,0,.75);
  display: flex;
  align-items: center;
  justify-content: center;
}
.cie-placer-modal {
  width: 90vw;
  max-width: 1000px;
  height: 85vh;
  background: #1f2937;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,.5);
}
.cie-placer-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  background: #111827;
  flex-shrink: 0;
}
.cie-placer-title {
  font-size: 13px;
  font-weight: 600;
  color: #e5e7eb;
  flex: 1;
}
.cie-placer-nav {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #9ca3af;
}
.cie-placer-nav button {
  background: #374151;
  border: 1px solid #4b5563;
  border-radius: 4px;
  padding: 3px 10px;
  color: #d1d5db;
  font-size: 11px;
  cursor: pointer;
}
.cie-placer-nav button:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}
.cie-placer-nav button:not(:disabled):hover {
  background: #4b5563;
}
.cie-placer-close {
  background: none;
  border: none;
  color: #9ca3af;
  font-size: 22px;
  cursor: pointer;
  padding: 0 4px;
  line-height: 1;
}
.cie-placer-close:hover { color: #fff; }
.cie-placer-canvas-area {
  flex: 1;
  overflow: auto;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  padding: 16px;
  background: #374151;
}
.cie-placer-canvas-area canvas {
  display: block;
  box-shadow: 0 4px 20px rgba(0,0,0,.3);
}
.cie-placer-dot-layer {
  position: absolute;
  cursor: crosshair;
}
.cie-placer-dot {
  position: absolute;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  transform: translate(-50%, -50%);
  cursor: pointer;
  box-shadow: 0 0 0 2px #fff, 0 2px 8px rgba(0,0,0,.4);
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.12s;
}
.cie-placer-dot:hover { transform: translate(-50%, -50%) scale(1.3); }
.cie-placer-dot--active {
  box-shadow: 0 0 0 3px #fbbf24, 0 0 12px rgba(251,191,36,.5);
  transform: translate(-50%, -50%) scale(1.25);
  z-index: 3;
}
.cie-placer-dot--active:hover {
  transform: translate(-50%, -50%) scale(1.4);
}
.cie-placer-dot-num {
  font-size: 9px;
  font-weight: 700;
  color: #fff;
  text-shadow: 0 1px 2px rgba(0,0,0,.5);
  pointer-events: none;
}
.cie-placer-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 16px;
  background: #111827;
  flex-shrink: 0;
}
.cie-placer-coords {
  font-size: 12px;
  font-weight: 600;
  color: #fbbf24;
  font-family: monospace;
}
.cie-placer-hint {
  font-size: 11px;
  color: #9CA3AF;
}
</style>
