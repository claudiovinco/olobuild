<template>
  <div class="olo-sitemap-tile" :class="['olo-sm-preset-'+preset, 'olo-sm-mode-'+layoutMode]" :style="containerStyle">
    <input
      v-if="s.enable_search"
      type="text"
      class="olo-sm-search"
      :placeholder="s.search_placeholder || 'Cerca…'"
      readonly
      :style="searchStyle"
    />

    <div :style="gridStyle">
      <div v-if="visibleSections.length === 0"
        class="mb-border-2 mb-border-dashed mb-border-gray-600 mb-rounded-lg mb-p-8 mb-text-center mb-text-gray-500"
        style="grid-column: 1 / -1;"
      >
        <div class="mb-text-3xl mb-mb-2">&#128466;</div>
        <div class="mb-text-sm">{{ t('Seleziona almeno una sezione da mostrare') }}</div>
      </div>

      <div v-for="(section, sIdx) in visibleSections" :key="sIdx" class="olo-sm-section" :style="sectionStyle">
        <component :is="s.title_tag || 'h3'" class="olo-sm-title" :style="titleStyle">
          <span v-if="s.show_icons" class="olo-sm-icon" :style="{ color: s.accent_color || s.link_color }" v-html="iconSvg(section.type)"></span>
          {{ section.heading }}
          <span v-if="s.show_counter" class="olo-sm-counter" :style="counterStyle">{{ section.items.length }}</span>
        </component>

        <!-- Index A-Z -->
        <template v-if="layoutMode === 'index-az'">
          <div v-for="(group, letter) in groupAZ(section.items)" :key="letter" style="margin-bottom:10px">
            <div class="olo-sm-az-letter" :style="azLetterStyle">{{ letter }}</div>
            <ul style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:4px 16px;list-style:none;padding:0;margin:0">
              <li v-for="(it, i) in group" :key="i"><a :style="linkStyle" href="#" @click.prevent>{{ it.title }}</a></li>
            </ul>
          </div>
        </template>

        <!-- Cloud -->
        <template v-else-if="layoutMode === 'cloud'">
          <ul style="list-style:none;padding:0;margin:0;display:flex;flex-wrap:wrap;align-items:center;gap:6px 14px">
            <li v-for="(it, i) in section.items" :key="i" style="display:inline">
              <a href="#" @click.prevent :style="cloudLinkStyle(it, section.items)">{{ it.title }}</a>
            </li>
          </ul>
        </template>

        <!-- Default ul list -->
        <template v-else>
          <ul :class="bulletClass" :style="listStyle">
            <li v-for="(it, i) in section.items" :key="i" :data-depth="it.depth || 0" :style="treeItemStyle(it)">
              <a :style="linkStyle" href="#" @click.prevent>
                <span v-if="s.show_icons && it.icon" class="olo-sm-icon" :style="{ color: s.accent_color || s.link_color }" v-html="iconSvg(it.icon)"></span>
                {{ it.title }}
                <span v-if="it.count > 0" class="olo-sm-counter" :style="counterStyle">{{ it.count }}</span>
              </a>
            </li>
          </ul>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { radiusToCss as radiusToCssRaw } from '@/composables/useRadius';
const radiusToCss = (r) => radiusToCssRaw(r, { fallback: '0', zero: '0', acceptPrimitive: false });

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  preset: 'classic-columns',
  show_pages: true,
  show_posts: true,
  show_cpt: false,
  cpt_names: '',
  show_categories: true,
  show_tags: false,
  show_authors: false,
  show_archives: false,
  page_tree: true,
  layout_mode: 'columns',
  columns: '2',
  title_tag: 'h3',
  title_color: '',
  link_color: '',
  hover_color: '',
  text_color: '',
  bg_color: '',
  accent_color: '',
  list_style: 'disc',
  indent: '20',
  gap: 24,
  item_gap: 6,
  show_counter: false,
  show_icons: false,
  font_family: 'inherit',
  font_weight: '400',
  text_transform: 'none',
  letter_spacing: 0,
  enable_search: false,
  search_placeholder: 'Cerca…',
  container_padding: { top: 16, right: 16, bottom: 16, left: 16 },
  container_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const preset = computed(() => s.value.preset || 'classic-columns');
