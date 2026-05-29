<template>
  <div class="olo-postmeta" :class="['olo-pm-preset-'+preset, 'olo-pm-chip-'+chipStyle]" :style="wrapStyle">
    <template v-for="(item, idx) in visibleItems" :key="item.key">
      <span v-if="idx > 0 && showSeparator" class="olo-postmeta-sep" :style="{ color: s.text_color }">{{ s.separator }}</span>
      <span class="olo-postmeta-item" :class="{ 'is-chip': chipStyle !== 'none' }" :style="itemStyle(idx)">
        <svg v-if="s.icon_style === 'before'" :style="{ color: s.icon_color }" class="olo-postmeta-icon" v-html="item.icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></svg>
        <a v-if="item.isLink" href="#" :style="{ color: s.link_color, textDecoration: 'none' }" v-html="item.label" @click.prevent></a>
        <span v-else v-html="item.label"></span>
      </span>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { radiusToCss as radiusToCssRaw } from '@/composables/useRadius';
const radiusToCss = (r) => radiusToCssRaw(r, { fallback: '0', zero: '0' });

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  preset: 'editorial-classic',
  show_date: true,
  show_author: true,
  show_categories: true,
  show_tags: false,
  show_comments_count: false,
  show_reading_time: false,
  date_format: 'd/m/Y',
  layout: 'inline',
  separator: ' · ',
  icon_style: 'none',
  text_color: '#9CA3AF',
  link_color: '#6366F1',
  icon_color: '#6B7280',
  bg_color: '',
  font_size: '14',
  font_family: 'inherit',
  font_weight: '400',
  text_transform: 'none',
  letter_spacing: 0,
  item_gap: 0,
  chip_style: 'none',
  chip_bg: '',
  chip_padding_x: 0,
  chip_padding_y: 0,
  chip_radius: 0,
  container_padding: { top: 0, right: 0, bottom: 0, left: 0 },
  container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
  effect_color: '',
  author_link: true,
  category_link: true,
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const preset = computed(() => s.value.preset || 'custom');
const chipStyle = computed(() => s.value.chip_style || 'none');
const showSeparator = computed(() =>
  s.value.layout !== 'stacked' && s.value.separator && chipStyle.value === 'none'
);

const icons = {
  date: '<line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/>',
  author: '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
  categories: '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>',
  tags: '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
  comments: '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
  reading_time: '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
};

function fmtPreviewDate() {
  const d = new Date();
  const fmt = s.value.date_format || 'd/m/Y';
  const dd = String(d.getDate()).padStart(2, '0');
  const mm = String(d.getMonth() + 1).padStart(2, '0');
  const yyyy = d.getFullYear();
  const monthsIT = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
  const monthsShortIT = ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];
  if (fmt === 'd/m/Y') return `${dd}/${mm}/${yyyy}`;
  if (fmt === 'j F Y') return `${d.getDate()} ${monthsIT[d.getMonth()]} ${yyyy}`;
  if (fmt === 'F j, Y') return `${monthsIT[d.getMonth()]} ${d.getDate()}, ${yyyy}`;
  if (fmt === 'Y-m-d') return `${yyyy}-${mm}-${dd}`;
  if (fmt === 'd M Y') return `${dd} ${monthsShortIT[d.getMonth()]} ${yyyy}`;
  return `${dd}/${mm}/${yyyy}`;
}

const visibleItems = computed(() => {
  const items = [];
  if (s.value.show_date) {
    items.push({ key: 'date', label: fmtPreviewDate(), icon: icons.date, isLink: false });
  }
  if (s.value.show_author) {
    items.push({ key: 'author', label: 'Claudio', icon: icons.author, isLink: !!s.value.author_link });
  }
  if (s.value.show_categories) {
    items.push({ key: 'categories', label: 'WordPress, Design', icon: icons.categories, isLink: !!s.value.category_link });
  }
  if (s.value.show_tags) {
    items.push({ key: 'tags', label: 'tutorial, guida', icon: icons.tags, isLink: false });
  }
  if (s.value.show_comments_count) {
    items.push({ key: 'comments', label: '3 commenti', icon: icons.comments, isLink: false });
  }
  if (s.value.show_reading_time) {
    items.push({ key: 'reading_time', label: '4 min di lettura', icon: icons.reading_time, isLink: false });
  }
  return items;
});

function fontFamilyCss(v) {
  if (v === 'sans')  return 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif';
  if (v === 'serif') return 'Georgia, "Times New Roman", Times, serif';
  if (v === 'mono')  return 'ui-monospace, "SF Mono", Menlo, Consolas, monospace';
  return 'inherit';
}

// Composable condiviso — vedi src/composables/useRadius.js

