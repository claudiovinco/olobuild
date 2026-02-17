<template>
  <div class="mb-space-y-1">
    <div class="mb-flex mb-gap-2">
      <input
        type="color"
        :value="hexPart"
        @input="onHexChange($event.target.value)"
        class="mb-w-8 mb-h-8 mb-rounded mb-cursor-pointer mb-border-0"
      />
      <input
        type="text"
        :value="displayValue"
        @change="onTextChange($event.target.value)"
        class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
      />
    </div>
    <div class="mb-flex mb-items-center mb-gap-2">
      <span class="mb-text-[10px] mb-text-gray-400 mb-shrink-0">Alfa</span>
      <input
        type="range"
        :value="alphaPct"
        @input="onAlphaChange(parseInt($event.target.value))"
        min="0" max="100" step="5"
        class="mb-flex-1"
      />
      <span class="mb-text-[10px] mb-text-gray-400 mb-w-8 mb-text-right">{{ alphaPct }}%</span>
      <div
        class="mb-w-5 mb-h-5 mb-rounded mb-border mb-border-gray-300 mb-shrink-0"
        :style="{ background: previewColor }"
      ></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: '#000000' },
});
const emit = defineEmits(['update:modelValue']);

/**
 * Parse incoming color: supports #hex and rgba(r,g,b,a)
 */
function parseColor(val) {
  if (!val) return { hex: '#000000', alpha: 1 };

  // rgba(r, g, b, a)
  const rgbaMatch = val.match(/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)(?:\s*,\s*([\d.]+))?\s*\)$/i);
  if (rgbaMatch) {
    const r = parseInt(rgbaMatch[1]);
    const g = parseInt(rgbaMatch[2]);
    const b = parseInt(rgbaMatch[3]);
    const a = rgbaMatch[4] !== undefined ? parseFloat(rgbaMatch[4]) : 1;
    const hex = '#' + [r, g, b].map(c => c.toString(16).padStart(2, '0')).join('');
    return { hex, alpha: a };
  }

  // #hex (3, 6, or 8 chars)
  let h = val.replace('#', '');
  if (h.length === 3) h = h[0]+h[0]+h[1]+h[1]+h[2]+h[2];
  if (h.length === 8) {
    const a = parseInt(h.substring(6, 8), 16) / 255;
    return { hex: '#' + h.substring(0, 6), alpha: Math.round(a * 100) / 100 };
  }
  if (h.length === 6) return { hex: '#' + h, alpha: 1 };

  return { hex: '#000000', alpha: 1 };
}

function toOutput(hex, alpha) {
  if (alpha >= 1) return hex;
  const h = hex.replace('#', '');
  const r = parseInt(h.substring(0, 2), 16) || 0;
  const g = parseInt(h.substring(2, 4), 16) || 0;
  const b = parseInt(h.substring(4, 6), 16) || 0;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

const parsed = computed(() => parseColor(props.modelValue));
const hexPart = computed(() => parsed.value.hex);
const alphaPct = computed(() => Math.round(parsed.value.alpha * 100));
const displayValue = computed(() => props.modelValue || '#000000');
const previewColor = computed(() => toOutput(parsed.value.hex, parsed.value.alpha));

function onHexChange(hex) {
  emit('update:modelValue', toOutput(hex, parsed.value.alpha));
}

function onAlphaChange(pct) {
  emit('update:modelValue', toOutput(parsed.value.hex, pct / 100));
}

function onTextChange(val) {
  // Accept both hex and rgba typed manually
  emit('update:modelValue', val);
}
</script>
