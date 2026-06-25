<template>
  <div class="olo-navmenu-preview">
    <template v-if="selectedMenu">
      <!-- Sticky badge -->
      <span v-if="s.sticky" class="olo-navmenu-badge olo-navmenu-badge--sticky">{{ t('STICKY') }}</span>

      <!-- Vertical mode -->
      <template v-if="s.style === 'vertical'">
        <div class="olo-navmenu-vert" :style="vertListStyle">
          <div v-for="(label, idx) in vertLabels" :key="idx"
            class="olo-navmenu-vert-item"
            :class="{ 'olo-navmenu-vert-item--active': idx === 0 }"
            :style="vertItemStyle(idx)">
            <span v-if="s.v_show_icons" class="olo-navmenu-vert-icon" :style="vertIconWrapStyle">
              <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="10" cy="10" r="4"/></svg>
            </span>
            <span>{{ label }}</span>
            <svg v-if="idx === 1 && s.v_expand_subs" class="olo-navmenu-vert-chev" width="10" height="10" viewBox="0 0 20 20" fill="currentColor"><path d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"/></svg>
          </div>
        </div>
        <div class="olo-navmenu-label">{{ selectedMenu.name }} — {{ t('verticale') }}</div>
      </template>

      <!-- Horizontal mode -->
      <template v-else>
        <div class="olo-navmenu-bar" :class="alignmentClass" :style="barStyle">
          <span v-if="hasSearch && s.search_position === 'before'" class="olo-navmenu-item olo-navmenu-item--search" :style="{ color: s.text_color || 'var(--olo-color-text-soft, #6b7280)' }" :title="t('Ricerca')">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/></svg>
          </span>
          <template v-for="(item, idx) in previewItems" :key="idx">
            <span
              class="olo-navmenu-item"
              :class="{
                'olo-navmenu-item--button': item.isButton,
                'olo-navmenu-item--mega': item.isMega,
              }"
              :style="item.isButton ? buttonItemStyle : itemStyle"
            >
              {{ item.label }}
              <span v-if="item.isMega" class="olo-navmenu-mega-arrow">&#9660;</span>
            </span>
          </template>
          <span v-if="hasSearch && s.search_position !== 'before'" class="olo-navmenu-item olo-navmenu-item--search" :style="{ color: s.text_color || 'var(--olo-color-text-soft, #6b7280)' }" :title="t('Ricerca')">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8.5" cy="8.5" r="5.5"/><line x1="13" y1="13" x2="17" y2="17"/></svg>
          </span>
        </div>
        <div class="olo-navmenu-label">{{ selectedMenu.name }}</div>
      </template>
    </template>
    <div v-else>
      <div class="olo-navmenu-bar" :class="alignmentClass" :style="barStyle">
        <span v-for="label in [t('Home'), t('Chi siamo'), t('Servizi'), t('Contatti')]" :key="label" class="olo-navmenu-item" :style="itemStyle">{{ label }}</span>
      </div>
      <div class="olo-navmenu-empty">{{ t("Seleziona un menu nell'Inspector") }}</div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  menu_id: 0,
  alignment: 'left',
  sticky: false,
  append_search: false,
  button_items: 'none',
  mega_menu: 'none',
  text_color: '',
  hover_color: '',
  active_color: '',
  font_size: '14',
  font_weight: '500',
  text_transform: 'none',
  letter_spacing: '0',
  gap: '8',
  button_style: 'primary',
  button_size: 'small',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const oloData = window.oloData || {};
const wpMenus = oloData.wpMenus || [];

const selectedMenu = computed(() => {
  const id = parseInt(s.value.menu_id) || 0;
  if (!id) return null;
  return wpMenus.find(m => m.id === id) || null;
});

const hasSearch = computed(() => !!s.value.search_tile_id || !!s.value.append_search);

const alignmentClass = computed(() => 'olo-navmenu-align-' + (s.value.alignment || 'left'));

const barStyle = computed(() => ({
  gap: (parseInt(s.value.gap) || 8) + 'px',
}));

// TOKEN-FIRST: testo voce → token testo soft (era #d1d5db nudo)
const itemStyle = computed(() => ({
  fontSize: (parseInt(s.value.font_size) || 14) + 'px',
  fontWeight: s.value.font_weight || '500',
  textTransform: s.value.text_transform || 'none',
  letterSpacing: (parseFloat(s.value.letter_spacing) || 0) + 'px',
  color: s.value.text_color || 'var(--olo-color-text-soft, #6b7280)',
}));

const buttonItemStyle = computed(() => {
  // CTA = primario brand (era #e1474f indaco off-brand)
  const accent = s.value.active_color || 'var(--olo-color-primary, #e1474f)';
  const isSecondary = s.value.button_style === 'secondary';
  return {
    fontSize: (parseInt(s.value.font_size) || 14) - 1 + 'px',
    fontWeight: '600',
    background: isSecondary ? 'transparent' : accent,
    color: isSecondary ? accent : 'var(--olo-color-primary-contrast, #fff)',
    border: isSecondary ? '1px solid ' + accent : 'none',
    padding: '3px 10px',
    borderRadius: '4px',
  };
});

