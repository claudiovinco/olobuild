<template>
  <div
    class="olo-ic-preview"
    :style="containerStyle"
    @mousedown="startDrag"
    @touchstart.prevent="startDrag"
  >
    <!-- After image (bottom layer) -->
    <div class="olo-ic-layer">
      <img v-if="s.after_image" :src="s.after_image" :alt="t('Dopo')" :style="imgStyle" />
      <div v-else class="olo-ic-placeholder" style="background:var(--olo-color-muted, #F3F4F6)">{{ t('Dopo') }}</div>
    </div>

    <!-- Before image (top layer, clipped) -->
    <div class="olo-ic-layer" :style="beforeClipStyle">
      <img v-if="s.before_image" :src="s.before_image" :alt="t('Prima')" :style="imgStyle" />
      <div v-else class="olo-ic-placeholder" style="background:var(--olo-color-background, #FFFFFF)">{{ t('Prima') }}</div>
    </div>

    <!-- Divider line -->
    <div class="olo-ic-line" :style="lineStyle"></div>

    <!-- Handle -->
    <div class="olo-ic-handle" :style="handleStyle">
      <svg v-if="isVert" viewBox="0 0 24 24" :style="svgStyle">
        <polyline points="6 9 12 3 18 9"/>
        <polyline points="6 15 12 21 18 15"/>
      </svg>
      <svg v-else viewBox="0 0 24 24" :style="svgStyle">
        <polyline points="9 6 3 12 9 18"/>
        <polyline points="15 6 21 12 15 18"/>
      </svg>
    </div>

    <!-- Labels -->
    <span v-if="s.show_labels && s.before_label" class="olo-ic-label" :style="labelBeforeStyle" data-olo-editable="before_label">{{ s.before_label }}</span>
    <span v-if="s.show_labels && s.after_label" class="olo-ic-label" :style="labelAfterStyle" data-olo-editable="after_label">{{ s.after_label }}</span>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { SHADOW } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  before_image: '',
  after_image: '',
  before_label: 'Prima',
  after_label: 'Dopo',
  show_labels: true,
  start_position: '50',
  orientation: 'horizontal',
  handle_color: '#FFFFFF',
  handle_size: '40',
  handle_border: '3',
  line_width: '3',
  height: '400',
  border_radius: '8',
  object_fit: 'cover',
  card_border_width: '0',
  card_border_color: 'var(--olo-color-border, #E5E7EB)',
  card_shadow: 'none',
  autoplay: false,
  autoplay_delay: '3',
  autoplay_speed: '2',
  ...props.settings,
}));

const position = ref(50);
const dragging = ref(false);
const containerRef = ref(null);

// Sync position with settings
const isVert = computed(() => s.value.orientation === 'vertical');

// Reset position when start_position changes
const startPos = computed(() => parseInt(s.value.start_position) || 50);
onMounted(() => { position.value = startPos.value; });

const shadowMap = SHADOW;

const containerStyle = computed(() => {
  const bw = parseInt(s.value.card_border_width) || 0;
  const st = {
    position: 'relative',
    overflow: 'hidden',
    height: (parseInt(s.value.height) || 400) + 'px',
    borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
    cursor: isVert.value ? 'row-resize' : 'col-resize',
    userSelect: 'none',
  };
  if (bw > 0) st.border = `${bw}px solid ${s.value.card_border_color || 'var(--olo-color-border, #E5E7EB)'}`;
  const sh = shadowMap[s.value.card_shadow];
  if (sh && sh !== 'none') st.boxShadow = sh;
  return st;
});

const imgStyle = computed(() => ({
  display: 'block',
  width: '100%',
  height: '100%',
  objectFit: s.value.object_fit || 'cover',
  pointerEvents: 'none',
}));

const beforeClipStyle = computed(() => {
  const p = position.value;
  return {
    clipPath: isVert.value
      ? `inset(0 0 ${100 - p}% 0)`
      : `inset(0 ${100 - p}% 0 0)`,
  };
});

const lineStyle = computed(() => {
  const c = s.value.handle_color || '#FFFFFF';
  const w = (parseInt(s.value.line_width) || 3) + 'px';
  const p = position.value + '%';
  if (isVert.value) {
    return { position: 'absolute', left: 0, right: 0, top: p, height: w, background: c, transform: 'translateY(-50%)', zIndex: 2, pointerEvents: 'none' };
  }
  return { position: 'absolute', top: 0, bottom: 0, left: p, width: w, background: c, transform: 'translateX(-50%)', zIndex: 2, pointerEvents: 'none' };
});

