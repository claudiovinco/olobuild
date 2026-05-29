<template>
  <div ref="rootEl" class="olo-megamenu-preview" :class="{ 'olo-mm-clean': isClean, 'olo-mm-mobile': isMobile }" :style="rootStyle">
    <!-- Badges (hidden in clean mode) -->
    <div v-if="!isClean" class="olo-mm-badges">
      <span v-if="s.sticky" class="olo-mm-badge olo-mm-badge--sticky">{{ t('STICKY') }}</span>
      <span v-if="s.header_mode === 'overlay'" class="olo-mm-badge olo-mm-badge--overlay">{{ t('OVERLAY') }}</span>
      <span v-if="s.panel_origin === 'section'" class="olo-mm-badge olo-mm-badge--origin">{{ t('ORIGIN: SECTION') }}</span>
      <span class="olo-mm-badge olo-mm-badge--mob">{{ mobileLabel }}</span>
      <span v-if="isMobile" class="olo-mm-badge olo-mm-badge--responsive">{{ t('📱 MOBILE') }}</span>
    </div>

    <!-- ═══════ DESKTOP VIEW ═══════ -->
    <template v-if="!isMobile">
      <!-- Navbar Bar -->
      <nav class="olo-mm-bar" :style="barStyle">
        <!-- Logo left / mobile logo -->
        <div
          v-if="s.logo_image && (s.logo_position === 'left' || s.logo_position === 'stacked' || s.logo_position === 'split')"
          class="olo-mm-logo-wrap"
          :style="logoWrapStyle"
        >
          <img :src="s.logo_image" :alt="t('Logo')" :style="logoImgStyle" />
        </div>

        <!-- Nav Left (split) -->
        <ul v-if="s.logo_position === 'split'" class="olo-mm-nav" :style="{ ...navStyle, flex: 'none' }">
          <li v-for="(item, idx) in leftItems" :key="'l'+idx" class="olo-mm-nav-item"
            :class="navItemClasses(item, idx)"
            @mouseenter="item.isMega ? (activeMegaIdx = idx) : null"
          >
            <a v-if="!item.isButton" class="olo-mm-nav-link" :style="getLinkStyle(item, idx)" href="javascript:void(0)"
              :data-text="item.label">
              {{ item.label }}
              <svg v-if="item.isMega || item.hasChildren" class="olo-mm-chevron" width="10" height="10" viewBox="0 0 12 12" fill="currentColor"><path d="M2 4l4 4 4-4z"/></svg>
            </a>
            <a v-else class="olo-mm-btn" :style="btnStyle" href="javascript:void(0)">{{ item.label }}</a>
            <span v-if="hoverEffectLine(item, idx)" class="olo-mm-hover-line" :style="hoverLineStyle"></span>
          </li>
        </ul>

        <!-- Logo center -->
        <div v-if="s.logo_image && s.logo_position === 'center'" class="olo-mm-logo-center">
          <img :src="s.logo_image" :alt="t('Logo')" :style="logoImgStyle" />
        </div>

        <!-- Hamburger (SVG curato) — hidden in clean mode (desktop) -->
        <button v-if="!isClean" class="olo-mm-hamburger-btn" :style="hamburgerBtnStyle" :title="'Stile: ' + s.hamburger_style">
          <component :is="hamburgerSvgComponent" />
        </button>

        <!-- Nav Items (main or right half of split) -->
        <ul class="olo-mm-nav" :style="navStyle">
          <li
            v-for="(item, idx) in (s.logo_position === 'split' ? rightItems : previewItems)"
            :key="idx"
            class="olo-mm-nav-item"
            :class="navItemClasses(item, idx)"
            @mouseenter="item.isMega ? (activeMegaIdx = idx) : null"
          >
            <a v-if="!item.isButton" class="olo-mm-nav-link" :style="getLinkStyle(item, idx)" href="javascript:void(0)"
              :data-text="item.label">
              {{ item.label }}
              <svg v-if="item.isMega || (item.hasChildren && !item.isButton)" class="olo-mm-chevron" width="10" height="10" viewBox="0 0 12 12" fill="currentColor"><path d="M2 4l4 4 4-4z"/></svg>
            </a>
            <a v-else class="olo-mm-btn" :style="btnStyle" href="javascript:void(0)">{{ item.label }}</a>
            <span v-if="hoverEffectLine(item, idx)" class="olo-mm-hover-line" :style="hoverLineStyle"></span>
          </li>

          <!-- Search icon -->
          <li v-if="s.search_icon" class="olo-mm-nav-item olo-mm-nav-item--search" :style="{ order: 99 }">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" :style="{ color: s.text_color || 'currentColor' }">
              <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/>
            </svg>
          </li>
        </ul>

        <!-- Social bar (desktop) -->
        <div v-if="hasSocialIcons && (s.social_position === 'bar-right' || s.social_position === 'both')"
          class="olo-mm-social-bar" :style="socialBarStyle">
          <span v-for="sn in activeSocials" :key="sn" class="olo-mm-social-icon" :style="socialIconStyle" :title="sn">
            <svg viewBox="0 0 24 24" :style="socialSvgStyle" fill="currentColor" v-html="socialSvg(sn)"></svg>
          </span>
        </div>

        <!-- Logo right -->
        <div v-if="s.logo_image && s.logo_position === 'right'" class="olo-mm-logo-wrap" :style="{ order: 99 }">
          <img :src="s.logo_image" :alt="t('Logo')" :style="logoImgStyle" />
        </div>
      </nav>

      <!-- Mega Panel -->
      <div
        v-if="activeMegaIdx >= 0 && previewItems[activeMegaIdx]?.isMega"
        class="olo-mm-panel-container"
        :style="panelContainerStyle"
        @mouseleave="activeMegaIdx = -1"
      >
        <div class="olo-mm-panel" :style="panelStyle">
          <div v-if="panelBorderTop > 0" class="olo-mm-panel-accent" :style="accentLineStyle"></div>
          <div class="olo-mm-grid" :style="gridStyle">
            <div v-for="(col, cIdx) in panelColumns" :key="cIdx" class="olo-mm-col" :style="colStyle(cIdx)">
              <div class="olo-mm-heading" :style="headingStyle">{{ col.heading }}</div>
              <div class="olo-mm-links">
                <a v-for="(link, lIdx) in col.links" :key="lIdx" class="olo-mm-link" :style="linkStyleObj" href="javascript:void(0)">
                  {{ link }}
                  <span v-if="s.show_descriptions" class="olo-mm-desc" :style="descStyleObj">{{ t('Descrizione breve') }}</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- ═══════ MOBILE VIEW ═══════ -->
    <template v-else>
      <!-- Mobile top bar (like frontend) -->
      <nav class="olo-mm-bar olo-mm-bar--mobile" :style="mobileNavBarStyle">
        <div v-if="s.mobile_bar_logo && (s.mobile_logo || s.logo_image)" class="olo-mm-logo-wrap" style="flex-shrink:0">
          <img :src="s.mobile_logo || s.logo_image" alt="Logo" :style="mobileLogoImgStyle" />
        </div>
        <div style="flex:1"></div>
        <div v-if="s.mobile_search" class="olo-mm-mob-search-icon" :style="{ color: s.hamburger_color || s.text_color || '#374151' }">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/>
          </svg>
        </div>
        <button class="olo-mm-hamburger-btn olo-mm-hamburger-btn--mobile" :style="mobileHamburgerBtnStyle" @click="mobileMenuOpen = !mobileMenuOpen">
          <component :is="hamburgerSvgComponent" />
        </button>
      </nav>

      <!-- Mobile menu (togglable in builder) -->
      <div v-if="mobileMenuOpen" class="olo-mm-mob-panel" :style="mobilePanelStyle">
        <div v-for="(item, i) in previewItems" :key="'mob'+i" class="olo-mm-mob-item" :style="mobilePanelItemStyle">
          <a href="javascript:void(0)" :style="mobilePanelLinkStyle">
            {{ item.label }}
            <svg v-if="item.hasChildren || item.isMega" width="12" height="12" viewBox="0 0 12 12" fill="currentColor" style="opacity:.4;margin-left:auto"><path d="M4 2l4 4-4 4z"/></svg>
          </a>
        </div>
        <!-- Social footer -->
        <div v-if="hasSocialIcons && (s.social_position === 'menu-footer' || s.social_position === 'both')"
          class="olo-mm-mob-social" :style="mobileSocialStyle">
          <span v-for="sn in activeSocials" :key="sn" class="olo-mm-social-icon" :style="mobileSocialIconStyle">
            <svg viewBox="0 0 24 24" :style="mobileSocialSvgStyle" fill="currentColor" v-html="socialSvg(sn)"></svg>
          </span>
        </div>
      </div>
    </template>

    <!-- Mobile Preview Strip (only in non-clean desktop mode) -->
    <div v-if="!isClean && !isMobile" class="olo-mm-mobile-strip" :style="mobileStripStyle">
      <div class="olo-mm-mobile-bar" :style="mobileBarStyle">
        <div v-if="s.mobile_bar_logo && (s.mobile_logo || s.logo_image)" class="olo-mm-mob-logo-mini">
          <img :src="s.mobile_logo || s.logo_image" alt="Logo" style="max-height:20px;width:auto;display:block;" />
        </div>
        <div style="flex:1"></div>
        <div v-if="s.mobile_search" class="olo-mm-mob-search-icon">
          <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/></svg>
        </div>
        <button class="olo-mm-mob-hamburger" :style="mobHamburgerStyle">
          <component :is="hamburgerSvgComponent" />
        </button>
      </div>
      <!-- Mobile menu preview -->
      <div class="olo-mm-mob-menu" :style="mobileMenuStyle">
        <div v-for="(item, i) in previewItems.slice(0, 5)" :key="'m'+i" class="olo-mm-mob-item" :style="mobileItemStyle">
          {{ item.label }}
        </div>
        <!-- Social footer -->
        <div v-if="hasSocialIcons && (s.social_position === 'menu-footer' || s.social_position === 'both')"
          class="olo-mm-mob-social" :style="mobileSocialStyle">
          <span v-for="sn in activeSocials" :key="sn" class="olo-mm-social-icon" :style="mobileSocialIconStyle">
            <svg viewBox="0 0 24 24" :style="mobileSocialSvgStyle" fill="currentColor" v-html="socialSvg(sn)"></svg>
          </span>
        </div>
      </div>
    </div>

    <!-- Info label (hidden in clean mode) -->
    <div v-if="!isClean" class="olo-mm-info">
      <span v-if="selectedMenu">{{ selectedMenu.name }} · {{ menuItemCount }} {{ t('voci') }}</span>
      <span v-else>{{ t('Nessun menu selezionato') }}</span>
      <span class="olo-mm-info-tags">
        <span class="olo-mm-info-tag">{{ s.hamburger_style }}</span>
        <span class="olo-mm-info-tag">hover: {{ s.hover_effect || 'none' }}</span>
        <span class="olo-mm-info-tag">panel: {{ s.panel_size || 'auto' }}</span>
        <span v-if="s.panel_open_animation !== 'fade'" class="olo-mm-info-tag">anim: {{ s.panel_open_animation }}</span>
        <span v-if="s.fullscreen_animation !== 'fade'" class="olo-mm-info-tag">fs: {{ s.fullscreen_animation }}</span>
        <span v-if="s.menu_items_animation !== 'none'" class="olo-mm-info-tag">stagger: {{ s.menu_items_animation }}</span>
        <span v-if="isMobile" class="olo-mm-info-tag" style="background:#7c3aed;color:#fff">{{ containerWidth }}px</span>
      </span>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed, ref, h, onMounted, onUnmounted } from 'vue';
