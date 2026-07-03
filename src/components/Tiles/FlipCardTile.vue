<template>
  <div
    class="olo-fc-preview"
    :style="containerStyle"
    @mouseenter="onMouseEnter"
    @mouseleave="onMouseLeave"
    @click="onClick"
  >
    <div class="olo-fc-inner" :style="innerStyle">
      <!-- Front -->
      <div class="olo-fc-face olo-fc-face-front" :style="frontStyle">
        <!-- Sfondo unificato (front_media): immagine/gradiente/colore via style + video element -->
        <div v-if="hasFrontMedia" class="olo-fc-bg" :style="frontMediaStyle"></div>
        <video
          v-if="isFrontVideo"
          class="olo-fc-bg"
          :src="s.front_media.video_url"
          autoplay loop muted playsinline
        ></video>
        <!-- Fallback legacy (solo se front_media non impostato) -->
        <img
          v-if="!hasFrontMedia && s.front_image"
          class="olo-fc-bg"
          :src="s.front_image"
          :style="frontImgStyle"
          alt=""
        />
        <div
          v-if="!hasFrontMedia && s.front_image && s.front_overlay"
          class="olo-fc-overlay"
          :style="{ background: s.front_overlay }"
        ></div>
        <video
          v-if="!hasFrontMedia && s.front_video && !s.front_image"
          class="olo-fc-bg"
          :src="s.front_video"
          autoplay loop muted playsinline
        ></video>
        <div class="olo-fc-content" :style="frontContentStyle">
          <div v-if="s.front_icon" class="olo-fc-icon" :style="frontIconStyle">
            <span :uk-icon="'icon: ' + s.front_icon + '; width: ' + (parseInt(s.front_icon_size)||40) + '; height: ' + (parseInt(s.front_icon_size)||40)"></span>
          </div>
          <div v-if="s.front_title" class="olo-fc-title" :style="titleStyle" data-olo-editable="front_title">{{ s.front_title }}</div>
          <div v-if="s.front_description" class="olo-fc-desc" :style="{ ...descStyle, whiteSpace: 'pre-wrap' }" data-olo-editable="front_description" data-olo-multiline>{{ s.front_description }}</div>
        </div>
      </div>

      <!-- Back -->
      <div class="olo-fc-face olo-fc-face-back" :style="backFaceStyle">
        <!-- Sfondo unificato (back_media): immagine/gradiente/colore via style + video element -->
        <div v-if="hasBackMedia" class="olo-fc-bg" :style="backMediaStyle"></div>
        <video
          v-if="isBackVideo"
          class="olo-fc-bg"
          :src="s.back_media.video_url"
          autoplay loop muted playsinline
        ></video>
        <!-- Fallback legacy (solo se back_media non impostato) -->
        <img
          v-if="!hasBackMedia && s.back_image"
          class="olo-fc-bg"
          :src="s.back_image"
          :style="backImgStyle"
          alt=""
        />
        <div
          v-if="!hasBackMedia && s.back_image && s.back_overlay"
          class="olo-fc-overlay"
          :style="{ background: s.back_overlay }"
        ></div>
        <video
          v-if="!hasBackMedia && s.back_video && !s.back_image"
          class="olo-fc-bg"
          :src="s.back_video"
          autoplay loop muted playsinline
        ></video>
        <div class="olo-fc-content" :style="backContentStyle">
          <div v-if="s.back_icon" class="olo-fc-icon" :style="backIconStyle">
            <span :uk-icon="'icon: ' + s.back_icon + '; width: ' + (parseInt(s.back_icon_size)||40) + '; height: ' + (parseInt(s.back_icon_size)||40)"></span>
          </div>
          <div v-if="s.back_title" class="olo-fc-title" :style="titleStyle" data-olo-editable="back_title">{{ s.back_title }}</div>
          <div v-if="s.back_description" class="olo-fc-desc" :style="{ ...descStyle, whiteSpace: 'pre-wrap' }" data-olo-editable="back_description" data-olo-multiline>{{ s.back_description }}</div>
          <div v-if="s.back_cta_text">
            <span class="olo-fc-cta" :style="ctaStyle" data-olo-editable="back_cta_text">{{ s.back_cta_text }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { resolveColor, TOKENS, SHADOW } from '@/composables/oloTileDefaults';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  front_media: { type: 'none' },
  back_media: { type: 'none' },
  front_image: '',
  front_video: '',
  front_icon: 'star',
  front_icon_size: '40',
  front_icon_color: '',
  front_title: 'Titolo fronte',
  front_description: 'Descrizione della card visibile.',
  front_bg: '',
  front_overlay: 'rgba(0,0,0,0.3)',
  front_image_fit: 'cover',
  front_image_position: 'center center',
  front_text_color: '',
  front_text_align: 'center',
  front_valign: 'center',

  back_image: '',
  back_video: '',
  back_icon: '',
  back_icon_size: '40',
  back_icon_color: '',
  back_title: 'Titolo retro',
  back_description: 'Contenuto retro con dettagli.',
  back_bg: '',
  back_overlay: '',
  back_image_fit: 'cover',
  back_image_position: 'center center',
  back_text_color: '',
  back_text_align: 'center',
  back_valign: 'center',
  back_cta_text: 'Scopri di più',
  back_cta_url: '',
  back_cta_target: false,
  back_cta_bg: '',
  back_cta_color: '',
  back_cta_radius: '6',

  flip_direction: 'horizontal',
  flip_duration: '600',
  flip_trigger: 'hover',
  flip_easing: 'ease-in-out',

  card_height: '350',
  card_border_radius: '12',
  card_shadow: 'md',
  card_border_width: '0',
  card_border_color: 'var(--olo-color-border, #E5E7EB)',
  card_padding: '24',

  title_size: '22',
  title_weight: '600',
  desc_size: '14',
  ...props.settings,
}));