const handleStyle = computed(() => {
  const sz = (parseInt(s.value.handle_size) || 40) + 'px';
  const bw = (parseInt(s.value.handle_border) || 3) + 'px';
  const c = s.value.handle_color || '#FFFFFF';
  const base = {
    position: 'absolute', zIndex: 3, width: sz, height: sz,
    borderRadius: '50%', border: `${bw} solid ${c}`,
    background: 'rgba(0,0,0,0.3)', backdropFilter: 'blur(4px)',
    display: 'flex', alignItems: 'center', justifyContent: 'center',
    pointerEvents: 'none',
  };
  if (isVert.value) {
    base.left = '50%'; base.top = position.value + '%';
    base.transform = 'translate(-50%, -50%)';
  } else {
    base.top = '50%'; base.left = position.value + '%';
    base.transform = 'translate(-50%, -50%)';
  }
  return base;
});

const svgStyle = computed(() => {
  const sz = Math.round((parseInt(s.value.handle_size) || 40) * 0.45) + 'px';
  return { width: sz, height: sz, fill: 'none', stroke: s.value.handle_color || '#FFFFFF', strokeWidth: '2.5', strokeLinecap: 'round', strokeLinejoin: 'round' };
});

const labelBase = { position: 'absolute', zIndex: 1, padding: '4px 12px', borderRadius: '4px', background: 'rgba(0,0,0,0.55)', color: '#fff', fontSize: '12px', fontWeight: '600', letterSpacing: '0.5px', textTransform: 'uppercase', pointerEvents: 'none' };

const labelBeforeStyle = computed(() => {
  if (isVert.value) return { ...labelBase, top: '12px', left: '12px' };
  return { ...labelBase, bottom: '12px', left: '12px' };
});
const labelAfterStyle = computed(() => {
  if (isVert.value) return { ...labelBase, bottom: '12px', right: '12px' };
  return { ...labelBase, bottom: '12px', right: '12px' };
});

function startDrag(e) {
  dragging.value = true;
  moveDrag(e);
}
function moveDrag(e) {
  if (!dragging.value) return;
  const el = e.currentTarget || e.target.closest('.olo-ic-preview');
  if (!el) return;
  const rect = el.getBoundingClientRect();
  let clientX, clientY;
  if (e.touches) {
    clientX = e.touches[0].clientX;
    clientY = e.touches[0].clientY;
  } else {
    clientX = e.clientX;
    clientY = e.clientY;
  }
  let pct;
  if (isVert.value) {
    pct = ((clientY - rect.top) / rect.height) * 100;
  } else {
    pct = ((clientX - rect.left) / rect.width) * 100;
  }
  position.value = Math.max(0, Math.min(100, pct));
}
function endDrag() { dragging.value = false; }

onMounted(() => {
  document.addEventListener('mousemove', onDocMove);
  document.addEventListener('mouseup', endDrag);
  document.addEventListener('touchmove', onDocMove, { passive: false });
  document.addEventListener('touchend', endDrag);
});
onBeforeUnmount(() => {
  document.removeEventListener('mousemove', onDocMove);
  document.removeEventListener('mouseup', endDrag);
  document.removeEventListener('touchmove', onDocMove);
  document.removeEventListener('touchend', endDrag);
});

function onDocMove(e) {
  if (!dragging.value) return;
  // Find the container from stored ref or DOM
  const el = document.querySelector('.olo-ic-preview');
  if (!el) return;
  const rect = el.getBoundingClientRect();
  let clientX, clientY;
  if (e.touches) {
    clientX = e.touches[0].clientX;
    clientY = e.touches[0].clientY;
  } else {
    clientX = e.clientX;
    clientY = e.clientY;
  }
  let pct;
  if (isVert.value) {
    pct = ((clientY - rect.top) / rect.height) * 100;
  } else {
    pct = ((clientX - rect.left) / rect.width) * 100;
  }
  position.value = Math.max(0, Math.min(100, pct));
}
</script>

<style scoped>
.olo-ic-layer {
  position: absolute;
  inset: 0;
}
.olo-ic-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--olo-color-text-faint, #94a3b8);
  font-size: 14px;
}
</style>
