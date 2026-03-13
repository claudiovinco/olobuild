<template>
  <div class="olo-megamenu-preview" :style="rootStyle">
    <template v-if="selectedMenu || true">
      <!-- Badges -->
      <div class="olo-mm-badges">
        <span v-if="s.sticky" class="olo-mm-badge olo-mm-badge--sticky">STICKY</span>
        <span v-if="s.header_mode === 'overlay'" class="olo-mm-badge olo-mm-badge--overlay">OVERLAY</span>
      </div>

      <!-- Navbar Bar -->
      <nav class="olo-mm-bar" :style="barStyle">
        <!-- Hamburger -->
        <button class="olo-mm-hamburger-btn" :style="{ color: s.hamburger_color || s.text_color || '#374151' }">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>

        <!-- Nav Items -->
        <ul class="olo-mm-nav" :style="navStyle">
          <li
            v-for="(item, idx) in previewItems"
            :key="idx"
            class="olo-mm-nav-item"
            :class="{
              'olo-mm-nav-item--active': item.isMega && idx === activeMegaIdx,
              'olo-mm-nav-item--mega': item.isMega,
              'olo-mm-nav-item--button': item.isButton,
              'olo-mm-nav-item--dropdown': item.hasChildren && !item.isMega && !item.isButton,
            }"
            @mouseenter="item.isMega ? (activeMegaIdx = idx) : null"
            @mouseleave="item.isMega ? null : null"
          >
            <a
              v-if="!item.isButton"
              class="olo-mm-nav-link"
              :style="getLinkStyle(item, idx)"
              href="javascript:void(0)"
            >
              {{ item.label }}
              <svg v-if="item.isMega || (item.hasChildren && !item.isButton)" class="olo-mm-chevron" width="10" height="10" viewBox="0 0 12 12" fill="currentColor"><path d="M2 4l4 4 4-4z"/></svg>
            </a>
            <a
              v-else
              class="olo-mm-btn"
              :style="btnStyle"
              href="javascript:void(0)"
            >{{ item.label }}</a>

            <!-- Active indicator line -->
            <span
              v-if="item.isMega && idx === activeMegaIdx"
              class="olo-mm-active-line"
              :style="{ background: s.active_color || s.panel_border_color || '#6366f1' }"
            ></span>
          </li>

          <!-- Search icon -->
          <li v-if="hasSearch" class="olo-mm-nav-item olo-mm-nav-item--search" :style="{ order: s.search_position === 'before' ? -1 : 99 }">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="{ color: s.text_color || '#6b7280' }">
              <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/>
            </svg>
          </li>
        </ul>
      </nav>

      <!-- Mega Panel -->
      <div
        v-if="activeMegaIdx >= 0 && previewItems[activeMegaIdx]?.isMega"
        class="olo-mm-panel-container"
        @mouseleave="activeMegaIdx = -1"
      >
        <div class="olo-mm-panel" :style="panelStyle">
          <!-- Accent border top -->
          <div v-if="panelBorderTop > 0" class="olo-mm-panel-accent" :style="accentLineStyle"></div>

          <!-- Grid columns -->
          <div class="olo-mm-grid" :style="gridStyle">
            <div
              v-for="(col, cIdx) in panelColumns"
              :key="cIdx"
              class="olo-mm-col"
              :style="colStyle(cIdx)"
            >
              <div class="olo-mm-heading" :style="headingStyle">{{ col.heading }}</div>
              <div class="olo-mm-links">
                <a
                  v-for="(link, lIdx) in col.links"
                  :key="lIdx"
                  class="olo-mm-link"
                  :style="linkStyleObj"
                  href="javascript:void(0)"
                >
                  {{ link }}
                  <span v-if="s.show_descriptions" class="olo-mm-desc" :style="descStyleObj">
                    Descrizione breve della voce
                  </span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Info label -->
      <div class="olo-mm-info">
        <span v-if="selectedMenu">{{ selectedMenu.name }} &middot; {{ menuItemCount }} voci</span>
        <span v-else>Nessun menu selezionato</span>
        <span class="olo-mm-info-meta">bp {{ s.mobile_breakpoint || '1024' }}px &middot; {{ s.layout }}</span>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { getShadowValue } from '@/composables/useShadowMap';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  menu_id: 0,
  layout: 'left',
  nav_bg: '',
  nav_height: '60',
  text_color: '',
  hover_color: '',
  active_color: '',
  font_size: '15',
  font_weight: 'normal',
  text_transform: 'none',
  letter_spacing: '0',
  item_gap: '15',
  mega_mode: 'auto',
  panel_width: 'container',
  panel_columns: '4',
  panel_bg: '#ffffff',
  panel_shadow: 'md',
  panel_radius: '8',
  panel_padding: '32',
  panel_border_top: '3',
  panel_border_color: '#6366f1',
  panel_animation: 'fade',
  show_dividers: false,
  heading_color: '#111827',
  heading_size: '14',
  heading_weight: '600',
  heading_transform: 'uppercase',
  link_color: '#374151',
  link_hover_color: '#6366f1',
  link_size: '14',
  link_spacing: '8',
  show_descriptions: false,
  desc_color: '#9ca3af',
  button_mode: 'none',
  btn_bg: '#6366f1',
  btn_color: '#ffffff',
  btn_radius: '6',
  btn_hover_bg: '',
  mobile_breakpoint: '1024',
  hamburger_color: '',
  sticky: false,
  header_mode: 'overlay',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const oloData = window.oloData || {};
