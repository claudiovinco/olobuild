<template>
  <!--
    NumberScrubber — atomo numerico UNICO e compatto del builder.
    A riposo è una "valbox" (numero + unità opzionale, 30px, radius 8px, colori
    chiari, identica per famiglia a .olo-bf-value di FieldBox). Due modalità:
      • sliderOnFocus=true  → valbox compatta; al focus/click apre uno SLIDER in
        popover (Teleport in body, NON clippato dallo scroll dell'inspector).
      • sliderOnFocus=false → slider sempre visibile inline + valbox (look classico
        per i contesti larghi / griglie dense).
    In entrambe la ROTELLINA regola il valore quando il campo è ATTIVO (input con
    focus o popover aperto) — niente furto di scroll quando non è attivo.
    Doppio-click sul numero o sullo slider = reset al defaultValue.

    L'atomo emette SEMPRE uno scalare (mai oggetti): stringa RAW se emitAs='string'
    (preserva l'accoppiamento PHP di FieldRange / type=number, '' resta ''), numero
    se emitAs='number'. Il wrapping (oggetto, unità, stringa CSS) resta al genitore:
    i contratti dati salvati NON cambiano.
  -->
  <div class="olo-ns" :class="{ 'olo-ns--withslider': showInlineSlider, 'olo-ns--dark': dark }">
    <!-- Slider inline (solo modalità non compatta) -->
    <input
      v-if="showInlineSlider"
      type="range"
      class="olo-ns-slider olo-ns-slider--inline"
      :value="sliderValue"
      :min="minN"
      :max="maxN"
      :step="step"
      :disabled="disabled"
      :aria-label="ariaLabel || undefined"
      :title="resetHint"
      @input="onSlider($event.target.value)"
      @dblclick="onReset"
    />

    <!-- Valbox compatta: numero + unità -->
    <div
      ref="boxEl"
      class="olo-ns-box"
      :class="{ 'is-open': open, 'is-disabled': disabled }"
      @click="focusNum"
      @focusout="onFocusOut"
    >
      <input
        ref="numEl"
        type="number"
        class="olo-ns-num"
        :value="modelValue"
        :min="hasBounds ? minN : undefined"
        :max="hasBounds ? maxN : undefined"
        :step="step"
        :placeholder="placeholder"
        :disabled="disabled"
        :aria-label="ariaLabel || undefined"
        :title="resetHint"
        @focus="onFocus"
        @input="onNum($event.target.value)"
        @dblclick="onReset"
        @wheel="onWheel"
        @keydown.esc="close"
      />
      <span v-if="unit" class="olo-ns-unit">{{ unit }}</span>
    </div>

    <!-- Popover slider (modalità compatta, on-focus). Teleport in body così lo
         scroll/overflow della sidebar non lo taglia. -->
    <Teleport to="body">
      <div
        v-if="open && popoverSlider"
        ref="popEl"
        class="olo-ns-pop"
        :class="{ 'olo-ns-pop--dark': dark }"
        :style="popStyle"
        role="group"
        :aria-label="ariaLabel || t('Regola valore')"
        @wheel="onWheel"
      >
        <input
          type="range"
          class="olo-ns-slider"
          :value="sliderValue"
          :min="minN"
          :max="maxN"
          :step="step"
          :aria-label="ariaLabel || t('Regola valore')"
          @input="onSlider($event.target.value)"
          @dblclick="onReset"
        />
        <span class="olo-ns-pop-val">{{ displayNum }}<i v-if="unit">{{ unit }}</i></span>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  // Valore corrente: stringa (FieldRange emette grezzo) o numero.
  modelValue: { type: [String, Number], default: 0 },
  // min/max: null o '' = ILLIMITATO su quel lato (es. type=number libero: z-index).
  min: { type: [Number, String, null], default: 0 },
  // max usato solo da slider/clamp-rotella; il numero in input NON viene clampato
  // in modo distruttivo (coerente con FieldBox).
  max: { type: [Number, String, null], default: 100 },
  step: { type: [Number, String], default: 1 },
  // Target del doppio-click. '' verbatim = "eredita/non impostato"; null = fallback a min.
  defaultValue: { type: [Number, String, null], default: null },
  // Tipo emesso: 'string' (RAW, default — preserva FieldRange/type=number) | 'number'.
  emitAs: { type: String, default: 'string' },
  // Etichetta unità a riposo (puramente visiva; NON modifica modelValue).
  unit: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  ariaLabel: { type: String, default: '' },
  // true → valbox compatta + slider in popover; false → slider inline sempre visibile.
  sliderOnFocus: { type: Boolean, default: true },
  // 'light' (default, chrome chiara) | 'dark' (pannelli scuri: editor ProSlider, ecc.).
  // Stesso pattern di FieldSelect. L'accento arancio resta in entrambi.
  theme: { type: String, default: 'light' },
});

