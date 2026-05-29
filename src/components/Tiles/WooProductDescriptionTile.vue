<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><circle cx="7.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><circle cx="13.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><polyline points="0 2 3.2 4 5.3 12.5 16 12.5 18 6.5 8 6.5"/></svg></span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else :style="descStyle">
      <p v-if="s.content_type === 'short'" style="margin:0;">
        {{ t('Questa maglietta in cotone biologico combina comfort e stile, perfetta per ogni occasione.') }}
      </p>
      <template v-else>
        <p style="margin:0 0 1em;">
          {{ t('Questa maglietta premium in cotone biologico al 100% offre una vestibilita comoda e un tessuto traspirante, perfetta per l\'uso quotidiano. Il design minimalista la rende versatile e adatta a qualsiasi stile.') }}
        </p>
        <p style="margin:0;">
          {{ t('Disponibile in diverse taglie e colori. Lavabile in lavatrice a 30 gradi. Prodotto in modo sostenibile nel rispetto dell\'ambiente.') }}
        </p>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  content_type: 'full',
  text_color: '',
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
    color: s.value.text_color || 'var(--olo-color-text, #1f2937)',
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
  background: color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);
  border: 1px solid var(--olo-color-warning, #b45309);
  border-radius: 8px;
  color: var(--olo-color-warning, #b45309);
  font-size: 14px;
  font-weight: 500;
}
.olo-woo-notice-icon {
  width: 20px;
  height: 20px;
  display: inline-flex;
  flex-shrink: 0;
}
.olo-woo-notice-icon :deep(svg) {
  width: 100%;
  height: 100%;
}
</style>
