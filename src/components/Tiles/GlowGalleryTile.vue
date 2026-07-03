<template>
  <section class="olo-glowgallery evh" :style="rootStyle">
    <span class="evh-glow" :style="glowStyle"></span>
    <div class="evh-in" :style="inStyle">
      <span v-if="s.eyebrow" class="evh-eyebrow" :style="eyebrowStyle">{{ s.eyebrow }}</span>
      <h1 class="evh-h" :style="hStyle">{{ s.headline_text }}<span v-if="s.accent_text" class="acc" :style="{ fontStyle: 'italic', color: accent }"> {{ s.accent_text }}</span></h1>
      <p v-if="s.subhead" class="evh-sub" :style="subStyle">{{ s.subhead }}</p>
      <div v-if="s.cta1_text || s.cta2_text" class="evh-cta" :style="{ display: 'flex', gap: '14px', justifyContent: 'center', flexWrap: 'wrap' }">
        <a v-if="s.cta1_text" class="evh-btn evh-btn--solid" :style="solidStyle" :href="s.cta1_url || '#'">{{ s.cta1_text }}</a>
        <a v-if="s.cta2_text" class="evh-btn evh-btn--out" :style="outStyle" :href="s.cta2_url || '#'">{{ s.cta2_text }}</a>
      </div>
    </div>
    <div v-if="items.length" class="evh-strip" :style="stripStyle">
      <div v-for="(it, i) in items" :key="i" class="evh-media" :style="mediaStyle(it, i)">
        <span v-if="it.caption" class="evh-cap" :style="capStyle">{{ it.caption }}</span>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  eyebrow: 'Events studio · est. 2011',
  headline_text: 'Celebrations worth',
  accent_text: 'remembering.',
  subhead: 'We design and produce weddings, galas and private events — the kind your guests talk about for years. From the first spark of an idea to the last dance.',
  cta1_text: 'Start planning', cta1_url: '#', cta2_text: 'See our work', cta2_url: '#',
  items: [
    { image: '', caption: 'tablescape, candlelight' },
    { image: '', caption: 'ballroom, florals' },
    { image: '', caption: 'couple, golden hour' },
  ],
  strip_offset: 28, strip_radius: 200,
  bg_color: '#241430', accent: '', accent_on: '#170c1f',
  text_color: '#f3e9ef', sub_color: '#c8b3c6', eyebrow_color: '#e0afca', media_bg: '#33203f',
  glow_color: 'rgba(224,175,202,0.22)', glow_w: 760, glow_h: 520, glow_blur: 120, glow_y: -160,
  h_size_min: 48, h_size_vw: 8, h_size_max: 108, max_width: 880,

  // Spaziatura & Raggio (additivi, no-op coi default)
  content_padding: { top: 0, right: 30, bottom: 0, left: 30 },
  btn_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
  media_radius_custom: false,
  media_radius: { tl: 200, tr: 200, br: 8, bl: 8 },

  // KIT standard OLObuild — sfondo completo + ombra + bordo (no-op coi default)
  bg: { type: 'none' },
  shadow: 'none',
  border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
  border_hover: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' },
  border_hover_duration: 300,
  border_effect: 'none', border_effect_intensity: 'medium', border_effect_color2: '',
  border_effect_angle: 135, border_effect_speed: 4,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const DISP = "var(--olo-font-family-heading, 'Italiana', Didot, serif)";
const SANS = "var(--olo-font-family, 'Tenor Sans', -apple-system, sans-serif)";

const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #e0afca)');
const accOn = computed(() => s.value.accent_on || '#170c1f');
const txt = computed(() => s.value.text_color || '#f3e9ef');
const sub = computed(() => s.value.sub_color || '#c8b3c6');
const mbg = computed(() => s.value.media_bg || '#33203f');

const items = computed(() => (Array.isArray(s.value.items) ? s.value.items : []));
const mw = computed(() => Math.max(480, Number(s.value.max_width) || 880));
const soff = computed(() => Math.max(0, Number(s.value.strip_offset) || 0));
const srad = computed(() => Math.max(0, Number(s.value.strip_radius) || 0));