const emit = defineEmits(['update:modelValue', 'reset']);

const boxEl = ref(null);
const numEl = ref(null);
const popEl = ref(null);
const open = ref(false);
const popStyle = ref({});
const dark = computed(() => props.theme === 'dark');

// Estremi normalizzati: null/'' = illimitato su quel lato.
const minN = computed(() => (props.min == null || props.min === '') ? null : Number(props.min));
const maxN = computed(() => (props.max == null || props.max === '') ? null : Number(props.max));
// Lo slider ha senso solo con estremi finiti (alcuni type=number sono illimitati).
const hasBounds = computed(() =>
  minN.value != null && maxN.value != null &&
  isFinite(minN.value) && isFinite(maxN.value) && maxN.value > minN.value
);
// Slider inline visibile = modalità non-compatta E estremi noti.
const showInlineSlider = computed(() => !props.sliderOnFocus && hasBounds.value);
// Popover con slider = modalità compatta E estremi noti.
const popoverSlider = computed(() => props.sliderOnFocus && hasBounds.value);

const resetHint = computed(() => t('Doppio click per reimpostare al valore predefinito'));

const stepNum = computed(() => {
  const s = parseFloat(props.step);
  return isNaN(s) || s <= 0 ? 1 : s;
});

// Decimali significativi dello step (0.05 → 2, 0.1 → 1, 1 → 0): l'arrotondamento
// rispetta la precisione dello step invece di un fisso 1 decimale (che romperebbe
// gli step 0.05 di interlinea/spaziatura).
const stepDecimals = computed(() => {
  const s = String(stepNum.value);
  const i = s.indexOf('.');
  return i < 0 ? 0 : (s.length - i - 1);
});
function roundToStep(n) {
  const d = stepDecimals.value;
  if (d <= 0) return Math.round(n);
  const f = Math.pow(10, d);
  return Math.round(n * f) / f;
}

// Parsing coerente con FieldBox: parseFloat se step<1, altrimenti parseInt; poi
// arrotondamento alla precisione dello step.
function parseNum(v) {
  if (v === '' || v == null) return 0;
  const n = stepNum.value < 1 ? parseFloat(v) : parseInt(v, 10);
  if (isNaN(n)) return 0;
  return stepNum.value < 1 ? roundToStep(n) : n;
}

const numericValue = computed(() => parseNum(props.modelValue));
const displayNum = computed(() => {
  const v = props.modelValue;
  return (v === '' || v == null) ? '—' : v;
});

const sliderValue = computed(() => {
  const mn = minN.value ?? 0, mx = maxN.value ?? 100;
  const n = parseFloat(props.modelValue);
  if (isNaN(n)) return mn;
  return Math.min(mx, Math.max(mn, n));
});

// ── Emissione (preserva il contratto dati) ──
function emitRaw(rawString) {
  if (props.emitAs === 'number') emit('update:modelValue', parseNum(rawString));
  else emit('update:modelValue', rawString); // RAW: '' resta '', niente parseInt
}
function emitNumber(n) {
  if (props.emitAs === 'number') emit('update:modelValue', n);
  else emit('update:modelValue', String(n));
}

function onNum(raw) { emitRaw(raw); }
function onSlider(raw) { emitNumber(parseNum(raw)); }

function clamp(n) {
  let out = n;
  if (minN.value != null && isFinite(minN.value)) out = Math.max(minN.value, out);
  if (maxN.value != null && isFinite(maxN.value)) out = Math.min(maxN.value, out);
  return out;
}

function onWheel(e) {
  // Solo quando il campo è ATTIVO: input con focus oppure popover aperto.
  if (!open.value && document.activeElement !== numEl.value) return;
  e.preventDefault();
  const delta = e.deltaY < 0 ? stepNum.value : -stepNum.value;
  let nv = clamp(numericValue.value + delta);
  if (stepNum.value < 1) nv = roundToStep(nv);
  emitNumber(nv);
}

function onReset() {
  emit('reset');
  const d = props.defaultValue;
  // defaultValue esplicito vince. Per emitAs='number' lo coercizziamo (un default
  // stringa non deve violare il tipo); per 'string' resta verbatim (incluso ''=unset).
  if (d !== null && d !== undefined) { emit('update:modelValue', props.emitAs === 'number' ? parseNum(d) : d); return; }
  // Nessun default: ripiega su min (come FieldRange), altrimenti 0 / '' (unset).
  if (minN.value != null) { emit('update:modelValue', props.emitAs === 'number' ? minN.value : String(minN.value)); return; }
  emit('update:modelValue', props.emitAs === 'number' ? 0 : '');
}

