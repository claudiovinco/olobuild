<template>
  <div class="mps-timeline mb-bg-gray-850 mb-border-t mb-border-gray-700 mb-select-none" :style="{ height: actualHeight + 'px' }">
    <!-- Resize handle (drag to increase height) -->
    <div
      class="mps-resize-handle"
      @pointerdown="startResize"
    ></div>
    <!-- Header: play controls + info -->
    <div class="mb-flex mb-items-center mb-gap-3 mb-px-3 mb-py-1.5 mb-border-b mb-border-gray-700">
      <!-- Play / Pause -->
      <button
        @click="$emit('toggle-play')"
        class="mb-w-6 mb-h-6 mb-flex mb-items-center mb-justify-center mb-bg-gray-700 mb-rounded hover:mb-bg-gray-600 mb-text-gray-300 mb-transition-colors"
        :title="isPlaying ? 'Pausa' : 'Riproduci'"
      >
        <svg v-if="!isPlaying" viewBox="0 0 16 16" class="mb-w-3 mb-h-3" fill="currentColor"><polygon points="4,2 14,8 4,14"/></svg>
        <svg v-else viewBox="0 0 16 16" class="mb-w-3 mb-h-3" fill="currentColor"><rect x="3" y="2" width="3.5" height="12"/><rect x="9.5" y="2" width="3.5" height="12"/></svg>
      </button>

      <!-- Prev keyframe -->
      <button
        @click="seekPrevKf"
        :disabled="!prevKfTime && prevKfTime !== 0"
        class="mb-w-6 mb-h-6 mb-flex mb-items-center mb-justify-center mb-bg-gray-700 mb-rounded hover:mb-bg-gray-600 mb-text-gray-300 mb-transition-colors disabled:mb-opacity-30 disabled:mb-cursor-default"
        title="Keyframe precedente"
      >
        <svg viewBox="0 0 16 16" class="mb-w-3 mb-h-3" fill="currentColor"><rect x="2" y="3" width="2.5" height="10"/><polygon points="14,3 6,8 14,13"/></svg>
      </button>

      <!-- Next keyframe -->
      <button
        @click="seekNextKf"
        :disabled="!nextKfTime && nextKfTime !== 0"
        class="mb-w-6 mb-h-6 mb-flex mb-items-center mb-justify-center mb-bg-gray-700 mb-rounded hover:mb-bg-gray-600 mb-text-gray-300 mb-transition-colors disabled:mb-opacity-30 disabled:mb-cursor-default"
        title="Keyframe successivo"
      >
        <svg viewBox="0 0 16 16" class="mb-w-3 mb-h-3" fill="currentColor"><polygon points="2,3 10,8 2,13"/><rect x="11.5" y="3" width="2.5" height="10"/></svg>
      </button>

      <!-- Current time -->
      <span class="mb-text-[10px] mb-text-gray-400 mb-font-mono mb-tabular-nums mb-w-20">
        {{ formatTime(playhead) }} / {{ formatTime(maxDuration) }}s
      </span>

      <!-- Selected layer controls (duration, delay, loop) -->
      <template v-if="activeTimeline">
        <label class="mb-flex mb-items-center mb-gap-1">
          <span class="mb-text-[10px] mb-text-gray-500">Durata</span>
          <input
            type="number"
            :value="activeTimeline.duration"
            @change="$emit('update-timeline-prop', 'duration', clampDuration(+$event.target.value))"
            class="mb-w-16 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-1.5 mb-py-0.5 mb-text-[10px] mb-text-gray-300 mb-text-center"
            min="500" max="30000" step="100"
          />
          <span class="mb-text-[10px] mb-text-gray-500">ms</span>
        </label>

        <label class="mb-flex mb-items-center mb-gap-1">
          <span class="mb-text-[10px] mb-text-gray-500">Delay</span>
          <input
            type="number"
            :value="activeTimeline.delay || 0"
            @change="$emit('update-timeline-prop', 'delay', Math.max(0, +$event.target.value))"
            class="mb-w-14 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-1.5 mb-py-0.5 mb-text-[10px] mb-text-gray-300 mb-text-center"
            min="0" max="10000" step="100"
          />
        </label>

        <label class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer">
          <input
            type="checkbox"
            :checked="activeTimeline.loop"
            @change="$emit('update-timeline-prop', 'loop', $event.target.checked)"
            class="mb-rounded mb-w-3 mb-h-3"
          />
          <span class="mb-text-[10px] mb-text-gray-400">Loop</span>
        </label>

        <label class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer" title="Layer OUT aspetta la fine della slide (WAIT)">
          <input
            type="checkbox"
            :checked="activeTimeline.endWithSlide !== false"
            @change="$emit('update-timeline-prop', 'endWithSlide', $event.target.checked)"
            class="mb-rounded mb-w-3 mb-h-3"
          />
          <span class="mb-text-[10px] mb-text-gray-400">WAIT</span>
        </label>

        <button
          @click="addKeyframeAtPlayhead"
          class="mb-ml-auto mb-px-2 mb-py-0.5 mb-bg-gray-700 mb-rounded mb-text-[10px] mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
          title="Aggiungi keyframe al tempo corrente"
        >+ Keyframe</button>
      </template>
      <span v-else class="mb-text-[10px] mb-text-gray-500 mb-italic">Seleziona un layer con timeline</span>

      <!-- Timeline zoom controls -->
      <div class="mb-ml-auto mb-flex mb-items-center mb-gap-1 mb-pl-3 mb-border-l mb-border-gray-600">
        <button
          @click="timelineZoom = Math.max(1, +(timelineZoom - 0.5).toFixed(1))"
          :disabled="timelineZoom <= 1"
          class="mb-w-5 mb-h-5 mb-flex mb-items-center mb-justify-center mb-bg-gray-700 mb-rounded mb-text-[10px] mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors disabled:mb-opacity-30"
          title="Riduci zoom timeline"
        >−</button>
        <span class="mb-text-[10px] mb-text-gray-400 mb-font-mono mb-w-8 mb-text-center">{{ Math.round(timelineZoom * 100) }}%</span>
        <button
          @click="timelineZoom = Math.min(10, +(timelineZoom + 0.5).toFixed(1))"
          :disabled="timelineZoom >= 10"
          class="mb-w-5 mb-h-5 mb-flex mb-items-center mb-justify-center mb-bg-gray-700 mb-rounded mb-text-[10px] mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors disabled:mb-opacity-30"
          title="Aumenta zoom timeline"
        >+</button>
        <button
          v-if="timelineZoom !== 1"
          @click="timelineZoom = 1"
          class="mb-w-5 mb-h-5 mb-flex mb-items-center mb-justify-center mb-bg-gray-700 mb-rounded mb-text-[10px] mb-text-gray-400 hover:mb-bg-gray-600 mb-transition-colors"
          title="Reset zoom"
        >⟲</button>
      </div>

      <!-- Separator + Slide duration controls (always visible) -->
      <div class="mb-flex mb-items-center mb-gap-2 mb-pl-3 mb-border-l mb-border-gray-600">
        <label class="mb-flex mb-items-center mb-gap-1">
          <span class="mb-text-[10px] mb-text-gray-500">Slide</span>
          <input
            type="number"
            :value="effectiveSlideDuration / 1000"
            @change="onSlideDurationChange(+$event.target.value)"
            class="mb-w-14 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-1.5 mb-py-0.5 mb-text-[10px] mb-text-gray-300 mb-text-center"
            min="0.5" max="120" step="0.5"
          />
          <span class="mb-text-[10px] mb-text-gray-500">s</span>
        </label>
        <button
          v-if="maxDuration > effectiveSlideDuration"
          @click="emit('update-slide-duration', maxDuration)"
          class="mb-px-1.5 mb-py-0.5 mb-bg-yellow-600/30 mb-border mb-border-yellow-600/50 mb-rounded mb-text-[10px] mb-text-yellow-300 hover:mb-bg-yellow-600/50 mb-transition-colors"
          :title="'La timeline (' + (maxDuration/1000).toFixed(1) + 's) è più lunga della slide (' + (effectiveSlideDuration/1000).toFixed(1) + 's). Clicca per sincronizzare.'"
        >⚠ Sync</button>
        <button
          v-if="timelineTracks.length > 1"
          @click="emit('set-all-durations', maxDuration)"
          class="mb-px-1.5 mb-py-0.5 mb-bg-gray-700 mb-rounded mb-text-[10px] mb-text-gray-400 hover:mb-bg-gray-600 mb-transition-colors"
          title="Imposta tutte le durate layer alla durata massima"
        >= Tutti</button>
      </div>
    </div>

    <!-- Multi-track area -->
    <div class="mb-flex mb-flex-1 mb-overflow-hidden" style="min-height:0;">
      <!-- Layer names column -->
      <div class="mb-flex mb-flex-col mb-shrink-0 mb-border-r mb-border-gray-700 mb-relative" :style="{ width: namesColWidth + 'px' }">
        <!-- Resize handle laterale -->
        <div class="mps-resize-handle-side" @pointerdown="startResizeSide"></div>
        <!-- Ruler spacer -->
        <div style="height:18px;" class="mb-border-b mb-border-gray-700"></div>
        <!-- Track labels -->
        <div
          v-for="track in timelineTracks"
          :key="track.layer.id"
          class="mb-flex mb-items-center mb-px-2 mb-cursor-pointer mb-transition-colors mb-border-b mb-border-gray-800"
          :class="track.layer.id === selectedLayerId ? 'mb-bg-gray-700' : 'hover:mb-bg-gray-800'"
          :style="{ height: trackHeight + 'px' }"
          @click="$emit('select-layer', track.layer.id)"
          :title="trackLabel(track.layer)"
        >
          <span class="mb-text-[10px] mb-truncate" :class="track.layer.id === selectedLayerId ? 'mb-text-white mb-font-semibold' : 'mb-text-gray-400'">
            <span class="mb-mr-1 mb-opacity-60">{{ typeIcon(track.layer.type) }}</span>{{ trackLabel(track.layer) }}
          </span>
        </div>
      </div>

      <!-- Tracks area (scrollable) -->
      <div ref="tracksScrollEl" class="mb-flex-1 mb-flex mb-flex-col mb-overflow-x-auto mb-overflow-y-hidden mb-relative" @wheel="onTimelineWheel">
        <div :style="{ width: timelineZoom * 100 + '%', minWidth: '100%' }">
        <!-- Time ruler -->
        <div class="mb-border-b mb-border-gray-700 mb-relative" style="height:18px;">
          <svg class="mb-w-full mb-h-full" preserveAspectRatio="none">
            <line
              v-for="tick in rulerTicks"
              :key="tick.ms"
              :x1="tick.pct + '%'"
              :x2="tick.pct + '%'"
              y1="0"
              :y2="tick.major ? 14 : 7"
              stroke="#4b5563"
              stroke-width="1"
            />
            <text
              v-for="tick in rulerTicks.filter(t => t.major)"
              :key="'t' + tick.ms"
              :x="tick.pct + '%'"
              y="16"
              text-anchor="middle"
              fill="#6b7280"
              font-size="8"
              font-family="monospace"
            >{{ (tick.ms / 1000).toFixed(1) }}s</text>
          </svg>
        </div>

        <!-- Track rows -->
        <div
          ref="tracksEl"
          class="mb-relative mb-cursor-pointer"
          :style="{ height: timelineTracks.length * trackHeight + 'px' }"
          @pointerdown="onTracksPointerDown"
          @dblclick="onTracksDblClick"
        >
          <!-- Track backgrounds + lines -->
          <div
            v-for="(track, ti) in timelineTracks"
            :key="'bg-' + track.layer.id"
            class="mb-absolute mb-left-0 mb-right-0 mb-border-b mb-border-gray-800"
            :class="track.layer.id === selectedLayerId ? 'mb-bg-gray-700/30' : ''"
            :style="{ top: ti * trackHeight + 'px', height: trackHeight + 'px' }"
          >
            <!-- Track line -->
            <div
              class="mb-absolute mb-left-0 mb-right-0 mb-rounded"
              :style="{ top: (trackHeight / 2 - 1) + 'px', height: '3px', background: track.color, opacity: 0.4, width: trackWidthPct(track) + '%' }"
            ></div>

            <!-- Delay indicator -->
            <div
              v-if="(track.timeline.delay || 0) > 0"
              class="mb-absolute mb-rounded-sm"
              :style="{
                left: '0',
                width: delayPct(track) + '%',
                top: (trackHeight / 2 - 3) + 'px',
                height: '6px',
                background: 'repeating-linear-gradient(45deg, transparent, transparent 2px, rgba(255,255,255,0.08) 2px, rgba(255,255,255,0.08) 4px)',
              }"
              :title="'Delay: ' + (track.timeline.delay || 0) + 'ms'"
            ></div>

            <!-- WAIT indicator (endWithSlide) -->
            <div
              v-if="track.timeline.endWithSlide !== false && effectiveSlideDuration > 0"
              class="mb-absolute mb-rounded-sm"
              :style="{
                left: trackWidthPct(track) + '%',
                width: Math.max(0, 100 - trackWidthPct(track)) + '%',
                top: (trackHeight / 2 - 2) + 'px',
                height: '4px',
                background: track.color,
                opacity: 0.15,
              }"
              title="WAIT — layer resta visibile fino a fine slide"
            ></div>

            <!-- Tloop range indicator -->
            <div
              v-if="track.timeline.tloop"
              class="mb-absolute mb-rounded-sm mb-border mb-border-dashed"
              :style="tloopStyle(track)"
              :title="'Loop: ' + (track.timeline.tloop.repeat === -1 ? '∞' : track.timeline.tloop.repeat + 'x')"
            ></div>

            <!-- Keyframe markers -->
            <div
              v-for="kf in sortedKfs(track)"
              :key="kf.id"
              class="mps-kf-marker"
              :class="{
                'mps-kf-selected': kf.id === selectedKeyframeId && track.layer.id === selectedLayerId,
                'mps-kf-other': track.layer.id !== selectedLayerId,
              }"
              :style="{ left: kfPctInTrack(kf, track) + '%', top: (trackHeight / 2 - 6) + 'px', color: track.color }"
              @pointerdown.stop="startDragKf($event, kf, track)"
              @click.stop="onKfClick(track.layer.id, kf.id)"
              @contextmenu.prevent="onKfContextMenu($event, kf, track)"
              :title="formatTime(kf.time) + 's'"
            >
              <svg viewBox="0 0 12 12" class="mb-w-3 mb-h-3"><polygon points="6,0 12,6 6,12 0,6"/></svg>
            </div>
          </div>

          <!-- Playhead line (vertical across all tracks) -->
          <div
            class="mps-playhead"
            :style="{ left: playheadPct + '%' }"
          >
            <div class="mps-playhead-line"></div>
          </div>
        </div>

        <!-- Playhead handle at top of tracks area -->
        <div
          class="mb-absolute mb-pointer-events-none"
          :style="{ left: 'calc(' + playheadPct + '% - 4px)', top: '10px', zIndex: 30 }"
        >
          <div style="width:8px;height:8px;background:#ef4444;border-radius:50%;pointer-events:none;"></div>
        </div>
      </div>
      </div><!-- /zoom wrapper -->
    </div>

    <!-- Context menu -->
    <div
      v-if="ctxMenu"
      class="mb-fixed mb-bg-gray-800 mb-border mb-border-gray-600 mb-rounded mb-shadow-lg mb-py-1 mb-z-50"
      :style="{ left: ctxMenu.x + 'px', top: ctxMenu.y + 'px' }"
    >
      <button
        @click="duplicateKeyframe(ctxMenu.kf, ctxMenu.track); ctxMenu = null"
        class="mb-block mb-w-full mb-text-left mb-px-3 mb-py-1 mb-text-xs mb-text-gray-300 hover:mb-bg-gray-700"
      >Duplica keyframe</button>
      <button
        v-if="canDeleteKf(ctxMenu.track)"
        @click="$emit('select-layer', ctxMenu.track.layer.id); $emit('remove-keyframe', ctxMenu.kf.id); ctxMenu = null"
        class="mb-block mb-w-full mb-text-left mb-px-3 mb-py-1 mb-text-xs mb-text-red-400 hover:mb-bg-gray-700"
      >Elimina keyframe</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { formatTime, interpolateAtTime, defaultKeyframe } from './timelineUtils.js';

