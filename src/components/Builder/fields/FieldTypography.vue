<template>
  <!--
    FieldTypography — popover floating stile Elementor.
    Raggruppa tutti i controlli tipografici (family, size, weight, transform,
    style, decoration, line-height, letter-spacing, word-spacing, color, shadow)
    in un singolo pulsante "matita". Accanto, un'icona "globe" apre il selettore
    di preset tipografici globali (quando presetKey è fornita).

    Props:
      keys           { family?, size?, weight?, transform?, style?, decoration?,
                       lineHeight?, letterSpacing?, wordSpacing?, color?, colorHover?, shadow? }
      values         oggetto con i valori correnti (indicizzati per chiave reale)
      label          etichetta del blocco (default: "Tipografia")
      presetKey      chiave dello store globale per il preset (opzionale).
                     Se presente, mostra l'icona globe accanto alla matita.
      responsiveKeys array di chiavi logiche ('size', 'lineHeight', 'letterSpacing'...)
                     che supportano i breakpoint (default: ['size'])
      sizeMin/Max/Step

    Eventi:
      update         { key, value } — per ogni modifica (incluso responsive con suffisso _tablet/_mobile)
      reset          azzera tutte le chiavi tipografiche
  -->
  <div class="typo-wrap" ref="rootEl">
    <label class="typo-row-label">{{ t(label) }}</label>
    <div class="typo-actions">
      <button
        v-if="presetKey"
        type="button"
        class="typo-trigger typo-trigger--globe"
        :class="{ 'typo-trigger--has-value': hasPresetValue }"
        :aria-expanded="presetOpen"
        :title="t('Preset tipografico globale')"
        @click="presetOpen = !presetOpen"
      >
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
        </svg>
      </button>
      <button
        type="button"
        class="typo-trigger"
        :class="{ 'typo-trigger--has-value': hasAnyValue }"
        :aria-expanded="open"
        :title="t('Apri editor tipografia')"
        @click="open = !open"
      >
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
        </svg>
      </button>
    </div>

    <!-- ── Globe: dropdown preset tipografici globali ── -->
    <Teleport to="body">
      <div v-if="presetOpen" class="typo-backdrop" @click="presetOpen = false"></div>
      <div v-if="presetOpen" class="typo-pop typo-pop--narrow" ref="presetPopEl" :style="presetPopPos">
        <div class="typo-head">
          <span class="typo-head-title">{{ t('Preset globale') }}</span>
          <button type="button" class="typo-close" :title="t('Chiudi')" @click="presetOpen = false">×</button>
        </div>
        <div class="typo-body">
          <div class="typo-preset-list">
            <button
              type="button"
              class="typo-preset-item"
              :class="{ 'typo-preset-item--active': !values[presetKey] }"
              @click="selectPreset('')"
            >
              <span class="typo-preset-name">{{ t('Nessuno (eredita)') }}</span>
            </button>
            <button
              v-for="opt in globalPresets"
              :key="opt.value"
              type="button"
              class="typo-preset-item"
              :class="{ 'typo-preset-item--active': values[presetKey] === opt.value }"
              @click="selectPreset(opt.value)"
            >
              <span class="typo-preset-name">{{ opt.label }}</span>
            </button>
            <div v-if="!globalPresets.length" class="typo-empty">
              {{ t('Nessun preset globale definito. Vai a Stili globali → Tipografia.') }}
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ── Matita: popover controlli tipografici ── -->
    <Teleport to="body">
      <div v-if="open" class="typo-backdrop" @click="open = false"></div>
      <div v-if="open" class="typo-pop" ref="popEl" :style="popPos">
        <div class="typo-head">
          <span class="typo-head-title">{{ t(label) }}</span>
          <div class="typo-head-actions">
            <button
              v-if="hasAnyValue"
              type="button"
              class="typo-reset"
              :title="t('Reset tipografia')"
              @click="resetAll"
            >
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12a9 9 0 1 0 9-9 9.7 9.7 0 0 0-6.8 2.8L3 8"/><path d="M3 3v5h5"/>
              </svg>
            </button>
            <button type="button" class="typo-close" :title="t('Chiudi')" @click="open = false">×</button>
          </div>
        </div>

        <div class="typo-body">
          <!-- Tag HTML (semantica) -->
          <div v-if="keys.tag" class="typo-row">
            <label class="typo-label">{{ t('Tag HTML') }}</label>
            <FieldSelect ui="dropdown" :model-value="values[keys.tag] || ''" :options="TAG_OPTIONS" @update:model-value="emitKey(keys.tag, $event)" />
          </div>

          <!-- Famiglia -->
          <div v-if="keys.family" class="typo-row">
            <label class="typo-label">{{ t('Famiglia') }}</label>
            <FieldFontFamily
              :modelValue="values[keys.family] || ''"
              @update:modelValue="emitKey(keys.family, $event)"
            />
          </div>

          <!-- Dimensione (responsive) -->
          <div v-if="keys.size" class="typo-row">
            <div class="typo-row-head">
              <label class="typo-label">{{ t('Dimensione (px)') }}</label>
              <button
                v-if="isResponsive('size')"
                type="button"
                class="typo-bp-toggle"
                :class="{ active: bpFor.size !== 'desktop' }"
                @click="cycleBp('size')"
                :title="t('Cambia breakpoint')"
              >{{ bpShort(bpFor.size) }}</button>
            </div>
            <div class="typo-range-row">
              <input
                type="range" :min="sizeMin" :max="sizeMax" :step="sizeStep"
                :value="numberOr(readResp(keys.size, bpFor.size), 0)"
                @input="writeResp(keys.size, bpFor.size, Number($event.target.value))"
                class="typo-range"
              />
              <input
                type="number" :min="sizeMin" :max="sizeMax" :step="sizeStep"
                :value="numberOr(readResp(keys.size, bpFor.size), '')"
                @input="writeResp(keys.size, bpFor.size, Number($event.target.value))"
                class="typo-number"
              />
            </div>
          </div>

          <!-- Peso -->
          <div v-if="keys.weight" class="typo-row">
            <label class="typo-label">{{ t('Peso') }}</label>
            <FieldSelect ui="dropdown" :model-value="values[keys.weight] || ''" :options="WEIGHT_OPTIONS" @update:model-value="emitKey(keys.weight, $event)" />
          </div>

          <!-- Trasformazione -->
          <div v-if="keys.transform" class="typo-row">
            <label class="typo-label">{{ t('Trasformazione') }}</label>
            <FieldSelect ui="dropdown" :model-value="values[keys.transform] || ''" :options="TRANSFORM_OPTIONS" @update:model-value="emitKey(keys.transform, $event)" />
          </div>

          <!-- Stile -->
          <div v-if="keys.style" class="typo-row">
            <label class="typo-label">{{ t('Stile') }}</label>
            <FieldSelect ui="dropdown" :model-value="values[keys.style] || ''" :options="STYLE_OPTIONS" @update:model-value="emitKey(keys.style, $event)" />
          </div>

          <!-- Decorazione -->
          <div v-if="keys.decoration" class="typo-row">
            <label class="typo-label">{{ t('Decorazione') }}</label>
            <FieldSelect ui="dropdown" :model-value="values[keys.decoration] || ''" :options="DECORATION_OPTIONS" @update:model-value="emitKey(keys.decoration, $event)" />
          </div>

          <!-- Interlinea (responsive) -->
          <div v-if="keys.lineHeight" class="typo-row">
            <div class="typo-row-head">
              <label class="typo-label">{{ t('Interlinea') }}</label>
              <button
                v-if="isResponsive('lineHeight')"
                type="button"
                class="typo-bp-toggle"
                :class="{ active: bpFor.lineHeight !== 'desktop' }"
                @click="cycleBp('lineHeight')"
                :title="t('Cambia breakpoint')"
              >{{ bpShort(bpFor.lineHeight) }}</button>
            </div>
            <div class="typo-range-row">
              <input
                type="range" min="0.8" max="3" step="0.05"
                :value="numberOr(readResp(keys.lineHeight, bpFor.lineHeight), 1.4)"
                @input="writeResp(keys.lineHeight, bpFor.lineHeight, Number($event.target.value))"
                class="typo-range"
              />
              <input
                type="number" min="0.8" max="3" step="0.05"
                :value="numberOr(readResp(keys.lineHeight, bpFor.lineHeight), '')"
                @input="writeResp(keys.lineHeight, bpFor.lineHeight, Number($event.target.value))"
                class="typo-number"
              />
            </div>
          </div>

          <!-- Spaziatura lettere (responsive, unità configurabile) -->
          <div v-if="keys.letterSpacing" class="typo-row">
            <div class="typo-row-head">
              <label class="typo-label">{{ lsRange.label }}</label>
              <button
                v-if="isResponsive('letterSpacing')"
                type="button"
                class="typo-bp-toggle"
                :class="{ active: bpFor.letterSpacing !== 'desktop' }"
                @click="cycleBp('letterSpacing')"
                :title="t('Cambia breakpoint')"
              >{{ bpShort(bpFor.letterSpacing) }}</button>
            </div>
            <div class="typo-range-row">
              <input
                type="range" :min="lsRange.min" :max="lsRange.max" :step="lsRange.step"
                :value="numberOr(readResp(keys.letterSpacing, bpFor.letterSpacing), 0)"
                @input="writeResp(keys.letterSpacing, bpFor.letterSpacing, Number($event.target.value))"
                class="typo-range"
              />
              <input
                type="number" :min="lsRange.min" :max="lsRange.max" :step="lsRange.step"
                :value="numberOr(readResp(keys.letterSpacing, bpFor.letterSpacing), '')"
                @input="writeResp(keys.letterSpacing, bpFor.letterSpacing, Number($event.target.value))"
                class="typo-number"
              />
            </div>
          </div>

          <!-- Spaziatura parole -->
          <div v-if="keys.wordSpacing" class="typo-row">
            <label class="typo-label">{{ t('Spaziatura parole (px)') }}</label>
            <div class="typo-range-row">
              <input
                type="range" min="-5" max="40" step="0.5"
                :value="numberOr(values[keys.wordSpacing], 0)"
                @input="emitKey(keys.wordSpacing, Number($event.target.value))"
                class="typo-range"
              />
              <input
                type="number" min="-5" max="40" step="0.5"
                :value="numberOr(values[keys.wordSpacing], '')"
                @input="emitKey(keys.wordSpacing, Number($event.target.value))"
                class="typo-number"
              />
            </div>
          </div>

          <!-- Colore testo -->
          <div v-if="keys.color" class="typo-row">
            <label class="typo-label">{{ t('Colore testo') }}</label>
            <FieldColor
              :modelValue="values[keys.color] || ''"
              @update:modelValue="emitKey(keys.color, $event)"
            />
          </div>

          <!-- Colore hover -->
          <div v-if="keys.colorHover" class="typo-row">
            <label class="typo-label">{{ t('Colore testo (hover)') }}</label>
            <FieldColor
              :modelValue="values[keys.colorHover] || ''"
              @update:modelValue="emitKey(keys.colorHover, $event)"
            />
          </div>

          <!-- Ombra testo preset -->
          <div v-if="keys.shadow" class="typo-row">
            <label class="typo-label">{{ t('Ombra testo') }}</label>
            <FieldSelect ui="dropdown" :model-value="values[keys.shadow] || ''" :options="SHADOW_OPTIONS" @update:model-value="emitKey(keys.shadow, $event)" />
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, reactive, computed, watch, nextTick } from 'vue';
import { useStylesStore } from '@/stores/styles';
import FieldFontFamily from './FieldFontFamily.vue';
import FieldColor from './FieldColor.vue';
import FieldSelect from './FieldSelect.vue';

