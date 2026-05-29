<template>
  <div
    class="mb-flex mb-items-center mb-gap-2 mb-py-3 mb-px-4"
    :style="containerStyle"
  >
    <span
      v-if="s.show_icon"
      :style="{ color: resolveColor(s.icon_color || s.text_color, TOKENS.textFaint), fontSize: s.font_size || '16px' }"
    >
      <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <polyline points="12 6 12 12 16 14"/>
      </svg>
    </span>
    <span :style="textStyle">{{ displayText }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  words_per_minute: 200,
  format: 'full',
  prefix: 'Tempo di lettura:',
  suffix: 'min',
  icon: 'clock',
  show_icon: true,
  text_color: '',
  icon_color: '',
  font_size: '',
  font_weight: '',
  text_align: 'left',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const displayText = computed(() => {
  const mins = 5; // placeholder in builder
  const fmt = s.value.format || 'full';
  if (fmt === 'minutes_only') return String(mins);
  if (fmt === 'short') return mins + ' ' + (s.value.suffix || 'min') + ' di lettura';
  return (s.value.prefix || 'Tempo di lettura:') + ' ' + mins + ' ' + (s.value.suffix || 'min');
});

const containerStyle = computed(() => ({
  textAlign: s.value.text_align || 'left',
  justifyContent: s.value.text_align === 'center' ? 'center' : s.value.text_align === 'right' ? 'flex-end' : 'flex-start',
  minHeight: '40px',
}));

const textStyle = computed(() => {
  const st = {};
  if (s.value.text_color) st.color = s.value.text_color;
  if (s.value.font_size) st.fontSize = s.value.font_size;
  if (s.value.font_weight) st.fontWeight = s.value.font_weight;
  return st;
});
</script>