const props = defineProps({
  layers: { type: Array, default: () => [] },
  selectedLayerId: { type: String, default: null },
  selectedKeyframeId: { type: String, default: null },
  playhead: { type: Number, default: 0 },
  isPlaying: { type: Boolean, default: false },
  slideDuration: { type: Number, default: 0 },
  autoplaySpeed: { type: Number, default: 5000 },
});

const emit = defineEmits([
  'seek', 'toggle-play', 'select-layer', 'select-keyframe',
  'add-keyframe', 'remove-keyframe', 'update-keyframe',
  'update-timeline-prop', 'update-slide-duration', 'set-all-durations',
]);

const tracksEl = ref(null);
const tracksScrollEl = ref(null);
const ctxMenu = ref(null);
const trackHeight = 28;
const extraHeight = ref(0);
const timelineZoom = ref(1);

// --- Resize handle ---
let resizeState = null;

function startResize(e) {
  resizeState = { startY: e.clientY, startExtra: extraHeight.value };
  window.addEventListener('pointermove', onResizeMove);
  window.addEventListener('pointerup', onResizeUp);
  e.preventDefault();
}

function onResizeMove(e) {
  if (!resizeState) return;
  const delta = resizeState.startY - e.clientY;
  extraHeight.value = Math.max(0, Math.min(400, resizeState.startExtra + delta));
}

