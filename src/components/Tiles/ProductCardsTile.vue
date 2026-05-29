<template>
  <div class="olo-pcards" :class="hoverClass" :style="gridStyle">
    <div
      v-for="(it, idx) in items"
      :key="idx"
      class="olo-pcards__card"
      :style="cardStyle"
    >
      <!-- TOP HALF -->
      <div class="olo-pcards__top" :style="topStyle(it)">
        <span v-if="it.letter" class="olo-pcards__letter" :style="letterStyle(it)">{{ it.letter }}</span>
        <div
          v-if="s.show_screenshot_label && it.screenshot_label"
          class="olo-pcards__screen-label"
          :style="screenLabelStyle"
        >{{ it.screenshot_label }}</div>
      </div>

      <!-- BOTTOM HALF -->
      <div class="olo-pcards__bottom" :style="{ padding: (s.card_padding || 28) + 'px', flex: 1, display: 'flex', flexDirection: 'column', gap: '14px' }">
        <div v-if="it.brand_label || (it.show_badge && it.badge_text)" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
          <span v-if="it.brand_label" :style="brandStyle(it)">{{ it.brand_label }}</span>
          <span v-if="it.show_badge && it.badge_text" :style="badgeStyle(it)">{{ it.badge_text }}</span>
        </div>

        <h3 v-if="it.title || it.title_accent" :style="titleStyle">
          <span v-if="it.title">{{ it.title }}</span><span v-if="it.title_accent" :style="{ fontStyle: it.title_accent_italic ? 'italic' : 'normal' }">{{ it.title_accent }}</span>
        </h3>

        <div v-if="it.description" :style="descStyle" v-html="it.description"></div>

        <a v-if="it.cta_text" :href="it.cta_url || '#'" :style="ctaStyle(it)">
          {{ it.cta_text }}<template v-if="s.cta_arrow"> →</template>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const R = (n) => ({ tl: n, tr: n, br: n, bl: n, linked: true });

const defaults = {
  columns: 5,
  gap: 24,
  items: [
    { letter: 'C', letter_color: '#b3261e', top_bg: { type: 'gradient', gradient_from: '#fdf2f2', gradient_to: '#fbe1e1', gradient_angle: 180 }, screenshot_label: 'SCREENSHOT · EDITOR LIVE', brand_label: 'OLOBUILD', brand_color: '#b3261e', show_badge: true, badge_text: 'GRATIS', badge_bg: '#0f172a', badge_color: '#ffffff', title: 'Co', title_accent: 'struisci', title_accent_italic: true, description: 'Page builder olonico. Alla pari dei top builder commerciali.', cta_text: 'SCOPRI OLOBUILD', cta_url: '#' },
  ],
  card_bg: { type: 'solid', color: '#ffffff' },
  card_color: '',
  card_radius: R(24),
  card_shadow: 'sm',
  card_padding: 28,
  top_aspect_ratio: '3/4',
  top_padding: 24,
  letter_font_family: 'serif',
  letter_size: 140,
  letter_italic: true,
  letter_align: 'center',
  show_screenshot_label: true,
  screenshot_label_color: '',
  brand_size: 13,
  brand_letter_spacing: 0.08,
  title_font_family: 'serif',
  title_size: 30,
  title_weight: '500',
  description_size: 15,
  cta_size: 12,
  cta_arrow: true,
  card_hover_effect: 'lift',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const SERIF = "'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif";
const SANS  = "'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";
const MONO  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";
const fmap  = { serif: SERIF, 'sans-serif': SANS, mono: MONO };

const SHADOW = {
  none: 'none',
  sm: '0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04)',
  md: '0 4px 12px rgba(0,0,0,0.10), 0 2px 4px rgba(0,0,0,0.06)',
  lg: '0 10px 30px rgba(0,0,0,0.12), 0 4px 8px rgba(0,0,0,0.08)',
  xl: '0 24px 60px rgba(0,0,0,0.18), 0 8px 16px rgba(0,0,0,0.10)',
};

const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);
const hoverClass = computed(() => 'olo-pcards-hover-' + (s.value.card_hover_effect || 'lift'));

function radiusToCss(r) {
  if (!r) return '0';
  if (typeof r === 'number') return r + 'px';
  const tl = r.tl ?? 0, tr = r.tr ?? 0, br = r.br ?? 0, bl = r.bl ?? 0;
  return `${tl}px ${tr}px ${br}px ${bl}px`;
}

