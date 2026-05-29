<template>
  <ul class="olo-list-preview" :style="listStyle">
    <li
      v-for="(item, i) in parsedItems"
      :key="i"
      class="olo-list-item"
      :style="{ marginTop: i > 0 ? spacing + 'px' : '0', gap: iconGap + 'px' }"
    >
      <!-- Number icon -->
      <span v-if="resolveIcon(item) === 'number'" class="olo-list-icon-num" :style="{ color: s.icon_color || 'var(--olo-color-success, #15803d)', fontSize: iconSize + 'px', minWidth: iconSize + 'px' }">{{ i + 1 }}.</span>
      <!-- SVG icon -->
      <span v-else class="olo-list-icon-svg" v-html="getIcon(resolveIcon(item))"></span>
      <span class="olo-list-text" :data-olo-editable="'items.' + i + '.text'">{{ item.text }}</span>
      <span v-if="item.link" class="olo-list-link-badge" :title="t('Link')">{{ t('&#x1F517;') }}</span>
    </li>
  </ul>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  items: [
    { text: 'Funzionalit\u00e0 uno', icon: 'check' },
    { text: 'Funzionalit\u00e0 due', icon: 'check' },
    { text: 'Funzionalit\u00e0 tre', icon: 'check' },
  ],
  icon_default: 'check',
  icon_color: '',
  text_color: 'var(--olo-color-text, #e5e7eb)',
  spacing: '12',
  icon_size: '18',
  icon_gap: '10',
  padding: '16',
  shadow: 'none',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const spacing = computed(() => parseInt(s.value.spacing) || 12);
const iconSize = computed(() => parseInt(s.value.icon_size) || 18);
const iconGap = computed(() => parseInt(s.value.icon_gap) || 10);

const listStyle = computed(() => {
  const style = {};
  style.padding = (parseInt(s.value.padding) || 16) + 'px';
  if (s.value.text_color) style.color = s.value.text_color;
  const shadowMap = {
    sm: '0 1px 3px 0 rgba(0,0,0,0.3), 0 1px 2px -1px rgba(0,0,0,0.25)',
    md: '0 4px 6px -1px rgba(0,0,0,0.35), 0 2px 4px -2px rgba(0,0,0,0.25)',
    lg: '0 10px 15px -3px rgba(0,0,0,0.4), 0 4px 6px -4px rgba(0,0,0,0.25)',
    xl: '0 20px 25px -5px rgba(0,0,0,0.45), 0 8px 10px -6px rgba(0,0,0,0.3)',
  };
  const sh = s.value.shadow || 'none';
  if (shadowMap[sh]) {
    style.boxShadow = shadowMap[sh];
  } else if (sh === 'custom') {
    const h = parseInt(s.value.shadow_h) || 0;
    const v = parseInt(s.value.shadow_v) || 4;
    const bl = parseInt(s.value.shadow_blur) || 10;
    const sp = parseInt(s.value.shadow_spread) || 0;
    const sc = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const ins = s.value.shadow_inset ? 'inset ' : '';
    style.boxShadow = `${ins}${h}px ${v}px ${bl}px ${sp}px ${sc}`;
  }
  return style;
});

// Support both new array format and legacy textarea string
const parsedItems = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw)) {
    return raw.filter(i => i && i.text);
  }
  // Legacy: textarea "icon|text\nicon|text"
  const defaultIcon = s.value.icon_default;
  return String(raw).split('\n').map(l => l.trim()).filter(Boolean).map(line => {
    const parts = line.split('|');
    if (parts.length >= 2) return { icon: parts[0].trim(), text: parts.slice(1).join('|').trim() };
    return { icon: defaultIcon, text: parts[0].trim() };
  });
});

function resolveIcon(item) {
  return item.icon || s.value.icon_default || 'check';
}

function getIcon(icon) {
  const c = s.value.icon_color || 'var(--olo-color-success, #15803d)';
  const sz = iconSize.value;
  const icons = {
    check:   `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="${c}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    arrow:   `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24" fill="none"><path d="M5 12h14m-6-6l6 6-6 6" stroke="${c}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    star:    `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24" fill="${c}"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01z"/></svg>`,
    dot:     `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5" fill="${c}"/></svg>`,
    x:       `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6l12 12" stroke="${c}" stroke-width="2.5" stroke-linecap="round"/></svg>`,
    heart:   `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24" fill="${c}"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>`,
    bolt:    `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24" fill="${c}"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>`,
    info:    `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="${c}" stroke-width="2"/><path d="M12 16v-4m0-4h.01" stroke="${c}" stroke-width="2" stroke-linecap="round"/></svg>`,
    warning: `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="${c}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
  };
  return icons[icon] || icons.check;
}
</script>

<style scoped>
.olo-list-preview {
  list-style: none;
  margin: 0;
}
.olo-list-item {
  display: flex;
  align-items: flex-start;
}
.olo-list-icon-num {
  flex-shrink: 0;
  font-weight: 700;
  line-height: normal;
  text-align: center;
}
.olo-list-icon-svg {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  line-height: 1;
}
.olo-list-text {
  line-height: 1.5;
}
.olo-list-link-badge {
  flex-shrink: 0;
  font-size: 12px;
  opacity: 0.5;
  margin-left: auto;
}
</style>
