<template>
  <div class="mb-relative" :style="containerStyle">
    <!-- Content -->
    <div class="mb-relative mb-flex mb-w-full" :style="contentFlexStyle">
      <div :style="contentBlockStyle">
        <component :is="s.title_tag || 'h1'" :style="titleStyle" style="margin:0 0 12px 0" data-olo-editable="title" v-html="safeInline(s.title || 'Hero Title')"></component>
        <div :style="subtitleStyle" style="margin-bottom:24px" data-olo-editable="subtitle" data-olo-multiline v-html="safeInline(s.subtitle || 'Subtitle goes here')"></div>

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
  text_color: 'var(--olo-color-on-primary, #FFFFFF)',
  min_height: '500px',
  content_max_width: '700',
  vertical_align: 'center',
  horizontal_align: 'center',
  text_align: 'center',
  tile_padding: { top: 60, right: 20, bottom: 60, left: 20 },
  cta_text: 'Inizia ora',
  cta_url: '#',
  cta_bg_color: 'var(--olo-color-on-primary, #FFFFFF)',
  cta_text_color: '',
  cta_radius: { tl: 6, tr: 6, br: 6, bl: 6 },
  cta_size: '15',
  cta_style: 'filled',
  cta2_text: '',
  cta2_url: '#',
  cta2_bg_color: 'transparent',
  cta2_text_color: 'var(--olo-color-on-primary, #FFFFFF)',
  cta2_style: 'outline',
  full_bleed: false,
  ...props.settings,
}));

/* ── Sanitizer per il preview WYSIWYG: permette inline tag sicuri (br, strong, em,
   span, b, i, u, sub, sup, mark, small). Tutti gli altri tag vengono strippati. ── */
const ALLOWED_INLINE_RE = /<(?!\/?(br|strong|em|b|i|u|span|sub|sup|mark|small)(?:\s[^>]*)?\s*\/?>)[^>]*>/gi;
function safeInline(input) {
  if (input == null) return '';
  return String(input).replace(ALLOWED_INLINE_RE, '');
}

/* ── Container (sfondo gestito dal wrapper esterno via tile.style.bg) ── */
const containerStyle = computed(() => ({
  minHeight: s.value.min_height || '500px',
  color: s.value.text_color || 'var(--olo-color-on-primary, #FFFFFF)',
  display: 'flex',
}));

/* ── Content flex alignment ── */
const vAlignMap = { top: 'flex-start', center: 'center', bottom: 'flex-end' };
const hAlignMap = { left: 'flex-start', center: 'center', right: 'flex-end', justify: 'center' };

const contentFlexStyle = computed(() => {
  const tp = s.value.tile_padding || {};
  const t = parseInt(tp.top ?? 60);
  const r = parseInt(tp.right ?? 20);
  const b = parseInt(tp.bottom ?? 60);
  const l = parseInt(tp.left ?? 20);
  return {
    flex: 1,
    alignItems: vAlignMap[s.value.vertical_align] || 'center',
    justifyContent: hAlignMap[s.value.horizontal_align] || 'center',
    padding: `${t}px ${r}px ${b}px ${l}px`,
  };
});

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

/* ── CTA radius helper (oggetto {tl,tr,br,bl} → CSS string) ── */
function radiusCss(val) {
  if (val && typeof val === 'object') {
    const tl = parseInt(val.tl ?? 0) || 0;
    const tr = parseInt(val.tr ?? 0) || 0;
    const br = parseInt(val.br ?? 0) || 0;
    const bl = parseInt(val.bl ?? 0) || 0;
    return `${tl}px ${tr}px ${br}px ${bl}px`;
  }
  const n = parseInt(val);
  return isNaN(n) ? '6px' : `${n}px`;
}

/* ── CTA button style ── */
function ctaStyle(n) {
  const prefix = n === 1 ? 'cta' : 'cta2';
  const style = s.value[prefix + '_style'] || (n === 1 ? 'filled' : 'outline');
  const bgColor = s.value[prefix + '_bg_color'] || (n === 1 ? 'var(--olo-color-on-primary, #FFFFFF)' : 'transparent');
  const radius = radiusCss(s.value.cta_radius);

  const fs = parseInt(s.value.cta_size) || 15;
  const padY = Math.round(fs * 0.8);
  const padX = Math.round(fs * 2.1);

  let textColor;
  if (n === 1) {
    const explicit = s.value.cta_text_color;
    if (explicit) {
      textColor = explicit;
    } else if (style === 'filled') {
      textColor = 'var(--olo-color-primary, #e1474f)';
    } else {
      textColor = s.value.text_color || 'var(--olo-color-on-primary, #FFFFFF)';
    }
  } else {
    textColor = s.value.cta2_text_color || 'var(--olo-color-on-primary, #FFFFFF)';
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
    base.backgroundColor = 'transparent';
    base.color = textColor;
    base.border = 'none';
  }

  return base;
}
</script>