// ── Popover ──
function focusNum() {
  if (props.disabled) return;
  numEl.value?.focus();
}

function onFocus() {
  if (!popoverSlider.value || props.disabled) return;
  openPop();
}

function onFocusOut(e) {
  // Chiude SOLO se il focus si sposta su un elemento reale FUORI da valbox e popover
  // (es. Tab verso un altro campo). Se relatedTarget è null — caso tipico del drag
  // dello slider, dove il browser può non spostare il focus — NON chiude: ci pensa
  // onDocDown sul mousedown esterno. Così trascinare lo slider non fa sparire il popover.
  const rt = e.relatedTarget;
  if (!rt) return;
  if (boxEl.value?.contains(rt) || popEl.value?.contains(rt)) return;
  close();
}

async function openPop() {
  if (open.value) return;
  open.value = true;
  await nextTick();
  position();
  window.addEventListener('scroll', onScrollOrResize, { capture: true, passive: true });
  window.addEventListener('resize', onScrollOrResize, { passive: true });
  document.addEventListener('mousedown', onDocDown, true);
}

function close() {
  if (!open.value) return;
  open.value = false;
  window.removeEventListener('scroll', onScrollOrResize, { capture: true });
  window.removeEventListener('resize', onScrollOrResize);
  document.removeEventListener('mousedown', onDocDown, true);
}

function onDocDown(e) {
  if (boxEl.value?.contains(e.target) || popEl.value?.contains(e.target)) return;
  close();
}

function onScrollOrResize() {
  if (!open.value) return;
  position();
}

function position() {
  const trigger = boxEl.value;
  const pop = popEl.value;
  if (!trigger || !pop) return;
  const r = trigger.getBoundingClientRect();
  const popW = pop.offsetWidth || 190; // larghezza fissa da CSS (.olo-ns-pop)
  const popH = pop.offsetHeight || 40;
  const spaceAbove = r.top;
  const spaceBelow = window.innerHeight - r.bottom;
  // Apri SOPRA il campo di default (richiesta UX); sotto solo se sopra non c'è spazio.
  const openUp = spaceAbove >= popH + 10 || spaceAbove >= spaceBelow;
  const left = Math.max(8, Math.min(Math.round(r.left + r.width / 2 - popW / 2), window.innerWidth - popW - 8));
  popStyle.value = {
    position: 'fixed',
    left: `${left}px`,
    top: openUp ? `${Math.round(r.top - popH - 7)}px` : `${Math.round(r.bottom + 7)}px`,
    zIndex: 100000,
  };
}

onBeforeUnmount(() => close());
</script>

<style scoped>
.olo-ns {
  --olo-ns-accent: var(--olo-ui-accent, #e8622a);
  display: flex;
  align-items: center;
  gap: 8px;
}
.olo-ns--withslider { width: 100%; }

/* ── Valbox compatta (numero + unità) ── */
.olo-ns-box {
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  gap: 1px;
  box-sizing: border-box;
  height: 30px;
  min-width: 56px;
  padding: 0 6px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: #fff;
  line-height: 1;
  cursor: text;
  transition: border-color .12s, box-shadow .12s, transform .12s;
}
.olo-ns-box:hover { border-color: #9ca3af; }
.olo-ns-box.is-open { border-color: var(--olo-ns-accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ns-accent) 18%, transparent); }
.olo-ns-box.is-disabled { opacity: .55; pointer-events: none; }
.olo-ns-box:focus-within { border-color: var(--olo-ns-accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ns-accent) 22%, transparent); }
.olo-ns-num {
  width: 40px;
  min-width: 0;
  /* Riempie l'altezza della valbox e azzera padding/min-height nativi dell'input
     number: così il numero resta centrato e NON sporge sopra il bordo. */
  height: 100%;
  min-height: 0;
  margin: 0;
  padding: 0;
  border: none;
  outline: none;
  background: transparent;
  font-size: 12px;
  font-weight: 600;
  line-height: 1;
  color: #1f2937;
  text-align: center;
  font-variant-numeric: tabular-nums;
  -moz-appearance: textfield;
}
.olo-ns-num::-webkit-outer-spin-button,
.olo-ns-num::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.olo-ns-num::placeholder { color: #9ca3af; font-weight: 500; }
.olo-ns-num:disabled { color: #9ca3af; }
/* L'unico indicatore di focus è l'anello arancio sulla valbox: spegniamo l'outline
   nativo dell'input (alcuni stili globali del builder lo forzano blu/indaco) per non
   avere un doppio bordo che "copre" la valbox. !important per vincere il globale. */
.olo-ns-num:focus,
.olo-ns-num:focus-visible { outline: none !important; box-shadow: none !important; }
.olo-ns-unit {
  font-size: 10px;
  font-weight: 600;
  color: #9ca3af;
  padding-right: 2px;
  text-transform: lowercase;
  pointer-events: none;
}

/* ── Slider (inline e popover, stessa lingua di FieldBox) ── */
.olo-ns-slider {
  flex: 1;
  min-width: 40px;
  width: 100%;
  -webkit-appearance: none;
  appearance: none;
  height: 4px;
  background: #d8dce1;
  border-radius: 99px;
  outline: none;
  cursor: pointer;
}
.olo-ns-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 13px; height: 13px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid var(--olo-ns-accent);
  cursor: pointer;
  box-shadow: 0 1px 2px rgba(0,0,0,.2);
}
.olo-ns-slider::-moz-range-thumb {
  width: 13px; height: 13px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid var(--olo-ns-accent);
  cursor: pointer;
}
.olo-ns-slider:focus-visible { box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ns-accent) 25%, transparent); }