function onResizeUp() {
  resizeState = null;
  window.removeEventListener('pointermove', onResizeMove);
  window.removeEventListener('pointerup', onResizeUp);
}

const actualHeight = computed(() => panelHeight.value + extraHeight.value);

// --- Resize laterale (colonna nomi) ---
const namesColWidth = ref(90);
let resizeSideState = null;

function startResizeSide(e) {
  resizeSideState = { startX: e.clientX, startWidth: namesColWidth.value };
  window.addEventListener('pointermove', onResizeSideMove);
  window.addEventListener('pointerup', onResizeSideUp);
  e.preventDefault();
}

function onResizeSideMove(e) {
  if (!resizeSideState) return;
  const delta = e.clientX - resizeSideState.startX;
  namesColWidth.value = Math.max(60, Math.min(250, resizeSideState.startWidth + delta));
}

function onResizeSideUp() {
  resizeSideState = null;
  window.removeEventListener('pointermove', onResizeSideMove);
  window.removeEventListener('pointerup', onResizeSideUp);
}

// --- Track colors per type ---
const typeColors = {
  text: '#60a5fa',
  button: '#f59e0b',
  image: '#34d399',
  icon: '#c084fc',
  shape: '#fb7185',
  video: '#38bdf8',
  audio: '#a78bfa',
};

