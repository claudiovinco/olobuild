<template>
  <div>
    <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
      <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400">
        {{ field.label }}
      </label>
      <button
        @click="open = !open"
        class="mb-text-gray-500 hover:mb-text-gray-300 mb-transition-colors mb-p-0.5 mb-rounded"
        :class="{ 'mb-text-primary-400': activeBp !== 'desktop' || open }"
        :title="t('Responsive')"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
      </button>
    </div>
    <!-- Breakpoint pills -->
    <div v-if="open" class="mb-flex mb-gap-0.5 mb-mb-1.5 mb-bg-gray-700 mb-rounded-lg mb-p-0.5">
      <button
        v-for="bp in breakpoints"
        :key="bp.key"
        @click="activeBp = bp.key"
        :class="[
          'mb-flex-1 mb-py-0.5 mb-text-[9px] mb-font-medium mb-rounded mb-transition-colors mb-text-center',
          activeBp === bp.key
            ? 'mb-bg-primary-600 mb-text-white'
            : hasValue(bp.key)
              ? 'mb-text-primary-300 hover:mb-text-primary-200'
              : 'mb-text-gray-500 hover:mb-text-gray-400'
        ]"
        :title="bp.label"
      >{{ bp.short }}</button>
    </div>
    <!-- Breakpoint badge (when closed, non-desktop) -->
    <div v-if="!open && activeBp !== 'desktop'" class="mb-mb-1">
      <span class="mb-text-[9px] mb-bg-primary-700 mb-text-primary-200 mb-px-1.5 mb-py-0.5 mb-rounded mb-font-medium">
        {{ breakpoints.find(b => b.key === activeBp)?.label }}
      </span>
    </div>
    <slot :activeKey="activeKey" :activeBp="activeBp" :placeholder="placeholder" />
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed } from 'vue';

const props = defineProps({
  field: { type: Object, required: true },
  settings: { type: Object, default: () => ({}) },
  style: { type: Object, default: () => ({}) },
  target: { type: String, default: 'settings' },
});

const allBreakpoints = [
  { key: 'desktop', label: 'Desktop', short: 'DT' },
  { key: 'tablet_landscape', label: 'Tablet L', short: 'TL' },
  { key: 'tablet', label: 'Tablet', short: 'TP' },
  { key: 'mobile_landscape', label: 'Mobile L', short: 'ML' },
  { key: 'mobile', label: 'Mobile', short: 'MB' },
];

const bpEnabled = (window.oloData || {}).breakpointsEnabled || {};
const breakpoints = allBreakpoints.filter(bp =>
  bp.key === 'desktop' || bpEnabled[bp.key] !== false
);

const open = ref(false);
const activeBp = ref('desktop');

const activeKey = computed(() => {
  if (activeBp.value === 'desktop') return props.field.key;
  return props.field.key + '_' + activeBp.value;
});

const placeholder = computed(() => {
  if (activeBp.value === 'desktop') return '';
  return 'Eredita';
});

function hasValue(bpKey) {
  if (bpKey === 'desktop') return true;
  const k = props.field.key + '_' + bpKey;
  const store = props.target === 'style' ? props.style : props.settings;
  const v = store[k];
  return v !== undefined && v !== '' && v !== null;
}
</script>
