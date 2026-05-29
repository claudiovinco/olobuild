<template>
  <div class="mb-overflow-hidden" :style="tileStyle">
    <!-- Photo -->
    <div :style="photoWrapStyle">
      <div :style="photoOuterStyle">
        <div :style="photoInnerStyle">
          <img v-if="s.photo" :src="s.photo" :alt="s.name" style="width:100%;height:100%;object-fit:cover;display:block" />
          <div v-else class="olo-team-photo-ph" :style="photoPlaceholderStyle">
            <span :style="{ width: (photoSize * 0.4) + 'px', height: (photoSize * 0.4) + 'px', display: 'inline-flex' }" v-html="avatarPlaceholderSvg"></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Gap -->
    <div :style="{ height: photoGap + 'px' }"></div>

    <!-- Info box -->
    <div :style="infoWrapStyle">
      <div :style="infoStyle">
        <h3 :style="nameStyle" data-olo-editable="name">{{ s.name || 'Nome' }}</h3>
        <div :style="roleStyle" data-olo-editable="role">{{ s.role || 'Ruolo' }}</div>
        <div v-if="s.bio" :style="{ ...bioStyle, whiteSpace: 'pre-wrap' }" data-olo-editable="bio" data-olo-multiline>{{ s.bio }}</div>
        <div v-if="s.link_url" :style="linkStyle" data-olo-editable="link_text">
          {{ s.link_text || 'Profilo' }} &rarr;
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

// Default colore = '' ⇒ risolti token-first a runtime (allineato a config team.js)
const s = computed(() => ({
  photo: '', hover_image: '', hover_video: '',
  name: 'Jane Smith', role: 'Lead Designer',
  bio: 'Appassionata di creare esperienze utente eccellenti.',
  link_text: 'Profilo', link_url: '',
  photo_size: '120', photo_shape: 'circle', photo_radius: '12',
  photo_border_width: '3', photo_border_color: '',
  photo_shadow: 'md', photo_gap: '12',
  info_bg_color: '', info_text_color: '', info_padding: '24',
  info_width: '100', info_margin: '0',
  info_radius: '16', info_border_width: '0', info_border_color: '',
  info_align: 'center',
  name_size: '20', name_weight: '600',
  role_size: '14', role_color: '', bio_size: '14',
  bg_color: '', tile_padding: '16',
  border_radius: '16', border_width: '0', border_color: '',
  ...props.settings,
}));

const photoSize = computed(() => parseInt(s.value.photo_size) || 120);
// Placeholder foto = icona SVG "user" (no più emoji)
const avatarPlaceholderSvg = computed(() => iconsSvg['user'] || iconsSvg['users'] || '');
const photoGap = computed(() => {
  const v = parseInt(s.value.photo_gap);
  return isNaN(v) ? 12 : Math.max(v, 0);
});
const photoBw = computed(() => parseInt(s.value.photo_border_width) || 0);

const shadowMap = { none: 'none', sm: '0 2px 6px rgba(0,0,0,.2)', md: '0 4px 12px rgba(0,0,0,.3)', lg: '0 8px 24px rgba(0,0,0,.4)' };

const tileStyle = computed(() => {
  const bw = parseInt(s.value.border_width) || 0;
  const tp = parseInt(s.value.tile_padding) || 0;
  const st = {
    borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
    background: s.value.bg_color || 'transparent',
    padding: tp + 'px',
    minHeight: '80px',
  };
  if (bw > 0) st.border = `${bw}px solid ${resolveColor(s.value.border_color, TOKENS.border)}`;
  return st;
});

const photoWrapStyle = computed(() => ({
  display: 'flex',
  justifyContent: s.value.info_align === 'left' ? 'flex-start' : s.value.info_align === 'right' ? 'flex-end' : 'center',
}));

const hexClip = 'polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%)';