import { getShadowValue } from '@/composables/useShadowMap';
import { useBuilderStore } from '@/stores/builder';

const builderStore = useBuilderStore();

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const isClean = computed(() => builderStore.cleanMode);

// ── Responsive: detect container width for mobile switch ──
const rootEl = ref(null);
const containerWidth = ref(9999);
let resizeObserver = null;

const isMobile = computed(() => {
  const bp = parseInt(s.value.mobile_breakpoint) || 1024;
  return containerWidth.value < bp;
});

onMounted(() => {
  if (rootEl.value) {
    containerWidth.value = rootEl.value.offsetWidth;
    resizeObserver = new ResizeObserver((entries) => {
      for (const entry of entries) {
        containerWidth.value = entry.contentRect.width;
      }
    });
    resizeObserver.observe(rootEl.value);
  }
});

onUnmounted(() => {
  if (resizeObserver) {
    resizeObserver.disconnect();
    resizeObserver = null;
  }
});

const defaults = {
  menu_id: 0, layout: 'left', nav_bg: '', nav_height: '60',
  text_color: '', hover_color: '', active_color: '',
  font_size: '15', font_weight: 'normal', text_transform: 'none',
  letter_spacing: '0', item_gap: '15',
  bar_padding: '16', bar_gap: '20', logo_margin_right: '0',
  logo_image: '', logo_width: '140', logo_position: 'left',
  hover_effect: 'none', hover_effect_color: '', hover_effect_height: '2',
  mega_mode: 'auto', panel_width: 'container', panel_columns: '4',
  panel_bg: '#ffffff', panel_shadow: 'md', panel_radius: '8',
  panel_padding: '32', panel_border_top: '3', panel_border_color: '',
  panel_animation: 'fade', panel_max_width: '900',
  panel_offset_top: '0', panel_origin: 'nav',
  panel_size: 'auto', panel_open_animation: 'fade',
  show_dividers: false,
  heading_color: '', heading_size: '14', heading_weight: '600',
  heading_transform: 'uppercase',
  link_color: '', link_hover_color: '',
  link_size: '14', link_spacing: '8',
  show_descriptions: false, desc_color: '',
  button_mode: 'none', btn_bg: '', btn_color: '',
  btn_radius: '6', btn_hover_bg: '',
  search_icon: true, hamburger_style: 'classic', hamburger_size: '28',
  hamburger_color: '',
  mobile_breakpoint: '1024', mobile_style: 'offcanvas', mobile_side: 'left',
  mobile_slide_direction: 'left', offcanvas_fullscreen: false,
  fullscreen_animation: 'fade',
  menu_items_animation: 'none', menu_items_stagger: '80',
  mobile_bg: '#1e1e2e', mobile_text_color: '#ffffff',
  mobile_heading_color: '', mobile_accent_color: '',
  mobile_separator: true, mobile_font_size: '17', mobile_item_padding: '16',
  mobile_logo: '', mobile_logo_height: '36',
  mobile_bar_logo: true, mobile_search: true,
  social_facebook: '', social_instagram: '', social_x: '',
  social_linkedin: '', social_youtube: '', social_tiktok: '',
  social_pinterest: '', social_whatsapp: '',
  social_position: 'menu-footer', social_size: '20',
  social_color: '', social_hover_color: '', social_style: 'plain',
  sticky: false, header_mode: 'overlay',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const oloData = window.oloData || {};
const wpMenus = oloData.wpMenus || [];

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
const mobileMenuOpen = ref(false);

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

// Split nav
const leftItems = computed(() => {
  const all = previewItems.value;
  const mid = Math.ceil(all.length / 2);
  return all.slice(0, mid);
});
const rightItems = computed(() => {
  const all = previewItems.value;
  const mid = Math.ceil(all.length / 2);
  return all.slice(mid);
});

// Social
const activeSocials = computed(() => {
  const keys = ['facebook','instagram','x','linkedin','youtube','tiktok','pinterest','whatsapp'];
  return keys.filter(k => !!s.value['social_' + k]);
});
const hasSocialIcons = computed(() => activeSocials.value.length > 0);

function socialSvg(name) {
  const m = {
    facebook: '<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>',
    instagram: '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>',
    x: '<path d="M4 4l6.5 8L4 20h2l5.5-6.8L16 20h4l-6.8-8.4L20 4h-2l-5.2 6.4L8 4z"/>',
    linkedin: '<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>',
    youtube: '<path d="M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 00-1.94 2A29 29 0 001 11.75a29 29 0 00.46 5.33A2.78 2.78 0 003.4 19.1c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 001.94-2 29 29 0 00.46-5.25 29 29 0 00-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>',
    tiktok: '<path d="M9 12a4 4 0 104 4V4a5 5 0 005 5"/>',
    pinterest: '<path d="M12 2C6.48 2 2 6.48 2 12c0 4.25 2.67 7.88 6.42 9.31-.09-.78-.17-1.99.04-2.85.19-.78 1.2-5.08 1.2-5.08s-.31-.61-.31-1.52c0-1.42.83-2.49 1.86-2.49.88 0 1.3.66 1.3 1.45 0 .88-.56 2.2-.85 3.42-.24 1.02.51 1.86 1.52 1.86 1.82 0 3.22-1.92 3.22-4.69 0-2.45-1.76-4.17-4.28-4.17-2.91 0-4.62 2.19-4.62 4.44 0 .88.34 1.82.76 2.34.08.1.1.19.07.29l-.28 1.16c-.05.19-.15.23-.35.14-1.31-.61-2.13-2.53-2.13-4.07 0-3.31 2.41-6.36 6.94-6.36 3.65 0 6.48 2.6 6.48 6.07 0 3.62-2.28 6.54-5.46 6.54-1.07 0-2.07-.55-2.41-1.21l-.66 2.5c-.24.91-.88 2.05-1.31 2.75A10 10 0 0022 12c0-5.52-4.48-10-10-10z"/>',
    whatsapp: '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.019-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438A9.96 9.96 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2z"/>',
  };
  return m[name] || '<circle cx="12" cy="12" r="8"/>';
}

// Mobile label
const mobileLabel = computed(() => {
  const ms = s.value.mobile_style;
  if (ms === 'fullscreen') return 'FULLSCREEN';
  if (ms === 'dropdown') return 'DROPDOWN';
  const dir = s.value.mobile_slide_direction || 'left';
  return 'OFFCANVAS ' + dir.toUpperCase();
});

// Panel columns
const panelColumns = computed(() => {
  const numCols = Math.max(2, Math.min(6, parseInt(s.value.panel_columns) || 4));
  const activeItem = previewItems.value[activeMegaIdx.value];

  if (activeItem && topItems.value.length > 0) {
    const children = getChildren(activeItem.id);
    if (children.length > 0) {
      const cols = [];
      for (const child of children) {
        const grandkids = getChildren(child.id);
        cols.push({ heading: child.title, links: grandkids.map(gc => gc.title) });
      }
      if (cols.length > 0) return cols.slice(0, numCols);
    }
  }

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

// ── Hamburger SVG Components ──
const hamburgerSvgs = {
  classic: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', style: 'width:100%;height:100%' }, [
    h('line', { x1: 3, y1: 6, x2: 21, y2: 6 }),
    h('line', { x1: 3, y1: 12, x2: 21, y2: 12 }),
    h('line', { x1: 3, y1: 18, x2: 21, y2: 18 }),
  ]),
  squeeze: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', style: 'width:100%;height:100%' }, [
    h('line', { x1: 3, y1: 6, x2: 21, y2: 6 }),
    h('line', { x1: 5, y1: 12, x2: 19, y2: 12 }),
    h('line', { x1: 3, y1: 18, x2: 21, y2: 18 }),
  ]),
  arrow: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', style: 'width:100%;height:100%' }, [
    h('line', { x1: 3, y1: 6, x2: 18, y2: 6 }),
    h('line', { x1: 3, y1: 12, x2: 21, y2: 12 }),
    h('line', { x1: 3, y1: 18, x2: 18, y2: 18 }),
  ]),
  minimal: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', style: 'width:100%;height:100%' }, [
    h('line', { x1: 3, y1: 9, x2: 16, y2: 9 }),
    h('line', { x1: 3, y1: 15, x2: 21, y2: 15 }),
  ]),
  'dot-grid': () => h('svg', { viewBox: '0 0 24 24', fill: 'currentColor', style: 'width:100%;height:100%' }, [
    h('circle', { cx: 5, cy: 5, r: 2 }), h('circle', { cx: 12, cy: 5, r: 2 }), h('circle', { cx: 19, cy: 5, r: 2 }),
    h('circle', { cx: 5, cy: 12, r: 2 }), h('circle', { cx: 12, cy: 12, r: 2 }), h('circle', { cx: 19, cy: 12, r: 2 }),
    h('circle', { cx: 5, cy: 19, r: 2 }), h('circle', { cx: 12, cy: 19, r: 2 }), h('circle', { cx: 19, cy: 19, r: 2 }),
  ]),
  collapse: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', style: 'width:100%;height:100%' }, [
    h('line', { x1: 3, y1: 6, x2: 21, y2: 6 }),
    h('line', { x1: 7, y1: 12, x2: 17, y2: 12, 'stroke-dasharray': '4 2' }),
    h('line', { x1: 3, y1: 18, x2: 21, y2: 18 }),
  ]),
  rotate: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', style: 'width:100%;height:100%' }, [
    h('line', { x1: 3, y1: 6, x2: 21, y2: 6 }),
    h('line', { x1: 3, y1: 12, x2: 21, y2: 12 }),
    h('line', { x1: 3, y1: 18, x2: 21, y2: 18 }),
    h('path', { d: 'M20 2l2 2-2 2', 'stroke-width': '1.5', opacity: '.4' }),
  ]),
  elastic: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', style: 'width:100%;height:100%' }, [
    h('path', { d: 'M3 6c3-2 15 2 18 0' }),
    h('line', { x1: 3, y1: 12, x2: 21, y2: 12 }),
    h('path', { d: 'M3 18c3 2 15-2 18 0' }),
  ]),
  morph: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2.5', 'stroke-linecap': 'round', style: 'width:100%;height:100%' }, [
    h('path', { d: 'M3 6h18' }),
    h('path', { d: 'M3 12h12' }),
    h('path', { d: 'M3 18h18' }),
  ]),
  magnetic: () => h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', style: 'width:100%;height:100%' }, [
    h('line', { x1: 3, y1: 6, x2: 21, y2: 6 }),
    h('line', { x1: 3, y1: 12, x2: 21, y2: 12 }),
    h('line', { x1: 3, y1: 18, x2: 21, y2: 18 }),
    h('circle', { cx: 12, cy: 12, r: 3, 'stroke-width': '1', opacity: '.3', 'stroke-dasharray': '2 2' }),
  ]),
};

