<template>
  <div class="mb-space-y-2">
    <!-- Gradient type -->
    <div class="mb-flex mb-gap-1">
      <button v-for="t in ['linear','radial']" :key="t"
        @click="updateType(t)"
        :class="['mb-flex-1 mb-py-1 mb-text-[10px] mb-rounded',
          type === t ? 'mb-bg-primary-600 mb-text-white' : 'mb-bg-gray-700 mb-text-gray-400']">
        {{ t === 'linear' ? 'Lineare' : 'Radiale' }}
      </button>
    </div>
    <!-- Angle (linear only) -->
    <div v-if="type === 'linear'" class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-500 mb-w-12">{{ t('Angolo') }}</label>
      <input type="range" :value="angle" @input="updateAngle($event.target.value)" min="0" max="360"
        class="mb-flex-1 mb-h-1.5 mb-rounded-full mb-appearance-none mb-bg-gray-600" />
      <span class="mb-text-[10px] mb-text-gray-400 mb-w-8 mb-text-right">{{ angle }}°</span>
    </div>
    <!-- Color stops -->
    <div class="mb-space-y-2">
      <div v-for="(stop, i) in stops" :key="i" class="mb-space-y-1">
        <div class="mb-flex mb-items-center mb-gap-1">
          <span class="mb-text-[10px] mb-text-gray-500 mb-w-6">{{ stop.position }}%</span>
          <input type="number" :value="stop.position" @input="updateStop(i, 'position', $event.target.value)"
            min="0" max="100" class="mb-w-14 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-1.5 mb-py-1 mb-text-[10px] mb-text-gray-200" />
          <button v-if="stops.length > 2" @click="removeStop(i)" class="mb-text-gray-500 hover:mb-text-red-400 mb-text-xs">&times;</button>
        </div>
        <FieldColor :modelValue="stop.color" @update:modelValue="updateStop(i, 'color', $event)" />
      </div>
    </div>
    <button @click="addStop" class="mb-w-full mb-py-1 mb-text-[10px] mb-text-gray-400 mb-bg-gray-700 mb-rounded hover:mb-bg-gray-600">
      {{ t('+ Aggiungi colore') }}
    </button>
    <!-- Preview -->
    <div class="mb-h-6 mb-rounded mb-border mb-border-gray-600" :style="{ background: previewGradient }"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import FieldColor from './FieldColor.vue';
import { t } from '@/i18n';

const props = defineProps({
  modelValue: { type: Object, default: () => ({
    type: 'linear', angle: 180,
    stops: [{ color: '#6366f1', position: 0 }, { color: '#ec4899', position: 100 }]
  })}
});
const emit = defineEmits(['update:modelValue']);

const type = computed(() => props.modelValue?.type || 'linear');
const angle = computed(() => props.modelValue?.angle ?? 180);
const stops = computed(() => props.modelValue?.stops || [
  { color: '#6366f1', position: 0 }, { color: '#ec4899', position: 100 }
]);

const previewGradient = computed(() => {
  const s = stops.value.map(st => `${st.color} ${st.position}%`).join(', ');
  return type.value === 'radial' ? `radial-gradient(circle, ${s})` : `linear-gradient(${angle.value}deg, ${s})`;
});

function emitUpdate(partial) {
  emit('update:modelValue', { type: type.value, angle: angle.value, stops: [...stops.value], ...partial });
}
function updateType(t) { emitUpdate({ type: t }); }
function updateAngle(a) { emitUpdate({ angle: parseInt(a) || 0 }); }
function updateStop(i, key, val) {
  const newStops = stops.value.map((s, idx) => idx === i ? { ...s, [key]: key === 'position' ? parseInt(val) || 0 : val } : s);
  emitUpdate({ stops: newStops });
}
function removeStop(i) {
  emitUpdate({ stops: stops.value.filter((_, idx) => idx !== i) });
}
function addStop() {
  // Il nuovo stop atterra a metà del gap più ampio tra gli stop esistenti:
  // con [0%, 100%] va al 50% ed è subito visibile (prima finiva a 100%,
  // sovrapposto all'ultimo = zero pixel nel gradiente).
  const sorted = [...stops.value].sort((a, b) => (a.position ?? 0) - (b.position ?? 0));
  let position = 50;
  let insertAt = sorted.length;
  if (sorted.length >= 2) {
    let bestGap = -1;
    for (let i = 0; i < sorted.length - 1; i++) {
      const gap = (sorted[i + 1].position ?? 0) - (sorted[i].position ?? 0);
      if (gap > bestGap) {
        bestGap = gap;
        position = Math.round(((sorted[i].position ?? 0) + (sorted[i + 1].position ?? 0)) / 2);
        insertAt = i + 1;
      }
    }
  } else if (sorted.length === 1) {
    position = (sorted[0].position ?? 50) < 50 ? 100 : 0;
    insertAt = position > (sorted[0].position ?? 50) ? 1 : 0;
  }
  const next = [...sorted];
  next.splice(insertAt, 0, { color: '#9ca3af', position });
  emitUpdate({ stops: next });
}
</script>
