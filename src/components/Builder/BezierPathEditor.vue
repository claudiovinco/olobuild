<template>
  <div class="bp-editor" ref="editorRef">
    <div class="bp-header">
      <span class="bp-title">{{ t('Percorso Bezier') }}</span>
      <div class="bp-zoom">
        <button @click="zoomIn" class="bp-zoom-btn" :title="t('Zoom in (meno range)')">🔍+</button>
        <span class="bp-zoom-label">{{ rangeX }}px</span>
        <button @click="zoomOut" class="bp-zoom-btn" :title="t('Zoom out (più range)')">🔍−</button>
      </div>
      <button @click="panMode = !panMode; if(!panMode) { panX = 0; panY = 0; }" class="bp-pan-btn" :class="{ 'bp-pan-active': panMode }" :title="t('Trascina vista (o tieni Spazio)')">
        &#9995;
      </button>
      <button v-if="panX !== 0 || panY !== 0" @click="panX = 0; panY = 0" class="bp-pan-btn" :title="t('Centra vista')">&#8982;</button>
      <button @click="showPresets = !showPresets" class="bp-pan-btn" :class="{ 'bp-pan-active': showPresets }" :title="t('Preset percorsi')">
        &#9776;
      </button>
      <button @click="resetAll" class="bp-pan-btn bp-reset-btn" :title="t('Reset — nessun percorso')">&#8635;</button>
    </div>

    <!-- Preset grid overlay -->
    <transition name="bp-fade">
      <div v-if="showPresets" class="bp-preset-overlay">
        <div class="bp-preset-grid">
          <div v-for="p in presets" :key="p.label"
            class="bp-preset-card" @click="applyPreset(p); showPresets = false"
          >
            <svg :viewBox="`0 0 ${W} ${H}`" class="bp-preset-svg">
              <line :x1="W/2" :y1="0" :x2="W/2" :y2="H" class="bp-mini-grid" />
              <line :x1="0" :y1="H/2" :x2="W" :y2="H/2" class="bp-mini-grid" />
              <path :d="presetPath(p.keyframes)" class="bp-mini-path" />
              <circle v-for="(kf, ki) in p.keyframes" :key="ki"
                :cx="toSvgX(kf.x)" :cy="toSvgY(kf.y)" r="5"
                :fill="ki === 0 ? '#22c55e' : ki === p.keyframes.length - 1 ? '#ef4444' : '#f59e0b'" />
            </svg>
            <span class="bp-preset-name">{{ p.label }}</span>
          </div>
        </div>
      </div>
    </transition>

    <!-- SVG Canvas -->
    <svg
      ref="svgRef"
      :viewBox="`${panX} ${panY} ${W} ${H}`"
      class="bp-svg"
      :class="{ 'bp-svg-pan': panMode || spaceDown }"
      :style="{ height: svgHeight + 'px' }"
      @mousedown="onSvgMouseDown"
    >
      <!-- Grid -->
      <line v-for="i in 4" :key="'gv'+i" :x1="W*i/5" :y1="0" :x2="W*i/5" :y2="H" class="bp-grid" />
      <line v-for="i in 4" :key="'gh'+i" :x1="0" :y1="H*i/5" :x2="W" :y2="H*i/5" class="bp-grid" />
      <!-- Center crosshair -->
      <line :x1="W/2" :y1="0" :x2="W/2" :y2="H" class="bp-grid bp-grid-center" />
      <line :x1="0" :y1="H/2" :x2="W" :y2="H/2" class="bp-grid bp-grid-center" />

      <!-- Path preview -->
      <path :d="pathD" class="bp-path" />

      <!-- Animated preview dot -->
      <circle v-if="isPlaying" :cx="previewDotX" :cy="previewDotY" r="5" class="bp-preview-dot" />

      <!-- Control handles -->
      <template v-for="(kf, i) in keyframes" :key="'kf'+i">
        <!-- Handle lines -->
        <line v-if="kf.cpOutX != null && i < keyframes.length - 1"
          :x1="toSvgX(kf.x)" :y1="toSvgY(kf.y)"
          :x2="toSvgX(kf.cpOutX)" :y2="toSvgY(kf.cpOutY)"
          class="bp-handle-line" />
        <line v-if="kf.cpInX != null && i > 0"
          :x1="toSvgX(kf.x)" :y1="toSvgY(kf.y)"
          :x2="toSvgX(kf.cpInX)" :y2="toSvgY(kf.cpInY)"
          class="bp-handle-line" />

        <!-- Handle dots -->
        <circle v-if="kf.cpOutX != null && i < keyframes.length - 1"
          :cx="toSvgX(kf.cpOutX)" :cy="toSvgY(kf.cpOutY)" r="4"
          class="bp-handle" :class="{ 'bp-active': dragging && dragTarget === 'cpOut' + i }"
          @mousedown.stop="startDrag($event, 'cpOut', i)" />
        <circle v-if="kf.cpInX != null && i > 0"
          :cx="toSvgX(kf.cpInX)" :cy="toSvgY(kf.cpInY)" r="4"
          class="bp-handle" :class="{ 'bp-active': dragging && dragTarget === 'cpIn' + i }"
          @mousedown.stop="startDrag($event, 'cpIn', i)" />

        <!-- Keyframe point -->
        <circle
          :cx="toSvgX(kf.x)" :cy="toSvgY(kf.y)" r="6"
          class="bp-point" :class="{ 'bp-selected': selectedKf === i }"
          @mousedown.stop="startDrag($event, 'point', i)"
          @click.stop="selectedKf = i"
        />
        <!-- Position label -->
        <text :x="toSvgX(kf.x)" :y="toSvgY(kf.y) - 10" class="bp-label">{{ kf.pos }}%</text>
      </template>
    </svg>
    <!-- Resize handle -->
    <div class="bp-resize-handle" @mousedown.prevent="startResize">
      <span class="bp-resize-grip">⋯</span>
    </div>

    <!-- Preview controls -->
    <div class="bp-preview-bar">
      <button @click="togglePreview" class="bp-play-btn" :class="{ 'bp-playing': isPlaying }">
        <span v-if="!isPlaying">&#9654;</span><span v-else>&#9632;</span>
      </button>
      <div class="bp-progress-track" @click="seekPreview">
        <div class="bp-progress-fill" :style="{ width: previewPct + '%' }"></div>
      </div>
      <span class="bp-pct-label">{{ Math.round(previewPct) }}%</span>
      <div class="bp-speed-btns">
        <button v-for="s in [0.5, 1, 2]" :key="s" @click="playbackSpeed = s"
          class="bp-speed-btn" :class="{ 'bp-speed-active': playbackSpeed === s }">{{ s }}x</button>
      </div>
      <button @click="forceApply" class="bp-apply-btn" :class="{ 'bp-applied': justApplied }" :title="t('Applica percorso al tile')">&#10003;</button>
    </div>

    <!-- Keyframe controls -->
    <div class="bp-controls">
      <div class="bp-kf-list">
        <div v-for="(kf, i) in keyframes" :key="'ctrl'+i"
          class="bp-kf-item" :class="{ 'bp-kf-selected': selectedKf === i }"
          @click="selectedKf = i"
        >
          <span class="bp-kf-dot" :style="{ background: i === 0 ? '#22c55e' : i === keyframes.length-1 ? '#ef4444' : '#f59e0b' }"></span>
          <span class="bp-kf-label">{{ kf.pos }}%</span>
          <span class="bp-kf-coords">X:{{ kf.x }} Y:{{ kf.y }}</span>
          <button v-if="i > 0 && i < keyframes.length - 1" @click.stop="removeKf(i)" class="bp-kf-del" :title="t('Rimuovi')">&times;</button>
        </div>
      </div>
      <button @click="addKf" class="bp-add-btn">+ Aggiungi punto</button>
    </div>

    <!-- Selected keyframe detail -->
    <div v-if="selectedKf != null && keyframes[selectedKf]" class="bp-detail-v2">
      <div class="bp-detail-title">Keyframe {{ selectedKf + 1 }} — {{ keyframes[selectedKf].pos }}% dello scroll</div>

      <!-- Posizione scroll -->
      <div class="bp-prop-row">
        <label class="bp-prop-label">📍 Posizione scroll</label>
        <div class="bp-prop-input">
          <input type="range" :value="keyframes[selectedKf].pos" @input="updateKf(selectedKf, 'pos', +$event.target.value)" min="0" max="100" step="1" class="bp-range" />
          <span class="bp-val">{{ keyframes[selectedKf].pos }}%</span>
        </div>
      </div>

      <!-- Asse X -->
      <div class="bp-prop-row">
        <label class="bp-prop-label">↔ Asse X</label>
        <div class="bp-prop-input">
          <input type="range" :value="keyframes[selectedKf].x" @input="updateKf(selectedKf, 'x', +$event.target.value)" :min="-rangeX" :max="rangeX" step="5" class="bp-range" />
          <span class="bp-val">{{ keyframes[selectedKf].x }}px</span>
        </div>
      </div>

      <!-- Asse Y -->
      <div class="bp-prop-row">
        <label class="bp-prop-label">↕ Asse Y</label>
        <div class="bp-prop-input">
          <input type="range" :value="keyframes[selectedKf].y" @input="updateKf(selectedKf, 'y', +$event.target.value)" :min="-rangeY" :max="rangeY" step="5" class="bp-range" />
          <span class="bp-val">{{ keyframes[selectedKf].y }}px</span>
        </div>
      </div>

      <!-- Scala -->
      <div class="bp-prop-row">
        <label class="bp-prop-label">🔍 Scala</label>
        <div class="bp-prop-input">
          <input type="range" :value="(keyframes[selectedKf].scale ?? 1) * 100" @input="updateKf(selectedKf, 'scale', +$event.target.value / 100)" min="10" max="300" step="5" class="bp-range" />
          <span class="bp-val">{{ Math.round((keyframes[selectedKf].scale ?? 1) * 100) }}%</span>
        </div>
      </div>

      <!-- Rotazione -->
      <div class="bp-prop-row">
        <label class="bp-prop-label">🔄 Rotazione</label>
        <div class="bp-prop-input">
          <input type="range" :value="keyframes[selectedKf].rotate ?? 0" @input="updateKf(selectedKf, 'rotate', +$event.target.value)" min="-360" max="360" step="5" class="bp-range" />
          <span class="bp-val">{{ keyframes[selectedKf].rotate ?? 0 }}°</span>
        </div>
      </div>

      <!-- Opacità -->
      <div class="bp-prop-row">
        <label class="bp-prop-label">👁 Opacità</label>
        <div class="bp-prop-input">
          <input type="range" :value="(keyframes[selectedKf].opacity ?? 1) * 100" @input="updateKf(selectedKf, 'opacity', +$event.target.value / 100)" min="0" max="100" step="5" class="bp-range" />
          <span class="bp-val">{{ Math.round((keyframes[selectedKf].opacity ?? 1) * 100) }}%</span>
        </div>
      </div>

      <!-- Blur -->
      <div class="bp-prop-row">
        <label class="bp-prop-label">💨 Sfocatura</label>
        <div class="bp-prop-input">
          <input type="range" :value="keyframes[selectedKf].blur ?? 0" @input="updateKf(selectedKf, 'blur', +$event.target.value)" min="0" max="20" step="1" class="bp-range" />
          <span class="bp-val">{{ keyframes[selectedKf].blur ?? 0 }}px</span>
        </div>
      </div>
    </div>

    <!-- Target & z-index -->
    <div class="bp-detail-v2">
      <div class="bp-detail-title">Applica a</div>
      <div class="bp-range-btns" style="margin-bottom: 8px">
        <button @click="setBezierTarget('element')" class="bp-range-opt" :class="{ 'bp-range-opt-active': bezierTarget === 'element' }">Elemento</button>
        <button @click="setBezierTarget('background')" class="bp-range-opt" :class="{ 'bp-range-opt-active': bezierTarget === 'background' }">Sfondo</button>
      </div>
      <p v-if="bezierTarget === 'background'" class="bp-range-desc">Il percorso anima l'immagine di sfondo della sezione/riga — l'elemento resta fermo</p>
      <div class="bp-zindex-row">
        <div class="bp-zindex-field">
          <span class="bp-prop-label">Z-index</span>
          <input type="number" :value="zIndex" @change="setZIndex(+$event.target.value)" min="-1" max="9999" step="1" class="bp-num-input" placeholder="auto" />
        </div>
        <label class="bp-mobile-check">
          <input type="checkbox" :checked="mobileEnabled" @change="onMobileToggle" class="bp-mobile-cb" />
          <span>Mobile</span>
        </label>
      </div>
    </div>

    <!-- Scroll range options -->
    <div class="bp-detail-v2">
      <div class="bp-detail-title">Intervallo scroll</div>
      <div class="bp-range-btns">
        <button v-for="opt in scrollRangeOptions" :key="opt.value"
          @click="setScrollRange(opt.value)"
          class="bp-range-opt" :class="{ 'bp-range-opt-active': scrollRange === opt.value }"
        >{{ opt.label }}</button>
      </div>
      <p class="bp-range-desc">{{ scrollRangeDesc }}</p>
      <template v-if="scrollRange === 'custom'">
        <div class="bp-prop-row">
          <label class="bp-prop-label">Inizio pagina</label>
          <div class="bp-prop-input">
            <input type="range" :value="scrollStart" @input="setScrollOpt('scrollStart', +$event.target.value)" min="0" max="100" step="1" class="bp-range" />
            <span class="bp-val">{{ scrollStart }}%</span>
          </div>
        </div>
        <div class="bp-prop-row">
          <label class="bp-prop-label">Fine pagina</label>
          <div class="bp-prop-input">
            <input type="range" :value="scrollEnd" @input="setScrollOpt('scrollEnd', +$event.target.value)" min="0" max="100" step="1" class="bp-range" />
            <span class="bp-val">{{ scrollEnd }}%</span>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  modelValue: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

