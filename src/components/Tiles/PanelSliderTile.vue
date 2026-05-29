<template>
  <div class="olo-panelslider" :class="['olo-ps--preset-' + (s.preset || 'card-modern'), 'olo-ps--img-' + imgPosition, 'olo-ps-arrows-' + arrowStyle]" style="position:relative;">
    <div class="olo-ps-viewport" style="overflow:hidden;">
      <div class="olo-ps-track" :style="trackStyle">
        <div
          v-for="(panel, i) in visiblePanels"
          :key="panel.id || i"
          class="olo-ps-card"
          :class="'olo-ps-card--' + imgPosition"
          :style="cardStyle"
        >
          <!-- Image (top, side, or background) -->
          <div
            v-if="panel.image && imgPosition !== 'bottom'"
            class="olo-ps-media"
            :style="mediaStyle"
          >
            <img :src="panel.image" :alt="panel.title || ''" class="olo-ps-img" :style="imgStyle" />
          </div>

          <!-- Background overlay (only for bg position) -->
          <div v-if="imgPosition === 'bg' && panel.image" class="olo-ps-overlay" :style="overlayStyle"></div>

          <!-- Body -->
          <div class="olo-ps-body" :style="bodyStyle">
            <div class="olo-ps-title" :style="titleStyle" :data-olo-editable="'panels.' + (offset + i) + '.title'">{{ panel.title }}</div>
            <div class="olo-ps-text" :style="textStyle" :data-olo-editable="'panels.' + (offset + i) + '.content'">{{ panel.content }}</div>
            <span v-if="s.show_cta" class="olo-ps-cta" :class="'olo-ps-cta--' + (s.cta_style || 'underline')" :style="ctaStyle">
              {{ s.cta_text || 'Scopri di più' }}
              <span v-if="(s.cta_style || 'underline') === 'arrow'" aria-hidden="true">&rarr;</span>
            </span>
          </div>

          <!-- Image bottom -->
          <div
            v-if="panel.image && imgPosition === 'bottom'"
            class="olo-ps-media"
            :style="mediaStyle"
          >
            <img :src="panel.image" :alt="panel.title || ''" class="olo-ps-img" :style="imgStyle" />
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
  card_radius: '12',
  card_padding: { top: 24, right: 24, bottom: 24, left: 24 },
  equal_height: true,
  image_ratio: '4/3',
  image_height: '',
  image_fit: 'cover',
  show_arrows: true,
  arrow_style: 'circle',
  arrow_size: '40',
  arrow_color: '',
  arrow_bg: '',
  shadow: 'sm',
  preset: 'card-modern',
  card_image_position: 'top',
  card_bg: '#ffffff',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const offset = ref(0);
const imgPosition = computed(() => s.value.card_image_position || 'top');

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
  const m = { collapse: '0px', small: '8px', default: '16px', medium: '20px', large: '28px' };
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
  const isSide = imgPosition.value === 'side-left' || imgPosition.value === 'side-right';
  const bw = parseInt(s.value.card_border_width) || 0;
  const bcol = s.value.card_border_color || 'transparent';
  return {
    background: s.value.card_bg || '#ffffff',
    borderRadius: radiusCss.value,
    boxShadow: shadowCss.value,
    overflow: 'hidden',
    display: 'flex',
    flexDirection: imgPosition.value === 'side-left' ? 'row' : (imgPosition.value === 'side-right' ? 'row-reverse' : 'column'),
    height: s.value.equal_height ? '100%' : 'auto',
    color: 'inherit',
    border: bw > 0 && bcol !== 'transparent' ? `${bw}px ${s.value.card_border_style || 'solid'} ${bcol}` : '0',
    position: 'relative',
    minHeight: imgPosition.value === 'bg' ? '180px' : 'auto',
  };
});

