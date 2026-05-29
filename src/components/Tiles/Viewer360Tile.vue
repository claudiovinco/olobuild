<template>
  <div>
    <div v-if="hasSrc" :style="containerStyle" class="mb-relative mb-overflow-hidden mb-bg-gray-900 mb-flex mb-items-center mb-justify-center">
      <img v-if="s.source_type === 'image'" :src="s.image_url" class="mb-w-full mb-h-full mb-object-cover" style="filter:brightness(0.85)" />
      <div v-else class="mb-text-white mb-text-sm">{{ t('Video 360° preview') }}</div>
      <div class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center">
        <div class="mb-bg-black/50 mb-rounded-full mb-w-14 mb-h-14 mb-flex mb-items-center mb-justify-center mb-text-white">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="9" />
            <ellipse cx="12" cy="12" rx="4" ry="9" />
            <path d="M3 12h18" />
          </svg>
        </div>
      </div>
      <div v-if="s.autorotate" class="mb-absolute mb-bottom-2 mb-left-2 mb-bg-black/60 mb-text-white mb-text-[10px] mb-px-2 mb-py-0.5 mb-rounded">Autorotazione {{ s.autorotate_speed }}°/s</div>
    </div>
    <div v-else class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-py-10 mb-px-6 mb-text-center mb-rounded-lg" style="min-height:120px" :style="{ background: 'var(--olo-color-surface-alt, #f6f7f9)', color: 'var(--olo-color-text-faint, #94a3b8)' }">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-mb-2">
        <circle cx="12" cy="12" r="9" />
        <ellipse cx="12" cy="12" rx="4" ry="9" />
        <path d="M3 12h18" />
      </svg>
      <span class="mb-text-xs">{{ t('Inserisci una foto o video 360°') }}</span>
    </div>
    <p v-if="s.caption" class="mb-text-center mb-text-xs mb-mt-1" :style="{ color: 'var(--olo-color-text-soft, #6b7280)' }">{{ s.caption }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  source_type: 'image', image_url: '', video_url: '', height: '400',
  border_radius: 0, autorotate: true, autorotate_speed: '1', caption: '',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const hasSrc = computed(() => {
  return s.value.source_type === 'video' ? !!s.value.video_url : !!s.value.image_url;
});

const containerStyle = computed(() => ({
  height: (s.value.height || 400) + 'px',
  borderRadius: (s.value.border_radius || 0) + 'px',
}));
</script>