const photoOuterStyle = computed(() => {
  const shape = s.value.photo_shape;
  const bw = photoBw.value;
  const outerSize = photoSize.value + bw * 2;
  const st = {
    width: outerSize + 'px', height: outerSize + 'px', flexShrink: 0,
    display: 'flex', alignItems: 'center', justifyContent: 'center',
  };
  if (shape === 'hexagon') {
    st.clipPath = hexClip;
    st.background = bw > 0 ? resolveColor(s.value.photo_border_color, TOKENS.onPrimary) : 'transparent';
  } else {
    st.background = bw > 0 ? resolveColor(s.value.photo_border_color, TOKENS.onPrimary) : 'transparent';
    if (shape === 'circle') st.borderRadius = '50%';
    else if (shape === 'rounded') st.borderRadius = ((v => isNaN(v) ? 12 : v)(parseInt(s.value.photo_radius))) + 'px';
    else st.borderRadius = '0';
    const sh = shadowMap[s.value.photo_shadow];
    if (sh && sh !== 'none') st.filter = `drop-shadow(${sh})`;
  }
  return st;
});

const photoInnerStyle = computed(() => {
  const shape = s.value.photo_shape;
  const st = {
    width: photoSize.value + 'px', height: photoSize.value + 'px',
    overflow: 'hidden', flexShrink: 0,
  };
  if (shape === 'hexagon') st.clipPath = hexClip;
  else if (shape === 'circle') st.borderRadius = '50%';
  else if (shape === 'rounded') st.borderRadius = Math.max(((v => isNaN(v) ? 12 : v)(parseInt(s.value.photo_radius))) - photoBw.value, 0) + 'px';
  else st.borderRadius = '0';
  return st;
});

const photoPlaceholderStyle = computed(() => ({
  width: '100%', height: '100%', background: TOKENS.surfaceAlt, color: TOKENS.textFaint,
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  fontSize: (photoSize.value * 0.4) + 'px',
}));

const infoWrapStyle = computed(() => {
  const m = parseInt(s.value.info_margin) || 0;
  return {
    display: 'flex',
    justifyContent: s.value.info_align === 'left' ? 'flex-start' : s.value.info_align === 'right' ? 'flex-end' : 'center',
    padding: m > 0 ? `0 ${m}px ${m}px` : '0',
  };
});

const infoStyle = computed(() => {
  const pad = parseInt(s.value.info_padding) || 24;
  const ibw = parseInt(s.value.info_border_width) || 0;
  const w = parseInt(s.value.info_width) || 100;
  const st = {
    padding: pad + 'px',
    textAlign: s.value.info_align || 'center',
    color: resolveColor(s.value.info_text_color, TOKENS.onPrimary),
    borderRadius: (parseInt(s.value.info_radius) || 0) + 'px',
    width: w + '%',
    background: resolveColor(s.value.info_bg_color, TOKENS.primary),
  };
  if (ibw > 0) st.border = `${ibw}px solid ${resolveColor(s.value.info_border_color, TOKENS.border)}`;
  return st;
});

const nameStyle = computed(() => ({
  fontSize: (parseInt(s.value.name_size) || 20) + 'px',
  fontWeight: s.value.name_weight || '600',
  margin: '0 0 4px', lineHeight: '1.3',
}));

const roleStyle = computed(() => {
  const rc = s.value.role_color || '';
  return {
    fontSize: (parseInt(s.value.role_size) || 14) + 'px',
    color: rc || undefined,
    opacity: rc ? 1 : 0.75,
    marginBottom: '10px',
  };
});

const bioStyle = computed(() => ({
  fontSize: (parseInt(s.value.bio_size) || 14) + 'px',
  opacity: 0.8, lineHeight: '1.5', margin: '0',
}));

const linkStyle = computed(() => ({
  display: 'inline-block', marginTop: '12px',
  fontWeight: '500', fontSize: '14px', cursor: 'pointer',
  opacity: 0.9,
}));
</script>

<style scoped>
.olo-team-photo-ph :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
</style>
