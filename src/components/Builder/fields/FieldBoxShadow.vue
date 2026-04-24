<template>
  <div class="mb-space-y-2">
    <div class="mb-flex mb-gap-2">
      <div class="mb-flex-1">
        <label class="mb-text-[10px] mb-text-gray-500">H</label>
        <input type="number" :value="val.h" @input="update('h', $event.target.value)" min="-100" max="100"
          :aria-label="t('Offset orizzontale ombra (px)')"
          class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200" />
      </div>
      <div class="mb-flex-1">
        <label class="mb-text-[10px] mb-text-gray-500">V</label>
        <input type="number" :value="val.v" @input="update('v', $event.target.value)" min="-100" max="100"
          :aria-label="t('Offset verticale ombra (px)')"
          class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200" />
      </div>
      <div class="mb-flex-1">
        <label class="mb-text-[10px] mb-text-gray-500">{{ t('Blur') }}</label>
        <input type="number" :value="val.blur" @input="update('blur', $event.target.value)" min="0" max="200"
          :aria-label="t('Sfocatura ombra (px)')"
          class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200" />
      </div>
      <div class="mb-flex-1">
        <label class="mb-text-[10px] mb-text-gray-500">{{ t('Spread') }}</label>
        <input type="number" :value="val.spread" @input="update('spread', $event.target.value)" min="-100" max="100"
          :aria-label="t('Diffusione ombra (px)')"
          class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200" />
      </div>
    </div>
    <div>
      <label class="mb-text-[10px] mb-text-gray-500">{{ t('Colore') }}</label>
      <FieldColor :modelValue="val.color || '#000000'" @update:modelValue="update('color', $event)" />
    </div>
    <label class="mb-flex mb-items-center mb-gap-1 mb-cursor-pointer mb-text-[10px] mb-text-gray-400">
      <input type="checkbox" :checked="val.inset" @change="update('inset', $event.target.checked)"
        class="mb-rounded mb-border-gray-600" />
      Inset
    </label>
    <div class="mb-text-[10px] mb-text-gray-500 mb-bg-gray-900 mb-rounded mb-px-2 mb-py-1 mb-font-mono">
      {{ previewText }}
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import FieldColor from './FieldColor.vue';

const props = defineProps({
  modelValue: { type: Object, default: () => ({ h: 0, v: 4, blur: 10, spread: 0, color: 'rgba(0,0,0,0.15)', inset: false }) }
});
const emit = defineEmits(['update:modelValue']);

const val = computed(() => ({
  h: props.modelValue?.h ?? 0,
  v: props.modelValue?.v ?? 4,
  blur: props.modelValue?.blur ?? 10,
  spread: props.modelValue?.spread ?? 0,
  color: props.modelValue?.color ?? 'rgba(0,0,0,0.15)',
  inset: props.modelValue?.inset ?? false,
}));

const previewText = computed(() => {
  const v = val.value;
  return `${v.inset ? 'inset ' : ''}${v.h}px ${v.v}px ${v.blur}px ${v.spread}px ${v.color}`;
});

function update(key, value) {
  const numKeys = ['h', 'v', 'blur', 'spread'];
  const newVal = { ...val.value, [key]: numKeys.includes(key) ? parseInt(value) || 0 : value };
  emit('update:modelValue', newVal);
}
</script>