const W = 280;
const H = 200;
const ZOOM_LEVELS = [150, 300, 600, 1200, 2400];
const zoomIdx = ref(1); // default = 300
const rangeX = computed(() => ZOOM_LEVELS[zoomIdx.value]);
const rangeY = computed(() => Math.round(ZOOM_LEVELS[zoomIdx.value] * 0.66));

function zoomIn() { if (zoomIdx.value > 0) zoomIdx.value--; }
function zoomOut() { if (zoomIdx.value < ZOOM_LEVELS.length - 1) zoomIdx.value++; }

const svgRef = ref(null);
const editorRef = ref(null);
const selectedKf = ref(0);
const dragging = ref(false);
const dragTarget = ref('');
const dragIdx = ref(0);

// SVG canvas height (resizable)
const svgHeight = ref(200);
let isResizing = false;
let resizeStartY = 0;
let resizeStartH = 0;

function startResize(e) {
  isResizing = true;
  resizeStartY = e.clientY;
  resizeStartH = svgHeight.value;
  window.addEventListener('mousemove', onResize);
  window.addEventListener('mouseup', stopResize);
}
function onResize(e) {
  if (!isResizing) return;
  const dy = e.clientY - resizeStartY;
  svgHeight.value = Math.max(120, Math.min(600, resizeStartH + dy));
}
function stopResize() {
  isResizing = false;
  window.removeEventListener('mousemove', onResize);
  window.removeEventListener('mouseup', stopResize);
}