const mediaStyle = computed(() => {
  const ratio = s.value.image_ratio;
  const h = parseInt(s.value.image_height) || 0;
  const isSide = imgPosition.value === 'side-left' || imgPosition.value === 'side-right';
  const isBg = imgPosition.value === 'bg';
  const radius = parseInt(s.value.card_image_radius) || 0;

  const style = {
    position: isBg ? 'absolute' : 'relative',
    overflow: 'hidden',
    width: isSide ? '45%' : '100%',
    flex: isSide ? '0 0 45%' : '0 0 auto',
    inset: isBg ? '0' : 'auto',
    zIndex: isBg ? '0' : 'auto',
    borderRadius: radius > 0 ? radius + 'px' : '',
  };
  if (isBg) {
    style.height = '100%';
  } else if (isSide) {
    style.height = '100%';
  } else if (ratio && ratio !== 'auto') {
    style.aspectRatio = ratio;
  } else if (h > 0) {
    style.height = h + 'px';
  } else {
    style.height = '120px';
  }
  return style;
});

const overlayStyle = computed(() => {
  const c = s.value.caption_overlay_color || 'rgba(0,0,0,0.55)';
  const grad = s.value.caption_overlay_gradient !== false;
  return {
    position: 'absolute',
    inset: 0,
    background: grad ? `linear-gradient(180deg, rgba(0,0,0,0) 0%, ${c} 100%)` : c,
    pointerEvents: 'none',
    zIndex: 1,
  };
});

const imgStyle = computed(() => ({
  width: '100%',
  height: '100%',
  objectFit: s.value.image_fit || 'cover',
  display: 'block',
  borderRadius: (parseInt(s.value.card_image_radius) || 0) + 'px' || '',
}));

const paddingCss = computed(() => {
  const p = s.value.card_padding;
  if (p && typeof p === 'object') {
    const shrink = (n) => Math.max(6, Math.round((parseInt(n) || 0) * 0.6));
    return `${shrink(p.top)}px ${shrink(p.right)}px ${shrink(p.bottom)}px ${shrink(p.left)}px`;
  }
  return Math.max(6, Math.round((parseInt(p) || 16) * 0.6)) + 'px';
});

const bodyStyle = computed(() => {
  const isBg = imgPosition.value === 'bg';
  return {
    padding: paddingCss.value,
    flex: s.value.equal_height ? '1 1 auto' : '0 0 auto',
    display: 'flex',
    flexDirection: 'column',
    gap: '4px',
    position: isBg ? 'relative' : 'static',
    zIndex: isBg ? '2' : 'auto',
    marginTop: isBg ? 'auto' : '0',
    color: isBg ? '#ffffff' : 'inherit',
  };
});

const titleStyle = computed(() => {
  const sz = parseInt(s.value.title_size) || 0;
  const c = s.value.title_color || '';
  const isBg = imgPosition.value === 'bg';
  return {
    fontWeight: s.value.title_weight || '700',
    fontSize: sz > 0 ? Math.max(10, Math.round(sz * 0.6)) + 'px' : '12px',
    lineHeight: '1.3',
    letterSpacing: (parseFloat(s.value.title_letter_spacing) || 0) + 'em',
    textTransform: s.value.title_uppercase ? 'uppercase' : 'none',
    textAlign: s.value.title_align || 'left',
    color: isBg ? '#ffffff' : (c || 'inherit'),
    margin: '0',
  };
});

const textStyle = computed(() => {
  const sz = parseInt(s.value.content_size) || 0;
  const c = s.value.content_color || '';
  const clamp = parseInt(s.value.content_lines_clamp) || 0;
  const isBg = imgPosition.value === 'bg';
  const base = {
    fontSize: sz > 0 ? Math.max(8, Math.round(sz * 0.65)) + 'px' : '9px',
    lineHeight: '1.55',
    textAlign: s.value.content_align || 'left',
    color: isBg ? 'rgba(255,255,255,0.9)' : (c || '#666'),
    margin: '0',
  };
  if (clamp > 0) {
    base.display = '-webkit-box';
    base.WebkitLineClamp = String(clamp);
    base.WebkitBoxOrient = 'vertical';
    base.overflow = 'hidden';
  } else {
    base.display = '-webkit-box';
    base.WebkitLineClamp = '3';
    base.WebkitBoxOrient = 'vertical';
    base.overflow = 'hidden';
  }
  return base;
});

