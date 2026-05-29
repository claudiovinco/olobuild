<template>
  <!--
    StyleBoxStack — pannello unico "Spazi & Bordi" (design handoff olobuild_boxcontrol,
    composizione "StackedPanel"). Impila controlli box-model compatti (FieldBox) con UNO
    switch device condiviso in cima, sincronizzato col viewport globale (builderStore.viewMode).
    Margine/Padding sono per-breakpoint (chiavi margin_top[_bp]…, già supportate dal renderer);
    il Raggio ha il toggle Normale/Hover. Nessun cambio di formato salvato.
    Il Bordo ricco (stile/colore/effetti/hover) resta nel suo controllo dedicato.
  -->
  <div class="olo-boxstack">
    <!-- Switch device condiviso -->
    <div class="olo-bs-devbar">
      <span class="olo-bs-devlabel">{{ t('Modifica per') }}</span>
      <div class="olo-bs-devices" role="group" :aria-label="t('Dispositivo')">
        <button
          v-for="d in devices"
          :key="d.key"
          type="button"
          :class="{ on: activeDevice === d.key }"
          @click="setDevice(d.key)"
          :title="t(d.label)"
          :aria-pressed="activeDevice === d.key"
          v-html="d.icon"
        ></button>
      </div>
    </div>

    <!-- Margine -->
    <div class="olo-bs-field">
      <div class="olo-bs-head">
        <span class="olo-bs-ic" v-html="ICONS.margin"></span>
        <span class="olo-bs-name">{{ t('Margine') }}</span>
        <span v-if="bp !== 'desktop'" class="olo-bs-bp">{{ bpLabel }}</span>
      </div>
      <FieldBox mode="sides" :sliderMax="200" :modelValue="spacingModel('margin')" @update:modelValue="onSpacing('margin', $event)" />
    </div>

    <!-- Padding -->
    <div class="olo-bs-field">
      <div class="olo-bs-head">
        <span class="olo-bs-ic" v-html="ICONS.padding"></span>
        <span class="olo-bs-name">{{ t('Padding') }}</span>
        <span v-if="bp !== 'desktop'" class="olo-bs-bp">{{ bpLabel }}</span>
      </div>
      <FieldBox mode="sides" :sliderMax="200" :modelValue="spacingModel('padding')" @update:modelValue="onSpacing('padding', $event)" />
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
      </div>
      <FieldBox mode="corners" :sliderMax="100" :modelValue="radiusModel" @update:modelValue="onRadius($event)" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import FieldBox from '../fields/FieldBox.vue';
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

const devices = [
  { key: 'desktop', label: 'Desktop', icon: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="13" rx="2"/><path d="M8 21h8M12 17v4"/></svg>' },
  { key: 'tablet', label: 'Tablet', icon: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M11 18h2"/></svg>' },
  { key: 'mobile', label: 'Mobile', icon: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg>' },
];

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

// Lo switch a 3 icone riflette/guida il viewport globale.
const activeDevice = computed(() => {
  const m = builderStore.viewMode;
  if (m === 'desktop' || m === 'widescreen') return 'desktop';
  if (m === 'tablet' || m === 'tablet_landscape') return 'tablet';
  return 'mobile';
});
function setDevice(key) {
  builderStore.setViewMode(key);
}

// ── Margine / Padding (per-breakpoint, 4 lati su chiavi piatte) ──
function sKey(prefix, side) {
  const base = `${prefix}_${side}`;
  return bp.value === 'desktop' ? base : `${base}_${bp.value}`;
}
function spacingModel(prefix) {
  const top = parseInt(props.tileStyle[sKey(prefix, 'top')]) || 0;
  const right = parseInt(props.tileStyle[sKey(prefix, 'right')]) || 0;
  const bottom = parseInt(props.tileStyle[sKey(prefix, 'bottom')]) || 0;
  const left = parseInt(props.tileStyle[sKey(prefix, 'left')]) || 0;
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
.olo-bs-devices {
  display: flex;
  gap: 2px;
  background: #374151;
  border-radius: 8px;
  padding: 2px;
}
.olo-bs-devices button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 26px;
  border: none;
  background: transparent;
  color: #9ca3af;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.15s;
}
.olo-bs-devices button:hover {
  color: #e5e7eb;
}
.olo-bs-devices button.on {
  background: var(--olo-color-primary, #6366f1);
  color: #fff;
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
.olo-bs-spacer {
  flex: 1;
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
.olo-bs-seg button.on {
  background: #fff;
  color: #1f2937;
}
.olo-bs-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: var(--olo-color-primary, #e8622a);
}
</style>
