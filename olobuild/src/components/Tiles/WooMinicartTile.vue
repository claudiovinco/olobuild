<template>
  <div style="display:inline-flex;align-items:center;gap:10px;padding:8px;">
    <!-- Icon -->
    <div v-if="s.style !== 'text'" style="position:relative;display:inline-flex;">
      <svg
        :width="iconSize"
        :height="iconSize"
        viewBox="0 0 24 24"
        fill="none"
        :stroke="s.icon_color || 'var(--olo-color-text, #374151)'"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <template v-if="s.icon === 'bag'">
          <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/>
          <path d="M16 10a4 4 0 01-8 0"/>
        </template>
        <template v-else-if="s.icon === 'basket'">
          <path d="M5.757 1.071a.5.5 0 01.172.686L3.383 6h9.234L10.07 1.757a.5.5 0 11.858-.514L13.783 6H15.5a.5.5 0 01.5.5v1a.5.5 0 01-.5.5H.5a.5.5 0 01-.5-.5v-1A.5.5 0 01.5 6h1.717L5.07 1.243a.5.5 0 01.686-.172z"/>
          <path d="M2 10l1.5 10h9L14 10"/>
        </template>
        <template v-else>
          <circle cx="9" cy="21" r="1"/>
          <circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
        </template>
      </svg>
      <!-- Badge count -->
      <div v-if="s.show_count" :style="countBadgeStyle">3</div>
    </div>
    <!-- Text -->
    <span
      v-if="s.style !== 'icon'"
      :style="{ color: s.text_color || 'var(--olo-color-text, #374151)', fontSize: '14px', fontWeight: 600 }"
    >99,00</span>
    <!-- Dropdown indicator -->
    <div v-if="s.dropdown" style="font-size:10px;color:#9CA3AF;">&#9660;</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  style: 'icon-text',
  icon: 'cart',
  show_count: true,
  show_total: true,
  dropdown: true,
  icon_size: '24',
  text_color: 'var(--olo-color-text, #374151)',
  icon_color: 'var(--olo-color-text, #374151)',
  badge_bg: '#EF4444',
  badge_color: '#FFFFFF',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const iconSize = computed(() => (parseInt(s.value.icon_size) || 24) + 'px');

const countBadgeStyle = computed(() => ({
  position: 'absolute',
  top: '-6px',
  right: '-8px',
  minWidth: '18px',
  height: '18px',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  background: s.value.badge_bg || '#EF4444',
  color: s.value.badge_color || '#fff',
  fontSize: '10px',
  fontWeight: 700,
  borderRadius: '9px',
  padding: '0 4px',
  lineHeight: 1,
}));
</script>
