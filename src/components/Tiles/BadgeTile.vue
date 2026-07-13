<template>
  <div class="olo-badge-wrap" :style="wrapStyle">
    <span class="olo-badge" :style="badgeStyle">
      <span
        v-if="s.badge_live"
        class="olo-live-dot"
        :class="{ 'is-brand': s.badge_live_color === 'primary' }"
        aria-hidden="true"
      ></span>
      <span v-if="iconSvg && s.icon_position !== 'after'" class="olo-badge-icon" v-html="iconSvg"></span>
      <span class="olo-badge-text" data-olo-editable="text">{{ s.text || 'Badge' }}</span>
      <span v-if="iconSvg && s.icon_position === 'after'" class="olo-badge-icon" v-html="iconSvg"></span>
    </span>
    <span v-for="(it, i) in extraItems" :key="i" class="olo-badge" :style="extraStyle(it)">
      <span class="olo-badge-text">{{ it.text }}</span>
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  text: 'Online',
  icon: '',
  icon_position: 'before',
  badge_live: false,
  badge_live_color: 'success',
  variant: 'soft',
  bg_color: '',
  text_color: '',
  font_size: '13',
  font_weight: '600',
  text_transform: 'none',
  letter_spacing: '0',
  badge_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
  padding_y: 7,
  padding_x: 13,
  alignment: 'left',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const accent = computed(() => resolveColor(s.value.bg_color, TOKENS.primary));
const txt    = computed(() => resolveColor(s.value.text_color, TOKENS.text));

function radiusCss(r) {
  if (r && typeof r === 'object') return `${r.tl ?? 0}px ${r.tr ?? 0}px ${r.br ?? 0}px ${r.bl ?? 0}px`;
  const n = parseInt(r); return isNaN(n) ? '999px' : `${n}px`;
}

const extraItems = computed(() =>
  (Array.isArray(s.value.extra_items) ? s.value.extra_items : []).filter((it) => (it.text || '').trim()));

const wrapStyle = computed(() => ({
  display: 'flex',
  justifyContent: s.value.alignment === 'center' ? 'center' : s.value.alignment === 'right' ? 'flex-end' : 'flex-start',
  ...(extraItems.value.length ? { flexWrap: 'wrap', gap: '8px' } : {}),
}));

// Stile pill per un accent dato (riusato per i badge aggiuntivi con colore
// per-item: lì il testo segue l'accent dell'item, non il text_color della tile).
function pillStyle(acc, ownText = true) {
  const v = s.value.variant;
  const textOverride = ownText ? s.value.text_color : '';
  const base = {
    display: 'inline-flex',
    alignItems: 'center',
    gap: '8px',
    padding: `${s.value.padding_y ?? 7}px ${s.value.padding_x ?? 13}px`,
    borderRadius: radiusCss(s.value.badge_radius),
    fontSize: `${parseInt(s.value.font_size) || 13}px`,
    fontWeight: s.value.font_weight || '600',
    textTransform: s.value.text_transform || 'none',
    letterSpacing: `${parseFloat(s.value.letter_spacing) || 0}px`,
    lineHeight: '1',
    color: txt.value,
  };
  if (v === 'solid') {
    return { ...base, background: acc, color: resolveColor(textOverride, 'var(--olo-color-on-primary, #fff)'), border: '1px solid transparent' };
  }
  if (v === 'outline') {
    return { ...base, background: 'transparent', border: `1px solid ${acc}`, color: resolveColor(textOverride, acc) };
  }
  if (v === 'light') {
    return { ...base, background: 'var(--olo-color-background, #fff)', border: '1px solid var(--olo-color-border, #e6e8ec)' };
  }
  // soft (default): tinta tenue del colore accent
  return { ...base, background: `color-mix(in srgb, ${acc} 12%, transparent)`, border: `1px solid color-mix(in srgb, ${acc} 22%, transparent)` };
}

const badgeStyle = computed(() => pillStyle(accent.value));
const extraStyle = (it) => {
  const st = pillStyle(resolveColor(it.color, 'var(--olo-color-border, #9aa0ad)'), false);
  if (it.text_color) st.color = resolveColor(it.text_color, st.color);
  return st;
};

const iconSvg = computed(() => {
  if (!s.value.icon) return '';
  return iconsSvg[s.value.icon] || '';
});
</script>

<style scoped>
.olo-badge-icon { display: inline-flex; width: 1em; height: 1em; }
.olo-badge-icon :deep(svg) { width: 100%; height: 100%; fill: currentColor; stroke: currentColor; }
/* .olo-live-dot e @keyframes olo-pulse sono globali (iframe-builder.css / frontend.css),
   registrati una sola volta — non scoped per-tile. */
</style>