// Pan state
const panMode = ref(false);
const panX = ref(0);
const panY = ref(0);
const spaceDown = ref(false);
let isPanning = false;
let panStartMouse = { x: 0, y: 0 };
let panStartOffset = { x: 0, y: 0 };

// --- Data ---
const keyframes = ref([
  { pos: 0, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpOutX: 50, cpOutY: 0 },
  { pos: 100, x: 0, y: -100, scale: 1, rotate: 0, opacity: 1, cpInX: -50, cpInY: -100 },
]);

// Load from modelValue
watch(() => props.modelValue, (val) => {
  if (val && val.keyframes && val.keyframes.length >= 2) {
    const kfs = val.keyframes.map(kf => ({ ...kf }));
    // Retrocompat: se qualche pos è null/undefined, distribuisci equi-spaziati 0..100.
    const missing = kfs.some(kf => kf.pos == null || isNaN(kf.pos));
    if (missing) {
      const n = kfs.length;
      kfs.forEach((kf, i) => { kf.pos = n === 1 ? 0 : Math.round((i / (n - 1)) * 100); });
    }
    keyframes.value = kfs;
  }
  if (val) {
    noMobile.value = val.nomobile !== false;
    bezierTarget.value = val.bezierTarget || 'element';
    scrollRange.value = val.scrollRange || 'viewport';
    scrollStart.value = val.scrollStart || 0;
    scrollEnd.value = val.scrollEnd || 100;
    zIndex.value = val.zIndex != null ? val.zIndex : 10;
  }
}, { immediate: true });

// Bezier target: 'element' or 'background'
const bezierTarget = ref('element');
function setBezierTarget(val) { bezierTarget.value = val; emitData(); }

// No mobile flag
const noMobile = ref(true);
const mobileEnabled = computed(() => !noMobile.value);
function onMobileToggle(e) { noMobile.value = !e.target.checked; emitData(); }

// Z-index (default 10 so bezier tile stays above siblings)
const zIndex = ref(10);
function setZIndex(val) { zIndex.value = val; emitData(); }

// Scroll range state
const scrollRange = ref('viewport');
const scrollStart = ref(0);
const scrollEnd = ref(100);

const scrollRangeOptions = [
  { value: 'viewport', label: 'Viewport' },
  { value: 'page', label: 'Intera pagina' },
  { value: 'custom', label: 'Personalizzato' },
];

const scrollRangeDesc = computed(() => {
  if (scrollRange.value === 'page') return 'Animazione da inizio a fine pagina — ideale per elementi che seguono lo scroll';
  if (scrollRange.value === 'custom') return 'Definisci inizio e fine come % dello scroll totale della pagina';
  return 'Animazione mentre il tile attraversa il viewport (comportamento standard)';
});

function setScrollRange(val) {
  scrollRange.value = val;
  emitData();
}

function setScrollOpt(key, val) {
  if (key === 'scrollStart') scrollStart.value = val;
  if (key === 'scrollEnd') scrollEnd.value = val;
  emitData();
}

function emitData() {
  const data = {
    keyframes: keyframes.value.map(kf => ({ ...kf })),
    smooth: props.modelValue?.smooth || 0,
    nomobile: noMobile.value,
    bezierTarget: bezierTarget.value,
    scrollRange: scrollRange.value,
    zIndex: zIndex.value,
  };
  if (scrollRange.value === 'custom') {
    data.scrollStart = scrollStart.value;
    data.scrollEnd = scrollEnd.value;
  }
  emit('update:modelValue', data);
}

// --- Coordinate transforms ---
function toSvgX(px) { return W / 2 + (px / rangeX.value) * (W / 2); }
function toSvgY(px) { return H / 2 - (px / rangeY.value) * (H / 2); }
function fromSvgX(sx) { return ((sx - W / 2) / (W / 2)) * rangeX.value; }
function fromSvgY(sy) { return -((sy - H / 2) / (H / 2)) * rangeY.value; }

