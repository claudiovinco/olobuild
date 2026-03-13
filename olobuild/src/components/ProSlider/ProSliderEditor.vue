<template>
  <Teleport to="body">
    <div class="mps-modal-overlay" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:999999;background:#111827;display:flex;flex-direction:column;outline:none;" @keydown="onKeydown" tabindex="-1" ref="modalEl">
      <!-- Header bar -->
      <div class="mb-flex mb-items-center mb-justify-between mb-bg-gray-800 mb-border-b mb-border-gray-700 mb-px-4 mb-py-2 mb-shrink-0">
        <h2 class="mb-text-sm mb-font-bold mb-text-gray-200">Editor ProSlider</h2>
        <div class="mb-flex mb-items-center mb-gap-2">
          <button @click="save" class="mb-px-4 mb-py-1.5 mb-bg-primary-600 mb-text-white mb-text-xs mb-font-semibold mb-rounded hover:mb-bg-primary-500 mb-transition-colors">
            Salva e chiudi
          </button>
          <button @click="cancel" class="mb-px-3 mb-py-1.5 mb-bg-gray-700 mb-text-gray-300 mb-text-xs mb-rounded hover:mb-bg-gray-600 mb-transition-colors">
            Annulla
          </button>
        </div>
      </div>

      <!-- Main area: 3 columns -->
      <div class="mb-flex mb-flex-1 mb-overflow-hidden">
        <!-- Left: Slide list -->
        <SlideList
          :slides="data.slides"
          :activeIndex="activeSlideIndex"
          :globalBackground="data.globalBackground"
          @select="activeSlideIndex = $event"
          @add="addSlide"
          @duplicate="duplicateSlide"
          @remove="removeSlide"
          @import-slides="importSlides"
        />

        <!-- Center: Canvas + Layer bar + Slide background panel -->
        <div class="mb-flex mb-flex-col mb-flex-1 mb-overflow-hidden">
          <!-- Slide bg controls (collapsible) -->
          <div v-if="showSlideBg" class="mb-bg-gray-850 mb-border-b mb-border-gray-700 mb-p-3">
            <div class="mb-flex mb-items-center mb-justify-between mb-mb-2">
              <span class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Sfondo Slide</span>
              <button @click="showSlideBg = false" class="mb-text-gray-500 mb-text-xs">&times;</button>
            </div>
            <div class="mb-flex mb-gap-3 mb-flex-wrap mb-items-end">
              <!-- Type -->
              <div>
                <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Tipo</label>
                <select :value="currentBg.type" @change="updateBg('type', $event.target.value)" class="mps-sel">
                  <option value="color">Colore</option>
                  <option value="image">Immagine</option>
                  <option value="video">Video</option>
                  <option value="gradient">Gradiente</option>
                  <option value="transparent">Trasparente</option>
                </select>
              </div>
              <!-- Transparent note -->
              <div v-if="currentBg.type === 'transparent'" class="mb-flex mb-items-center mb-gap-1">
                <span class="mb-text-[10px] mb-text-gray-400 mb-italic">Sarà visibile lo sfondo globale</span>
              </div>
              <!-- Color -->
              <div v-if="currentBg.type === 'color'">
                <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Colore</label>
                <FieldColor :modelValue="currentBg.color || '#ffffff'" @update:modelValue="updateBg('color', $event)" />
              </div>
              <!-- Image -->
              <div v-if="currentBg.type === 'image'" class="mb-flex mb-gap-2 mb-items-end">
                <div>
                  <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Immagine</label>
                  <div class="mb-flex mb-gap-1">
                    <input :value="currentBg.image" @input="updateBg('image', $event.target.value)" class="mps-inp mb-w-48" placeholder="URL" />
                    <button @click="pickSlideBg" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-[10px] mb-text-gray-300">Sfoglia</button>
                  </div>
                </div>
                <label class="mb-flex mb-items-center mb-gap-1">
                  <input type="checkbox" :checked="currentBg.kenBurns" @change="updateBg('kenBurns', $event.target.checked)" class="mb-rounded" />
                  <span class="mb-text-[10px] mb-text-gray-400">Ken Burns</span>
                </label>
              </div>
              <!-- Video -->
              <div v-if="currentBg.type === 'video'">
                <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">URL Video</label>
                <div class="mb-flex mb-gap-1">
                  <input :value="currentBg.video" @input="updateBg('video', $event.target.value)" class="mps-inp mb-w-52" placeholder="mp4 o URL YouTube" />
                  <button @click="pickSlideVideo" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-[10px] mb-text-gray-300 hover:mb-bg-gray-500">Sfoglia</button>
                </div>
              </div>
              <!-- Gradient -->
              <template v-if="currentBg.type === 'gradient'">
                <div>
                  <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Da</label>
                  <FieldColor :modelValue="currentBg.gradientFrom || '#ffffff'" @update:modelValue="updateBg('gradientFrom', $event)" />
                </div>
                <div>
                  <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">A</label>
                  <FieldColor :modelValue="currentBg.gradientTo || '#000000'" @update:modelValue="updateBg('gradientTo', $event)" />
                </div>
                <div>
                  <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Angolo</label>
                  <input type="number" :value="currentBg.gradientAngle" @input="updateBg('gradientAngle', parseInt($event.target.value))" min="0" max="360" class="mps-inp mb-w-14" />
                </div>
              </template>
              <!-- Slide duration (seconds, 0 = use global) -->
              <div class="mb-flex mb-items-center mb-gap-1">
                <label class="mb-text-[10px] mb-text-gray-400">Durata</label>
                <input type="number" :value="(activeSlide?.duration || 0) / 1000" @input="activeSlide.duration = Math.round((parseFloat($event.target.value) || 0) * 1000)" min="0" max="120" step="0.5" class="mps-sel mb-w-14 mb-text-center" :placeholder="(data.autoplaySpeed / 1000) + ''" />
                <span class="mb-text-[10px] mb-text-gray-400">sec</span>
              </div>
              <!-- Persist for N slides -->
              <div class="mb-flex mb-items-center mb-gap-1">
                <label class="mb-text-[10px] mb-text-gray-400">Persisti per</label>
                <input type="number" :value="activeSlide?.persistFor || 0" @input="activeSlide.persistFor = parseInt($event.target.value) || 0" min="0" max="20" class="mps-sel mb-w-12 mb-text-center" />
                <span class="mb-text-[10px] mb-text-gray-400">slide</span>
              </div>
              <!-- Overlay -->
              <div v-if="currentBg.type !== 'transparent'">
                <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Sovrapposizione</label>
                <FieldColor :modelValue="currentBg.overlay || '#000000'" @update:modelValue="updateBg('overlay', $event)" />
                <div class="mb-flex mb-gap-1 mb-items-center mb-mt-1">
                  <input type="range" :value="(currentBg.overlayOpacity ?? 0.3) * 100" @input="updateBg('overlayOpacity', parseInt($event.target.value) / 100)" min="0" max="100" step="5" class="mb-flex-1" />
                  <span class="mb-text-[10px] mb-text-gray-400">{{ Math.round((currentBg.overlayOpacity ?? 0.3) * 100) }}%</span>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="mb-border-b mb-border-gray-700 mb-px-3 mb-py-1">
            <button @click="showSlideBg = true" class="mb-text-[10px] mb-text-gray-400 hover:mb-text-gray-300">
              Mostra sfondo slide &darr;
            </button>
          </div>

          <!-- Canvas -->
          <SliderCanvas
            :slide="activeSlide"
            :sliderHeight="data.height"
            :globalBackground="data.globalBackground"
            :selectedLayerId="selectedLayerId"
            :editingLayerId="editingLayerId"
            @deselect="selectedLayerId = null"
            @select-layer="selectedLayerId = $event"
            @update-layer="updateLayer"
            @start-edit="editingLayerId = $event"
            @stop-edit="editingLayerId = null"
          />

          <!-- Layer bar -->
          <LayerBar
            :layers="activeSlide?.layers || []"
            :selectedId="selectedLayerId"
            :hiddenIds="hiddenLayerIds"
            @add-layer="addLayer"
            @select="selectedLayerId = $event"
            @remove="removeLayer"
            @toggle-visibility="toggleLayerVisibility"
            @move-up="moveLayerUp"
            @move-down="moveLayerDown"
          />
        </div>

        <!-- Right: Layer properties -->
        <LayerProperties
          :layer="selectedLayer"
          @update="updateLayerProp"
        />
      </div>

      <!-- Footer: Slider settings -->
      <SliderSettings :settings="data" @update="updateGlobal" />
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, reactive, nextTick } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';
import prosliderDef from '@/config/elements/proslider.js';
import SlideList from './SlideList.vue';
import SliderCanvas from './SliderCanvas.vue';
import LayerProperties from './LayerProperties.vue';
import LayerBar from './LayerBar.vue';
import SliderSettings from './SliderSettings.vue';
import FieldColor from '@/components/Builder/fields/FieldColor.vue';