const layoutMode = computed(() => s.value.layout_mode || 'columns');

// Demo data
const demoPages = [
  { title: 'Home', url: '#', depth: 0, icon: 'page', count: 0 },
  { title: 'Chi siamo', url: '#', depth: 0, icon: 'page', count: 0 },
  { title: 'Servizi', url: '#', depth: 0, icon: 'page', count: 0 },
  { title: 'Web design', url: '#', depth: 1, icon: 'page', count: 0 },
  { title: 'SEO', url: '#', depth: 1, icon: 'page', count: 0 },
  { title: 'Contatti', url: '#', depth: 0, icon: 'page', count: 0 },
];
const demoPosts = [
  { title: 'Articolo di esempio uno', url: '#', depth: 0, icon: 'post', count: 0 },
  { title: 'Bella guida al CSS', url: '#', depth: 0, icon: 'post', count: 0 },
  { title: 'Come pubblicare su WP', url: '#', depth: 0, icon: 'post', count: 0 },
];
const demoCats = [
  { title: 'Notizie', url: '#', depth: 0, icon: 'cat', count: 12 },
  { title: 'Tutorial', url: '#', depth: 0, icon: 'cat', count: 8 },
  { title: 'Risorse', url: '#', depth: 0, icon: 'cat', count: 3 },
];
const demoTags = [
  { title: 'design', url: '#', depth: 0, icon: 'tag', count: 24 },
  { title: 'css', url: '#', depth: 0, icon: 'tag', count: 18 },
  { title: 'javascript', url: '#', depth: 0, icon: 'tag', count: 12 },
  { title: 'wordpress', url: '#', depth: 0, icon: 'tag', count: 9 },
  { title: 'seo', url: '#', depth: 0, icon: 'tag', count: 5 },
  { title: 'guida', url: '#', depth: 0, icon: 'tag', count: 3 },
];
const demoAuthors = [
  { title: 'Claudio', url: '#', depth: 0, icon: 'author', count: 12 },
  { title: 'Mario Rossi', url: '#', depth: 0, icon: 'author', count: 6 },
];
const demoArchives = [
  { title: 'Maggio 2026', url: '#', depth: 0, icon: 'archive', count: 0 },
  { title: 'Aprile 2026', url: '#', depth: 0, icon: 'archive', count: 0 },
  { title: 'Marzo 2026', url: '#', depth: 0, icon: 'archive', count: 0 },
];

const visibleSections = computed(() => {
  const out = [];
  if (s.value.show_pages) out.push({ heading: 'Pagine', type: 'page', items: demoPages });
  if (s.value.show_posts) out.push({ heading: 'Articoli', type: 'post', items: demoPosts });
  if (s.value.show_categories) out.push({ heading: 'Categorie', type: 'cat', items: demoCats });
  if (s.value.show_tags) out.push({ heading: 'Tag', type: 'tag', items: demoTags });
  if (s.value.show_authors) out.push({ heading: 'Autori', type: 'author', items: demoAuthors });
  if (s.value.show_archives) out.push({ heading: 'Archivi', type: 'archive', items: demoArchives });
  if (s.value.show_cpt) out.push({ heading: s.value.cpt_names || 'Custom Post Type', type: 'cpt', items: [
    { title: 'Elemento CPT 1', url: '#', depth: 0, icon: 'cpt', count: 0 },
    { title: 'Elemento CPT 2', url: '#', depth: 0, icon: 'cpt', count: 0 },
  ]});
  return out;
});

function fontFamilyCss(v) {
  if (v === 'sans')  return 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif';
  if (v === 'serif') return 'Georgia, "Times New Roman", Times, serif';
  if (v === 'mono')  return 'ui-monospace, "SF Mono", Menlo, Consolas, monospace';
  return 'inherit';
}
// Composable condiviso — vedi src/composables/useRadius.js