/* ── Tema scuro (pannelli scuri: editor ProSlider, ecc.) ── */
.olo-ns--dark .olo-ns-box {
  background: #111827;
  border-color: #374151;
}
.olo-ns--dark .olo-ns-box:hover { border-color: #4b5563; }
.olo-ns--dark .olo-ns-box.is-open,
.olo-ns--dark .olo-ns-box:focus-within { border-color: var(--olo-ns-accent); }
.olo-ns--dark .olo-ns-num { color: #e5e7eb; }
.olo-ns--dark .olo-ns-num::placeholder { color: #6b7280; }
.olo-ns--dark .olo-ns-num:disabled { color: #6b7280; }
.olo-ns--dark .olo-ns-unit { color: #9ca3af; }
.olo-ns--dark .olo-ns-slider { background: #374151; }
</style>

<style>
/* Popover teleportato in <body>: non scoped (altrimenti l'attributo data-v non
   raggiunge il nodo fuori dal componente). Palette CHIARA fissa; accento chrome
   con fallback perché in <body> --olo-ui-accent potrebbe non ereditare. */
.olo-ns-pop {
  --olo-ns-accent: var(--olo-ui-accent, #e8622a);
  box-sizing: border-box;
  width: 190px;
  display: flex;
  align-items: center;
  gap: 10px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(16,24,40,.08), 0 12px 26px rgba(16,24,40,.14);
  padding: 8px 12px;
  animation: olo-ns-in .12s ease;
}
.olo-ns-pop .olo-ns-slider {
  flex: 1; min-width: 0;
  -webkit-appearance: none; appearance: none;
  width: 100%; height: 4px; border-radius: 99px; background: #d8dce1; outline: none; cursor: pointer;
}
.olo-ns-pop .olo-ns-slider::-webkit-slider-thumb {
  -webkit-appearance: none; width: 13px; height: 13px; border-radius: 50%;
  background: #fff; border: 2px solid var(--olo-ns-accent); cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,.2);
}
.olo-ns-pop .olo-ns-slider::-moz-range-thumb {
  width: 13px; height: 13px; border-radius: 50%; background: #fff; border: 2px solid var(--olo-ns-accent); cursor: pointer;
}
.olo-ns-pop .olo-ns-slider:focus-visible { box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ns-accent) 25%, transparent); }
.olo-ns-pop-val {
  flex: 0 0 auto; min-width: 22px; text-align: right;
  font-size: 12px; font-weight: 700; color: #1f2937; font-variant-numeric: tabular-nums;
}
.olo-ns-pop-val i { font-style: normal; font-size: 9px; font-weight: 600; color: #9ca3af; margin-left: 1px; }
/* popover tema scuro */
.olo-ns-pop--dark { background: #1f2937; border-color: #374151; box-shadow: 0 4px 10px rgba(0,0,0,.3), 0 12px 26px rgba(0,0,0,.4); }
.olo-ns-pop--dark .olo-ns-slider { background: #374151; }
.olo-ns-pop--dark .olo-ns-pop-val { color: #f9fafb; }
@keyframes olo-ns-in { from { opacity: 0; transform: translateY(2px); } to { opacity: 1; transform: translateY(0); } }
@media (prefers-reduced-motion: reduce) {
  .olo-ns-pop { animation: none; }
}
</style>
