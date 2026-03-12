<template>
  <div class="mb-space-y-1">
    <!-- Global Colors swatches -->
    <div v-if="globalColors.length" class="fc-global">
      <div class="fc-global-label">Colori globali</div>
      <div class="fc-global-swatches">
        <button
          v-for="gc in globalColors"
          :key="gc.id"
          type="button"
          class="fc-swatch"
          :class="{ 'fc-swatch--active': isGlobalSelected(gc.id) }"
          :style="{ background: gc.value }"
          :title="gc.label + ' — var(--olo-color-' + gc.id + ')'"
          @click="selectGlobalColor(gc.id)"
        ></button>
      </div>
    </div>

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
        aria-label="Opacità colore"
        :aria-valuetext="alphaPct + '%'"
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
import { useStylesStore } from '@/stores/styles';

const props = defineProps({
  modelValue: { type: String, default: '#000000' },
});
const emit = defineEmits(['update:modelValue']);

const stylesStore = useStylesStore();
const globalColors = computed(() => stylesStore.globalColors || []);

/**
 * Check if a global color is currently selected.
 */
function isGlobalSelected(colorId) {
  return props.modelValue === `var(--olo-color-${colorId})`;
}

/**
 * Select a global color — stores the CSS variable reference.
 */
function selectGlobalColor(colorId) {
  emit('update:modelValue', `var(--olo-color-${colorId})`);
}

/**
 * Parse incoming color: supports #hex, rgba(r,g,b,a), and var(--olo-color-*)
 */
function parseColor(val) {
  if (!val) return { hex: '#000000', alpha: 1 };

  // var(--olo-color-*) — resolve from global colors for preview
  if (val.startsWith('var(--olo-color-')) {
    const idMatch = val.match(/^var\(--olo-color-([^)]+)\)$/);
    if (idMatch) {
      const gc = (stylesStore.globalColors || []).find(c => c.id === idMatch[1]);
      if (gc) {
        return parseColor(gc.value);
      }
    }
    return { hex: '#000000', alpha: 1 };
  }

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
  const rp = parseInt(h.substring(0, 2), 16); const r = !isNaN(rp) ? rp : 0;
  const gp = parseInt(h.substring(2, 4), 16); const g = !isNaN(gp) ? gp : 0;
  const bp = parseInt(h.substring(4, 6), 16); const b = !isNaN(bp) ? bp : 0;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

const isGlobalVar = computed(() => {
  return props.modelValue && props.modelValue.startsWith('var(--olo-color-');
});

const parsed = computed(() => parseColor(props.modelValue));
const hexPart = computed(() => parsed.value.hex);
const alphaPct = computed(() => {
  if (isGlobalVar.value) return 100;
  return Math.round(parsed.value.alpha * 100);
});
const displayValue = computed(() => props.modelValue || '#000000');
const previewColor = computed(() => {
  if (isGlobalVar.value) {
    return toOutput(parsed.value.hex, 1);
  }
  return toOutput(parsed.value.hex, parsed.value.alpha);
});

function onHexChange(hex) {
  emit('update:modelValue', toOutput(hex, parsed.value.alpha));
}

function onAlphaChange(pct) {
  if (isGlobalVar.value) {
    // Switching away from global: use the resolved hex
    emit('update:modelValue', toOutput(parsed.value.hex, pct / 100));
    return;
  }
  emit('update:modelValue', toOutput(parsed.value.hex, pct / 100));
}

function onTextChange(val) {
  // Accept hex, rgba, and var(--olo-color-*) typed manually
  emit('update:modelValue', val);
}
</script>

<style scoped>
.fc-global {
  margin-bottom: 4px;
}

.fc-global-label {
  font-size: 9px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #9ca3af;
  margin-bottom: 4px;
}

.fc-global-swatches {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}

.fc-swatch {
  width: 22px;
  height: 22px;
  border-radius: 4px;
  border: 2px solid transparent;
  cursor: pointer;
  padding: 0;
  transition: border-color 0.15s, transform 0.1s;
  box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
}

.fc-swatch:hover {
  transform: scale(1.15);
  border-color: rgba(255, 255, 255, 0.5);
}

.fc-swatch--active {
  border-color: var(--olo-color-primary, #6366f1);
  box-shadow: 0 0 0 1px var(--olo-color-primary, #6366f1);
}
</style>
