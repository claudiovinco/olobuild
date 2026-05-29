<template>
  <div class="mb-py-4 mb-px-4" :style="{ textAlign: s.alignment }">
    <div v-if="s.title" class="mb-font-semibold mb-mb-2" :style="{ color: s.title_color || 'var(--olo-color-text, #374151)', fontSize: '16px' }" data-olo-editable="title">{{ s.title }}</div>
    <div class="olo-stars" :style="{ display: 'inline-flex', gap: '4px' }">
      <svg v-for="i in maxStars" :key="i" :width="starSize" :height="starSize" viewBox="0 0 24 24"
        :style="{ color: i <= Math.floor(rating) ? starColor : emptyColor }">
        <!-- Full star -->
        <path v-if="i <= Math.floor(rating)" :d="starPath" fill="currentColor" :stroke="isOutline ? 'currentColor' : 'none'" :stroke-width="isOutline ? 1.5 : 0" />
        <!-- Half star -->
        <template v-else-if="i === Math.ceil(rating) && rating % 1 !== 0">
          <defs><clipPath :id="'half-' + i"><rect x="0" y="0" width="12" height="24"/></clipPath></defs>
          <path :d="starPath" :fill="emptyColor" :stroke="isOutline ? emptyColor : 'none'" :stroke-width="isOutline ? 1.5 : 0" />
          <path :d="starPath" :fill="starColor" :clip-path="'url(#half-' + i + ')'" />
        </template>
        <!-- Empty star -->
        <path v-else :d="starPath" :fill="isOutline ? 'none' : 'currentColor'" :stroke="isOutline ? 'currentColor' : 'none'" :stroke-width="isOutline ? 1.5 : 0" />
      </svg>
    </div>
    <div class="mb-mt-1" :style="{ fontSize: '13px', color: starColor, fontWeight: '600' }">{{ rating }} / {{ maxStars }}</div>
    <div v-if="s.subtitle" class="mb-mt-1" :style="{ color: subtitleColor, fontSize: '13px' }" data-olo-editable="subtitle">{{ s.subtitle }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  rating: '4',
  max_stars: '5',
  star_size: '32',
  star_color: '',
  empty_color: '',
  style: 'filled',
  title: '',
  subtitle: '',
  title_color: 'var(--olo-color-text, #374151)',
  subtitle_color: '',
  alignment: 'center',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const rating = computed(() => parseFloat(s.value.rating) || 0);
const maxStars = computed(() => parseInt(s.value.max_stars) || 5);
const starSize = computed(() => parseInt(s.value.star_size) || 32);
const isOutline = computed(() => s.value.style === 'outline');

// Colori token-first: stelle piene = accento ambra brand (era #FBBF24),
// stelle vuote = border neutro, sottotitolo = grigio tenue.
const starColor = computed(() => resolveColor(s.value.star_color, TOKENS.accent));
const emptyColor = computed(() => resolveColor(s.value.empty_color, TOKENS.border));
const subtitleColor = computed(() => resolveColor(s.value.subtitle_color, TOKENS.textFaint));

const starPath = 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z';
</script>
