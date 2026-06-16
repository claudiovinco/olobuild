<template>
  <div class="olo-carousel" style="position:relative;">
    <!-- Track -->
    <div class="olo-carousel-track" :style="trackStyle">
      <div
        v-for="(slide, i) in visibleSlides"
        :key="slide.id || i"
        class="olo-carousel-slide"
        :style="slideStyle"
      >
        <component
          :is="slide.link_url ? 'a' : 'div'"
          v-if="slide.image_url || slide.widget_template_id"
          :href="slide.link_url || undefined"
          :style="slideInnerStyle"
          @click.prevent
        >
          <img
            v-if="slide.image_url"
            :src="slide.image_url"
            :alt="slide.image_alt || ''"
            :style="imgStyle"
          />
          <div v-else :style="widgetBadgeStyle">
            <span>{{ t('Widget') }}</span>
          </div>
        </component>
        <div v-else :style="placeholderStyle">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2" />
            <circle cx="8.5" cy="8.5" r="1.5" />
            <path d="m21 15-5-5L5 21" />
          </svg>
          <small :style="{ marginTop: '6px', fontSize: '11px', opacity: 0.7 }">{{ t('Slide') }} {{ (currentOffset + i + 1) }}</small>
        </div>
        <!-- Caption -->
        <div
          v-if="s.show_caption && slide.caption"
          :style="captionStyle"
          :data-olo-editable="`slides.${currentOffset + i}.caption`"
        >
          {{ slide.caption }}
        </div>
      </div>
    </div>

    <!-- Arrows -->
    <template v-if="s.show_arrows && slides.length > slidesToShow">
      <button class="olo-carousel-arrow olo-carousel-prev" :style="arrowStyle" :aria-label="t('Precedente')" @click="prevSlide">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <button class="olo-carousel-arrow olo-carousel-next" :style="arrowStyle" :aria-label="t('Successivo')" @click="nextSlide">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </template>

    <!-- Dots -->
    <div v-if="s.show_dots && slides.length > slidesToShow" class="olo-carousel-dots" :style="dotsWrapStyle">
      <button
        v-for="(_, i) in dotCount"
        :key="i"
        type="button"
        class="olo-carousel-dot"
        :style="dotStyle(i)"
        :aria-label="t('Vai a gruppo') + ' ' + (i + 1)"
        @click="goToDot(i)"
      ></button>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed, ref } from 'vue';
import { radiusToCss } from '@/composables/useRadius';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  slides_to_show: '3',
  gap: '16',
  autoplay: false,
  autoplay_speed: '4000',
  show_arrows: true,
  show_dots: true,
  loop: true,
  pause_on_hover: true,
  slide_height: 'auto',
  fixed_height: '300',
  border_radius: 8,
  arrow_color: '#FFFFFF',
  arrow_bg: 'rgba(0,0,0,0.5)',
  dot_color: 'var(--olo-color-primary, #e1474f)',
  dot_inactive_color: 'var(--olo-color-border, #E5E7EB)',
  show_caption: false,
  caption_color: '#FFFFFF',
  caption_bg: 'rgba(0,0,0,0.6)',
  object_fit: 'cover',
  object_position: 'center center',
  mobile_slides: '1',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const slides = computed(() => {
  const raw = s.value.slides;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { id: 'ph-1', image_url: '', image_alt: '', caption: '' },
    { id: 'ph-2', image_url: '', image_alt: '', caption: '' },
    { id: 'ph-3', image_url: '', image_alt: '', caption: '' },
  ];
});

const slidesToShow = computed(() => Math.max(1, Math.min(6, parseInt(s.value.slides_to_show) || 3)));
const gap = computed(() => parseInt(s.value.gap) || 16);

const radiusCss = computed(() => radiusToCss(s.value.border_radius, { fallback: '8px' }));
// Solo per il bottom-radius della caption (top resta 0 perché la caption sta sotto)
const captionRadiusCss = computed(() => {
  const v = s.value.border_radius;
  if (v && typeof v === 'object') {
    const br = parseInt(v.br) || 0;
    const bl = parseInt(v.bl) || 0;
    return `0 0 ${br}px ${bl}px`;
  }
  const n = parseInt(v);
  const r = isNaN(n) ? 8 : n;
  return `0 0 ${r}px ${r}px`;
});

const currentOffset = ref(0);
const visibleSlides = computed(() => {
  const start = currentOffset.value;
  const end = start + slidesToShow.value;
  return slides.value.slice(start, end);
});