const containerStyle = computed(() => {
  const cp = s.value.container_padding || {};
  return {
    fontFamily: fontFamilyCss(s.value.font_family),
    fontWeight: s.value.font_weight || '400',
    textTransform: s.value.text_transform || 'none',
    letterSpacing: (parseFloat(s.value.letter_spacing) || 0) + 'px',
    background: s.value.bg_color || 'transparent',
    padding: `${cp.top || 0}px ${cp.right || 0}px ${cp.bottom || 0}px ${cp.left || 0}px`,
    borderRadius: radiusToCss(s.value.container_radius),
    color: s.value.text_color || 'inherit',
    position: 'relative',
  };
});

const gridStyle = computed(() => {
  const cols = parseInt(s.value.columns) || 2;
  const gap = parseInt(s.value.gap) || 24;
  if (['tree','index-az','cloud','terminal'].includes(layoutMode.value)) {
    return { display: 'flex', flexDirection: 'column', gap: gap + 'px' };
  }
  if (layoutMode.value === 'mindmap') {
    return { position: 'relative', minHeight: '380px' };
  }
  return { display: 'grid', gridTemplateColumns: `repeat(${cols}, 1fr)`, gap: gap + 'px' };
});

const sectionStyle = computed(() => {
  if (layoutMode.value === 'cards' || preset.value === 'card-grid' || preset.value === 'glass-cards') {
    return { background: preset.value === 'glass-cards' ? 'rgba(255,255,255,0.55)' : '#fff', padding: '18px 20px', borderRadius: '10px', boxShadow: '0 2px 12px rgba(15,23,42,0.06)' };
  }
  return {};
});

const titleStyle = computed(() => ({
  color: s.value.title_color || 'var(--olo-color-text, #1e293b)',
  margin: '0 0 8px',
  fontSize: '1.1em',
  fontWeight: '600',
  display: 'flex',
  alignItems: 'center',
  gap: '6px',
}));

const counterStyle = computed(() => ({
  background: 'rgba(0,0,0,0.06)',
  color: s.value.text_color || 'var(--olo-color-text-soft, #64748b)',
  padding: '1px 7px',
  borderRadius: '999px',
  fontSize: '0.7em',
  fontWeight: '600',
  marginLeft: '4px',
}));

const listStyle = computed(() => {
  const itemGap = parseInt(s.value.item_gap) || 0;
  const isBulleted = ['disc','circle'].includes(s.value.list_style) && ['columns','cards'].includes(layoutMode.value);
  return {
    listStyleType: isBulleted ? s.value.list_style : 'none',
    paddingLeft: isBulleted ? (parseInt(s.value.indent) || 20) + 'px' : '0',
    margin: '0',
    lineHeight: '1.6',
    display: 'flex',
    flexDirection: 'column',
    gap: itemGap + 'px',
  };
});

const bulletClass = computed(() => {
  if (s.value.list_style === 'arrow') return 'olo-sm-bullet-arrow';
  if (s.value.list_style === 'check') return 'olo-sm-bullet-check';
  return '';
});

// TOKEN-FIRST: link = primario brand (era #2563eb blu off-brand)
const linkStyle = computed(() => ({
  color: s.value.link_color || 'var(--olo-color-primary, #e1474f)',
  textDecoration: 'none',
  display: 'inline-flex',
  alignItems: 'center',
  gap: '5px',
}));

const azLetterStyle = computed(() => ({
  fontSize: '1.4em',
  fontWeight: '700',
  // Accento iniziale A-Z = primario brand (era #e1474f arancio off-brand)
  color: s.value.accent_color || s.value.link_color || 'var(--olo-color-primary, #e1474f)',
  margin: '14px 0 6px',
  borderBottom: '2px solid currentColor',
  paddingBottom: '4px',
}));

const searchStyle = computed(() => ({
  width: '100%',
  padding: '10px 14px',
  border: '1px solid rgba(0,0,0,0.15)',
  borderRadius: '8px',
  fontSize: '14px',
  marginBottom: '16px',
  boxSizing: 'border-box',
  background: 'rgba(255,255,255,0.6)',
}));

