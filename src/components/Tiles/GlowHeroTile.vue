<template>
  <section class="olo-glowhero glw" :style="rootStyle">
    <span class="glw-glow" :style="glowStyle"></span>
    <div class="glw-in" :style="inStyle">
      <div v-if="words.length" class="glw-eyebrow" :class="{ 'glw-eyebrow--nodot': !s.eyebrow_dots }" :style="eyebrowStyle">
        <span v-for="(w, i) in words" :key="i" :style="eyebrowSpanStyle">{{ w }}</span>
      </div>
      <h1 class="glw-h" :style="hStyle">
        <span
          v-for="(ln, i) in visibleLines"
          :key="i"
          class="ln"
          :class="lnClass(ln.mode)"
          :style="lnStyle(ln.mode)"
        >{{ ln.text }}</span>
      </h1>
      <div class="glw-bottom" :style="bottomStyle">
        <p v-if="s.subhead" class="glw-sub" :style="subStyle">{{ s.subhead }}</p>
        <div class="glw-side" :style="sideStyle">
          <div v-if="s.cta1_text || s.cta2_text" class="glw-cta" :style="{ display: 'flex', gap: '13px', flexWrap: 'wrap' }">
            <a v-if="s.cta1_text" class="glw-btn glw-btn--solid" :style="solidStyle" :href="s.cta1_url || '#'">{{ s.cta1_text }}
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="width:17px;height:17px"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <a v-if="s.cta2_text" class="glw-btn glw-btn--ghost" :style="ghostBtnStyle" :href="s.cta2_url || '#'">{{ s.cta2_text }}</a>
          </div>
          <span v-if="s.show_scroll && s.scroll_text" class="glw-scroll" :style="scrollStyle"><span class="ln-deco" :style="scrollLnStyle"></span>{{ s.scroll_text }}</span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  eyebrow: 'Independent studio | Est. 2015 | Milan / everywhere',
  lines: [
    { text: 'Design with', mode: '' },
    { text: 'a point', mode: 'accent' },
    { text: 'of view.', mode: 'outline' },
  ],
  uppercase: true,
  eyebrow_dots: true,
  subhead: "We build brands, identities and digital products for people who'd rather stand out than blend in.",
  cta1_text: '', cta1_url: '#', cta2_text: '', cta2_url: '#',
  scroll_text: 'Scroll to see the work', show_scroll: true,
  bg_color: '#0a0a0c', accent: '', accent2: '', accent_on: '#0a0a0c',
  text_color: '#ece9e3', sub_color: '#9b988f', eyebrow_color: '#9b988f',
  glow_color: 'rgba(244,162,59,0.18)', glow_w: 760, glow_h: 560, glow_blur: 100, glow_x: 50, glow_y: 20,
  h_size_min: 54, h_size_vw: 12, h_size_max: 180, h_line_height: 0.86, stroke_width: 2,
  align: 'left', max_width: 1240, min_height: 100, bottom_split: true,
  // Spaziatura — override GATED del padding responsive del contenitore (clamp). No-op coi default.
  pad_custom: false,
  content_padding: { top: 96, right: 0, bottom: 96, left: 0 },
  // Forma — raggio dei pulsanti CTA (pill). No-op coi default (999 = pill attuale).
  btn_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
  // KIT standard OLObuild — sfondo completo / ombra / bordo (no-op coi default)
  bg: { type: 'none' },
  shadow: 'none',
  border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
  border_hover: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' },
  border_hover_duration: 300,
  border_effect: 'none',
  border_effect_intensity: 'medium',
  border_effect_color2: '',
  border_effect_angle: 135,
  border_effect_speed: 4,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const DISP = "var(--olo-font-family-heading, 'Syne',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";

const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #f4a23b)');
const accent2 = computed(() => s.value.accent2 || 'var(--olo-color-secondary, #4be0ff)');
const accOn = computed(() => s.value.accent_on || '#0a0a0c');
const txt = computed(() => s.value.text_color || '#ece9e3');
const sub = computed(() => s.value.sub_color || '#9b988f');

const words = computed(() => String(s.value.eyebrow || '').split('|').map(w => w.trim()).filter(w => w.length));
const visibleLines = computed(() => (Array.isArray(s.value.lines) ? s.value.lines : []).filter(ln => ln && String(ln.text || '').length));

