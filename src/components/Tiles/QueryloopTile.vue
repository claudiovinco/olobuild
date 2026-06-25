<template>
  <div class="olo-queryloop-preview" :class="['olo-ql-preset-'+preset, 'olo-ql-layout-'+layout, 'olo-ql-hover-'+hoverFx]" :style="wrapStyle">
    <div class="ql-header">
      <span class="ql-icon"><span class="dashicons dashicons-database"></span></span>
      <span class="ql-label">{{ t('Query Loop') }}</span>
      <span class="ql-info">{{ settings.post_type || 'post' }} &middot; {{ layout }} &middot; {{ settings.columns || 3 }} col</span>
    </div>

    <input v-if="settings.enable_search" type="text" class="ql-search" :placeholder="settings.search_placeholder || t('Cerca…')" readonly />

    <div v-if="settings.enable_sort_ui" class="ql-sort-wrap">
      <label>{{ t('Ordina:') }}
        <select disabled>
          <option>{{ t('Più recenti') }}</option>
        </select>
      </label>
    </div>

    <div v-if="settings.enable_taxonomy_tabs" class="ql-tabs">
      <button class="active">{{ t('Tutti') }}</button>
      <button>{{ t('Tutorial') }}</button>
      <button>{{ t('News') }}</button>
      <button>{{ t('Guide') }}</button>
    </div>

    <div class="ql-grid" :style="gridStyle">
      <div v-for="(item, n) in cards" :key="n" class="ql-card" :class="cardClasses(n)" :style="cardStyle(n)">
        <span v-if="settings.new_badge && n === 0" class="ql-new-badge" :style="badgeStyle">{{ settings.new_badge_text || 'New' }}</span>
        <span v-if="settings.trending_badge && n === 1" class="ql-trend-badge"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.07-2.14-.22-4.05 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.15.43-2.29 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg> Trending</span>

        <div v-if="settings.show_image !== false" class="ql-img" :style="imgStyle(n)"></div>
        <div class="ql-body" :style="bodyStyle(n)">
          <div v-if="settings.show_category !== false" class="ql-cat" :style="catStyle">{{ item.cat }}</div>
          <div v-if="settings.show_title !== false" class="ql-title" :style="titleStyle">{{ item.title }}</div>
          <div v-if="settings.show_date !== false || settings.show_author || settings.show_reading_time || settings.show_comment_count" class="ql-meta" :style="{ color: settings.meta_color || undefined }">
            <span v-if="settings.show_date !== false">{{ item.date }}</span>
            <span v-if="settings.show_author"> &middot; {{ item.author }}</span>
            <span v-if="settings.show_reading_time" class="ql-rt"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> {{ item.rt }} min</span>
            <span v-if="settings.show_comment_count" class="ql-cc"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg> {{ item.cc }}</span>
          </div>
          <div v-if="settings.show_excerpt !== false" class="ql-excerpt" :style="{ color: settings.text_color || undefined }">{{ item.excerpt }}</div>
          <div v-if="settings.show_read_more !== false" class="ql-readmore" :style="{ color: settings.link_color || settings.accent_color || 'var(--olo-color-primary, #e1474f)' }" data-olo-editable="read_more_text">{{ settings.read_more_text || t('Leggi tutto') }} &rarr;</div>
        </div>
      </div>
    </div>

    <div v-if="settings.pagination_type && settings.pagination_type !== 'none'" class="ql-pagination">
      <template v-if="settings.pagination_type === 'numbers'">
        <span class="ql-page ql-page-active">1</span><span class="ql-page">2</span><span class="ql-page">3</span>
      </template>
      <template v-else-if="settings.pagination_type === 'loadmore'">
        <button class="ql-loadmore">{{ t('Carica altro') }}</button>
      </template>
      <template v-else-if="settings.pagination_type === 'infinite'">
        <div class="ql-infinite">&#8595; {{ t('Scroll infinito attivo') }}</div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { radiusToCss as radiusToCssRaw } from '@/composables/useRadius';
const radiusToCss = (r) => radiusToCssRaw(r, { fallback: null, zero: '0', acceptPrimitive: false });

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const layout = computed(() => props.settings.layout || 'grid');
const preset = computed(() => props.settings.preset || 'custom');
const hoverFx = computed(() => props.settings.hover_effect || 'none');

const cardCount = computed(() => {
  const ppp = parseInt(props.settings.posts_per_page) || 6;
  return Math.min(Math.max(3, ppp), 6);
});

