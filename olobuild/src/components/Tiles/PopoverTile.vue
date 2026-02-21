<template>
  <div class="olo-popover" style="position:relative;">
    <!-- Image -->
    <div
      v-if="settings.image"
      class="olo-popover-img"
      :style="imgStyle"
    ></div>
    <div
      v-else
      class="olo-popover-placeholder mb-bg-gray-700 mb-flex mb-items-center mb-justify-center"
      :style="placeholderStyle"
    >
      <span class="mb-text-gray-500 mb-text-sm" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);">Image Placeholder</span>
    </div>

    <!-- Markers -->
    <div
      v-for="(marker, i) in markers"
      :key="marker.id || i"
      class="olo-popover-marker"
      :style="{
        left: marker.x + '%',
        top: marker.y + '%',
        background: settings.marker_color || '#6366F1',
      }"
      :title="marker.title"
      @click="activeMarker = activeMarker === i ? -1 : i"
    >
      <span class="olo-popover-marker-pulse" :style="{ borderColor: settings.marker_color || '#6366F1' }"></span>
      <!-- Mini popup preview -->
      <div v-if="activeMarker === i" class="pop-preview" :style="popStyle" @click.stop>
        <div v-if="marker.image" class="pop-preview__media" :style="{ height: popImgH + 'px' }">
          <img :src="marker.image" alt="" class="pop-preview__img" :class="hoverClass" />
          <div v-if="settings.popup_hover_effect === 'color-overlay'" class="pop-preview__overlay" :style="{ background: settings.popup_hover_color || '#6366F1' }"></div>
        </div>
        <div class="pop-preview__body">
          <div class="pop-preview__title">{{ marker.title }}</div>
          <div class="pop-preview__text">{{ (marker.content || '').substring(0, 60) }}…</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const activeMarker = ref(-1);

const markers = computed(() => {
  const raw = props.settings.markers;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { id: 'mk-1', x: 25, y: 30, title: 'Point 1', content: 'Description...', image: '' },
    { id: 'mk-2', x: 70, y: 60, title: 'Point 2', content: 'Description...', image: '' },
  ];
});

const imgHeight = computed(() => parseInt(props.settings.image_height) || 0);

const imgStyle = computed(() => {
  const s = { background: `url(${props.settings.image}) center/cover no-repeat`, width: '100%' };
  if (imgHeight.value > 0) {
    s.height = Math.min(imgHeight.value, 300) + 'px';
  } else {
    s.paddingBottom = '56.25%';
  }
  return s;
});

const placeholderStyle = computed(() => {
  const s = { position: 'relative', borderRadius: '6px' };
  if (imgHeight.value > 0) {
    s.height = Math.min(imgHeight.value, 300) + 'px';
  } else {
    s.paddingBottom = '56.25%';
  }
  return s;
});

const popStyle = computed(() => ({
  background: props.settings.popup_bg || '#ffffff',
  color: props.settings.popup_color || '#333333',
  borderRadius: (parseInt(props.settings.popup_radius) || 8) + 'px',
}));

const popImgH = computed(() => {
  const h = parseInt(props.settings.popup_img_height) || 120;
  return Math.min(h, 80);
});

const hoverClass = computed(() => {
  const fx = props.settings.popup_hover_effect || 'none';
  return fx !== 'none' && fx !== 'color-overlay' ? 'pop-hover-' + fx : '';
});
</script>

<style scoped>
.olo-popover {
  border-radius: 6px;
  overflow: hidden;
  min-height: 80px;
}

.olo-popover-img {
  width: 100%;
  border-radius: 6px;
}

.olo-popover-marker {
  position: absolute;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  transform: translate(-50%, -50%);
  cursor: pointer;
  z-index: 2;
  box-shadow: 0 0 0 3px rgba(255,255,255,0.4);
}

.olo-popover-marker-pulse {
  position: absolute;
  inset: -4px;
  border-radius: 50%;
  border: 2px solid;
  opacity: 0.5;
  animation: popover-pulse 2s ease-in-out infinite;
}

@keyframes popover-pulse {
  0%, 100% { transform: scale(1); opacity: 0.5; }
  50% { transform: scale(1.4); opacity: 0; }
}

/* Mini popup preview */
.pop-preview {
  position: absolute;
  bottom: 22px;
  left: 50%;
  transform: translateX(-50%);
  width: 160px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  z-index: 10;
}
.pop-preview__media {
  position: relative;
  overflow: hidden;
}
.pop-preview__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease, filter 0.4s ease;
}
.pop-preview__overlay {
  position: absolute;
  inset: 0;
  opacity: 0;
  mix-blend-mode: multiply;
  transition: opacity 0.4s;
  pointer-events: none;
}
.pop-preview:hover .pop-preview__overlay { opacity: 0.45; }

/* Hover effects */
.pop-preview:hover .pop-hover-zoom { transform: scale(1.08); }
.pop-preview:hover .pop-hover-zoom-rotate { transform: scale(1.08) rotate(2deg); }
.pop-hover-brightness { filter: brightness(0.7); }
.pop-preview:hover .pop-hover-brightness { filter: brightness(1); }
.pop-hover-desaturate { filter: grayscale(100%); }
.pop-preview:hover .pop-hover-desaturate { filter: grayscale(0%); }
.pop-hover-blur-in { filter: blur(2px); }
.pop-preview:hover .pop-hover-blur-in { filter: blur(0); }

.pop-preview__body {
  padding: 6px 8px;
}
.pop-preview__title {
  font-size: 9px;
  font-weight: 700;
  margin-bottom: 2px;
}
.pop-preview__text {
  font-size: 8px;
  opacity: 0.7;
  line-height: 1.3;
}
</style>
