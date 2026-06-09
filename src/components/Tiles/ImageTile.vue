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
    <!-- media_bg: sfondo/media di ogni tipo (se senza immagine) -->
    <div
      v-else-if="hasMediaBg"
      class="mb-w-full mb-block"
      :style="{ height: aspectSet ? undefined : s.height, aspectRatio: aspectSet ? aspectRatioCss : undefined, borderRadius: brStyle, ...mediaBgStyle }"
    ></div>
    <!-- placeholder a righe + etichetta -->
    <div
      v-else-if="s.media_label"
      class="mb-relative mb-overflow-hidden mb-w-full mb-block"
      :style="{ height: aspectSet ? undefined : s.height, aspectRatio: aspectSet ? aspectRatioCss : undefined, borderRadius: brStyle, background: 'var(--olo-color-muted,#2b2b2b)', backgroundImage: 'repeating-linear-gradient(135deg,rgba(255,255,255,.05) 0 16px,transparent 16px 32px)' }"
    >
      <span class="mb-absolute" style="left:14px;bottom:12px;right:14px;font-size:10.5px;letter-spacing:.1em;color:rgba(255,255,255,.4);text-transform:uppercase">{{ s.media_label }}</span>
    </div>
    <!-- empty state -->
    <div
      v-else
      class="mb-flex mb-flex-col mb-items-center mb-justify-center"
      :style="{ height: s.height, borderRadius: brStyle, background: 'var(--olo-color-surface-alt, #f6f7f9)', color: 'var(--olo-color-text-faint, #94a3b8)', aspectRatio: s.height ? undefined : '16/9' }"
    >
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-mb-2">
        <rect x="3" y="3" width="18" height="18" rx="2" />
        <circle cx="8.5" cy="8.5" r="1.5" />
        <path d="m21 15-5-5L5 21" />
      </svg>
      <span class="mb-text-sm">{{ t('Click to add image') }}</span>
    </div>
    <!-- Hover indicator -->
    <div
      v-if="s.hover_image || s.hover_video"
      class="mb-absolute mb-top-1 mb-right-1 mb-bg-black/60 mb-text-white mb-text-xs mb-px-1.5 mb-py-0.5 mb-rounded mb-inline-flex mb-items-center mb-gap-1"
      style="z-index:2"
    >
      <svg v-if="s.hover_video" width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><polygon points="6,4 20,12 6,20" /></svg>
      <svg v-else width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
      <span>hover</span>
    </div>
    <div v-if="s.lightbox" class="mb-absolute mb-bottom-1 mb-right-1 mb-bg-black/60 mb-text-white mb-text-xs mb-px-1.5 mb-py-0.5 mb-rounded mb-inline-flex mb-items-center mb-gap-1" style="z-index:2">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <span>{{ t('lightbox') }}</span>
    </div>
    <div
      v-if="s.caption"
      class="mb-text-sm mb-text-center mb-mt-2"
      :style="{ color: 'var(--olo-color-text-soft, #6b7280)' }"
    data-olo-editable="caption">{{ s.caption }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

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
  media_bg: { type: 'none' },
  media_label: '',
  aspect_ratio: 'auto',
  aspect_ratio_custom: '16/9',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const aspectSet = computed(() => s.value.aspect_ratio && s.value.aspect_ratio !== 'auto');
const aspectRatioCss = computed(() => {
  if (s.value.aspect_ratio === 'custom') return (s.value.aspect_ratio_custom || '16/9').replace(':', '/');
  return aspectSet.value ? s.value.aspect_ratio : '16/9';
});
const hasMediaBg = computed(() => {
  const b = s.value.media_bg;
  return !!(b && b.type && b.type !== 'none');
});
const mediaBgStyle = computed(() => (hasMediaBg.value ? buildBgStyle(s.value.media_bg) : {}));

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
