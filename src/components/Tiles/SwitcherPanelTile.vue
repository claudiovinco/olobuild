<template>
  <div class="olo-sp-preview" :class="['olo-sp--' + (s.nav_position || 'overlay'), 'olo-sp--preset-' + (s.preset || 'editorial-overlay'), { 'olo-sp--img-left': s.image_position === 'left' }]" :style="wrapStyle">
    <!-- Hero with overlay (overlay mode) -->
    <div v-if="(s.nav_position || 'overlay') === 'overlay'" class="sp-hero" :style="heroStyle">
      <img v-if="s.hero_image" :src="s.hero_image" alt="" class="sp-hero__img" :style="heroImgStyle" />
      <div v-else class="sp-hero__placeholder">{{ t('Hero') }}</div>
      <div v-if="heroOverlayShown" class="sp-hero__overlay" :style="heroOverlayStyle"></div>
      <div class="sp-nav-wrap sp-nav-wrap--overlay">
        <ul class="sp-nav" :style="navStyle">
          <li v-for="(item, i) in items" :key="item.id || i">
            <button
              class="sp-nav__btn"
              :class="{ 'is-active': activeIndex === i }"
              :style="navBtnStyle(activeIndex === i)"
              @click="activeIndex = i"
              :data-olo-editable="'items.' + i + '.nav_label'"
            >{{ item.nav_label || ('Tab ' + (i + 1)) }}</button>
          </li>
        </ul>
      </div>
    </div>

    <!-- Nav top -->
    <div v-else-if="s.nav_position === 'top'" class="sp-nav-wrap sp-nav-wrap--top">
      <ul class="sp-nav" :style="navStyle">
        <li v-for="(item, i) in items" :key="item.id || i">
          <button class="sp-nav__btn" :class="{ 'is-active': activeIndex === i }" :style="navBtnStyle(activeIndex === i)" @click="activeIndex = i" :data-olo-editable="'items.' + i + '.nav_label'">
            {{ item.nav_label || ('Tab ' + (i + 1)) }}
          </button>
        </li>
      </ul>
    </div>

    <!-- Nav side (left or right) -->
    <ul v-if="s.nav_position === 'side-left' || s.nav_position === 'side-right'" class="sp-nav sp-nav--side" :style="navStyle">
      <li v-for="(item, i) in items" :key="item.id || i">
        <button class="sp-nav__btn" :class="{ 'is-active': activeIndex === i }" :style="navBtnStyle(activeIndex === i)" @click="activeIndex = i" :data-olo-editable="'items.' + i + '.nav_label'">
          {{ item.nav_label || ('Tab ' + (i + 1)) }}
        </button>
      </li>
    </ul>

    <!-- Hero (non-overlay mode) -->
    <div v-if="hasHero && (s.nav_position === 'top' || s.nav_position === 'bottom' || s.nav_position === 'side-left' || s.nav_position === 'side-right')" class="sp-hero sp-hero--standalone" :style="heroStyle">
      <img v-if="s.hero_image" :src="s.hero_image" alt="" class="sp-hero__img" :style="heroImgStyle" />
      <div v-else class="sp-hero__placeholder">{{ t('Hero') }}</div>
    </div>

    <!-- Active panel content -->
    <div v-if="activeItem" class="sp-panel" :style="panelStyle">
      <div class="sp-panel__content">
        <component :is="s.title_tag || 'h3'" class="sp-panel__title" :style="titleStyle" :data-olo-editable="'items.' + activeIndex + '.title'">{{ activeItem.title || 'Titolo' }}</component>
        <div class="sp-panel__text" :style="textStyle" :data-olo-editable="'items.' + activeIndex + '.text'" data-olo-multiline>{{ activeItem.text || 'Testo del pannello...' }}</div>
        <div v-if="activeItem.button_text" class="sp-panel__btn" :class="'sp-btn--' + (s.button_style || 'primary')" :data-olo-editable="'items.' + activeIndex + '.button_text'">
          {{ activeItem.button_text }}
          <span v-if="(s.button_style || 'primary') === 'underline'" aria-hidden="true">&rarr;</span>
        </div>
      </div>
      <div v-if="activeItem.image" class="sp-panel__media" :style="mediaStyle">
        <img :src="activeItem.image" alt="" class="sp-panel__img" :style="imgStyle" />
      </div>
      <div v-else class="sp-panel__media sp-panel__media--empty" :style="mediaStyle">
        <span>{{ t('Immagine') }}</span>
      </div>
    </div>

    <!-- Nav bottom -->
    <div v-if="s.nav_position === 'bottom'" class="sp-nav-wrap sp-nav-wrap--bottom">
      <ul class="sp-nav" :style="navStyle">
        <li v-for="(item, i) in items" :key="item.id || i">
          <button class="sp-nav__btn" :class="{ 'is-active': activeIndex === i }" :style="navBtnStyle(activeIndex === i)" @click="activeIndex = i" :data-olo-editable="'items.' + i + '.nav_label'">
            {{ item.nav_label || ('Tab ' + (i + 1)) }}
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, watch } from 'vue';
import { radiusToCss } from '@/composables/useRadius';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => props.settings || {});
const activeIndex = ref(0);

