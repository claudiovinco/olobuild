<template>
  <div>
    <div
      class="mb-relative mb-overflow-hidden mb-rounded-lg mb-bg-gray-800"
      :style="{ paddingBottom: aspectPadding }"
    >
      <iframe
        v-if="embedUrl"
        :src="embedUrl"
        class="mb-absolute mb-inset-0 mb-w-full mb-h-full"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen
      ></iframe>
      <div
        v-else
        class="mb-absolute mb-inset-0 mb-flex mb-flex-col mb-items-center mb-justify-center mb-text-gray-500"
      >
        <span class="mb-text-4xl mb-mb-2">&#x1F3AC;</span>
        <span class="mb-text-sm">Enter a YouTube or Vimeo URL</span>
      </div>
    </div>
    <div
      v-if="settings.caption"
      class="mb-text-sm mb-text-gray-400 mb-text-center mb-mt-2"
      v-html="settings.caption"
    ></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const aspectPadding = computed(() => {
  const map = { '16:9': '56.25%', '4:3': '75%', '1:1': '100%' };
  return map[props.settings.aspect_ratio] || '56.25%';
});

const embedUrl = computed(() => {
  const url = props.settings.video_url || '';
  if (!url) return '';

  const params = [];
  if (props.settings.autoplay) params.push('autoplay=1');
  if (props.settings.muted) params.push('mute=1');
  const query = params.length ? '?' + params.join('&') : '';

  // YouTube
  const ytMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
  if (ytMatch) return `https://www.youtube.com/embed/${ytMatch[1]}${query}`;

  // Vimeo
  const vimeoMatch = url.match(/vimeo\.com\/(\d+)/);
  if (vimeoMatch) return `https://player.vimeo.com/video/${vimeoMatch[1]}${query}`;

  return '';
});
</script>
