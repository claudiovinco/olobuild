<template>
  <div class="olo-videoplaylist" :style="wrapperStyle">
    <!-- Player -->
    <div :style="playerStyle" class="olo-vp-player">
      <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#000;border-radius:6px;color:var(--olo-color-text-soft, #6b7280);flex-direction:column;gap:6px;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <polygon points="5,3 19,12 5,21" fill="currentColor" opacity="0.3" stroke="none"/>
          <polygon points="5,3 19,12 5,21"/>
        </svg>
        <span style="font-size:11px;">{{ activeVideo ? activeVideo.title : 'Seleziona un video' }}</span>
      </div>
    </div>
    <!-- Playlist -->
    <div :style="sidebarStyle" class="olo-vp-sidebar">
      <div
        v-for="(video, i) in videos"
        :key="video.id || i"
        :style="itemStyle(i)"
        class="olo-vp-item"
        @click="activeIndex = i"
      >
        <div style="display:flex;align-items:center;gap:8px;width:100%;">
          <!-- Thumbnail or number -->
          <div :style="thumbStyle(video)">
            <span v-if="!video.thumbnail" style="font-size:10px;font-weight:700;opacity:0.6;">{{ i + 1 }}</span>
            <img v-else :src="video.thumbnail" alt="" :style="thumbImgStyle" />
          </div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:12px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
              {{ video.title || 'Video ' + (i + 1) }}
            </div>
            <div v-if="s.show_duration && video.duration" style="font-size:10px;opacity:0.6;margin-top:1px;">
              {{ video.duration }}
            </div>
          </div>
          <!-- Play indicator -->
          <svg v-if="i === activeIndex" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0;opacity:0.8;">
            <polygon points="5,3 19,12 5,21"/>
          </svg>
        </div>
      </div>
      <div v-if="videos.length === 0" style="padding:16px;text-align:center;font-size:11px;opacity:0.5;">
        {{ t('Aggiungi video alla playlist') }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { t } from '@/i18n';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';
import { imageFrame } from '@/composables/useImageFrame';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  videos: [],
  layout: 'sidebar-right',
  player_height: '360',
  sidebar_width: '280',
  sidebar_bg: 'var(--olo-color-muted, #F3F4F6)',
  text_color: 'var(--olo-color-text, #374151)',
  active_color: '',
  show_duration: true,
  autoplay_next: false,
  // Cornice delle anteprime — stessi default del renderer PHP (48×32 = 3/2, cover).
  thumbnail_ratio: '3/2',
  thumbnail_ratio_custom: '16/9',
  thumbnail_fit: 'cover',
  thumbnail_object_position: 'center center',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const videos = computed(() => Array.isArray(s.value.videos) ? s.value.videos : []);
const activeIndex = ref(0);
const activeVideo = computed(() => videos.value[activeIndex.value] || null);

const playerHeight = computed(() => Math.max(200, Math.min(600, parseInt(s.value.player_height) || 360)));
const sidebarWidth = computed(() => Math.max(200, Math.min(400, parseInt(s.value.sidebar_width) || 280)));

const isBelow = computed(() => s.value.layout === 'below');
const isLeft = computed(() => s.value.layout === 'sidebar-left');

const wrapperStyle = computed(() => {
  if (isBelow.value) {
    return {
      display: 'flex',
      flexDirection: 'column',
      gap: '0',
      borderRadius: '8px',
      overflow: 'hidden',
    };
  }
  return {
    display: 'flex',
    flexDirection: isLeft.value ? 'row-reverse' : 'row',
    gap: '0',
    borderRadius: '8px',
    overflow: 'hidden',
  };
});

const playerStyle = computed(() => ({
  flex: '1',
  minWidth: '0',
  height: playerHeight.value + 'px',
}));

const sidebarStyle = computed(() => {
  const base = {
    background: s.value.sidebar_bg || 'var(--olo-color-muted, #F3F4F6)',
    color: s.value.text_color || 'var(--olo-color-text, #374151)',
    overflowY: 'auto',
  };
  if (isBelow.value) {
    base.width = '100%';
    base.maxHeight = '200px';
  } else {
    base.width = sidebarWidth.value + 'px';
    base.flexShrink = '0';
    base.height = playerHeight.value + 'px';
  }
  return base;
});

function itemStyle(index) {
  const isActive = index === activeIndex.value;
  return {
    padding: '8px 12px',
    cursor: 'pointer',
    borderLeft: isActive ? '3px solid ' + resolveColor(s.value.active_color, TOKENS.primary) : '3px solid transparent',
    background: isActive ? 'rgba(255,255,255,0.06)' : 'transparent',
    transition: 'background 0.15s, border-color 0.15s',
  };
}

// Cornice delle anteprime — gemella dell'helper PHP (image_frame).
// Nel canvas il riquadro e' più stretto del sito (40px invece di 48): e'
// un'anteprima compatta, ma la FORMA dev'essere quella vera, quindi l'altezza
// non e' più fissa e viene dalla proporzione scelta.
const thumbFrame = computed(() => imageFrame(s.value, 'thumbnail', { ratio: '3/2', fit: 'cover', pos: 'center center' }));

function thumbStyle(video) {
  return {
    width: '40px',
    // Senza proporzione valida si torna al riquadro storico, come fa il PHP.
    ...(thumbFrame.value.contenitore.aspectRatio ? thumbFrame.value.contenitore : { height: '28px' }),
    flexShrink: '0',
    background: video.thumbnail ? 'transparent' : 'rgba(255,255,255,0.08)',
    borderRadius: '3px',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    overflow: 'hidden',
  };
}

const thumbImgStyle = computed(() => ({
  width: '100%',
  height: '100%',
  ...thumbFrame.value.immagine,
  display: 'block',
  borderRadius: '3px',
}));
</script>

<style scoped>
.olo-vp-item:hover {
  background: rgba(255,255,255,0.04) !important;
}
</style>
