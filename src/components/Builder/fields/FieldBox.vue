<template>
  <!--
    FieldBox — controllo box-model compatto (design handoff "olobuild_boxcontrol").
    Sostituisce i controlli "4 lati / 4 angoli" alti con una riga compatta:
    collega/separa + slider + valore (+ unità opzionale), mini-input on-demand.
    Contratto dati: numero, oppure oggetto { tl,tr,br,bl } (corners) / { top,right,bottom,left }
    (sides) con `linked` facoltativo. Collegato = numero, linked:true o quattro valori uguali
    (salvo linked:false); il formato ricevuto si conserva (un oggetto resta oggetto).
    Negativi solo con allowNegative (margini).
    Hover e breakpoint NON sono gestiti qui: li avvolge InspectorField (occhio hover
    + wrapper responsive), come per gli altri field. Vedi docs handoff README.
  -->
  <div class="olo-boxfield">
    <div class="olo-bf-row">
      <button
        type="button"
        class="olo-bf-link"
        :class="{ 'is-linked': linked }"
        @click="toggleLink"
        :title="linked ? t('Separa i valori') : t('Collega i valori')"
        :aria-pressed="linked"
      >
        <svg v-if="linked" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 7h3a5 5 0 0 1 5 5 5 5 0 0 1-4.46 4.97"/><path d="M9 17H6a5 5 0 0 1-5-5 5 5 0 0 1 4.46-4.97"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
      </button>

      <input
        type="range"
        class="olo-bf-slider"
        :class="{ 'has-zero': sliderMinEff < 0 }"
        :style="sliderMinEff < 0 ? { '--olo-bf-zero': zeroPct + '%' } : null"
        :min="sliderMinEff"
        :max="sliderMax"
        :step="sliderStep"
        :value="sliderValue"
        @input="setAll($event.target.value)"
        :aria-label="t('Valore')"
      />

      <div class="olo-bf-value">
        <input
          type="number"
          class="olo-bf-num"
          :min="allowNegative ? null : 0"
          :step="sliderStep"
          :value="linked ? allValue : null"
          :placeholder="linked ? '' : '—'"
          :disabled="!linked"
          @input="onNumInput($event, null)"
          @blur="onNumBlur($event, allValue)"
          @wheel="handleNumberWheel"
        />
        <FieldSelect
          v-if="showUnitDropdown"
          ui="dropdown"
          size="compact"
          class="olo-bf-unitsel"
          :model-value="unit"
          :options="unitOptions"
          @update:model-value="unit = $event"
        />
        <span v-else class="olo-bf-unit">{{ unit }}</span>
      </div>

      <span
        v-if="showChip"
        class="olo-bf-chip"
        :style="chipStyle"
        :title="t('Anteprima')"
        aria-hidden="true"
      ></span>
    </div>

    <!-- Valori separati on-demand. Ogni cella sta dove sta il suo angolo o lato
         (row/col), mostra l'icona di QUELL'angolo e scrive QUELLA chiave: l'ordine
         dei dati ({tl,tr,br,bl} / {top,right,bottom,left}) non decide più la griglia.
         Audit `fieldbox-celle`. -->
    <div v-if="!linked" class="olo-bf-grid" :class="'is-' + mode">
      <label
        v-for="c in celle"
        :key="c.k"
        class="olo-bf-cell"
        :title="c.label"
        :style="{ gridRow: c.row, gridColumn: c.col }"
      >
        <span class="olo-bf-ic" :style="{ transform: 'rotate(' + c.r + ')' }" v-html="modeIcon"></span>
        <input
          type="number"
          :min="allowNegative ? null : 0"
          :step="sliderStep"
          class="olo-bf-mini"
          :value="vals[c.k]"
          :aria-label="c.label"
          @input="onNumInput($event, c.k)"
          @blur="onNumBlur($event, vals[c.k])"
          @wheel="handleNumberWheel"
        />
      </label>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { t } from '@/i18n';
import { handleNumberWheel } from '@/utils/numberInputWheel';
import FieldSelect from './FieldSelect.vue';

