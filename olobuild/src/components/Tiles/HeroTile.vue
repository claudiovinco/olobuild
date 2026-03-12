<template>
  <div class="mb-relative mb-overflow-hidden" :style="containerStyle">
    <!-- Background image -->
    <div
      v-if="s.bg_type === 'image' && s.bg_image"
      class="mb-absolute mb-inset-0"
      :style="bgImageStyle"
    ></div>

    <!-- Video background -->
    <video
      v-if="s.bg_type === 'video' && s.bg_video"
      class="mb-absolute mb-inset-0"
      :src="s.bg_video"
      autoplay
      loop
      muted
      playsinline
      style="width:100%;height:100%;object-fit:cover"
    ></video>
    <div
      v-if="s.bg_type === 'video' && !s.bg_video"
      class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center mb-bg-gray-900"
    >
      <div class="mb-text-center mb-text-gray-400">
        <div class="mb-text-3xl mb-mb-1">&#9654;</div>
        <div class="mb-text-xs">Video Background</div>
      </div>
    </div>

    <!-- Overlay -->
    <div
      v-if="s.overlay"
      class="mb-absolute mb-inset-0"
      :style="overlayStyle"
    ></div>

    <!-- Content -->
    <div class="mb-relative mb-flex mb-w-full" :style="contentFlexStyle">
      <div :style="contentBlockStyle">
        <component :is="s.title_tag || 'h1'" :style="titleStyle" style="margin:0 0 12px 0" data-olo-editable="title">{{ s.title || 'Hero Title' }}</component>
        <div :style="subtitleStyle" style="margin-bottom:24px" data-olo-editable="subtitle" data-olo-multiline>{{ s.subtitle || 'Subtitle goes here' }}</div>

        <!-- CTA buttons -->
        <div v-if="s.cta_text || s.cta2_text" class="mb-flex mb-gap-3 mb-flex-wrap" :style="ctaAlignStyle">
          <!-- CTA Primary -->
          <span
            v-if="s.cta_text"
            class="mb-inline-block mb-font-semibold mb-text-sm mb-cursor-pointer"
            :style="ctaStyle(1)"
          >
            <span data-olo-editable="cta_text">{{ s.cta_text }}</span>
          </span>
          <!-- CTA Secondary -->
          <span
            v-if="s.cta2_text"
            class="mb-inline-block mb-font-semibold mb-text-sm mb-cursor-pointer"
            :style="ctaStyle(2)"
          >
            <span data-olo-editable="cta2_text">{{ s.cta2_text }}</span>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  title: 'Benvenuto nel nostro sito',
  subtitle: 'Scopri qualcosa di straordinario',
  text_color: '#FFFFFF',
  min_height: '500px',
  content_max_width: '700',
  vertical_align: 'center',
  horizontal_align: 'center',
  text_align: 'center',
  padding_y: '80',
  padding_x: '30',
  bg_type: 'color',
  bg_color: 'var(--olo-color-primary, #6366F1)',
  bg_gradient_from: 'var(--olo-color-primary, #6366F1)',
  bg_gradient_to: '#8B5CF6',
  bg_gradient_angle: '135',
  bg_image: '',
  bg_video: '',
  bg_position: 'center',
  bg_size: 'cover',
  bg_fixed: false,
  overlay: false,
  overlay_color: '#000000',
  overlay_opacity: '50',
  overlay_gradient: false,
  overlay_gradient_to: 'transparent',
  overlay_gradient_angle: '180',
  cta_text: 'Inizia ora',
  cta_url: '#',
  cta_bg_color: '#FFFFFF',
  cta_text_color: '',
  cta_radius: '6',
  cta_size: '15',
  cta_style: 'filled',
  cta2_text: '',
  cta2_url: '#',
  cta2_bg_color: 'transparent',
  cta2_text_color: '#FFFFFF',
  cta2_style: 'outline',
  full_bleed: false,
  border_radius: '0',
  ...props.settings,
}));

/* ── Container style (background) ── */
const containerStyle = computed(() => {
  const st = {
    minHeight: s.value.min_height || '500px',
    color: s.value.text_color || '#FFFFFF',
    borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
    position: 'relative',
    display: 'flex',
  };

  const bgType = s.value.bg_type;
  if (bgType === 'color') {
    st.backgroundColor = s.value.bg_color || 'var(--olo-color-primary, #6366F1)';
  } else if (bgType === 'gradient') {
    const angle = parseInt(s.value.bg_gradient_angle) || 135;
    st.background = `linear-gradient(${angle}deg, ${s.value.bg_gradient_from || 'var(--olo-color-primary, #6366F1)'}, ${s.value.bg_gradient_to || '#8B5CF6'})`;
  } else if (bgType === 'image' && !s.value.bg_image) {
    st.backgroundColor = 'var(--olo-color-muted, #F3F4F6)';
  } else if (bgType === 'video') {
    st.backgroundColor = 'var(--olo-color-muted, #F3F4F6)';
  }

  return st;
});

/* ── Background image style ── */
const bgImageStyle = computed(() => ({
  backgroundImage: `url(${s.value.bg_image})`,
  backgroundPosition: s.value.bg_position || 'center',
  backgroundSize: s.value.bg_size || 'cover',
  backgroundRepeat: 'no-repeat',
  width: '100%',
  height: '100%',
}));

