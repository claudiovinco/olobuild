<template>
  <!--
    StyleLayoutStack — sezione "Layout" compatta e coerente col resto dell'inspector
    (sorella di StyleBoxStack). NIENTE switch device qui: il breakpoint si cambia dalla
    barra in alto (builderStore.viewMode); i campi dimensionali (responsive) lo seguono e
    mostrano un badge. I campi dimensionali usano FieldDimension (numero + selettore unità).
    Chiavi salvate INVARIATE: full_width, tile_width[_bp], tile_max_width[_bp],
    tile_min_height[_bp], overflow. Per le tile strutturali (section/row/column) le larghezze
    hanno controlli dedicati nel loro config → qui restano solo Altezza minima e Overflow.
  -->
  <div class="olo-laystack">
    <!-- Larghezza piena (toggle + descrizione) -->
    <div v-if="!isStructural" class="olo-ls-fullrow">
      <div class="olo-ls-fulltext">
        <span class="olo-ls-name">{{ t('Larghezza piena') }}</span>
        <span class="olo-ls-desc">{{ t('Occupa tutta la larghezza disponibile') }}</span>
      </div>
      <FieldToggle :modelValue="fullWidth" @update:modelValue="onFull($event)" />
    </div>

    <!-- Larghezza / Larghezza massima (solo non strutturali) -->
    <template v-if="!isStructural">
      <div class="olo-ls-field">
        <span class="olo-ls-lab">
          {{ t('Larghezza') }}
          <span v-if="bp !== 'desktop'" class="olo-ls-bp">{{ bpLabel }}</span>
        </span>
        <FieldDimension :modelValue="dim('tile_width')" :placeholder="t('auto')"
          :aria-label="t('Larghezza')" @update:modelValue="onDim('tile_width', $event)" />
      </div>

      <div class="olo-ls-field">
        <span class="olo-ls-lab">
          {{ t('Larghezza massima') }}
          <span v-if="bp !== 'desktop'" class="olo-ls-bp">{{ bpLabel }}</span>
        </span>
        <FieldDimension :modelValue="dim('tile_max_width')" :placeholder="t('nessuna')"
          :aria-label="t('Larghezza massima')" @update:modelValue="onDim('tile_max_width', $event)" />
      </div>
    </template>

    <!-- Altezza minima (tutti) -->
    <div class="olo-ls-field">
      <span class="olo-ls-lab">
        {{ t('Altezza minima') }}
        <span v-if="bp !== 'desktop'" class="olo-ls-bp">{{ bpLabel }}</span>
      </span>
      <FieldDimension :modelValue="dim('tile_min_height')" :placeholder="t('auto')"
        :units="['px', 'vh', '%', 'em', 'rem']" :aria-label="t('Altezza minima')"
        @update:modelValue="onDim('tile_min_height', $event)" />
    </div>

    <!-- Overflow (tutti) -->
    <div class="olo-ls-field">
      <span class="olo-ls-lab">{{ t('Overflow') }}</span>
      <div class="olo-ls-selwrap">
        <span class="olo-ls-selic" v-html="OVERFLOW_ICON[overflow] || OVERFLOW_ICON.visible"></span>
        <FieldSelect
          ui="dropdown"
          class="olo-ls-sel"
          :model-value="overflow"
          :options="OVERFLOW_OPTIONS"
          @update:model-value="onOverflow($event)"
        />
      </div>
    </div>

    <!-- Anteprima vincoli (solo non strutturali) -->
    <div v-if="!isStructural" class="olo-ls-preview">
      <div class="olo-ls-pv-head">
        <span class="olo-ls-pv-title">{{ t('Anteprima vincoli') }}</span>
        <span v-if="fullWidth" class="olo-ls-pv-badge">{{ t('Larghezza piena') }}</span>
      </div>
      <div class="olo-ls-pv-track">
        <div class="olo-ls-pv-box" :style="pvBoxStyle">
          <span class="olo-ls-pv-label">{{ constraintLabel }}</span>
        </div>
      </div>
      <div class="olo-ls-pv-foot">{{ footLabel }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import FieldToggle from '../fields/FieldToggle.vue';
import FieldDimension from '../fields/FieldDimension.vue';
import FieldSelect from '../fields/FieldSelect.vue';

// Label RAW: FieldSelect applica t() internamente. Value INVARIATI.
const OVERFLOW_OPTIONS = [
  { value: 'visible', label: 'Visibile' },
  { value: 'hidden', label: 'Nascosto' },
  { value: 'auto', label: 'Auto (scroll)' },
  { value: 'clip', label: 'Clip' },
];
import { useBuilderStore } from '@/stores/builder';
import { t } from '@/i18n';

const props = defineProps({
  tileStyle: { type: Object, required: true },
  tileType: { type: String, default: '' },
});
const emit = defineEmits(['update']);

const builderStore = useBuilderStore();

const STRUCTURAL = new Set(['section', 'row', 'column', 'inner-columns']);
const isStructural = computed(() => STRUCTURAL.has(props.tileType));

const OVERFLOW_ICON = {
  visible: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>',
  hidden: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 6.1A10.8 10.8 0 0 1 12 6c6.5 0 10 6 10 6a17.6 17.6 0 0 1-3.36 3.96M6.6 6.6A17.6 17.6 0 0 0 2 12s3.5 6 10 6a10.8 10.8 0 0 0 3.4-.55"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>',
  auto: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>',
  clip: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>',
};

// Breakpoint attivo dal viewport globale (stesso schema di StyleBoxStack).
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

// ── Dimensioni per-breakpoint (chiave base su desktop, _<bp> altrove) ──
function dimKey(base) {
  return bp.value === 'desktop' ? base : `${base}_${bp.value}`;
}
function dim(base) {
  const v = props.tileStyle[dimKey(base)];
  return v === undefined || v === null ? '' : String(v);
}
function onDim(base, val) {
  emit('update', { type: 'main', key: dimKey(base), value: val });
}

// ── full_width / overflow (NON per-breakpoint) ──
const fullWidth = computed(() =>
  props.tileStyle.full_width === true || props.tileStyle.full_width === 'true'
);
function onFull(v) {
  emit('update', { type: 'main', key: 'full_width', value: !!v });
}
const overflow = computed(() => props.tileStyle.overflow || 'visible');
function onOverflow(v) {
  emit('update', { type: 'main', key: 'overflow', value: v });
}

// ── Anteprima vincoli ──
const constraintLabel = computed(() => {
  const maxw = dim('tile_max_width');
  if (maxw) return t('max') + ' ' + maxw;
  const w = dim('tile_width');
  if (w) return w;
  return fullWidth.value ? '100%' : t('auto');
});
const footLabel = computed(() => {
  const h = dim('tile_min_height');
  const center = fullWidth.value || dim('tile_max_width') ? t('contenuto centrato') : t('contenuto a sinistra');
  return center + ' · ' + t('altezza') + ' ' + (h ? '≥ ' + h : t('auto'));
});
// Larghezza visiva del box interno: vincolato (max-width) → 58%, larghezza fissa → ~70%, pieno → 100%.
const pvBoxStyle = computed(() => {
  const maxw = dim('tile_max_width');
  const w = dim('tile_width');
  let width = '100%';
  if (maxw) width = '58%';
  else if (w) width = w.endsWith('%') ? w : '70%';
  return { width };
});
</script>

<style scoped>
.olo-laystack {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* Larghezza piena */
.olo-ls-fullrow {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.olo-ls-fulltext {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}
.olo-ls-name {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}
.olo-ls-desc {
  font-size: 12px;
  color: #9ca3af;
  line-height: 1.35;
}

/* campo dimensionale / overflow */
.olo-ls-field {
  display: flex;
  flex-direction: column;
  gap: 7px;
}
.olo-ls-lab {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}
.olo-ls-bp {
  font-size: 10px;
  font-weight: 700;
  color: var(--olo-ui-accent, #e8622a);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

/* select overflow */
.olo-ls-selwrap {
  position: relative;
  display: flex;
  align-items: center;
  height: 38px;
  border: 1px solid #e5e7eb;
  border-radius: 9px;
  background: #fff;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.olo-ls-selwrap:focus-within {
  border-color: var(--olo-ui-accent, #e8622a);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ui-accent, #e8622a) 18%, transparent);
}
.olo-ls-selic {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding-left: 11px;
  color: #6b7280;
  flex-shrink: 0;
  pointer-events: none;
}
/* FieldSelect "blended" nel wrap (bordo/sfondo li fornisce .olo-ls-selwrap):
   il trigger perde i propri, riempie l'altezza e mantiene font/padding originali */
.olo-ls-selwrap .olo-ls-sel {
  flex: 1;
  min-width: 0;
  height: 100%;
}
.olo-ls-sel :deep(.fsel-trigger) {
  height: 100%;
  border: none;
  border-radius: 0;
  background: transparent;
  padding: 0 11px 0 9px;
  font-size: 14px;
}

/* Anteprima vincoli */
.olo-ls-preview {
  margin-top: 2px;
  border: 1px solid #eef0f3;
  border-radius: 12px;
  background: #f9fafb;
  padding: 14px;
}
.olo-ls-pv-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}
.olo-ls-pv-title {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #9ca3af;
}
.olo-ls-pv-badge {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  color: var(--olo-ui-accent, #e8622a);
  border: 1px solid color-mix(in srgb, var(--olo-ui-accent, #e8622a) 40%, #fff);
  background: color-mix(in srgb, var(--olo-ui-accent, #e8622a) 8%, #fff);
  border-radius: 999px;
  padding: 3px 9px;
}
.olo-ls-pv-track {
  display: flex;
  justify-content: center;
  border-radius: 8px;
  background-image: repeating-linear-gradient(45deg, #eef0f3 0 6px, transparent 6px 12px);
  border: 1px solid #eef0f3;
  padding: 14px 10px;
}
.olo-ls-pv-box {
  min-width: 90px;
  height: 46px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1.5px dashed var(--olo-ui-accent, #e8622a);
  border-radius: 8px;
  background: color-mix(in srgb, var(--olo-ui-accent, #e8622a) 5%, #fff);
  transition: width 0.2s ease;
}
.olo-ls-pv-label {
  font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;
  font-size: 12px;
  font-weight: 500;
  color: var(--olo-ui-accent, #e8622a);
}
.olo-ls-pv-foot {
  margin-top: 10px;
  text-align: center;
  font-size: 11px;
  color: #9ca3af;
}
</style>
