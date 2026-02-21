<template>
  <div
    class="mb-flex mb-flex-wrap mb-py-4 mb-px-4"
    :style="{ gap: (settings.gap || 12) + 'px', justifyContent: justifyMap[settings.alignment] || 'center', minHeight: '60px' }"
  >
    <div
      v-for="(link, i) in links"
      :key="i"
      class="mb-flex mb-items-center mb-justify-center mb-rounded-full mb-text-white"
      :style="{
        width: iconSize + 'px',
        height: iconSize + 'px',
        background: platformColors[link.platform] || '#6366F1',
        fontSize: Math.round(iconSize * 0.5) + 'px'
      }"
      :title="link.platform"
    >{{ platformIcons[link.platform] || '\uD83D\uDD17' }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const justifyMap = { left: 'flex-start', center: 'center', right: 'flex-end' };

const platformIcons = {
  facebook: '\uD83C\uDDEB', twitter: '\uD83D\uDC26', instagram: '\uD83D\uDCF7',
  linkedin: '\uD83D\uDCBC', youtube: '\u25B6\uFE0F', tiktok: '\uD83C\uDFB5',
  github: '\uD83D\uDCBB', email: '\u2709\uFE0F', website: '\uD83C\uDF10',
};

const platformColors = {
  facebook: '#1877F2', twitter: '#1DA1F2', instagram: '#E4405F',
  linkedin: '#0A66C2', youtube: '#FF0000', tiktok: '#000000',
  github: '#333333', email: '#6366F1', website: '#6366F1',
};

const iconSize = computed(() => parseInt(props.settings.size) || 32);

const links = computed(() => {
  const text = props.settings.links || '';
  return text.split('\n').map(line => {
    const parts = line.split('|').map(s => s.trim());
    if (parts.length === 2) return { platform: parts[0].toLowerCase(), url: parts[1] };
    return null;
  }).filter(Boolean);
});
</script>
