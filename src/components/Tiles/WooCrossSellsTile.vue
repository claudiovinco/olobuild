<template>
  <div style="padding:10px;background:var(--olo-color-surface-alt, #f6f7f9);border-radius:8px;min-height:60px;">
    <div style="font-size:13px;font-weight:700;color:var(--olo-color-text, #1f2937);margin-bottom:10px;">{{ t('Cross-sell') }}</div>
    <div :style="gridStyle">
      <div
        v-for="i in cols"
        :key="i"
        style="background:var(--olo-color-surface, #ffffff);border-radius:6px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.08);"
      >
        <div style="width:100%;padding-top:75%;background:var(--olo-color-surface-alt, #f6f7f9);position:relative;">
          <svg style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:24px;height:24px;opacity:0.25;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <path d="M21 15l-5-5L5 21"/>
          </svg>
        </div>
        <div style="padding:8px;">
          <div style="font-size:12px;font-weight:600;color:var(--olo-color-text, #1f2937);margin-bottom:4px;">Prodotto {{ i }}</div>
          <div style="font-size:13px;font-weight:700;color:var(--olo-color-text, #374151);">€ 29,00</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  columns: '3',
  gap: '16',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const cols = computed(() => Math.max(1, Math.min(6, parseInt(s.value.columns) || 3)));

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gap: (parseInt(s.value.gap) || 16) + 'px',
}));
</script>
