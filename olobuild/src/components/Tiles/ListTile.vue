<template>
  <ul class="mb-list-none mb-m-0 mb-p-4" :style="{ color: s.text_color }">
    <li
      v-for="(item, i) in parsedItems"
      :key="item.text + '-' + i"
      class="mb-flex mb-items-start mb-gap-2.5"
      :style="{ marginTop: i > 0 ? spacing + 'px' : '0' }"
    >
      <!-- Number icon -->
      <span v-if="item.icon === 'number'" class="mb-flex-shrink-0 mb-font-bold mb-leading-normal" :style="{ color: s.icon_color, fontSize: iconSize + 'px', minWidth: iconSize + 'px', textAlign: 'center' }">{{ i + 1 }}.</span>
      <!-- SVG icon -->
      <span v-else class="mb-flex-shrink-0 mb-flex mb-items-center" style="line-height:1;" v-html="getIcon(item.icon)"></span>
      <span class="mb-leading-relaxed" :data-olo-editable="'items.' + i + '.text'">{{ item.text }}</span>
    </li>
  </ul>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  items: 'check|Funzionalit\u00e0 uno\ncheck|Funzionalit\u00e0 due\ncheck|Funzionalit\u00e0 tre',
  icon_default: 'check',
  icon_color: '#22C55E',
  text_color: 'var(--olo-color-text, #374151)',
  spacing: '12',
  icon_size: '18',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const spacing = computed(() => parseInt(s.value.spacing) || 12);
const iconSize = computed(() => parseInt(s.value.icon_size) || 18);

const parsedItems = computed(() => {
  const text = s.value.items;
  const defaultIcon = s.value.icon_default;
  return text.split('\n').map(line => line.trim()).filter(Boolean).map(line => {
    const parts = line.split('|');
    if (parts.length >= 2) return { icon: parts[0].trim(), text: parts.slice(1).join('|').trim() };
    return { icon: defaultIcon, text: parts[0].trim() };
  });
});

function getIcon(icon) {
  const c = s.value.icon_color;
  const sz = iconSize.value;
  const icons = {
    check: `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="${c}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    arrow: `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24" fill="none"><path d="M5 12h14m-6-6l6 6-6 6" stroke="${c}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    star: `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24" fill="${c}"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01z"/></svg>`,
    dot: `<svg width="${sz}" height="${sz}" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5" fill="${c}"/></svg>`,
  };
  return icons[icon] || icons.check;
}
</script>
