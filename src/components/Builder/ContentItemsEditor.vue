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
      {{ t('&#9889; Sorgente dinamica attiva — elementi generati dalla query') }}
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
            <span class="cie-grip" :title="t('Trascina per riordinare')">&#10303;</span>
            <span v-if="thumbField && element[thumbField]" class="cie-thumb">
              <img :src="element[thumbField]" alt="" class="cie-thumb-img" />
            </span>
            <span class="cie-title">{{ getItemLabel(element) }}</span>
            <div class="cie-actions">
              <button
                type="button"
                class="cie-btn"
                :title="t('Duplica')"
                @click.stop="duplicateItem(index)"
              >&#10697;</button>
              <button
                type="button"
                class="cie-btn cie-btn--delete"
                :title="t('Elimina')"
                :disabled="localItems.length <= 1"
                @click.stop="removeItem(index)"
              >{{ t('&times;') }}</button>
              <span class="cie-chevron" :class="{ 'cie-chevron--open': expandedId === element.id }">&#9660;</span>
            </div>
          </div>

          <!-- Expanded editor -->
          <div v-if="expandedId === element.id" class="cie-body">
            <div v-for="field in itemFields" :key="field.key || 'sep-' + field.label" v-show="isFieldVisible(field, element)" class="cie-field">
              <label v-if="field.type !== 'separator'" class="cie-label">{{ field.label }}</label>

              <!-- separator (intestazione di sezione, nessun input) -->
              <div v-if="field.type === 'separator'" class="cie-separator">{{ field.label }}</div>

              <!-- editor (RichTextEditor) -->
              <RichTextEditor
                v-else-if="field.type === 'editor'"
                :modelValue="element[field.key] || ''"
                :mode="field.mode || 'inline'"
                @update:modelValue="updateField(index, field.key, $event)"
              />

              <!-- image picker -->
              <div v-else-if="field.type === 'image'" class="cie-image-picker">
                <div v-if="element[field.key]" class="cie-image-preview">
                  <img :src="element[field.key]" alt="" />
                  <button @click="updateField(index, field.key, ''); updateField(index, field.key + '_id', 0)" class="cie-image-remove">{{ t('&times;') }}</button>
                </div>
                <button @click="pickImage(index, field.key)" class="cie-image-btn">
                  {{ element[field.key] ? 'Cambia immagine' : 'Seleziona immagine' }}
                </button>
              </div>

              <!-- media picker (image + video, auto-filtered by field key) -->
              <div v-else-if="field.type === 'media'" class="cie-image-picker">
                <div v-if="element[field.key]" class="cie-image-preview">
                  <img v-if="!/\.(mp4|webm|ogg)(\?.*)?$/i.test(element[field.key])" :src="element[field.key]" alt="" />
                  <div v-else class="cie-media-video-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                    <span class="cie-media-filename">{{ element[field.key].split('/').pop() }}</span>
                  </div>
                  <button @click="updateField(index, field.key, ''); updateField(index, field.key + '_id', 0)" class="cie-image-remove">{{ t('&times;') }}</button>
                </div>
                <button @click="pickMedia(index, field.key)" class="cie-image-btn"
                  :title="mediaPrimaryTitle(field)">
                  {{ mediaButtonLabel(element[field.key], field) }}
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
              <FieldColor
                v-else-if="field.type === 'color'"
                :modelValue="element[field.key] || '#000000'"
                @update:modelValue="updateField(index, field.key, $event)"
              />

              <!-- font family (picker con ruoli tema + Google/custom) -->
              <FieldFontFamily
                v-else-if="field.type === 'font-family'"
                :modelValue="element[field.key] || ''"
                @update:modelValue="updateField(index, field.key, $event)"
              />

              <!-- select -->
              <select
                v-else-if="field.type === 'select'"
                :value="element[field.key] || ''"
                @change="updateField(index, field.key, $event.target.value)"
                class="cie-input"
              >
                <option v-for="opt in resolveSelectOptions(field)" :key="opt.value" :value="opt.value">
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
                  :placeholder="t('es. star, home')"
                />
                <button
                  type="button"
                  @click="openIconPicker(index, field.key)"
                  class="cie-icon-btn"
                >{{ t('Sfoglia') }}</button>
              </div>

              <!-- hotspot-position (visual placer) -->
              <button
                v-else-if="field.type === 'hotspot-position'"
                type="button"
                class="cie-placer-btn"
                @click="openPlacer(index)"
              >{{ t('&#9678; Posiziona su PDF') }}</button>

              <!-- toggle (switch standard FieldToggle, allineato all'InspectorField) -->
              <FieldToggle
                v-else-if="field.type === 'toggle'"
                :modelValue="!!element[field.key]"
                @update:modelValue="updateField(index, field.key, $event)"
              />

              <!-- link (autocomplete pagine/post/CPT) -->
              <FieldLink
                v-else-if="field.type === 'link'"
                :modelValue="element[field.key] || ''"
                :placeholder="field.placeholder || ''"
                :types="field.linkTypes || ''"
                @update:modelValue="updateField(index, field.key, $event)"
              />

              <!-- background creativo unificato (solid/gradient/pattern/image/video/gallery) -->
              <BackgroundControls
                v-else-if="field.type === 'background'"
                :modelValue="element[field.key] || { type: 'none' }"
                :showParallax="field.showParallax !== false"
                @update:modelValue="updateField(index, field.key, $event)"
              />

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
import { t } from '@/i18n';
import { ref, computed, watch } from 'vue';
import draggable from 'vuedraggable';
import RichTextEditor from './RichTextEditor.vue';
import FieldColor from './fields/FieldColor.vue';
import FieldFontFamily from './fields/FieldFontFamily.vue';
import FieldLink from './fields/FieldLink.vue';
import FieldToggle from './fields/FieldToggle.vue';
import BackgroundControls from './BackgroundControls.vue';
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

// Risolve le options di un sub-field di tipo 'select' supportando sia
// `field.options` (statiche) sia `field.optionsSource` (dinamiche da oloData).
// Mirror della logica in InspectorField.vue per i sub-field nei content-items.
function resolveSelectOptions(field) {
  if (Array.isArray(field.options) && field.options.length) return field.options;
  if (!field.optionsSource) return [];
  const md = window.oloData || {};
  switch (field.optionsSource) {
    case 'wpMenus':         return (md.wpMenus || []).map(m => ({ value: m.id, label: m.name }));
    case 'postTypes':       return md.postTypes || [];
    case 'taxonomies':      return md.taxonomies || [];
    case 'templates':       return md.templateList || [];
    case 'widgetTemplates': return md.widgetTemplates || [{ value: 0, label: '— Nessun widget —' }];
    case 'wpPages':         return md.wpPages || [];
    case 'serviceList':     return [{ value: '', label: '— Tutti i servizi —' }, ...(md.serviceList || [])];
    case 'globalTypography':return md.globalTypography || [];
    default:                return [];
  }
}

// Ensure every item has a unique id. Without an id, expandedId === undefined
// would match every id-less item at once and all of them would expand together.
function _ensureIds(items) {
  return items.map((item, idx) => {
    if (item && item.id) return item;
    return { ...item, id: 'ci-' + Date.now() + '-' + idx + '-' + Math.random().toString(36).substr(2, 5) };
  });
}

const localItems = ref(_ensureIds(JSON.parse(JSON.stringify(ensureArray(props.modelValue)))));
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
    localItems.value = _ensureIds(JSON.parse(incoming));
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

function mediaButtonLabel(value, field) {
  const type = detectMediaType(field.key, field);
  const labels = {
    image: { pick: 'Seleziona immagine', change: 'Cambia immagine' },
    video: { pick: 'Seleziona video',    change: 'Cambia video' },
    audio: { pick: 'Seleziona audio',    change: 'Cambia audio' },
    'application/pdf': { pick: 'Seleziona PDF', change: 'Cambia PDF' },
    all:   { pick: 'Seleziona media',    change: 'Cambia media' },
  };
  const l = labels[type] || labels.all;
  return value ? l.change : l.pick;
}

function mediaPrimaryTitle(field) {
  const type = detectMediaType(field.key, field);
  const map = { image: 'immagini', video: 'video', audio: 'audio', 'application/pdf': 'PDF', all: 'tutti i media' };
  return 'Apre la libreria filtrata su ' + (map[type] || 'media');
}

function detectMediaType(fieldKey, fieldDef) {
  if (fieldDef && fieldDef.accept) return fieldDef.accept;
  const k = String(fieldKey || '').toLowerCase();
  if (/(^|_)(video)(_|$)|video_url|hover_video|bg_video|front_video|back_video/.test(k)) return 'video';
  if (/(^|_)(audio|sound|music)(_|$)|audio_url/.test(k)) return 'audio';
  if (/(^|_)(pdf)(_|$)|pdf_url/.test(k)) return 'application/pdf';
  if (/(^|_)(image|img|photo|poster|bg_image|hover_image|cover)(_|$)/.test(k)) return 'image';
  return 'all';
}

function pickMedia(index, fieldKey, forceType) {
  if (!window.wp || !window.wp.media) return;
  const fieldDef = props.itemFields.find(f => f.key === fieldKey);
  const type = forceType || detectMediaType(fieldKey, fieldDef);
  const titles = {
    image: 'Seleziona immagine',
    video: 'Seleziona video',
    audio: 'Seleziona audio',
    'application/pdf': 'Seleziona PDF',
    all: 'Seleziona media',
  };
  const frameOpts = {
    title: titles[type] || titles.all,
    button: { text: 'Usa questo media' },
    multiple: false,
  };
  if (type !== 'all') frameOpts.library = { type };
  const frame = wp.media(frameOpts);
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
  if (!pdfUrl) { toast.warning(t('Seleziona prima un file PDF')); return; }
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
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.7);
  overflow: hidden;
}