const hamburgerSvgComponent = computed(() => {
  const style = s.value.hamburger_style || 'classic';
  return hamburgerSvgs[style] || hamburgerSvgs.classic;
});

// ── Nav item classes ──
function navItemClasses(item, idx) {
  return {
    'olo-mm-nav-item--active': item.isMega && idx === activeMegaIdx.value,
    'olo-mm-nav-item--mega': item.isMega,
    'olo-mm-nav-item--button': item.isButton,
    'olo-mm-nav-item--dropdown': item.hasChildren && !item.isMega && !item.isButton,
  };
}

// ── Hover effect indicator ──
function hoverEffectLine(item, idx) {
  if (item.isButton) return false;
  return idx === activeMegaIdx.value && item.isMega;
}

const hoverLineStyle = computed(() => {
  const he = s.value.hover_effect || 'none';
  const color = s.value.hover_effect_color || s.value.active_color || 'var(--olo-color-primary, #e1474f)';
  const h2 = parseInt(s.value.hover_effect_height) || 2;

  if (he === 'overline') return { position: 'absolute', top: 0, left: 0, right: 0, height: h2 + 'px', background: color };
  if (he === 'double-line') return { position: 'absolute', top: 0, left: 0, right: 0, height: h2 + 'px', background: color, boxShadow: `0 ${28}px 0 ${color}` };
  if (he === 'background' || he === 'highlight' || he === 'fill-up') return { position: 'absolute', inset: 0, background: color, opacity: 0.12, borderRadius: '4px' };
  if (he === 'framed') return { position: 'absolute', inset: 0, border: h2 + 'px solid ' + color, borderRadius: '4px' };
  if (he === 'dot') return { position: 'absolute', bottom: '-6px', left: '50%', transform: 'translateX(-50%)', width: '6px', height: '6px', borderRadius: '50%', background: color };
  if (he === 'bracket') return { position: 'absolute', top: 0, bottom: 0, left: 0, width: '6px', borderTop: h2 + 'px solid ' + color, borderBottom: h2 + 'px solid ' + color, borderLeft: h2 + 'px solid ' + color };
  if (he === 'underline-grow') return { position: 'absolute', bottom: 0, left: 0, right: 0, height: Math.max(3, h2 + 1) + 'px', background: color };

  // Default: underline
  return { position: 'absolute', bottom: 0, left: 0, right: 0, height: h2 + 'px', background: color };
});

