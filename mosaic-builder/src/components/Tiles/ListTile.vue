<template>
  <ul class="mb-list-none mb-m-0 mb-p-4" :style="{ color: settings.text_color }">
    <li
      v-for="(item, i) in parsedItems"
      :key="i"
      class="mb-flex mb-items-start mb-gap-2.5"
      :style="{ marginTop: i > 0 ? spacing + 'px' : '0' }"
    >
      <!-- Number icon -->
      <span v-if="item.icon === 'number'" class="mb-flex-shrink-0 mb-font-bold mb-leading-normal" :style="{ color: settings.icon_color, fontSize: iconSize + 'px', minWidth: iconSize + 'px', textAlign: 'center' }">{{ i + 1 }}.</span>
      <!-- SVG icon -->
      <span v-else class="mb-flex-shrink-0 mb-flex mb-items-center" style="line-height:1;" v-html="getIcon(item.icon)"></span>
      <span class="mb-leading-relaxed">{{ item.text }}</span>
    </li>
  </ul>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const spacing = computed(() => parseInt(props.settings.spacing) || 12);
const iconSize = computed(() => parseInt(props.settings.icon_size) || 18);

const parsedItems = computed(() => {
  const text = props.settings.items || '';
  const defaultIcon = props.settings.icon_default || 'check';
  return text.split('\n').map(line => line.trim()).filter(Boolean).map(line => {
    const parts = line.split('|');
    if (parts.length >= 2) return { icon: parts[0].trim(), text: parts.slice(1).join('|').trim() };
    return { icon: defaultIcon, text: parts[0].trim() };
  });
});

function getIcon(icon) {
  const c = props.settings.icon_color || '#22C55E';
  const s = iconSize.value;
  const icons = {
    check: `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="${c}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    arrow: `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="none"><path d="M5 12h14m-6-6l6 6-6 6" stroke="${c}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
    star: `<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="${c}"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01z"/></svg>`,
    dot: `<svg width="${s}" height="${s}" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5" fill="${c}"/></svg>`,
  };
  return icons[icon] || icons.check;
}
</script>
