<template>
  <div
    ref="canvasWrap"
    class="mb-flex-1 mb-flex mb-items-center mb-justify-center mb-bg-gray-950 mb-overflow-hidden mb-p-4"
    @click.self="$emit('deselect')"
  >
    <div
      ref="canvasEl"
      class="mb-relative mb-shadow-2xl"
      :style="canvasStyle"
      @click.self="$emit('deselect')"
    >
      <!-- Global background (behind slide bg) -->
      <div v-if="globalBackground" class="mb-absolute mb-inset-0 mb-overflow-hidden mb-rounded" style="z-index:0;">
        <img v-if="globalBackground.type === 'image' && globalBackground.image" :src="globalBackground.image" class="mb-w-full mb-h-full mb-object-cover" draggable="false" />
        <video v-else-if="globalBackground.type === 'video' && globalBackground.video && !isYouTube(globalBackground.video)" :src="globalBackground.video" class="mb-w-full mb-h-full mb-object-cover" muted></video>
        <div v-else-if="globalBackground.type !== 'transparent'" class="mb-w-full mb-h-full" :style="globalBgStyle"></div>
      </div>

      <!-- Slide background -->
      <div class="mb-absolute mb-inset-0 mb-overflow-hidden mb-rounded" style="z-index:1;">
        <!-- Image -->
        <img
          v-if="slideBg.type === 'image' && slideBg.image"
          :src="slideBg.image"
          class="mb-w-full mb-h-full mb-object-cover"
          draggable="false"
        />
        <!-- Video (mp4 thumbnail) -->
        <video
          v-else-if="slideBg.type === 'video' && slideBg.video && !isYouTube(slideBg.video)"
          :src="slideBg.video"
          class="mb-w-full mb-h-full mb-object-cover"
          muted
        ></video>
        <!-- Color / Gradient -->
        <div v-else class="mb-w-full mb-h-full" :style="bgColorStyle"></div>

        <!-- Overlay -->
        <div
          v-if="slideBg.overlayOpacity > 0"
          class="mb-absolute mb-inset-0"
          :style="{ background: slideBg.overlay || '#000', opacity: slideBg.overlayOpacity || 0.3 }"
        ></div>
      </div>

      <!-- Layers -->
      <div
        v-for="layer in layers"
        :key="layer.id"
        :class="[
          'mb-absolute mb-cursor-move mb-select-none',
          layer.id === selectedLayerId ? 'mb-ring-2 mb-ring-primary-400 mb-ring-offset-1 mb-ring-offset-transparent' : ''
        ]"
        :style="layerStyle(layer)"
        @pointerdown.stop="startDrag($event, layer)"
        @dblclick.stop="onDblClick(layer)"
      >
        <!-- Text -->
        <component
          v-if="layer.type === 'text'"
          :is="layer.tag || 'h2'"
          :contenteditable="editingLayerId === layer.id"
          :style="textStyle(layer)"
          class="mb-m-0 mb-outline-none"
          @blur="onTextBlur($event, layer)"
          @keydown.enter.prevent="$event.target.blur()"
        >{{ layer.content }}</component>

        <!-- Image -->
        <img
          v-else-if="layer.type === 'image'"
          :src="layer.imageSrc || 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%2280%22><rect fill=%22%23475569%22 width=%22120%22 height=%2280%22/><text x=%2260%22 y=%2244%22 text-anchor=%22middle%22 fill=%22%23cbd5e1%22 font-size=%2213%22>Immagine</text></svg>'"
          class="mb-w-full mb-h-full mb-object-cover mb-pointer-events-none"
          draggable="false"
        />

        <!-- Button -->
        <span
          v-else-if="layer.type === 'button'"
          :style="buttonStyle(layer)"
          class="mb-inline-block mb-cursor-move"
        >{{ layer.content || 'Pulsante' }}</span>

        <!-- Icon -->
        <span
          v-else-if="layer.type === 'icon'"
          class="mps-canvas-icon"
          :style="iconStyle(layer)"
          v-html="getIconSvg(layer.iconName || 'star')"
        ></span>

        <!-- Shape -->
        <div
          v-else-if="layer.type === 'shape'"
          class="mb-w-full mb-h-full"
          :style="shapeStyle(layer)"
        ></div>

        <!-- Video -->
        <video
          v-else-if="layer.type === 'video' && layer.videoSrc && !isYouTube(layer.videoSrc)"
          :src="layer.videoSrc"
          class="mb-w-full mb-h-full mb-object-cover"
          :autoplay="layer.videoAutoplay"
          :muted="layer.videoMuted"
          :loop="layer.videoLoop"
          playsinline
          :style="{ borderRadius: (layer.borderRadius || 0) + 'px' }"
        ></video>
        <div
          v-else-if="layer.type === 'video' && layer.videoSrc && isYouTube(layer.videoSrc)"
          class="mb-w-full mb-h-full mb-flex mb-items-center mb-justify-center mb-bg-gray-800 mb-rounded"
          :style="{ borderRadius: (layer.borderRadius || 0) + 'px' }"
        >
          <span class="mb-text-4xl mb-text-white mb-opacity-60">&#9654;</span>
        </div>
        <div
          v-else-if="layer.type === 'video' && !layer.videoSrc"
          class="mb-w-full mb-h-full mb-flex mb-items-center mb-justify-center mb-bg-gray-700 mb-rounded"
        >
          <span class="mb-text-xs mb-text-gray-400">Video</span>
        </div>

        <!-- Resize handles (selected only) -->
        <template v-if="layer.id === selectedLayerId">
          <div
            v-for="handle in resizeHandles"
            :key="handle"
            :class="'mps-handle mps-handle-' + handle"
            @pointerdown.stop="startResize($event, layer, handle)"
          ></div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import iconsSvg from './uikitIconsSvg.js';

