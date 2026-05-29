<template>
  <div
    class="olo-panel-preview"
    :style="cardStyle"
  >
    <!-- Media: video > image > nothing -->
    <div
      v-if="hasMedia"
      class="olo-panel-media"
      :style="mediaStyle"
    >
      <video
        v-if="s.media_type === 'video' && s.video"
        :src="s.video"
        :poster="s.video_poster || ''"
        :muted="!!s.video_muted"
        :loop="!!s.video_loop"
        :autoplay="!!s.video_autoplay"
        :controls="!!s.video_controls"
        playsinline
        :style="mediaInnerStyle"
      />
      <img
        v-else-if="s.media_type === 'image' && s.image"
        :src="s.image"
        :alt="s.title || ''"
        :style="mediaInnerStyle"
      />
    </div>

    <!-- Card body -->
    <div class="olo-panel-body" :style="bodyStyle">
      <component
        :is="s.title_element || 'h3'"
        v-if="s.title"
        class="olo-panel-title"
        :style="titleStyle"
        data-olo-editable="title"
      >{{ s.title }}</component>

      <div
        v-if="s.meta"
        class="olo-panel-meta"
        :style="metaStyle"
        data-olo-editable="meta"
        v-text="s.meta"
      ></div>

      <div
        v-if="s.content || !s.title"
        class="olo-panel-content"
        :style="contentStyle"
        data-olo-editable="content"
        data-olo-richtext
        data-olo-multiline
        v-html="contentHtml"
      ></div>

      <div v-if="s.link_url && s.link_label" class="olo-panel-readmore" :style="linkStyle">
        {{ s.link_label }} &rarr;
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  style: 'default',
  title: '',
  meta: '',
  content: 'Panel content goes here.',
  media_type: 'image',
  image: '',
  image_ratio: 'auto',
  image_height: '',
  image_fit: 'cover',
  image_zoom: false,
  media_padding: { top: 0, right: 0, bottom: 0, left: 0 },
  text_align: 'left',
  title_size: '',
  title_weight: '',
  title_color: '',
  meta_size: '',
  meta_color: '',
  content_size: '',
  content_color: '',
  link_label: '',
  link_color: '',
  hover_image: '',
  hover_video: '',
  video: '',
  video_autoplay: true,
  video_loop: true,
  video_muted: true,
  video_controls: false,
  video_poster: '',
  link_url: '',
  title_element: 'h3',
  card_padding: { top: 20, right: 20, bottom: 20, left: 20 },
  shadow: 'none',
  border_radius: '0',
  card_radius: '0',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const contentHtml = computed(() => s.value.content || '<span class="olo-editable-ph">Panel content goes here.</span>');

const hasMedia = computed(() => {
  if (s.value.media_type === 'video') return !!s.value.video;
  if (s.value.media_type === 'image') return !!s.value.image;
  return false;
});

// Card background by style key — match UIkit card style intent
const styleBgs = {
  default:   'var(--olo-color-surface, #ffffff)',
  primary:   'var(--olo-color-primary, #e1474f)',
  secondary: 'var(--olo-color-surface-alt, #f3f4f6)',
  hover:     'var(--olo-color-surface, #ffffff)',
};
const styleColors = {
  default:   'inherit',
  primary:   'var(--olo-color-on-primary, #ffffff)',
  secondary: 'inherit',
  hover:     'inherit',
};

const cardRadiusCss = computed(() => {
  const r = parseInt(s.value.card_radius) || 0;
  return r > 0 ? r + 'px' : '0';
});

const mediaRadiusCss = computed(() => {
  const r = s.value.border_radius;
  if (r && typeof r === 'object') {
    const tl = parseInt(r.tl) || 0, tr = parseInt(r.tr) || 0, br = parseInt(r.br) || 0, bl = parseInt(r.bl) || 0;
    if (!tl && !tr && !br && !bl) return '0';
    return `${tl}px ${tr}px ${br}px ${bl}px`;
  }
  return (parseInt(r) || 0) + 'px';
});

