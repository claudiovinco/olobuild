<template>
  <div class="olo-revealbox" :class="'olo-reveal-' + s.reveal_effect" :style="containerStyle">
    <!-- Global background image (behind everything, fixed) -->
    <div v-if="s.image_url" class="olo-revealbox-bg" :style="globalBgStyle"></div>

    <!-- SLIDE effects: inner wrapper moves -->
    <template v-if="isSlideEffect">
      <div class="olo-revealbox-slider" :style="sliderStyle">
        <!-- slide-down / slide-right: bottom first, then top -->
        <template v-if="s.reveal_effect === 'slide-down' || s.reveal_effect === 'slide-right'">
          <div class="olo-revealbox-face olo-revealbox-bottom" :style="faceStyleObj('bottom')">
            <div v-if="s.bottom_image_url" class="olo-revealbox-bg" :style="bgStyle('bottom')"></div>
            <video v-else-if="s.bottom_video_url" class="olo-revealbox-bgvid" :src="s.bottom_video_url" autoplay muted loop playsinline></video>
            <div v-if="bottomOverlayOpacity > 0" class="olo-revealbox-overlay" :style="bottomOverlayStyle"></div>
            <div class="olo-revealbox-content" :style="contentPad('bottom')">
              <div v-if="s.bottom_icon" class="olo-revealbox-icon" :style="iconStyle('bottom')">
                <span :uk-icon="'icon: ' + s.bottom_icon + '; ratio: ' + (s.bottom_icon_size || 2)"></span>
              </div>
              <div v-html="s.bottom_content" data-olo-editable="bottom_content"></div>
            </div>
          </div>
          <div class="olo-revealbox-face olo-revealbox-top" :style="faceStyleObj('top')">
            <div v-if="s.top_image_url" class="olo-revealbox-bg" :style="bgStyle('top')"></div>
            <video v-else-if="s.top_video_url" class="olo-revealbox-bgvid" :src="s.top_video_url" autoplay muted loop playsinline></video>
            <div v-if="topOverlayOpacity > 0" class="olo-revealbox-overlay" :style="topOverlayStyle"></div>
            <div class="olo-revealbox-content" :style="contentPad('top')">
              <div v-if="s.top_icon" class="olo-revealbox-icon" :style="iconStyle('top')">
                <span :uk-icon="'icon: ' + s.top_icon + '; ratio: ' + (s.top_icon_size || 2)"></span>
              </div>
              <div v-html="s.top_content" data-olo-editable="top_content"></div>
            </div>
          </div>
        </template>
        <!-- Other slides: top first, then bottom -->
        <template v-else>
          <div class="olo-revealbox-face olo-revealbox-top" :style="faceStyleObj('top')">
            <div v-if="s.top_image_url" class="olo-revealbox-bg" :style="bgStyle('top')"></div>
            <video v-else-if="s.top_video_url" class="olo-revealbox-bgvid" :src="s.top_video_url" autoplay muted loop playsinline></video>
            <div v-if="topOverlayOpacity > 0" class="olo-revealbox-overlay" :style="topOverlayStyle"></div>
            <div class="olo-revealbox-content" :style="contentPad('top')">
              <div v-if="s.top_icon" class="olo-revealbox-icon" :style="iconStyle('top')">
                <span :uk-icon="'icon: ' + s.top_icon + '; ratio: ' + (s.top_icon_size || 2)"></span>
              </div>
              <div v-html="s.top_content" data-olo-editable="top_content"></div>
            </div>
          </div>
          <div class="olo-revealbox-face olo-revealbox-bottom" :style="faceStyleObj('bottom')">
            <div v-if="s.bottom_image_url" class="olo-revealbox-bg" :style="bgStyle('bottom')"></div>
            <video v-else-if="s.bottom_video_url" class="olo-revealbox-bgvid" :src="s.bottom_video_url" autoplay muted loop playsinline></video>
            <div v-if="bottomOverlayOpacity > 0" class="olo-revealbox-overlay" :style="bottomOverlayStyle"></div>
            <div class="olo-revealbox-content" :style="contentPad('bottom')">
              <div v-if="s.bottom_icon" class="olo-revealbox-icon" :style="iconStyle('bottom')">
                <span :uk-icon="'icon: ' + s.bottom_icon + '; ratio: ' + (s.bottom_icon_size || 2)"></span>
              </div>
              <div v-html="s.bottom_content" data-olo-editable="bottom_content"></div>
            </div>
          </div>
        </template>
      </div>
    </template>

    <!-- FADE / ZOOM / ROTATE effects: two stacked layers -->
    <template v-else-if="isStackEffect">
      <div class="olo-revealbox-face olo-revealbox-top olo-revealbox-stack-top" :style="stackTopStyle">
        <div v-if="s.top_image_url" class="olo-revealbox-bg" :style="bgStyle('top')"></div>
        <video v-else-if="s.top_video_url" class="olo-revealbox-bgvid" :src="s.top_video_url" autoplay muted loop playsinline></video>
        <div v-if="topOverlayOpacity > 0" class="olo-revealbox-overlay" :style="topOverlayStyle"></div>
        <div class="olo-revealbox-content" :style="contentPad('top')">
          <div v-if="s.top_icon" class="olo-revealbox-icon" :style="iconStyle('top')">
            <span :uk-icon="'icon: ' + s.top_icon + '; ratio: ' + (s.top_icon_size || 2)"></span>
          </div>
          <div v-html="s.top_content" data-olo-editable="top_content"></div>
        </div>
      </div>
      <div class="olo-revealbox-face olo-revealbox-bottom olo-revealbox-stack-bottom" :style="stackBottomStyle">
        <div v-if="s.bottom_image_url" class="olo-revealbox-bg" :style="bgStyle('bottom')"></div>
        <video v-else-if="s.bottom_video_url" class="olo-revealbox-bgvid" :src="s.bottom_video_url" autoplay muted loop playsinline></video>
        <div v-if="bottomOverlayOpacity > 0" class="olo-revealbox-overlay" :style="bottomOverlayStyle"></div>
        <div class="olo-revealbox-content" :style="contentPad('bottom')">
          <div v-if="s.bottom_icon" class="olo-revealbox-icon" :style="iconStyle('bottom')">
            <span :uk-icon="'icon: ' + s.bottom_icon + '; ratio: ' + (s.bottom_icon_size || 2)"></span>
          </div>
          <div v-html="s.bottom_content" data-olo-editable="bottom_content"></div>
        </div>
      </div>
    </template>

    <!-- FLIP 3D effects: two faces in 3D space -->
    <template v-else-if="isFlipEffect">
      <div class="olo-revealbox-flipper" :style="flipperStyle">
        <div class="olo-revealbox-face olo-revealbox-flip-front" :style="flipFrontStyle">
          <div v-if="s.top_image_url" class="olo-revealbox-bg" :style="bgStyle('top')"></div>
          <video v-else-if="s.top_video_url" class="olo-revealbox-bgvid" :src="s.top_video_url" autoplay muted loop playsinline></video>
          <div v-if="topOverlayOpacity > 0" class="olo-revealbox-overlay" :style="topOverlayStyle"></div>
          <div class="olo-revealbox-content" :style="contentPad('top')">
            <div v-if="s.top_icon" class="olo-revealbox-icon" :style="iconStyle('top')">
              <span :uk-icon="'icon: ' + s.top_icon + '; ratio: ' + (s.top_icon_size || 2)"></span>
            </div>
            <div v-html="s.top_content" data-olo-editable="top_content"></div>
          </div>
        </div>
        <div class="olo-revealbox-face olo-revealbox-flip-back" :style="flipBackStyle">
          <div v-if="s.bottom_image_url" class="olo-revealbox-bg" :style="bgStyle('bottom')"></div>
          <video v-else-if="s.bottom_video_url" class="olo-revealbox-bgvid" :src="s.bottom_video_url" autoplay muted loop playsinline></video>
          <div v-if="bottomOverlayOpacity > 0" class="olo-revealbox-overlay" :style="bottomOverlayStyle"></div>
          <div class="olo-revealbox-content" :style="contentPad('bottom')">
            <div v-if="s.bottom_icon" class="olo-revealbox-icon" :style="iconStyle('bottom')">
              <span :uk-icon="'icon: ' + s.bottom_icon + '; ratio: ' + (s.bottom_icon_size || 2)"></span>
            </div>
            <div v-html="s.bottom_content" data-olo-editable="bottom_content"></div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  visible_height: '300',
  top_image_url: '', top_image_position: 'center center', top_image_size: 'cover', top_video_url: '',
  bottom_image_url: '', bottom_image_position: 'center center', bottom_image_size: 'cover', bottom_video_url: '',
  top_content: '<h3>Titolo</h3>',
  bottom_content: '<p>Contenuto rivelato al passaggio del mouse</p>',
  top_icon: '', top_icon_size: '2', top_icon_color: '#ffffff',
  bottom_icon: '', bottom_icon_size: '2', bottom_icon_color: '#ffffff',
  reveal_effect: 'slide-up',
  reveal_amount: '',
  transition_speed: '0.5',
  transition_easing: 'ease',
  overlay_color: '#000000',
  overlay_opacity: '0',
  reveal_overlay_color: '#000000',
  reveal_overlay_opacity: '60',
  text_color: '#ffffff',
  top_align: 'flex-end', top_justify: 'flex-start',
  bottom_align: 'flex-start', bottom_justify: 'flex-start',
  top_text_color: '#ffffff', top_font_size: '',
  bottom_text_color: '#ffffff', bottom_font_size: '',
  top_padding: '24', bottom_padding: '24',
  border_radius: '0',
  perspective: '800',
  // backward compat
  image_url: '', image_position: 'center center', image_size: 'cover',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const visibleH = computed(() => parseInt(s.value.visible_height) || 300);
