<template>
  <div>
    <!-- Linked mode: single input -->
    <div v-if="linked" class="mb-flex mb-items-center mb-gap-2">
      <input
        type="range"
        :value="uniformValue"
        @input="onUniformInput($event.target.value)"
        @dblclick="onReset"
        :min="min" :max="max" :step="step"
        class="mb-flex-1"
        :title="t('Doppio click per reimpostare al valore predefinito')"
      />
      <input
        type="number"
        :value="uniformValue"
        @input="onUniformInput($event.target.value)"
        @dblclick="onReset"
        @wheel="handleNumberWheel"
        :min="min" :max="max" :step="step"
        class="olo-num-input mb-w-16 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-1.5 mb-py-0.5 mb-text-xs mb-text-gray-900 mb-text-center"
        :title="t('Doppio click per reimpostare al valore predefinito')"
      />
      <button
        @click="unlink"
        class="mb-p-1 mb-text-gray-400 hover:mb-text-primary-400 mb-transition-colors"
        :title="t('Modifica singoli lati')"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
          <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
        </svg>
      </button>
    </div>

    <!-- Unlinked mode: 4 side inputs -->
    <div v-else>
      <div class="mb-grid mb-grid-cols-[1fr_1fr_auto] mb-gap-x-2 mb-gap-y-1 mb-items-center">
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ labels.top }}</label>
          <input
            type="number"
            :value="sides.top"
            @input="onSideInput('top', $event.target.value)"
            @wheel="handleNumberWheel"
            :min="min" :max="max" :step="step"
            class="olo-num-input mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900 mb-text-center"
          />
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ labels.right }}</label>
          <input
            type="number"
            :value="sides.right"
            @input="onSideInput('right', $event.target.value)"
            @wheel="handleNumberWheel"
            :min="min" :max="max" :step="step"
            class="olo-num-input mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900 mb-text-center"
          />
        </div>
        <button
          @click="relink"
          class="mb-p-1 mb-text-gray-500 hover:mb-text-primary-400 mb-transition-colors mb-mt-4"
          :title="t('Collega tutti i lati')"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ labels.bottom }}</label>
          <input
            type="number"
            :value="sides.bottom"
            @input="onSideInput('bottom', $event.target.value)"
            @wheel="handleNumberWheel"
            :min="min" :max="max" :step="step"
            class="olo-num-input mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900 mb-text-center"
          />
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">{{ labels.left }}</label>
          <input
            type="number"
            :value="sides.left"
            @input="onSideInput('left', $event.target.value)"
            @wheel="handleNumberWheel"
            :min="min" :max="max" :step="step"
            class="olo-num-input mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900 mb-text-center"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, watch } from 'vue';
import { handleNumberWheel } from '@/utils/numberInputWheel';

const props = defineProps({
  modelValue: { default: () => ({ top: 0, right: 0, bottom: 0, left: 0 }) },
  min: { type: [Number, String], default: 0 },
  max: { type: [Number, String], default: 200 },
  step: { type: [Number, String], default: 1 },
  defaultValue: { type: [Object, Number, String, null], default: null },
  labels: {
    type: Object,
    default: () => ({ top: 'Sopra', right: 'Destra', bottom: 'Sotto', left: 'Sinistra' }),
  },
});
const emit = defineEmits(['update:modelValue']);

// Parse sides from modelValue
const sides = computed(() => {
  const v = props.modelValue;
  if (v && typeof v === 'object') {
    return {
      top: parseInt(v.top) || 0,
      right: parseInt(v.right) || 0,
      bottom: parseInt(v.bottom) || 0,
      left: parseInt(v.left) || 0,
    };
  }
  const n = parseInt(String(v || '0')) || 0;
  return { top: n, right: n, bottom: n, left: n };
});

// Detect linked/unlinked from modelValue
function areAllEqual(s) {
  return s.top === s.right && s.right === s.bottom && s.bottom === s.left;
}

const linked = ref(areAllEqual(sides.value));

// Re-detect linked state when modelValue changes externally
watch(() => props.modelValue, () => {
  if (typeof props.modelValue !== 'object') {
    linked.value = true;
  }
}, { immediate: false });

const uniformValue = computed(() => sides.value.top);

function onUniformInput(val) {
  const n = parseInt(val) || 0;
  emit('update:modelValue', { top: n, right: n, bottom: n, left: n });
}

function onSideInput(side, val) {
  const n = parseInt(val) || 0;
  emit('update:modelValue', { ...sides.value, [side]: n });
}

function unlink() {
  linked.value = false;
  emit('update:modelValue', { ...sides.value });
}

function relink() {
  linked.value = true;
  const s = sides.value;
  const max = Math.max(s.top, s.right, s.bottom, s.left);
  emit('update:modelValue', { top: max, right: max, bottom: max, left: max });
}

function onReset() {
  const d = props.defaultValue;
  if (d && typeof d === 'object') {
    emit('update:modelValue', {
      top: parseInt(d.top) || 0,
      right: parseInt(d.right) || 0,
      bottom: parseInt(d.bottom) || 0,
      left: parseInt(d.left) || 0,
    });
    return;
  }
  // Empty default ('' / null / undefined) → emit empty value to mean "inherit/unset"
  if (d === null || d === undefined || d === '') {
    emit('update:modelValue', '');
    return;
  }
  const n = parseInt(d) || 0;
  emit('update:modelValue', { top: n, right: n, bottom: n, left: n });
}
</script>

<style scoped>
/* Hide native number spin-buttons: liberano ~12px utili a mostrare 3 cifre. */
.olo-num-input::-webkit-outer-spin-button,
.olo-num-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.olo-num-input {
  -moz-appearance: textfield;
}
</style>