const cards = computed(() => {
  const out = [];
  const cats = ['Tutorial', 'News', 'Guide', 'Recensioni', 'Eventi', 'Opinioni'];
  const titles = [
    'Articolo principale di esempio',
    'Bella guida al CSS moderno',
    'Come pubblicare su WordPress',
    'Le 10 migliori tecniche di design',
    'Aggiornamento maggio 2026',
    'Reportage dalla redazione',
  ];
  const excerpts = [
    'Un breve estratto introduttivo del contenuto principale.',
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    'Sed do eiusmod tempor incididunt ut labore et dolore magna.',
    'Ut enim ad minim veniam, quis nostrud exercitation ullamco.',
    'Duis aute irure dolor in reprehenderit in voluptate velit.',
    'Excepteur sint occaecat cupidatat non proident sunt in culpa.',
  ];
  for (let i = 0; i < cardCount.value; i++) {
    out.push({
      title: titles[i % titles.length],
      cat: cats[i % cats.length],
      excerpt: excerpts[i % excerpts.length],
      date: '5 Mag 2026',
      author: 'Claudio',
      rt: 3 + i,
      cc: i * 3 + 2,
    });
  }
  return out;
});

const ratioMap = { '16:9': '56.25%', '4:3': '75%', '1:1': '100%', '3:2': '66.67%', '21:9': '42.85%', '3:4': '133.33%', 'auto': '60%' };

function fontFamilyCss(v) {
  if (v === 'sans')  return 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif';
  if (v === 'serif') return 'Georgia, "Times New Roman", Times, serif';
  if (v === 'mono')  return 'ui-monospace, "SF Mono", Menlo, Consolas, monospace';
  return 'inherit';
}
// Composable condiviso — vedi src/composables/useRadius.js

const wrapStyle = computed(() => {
  const cp = props.settings.container_padding || {};
  return {
    fontFamily: fontFamilyCss(props.settings.font_family),
    background: props.settings.bg_color || 'transparent',
    padding: `${cp.top || 0}px ${cp.right || 0}px ${cp.bottom || 0}px ${cp.left || 0}px`,
    borderRadius: radiusToCss(props.settings.container_radius) || '0',
  };
});

const gridStyle = computed(() => {
  const cols = parseInt(props.settings.columns) || 3;
  const gap = parseInt(props.settings.gap) || 30;
  const l = layout.value;
  if (l === 'list' || l === 'list-rich' || l === 'stacked' || l === 'alternating' || l === 'timeline') {
    return { display: 'flex', flexDirection: 'column', gap: gap + 'px' };
  }
  if (l === 'masonry' || l === 'newspaper') {
    return { columnCount: cols, columnGap: gap + 'px' };
  }
  if (l === 'magazine-trio') {
    return { display: 'grid', gridTemplateColumns: '2fr 1fr', gridTemplateRows: 'auto auto', gap: gap + 'px' };
  }
  if (l === 'magazine-hero' || l === 'bento') {
    return { display: 'grid', gridTemplateColumns: `repeat(${cols}, 1fr)`, gridAutoRows: 'minmax(120px, auto)', gap: gap + 'px' };
  }
  if (l === 'ticker-strip') {
    return { display: 'flex', gap: gap + 'px', overflow: 'hidden' };
  }
  return { display: 'grid', gridTemplateColumns: `repeat(${cols}, 1fr)`, gap: gap + 'px' };
});

function cardStyle(n) {
  const cardR = radiusToCss(props.settings.card_radius);
  const base = { borderRadius: cardR || '6px', position: 'relative' };
  const l = layout.value;
  if (l === 'magazine-trio' && n === 0) { base.gridRow = 'span 2'; base.minHeight = '320px'; }
  if (l === 'magazine-hero' && n === 0) { base.gridColumn = '1/-1'; base.minHeight = '300px'; }
  if (l === 'bento') {
    if (n % 7 === 0) { base.gridColumn = 'span 2'; base.gridRow = 'span 2'; }
    if (n % 7 === 3) { base.gridColumn = 'span 2'; }
  }
  if (l === 'ticker-strip') {
    base.flexShrink = '0'; base.width = '240px';
  }
  if (l === 'list-rich') {
    base.display = 'grid';
    base.gridTemplateColumns = '180px 1fr';
    base.gap = '20px';
    base.alignItems = 'start';
    base.background = 'transparent';
    base.boxShadow = 'none';
  }
  if (l === 'alternating') {
    base.display = 'grid';
    base.gridTemplateColumns = '1fr 1fr';
    base.gap = '32px';
    base.alignItems = 'center';
    base.direction = n % 2 === 1 ? 'rtl' : 'ltr';
    base.background = 'transparent';
    base.boxShadow = 'none';
  }
  if (l === 'stacked') {
    base.minHeight = '260px';
    base.background = '#0a0a0a';
    base.color = '#fff';
  }
  return base;
}

