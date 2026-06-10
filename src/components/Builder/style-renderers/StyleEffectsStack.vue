<template>
  <!--
    StyleEffectsStack — sezione "Effetti" compatta e coerente (sorella di StyleBoxStack /
    StyleLayoutStack). Raggruppa Ombra / Opacità / Trasformazione / Ombra testo / Filtro
    sfondo / Maschera con UN toggle Normale/Hover in cima. NIENTE switch device.
    Chiavi salvate INVARIATE e allineate al renderer PHP (collect_hover_css):
      Normale → style.<key>          Hover → style.hover.<key>
      opacity · transform(oggetto, hover su chiavi piatte transform_*) · text_shadow_* ·
      backdrop_* · shadow + shadow_custom · mask (non hoverable).
    Accento arancio CHROME del builder (--olo-ui-accent). Anteprima effetti dal vivo in fondo.
  -->
  <div class="olo-effstack">
    <!-- Stato Normale / Hover + indicatore device attivo (gli effetti sono per-device) -->
    <div class="olo-es-state">
      <span v-if="bp !== 'desktop'" class="olo-es-bp">{{ bpLabel }}</span>
      <div class="olo-es-seg">
        <button type="button" :class="{ on: !isHover }" @click="state = 'normal'">{{ t('Normale') }}</button>
        <button type="button" :class="{ on: isHover }" @click="state = 'hover'">
          {{ t('Hover') }}<span v-if="hasAnyHover" class="olo-es-dot"></span>
        </button>
      </div>
    </div>

    <!-- OMBRA -->
    <div class="olo-es-group">
      <span class="olo-es-gtitle">{{ t('Ombra') }}</span>
      <div class="olo-es-scale" role="radiogroup" :aria-label="t('Livello ombra')">
        <button v-for="opt in shadowScale" :key="opt.v" type="button"
                role="radio" :aria-checked="String(isShadow(opt.v))"
                :class="['olo-es-sc', `olo-es-sc--${opt.v}`, { on: isShadow(opt.v) }]"
                :aria-label="opt.aria" @click="setvGlobal('shadow', opt.v)">
          <span class="olo-es-sc-chip"></span>
          <span class="olo-es-sc-lab">{{ opt.label }}</span>
        </button>
      </div>
      <FieldBoxShadow v-if="shadowPreset === 'custom'" :modelValue="svGlobal('shadow_custom', {})" @update:modelValue="setvGlobal('shadow_custom', $event)" />
    </div>

    <!-- OPACITÀ -->
    <div class="olo-es-group">
      <span class="olo-es-gtitle">{{ t('Opacità') }}<span v-if="bp !== 'desktop'" class="olo-es-gbp">{{ bpLabel }}</span></span>
      <div class="olo-es-sliderrow">
        <span class="olo-es-lab">{{ t('Opacità') }}</span>
        <input type="range" class="olo-es-slider" min="0" max="100" step="1"
          :value="num(sv('opacity', 100), 100)" @input="setv('opacity', parseInt($event.target.value))" :aria-label="t('Opacità')" />
        <span class="olo-es-val">
          <input type="text" inputmode="numeric" class="olo-es-valinput"
            :value="num(sv('opacity', 100), 100)" @change="setvClamped('opacity', $event.target.value, 0, 100, 100)" :aria-label="t('Opacità')" />
          <i>%</i>
        </span>
      </div>
    </div>

    <!-- TRASFORMAZIONE -->
    <div class="olo-es-group">
      <span class="olo-es-gtitle">{{ t('Trasformazione') }}<span v-if="bp !== 'desktop'" class="olo-es-gbp">{{ bpLabel }}</span></span>
      <div class="olo-es-sliderrow">
        <span class="olo-es-lab">{{ t('Rotaz.') }}</span>
        <input type="range" class="olo-es-slider" min="-180" max="180" step="1"
          :value="tget('rotate', 0)" @input="tset('rotate', parseInt($event.target.value))" :aria-label="t('Rotazione')" />
        <span class="olo-es-val">
          <input type="text" inputmode="numeric" class="olo-es-valinput"
            :value="tget('rotate', 0)" @change="tsetClamped('rotate', $event.target.value, -180, 180, 0)" :aria-label="t('Rotazione')" />
          <i>°</i>
        </span>
      </div>
      <div class="olo-es-sliderrow">
        <span class="olo-es-lab">{{ t('Scala') }}</span>
        <input type="range" class="olo-es-slider" min="10" max="300" step="5"
          :value="Math.round(tget('scale', 1) * 100)" @input="tset('scale', parseInt($event.target.value) / 100)" :aria-label="t('Scala')" />
        <span class="olo-es-val">
          <input type="text" inputmode="numeric" class="olo-es-valinput"
            :value="Math.round(tget('scale', 1) * 100)" @change="setScalePct($event.target.value)" :aria-label="t('Scala')" />
          <i>%</i>
        </span>
      </div>
      <div class="olo-es-duo">
        <div class="olo-es-field">
          <span class="olo-es-lab">{{ t('Sposta X') }}</span>
          <div class="olo-es-numwrap">
            <input type="text" inputmode="numeric" class="olo-es-num" :value="tget('translateX', 0)"
              @input="tset('translateX', int($event.target.value))" :aria-label="t('Sposta X')" />
            <span class="olo-es-unit">px</span>
          </div>
        </div>
        <div class="olo-es-field">
          <span class="olo-es-lab">{{ t('Sposta Y') }}</span>
          <div class="olo-es-numwrap">
            <input type="text" inputmode="numeric" class="olo-es-num" :value="tget('translateY', 0)"
              @input="tset('translateY', int($event.target.value))" :aria-label="t('Sposta Y')" />
            <span class="olo-es-unit">px</span>
          </div>
        </div>
      </div>
      <div class="olo-es-duo">
        <div class="olo-es-field">
          <span class="olo-es-lab">{{ t('Skew X') }}</span>
          <div class="olo-es-numwrap">
            <input type="text" inputmode="numeric" class="olo-es-num" :value="tget('skewX', 0)"
              @input="tset('skewX', int($event.target.value))" :aria-label="t('Skew X')" />
            <span class="olo-es-unit">°</span>
          </div>
        </div>
        <div class="olo-es-field">
          <span class="olo-es-lab">{{ t('Skew Y') }}</span>
          <div class="olo-es-numwrap">
            <input type="text" inputmode="numeric" class="olo-es-num" :value="tget('skewY', 0)"
              @input="tset('skewY', int($event.target.value))" :aria-label="t('Skew Y')" />
            <span class="olo-es-unit">°</span>
          </div>
        </div>
      </div>
      <div v-if="!isHover && bp === 'desktop'" class="olo-es-row">
        <span class="olo-es-lab">{{ t('Origine') }}</span>
        <FieldSelect
          ui="dropdown"
          class="olo-es-selwrap"
          :model-value="tget('origin', 'center')"
          :options="ORIGIN_OPTIONS"
          @update:model-value="tset('origin', $event)"
        />
      </div>
    </div>

    <!-- OMBRA TESTO -->
    <div class="olo-es-group">
      <span class="olo-es-gtitle">{{ t('Ombra testo') }}<span v-if="bp !== 'desktop'" class="olo-es-gbp">{{ bpLabel }}</span></span>
      <div class="olo-es-trio">
        <div class="olo-es-field">
          <span class="olo-es-lab">{{ t('Oriz.') }}</span>
          <div class="olo-es-numwrap">
            <input type="text" inputmode="numeric" class="olo-es-num" :value="tsGet('h')" @input="setv('text_shadow_h', int($event.target.value))" :aria-label="t('Orizzontale')" />
            <span class="olo-es-unit">px</span>
          </div>
        </div>
        <div class="olo-es-field">
          <span class="olo-es-lab">{{ t('Vert.') }}</span>
          <div class="olo-es-numwrap">
            <input type="text" inputmode="numeric" class="olo-es-num" :value="tsGet('v')" @input="setv('text_shadow_v', int($event.target.value))" :aria-label="t('Verticale')" />
            <span class="olo-es-unit">px</span>
          </div>
        </div>
        <div class="olo-es-field">
          <span class="olo-es-lab">{{ t('Sfoc.') }}</span>
          <div class="olo-es-numwrap">
            <input type="text" inputmode="numeric" class="olo-es-num" :value="tsGet('blur')" @input="setv('text_shadow_blur', Math.max(0, int($event.target.value)))" :aria-label="t('Sfocatura')" />
            <span class="olo-es-unit">px</span>
          </div>
        </div>
      </div>
      <FieldColor :modelValue="String(sv('text_shadow_color', '') || '#000000')" @update:modelValue="setv('text_shadow_color', $event)" />
    </div>

    <!-- FILTRO SFONDO -->
    <div class="olo-es-group">
      <span class="olo-es-gtitle">{{ t('Filtro sfondo · glassmorphism') }}<span v-if="bp !== 'desktop'" class="olo-es-gbp">{{ bpLabel }}</span></span>
      <div class="olo-es-sliderrow">
        <span class="olo-es-lab">{{ t('Sfoc.') }}</span>
        <input type="range" class="olo-es-slider" min="0" max="30" step="1"
          :value="num(sv('backdrop_blur', 0), 0)" @input="setv('backdrop_blur', parseInt($event.target.value))" :aria-label="t('Sfocatura sfondo')" />
        <span class="olo-es-val">
          <input type="text" inputmode="numeric" class="olo-es-valinput"
            :value="num(sv('backdrop_blur', 0), 0)" @change="setvClamped('backdrop_blur', $event.target.value, 0, 30, 0)" :aria-label="t('Sfocatura sfondo')" />
          <i>px</i>
        </span>
      </div>
      <div class="olo-es-sliderrow">
        <span class="olo-es-lab">{{ t('Lumin.') }}</span>
        <input type="range" class="olo-es-slider" min="0" max="200" step="5"
          :value="num(sv('backdrop_brightness', 100), 100)" @input="setv('backdrop_brightness', parseInt($event.target.value))" :aria-label="t('Luminosità')" />
        <span class="olo-es-val">
          <input type="text" inputmode="numeric" class="olo-es-valinput"
            :value="num(sv('backdrop_brightness', 100), 100)" @change="setvClamped('backdrop_brightness', $event.target.value, 0, 200, 100)" :aria-label="t('Luminosità')" />
          <i>%</i>
        </span>
      </div>
      <div class="olo-es-sliderrow">
        <span class="olo-es-lab">{{ t('Satur.') }}</span>
        <input type="range" class="olo-es-slider" min="0" max="200" step="5"
          :value="num(sv('backdrop_saturate', 100), 100)" @input="setv('backdrop_saturate', parseInt($event.target.value))" :aria-label="t('Saturazione')" />
        <span class="olo-es-val">
          <input type="text" inputmode="numeric" class="olo-es-valinput"
            :value="num(sv('backdrop_saturate', 100), 100)" @change="setvClamped('backdrop_saturate', $event.target.value, 0, 200, 100)" :aria-label="t('Saturazione')" />
          <i>%</i>
        </span>
      </div>
    </div>

    <!-- MASCHERA (non hoverable: solo stato normale) -->
    <div v-if="!isHover" class="olo-es-group">
      <span class="olo-es-gtitle">{{ t('Maschera') }}</span>
      <div class="olo-es-row">
        <span class="olo-es-lab">{{ t('Forma') }}</span>
        <FieldSelect
          ui="dropdown"
          class="olo-es-selwrap"
          :model-value="mask"
          :options="MASK_OPTIONS"
          @update:model-value="onMask($event)"
        />
      </div>
    </div>

    <!-- Anteprima effetti -->
    <div class="olo-es-preview">
      <div class="olo-es-pv-chip" :style="previewStyle">{{ t('anteprima effetti') }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import FieldColor from '../fields/FieldColor.vue';
import FieldBoxShadow from '../fields/FieldBoxShadow.vue';
import FieldSelect from '../fields/FieldSelect.vue';

// Label RAW: FieldSelect applica t() internamente. Value INVARIATI.
const ORIGIN_OPTIONS = [
  { value: 'center', label: 'Centro' },
  { value: 'top left', label: 'Alto SX' },
  { value: 'top center', label: 'Alto Centro' },
  { value: 'top right', label: 'Alto DX' },
  { value: 'center left', label: 'Centro SX' },
  { value: 'center right', label: 'Centro DX' },
  { value: 'bottom left', label: 'Basso SX' },
  { value: 'bottom center', label: 'Basso Centro' },
  { value: 'bottom right', label: 'Basso DX' },
];
const MASK_OPTIONS = [
  { value: 'none', label: 'Nessuna' },
  { value: 'circle', label: 'Cerchio' },
  { value: 'triangle', label: 'Triangolo' },
  { value: 'diamond', label: 'Diamante' },
  { value: 'hexagon', label: 'Esagono' },
  { value: 'star', label: 'Stella' },
  { value: 'blob', label: 'Blob' },
  { value: 'wave', label: 'Onda' },
];
import { useBuilderStore } from '@/stores/builder';
import { t } from '@/i18n';

const props = defineProps({
  tileStyle: { type: Object, required: true },
});
const emit = defineEmits(['update']);

const builderStore = useBuilderStore();

const state = ref('normal');
const isHover = computed(() => state.value === 'hover');

// Breakpoint attivo dal viewport globale (come StyleBoxStack). Gli effetti sono PER-DEVICE:
// chiave base su Desktop, chiave_<bp> sugli altri device — sia per Normale sia per Hover.
const BP_LABELS = {
  desktop: 'Desktop', widescreen: 'Desktop',
  tablet_landscape: 'Tablet L', tablet: 'Tablet',
  mobile_landscape: 'Mobile L', mobile: 'Mobile',
};
const bp = ref('desktop');
watch(() => builderStore.viewMode, (mode) => {
  bp.value = (mode === 'desktop' || mode === 'widescreen') ? 'desktop' : mode;
}, { immediate: true });
const bpLabel = computed(() => t(BP_LABELS[bp.value] || ''));
function bpKey(base) {
  return bp.value === 'desktop' ? base : `${base}_${bp.value}`;
}

function int(v) { const n = parseInt(v, 10); return isNaN(n) ? 0 : n; }
function num(v, def) { const n = parseInt(v, 10); return isNaN(n) ? def : n; }
function clamp(n, lo, hi) { return Math.min(hi, Math.max(lo, n)); }

// Scrittura da input numerico (box a destra degli slider): parse → clamp ai limiti dello
// slider → set. Così digitare un valore equivale a trascinare, e i due restano sincronizzati.
function setvClamped(key, raw, lo, hi, def) {
  let n = parseInt(raw, 10);
  if (isNaN(n)) n = def;
  setv(key, clamp(n, lo, hi));
}
function tsetClamped(prop, raw, lo, hi, def) {
  let n = parseInt(raw, 10);
  if (isNaN(n)) n = def;
  tset(prop, clamp(n, lo, hi));
}
function setScalePct(raw) {
  // La scala è mostrata in % ma salvata come fattore (1 = 100%)
  let n = parseInt(raw, 10);
  if (isNaN(n)) n = 100;
  tset('scale', clamp(n, 10, 300) / 100);
}

// ── lettura/scrittura per stato (Normale = style.<key>; Hover = style.hover.<key>) ──
function sv(key, def) {
  const src = isHover.value ? (props.tileStyle.hover || {}) : props.tileStyle;
  const v = src[bpKey(key)];
  return v === undefined || v === null ? def : v;
}
function setv(key, value) {
  emit('update', { type: isHover.value ? 'hover' : 'main', key: bpKey(key), value });
}
// Varianti NON per-device (solo hover-aware) per Ombra; e main-only per Maschera.
// Ombra (box-shadow) e Maschera restano globali: il loro rendering responsivo è complesso e poco
// comune. Gli altri 4 effetti (opacità, trasformazione, ombra testo, filtro sfondo) sono per-device.
function svGlobal(key, def) {
  const src = isHover.value ? (props.tileStyle.hover || {}) : props.tileStyle;
  const v = src[key];
  return v === undefined || v === null ? def : v;
}
function setvGlobal(key, value) {
  emit('update', { type: isHover.value ? 'hover' : 'main', key, value });
}
function onMask(value) {
  emit('update', { type: 'main', key: 'mask', value });
}

// ── Ombra ── scala segmented (None · S · M · L · Custom). La chiave salvata resta
// 'none|sm|md|lg|xl|custom': 'xl' è un valore valido che ricade visivamente su "L".
const shadowPreset = computed(() => svGlobal('shadow', 'none'));
const shadowScale = [
  { v: 'none', label: t('None'), aria: t('Nessuna ombra') },
  { v: 'sm', label: 'S', aria: t('Ombra piccola') },
  { v: 'md', label: 'M', aria: t('Ombra media') },
  { v: 'lg', label: 'L', aria: t('Ombra grande') },
  { v: 'custom', label: t('Custom'), aria: t('Ombra personalizzata') },
];
function isShadow(v) {
  const cur = shadowPreset.value;
  if (v === 'lg') return cur === 'lg' || cur === 'xl';
  return cur === v;
}

// ── Trasformazione (Normale: oggetto style.transform; Hover: chiavi piatte transform_*) ──
function tget(prop, def) {
  // Normale + Desktop: oggetto unico style.transform (formato storico). Tutte le altre
  // combinazioni (hover e/o device) usano chiavi piatte transform_<prop>[_<bp>], come il PHP.
  if (!isHover.value && bp.value === 'desktop') {
    const v = (props.tileStyle.transform || {})[prop];
    return v === undefined || v === null ? def : v;
  }
  const src = isHover.value ? (props.tileStyle.hover || {}) : props.tileStyle;
  const v = src[bpKey('transform_' + prop)];
  return v === undefined || v === null || v === '' ? def : v;
}
function tset(prop, value) {
  if (!isHover.value && bp.value === 'desktop') {
    emit('update', { type: 'main', key: 'transform', value: { ...(props.tileStyle.transform || {}), [prop]: value } });
    return;
  }
  emit('update', { type: isHover.value ? 'hover' : 'main', key: bpKey('transform_' + prop), value });
}

// ── Ombra testo (chiavi piatte text_shadow_*, simmetriche normale/hover) ──
function tsGet(prop) {
  return num(sv('text_shadow_' + prop, 0), 0);
}

// ── Maschera (non hoverable) ──
const mask = computed(() => props.tileStyle.mask || 'none');

// ── Indicatore "ha valori hover" ──
const hasAnyHover = computed(() => {
  const h = props.tileStyle.hover || {};
  return Object.keys(h).some((k) => {
    const v = h[k];
    if (v == null || v === '') return false;
    if (typeof v === 'object') return Object.keys(v).length > 0;
    return true;
  });
});

// ── Anteprima effetti (applica lo stato corrente a un chip campione) ──
const previewStyle = computed(() => {
  const tr = [];
  const sc = tget('scale', 1); if (Math.round(sc * 100) !== 100) tr.push(`scale(${sc})`);
  const rot = tget('rotate', 0); if (rot) tr.push(`rotate(${rot}deg)`);
  const tx = tget('translateX', 0); if (tx) tr.push(`translateX(${tx}px)`);
  const ty = tget('translateY', 0); if (ty) tr.push(`translateY(${ty}px)`);
  const sx = tget('skewX', 0); if (sx) tr.push(`skewX(${sx}deg)`);
  const sy = tget('skewY', 0); if (sy) tr.push(`skewY(${sy}deg)`);

  const tsh = tsGet('h'), tsv = tsGet('v'), tsb = tsGet('blur');
  const tscol = String(sv('text_shadow_color', '') || '#000000');
  const textShadow = (tsh || tsv || tsb) ? `${tsh}px ${tsv}px ${tsb}px ${tscol}` : 'none';

  const bdb = num(sv('backdrop_blur', 0), 0);
  const bdbr = num(sv('backdrop_brightness', 100), 100);
  const bds = num(sv('backdrop_saturate', 100), 100);
  const backdrop = (bdb || bdbr !== 100 || bds !== 100) ? `blur(${bdb}px) brightness(${bdbr}%) saturate(${bds}%)` : 'none';

  return {
    opacity: num(sv('opacity', 100), 100) / 100,
    transform: tr.length ? tr.join(' ') : 'none',
    transformOrigin: tget('origin', 'center'),
    textShadow,
    backdropFilter: backdrop,
    WebkitBackdropFilter: backdrop,
  };
});
</script>

<style scoped>
.olo-effstack {
  --olo-ui-accent: #e8622a;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

/* stato Normale / Hover */
.olo-es-state { display: flex; align-items: center; justify-content: flex-end; gap: 10px; }
.olo-es-bp {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--olo-ui-accent);
}
.olo-es-seg {
  display: inline-flex;
  background: #16263d;
  border-radius: 9px;
  padding: 3px;
  gap: 2px;
}
.olo-es-seg button {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  border: none;
  background: transparent;
  color: rgba(255, 255, 255, 0.7);
  font-size: 13px;
  font-weight: 600;
  padding: 5px 14px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-es-seg button.on { background: #fff; color: #1f2937; box-shadow: 0 1px 2px rgba(16, 24, 40, 0.12); }
.olo-es-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--olo-ui-accent); }

/* gruppo */
.olo-es-group { display: flex; flex-direction: column; gap: 10px; }
.olo-es-gtitle {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #1f2937;
}
/* badge per-voce: segnala i gruppi PER-DEVICE (Ombra e Maschera ne sono privi = globali) */
.olo-es-gbp {
  margin-left: 8px;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.04em;
  color: var(--olo-ui-accent);
}

/* riga generica (label + controllo) */
.olo-es-row { display: flex; align-items: center; gap: 12px; }
.olo-es-lab {
  flex: 0 0 64px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #9ca3af;
}

/* slider */
.olo-es-sliderrow { display: flex; align-items: center; gap: 12px; }
.olo-es-slider {
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
.olo-es-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 16px; height: 16px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid var(--olo-ui-accent);
  cursor: pointer;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.18);
}
.olo-es-slider::-moz-range-thumb {
  width: 16px; height: 16px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid var(--olo-ui-accent);
  cursor: pointer;
}
.olo-es-slider:focus-visible { box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ui-accent) 25%, transparent); }
.olo-es-val {
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
.olo-es-val:focus-within {
  border-color: var(--olo-ui-accent);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ui-accent) 18%, transparent);
}
.olo-es-valinput {
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
.olo-es-valinput::-webkit-outer-spin-button,
.olo-es-valinput::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.olo-es-val i { font-style: normal; font-size: 11px; color: #9ca3af; padding-right: 8px; flex-shrink: 0; }

/* input numerico + unità */
.olo-es-duo, .olo-es-trio { display: flex; gap: 10px; }
.olo-es-duo .olo-es-field, .olo-es-trio .olo-es-field { flex: 1; min-width: 0; }
.olo-es-field { display: flex; flex-direction: column; gap: 6px; }
/* In colonna la label NON deve ereditare flex:0 0 64px (= altezza fissa 64px) dalle righe
   orizzontali: altrimenti crea un vuoto verticale enorme sotto SPOSTA/SKEW/OMBRA TESTO. */
.olo-es-field .olo-es-lab { flex: 0 0 auto; }
.olo-es-numwrap {
  display: flex;
  align-items: center;
  height: 38px;
  border: 1px solid #e5e7eb;
  border-radius: 9px;
  background: #fff;
  overflow: hidden;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.olo-es-numwrap:focus-within {
  border-color: var(--olo-ui-accent);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ui-accent) 18%, transparent);
}
.olo-es-num {
  flex: 1; min-width: 0;
  border: none; outline: none; background: transparent;
  padding: 0 10px;
  font: 500 13px 'SF Mono', Monaco, monospace;
  color: #1f2937;
  text-align: center;
}
.olo-es-unit {
  flex-shrink: 0;
  align-self: stretch;
  display: flex; align-items: center;
  padding: 0 10px;
  border-left: 1px solid #eef0f3;
  background: #f6f7f9;
  font-size: 11px;
  font-weight: 600;
  color: #9ca3af;
}

/* select (FieldSelect dropdown custom) */
.olo-es-row .olo-es-selwrap { flex: 1; min-width: 0; }

/* scala segmented ombra (chip = anteprima elevazione, fallback box-shadow inline) */
.olo-es-scale { display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; }
.olo-es-sc {
  appearance: none; border: 1px solid #e5e7eb; background: #fff; border-radius: 9px;
  cursor: pointer; padding: 9px 0 8px; display: flex; flex-direction: column;
  align-items: center; gap: 7px; transition: border-color .15s, background .15s;
}
.olo-es-sc-chip { width: 30px; height: 18px; border-radius: 5px; background: #fff; border: 1px solid #eef0f3; }
.olo-es-sc--none .olo-es-sc-chip { box-shadow: none; }
.olo-es-sc--sm .olo-es-sc-chip { box-shadow: 0 1px 2px rgba(0,0,0,.10); }
.olo-es-sc--md .olo-es-sc-chip { box-shadow: 0 3px 7px rgba(16,24,40,.16); }
.olo-es-sc--lg .olo-es-sc-chip { box-shadow: 0 6px 14px rgba(16,24,40,.20); }
.olo-es-sc--custom .olo-es-sc-chip { box-shadow: 0 3px 9px rgba(232,98,42,.34); border-color: #f3c5a3; }
.olo-es-sc-lab { font-size: 10px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: #94a3b8; }
.olo-es-sc.on { border-color: var(--olo-ui-accent); background: #fdeee2; }
.olo-es-sc.on .olo-es-sc-lab { color: var(--olo-ui-accent); }
.olo-es-sc:focus-visible { outline: 2px solid var(--olo-ui-accent); outline-offset: 2px; }

/* anteprima */
.olo-es-preview {
  margin-top: 2px;
  border: 1px solid #eef0f3;
  border-radius: 12px;
  background: #f9fafb;
  min-height: 96px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 18px;
  overflow: hidden;
}
.olo-es-pv-chip {
  padding: 16px 24px;
  border-radius: 10px;
  background: #fff;
  border: 1px solid #e5e7eb;
  box-shadow: 0 6px 18px rgba(16, 24, 40, 0.1);
  font-size: 13px;
  font-weight: 600;
  color: #6b7280;
  transition: all 0.2s ease;
}
</style>
