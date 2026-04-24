<template>
  <div :style="wrapStyle">
    <h3 v-if="s.show_title && s.title_text" :style="{margin:'0 0 8px',fontSize:titleSz+'px',fontWeight:'700',color:s.title_color || 'var(--olo-color-text, #374151)'}">{{ s.title_text }}</h3>
    <p :style="{margin:0,fontSize:textSz+'px',color:s.text_color || 'var(--olo-color-text-muted, #9CA3AF)',lineHeight:s.line_height||'1.7',fontStyle:s.font_style||'italic',textAlign:s.text_align||'left'}">
      {{ t('Una breve descrizione della struttura che cattura l\'essenza del luogo e invita il visitatore a scoprire di pi&ugrave;.') }}
    </p>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = {
  show_title: false, title_text: '', title_size: '16', text_size: '15',
  title_color: '', text_color: '', font_style: 'italic',
  line_height: '1.7', text_align: 'left', max_width: '0',
  bg_color: '', border_color: '', border_radius: '0', padding: '0',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const titleSz = computed(() => Math.min(parseInt(s.value.title_size) || 16, 20));
const textSz = computed(() => Math.min(parseInt(s.value.text_size) || 15, 16));
const wrapStyle = computed(() => {
  const st = {};
  if (s.value.bg_color) st.background = s.value.bg_color;
  if (s.value.border_color) st.border = '1px solid ' + s.value.border_color;
  if (parseInt(s.value.border_radius)) st.borderRadius = s.value.border_radius + 'px';
  if (parseInt(s.value.padding)) st.padding = s.value.padding + 'px';
  if (parseInt(s.value.max_width)) st.maxWidth = s.value.max_width + 'px';
  st.textAlign = s.value.text_align || 'left';
  return st;
});
</script>