// ── Sfondo unificato per faccia (pannelli front_media / back_media) con fallback legacy ──
const hasFrontMedia = computed(() => {
  const m = s.value.front_media;
  return !!(m && typeof m === 'object' && m.type && m.type !== 'none');
});
const frontMediaStyle = computed(() => (hasFrontMedia.value ? buildBgStyle(s.value.front_media) : {}));
const isFrontVideo = computed(() => {
  const m = s.value.front_media;
  return hasFrontMedia.value && m.type === 'video' && !!m.video_url;
});

const hasBackMedia = computed(() => {
  const m = s.value.back_media;
  return !!(m && typeof m === 'object' && m.type && m.type !== 'none');
});
const backMediaStyle = computed(() => (hasBackMedia.value ? buildBgStyle(s.value.back_media) : {}));
const isBackVideo = computed(() => {
  const m = s.value.back_media;
  return hasBackMedia.value && m.type === 'video' && !!m.video_url;
});

const flipped = ref(false);

function onMouseEnter() {
  if (s.value.flip_trigger !== 'click') flipped.value = true;
}
function onMouseLeave() {
  if (s.value.flip_trigger !== 'click') flipped.value = false;
}
function onClick() {
  if (s.value.flip_trigger === 'click') flipped.value = !flipped.value;
}

const vAlignMap = { top: 'flex-start', center: 'center', bottom: 'flex-end' };

const shadowMap = SHADOW;

const halfH = computed(() => Math.round((parseInt(s.value.card_height) || 350) / 2));

const containerStyle = computed(() => {
  const h = parseInt(s.value.card_height) || 350;
  const r = parseInt(s.value.card_border_radius) || 0;
  const bw = parseInt(s.value.card_border_width) || 0;
  const dir = s.value.flip_direction;
  return {
    perspective: (dir === 'slide-flip' || dir === 'cube') ? '2000px' : '1200px',
    height: h + 'px',
    borderRadius: r + 'px',
    boxShadow: shadowMap[s.value.card_shadow] || shadowMap.md,
    border: bw > 0 ? `${bw}px solid ${s.value.card_border_color || 'var(--olo-color-border, #E5E7EB)'}` : 'none',
    cursor: 'pointer',
  };
});

const flipTransform = computed(() => {
  if (!flipped.value) return 'none';
  const d = s.value.flip_direction;
  switch (d) {
    case 'vertical':   return 'rotateX(180deg)';
    case 'diagonal':   return 'rotateX(180deg) rotateY(180deg)';
    case 'cube':       return 'rotateY(90deg)';
    case 'slide-flip': return 'translateX(-10%) rotateY(-180deg)';
    case 'zoom-flip':  return 'scale(1.05) rotateY(180deg)';
    default:           return 'rotateY(180deg)';
  }
});

const innerStyle = computed(() => {
  const st = {
    position: 'relative',
    width: '100%',
    height: '100%',
    transformStyle: 'preserve-3d',
    transition: `transform ${parseInt(s.value.flip_duration) || 600}ms ${s.value.flip_easing || 'ease-in-out'}`,
    transform: flipTransform.value,
  };
  if (s.value.flip_direction === 'cube') {
    st.transformOrigin = `center center -${halfH.value}px`;
  }
  return st;
});