const wpMenus = oloData.wpMenus || [];

const hasSearch = computed(() => !!s.value.search_tile_id);

const selectedMenu = computed(() => {
  const id = parseInt(s.value.menu_id) || 0;
  if (!id) return null;
  return wpMenus.find(m => m.id === id) || null;
});

const menuItems = computed(() => {
  if (!selectedMenu.value || !selectedMenu.value.items) return [];
  return selectedMenu.value.items;
});

const menuItemCount = computed(() => menuItems.value.length);

const topItems = computed(() => {
  const items = menuItems.value;
  if (!items.length) return [];
  return items.filter(i => !i.parent);
});

function getChildren(parentId) {
  return menuItems.value.filter(i => i.parent === parentId);
}

function hasGrandchildren(parentId) {
  const kids = getChildren(parentId);
  return kids.some(kid => getChildren(kid.id).length > 0);
}

const activeMegaIdx = ref(0);

const previewItems = computed(() => {
  const btnMode = s.value.button_mode || 'none';
  const megaMode = s.value.mega_mode || 'auto';
  const tops = topItems.value;

  if (tops.length > 0) {
    return tops.map((item, i) => {
      let isButton = false;
      let isMega = false;
      const kids = getChildren(item.id);
      const hasKids = kids.length > 0;
      const hasGC = hasGrandchildren(item.id);

      if (btnMode === 'last' && i === tops.length - 1) isButton = true;
      if (btnMode === 'last-2' && i >= tops.length - 2) isButton = true;
      if (btnMode === 'css-class' && (item.classes || []).includes('olo-btn')) isButton = true;

      if (!isButton && megaMode === 'auto' && hasGC) isMega = true;
      if (!isButton && megaMode === 'all' && hasKids) isMega = true;

      return { label: item.title, isButton, isMega, hasChildren: hasKids, id: item.id };
    });
  }

  // Fallback: fake items
  const labels = ['Home', 'Servizi', 'Chi siamo', 'Portfolio', 'Contatti'];
  return labels.map((label, i) => {
    let isButton = false;
    let isMega = false;
    if (btnMode === 'last' && i === labels.length - 1) isButton = true;
    if (btnMode === 'last-2' && i >= labels.length - 2) isButton = true;
    if (!isButton && megaMode !== 'none' && i >= 1 && i <= 2) isMega = true;
    return { label, isButton, isMega, hasChildren: isMega, id: i };
  });
});

// Panel columns
const panelColumns = computed(() => {
  const numCols = Math.max(2, Math.min(6, parseInt(s.value.panel_columns) || 4));
  const activeItem = previewItems.value[activeMegaIdx.value];

  if (activeItem && topItems.value.length > 0) {
    const children = getChildren(activeItem.id);
    if (children.length > 0) {
      const cols = [];
      // Use L2 items as column headings, L3 as links
      for (const child of children) {
        const grandkids = getChildren(child.id);
        cols.push({
          heading: child.title,
          links: grandkids.map(gc => gc.title),
        });
      }
      if (cols.length > 0) return cols.slice(0, numCols);
    }
  }

  // Fallback
  const fakeData = [
    { heading: 'Prodotti', links: ['Software', 'Servizi Cloud', 'Consulenza', 'Supporto'] },
    { heading: 'Risorse', links: ['Documentazione', 'API Reference', 'Tutorial'] },
    { heading: 'Azienda', links: ['Chi siamo', 'Lavora con noi', 'Blog'] },
    { heading: 'Legale', links: ['Privacy', 'Termini', 'Cookie Policy'] },
    { heading: 'Community', links: ['Forum', 'Eventi', 'Partner'] },
    { heading: 'Contatti', links: ['Email', 'Telefono', 'Sedi'] },
  ];
  return fakeData.slice(0, numCols);
});

