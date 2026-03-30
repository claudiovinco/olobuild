<template>
  <div style="min-height: 60px;">
    <!-- Filter bar preview -->
    <div v-if="s.show_filter && uniqueTags.length > 1" class="mb-flex mb-gap-2 mb-mb-3 mb-flex-wrap" :style="filterAlignStyle">
      <span
        v-for="tag in uniqueTags"
        :key="tag"
        :class="filterClass"
      >{{ tag }}</span>
    </div>
    <div v-else-if="s.show_filter" class="mb-text-xs mb-text-gray-500 mb-mb-2 mb-italic">
      Filtro attivo — aggiungi tag diversi agli elementi
    </div>
    <!-- Grid -->
    <div class="mb-grid" :style="gridStyle">
      <div
        v-for="(item, i) in items"
        :key="item.id || i"
        class="olo-grid-card-wrap"
        :class="{ 'olo-grid-eq': s.equal_height }"
      >
        <component
          :is="item.link ? 'a' : 'div'"
          class="olo-grid-card-inner"
          :class="[cardClassForStyle, hoverClass]"
          :style="cardStyle"
          :href="item.link || undefined"
          :target="item.link && item.link_target ? '_blank' : undefined"
        >
          <!-- Badge -->
          <span v-if="item.badge" class="olo-grid-badge-preview" :style="badgeStyle(item)">{{ item.badge }}</span>

          <!-- Image area -->
          <div v-if="item.image || hasAnyImage" class="olo-grid-media-preview" :style="imageContainerStyle">
            <img
              v-if="item.image"
              :src="item.image"
              alt=""
              class="olo-grid-img-preview"
              :style="imageStyle"
              :class="imageAnimClass"
            />
            <div v-else class="olo-grid-img-placeholder">IMG</div>

            <!-- Overlay text on image -->
            <div v-if="s.overlay_text && item.image" class="olo-grid-overlay-preview" :class="'olo-grid-overlay--' + (s.overlay_position || 'bottom')">
              <h3 class="olo-grid-title-preview" :style="overlayTitleStyle" :data-olo-editable="'items.' + i + '.title'">{{ item.title }}</h3>
              <p v-if="item.content" class="olo-grid-text-preview" style="color:rgba(255,255,255,0.85);margin:0;" :data-olo-editable="'items.' + i + '.content'">{{ item.content }}</p>
            </div>
          </div>

          <!-- Content area (hidden if overlay_text + has image) -->
          <div v-if="!(s.overlay_text && item.image)" class="olo-grid-body-preview" :style="contentPadStyle">
            <span v-if="item.icon" class="olo-grid-icon-preview" :class="item.icon"></span>
            <h3
              class="olo-grid-title-preview"
              :style="titleStyle"
              :data-olo-editable="'items.' + i + '.title'"
            >{{ item.title }}</h3>
            <p
              v-if="item.content"
              class="olo-grid-text-preview"
              :style="contentTextStyle"
              :data-olo-editable="'items.' + i + '.content'"
            >{{ item.content }}</p>
            <span v-if="item.link" class="olo-grid-link-hint">&#x1F517; Link</span>
          </div>

          <!-- Overlay with no image: show content normally -->
          <div v-if="s.overlay_text && !item.image" class="olo-grid-body-preview" :style="contentPadStyle">
            <h3
              class="olo-grid-title-preview"
              :style="titleStyle"
              :data-olo-editable="'items.' + i + '.title'"
            >{{ item.title }}</h3>
            <p
              v-if="item.content"
              class="olo-grid-text-preview"
              :style="contentTextStyle"
              :data-olo-editable="'items.' + i + '.content'"
            >{{ item.content }}</p>
          </div>
        </component>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  columns: '3',
  gap: 'default',
  show_filter: false,
  filter_style: 'pills',
  filter_align: 'left',
  masonry: false,
  card_style: 'default',
  card_hover: 'none',
  image_ratio: 'auto',
  image_height: '',
  image_fit: 'cover',
  image_zoom: false,
  card_radius: '8',
  card_padding: '16',
  card_bg_color: '',
  card_border_color: '',
  equal_height: false,
  overlay_text: false,
  overlay_position: 'bottom',
  title_size: '',
  content_size: '',
  title_color: '',
  content_color: '',
  shadow: 'none',
  filter_all_label: '',
  image_animation: 'none',
  image_animation_speed: '3',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const items = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { title: 'Item 1', content: 'Description...', image: '', tag: 'all' },
    { title: 'Item 2', content: 'Description...', image: '', tag: 'all' },
    { title: 'Item 3', content: 'Description...', image: '', tag: 'all' },
  ];
});