const typeIcons = {
  text: 'T',
  button: '▢',
  image: '🖼',
  icon: '★',
  shape: '◆',
  video: '▶',
  audio: '♪',
};

function typeIcon(type) {
  return typeIcons[type] || '•';
}

function trackLabel(layer) {
  if (layer.type === 'text' || layer.type === 'button') {
    const txt = (layer.content || '').replace(/<[^>]+>/g, '').trim();
    return txt.length > 12 ? txt.slice(0, 12) + '…' : (txt || layer.type);
  }
  return layer.type.charAt(0).toUpperCase() + layer.type.slice(1);
}

// --- Computed: tracks with timeline ---
const timelineTracks = computed(() => {
  return props.layers
    .filter(l => l.timeline?.keyframes?.length >= 2)
    .map(l => ({
      layer: l,
      timeline: l.timeline,
      color: typeColors[l.type] || '#9ca3af',
    }));
});

const activeTimeline = computed(() => {
  if (!props.selectedLayerId) return null;
  const track = timelineTracks.value.find(t => t.layer.id === props.selectedLayerId);
  return track?.timeline || null;
});

// Max duration across all tracks (for shared ruler)
const maxDuration = computed(() => {
  let max = 3000;
  for (const t of timelineTracks.value) {
    const d = (t.timeline.duration || 3000) + (t.timeline.delay || 0);
    if (d > max) max = d;
  }
  if (effectiveSlideDuration.value > max) max = effectiveSlideDuration.value;
  return max;
});

