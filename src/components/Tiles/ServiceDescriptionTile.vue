<template>
  <div :style="wrapStyle">
    <h3 v-if="s.show_title && s.title_text" :style="{margin:'0 0 12px',fontSize:titleSz+'px',fontWeight:'700',color:s.title_color || 'var(--olo-color-text, #374151)'}">{{ s.title_text }}</h3>
    <div :style="{fontSize:textSz+'px',color:s.text_color || 'var(--olo-color-text, #374151)',lineHeight:s.line_height||'1.7',textAlign:s.text_align||'left'}">
      <p style="margin:0 0 1em">{{ t('Immersa nel cuore delle Dolomiti, questa baita offre un rifugio autentico dove natura e comfort si fondono armoniosamente. Costruita in legno e pietra locale, la struttura conserva il fascino della tradizione alpina.') }}</p>
      <p style="margin:0">{{ t('Gli interni sono stati recentemente rinnovati con materiali naturali e arredi di design, creando un\'atmosfera calda e accogliente. La posizione privilegiata permette di godere di panorami mozzafiato sulle vette circostanti.') }}</p>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = {
  show_title: true, title_text: 'La struttura', title_size: '20', text_size: '15',
  title_color: '', text_color: '',
  line_height: '1.7', text_align: 'left', max_width: '0',
  bg_color: '', border_color: '', border_radius: '0', padding: '0',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const titleSz = computed(() => Math.min(parseInt(s.value.title_size) || 20, 22));
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