const items = computed(() => {
  const raw = s.value.items;
  return Array.isArray(raw) ? raw : [];
});

const activeItem = computed(() => items.value[activeIndex.value] || items.value[0] || null);
const hasHero = computed(() => !!s.value.hero_image);

watch(() => items.value.length, () => {
  if (activeIndex.value >= items.value.length) activeIndex.value = 0;
});

const heroOverlayShown = computed(() => {
  return (s.value.nav_position || 'overlay') === 'overlay' && s.value.hero_overlay_color && s.value.hero_overlay_color !== 'transparent';
});

const heroOverlayStyle = computed(() => {
  const c = s.value.hero_overlay_color || 'rgba(0,0,0,0.35)';
  if (s.value.hero_overlay_gradient) {
    return { background: `linear-gradient(180deg, rgba(0,0,0,0) 0%, ${c} 100%)` };
  }
  return { background: c };
});

const heroStyle = computed(() => {
  const h = parseInt(s.value.hero_height) || 400;
  // Cap at 220px in builder for compact preview, but respect requested ratio.
  const capped = Math.min(h, 220);
  return {
    height: capped + 'px',
    // Dual-format: Number legacy E oggetto {tl,tr,br,bl}.
    borderRadius: radiusToCss(s.value.hero_radius, { fallback: '0px' }),
  };
});

const heroImgStyle = computed(() => ({
  objectPosition: s.value.hero_object_position || 'center center',
}));

const wrapStyle = computed(() => {
  const np = s.value.nav_position || 'overlay';
  if (np === 'side-left' || np === 'side-right') {
    return { display: 'flex', flexDirection: np === 'side-left' ? 'row' : 'row-reverse', gap: '12px', alignItems: 'flex-start' };
  }
  return {};
});

const navStyle = computed(() => {
  const np = s.value.nav_position || 'overlay';
  const isVert = np === 'side-left' || np === 'side-right';
  return {
    margin: '0',
    padding: (parseInt(s.value.nav_container_padding) || 0) + 'px',
    listStyle: 'none',
    display: 'flex',
    gap: (parseInt(s.value.nav_gap) || 0) + 'px',
    flexDirection: isVert ? 'column' : 'row',
    background: s.value.nav_container_bg && s.value.nav_container_bg !== 'transparent' ? s.value.nav_container_bg : '',
    borderRadius: radiusToCss(s.value.nav_container_radius, { fallback: '0px' }),
    minWidth: isVert ? '100px' : 'auto',
  };
});

function navBtnStyle(isActive) {
  const np = s.value.nav_position || 'overlay';
  const isVert = np === 'side-left' || np === 'side-right';
  const ind = s.value.nav_indicator_type || 'underline';
  const indC = s.value.nav_indicator_color || '#ffffff';
  const thick = parseInt(s.value.nav_indicator_thickness) || 2;
  const fontSize = Math.max(8, (parseInt(s.value.nav_font_size) || 12) * 0.7) + 'px'; // shrink for preview

  const style = {
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: isVert ? 'flex-start' : 'center',
    width: isVert ? '100%' : 'auto',
    padding: `${Math.max(2, (parseInt(s.value.nav_padding_y) || 12) * 0.5)}px ${Math.max(4, (parseInt(s.value.nav_padding_x) || 18) * 0.6)}px`,
    fontSize,
    fontWeight: s.value.nav_font_weight || '700',
    textTransform: s.value.nav_uppercase ? 'uppercase' : 'none',
    letterSpacing: (parseFloat(s.value.nav_letter_spacing) || 0.08) + 'em',
    color: isActive ? (s.value.nav_active_color || '#ffffff') : (s.value.nav_inactive_color || 'rgba(255,255,255,0.65)'),
    background: 'transparent',
    border: 0,
    borderRadius: radiusToCss(s.value.nav_radius, { fallback: '0px' }),
    cursor: 'pointer',
    transition: 'all 250ms ease',
    whiteSpace: 'nowrap',
  };

  if (ind === 'underline' && !isVert) {
    style.borderBottom = `${thick}px solid ${isActive ? indC : 'transparent'}`;
    style.borderRadius = '0';
  }
  if (ind === 'overline' && !isVert) {
    style.borderTop = `${thick}px solid ${isActive ? indC : 'transparent'}`;
    style.borderRadius = '0';
  }
  if (ind === 'left-bar' && isVert) {
    style.borderLeft = `${thick}px solid ${isActive ? indC : 'transparent'}`;
    style.borderRadius = '0 6px 6px 0';
  }
  if (isActive && s.value.nav_active_bg && s.value.nav_active_bg !== 'transparent') {
    style.background = s.value.nav_active_bg;
  }
  return style;
}

