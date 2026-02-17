<template>
  <div>
    <!-- Linked mode: single input -->
    <div v-if="linked" class="mb-flex mb-items-center mb-gap-2">
      <input
        type="range"
        :value="uniformValue"
        @input="onUniformInput($event.target.value)"
        min="0" max="50" step="1"
        class="mb-flex-1"
      />
      <span class="mb-text-xs mb-text-gray-400 mb-w-8 mb-text-right">{{ uniformValue }}</span>
      <button
        @click="unlink"
        class="mb-p-1 mb-text-gray-400 hover:mb-text-primary-400 mb-transition-colors"
        title="Unlink corners"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
          <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
        </svg>
      </button>
    </div>

    <!-- Unlinked mode: 4 corner inputs -->
    <div v-else>
      <div class="mb-grid mb-grid-cols-[1fr_1fr_auto] mb-gap-x-2 mb-gap-y-1 mb-items-center">
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">TL</label>
          <input
            type="number"
            :value="corners.tl"
            @input="onCornerInput('tl', $event.target.value)"
            min="0" max="50" step="1"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
          />
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">TR</label>
          <input
            type="number"
            :value="corners.tr"
            @input="onCornerInput('tr', $event.target.value)"
            min="0" max="50" step="1"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
          />
        </div>
        <button
          @click="relink"
          class="mb-p-1 mb-text-gray-500 hover:mb-text-primary-400 mb-transition-colors mb-mt-4"
          title="Link corners"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">BL</label>
          <input
            type="number"
            :value="corners.bl"
            @input="onCornerInput('bl', $event.target.value)"
            min="0" max="50" step="1"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
          />
        </div>
        <div>
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">BR</label>
          <input
            type="number"
            :value="corners.br"
            @input="onCornerInput('br', $event.target.value)"
            min="0" max="50" step="1"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  modelValue: { default: 0 },
});
const emit = defineEmits(['update:modelValue']);

// Detect linked/unlinked from modelValue
const linked = ref(!(props.modelValue && typeof props.modelValue === 'object'));

// Parse uniform value from modelValue
const uniformValue = computed(() => {
  const v = props.modelValue;
  if (v && typeof v === 'object') return v.tl || 0;
  // Handle string like '4px'
  const s = String(v || '0');
  return parseInt(s) || 0;
});

// Parse 4 corners from modelValue
const corners = computed(() => {
  const v = props.modelValue;
  if (v && typeof v === 'object') {
    return { tl: v.tl || 0, tr: v.tr || 0, br: v.br || 0, bl: v.bl || 0 };
  }
  const n = parseInt(String(v || '0')) || 0;
  return { tl: n, tr: n, br: n, bl: n };
});

// Re-detect linked state when modelValue changes externally
watch(() => props.modelValue, (val) => {
  linked.value = !(val && typeof val === 'object');
}, { immediate: false });

function onUniformInput(val) {
  emit('update:modelValue', parseInt(val) || 0);
}

function onCornerInput(corner, val) {
  const n = parseInt(val) || 0;
  emit('update:modelValue', { ...corners.value, [corner]: n });
}

function unlink() {
  linked.value = false;
  const n = uniformValue.value;
  emit('update:modelValue', { tl: n, tr: n, br: n, bl: n });
}

function relink() {
  linked.value = true;
  // Use max of all corners
  const c = corners.value;
  const max = Math.max(c.tl, c.tr, c.br, c.bl);
  emit('update:modelValue', max);
}
</script>
