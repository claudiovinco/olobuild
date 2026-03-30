<template>
  <div class="mb-relative mb-overflow-hidden" :style="{ borderRadius: brStyle }">
    <img
      v-if="s.image_url"
      :src="s.image_url"
      :alt="s.alt_text"
      class="mb-w-full mb-block olo-img-anim"
      :class="s.hover_animation !== 'none' ? 'olo-img-' + s.hover_animation : ''"
      :style="{
        height: s.height,
        objectFit: s.object_fit,
        filter: filterStyle || undefined,
        borderRadius: brStyle,
        transition: 'filter 0.4s ease, transform 0.4s ease',
      }"
    />
    <div
      v-else
      class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-bg-gray-800 mb-text-gray-500"
      :style="{ height: s.height, borderRadius: brStyle }"
    >
      <span class="mb-text-4xl mb-mb-2">&#x1F5BC;</span>
      <span class="mb-text-sm">Click to add image</span>
    </div>
    <!-- Hover indicator -->
    <div
      v-if="s.hover_image || s.hover_video"
      class="mb-absolute mb-top-1 mb-right-1 mb-bg-black/60 mb-text-white mb-text-xs mb-px-1.5 mb-py-0.5 mb-rounded"
      style="z-index:2"
    >{{ s.hover_video ? '▶ hover' : '⇄ hover' }}</div>
    <div v-if="s.lightbox" class="mb-absolute mb-bottom-1 mb-right-1 mb-bg-black/60 mb-text-white mb-text-xs mb-px-1.5 mb-py-0.5 mb-rounded" style="z-index:2">&#x1F50D; lightbox</div>
    <div
      v-if="s.caption"
      class="mb-text-sm mb-text-gray-400 mb-text-center mb-mt-2"
    data-olo-editable="caption">{{ s.caption }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  image_url: '',
  hover_image: '',
  hover_video: '',
  alt_text: '',
  caption: '',
  link_url: '',
  link_target: '_self',
  object_fit: 'cover',
  height: '300px',
  filter_blur: '0',
  filter_brightness: '100',
  filter_contrast: '100',
  filter_saturate: '100',
  filter_grayscale: '0',
  filter_sepia: '0',
  hover_animation: 'none',
  lightbox: false,
  border_radius: '0',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const brStyle = computed(() => {
  const v = s.value.border_radius;
  if (!v || v === '0' || v === 0) return undefined;
  if (v && typeof v === 'object') {
    return `${v.tl || 0}px ${v.tr || 0}px ${v.br || 0}px ${v.bl || 0}px`;
  }
  const n = parseInt(String(v)) || 0;
  return n > 0 ? `${n}px` : undefined;
});

const filterStyle = computed(() => {
  const parts = [];
  const blur = parseInt(s.value.filter_blur) || 0;
  const brightness = parseInt(s.value.filter_brightness);
  const contrast = parseInt(s.value.filter_contrast);
  const saturate = parseInt(s.value.filter_saturate);
  const grayscale = parseInt(s.value.filter_grayscale) || 0;
  const sepia = parseInt(s.value.filter_sepia) || 0;
  if (blur > 0) parts.push(`blur(${blur}px)`);
  if (brightness !== 100 && !isNaN(brightness)) parts.push(`brightness(${brightness}%)`);
  if (contrast !== 100 && !isNaN(contrast)) parts.push(`contrast(${contrast}%)`);
  if (saturate !== 100 && !isNaN(saturate)) parts.push(`saturate(${saturate}%)`);
  if (grayscale > 0) parts.push(`grayscale(${grayscale}%)`);
  if (sepia > 0) parts.push(`sepia(${sepia}%)`);
  return parts.length ? parts.join(' ') : '';
});
</script>

<style scoped>
.olo-img-anim { overflow: hidden; }
.olo-img-zoom-in:hover { transform: scale(1.08); }
.olo-img-zoom-out { transform: scale(1.05); }
.olo-img-zoom-out:hover { transform: scale(1); }
.olo-img-slide-up:hover { transform: translateY(-5px); }
.olo-img-rotate-cw:hover { transform: rotate(2deg) scale(1.02); }
.olo-img-rotate-ccw:hover { transform: rotate(-2deg) scale(1.02); }
.olo-img-blur-in { filter: blur(3px) !important; }
.olo-img-blur-in:hover { filter: blur(0) !important; }
</style>