.cie-item--open {
  border-color: var(--olo-color-primary, #e8622a);
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
  background: rgba(0, 0, 0, 0.03);
}

.cie-grip {
  cursor: grab;
  color: #aaa;
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
  background: rgba(0, 0, 0, 0.06);
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
  color: #1a1a1a;
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
  color: #999;
  cursor: pointer;
  font-size: 14px;
  padding: 0;
}

.cie-btn:hover {
  background: rgba(0, 0, 0, 0.08);
  color: #333;
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
  color: #999;
}

.cie-chevron {
  font-size: 8px;
  color: #aaa;
  transition: transform 0.2s;
  margin-left: 2px;
}

.cie-chevron--open {
  transform: rotate(180deg);
}

.cie-body {
  padding: 8px;
  border-top: 1px solid rgba(0, 0, 0, 0.08);
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
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.cie-separator {
  margin-top: 6px;
  padding-top: 8px;
  border-top: 1px solid rgba(0, 0, 0, 0.08);
  font-size: 10px;
  font-weight: 700;
  color: #888;
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
  border-color: var(--olo-color-primary, #e8622a);
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
  color: #666;
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
  background: rgba(0, 0, 0, 0.05);
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  color: #555;
  font-size: 11px;
  cursor: pointer;
}

.cie-image-btn:hover {
  background: rgba(0, 0, 0, 0.08);
  color: #333;
}

.cie-media-video-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px;
  background: rgba(0, 0, 0, 0.04);
  color: #888;
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
  border: 1px dashed rgba(0, 0, 0, 0.15);
  border-radius: 6px;
  background: transparent;
  color: #999;
  font-size: 12px;
  cursor: pointer;
  transition: border-color 0.15s, color 0.15s;
}

.cie-add:hover {
  border-color: var(--olo-color-primary, #e8622a);
  color: #e8622a;
}

.cie-ghost {
  opacity: 0.4;
  border: 1px dashed var(--olo-color-primary, #e8622a) !important;
}

.cie-dynamic-info {
  padding: 10px 12px;
  background: rgb(var(--olo-primary-rgb, 232 98 42) / 0.08);
  border: 1px solid rgb(var(--olo-primary-rgb, 232 98 42) / 0.2);
  border-radius: 6px;
  font-size: 12px;
  color: #e8622a;
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
  background: rgba(0, 0, 0, 0.06);
  border-radius: 4px;
  flex-shrink: 0;
}
.cie-icon-preview :deep(svg) {
  width: 18px;
  height: 18px;
  color: #555;
  stroke: currentColor;
}
.cie-icon-preview :deep(svg:not([fill="none"])) {
  fill: currentColor;
}
.cie-icon-preview :deep(svg [fill="none"]) {
  fill: none;
}
.cie-icon-empty {
  color: #999;
  font-size: 11px;
}
.cie-icon-input {
  flex: 1;
  min-width: 0;
}
.cie-icon-btn {
  padding: 5px 8px;
  background: rgba(0, 0, 0, 0.05);
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  color: #555;
  font-size: 10px;
  cursor: pointer;
  white-space: nowrap;
  flex-shrink: 0;
}
.cie-icon-btn:hover {
  background: rgba(0, 0, 0, 0.08);
  color: #333;
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
  accent-color: var(--olo-color-primary, #e8622a);
  cursor: pointer;
}

.cie-toggle-label {
  font-size: 11px;
  color: #666;
}

/* Placer button */
.cie-placer-btn {
  width: 100%;
  padding: 6px 10px;
  background: var(--olo-color-primary, #e8622a);
  border: none;
  border-radius: 5px;
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
}
.cie-placer-btn:hover { background: #d4571f; }

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