const props = defineProps({
  modelValue: { default: 0 },
  // 'corners' (tl/tr/br/bl) | 'sides' (top/right/bottom/left)
  mode: { type: String, default: 'corners' },
  // unità nel dropdown; se 1 sola → solo etichetta (nessun dropdown)
  units: { type: Array, default: () => ['px'] },
  defaultUnit: { type: String, default: 'px' },
  // range dello slider (il campo numerico accetta anche oltre, senza clamp distruttivo)
  sliderMax: { type: Number, default: 100 },
  sliderStep: { type: Number, default: 1 },
  // 'auto' | 'radius' | 'spacing' | 'border' | 'none'
  preview: { type: String, default: 'auto' },
  // Valori negativi ammessi (solo i margini: padding, raggio e spessori restano ≥ 0).
  // Si accende ESPLICITAMENTE — campo con `min < 0`, Margine del Contenitore — mai dal
  // nome della chiave: dove il renderer usa absint un negativo non farebbe niente.
  allowNegative: { type: Boolean, default: false },
  // Inizio dello slider con allowNegative (null = simmetrico, -sliderMax).
  sliderMin: { type: Number, default: null },
});
const emit = defineEmits(['update:modelValue']);

// k = chiave salvata · r = rotazione dell'icona di base (che disegna l'angolo in ALTO A
// SINISTRA, o il lato ALTO) · row/col = posizione della cella nella griglia.
// L'ordine dell'array è l'ordine dei DATI; la griglia si ordina per posizione (celle).
const KEYS = {
  corners: [
    { k: 'tl', r: '0deg', row: 1, col: 1, label: t('Alto sinistra') },
    { k: 'tr', r: '90deg', row: 1, col: 2, label: t('Alto destra') },
    { k: 'br', r: '180deg', row: 2, col: 2, label: t('Basso destra') },
    { k: 'bl', r: '270deg', row: 2, col: 1, label: t('Basso sinistra') },
  ],
  sides: [
    { k: 'top', r: '0deg', row: 1, col: 1, label: t('Alto') },
    { k: 'right', r: '90deg', row: 1, col: 2, label: t('Destra') },
    { k: 'bottom', r: '180deg', row: 1, col: 3, label: t('Basso') },
    { k: 'left', r: '270deg', row: 1, col: 4, label: t('Sinistra') },
  ],
};
const keys = computed(() => KEYS[props.mode] || KEYS.corners);
const keyList = computed(() => keys.value.map((x) => x.k));
// Ordine di lettura (riga, poi colonna): anche il Tab segue la posizione sullo schermo.
const celle = computed(() => [...keys.value].sort((a, b) => a.row - b.row || a.col - b.col));

const CORNER_SVG = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20v-7a9 9 0 0 1 9-9h7"/></svg>';
const EDGE_SVG = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="7" width="16" height="13" rx="2" stroke-width="1.5" opacity="0.4"/><line x1="4" y1="4" x2="20" y2="4" stroke-width="2.6"/></svg>';
const modeIcon = computed(() => (props.mode === 'sides' ? EDGE_SVG : CORNER_SVG));

const unit = ref(props.defaultUnit || props.units[0] || 'px');
const showUnitDropdown = computed(() => props.units.length > 1);
// Opzioni dropdown unità (label tecniche as-is)
const unitOptions = computed(() => props.units.map((u) => ({ value: u, label: u })));

function num(v) {
  const parsed = props.sliderStep < 1 ? parseFloat(v) : parseInt(v, 10);
  if (isNaN(parsed) || (parsed < 0 && !props.allowNegative)) return 0;
  return props.sliderStep < 1 ? Math.round(parsed * 10) / 10 : parsed;
}

const isObj = (v) => !!v && typeof v === 'object';

// Collegato = un valore solo: numero, oggetto con linked:true, oppure oggetto con i
// quattro valori uguali (ponte legacy, default dei config, reset dell'hover) — salvo
// linked:false esplicito, che è la scelta «Separa» dell'utente. Stato locale aggiornato
// a ogni nuovo valore: chi riduce a numero i valori uniformi (StyleBoxStack) non chiude
// la griglia appena aperta, perché il numero che torna è lo stesso.
function derivaCollegato(v) {
  if (!isObj(v)) return true;
  if (v.linked === true) return true;
  if (v.linked === false) return false;
  const n = keyList.value.map((k) => num(v[k]));
  return n.every((x) => x === n[0]);
}
const linked = ref(derivaCollegato(props.modelValue));
watch(
  () => props.modelValue,
  (v) => { linked.value = derivaCollegato(v); },
);

