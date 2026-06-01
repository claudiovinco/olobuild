<template>
  <!--
    StyleBoxStack — pannello unico "Spazi & Bordi" (design handoff olobuild_boxcontrol,
    composizione "StackedPanel"). Impila controlli box-model compatti (FieldBox) con UNO
    switch device condiviso in cima, sincronizzato col viewport globale (builderStore.viewMode).
    Ogni controllo ha un "occhio" (peek) che mostra un'anteprima live, neutra (non altera i valori).
    Margine/Padding sono per-breakpoint (chiavi margin_top[_bp]…, già supportate dal renderer);
    il Raggio ha il toggle Normale/Hover. Nessun cambio di formato salvato.
  -->
  <div class="olo-boxstack">
    <!-- Nessuno switch device qui: il breakpoint si cambia dalla barra in alto
         (builderStore.viewMode); i controlli lo seguono e il badge accanto a ogni
         voce indica il device attivo. Niente duplicati. -->

    <!-- Margine -->
    <div class="olo-bs-field">
      <div class="olo-bs-head">
        <span class="olo-bs-ic" v-html="ICONS.margin"></span>
        <span class="olo-bs-name">{{ t('Margine') }}</span>
        <span v-if="bp !== 'desktop'" class="olo-bs-bp">{{ bpLabel }}</span>
        <span class="olo-bs-spacer"></span>
        <button type="button" class="olo-bs-eye" :class="{ on: peek.margin }" @click="peek.margin = !peek.margin"
          :title="peek.margin ? t('Nascondi anteprima') : t('Mostra anteprima')" :aria-pressed="peek.margin"
          v-html="peek.margin ? EYE : EYE_OFF"></button>
      </div>
      <FieldBox mode="sides" preview="none" :sliderMax="200" :modelValue="spacingModel('margin')" @update:modelValue="onSpacing('margin', $event)" />
      <div v-if="peek.margin" class="olo-bs-pv">
        <div class="olo-bs-pv-box"><div class="olo-bs-pv-inner" :style="spacingPreviewStyle('margin')"></div></div>
      </div>
    </div>

    <!-- Padding -->
    <div class="olo-bs-field">
      <div class="olo-bs-head">
        <span class="olo-bs-ic" v-html="ICONS.padding"></span>
        <span class="olo-bs-name">{{ t('Padding') }}</span>
        <span v-if="bp !== 'desktop'" class="olo-bs-bp">{{ bpLabel }}</span>
        <span class="olo-bs-spacer"></span>
        <button type="button" class="olo-bs-eye" :class="{ on: peek.padding }" @click="peek.padding = !peek.padding"
          :title="peek.padding ? t('Nascondi anteprima') : t('Mostra anteprima')" :aria-pressed="peek.padding"
          v-html="peek.padding ? EYE : EYE_OFF"></button>
      </div>
      <FieldBox mode="sides" preview="none" :sliderMax="200" :modelValue="spacingModel('padding')" @update:modelValue="onSpacing('padding', $event)" />
      <div v-if="peek.padding" class="olo-bs-pv">
        <div class="olo-bs-pv-box"><div class="olo-bs-pv-inner" :style="spacingPreviewStyle('padding')"></div></div>
      </div>
    </div>

    <!-- Raggio (Normale / Hover) -->
    <div class="olo-bs-field">
      <div class="olo-bs-head">
        <span class="olo-bs-ic" v-html="ICONS.radius"></span>
        <span class="olo-bs-name">{{ t('Raggio') }}</span>
        <span class="olo-bs-spacer"></span>
        <div class="olo-bs-seg">
          <button type="button" :class="{ on: radiusState === 'normal' }" @click="radiusState = 'normal'">{{ t('Normale') }}</button>
          <button type="button" :class="{ on: radiusState === 'hover' }" @click="radiusState = 'hover'">
            {{ t('Hover') }}<span v-if="radiusHoverDiffers" class="olo-bs-dot"></span>
          </button>
        </div>
        <button type="button" class="olo-bs-eye" :class="{ on: peek.radius }" @click="peek.radius = !peek.radius"
          :title="peek.radius ? t('Nascondi anteprima') : t('Mostra anteprima')" :aria-pressed="peek.radius"
          v-html="peek.radius ? EYE : EYE_OFF"></button>
      </div>
      <FieldBox mode="corners" preview="none" :sliderMax="100" :modelValue="radiusModel" @update:modelValue="onRadius($event)" />
      <div v-if="peek.radius" class="olo-bs-pv">
        <div class="olo-bs-pv-chip" :style="{ borderRadius: radiusPreviewCss }"></div>
      </div>
    </div>

    <!-- Bordo (Normale per-device / Hover globale) -->
    <div class="olo-bs-field">
      <div class="olo-bs-head">
        <span class="olo-bs-ic" v-html="ICONS.border"></span>
        <span class="olo-bs-name">{{ t('Bordo') }}</span>
        <span v-if="borderState === 'normal' && bp !== 'desktop'" class="olo-bs-bp">{{ bpLabel }}</span>
        <span class="olo-bs-spacer"></span>
        <div class="olo-bs-seg">
          <button type="button" :class="{ on: borderState === 'normal' }" @click="borderState = 'normal'">{{ t('Normale') }}</button>
          <button type="button" :class="{ on: borderState === 'hover' }" @click="borderState = 'hover'">
            {{ t('Hover') }}<span v-if="borderHoverDiffers" class="olo-bs-dot"></span>
          </button>
        </div>
        <button type="button" class="olo-bs-eye" :class="{ on: peek.border }" @click="peek.border = !peek.border"
          :title="peek.border ? t('Nascondi anteprima') : t('Mostra anteprima')" :aria-pressed="peek.border"
          v-html="peek.border ? EYE : EYE_OFF"></button>
      </div>
      <FieldBorder :show-peek="false" :modelValue="borderModel" @update:modelValue="onBorder($event)" />
      <div v-if="peek.border" class="olo-bs-pv">
        <div class="olo-bs-pv-borderbox" :style="borderPreviewStyle"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue';
