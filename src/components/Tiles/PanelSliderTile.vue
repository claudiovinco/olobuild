<template>
  <div class="olo-panelslider" :class="['olo-ps-arrows-' + arrowStyle]" style="position:relative;">
    <!-- Cards track -->
    <div class="olo-ps-viewport" style="overflow:hidden;">
      <div class="olo-ps-track" :style="trackStyle">
        <div
          v-for="(panel, i) in visiblePanels"
          :key="panel.id || i"
          class="olo-ps-card"
          :style="cardStyle"
        >
          <div
            v-if="panel.image"
            class="olo-ps-media"
            :style="mediaStyle"
          >
            <img
              :src="panel.image"
              :alt="panel.title || ''"
              class="olo-ps-img"
              :style="{ objectFit: imageFit }"
            />
          </div>
          <div class="olo-ps-body" :style="bodyStyle">
            <div class="olo-ps-title" :style="titleStyle" :data-olo-editable="'panels.' + (offset + i) + '.title'">{{ panel.title }}</div>
            <div class="olo-ps-text" :style="textStyle" :data-olo-editable="'panels.' + (offset + i) + '.content'">{{ panel.content }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Arrows -->
    <template v-if="showArrows && panels.length > maxVisible">
      <button class="olo-ps-arrow olo-ps-prev" :style="arrowStyleObj" @click.stop="prev" :aria-label="t('Precedente')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" :stroke-width="arrowStrokeWidth" stroke-linecap="round" stroke-linejoin="round" :style="prevSvgStyle"><polyline :points="arrowPoints"/><line v-if="arrowStyle==='arrow-long'" x1="3" y1="12" x2="20" y2="12"/></svg>
      </button>
      <button class="olo-ps-arrow olo-ps-next" :style="arrowStyleObj" @click.stop="next" :aria-label="t('Successivo')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" :stroke-width="arrowStrokeWidth" stroke-linecap="round" stroke-linejoin="round"><polyline :points="arrowPoints"/><line v-if="arrowStyle==='arrow-long'" x1="3" y1="12" x2="20" y2="12"/></svg>
      </button>
    </template>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  columns: '3',
  card_style: 'default',
  card_radius: '8',
  card_padding: { top: 16, right: 16, bottom: 16, left: 16 },
  equal_height: true,
  image_ratio: '4/3',
  image_height: '',
  image_fit: 'cover',
  show_arrows: true,
  arrow_style: 'circle',
  arrow_size: '40',
  arrow_color: '',
  arrow_bg: '',
  shadow: 'none',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const offset = ref(0);

const panels = computed(() => {
  const raw = s.value.panels;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { id: 'ps-1', title: 'Card 1', content: 'Contenuto...', image: '' },
    { id: 'ps-2', title: 'Card 2', content: 'Contenuto...', image: '' },
    { id: 'ps-3', title: 'Card 3', content: 'Contenuto...', image: '' },
  ];
});

const maxVisible = computed(() => Math.min(parseInt(s.value.columns) || 3, 4));
const showArrows = computed(() => s.value.show_arrows !== false);
const arrowStyle = computed(() => s.value.arrow_style || 'circle');
const imageFit = computed(() => s.value.image_fit || 'cover');

const visiblePanels = computed(() => {
  const start = offset.value;
  const end = start + maxVisible.value;
  return panels.value.slice(start, end);
});

const trackStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${maxVisible.value}, 1fr)`,
  gap: gapPx.value,
  alignItems: s.value.equal_height ? 'stretch' : 'flex-start',
}));

const gapPx = computed(() => {
  const m = { collapse: '0px', small: '8px', default: '16px', medium: '24px', large: '32px' };
  return m[s.value.gap] || '16px';
});

const radiusCss = computed(() => {
  const r = s.value.card_radius;
  if (r && typeof r === 'object') {
    const tl = parseInt(r.tl) || 0, tr = parseInt(r.tr) || 0, br = parseInt(r.br) || 0, bl = parseInt(r.bl) || 0;
    return `${tl}px ${tr}px ${br}px ${bl}px`;
  }
  return (parseInt(r) || 0) + 'px';
});

const shadowCss = computed(() => {
  const map = {
    sm: '0 4px 12px rgba(0,0,0,.08)',
    md: '0 8px 30px rgba(0,0,0,.12)',
    lg: '0 16px 48px rgba(0,0,0,.18)',
    xl: '0 24px 64px rgba(0,0,0,.22)',
  };
  const v = s.value.shadow || 'none';
  if (v === 'custom') {
    const h = parseInt(s.value.shadow_h) || 0;
    const vv = parseInt(s.value.shadow_v) || 4;
    const b = parseInt(s.value.shadow_blur) || 10;
    const sp = parseInt(s.value.shadow_spread) || 0;
    const c = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${vv}px ${b}px ${sp}px ${c}`;
  }
  return map[v] || 'none';
});

const cardStyle = computed(() => {
  const styleKey = s.value.card_style || 'default';
  const bgMap = {
    default: '#ffffff',
    primary: 'var(--olo-color-primary, #6366F1)',
    secondary: '#f3f4f6',
    hover: '#ffffff',
  };
  return {
    background: bgMap[styleKey] || '#ffffff',
    borderRadius: radiusCss.value,
    boxShadow: shadowCss.value,
    overflow: 'hidden',
    display: 'flex',
    flexDirection: 'column',
    height: s.value.equal_height ? '100%' : 'auto',
    color: styleKey === 'primary' ? '#fff' : 'inherit',
    border: styleKey === 'default' ? '1px solid #e5e7eb' : 'none',
  };
});

