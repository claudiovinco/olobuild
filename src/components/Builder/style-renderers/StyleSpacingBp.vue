<template>
  <div>
    <!-- Breakpoint switcher (locale a questo controllo) -->
    <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
      <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400">
        {{ t(field.label) }}
        <span v-if="bp !== 'desktop'" class="mb-text-amber-400 mb-text-[10px] mb-ml-1">
          {{ bpLabel }}
        </span>
      </label>
      <button
        @click="bpOpen = !bpOpen"
        class="mb-p-0.5 mb-rounded mb-transition-colors"
        :class="bp !== 'desktop' || bpOpen ? 'mb-text-primary-400' : 'mb-text-gray-500 hover:mb-text-gray-300'"
        :title="t('Responsive')"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect width="20" height="14" x="2" y="3" rx="2"/>
          <line x1="8" x2="16" y1="21" y2="21"/>
          <line x1="12" x2="12" y1="17" y2="21"/>
        </svg>
      </button>
    </div>

    <div v-if="bpOpen" class="mb-flex mb-gap-0.5 mb-mb-1.5 mb-bg-gray-700 mb-rounded-lg mb-p-0.5">
      <button
        v-for="b in breakpoints"
        :key="b.key"
        @click="bp = b.key"
        :class="[
          'mb-flex-1 mb-py-0.5 mb-text-[9px] mb-font-medium mb-rounded mb-transition-colors mb-text-center',
          bp === b.key ? 'mb-bg-primary-600 mb-text-white' : 'mb-text-gray-500 hover:mb-text-gray-400'
        ]"
        :title="t(b.label)"
      >{{ b.short }}</button>
    </div>

    <FieldSpacing
      :modelValue="spacingValue"
      :max="field.max ?? 200"
      @update:modelValue="onSpacingUpdate"
    />
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import FieldSpacing from '../fields/FieldSpacing.vue';
import { useBuilderStore } from '@/stores/builder';
import { t } from '@/i18n';

const props = defineProps({
  field: { type: Object, required: true },     // { key: 'margin'|'padding', label, max }
  tileStyle: { type: Object, required: true }, // selectedTile.style
});
const emit = defineEmits(['update']);

const breakpoints = [
  { key: 'desktop',          label: 'Desktop',  short: 'DT' },
  { key: 'tablet_landscape', label: 'Tablet L', short: 'TL' },
  { key: 'tablet',           label: 'Tablet',   short: 'TP' },
  { key: 'mobile_landscape', label: 'Mobile L', short: 'ML' },
  { key: 'mobile',           label: 'Mobile',   short: 'MB' },
];

const bp = ref('desktop');
const bpOpen = ref(false);
const bpLabel = computed(() => breakpoints.find(b => b.key === bp.value)?.label || '');

// Sincronizza con il viewMode globale del builder (toolbar canvas)
const builderStore = useBuilderStore();
watch(() => builderStore.viewMode, (mode) => {
  if (mode === 'desktop' || mode === 'widescreen') {
    bp.value = 'desktop';
  } else {
    bp.value = mode;
    bpOpen.value = true;
  }
}, { immediate: true });

function bpKey(side) {
  const base = `${props.field.key}_${side}`;  // es. "margin_top"
  return bp.value === 'desktop' ? base : `${base}_${bp.value}`;
}

const spacingValue = computed(() => ({
  top:    parseInt(props.tileStyle[bpKey('top')])    || 0,
  right:  parseInt(props.tileStyle[bpKey('right')])  || 0,
  bottom: parseInt(props.tileStyle[bpKey('bottom')]) || 0,
  left:   parseInt(props.tileStyle[bpKey('left')])   || 0,
}));

function onSpacingUpdate(val) {
  emit('update', {
    type: 'multi',
    updates: [
      { key: bpKey('top'),    value: val.top    },
      { key: bpKey('right'),  value: val.right  },
      { key: bpKey('bottom'), value: val.bottom },
      { key: bpKey('left'),   value: val.left   },
    ],
  });
}
</script>