const ctaStyle = computed(() => {
  const style = s.value.cta_style || 'underline';
  const base = {
    display: 'inline-flex',
    alignItems: 'center',
    gap: '4px',
    fontSize: '8px',
    fontWeight: '600',
    marginTop: '6px',
    width: 'fit-content',
    color: 'var(--olo-color-primary, #e8622a)',
  };
  if (style === 'underline') return { ...base, borderBottom: '1px solid currentColor', paddingBottom: '1px' };
  if (style === 'pill') return { ...base, background: 'var(--olo-color-primary, #e8622a)', color: '#fff', borderRadius: '999px', padding: '3px 10px' };
  return base;
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
    case 'circle':         return { ...base, borderRadius: '50%', boxShadow: '0 4px 12px rgba(0,0,0,.15)' };
    case 'circle-outline': return { ...base, borderRadius: '50%', background: 'transparent', border: `2px solid ${arrowColor.value}` };
    case 'square':         return { ...base, borderRadius: '6px', boxShadow: '0 4px 12px rgba(0,0,0,.15)' };
    case 'minimal':        return { ...base, background: 'transparent', boxShadow: 'none' };
    case 'chevron-bold':   return { ...base, background: 'transparent', boxShadow: 'none' };
    case 'arrow-long':     return { ...base, width: (size + 12) + 'px', borderRadius: half + 'px', boxShadow: '0 4px 12px rgba(0,0,0,.15)' };
    case 'fancy':          return { ...base, borderRadius: '50%', background: 'linear-gradient(135deg,var(--olo-color-primary,#6366F1),#8b5cf6)', color: '#fff', boxShadow: '0 6px 20px rgba(99,102,241,.35)' };
    case 'uikit':
    default:               return { ...base, background: 'rgba(0,0,0,0.6)', color: '#fff', borderRadius: '50%' };
  }
});

const arrowStrokeWidth = computed(() => arrowStyle.value === 'chevron-bold' ? 3 : 2.2);
const arrowPoints = computed(() => '9 6 15 12 9 18');
const prevSvgStyle = computed(() => ({ transform: 'rotate(180deg)', width: arrowStyle.value === 'minimal' ? '80%' : '50%', height: arrowStyle.value === 'minimal' ? '80%' : '50%' }));

function next() {
  if (offset.value + maxVisible.value < panels.value.length) offset.value++;
}
function prev() {
  if (offset.value > 0) offset.value--;
}
</script>

<style scoped>
.olo-panelslider { min-height: 80px; }