const topOverlayOpacity = computed(() => parseInt(s.value.overlay_opacity) || 0);
const bottomOverlayOpacity = computed(() => parseInt(s.value.reveal_overlay_opacity) || 0);
const speed = computed(() => parseFloat(s.value.transition_speed) || 0.5);
const easing = computed(() => s.value.transition_easing || 'ease');
const radius = computed(() => (parseInt(s.value.border_radius) || 0) + 'px');

const effect = computed(() => s.value.reveal_effect || 'slide-up');
const isSlideEffect = computed(() => effect.value.startsWith('slide-'));
const isFlipEffect = computed(() => effect.value.startsWith('flip-'));
const isStackEffect = computed(() => ['fade', 'zoom-in', 'zoom-out', 'rotate-in'].includes(effect.value));
const isHorizSlide = computed(() => effect.value === 'slide-left' || effect.value === 'slide-right');

// ── Container ──
const containerStyle = computed(() => ({
  height: visibleH.value + 'px',
  overflow: 'hidden',
  position: 'relative',
  borderRadius: radius.value,
  color: s.value.top_text_color || s.value.text_color,
  perspective: isFlipEffect.value ? (parseInt(s.value.perspective) || 800) + 'px' : undefined,
}));

// ── Global background ──
const globalBgStyle = computed(() => ({
  position: 'absolute', inset: '0', zIndex: '0',
  backgroundImage: `url(${s.value.image_url})`,
  backgroundSize: s.value.image_size || 'cover',
  backgroundPosition: s.value.image_position || 'center center',
  backgroundRepeat: 'no-repeat',
}));

