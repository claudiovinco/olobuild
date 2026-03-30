<template>
  <div class="olo-divider-tile" :style="wrapStyle">
    <!-- With center text/emoji -->
    <div v-if="hasCenter" class="olo-divider-center" :style="{ width: s.width + '%' }">
      <span class="olo-divider-line olo-divider-line--left" :style="lineStyle"></span>
      <span class="olo-divider-label" :style="labelStyle" data-olo-editable="text">
        <template v-if="s.icon_emoji">{{ s.icon_emoji }} </template>{{ s.text }}
      </span>
      <span class="olo-divider-line olo-divider-line--right" :style="lineStyle"></span>
    </div>

    <!-- Decorative SVG styles -->
    <div v-else-if="isDecorative" class="olo-divider-deco" :style="{ width: s.width + '%' }">
      <!-- Wave -->
      <svg v-if="s.style === 'wave'" viewBox="0 0 1200 24" preserveAspectRatio="none" :style="svgStyle">
        <path :d="wavePath" fill="none" :stroke="lineColor" :stroke-width="svgThick" stroke-linecap="round"/>
      </svg>
      <!-- Zigzag -->
      <svg v-else-if="s.style === 'zigzag'" viewBox="0 0 1200 24" preserveAspectRatio="none" :style="svgStyle">
        <path :d="zigzagPath" fill="none" :stroke="lineColor" :stroke-width="svgThick" stroke-linejoin="round" stroke-linecap="round"/>
      </svg>
      <!-- Dots -->
      <svg v-else-if="s.style === 'dots'" viewBox="0 0 1200 12" preserveAspectRatio="none" :style="svgStyle">
        <pattern id="dot-pattern" x="0" y="0" width="24" height="12" patternUnits="userSpaceOnUse">
          <circle :cx="6" :cy="6" :r="Math.min(parseInt(s.thickness) || 1, 5)" :fill="lineColor"/>
        </pattern>
        <rect width="1200" height="12" fill="url(#dot-pattern)"/>
      </svg>
      <!-- Diamonds -->
      <svg v-else-if="s.style === 'diamonds'" viewBox="0 0 1200 16" preserveAspectRatio="none" :style="svgStyle">
        <pattern id="diamond-pattern" x="0" y="0" width="28" height="16" patternUnits="userSpaceOnUse">
          <polygon points="14,1 27,8 14,15 1,8" fill="none" :stroke="lineColor" :stroke-width="Math.max(parseInt(s.thickness) || 1, 1)" />
        </pattern>
        <rect width="1200" height="16" fill="url(#diamond-pattern)"/>
      </svg>
    </div>

    <!-- Shadow line -->
    <div v-else-if="s.style === 'shadow'" class="olo-divider-shadow" :style="{ width: s.width + '%' }">
      <div :style="shadowLineStyle"></div>
    </div>

    <!-- Fade line (gradient from transparent to color to transparent) -->
    <div v-else-if="s.style === 'fade'" :style="[fadeLineStyle, { width: s.width + '%' }]"></div>

    <!-- Simple line: solid, dashed, dotted, double, gradient -->
    <span v-else :style="[lineStyle, { width: s.width + '%' }]"></span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  style: 'solid',
  width: '100',
  thickness: '1',
  color: '#d1d5db',
  alignment: 'center',
  text: '',
  text_color: '#6b7280',
  text_size: '14',
  icon_emoji: '',
  spacing: '16',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const hasCenter = computed(() => !!(s.value.text || s.value.icon_emoji));
const isDecorative = computed(() => ['wave', 'zigzag', 'dots', 'diamonds'].includes(s.value.style));

const lineColor = computed(() => s.value.color || '#d1d5db');
const svgThick = computed(() => Math.max(parseInt(s.value.thickness) || 1, 1));

const wrapStyle = computed(() => ({
  display: 'flex',
  justifyContent: { left: 'flex-start', center: 'center', right: 'flex-end' }[s.value.alignment] || 'center',
  padding: `${parseInt(s.value.spacing) || 16}px 16px`,
}));

const lineStyle = computed(() => {
  const v = s.value;
  const thick = parseInt(v.thickness) || 1;
  if (v.style === 'gradient') {
    return {
      display: 'block',
      border: 'none',
      height: thick + 'px',
      flexGrow: hasCenter.value ? 1 : undefined,
      background: `linear-gradient(90deg, transparent, ${lineColor.value}, transparent)`,
    };
  }
  return {
    display: 'block',
    border: 'none',
    borderTop: `${thick}px ${v.style || 'solid'} ${lineColor.value}`,
    flexGrow: hasCenter.value ? 1 : undefined,
  };
});

const fadeLineStyle = computed(() => {
  const thick = parseInt(s.value.thickness) || 1;
  return {
    height: thick + 'px',
    background: `linear-gradient(90deg, transparent, ${lineColor.value} 20%, ${lineColor.value} 80%, transparent)`,
    borderRadius: thick > 1 ? (thick / 2) + 'px' : '0',
  };
});

const shadowLineStyle = computed(() => {
  const thick = parseInt(s.value.thickness) || 1;
  return {
    height: thick + 'px',
    background: lineColor.value,
    boxShadow: `0 2px ${thick * 3}px ${lineColor.value}40, 0 1px ${thick * 2}px ${lineColor.value}25`,
    borderRadius: thick > 1 ? (thick / 2) + 'px' : '0',
  };
});

const svgStyle = computed(() => ({
  width: '100%',
  height: (parseInt(s.value.thickness) * 3 + 12) + 'px',
  display: 'block',
}));

const labelStyle = computed(() => ({
  color: s.value.text_color || '#6b7280',
  fontSize: (parseInt(s.value.text_size) || 14) + 'px',
  whiteSpace: 'nowrap',
  lineHeight: '1.2',
  padding: '0 4px',
  userSelect: 'none',
}));

const wavePath = computed(() => {
  const amp = Math.max(parseInt(s.value.thickness) * 2, 4);
  const mid = 12;
  let d = `M 0 ${mid}`;
  for (let x = 0; x < 1200; x += 60) {
    d += ` C ${x + 15} ${mid - amp}, ${x + 30} ${mid - amp}, ${x + 30} ${mid}`;
    d += ` C ${x + 30} ${mid}, ${x + 45} ${mid + amp}, ${x + 60} ${mid}`;
  }
  return d;
});

const zigzagPath = computed(() => {
  const amp = Math.max(parseInt(s.value.thickness) * 2, 4);
  const mid = 12;
  let d = `M 0 ${mid}`;
  for (let x = 0; x < 1200; x += 24) {
    d += ` L ${x + 12} ${mid - amp} L ${x + 24} ${mid}`;
  }
  return d;
});
</script>

<style scoped>
.olo-divider-tile {
  width: 100%;
  min-height: 8px;
}

.olo-divider-center {
  display: flex;
  align-items: center;
  gap: 12px;
}

.olo-divider-line {
  flex: 1;
  min-width: 20px;
}

.olo-divider-label {
  flex-shrink: 0;
}

.olo-divider-deco {
  display: flex;
  align-items: center;
}

.olo-divider-shadow {
  display: flex;
  align-items: center;
}
.olo-divider-shadow > div {
  width: 100%;
}
</style>
