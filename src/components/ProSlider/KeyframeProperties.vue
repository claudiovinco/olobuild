<template>
  <div class="mb-space-y-3">
    <!-- Header con info keyframe -->
    <div v-if="keyframe" class="mb-space-y-2">
      <!-- Tempo -->
      <div>
        <label class="mps-label">{{ t('Tempo (ms)') }}</label>
        <NumberScrubber
          theme="dark"
          :modelValue="keyframe.time"
          :min="0"
          :max="timeline.duration"
          :step="50"
          :defaultValue="0"
          emitAs="number"
          unit="ms"
          :ariaLabel="t('Tempo (ms)')"
          @update:modelValue="upKf('time', Math.max(0, Math.min(timeline.duration, $event)))"
        />
      </div>

      <!-- Easing -->
      <div>
        <label class="mps-label">Easing</label>
        <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="keyframe.easing || 'ease'" :options="EASING_GROUPS" @update:model-value="upKf('easing', $event)" />
      </div>

      <div class="mb-border-t mb-border-gray-700 mb-pt-2"></div>

      <!-- Proprietà animabili -->
      <div v-for="(meta, key) in ANIMATABLE_PROPS" :key="key">
        <label class="mps-label">{{ meta.label }}</label>
        <NumberScrubber
          theme="dark"
          :modelValue="keyframe.props[key] ?? meta.default"
          :min="meta.min"
          :max="meta.max"
          :step="meta.step"
          :defaultValue="meta.default"
          emitAs="number"
          :sliderOnFocus="false"
          :ariaLabel="meta.label"
          @update:modelValue="upProp(key, clampVal($event, meta.min, meta.max))"
        />
      </div>

      <!-- Cattura da canvas -->
      <button
        @click="$emit('capture-from-canvas')"
        class="mb-w-full mb-mt-2 mb-px-2 mb-py-1 mb-bg-gray-700 mb-rounded mb-text-[10px] mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors mb-text-center"
      >{{ t('Cattura posizione dal canvas') }}</button>
    </div>

    <!-- Nessun keyframe selezionato -->
    <div v-else class="mb-text-[10px] mb-text-gray-500 mb-italic mb-text-center mb-py-4">
      {{ t('Seleziona un keyframe nella timeline') }}
    </div>
  </div>
</template>

<script setup>
import { ANIMATABLE_PROPS, EASING_GROUPS } from './timelineUtils.js';
import FieldSelect from '../Builder/fields/FieldSelect.vue';
import NumberScrubber from '../Builder/fields/NumberScrubber.vue';
import { t } from '@/i18n';

const props = defineProps({
  keyframe: { type: Object, default: null },
  timeline: { type: Object, required: true },
});

const emit = defineEmits(['update-keyframe', 'capture-from-canvas']);

function upKf(field, value) {
  if (!props.keyframe) return;
  emit('update-keyframe', props.keyframe.id, { [field]: value });
}

function upProp(propName, value) {
  if (!props.keyframe) return;
  const newProps = { ...props.keyframe.props, [propName]: value };
  emit('update-keyframe', props.keyframe.id, { props: newProps });
}

function clampVal(v, min, max) {
  return Math.max(min, Math.min(max, v));
}

function roundVal(v, step) {
  if (step >= 1) return Math.round(v);
  const decimals = String(step).split('.')[1]?.length || 1;
  return Number(v.toFixed(decimals));
}
</script>
