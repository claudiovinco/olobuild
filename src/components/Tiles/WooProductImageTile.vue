<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><circle cx="7.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><circle cx="13.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><polyline points="0 2 3.2 4 5.3 12.5 16 12.5 18 6.5 8 6.5"/></svg></span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else :style="wrapStyle">
      <!-- Main image -->
      <div :style="mainStyle">
        <svg :style="{ position: autoRatio ? 'static' : 'absolute', top: '50%', left: '50%', transform: autoRatio ? 'none' : 'translate(-50%,-50%)', width: '48px', height: '48px', opacity: 0.3 }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="3" y="3" width="18" height="18" rx="2" />
          <circle cx="8.5" cy="8.5" r="1.5" />
          <path d="M21 15l-5-5L5 21" />
        </svg>
      </div>
      <!-- Thumbnails -->
      <div v-if="s.show_gallery" :style="thumbsStyle">
        <div
          v-for="i in 4"
          :key="i"
          :style="thumbStyle(i === 1)"
        >
          <svg style="width:20px;height:20px;opacity:0.3;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2" />
            <circle cx="8.5" cy="8.5" r="1.5" />
            <path d="M21 15l-5-5L5 21" />
          </svg>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  show_gallery: true,
  gallery_position: 'bottom',
  lightbox: false,
  zoom_on_hover: true,
  image_ratio: '1-1',
  border_radius: '8',
  thumb_size: '64',
  thumb_gap: '8',
  thumb_border_radius: '4',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const wooActive = computed(() => true);

const ratioMap = {
  '1-1': '100%',
  '4-3': '75%',
  '3-4': '133.33%',
  '3-2': '66.66%',
  '16-9': '56.25%',
  'auto': '0',
};

const autoRatio = computed(() => s.value.image_ratio === 'auto');

const wrapStyle = computed(() => ({
  display: 'flex',
  flexDirection: s.value.gallery_position === 'left' ? 'row' : 'column',
  gap: parseInt(s.value.thumb_gap) + 'px',
}));

const mainStyle = computed(() => {
  const st = {
    position: 'relative',
    overflow: 'hidden',
    borderRadius: parseInt(s.value.border_radius) + 'px',
    // placeholder elegante: tinta neutra + icona faint; immagine reale userà object-fit:cover
    background: TOKENS.surfaceAlt,
    color: TOKENS.textFaint,
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
  };
  if (s.value.gallery_position === 'left') {
    st.flex = '1';
  }
  if (!autoRatio.value) {
    st.paddingTop = ratioMap[s.value.image_ratio] || '100%';
  } else {
    st.padding = '60px 20px';
  }
  return st;
});

const thumbsStyle = computed(() => ({
  display: 'flex',
  flexDirection: s.value.gallery_position === 'left' ? 'column' : 'row',
  gap: parseInt(s.value.thumb_gap) + 'px',
  flexWrap: 'wrap',
  ...(s.value.gallery_position === 'left' ? { width: parseInt(s.value.thumb_size) + 'px', order: '-1' } : {}),
}));

const thumbStyle = (active) => ({
  width: parseInt(s.value.thumb_size) + 'px',
  height: parseInt(s.value.thumb_size) + 'px',
  borderRadius: parseInt(s.value.thumb_border_radius) + 'px',
  overflow: 'hidden',
  background: TOKENS.surfaceAlt,
  color: TOKENS.textFaint,
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  border: active ? '2px solid var(--olo-color-primary, #e1474f)' : '2px solid transparent',
});
</script>

<style scoped>
.olo-woo-notice {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 16px 20px;
  background: color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);
  border: 1px solid var(--olo-color-warning, #b45309);
  border-radius: 8px;
  color: var(--olo-color-warning, #b45309);
  font-size: 14px;
  font-weight: 500;
}
.olo-woo-notice-icon {
  width: 20px;
  height: 20px;
  display: inline-flex;
  flex-shrink: 0;
}
.olo-woo-notice-icon :deep(svg) {
  width: 100%;
  height: 100%;
}
</style>