// ── Per-face background ──
function bgStyle(zone) {
  const url = zone === 'top' ? s.value.top_image_url : s.value.bottom_image_url;
  const size = zone === 'top' ? s.value.top_image_size : s.value.bottom_image_size;
  const pos = zone === 'top' ? s.value.top_image_position : s.value.bottom_image_position;
  return {
    position: 'absolute', inset: '0', zIndex: '0',
    backgroundImage: `url(${url})`,
    backgroundSize: size, backgroundPosition: pos, backgroundRepeat: 'no-repeat',
  };
}

// ── Icon style ──
function iconStyle(zone) {
  const color = zone === 'top' ? s.value.top_icon_color : s.value.bottom_icon_color;
  return { color, lineHeight: '1', marginBottom: '8px' };
}

// ── Face style ──
function faceStyleObj(zone) {
  const align = zone === 'top' ? s.value.top_align : s.value.bottom_align;
  const justify = zone === 'top' ? s.value.top_justify : s.value.bottom_justify;
  const base = {
    position: 'relative',
    display: 'flex', flexDirection: 'column',
    alignItems: justify, justifyContent: align,
    boxSizing: 'border-box',
  };
  if (isHorizSlide.value) {
    base.width = '50%';
    base.height = visibleH.value + 'px';
    base.flexShrink = '0';
  } else {
    base.width = '100%';
    base.height = visibleH.value + 'px';
  }
  return base;
}