// ── Spaziatura & Raggio (additivi, no-op coi default — parità col PHP) ──
function radiusCss(v) {
  if (v && typeof v === 'object') {
    const tl = parseInt(v.tl ?? 0, 10) || 0;
    const tr = parseInt(v.tr ?? 0, 10) || 0;
    const br = parseInt(v.br ?? 0, 10) || 0;
    const bl = parseInt(v.bl ?? 0, 10) || 0;
    if (tl || tr || br || bl) return `${tl}px ${tr}px ${br}px ${bl}px`;
    return '';
  }
  const n = parseInt(v, 10) || 0;
  return n > 0 ? `${n}px` : '';
}
const inPad = computed(() => {
  const cp = s.value.content_padding;
  const t = cp && typeof cp === 'object' ? (parseInt(cp.top, 10) || 0) : 0;
  const r = cp && typeof cp === 'object' ? (cp.right != null ? (parseInt(cp.right, 10) || 0) : 30) : 30;
  const b = cp && typeof cp === 'object' ? (parseInt(cp.bottom, 10) || 0) : 0;
  const l = cp && typeof cp === 'object' ? (cp.left != null ? (parseInt(cp.left, 10) || 0) : 30) : 30;
  return `${t}px ${r}px ${b}px ${l}px`;
});
const btnRad = computed(() => radiusCss(s.value.btn_radius) || '999px');
const mediaRad = computed(() => {
  if (s.value.media_radius_custom) {
    const mr = radiusCss(s.value.media_radius);
    if (mr) return mr;
  }
  return `${srad.value}px ${srad.value}px 8px 8px`;
});

