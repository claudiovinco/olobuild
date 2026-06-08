<template>
  <div class="olo-ctab" :style="bannerStyle">
    <h2 v-if="s.headline || s.headline_accent" :style="headlineStyle">
      <span v-if="s.headline">{{ s.headline }}</span><template v-if="s.headline_accent"> <span :style="{ color: s.accent_color || 'var(--olo-color-primary, #e1474f)', fontStyle: s.headline_accent_italic ? 'italic' : 'normal' }">{{ s.headline_accent }}</span></template>
    </h2>
    <div v-if="s.layout !== 'split-2' && s.subtitle" :style="subtitleStyle" v-html="s.subtitle"></div>
    <div v-else-if="s.layout === 'split-2' && s.subtitle" :style="{ ...subtitleStyle, gridColumn: 1, marginTop: '8px' }" v-html="s.subtitle"></div>
    <div v-if="s.cta_text || s.cta2_text" class="olo-ctab__ctas" :style="ctasWrapStyle">
      <a v-if="s.cta_text" :href="s.cta_url || '#'" class="olo-ctab__cta" :style="ctaStyle">{{ s.cta_text }}</a>
      <a v-if="s.cta2_text" :href="s.cta2_url || '#'" class="olo-ctab__cta2" :style="cta2Style">{{ s.cta2_text }}</a>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const R = (n) => ({ tl: n, tr: n, br: n, bl: n, linked: true });

const defaults = {
  headline: 'Il tuo primo sito OLObuild è online',
  headline_accent: 'oggi pomeriggio.',
  headline_accent_italic: true,
  subtitle: 'Trial gratuita, niente carta. Tre passi, una sigaretta a testa di pausa.',
  cta_text: 'Inizia ora →', cta_url: '#',
  cta2_text: '', cta2_url: '#', cta2_bg: 'transparent', cta2_color: '#ffffff', cta2_border: 'rgba(255,255,255,.28)',
  bg: { type: 'solid', color: '#0f172a' },
  text_color: '#ffffff', accent_color: 'var(--olo-color-primary, #e1474f)', subtitle_color: '#9ca3af',
  cta_bg: 'var(--olo-color-primary, #e1474f)', cta_color: '#ffffff',
  cta_radius: R(999), cta_size: 15, cta_padding_y: 18, cta_padding_x: 32,
  headline_font_family: 'serif', headline_size: 36, headline_weight: '400',
  subtitle_size: 14,
  layout: 'split-3', ratio: '1.4fr 1fr auto', gap: 40, vertical_align: 'center',
  banner_radius: R(20), banner_padding: 40,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const SERIF = "'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif";
const SANS  = "'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";
const MONO  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
const fmap  = { serif: SERIF, 'sans-serif': SANS, mono: MONO };

function radiusToCss(r) {
  if (!r) return '0';
  if (typeof r === 'number') return r + 'px';
  return `${r.tl ?? 0}px ${r.tr ?? 0}px ${r.br ?? 0}px ${r.bl ?? 0}px`;
}

function bgToCss(bg) {
  if (!bg || bg.type === 'none') return '#0f172a';
  if (bg.type === 'solid') return bg.color || '#0f172a';
  if (bg.type === 'gradient') {
    return `linear-gradient(${bg.gradient_angle ?? 180}deg, ${bg.gradient_from || '#0f172a'}, ${bg.gradient_to || '#000'})`;
  }
  if (bg.type === 'image' && bg.image_url) {
    return `url(${bg.image_url}) ${bg.image_position || 'center'} / ${bg.image_size || 'cover'} no-repeat`;
  }
  return bg.color || '#0f172a';
}

const gridCols = computed(() => {
  if (s.value.layout === 'split-3') return s.value.ratio || '1.4fr 1fr auto';
  if (s.value.layout === 'split-2') return '1fr auto';
  return '1fr';
});

const bannerStyle = computed(() => ({
  background: bgToCss(s.value.bg),
  borderRadius: radiusToCss(s.value.banner_radius),
  padding: (s.value.banner_padding || 40) + 'px',
  color: s.value.text_color || '#ffffff',
  display: 'grid',
  gridTemplateColumns: gridCols.value,
  gap: (s.value.gap || 40) + 'px',
  alignItems: s.value.vertical_align === 'start' ? 'flex-start' : (s.value.vertical_align === 'end' ? 'flex-end' : 'center'),
  textAlign: s.value.layout === 'stack' ? 'center' : 'left',
  justifyItems: s.value.layout === 'stack' ? 'center' : 'stretch',
  transition: 'border-radius .3s ease',
}));

const headlineStyle = computed(() => ({
  fontFamily: fmap[s.value.headline_font_family] || SERIF,
  fontSize: (s.value.headline_size || 36) + 'px',
  fontWeight: s.value.headline_weight || '400',
  lineHeight: 1.15,
  letterSpacing: '-0.01em',
  color: s.value.text_color || '#ffffff',
  margin: 0,
}));

const subtitleStyle = computed(() => ({
  fontFamily: SANS,
  fontSize: (s.value.subtitle_size || 14) + 'px',
  lineHeight: 1.5,
  color: s.value.subtitle_color || '#9ca3af',
}));

const ctaStyle = computed(() => ({
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  padding: `${s.value.cta_padding_y || 18}px ${s.value.cta_padding_x || 32}px`,
  background: s.value.cta_bg || 'var(--olo-color-primary, #e1474f)',
  color: s.value.cta_color || '#ffffff',
  borderRadius: radiusToCss(s.value.cta_radius),
  fontFamily: SANS,
  fontSize: (s.value.cta_size || 15) + 'px',
  fontWeight: 600,
  textDecoration: 'none',
  whiteSpace: 'nowrap',
  transition: 'transform .2s ease, background .2s, color .2s',
}));

const ctasWrapStyle = computed(() => ({
  display: 'inline-flex',
  gap: '12px',
  flexWrap: 'wrap',
  alignItems: 'center',
  justifyContent: s.value.layout === 'stack' ? 'center' : 'flex-start',
}));

const cta2Style = computed(() => ({
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  padding: `${s.value.cta_padding_y || 18}px ${s.value.cta_padding_x || 32}px`,
  background: s.value.cta2_bg || 'transparent',
  color: s.value.cta2_color || '#ffffff',
  border: s.value.cta2_border ? `1px solid ${s.value.cta2_border}` : 'none',
  borderRadius: radiusToCss(s.value.cta_radius),
  fontFamily: SANS,
  fontSize: (s.value.cta_size || 15) + 'px',
  fontWeight: 600,
  textDecoration: 'none',
  whiteSpace: 'nowrap',
  transition: 'transform .2s ease, background .2s, color .2s, border-color .2s',
}));
</script>

<style scoped>
.olo-ctab__cta:hover { transform: translateY(-1px); }
.olo-ctab__cta:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