function treeItemStyle(it) {
  if (layoutMode.value !== 'tree') return {};
  const d = it.depth || 0;
  if (!d) return {};
  return { paddingLeft: (d * 18) + 'px', position: 'relative' };
}

function groupAZ(items) {
  const groups = {};
  items.forEach(it => {
    const f = (it.title || '').charAt(0).toUpperCase();
    const key = /[A-Z]/.test(f) ? f : '#';
    if (!groups[key]) groups[key] = [];
    groups[key].push(it);
  });
  const sorted = {};
  Object.keys(groups).sort().forEach(k => { sorted[k] = groups[k]; });
  return sorted;
}

function cloudLinkStyle(item, all) {
  const max = Math.max(1, ...all.map(i => i.count || 1));
  const w = (item.count || 1) / max;
  const size = (0.8 + w * 0.9).toFixed(2);
  return {
    fontSize: size + 'em',
    fontWeight: size > 1.3 ? 700 : (size > 1 ? 600 : 400),
    color: s.value.link_color || 'var(--olo-color-primary, #e1474f)',
    textDecoration: 'none',
  };
}

function iconSvg(type) {
  const stroke = 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
  switch (type) {
    case 'page':    return `<svg width="14" height="14" viewBox="0 0 24 24" ${stroke}><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>`;
    case 'post':    return `<svg width="14" height="14" viewBox="0 0 24 24" ${stroke}><path d="M19 4H5a2 2 0 0 0-2 2v14l4-4h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"/><line x1="8" y1="9" x2="16" y2="9"/><line x1="8" y1="13" x2="14" y2="13"/></svg>`;
    case 'cat':     return `<svg width="14" height="14" viewBox="0 0 24 24" ${stroke}><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>`;
    case 'tag':     return `<svg width="14" height="14" viewBox="0 0 24 24" ${stroke}><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>`;
    case 'author':  return `<svg width="14" height="14" viewBox="0 0 24 24" ${stroke}><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>`;
    case 'archive': return `<svg width="14" height="14" viewBox="0 0 24 24" ${stroke}><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`;
    case 'cpt':     return `<svg width="14" height="14" viewBox="0 0 24 24" ${stroke}><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h6v6H9z"/></svg>`;
  }
  return '';
}
</script>

<style scoped>
.olo-sm-bullet-arrow li::marker, .olo-sm-bullet-check li::marker { content: ''; }
.olo-sm-bullet-arrow li::before { content: '→  '; font-weight: 700; }
.olo-sm-bullet-check li::before { content: '✓  '; font-weight: 700; }
.olo-sm-tile a:hover { text-decoration: underline; }
/* a11y tastiera: anello di focus visibile sui link della mappa del sito */
.olo-sitemap-tile a:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
  border-radius: 3px;
}

