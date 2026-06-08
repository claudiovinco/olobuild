<template>
  <section class="olo-mediacta omc" :style="rootStyle">
    <div class="omc-media" :style="mediaStyle"><span v-if="!s.bg_image && s.media_label" :style="mediaLabelStyle">{{ s.media_label }}</span></div>
    <div class="omc-grad" :style="gradStyle"></div>
    <div class="omc-in" :style="inStyle">
      <span v-if="s.eyebrow" :style="eyebrowStyle">{{ s.eyebrow }}</span>
      <h2 v-if="s.headline" :style="hStyle">{{ s.headline }}<span v-if="s.accent_text" :style="{ color: accent }"> {{ s.accent_text }}</span></h2>
      <p v-if="s.subhead" :style="subStyle">{{ s.subhead }}</p>
      <div v-if="s.cta1_text || s.cta2_text" :style="ctaWrap">
        <a v-if="s.cta1_text" class="omc-btn" :style="solidStyle" :href="s.cta1_url || '#'">{{ s.cta1_text }}
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="width:17px;height:17px"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a v-if="s.cta2_text" class="omc-btn" :style="ghostStyle" :href="s.cta2_url || '#'">{{ s.cta2_text }}</a>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { SHADOW } from '@/composables/oloTileDefaults';
import { borderDefault, borderHoverDefault, borderEffectDefaults } from '@/config/elements/_shared.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  eyebrow: 'Membership', eyebrow_color: '', headline: 'Become a member of our', accent_text: 'club', uppercase: true, headline_color: '#ffffff',
  subhead: '', subhead_color: 'rgba(255,255,255,0.78)', cta1_text: 'Go to membership', cta1_url: '#', cta2_text: '', cta2_url: '',
  bg_image: '', media_label: 'membership — supporters in the stands · background video',
  overlay_color: '#0a2a1e', overlay_top: 0.78, overlay_bottom: 0.9, accent: '', accent_on: '#0a2a1e', text_color: '#ffffff', align: 'center', pad_y: 160,

  // SPAZIATURA (additivo, no-op coi default)
  content_padding: { top: 0, right: 28, bottom: 0, left: 28 },
  pad_custom: false,
  root_padding: { top: 64, right: 0, bottom: 64, left: 0 },

  // FORMA — raggio pill dei pulsanti (default 999 = invariato)
  btn_radius: { tl: 999, tr: 999, br: 999, bl: 999 },

  // KIT standard OLObuild (additivo, no-op coi default)
  bg: { type: 'none' },
  shadow: 'none',
  border: { ...borderDefault },
  border_hover: { ...borderHoverDefault },
  border_hover_duration: 300,
  ...borderEffectDefaults,
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const DISP = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";
const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #c8ff3c)');
const txt = computed(() => s.value.text_color || '#ffffff');
const center = computed(() => s.value.align !== 'left');

// ── KIT standard OLObuild: sfondo completo + ombra + bordo sul contenitore (parità PHP) ──
const kitBgStyle = computed(() => {
  const bg = s.value.bg;
  if (!bg || !bg.type || bg.type === 'none') return {};
  return buildBgStyle(bg);
});
const kitShadow = computed(() => {
  const sh = s.value.shadow || 'none';
  if (sh === 'none' || sh === '') return '';
  if (sh === 'custom') {
    const h = parseInt(s.value.shadow_h, 10) || 0;
    const v = parseInt(s.value.shadow_v, 10) || 4;
    const bl = Math.max(0, parseInt(s.value.shadow_blur, 10) || 10);
    const sp = parseInt(s.value.shadow_spread, 10) || 0;
    const sc = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const ins = s.value.shadow_inset ? 'inset ' : '';
    return `${ins}${h}px ${v}px ${bl}px ${sp}px ${sc}`;
  }
  return SHADOW[sh] || '';
});
// Gemella JS di parse_border + build_border_css (PHP).
const kitBorderStyle = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return {};
  const color = String(b.color || '').trim();
  if (color === '') return {};
  const style = b.style || 'solid';
  const t = Math.max(0, parseInt(b.top, 10) || 0);
  const r = Math.max(0, parseInt(b.right, 10) || 0);
  const bo = Math.max(0, parseInt(b.bottom, 10) || 0);
  const l = Math.max(0, parseInt(b.left, 10) || 0);
  if (!t && !r && !bo && !l) return {};
  if (t === r && r === bo && bo === l) {
    return { border: `${t}px ${style} ${color}` };
  }
  const out = {};
  if (t) out.borderTop = `${t}px ${style} ${color}`;
  if (r) out.borderRight = `${r}px ${style} ${color}`;
  if (bo) out.borderBottom = `${bo}px ${style} ${color}`;
  if (l) out.borderLeft = `${l}px ${style} ${color}`;
  return out;
});

function hexRgb(hex, fb = '10,42,30') {
  let h = String(hex || '').replace('#', '');
  if (h.length === 3) h = h.split('').map(c => c + c).join('');
  if (!/^[0-9a-fA-F]{6}$/.test(h)) return fb;
  return parseInt(h.slice(0, 2), 16) + ',' + parseInt(h.slice(2, 4), 16) + ',' + parseInt(h.slice(4, 6), 16);
}