const align = computed(() => (s.value.align === 'center' ? 'center' : 'left'));
const alignI = computed(() => (align.value === 'center' ? 'center' : 'flex-start'));
const split = computed(() => !!s.value.bottom_split);

// ── Spaziatura: override GATED del padding responsive (clamp) del contenitore — parità PHP ──
const padDecl = computed(() => {
  const cp = s.value.content_padding;
  if (!s.value.pad_custom || !cp || typeof cp !== 'object') return 'clamp(96px,14vh,140px) 0';
  const pt = parseInt(cp.top ?? 0, 10) || 0;
  const pr = parseInt(cp.right ?? 0, 10) || 0;
  const pb = parseInt(cp.bottom ?? 0, 10) || 0;
  const pl = parseInt(cp.left ?? 0, 10) || 0;
  return `${pt}px ${pr}px ${pb}px ${pl}px`;
});

// ── Forma: raggio pulsanti CTA (pill) — parità con build_border_radius_css PHP ──
const btnRadius = computed(() => {
  const r = s.value.btn_radius;
  if (!r || typeof r !== 'object') return '999px';
  const tl = parseInt(r.tl ?? 0, 10) || 0;
  const tr = parseInt(r.tr ?? 0, 10) || 0;
  const br = parseInt(r.br ?? 0, 10) || 0;
  const bl = parseInt(r.bl ?? 0, 10) || 0;
  if (!tl && !tr && !br && !bl) return '999px';
  return `${tl}px ${tr}px ${br}px ${bl}px`;
});

// ── KIT standard: sfondo completo (override del bg solo se valorizzato) ──
const kitBgStyle = computed(() => {
  const bg = s.value.bg;
  if (!bg || !bg.type || bg.type === 'none') return {};
  return buildBgStyle(bg);
});

// ── KIT standard: ombra (preset/custom) — parità con build_shadow_decl PHP ──
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};
const kitShadow = computed(() => {
  const p = s.value.shadow || 'none';
  if (p === 'none' || p === '') return '';
  if (p === 'custom') {
    const h = parseInt(s.value.shadow_h ?? 0, 10) || 0;
    const v = parseInt(s.value.shadow_v ?? 4, 10) || 0;
    const blur = Math.max(0, parseInt(s.value.shadow_blur ?? 10, 10) || 0);
    const spread = parseInt(s.value.shadow_spread ?? 0, 10) || 0;
    const color = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  return SHADOW_MAP[p] || '';
});

// ── KIT standard: bordo base — parità con build_border_css PHP (no-op coi default) ──
const kitBorder = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return {};
  const tp = Math.max(0, parseInt(b.top ?? 0, 10) || 0);
  const rt = Math.max(0, parseInt(b.right ?? 0, 10) || 0);
  const bt = Math.max(0, parseInt(b.bottom ?? 0, 10) || 0);
  const lf = Math.max(0, parseInt(b.left ?? 0, 10) || 0);
  if (tp === 0 && rt === 0 && bt === 0 && lf === 0) return {};
  const style = b.style || 'solid';
  const color = b.color || 'currentColor';
  if (tp === rt && rt === bt && bt === lf) {
    return { border: `${tp}px ${style} ${color}` };
  }
  const st = {};
  if (tp) st.borderTop = `${tp}px ${style} ${color}`;
  if (rt) st.borderRight = `${rt}px ${style} ${color}`;
  if (bt) st.borderBottom = `${bt}px ${style} ${color}`;
  if (lf) st.borderLeft = `${lf}px ${style} ${color}`;
  return st;
});