const wrapStyle = computed(() => {
  const cp = s.value.container_padding || {};
  const isStacked = s.value.layout === 'stacked';
  const gap = parseInt(s.value.item_gap) || 0;
  const st = {
    fontSize: (parseInt(s.value.font_size) || 14) + 'px',
    fontFamily: fontFamilyCss(s.value.font_family),
    fontWeight: s.value.font_weight || '400',
    textTransform: s.value.text_transform || 'none',
    letterSpacing: (parseFloat(s.value.letter_spacing) || 0) + 'px',
    background: s.value.bg_color || 'transparent',
    padding: `${cp.top || 0}px ${cp.right || 0}px ${cp.bottom || 0}px ${cp.left || 0}px`,
    borderRadius: radiusToCss(s.value.container_radius),
    color: s.value.text_color || '#9CA3AF',
    lineHeight: '1.6',
    position: 'relative',
    display: 'flex',
    flexDirection: isStacked ? 'column' : 'row',
    flexWrap: isStacked ? 'nowrap' : 'wrap',
    alignItems: isStacked ? 'flex-start' : 'center',
    gap: isStacked ? Math.max(4, gap) + 'px' : gap + 'px',
  };
  return st;
});

function itemStyle(idx) {
  const base = {
    display: 'inline-flex',
    alignItems: 'center',
    gap: '5px',
    color: s.value.text_color,
  };
  if (chipStyle.value !== 'none') {
    if (s.value.chip_bg) base.background = s.value.chip_bg;
    const px = parseInt(s.value.chip_padding_x) || 0;
    const py = parseInt(s.value.chip_padding_y) || 0;
    if (px || py) base.padding = `${py}px ${px}px`;
    const r = parseInt(s.value.chip_radius) || 0;
    if (r > 0) base.borderRadius = r + 'px';
    if (preset.value === 'sticker-scrap') {
      base.border = '2px dashed rgba(15,23,42,0.4)';
      const rotations = [-1.5, 1, -0.6, 1.2, -1, 0.8];
      base.transform = `rotate(${rotations[idx % rotations.length]}deg)`;
    } else if (preset.value === 'brutalist-stamp') {
      base.border = '2px solid #000';
      base.boxShadow = '2px 2px 0 0 #000';
    } else if (preset.value === 'glass-floating') {
      base.backdropFilter = 'blur(14px) saturate(160%)';
      base.WebkitBackdropFilter = 'blur(14px) saturate(160%)';
      base.border = '1px solid rgba(255,255,255,0.5)';
      base.boxShadow = '0 4px 16px rgba(15,23,42,0.08)';
    } else if (preset.value === 'tilt-3d') {
      base.transform = 'rotateX(-6deg)';
      base.transformOrigin = 'center bottom';
      base.boxShadow = '0 6px 0 rgba(15,23,42,0.12), 0 8px 16px rgba(15,23,42,0.08)';
    }
  }
  return base;
}
</script>

<style scoped>
.olo-postmeta-item { display: inline-flex; align-items: center; gap: 5px; }
.olo-postmeta-icon { flex-shrink: 0; }
.olo-postmeta-sep { display: inline-block; }

.olo-pm-preset-tilt-3d { perspective: 800px; }

.olo-pm-preset-neon-cyber { border: 1px solid rgba(255,106,42,0.25); }
.olo-pm-preset-neon-cyber .olo-postmeta-item a { color: v-bind('s.link_color || "#ff6a2a"') !important; text-shadow: 0 0 8px currentColor; }
.olo-pm-preset-neon-cyber .olo-postmeta-icon { filter: drop-shadow(0 0 6px currentColor); }

.olo-pm-preset-retro-terminal { font-variant-numeric: tabular-nums; }
.olo-pm-preset-retro-terminal::before {
  content: ''; position: absolute; inset: 0; pointer-events: none;
  background: repeating-linear-gradient(0deg, rgba(0,255,140,0.04) 0px, rgba(0,255,140,0.04) 1px, transparent 1px, transparent 3px);
}
.olo-pm-preset-retro-terminal .olo-postmeta-item::before { content: '> '; opacity: 0.6; }
.olo-pm-preset-retro-terminal .olo-postmeta-item:last-of-type::after {
  content: '_'; margin-left: 4px;
  animation: olo-pm-blink 1s steps(2) infinite;
}
@keyframes olo-pm-blink { 50% { opacity: 0; } }

.olo-pm-preset-gradient-glow .olo-postmeta-item {
  background: linear-gradient(90deg, #e8622a 0%, rgba(232,98,42,0.6) 50%, #e8622a 100%);
  background-size: 200% 100%;
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  color: transparent !important;
  animation: olo-pm-grad 4s ease-in-out infinite;
}
.olo-pm-preset-gradient-glow .olo-postmeta-icon {
  -webkit-text-fill-color: initial;
  color: #e8622a !important;
}
@keyframes olo-pm-grad {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.olo-pm-preset-underline-animated .olo-postmeta-item a {
  position: relative; text-decoration: none;
}
.olo-pm-preset-underline-animated .olo-postmeta-item a::after {
  content: ''; position: absolute; left: 0; bottom: -2px; width: 100%; height: 1px;
  background: currentColor;
  transform: scaleX(0); transform-origin: left;
  transition: transform 250ms ease;
}
.olo-pm-preset-underline-animated .olo-postmeta-item a:hover::after { transform: scaleX(1); }
</style>
