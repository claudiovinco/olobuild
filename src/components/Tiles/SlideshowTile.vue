<template>
  <div class="olo-slideshow" style="position:relative;overflow:hidden;" :style="{ height: slideHeight + 'px' }">
    <!-- Track -->
    <div
      class="olo-ss-track"
      :style="trackStyle"
    >
      <div
        v-for="(slide, i) in slides"
        :key="slide.id || i"
        class="olo-ss-slide"
        :style="slideStyle(slide)"
      >
        <div class="olo-ss-overlay" :style="{ background: s.overlay_color, opacity: (parseInt(s.overlay_opacity) || 45) / 100 }"></div>
        <div class="olo-ss-content" :style="{ color: s.text_color }">
          <div v-if="slide.title" class="mb-text-3xl mb-font-bold mb-mb-2" :data-olo-editable="'slides.' + i + '.title'">{{ slide.title }}</div>
          <div v-if="slide.subtitle" class="mb-text-lg" style="opacity:0.85;" :data-olo-editable="'slides.' + i + '.subtitle'">{{ slide.subtitle }}</div>
        </div>
      </div>
    </div>

    <!-- Arrows -->
    <template v-if="s.show_arrows !== false && slides.length > 1">
      <button class="olo-ss-arrow olo-ss-prev" @click="prev" :aria-label="t('Precedente')">&#10094;</button>
      <button class="olo-ss-arrow olo-ss-next" @click="next" :aria-label="t('Successivo')">&#10095;</button>
    </template>

    <!-- Dots -->
    <div v-if="s.show_dots !== false && slides.length > 1" class="olo-ss-dots">
      <button
        v-for="(_, i) in slides"
        :key="i"
        class="olo-ss-dot"
        :class="{ 'olo-ss-dot--active': i === current }"
        @click="goTo(i)"
        :aria-label="'Vai a slide ' + (i + 1)"
      ></button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  autoplay: true,
  autoplay_speed: '5000',
  show_arrows: true,
  show_dots: true,
  slide_height: '400',
  overlay_color: '#000000',
  overlay_opacity: '45',
  text_color: '#FFFFFF',
  transition: 'slide',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const current = ref(0);
let autoTimer = null;

const slides = computed(() => {
  const raw = s.value.slides;
  if (Array.isArray(raw) && raw.length) return raw;
  return [{ id: 'empty', image: '', title: 'Slide 1', subtitle: 'Aggiungi slide nell\'inspector' }];
});

const slideHeight = computed(() => parseInt(s.value.slide_height) || 400);

const trackStyle = computed(() => ({
  display: 'flex',
  height: '100%',
  transition: s.value.transition === 'fade' ? 'none' : 'transform 0.5s ease',
  transform: s.value.transition === 'fade' ? 'none' : `translateX(-${current.value * 100}%)`,
}));

function slideStyle(slide) {
  const bg = slide.image ? `url(${slide.image}) center/cover no-repeat` : 'var(--olo-color-muted, #F3F4F6)';
  const opacity = s.value.transition === 'fade'
    ? (slides.value.indexOf(slide) === current.value ? 1 : 0)
    : 1;
  const base = {
    minWidth: '100%',
    height: '100%',
    position: 'relative',
    background: bg,
  };
  if (s.value.transition === 'fade') {
    base.position = 'absolute';
    base.top = '0';
    base.left = '0';
    base.width = '100%';
    base.opacity = opacity;
    base.transition = 'opacity 0.5s ease';
  }
  return base;
}

function goTo(index) {
  current.value = ((index % slides.value.length) + slides.value.length) % slides.value.length;
  resetAuto();
}

function next() { goTo(current.value + 1); }
function prev() { goTo(current.value - 1); }

function startAuto() {
  if (s.value.autoplay && slides.value.length > 1) {
    autoTimer = setInterval(() => goTo(current.value + 1), parseInt(s.value.autoplay_speed) || 5000);
  }
}

function resetAuto() {
  clearInterval(autoTimer);
  startAuto();
}

onMounted(() => startAuto());
onUnmounted(() => clearInterval(autoTimer));

watch(() => s.value.autoplay, () => { clearInterval(autoTimer); startAuto(); });
watch(() => s.value.autoplay_speed, () => { clearInterval(autoTimer); startAuto(); });
</script>

<style scoped>
.olo-ss-overlay {
  position: absolute;
  inset: 0;
}

.olo-ss-content {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  padding: 24px;
  text-align: center;
}

.olo-ss-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0,0,0,0.5);
  color: #fff;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 18px;
  z-index: 2;
  transition: background 0.2s;
}

.olo-ss-arrow:hover {
  background: rgba(0,0,0,0.75);
}

.olo-ss-arrow:focus-visible,
.olo-ss-dot:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}

.olo-ss-prev { left: 12px; }
.olo-ss-next { right: 12px; }

.olo-ss-dots {
  position: absolute;
  bottom: 14px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 8px;
  z-index: 2;
}

.olo-ss-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  border: 2px solid #fff;
  background: transparent;
  cursor: pointer;
  padding: 0;
  transition: background 0.2s;
}

.olo-ss-dot--active {
  background: #fff;
}
</style>
