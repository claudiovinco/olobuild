<template>
  <div class="mb-space-y-1">
    <!-- Global Colors swatches (espansi al bisogno via icona globe) -->
    <div v-if="showGlobals" class="fc-global">
      <div class="fc-global-swatches">
        <span
          v-for="gc in globalColors"
          :key="gc.id"
          class="fc-swatch-wrap"
        >
          <button
            type="button"
            class="fc-swatch"
            :class="{ 'fc-swatch--active': isGlobalSelected(gc.id) }"
            :style="{ background: gc.value }"
            :title="gc.label + ' — var(--olo-color-' + gc.id + ')'"
            @click="selectGlobalColor(gc.id)"
          ></button>
          <button
            v-if="gc.quick"
            type="button"
            class="fc-swatch-del"
            :title="t('Rimuovi colore')"
            @click.stop="removeQuickColor(gc.id)"
          >{{ t('&times;') }}</button>
        </span>
        <button
          type="button"
          class="fc-swatch fc-swatch--add"
          :title="t('Aggiungi colore corrente ai globali')"
          @click="addCurrentAsGlobal"
        >+</button>
      </div>
    </div>

    <div class="fc-hex-wrap">
      <input
        type="color"
        :value="hexPart"
        @input="onHexChange($event.target.value)"
        @change="onHexChange($event.target.value)"
        class="fc-swatch-inline"
      />
      <input
        type="text"
        :value="displayValue"
        @change="onTextChange($event.target.value)"
        class="fc-hex-input"
      />
      <button
        type="button"
        class="fc-globe-btn"
        :class="{ 'fc-globe-btn--active': showGlobals || isGlobalActive }"
        :title="t('Colori globali')"
        :aria-pressed="showGlobals"
        @click="showGlobals = !showGlobals"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="10"/>
          <path d="M2 12h20"/>
          <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>
      </button>
    </div>
    <div class="fc-alpha-row">
      <span class="fc-alpha-label">{{ t('Alfa') }}</span>
      <input
        type="range"
        :value="alphaPct"
        @input="onAlphaChange(parseInt($event.target.value))"
        min="0" max="100" step="5"
        class="fc-alpha-range"
        :style="{ '--fc-rgb': previewRgb }"
        :aria-label="t('Opacità colore')"
        :aria-valuetext="alphaPct + '%'"
      />
      <span class="fc-alpha-val">{{ alphaPct }}%</span>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed, ref } from 'vue';
import { useStylesStore } from '@/stores/styles';

const props = defineProps({
  modelValue: { type: String, default: '#000000' },
});
const emit = defineEmits(['update:modelValue']);

const stylesStore = useStylesStore();
const globalColors = computed(() => stylesStore.globalColors || []);

const showGlobals = ref(false);

const isGlobalActive = computed(() => {
  const cur = (props.modelValue || '').toLowerCase();
  if (!cur) return false;
  if (cur.startsWith('var(--olo-color-')) return true;
  return (stylesStore.globalColors || []).some(gc => gc.value?.toLowerCase() === cur);
});

/**
 * Check if a global color is currently selected.
 */
function isGlobalSelected(colorId) {
  const gc = (stylesStore.globalColors || []).find(c => c.id === colorId);
  if (!gc) return false;
  const gcHex = gc.value?.toLowerCase();
  const cur = props.modelValue?.toLowerCase();
  return cur === `var(--olo-color-${colorId})` || cur === gcHex;
}

/**
 * Select a global color — stores the resolved hex value directly.
 */