const panelHeight = computed(() => {
  const tracks = timelineTracks.value.length;
  if (tracks === 0) return 60;
  return 32 + 18 + tracks * trackHeight + 4; // header + ruler + tracks + padding
});

// --- Ruler ---
const rulerTicks = computed(() => {
  const dur = maxDuration.value;
  const ticks = [];
  const step = dur <= 5000 ? 500 : 1000;
  for (let ms = 0; ms <= dur; ms += step) {
    ticks.push({ ms, pct: (ms / dur) * 100, major: ms % 1000 === 0 });
  }
  return ticks;
});

// --- Keyframe navigation (for selected layer) ---
const selectedTrackKfs = computed(() => {
  if (!activeTimeline.value) return [];
  return [...(activeTimeline.value.keyframes || [])].sort((a, b) => a.time - b.time);
});

const prevKfTime = computed(() => {
  const times = selectedTrackKfs.value.map(kf => kf.time).filter(t => t < props.playhead);
  return times.length ? times[times.length - 1] : null;
});

const nextKfTime = computed(() => {
  const times = selectedTrackKfs.value.map(kf => kf.time).filter(t => t > props.playhead);
  return times.length ? times[0] : null;
});

function seekPrevKf() { if (prevKfTime.value != null) emit('seek', prevKfTime.value); }
function seekNextKf() { if (nextKfTime.value != null) emit('seek', nextKfTime.value); }