.olo-ps-card { background: #fff; }
.olo-ps-arrow { position: absolute; top: 50%; z-index: 5; }
.olo-ps-arrow svg { width: 50%; height: 50%; pointer-events: none; }
.olo-ps-arrow:hover { transform: translateY(-50%) scale(1.08); }
.olo-ps-prev { left: -8px; transform: translateY(-50%); }
.olo-ps-next { right: -8px; transform: translateY(-50%); }
@media (min-width: 720px) {
  .olo-ps-prev { left: -22px; }
  .olo-ps-next { right: -22px; }
}

/* ───── Preset visual hints in builder ───── */

/* Liquid Glass */
.olo-ps--preset-liquid-glass .olo-ps-card {
  background: rgba(255,255,255,0.55) !important;
  backdrop-filter: blur(12px) saturate(180%);
  -webkit-backdrop-filter: blur(12px) saturate(180%);
  border: 1px solid rgba(255,255,255,0.4) !important;
  box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important;
}

/* Neon Cyber */
.olo-ps--preset-neon-cyber .olo-ps-card {
  background: #0a0f1c !important;
  border: 1px solid rgba(255,106,42,0.35) !important;
  box-shadow: 0 0 14px rgba(255,106,42,0.25) !important;
}
.olo-ps--preset-neon-cyber .olo-ps-title {
  color: #ff6a2a !important;
  text-shadow: 0 0 6px rgba(255,106,42,0.5);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.olo-ps--preset-neon-cyber .olo-ps-text { color: rgba(255,255,255,0.75) !important; }

/* Brutalist */
.olo-ps--preset-brutalist-block .olo-ps-card {
  background: #fff !important;
  border: 3px solid #000 !important;
  box-shadow: 5px 5px 0 0 #000 !important;
  border-radius: 0 !important;
}
.olo-ps--preset-brutalist-block .olo-ps-media {
  border-bottom: 3px solid #000;
}
.olo-ps--preset-brutalist-block .olo-ps-title {
  font-weight: 900 !important;
  text-transform: uppercase;
  color: #000 !important;
}
.olo-ps--preset-brutalist-block .olo-ps-cta {
  border: 2px solid #000 !important;
  box-shadow: 2px 2px 0 0 #000;
  background: #fff !important;
  color: #000 !important;
  border-radius: 0 !important;
}

/* Magnetic */
.olo-ps--preset-magnetic-liquid .olo-ps-card {
  border-radius: 18px !important;
  box-shadow: 0 6px 18px rgba(232,98,42,0.10) !important;
}
.olo-ps--preset-magnetic-liquid .olo-ps-cta {
  background: linear-gradient(135deg, #e8622a 0%, #ff8a5b 100%) !important;
  color: #fff !important;
  border-radius: 999px !important;
  padding: 4px 12px !important;
  border: 0 !important;
}

/* Sticker */
.olo-ps--preset-sticker .olo-ps-card {
  background: #fff !important;
  border: 1.2px dashed rgba(232,98,42,0.5) !important;
  box-shadow: 0 6px 14px rgba(0,0,0,0.10) !important;
}
.olo-ps--preset-sticker .olo-ps-track > .olo-ps-card:nth-child(3n+1) { transform: rotate(-1.2deg); }
.olo-ps--preset-sticker .olo-ps-track > .olo-ps-card:nth-child(3n+2) { transform: rotate(0.8deg); }
.olo-ps--preset-sticker .olo-ps-track > .olo-ps-card:nth-child(3n+3) { transform: rotate(-0.5deg); }

/* Retro Terminal */
.olo-ps--preset-retro-terminal .olo-ps-card {
  background: #0c0c0c !important;
  border: 1px solid rgba(0,255,140,0.3) !important;
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace !important;
  background-image: repeating-linear-gradient(0deg, transparent 0, transparent 2px, rgba(0,255,140,0.04) 2px, rgba(0,255,140,0.04) 3px);
  border-radius: 0 !important;
}
.olo-ps--preset-retro-terminal .olo-ps-img {
  filter: hue-rotate(70deg) saturate(2) brightness(0.6);
  opacity: 0.8;
}
.olo-ps--preset-retro-terminal .olo-ps-title {
  color: #00ff8c !important;
  text-shadow: 0 0 6px rgba(0,255,140,0.5);
  font-family: inherit !important;
  text-transform: uppercase;
}
.olo-ps--preset-retro-terminal .olo-ps-title::before {
  content: '> ';
  opacity: 0.7;
}
.olo-ps--preset-retro-terminal .olo-ps-text {
  color: rgba(0,255,140,0.85) !important;
  font-family: inherit !important;
}

/* 3D Tilt */
.olo-ps--preset-3d-tilt .olo-ps-card {
  transform: perspective(800px) rotateX(2deg);
  transform-origin: center top;
  box-shadow: 0 16px 32px rgba(0,0,0,0.12) !important;
}

/* Polaroid */
.olo-ps--preset-polaroid .olo-ps-card {
  background: #fff !important;
  padding: 8px !important;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15) !important;
}

/* Editorial Magazine */
.olo-ps--preset-editorial-magazine .olo-ps-card {
  background: transparent !important;
  border: 0 !important;
  box-shadow: none !important;
  border-radius: 0 !important;
}
.olo-ps--preset-editorial-magazine .olo-ps-title {
  font-weight: 600;
  letter-spacing: 0.02em;
}

/* Overlay Caption */
.olo-ps--preset-overlay-caption.olo-ps--img-bg .olo-ps-card {
  border-radius: 12px !important;
}
</style>
