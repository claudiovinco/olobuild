<template>
  <div
    class="olo-overlay-tile"
    :style="containerStyle"
    @mouseenter="hovered = true"
    @mouseleave="hovered = false"
  >
    <!-- L'immagine è un <img> come sul sito (prima era uno sfondo CSS): solo così
         object-fit e object-position si comportano allo stesso modo nei due posti. -->
    <img v-if="s.image_url" class="olo-ov-img" :src="s.image_url" alt="" :style="imgStyle" />
    <div v-else class="olo-ov-img" :style="imgStyle"></div>

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
import { imageFrame } from '@/composables/useImageFrame';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  image_url: '',
  aspect_ratio: 'auto',
  aspect_ratio_custom: '16/9',
  object_fit: 'cover',
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

// Cornice dell'immagine, con le stesse regole del renderer PHP. Le chiavi di questa
// tile non hanno prefisso (come nella tile Immagine): si passa all'helper un alias
// con lo schema che si aspetta, senza toccare niente di ciò che viene salvato.
const frame = computed(() => imageFrame({
  img_ratio: s.value.aspect_ratio,
  img_ratio_custom: s.value.aspect_ratio_custom,
  img_fit: s.value.object_fit,
  img_object_position: s.value.object_position,
}, 'img', { ratio: 'auto', fit: 'cover' }));

const containerStyle = computed(() => ({
  position: 'relative',
  overflow: 'hidden',
  borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
  // Altezza fissa OPPURE proporzioni: con tutt'e due definite il browser ignora
  // l'aspect-ratio, quindi comanda una sola delle due (come nel PHP).
  ...(frame.value.contenitore.aspectRatio
    ? frame.value.contenitore
    : { height: (parseInt(s.value.height) || 300) + 'px' }),
  cursor: 'pointer',
}));

const imgStyle = computed(() => ({
  position: 'absolute',
  inset: '0',
  width: '100%',
  height: '100%',
  // Senza immagine resta il rettangolo grigio di prima.
  background: s.value.image_url ? undefined : '#374151',
  ...frame.value.immagine,
  transition: 'transform 0.4s ease',
  transform: hovered.value && s.value.hover_effect === 'zoom' ? 'scale(1.1)' : 'scale(1)',
}));

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
