<template>
  <div class="mb-flex mb-flex-col mb-items-center" :style="wrapStyle">
    <div v-if="hasSvg" ref="svgWrap" class="mb-w-full" v-html="sanitizedSvg"></div>
    <div v-else class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-py-10 mb-px-6 mb-text-center mb-bg-gray-100 mb-rounded-lg mb-w-full" style="min-height:120px">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
        <path d="M2 17l10 5 10-5"/>
        <path d="M2 12l10 5 10-5"/>
      </svg>
      <span class="mb-text-xs mb-text-gray-400 mb-mt-2">Inserisci un file SVG</span>
    </div>
    <div v-if="s.replay_button && hasSvg" class="mb-mt-2">
      <span class="mb-text-xs mb-px-3 mb-py-1 mb-bg-blue-500 mb-text-white mb-rounded mb-cursor-pointer">{{ s.replay_button_label || 'Replay' }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted, nextTick } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  source_type: 'upload', svg_url: '', svg_code: '',
  anim_type: 'draw', anim_sequence: 'delayed', trigger: 'viewport',
  duration: 1500, delay: 0, easing: 'ease', stagger_delay: 100,
  stroke_width: '', stroke_color: '', stroke_linecap: '', stroke_linejoin: '',
  show_fill: true, fill_color: '', fill_delay: 300, fill_duration: 500,
  reverse: false, loop: false, replay_button: false, replay_button_label: 'Replay',
  max_width: '', alignment: 'center', shadow: 'none',
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const svgWrap = ref(null);

const hasSvg = computed(() => {
  if (s.value.source_type === 'code') return !!s.value.svg_code;
  return !!s.value.svg_url;
});

const sanitizedSvg = computed(() => {
  let raw = '';
  if (s.value.source_type === 'code') {
    raw = s.value.svg_code || '';
  } else {
    // For upload, we can't fetch the SVG content in the Vue preview easily
    // Show an <img> tag instead — the PHP render will inline it
    if (s.value.svg_url) {
      return '<img src="' + s.value.svg_url + '" style="width:100%;height:auto;display:block" />';
    }
    return '';
  }
  // Basic sanitization: strip script tags and on* attributes
  raw = raw.replace(/<script[\s\S]*?<\/script>/gi, '');
  raw = raw.replace(/\s+on\w+\s*=\s*"[^"]*"/gi, '');
  raw = raw.replace(/\s+on\w+\s*=\s*'[^']*'/gi, '');
  return raw;
});

const wrapStyle = computed(() => {
  const st = {};
  if (s.value.max_width) st.maxWidth = s.value.max_width + (String(s.value.max_width).includes('%') ? '' : 'px');
  st.textAlign = s.value.alignment || 'center';
  if (s.value.alignment === 'center') st.margin = '0 auto';
  else if (s.value.alignment === 'right') st.marginLeft = 'auto';
  return st;
});

// Animate SVG paths in the builder preview (draw effect)
function animatePaths() {
  if (!svgWrap.value) return;
  const svg = svgWrap.value.querySelector('svg');
  if (!svg) return;

  const drawables = svg.querySelectorAll('path, line, polyline, polygon, circle, ellipse, rect');
  if (!drawables.length) return;

  drawables.forEach((el, i) => {
    let len = 0;
    try { len = el.getTotalLength(); } catch (e) {
      // Fallback for elements without getTotalLength
      if (el.tagName === 'circle') { len = 2 * Math.PI * (parseFloat(el.getAttribute('r')) || 0); }
      else if (el.tagName === 'rect') { len = 2 * ((parseFloat(el.getAttribute('width')) || 0) + (parseFloat(el.getAttribute('height')) || 0)); }
      else if (el.tagName === 'ellipse') {
        const rx = parseFloat(el.getAttribute('rx')) || 0;
        const ry = parseFloat(el.getAttribute('ry')) || 0;
        len = Math.PI * (3 * (rx + ry) - Math.sqrt((3 * rx + ry) * (rx + 3 * ry)));
      }
    }
    if (len <= 0) return;

    // Apply stroke overrides
    if (s.value.stroke_width) el.style.strokeWidth = s.value.stroke_width + 'px';
    if (s.value.stroke_color) el.style.stroke = s.value.stroke_color;
    if (s.value.stroke_linecap) el.style.strokeLinecap = s.value.stroke_linecap;

    // Ensure stroke is visible
    if (!el.getAttribute('stroke') && !el.style.stroke) {
      el.style.stroke = s.value.stroke_color || '#333';
    }

    // Hide fill initially if animating
    const origFill = el.getAttribute('fill') || '';
    if (!s.value.show_fill || s.value.anim_type === 'draw') {
      el.style.fillOpacity = '0';
    }

    // Set up stroke dash
    el.style.strokeDasharray = len;
    el.style.strokeDashoffset = s.value.reverse ? -len : len;

    // Animate
    const dur = s.value.duration || 1500;
    const stagger = s.value.anim_sequence === 'sync' ? 0 : (s.value.stagger_delay || 100);
    const delay = (s.value.delay || 0) + i * stagger;

    el.style.transition = 'stroke-dashoffset ' + dur + 'ms ' + (s.value.easing || 'ease') + ' ' + delay + 'ms';

    requestAnimationFrame(() => {
      el.style.strokeDashoffset = '0';

      // Show fill after draw
      if (s.value.show_fill && origFill !== 'none') {
        const fillDelay = delay + dur + (s.value.fill_delay || 300);
        setTimeout(() => {
          el.style.transition = 'fill-opacity ' + (s.value.fill_duration || 500) + 'ms ease';
          el.style.fillOpacity = '1';
        }, fillDelay);
      }
    });
  });
}

onMounted(() => { nextTick(() => setTimeout(animatePaths, 100)); });
watch(() => [s.value.svg_code, s.value.svg_url, s.value.anim_type, s.value.duration, s.value.stroke_color], () => {
  nextTick(() => setTimeout(animatePaths, 100));
});
</script>
