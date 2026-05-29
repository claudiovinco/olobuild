<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><circle cx="7.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><circle cx="13.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><polyline points="0 2 3.2 4 5.3 12.5 16 12.5 18 6.5 8 6.5"/></svg></span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else :style="wrapStyle">
      <span :style="linkStyle">{{ t('Home') }}</span>
      <span :style="sepStyle">{{ sepChar }}</span>
      <span :style="linkStyle">{{ t('Shop') }}</span>
      <span :style="sepStyle">{{ sepChar }}</span>
      <span :style="linkStyle">{{ t('Categoria') }}</span>
      <span :style="sepStyle">{{ sepChar }}</span>
      <span :style="textStyle">{{ t('Prodotto esempio') }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  separator: '/',
  text_color: '',           // '' ⇒ TOKENS.textSoft
  link_color: '',           // '' ⇒ var(--olo-color-link) (link breadcrumb)
  font_size: '14',
  alignment: 'left',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const wooActive = computed(() => true);

const textColor = computed(() => resolveColor(s.value.text_color, TOKENS.textSoft));
const linkColor = computed(() => resolveColor(s.value.link_color, 'var(--olo-color-link, #e1474f)'));

const sepMap = {
  '/': '/',
  '>': '>',
  '-': '-',
  '>>': '\u00BB',
};
const sepChar = computed(() => sepMap[s.value.separator] || '/');

const wrapStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  gap: '6px',
  flexWrap: 'wrap',
  fontSize: (parseInt(s.value.font_size) || 14) + 'px',
  padding: '8px 0',
  justifyContent: s.value.alignment === 'center' ? 'center' : s.value.alignment === 'right' ? 'flex-end' : 'flex-start',
}));

const textStyle = computed(() => ({
  color: textColor.value,
}));

const linkStyle = computed(() => ({
  color: linkColor.value,
  textDecoration: 'none',
  cursor: 'pointer',
}));

const sepStyle = computed(() => ({
  color: textColor.value,
  opacity: 0.5,
}));
</script>

<style scoped>
.olo-woo-notice { display:flex;align-items:center;gap:8px;padding:16px 20px;background:color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);border:1px solid var(--olo-color-warning, #b45309);border-radius:8px;color:var(--olo-color-warning, #b45309);font-size:14px;font-weight:500 }
.olo-woo-notice-icon { width:20px;height:20px;display:inline-flex;flex-shrink:0 } .olo-woo-notice-icon :deep(svg) { width:100%;height:100% }
</style>
