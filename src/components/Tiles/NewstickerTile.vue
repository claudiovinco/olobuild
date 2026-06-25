<template>
  <div class="olo-newsticker-preview" :class="['olo-nt-anim-'+animationType, 'olo-nt-dir-'+direction]" :style="containerStyle">
    <!-- Label -->
    <div v-if="s.show_label" class="olo-newsticker-label" :class="['olo-nt-shape-'+labelShape, 'olo-nt-label-'+labelPosition]" :style="labelStyle">
      <span v-if="s.label_icon" class="olo-newsticker-label-icon">
        <span v-if="iconSvg(s.label_icon)" class="olo-nt-svg" v-html="iconSvg(s.label_icon)"></span>
        <template v-else>{{ s.label_icon }}</template>
      </span>
      <span data-olo-editable="label_text">{{ s.label_text }}</span>
      <span v-if="labelShape === 'arrow' && direction === 'horizontal'" class="olo-newsticker-arrow" :style="arrowStyle"></span>
    </div>

    <!-- Ticker area -->
    <div class="olo-newsticker-content" :style="contentAreaStyle">
      <!-- Marquee -->
      <div v-if="animationType === 'marquee'" class="olo-newsticker-marquee" :style="marqueeStyle">
        <span v-for="(item, idx) in marqueeItems" :key="'mq-'+idx" class="olo-newsticker-marquee-item" :style="itemTypoStyle">
          <template v-if="item.logo">
            <img class="olo-newsticker-logo" :src="item.logo" :alt="item.title || ''" :style="logoStyle" loading="lazy" decoding="async">
          </template>
          <template v-else>
            <span v-if="item.icon" class="olo-newsticker-icon">
              <span v-if="iconSvg(item.icon)" class="olo-nt-svg" v-html="iconSvg(item.icon)"></span>
              <template v-else>{{ item.icon }}</template>
            </span>
            <span v-if="item.badge" class="olo-newsticker-badge" :style="badgeStyleFor(item)">{{ item.badge }}</span>
            <span class="olo-newsticker-title">{{ item.title || 'Notizia...' }}</span>
            <span v-if="item.timestamp" class="olo-newsticker-time">{{ item.timestamp }}</span>
          </template>
        </span>
      </div>

      <!-- Single item (slide/fade) -->
      <div v-else-if="currentItem" class="olo-newsticker-item" :key="currentIndex" :style="itemStyle">
        <template v-if="currentItem.logo">
          <img class="olo-newsticker-logo" :src="currentItem.logo" :alt="currentItem.title || ''" :style="logoStyle" loading="lazy" decoding="async">
        </template>
        <template v-else>
          <span v-if="currentItem.icon" class="olo-newsticker-icon">
            <span v-if="iconSvg(currentItem.icon)" class="olo-nt-svg" v-html="iconSvg(currentItem.icon)"></span>
            <template v-else>{{ currentItem.icon }}</template>
          </span>
          <span v-if="currentItem.badge" class="olo-newsticker-badge" :style="badgeStyleFor(currentItem)" :data-olo-editable="`items.${currentIndex}.badge`">{{ currentItem.badge }}</span>
          <span class="olo-newsticker-title" :data-olo-editable="`items.${currentIndex}.title`">{{ currentItem.title || 'Notizia...' }}</span>
          <span v-if="currentItem.timestamp" class="olo-newsticker-time" :data-olo-editable="`items.${currentIndex}.timestamp`">{{ currentItem.timestamp }}</span>
        </template>
      </div>
      <div v-else class="olo-newsticker-item" :style="itemStyle">
        <span class="olo-newsticker-title" style="opacity:0.5">{{ t('Aggiungi notizie...') }}</span>
      </div>
    </div>

    <!-- Counter -->
    <div v-if="s.show_counter && animationType !== 'marquee'" class="olo-newsticker-counter" :style="{ color: s.text_color || '#374151' }">
      {{ currentIndex + 1 }}/{{ items.length }}
    </div>

    <!-- Controlli prev/next -->
    <div v-if="s.show_controls && animationType !== 'marquee'" class="olo-newsticker-controls">
      <button type="button" class="olo-newsticker-ctrl" @click.stop="prev">‹</button>
      <button type="button" class="olo-newsticker-ctrl" @click.stop="next">›</button>
    </div>

    <!-- Indicators -->
    <div v-if="s.show_indicators && animationType !== 'marquee'" class="olo-newsticker-indicators">
      <button v-for="(item, idx) in items" :key="'dot-'+idx" type="button"
        class="olo-newsticker-dot"
        :class="{ 'olo-newsticker-dot-active': idx === currentIndex }"
        :style="idx === currentIndex ? { background: s.text_color || '#374151' } : {}"
        @click.stop="goTo(idx)"></button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { radiusToCss as radiusToCssRaw } from '@/composables/useRadius';
