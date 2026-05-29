<template>
  <div :style="wrapStyle">
    <div v-if="s.show_title" style="display:flex;align-items:center;gap:6px;margin-bottom:6px">
      <svg v-if="s.show_icon" :width="titleSz+2" :height="titleSz+2" viewBox="0 0 24 24" fill="none">
        <path d="M12 2L4.5 20.3l.7.7L12 18l6.8 3 .7-.7L12 2z" :stroke="iconC" stroke-width="1.8" stroke-linejoin="round" :fill="iconC" fill-opacity="0.12"/>
      </svg>
      <span :style="{fontSize:titleSz+'px',fontWeight:'700',color:s.title_color || 'var(--olo-color-text, #374151)'}">{{ s.title_text || 'Come arrivare' }}</span>
    </div>
    <div :style="{fontSize:textSz+'px',color:s.text_color || 'var(--olo-color-text, #374151)',lineHeight:'1.6'}">
      {{ t('Da Trento: prendere la SS47 in direzione Valsugana, uscita Val di Fassa. Proseguire sulla SS48 per circa 30 km fino a destinazione. Parcheggio gratuito disponibile.') }}
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = {
  show_icon: true, show_title: true, title_text: 'Come arrivare',
  title_size: '18', text_size: '14', title_color: '', text_color: '', icon_color: '',
  bg_color: '', border_color: '', border_radius: '8', padding: '16',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
// icona "come arrivare": token-first sul brand (era #e1474f indaco off-brand)
const iconC = computed(() => s.value.icon_color || 'var(--olo-color-primary, #e1474f)');
const titleSz = computed(() => Math.min(parseInt(s.value.title_size) || 18, 20));
const textSz = computed(() => Math.min(parseInt(s.value.text_size) || 14, 15));
const wrapStyle = computed(() => {
  const st = {};
  if (s.value.bg_color) st.background = s.value.bg_color;
  if (s.value.border_color) st.border = '1px solid ' + s.value.border_color;
  if (parseInt(s.value.border_radius)) st.borderRadius = s.value.border_radius + 'px';
  if (parseInt(s.value.padding)) st.padding = s.value.padding + 'px';
  return st;
});
</script>