// --- Playhead ---
const playheadPct = computed(() => {
  const dur = maxDuration.value;
  return Math.min(100, (props.playhead / dur) * 100);
});

// --- Helpers ---
function sortedKfs(track) {
  return [...(track.timeline.keyframes || [])].sort((a, b) => a.time - b.time);
}

function kfPctInTrack(kf, track) {
  const delay = track.timeline.delay || 0;
  const dur = maxDuration.value;
  return ((delay + kf.time) / dur) * 100;
}

function trackWidthPct(track) {
  const delay = track.timeline.delay || 0;
  const dur = maxDuration.value;
  return ((delay + (track.timeline.duration || 3000)) / dur) * 100;
}

function delayPct(track) {
  const delay = track.timeline.delay || 0;
  return (delay / maxDuration.value) * 100;
}

function clampDuration(v) {
  return Math.max(500, Math.min(30000, v || 3000));
}

function tloopStyle(track) {
  const tl = track.timeline.tloop;
  if (!tl) return { display: 'none' };
  const kfs = sortedKfs(track);
  const fromKf = kfs.find(kf => kf.id === tl.from) || kfs[0];
  const toKf = kfs.find(kf => kf.id === tl.to) || kfs[kfs.length - 1];
  const fromPct = kfPctInTrack(fromKf, track);
  const toPct = kfPctInTrack(toKf, track);
  return {
    left: fromPct + '%',
    width: Math.max(0, toPct - fromPct) + '%',
    top: (trackHeight / 2 - 5) + 'px',
    height: '10px',
    borderColor: track.color,
    opacity: 0.5,
  };
}