const radiusToCss = (r) => radiusToCssRaw(r, { fallback: '4px', zero: '0px' });

function isIconName(v) {
  return typeof v === 'string' && /^[a-z][a-z0-9-]*$/.test(v);
}
function iconSvg(v) {
  return iconsSvg[v] || '';
}

const defaults = {
  items: [
    { id: 'nt-1', title: 'Nuova funzionalità disponibile', url: '', badge: 'Novità', icon: 'star', badge_bg: '', timestamp: '' },
    { id: 'nt-2', title: 'Manutenzione programmata venerdì', url: '', badge: 'Avviso', icon: 'warning', badge_bg: '', timestamp: '' },
    { id: 'nt-3', title: 'Aggiornamento versione 2.0 rilasciato', url: '', badge: '', icon: 'bolt', badge_bg: '', timestamp: '' },
  ],
  direction: 'horizontal',
  show_label: true,
  label_text: 'Breaking',
  label_icon: '',
  label_shape: 'arrow',
  label_position: 'left',
  label_bg: '#dc2626',
  label_color: '#ffffff',
  badge_bg: '',
  badge_color: '',
  bg_color: 'var(--olo-color-muted, #F3F4F6)',
  bg_gradient: false,
  bg_color2: '',
  bg_angle: 90,
  text_color: 'var(--olo-color-text, #374151)',
  speed: '3000',
  height: '42',
  separator: '|',
  font_size: 14,
  font_weight: '400',
  text_transform: 'none',
  letter_spacing: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
  animation_type: 'slide-up',
  transition_duration: 400,
  auto_scroll: true,
  pause_on_hover: true,
  loop: true,
  random_order: false,
  stop_on_click: false,
  marquee_direction: 'left',
  marquee_gap: 60,
  marquee_duration: 25,
  show_controls: false,
  show_indicators: false,
  show_counter: false,
  logo_height: 32,
  logo_grayscale: false,
  logo_opacity: 100,
  item_hover_effect: 'none',
  border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));

const items = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw)) return raw;
  return [];
});

const animationType = computed(() => s.value.animation_type || 'slide-up');
const direction = computed(() => s.value.direction || 'horizontal');
const labelShape = computed(() => s.value.label_shape || 'arrow');
const labelPosition = computed(() => s.value.label_position === 'right' ? 'right' : 'left');

const currentIndex = ref(0);
let intervalId = null;

const currentItem = computed(() => {
  if (items.value.length === 0) return null;
  return items.value[currentIndex.value % items.value.length];
});

const marqueeItems = computed(() => {
  const arr = items.value;
  return arr.concat(arr);
});

function startTicker() {
  stopTicker();
  if (animationType.value === 'marquee') return;
  if (!s.value.auto_scroll) return;
  if (items.value.length <= 1) return;
  const speed = Math.max(1500, parseInt(s.value.speed) || 3000);
  intervalId = setInterval(() => {
    let next = currentIndex.value + 1;
    if (next >= items.value.length) {
      if (!s.value.loop) {
        stopTicker();
        return;
      }
      next = 0;
    }
    if (s.value.random_order && items.value.length > 1) {
      do { next = Math.floor(Math.random() * items.value.length); } while (next === currentIndex.value);
    }
    currentIndex.value = next;
  }, speed);
}

function stopTicker() {
  if (intervalId) { clearInterval(intervalId); intervalId = null; }
}

function next() {
  let n = currentIndex.value + 1;
  if (n >= items.value.length) n = 0;
  currentIndex.value = n;
}
function prev() {
  let p = currentIndex.value - 1;
  if (p < 0) p = items.value.length - 1;
  currentIndex.value = p;
}
function goTo(idx) {
  currentIndex.value = idx;
}