// Vertical mode
const vertLabels = ['Home', 'Chi siamo', 'Servizi', 'Contatti', 'Blog'];

const vertListStyle = computed(() => ({
  gap: (parseInt(s.value.v_item_spacing) || 4) + 'px',
  textAlign: s.value.alignment || 'left',
}));

function vertItemStyle(idx) {
  const pad = parseInt(s.value.v_item_padding) || 10;
  const radius = parseInt(s.value.v_border_radius) || 6;
  // TOKEN-FIRST: voce attiva = primario brand (era #e1474f / rgba indaco off-brand)
  const accent = s.value.active_color || 'var(--olo-color-primary, #e1474f)';
  const base = {
    padding: pad + 'px ' + (pad + 4) + 'px',
    borderRadius: radius + 'px',
    fontSize: (parseInt(s.value.font_size) || 14) + 'px',
    fontWeight: s.value.font_weight || '500',
    color: s.value.text_color || 'var(--olo-color-text-soft, #6b7280)',
    textTransform: s.value.text_transform || 'none',
    letterSpacing: (parseFloat(s.value.letter_spacing) || 0) + 'px',
  };
  if (idx === 0) {
    const indicator = s.value.v_active_indicator || 'left-border';
    if (indicator === 'left-border') {
      base.borderLeft = '3px solid ' + accent;
      base.background = s.value.v_active_bg || `color-mix(in srgb, ${accent} 8%, transparent)`;
    } else if (indicator === 'background') {
      base.background = s.value.v_active_bg || `color-mix(in srgb, ${accent} 10%, transparent)`;
    }
    base.color = accent;
    if (indicator === 'bold') base.fontWeight = '700';
  }
  if (s.value.v_separator && idx > 0) {
    base.borderTop = '1px solid ' + (s.value.v_separator_color || 'rgba(255,255,255,0.08)');
  }
  return base;
}

const vertIconWrapStyle = computed(() => ({
  color: s.value.v_icon_color || s.value.text_color || 'var(--olo-color-text-soft, #6b7280)',
  width: (parseInt(s.value.v_icon_size) || 20) + 'px',
  height: (parseInt(s.value.v_icon_size) || 20) + 'px',
}));

const previewItems = computed(() => {
  const count = 4;
  const items = [];
  const btnMode = s.value.button_items || 'none';
  const megaMode = s.value.mega_menu || 'none';

  for (let i = 0; i < count; i++) {
    const label = i === 0 && selectedMenu.value ? selectedMenu.value.name : 'Link ' + (i + 1);
    let isButton = false;
    let isMega = false;

    if (btnMode === 'last' && i === count - 1) isButton = true;
    if (btnMode === 'last-2' && i >= count - 2) isButton = true;
    if (!isButton && megaMode !== 'none' && i < 2) isMega = true;

    items.push({ label, isButton, isMega });
  }
  return items;
});
</script>

<style scoped>
.olo-navmenu-preview {
  min-height: 36px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  position: relative;
}
.olo-navmenu-badge {
  position: absolute;
  top: -2px;
  right: 0;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.05em;
  padding: 1px 5px;
  border-radius: 3px;
  line-height: 1.4;
}
.olo-navmenu-badge--sticky {
  background: #7c3aed;
  color: #fff;
}
.olo-navmenu-bar {
  display: flex;
  padding: 8px 0;
  align-items: center;
}
.olo-navmenu-align-center {
  justify-content: center;
}
.olo-navmenu-align-right {
  justify-content: flex-end;
}
.olo-navmenu-item {
  padding: 4px 8px;
  border-radius: 4px;
  background: rgba(255, 255, 255, 0.05);
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
/* a11y tastiera: anello di focus visibile sulle voci di menu */
.olo-navmenu-item:focus-visible,
.olo-navmenu-vert-item:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
.olo-navmenu-item--mega {
  border-bottom: 2px solid var(--olo-color-primary, #e1474f);
}
.olo-navmenu-mega-arrow {
  font-size: 8px;
  opacity: 0.6;
}
.olo-navmenu-label {
  font-size: 10px;
  color: #6b7280;
}
.olo-navmenu-item--search {
  padding: 4px 6px;
  border: 1px dashed #4b5563;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: transparent;
}
.olo-navmenu-empty {
  color: #6b7280;
  font-size: 13px;
  font-style: italic;
  padding: 8px 0;
}
/* Vertical mode */
.olo-navmenu-vert {
  display: flex;
  flex-direction: column;
}
.olo-navmenu-vert-item {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: default;
  transition: background .15s;
}
.olo-navmenu-vert-item:hover {
  background: rgba(255,255,255,0.04);
}
.olo-navmenu-vert-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.olo-navmenu-vert-chev {
  margin-left: auto;
  opacity: 0.5;
}
</style>