const backTransform = computed(() => {
  const d = s.value.flip_direction;
  const hH = halfH.value;
  switch (d) {
    case 'vertical':   return 'rotateX(180deg)';
    case 'diagonal':   return 'rotateX(180deg) rotateY(180deg)';
    case 'cube':       return `rotateY(-90deg) translateZ(${hH}px)`;
    case 'slide-flip': return 'rotateY(180deg)';
    case 'zoom-flip':  return 'rotateY(180deg)';
    default:           return 'rotateY(180deg)';
  }
});

const frontStyle = computed(() => {
  const r = parseInt(s.value.card_border_radius) || 0;
  const st = {
    backgroundColor: resolveColor(s.value.front_bg, TOKENS.surfaceAlt),
    color: resolveColor(s.value.front_text_color, TOKENS.text),
    borderRadius: r + 'px',
  };
  if (s.value.flip_direction === 'cube') {
    st.transform = `translateZ(${halfH.value}px)`;
  }
  return st;
});

const backFaceStyle = computed(() => {
  const r = parseInt(s.value.card_border_radius) || 0;
  return {
    backgroundColor: resolveColor(s.value.back_bg, TOKENS.primary),
    color: resolveColor(s.value.back_text_color, TOKENS.onPrimary),
    borderRadius: r + 'px',
    transform: backTransform.value,
  };
});

const frontContentStyle = computed(() => ({
  padding: (parseInt(s.value.card_padding) || 24) + 'px',
  justifyContent: vAlignMap[s.value.front_valign] || 'center',
  textAlign: s.value.front_text_align || 'center',
}));

const backContentStyle = computed(() => ({
  padding: (parseInt(s.value.card_padding) || 24) + 'px',
  justifyContent: vAlignMap[s.value.back_valign] || 'center',
  textAlign: s.value.back_text_align || 'center',
}));

const frontIconStyle = computed(() => {
  const st = { fontSize: (parseInt(s.value.front_icon_size) || 40) + 'px' };
  if (s.value.front_icon_color) st.color = s.value.front_icon_color;
  return st;
});

const backIconStyle = computed(() => {
  const st = { fontSize: (parseInt(s.value.back_icon_size) || 40) + 'px' };
  if (s.value.back_icon_color) st.color = s.value.back_icon_color;
  return st;
});

const frontImgStyle = computed(() => ({
  objectFit: s.value.front_image_fit || 'cover',
  objectPosition: s.value.front_image_position || 'center center',
}));

const backImgStyle = computed(() => ({
  objectFit: s.value.back_image_fit || 'cover',
  objectPosition: s.value.back_image_position || 'center center',
}));

const titleStyle = computed(() => ({
  fontSize: (parseInt(s.value.title_size) || 22) + 'px',
  fontWeight: s.value.title_weight || '600',
}));

const descStyle = computed(() => ({
  fontSize: (parseInt(s.value.desc_size) || 14) + 'px',
}));

const ctaStyle = computed(() => ({
  display: 'inline-block',
  marginTop: '16px',
  padding: '10px 24px',
  background: resolveColor(s.value.back_cta_bg, TOKENS.onPrimary),
  color: resolveColor(s.value.back_cta_color, TOKENS.primary),
  borderRadius: ((v => isNaN(v) ? 6 : v)(parseInt(s.value.back_cta_radius))) + 'px',
  textDecoration: 'none',
  fontWeight: '600',
  fontSize: (parseInt(s.value.desc_size) || 14) + 'px',
  cursor: 'default',
}));
</script>

<style scoped>
.olo-fc-preview {
  position: relative;
  width: 100%;
  box-sizing: border-box;
  overflow: hidden;
}
.olo-fc-face {
  position: absolute;
  inset: 0;
  backface-visibility: hidden;
  -webkit-backface-visibility: hidden;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
.olo-fc-bg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: 0;
}
.olo-fc-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
}
.olo-fc-content {
  position: relative;
  z-index: 2;
  flex: 1;
  display: flex;
  flex-direction: column;
}
.olo-fc-icon {
  line-height: 1;
  margin-bottom: 12px;
}
.olo-fc-title {
  margin: 0 0 8px;
  line-height: 1.3;
}
.olo-fc-desc {
  line-height: 1.5;
  margin: 0;
  opacity: 0.9;
}
</style>