onMounted(() => startTicker());
onBeforeUnmount(() => stopTicker());

watch(() => [s.value.speed, s.value.auto_scroll, items.value.length, animationType.value], () => {
  startTicker();
});

// Composable condiviso — vedi src/composables/useRadius.js

function bgValue() {
  if (s.value.bg_gradient && s.value.bg_color2) {
    const a = parseInt(s.value.bg_angle) || 90;
    return `linear-gradient(${a}deg, ${s.value.bg_color || '#F3F4F6'} 0%, ${s.value.bg_color2} 100%)`;
  }
  return s.value.bg_color || 'var(--olo-color-muted, #F3F4F6)';
}

const containerStyle = computed(() => {
  const tp = s.value.tile_padding || {};
  const isVert = direction.value === 'vertical';
  return {
    display: 'flex',
    flexDirection: isVert ? 'column' : 'row',
    alignItems: isVert ? 'stretch' : 'center',
    background: bgValue(),
    height: isVert ? 'auto' : ((parseInt(s.value.height) || 42) + 'px'),
    minHeight: isVert ? ((parseInt(s.value.height) || 42) * 2 + 'px') : null,
    padding: `${tp.top || 0}px ${tp.right || 0}px ${tp.bottom || 0}px ${tp.left || 0}px`,
    overflow: 'hidden',
    borderRadius: radiusToCss(s.value.border_radius),
    fontFamily: 'inherit',
    position: 'relative',
  };
});

const labelStyle = computed(() => {
  const base = {
    background: s.value.label_bg || '#dc2626',
    color: s.value.label_color || '#ffffff',
    height: direction.value === 'vertical' ? 'auto' : '100%',
    padding: direction.value === 'vertical' ? '6px 14px' : '0 14px',
    display: 'flex',
    alignItems: 'center',
    gap: '6px',
    fontWeight: '700',
    fontSize: '12px',
    textTransform: 'uppercase',
    letterSpacing: '1px',
    whiteSpace: 'nowrap',
    flexShrink: 0,
    position: 'relative',
    zIndex: 2,
    order: labelPosition.value === 'right' ? 2 : 0,
  };
  if (labelShape.value === 'pill') {
    base.borderRadius = '999px';
    base.margin = '4px';
    base.padding = '0 18px';
  } else if (labelShape.value === 'square') {
    base.borderRadius = '0';
  } else if (labelShape.value === 'tag') {
    base.borderRadius = labelPosition.value === 'right' ? '0 4px 4px 0' : '4px 0 0 4px';
  }
  return base;
});

const arrowStyle = computed(() => {
  const half = (parseInt(s.value.height) || 42) / 2;
  const bg = s.value.label_bg || '#dc2626';
  const isRight = labelPosition.value === 'right';
  return {
    content: '""',
    position: 'absolute',
    top: 0,
    [isRight ? 'left' : 'right']: '-8px',
    width: 0,
    height: 0,
    borderStyle: 'solid',
    borderWidth: isRight ? `${half}px 8px ${half}px 0` : `${half}px 0 ${half}px 8px`,
    borderColor: isRight ? `transparent ${bg} transparent transparent` : `transparent transparent transparent ${bg}`,
    display: 'block',
  };
});

const contentAreaStyle = computed(() => ({
  flex: 1,
  overflow: 'hidden',
  padding: '0 14px',
  minWidth: 0,
  position: 'relative',
}));

const itemTypoStyle = computed(() => ({
  color: s.value.text_color || 'var(--olo-color-text, #374151)',
  fontSize: (parseInt(s.value.font_size) || 14) + 'px',
  fontWeight: s.value.font_weight || '400',
  textTransform: s.value.text_transform || 'none',
  letterSpacing: (parseFloat(s.value.letter_spacing) || 0) + 'px',
  whiteSpace: 'nowrap',
  display: 'inline-flex',
  alignItems: 'center',
  gap: '8px',
}));

const itemStyle = computed(() => ({
  ...itemTypoStyle.value,
  overflow: 'hidden',
  textOverflow: 'ellipsis',
  display: 'flex',
  alignItems: 'center',
  width: '100%',
}));

