<template>
  <div class="mb-space-y-2">
    <!-- Property rows -->
    <div v-for="prop in properties" :key="prop.key" class="mb-border mb-border-gray-600 mb-rounded-md mb-overflow-hidden">
      <!-- Property header -->
      <div class="mb-flex mb-items-center mb-justify-between mb-bg-gray-700 mb-px-2 mb-py-1">
        <span class="mb-text-[10px] mb-font-medium mb-text-gray-300">
          {{ prop.label }}
          <span v-if="getStops(prop.key).length" class="mb-text-gray-500 mb-ml-1">({{ getStops(prop.key).length }})</span>
        </span>
        <div class="mb-flex mb-items-center mb-gap-1">
          <button
            @click="addStop(prop.key, prop)"
            class="mb-text-[10px] mb-text-primary-400 hover:mb-text-primary-300 mb-px-1"
            :title="t('Aggiungi stop')"
          >+</button>
          <button
            v-if="getStops(prop.key).length"
            @click="clearStops(prop.key)"
            class="mb-text-[10px] mb-text-red-400 hover:mb-text-red-300 mb-px-1"
            :title="t('Rimuovi tutti')"
          >{{ t('&times;') }}</button>
        </div>
      </div>
      <!-- Stops -->
      <div v-if="getStops(prop.key).length" class="mb-px-2 mb-py-1 mb-space-y-1">
        <div
          v-for="(stop, idx) in getStops(prop.key)"
          :key="idx"
          class="mb-flex mb-items-center mb-gap-1"
        >
          <NumberScrubber
            :modelValue="stop.value"
            :min="prop.min ?? null" :max="prop.max ?? null" :step="prop.step || 1"
            :defaultValue="prop.min ?? 0"
            emitAs="number"
            :unit="prop.unit || ''"
            :ariaLabel="t('Valore stop')"
            @update:modelValue="updateStopValue(prop.key, idx, $event)"
          />
          <span class="mb-text-[9px] mb-text-gray-500">@</span>
          <NumberScrubber
            :modelValue="stop.position"
            :min="0" :max="100" :step="1"
            :defaultValue="0"
            emitAs="number"
            unit="%"
            :ariaLabel="t('Posizione stop')"
            @update:modelValue="updateStopPosition(prop.key, idx, $event)"
          />
          <button
            v-if="idx > 0 && idx < getStops(prop.key).length - 1"
            @click="removeStop(prop.key, idx)"
            class="mb-text-[12px] mb-text-red-400 hover:mb-text-red-300 mb-px-1"
            :title="t('Rimuovi stop')"
          >&times;</button>
          <span v-else class="mb-w-3"></span>
        </div>
      </div>
    </div>

    <!-- Advanced options -->
    <details class="mb-border mb-border-gray-600 mb-rounded-md">
      <summary class="mb-px-2 mb-py-1 mb-text-[10px] mb-font-medium mb-text-gray-400 mb-cursor-pointer hover:mb-text-gray-300">
        Opzioni avanzate
      </summary>
      <div class="mb-px-2 mb-py-2 mb-space-y-2 mb-border-t mb-border-gray-600">
        <!-- Start viewport -->
        <div>
          <label class="mb-block mb-text-[9px] mb-text-gray-500 mb-mb-0.5">{{ t('Start viewport') }}</label>
          <input
            type="text"
            :value="data.start || ''"
            @change="updateOption('start', $event.target.value)"
            :placeholder="t('es. 100%, 50vh')"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
          />
        </div>
        <!-- End viewport -->
        <div>
          <label class="mb-block mb-text-[9px] mb-text-gray-500 mb-mb-0.5">{{ t('End viewport') }}</label>
          <input
            type="text"
            :value="data.end || ''"
            @change="updateOption('end', $event.target.value)"
            :placeholder="t('es. 100%, 50vh')"
            class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-0.5 mb-text-[11px] mb-text-gray-900"
          />
        </div>
        <!-- Easing -->
        <div>
          <label class="mb-block mb-text-[9px] mb-text-gray-500 mb-mb-0.5">{{ t('Easing') }}</label>
          <div class="mb-flex mb-items-center mb-gap-2">
            <NumberScrubber
              class="mb-flex-1 mb-min-w-0"
              :modelValue="data.easing ?? 0"
              :min="0" :max="2" :step="0.1"
              :defaultValue="0"
              emitAs="number"
              :sliderOnFocus="false"
              :ariaLabel="t('Easing')"
              @update:modelValue="updateOption('easing', $event)"
            />
            <span class="mb-text-[10px] mb-text-gray-400 mb-w-6 mb-text-right">{{ data.easing != null ? data.easing : '-' }}</span>
          </div>
        </div>
      </div>
    </details>

    <!-- Disattiva su mobile: fuori dalle «Opzioni avanzate» — decide se
         l'effetto esiste sui telefoni, deve essere visibile subito. -->
    <div>
      <label class="mb-flex mb-items-center mb-gap-2 mb-cursor-pointer">
        <button
          @click="updateOption('nomobile', !(data.nomobile === true))"
          :class="[
            'mb-relative mb-w-8 mb-h-4 mb-rounded-full mb-transition-colors mb-shrink-0',
            (data.nomobile === true) ? 'mb-bg-primary-600' : 'mb-bg-gray-600'
          ]"
        >
          <span
            :class="[
              'mb-absolute mb-top-0.5 mb-w-3 mb-h-3 mb-rounded-full mb-bg-white mb-transition-transform',
              (data.nomobile === true) ? 'mb-left-4' : 'mb-left-0.5'
            ]"
          ></span>
        </button>
        <span class="mb-text-[10px] mb-text-gray-400">{{ t('Disattiva su mobile') }}</span>
      </label>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import NumberScrubber from './fields/NumberScrubber.vue';

