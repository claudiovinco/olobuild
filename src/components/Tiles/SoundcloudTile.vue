<template>
  <div class="olo-soundcloud" :style="wrapperStyle">
    <!-- URL impostato: mostra preview -->
    <div v-if="s.url" class="mb-rounded-lg mb-overflow-hidden" :style="{ borderRadius: s.border_radius + 'px' }">
      <div class="mb-flex mb-items-center mb-gap-3 mb-p-4" :style="previewStyle">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;">
          <rect width="24" height="24" rx="4" fill="#ff5500"/>
          <path d="M4 15.5v-3c0-.28.22-.5.5-.5s.5.22.5.5v3c0 .28-.22.5-.5.5s-.5-.22-.5-.5zm2 1v-5c0-.28.22-.5.5-.5s.5.22.5.5v5c0 .28-.22.5-.5.5s-.5-.22-.5-.5zm2 .5v-7c0-.28.22-.5.5-.5s.5.22.5.5v7c0 .28-.22.5-.5.5s-.5-.22-.5-.5zm2-.5v-5.5c0-.28.22-.5.5-.5s.5.22.5.5v5.5c0 .28-.22.5-.5.5s-.5-.22-.5-.5zm2 .5v-7.5c0-.28.22-.5.5-.5s.5.22.5.5v7.5c0 .28-.22.5-.5.5s-.5-.22-.5-.5zm2-1v-5c0-.28.22-.5.5-.5s.5.22.5.5v5c0 .28-.22.5-.5.5s-.5-.22-.5-.5zm2.5 1.5c-1.1 0-2-.5-2.5-1v-6.5c.5-.5 1.4-1 2.5-1 1.93 0 3.5 1.57 3.5 3.5v1.5c0 1.93-1.57 3.5-3.5 3.5z" fill="#fff"/>
        </svg>
        <div class="mb-flex-1 mb-min-w-0">
          <div class="mb-text-sm mb-font-semibold mb-text-white mb-truncate">{{ t('SoundCloud') }}</div>
          <div class="mb-text-xs mb-text-white mb-truncate" style="opacity:.7">{{ s.url }}</div>
        </div>
        <svg width="28" height="28" viewBox="0 0 24 24" fill="white" style="opacity:.8; flex-shrink:0;">
          <path d="M8 5v14l11-7z"/>
        </svg>
      </div>
      <div v-if="s.visual" class="mb-bg-gray-800" :style="{ height: (parseInt(s.height) || 166) + 'px', opacity: .3 }"></div>
    </div>

    <!-- Nessun URL: placeholder -->
    <div v-else class="mb-rounded-lg mb-overflow-hidden mb-text-center mb-py-10 mb-px-6" :style="placeholderStyle">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="mb-mx-auto mb-mb-3">
        <rect width="24" height="24" rx="4" fill="#ff5500" opacity=".2"/>
        <path d="M4 15.5v-3c0-.28.22-.5.5-.5s.5.22.5.5v3c0 .28-.22.5-.5.5s-.5-.22-.5-.5zm2 1v-5c0-.28.22-.5.5-.5s.5.22.5.5v5c0 .28-.22.5-.5.5s-.5-.22-.5-.5zm2 .5v-7c0-.28.22-.5.5-.5s.5.22.5.5v7c0 .28-.22.5-.5.5s-.5-.22-.5-.5zm2-.5v-5.5c0-.28.22-.5.5-.5s.5.22.5.5v5.5c0 .28-.22.5-.5.5s-.5-.22-.5-.5zm2 .5v-7.5c0-.28.22-.5.5-.5s.5.22.5.5v7.5c0 .28-.22.5-.5.5s-.5-.22-.5-.5zm2-1v-5c0-.28.22-.5.5-.5s.5.22.5.5v5c0 .28-.22.5-.5.5s-.5-.22-.5-.5zm2.5 1.5c-1.1 0-2-.5-2.5-1v-6.5c.5-.5 1.4-1 2.5-1 1.93 0 3.5 1.57 3.5 3.5v1.5c0 1.93-1.57 3.5-3.5 3.5z" fill="#ff5500" opacity=".5"/>
      </svg>
      <div class="mb-text-sm" style="color:#ff5500; opacity:.8;">{{ t('Inserisci URL SoundCloud') }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  url: '',
  auto_play: false,
  show_artwork: true,
  show_user: true,
  color: '#ff5500',
  visual: true,
  height: '166',
  alignment: 'center',
  border_radius: '8',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const alignMap = { left: 'flex-start', center: 'center', right: 'flex-end' };

const wrapperStyle = computed(() => ({
  display: 'flex',
  justifyContent: alignMap[s.value.alignment] || 'center',
}));

const previewStyle = computed(() => ({
  background: 'linear-gradient(135deg, #ff5500 0%, #ff8800 100%)',
  borderRadius: s.value.border_radius + 'px ' + s.value.border_radius + 'px 0 0',
}));

const placeholderStyle = computed(() => ({
  background: 'linear-gradient(135deg, rgba(255,85,0,0.08) 0%, rgba(255,136,0,0.08) 100%)',
  border: '2px dashed rgba(255,85,0,0.3)',
  borderRadius: s.value.border_radius + 'px',
}));
</script>