const props = defineProps({
  tileId: { type: String, required: true },
});

const emit = defineEmits(['close']);

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();

const { defaultSlide, defaultLayer, makeId } = prosliderDef.helpers;

// Clone current tile settings into local state
const tile = tilesStore.getTileById(props.tileId);
const data = reactive(JSON.parse(JSON.stringify(tile?.settings || prosliderDef.defaults)));

// Ensure slides array
if (!data.slides || !data.slides.length) {
  data.slides = [defaultSlide()];
}

const activeSlideIndex = ref(0);
const selectedLayerId = ref(null);
const editingLayerId = ref(null);
const showSlideBg = ref(false);
const hiddenLayerIds = reactive(new Set());
const modalEl = ref(null);

const activeSlide = computed(() => data.slides[activeSlideIndex.value] || null);
const currentBg = computed(() => activeSlide.value?.background || {});

const selectedLayer = computed(() => {
  if (!selectedLayerId.value || !activeSlide.value) return null;
  return activeSlide.value.layers.find(l => l.id === selectedLayerId.value) || null;
});

// --- Slide operations ---
function addSlide() {
  data.slides.push(defaultSlide());
  activeSlideIndex.value = data.slides.length - 1;
  selectedLayerId.value = null;
}

function duplicateSlide(idx) {
  const clone = JSON.parse(JSON.stringify(data.slides[idx]));
  clone.id = makeId();
  clone.layers.forEach(l => { l.id = makeId(); });
  data.slides.splice(idx + 1, 0, clone);
  activeSlideIndex.value = idx + 1;
}

