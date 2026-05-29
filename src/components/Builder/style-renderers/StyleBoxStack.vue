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
    <!-- Switch device condiviso -->
    <div class="olo-bs-devbar">
      <span class="olo-bs-devlabel">{{ t('Modifica per') }}</span>
      <DeviceSwitch />
    </div>

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
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue';
import FieldBox from '../fields/FieldBox.vue';
import DeviceSwitch from '../DeviceSwitch.vue';
import { useBuilderStore } from '@/stores/builder';
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
};
const EYE = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>';
const EYE_OFF = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 6.1A10.8 10.8 0 0 1 12 6c6.5 0 10 6 10 6a17.6 17.6 0 0 1-3.36 3.96M6.6 6.6A17.6 17.6 0 0 0 2 12s3.5 6 10 6a10.8 10.8 0 0 0 3.4-.55"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';

// Stato peek (anteprima) per-controllo — neutro, non altera i valori.
const peek = reactive({ margin: false, padding: false, radius: false });

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
</script>

<style scoped>
.olo-boxstack {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

/* switch device */
.olo-bs-devbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.olo-bs-devlabel {
  font-size: 11px;
  font-weight: 500;
  color: #9ca3af;
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
  color: #e5e7eb;
}
.olo-bs-bp {
  font-size: 10px;
  font-weight: 600;
  color: #fbbf24;
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
  border: 1px solid rgba(255, 255, 255, 0.12);
  background: transparent;
  color: #9ca3af;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.15s;
  flex-shrink: 0;
}
.olo-bs-eye:hover {
  color: #e5e7eb;
  border-color: rgba(255, 255, 255, 0.25);
}
.olo-bs-eye.on {
  color: var(--olo-color-primary, #6366f1);
  border-color: var(--olo-color-primary, #6366f1);
}

/* toggle Normale / Hover */
.olo-bs-seg {
  display: flex;
  background: #374151;
  border-radius: 7px;
  padding: 2px;
}
.olo-bs-seg button {
  display: flex;
  align-items: center;
  gap: 4px;
  border: none;
  background: transparent;
  color: #9ca3af;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-bs-seg button.on { background: #fff; color: #1f2937; }
.olo-bs-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: var(--olo-color-primary, #e8622a);
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
</style>