const vals = computed(() => {
  const v = props.modelValue;
  const out = {};
  if (isObj(v)) {
    keyList.value.forEach((k) => { out[k] = num(v[k]); });
  } else {
    const n = num(v);
    keyList.value.forEach((k) => { out[k] = n; });
  }
  return out;
});

const allValue = computed(() => {
  if (!isObj(props.modelValue)) return num(props.modelValue);
  const n = keyList.value.map((k) => vals.value[k]);
  return linked.value ? n[0] : Math.max(props.allowNegative ? -Infinity : 0, ...n);
});
const sliderMinEff = computed(() => {
  if (!props.allowNegative) return 0;
  return props.sliderMin !== null && props.sliderMin < 0 ? props.sliderMin : -props.sliderMax;
});
const sliderValue = computed(() => Math.max(sliderMinEff.value, Math.min(props.sliderMax, allValue.value)));
// Posizione dello zero sul binario (slider con negativi): una tacca lo segna.
const zeroPct = computed(() => (-sliderMinEff.value / (props.sliderMax - sliderMinEff.value)) * 100);

const pvKind = computed(() =>
  props.preview === 'auto' ? (props.mode === 'corners' ? 'radius' : 'spacing') : props.preview,
);
const showChip = computed(() => pvKind.value === 'radius');
const chipStyle = computed(() => {
  const c = vals.value;
  return { borderRadius: `${c.tl}px ${c.tr}px ${c.br}px ${c.bl}px` };
});

// Oggetto da emettere: conserva le chiavi che non sono lati/angoli (es. quelle del
// bordo) e mette `linked` esplicito, così la scelta Collega/Separa sopravvive anche a
// quattro valori uguali.
function oggetto(sides, isLinked) {
  const base = isObj(props.modelValue) ? { ...props.modelValue } : {};
  return { ...base, ...sides, linked: isLinked };
}
function uguali(n) {
  const obj = {};
  keyList.value.forEach((k) => { obj[k] = n; });
  return obj;
}

function setAll(v) {
  const n = num(v);
  if (linked.value) {
    // Il formato ricevuto resta: un numero torna numero, un oggetto resta oggetto.
    emit('update:modelValue', isObj(props.modelValue) ? oggetto(uguali(n), true) : n);
  } else {
    // slider in modalità separata: imposta tutti i lati/angoli mantenendo l'oggetto
    emit('update:modelValue', oggetto(uguali(n), false));
  }
}

function setKey(k, v) {
  emit('update:modelValue', oggetto({ ...vals.value, [k]: num(v) }, false));
}

// Un campo vuoto o col solo «-» (si sta scrivendo un negativo) non è un valore:
// emetterlo come 0 rimetterebbe «0» nella casella sotto le dita.
function onNumInput(e, k) {
  const raw = e.target.value;
  if (raw === '' || isNaN(parseFloat(raw))) return;
  if (k === null) setAll(raw); else setKey(k, raw);
}
function onNumBlur(e, valore) {
  if (e.target.value === '' || isNaN(parseFloat(e.target.value))) e.target.value = valore;
}

function toggleLink() {
  if (linked.value) {
    linked.value = false;
    emit('update:modelValue', oggetto({ ...vals.value }, false));
  } else {
    linked.value = true;
    const n = Math.max(props.allowNegative ? -Infinity : 0, ...keyList.value.map((k) => vals.value[k]));
    emit('update:modelValue', isObj(props.modelValue) ? oggetto(uguali(n), true) : n);
  }
}
</script>

