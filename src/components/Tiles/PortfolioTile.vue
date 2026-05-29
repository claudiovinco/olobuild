<template>
  <div class="olo-portfolio-tile" :class="['olo-pf-preset-'+preset, 'olo-pf-layout-'+layout]" :style="wrapStyle">
    <input v-if="s.enable_search" type="text" class="olo-pf-search-prev" :placeholder="s.search_placeholder || 'Cerca…'" readonly :style="searchStyle" />

    <!-- Filter bar preview -->
    <div v-if="s.filter_bar" class="olo-pf-filter-preview" :style="filterBarStyle">
      <span v-for="(cat, idx) in previewCategories" :key="idx"
        :style="idx === 0 ? activeFilterStyle : inactiveFilterStyle" :class="filterItemClass"
        v-bind="idx === 0 ? { 'data-olo-editable': 'filter_all_label' } : {}">
        {{ cat }}
      </span>
    </div>

    <!-- Grid preview -->
    <div :style="gridStyle" class="olo-pf-grid-prev">
      <div v-for="(item, idx) in previewItems" :key="item.id || idx"
        class="olo-pf-card" :class="cardClasses(item, idx)" :style="cardStyle(idx)">
        <!-- Index numbering -->
        <span v-if="s.index_numbering" class="olo-pf-index-prev" :style="indexStyle">
          {{ String(idx + 1).padStart(2, '0') }}/{{ String(previewItems.length).padStart(2, '0') }}
        </span>
        <!-- External link badge -->
        <span v-if="s.external_link_badge && isExternal(item)" class="olo-pf-ext-prev">↗</span>
        <!-- Featured ribbon -->
        <span v-if="s.featured_ribbon && item.featured" class="olo-pf-ribbon-prev" :style="ribbonStyle">
          {{ s.featured_ribbon_text || 'Featured' }}
        </span>

        <!-- Image area -->
        <div :style="imageAreaStyle(idx)">
          <img v-if="item.image_url" :src="item.image_url" :alt="item.title"
            :style="imgStyle" :class="{ 'olo-pf-grayscale': s.grayscale_default }" />
          <div v-else :style="placeholderStyle">
            <span style="font-size:24px;opacity:0.5;">{{ t('&#x1F5BC;') }}</span>
          </div>
          <!-- Year stamp -->
          <span v-if="s.year_stamp && item.year" class="olo-pf-year-prev">{{ item.year }}</span>
          <!-- Hover overlay preview -->
          <div v-if="['fade','overlay','slide-up','reveal-mask','tilt-3d','caption-corner'].includes(s.hover_effect)"
            class="olo-pf-overlay" :style="overlayPreviewStyle">
            <span v-if="s.show_title" style="font-weight:600;font-size:12px;">{{ item.title }}</span>
          </div>
        </div>

        <!-- Text content below image -->
        <div v-if="showTextBelow(idx)" :style="textBelowStyle">
          <div v-if="s.show_category && item.category" :style="catStyle" :data-olo-editable="`items.${idx}.category`">{{ item.category }}</div>
          <div v-if="s.show_title" :style="titleStyle" :data-olo-editable="`items.${idx}.title`">{{ item.title }}</div>
          <div v-if="s.show_excerpt && item.description" :style="descStyle" :data-olo-editable="`items.${idx}.description`">{{ item.description }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';
import { radiusToCss as radiusToCssRaw } from '@/composables/useRadius';
const radiusToCss = (r) => radiusToCssRaw(r, { fallback: null, zero: '0', acceptPrimitive: false });

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  preset: 'editorial-magazine',
  source: 'manual',
  columns: '3',
  gap: '20',
  filter_bar: true,
  filter_style: 'buttons',
  filter_all_label: 'Tutti',
  filter_color: '',
  filter_active_color: '',
  layout: 'grid',
  hover_effect: 'fade',
  caption_position: 'overlay',
  caption_corner: 'bottom-left',
  show_title: true,
  show_category: true,
  show_excerpt: false,
  title_color: '',
  text_color: '',
  bg_color: '',
  accent_color: '',
  overlay_color: '#000000',
  overlay_opacity: '80',
  image_ratio: '4:3',
  border_radius: '8',
  font_family: 'inherit',
  font_weight: '500',
  text_transform: 'none',
  letter_spacing: 0,
  enable_search: false,
  search_placeholder: 'Cerca…',
  cursor_label_enabled: false,
  cursor_label_text: 'Vedi progetto',
  stagger_entrance: false,
  dim_others: false,
  featured_ribbon: false,
  featured_ribbon_text: 'Featured',
  year_stamp: false,
  index_numbering: false,
  external_link_badge: false,
  grayscale_default: false,
  container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
  container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
  effect_color: '',
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const preset = computed(() => s.value.preset || 'custom');
const layout = computed(() => s.value.layout || 'grid');

const previewItems = computed(() => {
  const items = Array.isArray(props.settings.items) ? props.settings.items : [];
  if (items.length > 0) return items.slice(0, 8);
  return [
    { id: 'ph-1', title: 'Progetto Alpha',   category: 'Design', description: 'Descrizione progetto.', image_url: '', link_url: '', year: '2026', featured: true },
    { id: 'ph-2', title: 'Progetto Beta',    category: 'Web',    description: 'Descrizione progetto.', image_url: '', link_url: '', year: '2026', featured: false },
    { id: 'ph-3', title: 'Progetto Gamma',   category: 'Design', description: 'Descrizione progetto.', image_url: '', link_url: '', year: '2025', featured: false },
    { id: 'ph-4', title: 'Progetto Delta',   category: 'Foto',   description: 'Descrizione progetto.', image_url: '', link_url: '', year: '2025', featured: true },
    { id: 'ph-5', title: 'Progetto Epsilon', category: 'Web',    description: 'Descrizione progetto.', image_url: '', link_url: '', year: '2024', featured: false },
    { id: 'ph-6', title: 'Progetto Zeta',    category: 'Foto',   description: 'Descrizione progetto.', image_url: '', link_url: '', year: '2024', featured: false },
  ];
});

const previewCategories = computed(() => {
  const cats = new Set();
  previewItems.value.forEach(item => { if (item.category) cats.add(item.category); });
  return [s.value.filter_all_label || 'Tutti', ...Array.from(cats)];
});

const ratioMap = { '1:1': '100%', '4:3': '75%', '16:9': '56.25%', '3:2': '66.67%', '3:4': '133.33%', 'auto': '70%' };
const cols = computed(() => Math.max(1, Math.min(6, parseInt(s.value.columns) || 3)));

function fontFamilyCss(v) {
  if (v === 'sans')  return 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif';
  if (v === 'serif') return 'Georgia, "Times New Roman", Times, serif';
  if (v === 'mono')  return 'ui-monospace, "SF Mono", Menlo, Consolas, monospace';
  return 'inherit';
}
// Composable condiviso — vedi src/composables/useRadius.js

const wrapStyle = computed(() => {
  const cp = s.value.container_padding || {};
  return {
    fontFamily: fontFamilyCss(s.value.font_family),
    fontWeight: s.value.font_weight || '500',
    textTransform: s.value.text_transform || 'none',
    letterSpacing: (parseFloat(s.value.letter_spacing) || 0) + 'px',
    background: s.value.bg_color || 'transparent',
    padding: `${cp.top || 0}px ${cp.right || 0}px ${cp.bottom || 0}px ${cp.left || 0}px`,
    borderRadius: radiusToCss(s.value.container_radius) || '0',
    color: resolveColor(s.value.text_color, TOKENS.textSoft),
    minHeight: '100px',
    position: 'relative',
  };
});

const gridStyle = computed(() => {
  const gap = parseInt(s.value.gap) || 20;
  if (layout.value === 'masonry' || layout.value === 'masonry-pin') {
    return { columnCount: cols.value, columnGap: gap + 'px' };
  }
  if (layout.value === 'bento') {
    return { display: 'grid', gridTemplateColumns: `repeat(${cols.value}, 1fr)`, gridAutoRows: 'minmax(120px, auto)', gap: gap + 'px' };
  }
  if (layout.value === 'magazine') {
    return { display: 'grid', gridTemplateColumns: '2fr 1fr 1fr', gridAutoRows: 'minmax(100px, auto)', gap: gap + 'px' };
  }
  if (layout.value === 'mosaic') {
    return { display: 'grid', gridTemplateColumns: `repeat(${cols.value}, 1fr)`, gridAutoRows: '160px', gridAutoFlow: 'dense', gap: gap + 'px' };
  }
  if (layout.value === 'split-index') {
    return { display: 'grid', gridTemplateColumns: '280px 1fr', gap: '40px' };
  }
  if (layout.value === 'carousel') {
    return { display: 'flex', gap: gap + 'px', overflow: 'hidden' };
  }
  if (layout.value === 'polaroid') {
    return { display: 'grid', gridTemplateColumns: `repeat(${cols.value}, 1fr)`, gap: gap + 'px', padding: '16px' };
  }
  if (layout.value === 'postcard-stack') {
    return { display: 'grid', gridTemplateColumns: `repeat(${cols.value}, 1fr)`, gap: gap + 'px', perspective: '1200px' };
  }
  return { display: 'grid', gridTemplateColumns: `repeat(${cols.value}, 1fr)`, gap: gap + 'px' };
});

function cardStyle(idx) {
  const style = {
    borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
    overflow: 'hidden',
    position: 'relative',
    transition: 'transform 0.3s ease',
  };
  if (layout.value === 'masonry' || layout.value === 'masonry-pin') {
    style.breakInside = 'avoid';
    style.marginBottom = (parseInt(s.value.gap) || 20) + 'px';
    style.display = 'inline-block';
    style.width = '100%';
  }
  if (layout.value === 'bento') {
    if (idx % 7 === 0) { style.gridColumn = 'span 2'; style.gridRow = 'span 2'; }
    if (idx % 7 === 3) { style.gridColumn = 'span 2'; }
    if (idx % 7 === 5) { style.gridRow = 'span 2'; }
    style.borderRadius = '20px';
  }
  if (layout.value === 'magazine' && idx === 0) {
    style.gridColumn = 'span 1';
    style.gridRow = 'span 2';
  }
  if (layout.value === 'mosaic') {
    if (idx % 5 === 0) style.gridRow = 'span 2';
    if (idx % 5 === 2) style.gridColumn = 'span 2';
  }
  if (layout.value === 'carousel') {
    style.flexShrink = '0';
    style.width = '260px';
  }
  if (layout.value === 'polaroid') {
    style.background = '#fff';
    style.padding = '14px 14px 36px';
    style.boxShadow = '0 6px 18px rgba(15,23,42,0.12)';
    const rotations = [-1.5, 0.8, -0.6, 1.2];
    style.transform = `rotate(${rotations[idx % rotations.length]}deg)`;
    style.borderRadius = '0';
  }
  return style;
}

function cardClasses(item, idx) {
  return {
    'is-featured': item.featured,
    'is-first': idx === 0,
  };
}

function imageAreaStyle(idx) {
  const skipPad = ['masonry-pin', 'split-index', 'postcard-stack', 'polaroid'].includes(layout.value);
  const isBentoLarge = layout.value === 'bento' && idx % 7 === 0;
  const isMagazineFirst = layout.value === 'magazine' && idx === 0;
  const fullHeight = isBentoLarge || isMagazineFirst || layout.value === 'mosaic';

  const base = {
    position: 'relative',
    overflow: 'hidden',
    borderRadius: 'inherit',
    background: TOKENS.surfaceAlt,
  };

  if (fullHeight) {
    base.height = '100%';
    base.minHeight = '160px';
  } else if (skipPad) {
    base.paddingTop = ratioMap[s.value.image_ratio] || '75%';
  } else {
    base.paddingTop = ratioMap[s.value.image_ratio] || '75%';
  }

  return base;
}

const imgStyle = computed(() => ({
  position: 'absolute',
  inset: '0',
  width: '100%',
  height: '100%',
  objectFit: 'cover',
  display: 'block',
}));

const placeholderStyle = computed(() => ({
  position: 'absolute',
  inset: '0',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  background: TOKENS.border,
}));

const overlayPreviewStyle = computed(() => ({
  position: 'absolute',
  inset: '0',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  background: s.value.overlay_color || '#000000',
  opacity: '0.35',
  color: '#fff',
  transition: 'opacity 0.3s ease',
  borderRadius: 'inherit',
  pointerEvents: 'none',
}));

const filterBarStyle = computed(() => ({
  display: 'flex', flexWrap: 'wrap', gap: '8px', marginBottom: '16px', padding: '4px 0',
}));

const searchStyle = computed(() => ({
  width: '100%', padding: '8px 12px', border: '1px solid rgba(0,0,0,0.15)',
  borderRadius: '8px', marginBottom: '12px', boxSizing: 'border-box', fontSize: '13px',
  background: 'rgba(255,255,255,0.6)', pointerEvents: 'none',
}));

const filterItemClass = computed(() => 'olo-pf-filter-' + (s.value.filter_style || 'buttons'));

const activeFilterStyle = computed(() => {
  const style = s.value.filter_style || 'buttons';
  const activeC = resolveColor(s.value.filter_active_color, TOKENS.primary);
  const base = { fontSize: '11px', fontWeight: '600', cursor: 'pointer', transition: 'all 0.2s', color: TOKENS.onPrimary };
  if (style === 'pills') { base.padding = '4px 14px'; base.borderRadius = '20px'; base.background = activeC; }
  else if (style === 'underline') { base.padding = '4px 8px'; base.borderBottom = '2px solid ' + activeC; base.background = 'transparent'; base.color = activeC; }
  else { base.padding = '4px 12px'; base.borderRadius = '4px'; base.background = activeC; }
  return base;
});

const inactiveFilterStyle = computed(() => {
  const style = s.value.filter_style || 'buttons';
  const base = { fontSize: '11px', fontWeight: '500', cursor: 'pointer', transition: 'all 0.2s', color: resolveColor(s.value.filter_color, TOKENS.textSoft) };
  if (style === 'pills') { base.padding = '4px 14px'; base.borderRadius = '20px'; base.background = TOKENS.surfaceAlt; }
  else if (style === 'underline') { base.padding = '4px 8px'; base.borderBottom = '2px solid transparent'; base.background = 'transparent'; }
  else { base.padding = '4px 12px'; base.borderRadius = '4px'; base.background = TOKENS.surfaceAlt; }
  return base;
});

const textBelowStyle = computed(() => ({ padding: '12px 4px 4px', position: 'relative' }));

const catStyle = computed(() => ({
  fontSize: '10px',
  color: resolveColor(s.value.accent_color || s.value.filter_active_color, TOKENS.primary),
  fontWeight: '600',
  textTransform: 'uppercase',
  letterSpacing: '0.5px',
  marginBottom: '2px',
}));

const titleStyle = computed(() => ({
  fontSize: '13px',
  fontWeight: '600',
  color: resolveColor(s.value.title_color, TOKENS.text),
}));

const descStyle = computed(() => ({
  fontSize: '11px',
  color: resolveColor(s.value.text_color, TOKENS.textSoft),
  marginTop: '4px',
  lineHeight: '1.4',
}));

const ribbonStyle = computed(() => ({
  position: 'absolute',
  top: '12px',
  left: '-26px',
  background: resolveColor(s.value.accent_color, TOKENS.primary),
  color: TOKENS.onPrimary,
  padding: '3px 28px',
  fontSize: '9px',
  fontWeight: '700',
  textTransform: 'uppercase',
  letterSpacing: '1px',
  transform: 'rotate(-45deg)',
  boxShadow: '0 4px 8px rgba(0,0,0,0.15)',
  zIndex: 3,
  pointerEvents: 'none',
}));

const indexStyle = computed(() => ({
  position: 'absolute',
  top: '10px',
  left: '12px',
  fontSize: '9px',
  fontWeight: '600',
  letterSpacing: '2px',
  color: resolveColor(s.value.accent_color, TOKENS.primary),
  fontFamily: 'ui-monospace, monospace',
  background: 'rgba(255,255,255,0.9)',
  padding: '2px 6px',
  borderRadius: '3px',
  zIndex: 3,
}));

function showTextBelow(idx) {
  if (s.value.hover_effect === 'overlay' && s.value.caption_position === 'overlay') return false;
  if (!s.value.show_title && !s.value.show_category && !s.value.show_excerpt) return false;
  return true;
}

function isExternal(item) {
  if (!item.link_url) return false;
  try {
    const u = new URL(item.link_url);
    return u.host && !u.host.includes(window.location.host);
  } catch (e) { return false; }
}
</script>

<style scoped>
.olo-portfolio-tile { min-height: 100px; }
.olo-pf-card:hover .olo-pf-overlay { opacity: 0.85 !important; }
.olo-pf-grayscale { filter: grayscale(80%); transition: filter 400ms ease; }
.olo-pf-card:hover .olo-pf-grayscale { filter: grayscale(0%); }
.olo-pf-ext-prev {
  position: absolute; top: 8px; right: 8px; width: 22px; height: 22px;
  border-radius: 50%; background: rgba(255,255,255,0.95); color: #0f172a;
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 700; z-index: 3;
  box-shadow: 0 2px 6px rgba(15,23,42,0.15);
}
.olo-pf-year-prev {
  position: absolute; right: 6px; bottom: 0; font-size: 4em; font-weight: 900;
  color: rgba(255,255,255,0.18); line-height: 1; pointer-events: none;
  font-family: Georgia, serif; letter-spacing: -0.04em;
}

/* Preset hints (preview) */
.olo-pf-preset-cinema-showcase { background: #0a0a0a !important; color: #fff; padding: 16px; }
.olo-pf-preset-cinema-showcase :deep(.olo-pf-card) { background: #0a0a0a; }

.olo-pf-preset-photographer-mosaic { background: #0a0a0a !important; }
.olo-pf-preset-photographer-mosaic :deep(.olo-pf-card img) { filter: grayscale(80%); transition: filter 400ms ease; }
.olo-pf-preset-photographer-mosaic :deep(.olo-pf-card:hover img) { filter: grayscale(0%); }

.olo-pf-preset-architect-line :deep(.olo-pf-card) { border: 1px solid #000; background: #fff; }
.olo-pf-preset-architect-line :deep(img) { filter: contrast(1.15) grayscale(40%); }

.olo-pf-preset-dribbble-cards :deep(.olo-pf-card) { box-shadow: 0 4px 12px rgba(15,23,42,0.06); transition: transform 250ms ease; }
.olo-pf-preset-dribbble-cards :deep(.olo-pf-card:hover) { transform: translateY(-3px); }

.olo-pf-preset-postcard-stack :deep(.olo-pf-card) {
  position: relative; transition: transform 350ms cubic-bezier(0.34,1.56,0.64,1);
}
.olo-pf-preset-postcard-stack :deep(.olo-pf-card)::before,
.olo-pf-preset-postcard-stack :deep(.olo-pf-card)::after {
  content: ''; position: absolute; inset: 0; border-radius: inherit; background: #fff;
  box-shadow: 0 4px 12px rgba(15,23,42,0.08); z-index: -1;
}
.olo-pf-preset-postcard-stack :deep(.olo-pf-card)::before { transform: translate(6px, 6px) rotate(2deg); }
.olo-pf-preset-postcard-stack :deep(.olo-pf-card)::after  { transform: translate(12px, 12px) rotate(-2deg); }
</style>
