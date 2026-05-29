<template>
  <div
    class="mb-rounded-lg mb-overflow-hidden mb-p-4"
    :style="{ background: s.bg_color || 'var(--olo-color-muted, #F3F4F6)', minHeight: '60px', border: '1px solid var(--olo-color-border, #E5E7EB)' }"
  >
    <div class="mb-flex mb-items-center mb-gap-3">
      <!-- Cover image or play button -->
      <div
        class="mb-flex-shrink-0 mb-rounded mb-overflow-hidden mb-flex mb-items-center mb-justify-center"
        :style="{
          width: '48px',
          height: '48px',
          background: s.cover_image ? 'transparent' : 'var(--olo-color-border, #E5E7EB)',
        }"
      >
        <img
          v-if="s.cover_image"
          :src="s.cover_image"
          alt=""
          class="mb-w-full mb-h-full mb-object-cover"
        />
        <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="none">
          <polygon points="8,5 8,19 19,12" :fill="s.text_color || 'var(--olo-color-text, #374151)'" />
        </svg>
      </div>

      <!-- Title / Artist / Waveform placeholder -->
      <div class="mb-flex-1 mb-min-w-0">
        <div
          v-if="s.title"
          class="mb-text-sm mb-font-semibold mb-truncate"
          :style="{ color: s.text_color || 'var(--olo-color-text, #374151)' }"
          data-olo-editable="title"
        >
          {{ s.title }}
        </div>
        <div
          v-if="s.artist"
          class="mb-text-xs mb-truncate"
          :style="{ color: (s.text_color || 'var(--olo-color-text, #374151)') + '99' }"
          data-olo-editable="artist"
        >
          {{ s.artist }}
        </div>

        <!-- Waveform placeholder -->
        <div class="mb-flex mb-items-end mb-gap-px mb-mt-1.5" style="height: 16px;">
          <div
            v-for="i in 20"
            :key="i"
            :style="{
              width: '3px',
              height: waveBarHeight(i) + 'px',
              background: accentColor,
              borderRadius: '1px',
              opacity: i <= 8 ? 1 : 0.4,
            }"
          ></div>
        </div>
      </div>

      <!-- Style label -->
      <div class="mb-text-xs mb-flex-shrink-0" :style="{ color: 'var(--olo-color-text-faint, #94a3b8)' }">
        {{ styleLabel }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  source_type: 'file',
  file_url: '',
  audio_url: '',
  autoplay: false,
  loop: false,
  muted: false,
  show_controls: true,
  player_style: 'default',
  accent_color: '',
  bg_color: 'var(--olo-color-muted, #F3F4F6)',
  text_color: 'var(--olo-color-text, #374151)',
  border_radius: '8',
  title: '',
  artist: '',
  cover_image: '',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const accentColor = computed(() => s.value.accent_color || 'var(--olo-color-primary, #e1474f)');

const styleLabels = { default: 'Standard', minimal: 'Minimale', custom: 'Custom' };
const styleLabel = computed(() => styleLabels[s.value.player_style] || 'Standard');

function waveBarHeight(i) {
  // Pseudo-random wave pattern
  const heights = [4, 8, 12, 6, 14, 10, 16, 8, 12, 6, 10, 14, 8, 12, 6, 10, 14, 8, 4, 6];
  return heights[(i - 1) % heights.length];
}
</script>