const marqueeStyle = computed(() => ({
  display: 'flex',
  flexDirection: direction.value === 'vertical' ? 'column' : 'row',
  gap: (parseInt(s.value.marquee_gap) || 60) + 'px',
  width: 'max-content',
  animation: `olo-nt-mq-${direction.value === 'vertical' ? 'v' : 'h'} ${s.value.marquee_duration || 25}s linear infinite`,
  animationDirection: s.value.marquee_direction === 'right' ? 'reverse' : 'normal',
}));

function badgeStyleFor(item) {
  return {
    background: item.badge_bg || s.value.badge_bg || 'rgba(255,255,255,0.15)',
    color: s.value.badge_color || 'inherit',
    padding: '2px 8px',
    borderRadius: '3px',
    fontSize: '11px',
    fontWeight: '600',
    flexShrink: 0,
    textTransform: 'none',
    letterSpacing: 0,
  };
}

const logoStyle = computed(() => {
  const h = Math.max(16, Math.min(60, parseInt(s.value.logo_height) || 32));
  const base = {
    height: h + 'px',
    width: 'auto',
    display: 'block',
    flexShrink: 0,
    objectFit: 'contain',
  };
  if (s.value.logo_grayscale) {
    const op = Math.max(0, Math.min(100, parseInt(s.value.logo_opacity) ?? 100)) / 100;
    base.filter = 'grayscale(1)';
    base.opacity = op;
    base.transition = 'filter 250ms ease, opacity 250ms ease';
  }
  return base;
});
</script>

<style scoped>
.olo-newsticker-preview {
  position: relative;
}
.olo-newsticker-item {
  animation: olo-nt-fadein 0.3s ease;
}
@keyframes olo-nt-fadein {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.olo-nt-anim-fade .olo-newsticker-item {
  animation: olo-nt-opacity 0.3s ease;
}
.olo-nt-anim-slide-down .olo-newsticker-item {
  animation: olo-nt-slidedown 0.3s ease;
}
@keyframes olo-nt-opacity { from { opacity: 0 } to { opacity: 1 } }
@keyframes olo-nt-slidedown { from { opacity: 0; transform: translateY(-8px) } to { opacity: 1; transform: translateY(0) } }
@keyframes olo-nt-mq-h { from { transform: translateX(0) } to { transform: translateX(-50%) } }
@keyframes olo-nt-mq-v { from { transform: translateY(0) } to { transform: translateY(-50%) } }

.olo-newsticker-counter {
  font-size: 11px;
  opacity: 0.7;
  padding: 0 8px;
  flex-shrink: 0;
  font-variant-numeric: tabular-nums;
}
.olo-newsticker-controls {
  display: flex;
  gap: 4px;
  align-items: center;
  padding: 0 8px;
  flex-shrink: 0;
}
.olo-newsticker-ctrl {
  width: 24px;
  height: 24px;
  border: none;
  border-radius: 50%;
  background: rgba(255,255,255,0.15);
  color: inherit;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  line-height: 1;
}
.olo-newsticker-ctrl:hover { background: rgba(255,255,255,0.3); }
/* a11y tastiera: anello di focus visibile sui controlli prev/next e indicatori */
.olo-newsticker-ctrl:focus-visible,
.olo-newsticker-dot:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
.olo-newsticker-indicators {
  display: flex;
  gap: 6px;
  padding: 0 8px;
  flex-shrink: 0;
  align-items: center;
}
.olo-newsticker-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: rgba(255,255,255,0.3);
  border: none;
  cursor: pointer;
  padding: 0;
  transition: transform 150ms ease;
}
.olo-newsticker-dot-active { transform: scale(1.4); }

.olo-newsticker-logo { vertical-align: middle; }
.olo-newsticker-marquee-item:hover .olo-newsticker-logo,
.olo-newsticker-item:hover .olo-newsticker-logo {
  filter: grayscale(0) !important;
  opacity: 1 !important;
}
.olo-newsticker-icon { font-size: 16px; line-height: 1; flex-shrink: 0; display: inline-flex; align-items: center; }
.olo-newsticker-time { font-size: 11px; opacity: 0.65; margin-left: auto; padding-left: 12px; flex-shrink: 0; }
.olo-newsticker-label-icon { font-size: 14px; line-height: 1; display: inline-flex; align-items: center; }
.olo-nt-svg { display: inline-flex; align-items: center; justify-content: center; }
.olo-nt-svg :deep(svg) { width: 1em; height: 1em; fill: currentColor; }
</style>