const props = defineProps({
  slide: { type: Object, required: true },
  sliderHeight: { type: Number, default: 600 },
  globalBackground: { type: Object, default: null },
  selectedLayerId: { type: String, default: null },
  editingLayerId: { type: String, default: null },
});

const emit = defineEmits(['deselect', 'select-layer', 'update-layer', 'start-edit', 'stop-edit']);

const canvasWrap = ref(null);
const canvasEl = ref(null);

const CANVAS_W = 1200;
const canvasH = computed(() => props.sliderHeight || 600);

const scale = ref(1);
const resizeHandles = ['nw', 'n', 'ne', 'e', 'se', 's', 'sw', 'w'];

const layers = computed(() => props.slide?.layers || []);
const slideBg = computed(() => props.slide?.background || { type: 'color', color: '#1e293b' });

const canvasStyle = computed(() => ({
  width: CANVAS_W + 'px',
  height: canvasH.value + 'px',
  transform: `scale(${scale.value})`,
  transformOrigin: 'center center',
}));

const bgColorStyle = computed(() => {
  const bg = slideBg.value;
  if (bg.type === 'transparent') {
    return { background: 'transparent' };
  }
  if (bg.type === 'gradient') {
    return { background: `linear-gradient(${bg.gradientAngle || 180}deg, ${bg.gradientFrom || '#1e293b'}, ${bg.gradientTo || '#0f172a'})` };
  }
  return { background: bg.color || '#1e293b' };
});

const globalBgStyle = computed(() => {
  const gb = props.globalBackground;
  if (!gb) return { background: '#1e293b' };
  if (gb.type === 'gradient') {
    return { background: `linear-gradient(${gb.gradientAngle || 180}deg, ${gb.gradientFrom || '#1e293b'}, ${gb.gradientTo || '#0f172a'})` };
  }
  return { background: gb.color || '#1e293b' };
});

function isYouTube(url) {
  return /youtube\.com|youtu\.be/i.test(url || '');
}

function layerStyle(l) {
  return {
    left: l.x + '%',
    top: l.y + '%',
    width: l.width === 'auto' ? 'auto' : l.width + '%',
    height: l.height === 'auto' ? 'auto' : l.height + '%',
    opacity: (l.opacity ?? 100) / 100,
    zIndex: 2,
  };
}

function textStyle(l) {
  return {
    fontSize: l.fontSize + 'px',
    fontWeight: l.fontWeight || '700',
    color: l.color || '#fff',
    textAlign: l.textAlign || 'left',
    backgroundColor: l.bgColor || 'transparent',
    borderRadius: l.borderRadius + 'px',
    padding: l.padding + 'px',
    lineHeight: 1.2,
    whiteSpace: 'pre-wrap',
    wordBreak: 'break-word',
  };
}

function buttonStyle(l) {
  return {
    fontSize: l.fontSize + 'px',
    fontWeight: l.fontWeight || '600',
    color: l.color || '#fff',
    backgroundColor: l.bgColor || '#2563eb',
    borderRadius: l.borderRadius + 'px',
    padding: l.padding + 'px ' + (l.padding * 2) + 'px',
    display: 'inline-block',
    textAlign: 'center',
  };
}

function shapeStyle(l) {
  return {
    backgroundColor: l.bgColor || '#3b82f6',
    borderRadius: l.borderRadius + 'px',
    minWidth: '40px',
    minHeight: '40px',
  };
}

function iconStyle(l) {
  const size = Math.max(24, l.fontSize || 24);
  return {
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    width: size + 'px',
    height: size + 'px',
    color: l.color || '#fff',
  };
}

function getIconSvg(name) {
  return iconsSvg[name] || iconsSvg['star'] || '';
}

// --- Dragging ---
let dragState = null;

