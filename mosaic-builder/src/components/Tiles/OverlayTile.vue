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
      <div class="olo-ov-text" :style="{ color: settings.text_color }">
        <div v-if="settings.title" class="mb-text-2xl mb-font-bold mb-mb-2" v-html="settings.title"></div>
        <div v-if="settings.description" class="mb-text-sm" style="opacity:0.9;line-height:1.5;" v-html="settings.description"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const hovered = ref(false);

const containerStyle = computed(() => ({
  position: 'relative',
  overflow: 'hidden',
  borderRadius: (parseInt(props.settings.border_radius) || 0) + 'px',
  height: (parseInt(props.settings.height) || 300) + 'px',
  cursor: 'pointer',
}));

const imgStyle = computed(() => {
  const bg = props.settings.image_url ? `url(${props.settings.image_url}) center/cover no-repeat` : '#374151';
  return {
    position: 'absolute',
    inset: '0',
    background: bg,
    transition: 'transform 0.4s ease',
    transform: hovered.value && props.settings.hover_effect === 'zoom' ? 'scale(1.1)' : 'scale(1)',
  };
});

const overlayStyle = computed(() => {
  const effect = props.settings.hover_effect || 'fade';
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
  background: props.settings.overlay_color || '#000000',
  opacity: (parseInt(props.settings.overlay_opacity) || 70) / 100,
}));
</script>