function imgStyle(n) {
  const r = props.settings.image_ratio || '16:9';
  const l = layout.value;
  if (l === 'magazine-trio' && n === 0) {
    return { position: 'absolute', inset: '0', height: '100%', width: '100%', borderRadius: 'inherit' };
  }
  if (l === 'magazine-hero' && n === 0) {
    return { position: 'absolute', inset: '0', height: '100%', width: '100%', borderRadius: 'inherit' };
  }
  if (l === 'list-rich') {
    return { paddingBottom: '0', height: '140px', width: '100%', borderRadius: '8px' };
  }
  if (l === 'alternating') {
    return { paddingBottom: '60%', borderRadius: '8px', direction: 'ltr' };
  }
  if (l === 'stacked') {
    return { position: 'absolute', inset: '0', height: '100%', width: '100%', borderRadius: 'inherit' };
  }
  return { paddingBottom: ratioMap[r] || '56.25%' };
}

function bodyStyle(n) {
  const l = layout.value;
  if (l === 'magazine-trio' && n === 0) {
    return { position: 'absolute', left: '0', right: '0', bottom: '0', padding: '20px', background: 'linear-gradient(180deg, transparent, rgba(0,0,0,0.85))', color: '#fff', borderRadius: 'inherit', zIndex: '2' };
  }
  if (l === 'magazine-hero' && n === 0) {
    return { position: 'absolute', left: '0', right: '0', bottom: '0', padding: '24px', background: 'linear-gradient(180deg, transparent, rgba(0,0,0,0.85))', color: '#fff', borderRadius: 'inherit', zIndex: '2' };
  }
  if (l === 'bento' && n % 7 === 0) {
    return { position: 'absolute', left: '0', right: '0', bottom: '0', padding: '16px', background: 'linear-gradient(180deg, transparent, rgba(0,0,0,0.85))', color: '#fff', borderRadius: 'inherit', zIndex: '2' };
  }
  if (l === 'stacked') {
    return { position: 'absolute', left: '0', right: '0', bottom: '0', padding: '24px', background: 'linear-gradient(180deg, transparent, rgba(0,0,0,0.85))', color: '#fff' };
  }
  if (l === 'alternating' || l === 'list-rich') {
    return { padding: '0', direction: 'ltr' };
  }
  return { padding: '14px' };
}

const titleStyle = computed(() => ({
  color: props.settings.title_color || undefined,
  fontWeight: props.settings.title_weight || '700',
  textTransform: props.settings.text_transform || 'none',
  letterSpacing: (parseFloat(props.settings.letter_spacing) || 0) + 'px',
}));

// TOKEN-FIRST: accento/categoria/badge = primario brand (era #e1474f indaco / #e1474f arancio off-brand)
const catStyle = computed(() => ({
  color: props.settings.accent_color || props.settings.link_color || 'var(--olo-color-primary, #e1474f)',
}));

const badgeStyle = computed(() => ({
  background: props.settings.accent_color || 'var(--olo-color-primary, #e1474f)',
}));

function cardClasses(n) {
  const style = props.settings.card_style || 'none';
  return {
    'ql-card--shadow': style === 'shadow',
    'ql-card--border': style === 'border',
    'ql-card--filled': style === 'filled',
    'is-first': n === 0,
  };
}
</script>

