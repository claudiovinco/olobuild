<template>
  <div class="mb-space-y-3">
    <!-- Header con info keyframe -->
    <div v-if="keyframe" class="mb-space-y-2">
      <!-- Tempo -->
      <div>
        <label class="mps-label">Tempo (ms)</label>
        <input
          type="number"
          :value="keyframe.time"
          @change="upKf('time', Math.max(0, Math.min(timeline.duration, +$event.target.value)))"
          class="mps-input mb-w-full"
          min="0"
          :max="timeline.duration"
          step="50"
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
        <div class="mb-flex mb-gap-2 mb-items-center">
          <input
            type="range"
            :min="meta.min"
            :max="meta.max"
            :step="meta.step"
            :value="keyframe.props[key] ?? meta.default"
            @input="upProp(key, +$event.target.value)"
            class="mb-flex-1 mb-h-1 mb-accent-primary-500"
          />
          <input
            type="number"
            :value="roundVal(keyframe.props[key] ?? meta.default, meta.step)"
            @change="upProp(key, clampVal(+$event.target.value, meta.min, meta.max))"
            class="mb-w-16 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-1.5 mb-py-0.5 mb-text-[10px] mb-text-gray-300 mb-text-right"
            :min="meta.min"
            :max="meta.max"
            :step="meta.step"
          />
        </div>
      </div>

      <!-- Cattura da canvas -->
      <button
        @click="$emit('capture-from-canvas')"
        class="mb-w-full mb-mt-2 mb-px-2 mb-py-1 mb-bg-gray-700 mb-rounded mb-text-[10px] mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors mb-text-center"
      >Cattura posizione dal canvas</button>
    </div>

    <!-- Nessun keyframe selezionato -->
    <div v-else class="mb-text-[10px] mb-text-gray-500 mb-italic mb-text-center mb-py-4">
      Seleziona un keyframe nella timeline
    </div>
  </div>
</template>

<script setup>
import { ANIMATABLE_PROPS, EASING_GROUPS } from './timelineUtils.js';
import FieldSelect from '../Builder/fields/FieldSelect.vue';

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
