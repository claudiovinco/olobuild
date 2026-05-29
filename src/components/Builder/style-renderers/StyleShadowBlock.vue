<template>
  <div>
    <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
      <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400">{{ t(field.label) }}</label>
      <button
        v-if="field.hoverable"
        @click="hoverOpen = !hoverOpen"
        class="mb-p-0.5 mb-rounded mb-transition-colors"
        :class="hoverOpen || hasHoverValue ? 'mb-text-orange-400' : 'mb-text-gray-500 hover:mb-text-gray-300'"
        :title="t('Stato hover')"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
      </button>
    </div>

    <select
      :value="preset"
      @change="onPresetUpdate($event.target.value)"
      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
    >
      <option value="none">{{ t('Nessuna') }}</option>
      <option value="sm">{{ t('Piccola') }}</option>
      <option value="md">{{ t('Media') }}</option>
      <option value="lg">{{ t('Grande') }}</option>
      <option value="xl">{{ t('Extra grande') }}</option>
      <option value="custom">{{ t('Personalizzata') }}</option>
    </select>

    <FieldBoxShadow
      v-if="preset === 'custom'"
      :modelValue="customValue"
      class="mb-mt-2"
      @update:modelValue="onCustomUpdate"
    />

    <!-- Hover panel -->
    <div
      v-if="field.hoverable && hoverOpen"
      class="mb-mt-1.5 mb-pl-2 mb-pr-1 mb-py-1.5 mb-border-l-2 mb-border-orange-400/60 mb-bg-orange-400/5 mb-rounded-r"
    >
      <div class="mb-flex mb-items-center mb-justify-between mb-mb-1">
        <span class="mb-text-[10px] mb-font-semibold mb-text-orange-400 mb-uppercase mb-tracking-wide">
          {{ t('Hover') }}
        </span>
        <button
          v-if="hasHoverValue"
          @click="resetHover"
          class="mb-text-[10px] mb-text-gray-500 hover:mb-text-red-400 mb-px-1"
          :title="t('Reset')"
        >×</button>
      </div>
      <select
        :value="hoverPreset"
        @change="onHoverPresetUpdate($event.target.value)"
        class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
      >
        <option value="">{{ t('Invariata') }}</option>
        <option value="none">{{ t('Nessuna') }}</option>
        <option value="sm">{{ t('Piccola') }}</option>
        <option value="md">{{ t('Media') }}</option>
        <option value="lg">{{ t('Grande') }}</option>
        <option value="xl">{{ t('Extra grande') }}</option>
        <option value="custom">{{ t('Personalizzata') }}</option>
      </select>
      <FieldBoxShadow
        v-if="hoverPreset === 'custom'"
        :modelValue="hoverCustomValue"
        class="mb-mt-2"
        @update:modelValue="onHoverCustomUpdate"
      />
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import FieldBoxShadow from '../fields/FieldBoxShadow.vue';
import { t } from '@/i18n';

/**
 * StyleShadowBlock — pseudo-field "shadow_block" del tab Stile.
 * Salva su due chiavi reali:
 *   - tile.style.shadow         (preset string: 'none|sm|md|lg|xl|custom')
 *   - tile.style.shadow_custom  (oggetto FieldBoxShadow se preset === 'custom')
 *
 * Hover (legacy nested):
 *   - tile.style.hover.shadow
 *   - tile.style.hover.shadow_custom
 */
const props = defineProps({
  field: { type: Object, required: true },
  tileStyle: { type: Object, required: true },
});
const emit = defineEmits(['update']);

const preset = computed(() => props.tileStyle.shadow || 'none');
const customValue = computed(() => props.tileStyle.shadow_custom || {});

const hoverOpen = ref(false);
const hoverState = computed(() => props.tileStyle.hover || {});
const hoverPreset = computed(() => hoverState.value.shadow ?? '');
const hoverCustomValue = computed(() => hoverState.value.shadow_custom || {});
const hasHoverValue = computed(() => hoverPreset.value !== '' && hoverPreset.value !== null && hoverPreset.value !== undefined);

function onPresetUpdate(val) {
  emit('update', { type: 'main', key: 'shadow', value: val });
}
function onCustomUpdate(val) {
  emit('update', { type: 'main', key: 'shadow_custom', value: val });
}
function onHoverPresetUpdate(val) {
  emit('update', { type: 'hover', key: 'shadow', value: val });
}
function onHoverCustomUpdate(val) {
  emit('update', { type: 'hover', key: 'shadow_custom', value: val });
}
function resetHover() {
  emit('update', { type: 'hover', key: 'shadow', value: '' });
  emit('update', { type: 'hover', key: 'shadow_custom', value: {} });
}
</script>