// Opzioni dei select del popover. Label RAW: FieldSelect applica t() internamente.
// I value (incluse le stringhe CSS dell'ombra) restano IDENTICI al vecchio select.
const TAG_OPTIONS = [
  { value: 'h1', label: 'H1' },
  { value: 'h2', label: 'H2' },
  { value: 'h3', label: 'H3' },
  { value: 'h4', label: 'H4' },
  { value: 'h5', label: 'H5' },
  { value: 'h6', label: 'H6' },
  { value: 'p', label: 'Paragrafo' },
  { value: 'span', label: 'Span' },
  { value: 'div', label: 'Div' },
];
const WEIGHT_OPTIONS = [
  { value: '', label: 'Predefinito' },
  { value: '100', label: '100 — Thin' },
  { value: '200', label: '200 — Extra Light' },
  { value: '300', label: '300 — Light' },
  { value: '400', label: '400 — Normale' },
  { value: '500', label: '500 — Medium' },
  { value: '600', label: '600 — Semibold' },
  { value: '700', label: '700 — Bold' },
  { value: '800', label: '800 — Extra Bold' },
  { value: '900', label: '900 — Black' },
];
const TRANSFORM_OPTIONS = [
  { value: '', label: 'Predefinito' },
  { value: 'none', label: 'Nessuna' },
  { value: 'uppercase', label: 'MAIUSCOLO' },
  { value: 'lowercase', label: 'minuscolo' },
  { value: 'capitalize', label: 'Capitalizza' },
];
const STYLE_OPTIONS = [
  { value: '', label: 'Predefinito' },
  { value: 'normal', label: 'Normale' },
  { value: 'italic', label: 'Corsivo' },
  { value: 'oblique', label: 'Obliquo' },
];
const DECORATION_OPTIONS = [
  { value: '', label: 'Predefinito' },
  { value: 'none', label: 'Nessuna' },
  { value: 'underline', label: 'Sottolineato' },
  { value: 'overline', label: 'Sopralineato' },
  { value: 'line-through', label: 'Barrato' },
];
const SHADOW_OPTIONS = [
  { value: '', label: 'Nessuna' },
  { value: '2px 2px 4px rgba(0,0,0,0.3)', label: 'Leggera' },
  { value: '3px 3px 6px rgba(0,0,0,0.4)', label: 'Media' },
  { value: '3px 3px 8px rgba(0,0,0,0.5)', label: 'Media+' },
  { value: '4px 4px 10px rgba(0,0,0,0.5)', label: 'Forte' },
  { value: '4px 4px 12px rgba(0,0,0,0.6)', label: 'Forte+' },
  { value: '0 0 10px rgba(99,102,241,0.6)', label: 'Bagliore primario' },
  { value: '0 0 20px rgba(0,0,0,0.8)', label: 'Alone scuro' },
  { value: '0 0 30px rgba(255,255,255,0.6)', label: 'Alone chiaro' },
];

