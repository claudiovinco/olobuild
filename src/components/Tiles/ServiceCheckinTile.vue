<template>
  <div :style="barStyle">
    <strong :style="{ color: s.label_color || 'var(--olo-color-text, #374151)' }">Check-in:</strong> dalle 15:00
    &nbsp;| &nbsp;<strong :style="{ color: s.label_color || 'var(--olo-color-text, #374151)' }">Check-out:</strong> entro 10:00
    <br><strong :style="{ color: s.label_color || 'var(--olo-color-text, #374151)' }">Soggiorno minimo:</strong> 2 notti
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  bg_color: '', text_color: '', label_color: '',
  border_color: '#E5E7EB', border_radius: '12', padding: '16', font_size: '14',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const barStyle = computed(() => ({
  background: s.value.bg_color || 'var(--olo-color-muted, #F3F4F6)',
  color: s.value.text_color || 'var(--olo-color-text, #374151)',
  border: '1px solid ' + s.value.border_color,
  borderRadius: s.value.border_radius + 'px',
  padding: s.value.padding + 'px',
  fontSize: s.value.font_size + 'px',
}));
</script>