const panelBorderTop = computed(() => parseInt(s.value.panel_border_top) || 0);

// ── Styles ──

const rootStyle = computed(() => ({
  position: 'relative',
}));

const barStyle = computed(() => {
  const st = {
    display: 'flex',
    alignItems: 'center',
    padding: '0 16px',
    minHeight: (parseInt(s.value.nav_height) || 60) + 'px',
    transition: 'background .3s',
  };
  if (s.value.nav_bg) st.background = s.value.nav_bg;
  return st;
});

const navStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  listStyle: 'none',
  margin: 0,
  padding: 0,
  flex: 1,
  gap: (parseInt(s.value.item_gap) || 15) + 'px',
  justifyContent: s.value.layout === 'center' ? 'center' : s.value.layout === 'right' ? 'flex-end' : 'flex-start',
}));

function getLinkStyle(item, idx) {
  const isActive = idx === activeMegaIdx.value && item.isMega;
  return {
    color: isActive ? (s.value.active_color || '#6366f1') : (s.value.text_color || '#374151'),
    fontSize: (parseInt(s.value.font_size) || 15) + 'px',
    fontWeight: s.value.font_weight || 'normal',
    textTransform: s.value.text_transform || 'none',
    letterSpacing: (parseFloat(s.value.letter_spacing) || 0) + 'px',
    textDecoration: 'none',
    display: 'inline-flex',
    alignItems: 'center',
    gap: '4px',
    whiteSpace: 'nowrap',
    padding: '8px 0',
    transition: 'color .2s',
  };
}

const btnStyle = computed(() => ({
  display: 'inline-flex',
  alignItems: 'center',
  padding: '8px 20px',
  background: s.value.btn_bg || '#6366f1',
  color: (s.value.btn_color || '#fff') + ' !important',
  borderRadius: (parseInt(s.value.btn_radius) || 6) + 'px',
  fontSize: (parseInt(s.value.font_size) || 15) + 'px',
  fontWeight: '600',
  textDecoration: 'none',
  whiteSpace: 'nowrap',
  transition: 'background .2s, transform .15s',
}));

const panelStyle = computed(() => ({
  background: s.value.panel_bg || '#ffffff',
  borderRadius: (parseInt(s.value.panel_radius) || 8) + 'px',
  boxShadow: getShadowValue(s.value, 'panel_shadow') || '0 10px 40px rgba(0,0,0,0.12)',
  padding: (parseInt(s.value.panel_padding) || 32) + 'px',
  position: 'relative',
  overflow: 'hidden',
}));