import FieldBox from '../fields/FieldBox.vue';
import FieldBorder from '../fields/FieldBorder.vue';
import { useBuilderStore } from '@/stores/builder';
import { borderDefault, borderHoverDefault } from '@/config/elements/_shared.js';
import { t } from '@/i18n';

const props = defineProps({
  tileStyle: { type: Object, required: true },
});
const emit = defineEmits(['update']);

const builderStore = useBuilderStore();

const ICONS = {
  margin: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="1"/><rect x="8" y="8" width="8" height="8" rx="1" opacity=".5"/></svg>',
  padding: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="1" opacity=".5"/><rect x="7" y="7" width="10" height="10" rx="1"/></svg>',
  radius: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 21v-6a8 8 0 0 0-8-8H3"/></svg>',
  border: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>',
};
const EYE = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>';
const EYE_OFF = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 6.1A10.8 10.8 0 0 1 12 6c6.5 0 10 6 10 6a17.6 17.6 0 0 1-3.36 3.96M6.6 6.6A17.6 17.6 0 0 0 2 12s3.5 6 10 6a10.8 10.8 0 0 0 3.4-.55"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';

// Stato peek (anteprima) per-controllo — neutro, non altera i valori.
const peek = reactive({ margin: false, padding: false, radius: false, border: false });

// Breakpoint attivo derivato dal viewport globale (stesso schema di StyleSpacingBp).
const breakpoints = {
  desktop: 'Desktop', widescreen: 'Desktop',
  tablet_landscape: 'Tablet L', tablet: 'Tablet',
  mobile_landscape: 'Mobile L', mobile: 'Mobile',
};
const bp = ref('desktop');
watch(() => builderStore.viewMode, (mode) => {
  bp.value = (mode === 'desktop' || mode === 'widescreen') ? 'desktop' : mode;
}, { immediate: true });
const bpLabel = computed(() => t(breakpoints[bp.value] || ''));