const panelStyle = computed(() => {
  const pad = s.value.tile_padding || { top: 40, right: 40, bottom: 40, left: 40 };
  // Shrink padding for builder preview
  const shrink = (n) => Math.max(8, Math.round(n * 0.45));
  return {
    display: 'flex',
    gap: Math.max(8, Math.round((parseInt(s.value.panel_gap) || 24) * 0.6)) + 'px',
    padding: `${shrink(pad.top)}px ${shrink(pad.right)}px ${shrink(pad.bottom)}px ${shrink(pad.left)}px`,
    background: s.value.panel_bg || '#ffffff',
    color: s.value.panel_text_color || '#1e293b',
    borderRadius: radiusToCss(s.value.panel_radius, { fallback: '0px' }),
    flexDirection: s.value.image_position === 'left' ? 'row-reverse' : 'row',
    alignItems: 'stretch',
  };
});

const titleStyle = computed(() => ({
  fontSize: Math.max(11, Math.round((parseInt(s.value.panel_title_size) || 28) * 0.5)) + 'px',
  fontWeight: s.value.panel_title_weight || '700',
  lineHeight: '1.2',
  color: s.value.panel_title_color || '#0f172a',
  margin: '0 0 6px',
}));

const textStyle = computed(() => ({
  fontSize: Math.max(8, Math.round((parseInt(s.value.panel_text_size) || 15) * 0.65)) + 'px',
  lineHeight: '1.5',
  margin: '0 0 8px',
  color: s.value.panel_text_color || '#1e293b',
  overflow: 'hidden',
  display: '-webkit-box',
  WebkitLineClamp: '3',
  WebkitBoxOrient: 'vertical',
}));

const mediaStyle = computed(() => {
  const w = Math.max(20, Math.min(70, parseInt(s.value.panel_image_width) || 40));
  return {
    flex: `0 0 ${w}%`,
    maxWidth: `${w}%`,
    borderRadius: radiusToCss(s.value.panel_image_radius, { fallback: '0px' }),
    overflow: 'hidden',
  };
});

const imgStyle = computed(() => {
  const ratio = s.value.panel_image_ratio || 'auto';
  const map = { 'auto': '', '16:9': '16/9', '4:3': '4/3', '1:1': '1/1', '3:4': '3/4' };
  const arOk = map[ratio];
  return {
    width: '100%',
    height: arOk ? 'auto' : '100%',
    aspectRatio: arOk || 'auto',
    objectFit: 'cover',
    display: 'block',
    borderRadius: radiusToCss(s.value.panel_image_radius, { fallback: '0px' }),
  };
});
</script>

<style scoped>
.olo-sp-preview {
  min-height: 120px;
  position: relative;
}

/* Hero */
.sp-hero {
  position: relative;
  overflow: hidden;
  background: #e5e7eb;
}
.sp-hero__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.sp-hero__placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #94a3b8, #64748b);
  color: #fff;
  font-size: 10px;
  font-weight: 600;
}
.sp-hero__overlay {
  position: absolute;
  inset: 0;
  pointer-events: none;
}
.sp-hero--standalone { margin-bottom: 8px; }

/* Nav wrap */
.sp-nav-wrap--overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 0 8px;
  z-index: 2;
}
.sp-nav-wrap--top { margin-bottom: 8px; }
.sp-nav-wrap--bottom { margin-top: 8px; }

.sp-nav { margin: 0; }
.sp-nav--side { min-width: 80px; }

.sp-nav__btn {
  cursor: pointer;
  white-space: nowrap;
}
/* a11y: anello di focus visibile da tastiera sul tab */
.sp-nav__btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 35%, transparent);
}