const accentLineStyle = computed(() => ({
  position: 'absolute',
  top: 0,
  left: 0,
  right: 0,
  height: panelBorderTop.value + 'px',
  background: s.value.panel_border_color || '#6366f1',
  borderRadius: `${parseInt(s.value.panel_radius) || 8}px ${parseInt(s.value.panel_radius) || 8}px 0 0`,
}));

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${Math.max(2, Math.min(6, parseInt(s.value.panel_columns) || 4))}, 1fr)`,
  gap: (parseInt(s.value.panel_padding) || 32) + 'px',
}));

function colStyle(colIdx) {
  if (!s.value.show_dividers) return {};
  const numCols = Math.max(2, parseInt(s.value.panel_columns) || 4);
  if (colIdx >= numCols - 1) return {};
  return {
    borderRight: '1px solid rgba(0,0,0,0.08)',
    paddingRight: (parseInt(s.value.panel_padding) || 32) + 'px',
  };
}

const headingStyle = computed(() => ({
  fontSize: (parseInt(s.value.heading_size) || 14) + 'px',
  fontWeight: s.value.heading_weight || '600',
  color: s.value.heading_color || '#111827',
  textTransform: s.value.heading_transform || 'uppercase',
  letterSpacing: s.value.heading_transform === 'uppercase' ? '0.5px' : '0',
  margin: '0 0 12px',
  paddingBottom: '8px',
  borderBottom: '1px solid rgba(0,0,0,0.06)',
  lineHeight: '1.3',
}));

const linkStyleObj = computed(() => ({
  display: 'block',
  color: s.value.link_color || '#374151',
  fontSize: (parseInt(s.value.link_size) || 14) + 'px',
  padding: (parseInt(s.value.link_spacing) || 8) + 'px 0',
  textDecoration: 'none',
  transition: 'color .15s, padding-left .15s',
  lineHeight: '1.4',
}));

const descStyleObj = computed(() => ({
  display: 'block',
  color: s.value.desc_color || '#9ca3af',
  fontSize: Math.max(11, (parseInt(s.value.link_size) || 14) - 2) + 'px',
  marginTop: '2px',
  lineHeight: '1.3',
}));
</script>

<style scoped>
.olo-megamenu-preview {
  min-height: 36px;
  display: flex;
  flex-direction: column;
}

/* Badges */
.olo-mm-badges {
  display: flex;
  gap: 4px;
  justify-content: flex-end;
  margin-bottom: 2px;
}
.olo-mm-badge {
  font-size: 8px;
  font-weight: 700;
  letter-spacing: 0.06em;
  padding: 1px 5px;
  border-radius: 3px;
  line-height: 1.4;
}
.olo-mm-badge--sticky { background: #7c3aed; color: #fff; }
.olo-mm-badge--overlay { background: #059669; color: #fff; }

/* Navbar bar */
.olo-mm-bar {
  border-radius: 6px 6px 0 0;
  border: 1px solid rgba(0,0,0,0.06);
  position: relative;
  z-index: 2;
}

/* Hamburger button (visual only) */
.olo-mm-hamburger-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border: none;
  background: rgba(0,0,0,0.04);
  border-radius: 4px;
  cursor: default;
  flex-shrink: 0;
  margin-right: 8px;
}

/* Nav list */
.olo-mm-nav {
  flex-wrap: nowrap;
  overflow: hidden;
}
.olo-mm-nav-item {
  position: relative;
  list-style: none;
  display: flex;
  align-items: center;
}

/* Nav link */
.olo-mm-nav-link {
  cursor: default;
  position: relative;
}
.olo-mm-nav-link:hover {
  opacity: 0.8;
}

/* Chevron icon */
.olo-mm-chevron {
  opacity: 0.4;
  transition: transform 0.2s;
}
.olo-mm-nav-item--active .olo-mm-chevron {
  transform: rotate(180deg);
}

/* Active underline indicator */
.olo-mm-active-line {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2px;
  border-radius: 1px;
  animation: olo-mm-scaleIn 0.25s ease forwards;
}
@keyframes olo-mm-scaleIn {
  from { transform: scaleX(0); }
  to { transform: scaleX(1); }
}

/* CTA Button */
.olo-mm-btn {
  cursor: default;
}
.olo-mm-btn:hover {
  filter: brightness(1.1);
}

/* Search nav item */
.olo-mm-nav-item--search {
  padding: 4px;
  opacity: 0.6;
}

/* Panel container */
.olo-mm-panel-container {
  position: relative;
  z-index: 1;
  animation: olo-mm-panelIn 0.2s ease;
  max-width: 100%;
  overflow: hidden;
  border-radius: 0 0 8px 8px;
}
@keyframes olo-mm-panelIn {
  from { opacity: 0; transform: translateY(-8px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Panel */
.olo-mm-panel {
  border: 1px solid rgba(0,0,0,0.06);
}

/* Panel accent line */
.olo-mm-panel-accent {
  pointer-events: none;
}

/* Grid */
.olo-mm-col {
  min-width: 0;
}

/* Heading */
.olo-mm-heading {
  cursor: default;
}
.olo-mm-heading a {
  color: inherit;
  text-decoration: none;
}

/* Links */
.olo-mm-links {
  display: flex;
  flex-direction: column;
}
.olo-mm-link {
  cursor: default;
  border-radius: 3px;
}
.olo-mm-link:hover {
  padding-left: 4px;
  color: var(--olo-color-primary, #6366f1) !important;
}

/* Desc */
.olo-mm-desc {
  pointer-events: none;
}

/* Info label */
.olo-mm-info {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 10px;
  color: #9ca3af;
  margin-top: 4px;
  padding: 2px 4px;
}
.olo-mm-info-meta {
  font-size: 9px;
  color: #b0b5bc;
}
</style>