const props = defineProps({
  keys: { type: Object, default: () => ({}) },
  values: { type: Object, default: () => ({}) },
  label: { type: String, default: 'Tipografia' },
  presetKey: { type: String, default: '' },
  responsiveKeys: { type: Array, default: () => ['size'] },
  sizeMin: { type: Number, default: 8 },
  sizeMax: { type: Number, default: 120 },
  sizeStep: { type: Number, default: 1 },
  // Unità per letter_spacing: 'px' (range -5..20) o 'em' (range 0..0.3)
  letterSpacingUnit: { type: String, default: 'px' },
});

const lsRange = computed(() => {
  if (props.letterSpacingUnit === 'em') return { min: 0, max: 0.3, step: 0.01, label: t('Spaziatura caratteri (em)') };
  return { min: -5, max: 20, step: 0.1, label: t('Spaziatura caratteri (px)') };
});

const emit = defineEmits(['update', 'reset']);

const stylesStore = useStylesStore();
const open = ref(false);
const presetOpen = ref(false);
const rootEl = ref(null);
const popEl = ref(null);
const presetPopEl = ref(null);
const popPos = ref({});
const presetPopPos = ref({});

// Stato breakpoint per ogni chiave logica supportata
const bpFor = reactive({
  size: 'desktop',
  lineHeight: 'desktop',
  letterSpacing: 'desktop',
});