/* Preset audaci — preview indicativo */
.olo-sm-preset-neon-schematic { background: #0a0f1c !important; color: #fff; padding: 24px; border-radius: 4px; }
.olo-sm-preset-neon-schematic .olo-sm-section { border: 1px solid rgba(34,211,238,0.3); padding: 14px; border-radius: 4px; background: rgba(34,211,238,0.04); }
.olo-sm-preset-neon-schematic :deep(.olo-sm-title) { color: #22d3ee !important; text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 0 8px #22d3ee; }
.olo-sm-preset-neon-schematic a { color: rgba(255,255,255,0.85) !important; }

.olo-sm-preset-brutalist-map { background: #fef9c3 !important; border: 3px solid #000 !important; box-shadow: 4px 4px 0 0 #000; }
.olo-sm-preset-brutalist-map .olo-sm-section { border: 2px solid #000; box-shadow: 3px 3px 0 0 #000; padding: 12px 14px; background: #fff; }

.olo-sm-preset-retro-terminal { background: #0c0c0c !important; color: #00ff8c; font-family: ui-monospace, monospace !important; }
.olo-sm-preset-retro-terminal .olo-sm-section { border: 1px solid rgba(0,255,140,0.2); padding: 12px 16px; background: transparent; }
.olo-sm-preset-retro-terminal :deep(.olo-sm-title) { color: #00ff8c !important; text-transform: lowercase; }
.olo-sm-preset-retro-terminal :deep(.olo-sm-title)::before { content: '$ '; }
.olo-sm-preset-retro-terminal :deep(.olo-sm-title)::after { content: '_'; margin-left: 4px; animation: olo-sm-blink 1s steps(2) infinite; }
.olo-sm-preset-retro-terminal a { color: #7cf3b9 !important; }
.olo-sm-preset-retro-terminal li::before { content: '├── '; opacity: 0.5; }
.olo-sm-preset-retro-terminal li:last-child::before { content: '└── '; opacity: 0.5; }
@keyframes olo-sm-blink { 50% { opacity: 0; } }

.olo-sm-preset-sticky-notes .olo-sm-section { padding: 18px; border-radius: 2px; box-shadow: 0 6px 14px rgba(15,23,42,0.08); transition: transform 250ms cubic-bezier(0.68,-0.55,0.265,1.55); }
.olo-sm-preset-sticky-notes .olo-sm-section:nth-child(1) { background: #fef3c7; transform: rotate(-1.4deg); }
.olo-sm-preset-sticky-notes .olo-sm-section:nth-child(2) { background: #fce7f3; transform: rotate(0.9deg); }
.olo-sm-preset-sticky-notes .olo-sm-section:nth-child(3) { background: #dbeafe; transform: rotate(-0.7deg); }
.olo-sm-preset-sticky-notes .olo-sm-section:nth-child(4) { background: #dcfce7; transform: rotate(1.2deg); }
.olo-sm-preset-sticky-notes .olo-sm-section:nth-child(5) { background: #fed7aa; transform: rotate(-1.1deg); }
.olo-sm-preset-sticky-notes .olo-sm-section:nth-child(6) { background: #e9d5ff; transform: rotate(0.6deg); }
.olo-sm-preset-sticky-notes :deep(.olo-sm-title) { font-family: 'Caveat', cursive; font-size: 1.3em; border-bottom: 2px dashed rgba(15,23,42,0.3); padding-bottom: 6px; }

.olo-sm-preset-honeycomb { display: grid !important; }
.olo-sm-preset-honeycomb .olo-sm-section { aspect-ratio: 1.155; clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%); padding: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: #fff !important; background: linear-gradient(135deg, var(--olo-color-primary, #e1474f) 0%, #fbbf24 100%); }
.olo-sm-preset-honeycomb .olo-sm-section:nth-child(2) { background: linear-gradient(135deg, #22d3ee 0%, #3b82f6 100%); }
.olo-sm-preset-honeycomb .olo-sm-section:nth-child(3) { background: linear-gradient(135deg, #f4a23b 0%, #f4a23b 100%); }
.olo-sm-preset-honeycomb .olo-sm-section:nth-child(4) { background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%); }

.olo-sm-preset-mind-map { position: relative; min-height: 380px; }
.olo-sm-preset-mind-map::before { content: '⚡'; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); width: 70px; height: 70px; background: radial-gradient(circle, var(--olo-color-primary, #e1474f) 0%, color-mix(in srgb, var(--olo-color-primary, #e1474f) 40%, transparent) 60%, transparent 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #fff; box-shadow: 0 0 40px var(--olo-color-primary, #e1474f); z-index: 2; }

.olo-sm-preset-glass-cards .olo-sm-section { backdrop-filter: blur(14px) saturate(160%); -webkit-backdrop-filter: blur(14px) saturate(160%); background: rgba(255,255,255,0.55) !important; border: 1px solid rgba(255,255,255,0.5); box-shadow: 0 8px 32px rgba(15,23,42,0.08); }

.olo-sm-mode-compact :deep(li) { padding: 0; }
.olo-sm-mode-compact :deep(li a) { background: rgba(0,0,0,0.04); padding: 5px 12px; border-radius: 999px; font-size: 0.92em; }

.olo-sm-search { pointer-events: none; }
</style>
