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
      :placeholder="t('es. star, home, check')"
    />

    <!-- Browse button -->
    <button
      type="button"
      @click="showPicker = true"
      class="mb-px-2 mb-py-1.5 mb-bg-gray-100 mb-border mb-border-gray-300 mb-rounded mb-text-[10px] mb-text-gray-600 hover:mb-bg-gray-200 mb-whitespace-nowrap"
      :aria-label="t('Sfoglia libreria icone')"
    >{{ t('Sfoglia') }}</button>

    <!-- Icon picker modal -->
    <IconPicker
      v-if="showPicker"
      @select="onSelect"
      @close="showPicker = false"
    />
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, watch } from 'vue';
import iconsSvg from '../../ProSlider/iconsLibrary.js';
import IconPicker from '../../ProSlider/IconPicker.vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const showPicker = ref(false);
const customSvgCache = ref({});

const isCustom = computed(() => props.modelValue?.startsWith('custom:'));

const iconSvg = computed(() => {
  if (isCustom.value) {
    const name = props.modelValue.slice(7);
    return customSvgCache.value[name] || '';
  }
  return iconsSvg[props.modelValue] || '';
});

// Load custom icon SVG if needed
watch(() => props.modelValue, async (val) => {
  if (!val?.startsWith('custom:')) return;
  const name = val.slice(7);
  if (customSvgCache.value[name]) return;
  try {
    const res = await fetch(`${window.oloData?.restUrl || '/wp-json/'}olobuild/v1/custom-icons`, {
      headers: { 'X-WP-Nonce': window.oloData?.nonce || '' },
    });
    if (res.ok) {
      const icons = await res.json();
      customSvgCache.value = icons;
    }
  } catch (e) { /* silently fail */ }
}, { immediate: true });

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
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  flex-shrink: 0;
}
.field-icon-preview :deep(svg) {
  width: 20px;
  height: 20px;
  color: #374151;
  stroke: currentColor;
}
.field-icon-preview :deep(svg:not([fill="none"])) {
  fill: currentColor;
}
.field-icon-preview :deep(svg [fill="none"]) {
  fill: none;
}
.field-icon-empty {
  color: #9CA3AF;
  font-size: 12px;
}
</style>
