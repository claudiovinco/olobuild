<template>
  <div :style="wrapStyle">
    <div v-if="s.show_label && s.label_position === 'top'" :style="labelStyle" style="margin-bottom:4px">
      {{ s.label_text }}
    </div>
    <div style="display:flex;align-items:center" :style="{ gap: s.gap + 'px' }">
      <svg v-for="i in 5" :key="i" :width="sz" :height="sz" viewBox="0 0 24 24" fill="none">
        <path d="M12 2C8 2 4 5.5 4 9.5C4 13 7 14.5 7 14.5L7.5 20C7.5 20.5 8 21 8.5 21H15.5C16 21 16.5 20.5 16.5 20L17 14.5C17 14.5 20 13 20 9.5C20 5.5 16 2 12 2Z"
              :fill="i <= 3 ? s.active_color : s.inactive_color" />
      </svg>
      <span v-if="s.style === 'with-number'" :style="{ fontSize: sz+'px', fontWeight:'700', color: s.active_color, marginLeft:'4px' }">3/5</span>
    </div>
    <div v-if="s.show_label && s.label_position === 'bottom'" :style="labelStyle" style="margin-top:4px">
      {{ s.label_text }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  style: 'inline', size: '28', gap: '4', active_color: '#D97706', inactive_color: '#E5E7EB',
  show_label: true, label_text: 'Classificazione comfort', label_size: '13',
  label_color: '#6B7280', label_position: 'top', align: 'left',
  bg_color: '', border_radius: '0', padding: '0',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const sz = computed(() => Math.min(parseInt(s.value.size) || 28, 36));

const alignMap = { left: 'flex-start', center: 'center', right: 'flex-end' };
const wrapStyle = computed(() => {
  const st = { display: 'flex', flexDirection: 'column', alignItems: alignMap[s.value.align] || 'flex-start' };
  if (s.value.bg_color) st.background = s.value.bg_color;
  if (parseInt(s.value.border_radius)) st.borderRadius = s.value.border_radius + 'px';
  if (parseInt(s.value.padding)) st.padding = s.value.padding + 'px';
  return st;
});

const labelStyle = computed(() => ({
  fontSize: Math.min(parseInt(s.value.label_size) || 13, 16) + 'px',
  color: s.value.label_color, fontWeight: '600',
}));
</script>