const effectiveSlideDuration = computed(() => {
  return props.slideDuration > 0 ? props.slideDuration : props.autoplaySpeed;
});

function onSlideDurationChange(seconds) {
  const ms = Math.round(Math.max(0.5, Math.min(120, seconds)) * 1000);
  emit('update-slide-duration', ms);
}

function canDeleteKf(track) {
  return (track.timeline.keyframes || []).length > 2;
}

// --- Track pointer (seek + playhead drag) ---
function onTracksPointerDown(e) {
  seekToPointer(e);
  window.addEventListener('pointermove', onTracksPointerMove);
  window.addEventListener('pointerup', onTracksPointerUp);
}

function onTracksPointerMove(e) { seekToPointer(e); }

function onTracksPointerUp() {
  window.removeEventListener('pointermove', onTracksPointerMove);
  window.removeEventListener('pointerup', onTracksPointerUp);
}

function seekToPointer(e) {
  if (!tracksEl.value) return;
  const rect = tracksEl.value.getBoundingClientRect();
  const pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
  const dur = maxDuration.value;
  emit('seek', Math.round(pct * dur));
}

// --- Dblclick: add keyframe ---
function onTracksDblClick() {
  addKeyframeAtPlayhead();
}

function addKeyframeAtPlayhead() {
  if (!activeTimeline.value) return;
  const time = props.playhead;
  const interpolated = interpolateAtTime(activeTimeline.value, time);
  const kf = defaultKeyframe(time, interpolated || {});
  emit('add-keyframe', kf);
}

// --- Click keyframe ---
function onKfClick(layerId, kfId) {
  if (layerId !== props.selectedLayerId) {
    emit('select-layer', layerId);
  }
  emit('select-keyframe', kfId);
}

// --- Keyframe dragging ---
let dragKfState = null;

function startDragKf(e, kf, track) {
  // Select this layer and keyframe
  if (track.layer.id !== props.selectedLayerId) {
    emit('select-layer', track.layer.id);
  }
  emit('select-keyframe', kf.id);

  if (!tracksEl.value) return;
  const rect = tracksEl.value.getBoundingClientRect();
  dragKfState = {
    kfId: kf.id,
    trackLeft: rect.left,
    trackWidth: rect.width,
    delay: track.timeline.delay || 0,
    layerId: track.layer.id,
  };
  window.addEventListener('pointermove', onDragKfMove);
  window.addEventListener('pointerup', onDragKfUp);
}

function onDragKfMove(e) {
  if (!dragKfState) return;
  const pct = Math.max(0, Math.min(1, (e.clientX - dragKfState.trackLeft) / dragKfState.trackWidth));
  const dur = maxDuration.value;
  const absTime = Math.round(pct * dur);
  const newTime = Math.max(0, absTime - dragKfState.delay);
  // Auto-extend layer duration if dragging keyframe beyond it
  const track = timelineTracks.value.find(t => t.layer.id === dragKfState.layerId);
  if (track && newTime > (track.timeline.duration || 3000)) {
    emit('update-timeline-prop', 'duration', newTime);
  }
  emit('update-keyframe', dragKfState.kfId, { time: newTime });
  emit('seek', absTime);
}

function onDragKfUp() {
  dragKfState = null;
  window.removeEventListener('pointermove', onDragKfMove);
  window.removeEventListener('pointerup', onDragKfUp);
}

// --- Context menu ---
function onKfContextMenu(e, kf, track) {
  ctxMenu.value = { x: e.clientX, y: e.clientY, kf, track };
}

