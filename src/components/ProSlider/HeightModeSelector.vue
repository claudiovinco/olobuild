<template>
  <div class="mb-space-y-2">
    <!-- Breakpoint tabs -->
    <div class="mb-flex mb-gap-1">
      <button
        v-for="bp in breakpointList"
        :key="bp.key"
        @click="activeBp = bp.key"
        :class="[
          'mb-px-2 mb-py-0.5 mb-rounded mb-text-[10px] mb-transition-colors',
          activeBp === bp.key
            ? 'mb-bg-primary-600 mb-text-white'
            : 'mb-bg-gray-700 mb-text-gray-400 hover:mb-bg-gray-600'
        ]"
      ><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:inline-block;vertical-align:-1px;margin-right:3px"><path :d="bp.icon" /></svg>{{ bp.label }}</button>
    </div>

    <!-- Inherited badge + reset for non-desktop -->
    <div v-if="activeBp !== 'desktop' && !currentHeight" class="mb-text-[10px] mb-text-gray-500 mb-italic">
      {{ t('Ereditato dal livello superiore') }} ({{ resolvedLabel }})
    </div>
    <div v-if="activeBp !== 'desktop' && currentHeight" class="mb-flex mb-items-center mb-gap-2">
      <span class="mb-text-[10px] mb-text-yellow-400">{{ t('Override per') }} {{ activeBp }}</span>
      <button @click="clearBreakpoint" class="mb-text-[10px] mb-text-yellow-500 hover:mb-text-yellow-300">&times; {{ t('Reset') }}</button>
    </div>

    <!-- Preset buttons: viewport -->
    <div>
      <span class="mb-text-[10px] mb-text-gray-500 mb-block mb-mb-1">Viewport</span>
      <div class="mb-flex mb-flex-wrap mb-gap-1">
        <button
          v-for="p in vhPresets"
          :key="p.value"
          @click="setHeight({ mode: 'vh', value: p.value })"
          :class="presetClass('vh', p.value)"
        >{{ p.label }}</button>
      </div>
    </div>

    <!-- Preset buttons: aspect ratio -->
    <div>
      <span class="mb-text-[10px] mb-text-gray-500 mb-block mb-mb-1">Aspect Ratio</span>
      <div class="mb-flex mb-flex-wrap mb-gap-1">
        <button
          v-for="p in ratioPresets"
          :key="p.value"
          @click="setHeight({ mode: 'ratio', value: p.value })"
          :class="presetClass('ratio', p.value)"
        >{{ p.label }}</button>
      </div>
    </div>

    <!-- Custom px -->
    <div>
      <span class="mb-text-[10px] mb-text-gray-500 mb-block mb-mb-1">{{ t('Personalizzato') }}</span>
      <div class="mb-flex mb-items-center mb-gap-2">
        <button
          @click="setHeight({ mode: 'px', value: effectiveHeight.value || 600 })"
          :class="presetClass('px', null)"
        >px</button>
        <template v-if="effectiveHeight.mode === 'px'">
          <NumberScrubber
            theme="dark"
            class="mb-flex-1"
            :modelValue="effectiveHeight.value"
            :min="200"
            :max="1200"
            :step="10"
            :defaultValue="600"
            emitAs="number"
            unit="px"
            :sliderOnFocus="false"
            ariaLabel="Altezza personalizzata (px)"
            @update:modelValue="setHeight({ mode: 'px', value: $event || 600 })"
          />
        </template>
        <template v-else-if="effectiveHeight.mode === 'vh'">
          <NumberScrubber
            theme="dark"
            class="mb-flex-1"
            :modelValue="effectiveHeight.value"
            :min="25"
            :max="100"
            :step="5"
            :defaultValue="100"
            emitAs="number"
            unit="vh"
            :sliderOnFocus="false"
            ariaLabel="Altezza viewport (vh)"
            @update:modelValue="setHeight({ mode: 'vh', value: $event })"
          />
        </template>
        <template v-else-if="effectiveHeight.mode === 'ratio'">
          <span class="mb-text-[11px] mb-text-gray-400">{{ effectiveHeight.value }}</span>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { t } from '@/i18n';
import { normalizeHeight } from '@/config/elements/proslider.js';
import NumberScrubber from '../Builder/fields/NumberScrubber.vue';

const props = defineProps({
  settings: { type: Object, required: true },
});

const emit = defineEmits(['update']);

const activeBp = ref('desktop');

const breakpointList = [
  // Icone SVG (tracciati Lucide), mai emoji: le stesse dei formati in ProSliderEditor.
  { key: 'desktop',  label: 'Desktop',  icon: 'M4 3h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zM8 21h8M12 17v4' },
  { key: 'notebook', label: 'Notebook', icon: 'M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16' },
  { key: 'tablet',   label: 'Tablet',   icon: 'M6 2h12a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zM12 18h.01' },
  { key: 'mobile',   label: 'Mobile',   icon: 'M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zM12 18h.01' },
];

const vhPresets = [
  { label: 'Full Screen', value: 100 },
  { label: '3/4', value: 75 },
  { label: '2/3', value: 66 },
  { label: '1/2', value: 50 },
];

const ratioPresets = [
  { label: '21:9', value: '21:9' },
  { label: '16:9', value: '16:9' },
  { label: '4:3', value: '4:3' },
  { label: '1:1', value: '1:1' },
];

const heightKey = computed(() => {
  if (activeBp.value === 'desktop') return 'height';
  return 'height' + activeBp.value.charAt(0).toUpperCase() + activeBp.value.slice(1);
});

const currentHeight = computed(() => {
  const raw = props.settings[heightKey.value];
  if (raw === null || raw === undefined) return null;
  return normalizeHeight(raw);
});

// Risolve l'altezza effettiva risalendo la chain dei breakpoint
const effectiveHeight = computed(() => {
  if (currentHeight.value) return currentHeight.value;
  // Risali la chain
  const chain = ['desktop', 'notebook', 'tablet', 'mobile'];
  const idx = chain.indexOf(activeBp.value);
  for (let i = idx - 1; i >= 0; i--) {
    const key = i === 0 ? 'height' : 'height' + chain[i].charAt(0).toUpperCase() + chain[i].slice(1);
    const raw = props.settings[key];
    if (raw !== null && raw !== undefined) {
      const nh = normalizeHeight(raw);
      if (nh) return nh;
    }
  }
  return { mode: 'px', value: 600 };
});

const resolvedLabel = computed(() => {
  const h = effectiveHeight.value;
  if (h.mode === 'vh') return h.value + 'vh';
  if (h.mode === 'ratio') return h.value;
  return h.value + 'px';
});

function setHeight(h) {
  emit('update', heightKey.value, h);
}

function clearBreakpoint() {
  emit('update', heightKey.value, null);
}

function presetClass(mode, value) {
  const h = effectiveHeight.value;
  const active = h.mode === mode && (value === null || h.value === value || (mode === 'px' && value === null));
  return [
    'mb-px-2 mb-py-1 mb-text-[10px] mb-rounded mb-transition-colors mb-border mb-font-medium',
    active
      ? 'mb-bg-primary-600 mb-text-white mb-border-primary-600'
      : 'mb-bg-white mb-text-gray-800 mb-border-gray-300 hover:mb-bg-gray-100 hover:mb-border-gray-400'
  ];
}
</script>