function isResponsive(logicalKey) {
  return props.responsiveKeys.includes(logicalKey);
}

function bpShort(bp) {
  if (bp === 'desktop') return '🖥';
  if (bp === 'tablet') return '◫';
  if (bp === 'mobile') return '▯';
  return bp;
}

function cycleBp(logicalKey) {
  const order = ['desktop', 'tablet', 'mobile'];
  const cur = bpFor[logicalKey] || 'desktop';
  const idx = order.indexOf(cur);
  bpFor[logicalKey] = order[(idx + 1) % order.length];
}

function suffixForBp(bp) {
  return bp === 'desktop' ? '' : '_' + bp;
}

function readResp(baseKey, bp) {
  if (!baseKey) return '';
  const k = baseKey + suffixForBp(bp);
  if (k in (props.values || {})) return props.values[k];
  // fallback al desktop se mancante
  return props.values?.[baseKey] ?? '';
}

function writeResp(baseKey, bp, value) {
  if (!baseKey) return;
  const k = baseKey + suffixForBp(bp);
  emit('update', { key: k, value });
}

function numberOr(v, fallback) {
  if (v === undefined || v === null || v === '') return fallback;
  const n = Number(v);
  return Number.isFinite(n) ? n : fallback;
}

function emitKey(key, value) {
  if (!key) return;
  emit('update', { key, value });
}