const mediaStyle = computed(() => {
  const ratio = s.value.image_ratio;
  const h = parseInt(s.value.image_height) || 0;
  const style = {
    position: 'relative',
    overflow: 'hidden',
    width: '100%',
    flex: '0 0 auto',
  };
  if (ratio && ratio !== 'auto') {
    style.aspectRatio = ratio;
  } else if (h > 0) {
    style.height = h + 'px';
  } else {
    style.height = '160px';
  }
  return style;
});

const paddingCss = computed(() => {
  const p = s.value.card_padding;
  if (p && typeof p === 'object') {
    return `${parseInt(p.top)||0}px ${parseInt(p.right)||0}px ${parseInt(p.bottom)||0}px ${parseInt(p.left)||0}px`;
  }
  return (parseInt(p) || 16) + 'px';
});

const bodyStyle = computed(() => ({
  padding: paddingCss.value,
  flex: s.value.equal_height ? '1 1 auto' : '0 0 auto',
  display: 'flex',
  flexDirection: 'column',
  gap: '6px',
}));

const titleStyle = computed(() => {
  const sz = parseInt(s.value.title_size) || 0;
  const c = s.value.title_color || '';
  return {
    fontWeight: '700',
    fontSize: sz > 0 ? sz + 'px' : '1.05em',
    lineHeight: '1.3',
    color: c || (s.value.card_style === 'primary' ? '#fff' : 'inherit'),
  };
});

const textStyle = computed(() => {
  const sz = parseInt(s.value.content_size) || 0;
  const c = s.value.content_color || '';
  return {
    fontSize: sz > 0 ? sz + 'px' : '0.9em',
    lineHeight: '1.55',
    color: c || (s.value.card_style === 'primary' ? 'rgba(255,255,255,.85)' : '#666'),
  };
});

const arrowSize = computed(() => parseInt(s.value.arrow_size) || 40);
const arrowColor = computed(() => s.value.arrow_color || '#1f2937');
const arrowBg = computed(() => s.value.arrow_bg || '#ffffff');

const arrowStyleObj = computed(() => {
  const size = arrowSize.value;
  const half = Math.round(size / 2);
  const base = {
    width: size + 'px',
    height: size + 'px',
    border: 'none',
    cursor: 'pointer',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    transition: 'all 0.25s ease',
    color: arrowColor.value,
    background: arrowBg.value,
  };
  switch (arrowStyle.value) {
    case 'circle':
      return { ...base, borderRadius: '50%', boxShadow: '0 4px 12px rgba(0,0,0,.15)' };
    case 'circle-outline':
      return { ...base, borderRadius: '50%', background: 'transparent', border: `2px solid ${arrowColor.value}` };
    case 'square':
      return { ...base, borderRadius: '6px', boxShadow: '0 4px 12px rgba(0,0,0,.15)' };
    case 'minimal':
      return { ...base, background: 'transparent', boxShadow: 'none' };
    case 'chevron-bold':
      return { ...base, background: 'transparent', boxShadow: 'none' };
    case 'arrow-long':
      return { ...base, width: (size + 12) + 'px', borderRadius: half + 'px', boxShadow: '0 4px 12px rgba(0,0,0,.15)' };
    case 'fancy':
      return { ...base, borderRadius: '50%', background: 'linear-gradient(135deg,var(--olo-color-primary,#6366F1),#8b5cf6)', color: '#fff', boxShadow: '0 6px 20px rgba(99,102,241,.35)' };
    case 'uikit':
    default:
      return { ...base, background: 'rgba(0,0,0,0.6)', color: '#fff', borderRadius: '50%' };
  }
});

const arrowStrokeWidth = computed(() => arrowStyle.value === 'chevron-bold' ? 3 : 2.2);
const arrowPoints = computed(() => '9 6 15 12 9 18');
const prevSvgStyle = computed(() => ({ transform: 'rotate(180deg)', width: arrowStyle.value === 'minimal' ? '80%' : '50%', height: arrowStyle.value === 'minimal' ? '80%' : '50%' }));

function next() {
  if (offset.value + maxVisible.value < panels.value.length) {
    offset.value++;
  }
}

function prev() {
  if (offset.value > 0) {
    offset.value--;
  }
}
</script>

<style scoped>
.olo-panelslider {
  min-height: 80px;
}

.olo-ps-arrow {
  position: absolute;
  top: 50%;
  z-index: 5;
}

.olo-ps-arrow svg {
  width: 50%;
  height: 50%;
  pointer-events: none;
}

.olo-ps-arrow:hover {
  transform: translateY(-50%) scale(1.08);
}

.olo-ps-prev {
  left: -8px;
  transform: translateY(-50%);
}

.olo-ps-next {
  right: -8px;
  transform: translateY(-50%);
}

/* Push arrows out of viewport on wide canvas, stay in on mobile */
@media (min-width: 720px) {
  .olo-ps-prev { left: -22px; }
  .olo-ps-next { right: -22px; }
}
</style>