const props = defineProps({
  modelValue: { type: Object, default: () => ({}) },
  properties: { type: Array, required: true },
});

const emit = defineEmits(['update:modelValue']);

const data = computed(() => props.modelValue || {});

function getStops(key) {
  const val = data.value[key];
  return Array.isArray(val) ? val : [];
}

function emitUpdate(patch) {
  emit('update:modelValue', { ...data.value, ...patch });
}

function addStop(key, prop) {
  const current = [...getStops(key)];
  if (current.length === 0) {
    // First add: create 2 stops (start at 0%, end at 100%)
    const defaultStart = prop.key === 'opacity' ? 1 : (prop.key === 'scale' ? 1 : 0);
    const defaultEnd = prop.key === 'opacity' ? 1 : (prop.key === 'scale' ? 1 : 0);
    emitUpdate({ [key]: [
      { value: defaultStart, position: 0 },
      { value: defaultEnd, position: 100 },
    ]});
  } else {
    // Add intermediate stop: insert before last, at midpoint
    const lastIdx = current.length - 1;
    const prevPos = current[lastIdx - 1].position;
    const lastPos = current[lastIdx].position;
    const midPos = Math.round((prevPos + lastPos) / 2);
    const midVal = Math.round(((current[lastIdx - 1].value + current[lastIdx].value) / 2) / (prop.step || 1)) * (prop.step || 1);
    const newStop = { value: parseFloat(midVal.toFixed(4)), position: midPos };
    const updated = [...current];
    updated.splice(lastIdx, 0, newStop);
    emitUpdate({ [key]: updated });
  }
}

function clearStops(key) {
  emitUpdate({ [key]: [] });
}

function updateStopValue(key, idx, num) {
  // num è già un Number (NumberScrubber emitAs='number'); parseFloat è innocuo.
  const val = parseFloat(num) || 0;
  const stops = [...getStops(key)];
  stops[idx] = { ...stops[idx], value: parseFloat(val.toFixed(4)) };
  emitUpdate({ [key]: stops });
}

function updateStopPosition(key, idx, num) {
  // num è già un Number (NumberScrubber emitAs='number'); parseInt è innocuo.
  const pos = Math.max(0, Math.min(100, parseInt(num) || 0));
  const stops = [...getStops(key)];
  stops[idx] = { ...stops[idx], position: pos };
  // Sort all stops by position
  stops.sort((a, b) => a.position - b.position);
  emitUpdate({ [key]: stops });
}

function removeStop(key, idx) {
  const stops = [...getStops(key)];
  stops.splice(idx, 1);
  emitUpdate({ [key]: stops });
}

function updateOption(optKey, value) {
  emitUpdate({ [optKey]: value });
}
</script>