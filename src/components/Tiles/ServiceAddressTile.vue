<template>
  <div :style="wrapStyle">
    <span v-if="s.show_icon" style="display:flex;flex-shrink:0;margin-top:2px">
      <svg :width="iconSz" :height="iconSz" viewBox="0 0 24 24" fill="none">
        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" :stroke="iconC" stroke-width="1.8" :fill="iconC" fill-opacity="0.15"/>
        <circle cx="12" cy="9" r="2.5" :stroke="iconC" stroke-width="1.8" :fill="iconC" fill-opacity="0.3"/>
      </svg>
    </span>
    <div style="display:flex;flex-direction:column;gap:2px">
      <span v-if="s.show_label" :style="labelStyle">{{ s.label_text || 'Indirizzo' }}</span>
      <span :style="textStyle">{{ t('Via Roma 12, 38032 Canazei (TN)') }}</span>
      <span v-if="s.show_locality" :style="{fontSize:(fs-1)+'px',color:s.label_color || 'var(--olo-color-text-muted, #9CA3AF)'}">{{ t('Val di Fassa') }}</span>
      <span v-if="s.show_map_link" :style="{fontSize:(fs-2)+'px',color:s.link_color || 'var(--olo-color-primary, #e1474f)',marginTop:'3px'}">{{ s.map_link_text || 'Apri in Google Maps' }} ↗</span>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = {
  show_icon: true, show_label: true, label_text: 'Indirizzo', show_locality: true,
  show_map_link: true, map_link_text: 'Apri in Google Maps',
  font_size: '15', label_color: '', text_color: '', icon_color: '', link_color: '',
  bg_color: '', border_color: '', border_radius: '8', padding: '16',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const fs = computed(() => Math.min(parseInt(s.value.font_size) || 15, 18));
const iconSz = computed(() => fs.value + 4);
// icona pin indirizzo: token-first sul brand (era #EF4444 off-brand)
const iconC = computed(() => s.value.icon_color || 'var(--olo-color-primary, #e1474f)');
const wrapStyle = computed(() => {
  const st = { display: 'flex', alignItems: 'flex-start', gap: '8px' };
  if (s.value.bg_color) st.background = s.value.bg_color;
  if (s.value.border_color) st.border = '1px solid ' + s.value.border_color;
  if (parseInt(s.value.border_radius)) st.borderRadius = s.value.border_radius + 'px';
  if (parseInt(s.value.padding)) st.padding = s.value.padding + 'px';
  return st;
});
const labelStyle = computed(() => ({ fontSize: (fs.value - 3) + 'px', color: s.value.label_color || 'var(--olo-color-text-muted, #9CA3AF)', fontWeight: '600', textTransform: 'uppercase', letterSpacing: '0.5px' }));
const textStyle = computed(() => ({ fontSize: fs.value + 'px', color: s.value.text_color || 'var(--olo-color-text, #374151)', lineHeight: '1.4' }));
</script>