<style scoped>
.olo-queryloop-preview { padding: 8px; min-height: 100px; position: relative; }
.ql-header { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; font-size: 13px; color: #6b7280; }
.ql-icon { color: var(--olo-color-primary, #e1474f); }
.ql-label { font-weight: 600; color: #374151; }
.ql-info { font-size: 11px; background: #f3f4f6; padding: 2px 8px; border-radius: 4px; }

.ql-search { width: 100%; padding: 8px 12px; border: 1px solid rgba(0,0,0,0.15); border-radius: 8px; font-size: 12px; box-sizing: border-box; margin-bottom: 12px; pointer-events: none; background: rgba(255,255,255,0.6); }
.ql-sort-wrap { font-size: 12px; margin-bottom: 12px; }
.ql-sort-wrap select { padding: 4px 8px; border-radius: 4px; border: 1px solid rgba(0,0,0,0.15); margin-left: 6px; pointer-events: none; }
.ql-tabs { display: flex; gap: 6px; margin-bottom: 14px; border-bottom: 1px solid rgba(0,0,0,0.08); }
.ql-tabs button { background: transparent; border: 0; border-bottom: 2px solid transparent; padding: 6px 12px; font-size: 12px; font-weight: 600; color: rgba(0,0,0,0.6); cursor: pointer; margin-bottom: -1px; }
.ql-tabs button.active { color: var(--olo-color-primary, #e1474f); border-bottom-color: var(--olo-color-primary, #e1474f); }

.ql-card { overflow: hidden; background: #fff; }
.ql-card--shadow { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.ql-card--border { border: 1px solid #e5e7eb; }
.ql-card--filled { background: #f9fafb; }
.ql-img { width: 100%; background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%); position: relative; }
.ql-body { padding: 12px; }
.ql-cat { font-size: 10px; text-transform: uppercase; font-weight: 600; margin-bottom: 4px; }
.ql-title { font-size: 14px; color: var(--olo-color-text, #374151); margin-bottom: 4px; line-height: 1.25; }
.ql-meta { font-size: 11px; color: #9ca3af; margin-bottom: 6px; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.ql-rt, .ql-cc { font-size: 11px; opacity: 0.75; }
.ql-excerpt { font-size: 12px; color: #6b7280; line-height: 1.5; margin-bottom: 8px; }
.ql-readmore { font-size: 12px; font-weight: 500; }

.ql-new-badge {
  position: absolute; top: 8px; left: 8px; padding: 2px 8px;
  border-radius: 999px; color: #fff; font-size: 9px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 1px; z-index: 5;
}
.ql-trend-badge {
  position: absolute; top: 8px; right: 8px; padding: 2px 8px;
  border-radius: 999px; background: #0f172a; color: #fff; font-size: 9px;
  font-weight: 700; text-transform: uppercase; letter-spacing: 1px; z-index: 5;
}

.ql-pagination { display: flex; justify-content: center; gap: 6px; margin-top: 16px; }
.ql-page { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 4px; font-size: 12px; background: #f3f4f6; color: #374151; cursor: pointer; }
.ql-page-active { background: var(--olo-color-primary, #e1474f); color: #fff; }
.ql-loadmore { padding: 6px 20px; font-size: 12px; border: 1px solid #d1d5db; border-radius: 4px; background: #fff; cursor: pointer; }
.ql-infinite { font-size: 11px; color: #9ca3af; text-align: center; }

/* Preset hints */
.olo-ql-preset-magazine-trio :deep(.ql-card.is-first .ql-title) { font-size: 1.4em !important; }
.olo-ql-preset-magazine-hero :deep(.ql-card.is-first .ql-title) { font-size: 1.6em !important; }
.olo-ql-preset-newspaper-cols { font-family: Georgia, serif; }
.olo-ql-preset-stacked-blog :deep(.ql-card .ql-title) { color: #fff !important; font-family: Georgia, serif; }

/* Hover hints */
.olo-ql-hover-lift .ql-card { transition: transform 280ms cubic-bezier(0.34,1.56,0.64,1), box-shadow 280ms ease; }
.olo-ql-hover-lift .ql-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px rgba(15,23,42,0.12); }
.olo-ql-hover-image-zoom .ql-img { transition: transform 600ms ease; }
.olo-ql-hover-image-zoom .ql-card:hover .ql-img { transform: scale(1.04); }

/* Layout-specific tweaks (preview) */
.olo-ql-layout-timeline .ql-card { border-left: 2px solid var(--olo-color-primary, #e1474f); padding-left: 14px; background: transparent; }
.olo-ql-layout-newspaper .ql-card { background: transparent; box-shadow: none; padding-bottom: 12px; border-bottom: 1px solid rgba(0,0,0,0.1); break-inside: avoid; }
.olo-ql-layout-stacked :deep(.ql-img) { background: linear-gradient(135deg, #1f2937 0%, #0a0a0a 100%); }
</style>
