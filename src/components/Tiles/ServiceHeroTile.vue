<template>
  <div :style="wrapStyle">
    <!-- Image placeholder with Ken Burns preview -->
    <div :style="imgStyle"></div>
    <!-- Overlay -->
    <div :style="overlayStyle"></div>
    <!-- Tint -->
    <div v-if="s.fx_tint" :style="tintStyle"></div>
    <!-- Shimmer -->
    <div v-if="s.fx_shimmer" :style="shimmerStyle"></div>
    <!-- Vignette -->
    <div v-if="s.fx_vignette" :style="vignetteStyle"></div>
    <!-- Grain -->
    <div v-if="s.fx_grain" style="position:absolute;inset:0;opacity:0.06;pointer-events:none;background:repeating-conic-gradient(#888 0% 25%, transparent 0% 50%) 0 0/4px 4px;mix-blend-mode:overlay;z-index:2"></div>

    <!-- Badges -->
    <div v-if="s.show_badges || s.show_opening" style="position:absolute;top:10px;right:10px;display:flex;gap:5px;z-index:3;flex-wrap:wrap;justify-content:flex-end">
      <span v-if="s.show_opening" :style="openingBadgeStyle">{{ t('Apertura annuale') }}</span>
      <span v-if="s.show_badges" :style="badgeStyle" style="display:inline-flex;align-items:center;gap:3px"><span class="olo-svhero-badge-ico" v-html="pinSvg"></span>{{ t('Località') }}</span>
      <span v-if="s.show_badges" :style="badgeStyle">{{ t('1900 m') }}</span>
    </div>

    <!-- Title -->
    <div v-if="s.show_title" :style="titleStyle">{{ t('Nome Baita / Servizio') }}</div>

    <!-- Effect indicators -->
    <div v-if="activeEffects.length" style="position:absolute;bottom:6px;right:8px;z-index:4;display:flex;gap:3px">
      <span v-for="fx in activeEffects" :key="fx" style="background:rgba(0,0,0,0.5);color:var(--olo-color-on-primary, #ffffff);padding:1px 5px;border-radius:8px;font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.3px">{{ fx }}</span>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const pinSvg = iconsSvg['map-pin'] || iconsSvg['location'] || '';

const defaults = {
  hero_height: '400', hero_radius: '16', hero_overlay: 'gradient',
  overlay_color: '#000000', overlay_opacity: '45',
  show_badges: true, badge_bg: 'rgba(22,38,61,0.78)', badge_color: '#FFFFFF',
  show_title: true, title_size: '30', title_color: '#FFFFFF', title_position: 'bottom',
  // badge apertura → semantico success (era #059669 hardcoded)
  show_opening: true, opening_bg: 'var(--olo-color-success, #15803d)', opening_color: '#FFFFFF',
  fx_kenburns: false, fx_shimmer: false, fx_hover_zoom: true, fx_hover_tilt: false,
  fx_vignette: false, fx_vignette_strength: '50', fx_grain: false,
  // tint creativo default → navy brand (era #1E3A5F hardcoded)
  fx_tint: false, fx_tint_color: 'var(--olo-color-secondary, #16263d)', fx_tint_opacity: '15', fx_tint_blend: 'multiply',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const wrapStyle = computed(() => ({
  position: 'relative',
  height: Math.min(parseInt(s.value.hero_height) || 400, 250) + 'px',
  borderRadius: s.value.hero_radius + 'px',
  overflow: 'hidden',
}));

const imgStyle = computed(() => ({
  position: 'absolute', inset: '0',
  // placeholder hero elegante: brand → navy secondario (no più teal off-brand)
  background: 'linear-gradient(135deg, var(--olo-color-primary, #e1474f) 0%, var(--olo-color-secondary, #16263d) 100%)',
  animation: s.value.fx_kenburns ? 'olo-kb-preview 8s ease-in-out infinite' : 'none',
}));

const overlayStyle = computed(() => {
  const o = s.value.hero_overlay;
  let bg = 'transparent';
  if (o === 'gradient') bg = 'linear-gradient(to bottom,rgba(0,0,0,0.02) 40%,rgba(0,0,0,0.45) 100%)';
  else if (o === 'dark') bg = 'rgba(0,0,0,0.4)';
  else if (o === 'radial') bg = 'radial-gradient(ellipse at center,rgba(0,0,0,0) 30%,rgba(0,0,0,0.5) 100%)';
  return { position: 'absolute', inset: '0', background: bg, zIndex: '1', pointerEvents: 'none' };
});

const tintStyle = computed(() => ({
  position: 'absolute', inset: '0', zIndex: '1', pointerEvents: 'none',
  background: s.value.fx_tint_color,
  opacity: parseInt(s.value.fx_tint_opacity || 15) / 100,
  mixBlendMode: s.value.fx_tint_blend || 'multiply',
}));

const shimmerStyle = computed(() => ({
  position: 'absolute', inset: '-50%', zIndex: '2', pointerEvents: 'none',
  background: 'linear-gradient(90deg,transparent 30%,rgba(255,255,255,0.1) 50%,transparent 70%)',
  animation: 'olo-shimmer-preview 4s ease-in-out infinite',
}));

const vignetteStyle = computed(() => {
  const str = parseInt(s.value.fx_vignette_strength || 50);
  return {
    position: 'absolute', inset: '0', zIndex: '1', pointerEvents: 'none',
    boxShadow: `inset 0 0 ${str}px ${str / 2}px rgba(0,0,0,0.4)`,
    borderRadius: s.value.hero_radius + 'px',
  };
});

const openingBadgeStyle = computed(() => ({
  background: s.value.opening_bg, color: s.value.opening_color,
  padding: '3px 8px', borderRadius: '12px',
  fontSize: '8px', fontWeight: '700',
}));

const badgeStyle = computed(() => ({
  background: s.value.badge_bg, color: s.value.badge_color,
  padding: '3px 8px', borderRadius: '12px',
  fontSize: '8px', fontWeight: '600',
}));

const titleStyle = computed(() => {
  const base = {
    position: 'absolute', zIndex: '3',
    fontSize: Math.min(parseInt(s.value.title_size) || 30, 22) + 'px',
    fontWeight: '700', color: s.value.title_color, margin: '0', lineHeight: '1.2',
    textShadow: '0 2px 12px rgba(0,0,0,0.4)',
  };
  if (s.value.title_position === 'center') {
    return { ...base, top: '50%', left: '50%', transform: 'translate(-50%,-50%)', textAlign: 'center', width: '80%' };
  }
  return { ...base, bottom: '14px', left: '14px', right: '14px' };
});

const activeEffects = computed(() => {
  const list = [];
  if (s.value.fx_kenburns) list.push('KB');
  if (s.value.fx_shimmer) list.push('Shimmer');
  if (s.value.fx_hover_zoom) list.push('Zoom');
  if (s.value.fx_hover_tilt) list.push('Tilt');
  if (s.value.fx_vignette) list.push('Vig');
  if (s.value.fx_grain) list.push('Grain');
  if (s.value.fx_tint) list.push('Tint');
  return list;
});
</script>

<style scoped>
.olo-svhero-badge-ico { display: inline-flex; }
.olo-svhero-badge-ico :deep(svg) { width: 11px; height: 11px; stroke: currentColor; fill: none; }
@keyframes olo-kb-preview {
  0%   { transform: scale(1); }
  50%  { transform: scale(1.08) translate(-1%, -0.5%); }
  100% { transform: scale(1); }
}
@keyframes olo-shimmer-preview {
  0%   { transform: translateX(-100%) rotate(25deg); }
  100% { transform: translateX(200%) rotate(25deg); }
}
</style>
