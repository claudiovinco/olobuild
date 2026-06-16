<template>
  <div
    class="olo-overlay-tile"
    :style="containerStyle"
    @mouseenter="hovered = true"
    @mouseleave="hovered = false"
  >
    <!-- Background image -->
    <div class="olo-ov-img" :style="imgStyle"></div>

    <!-- Overlay -->
    <div class="olo-ov-overlay" :style="overlayStyle">
      <div class="olo-ov-bg" :style="overlayBgStyle"></div>
      <div class="olo-ov-text" :style="{ color: s.text_color }">
        <div v-if="s.title" class="mb-text-2xl mb-font-bold mb-mb-2" data-olo-editable="title">{{ s.title }}</div>
        <div v-if="s.description" class="mb-text-sm" style="opacity:0.9;line-height:1.5;white-space:pre-wrap" data-olo-editable="description" data-olo-multiline>{{ s.description }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  image_url: '',
  object_position: 'center center',
  title: '',
  description: '',
  overlay_color: '#000000',
  text_color: '#FFFFFF',
  hover_effect: 'fade',
  overlay_opacity: '70',
  height: '300',
  border_radius: '0',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const hovered = ref(false);

const containerStyle = computed(() => ({
  position: 'relative',
  overflow: 'hidden',
  borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
  height: (parseInt(s.value.height) || 300) + 'px',
  cursor: 'pointer',
}));

const imgStyle = computed(() => {
  const pos = s.value.object_position || 'center center';
  const bg = s.value.image_url ? `url(${s.value.image_url}) ${pos}/cover no-repeat` : '#374151';
  return {
    position: 'absolute',
    inset: '0',
    background: bg,
    backgroundPosition: pos,
    transition: 'transform 0.4s ease',
    transform: hovered.value && s.value.hover_effect === 'zoom' ? 'scale(1.1)' : 'scale(1)',
  };
});

const overlayStyle = computed(() => {
  const effect = s.value.hover_effect || 'fade';
  const style = {
    position: 'absolute',
    inset: '0',
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    justifyContent: 'center',
    padding: '24px',
    textAlign: 'center',
    transition: 'opacity 0.4s ease, transform 0.4s ease',
    opacity: hovered.value ? '1' : '0',
  };
  if (effect === 'slide-up') {
    style.transform = hovered.value ? 'translateY(0)' : 'translateY(20px)';
  }
  return style;
});

const overlayBgStyle = computed(() => ({
  position: 'absolute',
  inset: '0',
  background: s.value.overlay_color || '#000000',
  opacity: (parseInt(s.value.overlay_opacity) || 70) / 100,
}));
</script>
