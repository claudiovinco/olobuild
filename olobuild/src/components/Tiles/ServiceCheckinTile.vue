<template>
  <div :style="barStyle">
    <strong :style="{ color: s.label_color }">Check-in:</strong> dalle 15:00
    &nbsp;| &nbsp;<strong :style="{ color: s.label_color }">Check-out:</strong> entro 10:00
    <br><strong :style="{ color: s.label_color }">Soggiorno minimo:</strong> 2 notti
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  bg_color: '#F9FAFB', text_color: '#374151', label_color: '#1F2937',
  border_color: '#E5E7EB', border_radius: '12', padding: '16', font_size: '14',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const barStyle = computed(() => ({
  background: s.value.bg_color,
  color: s.value.text_color,
  border: '1px solid ' + s.value.border_color,
  borderRadius: s.value.border_radius + 'px',
  padding: s.value.padding + 'px',
  fontSize: s.value.font_size + 'px',
}));
</script>