function duplicateKeyframe(kf, track) {
  if (track.layer.id !== props.selectedLayerId) {
    emit('select-layer', track.layer.id);
  }
  const newKf = defaultKeyframe(
    Math.min(kf.time + 200, track.timeline.duration),
    { ...kf.props }
  );
  newKf.easing = kf.easing;
  emit('add-keyframe', newKf);
}

// --- Timeline zoom wheel ---
function onTimelineWheel(e) {
  if (e.ctrlKey || e.metaKey) {
    e.preventDefault();
    const delta = e.deltaY > 0 ? -0.5 : 0.5;
    const oldZoom = timelineZoom.value;
    timelineZoom.value = Math.round(Math.max(1, Math.min(10, oldZoom + delta)) * 10) / 10;
    // Keep scroll position centered on cursor
    if (tracksScrollEl.value && timelineZoom.value !== oldZoom) {
      const el = tracksScrollEl.value;
      const rect = el.getBoundingClientRect();
      const cursorRatio = (e.clientX - rect.left + el.scrollLeft) / (el.scrollWidth);
      requestAnimationFrame(() => {
        const newScrollWidth = el.scrollWidth;
        el.scrollLeft = cursorRatio * newScrollWidth - (e.clientX - rect.left);
      });
    }
  }
}

// Close context menu on click outside
function onDocClick() { ctxMenu.value = null; }
onMounted(() => document.addEventListener('click', onDocClick));
onUnmounted(() => {
  document.removeEventListener('click', onDocClick);
  window.removeEventListener('pointermove', onTracksPointerMove);
  window.removeEventListener('pointerup', onTracksPointerUp);
  window.removeEventListener('pointermove', onDragKfMove);
  window.removeEventListener('pointerup', onDragKfUp);
  window.removeEventListener('pointermove', onResizeMove);
  window.removeEventListener('pointerup', onResizeUp);
  window.removeEventListener('pointermove', onResizeSideMove);
  window.removeEventListener('pointerup', onResizeSideUp);
});
</script>

<style scoped>
.mps-timeline { display: flex; flex-direction: column; min-height: 50px; position: relative; }
.mps-kf-marker {
  position: absolute;
  transform: translateX(-50%);
  cursor: pointer;
  z-index: 10;
  transition: color 0.15s, transform 0.15s;
}
.mps-kf-marker:hover { transform: translateX(-50%) scale(1.3); }
.mps-kf-selected { filter: brightness(1.5); transform: translateX(-50%) scale(1.3) !important; }
.mps-kf-other { opacity: 0.7; }
.mps-playhead {
  position: absolute;
  top: 0;
  bottom: 0;
  transform: translateX(-50%);
  z-index: 20;
  pointer-events: none;
}
.mps-playhead-line {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 50%;
  width: 2px;
  margin-left: -1px;
  background: #ef4444;
}
.mps-resize-handle {
  position: absolute;
  top: -4px;
  left: 0;
  right: 0;
  height: 8px;
  cursor: ns-resize;
  z-index: 40;
  background: transparent;
}
.mps-resize-handle::after {
  content: '';
  position: absolute;
  left: 50%;
  top: 2px;
  transform: translateX(-50%);
  width: 32px;
  height: 4px;
  border-radius: 2px;
  background: #4b5563;
  opacity: 0;
  transition: opacity 0.15s;
}
.mps-resize-handle:hover::after {
  opacity: 1;
}
.mps-resize-handle-side {
  position: absolute;
  top: 0;
  bottom: 0;
  right: -4px;
  width: 8px;
  cursor: ew-resize;
  z-index: 40;
  background: transparent;
}
.mps-resize-handle-side::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 2px;
  transform: translateY(-50%);
  width: 4px;
  height: 32px;
  border-radius: 2px;
  background: #4b5563;
  opacity: 0;
  transition: opacity 0.15s;
}
.mps-resize-handle-side:hover::after {
  opacity: 1;
}
</style>
