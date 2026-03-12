<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon">&#x1F6D2;</span>
      <span>WooCommerce richiesto</span>
    </div>
    <div v-else :style="descStyle">
      <p v-if="s.content_type === 'short'" style="margin:0;">
        Questa maglietta in cotone biologico combina comfort e stile, perfetta per ogni occasione.
      </p>
      <template v-else>
        <p style="margin:0 0 1em;">
          Questa maglietta premium in cotone biologico al 100% offre una vestibilita comoda e un tessuto traspirante, perfetta per l'uso quotidiano. Il design minimalista la rende versatile e adatta a qualsiasi stile.
        </p>
        <p style="margin:0;">
          Disponibile in diverse taglie e colori. Lavabile in lavatrice a 30 gradi. Prodotto in modo sostenibile nel rispetto dell'ambiente.
        </p>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  content_type: 'full',
  text_color: '#374151',
  font_size: '16',
  line_height: '1.6',
  text_align: 'left',
  max_lines: '0',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const wooActive = computed(() => true);

const maxLines = computed(() => parseInt(s.value.max_lines) || 0);

const descStyle = computed(() => {
  const st = {
    color: s.value.text_color || '#374151',
    fontSize: parseInt(s.value.font_size) + 'px',
    lineHeight: s.value.line_height || '1.6',
    textAlign: s.value.text_align || 'left',
  };
  if (maxLines.value > 0) {
    st.display = '-webkit-box';
    st.webkitLineClamp = maxLines.value;
    st.webkitBoxOrient = 'vertical';
    st.overflow = 'hidden';
  }
  return st;
});
</script>

<style scoped>
.olo-woo-notice {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 16px 20px;
  background: #FEF3C7;
  border: 1px solid #F59E0B;
  border-radius: 8px;
  color: #92400E;
  font-size: 14px;
  font-weight: 500;
}
.olo-woo-notice-icon {
  font-size: 20px;
}
</style>