const hasAnyImage = computed(() => items.value.some(i => i.image));

const uniqueTags = computed(() => {
  const tags = new Set();
  tags.add(s.value.filter_all_label || 'All');
  items.value.forEach(item => {
    if (item.tag && item.tag !== 'all') {
      item.tag.split(',').forEach(t => {
        const trimmed = t.trim();
        if (trimmed) tags.add(trimmed);
      });
    }
  });
  return [...tags];
});

const filterAlignStyle = computed(() => {
  const a = s.value.filter_align || 'left';
  if (a === 'center') return { justifyContent: 'center' };
  if (a === 'right') return { justifyContent: 'flex-end' };
  return {};
});

const filterClass = computed(() => {
  const st = s.value.filter_style;
  if (st === 'minimal') return 'mb-px-1 mb-py-0.5 mb-text-xs mb-text-gray-300 mb-uppercase mb-tracking-wide mb-border-b mb-border-gray-500';
  if (st === 'buttons') return 'mb-px-3 mb-py-1 mb-text-xs mb-rounded mb-border mb-border-gray-500 mb-text-gray-300';
  return 'mb-px-2 mb-py-0.5 mb-text-xs mb-rounded mb-bg-gray-600 mb-text-gray-300';
});

const gapMap = { collapse: '0px', small: '8px', default: '16px', medium: '24px', large: '40px' };

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${parseInt(s.value.columns) || 3}, 1fr)`,
  gap: gapMap[s.value.gap] || '16px',
}));

// Card style class (matches frontend olo-grid-card--*)
const cardClassForStyle = computed(() => {
  return 'olo-grid-card--' + (s.value.card_style || 'default');
});

// Hover class (matches frontend olo-grid-hover--*)
const hoverClass = computed(() => {
  const h = s.value.card_hover;
  if (!h || h === 'none') return '';
  return 'olo-grid-hover--' + h;
});

const cardStyle = computed(() => {
  const cs = s.value.card_style;
  const r = (parseInt(s.value.card_radius) || 0) + 'px';
  const style = {
    borderRadius: r,
    transition: 'transform 0.35s cubic-bezier(.4,0,.2,1), box-shadow 0.35s ease, border-color 0.3s',
  };

  // Border color
  const bc = s.value.card_border_color;
  if (bc) {
    style.border = '2px solid ' + bc;
  } else if (cs === 'default') {
    style.border = '1px solid rgba(255,255,255,0.12)';
  } else if (cs === 'outlined') {
    style.border = '2px solid rgba(255,255,255,0.2)';
  } else if (cs === 'glass') {
    style.border = '1px solid rgba(255,255,255,0.15)';
  }

  // Backgrounds (adapted for dark builder canvas)
  if (cs === 'default') {
    style.background = 'rgba(255,255,255,0.06)';
  } else if (cs === 'outlined') {
    style.background = 'transparent';
  } else if (cs === 'elevated') {
    style.background = 'rgba(255,255,255,0.08)';
    style.border = 'none';
    style.boxShadow = '0 4px 20px rgba(0,0,0,0.3)';
  } else if (cs === 'glass') {
    style.background = 'rgba(255,255,255,0.06)';
    style.backdropFilter = 'blur(12px)';
    style.WebkitBackdropFilter = 'blur(12px)';
  } else if (cs === 'gradient' && !bc) {
    style.border = '2px solid transparent';
    style.backgroundImage = 'linear-gradient(rgba(30,30,46,1),rgba(30,30,46,1)),linear-gradient(135deg,#6366F1,#EC4899,#F59E0B)';
    style.backgroundOrigin = 'border-box';
    style.backgroundClip = 'padding-box,border-box';
  } else if (cs === 'flat') {
    style.background = 'rgba(255,255,255,0.04)';
  } else if (cs === 'minimal') {
    style.background = 'none';
    style.border = 'none';
    if (bc) style.border = '1px solid ' + bc;
  }

  // Custom background color overrides card style defaults
  const bgc = s.value.card_bg_color;
  if (bgc) {
    style.background = bgc;
    // Clear gradient background-image if custom bg is set
    delete style.backgroundImage;
    delete style.backgroundOrigin;
    delete style.backgroundClip;
  }

  // Shadow on cards
  const shadowMap = {
    sm: '0 1px 3px 0 rgba(0,0,0,0.3), 0 1px 2px -1px rgba(0,0,0,0.25)',
    md: '0 4px 6px -1px rgba(0,0,0,0.35), 0 2px 4px -2px rgba(0,0,0,0.25)',
    lg: '0 10px 15px -3px rgba(0,0,0,0.4), 0 4px 6px -4px rgba(0,0,0,0.25)',
    xl: '0 20px 25px -5px rgba(0,0,0,0.45), 0 8px 10px -6px rgba(0,0,0,0.3)',
  };
  const sh = s.value.shadow || 'none';
  if (shadowMap[sh]) {
    style.boxShadow = shadowMap[sh];
  } else if (sh === 'custom') {
    const h = parseInt(s.value.shadow_h) || 0;
    const v = parseInt(s.value.shadow_v) || 4;
    const bl = parseInt(s.value.shadow_blur) || 10;
    const sp = parseInt(s.value.shadow_spread) || 0;
    const sc = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const ins = s.value.shadow_inset ? 'inset ' : '';
    style.boxShadow = `${ins}${h}px ${v}px ${bl}px ${sp}px ${sc}`;
  }

  return style;
});

const imageContainerStyle = computed(() => {
  const style = {};
  const ratio = s.value.image_ratio;
  const h = parseInt(s.value.image_height);
  const r = parseInt(s.value.card_radius) || 0;
  if (ratio && ratio !== 'auto') {
    style.aspectRatio = ratio;
  } else if (h > 0) {
    style.height = h + 'px';
  }
  // Match frontend: top corners rounded for non-minimal
  if (s.value.card_style === 'minimal' && r > 0) {
    style.borderRadius = r + 'px';
  } else if (r > 0) {
    style.borderRadius = r + 'px ' + r + 'px 0 0';
  }
  return style;
});

const imageStyle = computed(() => ({
  width: '100%',
  height: '100%',
  objectFit: s.value.image_fit || 'cover',
  display: 'block',
  transition: 'transform 0.5s cubic-bezier(.4,0,.2,1)',
}));

const imageAnimClass = computed(() => {
  const a = s.value.image_animation;
  if (!a || a === 'none') return '';
  return 'olo-imgaim-' + a;
});

const contentPadStyle = computed(() => {
  const p = parseInt(s.value.card_padding) || 16;
  const cs = s.value.card_style;
  if (cs === 'minimal') return { padding: Math.max(4, Math.floor(p / 4)) + 'px 0 0' };
  return { padding: p + 'px' };
});

const titleStyle = computed(() => {
  const style = {};
  const sz = parseInt(s.value.title_size);
  if (sz > 0) {
    style.fontSize = sz + 'px';
  }
  if (s.value.title_color) style.color = s.value.title_color;
  return style;
});

const overlayTitleStyle = computed(() => {
  const style = { color: '#fff' };
  const sz = parseInt(s.value.title_size);
  if (sz > 0) style.fontSize = sz + 'px';
  return style;
});

const contentTextStyle = computed(() => {
  const style = {};
  const sz = parseInt(s.value.content_size);
  if (sz > 0) style.fontSize = sz + 'px';
  if (s.value.content_color) style.color = s.value.content_color;
  return style;
});

function badgeStyle(item) {
  return {
    background: item.badge_color || 'var(--olo-color-primary, #6366F1)',
  };
}
</script>

<style scoped>
/* ── Card wrapper (grid item) ── */
.olo-grid-card-wrap {
  min-width: 0;
}
.olo-grid-card-wrap.olo-grid-eq {
  display: flex;
}
.olo-grid-eq .olo-grid-card-inner {
  display: flex;
  flex-direction: column;
  width: 100%;
}
.olo-grid-eq .olo-grid-body-preview {
  flex: 1;
}

/* ── Card inner ── */
.olo-grid-card-inner {
  position: relative;
  display: block;
  text-decoration: none;
  color: inherit;
}

/* ── Hover effects (match frontend) ── */
.olo-grid-hover--lift:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 32px rgba(0,0,0,0.25) !important;
}
.olo-grid-hover--scale:hover {
  transform: scale(1.03);
}
.olo-grid-hover--glow:hover {
  box-shadow: 0 0 20px rgba(99,102,241,0.4) !important;
}
.olo-grid-hover--border-glow:hover {
  border-color: var(--olo-color-primary, #6366F1) !important;
  box-shadow: 0 0 15px rgba(99,102,241,0.3) !important;
}
.olo-grid-hover--tilt:hover {
  transform: perspective(800px) rotateY(4deg) rotateX(2deg);
  box-shadow: 4px 8px 24px rgba(0,0,0,0.25) !important;
}

/* ── Image zoom on card hover ── */
.olo-grid-card-inner:hover .olo-grid-img-preview {
  transform: scale(1.08);
}

/* ── Media container ── */
.olo-grid-media-preview {
  position: relative;
  overflow: hidden;
}

.olo-grid-img-preview {
  display: block;
}

.olo-grid-img-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 80px;
  height: 100%;
  background: rgba(255,255,255,0.03);
  color: rgba(255,255,255,0.25);
  font-size: 11px;
}

/* ── Body ── */
.olo-grid-body-preview {
  position: relative;
}

/* ── Typography (match frontend sizes) ── */
.olo-grid-title-preview {
  margin: 0 0 6px;
  line-height: 1.3;
  font-weight: 700;
  font-size: 1.05em;
  color: #e5e7eb;
}

.olo-grid-text-preview {
  margin: 0;
  line-height: 1.55;
  font-size: 0.9em;
  color: rgba(255,255,255,0.45);
}

/* ── Icon ── */
.olo-grid-icon-preview {
  font-size: 1.5em;
  margin-bottom: 8px;
  display: inline-block;
  color: var(--olo-color-primary, #6366F1);
}

/* ── Link hint ── */
.olo-grid-link-hint {
  display: inline-block;
  margin-top: 6px;
  font-size: 0.72em;
  color: var(--olo-color-primary, #6366F1);
  opacity: 0.7;
}

/* ── Badge ── */
.olo-grid-badge-preview {
  position: absolute;
  top: 10px;
  right: 10px;
  padding: 3px 10px;
  border-radius: 4px;
  font-size: 0.72em;
  font-weight: 700;
  color: #fff;
  letter-spacing: 0.3px;
  text-transform: uppercase;
  z-index: 3;
}

/* ── Overlay on image ── */
.olo-grid-overlay-preview {
  position: absolute;
  left: 0;
  right: 0;
  padding: 16px;
  z-index: 2;
}
.olo-grid-overlay--bottom {
  bottom: 0;
  background: linear-gradient(transparent, rgba(0,0,0,0.65));
}
.olo-grid-overlay--top {
  top: 0;
  bottom: auto;
  background: linear-gradient(rgba(0,0,0,0.65), transparent);
}
.olo-grid-overlay--center {
  top: 0;
  bottom: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  background: rgba(0,0,0,0.45);
  opacity: 0;
  transition: opacity 0.35s;
}
.olo-grid-card-inner:hover .olo-grid-overlay--center {
  opacity: 1;
}
.olo-grid-overlay-preview .olo-grid-title-preview {
  color: #fff;
}

/* ── Image continuous animations (preview) ── */
.olo-imgaim-ken-burns {
  animation: olo-kb 8s ease-in-out infinite alternate;
}
.olo-imgaim-pan-left {
  animation: olo-pan-l 6s linear infinite alternate;
}
.olo-imgaim-pan-right {
  animation: olo-pan-r 6s linear infinite alternate;
}
.olo-imgaim-pan-up {
  animation: olo-pan-u 6s linear infinite alternate;
}
.olo-imgaim-pan-down {
  animation: olo-pan-d 6s linear infinite alternate;
}
.olo-imgaim-pulse {
  animation: olo-img-pulse 3s ease-in-out infinite;
}
.olo-imgaim-float {
  animation: olo-img-float 4s ease-in-out infinite;
}
.olo-imgaim-rotate {
  animation: olo-img-rotate 20s linear infinite;
}
.olo-imgaim-shimmer {
  animation: olo-img-shimmer 3s ease-in-out infinite;
}

@keyframes olo-kb {
  0% { transform: scale(1) translate(0,0); }
  100% { transform: scale(1.15) translate(-2%,-2%); }
}
@keyframes olo-pan-l {
  0% { transform: translateX(0) scale(1.15); }
  100% { transform: translateX(-10%) scale(1.15); }
}
@keyframes olo-pan-r {
  0% { transform: translateX(-10%) scale(1.15); }
  100% { transform: translateX(0) scale(1.15); }
}
@keyframes olo-pan-u {
  0% { transform: translateY(0) scale(1.15); }
  100% { transform: translateY(-10%) scale(1.15); }
}
@keyframes olo-pan-d {
  0% { transform: translateY(-10%) scale(1.15); }
  100% { transform: translateY(0) scale(1.15); }
}
@keyframes olo-img-pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}
@keyframes olo-img-float {
  0%, 100% { transform: scale(1.06) translateY(0); }
  50% { transform: scale(1.06) translateY(-4px); }
}
@keyframes olo-img-rotate {
  from { transform: rotate(0deg) scale(1.3); }
  to { transform: rotate(360deg) scale(1.3); }
}
@keyframes olo-img-shimmer {
  0%, 100% { filter: brightness(1); }
  50% { filter: brightness(1.15); }
}
</style>
