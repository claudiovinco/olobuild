<template>
  <div :style="wrapStyle">
    <!-- Top shape -->
    <div
      v-if="topShape"
      class="mb-absolute mb-left-0 mb-w-full mb-pointer-events-none"
      :style="{ bottom: '100%', zIndex: 15, overflow: 'hidden' }"
    >
      <svg preserveAspectRatio="none" viewBox="0 0 1200 120" :style="svgStyleFor('top')" xmlns="http://www.w3.org/2000/svg">
        <path v-if="s.shape_top_layer2" :d="topShape" :fill="s.shape_top_layer2_color || '#000'" :opacity="(parseInt(s.shape_top_layer2_opacity) || 30) / 100" transform="translate(-30, 8) scale(1.05, 1)" />
        <path :d="topShape" :fill="topFill" :opacity="topOpacity" />
      </svg>
      <div v-if="s.shape_top_fill !== 'color'" class="mb-absolute mb-top-0 mb-right-1 mb-bg-black/60 mb-text-white mb-rounded mb-px-1 mb-text-xs" style="font-size:9px">
        {{ s.shape_top_fill === 'video' ? '▶' : '🖼' }}
      </div>
    </div>

    <!-- Center -->
    <div class="mb-flex mb-items-center mb-justify-center mb-h-full mb-relative" style="z-index: 20;">
      <hr v-if="s.show_divider" class="mb-border-0 mb-m-0" :style="dividerStyle" />
      <div v-if="s.custom_svg" class="mb-text-xs mb-text-blue-400 mb-bg-gray-800 mb-px-2 mb-py-0.5 mb-rounded">{{ t('&lt;svg&gt;') }}</div>
      <span v-if="showLabel" class="mb-text-xs mb-text-gray-600 mb-bg-gray-900 mb-px-2 mb-relative mb-z-10">{{ labelText }}</span>
    </div>

    <!-- Bottom shape -->
    <div
      v-if="bottomShape"
      class="mb-absolute mb-left-0 mb-w-full mb-pointer-events-none"
      :style="{ top: '100%', zIndex: 15, overflow: 'hidden' }"
    >
      <svg preserveAspectRatio="none" viewBox="0 0 1200 120" :style="svgStyleFor('bottom')" xmlns="http://www.w3.org/2000/svg">
        <path v-if="s.shape_bottom_layer2" :d="bottomShape" :fill="s.shape_bottom_layer2_color || '#000'" :opacity="(parseInt(s.shape_bottom_layer2_opacity) || 30) / 100" transform="translate(-30, 8) scale(1.05, 1)" />
        <path :d="bottomShape" :fill="bottomFill" :opacity="bottomOpacity" />
      </svg>
      <div v-if="s.shape_bottom_fill !== 'color'" class="mb-absolute mb-top-0 mb-right-1 mb-bg-black/60 mb-text-white mb-rounded mb-px-1 mb-text-xs" style="font-size:9px">
        {{ s.shape_bottom_fill === 'video' ? '▶' : '🖼' }}
      </div>
    </div>

    <!-- Dashed border -->
    <div class="mb-absolute mb-inset-0 mb-border mb-border-dashed mb-border-gray-700 mb-rounded mb-opacity-30 mb-pointer-events-none"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const shapes = {
  'wave':       'M0,60 C150,110 350,0 600,60 C850,120 1050,0 1200,60 L1200,120 L0,120 Z',
  'wave-rough': 'M0,50 C60,100 120,20 180,70 C240,120 300,30 360,80 C420,130 480,10 540,60 C600,110 660,20 720,70 C780,120 840,25 900,75 C960,125 1020,15 1080,65 C1140,115 1180,40 1200,55 L1200,120 L0,120 Z',
  'tilt':       'M0,0 L1200,120 L0,120 Z',
  'triangle':   'M0,120 L600,0 L1200,120 Z',
  'curve':      'M0,120 Q600,-20 1200,120 Z',
  'mountains':  'M0,120 L100,80 L200,100 L350,30 L500,90 L650,15 L800,70 L950,25 L1100,85 L1200,40 L1200,120 Z',
  'drops':      'M0,80 Q75,0 150,80 Q225,0 300,80 Q375,0 450,80 Q525,0 600,80 Q675,0 750,80 Q825,0 900,80 Q975,0 1050,80 Q1125,0 1200,80 L1200,120 L0,120 Z',
  'zigzag':     'M0,80 L50,30 L100,80 L150,30 L200,80 L250,30 L300,80 L350,30 L400,80 L450,30 L500,80 L550,30 L600,80 L650,30 L700,80 L750,30 L800,80 L850,30 L900,80 L950,30 L1000,80 L1050,30 L1100,80 L1150,30 L1200,80 L1200,120 L0,120 Z',
  'clouds':     'M0,100 C50,100 50,40 100,40 C150,40 150,80 200,80 C250,80 250,20 300,20 C350,20 350,70 400,70 C450,70 450,30 500,30 C550,30 550,60 600,60 C650,60 650,15 700,15 C750,15 750,75 800,75 C850,75 850,35 900,35 C950,35 950,85 1000,85 C1050,85 1050,45 1100,45 C1150,45 1150,90 1200,90 L1200,120 L0,120 Z',
  'brush':      'M0,55 C30,35 60,70 100,50 C140,25 180,80 230,52 C280,24 320,78 370,48 C420,18 460,82 510,54 C560,26 600,76 650,46 C700,16 740,84 790,56 C840,28 880,74 930,44 C980,14 1020,82 1070,58 C1120,32 1160,72 1200,50 L1200,120 L0,120 Z',
};

