<template>
  <div class="olo-megamenu-preview">
    <template v-if="selectedMenu">
      <!-- Badges -->
      <div class="olo-mm-badges">
        <span v-if="s.sticky" class="olo-mm-badge olo-mm-badge--sticky">STICKY</span>
        <span v-if="s.header_mode === 'overlay'" class="olo-mm-badge olo-mm-badge--overlay">OVERLAY</span>
        <span class="olo-mm-badge olo-mm-badge--mega">MEGA</span>
      </div>

      <!-- Navbar -->
      <div class="olo-mm-navbar" :style="navbarStyle">
        <!-- Hamburger (mobile preview) -->
        <div class="olo-mm-hamburger" :style="{ color: s.hamburger_color || s.text_color || '#9ca3af' }">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </div>

        <!-- Nav items -->
        <div class="olo-mm-items" :style="itemsAlignStyle">
          <span v-if="hasSearch && s.search_position === 'before'" class="olo-mm-item olo-mm-item--search" :style="{ color: s.text_color || '#d1d5db' }" title="Ricerca">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/></svg>
          </span>
          <template v-for="(item, idx) in previewItems" :key="idx">
            <span
              class="olo-mm-item"
              :class="{
                'olo-mm-item--active': idx === activeMegaIdx,
                'olo-mm-item--button': item.isButton,
                'olo-mm-item--mega': item.isMega,
              }"
              :style="getItemStyle(item, idx)"
              @mouseenter="item.isMega ? activeMegaIdx = idx : null"
            >
              {{ item.label }}
              <svg v-if="item.isMega" width="8" height="8" viewBox="0 0 12 12" fill="currentColor" style="opacity:0.5">
                <path d="M2 4l4 4 4-4z"/>
              </svg>
            </span>
          </template>
          <span v-if="hasSearch && s.search_position !== 'before'" class="olo-mm-item olo-mm-item--search" :style="{ color: s.text_color || '#d1d5db' }" title="Ricerca">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/></svg>
          </span>
        </div>
      </div>

      <!-- Mega Panel (preview sotto la navbar) -->
      <div v-if="activeMegaIdx >= 0" class="olo-mm-panel-wrap">
        <div class="olo-mm-panel" :style="panelStyle">
          <!-- Accent border top -->
          <div v-if="panelBorderTop > 0" :style="accentLineStyle"></div>

          <!-- Columns -->
          <div class="olo-mm-columns" :style="columnsStyle">
            <div
              v-for="(col, cIdx) in panelColumns"
              :key="cIdx"
              class="olo-mm-column"
              :style="columnStyle(cIdx)"
            >
              <!-- Column heading -->
              <div class="olo-mm-col-heading" :style="headingStyle">
                {{ col.heading }}
              </div>
              <!-- Column links -->
              <div class="olo-mm-col-links">
                <a
                  v-for="(link, lIdx) in col.links"
                  :key="lIdx"
                  class="olo-mm-col-link"
                  :style="linkStyle"
                >
                  {{ link }}
                  <span v-if="s.show_descriptions" class="olo-mm-col-desc" :style="descStyle">
                    Descrizione breve della voce
                  </span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="olo-mm-label">
        <span>{{ selectedMenu.name }}</span>
        <span class="olo-mm-label-info">{{ menuItemCount }} voci &middot; bp {{ s.mobile_breakpoint || '1024' }}px</span>
      </div>
    </template>
    <div v-else class="olo-mm-empty">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" style="opacity:0.4;margin-bottom:4px">
        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/>
      </svg>
      Seleziona un menu WordPress
    </div>
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

// Top-level items (no parent)
const topItems = computed(() => {
  const items = menuItems.value;
  if (!items.length) return [];
  return items.filter(i => !i.parent);
});

// Children of a given item
function getChildren(parentId) {
  return menuItems.value.filter(i => i.parent === parentId);
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
      const hasChildren = getChildren(item.id).length > 0;

      if (btnMode === 'last' && i === tops.length - 1) isButton = true;
      if (btnMode === 'last-2' && i >= tops.length - 2) isButton = true;

      if (!isButton && megaMode !== 'none' && hasChildren) isMega = true;

      return { label: item.title, isButton, isMega, id: item.id };
    });
  }

  // Fallback: fake items
  const count = 5;
  const labels = ['Home', 'Servizi', 'Chi siamo', 'Portfolio', 'Contatti'];
  return labels.map((label, i) => {
    let isButton = false;
    let isMega = false;
    if (btnMode === 'last' && i === count - 1) isButton = true;
    if (btnMode === 'last-2' && i >= count - 2) isButton = true;
    if (!isButton && megaMode !== 'none' && i >= 1 && i <= 2) isMega = true;
    return { label, isButton, isMega, id: i };
  });
});