// ── Styles ──

const rootStyle = computed(() => ({ position: 'relative' }));

const barStyle = computed(() => {
  const st = {
    display: 'flex', alignItems: 'center',
    padding: '0 ' + (parseInt(s.value.bar_padding) || 16) + 'px',
    gap: (parseInt(s.value.bar_gap) || 20) + 'px',
    minHeight: (parseInt(s.value.nav_height) || 60) + 'px',
    transition: 'background .3s',
  };
  if (s.value.nav_bg) st.background = s.value.nav_bg;
  // Clean mode: remove builder chrome
  if (isClean.value) {
    st.borderRadius = '0';
    st.border = 'none';
  }
  return st;
});

const navStyle = computed(() => ({
  display: 'flex', alignItems: 'center', listStyle: 'none',
  margin: 0, padding: 0, flex: 1,
  gap: (parseInt(s.value.item_gap) || 15) + 'px',
  justifyContent: s.value.layout === 'center' ? 'center' : s.value.layout === 'right' ? 'flex-end' : 'flex-start',
}));

const logoWrapStyle = computed(() => {
  const st = { display: 'flex', alignItems: 'center', flexShrink: 0 };
  const mr = parseInt(s.value.logo_margin_right) || 0;
  if (mr > 0) st.marginRight = mr + 'px';
  if (s.value.logo_position === 'left') st.order = -1;
  return st;
});

