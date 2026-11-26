<template>
  <div class="mb-space-y-3">
    <!-- Header con info keyframe -->
    <div v-if="keyframe" class="mb-space-y-2">
      <!-- Tempo -->
      <div>
        <label class="mps-label">{{ t('Tempo') }}</label>
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
        <label class="mps-label mb-flex mb-items-center mb-gap-1">{{ t('Andamento') }}<InfoTip :testo="t('Come il livello va da questo fotogramma chiave al successivo: costante, morbido, che frena alla fine… Nell’ultimo fotogramma non conta.')" :titolo="t('Andamento')" /></label>
        <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="keyframe.easing || 'ease'" :options="EASING_GROUPS" @update:model-value="upKf('easing', $event)" />
      </div>

      <div class="mb-border-t mb-border-gray-700 mb-pt-2"></div>

      <!-- Proprietà animabili -->
      <div v-for="(meta, key) in ANIMATABLE_PROPS" :key="key">
        <label class="mps-label">{{ t(meta.label) }}</label>
        <!-- 1.4.1462: un valore scritto tiene fino a 3 decimali (il passo vale per frecce e trascinamento) -->
        <NumberScrubber
          theme="dark"
          :modelValue="keyframe.props[key] ?? meta.default"
          :min="meta.min"
          :max="meta.max"
          :step="meta.step"
          :scriviDecimali="3"
          :defaultValue="meta.default"
          emitAs="number"
          :unit="meta.unit"
          :sliderOnFocus="false"
          :ariaLabel="t(meta.label)"
          @update:modelValue="upProp(key, clampVal($event, meta.min, meta.max))"
        />
      </div>

      <!-- 1.4.1472: tolto «Prendi la posizione dalla tela». Copiava nel fotogramma la posizione FERMA del livello (quella
           di «Semplice»), che con la linea del tempo non si vede né si sposta: annullava lo spostamento appena fatto.
           Trascinare il livello sulla tela scrive già la posizione nel fotogramma scelto, e «+ Fotogramma chiave»
           prende la posa del momento. -->
    </div>

    <!-- Nessun keyframe selezionato -->
    <div v-else class="mb-text-[10px] mb-text-gray-500 mb-italic mb-text-center mb-py-4">
      {{ t('Seleziona un fotogramma chiave nella linea del tempo') }}
    </div>
  </div>
</template>

<script setup>
import { ANIMATABLE_PROPS, EASING_GROUPS } from './timelineUtils.js';
import FieldSelect from '../Builder/fields/FieldSelect.vue';
import NumberScrubber from '../Builder/fields/NumberScrubber.vue';
import InfoTip from '../Builder/InfoTip.vue';
import { t } from '@/i18n';

const props = defineProps({
  keyframe: { type: Object, default: null },
  timeline: { type: Object, required: true },
});

const emit = defineEmits(['update-keyframe']);

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