function nextSlide() {
  if (currentOffset.value + slidesToShow.value < slides.value.length) currentOffset.value++;
  else if (s.value.loop) currentOffset.value = 0;
}
function prevSlide() {
  if (currentOffset.value > 0) currentOffset.value--;
  else if (s.value.loop) currentOffset.value = Math.max(0, slides.value.length - slidesToShow.value);
}
function goToDot(i) {
  const target = i * slidesToShow.value;
  const max = Math.max(0, slides.value.length - slidesToShow.value);
  currentOffset.value = Math.min(target, max);
}

const dotCount = computed(() => Math.max(1, Math.ceil(slides.value.length / slidesToShow.value)));

const trackStyle = computed(() => ({
  display: 'flex',
  gap: gap.value + 'px',
  overflow: 'hidden',
}));

const slideStyle = computed(() => {
  const n = slidesToShow.value;
  const g = gap.value;
  return {
    flex: `0 0 calc((100% - ${g * (n - 1)}px) / ${n})`,
    minWidth: '0',
    position: 'relative',
    borderRadius: radiusCss.value,
    overflow: 'hidden',
  };
});

const slideInnerStyle = computed(() => ({
  display: 'block',
  width: '100%',
  height: '100%',
  textDecoration: 'none',
  color: 'inherit',
}));

const imgStyle = computed(() => {
  const st = {
    width: '100%',
    display: 'block',
    objectFit: s.value.object_fit || 'cover',
    objectPosition: s.value.object_position || 'center center',
    borderRadius: radiusCss.value,
  };
  if (s.value.slide_height === 'fixed') {
    st.height = (parseInt(s.value.fixed_height) || 300) + 'px';
  } else {
    st.height = 'auto';
    st.aspectRatio = '16/10';
  }
  return st;
});

const placeholderStyle = computed(() => {
  const st = {
    width: '100%',
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    justifyContent: 'center',
    background: 'var(--olo-color-muted, #F3F4F6)',
    color: 'var(--olo-color-text-muted, #9CA3AF)',
    borderRadius: radiusCss.value,
  };
  if (s.value.slide_height === 'fixed') {
    st.height = (parseInt(s.value.fixed_height) || 300) + 'px';
  } else {
    st.aspectRatio = '16/10';
  }
  return st;
});

const widgetBadgeStyle = computed(() => {
  const st = {
    width: '100%',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    background: 'linear-gradient(135deg, var(--olo-color-primary, #e1474f), var(--olo-color-accent, #f4a23b))',
    color: '#FFFFFF',
    fontWeight: '600',
    fontSize: '13px',
    letterSpacing: '0.5px',
    textTransform: 'uppercase',
    borderRadius: radiusCss.value,
  };
  if (s.value.slide_height === 'fixed') {
    st.height = (parseInt(s.value.fixed_height) || 300) + 'px';
  } else {
    st.aspectRatio = '16/10';
  }
  return st;
});

const arrowStyle = computed(() => ({
  color: s.value.arrow_color || '#FFFFFF',
  background: s.value.arrow_bg || 'rgba(0,0,0,0.5)',
}));

const dotsWrapStyle = computed(() => ({
  display: 'flex',
  justifyContent: 'center',
  gap: '8px',
  marginTop: '12px',
}));

function dotStyle(index) {
  const activeIdx = Math.floor(currentOffset.value / slidesToShow.value);
  return {
    width: '10px',
    height: '10px',
    borderRadius: '50%',
    border: 'none',
    padding: '0',
    cursor: 'pointer',
    background: index === activeIdx ? (s.value.dot_color || 'var(--olo-color-primary, #e1474f)') : (s.value.dot_inactive_color || 'var(--olo-color-border, #E5E7EB)'),
    transition: 'background 0.2s',
  };
}

const captionStyle = computed(() => ({
  position: 'absolute',
  bottom: '0',
  left: '0',
  right: '0',
  padding: '6px 10px',
  fontSize: '12px',
  color: s.value.caption_color || '#FFFFFF',
  background: s.value.caption_bg || 'rgba(0,0,0,0.6)',
  borderRadius: captionRadiusCss.value,
}));
</script>

<style scoped>
.olo-carousel-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
  transition: opacity 0.2s;
}

.olo-carousel-arrow:hover {
  opacity: 0.85;
}

.olo-carousel-prev {
  left: 8px;
}

.olo-carousel-next {
  right: 8px;
}

.olo-carousel-arrow:focus-visible,
.olo-carousel-dot:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