const logoImgStyle = computed(() => ({
  maxWidth: (parseInt(s.value.logo_width) || 140) + 'px',
  height: 'auto', maxHeight: '36px', display: 'block',
}));

const hamburgerBtnStyle = computed(() => ({
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  width: Math.min(28, parseInt(s.value.hamburger_size) || 28) + 'px',
  height: Math.min(28, parseInt(s.value.hamburger_size) || 28) + 'px',
  border: 'none', background: 'rgba(0,0,0,0.04)', borderRadius: '4px',
  cursor: 'default', flexShrink: 0, marginRight: '8px',
  color: s.value.hamburger_color || s.value.text_color || '#374151',
  padding: '3px',
}));

function getLinkStyle(item, idx) {
  const isActive = idx === activeMegaIdx.value && item.isMega;
  // TOKEN-FIRST: voce attiva = primario brand (era #e1474f); testo neutro → token
  return {
    color: isActive ? (s.value.active_color || 'var(--olo-color-primary, #e1474f)') : (s.value.text_color || 'var(--olo-color-text, #374151)'),
    fontSize: (parseInt(s.value.font_size) || 15) + 'px',
    fontWeight: s.value.font_weight || 'normal',
    textTransform: s.value.text_transform || 'none',
    letterSpacing: (parseFloat(s.value.letter_spacing) || 0) + 'px',
    textDecoration: 'none', display: 'inline-flex', alignItems: 'center',
    gap: '4px', whiteSpace: 'nowrap', padding: '8px 0', transition: 'color .2s',
  };
}