function resetAll() {
  for (const k of Object.values(props.keys)) {
    if (!k) continue;
    emit('update', { key: k, value: '' });
    // pulisci anche eventuali varianti responsive
    emit('update', { key: k + '_tablet', value: '' });
    emit('update', { key: k + '_mobile', value: '' });
  }
  emit('reset');
}

function selectPreset(value) {
  if (!props.presetKey) return;
  emit('update', { key: props.presetKey, value });
  presetOpen.value = false;
}

const globalPresets = computed(() => {
  const sets = stylesStore.globalTypography || [];
  return sets.map(s => ({ value: s.id || s.slug || s.name, label: s.name || s.label || s.id }));
});

const hasAnyValue = computed(() => {
  for (const k of Object.values(props.keys)) {
    if (!k) continue;
    const v = props.values?.[k];
    if (v !== undefined && v !== null && v !== '' && v !== 0) return true;
    if (props.values?.[k + '_tablet']) return true;
    if (props.values?.[k + '_mobile']) return true;
  }
  return false;
});

const hasPresetValue = computed(() => {
  return !!props.presetKey && !!props.values?.[props.presetKey];
});

function positionPop(el, target) {
  if (!target) return {};
  const rect = target.getBoundingClientRect();
  const popW = el === 'preset' ? 220 : 280;
  const popH = el === 'preset' ? 280 : 480;
  const spaceBelow = window.innerHeight - rect.bottom;
  const left = Math.max(8, Math.min(window.innerWidth - popW - 8, rect.right - popW));
  if (spaceBelow >= popH || spaceBelow > rect.top) {
    return { position: 'fixed', top: (rect.bottom + 4) + 'px', left: left + 'px', width: popW + 'px' };
  }
  return { position: 'fixed', bottom: (window.innerHeight - rect.top + 4) + 'px', left: left + 'px', width: popW + 'px' };
}

