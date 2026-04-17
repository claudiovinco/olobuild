<template>
  <div class="mb-font-sans">
    <p :style="textStyle">Trilocale luminoso con terrazzo panoramico, completamente ristrutturato, situato in posizione strategica nel centro storico. Ideale come prima casa o investimento.</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { font_size: 15, text_color: '#6B7280', line_height: 1.7 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const textStyle = computed(() => ({ fontSize: s.value.font_size+'px', color: s.value.text_color, lineHeight: s.value.line_height, fontStyle:'italic', margin:0 }));
</script>