const btnStyle = computed(() => ({
  display: 'inline-flex', alignItems: 'center',
  padding: '8px 20px',
  // CTA = primario brand (era #e1474f indaco off-brand)
  background: s.value.btn_bg || 'var(--olo-color-primary, #e1474f)',
  color: (s.value.btn_color || 'var(--olo-color-primary-contrast, #fff)') + ' !important',
  borderRadius: (parseInt(s.value.btn_radius) || 6) + 'px',
  fontSize: (parseInt(s.value.font_size) || 15) + 'px',
  fontWeight: '600', textDecoration: 'none', whiteSpace: 'nowrap',
}));

// ── Panel styles ──
const panelContainerStyle = computed(() => {
  const ps = s.value.panel_size || 'auto';
  const st = { position: 'absolute', left: '0', right: '0', top: '100%', zIndex: 99, maxWidth: '100%', overflow: 'hidden', borderRadius: '0 0 8px 8px' };
  const offset = parseInt(s.value.panel_offset_top) || 0;
  if (offset > 0) st.marginTop = offset + 'px';

  // Panel open animation preview
  const anim = s.value.panel_open_animation || 'fade';
  const animMap = {
    'fade': 'olo-mm-panelFade', 'slide-down': 'olo-mm-panelSlideDown',
    'slide-up': 'olo-mm-panelSlideUp', 'scale': 'olo-mm-panelScale',
    'scale-center': 'olo-mm-panelScaleCenter', 'flip': 'olo-mm-panelFlip',
    'reveal': 'olo-mm-panelReveal', 'blur': 'olo-mm-panelBlur',
  };
  st.animationName = animMap[anim] || 'olo-mm-panelFade';
  st.animationDuration = '0.3s';
  st.animationTimingFunction = 'ease';
  st.animationFillMode = 'both';

  return st;
});

const panelStyle = computed(() => ({
  background: s.value.panel_bg || '#ffffff',
  borderRadius: (parseInt(s.value.panel_radius) || 8) + 'px',
  boxShadow: getShadowValue(s.value, 'panel_shadow') || '0 10px 40px rgba(0,0,0,0.12)',
  padding: (parseInt(s.value.panel_padding) || 32) + 'px',
  position: 'relative', overflow: 'hidden',
  maxWidth: (s.value.panel_size === 'auto' || s.value.panel_size === 'centered') ? (parseInt(s.value.panel_max_width) || 900) + 'px' : 'none',
  margin: s.value.panel_size === 'centered' ? '0 auto' : undefined,
}));

