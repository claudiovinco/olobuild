<template>
  <div class="mb-relative" :style="wrapStyle">
    <!-- SVG shape -->
    <svg
      preserveAspectRatio="none"
      viewBox="0 0 1200 120"
      xmlns="http://www.w3.org/2000/svg"
      :style="svgStyle"
    >
      <path :d="shapePath" :fill="s.color || '#ffffff'" />
    </svg>

    <!-- Label (only in cell mode, not overlay) -->
    <div
      v-if="!overlayMode"
      class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center mb-pointer-events-none"
      style="z-index:2"
    >
      <span class="mb-text-xs mb-text-gray-500 mb-bg-gray-900/80 mb-px-2 mb-py-0.5 mb-rounded">
        {{ s.shape.toUpperCase() }} &middot; {{ s.position === 'top' ? 'ALTO' : 'BASSO' }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  overlayMode: { type: Boolean, default: false },
});

const defaults = {
  shape: 'wave',
  position: 'bottom',
  flip_horizontal: false,
  flip_vertical: false,
  width: '100',
  height: '80',
  color: '#ffffff',
  z_index: '1',
  responsive_height_tablet: '',
  responsive_height_mobile: '',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const shapePaths = {
  wave:      'M0,0 C300,100 900,0 1200,80 L1200,120 L0,120 Z',
  wave2:     'M0,0 C150,80 350,0 500,50 C650,100 850,20 1000,60 C1100,80 1150,40 1200,50 L1200,120 L0,120 Z',
  triangle:  'M600,0 L1200,120 L0,120 Z',
  tilt:      'M0,0 L1200,120 L0,120 Z',
  arrow:     'M0,120 L600,0 L1200,120 Z',
  zigzag:    'M0,60 L100,0 L200,60 L300,0 L400,60 L500,0 L600,60 L700,0 L800,60 L900,0 L1000,60 L1100,0 L1200,60 L1200,120 L0,120 Z',
  mountains: 'M0,60 L300,10 L500,80 L800,20 L1000,70 L1200,30 L1200,120 L0,120 Z',
  clouds:    'M0,80 C100,80 100,30 200,30 C300,30 300,70 400,70 C500,70 500,20 600,20 C700,20 700,60 800,60 C900,60 900,10 1000,10 C1100,10 1100,80 1200,80 L1200,120 L0,120 Z',
  drops:     'M0,80 Q150,0 300,80 Q450,0 600,80 Q750,0 900,80 Q1050,0 1200,80 L1200,120 L0,120 Z',
  curve:     'M0,100 Q600,0 1200,100 L1200,120 L0,120 Z',
};

const shapePath = computed(() => shapePaths[s.value.shape] || shapePaths.wave);

const svgStyle = computed(() => {
  const h = parseInt(s.value.height) || 80;
  const w = parseInt(s.value.width) || 100;
  const transforms = [];
  if (s.value.flip_horizontal) transforms.push('scaleX(-1)');
  if (s.value.flip_vertical) transforms.push('scaleY(-1)');

  return {
    width: w + '%',
    height: h + 'px',
    display: 'block',
    position: 'relative',
    left: w > 100 ? -(w - 100) / 2 + '%' : undefined,
    transform: transforms.length ? transforms.join(' ') : undefined,
  };
});

const wrapStyle = computed(() => {
  const h = parseInt(s.value.height) || 80;
  const z = parseInt(s.value.z_index) || 1;
  return {
    height: h + 'px',
    overflow: 'hidden',
    zIndex: z,
    lineHeight: 0,
  };
});
</script>
