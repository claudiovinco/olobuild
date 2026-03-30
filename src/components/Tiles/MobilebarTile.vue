<template>
  <div class="olo-mobilebar-preview" :style="barStyle">
    <!-- Bar preview -->
    <div class="olo-mbp-bar">
      <div class="olo-mbp-logo">
        <img v-if="s.logo_image" :src="s.logo_image" :style="{ maxWidth: logoW + 'px', maxHeight: '32px' }" alt="Logo">
        <span v-else class="olo-mbp-logo-placeholder">Logo</span>
      </div>
      <div class="olo-mbp-spacer"></div>
      <div v-if="s.search_enabled" class="olo-mbp-icon" title="Ricerca">
        <svg viewBox="0 0 24 24" :style="{ stroke: s.search_icon_color || '#fff' }"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </div>
      <div class="olo-mbp-icon olo-mbp-hamburger" title="Hamburger">
        <span :style="{ background: hamColor }"></span>
        <span :style="{ background: hamColor, width: hamStyle === 'minimal' ? '70%' : '100%' }"></span>
        <span v-if="hamStyle !== 'minimal'" :style="{ background: hamColor }"></span>
      </div>
    </div>

    <!-- Info badges -->
    <div class="olo-mbp-info">
      <span class="olo-mbp-badge">📱 Mobile &lt; {{ bp }}px</span>
      <span class="olo-mbp-badge olo-mbp-badge-style">☰ {{ styleLabel }}</span>
      <span v-if="s.menu_id" class="olo-mbp-badge olo-mbp-badge-menu">Menu #{{ s.menu_id }}</span>
    </div>

    <!-- Menu preview -->
    <div class="olo-mbp-menu-preview" :style="panelStyle">
      <div class="olo-mbp-menu-item" :style="itemStyle">
        <span :style="{ color: panelActive }">Home</span>
      </div>
      <div class="olo-mbp-menu-item" :style="itemStyle">
        <span>Pagina 1</span>
        <span class="olo-mbp-chev" :style="{ color: s.panel_chevron_color || '#999' }">›</span>
      </div>
      <div class="olo-mbp-menu-item" :style="itemStyle">
        <span>Pagina 2</span>
        <span class="olo-mbp-chev" :style="{ color: s.panel_chevron_color || '#999' }">›</span>
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
  breakpoint: '1024',
  logo_image: '',
  logo_width: '120',
  bar_bg: '#1a3a5c',
  bar_height: '56',
  bar_shadow: true,
  bar_padding: '12',
  hamburger_style: 'classic',
  hamburger_size: '28',
  hamburger_color: '#ffffff',
  menu_id: '',
  panel_bg: '#ffffff',
  panel_text_color: '#222222',
  panel_active_color: '',
  panel_font_size: '17',
  panel_item_padding: '16',
  panel_separator: true,
  panel_chevron_color: '#999999',
  search_enabled: true,
  search_icon_color: '#ffffff',
  search_placeholder: 'Cerca...',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const bp = computed(() => parseInt(s.value.breakpoint) || 1024);
const logoW = computed(() => parseInt(s.value.logo_width) || 120);
const hamColor = computed(() => s.value.hamburger_color || '#fff');
const hamStyle = computed(() => s.value.hamburger_style || 'classic');
const panelActive = computed(() => s.value.panel_active_color || 'var(--olo-color-primary, #e74c3c)');

const styleLabel = computed(() => {
  const map = {
    classic: 'Classic',
    squeeze: 'Squeeze',
    arrow: 'Arrow',
    'dot-grid': 'Dot Grid',
    minimal: 'Minimal',
  };
  return map[hamStyle.value] || 'Classic';
});

const barStyle = computed(() => ({
  '--mb-bar-bg': s.value.bar_bg || '#1a3a5c',
  '--mb-bar-h': (parseInt(s.value.bar_height) || 56) + 'px',
  borderRadius: '8px',
  overflow: 'hidden',
}));

const panelStyle = computed(() => ({
  background: s.value.panel_bg || '#fff',
  borderTop: '1px solid rgba(0,0,0,.08)',
}));

const itemStyle = computed(() => ({
  padding: (parseInt(s.value.panel_item_padding) || 16) + 'px',
  fontSize: (parseInt(s.value.panel_font_size) || 17) + 'px',
  color: s.value.panel_text_color || '#222',
  borderBottom: s.value.panel_separator ? '1px solid rgba(0,0,0,.06)' : 'none',
}));
</script>

<style scoped>
.olo-mobilebar-preview {
  border: 1px solid rgba(0,0,0,.1);
}
.olo-mbp-bar {
  display: flex;
  align-items: center;
  height: var(--mb-bar-h, 56px);
  padding: 0 12px;
  background: var(--mb-bar-bg, #1a3a5c);
  gap: 4px;
}
.olo-mbp-logo img {
  display: block;
  height: auto;
}
.olo-mbp-logo-placeholder {
  color: #fff;
  font-weight: 700;
  font-size: 16px;
  opacity: .7;
}
.olo-mbp-spacer { flex: 1; }
.olo-mbp-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  padding: 4px;
}
.olo-mbp-icon svg {
  width: 20px;
  height: 20px;
  fill: none;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
}
.olo-mbp-hamburger {
  flex-direction: column;
  gap: 4px;
}
.olo-mbp-hamburger span {
  display: block;
  width: 100%;
  height: 2px;
  border-radius: 2px;
}
.olo-mbp-info {
  display: flex;
  gap: 6px;
  padding: 8px;
  flex-wrap: wrap;
}
.olo-mbp-badge {
  display: inline-block;
  padding: 2px 8px;
  background: #f0f0f0;
  border-radius: 10px;
  font-size: 11px;
  color: #555;
  white-space: nowrap;
}
.olo-mbp-badge-style { background: #e8f4ff; color: #1a6db5; }
.olo-mbp-badge-menu { background: #e8ffe8; color: #2a7a2a; }
.olo-mbp-menu-preview {
  border-top: 1px solid rgba(0,0,0,.06);
}
.olo-mbp-menu-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-weight: 500;
  line-height: 1.3;
}
.olo-mbp-chev {
  font-size: 20px;
  opacity: .5;
}
</style>