const s = computed(() => ({
  height: '60',
  shape_top: 'none', shape_top_height: '80', shape_top_color: '#ffffff', shape_top_opacity: '100',
  shape_top_fill: 'color', shape_top_fill_image: '', shape_top_fill_video: '',
  shape_top_flip: false, shape_top_invert: false, shape_top_scale_x: '100',
  shape_top_layer2: false, shape_top_layer2_color: '#000000', shape_top_layer2_opacity: '30',
  shape_bottom: 'none', shape_bottom_height: '80', shape_bottom_color: '#ffffff', shape_bottom_opacity: '100',
  shape_bottom_fill: 'color', shape_bottom_fill_image: '', shape_bottom_fill_video: '',
  shape_bottom_flip: false, shape_bottom_invert: false, shape_bottom_scale_x: '100',
  shape_bottom_layer2: false, shape_bottom_layer2_color: '#000000', shape_bottom_layer2_opacity: '30',
  bg_color: '', bg_gradient: false, bg_gradient_from: 'var(--olo-color-primary, #e1474f)', bg_gradient_to: 'var(--olo-color-accent, #f4a23b)', bg_gradient_angle: '180',
  full_bleed: false, overlap_top: '0', overlap_bottom: '0',
  custom_svg: '', show_divider: false, divider_style: 'solid', divider_color: 'var(--olo-color-text, #374151)', divider_width: '100', divider_thickness: '1',
  ...props.settings,
}));

const topShape = computed(() => shapes[s.value.shape_top] || '');
const bottomShape = computed(() => shapes[s.value.shape_bottom] || '');
const hasAnyShape = computed(() => !!topShape.value || !!bottomShape.value);

const topFill = computed(() => s.value.shape_top_fill === 'color' ? (s.value.shape_top_color || '#fff') : '#888');
const topOpacity = computed(() => s.value.shape_top_fill === 'color' ? (parseInt(s.value.shape_top_opacity) || 100) / 100 : 0.5);
const bottomFill = computed(() => s.value.shape_bottom_fill === 'color' ? (s.value.shape_bottom_color || '#fff') : '#888');
const bottomOpacity = computed(() => s.value.shape_bottom_fill === 'color' ? (parseInt(s.value.shape_bottom_opacity) || 100) / 100 : 0.5);

const rawHeight = computed(() => parseInt(s.value.height) || 0);
const effectiveHeight = computed(() => {
  if (rawHeight.value <= 0 && hasAnyShape.value) return 4;
  if (rawHeight.value <= 0) return 0;
  return rawHeight.value;
});

const showLabel = computed(() => {
  if (s.value.show_divider || s.value.custom_svg) return false;
  if (hasAnyShape.value && rawHeight.value <= 8) return false;
  return !hasAnyShape.value || rawHeight.value > 20;
});
const labelText = computed(() => `SPACER ${rawHeight.value}px`);

const wrapStyle = computed(() => {
  const mt = parseInt(s.value.overlap_top) || 0;
  const mb = parseInt(s.value.overlap_bottom) || 0;
  const style = { height: effectiveHeight.value + 'px', overflow: 'visible', position: 'relative' };
  if (hasAnyShape.value) style.zIndex = 5;
  if (mt > 0) style.marginTop = -mt + 'px';
  if (mb > 0) style.marginBottom = -mb + 'px';
  if (s.value.bg_gradient) {
    const angle = parseInt(s.value.bg_gradient_angle) || 180;
    style.background = `linear-gradient(${angle}deg, ${s.value.bg_gradient_from}, ${s.value.bg_gradient_to})`;
  } else if (s.value.bg_color) {
    style.backgroundColor = s.value.bg_color;
  }
  return style;
});

function svgStyleFor(pos) {
  const p = 'shape_' + pos;
  const h = parseInt(s.value[p + '_height']) || 80;
  const parts = [];
  if (pos === 'bottom') parts.push('scaleY(-1)');
  if (s.value[p + '_flip']) parts.push('scaleX(-1)');
  if (s.value[p + '_invert']) parts.push(pos === 'bottom' ? 'scaleY(1)' : 'scaleY(-1)');
  const sx = parseInt(s.value[p + '_scale_x']) || 100;
  if (sx !== 100) parts.push(`scaleX(${sx / 100})`);
  return { width: '100%', height: h + 'px', display: 'block', transform: parts.length ? parts.join(' ') : undefined };
}

const dividerStyle = computed(() => ({
  width: (parseInt(s.value.divider_width) || 100) + '%',
  borderTop: `${parseInt(s.value.divider_thickness) || 1}px ${s.value.divider_style || 'solid'} ${s.value.divider_color || 'var(--olo-color-text, #374151)'}`,
}));
</script>