// ── Margine / Padding (per-breakpoint, 4 lati su chiavi piatte) ──
function sKey(prefix, side) {
  const base = `${prefix}_${side}`;
  return bp.value === 'desktop' ? base : `${base}_${bp.value}`;
}
function spacingSides(prefix) {
  return {
    top: parseInt(props.tileStyle[sKey(prefix, 'top')]) || 0,
    right: parseInt(props.tileStyle[sKey(prefix, 'right')]) || 0,
    bottom: parseInt(props.tileStyle[sKey(prefix, 'bottom')]) || 0,
    left: parseInt(props.tileStyle[sKey(prefix, 'left')]) || 0,
  };
}
function spacingModel(prefix) {
  const { top, right, bottom, left } = spacingSides(prefix);
  // Valori uniformi → numero (FieldBox parte "collegato", riga compatta)
  if (top === right && right === bottom && bottom === left) return top;
  return { top, right, bottom, left };
}
function onSpacing(prefix, val) {
  let sides;
  if (val && typeof val === 'object') {
    sides = {
      top: parseInt(val.top) || 0,
      right: parseInt(val.right) || 0,
      bottom: parseInt(val.bottom) || 0,
      left: parseInt(val.left) || 0,
    };
  } else {
    const n = parseInt(val) || 0;
    sides = { top: n, right: n, bottom: n, left: n };
  }
  emit('update', {
    type: 'multi',
    updates: [
      { key: sKey(prefix, 'top'), value: sides.top },
      { key: sKey(prefix, 'right'), value: sides.right },
      { key: sKey(prefix, 'bottom'), value: sides.bottom },
      { key: sKey(prefix, 'left'), value: sides.left },
    ],
  });
}
// Anteprima spaziatura: gap interno proporzionale ai 4 valori (clampati per restare nel box).
function spacingPreviewStyle(prefix) {
  const s = spacingSides(prefix);
  const c = (v, max) => Math.min(Math.max(v, 0), max);
  return { margin: `${c(s.top, 22)}px ${c(s.right, 44)}px ${c(s.bottom, 22)}px ${c(s.left, 44)}px` };
}

// ── Raggio (Normale / Hover) ──
const radiusState = ref('normal');
const radiusModel = computed(() =>
  radiusState.value === 'hover'
    ? (props.tileStyle.hover?.border_radius ?? 0)
    : (props.tileStyle.border_radius ?? 0),
);
const radiusHoverDiffers = computed(() => {
  const h = props.tileStyle.hover?.border_radius;
  if (h === undefined || h === null) return false;
  return JSON.stringify(h) !== JSON.stringify(props.tileStyle.border_radius ?? 0);
});
const radiusPreviewCss = computed(() => {
  const v = radiusModel.value;
  if (v && typeof v === 'object') {
    return `${parseInt(v.tl) || 0}px ${parseInt(v.tr) || 0}px ${parseInt(v.br) || 0}px ${parseInt(v.bl) || 0}px`;
  }
  return `${parseInt(v) || 0}px`;
});
function onRadius(val) {
  emit('update', {
    type: radiusState.value === 'hover' ? 'hover' : 'main',
    key: 'border_radius',
    value: val,
  });
}

// ── Bordo ──
// Normale: PER-DEVICE come margine/padding → chiave `border` (desktop) o `border_<bp>`,
//          pilotata dallo switch device condiviso. Il PHP rende il desktop inline e i
//          breakpoint via media query (collect_responsive_css).
// Hover:   GLOBALE (non per-device) → chiave piatta `border_hover`, letta da collect_hover_css.
// Entrambi via {type:'main'} perché il dispatcher scrive su tile.style[key] (anche border_hover).
const borderState = ref('normal');
function borderKey() {
  return bp.value === 'desktop' ? 'border' : `border_${bp.value}`;
}
const borderModel = computed(() =>
  borderState.value === 'hover'
    ? (props.tileStyle.border_hover ?? { ...borderHoverDefault })
    : (props.tileStyle[borderKey()] ?? { ...borderDefault })
);
const borderHoverDiffers = computed(() => {
  const h = props.tileStyle.border_hover;
  if (!h || typeof h !== 'object') return false;
  return !!(String(h.color || '').trim() || String(h.style || '').trim()
    || parseInt(h.top) || parseInt(h.right) || parseInt(h.bottom) || parseInt(h.left));
});
function onBorder(val) {
  emit('update', {
    type: 'main',
    key: borderState.value === 'hover' ? 'border_hover' : borderKey(),
    value: val,
  });
}
const borderPreviewStyle = computed(() => {
  const b = (borderState.value === 'hover'
    ? props.tileStyle.border_hover
    : props.tileStyle[borderKey()]) || {};
  return {
    borderStyle: b.style || 'solid',
    borderColor: b.color || 'transparent',
    borderTopWidth: `${Math.max(0, parseInt(b.top) || 0)}px`,
    borderRightWidth: `${Math.max(0, parseInt(b.right) || 0)}px`,
    borderBottomWidth: `${Math.max(0, parseInt(b.bottom) || 0)}px`,
    borderLeftWidth: `${Math.max(0, parseInt(b.left) || 0)}px`,
  };
});
</script>