const accentLineStyle = computed(() => ({
  position: 'absolute', top: 0, left: 0, right: 0,
  height: panelBorderTop.value + 'px',
  background: s.value.panel_border_color || 'var(--olo-color-primary, #e1474f)',
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
  return { borderRight: '1px solid rgba(0,0,0,0.08)', paddingRight: (parseInt(s.value.panel_padding) || 32) + 'px' };
}

const headingStyle = computed(() => ({
  fontSize: (parseInt(s.value.heading_size) || 14) + 'px',
  fontWeight: s.value.heading_weight || '600',
  color: s.value.heading_color || 'var(--olo-color-text, #111827)',
  textTransform: s.value.heading_transform || 'uppercase',
  letterSpacing: s.value.heading_transform === 'uppercase' ? '0.5px' : '0',
  margin: '0 0 12px', paddingBottom: '8px',
  borderBottom: '1px solid rgba(0,0,0,0.06)', lineHeight: '1.3',
}));

const linkStyleObj = computed(() => ({
  display: 'block', color: s.value.link_color || 'var(--olo-color-text, #374151)',
  fontSize: (parseInt(s.value.link_size) || 14) + 'px',
  padding: (parseInt(s.value.link_spacing) || 8) + 'px 0',
  textDecoration: 'none', lineHeight: '1.4',
}));

const descStyleObj = computed(() => ({
  display: 'block', color: s.value.desc_color || 'var(--olo-color-text-faint, #9ca3af)',
  fontSize: Math.max(11, (parseInt(s.value.link_size) || 14) - 2) + 'px',
  marginTop: '2px', lineHeight: '1.3',
}));

// ── Social styles ──
const socialBarStyle = computed(() => ({
  display: 'flex', alignItems: 'center', gap: '4px', order: 89,
}));

const socialIconStyle = computed(() => {
  const st = { display: 'flex', alignItems: 'center', justifyContent: 'center', color: s.value.social_color || 'inherit' };
  if (s.value.social_style === 'circle') {
    const sz = Math.min(parseInt(s.value.social_size) || 20, 20);
    Object.assign(st, { width: (sz + 10) + 'px', height: (sz + 10) + 'px', borderRadius: '50%', background: 'rgba(128,128,128,.12)' });
  } else if (s.value.social_style === 'rounded') {
    Object.assign(st, { padding: '4px', borderRadius: '4px', background: 'rgba(128,128,128,.12)' });
  }
  return st;
});

const socialSvgStyle = computed(() => {
  const sz = Math.min(parseInt(s.value.social_size) || 20, 16);
  return { width: sz + 'px', height: sz + 'px' };
});

// ── Mobile styles ──
const mobileStripStyle = computed(() => ({
  marginTop: '6px', borderRadius: '6px', overflow: 'hidden',
  border: '1px solid rgba(0,0,0,0.08)',
}));

const mobileBarStyle = computed(() => ({
  display: 'flex', alignItems: 'center', gap: '6px',
  padding: '4px 8px', minHeight: '28px',
  background: s.value.nav_bg || '#f3f4f6',
}));

const mobHamburgerStyle = computed(() => ({
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  width: '20px', height: '20px', border: 'none', background: 'none',
  cursor: 'default', color: s.value.hamburger_color || s.value.text_color || '#374151',
  padding: '2px',
}));

const mobileMenuStyle = computed(() => ({
  background: s.value.mobile_bg || '#1e1e2e',
  padding: '4px 0',
}));

const mobileItemStyle = computed(() => ({
  padding: '4px 12px',
  color: s.value.mobile_text_color || '#ffffff',
  fontSize: '11px', fontWeight: '500',
  borderBottom: s.value.mobile_separator ? '1px solid rgba(255,255,255,.08)' : 'none',
}));

const mobileSocialStyle = computed(() => ({
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  gap: '6px', padding: '6px 12px',
  borderTop: '1px solid rgba(255,255,255,.08)',
}));

const mobileSocialIconStyle = computed(() => {
  const st = { display: 'flex', alignItems: 'center', justifyContent: 'center', color: s.value.social_color || s.value.mobile_text_color || '#fff' };
  if (s.value.social_style === 'circle') Object.assign(st, { width: '20px', height: '20px', borderRadius: '50%', background: 'rgba(255,255,255,.12)' });
  else if (s.value.social_style === 'rounded') Object.assign(st, { padding: '3px', borderRadius: '3px', background: 'rgba(255,255,255,.12)' });
  return st;
});

const mobileSocialSvgStyle = computed(() => ({ width: '12px', height: '12px' }));

// ── Responsive mobile view styles (actual mobile bar, not the mini preview strip) ──
const mobileNavBarStyle = computed(() => {
  const st = {
    display: 'flex', alignItems: 'center',
    padding: '0 ' + (parseInt(s.value.bar_padding) || 16) + 'px',
    gap: '12px',
    minHeight: (parseInt(s.value.nav_height) || 60) + 'px',
    transition: 'background .3s',
  };
  if (s.value.nav_bg) st.background = s.value.nav_bg;
  if (isClean.value) {
    st.borderRadius = '0';
    st.border = 'none';
  }
  return st;
});

const mobileLogoImgStyle = computed(() => ({
  maxHeight: (parseInt(s.value.mobile_logo_height) || 36) + 'px',
  width: 'auto', display: 'block',
}));

const mobileHamburgerBtnStyle = computed(() => {
  const sz = parseInt(s.value.hamburger_size) || 28;
  return {
    display: 'flex', alignItems: 'center', justifyContent: 'center',
    width: sz + 'px', height: sz + 'px',
    border: 'none', background: 'none', cursor: 'pointer', flexShrink: 0,
    color: s.value.hamburger_color || s.value.text_color || '#374151',
    padding: '3px',
  };
});

const mobilePanelStyle = computed(() => ({
  background: s.value.mobile_bg || '#1e1e2e',
  padding: '8px 0',
  animationName: 'olo-mm-panelSlideDown',
  animationDuration: '0.25s',
  animationTimingFunction: 'ease',
  animationFillMode: 'both',
}));

const mobilePanelItemStyle = computed(() => ({
  padding: (parseInt(s.value.mobile_item_padding) || 16) + 'px ' + (parseInt(s.value.bar_padding) || 16) + 'px',
  borderBottom: s.value.mobile_separator !== false ? '1px solid rgba(255,255,255,.08)' : 'none',
}));

const mobilePanelLinkStyle = computed(() => ({
  display: 'flex', alignItems: 'center',
  color: s.value.mobile_text_color || '#ffffff',
  fontSize: (parseInt(s.value.mobile_font_size) || 17) + 'px',
  fontWeight: '500',
  textDecoration: 'none',
  width: '100%',
}));
</script>

<style scoped>
.olo-megamenu-preview {
  min-height: 36px;
  display: flex;
  flex-direction: column;
}

/* Badges */
.olo-mm-badges { display: flex; gap: 4px; justify-content: flex-end; margin-bottom: 2px; flex-wrap: wrap; }
.olo-mm-badge { font-size: 8px; font-weight: 700; letter-spacing: .06em; padding: 1px 5px; border-radius: 3px; line-height: 1.4; }
.olo-mm-badge--sticky { background: #7c3aed; color: #fff; }
.olo-mm-badge--overlay { background: #059669; color: #fff; }
.olo-mm-badge--origin { background: #d97706; color: #fff; }
.olo-mm-badge--mob { background: #374151; color: #fff; }

/* Navbar bar */
.olo-mm-bar { border-radius: 6px 6px 0 0; border: 1px solid rgba(0,0,0,.06); position: relative; z-index: 2; }

/* Logo center */
.olo-mm-logo-center { position: absolute; left: 50%; transform: translateX(-50%); }

/* Hamburger */
.olo-mm-hamburger-btn { flex-shrink: 0; }

/* Nav */
.olo-mm-nav { flex-wrap: nowrap; overflow: hidden; }
.olo-mm-nav-item { position: relative; list-style: none; display: flex; align-items: center; }
.olo-mm-nav-link { cursor: default; position: relative; }
.olo-mm-nav-link:hover { opacity: .8; }
/* a11y tastiera: anello di focus visibile su link/CTA/hamburger del megamenu */
.olo-mm-nav-link:focus-visible,
.olo-mm-btn:focus-visible,
.olo-mm-link:focus-visible,
.olo-mm-hamburger-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
  border-radius: 3px;
}
.olo-mm-chevron { opacity: .4; transition: transform .2s; }
.olo-mm-nav-item--active .olo-mm-chevron { transform: rotate(180deg); }

/* Hover effect line */
.olo-mm-hover-line { pointer-events: none; animation: olo-mm-lineIn .25s ease forwards; }
@keyframes olo-mm-lineIn { from { transform: scaleX(0); } to { transform: scaleX(1); } }

/* CTA Button */
.olo-mm-btn { cursor: default; }

/* Search */
.olo-mm-nav-item--search { padding: 4px; opacity: .85; }

/* Panel container */
.olo-mm-panel-container { position: relative; z-index: 1; }

/* Panel animations */
@keyframes olo-mm-panelFade { from { opacity: 0; } to { opacity: 1; } }
@keyframes olo-mm-panelSlideDown { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: translateY(0); } }
@keyframes olo-mm-panelSlideUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
@keyframes olo-mm-panelScale { from { opacity: 0; transform: scaleY(.85); transform-origin: top; } to { opacity: 1; transform: scaleY(1); } }
@keyframes olo-mm-panelScaleCenter { from { opacity: 0; transform: scale(.9); } to { opacity: 1; transform: scale(1); } }
@keyframes olo-mm-panelFlip { from { opacity: 0; transform: perspective(800px) rotateX(-15deg); transform-origin: top; } to { opacity: 1; transform: perspective(800px) rotateX(0); } }
@keyframes olo-mm-panelReveal { from { clip-path: inset(0 0 100% 0); } to { clip-path: inset(0 0 0 0); } }
@keyframes olo-mm-panelBlur { from { opacity: 0; filter: blur(8px); } to { opacity: 1; filter: blur(0); } }

/* Panel */
.olo-mm-panel { border: 1px solid rgba(0,0,0,.06); }
.olo-mm-panel-accent { pointer-events: none; }
.olo-mm-col { min-width: 0; }
.olo-mm-heading { cursor: default; }
.olo-mm-links { display: flex; flex-direction: column; }
.olo-mm-link { cursor: default; border-radius: 3px; }
.olo-mm-link:hover { padding-left: 4px; color: var(--olo-color-primary, #e1474f) !important; }
.olo-mm-desc { pointer-events: none; }

/* Social bar */
.olo-mm-social-bar { flex-shrink: 0; }
.olo-mm-social-icon { cursor: default; }

/* Mobile strip */
.olo-mm-mobile-strip { font-size: 11px; }
.olo-mm-mob-logo-mini { display: flex; align-items: center; }
.olo-mm-mob-search-icon { display: flex; align-items: center; opacity: .6; }
.olo-mm-mob-hamburger { flex-shrink: 0; }
.olo-mm-mob-item { line-height: 1.4; }
.olo-mm-mob-social { flex-wrap: wrap; }

/* Info */
.olo-mm-info { display: flex; align-items: center; justify-content: space-between; font-size: 10px; color: #9ca3af; margin-top: 4px; padding: 2px 4px; flex-wrap: wrap; gap: 4px; }
.olo-mm-info-tags { display: flex; gap: 3px; flex-wrap: wrap; }
.olo-mm-info-tag { font-size: 8px; background: #f3f4f6; color: #6b7280; padding: 1px 4px; border-radius: 2px; white-space: nowrap; }

/* ── Clean mode: frontend-like rendering ── */
.olo-mm-clean {
  /* No min-height constraint */
  min-height: 0;
}
.olo-mm-clean .olo-mm-bar {
  border-radius: 0 !important;
  border: none !important;
}
.olo-mm-clean .olo-mm-nav-link {
  cursor: pointer;
}
.olo-mm-clean .olo-mm-nav-link:hover {
  opacity: 1;
}
.olo-mm-clean .olo-mm-btn {
  cursor: pointer;
}
.olo-mm-clean .olo-mm-link {
  cursor: pointer;
}
.olo-mm-clean .olo-mm-panel {
  border: none;
}
.olo-mm-clean .olo-mm-panel-container {
  border-radius: 0 0 8px 8px;
}
.olo-mm-clean .olo-mm-heading {
  cursor: default;
}

/* ── Responsive mobile view ── */
.olo-mm-badge--responsive { background: #2563eb; color: #fff; }

.olo-mm-bar--mobile {
  border-radius: 0;
  border: 1px solid rgba(0,0,0,.06);
}
.olo-mm-clean .olo-mm-bar--mobile {
  border: none !important;
}

.olo-mm-hamburger-btn--mobile {
  background: none !important;
}

.olo-mm-mob-panel {
  overflow: hidden;
}
.olo-mm-mob-panel a {
  text-decoration: none;
}

.olo-mm-mob-search-icon {
  display: flex;
  align-items: center;
  opacity: .7;
}
</style>