function startDrag(e, layer) {
  emit('select-layer', layer.id);
  if (props.editingLayerId === layer.id) return;

  const rect = canvasEl.value.getBoundingClientRect();
  dragState = {
    layerId: layer.id,
    startX: e.clientX,
    startY: e.clientY,
    origX: layer.x,
    origY: layer.y,
    canvasW: rect.width / scale.value,
    canvasH: rect.height / scale.value,
    mode: 'drag',
  };
  e.target.setPointerCapture?.(e.pointerId);
  window.addEventListener('pointermove', onPointerMove);
  window.addEventListener('pointerup', onPointerUp);
}

// --- Resizing ---
function startResize(e, layer, handle) {
  const rect = canvasEl.value.getBoundingClientRect();
  dragState = {
    layerId: layer.id,
    startX: e.clientX,
    startY: e.clientY,
    origX: layer.x,
    origY: layer.y,
    origW: typeof layer.width === 'number' ? layer.width : 80,
    origH: typeof layer.height === 'number' ? layer.height : 20,
    canvasW: rect.width / scale.value,
    canvasH: rect.height / scale.value,
    handle,
    mode: 'resize',
  };
  e.target.setPointerCapture?.(e.pointerId);
  window.addEventListener('pointermove', onPointerMove);
  window.addEventListener('pointerup', onPointerUp);
}

function onPointerMove(e) {
  if (!dragState) return;
  const dx = (e.clientX - dragState.startX) / scale.value;
  const dy = (e.clientY - dragState.startY) / scale.value;

  if (dragState.mode === 'drag') {
    const pctX = (dx / CANVAS_W) * 100;
    const pctY = (dy / canvasH.value) * 100;
    emit('update-layer', dragState.layerId, {
      x: Math.max(0, Math.min(100, dragState.origX + pctX)),
      y: Math.max(0, Math.min(100, dragState.origY + pctY)),
    });
  } else if (dragState.mode === 'resize') {
    const pctX = (dx / CANVAS_W) * 100;
    const pctY = (dy / canvasH.value) * 100;
    const h = dragState.handle;
    const updates = {};

    if (h.includes('e')) updates.width = Math.max(5, dragState.origW + pctX);
    if (h.includes('w')) {
      updates.width = Math.max(5, dragState.origW - pctX);
      updates.x = dragState.origX + pctX;
    }
    if (h.includes('s')) updates.height = Math.max(5, dragState.origH + pctY);
    if (h.includes('n')) {
      updates.height = Math.max(5, dragState.origH - pctY);
      updates.y = dragState.origY + pctY;
    }
    emit('update-layer', dragState.layerId, updates);
  }
}

function onPointerUp() {
  dragState = null;
  window.removeEventListener('pointermove', onPointerMove);
  window.removeEventListener('pointerup', onPointerUp);
}

function onDblClick(layer) {
  if (layer.type === 'text') {
    emit('start-edit', layer.id);
  }
}

function onTextBlur(e, layer) {
  emit('update-layer', layer.id, { content: e.target.textContent });
  emit('stop-edit');
}

// --- Canvas scaling ---
function recalcScale() {
  if (!canvasWrap.value) return;
  const rect = canvasWrap.value.getBoundingClientRect();
  const pad = 32;
  const scaleX = (rect.width - pad * 2) / CANVAS_W;
  const scaleY = (rect.height - pad * 2) / canvasH.value;
  scale.value = Math.min(1, scaleX, scaleY);
}

let resizeObs;
onMounted(() => {
  recalcScale();
  resizeObs = new ResizeObserver(recalcScale);
  if (canvasWrap.value) resizeObs.observe(canvasWrap.value);
});
onUnmounted(() => {
  resizeObs?.disconnect();
  window.removeEventListener('pointermove', onPointerMove);
  window.removeEventListener('pointerup', onPointerUp);
});
</script>

<style scoped>
.mps-handle {
  position: absolute;
  width: 10px;
  height: 10px;
  background: #3b82f6;
  border: 1px solid #fff;
  border-radius: 2px;
  z-index: 100;
}
.mps-handle-nw { top: -5px; left: -5px; cursor: nw-resize; }
.mps-handle-n  { top: -5px; left: 50%; margin-left: -5px; cursor: n-resize; }
.mps-handle-ne { top: -5px; right: -5px; cursor: ne-resize; }
.mps-handle-e  { top: 50%; right: -5px; margin-top: -5px; cursor: e-resize; }
.mps-handle-se { bottom: -5px; right: -5px; cursor: se-resize; }
.mps-handle-s  { bottom: -5px; left: 50%; margin-left: -5px; cursor: s-resize; }
.mps-handle-sw { bottom: -5px; left: -5px; cursor: sw-resize; }
.mps-handle-w  { top: 50%; left: -5px; margin-top: -5px; cursor: w-resize; }
.mps-canvas-icon :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
</style>