// Panel columns content
const panelColumns = computed(() => {
  const numCols = Math.max(2, Math.min(6, parseInt(s.value.panel_columns) || 4));
  const activeItem = previewItems.value[activeMegaIdx.value];

  if (activeItem && topItems.value.length > 0) {
    const children = getChildren(activeItem.id);
    if (children.length > 0) {
      // Group children into columns
      const cols = [];
      const perCol = Math.ceil(children.length / numCols);
      for (let c = 0; c < numCols; c++) {
        const slice = children.slice(c * perCol, (c + 1) * perCol);
        if (slice.length > 0) {
          cols.push({
            heading: slice[0].title,
            links: slice.slice(1).map(ch => ch.title),
          });
        }
      }
      // Fill empty columns if needed
      while (cols.length < numCols) {
        cols.push({ heading: 'Sezione', links: ['Link 1', 'Link 2'] });
      }
      return cols;
    }
  }

  // Fallback: fake content
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

const navbarStyle = computed(() => {
  const st = {
    minHeight: (parseInt(s.value.nav_height) || 60) + 'px',
  };
  if (s.value.nav_bg) st.background = s.value.nav_bg;
  return st;
});

const itemsAlignStyle = computed(() => ({
  justifyContent: s.value.layout === 'center' ? 'center' : s.value.layout === 'right' ? 'flex-end' : 'flex-start',
  gap: (parseInt(s.value.item_gap) || 15) + 'px',
}));

function getItemStyle(item, idx) {
  if (item.isButton) {
    return {
      background: s.value.btn_bg || '#6366f1',
      color: s.value.btn_color || '#fff',
      borderRadius: ((v => isNaN(v) ? 6 : v)(parseInt(s.value.btn_radius))) + 'px',
      fontWeight: '600',
      fontSize: (parseInt(s.value.font_size) || 15) + 'px',
      padding: '4px 12px',
    };
  }
  const isActive = idx === activeMegaIdx.value && item.isMega;
  return {
    color: isActive ? (s.value.active_color || '#6366f1') : (s.value.text_color || '#d1d5db'),
    fontSize: (parseInt(s.value.font_size) || 15) + 'px',
    fontWeight: s.value.font_weight || 'normal',
    textTransform: s.value.text_transform || 'none',
    letterSpacing: (parseFloat(s.value.letter_spacing) || 0) + 'px',
  };
}

const panelStyle = computed(() => ({
  background: s.value.panel_bg || '#ffffff',
  borderRadius: ((v => isNaN(v) ? 8 : v)(parseInt(s.value.panel_radius))) + 'px',
  boxShadow: getShadowValue(s.value, 'panel_shadow'),
  padding: (parseInt(s.value.panel_padding) || 32) + 'px',
  overflow: 'hidden',
  position: 'relative',
}));

const accentLineStyle = computed(() => ({
  position: 'absolute',
  top: '0',
  left: '0',
  right: '0',
  height: panelBorderTop.value + 'px',
  background: s.value.panel_border_color || '#6366f1',
  borderRadius: ((v => isNaN(v) ? 8 : v)(parseInt(s.value.panel_radius))) + 'px ' + ((v => isNaN(v) ? 8 : v)(parseInt(s.value.panel_radius))) + 'px 0 0',
}));

const columnsStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${Math.max(2, Math.min(6, parseInt(s.value.panel_columns) || 4))}, 1fr)`,
  gap: Math.round((parseInt(s.value.panel_padding) || 32) * 0.4) + 'px',
}));

function columnStyle(colIdx) {
  if (!s.value.show_dividers) return {};
  const numCols = Math.max(2, parseInt(s.value.panel_columns) || 4);
  if (colIdx >= numCols - 1) return {};
  return {
    borderRight: '1px solid rgba(0,0,0,0.08)',
    paddingRight: Math.round((parseInt(s.value.panel_padding) || 32) * 0.3) + 'px',
  };
}

const headingStyle = computed(() => ({
  color: s.value.heading_color || '#111827',
  fontSize: (parseInt(s.value.heading_size) || 14) + 'px',
  fontWeight: s.value.heading_weight || '600',
  textTransform: s.value.heading_transform || 'uppercase',
  letterSpacing: s.value.heading_transform === 'uppercase' ? '0.5px' : '0',
  marginBottom: '6px',
  paddingBottom: '4px',
  borderBottom: '1px solid rgba(0,0,0,0.06)',
}));

const linkStyle = computed(() => ({
  color: s.value.link_color || '#374151',
  fontSize: (parseInt(s.value.link_size) || 14) + 'px',
  padding: (parseInt(s.value.link_spacing) || 8) + 'px 0',
}));

const descStyle = computed(() => ({
  color: s.value.desc_color || '#9ca3af',
  fontSize: Math.max(10, (parseInt(s.value.link_size) || 14) - 2) + 'px',
}));
</script>

<style scoped>
.olo-megamenu-preview {
  min-height: 36px;
  display: flex;
  flex-direction: column;
  gap: 0;
  position: relative;
}

/* Badges */
.olo-mm-badges {
  display: flex;
  gap: 4px;
  justify-content: flex-end;
  margin-bottom: 4px;
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
.olo-mm-badge--mega { background: #3b82f6; color: #fff; }
.olo-mm-badge--overlay { background: #059669; color: #fff; }

/* Navbar */
.olo-mm-navbar {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 8px;
  border-radius: 6px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.06);
}

.olo-mm-hamburger {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 4px;
  background: rgba(255,255,255,0.06);
  flex-shrink: 0;
}

.olo-mm-items {
  display: flex;
  align-items: center;
  flex: 1;
  overflow: hidden;
}

.olo-mm-item {
  padding: 3px 6px;
  border-radius: 3px;
  display: inline-flex;
  align-items: center;
  gap: 3px;
  white-space: nowrap;
  cursor: default;
  transition: color 0.15s;
  font-size: 12px;
  color: #d1d5db;
}

.olo-mm-item--mega {
  position: relative;
}
.olo-mm-item--mega::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 4px;
  right: 4px;
  height: 2px;
  background: transparent;
  border-radius: 1px;
  transition: background 0.15s;
}
.olo-mm-item--active.olo-mm-item--mega::after {
  background: #6366f1;
}

.olo-mm-item--button {
  background: #6366f1;
  color: #fff;
  font-weight: 600;
  font-size: 11px;
  padding: 3px 10px;
}
.olo-mm-item--search {
  padding: 3px 5px;
  border: 1px dashed #4b5563;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: transparent;
}

/* Panel */
.olo-mm-panel-wrap {
  margin-top: 4px;
  animation: olo-mm-fadeIn 0.2s ease;
}

@keyframes olo-mm-fadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}

.olo-mm-panel {
  background: #fff;
  border-radius: 8px;
}

.olo-mm-columns {
  display: grid;
  gap: 12px;
}

.olo-mm-column {
  min-width: 0;
}

.olo-mm-col-heading {
  font-size: 10px;
  font-weight: 600;
  color: #111827;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
  padding-bottom: 3px;
  border-bottom: 1px solid rgba(0,0,0,0.06);
}

.olo-mm-col-links {
  display: flex;
  flex-direction: column;
}

.olo-mm-col-link {
  font-size: 11px;
  color: #374151;
  text-decoration: none;
  padding: 2px 0;
  display: flex;
  flex-direction: column;
  gap: 1px;
  cursor: default;
  border-radius: 2px;
  transition: color 0.12s;
}
.olo-mm-col-link:hover {
  color: #6366f1;
}

.olo-mm-col-desc {
  font-size: 9px;
  color: #9ca3af;
  line-height: 1.3;
}

/* Label */
.olo-mm-label {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 10px;
  color: #6b7280;
  margin-top: 4px;
  padding: 0 2px;
}
.olo-mm-label-info {
  font-size: 9px;
  color: #4b5563;
  opacity: 0.7;
}

/* Empty */
.olo-mm-empty {
  color: #6b7280;
  font-size: 12px;
  padding: 16px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  opacity: 0.8;
}
</style>
