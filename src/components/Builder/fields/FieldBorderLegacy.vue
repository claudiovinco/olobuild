<template>
  <div class="mb-space-y-2">
    <!-- Toggle "occhio" hover (visibile solo se hoverable=true) -->
    <div v-if="hoverable" class="mb-flex mb-items-center mb-justify-end">
      <button
        type="button"
        @click="hoverOpen = !hoverOpen"
        :class="[
          'mb-text-[10px] mb-px-2 mb-py-0.5 mb-rounded mb-transition-colors',
          hoverOpen
            ? 'mb-bg-amber-100 mb-text-amber-700'
            : 'mb-bg-gray-100 mb-text-gray-500 hover:mb-bg-gray-200'
        ]"
        :title="t('Configura bordo allo hover')"
      >
        {{ hoverOpen ? t('▼ Hover') : t('▸ Hover') }}
      </button>
    </div>

    <!-- Set normale -->
    <div class="mb-flex mb-gap-2">
      <input
        type="number"
        :value="value.width"
        @input="onUpdate('width', Math.max(0, parseInt($event.target.value) || 0))"
        :placeholder="t('Larghezza')"
        min="0" max="20" step="1"
        class="mb-w-16 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
      />
      <select
        :value="value.style"
        @change="onUpdate('style', $event.target.value)"
        class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
      >
        <option value="solid">{{ t('Continuo') }}</option>
        <option value="dashed">{{ t('Tratteggiato') }}</option>
        <option value="dotted">{{ t('Punteggiato') }}</option>
        <option value="double">{{ t('Doppio') }}</option>
        <option value="groove">{{ t('Incasso') }}</option>
        <option value="ridge">{{ t('Rilievo') }}</option>
        <option value="inset">{{ t('Inset') }}</option>
        <option value="outset">{{ t('Outset') }}</option>
        <option value="none">{{ t('Nessuno') }}</option>
      </select>
    </div>
    <FieldColor
      :modelValue="value.color || '#cccccc'"
      @update:modelValue="onUpdate('color', $event)"
    />

    <!-- Set hover (visibile solo quando hoverOpen=true) -->
    <div v-if="hoverable && hoverOpen" class="mb-mt-2 mb-pt-2 mb-border-t mb-border-amber-200 mb-space-y-2">
      <div class="mb-text-[10px] mb-text-amber-700 mb-font-semibold">{{ t('Bordo allo hover') }}</div>
      <div class="mb-flex mb-gap-2">
        <input
          type="number"
          :value="hoverValue.width"
          @input="onHoverUpdate('width', Math.max(0, parseInt($event.target.value) || 0))"
          :placeholder="t('Larghezza')"
          min="0" max="20" step="1"
          class="mb-w-16 mb-bg-white mb-border mb-border-amber-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
        />
        <select
          :value="hoverValue.style"
          @change="onHoverUpdate('style', $event.target.value)"
          class="mb-flex-1 mb-bg-white mb-border mb-border-amber-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900"
        >
          <option value="">{{ t('— invariato —') }}</option>
          <option value="solid">{{ t('Continuo') }}</option>
          <option value="dashed">{{ t('Tratteggiato') }}</option>
          <option value="dotted">{{ t('Punteggiato') }}</option>
          <option value="double">{{ t('Doppio') }}</option>
          <option value="none">{{ t('Nessuno') }}</option>
        </select>
      </div>
      <FieldColor
        :modelValue="hoverValue.color || ''"
        @update:modelValue="onHoverUpdate('color', $event)"
      />
      <button
        v-if="hasHoverValue"
        type="button"
        @click="resetHover"
        class="mb-text-[10px] mb-text-gray-500 hover:mb-text-red-600 mb-underline"
      >
        {{ t('Rimuovi hover') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import FieldColor from './FieldColor.vue';
import { t } from '@/i18n';

/**
 * FieldBorderLegacy — UI legacy per il "border" del tab Stile, che usa
 * 3 scalari piatti: width + style + color (uniformi sui 4 lati).
 * NON è il `FieldBorder` (oggetto 4-side) usato da borderFields() in Contenuto.
 *
 * modelValue:      oggetto { width, style, color }      → tile.style.border_width/_style/_color
 * hoverModelValue: oggetto { width, style, color }      → tile.style.hover.border_width/_style/_color
 *
 * v3.55.17 — supporto hover esteso anche a width + style (prima solo color).
 */
const props = defineProps({
  modelValue:      { type: [Object, null], default: () => ({}) },
  hoverModelValue: { type: [Object, null], default: () => ({}) },
  hoverable:       { type: Boolean,        default: false },
});
const emit = defineEmits(['update:modelValue', 'update:hoverModelValue']);

const hoverOpen = ref(false);

const value = computed(() => ({
  width: props.modelValue?.width ?? 0,
  style: props.modelValue?.style ?? 'solid',
  color: props.modelValue?.color ?? '',
}));

const hoverValue = computed(() => ({
  width: props.hoverModelValue?.width ?? '',
  style: props.hoverModelValue?.style ?? '',
  color: props.hoverModelValue?.color ?? '',
}));

const hasHoverValue = computed(() =>
  hoverValue.value.width !== '' || hoverValue.value.style !== '' || hoverValue.value.color !== ''
);

function onUpdate(key, val) {
  emit('update:modelValue', { ...value.value, [key]: val });
}
function onHoverUpdate(key, val) {
  emit('update:hoverModelValue', { ...hoverValue.value, [key]: val });
}
function resetHover() {
  emit('update:hoverModelValue', { width: '', style: '', color: '' });
}
</script>