<style scoped>
.olo-boxfield {
  /* Accento del controllo: arancio CHROME del builder quando un contenitore
     espone --olo-ui-accent (es. FieldBorder), altrimenti fallback INVARIATO al
     primario — nessuna regressione per margine/padding/raggio usati altrove. */
  --olo-bf-accent: var(--olo-ui-accent, #e8622a);
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 2px 0;
}
.olo-bf-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* collega/separa */
.olo-bf-link {
  flex-shrink: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: #fff;
  color: #6b7280;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-bf-link:hover {
  border-color: var(--olo-bf-accent);
  color: var(--olo-bf-accent);
}
.olo-bf-link.is-linked {
  border-color: var(--olo-bf-accent);
  color: var(--olo-bf-accent);
  background: color-mix(in srgb, var(--olo-bf-accent) 10%, #fff);
}

/* slider */
.olo-bf-slider {
  flex: 1;
  min-width: 40px;
  -webkit-appearance: none;
  appearance: none;
  height: 5px;
  background: #d1d5db;
  border-radius: 3px;
  outline: none;
  cursor: pointer;
}
.olo-bf-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid var(--olo-bf-accent);
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}
.olo-bf-slider::-moz-range-thumb {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid var(--olo-bf-accent);
  cursor: pointer;
}
.olo-bf-slider:focus-visible {
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-bf-accent) 25%, transparent);
}
/* Con i negativi una tacca scura segna lo zero sul binario */
.olo-bf-slider.has-zero {
  background: linear-gradient(to right,
    #d1d5db calc(var(--olo-bf-zero) - 1px),
    #4b5563 calc(var(--olo-bf-zero) - 1px),
    #4b5563 calc(var(--olo-bf-zero) + 1px),
    #d1d5db calc(var(--olo-bf-zero) + 1px));
}

/* valore + unità */
.olo-bf-value {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 2px;
  box-sizing: border-box;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: #fff;
  height: 30px;
  padding: 0 2px 0 6px;
  line-height: 1;
}
/* Allineato a NumberScrubber (.olo-ns-box): stesso bordo, stesso peso del numero,
   input che riempie l'altezza e resta centrato senza sporgere sul bordo. */
.olo-bf-num {
  width: 40px;
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
.olo-bf-num:disabled {
  color: #9ca3af;
}
.olo-bf-num::-webkit-inner-spin-button,
.olo-bf-num::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.olo-bf-unit {
  font-size: 10px;
  font-weight: 600;
  color: #9ca3af;
  padding-right: 4px;
  text-transform: lowercase;
}
/* FieldSelect unità "blended" nel box valore: niente bordo/sfondo propri
   (li fornisce .olo-bf-value), larghezza al contenuto e non al 100% */
.olo-bf-value .olo-bf-unitsel {
  flex: 0 0 auto;
  width: auto;
}
.olo-bf-unitsel :deep(.fsel-trigger) {
  height: 100%;
  border: none;
  border-radius: 0;
  background: transparent;
  padding: 0 2px;
  font-size: 10px;
  font-weight: 600;
  color: #6b7280;
}

/* chip anteprima radius */
.olo-bf-chip {
  flex-shrink: 0;
  width: 26px;
  height: 26px;
  border: 2px solid var(--olo-bf-accent);
  background: color-mix(in srgb, var(--olo-bf-accent) 12%, #fff);
  transition: border-radius 0.2s ease;
}

/* mini-input lati/angoli separati */
.olo-bf-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 6px;
  padding-left: 38px;
}
/* Lati: una riga Alto · Destra · Basso · Sinistra (l'ordine del CSS), a tutta larghezza */
.olo-bf-grid.is-sides {
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 4px;
  padding-left: 0;
}
.olo-bf-grid.is-sides .olo-bf-cell {
  gap: 3px;
  padding: 0 3px 0 5px;
}
.olo-bf-cell {
  display: flex;
  align-items: center;
  gap: 5px;
  border: 1px solid #d1d5db;
  border-radius: 7px;
  background: #fff;
  height: 28px;
  padding: 0 4px 0 7px;
  cursor: text;
}
.olo-bf-cell:focus-within {
  border-color: var(--olo-bf-accent);
  box-shadow: 0 0 0 2px color-mix(in srgb, var(--olo-bf-accent) 18%, transparent);
}
.olo-bf-ic {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  flex-shrink: 0;
}
.olo-bf-mini {
  width: 100%;
  min-width: 0;
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
.olo-bf-mini::-webkit-inner-spin-button,
.olo-bf-mini::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
