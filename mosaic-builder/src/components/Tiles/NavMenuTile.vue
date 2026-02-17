<template>
  <div class="olo-navmenu-preview">
    <template v-if="selectedMenu">
      <!-- Sticky badge -->
      <span v-if="settings.sticky" class="olo-navmenu-badge olo-navmenu-badge--sticky">STICKY</span>
      <div class="olo-navmenu-bar" :class="alignmentClass">
        <template v-for="(item, idx) in previewItems" :key="idx">
          <span
            class="olo-navmenu-item"
            :class="{
              'olo-navmenu-item--button': item.isButton,
              'olo-navmenu-item--mega': item.isMega,
            }"
          >
            {{ item.label }}
            <span v-if="item.isMega" class="olo-navmenu-mega-arrow">&#9660;</span>
          </span>
        </template>
      </div>
      <div class="olo-navmenu-label">{{ selectedMenu.name }}</div>
    </template>
    <div v-else class="olo-navmenu-empty">
      Select a menu in Inspector
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const oloData = window.oloData || {};
const wpMenus = oloData.wpMenus || [];

const selectedMenu = computed(() => {
  const id = parseInt(props.settings.menu_id) || 0;
  if (!id) return null;
  return wpMenus.find(m => m.id === id) || null;
});

const alignmentClass = computed(() => {
  const a = props.settings.alignment || 'left';
  return 'olo-navmenu-align-' + a;
});

const previewItems = computed(() => {
  const count = 4;
  const items = [];
  const btnMode = props.settings.button_items || 'none';
  const megaMode = props.settings.mega_menu || 'none';

  for (let i = 0; i < count; i++) {
    const label = i === 0 && selectedMenu.value ? selectedMenu.value.name : 'Link ' + (i + 1);
    let isButton = false;
    let isMega = false;

    // Button logic
    if (btnMode === 'last' && i === count - 1) isButton = true;
    if (btnMode === 'last-2' && i >= count - 2) isButton = true;

    // Mega logic (items with "subs" = first 2 items for preview)
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
  gap: 8px;
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
  font-size: 13px;
  color: #d1d5db;
  padding: 4px 8px;
  border-radius: 4px;
  background: rgba(255, 255, 255, 0.05);
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.olo-navmenu-item--button {
  background: #7c3aed;
  color: #fff;
  font-weight: 600;
  font-size: 12px;
  padding: 3px 10px;
  border-radius: 4px;
}
.olo-navmenu-item--mega {
  border-bottom: 2px solid #3b82f6;
}
.olo-navmenu-mega-arrow {
  font-size: 8px;
  opacity: 0.6;
}
.olo-navmenu-label {
  font-size: 10px;
  color: #6b7280;
}
.olo-navmenu-empty {
  color: #6b7280;
  font-size: 13px;
  font-style: italic;
  padding: 8px 0;
}
</style>