// Spacing { top,right,bottom,left } → 'Tpx Rpx Bpx Lpx' (gemella PHP pad_css).
// Il fallback vale SOLO a chiave assente/undefined (come PHP `?? $fb` + intval),
// non quando il valore è 0 → 0 esplicito viene rispettato (parità PHP).
function padCss(v, fb = { top: 0, right: 0, bottom: 0, left: 0 }) {
  const o = (v && typeof v === 'object') ? v : {};
  const n = (a, b) => { const x = parseInt((a ?? b), 10); return Number.isNaN(x) ? 0 : x; };
  return `${n(o.top, fb.top)}px ${n(o.right, fb.right)}px ${n(o.bottom, fb.bottom)}px ${n(o.left, fb.left)}px`;
}
// Border-radius { tl,tr,br,bl } → 'TLpx TRpx BRpx BLpx' (gemella PHP build_border_radius_css), '' se tutti 0.
function radiusCss(v) {
  if (!v || typeof v !== 'object') { const n = parseInt(v, 10) || 0; return n > 0 ? `${n}px` : ''; }
  const tl = parseInt(v.tl, 10) || 0, tr = parseInt(v.tr, 10) || 0, br = parseInt(v.br, 10) || 0, bl = parseInt(v.bl, 10) || 0;
  return (tl || tr || br || bl) ? `${tl}px ${tr}px ${br}px ${bl}px` : '';
}

const rootStyle = computed(() => {
  const rootPad = s.value.pad_custom
    ? padCss(s.value.root_padding, { top: 64, right: 0, bottom: 64, left: 0 })
    : 'clamp(64px,12vw,' + (parseInt(s.value.pad_y, 10) || 160) + 'px) 0';
  const st = { position: 'relative', overflow: 'hidden', color: txt.value, fontFamily: SANS, textAlign: center.value ? 'center' : 'left', padding: rootPad };
  Object.assign(st, kitBgStyle.value, kitBorderStyle.value);
  if (kitShadow.value) st.boxShadow = kitShadow.value;
  return st;
});
const mediaStyle = computed(() => ({ position: 'absolute', inset: 0, zIndex: 0, background: s.value.overlay_color || '#0a2a1e', backgroundImage: s.value.bg_image ? 'url(' + s.value.bg_image + ')' : 'repeating-linear-gradient(135deg, rgba(255,255,255,.05) 0 18px, rgba(255,255,255,0) 18px 36px)', backgroundSize: 'cover', backgroundPosition: 'center' }));
const mediaLabelStyle = { position: 'absolute', left: '18px', bottom: '14px', zIndex: 1, fontSize: '11px', letterSpacing: '.04em', textTransform: 'uppercase', fontWeight: 600, color: 'rgba(255,255,255,.4)' };
const gradStyle = computed(() => {
  const rgb = hexRgb(s.value.overlay_color || '#0a2a1e');
  return { position: 'absolute', inset: 0, zIndex: 1, background: `linear-gradient(180deg, rgba(${rgb},${s.value.overlay_top}), rgba(${rgb},${s.value.overlay_bottom}))` };
});
const inStyle = computed(() => ({ position: 'relative', zIndex: 2, maxWidth: '1240px', margin: '0 auto', padding: padCss(s.value.content_padding, { top: 0, right: 28, bottom: 0, left: 28 }) }));
const eyebrowStyle = computed(() => ({ fontWeight: 700, fontSize: '12px', letterSpacing: '.18em', textTransform: 'uppercase', color: s.value.eyebrow_color || accent.value, display: 'block', marginBottom: '18px' }));
const hStyle = computed(() => ({ fontFamily: DISP, fontWeight: 900, fontSize: 'clamp(40px,7.2vw,104px)', lineHeight: .88, letterSpacing: '-.01em', textTransform: s.value.uppercase ? 'uppercase' : 'none', margin: 0, color: txt.value }));
const subStyle = computed(() => ({ fontSize: '17px', lineHeight: 1.6, color: s.value.subhead_color || 'rgba(255,255,255,0.78)', margin: center.value ? '18px auto 0' : '18px 0 0', maxWidth: '560px' }));
const ctaWrap = computed(() => ({ display: 'flex', gap: '12px', flexWrap: 'wrap', marginTop: '32px', justifyContent: center.value ? 'center' : 'flex-start' }));
const btnRadius = computed(() => radiusCss(s.value.btn_radius) || '999px');
const solidStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '9px', padding: '17px 30px', borderRadius: btnRadius.value, fontWeight: 700, fontSize: '15px', textDecoration: 'none', background: accent.value, color: s.value.accent_on || '#0a2a1e', border: 0 }));
const ghostStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '9px', padding: '17px 30px', borderRadius: btnRadius.value, fontWeight: 700, fontSize: '15px', textDecoration: 'none', background: 'rgba(255,255,255,.08)', color: txt.value, border: '1.5px solid rgba(255,255,255,.26)' }));
</script>
