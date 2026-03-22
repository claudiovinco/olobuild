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
      >{{ bp.icon }} {{ bp.label }}</button>
    </div>

    <!-- Inherited badge + reset for non-desktop -->
    <div v-if="activeBp !== 'desktop' && !currentHeight" class="mb-text-[10px] mb-text-gray-500 mb-italic">
      Ereditato dal livello superiore ({{ resolvedLabel }})
    </div>
    <div v-if="activeBp !== 'desktop' && currentHeight" class="mb-flex mb-items-center mb-gap-2">
      <span class="mb-text-[10px] mb-text-yellow-400">Override per {{ activeBp }}</span>
      <button @click="clearBreakpoint" class="mb-text-[10px] mb-text-yellow-500 hover:mb-text-yellow-300">&times; Reset</button>
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
      <span class="mb-text-[10px] mb-text-gray-500 mb-block mb-mb-1">Personalizzato</span>
      <div class="mb-flex mb-items-center mb-gap-2">
        <button
          @click="setHeight({ mode: 'px', value: effectiveHeight.value || 600 })"
          :class="presetClass('px', null)"
        >px</button>
        <template v-if="effectiveHeight.mode === 'px'">
          <input
            type="range"
            :value="effectiveHeight.value"
            @input="setHeight({ mode: 'px', value: parseInt($event.target.value) })"
            min="200" max="1200" step="10"
            class="mb-flex-1"
          />
          <input
            type="number"
            :value="effectiveHeight.value"
            @input="setHeight({ mode: 'px', value: parseInt($event.target.value) || 600 })"
            min="200" max="1200" step="10"
            class="mb-w-16 mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-1 mb-py-0.5 mb-text-[11px] mb-text-gray-900 mb-text-center"
          />
        </template>
        <template v-else-if="effectiveHeight.mode === 'vh'">
          <input
            type="range"
            :value="effectiveHeight.value"
            @input="setHeight({ mode: 'vh', value: parseInt($event.target.value) })"
            min="25" max="100" step="5"
            class="mb-flex-1"
          />
          <span class="mb-text-[11px] mb-text-gray-400">{{ effectiveHeight.value }}vh</span>
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
import { normalizeHeight } from '@/config/elements/proslider.js';

const props = defineProps({
  settings: { type: Object, required: true },
});

const emit = defineEmits(['update']);

const activeBp = ref('desktop');

const breakpointList = [
  { key: 'desktop',  label: 'Desktop',  icon: '🖥' },
  { key: 'notebook', label: 'Notebook', icon: '💻' },
  { key: 'tablet',   label: 'Tablet',   icon: '📱' },
  { key: 'mobile',   label: 'Mobile',   icon: '📲' },
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