function bgToCss(bg, fallback) {
  if (!bg || bg.type === 'none') return fallback || {};
  if (bg.type === 'solid') return { background: bg.color || (fallback?.background || '#ffffff') };
  if (bg.type === 'gradient') {
    const a = bg.gradient_angle ?? 180;
    return { background: `linear-gradient(${a}deg, ${bg.gradient_from || '#fff'}, ${bg.gradient_to || '#eee'})` };
  }
  if (bg.type === 'image' && bg.image_url) {
    return {
      backgroundImage: `url(${bg.image_url})`,
      backgroundSize: bg.image_size || 'cover',
      backgroundPosition: bg.image_position || 'center',
      backgroundRepeat: bg.image_repeat || 'no-repeat',
    };
  }
  return fallback || {};
}

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${s.value.columns || 5}, 1fr)`,
  gap: (s.value.gap || 24) + 'px',
}));

const cardStyle = computed(() => ({
  ...bgToCss(s.value.card_bg, { background: '#ffffff' }),
  color: resolveColor(s.value.card_color, TOKENS.text),
  borderRadius: radiusToCss(s.value.card_radius),
  boxShadow: SHADOW[s.value.card_shadow || 'sm'] || SHADOW.sm,
  overflow: 'hidden',
  display: 'flex',
  flexDirection: 'column',
  transition: 'transform .3s ease, box-shadow .3s ease',
}));

function topStyle(it) {
  return {
    ...bgToCss(it.top_bg, { background: '#f5f5f5' }),
    aspectRatio: s.value.top_aspect_ratio || '3/4',
    padding: (s.value.top_padding || 24) + 'px',
    position: 'relative',
    display: 'flex',
    alignItems: 'center',
    justifyContent: s.value.letter_align === 'left' ? 'flex-start' : (s.value.letter_align === 'right' ? 'flex-end' : 'center'),
  };
}

function letterStyle(it) {
  return {
    fontFamily: fmap[s.value.letter_font_family] || SERIF,
    fontSize: (s.value.letter_size || 140) + 'px',
    fontStyle: s.value.letter_italic ? 'italic' : 'normal',
    color: it.letter_color || '#0f172a',
    lineHeight: 1,
    fontWeight: 500,
  };
}

const screenLabelStyle = computed(() => {
  const labelC = resolveColor(s.value.screenshot_label_color, TOKENS.textFaint);
  return {
    position: 'absolute',
    left: (s.value.top_padding || 24) + 'px',
    right: (s.value.top_padding || 24) + 'px',
    bottom: (s.value.top_padding || 24) + 'px',
    border: `1px dashed color-mix(in srgb, ${labelC} 40%, transparent)`,
    borderRadius: '6px',
    padding: '8px 12px',
    textAlign: 'center',
    fontFamily: MONO,
    fontSize: '10px',
    letterSpacing: '0.1em',
    textTransform: 'uppercase',
    color: labelC,
  };
});

function brandStyle(it) {
  return {
    fontFamily: MONO,
    fontSize: (s.value.brand_size || 13) + 'px',
    letterSpacing: (s.value.brand_letter_spacing || 0.08) + 'em',
    textTransform: 'uppercase',
    color: it.brand_color || '#0f172a',
    fontWeight: 600,
  };
}

function badgeStyle(it) {
  return {
    display: 'inline-flex',
    alignItems: 'center',
    padding: '3px 10px',
    background: it.badge_bg || '#0f172a',
    color: resolveColor(it.badge_color, TOKENS.onPrimary),
    fontFamily: MONO,
    fontSize: '11px',
    letterSpacing: '0.06em',
    textTransform: 'uppercase',
    borderRadius: '4px',
    fontWeight: 600,
  };
}

const titleStyle = computed(() => ({
  fontFamily: fmap[s.value.title_font_family] || SERIF,
  fontSize: (s.value.title_size || 30) + 'px',
  fontWeight: s.value.title_weight || '500',
  color: resolveColor(s.value.card_color, TOKENS.text),
  margin: 0,
  lineHeight: 1.1,
  letterSpacing: '-0.01em',
}));

const descStyle = computed(() => ({
  fontFamily: SANS,
  fontSize: (s.value.description_size || 15) + 'px',
  lineHeight: 1.55,
  color: resolveColor(s.value.card_color, TOKENS.text),
  flex: 1,
}));

function ctaStyle(it) {
  return {
    fontFamily: MONO,
    fontSize: (s.value.cta_size || 12) + 'px',
    letterSpacing: '0.08em',
    textTransform: 'uppercase',
    color: it.brand_color || '#0f172a',
    fontWeight: 600,
    textDecoration: 'none',
    display: 'inline-flex',
    alignItems: 'center',
    gap: '6px',
    marginTop: 'auto',
  };
}
</script>

<style scoped>
.olo-pcards-hover-lift .olo-pcards__card:hover { transform: translateY(-6px); box-shadow: 0 14px 36px rgba(0,0,0,0.15) !important; }
.olo-pcards-hover-scale .olo-pcards__card:hover { transform: scale(1.03); z-index: 2; }
.olo-pcards-hover-tilt .olo-pcards__card { transform-style: preserve-3d; }
.olo-pcards-hover-tilt .olo-pcards__card:hover { transform: perspective(800px) rotateX(2deg) rotateY(-2deg) scale(1.02); }
</style>