function selectGlobalColor(colorId) {
  const gc = (stylesStore.globalColors || []).find(c => c.id === colorId);
  if (gc && gc.value) {
    emit('update:modelValue', gc.value);
  } else {
    emit('update:modelValue', `var(--olo-color-${colorId})`);
  }
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

// Terna "r, g, b" del colore corrente (senza alfa) per il gradiente della barra Alfa.
const previewRgb = computed(() => {
  const h = parsed.value.hex.replace('#', '');
  const rp = parseInt(h.substring(0, 2), 16); const r = !isNaN(rp) ? rp : 0;
  const gp = parseInt(h.substring(2, 4), 16); const g = !isNaN(gp) ? gp : 0;
  const bp = parseInt(h.substring(4, 6), 16); const b = !isNaN(bp) ? bp : 0;
  return `${r}, ${g}, ${b}`;
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

/**
 * Add the current color as a new global color and persist immediately.
 */
async function addCurrentAsGlobal() {
  const hex = parsed.value.hex;
  // Check if this hex already exists in globals
  const existing = (stylesStore.globalColors || []).find(
    gc => gc.value.toLowerCase() === hex.toLowerCase()
  );
  if (existing) {
    // Already exists — just select it
    selectGlobalColor(existing.id);
    return;
  }
  const id = 'c' + Date.now().toString(36);
  const label = hex.toUpperCase();
  const newColors = [...(stylesStore.globalColors || []), { id, label, value: hex, quick: true }];
  stylesStore.setGlobalColors(newColors);
  await stylesStore.saveGlobalColors();
  // Auto-select the newly added color
  selectGlobalColor(id);
}

/**
 * Remove a quick-added global color and persist.
 */
async function removeQuickColor(colorId) {
  const newColors = (stylesStore.globalColors || []).filter(gc => gc.id !== colorId);
  stylesStore.setGlobalColors(newColors);
  await stylesStore.saveGlobalColors();
  // If this color was selected, revert to its resolved hex
  if (isGlobalSelected(colorId)) {
    emit('update:modelValue', parsed.value.hex);
  }
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
  gap: 3px;
  align-items: flex-start;
}

.fc-swatch-wrap {
  position: relative;
  display: inline-flex;
}

.fc-swatch-del {
  display: none;
  position: absolute;
  top: -5px;
  right: -5px;
  width: 11px;
  height: 11px;
  border-radius: 50%;
  background: #ef4444;
  color: #fff;
  font-size: 8px;
  line-height: 1;
  border: none;
  cursor: pointer;
  padding: 0;
  align-items: center;
  justify-content: center;
  z-index: 1;
}
.fc-swatch-wrap:hover .fc-swatch-del {
  display: flex;
}

.fc-swatch {
  width: 14px;
  height: 14px;
  border-radius: 3px;
  border: 1.5px solid transparent;
  cursor: pointer;
  padding: 0;
  transition: border-color 0.15s, transform 0.1s;
  box-shadow: 0 0 0 0.5px rgba(0, 0, 0, 0.12);
}

.fc-swatch:hover {
  transform: scale(1.15);
  border-color: rgba(255, 255, 255, 0.5);
}

.fc-swatch--active {
  border-color: var(--olo-color-primary, #6366f1);
  box-shadow: 0 0 0 1px var(--olo-color-primary, #6366f1);
}

.fc-swatch--add {
  background: #f3f4f6;
  color: #9ca3af;
  font-size: 11px;
  font-weight: 700;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: none;
  border: 1px dashed #d1d5db;
}
.fc-swatch--add:hover {
  background: #e5e7eb;
  color: #374151;
  border-color: #9ca3af;
  transform: scale(1.15);
}

/* ── Hex wrap (swatch inline + text input) ── */
.fc-hex-wrap {
  display: flex;
  align-items: center;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  overflow: hidden;
  background: #fff;
}
.fc-swatch-inline {
  width: 32px;
  height: 32px;
  border: none;
  cursor: pointer;
  padding: 0;
  background: none;
  flex-shrink: 0;
}
.fc-swatch-inline::-webkit-color-swatch-wrapper {
  padding: 2px;
}
.fc-swatch-inline::-webkit-color-swatch {
  border: none;
  border-radius: 3px;
}
.fc-hex-input {
  flex: 1;
  min-width: 0;
  border: none;
  background: transparent;
  padding: 4px 8px 4px 0;
  font-size: 13px;
  color: #374151;
  outline: none;
  font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;
}

.fc-globe-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 32px;
  margin-right: 2px;
  padding: 0;
  border: none;
  background: transparent;
  color: #9ca3af;
  cursor: pointer;
  transition: color 0.15s, background-color 0.15s;
  flex-shrink: 0;
  border-radius: 4px;
}
.fc-globe-btn:hover {
  color: #374151;
  background: #f3f4f6;
}
.fc-globe-btn--active {
  color: var(--olo-color-primary, #6366f1);
}
.fc-globe-btn--active:hover {
  color: var(--olo-color-primary, #6366f1);
  background: rgba(99, 102, 241, 0.08);
}

/* ── Barra Alfa (checkerboard + gradiente trasparenza→colore) ── */
.fc-alpha-row {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 2px;
}
.fc-alpha-label {
  font-size: 10px;
  color: #9ca3af;
  flex-shrink: 0;
}
.fc-alpha-val {
  font-size: 10px;
  color: #9ca3af;
  width: 30px;
  text-align: right;
  flex-shrink: 0;
  font-variant-numeric: tabular-nums;
}
.fc-alpha-range {
  -webkit-appearance: none;
  appearance: none;
  flex: 1;
  min-width: 0;
  height: 14px;
  border-radius: 7px;
  outline: none;
  cursor: pointer;
  border: 1px solid #d1d5db;
  /* alto → basso: gradiente alfa, scacchiera (2 layer), base bianca */
  background:
    linear-gradient(90deg, rgba(var(--fc-rgb, 0, 0, 0), 0), rgba(var(--fc-rgb, 0, 0, 0), 1)),
    linear-gradient(45deg, #cbd5e1 25%, transparent 0, transparent 75%, #cbd5e1 0) 0 0 / 10px 10px,
    linear-gradient(45deg, #cbd5e1 25%, transparent 0, transparent 75%, #cbd5e1 0) 5px 5px / 10px 10px,
    #fff;
}
.fc-alpha-range::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #fff;
  border: 1px solid rgba(0, 0, 0, 0.35);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
  cursor: pointer;
}
.fc-alpha-range::-moz-range-thumb {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #fff;
  border: 1px solid rgba(0, 0, 0, 0.35);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
  cursor: pointer;
}
.fc-alpha-range::-moz-range-track {
  height: 14px;
  border-radius: 7px;
  background: transparent;
}
.fc-alpha-range:focus-visible {
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5);
}
</style>