watch(open, (val) => {
  if (val) nextTick(() => { popPos.value = positionPop('main', rootEl.value); });
});
watch(presetOpen, (val) => {
  if (val) nextTick(() => { presetPopPos.value = positionPop('preset', rootEl.value); });
});
</script>

<style scoped>
.typo-wrap {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.typo-row-label {
  font-size: 12px;
  font-weight: 500;
  color: #9CA3AF;
  flex: 1;
}
.typo-actions {
  display: flex;
  align-items: center;
  gap: 4px;
}
.typo-trigger {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 26px;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.12s;
}
.typo-trigger:hover {
  border-color: var(--olo-ui-accent, #e8622a);
  color: var(--olo-ui-accent, #e8622a);
}
.typo-trigger--has-value {
  color: var(--olo-ui-accent, #e8622a);
  border-color: var(--olo-ui-accent, #e8622a);
}

.typo-backdrop {
  position: fixed;
  inset: 0;
  z-index: 99998;
}
.typo-pop {
  z-index: 99999;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.18);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  max-height: 80vh;
}
.typo-pop--narrow { max-height: 60vh; }

.typo-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 12px;
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}
.typo-head-title {
  font-size: 12px;
  font-weight: 600;
  color: #1f2937;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.typo-head-actions {
  display: flex;
  align-items: center;
  gap: 4px;
}
.typo-reset, .typo-close {
  background: transparent;
  border: 0;
  color: #6b7280;
  cursor: pointer;
  padding: 2px 4px;
  border-radius: 4px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.typo-close {
  font-size: 18px;
  line-height: 1;
  width: 20px;
  height: 20px;
}
.typo-reset:hover, .typo-close:hover {
  background: #f3f4f6;
  color: #1f2937;
}

.typo-body {
  padding: 10px 12px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.typo-row {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.typo-row-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.typo-label {
  font-size: 11px;
  font-weight: 500;
  color: #6b7280;
}
.typo-bp-toggle {
  background: transparent;
  border: 1px solid transparent;
  color: #9ca3af;
  cursor: pointer;
  font-size: 12px;
  line-height: 1;
  padding: 2px 5px;
  border-radius: 4px;
  transition: all 0.12s;
}
.typo-bp-toggle:hover {
  background: #f3f4f6;
  color: #1f2937;
}
.typo-bp-toggle.active {
  color: var(--olo-ui-accent, #e8622a);
  background: rgba(232, 98, 42, 0.08);
}
.typo-range-row {
  display: flex;
  align-items: center;
  gap: 8px;
}
.typo-range {
  flex: 1;
  appearance: none;
  height: 4px;
  background: #e5e7eb;
  border-radius: 2px;
}
.typo-range::-webkit-slider-thumb {
  appearance: none;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: var(--olo-ui-accent, #e8622a);
  cursor: pointer;
  border: 2px solid #fff;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.typo-number {
  width: 56px;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 4px 6px;
  font-size: 12px;
  color: #1f2937;
  text-align: right;
}
.typo-number:focus { outline: none; border-color: var(--olo-ui-accent, #e8622a); }

.typo-preset-list {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.typo-preset-item {
  text-align: left;
  background: transparent;
  border: 1px solid transparent;
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
  color: #374151;
}
.typo-preset-item:hover {
  background: #f3f4f6;
}
.typo-preset-item--active {
  background: var(--olo-ui-accent, #e8622a);
  color: #fff;
}
.typo-preset-item--active:hover { filter: brightness(0.92); }
.typo-preset-name { display: block; }

.typo-empty {
  padding: 12px;
  font-size: 11px;
  color: #9ca3af;
  text-align: center;
  font-style: italic;
}

.typo-body::-webkit-scrollbar { width: 6px; }
.typo-body::-webkit-scrollbar-track { background: transparent; }
.typo-body::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
</style>
