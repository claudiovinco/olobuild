<template>
  <!--
    FieldRange — ora è un sottile wrapper su NumberScrubber.
    Contratto INVARIATO: emette il valore come STRINGA RAW (emitAs='string'), il
    backend PHP fa parseInt se numerico; doppio-click = reset al defaultValue
    (verbatim, incluso '' = eredita). `compact` decide la resa: compatto con
    slider in popover (layout inline dell'inspector) oppure slider inline classico.
  -->
  <NumberScrubber
    :modelValue="modelValue"
    :min="min"
    :max="max"
    :step="step"
    :defaultValue="defaultValue"
    :placeholder="placeholder"
    emitAs="string"
    :sliderOnFocus="compact"
    @update:modelValue="$emit('update:modelValue', $event)"
  />
</template>

<script setup>
import NumberScrubber from './NumberScrubber.vue';

defineProps({
  modelValue: { type: [String, Number], default: 0 },
  min: { type: [Number, String], default: 0 },
  max: { type: [Number, String], default: 100 },
  step: { type: [Number, String], default: 1 },
  defaultValue: { type: [String, Number, null], default: null },
  placeholder: { type: String, default: '' },
  // true → valbox compatta + slider in popover (layout inline); false → slider inline classico
  compact: { type: Boolean, default: false },
});
defineEmits(['update:modelValue']);
</script>