// ── KIT standard OLObuild — sfondo completo + ombra + bordo (parità col PHP) ──
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};
function shadowDecl(st) {
  const p = st.shadow || 'none';
  if (p === 'none' || p === '') return '';
  if (p === 'custom') {
    const h = parseInt(st.shadow_h ?? 0, 10) || 0;
    const v = parseInt(st.shadow_v ?? 4, 10) || 0;
    const blur = Math.max(0, parseInt(st.shadow_blur ?? 10, 10) || 0);
    const spread = parseInt(st.shadow_spread ?? 0, 10) || 0;
    const color = st.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = st.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  return SHADOW_MAP[p] || '';
}
const kitStyle = computed(() => {
  const st = s.value;
  const out = {};
  // Sfondo completo: override SOLO se valorizzato (default type:'none' = no-op)
  const bg = st.bg;
  if (bg && bg.type && bg.type !== 'none') Object.assign(out, buildBgStyle(bg));
  // Ombra
  const sh = shadowDecl(st);
  if (sh) out.boxShadow = sh;
  // Bordo (solo se valorizzato): mirror minimale di build_border_css
  const b = st.border;
  if (b && (Number(b.top) || Number(b.right) || Number(b.bottom) || Number(b.left))) {
    const style = b.style || 'solid';
    const color = b.color || 'currentColor';
    if (b.linked) {
      out.border = `${Number(b.top) || 0}px ${style} ${color}`;
    } else {
      out.borderStyle = style;
      out.borderColor = color;
      out.borderWidth = `${Number(b.top) || 0}px ${Number(b.right) || 0}px ${Number(b.bottom) || 0}px ${Number(b.left) || 0}px`;
    }
  }
  return out;
});

const rootStyle = computed(() => ({
  position: 'relative', overflow: 'hidden', textAlign: 'center',
  background: s.value.bg_color || '#241430', color: txt.value, fontFamily: SANS,
  padding: 'clamp(64px,9vw,120px) 0', '--evh-accent': accent.value,
  ...kitStyle.value,
}));
const glowStyle = computed(() => ({
  position: 'absolute', top: (Number(s.value.glow_y) || 0) + 'px', left: '50%', transform: 'translateX(-50%)',
  width: Math.max(100, Number(s.value.glow_w) || 760) + 'px', height: Math.max(100, Number(s.value.glow_h) || 520) + 'px',
  borderRadius: '50%', filter: 'blur(' + Math.max(0, Number(s.value.glow_blur) || 0) + 'px)', pointerEvents: 'none',
  background: 'radial-gradient(circle, ' + (s.value.glow_color || 'rgba(224,175,202,0.22)') + ', transparent 70%)', zIndex: 0,
}));
const inStyle = computed(() => ({ position: 'relative', zIndex: 2, maxWidth: mw.value + 'px', margin: '0 auto', padding: inPad.value }));
const eyebrowStyle = computed(() => ({ display: 'block', marginBottom: '22px', fontFamily: SANS, fontSize: '12px', letterSpacing: '.32em', textTransform: 'uppercase', color: s.value.eyebrow_color || '#e0afca' }));
const hStyle = computed(() => ({
  fontFamily: DISP, fontWeight: 400,
  fontSize: `clamp(${Math.max(20, Number(s.value.h_size_min) || 48)}px,${Number(s.value.h_size_vw) || 8}vw,${Number(s.value.h_size_max) || 108}px)`,
  lineHeight: 1.02, letterSpacing: '.01em', color: txt.value, margin: 0,
}));
const subStyle = computed(() => ({ fontFamily: SANS, fontSize: '18px', lineHeight: 1.7, color: sub.value, maxWidth: '520px', margin: '26px auto 32px' }));
const solidStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '10px', padding: '14px 30px', borderRadius: btnRad.value, fontFamily: SANS, fontSize: '13px', letterSpacing: '.14em', textTransform: 'uppercase', textDecoration: 'none', background: accent.value, color: accOn.value, border: 0 }));
const outStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '10px', padding: '14px 30px', borderRadius: btnRad.value, fontFamily: SANS, fontSize: '13px', letterSpacing: '.14em', textTransform: 'uppercase', textDecoration: 'none', background: 'transparent', color: txt.value, border: '1px solid rgba(224,175,202,.42)' }));
const stripStyle = computed(() => ({ position: 'relative', zIndex: 2, display: 'flex', gap: '14px', justifyContent: 'center', marginTop: 'clamp(40px,6vw,64px)', flexWrap: 'wrap', maxWidth: Math.max(mw.value, 1180) + 'px', marginLeft: 'auto', marginRight: 'auto', padding: '0 30px' }));
function mediaStyle(it, i) {
  const st = {
    position: 'relative', overflow: 'hidden', width: 'clamp(150px,22vw,240px)', aspectRatio: '3/4',
    borderRadius: mediaRad.value, background: mbg.value,
    backgroundImage: 'repeating-linear-gradient(135deg, rgba(243,233,239,.05) 0 16px, transparent 16px 32px)',
    backgroundSize: 'cover', backgroundPosition: (s.value.object_position || 'center center'),
  };
  if (it && it.image) st.backgroundImage = 'url(' + it.image + ')';
  if (i === 1) st.marginTop = '-' + soff.value + 'px';
  return st;
}
const capStyle = { position: 'absolute', left: '14px', bottom: '12px', right: '14px', fontFamily: SANS, fontSize: '10.5px', letterSpacing: '.1em', color: 'rgba(243,233,239,.4)', textTransform: 'uppercase' };
</script>

<style scoped>
.evh-btn { transition: transform .25s, background .25s, color .25s, border-color .25s, filter .2s; }
.evh-btn:hover { transform: translateY(-2px); }
.evh-btn--solid:hover { filter: brightness(.92); }
.evh-btn--out:hover { border-color: var(--evh-accent, #e0afca); color: var(--evh-accent, #e0afca); }
.evh-btn:focus-visible { outline: 2px solid var(--evh-accent, #e0afca); outline-offset: 3px; }
@media (max-width: 600px) { .evh-strip .evh-media:nth-child(3) { display: none; } }
</style>