/* ── Overlay style ── */
const overlayStyle = computed(() => {
  const opacity = (parseInt(s.value.overlay_opacity) || 50) / 100;
  const color = s.value.overlay_color || '#000000';

  if (s.value.overlay_gradient) {
    const angle = parseInt(s.value.overlay_gradient_angle) || 180;
    const to = s.value.overlay_gradient_to || 'transparent';
    return {
      background: `linear-gradient(${angle}deg, ${color}, ${to})`,
      opacity,
      zIndex: 1,
    };
  }
  return {
    backgroundColor: color,
    opacity,
    zIndex: 1,
  };
});

/* ── Content flex alignment ── */
const vAlignMap = { top: 'flex-start', center: 'center', bottom: 'flex-end' };
const hAlignMap = { left: 'flex-start', center: 'center', right: 'flex-end' };

const contentFlexStyle = computed(() => ({
  flex: 1,
  alignItems: vAlignMap[s.value.vertical_align] || 'center',
  justifyContent: hAlignMap[s.value.horizontal_align] || 'center',
  padding: `${parseInt(s.value.padding_y) || 80}px ${parseInt(s.value.padding_x) || 30}px`,
  zIndex: 2,
}));

const contentBlockStyle = computed(() => ({
  maxWidth: (parseInt(s.value.content_max_width) || 700) + 'px',
  textAlign: s.value.text_align || 'center',
  width: '100%',
}));

/* ── Title style ── */
const titleStyle = computed(() => {
  const st = {};
  if (s.value.title_font_family) st.fontFamily = s.value.title_font_family;
  if (s.value.title_font_size) st.fontSize = parseInt(s.value.title_font_size) + 'px';
  else st.fontSize = '2rem';
  st.fontWeight = s.value.title_font_weight || '700';
  if (s.value.title_letter_spacing && parseFloat(s.value.title_letter_spacing) !== 0) {
    st.letterSpacing = parseFloat(s.value.title_letter_spacing) + 'px';
  }
  st.lineHeight = s.value.title_line_height || '1.2';
  if (s.value.title_text_transform && s.value.title_text_transform !== 'none') {
    st.textTransform = s.value.title_text_transform;
  }
  if (s.value.title_color) st.color = s.value.title_color;
  // Text shadow
  if (s.value.title_text_shadow) {
    if (s.value.title_text_shadow === 'custom') {
      const h = parseInt(s.value.title_text_shadow_h, 10) || 0;
      const v = parseInt(s.value.title_text_shadow_v, 10) || 0;
      const b = parseInt(s.value.title_text_shadow_blur, 10) || 0;
      const c = s.value.title_text_shadow_color || 'rgba(0,0,0,0.3)';
      st.textShadow = `${h}px ${v}px ${b}px ${c}`;
    } else {
      st.textShadow = s.value.title_text_shadow;
    }
  }
  return st;
});

/* ── Subtitle style ── */
const subtitleStyle = computed(() => {
  const st = { opacity: 0.9 };
  if (s.value.subtitle_font_size) st.fontSize = parseInt(s.value.subtitle_font_size) + 'px';
  else st.fontSize = '1.125rem';
  if (s.value.subtitle_font_weight) st.fontWeight = s.value.subtitle_font_weight;
  if (s.value.subtitle_letter_spacing && parseFloat(s.value.subtitle_letter_spacing) !== 0) {
    st.letterSpacing = parseFloat(s.value.subtitle_letter_spacing) + 'px';
  }
  if (s.value.subtitle_color) { st.color = s.value.subtitle_color; st.opacity = 1; }
  if (s.value.subtitle_max_width) st.maxWidth = parseInt(s.value.subtitle_max_width) + 'px';
  return st;
});

/* ── CTA alignment ── */
const ctaAlignStyle = computed(() => {
  const ta = s.value.text_align || 'center';
  return { justifyContent: hAlignMap[ta] || 'center' };
});

/* ── CTA button style ── */
function ctaStyle(n) {
  const prefix = n === 1 ? 'cta' : 'cta2';
  const style = s.value[prefix + '_style'] || (n === 1 ? 'filled' : 'outline');
  const bgColor = s.value[prefix + '_bg_color'] || (n === 1 ? '#FFFFFF' : 'transparent');
  const radius = ((v => isNaN(v) ? 6 : v)(parseInt(s.value.cta_radius))) + 'px';

  // Size from range (font-size in px, padding proportional)
  const fs = parseInt(s.value.cta_size) || 15;
  const padY = Math.round(fs * 0.8);
  const padX = Math.round(fs * 2.1);

  // Text color: filled → dark on light button; outline/ghost → hero text color (visible)
  let textColor;
  if (n === 1) {
    const explicit = s.value.cta_text_color;
    if (explicit) {
      textColor = explicit;
    } else if (style === 'filled') {
      textColor = s.value.bg_color || 'var(--olo-color-primary, #6366F1)';
    } else {
      textColor = s.value.text_color || '#FFFFFF';
    }
  } else {
    textColor = s.value.cta2_text_color || '#FFFFFF';
  }

  const base = {
    borderRadius: radius,
    fontWeight: '600',
    fontSize: fs + 'px',
    padding: `${padY}px ${padX}px`,
    textDecoration: 'none',
    transition: 'opacity .2s, transform .2s',
  };

  if (style === 'filled') {
    base.backgroundColor = bgColor;
    base.color = textColor;
    base.border = 'none';
  } else if (style === 'outline') {
    base.backgroundColor = 'transparent';
    base.color = textColor;
    base.border = `2px solid ${textColor}`;
  } else {
    // ghost
    base.backgroundColor = 'transparent';
    base.color = textColor;
    base.border = 'none';
  }

  return base;
}
</script>
