<template>
  <div class="olo-page-title-bar mb-relative mb-overflow-hidden" :style="wrapperStyle">
    <!-- Sfondo unificato (media_bg): video element (immagine/gradiente/colore vanno sul wrapper) -->
    <video
      v-if="isMediaVideo"
      class="mb-absolute mb-inset-0"
      :src="s.media_bg.video_url"
      autoplay loop muted playsinline
      style="width:100%;height:100%;object-fit:cover;z-index:0"
    ></video>
    <!-- Overlay scuro: media_bg immagine/video oppure legacy bg_image -->
    <div v-if="showOverlay" :style="overlayStyle" aria-hidden="true"></div>
    <div :style="innerStyle">
      <component :is="titleTag" :style="titleStyle">{{ titleText }}</component>
      <p v-if="s.subtitle" :style="subtitleStyle" data-olo-editable="subtitle">{{ s.subtitle }}</p>
      <nav v-if="s.show_breadcrumbs" :style="{ marginTop: '16px', fontSize: '13px', color: bcColor }">
        <span style="opacity:.7">{{ t('Home') }}</span>
        <span :style="{ margin: '0 6px', opacity: '.5' }">{{ s.breadcrumb_separator || '/' }}</span>
        <span>{{ t('Titolo pagina') }}</span>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => props.settings || {});

// ── Sfondo unificato (pannello media_bg) con fallback ai campi legacy (bg_image) ──
const hasMediaBg = computed(() => {
  const m = s.value.media_bg;
  return !!(m && typeof m === 'object' && m.type && m.type !== 'none');
});
const mediaBgStyle = computed(() => (hasMediaBg.value ? buildBgStyle(s.value.media_bg) : {}));
const isMediaVideo = computed(() => {
  const m = s.value.media_bg;
  return hasMediaBg.value && m.type === 'video' && !!m.video_url;
});
// Un'immagine di sfondo è presente se: media_bg immagine/video/gallery OPPURE legacy bg_image.
const hasBgImageLike = computed(() => {
  if (hasMediaBg.value) {
    const tp = s.value.media_bg.type;
    return tp === 'image' || tp === 'video' || tp === 'gallery';
  }
  return !!s.value.bg_image;
});

const titleTag = computed(() => {
  const valid = ['h1','h2','h3','h4','h5','h6','div','span'];
  return valid.includes(s.value.title_tag) ? s.value.title_tag : 'h1';
});
const titleText = computed(() => 'Titolo pagina');
// TOKEN-FIRST: barra titolo = superficie scura del brand; testo bianco in contrasto;
// neutri (breadcrumb/sottotitolo/bordo) → token tema
const titleColor = computed(() => s.value.title_color || 'var(--olo-color-primary-contrast, #FFFFFF)');
const titleSize = computed(() => Math.max(14, parseInt(s.value.title_size) || 36));
const titleWeight = computed(() => s.value.title_weight || '700');
const align = computed(() => ['left','center','right'].includes(s.value.title_align) ? s.value.title_align : 'center');

const bgColor = computed(() => s.value.bg_color || 'var(--olo-color-dark, #1F2937)');
const overlayOpacity = computed(() => Math.max(0, Math.min(100, parseInt(s.value.bg_overlay) || 60)));
const overlayColor = computed(() => s.value.bg_overlay_color || '#000000');
const minH = computed(() => Math.max(0, parseInt(s.value.min_height) || 200));
const padY = computed(() => Math.max(0, parseInt(s.value.padding_y) || 60));
const maxW = computed(() => Math.max(0, parseInt(s.value.content_width) || 1200));
const bcColor = computed(() => s.value.breadcrumb_color || 'var(--olo-color-text-faint, #9CA3AF)');

// Overlay attivo se c'è un media immagine/video (media_bg o legacy bg_image) e opacità > 0.
const showOverlay = computed(() => hasBgImageLike.value && overlayOpacity.value > 0);

const wrapperStyle = computed(() => {
  const st = {
    // Superficie di base (backdrop scuro del brand): resta dietro al media_bg.
    backgroundColor: bgColor.value,
    position: 'relative',
    minHeight: minH.value + 'px',
    display: 'flex',
    alignItems: 'center',
    textAlign: align.value,
  };
  if (hasMediaBg.value) {
    // media_bg (immagine/gradiente/colore/pattern…) sul wrapper, precedenza sul legacy.
    // Il video ha un layer <video> dedicato; qui restano eventuali dichiarazioni CSS.
    Object.assign(st, mediaBgStyle.value);
  } else if (s.value.bg_image) {
    // Fallback legacy: immagine di sfondo con posizione focal (bg_position).
    st.backgroundImage = `url(${s.value.bg_image})`;
    st.backgroundSize = s.value.bg_size || 'cover';
    st.backgroundPosition = s.value.bg_position || 'center center';
    st.backgroundRepeat = 'no-repeat';
  }
  if (s.value.border_bottom) {
    st.borderBottom = `1px solid ${s.value.border_color || 'var(--olo-color-border, #374151)'}`;
  }
  return st;
});

const overlayStyle = computed(() => ({
  position: 'absolute',
  inset: '0',
  zIndex: 1,
  background: overlayColor.value,
  opacity: overlayOpacity.value / 100,
  pointerEvents: 'none',
}));

const innerStyle = computed(() => ({
  position: 'relative',
  zIndex: 1,
  width: '100%',
  maxWidth: maxW.value + 'px',
  margin: '0 auto',
  padding: padY.value + 'px 20px',
}));

const titleStyle = computed(() => ({
  color: titleColor.value,
  fontSize: titleSize.value + 'px',
  fontWeight: titleWeight.value,
  margin: '0',
  lineHeight: '1.2',
}));

const subtitleStyle = computed(() => ({
  color: s.value.subtitle_color || 'var(--olo-color-text-soft, #D1D5DB)',
  fontSize: Math.max(12, parseInt(s.value.subtitle_size) || 16) + 'px',
  margin: '10px 0 0',
  opacity: '.85',
}));
</script>
