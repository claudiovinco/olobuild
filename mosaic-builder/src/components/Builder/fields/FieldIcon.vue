<template>
  <div class="mb-flex mb-items-center mb-gap-2">
    <!-- Icon preview -->
    <span
      v-if="modelValue && iconSvg"
      class="field-icon-preview"
      v-html="iconSvg"
    ></span>
    <span v-else class="field-icon-preview field-icon-empty">?</span>

    <!-- Text input -->
    <input
      type="text"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-900"
      placeholder="es. star, home, check"
    />

    <!-- Browse button -->
    <button
      type="button"
      @click="showPicker = true"
      class="mb-px-2 mb-py-1.5 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-text-[10px] mb-text-gray-300 hover:mb-bg-gray-600 mb-whitespace-nowrap"
    >Sfoglia</button>

    <!-- Icon picker modal -->
    <IconPicker
      v-if="showPicker"
      @select="onSelect"
      @close="showPicker = false"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import iconsSvg from '../../ProSlider/uikitIconsSvg.js';
import IconPicker from '../../ProSlider/IconPicker.vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const showPicker = ref(false);

const iconSvg = computed(() => iconsSvg[props.modelValue] || '');

function onSelect(name) {
  emit('update:modelValue', name);
  showPicker.value = false;
}
</script>

<style scoped>
.field-icon-preview {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  background: #374151;
  border-radius: 4px;
  flex-shrink: 0;
}
.field-icon-preview :deep(svg) {
  width: 20px;
  height: 20px;
  fill: #d1d5db;
  stroke: #d1d5db;
}
.field-icon-empty {
  color: #6b7280;
  font-size: 12px;
}
</style>
