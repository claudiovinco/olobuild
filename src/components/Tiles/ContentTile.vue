<template>
  <div class="mb-p-8">
    <div class="mb-flex" :style="wrapStyle">
      <!-- Image -->
      <div v-if="s.image" class="olo-ct-img-col" :style="imgColStyle">
        <div class="olo-ct-wrap mb-relative mb-overflow-hidden" :style="imgWrapStyle">
          <img
            :src="s.image"
            alt=""
            class="olo-ct-img mb-w-full mb-block"
            :class="hoverClass"
            :style="imgStyle"
          />
          <!-- hover indicator -->
          <div
            v-if="s.hover_image || s.hover_video"
            class="mb-absolute mb-top-1 mb-right-1 mb-bg-black/60 mb-text-white mb-text-xs mb-px-1.5 mb-py-0.5 mb-rounded"
          >
            {{ s.hover_video ? '&#x25B6; hover' : '&#x21C4; hover' }}
          </div>
        </div>
      </div>

      <!-- Text -->
      <div :style="textColStyle">
        <component :is="s.heading_tag || 'h2'" class="mb-font-bold" :style="headingStyle" data-olo-editable="heading">{{ s.heading || 'Titolo Sezione' }}</component>
        <div class="mb-leading-relaxed" :style="textStyle" data-olo-editable="text" data-olo-richtext data-olo-multiline v-html="textHtml"></div>
        <div v-if="s.link_url" class="mb-mt-2 mb-text-sm mb-truncate" style="color: var(--olo-color-primary, #e1474f);">{{ s.link_url }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import { rv } from '@/composables/useResponsiveValue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const builderStore = useBuilderStore();

const s = computed(() => ({
  heading: '',
  heading_tag: 'h2',
  heading_size: 'md',
  heading_line_height: 1.2,
  heading_align: '',
  heading_color: '',
  text: '',
  text_color: '',
  image: '',
  image_position: 'top',
  image_width: '40',
  image_height: 'auto',
  image_fit: 'cover',
  image_radius: '0',
  image_border_width: '0',
  image_border_color: '',
  image_shadow: 'none',
  heading_gap: '8',
  image_gap: '16',
  hover_effect: 'none',
  hover_image: '',
  hover_video: '',
  link_url: '',
  link_target: '_self',
  ...props.settings,
}));

// Resolve responsive values
const effectivePosition = computed(() => rv(props.settings, 'image_position', s.value.image_position, builderStore.viewMode));
const effectiveSize = computed(() => rv(props.settings, 'heading_size', s.value.heading_size, builderStore.viewMode));
const effectiveLineHeight = computed(() => rv(props.settings, 'heading_line_height', s.value.heading_line_height, builderStore.viewMode));
const effectiveAlign = computed(() => rv(props.settings, 'heading_align', s.value.heading_align, builderStore.viewMode));

const hasText = computed(() => {
  const raw = s.value.text || '';
  return raw.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim().length > 0;
});

const textHtml = computed(() => s.value.text || '<span class="olo-editable-ph">Il contenuto va qui...</span>');

const sizeMap = { sm: '1em', md: '1.25em', lg: '1.75em', xl: '2.25em' };

const isHorizontal = computed(() => effectivePosition.value === 'left' || effectivePosition.value === 'right');
const isReversed = computed(() => effectivePosition.value === 'bottom' || effectivePosition.value === 'right');

const headingStyle = computed(() => {
  // gap solo se c'è davvero del testo sotto
  const gapPx = hasText.value ? (parseInt(s.value.heading_gap) || 0) : 0;
  const st = {
    fontSize: sizeMap[effectiveSize.value] || '1.25em',
    margin: '0 0 ' + gapPx + 'px 0',
  };
  const lh = parseFloat(effectiveLineHeight.value);
  if (lh && lh > 0) st.lineHeight = lh;
  if (effectiveAlign.value) st.textAlign = effectiveAlign.value;
  if (s.value.heading_color) st.color = s.value.heading_color;
  return st;
});

const textStyle = computed(() => {
  const st = {};
  if (s.value.text_color) st.color = s.value.text_color;
  return st;
});

const dirMap = { top: 'column', bottom: 'column-reverse', left: 'row', right: 'row-reverse' };

const wrapStyle = computed(() => {
  const gap = parseInt(s.value.image_gap) || 0;
  return {
    flexDirection: dirMap[effectivePosition.value] || 'column',
    gap: gap + 'px',
    alignItems: isHorizontal.value ? 'flex-start' : 'stretch',
  };
});

const imgColStyle = computed(() => {
  const style = {};
  if (isHorizontal.value) {
    style.width = s.value.image_width + '%';
    style.flexShrink = 0;
  }
  return style;
});

const textColStyle = computed(() => {
  return isHorizontal.value ? { flex: 1, minWidth: 0 } : {};
});

const shadowMap = {
  none: 'none',
  sm: '0 1px 2px rgba(0,0,0,.05)',
  md: '0 4px 6px rgba(0,0,0,.1)',
  lg: '0 10px 15px rgba(0,0,0,.1)',
  xl: '0 20px 25px rgba(0,0,0,.1)',
};

const hoverClass = computed(() => {
  const fx = s.value.hover_effect;
  if (!fx || fx === 'none') return '';
  return 'olo-ct-hover-' + fx;
});

const imgWrapStyle = computed(() => {
  const r = parseInt(s.value.image_radius) || 0;
  const bw = parseInt(s.value.image_border_width) || 0;
  const style = { borderRadius: r + 'px' };
  if (bw > 0) {
    style.border = bw + 'px solid ' + (s.value.image_border_color || 'var(--olo-color-border, #e5e7eb)');
  }
  const sh = shadowMap[s.value.image_shadow] || 'none';
  if (sh !== 'none') style.boxShadow = sh;
  return style;
});

const imgStyle = computed(() => {
  const h = s.value.image_height;
  const style = {
    objectFit: s.value.image_fit || 'cover',
  };
  if (h && h !== 'auto') {
    style.height = /^\d+$/.test(h) ? h + 'px' : h;
  }
  return style;
});
</script>

<style scoped>
.olo-ct-img {
  transition: transform 0.5s ease, filter 0.5s ease;
}

/* Zoom */
.olo-ct-wrap:hover .olo-ct-hover-zoom {
  transform: scale(1.08);
}

/* Zoom + Rotate */
.olo-ct-wrap:hover .olo-ct-hover-zoom-rotate {
  transform: scale(1.08) rotate(2deg);
}

/* Brightness (scura di default, normale su hover) */
.olo-ct-hover-brightness {
  filter: brightness(0.7);
}
.olo-ct-wrap:hover .olo-ct-hover-brightness {
  filter: brightness(1);
}

/* Desaturate (grigio di default, colore su hover) */
.olo-ct-hover-desaturate {
  filter: grayscale(100%);
}
.olo-ct-wrap:hover .olo-ct-hover-desaturate {
  filter: grayscale(0%);
}

/* Blur in (sfocata di default, nitida su hover) */
.olo-ct-hover-blur-in {
  filter: blur(3px);
}
.olo-ct-wrap:hover .olo-ct-hover-blur-in {
  filter: blur(0);
}
</style>