const rootStyle = computed(() => ({
  position: 'relative', overflow: 'hidden', minHeight: Math.max(40, Math.min(100, Number(s.value.min_height) || 100)) + 'vh',
  display: 'flex', flexDirection: 'column', justifyContent: 'center', background: s.value.bg_color || '#0a0a0c',
  color: txt.value, fontFamily: SANS, padding: padDecl.value, '--glw-accent': accent.value,
  ...kitBgStyle.value,
  ...(kitShadow.value ? { boxShadow: kitShadow.value } : {}),
  ...kitBorder.value,
}));
const glowStyle = computed(() => ({
  position: 'absolute',
  top: Math.max(-50, Math.min(100, Number(s.value.glow_y) || 0)) + '%',
  left: Math.max(0, Math.min(100, Number(s.value.glow_x) || 0)) + '%',
  transform: 'translate(-50%,-30%)', width: (Number(s.value.glow_w) || 760) + 'px', height: (Number(s.value.glow_h) || 560) + 'px',
  borderRadius: '50%', filter: 'blur(' + (Number(s.value.glow_blur) || 0) + 'px)', pointerEvents: 'none',
  background: 'radial-gradient(circle, ' + (s.value.glow_color || 'rgba(244,162,59,0.18)') + ', transparent 70%)', zIndex: 0,
}));
const inStyle = computed(() => ({
  position: 'relative', zIndex: 2, width: '100%', maxWidth: (Number(s.value.max_width) || 1240) + 'px', margin: '0 auto',
  padding: '0 28px', display: 'flex', flexDirection: 'column', alignItems: alignI.value, textAlign: align.value === 'center' ? 'center' : 'left',
}));
const eyebrowStyle = computed(() => ({ display: 'flex', gap: '26px', flexWrap: 'wrap', justifyContent: alignI.value, marginBottom: '34px', color: s.value.eyebrow_color || '#9b988f', fontSize: '13px', letterSpacing: '.04em' }));
const eyebrowSpanStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: s.value.eyebrow_dots ? '9px' : '0', '--glw-dot': accent.value }));
const hStyle = computed(() => ({
  fontFamily: DISP, fontWeight: 800,
  fontSize: `clamp(${Math.max(20, Number(s.value.h_size_min) || 54)}px,${Number(s.value.h_size_vw) || 12}vw,${Number(s.value.h_size_max) || 180}px)`,
  lineHeight: Number(s.value.h_line_height) || 0.86, letterSpacing: '-.02em',
  textTransform: s.value.uppercase ? 'uppercase' : 'none', color: txt.value, margin: 0,
}));
function lnClass(mode) { return { acc: mode === 'accent', out: mode === 'outline', grd: mode === 'gradient' }; }
function lnStyle(mode) {
  const base = { display: 'block' };
  if (mode === 'accent') base.color = accent.value;
  if (mode === 'outline') { base.webkitTextStroke = (Number(s.value.stroke_width) || 0) + 'px ' + txt.value; base.WebkitTextStroke = (Number(s.value.stroke_width) || 0) + 'px ' + txt.value; base.color = 'transparent'; }
  if (mode === 'gradient') { base.background = 'linear-gradient(110deg, ' + accent.value + ', ' + accent2.value + ')'; base.webkitBackgroundClip = 'text'; base.backgroundClip = 'text'; base.color = 'transparent'; }
  return base;
}
const bottomStyle = computed(() => {
  const st = { display: 'flex', gap: '30px', marginTop: '42px', flexWrap: 'wrap', width: '100%' };
  if (split.value) { st.justifyContent = 'space-between'; st.alignItems = 'flex-end'; }
  else { st.flexDirection = 'column'; st.alignItems = alignI.value; }
  return st;
});
const subStyle = computed(() => ({ maxWidth: '440px', fontSize: '17px', lineHeight: 1.6, color: sub.value, margin: 0 }));
const sideStyle = computed(() => ({ display: 'flex', flexDirection: 'column', gap: '18px', alignItems: align.value === 'center' ? 'center' : 'flex-start' }));
const solidStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '9px', padding: '16px 30px', borderRadius: btnRadius.value, fontFamily: SANS, fontWeight: 700, fontSize: '15px', textDecoration: 'none', background: accent.value, color: accOn.value, border: 0 }));
const ghostBtnStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '9px', padding: '16px 30px', borderRadius: btnRadius.value, fontFamily: SANS, fontWeight: 700, fontSize: '15px', textDecoration: 'none', background: 'transparent', color: txt.value, border: '1.5px solid rgba(255,255,255,.22)' }));
const scrollStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '10px', fontSize: '12px', letterSpacing: '.1em', textTransform: 'uppercase', color: sub.value }));
const scrollLnStyle = computed(() => ({ display: 'inline-block', width: '46px', height: '1px', background: sub.value }));
</script>

<style scoped>
.glw-eyebrow span::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: var(--glw-dot, #f4a23b); }
.glw-eyebrow--nodot span::before { display: none; }
.glw-btn { transition: transform .15s, filter .2s; }
.glw-btn:hover { transform: translateY(-2px); filter: brightness(1.06); }
.glw-btn:focus-visible { outline: 2px solid var(--glw-accent, #f4a23b); outline-offset: 3px; }
</style>