// --- SVG Path ---
const pathD = computed(() => {
  const kf = keyframes.value;
  if (kf.length < 2) return '';
  let d = `M ${toSvgX(kf[0].x)} ${toSvgY(kf[0].y)}`;
  for (let i = 1; i < kf.length; i++) {
    const prev = kf[i - 1];
    const curr = kf[i];
    const cp1x = prev.cpOutX != null ? toSvgX(prev.cpOutX) : toSvgX(prev.x);
    const cp1y = prev.cpOutY != null ? toSvgY(prev.cpOutY) : toSvgY(prev.y);
    const cp2x = curr.cpInX != null ? toSvgX(curr.cpInX) : toSvgX(curr.x);
    const cp2y = curr.cpInY != null ? toSvgY(curr.cpInY) : toSvgY(curr.y);
    d += ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${toSvgX(curr.x)} ${toSvgY(curr.y)}`;
  }
  return d;
});

// --- Drag ---
function getSvgCoords(e) {
  const svg = svgRef.value;
  if (!svg) return { x: 0, y: 0 };
  const rect = svg.getBoundingClientRect();
  return {
    x: (e.clientX - rect.left) / rect.width * W,
    y: (e.clientY - rect.top) / rect.height * H,
  };
}

function startDrag(e, type, idx) {
  dragging.value = true;
  dragTarget.value = type + idx;
  dragIdx.value = idx;
  selectedKf.value = idx;
  window.addEventListener('mousemove', onDrag);
  window.addEventListener('mouseup', stopDrag);
}

function onDrag(e) {
  if (!dragging.value) return;
  const coords = getSvgCoords(e);
  const px = Math.round(fromSvgX(coords.x));
  const py = Math.round(fromSvgY(coords.y));
  const idx = dragIdx.value;
  const kf = keyframes.value[idx];

  if (dragTarget.value.startsWith('point')) {
    const dx = px - kf.x;
    const dy = py - kf.y;
    kf.x = px;
    kf.y = py;
    // Move handles with point
    if (kf.cpOutX != null) { kf.cpOutX += dx; kf.cpOutY += dy; }
    if (kf.cpInX != null) { kf.cpInX += dx; kf.cpInY += dy; }
  } else if (dragTarget.value.startsWith('cpOut')) {
    kf.cpOutX = px;
    kf.cpOutY = py;
  } else if (dragTarget.value.startsWith('cpIn')) {
    kf.cpInX = px;
    kf.cpInY = py;
  }
}

function stopDrag() {
  dragging.value = false;
  dragTarget.value = '';
  window.removeEventListener('mousemove', onDrag);
  window.removeEventListener('mouseup', stopDrag);
  emitData();
}

function onSvgMouseDown(e) {
  // Pan with middle button, or when panMode/space is active
  if (e.button === 1 || panMode.value || spaceDown.value) {
    e.preventDefault();
    isPanning = true;
    panStartMouse = { x: e.clientX, y: e.clientY };
    panStartOffset = { x: panX.value, y: panY.value };
    window.addEventListener('mousemove', onPanMove);
    window.addEventListener('mouseup', onPanEnd);
  }
}

function onPanMove(e) {
  if (!isPanning) return;
  const svg = svgRef.value;
  if (!svg) return;
  const rect = svg.getBoundingClientRect();
  // Convert pixel delta to viewBox units
  const dx = (e.clientX - panStartMouse.x) / rect.width * W;
  const dy = (e.clientY - panStartMouse.y) / rect.height * H;
  panX.value = panStartOffset.x - dx;
  panY.value = panStartOffset.y - dy;
}

function onPanEnd() {
  isPanning = false;
  window.removeEventListener('mousemove', onPanMove);
  window.removeEventListener('mouseup', onPanEnd);
}

function onKeyDown(e) {
  if (e.code === 'Space' && editorRef.value && editorRef.value.contains(document.activeElement || e.target)) {
    e.preventDefault();
    spaceDown.value = true;
  }
}
function onKeyUp(e) {
  if (e.code === 'Space') spaceDown.value = false;
}

onMounted(() => {
  window.addEventListener('keydown', onKeyDown);
  window.addEventListener('keyup', onKeyUp);
});

onBeforeUnmount(() => {
  window.removeEventListener('keydown', onKeyDown);
  window.removeEventListener('keyup', onKeyUp);
});

// --- Keyframe management ---
function addKf() {
  const kf = keyframes.value;
  const last = kf[kf.length - 1];
  const prevLast = kf[kf.length - 2];
  const newPos = Math.round((prevLast.pos + last.pos) / 2);
  const newX = Math.round((prevLast.x + last.x) / 2);
  const newY = Math.round((prevLast.y + last.y) / 2);
  kf.splice(kf.length - 1, 0, {
    pos: newPos, x: newX, y: newY, scale: 1, rotate: 0, opacity: 1,
    cpInX: newX - 30, cpInY: newY, cpOutX: newX + 30, cpOutY: newY,
  });
  selectedKf.value = kf.length - 2;
  emitData();
}

function removeKf(idx) {
  if (keyframes.value.length <= 2) return;
  keyframes.value.splice(idx, 1);
  if (selectedKf.value >= keyframes.value.length) selectedKf.value = keyframes.value.length - 1;
  emitData();
}

function updateKf(idx, key, val) {
  keyframes.value[idx][key] = val;
  emitData();
}

// --- Presets ---
const showPresets = ref(false);

function presetPath(kf) {
  if (kf.length < 2) return '';
  let d = `M ${toSvgX(kf[0].x)} ${toSvgY(kf[0].y)}`;
  for (let i = 1; i < kf.length; i++) {
    const prev = kf[i - 1], curr = kf[i];
    const cp1x = prev.cpOutX != null ? toSvgX(prev.cpOutX) : toSvgX(prev.x);
    const cp1y = prev.cpOutY != null ? toSvgY(prev.cpOutY) : toSvgY(prev.y);
    const cp2x = curr.cpInX != null ? toSvgX(curr.cpInX) : toSvgX(curr.x);
    const cp2y = curr.cpInY != null ? toSvgY(curr.cpInY) : toSvgY(curr.y);
    d += ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${toSvgX(curr.x)} ${toSvgY(curr.y)}`;
  }
  return d;
}

const presets = [
  // --- Movimenti lineari ---
  { label: 'Lineare su', keyframes: [
    { pos: 0, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpOutX: 0, cpOutY: -33 },
    { pos: 100, x: 0, y: -100, scale: 1, rotate: 0, opacity: 1, cpInX: 0, cpInY: -66 },
  ]},
  { label: 'Lineare giù', keyframes: [
    { pos: 0, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpOutX: 0, cpOutY: 33 },
    { pos: 100, x: 0, y: 100, scale: 1, rotate: 0, opacity: 1, cpInX: 0, cpInY: 66 },
  ]},
  { label: 'Da sinistra', keyframes: [
    { pos: 0, x: -150, y: 0, scale: 1, rotate: 0, opacity: 1, cpOutX: -100, cpOutY: 0 },
    { pos: 100, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: -50, cpInY: 0 },
  ]},
  { label: 'Da destra', keyframes: [
    { pos: 0, x: 150, y: 0, scale: 1, rotate: 0, opacity: 1, cpOutX: 100, cpOutY: 0 },
    { pos: 100, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: 50, cpInY: 0 },
  ]},
  { label: 'Diagonale ↗', keyframes: [
    { pos: 0, x: -120, y: 80, scale: 1, rotate: 0, opacity: 1, cpOutX: -40, cpOutY: 27 },
    { pos: 100, x: 120, y: -80, scale: 1, rotate: 0, opacity: 1, cpInX: 40, cpInY: -27 },
  ]},
  // --- Curve ---
  { label: 'Curva S', keyframes: [
    { pos: 0, x: -100, y: 100, scale: 1, rotate: 0, opacity: 1, cpOutX: 100, cpOutY: 100 },
    { pos: 100, x: 100, y: -100, scale: 1, rotate: 0, opacity: 1, cpInX: -100, cpInY: -100 },
  ]},
  { label: 'Arco su', keyframes: [
    { pos: 0, x: -120, y: 0, scale: 1, rotate: 0, opacity: 1, cpOutX: -120, cpOutY: -120 },
    { pos: 100, x: 120, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: 120, cpInY: -120 },
  ]},
  { label: 'Arco giù', keyframes: [
    { pos: 0, x: -120, y: 0, scale: 1, rotate: 0, opacity: 1, cpOutX: -120, cpOutY: 120 },
    { pos: 100, x: 120, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: 120, cpInY: 120 },
  ]},
  { label: 'Parabola', keyframes: [
    { pos: 0, x: -100, y: 80, scale: 1, rotate: 0, opacity: 1, cpOutX: -50, cpOutY: -80 },
    { pos: 100, x: 100, y: 80, scale: 1, rotate: 0, opacity: 1, cpInX: 50, cpInY: -80 },
  ]},
  // --- Oscillanti ---
  { label: 'Altalena', keyframes: [
    { pos: 0, x: -80, y: 0, scale: 1, rotate: -15, opacity: 1, cpOutX: -80, cpOutY: -60 },
    { pos: 50, x: 80, y: 0, scale: 1, rotate: 15, opacity: 1, cpInX: 80, cpInY: -60, cpOutX: 80, cpOutY: 60 },
    { pos: 100, x: -80, y: 0, scale: 1, rotate: -15, opacity: 1, cpInX: -80, cpInY: 60 },
  ]},
  { label: 'Zigzag', keyframes: [
    { pos: 0, x: -80, y: 80, scale: 1, rotate: 0, opacity: 1, cpOutX: -80, cpOutY: 80 },
    { pos: 33, x: 80, y: 20, scale: 1, rotate: 0, opacity: 1, cpInX: 80, cpInY: 20, cpOutX: 80, cpOutY: 20 },
    { pos: 66, x: -80, y: -40, scale: 1, rotate: 0, opacity: 1, cpInX: -80, cpInY: -40, cpOutX: -80, cpOutY: -40 },
    { pos: 100, x: 80, y: -80, scale: 1, rotate: 0, opacity: 1, cpInX: 80, cpInY: -80 },
  ]},
  { label: 'Onda', keyframes: [
    { pos: 0, x: -100, y: 0, scale: 1, rotate: 0, opacity: 1, cpOutX: -50, cpOutY: -80 },
    { pos: 50, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: -50, cpInY: 80, cpOutX: 50, cpOutY: 80 },
    { pos: 100, x: 100, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: 50, cpInY: -80 },
  ]},
  { label: 'Serpentina', keyframes: [
    { pos: 0, x: -60, y: 80, scale: 1, rotate: 0, opacity: 1, cpOutX: 60, cpOutY: 80 },
    { pos: 33, x: 60, y: 30, scale: 1, rotate: 0, opacity: 1, cpInX: -60, cpInY: 30, cpOutX: -60, cpOutY: 30 },
    { pos: 66, x: -60, y: -30, scale: 1, rotate: 0, opacity: 1, cpInX: 60, cpInY: -30, cpOutX: 60, cpOutY: -30 },
    { pos: 100, x: 60, y: -80, scale: 1, rotate: 0, opacity: 1, cpInX: -60, cpInY: -80 },
  ]},
  // --- Circolari ---
  { label: 'Cerchio', keyframes: [
    { pos: 0, x: 0, y: -80, scale: 1, rotate: 0, opacity: 1, cpOutX: 60, cpOutY: -80 },
    { pos: 25, x: 80, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: 80, cpInY: -60, cpOutX: 80, cpOutY: 60 },
    { pos: 50, x: 0, y: 80, scale: 1, rotate: 0, opacity: 1, cpInX: 60, cpInY: 80, cpOutX: -60, cpOutY: 80 },
    { pos: 75, x: -80, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: -80, cpInY: 60, cpOutX: -80, cpOutY: -60 },
    { pos: 100, x: 0, y: -80, scale: 1, rotate: 0, opacity: 1, cpInX: -60, cpInY: -80 },
  ]},
  { label: 'Semicerchio', keyframes: [
    { pos: 0, x: -80, y: 0, scale: 1, rotate: 0, opacity: 1, cpOutX: -80, cpOutY: -70 },
    { pos: 50, x: 0, y: -80, scale: 1, rotate: 0, opacity: 1, cpInX: -40, cpInY: -80, cpOutX: 40, cpOutY: -80 },
    { pos: 100, x: 80, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: 80, cpInY: -70 },
  ]},
  { label: 'Otto', keyframes: [
    { pos: 0, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpOutX: 70, cpOutY: -70 },
    { pos: 25, x: 60, y: -50, scale: 1, rotate: 0, opacity: 1, cpInX: 60, cpInY: -70, cpOutX: 60, cpOutY: -20 },
    { pos: 50, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: 30, cpInY: 0, cpOutX: -70, cpOutY: 70 },
    { pos: 75, x: -60, y: 50, scale: 1, rotate: 0, opacity: 1, cpInX: -60, cpInY: 70, cpOutX: -60, cpOutY: 20 },
    { pos: 100, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: -30, cpInY: 0 },
  ]},
  // --- Effetti speciali ---
  { label: 'Zoom in', keyframes: [
    { pos: 0, x: 0, y: 0, scale: 0.5, rotate: 0, opacity: 0, cpOutX: 0, cpOutY: 0 },
    { pos: 100, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: 0, cpInY: 0 },
  ]},
  { label: 'Zoom out', keyframes: [
    { pos: 0, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpOutX: 0, cpOutY: 0 },
    { pos: 100, x: 0, y: 0, scale: 0.3, rotate: 0, opacity: 0, cpInX: 0, cpInY: 0 },
  ]},
  { label: 'Spirale', keyframes: [
    { pos: 0, x: -80, y: 80, scale: 0.5, rotate: -180, opacity: 0.3, cpOutX: 80, cpOutY: 80 },
    { pos: 50, x: 80, y: 0, scale: 0.8, rotate: 0, opacity: 0.7, cpInX: 80, cpInY: 40, cpOutX: 80, cpOutY: -40 },
    { pos: 100, x: 0, y: -60, scale: 1, rotate: 0, opacity: 1, cpInX: -80, cpInY: -60 },
  ]},
  { label: 'Rotazione', keyframes: [
    { pos: 0, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpOutX: 0, cpOutY: 0 },
    { pos: 100, x: 0, y: 0, scale: 1, rotate: 360, opacity: 1, cpInX: 0, cpInY: 0 },
  ]},
  { label: 'Caduta foglia', keyframes: [
    { pos: 0, x: 60, y: -80, scale: 0.8, rotate: -30, opacity: 1, cpOutX: -40, cpOutY: -60 },
    { pos: 35, x: -50, y: -20, scale: 0.9, rotate: 20, opacity: 1, cpInX: -80, cpInY: -40, cpOutX: 40, cpOutY: 0 },
    { pos: 70, x: 40, y: 40, scale: 0.95, rotate: -10, opacity: 0.9, cpInX: 80, cpInY: 20, cpOutX: -30, cpOutY: 60 },
    { pos: 100, x: -20, y: 80, scale: 1, rotate: 5, opacity: 0.7, cpInX: -60, cpInY: 80 },
  ]},
  { label: 'Fiocco neve', keyframes: [
    { pos: 0, x: 30, y: -80, scale: 0.7, rotate: 0, opacity: 0.8, cpOutX: -40, cpOutY: -60 },
    { pos: 25, x: -40, y: -30, scale: 0.8, rotate: 45, opacity: 0.9, cpInX: -60, cpInY: -40, cpOutX: 30, cpOutY: -20 },
    { pos: 50, x: 50, y: 10, scale: 0.85, rotate: -30, opacity: 0.85, cpInX: 60, cpInY: 0, cpOutX: -20, cpOutY: 30 },
    { pos: 75, x: -30, y: 50, scale: 0.9, rotate: 60, opacity: 0.8, cpInX: -50, cpInY: 40, cpOutX: 20, cpOutY: 60 },
    { pos: 100, x: 10, y: 80, scale: 1, rotate: 0, opacity: 0.6, cpInX: 30, cpInY: 80 },
  ]},
  { label: 'Rimbalzo', keyframes: [
    { pos: 0, x: -80, y: -60, scale: 1, rotate: 0, opacity: 1, cpOutX: -40, cpOutY: 80 },
    { pos: 30, x: 0, y: 60, scale: 1, rotate: 0, opacity: 1, cpInX: -20, cpInY: 60, cpOutX: 20, cpOutY: 60 },
    { pos: 55, x: 50, y: -20, scale: 1, rotate: 0, opacity: 1, cpInX: 30, cpInY: -40, cpOutX: 60, cpOutY: 40 },
    { pos: 75, x: 70, y: 60, scale: 1, rotate: 0, opacity: 1, cpInX: 65, cpInY: 60, cpOutX: 75, cpOutY: 60 },
    { pos: 100, x: 80, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: 80, cpInY: -20 },
  ]},
  { label: 'Pendolo', keyframes: [
    { pos: 0, x: -80, y: -60, scale: 1, rotate: -25, opacity: 1, cpOutX: -80, cpOutY: 40 },
    { pos: 25, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: -40, cpInY: 0, cpOutX: 40, cpOutY: 0 },
    { pos: 50, x: 80, y: -60, scale: 1, rotate: 25, opacity: 1, cpInX: 80, cpInY: 40, cpOutX: 80, cpOutY: 40 },
    { pos: 75, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, cpInX: 40, cpInY: 0, cpOutX: -40, cpOutY: 0 },
    { pos: 100, x: -80, y: -60, scale: 1, rotate: -25, opacity: 1, cpInX: -80, cpInY: 40 },
  ]},
  { label: 'Fluttuante', keyframes: [
    { pos: 0, x: 0, y: 30, scale: 1, rotate: 0, opacity: 1, cpOutX: 40, cpOutY: 30 },
    { pos: 25, x: 30, y: -20, scale: 1.05, rotate: 3, opacity: 1, cpInX: 30, cpInY: 10, cpOutX: 30, cpOutY: -40 },
    { pos: 50, x: 0, y: -40, scale: 1, rotate: 0, opacity: 1, cpInX: 20, cpInY: -40, cpOutX: -20, cpOutY: -40 },
    { pos: 75, x: -30, y: -20, scale: 1.05, rotate: -3, opacity: 1, cpInX: -30, cpInY: -40, cpOutX: -30, cpOutY: 10 },
    { pos: 100, x: 0, y: 30, scale: 1, rotate: 0, opacity: 1, cpInX: -40, cpInY: 30 },
  ]},
];

function applyPreset(p) {
  keyframes.value = p.keyframes.map(kf => ({ ...kf }));
  selectedKf.value = 0;
  emitData();
}

const justApplied = ref(false);
let appliedTimer = null;

function forceApply() {
  emitData();
  justApplied.value = true;
  if (appliedTimer) clearTimeout(appliedTimer);
  appliedTimer = setTimeout(() => { justApplied.value = false; }, 1500);
}

function resetAll() {
  keyframes.value = [
    { pos: 0, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, blur: 0, cpOutX: 0, cpOutY: 0 },
    { pos: 100, x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, blur: 0, cpInX: 0, cpInY: 0 },
  ];
  noMobile.value = true;
  bezierTarget.value = 'element';
  scrollRange.value = 'viewport';
  scrollStart.value = 0;
  scrollEnd.value = 100;
  zIndex.value = 10;
  selectedKf.value = 0;
  panX.value = 0;
  panY.value = 0;
  emitData();
}

// --- Preview animation ---
const isPlaying = ref(false);
const previewPct = ref(0);
const playbackSpeed = ref(1);
const previewDotX = ref(W / 2);
const previewDotY = ref(H / 2);
let animFrame = null;
let animStart = 0;
const ANIM_DURATION = 3000; // base ms for one loop

function bezier2D(t, p0x, p0y, p1x, p1y, p2x, p2y, p3x, p3y) {
  const u = 1 - t;
  return {
    x: u*u*u*p0x + 3*u*u*t*p1x + 3*u*t*t*p2x + t*t*t*p3x,
    y: u*u*u*p0y + 3*u*u*t*p1y + 3*u*t*t*p2y + t*t*t*p3y,
  };
}

function lerp(a, b, t) { return a + (b - a) * t; }

function interpolateAtPct(pct) {
  const kf = keyframes.value;
  if (kf.length < 2) return { x: 0, y: 0, scale: 1, rotate: 0, opacity: 1, blur: 0 };

  // Clamp and find segment
  pct = Math.max(kf[0].pos, Math.min(kf[kf.length - 1].pos, pct));
  let fromIdx = 0;
  for (let i = 0; i < kf.length - 1; i++) {
    if (pct >= kf[i].pos && pct <= kf[i + 1].pos) { fromIdx = i; break; }
  }
  const from = kf[fromIdx];
  const to = kf[fromIdx + 1];
  const range = to.pos - from.pos;
  const t = range > 0 ? (pct - from.pos) / range : 0;

  // 2D bezier for X/Y
  let x, y;
  const hasCurve = from.cpOutX != null || to.cpInX != null;
  if (hasCurve) {
    const cp1x = from.cpOutX ?? from.x;
    const cp1y = from.cpOutY ?? from.y;
    const cp2x = to.cpInX ?? to.x;
    const cp2y = to.cpInY ?? to.y;
    const pt = bezier2D(t, from.x, from.y, cp1x, cp1y, cp2x, cp2y, to.x, to.y);
    x = pt.x;
    y = pt.y;
  } else {
    x = lerp(from.x, to.x, t);
    y = lerp(from.y, to.y, t);
  }

  return {
    x,
    y,
    scale: lerp(from.scale ?? 1, to.scale ?? 1, t),
    rotate: lerp(from.rotate ?? 0, to.rotate ?? 0, t),
    opacity: lerp(from.opacity ?? 1, to.opacity ?? 1, t),
    blur: lerp(from.blur ?? 0, to.blur ?? 0, t),
  };
}

function animLoop(ts) {
  if (!isPlaying.value) return;
  const elapsed = (ts - animStart) * playbackSpeed.value;
  const duration = ANIM_DURATION;
  const pct = (elapsed % duration) / duration * 100;
  previewPct.value = pct;

  const state = interpolateAtPct(pct);

  // Update SVG dot
  previewDotX.value = toSvgX(state.x);
  previewDotY.value = toSvgY(state.y);

  // Apply transform to actual tile in canvas
  applyPreviewToTile(state);

  animFrame = requestAnimationFrame(animLoop);
}

function postToPreviewIframe(data) {
  const iframe = document.querySelector('.olo-live-iframe');
  if (iframe && iframe.contentWindow) {
    iframe.contentWindow.postMessage(Object.assign({ type: 'olo:bezier-preview' }, data), window.location.origin);
  }
}

function applyPreviewToTile(state) {
  if (!props.tileId) return;
  postToPreviewIframe({
    tileId: props.tileId,
    x: state.x,
    y: -state.y, // editor Y-up → CSS Y-down
    scale: state.scale,
    rotate: state.rotate,
    opacity: state.opacity,
    blur: state.blur,
  });
}

function resetTileTransform() {
  if (!props.tileId) return;
  postToPreviewIframe({ tileId: props.tileId, reset: true });
}

function togglePreview() {
  if (isPlaying.value) {
    isPlaying.value = false;
    if (animFrame) cancelAnimationFrame(animFrame);
    resetTileTransform();
    previewPct.value = 0;
  } else {
    isPlaying.value = true;
    animStart = performance.now();
    animFrame = requestAnimationFrame(animLoop);
  }
}

function seekPreview(e) {
  const rect = e.currentTarget.getBoundingClientRect();
  const pct = Math.max(0, Math.min(100, (e.clientX - rect.left) / rect.width * 100));
  previewPct.value = pct;
  const state = interpolateAtPct(pct);
  previewDotX.value = toSvgX(state.x);
  previewDotY.value = toSvgY(state.y);
  applyPreviewToTile(state);

  // If not playing, reset tile after 1.5s
  if (!isPlaying.value) {
    setTimeout(resetTileTransform, 1500);
  }
}

onBeforeUnmount(() => {
  if (animFrame) cancelAnimationFrame(animFrame);
  resetTileTransform();
});
</script>

<style scoped>
.bp-editor { background: #1a1b26; border-radius: 8px; padding: 8px; }
.bp-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
.bp-title { font-size: 11px; font-weight: 600; color: #a0a0b0; text-transform: uppercase; letter-spacing: 0.5px; }
.bp-zoom { display: flex; align-items: center; gap: 4px; }
.bp-zoom-btn { width: 28px; height: 22px; border-radius: 4px; border: 1px solid #333; background: #252535; color: #ccc; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; padding: 0; }
.bp-zoom-btn:hover { background: #3a3a50; border-color: var(--olo-ui-accent, #e8622a); }
.bp-zoom-label { font-size: 10px; color: #888; min-width: 38px; text-align: center; }
.bp-presets { display: flex; gap: 3px; }
.bp-preset-btn { width: 26px; height: 26px; border-radius: 4px; border: 1px solid #333; background: #252535; color: #ccc; font-size: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
.bp-preset-btn:hover { background: #3a3a50; border-color: var(--olo-ui-accent, #e8622a); }
.bp-reset-btn { color: #ef4444; border-color: #442222; }
.bp-reset-btn:hover { border-color: #ef4444; background: #2a1515; }

/* Preset overlay grid */
.bp-preset-overlay { position: relative; background: #0e0f1a; border: 1px solid #2a2b3a; border-radius: 8px; padding: 8px; margin-bottom: 6px; max-height: 320px; overflow-y: auto; }
.bp-preset-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; }
.bp-preset-card { display: flex; flex-direction: column; align-items: center; padding: 4px; border-radius: 6px; border: 1px solid #2a2b3a; background: #12131e; cursor: pointer; transition: all 0.15s; }
.bp-preset-card:hover { border-color: var(--olo-ui-accent, #e8622a); background: #1a1b30; transform: scale(1.03); }
.bp-preset-svg { width: 100%; height: 50px; }
.bp-mini-grid { stroke: #1e1f2e; stroke-width: 0.5; }
.bp-mini-path { fill: none; stroke: var(--olo-ui-accent, #e8622a); stroke-width: 2; stroke-linecap: round; }
.bp-preset-name { font-size: 8px; color: #888; margin-top: 2px; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; }
.bp-fade-enter-active, .bp-fade-leave-active { transition: opacity 0.15s, transform 0.15s; }
.bp-fade-enter-from, .bp-fade-leave-to { opacity: 0; transform: translateY(-4px); }

.bp-svg { width: 100%; background: #12131e; border-radius: 6px 6px 0 0; cursor: crosshair; user-select: none; }
.bp-resize-handle { display: flex; justify-content: center; align-items: center; height: 10px; background: #12131e; border-radius: 0 0 6px 6px; cursor: ns-resize; user-select: none; }
.bp-resize-grip { color: #444; font-size: 10px; line-height: 1; letter-spacing: 2px; }
.bp-svg-pan { cursor: grab; }
.bp-svg-pan:active { cursor: grabbing; }
.bp-pan-btn { width: 26px; height: 26px; border-radius: 4px; border: 1px solid #333; background: #252535; color: #ccc; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; padding: 0; }
.bp-pan-btn:hover { background: #3a3a50; border-color: var(--olo-ui-accent, #e8622a); }
.bp-pan-active { background: #1e1e3a; border-color: var(--olo-ui-accent, #e8622a); color: #f6a06b; }
.bp-grid { stroke: #2a2b3a; stroke-width: 0.5; }
.bp-grid-center { stroke: #3a3b50; stroke-width: 0.8; stroke-dasharray: 4 4; }
.bp-path { fill: none; stroke: var(--olo-ui-accent, #e8622a); stroke-width: 2; stroke-linecap: round; }
.bp-point { fill: var(--olo-ui-accent, #e8622a); stroke: #fff; stroke-width: 1.5; cursor: grab; transition: r 0.15s; }
.bp-point:hover, .bp-selected { r: 8; fill: #f0833f; }
.bp-handle { fill: #f59e0b; stroke: #fff; stroke-width: 1; cursor: grab; }
.bp-handle:hover, .bp-active { fill: #fbbf24; r: 5; }
.bp-handle-line { stroke: #f59e0b; stroke-width: 0.8; stroke-dasharray: 3 3; opacity: 0.6; }
.bp-label { fill: #888; font-size: 9px; text-anchor: middle; pointer-events: none; }

.bp-controls { margin-top: 6px; }
.bp-kf-list { display: flex; flex-direction: column; gap: 2px; }
.bp-kf-item { display: flex; align-items: center; gap: 6px; padding: 3px 6px; border-radius: 4px; cursor: pointer; font-size: 10px; color: #888; transition: background 0.15s; }
.bp-kf-item:hover { background: #252535; }
.bp-kf-selected { background: #1e1e3a !important; color: #ccc; }
.bp-kf-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.bp-kf-label { font-weight: 600; color: #aaa; min-width: 32px; }
.bp-kf-coords { color: #666; }
.bp-kf-del { margin-left: auto; background: none; border: none; color: #666; cursor: pointer; font-size: 14px; line-height: 1; }
.bp-kf-del:hover { color: #ef4444; }
.bp-add-btn { width: 100%; margin-top: 4px; padding: 4px; background: #252535; border: 1px dashed #444; border-radius: 4px; color: #888; font-size: 10px; cursor: pointer; transition: all 0.15s; }
.bp-add-btn:hover { background: #2a2a40; border-color: var(--olo-ui-accent, #e8622a); color: #aaa; }

.bp-detail-v2 { margin-top: 8px; padding-top: 8px; border-top: 1px solid #2a2b3a; }
.bp-detail-title { font-size: 10px; font-weight: 600; color: #8888aa; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.3px; }
.bp-prop-row { margin-bottom: 6px; }
.bp-prop-label { display: block; font-size: 10px; color: #999; margin-bottom: 2px; }
.bp-prop-input { display: flex; align-items: center; gap: 8px; }
.bp-range { flex: 1; height: 4px; accent-color: var(--olo-ui-accent, #e8622a); cursor: pointer; }
.bp-val { font-size: 10px; color: #aaa; min-width: 42px; text-align: right; font-variant-numeric: tabular-nums; }

/* Preview animation */
.bp-preview-dot { fill: #22d3ee; stroke: #fff; stroke-width: 1.5; filter: drop-shadow(0 0 4px #22d3ee); }

.bp-preview-bar { display: flex; align-items: center; gap: 6px; margin-top: 6px; padding: 5px 6px; background: #12131e; border-radius: 6px; }
.bp-play-btn { width: 26px; height: 26px; border-radius: 50%; border: 1.5px solid #444; background: #252535; color: #ccc; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0; }
.bp-play-btn:hover { border-color: var(--olo-ui-accent, #e8622a); background: #2a2a40; }
.bp-playing { border-color: #22d3ee; background: #0e3a3f; color: #22d3ee; }
.bp-progress-track { flex: 1; height: 4px; background: #2a2b3a; border-radius: 2px; cursor: pointer; position: relative; overflow: hidden; }
.bp-progress-fill { height: 100%; background: linear-gradient(90deg, var(--olo-ui-accent, #e8622a), #22d3ee); border-radius: 2px; transition: width 0.05s linear; }
.bp-pct-label { font-size: 9px; color: #888; min-width: 28px; text-align: right; font-variant-numeric: tabular-nums; }
.bp-speed-btns { display: flex; gap: 2px; }
.bp-speed-btn { padding: 1px 4px; border-radius: 3px; border: 1px solid #333; background: #1a1b26; color: #777; font-size: 9px; cursor: pointer; transition: all 0.15s; }
.bp-speed-btn:hover { border-color: #555; color: #aaa; }
.bp-speed-active { border-color: var(--olo-ui-accent, #e8622a); color: #f6a06b; background: #1e1e3a; }
.bp-apply-btn { width: 26px; height: 26px; border-radius: 50%; border: 1.5px solid #22c55e; background: #0a2a15; color: #22c55e; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0; font-weight: bold; }
.bp-apply-btn:hover { background: #0f3d1e; border-color: #4ade80; color: #4ade80; }
.bp-applied { background: #22c55e; color: #fff; border-color: #22c55e; transform: scale(1.15); }
.bp-applied:hover { background: #22c55e; color: #fff; }

/* Scroll range selector */
.bp-range-btns { display: flex; gap: 3px; margin-bottom: 6px; }
.bp-range-opt { flex: 1; padding: 4px 2px; border-radius: 4px; border: 1px solid #333; background: #1a1b26; color: #888; font-size: 9px; cursor: pointer; transition: all 0.15s; text-align: center; }
.bp-range-opt:hover { border-color: #555; color: #aaa; }
.bp-range-opt-active { border-color: var(--olo-ui-accent, #e8622a); color: #f6a06b; background: #1e1e3a; }
.bp-range-desc { font-size: 9px; color: #666; margin-bottom: 6px; line-height: 1.3; }
.bp-zindex-row { display: flex; align-items: center; gap: 12px; margin-top: 4px; }
.bp-zindex-field { display: flex; align-items: center; gap: 6px; }
.bp-zindex-field .bp-prop-label { margin: 0; white-space: nowrap; }
.bp-mobile-check { display: flex; align-items: center; gap: 5px; cursor: pointer; font-size: 10px; color: #888; white-space: nowrap; }
.bp-mobile-cb { accent-color: var(--olo-ui-accent, #e8622a); }
.bp-num-input { width: 70px; background: #252535; border: 1px solid #444; border-radius: 4px; padding: 3px 6px; color: #ccc; font-size: 11px; text-align: center; }
.bp-num-input:focus { border-color: var(--olo-ui-accent, #e8622a); outline: none; }
.bp-val-hint { color: #666; font-size: 9px; }
</style>
