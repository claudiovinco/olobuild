<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon" v-html="cartIcon"></span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else :style="wrapStyle">
      <span :style="badgeStyle" data-olo-editable="custom_text">{{ badgeLabel }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  badge_text: 'auto',
  custom_text: 'Offerta!',
  badge_bg: '',             // '' ⇒ TOKENS.primary (badge = brand)
  badge_color: '',          // '' ⇒ TOKENS.onPrimary
  badge_shape: 'pill',
  position: 'top-left',
  font_size: '14',
  font_weight: '700',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const wooActive = computed(() => true);
const cartIcon = computed(() => iconsSvg['cart'] || '');

const badgeLabel = computed(() => {
  if (s.value.badge_text === 'custom') {
    return s.value.custom_text || 'Offerta!';
  }
  // auto or % — show mock percentage
  return '-25%';
});

const shapeRadius = computed(() => {
  const map = { circle: '50%', pill: '999px', rectangle: '4px' };
  return map[s.value.badge_shape] || '999px';
});

const fontSize = computed(() => parseInt(s.value.font_size) || 14);
const isCircle = computed(() => s.value.badge_shape === 'circle');

const wrapStyle = computed(() => ({
  display: 'inline-flex',
}));

const badgeStyle = computed(() => {
  const base = {
    background: resolveColor(s.value.badge_bg, TOKENS.primary),
    color: resolveColor(s.value.badge_color, TOKENS.onPrimary),
    fontSize: fontSize.value + 'px',
    fontWeight: s.value.font_weight || '700',
    borderRadius: shapeRadius.value,
    lineHeight: 1,
    textAlign: 'center',
    whiteSpace: 'nowrap',
  };
  if (isCircle.value) {
    base.width = (fontSize.value * 3) + 'px';
    base.height = (fontSize.value * 3) + 'px';
    base.display = 'flex';
    base.alignItems = 'center';
    base.justifyContent = 'center';
  } else {
    base.padding = `${Math.round(fontSize.value * 0.4)}px ${Math.round(fontSize.value * 0.8)}px`;
    base.display = 'inline-flex';
    base.alignItems = 'center';
    base.justifyContent = 'center';
  }
  return base;
});
</script>

<style scoped>
.olo-woo-notice { display:flex;align-items:center;gap:8px;padding:16px 20px;background:color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);border:1px solid var(--olo-color-warning, #b45309);border-radius:8px;color:var(--olo-color-warning, #b45309);font-size:14px;font-weight:500 }
.olo-woo-notice-icon { width:20px;height:20px;display:inline-flex;flex-shrink:0 } .olo-woo-notice-icon :deep(svg) { width:100%;height:100% }
</style>