function contentPad(zone) {
  const pad = parseInt(zone === 'top' ? s.value.top_padding : s.value.bottom_padding) || 0;
  const color = zone === 'top' ? (s.value.top_text_color || s.value.text_color) : (s.value.bottom_text_color || s.value.text_color);
  const fontSize = zone === 'top' ? s.value.top_font_size : s.value.bottom_font_size;
  const style = { position: 'relative', zIndex: '2', padding: pad + 'px', color };
  if (fontSize && parseInt(fontSize) > 0) {
    style.fontSize = parseInt(fontSize) + 'px';
  }
  return style;
}

// ── Overlays ──
const topOverlayStyle = computed(() => ({
  position: 'absolute', inset: '0',
  backgroundColor: s.value.overlay_color,
  opacity: topOverlayOpacity.value / 100,
  zIndex: '1', pointerEvents: 'none',
}));
const bottomOverlayStyle = computed(() => ({
  position: 'absolute', inset: '0',
  backgroundColor: s.value.reveal_overlay_color,
  opacity: bottomOverlayOpacity.value / 100,
  zIndex: '1', pointerEvents: 'none',
}));

// ── SLIDE mode ──
const sliderStyle = computed(() => {
  const base = {
    position: 'relative', zIndex: '1',
    display: isHorizSlide.value ? 'flex' : 'block',
    transition: `transform ${speed.value}s ${easing.value}`,
    willChange: 'transform',
  };
  if (isHorizSlide.value) {
    base.width = '200%';
  }
  // Initial offset: slide-down starts shifted up, slide-right starts shifted left
  if (effect.value === 'slide-down') {
    const rh = parseInt(s.value.reveal_amount) || visibleH.value;
    base.transform = `translateY(-${rh}px)`;
  } else if (effect.value === 'slide-right') {
    base.transform = 'translateX(-50%)';
  }
  return base;
});

// ── STACK mode ──
const stackTopStyle = computed(() => ({
  ...faceStyleObj('top'),
  position: 'absolute', inset: '0', zIndex: '2',
  transition: `opacity ${speed.value}s ${easing.value}, transform ${speed.value}s ${easing.value}`,
}));
const stackBottomStyle = computed(() => ({
  ...faceStyleObj('bottom'),
  position: 'absolute', inset: '0', zIndex: '1',
}));

// ── FLIP mode ──
const flipperStyle = computed(() => ({
  position: 'relative', width: '100%', height: '100%',
  transition: `transform ${speed.value}s ${easing.value}`,
  transformStyle: 'preserve-3d',
}));
const flipFrontStyle = computed(() => ({
  ...faceStyleObj('top'),
  position: 'absolute', inset: '0',
  backfaceVisibility: 'hidden', zIndex: '2',
}));
const flipBackStyle = computed(() => ({
  ...faceStyleObj('bottom'),
  position: 'absolute', inset: '0',
  backfaceVisibility: 'hidden',
  transform: effect.value === 'flip-x' ? 'rotateY(180deg)' : 'rotateX(180deg)',
}));
</script>

<style scoped>
.olo-revealbox-bg {
  position: absolute;
  inset: 0;
  z-index: 0;
}
.olo-revealbox-bgvid {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  pointer-events: none;
  z-index: 0;
}
.olo-revealbox-overlay {
  position: absolute;
  inset: 0;
  pointer-events: none;
}
</style>