const shadowCss = computed(() => {
  const map = {
    sm: '0 4px 12px rgba(0,0,0,.08)',
    md: '0 8px 30px rgba(0,0,0,.12)',
    lg: '0 16px 48px rgba(0,0,0,.18)',
    xl: '0 24px 64px rgba(0,0,0,.22)',
  };
  const v = s.value.shadow || 'none';
  if (v === 'custom') {
    const h = parseInt(s.value.shadow_h) || 0;
    const vv = parseInt(s.value.shadow_v) || 4;
    const b = parseInt(s.value.shadow_blur) || 10;
    const sp = parseInt(s.value.shadow_spread) || 0;
    const c = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${vv}px ${b}px ${sp}px ${c}`;
  }
  return map[v] || 'none';
});

const paddingCss = computed(() => {
  const p = s.value.card_padding;
  if (p && typeof p === 'object') {
    return `${parseInt(p.top)||0}px ${parseInt(p.right)||0}px ${parseInt(p.bottom)||0}px ${parseInt(p.left)||0}px`;
  }
  return (parseInt(p) || 20) + 'px';
});

// Negative margin for media to span card edges
const mediaMarginCss = computed(() => {
  const p = s.value.card_padding;
  let t = 20, r = 20, l = 20;
  if (p && typeof p === 'object') {
    t = parseInt(p.top) || 20;
    r = parseInt(p.right) || 20;
    l = parseInt(p.left) || 20;
  } else if (p) {
    t = r = l = parseInt(p) || 20;
  }
  return `-${t}px -${r}px 16px -${l}px`;
});

const mediaInset = computed(() => {
  const r = s.value.border_radius;
  let hasRadius = false;
  if (r && typeof r === 'object') {
    hasRadius = (parseInt(r.tl) || 0) > 0 || (parseInt(r.tr) || 0) > 0 || (parseInt(r.br) || 0) > 0 || (parseInt(r.bl) || 0) > 0;
  } else {
    hasRadius = (parseInt(r) || 0) > 0;
  }
  const p = s.value.media_padding;
  let hasPad = false;
  if (p && typeof p === 'object') {
    hasPad = (parseInt(p.top) || 0) > 0 || (parseInt(p.right) || 0) > 0 || (parseInt(p.bottom) || 0) > 0 || (parseInt(p.left) || 0) > 0;
  }
  return hasRadius || hasPad;
});

const cardStyle = computed(() => ({
  background: styleBgs[s.value.style] || styleBgs.default,
  color: styleColors[s.value.style] || 'inherit',
  borderRadius: cardRadiusCss.value,
  boxShadow: shadowCss.value,
  border: s.value.style === 'default' ? '1px solid var(--olo-color-border, #E5E7EB)' : 'none',
  overflow: 'hidden',
  minHeight: '80px',
  padding: paddingCss.value,
  textAlign: s.value.text_align || 'left',
  transition: 'transform 0.35s cubic-bezier(.4,0,.2,1), box-shadow 0.35s ease',
  position: 'relative',
}));

const mediaPaddingCss = computed(() => {
  const p = s.value.media_padding;
  if (p && typeof p === 'object') {
    const t = parseInt(p.top) || 0, r = parseInt(p.right) || 0, b = parseInt(p.bottom) || 0, l = parseInt(p.left) || 0;
    if (!t && !r && !b && !l) return '0';
    return `${t}px ${r}px ${b}px ${l}px`;
  }
  return (parseInt(p) || 0) + 'px';
});

const mediaStyle = computed(() => {
  const ratio = s.value.image_ratio;
  const h = parseInt(s.value.image_height) || 0;
  // When media is "inset" (has radius or padding), don't pull it to card edges — keep it inside the card padding so radius is visible
  const margin = mediaInset.value ? '0 0 16px 0' : mediaMarginCss.value;
  const style = {
    position: 'relative',
    overflow: 'hidden',
    display: 'block',
    width: 'auto',
    margin: margin,
    padding: mediaPaddingCss.value,
    borderRadius: mediaRadiusCss.value,
    boxSizing: 'border-box',
  };
  if (ratio && ratio !== 'auto') {
    style.aspectRatio = ratio.replace('/', ' / ');
  } else if (h > 0) {
    style.height = h + 'px';
  } else {
    style.height = '180px';
  }
  return style;
});

const mediaInnerStyle = computed(() => ({
  width: '100%',
  height: '100%',
  objectFit: s.value.image_fit || 'cover',
  display: 'block',
  transition: 'transform 0.5s cubic-bezier(.4,0,.2,1)',
}));

const bodyStyle = computed(() => ({
  display: 'flex',
  flexDirection: 'column',
  gap: '8px',
}));

const titleStyle = computed(() => {
  const sz = parseInt(s.value.title_size) || 0;
  const w = s.value.title_weight || '';
  const c = s.value.title_color || '';
  return {
    fontSize: sz > 0 ? sz + 'px' : '1.15em',
    fontWeight: w || '700',
    lineHeight: '1.3',
    margin: 0,
    color: c || (s.value.style === 'primary' ? '#fff' : 'inherit'),
  };
});

const metaStyle = computed(() => {
  const sz = parseInt(s.value.meta_size) || 0;
  const c = s.value.meta_color || '';
  return {
    fontSize: sz > 0 ? sz + 'px' : '0.8em',
    opacity: c ? 1 : 0.7,
    margin: 0,
    color: c || 'inherit',
  };
});

const contentStyle = computed(() => {
  const sz = parseInt(s.value.content_size) || 0;
  const c = s.value.content_color || '';
  return {
    fontSize: sz > 0 ? sz + 'px' : '0.9em',
    lineHeight: '1.55',
    color: c || (s.value.style === 'primary' ? 'rgba(255,255,255,.9)' : 'inherit'),
    opacity: c ? 1 : (s.value.style === 'primary' ? 1 : 0.85),
  };
});

const linkStyle = computed(() => ({
  marginTop: '8px',
  fontSize: '0.85em',
  fontWeight: '600',
  color: s.value.link_color || (s.value.style === 'primary' ? '#fff' : 'var(--olo-color-primary, #e1474f)'),
}));
</script>

<style scoped>
.olo-panel-preview:hover img,
.olo-panel-preview:hover video {
  /* zoom è applicato solo se image_zoom è true via JS check below */
}
</style>
