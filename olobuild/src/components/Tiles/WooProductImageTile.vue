<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon">&#x1F6D2;</span>
      <span>WooCommerce richiesto</span>
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
    background: '#F3F4F6',
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
  background: '#F3F4F6',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  border: active ? '2px solid var(--olo-color-primary, #6366F1)' : '2px solid transparent',
});
</script>

<style scoped>
.olo-woo-notice {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 16px 20px;
  background: #FEF3C7;
  border: 1px solid #F59E0B;
  border-radius: 8px;
  color: #92400E;
  font-size: 14px;
  font-weight: 500;
}
.olo-woo-notice-icon {
  font-size: 20px;
}
</style>
