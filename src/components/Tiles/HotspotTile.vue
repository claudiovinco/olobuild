<template>
  <div class="olo-hotspot" :style="containerStyle">
    <!-- Image -->
    <img
      v-if="s.image"
      :src="s.image"
      alt=""
      :style="imageStyle"
    />
    <div
      v-else
      style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--olo-color-surface-alt, #F3F4F6);color:var(--olo-color-text-faint, #9CA3AF);font-size:13px;"
    >
      {{ t('Seleziona un\'immagine') }}
    </div>
    <!-- Markers -->
    <div
      v-for="(marker, i) in markers"
      :key="marker.id || i"
      :style="markerStyle(marker)"
      class="olo-hotspot-marker"
      :class="{ 'olo-hotspot-pulse': s.pulse_animation }"
    >
      <svg v-if="!marker.icon || marker.icon === 'pin'" :width="markerSize" :height="markerSize" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/>
      </svg>
      <span v-else style="font-size:14px;">{{ marker.icon }}</span>
      <!-- Tooltip preview -->
      <div class="olo-hotspot-tooltip-preview" :style="tooltipPreviewStyle(marker)">
        <span style="font-weight:600;font-size:11px;" :data-olo-editable="`markers.${i}.title`">{{ marker.title || 'Marker' }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  image: '',
  image_height: '400',
  object_position: 'center center',
  markers: [],
  marker_color: '',
  marker_size: '24',
  pulse_animation: true,
  tooltip_bg: '',
  tooltip_color: '',
  tooltip_width: '220',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const markers = computed(() => Array.isArray(s.value.markers) ? s.value.markers : []);
const markerSize = computed(() => Math.max(16, Math.min(40, parseInt(s.value.marker_size) || 24)));

const imageStyle = computed(() => ({
  width: '100%',
  height: '100%',
  objectFit: 'cover',
  objectPosition: s.value.object_position || 'center center',
  display: 'block',
}));

const containerStyle = computed(() => ({
  position: 'relative',
  width: '100%',
  height: (parseInt(s.value.image_height) || 400) + 'px',
  overflow: 'hidden',
  borderRadius: '8px',
}));

function markerStyle(marker) {
  return {
    position: 'absolute',
    left: (parseFloat(marker.pos_x) || 50) + '%',
    top: (parseFloat(marker.pos_y) || 50) + '%',
    transform: 'translate(-50%, -50%)',
    color: resolveColor(s.value.marker_color, TOKENS.primary),
    cursor: 'pointer',
    zIndex: '10',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    filter: 'drop-shadow(0 2px 4px rgba(0,0,0,0.4))',
  };
}

function tooltipPreviewStyle(marker) {
  const pos = marker.tooltip_position || 'top';
  const base = {
    position: 'absolute',
    background: resolveColor(s.value.tooltip_bg, TOKENS.surface),
    color: resolveColor(s.value.tooltip_color, TOKENS.text),
    padding: '3px 8px',
    borderRadius: '4px',
    whiteSpace: 'nowrap',
    pointerEvents: 'none',
    fontSize: '10px',
    opacity: '0.85',
  };
  if (pos === 'top') {
    base.bottom = '100%';
    base.left = '50%';
    base.transform = 'translateX(-50%)';
    base.marginBottom = '6px';
  } else if (pos === 'bottom') {
    base.top = '100%';
    base.left = '50%';
    base.transform = 'translateX(-50%)';
    base.marginTop = '6px';
  } else if (pos === 'left') {
    base.right = '100%';
    base.top = '50%';
    base.transform = 'translateY(-50%)';
    base.marginRight = '6px';
  } else {
    base.left = '100%';
    base.top = '50%';
    base.transform = 'translateY(-50%)';
    base.marginLeft = '6px';
  }
  return base;
}
</script>

<style scoped>
.olo-hotspot-pulse {
  animation: olo-hotspot-pulse-anim 2s ease-in-out infinite;
}
@keyframes olo-hotspot-pulse-anim {
  0%, 100% { transform: translate(-50%, -50%) scale(1); }
  50% { transform: translate(-50%, -50%) scale(1.2); }
}
.olo-hotspot-tooltip-preview {
  display: none;
}
.olo-hotspot-marker:hover .olo-hotspot-tooltip-preview {
  display: block;
}
</style>
