<template>
  <div class="olo-megamenu-preview">
    <template v-if="selectedMenu">
      <!-- Badges -->
      <div class="olo-mm-badges">
        <span v-if="s.sticky" class="olo-mm-badge olo-mm-badge--sticky">STICKY</span>
        <span class="olo-mm-badge olo-mm-badge--mega">MEGA</span>
      </div>
      <!-- Navbar bar -->
      <div class="olo-mm-bar" :style="barStyle">
        <template v-for="(item, idx) in previewItems" :key="idx">
          <span
            class="olo-mm-item"
            :class="{
              'olo-mm-item--button': item.isButton,
              'olo-mm-item--mega': item.isMega,
            }"
            :style="item.isButton ? btnStyle : itemStyle"
          >
            {{ item.label }}
            <span v-if="item.isMega" class="olo-mm-arrow">&#9660;</span>
          </span>
        </template>
      </div>
      <div class="olo-mm-label">{{ selectedMenu.name }}</div>
    </template>
    <div v-else class="olo-mm-empty">
      Seleziona un menu nell'Inspector
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
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
  button_mode: 'none',
  btn_bg: '',
  btn_color: '#FFFFFF',
  btn_radius: '6',
  sticky: false,
  ...props.settings,
}));

const oloData = window.oloData || {};
const wpMenus = oloData.wpMenus || [];

const selectedMenu = computed(() => {
  const id = parseInt(s.value.menu_id) || 0;
  if (!id) return null;
  return wpMenus.find(m => m.id === id) || null;
});

const barStyle = computed(() => {
  const st = {
    justifyContent: s.value.layout === 'center' ? 'center' : s.value.layout === 'right' ? 'flex-end' : 'flex-start',
    gap: (parseInt(s.value.item_gap) || 15) + 'px',
    minHeight: (parseInt(s.value.nav_height) || 60) * 0.5 + 'px',
  };
  if (s.value.nav_bg) st.background = s.value.nav_bg;
  return st;
});

const itemStyle = computed(() => {
  const st = {};
  if (s.value.text_color) st.color = s.value.text_color;
  const fs = parseInt(s.value.font_size) || 15;
  st.fontSize = Math.max(11, fs - 2) + 'px';
  if (s.value.font_weight && s.value.font_weight !== 'normal') st.fontWeight = s.value.font_weight;
  if (s.value.text_transform && s.value.text_transform !== 'none') st.textTransform = s.value.text_transform;
  const ls = parseFloat(s.value.letter_spacing) || 0;
  if (ls > 0) st.letterSpacing = ls + 'px';
  return st;
});

const btnStyle = computed(() => {
  const st = {};
  if (s.value.btn_bg) st.background = s.value.btn_bg;
  else st.background = '#6366F1';
  if (s.value.btn_color) st.color = s.value.btn_color;
  else st.color = '#FFF';
  st.borderRadius = (parseInt(s.value.btn_radius) || 6) + 'px';
  return st;
});

const previewItems = computed(() => {
  const count = 5;
  const items = [];
  const btnMode = s.value.button_mode || 'none';
  const megaMode = s.value.mega_mode || 'auto';

  for (let i = 0; i < count; i++) {
    const label = i === 0 && selectedMenu.value ? selectedMenu.value.name : 'Voce ' + (i + 1);
    let isButton = false;
    let isMega = false;

    if (btnMode === 'last' && i === count - 1) isButton = true;
    if (btnMode === 'last-2' && i >= count - 2) isButton = true;

    if (!isButton && megaMode !== 'none' && i < 3) isMega = true;

    items.push({ label, isButton, isMega });
  }
  return items;
});
</script>

<style scoped>
.olo-megamenu-preview {
  min-height: 36px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  position: relative;
}
.olo-mm-badges {
  display: flex;
  gap: 4px;
  justify-content: flex-end;
  margin-bottom: 2px;
}
.olo-mm-badge {
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.05em;
  padding: 1px 5px;
  border-radius: 3px;
  line-height: 1.4;
}
.olo-mm-badge--sticky {
  background: #7c3aed;
  color: #fff;
}
.olo-mm-badge--mega {
  background: #3b82f6;
  color: #fff;
}
.olo-mm-bar {
  display: flex;
  padding: 6px 8px;
  align-items: center;
  border-radius: 6px;
  background: rgba(255, 255, 255, 0.04);
}
.olo-mm-item {
  font-size: 13px;
  color: #d1d5db;
  padding: 4px 8px;
  border-radius: 4px;
  background: rgba(255, 255, 255, 0.05);
  display: inline-flex;
  align-items: center;
  gap: 4px;
  white-space: nowrap;
}
.olo-mm-item--button {
  background: #6366F1;
  color: #fff;
  font-weight: 600;
  font-size: 12px;
  padding: 3px 10px;
}
.olo-mm-item--mega {
  border-bottom: 2px solid #3b82f6;
}
.olo-mm-arrow {
  font-size: 7px;
  opacity: 0.5;
}
.olo-mm-label {
  font-size: 10px;
  color: #6b7280;
}
.olo-mm-empty {
  color: #6b7280;
  font-size: 13px;
  font-style: italic;
  padding: 8px 0;
}
</style>