/* Panel */
.sp-panel {
  background: #fff;
}
.sp-panel__content {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.sp-panel__title {
  font-size: 13px;
  font-weight: 700;
  margin: 0 0 6px;
}
.sp-panel__text {
  font-size: 9px;
  line-height: 1.5;
  margin: 0 0 8px;
}
.sp-panel__btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 8px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 4px 10px;
  border: 1px solid var(--olo-color-border, #E5E7EB);
  color: var(--olo-color-text, #374151);
  border-radius: 0;
  width: fit-content;
}
.sp-btn--default { background: #fff; }
.sp-btn--primary {
  background: var(--olo-color-primary, #e1474f);
  border-color: var(--olo-color-primary, #e1474f);
  color: #fff;
}
.sp-btn--secondary {
  background: var(--olo-color-text, #1e293b);
  border-color: var(--olo-color-text, #1e293b);
  color: #fff;
}
.sp-btn--text {
  border: none;
  padding: 0;
  color: var(--olo-color-primary, #e1474f);
  text-transform: none;
  letter-spacing: 0;
  font-weight: 600;
}
.sp-btn--underline {
  border: 0;
  border-bottom: 1.5px solid currentColor;
  border-radius: 0;
  padding: 2px 0;
}
.sp-btn--pill {
  border-radius: 999px;
  background: var(--olo-color-primary, #e1474f);
  border-color: var(--olo-color-primary, #e1474f);
  color: #fff;
}

/* Panel media */
.sp-panel__media {
  flex-shrink: 0;
}
.sp-panel__img {
  display: block;
}
.sp-panel__media--empty {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  color: #9ca3af;
  font-size: 9px;
  border-radius: 3px;
  min-height: 80px;
}

/* ───── Preset visual hints in builder ───── */

/* Liquid Glass */
.olo-sp--preset-liquid-glass .sp-nav-wrap--overlay .sp-nav,
.olo-sp--preset-liquid-glass .sp-panel {
  backdrop-filter: blur(12px) saturate(180%);
  -webkit-backdrop-filter: blur(12px) saturate(180%);
  background: rgba(255,255,255,0.55) !important;
  border: 1px solid rgba(255,255,255,0.4);
  box-shadow: 0 8px 24px rgba(232,98,42,0.15);
}
.olo-sp--preset-liquid-glass .sp-nav__btn.is-active {
  background: rgba(255,255,255,0.95) !important;
  color: #0f172a !important;
}

/* Neon Cyber */
.olo-sp--preset-neon-cyber .sp-hero { background: #0a0f1c; }
.olo-sp--preset-neon-cyber .sp-nav {
  border: 1px solid rgba(255,106,42,0.35);
  box-shadow: 0 0 16px rgba(255,106,42,0.25);
}
.olo-sp--preset-neon-cyber .sp-nav__btn.is-active {
  text-shadow: 0 0 8px #ff6a2a, 0 0 4px #ff6a2a;
  border-color: #ff6a2a !important;
}
.olo-sp--preset-neon-cyber .sp-panel {
  background: #0a0f1c !important;
}
.olo-sp--preset-neon-cyber .sp-panel__title {
  color: #ff6a2a !important;
  text-shadow: 0 0 8px rgba(255,106,42,0.5);
}
.olo-sp--preset-neon-cyber .sp-panel__text {
  color: rgba(255,255,255,0.85) !important;
}

/* Brutalist */
.olo-sp--preset-brutalist-block .sp-hero {
  border: 3px solid #000;
  box-shadow: 4px 4px 0 0 #000;
}
.olo-sp--preset-brutalist-block .sp-nav {
  background: #fff !important;
  border: 2px solid #000;
  box-shadow: 3px 3px 0 0 #000;
  padding: 0 !important;
}
.olo-sp--preset-brutalist-block .sp-nav__btn {
  border-right: 2px solid #000;
  color: #000 !important;
}
.olo-sp--preset-brutalist-block .sp-nav > li:last-child .sp-nav__btn { border-right: 0; }
.olo-sp--preset-brutalist-block .sp-nav__btn.is-active {
  background: #e1474f !important;
  color: #000 !important;
}
.olo-sp--preset-brutalist-block .sp-panel {
  border: 3px solid #000 !important;
  box-shadow: 5px 5px 0 0 #000;
  margin-top: 8px;
}
.olo-sp--preset-brutalist-block .sp-panel__title {
  font-weight: 900 !important;
  text-transform: uppercase;
}
.olo-sp--preset-brutalist-block .sp-panel__btn {
  border: 2px solid #000 !important;
  box-shadow: 2px 2px 0 0 #000;
  background: #fff !important;
  color: #000 !important;
}

/* Magnetic */
.olo-sp--preset-magnetic-liquid .sp-nav__btn {
  transition: all 0.45s cubic-bezier(0.68, -0.55, 0.265, 1.55) !important;
}
.olo-sp--preset-magnetic-liquid .sp-nav__btn.is-active {
  transform: scale(1.06);
  box-shadow: 0 4px 12px rgba(232,98,42,0.45) !important;
}
.olo-sp--preset-magnetic-liquid .sp-panel__btn {
  background: linear-gradient(135deg, #e1474f 0%, #f07a80 100%) !important;
  color: #fff !important;
  border: 0 !important;
}

/* Sticker */
.olo-sp--preset-sticker .sp-nav > li:nth-child(1) .sp-nav__btn { transform: rotate(-1.5deg); }
.olo-sp--preset-sticker .sp-nav > li:nth-child(2) .sp-nav__btn { transform: rotate(0.8deg); }
.olo-sp--preset-sticker .sp-nav > li:nth-child(3) .sp-nav__btn { transform: rotate(-0.6deg); }
.olo-sp--preset-sticker .sp-nav > li:nth-child(4) .sp-nav__btn { transform: rotate(1.2deg); }
.olo-sp--preset-sticker .sp-nav__btn {
  border: 1.2px dashed rgba(232,98,42,0.5) !important;
  background: #fff !important;
  color: #1e293b !important;
  box-shadow: 0 2px 4px rgba(0,0,0,0.10);
}
.olo-sp--preset-sticker .sp-nav__btn.is-active {
  transform: rotate(0deg) scale(1.06) !important;
  border-style: solid !important;
  border-color: #e1474f !important;
  background: #fdf2ec !important;
}
.olo-sp--preset-sticker .sp-panel {
  transform: rotate(-0.4deg);
  border: 1.2px dashed rgba(232,98,42,0.4);
  box-shadow: 0 6px 14px rgba(0,0,0,0.10);
}
.olo-sp--preset-sticker .sp-panel__img {
  transform: rotate(1.2deg);
  box-shadow: 0 4px 10px rgba(0,0,0,0.12);
}

/* Retro Terminal */
.olo-sp--preset-retro-terminal .sp-hero { background: #0c0c0c !important; }
.olo-sp--preset-retro-terminal .sp-hero__img {
  opacity: 0.3;
  filter: hue-rotate(70deg) saturate(2);
}
.olo-sp--preset-retro-terminal .sp-nav {
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  border: 1px solid rgba(0,255,140,0.3);
  background-image: repeating-linear-gradient(0deg, transparent 0, transparent 2px, rgba(0,255,140,0.06) 2px, rgba(0,255,140,0.06) 3px);
}
.olo-sp--preset-retro-terminal .sp-nav__btn {
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace !important;
  color: rgba(0,255,140,0.6) !important;
}
.olo-sp--preset-retro-terminal .sp-nav__btn::before {
  content: '> ';
  opacity: 0.7;
}
.olo-sp--preset-retro-terminal .sp-nav__btn.is-active {
  color: #00ff8c !important;
  text-shadow: 0 0 6px #00ff8c;
}
.olo-sp--preset-retro-terminal .sp-panel {
  background: #0c0c0c !important;
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace !important;
  background-image: repeating-linear-gradient(0deg, transparent 0, transparent 2px, rgba(0,255,140,0.04) 2px, rgba(0,255,140,0.04) 3px);
  border: 1px solid rgba(0,255,140,0.3);
}
.olo-sp--preset-retro-terminal .sp-panel__title { color: #00ff8c !important; text-shadow: 0 0 6px rgba(0,255,140,0.5); }
.olo-sp--preset-retro-terminal .sp-panel__text { color: rgba(0,255,140,0.85) !important; }
.olo-sp--preset-retro-terminal .sp-panel__btn {
  background: transparent !important;
  border: 1px solid #00ff8c !important;
  color: #00ff8c !important;
}

/* 3D Tilt */
.olo-sp--preset-3d-tilt .sp-nav { perspective: 600px; }
.olo-sp--preset-3d-tilt .sp-nav__btn {
  transform: perspective(600px) rotateX(-8deg);
  transform-origin: center bottom;
  transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.olo-sp--preset-3d-tilt .sp-nav__btn.is-active {
  transform: perspective(600px) rotateX(0deg) translateY(-4px) scale(1.04) !important;
  box-shadow: 0 8px 18px rgba(0,0,0,0.18) !important;
}
.olo-sp--preset-3d-tilt .sp-panel {
  transform: perspective(800px) rotateX(2deg);
  transform-origin: center top;
  box-shadow: 0 16px 32px rgba(0,0,0,0.12);
}
</style>