<style scoped>
.olo-boxstack {
  /* Accento CHROME del builder (arancio fisso) condiviso da TUTTI i controlli del
     pannello: i FieldBox (margine/padding/raggio) lo ereditano via --olo-bf-accent,
     il FieldBorder via --olo-ui-accent → coerenza cromatica dell'intero blocco. */
  --olo-ui-accent: #e8622a;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

/* singolo controllo */
.olo-bs-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.olo-bs-head {
  display: flex;
  align-items: center;
  gap: 8px;
}
.olo-bs-ic {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
}
.olo-bs-name {
  font-size: 13px;
  font-weight: 600;
  color: #1f2937;
}
.olo-bs-bp {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.04em;
  color: var(--olo-ui-accent, #e8622a);
  margin-left: 2px;
}
.olo-bs-spacer { flex: 1; }

/* occhio anteprima (peek) */
.olo-bs-eye {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 28px;
  border: 1px solid #e5e7eb;
  background: #fff;
  color: #9ca3af;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.15s;
  flex-shrink: 0;
}
.olo-bs-eye:hover {
  color: #6b7280;
  border-color: #d1d5db;
}
.olo-bs-eye.on {
  color: var(--olo-ui-accent, #e8622a);
  border-color: var(--olo-ui-accent, #e8622a);
  background: color-mix(in srgb, var(--olo-ui-accent) 12%, transparent);
}
.olo-bs-eye:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: 1px;
}

/* toggle Normale / Hover — STESSA grafica di StyleEffectsStack/.olo-es-seg
   (navy + pill bianca): un'unica lingua per tutti i toggle del builder. */
.olo-bs-seg {
  display: inline-flex;
  background: #16263d;
  border-radius: 9px;
  padding: 3px;
  gap: 2px;
}
.olo-bs-seg button {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  border: none;
  background: transparent;
  color: rgba(255, 255, 255, 0.7);
  font-size: 12px;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-bs-seg button.on { background: #fff; color: #1f2937; box-shadow: 0 1px 2px rgba(16, 24, 40, 0.12); }
.olo-bs-seg button:focus-visible { outline: 2px solid var(--olo-ui-accent, #e8622a); outline-offset: 1px; }
.olo-bs-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: var(--olo-ui-accent, #e8622a);
}

/* anteprima live (peek) */
.olo-bs-pv {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 14px;
  border-radius: 8px;
  background-color: #f3f4f6;
  background-image:
    linear-gradient(45deg, #e2e5ea 25%, transparent 25%),
    linear-gradient(-45deg, #e2e5ea 25%, transparent 25%),
    linear-gradient(45deg, transparent 75%, #e2e5ea 75%),
    linear-gradient(-45deg, transparent 75%, #e2e5ea 75%);
  background-size: 14px 14px;
  background-position: 0 0, 0 7px, 7px -7px, -7px 0;
}
.olo-bs-pv-box {
  width: 100%;
  max-width: 260px;
  border: 1.5px dashed #9ca3af;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.65);
}
.olo-bs-pv-inner {
  height: 30px;
  background: var(--olo-color-primary, #6366f1);
  border-radius: 7px;
}
.olo-bs-pv-chip {
  width: 66px;
  height: 46px;
  background: var(--olo-color-primary, #6366f1);
  transition: border-radius 0.2s ease;
}
/* anteprima bordo: box neutro con il bordo reale applicato (width/style/color inline) */
.olo-bs-pv-borderbox {
  width: 100%;
  max-width: 220px;
  height: 46px;
  background: rgba(255, 255, 255, 0.65);
  border-radius: 8px;
  border-style: solid;
  border-color: transparent;
  border-width: 0;
  box-sizing: border-box;
  transition: border 0.15s ease;
}
</style>
