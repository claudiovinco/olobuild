<template>
  <div class="olo-popover" style="position:relative;">
    <!-- Image -->
    <div
      v-if="s.image"
      class="olo-popover-img"
      :style="imgStyle"
    ></div>
    <div
      v-else
      class="olo-popover-placeholder mb-flex mb-items-center mb-justify-center"
      :style="placeholderStyle"
    >
      <span class="mb-text-sm" :style="{ color: TOKENS.textFaint, position:'absolute', top:'50%', left:'50%', transform:'translate(-50%,-50%)' }">{{ t('Image Placeholder') }}</span>
    </div>

    <!-- Markers -->
    <div
      v-for="(marker, i) in markers"
      :key="marker.id || i"
      class="olo-popover-marker"
      role="button"
      tabindex="0"
      :aria-label="marker.title"
      :style="{
        left: marker.x + '%',
        top: marker.y + '%',
        background: markerColor,
      }"
      :title="marker.title"
      @click="activeMarker = activeMarker === i ? -1 : i"
      @keydown.enter.prevent="activeMarker = activeMarker === i ? -1 : i"
      @keydown.space.prevent="activeMarker = activeMarker === i ? -1 : i"
    >
      <span class="olo-popover-marker-pulse" :style="{ borderColor: markerColor }"></span>
      <!-- Mini popup preview -->
      <div v-if="activeMarker === i" class="pop-preview" :style="popStyle" @click.stop>
        <div v-if="marker.image" class="pop-preview__media" :style="{ height: popImgH + 'px' }">
          <img :src="marker.image" alt="" class="pop-preview__img" :class="hoverClass" />
          <div v-if="s.popup_hover_effect === 'color-overlay'" class="pop-preview__overlay" :style="{ background: resolveColor(s.popup_hover_color, 'var(--olo-color-primary, #e1474f)') }"></div>
        </div>
        <div class="pop-preview__body">
          <div class="pop-preview__title" :data-olo-editable="'markers.' + i + '.title'">{{ marker.title }}</div>
          <div class="pop-preview__text" :data-olo-editable="'markers.' + i + '.content'">{{ (marker.content || '').substring(0, 60) }}…</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  image: '',
  image_height: '0',
  object_position: 'center center',
  marker_color: '',          // '' ⇒ primary (era #e1474f off-brand)
  popup_bg: '#ffffff',
  popup_color: '#333333',
  popup_radius: '8',
  popup_img_height: '120',
  popup_hover_effect: 'none',
  popup_hover_color: '',     // '' ⇒ primary (era #e1474f off-brand)
};
const s = computed(() => ({ ...defaults, ...props.settings }));

// TOKEN-FIRST: marker → primary
const markerColor = computed(() => resolveColor(s.value.marker_color, 'var(--olo-color-primary, #e1474f)'));

const activeMarker = ref(-1);

const markers = computed(() => {
  const raw = s.value.markers;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { id: 'mk-1', x: 25, y: 30, title: 'Point 1', content: 'Description...', image: '' },
    { id: 'mk-2', x: 70, y: 60, title: 'Point 2', content: 'Description...', image: '' },
  ];
});

const imgHeight = computed(() => parseInt(s.value.image_height) || 0);

const imgStyle = computed(() => {
  const st = {
    backgroundImage: `url(${s.value.image})`,
    backgroundSize: 'cover',
    backgroundRepeat: 'no-repeat',
    backgroundPosition: s.value.object_position || 'center center',
    width: '100%',
  };
  if (imgHeight.value > 0) {
    st.height = Math.min(imgHeight.value, 300) + 'px';
  } else {
    st.paddingBottom = '56.25%';
  }
  return st;
});

const placeholderStyle = computed(() => {
  const st = { position: 'relative', borderRadius: '6px', background: TOKENS.surfaceAlt };
  if (imgHeight.value > 0) {
    st.height = Math.min(imgHeight.value, 300) + 'px';
  } else {
    st.paddingBottom = '56.25%';
  }
  return st;
});

const popStyle = computed(() => ({
  background: s.value.popup_bg || '#ffffff',
  color: s.value.popup_color || '#333333',
  borderRadius: ((v => isNaN(v) ? 8 : v)(parseInt(s.value.popup_radius))) + 'px',
}));

const popImgH = computed(() => {
  const h = parseInt(s.value.popup_img_height) || 120;
  return Math.min(h, 80);
});

const hoverClass = computed(() => {
  const fx = s.value.popup_hover_effect || 'none';
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
/* a11y: anello di focus visibile da tastiera sul marker */
.olo-popover-marker:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px rgba(255,255,255,0.4), 0 0 0 6px color-mix(in srgb, var(--olo-color-primary, #e1474f) 40%, transparent);
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
