<template>
  <div class="olo-nf">
    <label class="olo-nf-lab">{{ t(field.label) }}</label>

    <!-- Range: slider arancio chrome + valbox con unità (coerente con StyleEffectsStack) -->
    <div v-if="field.type === 'range'" class="olo-nf-sliderrow">
      <input
        type="range"
        class="olo-nf-slider"
        :value="num"
        :min="field.min ?? 0"
        :max="field.max ?? 100"
        :step="field.step ?? 1"
        :aria-label="t(field.label)"
        @input="onUpdate(Number($event.target.value))"
      />
      <span class="olo-nf-val">
        <input
          type="text"
          inputmode="numeric"
          class="olo-nf-valinput"
          :value="num"
          :aria-label="t(field.label)"
          @change="onUpdateClamped($event.target.value)"
        />
        <i v-if="field.unit">{{ field.unit }}</i>
      </span>
    </div>

    <!-- Select -->
    <div v-else-if="field.type === 'select'" class="olo-nf-selwrap">
      <select
        class="olo-nf-sel"
        :value="value"
        :aria-label="t(field.label)"
        @change="onUpdate($event.target.value)"
      >
        <option v-for="opt in (field.options || [])" :key="opt.value" :value="opt.value">{{ t(opt.label) }}</option>
      </select>
      <svg class="olo-nf-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
    </div>

    <!-- Text fallback -->
    <input
      v-else
      type="text"
      class="olo-nf-text"
      :value="value"
      @input="onUpdate($event.target.value)"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

/**
 * StyleNestedField — gestisce field con chiave "path" annidata via "."
 *  es. field.key = 'transition.duration' → legge tile.style.transition.duration
 *
 * Emette update di tipo 'transition' (per ora) — il path completo è inferito dal segmento iniziale.
 * Se il primo segmento è 'transition', emette { type: 'transition', key: 'duration', value }.
 * Altri prefix (futuri): mappare in StyleFieldsRenderer.
 *
 * UI: il range usa lo slider arancio CHROME + valbox con unità, coerente con StyleEffectsStack
 * (pannello "Transizione hover"). Le chiavi salvate restano INVARIATE (transition.duration/easing).
 */
const props = defineProps({
  field: { type: Object, required: true },     // { key: 'transition.duration', type, label, ... }
  tileStyle: { type: Object, required: true },
});
const emit = defineEmits(['update']);

const segments = computed(() => (props.field.key || '').split('.'));
const root = computed(() => segments.value[0]);
const sub = computed(() => segments.value.slice(1).join('.'));

const value = computed(() => {
  let cur = props.tileStyle;
  for (const s of segments.value) {
    if (cur == null) return props.field.default ?? '';
    cur = cur[s];
  }
  return cur ?? props.field.default ?? '';
});

// Valore numerico per slider/valbox (range). Fallback al default del field.
const num = computed(() => {
  const n = parseInt(value.value, 10);
  return isNaN(n) ? (props.field.default ?? 0) : n;
});

function onUpdate(val) {
  if (root.value === 'transition') {
    emit('update', { type: 'transition', key: sub.value, value: val });
  } else {
    // Generic: emit nested path
    emit('update', { type: 'nested', path: props.field.key, value: val });
  }
}

// Scrittura dalla valbox: parse → clamp ai limiti dello slider → set (digitare = trascinare).
function onUpdateClamped(raw) {
  let n = parseInt(raw, 10);
  if (isNaN(n)) n = props.field.default ?? 0;
  const lo = props.field.min ?? 0;
  const hi = props.field.max ?? 100;
  onUpdate(Math.min(hi, Math.max(lo, n)));
}
</script>

<style scoped>
.olo-nf {
  --olo-ui-accent: #e8622a;
}
.olo-nf-lab {
  display: block;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #9ca3af;
  margin-bottom: 6px;
}

/* slider + valbox (stesso linguaggio di StyleEffectsStack) */
.olo-nf-sliderrow { display: flex; align-items: center; gap: 12px; }
.olo-nf-slider {
  flex: 1;
  min-width: 40px;
  -webkit-appearance: none;
  appearance: none;
  height: 5px;
  border-radius: 99px;
  background: #eef0f3;
  outline: none;
  cursor: pointer;
}
.olo-nf-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 16px; height: 16px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid var(--olo-ui-accent);
  cursor: pointer;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.18);
}
.olo-nf-slider::-moz-range-thumb {
  width: 16px; height: 16px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid var(--olo-ui-accent);
  cursor: pointer;
}
.olo-nf-slider:focus-visible { box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ui-accent) 25%, transparent); }
.olo-nf-val {
  flex: 0 0 auto;
  width: 70px;
  display: inline-flex;
  align-items: center;
  height: 32px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #fff;
  overflow: hidden;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.olo-nf-val:focus-within {
  border-color: var(--olo-ui-accent);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ui-accent) 18%, transparent);
}
.olo-nf-valinput {
  flex: 1;
  min-width: 0;
  width: 100%;
  border: none;
  outline: none;
  background: transparent;
  padding: 0 2px 0 8px;
  font: 500 13px 'SF Mono', Monaco, monospace;
  color: #1f2937;
  text-align: center;
}
.olo-nf-valinput::-webkit-outer-spin-button,
.olo-nf-valinput::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.olo-nf-val i { font-style: normal; font-size: 11px; color: #9ca3af; padding-right: 8px; flex-shrink: 0; }

/* select coerente */
.olo-nf-selwrap { position: relative; }
.olo-nf-sel {
  width: 100%;
  height: 38px;
  padding: 0 32px 0 11px;
  border: 1px solid #e5e7eb;
  border-radius: 9px;
  background: #fff;
  font-size: 14px;
  color: #1f2937;
  outline: none;
  appearance: none;
  -webkit-appearance: none;
  cursor: pointer;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.olo-nf-sel:focus { border-color: var(--olo-ui-accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ui-accent) 18%, transparent); }
.olo-nf-chev {
  position: absolute; right: 11px; top: 50%; transform: translateY(-50%);
  width: 14px; height: 14px; color: #9ca3af; pointer-events: none;
}

/* text fallback */
.olo-nf-text {
  width: 100%;
  height: 38px;
  padding: 0 11px;
  border: 1px solid #e5e7eb;
  border-radius: 9px;
  background: #fff;
  font-size: 14px;
  color: #1f2937;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.olo-nf-text:focus { border-color: var(--olo-ui-accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ui-accent) 18%, transparent); }
</style>