function removeSlide(idx) {
  if (data.slides.length <= 1) return;
  data.slides.splice(idx, 1);
  if (activeSlideIndex.value >= data.slides.length) {
    activeSlideIndex.value = data.slides.length - 1;
  }
  selectedLayerId.value = null;
}

function importSlides(json) {
  data.slides = json.slides;
  if (json.globalBackground) {
    data.globalBackground = json.globalBackground;
  }
  activeSlideIndex.value = 0;
  selectedLayerId.value = null;
}

// --- Background ---
function updateBg(key, val) {
  if (activeSlide.value) {
    activeSlide.value.background[key] = val;
  }
}

function pickSlideBg() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona immagine di sfondo', multiple: false, library: { type: 'image' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    updateBg('image', url);
  });
  frame.open();
}

function pickSlideVideo() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona video', multiple: false, library: { type: 'video' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    updateBg('video', url);
  });
  frame.open();
}

// --- Layer operations ---
function addLayer(type) {
  if (!activeSlide.value) return;
  const layer = defaultLayer(type);
  // Offset each new layer slightly
  const count = activeSlide.value.layers.length;
  layer.y = 20 + count * 8;
  layer.animInDelay = count * 200;
  activeSlide.value.layers.push(layer);
  selectedLayerId.value = layer.id;
}

function removeLayer(id) {
  if (!activeSlide.value) return;
  const idx = activeSlide.value.layers.findIndex(l => l.id === id);
  if (idx >= 0) {
    activeSlide.value.layers.splice(idx, 1);
    if (selectedLayerId.value === id) selectedLayerId.value = null;
  }
}

function updateLayer(layerId, updates) {
  if (!activeSlide.value) return;
  const layer = activeSlide.value.layers.find(l => l.id === layerId);
  if (layer) Object.assign(layer, updates);
}

function updateLayerProp(key, val) {
  if (!selectedLayerId.value || !activeSlide.value) return;
  const layer = activeSlide.value.layers.find(l => l.id === selectedLayerId.value);
  if (layer) layer[key] = val;
}

function toggleLayerVisibility(id) {
  if (hiddenLayerIds.has(id)) hiddenLayerIds.delete(id);
  else hiddenLayerIds.add(id);
}

// Move layer toward front (higher z-index = later in array)
function moveLayerUp(id) {
  if (!activeSlide.value) return;
  const layers = activeSlide.value.layers;
  const idx = layers.findIndex(l => l.id === id);
  if (idx < 0 || idx >= layers.length - 1) return;
  const item = layers.splice(idx, 1)[0];
  layers.splice(idx + 1, 0, item);
}

// Move layer toward back (lower z-index = earlier in array)
function moveLayerDown(id) {
  if (!activeSlide.value) return;
  const layers = activeSlide.value.layers;
  const idx = layers.findIndex(l => l.id === id);
  if (idx <= 0) return;
  const item = layers.splice(idx, 1)[0];
  layers.splice(idx - 1, 0, item);
}

// --- Global settings ---
function updateGlobal(key, val) {
  data[key] = val;
}

// --- Save / Cancel ---
function save() {
  // Write back to tile store
  const settings = JSON.parse(JSON.stringify(data));
  // Update all settings keys
  for (const key of Object.keys(settings)) {
    tilesStore.updateTile(props.tileId, { [key]: settings[key] });
  }
  builderStore.isDirty = true;
  emit('close');
}

function cancel() {
  emit('close');
}

// --- Keyboard ---
function onKeydown(e) {
  if (e.key === 'Escape') {
    if (editingLayerId.value) {
      editingLayerId.value = null;
    } else {
      cancel();
    }
  }
  if (e.key === 'Delete' && selectedLayerId.value && !editingLayerId.value) {
    removeLayer(selectedLayerId.value);
  }
}

onMounted(() => {
  nextTick(() => modalEl.value?.focus());
});
</script>

<style scoped>
.mb-bg-gray-850 {
  background: #1a2234;
}
.mps-inp {
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.25rem;
  padding: 3px 6px;
  font-size: 11px;
  color: #111827;
}
.mps-sel {
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.25rem;
  padding: 3px 6px;
  font-size: 11px;
  color: #111827;
}
</style>

<!-- Global: force WP media modal above ProSlider editor -->
<style>
.media-modal { z-index: 1100000 !important; }
.media-modal-backdrop { z-index: 1099999 !important; }
</style>
