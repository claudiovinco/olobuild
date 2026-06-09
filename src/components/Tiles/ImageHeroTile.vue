<template>
  <section class="olo-imagehero oih" :style="rootStyle">
    <div class="oih-media" :style="mediaStyle">
      <span v-if="!s.bg_image && !hasMediaBg && s.media_label" class="oih-medialabel" :style="mediaLabelStyle">{{ s.media_label }}</span>
    </div>
    <div class="oih-grad" :style="gradStyle"></div>
    <div class="oih-in" :style="inStyle">
      <div class="oih-c" :style="cStyle">
        <span v-if="s.eyebrow_text" class="oih-eyebrow" :style="eyebrowStyle">
          <span v-if="s.eyebrow_dot" class="oih-dot" :style="dotStyle"></span>{{ s.eyebrow_text }}
        </span>
        <h1 class="oih-h" :style="hStyle">{{ s.headline_text }}<template v-if="s.accent_text"><br v-if="s.stack_lines"><template v-else> </template><span class="oih-acc" :style="accStyle">{{ s.accent_text }}</span></template><template v-if="s.headline_tail"><br v-if="s.stack_lines"><template v-else> </template>{{ s.headline_tail }}</template></h1>
        <p v-if="s.subhead" class="oih-sub" :style="subStyle">{{ s.subhead }}</p>
        <div v-if="s.meta_text" class="oih-meta" :style="metaStyle">{{ s.meta_text }}</div>
        <div v-if="s.cta1_text || s.cta2_text" class="oih-cta" :style="ctaStyle">
          <a v-if="s.cta1_text" class="oih-btn oih-btn--solid" :style="solidStyle" :href="s.cta1_url || '#'">{{ s.cta1_text }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
          <a v-if="s.cta2_text" class="oih-btn oih-btn--out" :style="outStyle" :href="s.cta2_url || '#'">{{ s.cta2_text }}</a>
        </div>
      </div>
    </div>
    <span v-if="s.scroll_hint" class="oih-scroll" :style="scrollStyle">{{ s.scroll_hint }}</span>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { borderDefault, borderHoverDefault, borderEffectDefaults } from '@/config/elements/_shared.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  eyebrow_text: "Autumn / Winter '26",
  eyebrow_dot: false,
  headline_text: 'The',
  accent_text: 'Nocturne',
  headline_tail: 'Collection',
  accent_italic: true,
  stack_lines: false,
  subhead: 'Tailoring for the hours after dark. Cut in wool crêpe and silk, finished by hand in our Paris atelier.',
  cta1_text: 'Shop the collection',
  cta1_url: '#',
  cta2_text: 'View lookbook',
  cta2_url: '#',
  meta_text: '',
  scroll_hint: '',
  bg_image: '',
  media_bg: { type: 'none' },
  bg_color: '#0c0c0c',
  media_label: 'campaign — figure in black tailoring, gold light, full bleed',
  text_position: 'left',
  text_align: 'left',
  content_width: 600,
  aspect_ratio: '21/10',
  min_height: 520,
  heading_font: 'serif',
  overlay_color: '#0c0c0c',
  overlay_top: 0.2,
  overlay_bottom: 0.75,
  overlay_sides: true,
  accent: '',
  accent_on: '#0c0c0c',
  text_color: '#ffffff',
  sub_color: '#efe9de',
  eyebrow_color: '',

  // Spaziatura / Forma (additivi, default no-op) — parità col PHP
  pad_custom: false,
  content_padding: { top: 60, right: 32, bottom: 60, left: 32 },
  cta_radius: { tl: 2, tr: 2, br: 2, bl: 2 },
  wrap_radius: { tl: 0, tr: 0, br: 0, bl: 0 },

  // Kit standard OLObuild — sfondo completo + ombra + bordo (default no-op)
  bg: { type: 'none' },
  shadow: 'none',
  border: { ...borderDefault },
  border_hover: { ...borderHoverDefault },
  border_hover_duration: 300,
  ...borderEffectDefaults,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const SANS = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";
const SERIF = "var(--olo-font-family-heading, 'Marcellus','Cormorant Garamond',Georgia,serif)";

const bg = computed(() => s.value.bg_color || '#0c0c0c');
const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #c9a86a)');
const accOn = computed(() => s.value.accent_on || '#0c0c0c');
const txt = computed(() => s.value.text_color || '#ffffff');
const sub = computed(() => s.value.sub_color || '#efe9de');
const eyeCol = computed(() => s.value.eyebrow_color || accent.value);
const disp = computed(() => (s.value.heading_font === 'sans' ? SANS : SERIF));

function hexRgb(hex, fb = '12,12,12') {
  let h = String(hex || '').replace('#', '');
  if (h.length === 3) h = h.split('').map(c => c + c).join('');
  if (!/^[0-9a-fA-F]{6}$/.test(h)) return fb;
  return parseInt(h.slice(0, 2), 16) + ',' + parseInt(h.slice(2, 4), 16) + ',' + parseInt(h.slice(4, 6), 16);
}
function clamp01(v, fb) {
  const n = (v === '' || v === null || v === undefined || isNaN(Number(v))) ? fb : Number(v);
  return Math.round(Math.max(0, Math.min(1, n)) * 1000) / 1000;
}

const oTop = computed(() => clamp01(s.value.overlay_top, 0.2));
const oBot = computed(() => clamp01(s.value.overlay_bottom, 0.75));
const oMid = computed(() => Math.round(oTop.value * 0.6 * 1000) / 1000);

const minHeightCss = computed(() => {
  const mh = parseInt(s.value.min_height, 10);
  if (mh > 0 && mh <= 100) return mh + 'vh';
  return Math.max(200, isNaN(mh) ? 520 : mh) + 'px';
});
const aspect = computed(() => (/^\d+\s*\/\s*\d+$/.test(String(s.value.aspect_ratio)) ? String(s.value.aspect_ratio).replace(/\s/g, '') : '21/10'));
const contentWidth = computed(() => Math.max(280, Math.min(1200, parseInt(s.value.content_width, 10) || 600)));
const align = computed(() => (['left', 'center', 'right'].includes(s.value.text_align) ? s.value.text_align : 'left'));
const pos = computed(() => (['center', 'bottom-left', 'left', 'center-right'].includes(s.value.text_position) ? s.value.text_position : 'left'));

const justifyV = computed(() => {
  if (pos.value === 'bottom-left') return 'flex-end';
  return 'center';
});
const alignH = computed(() => {
  if (pos.value === 'center') return 'center';
  if (pos.value === 'center-right') return 'flex-end';
  if (pos.value === 'bottom-left') return 'flex-start';
  return 'flex-start';
});
const marginC = computed(() => (align.value === 'center' ? '0 auto' : (align.value === 'right' ? '0 0 0 auto' : '0')));
const cAlignItems = computed(() => (align.value === 'center' ? 'center' : (align.value === 'right' ? 'flex-end' : 'flex-start')));

const gradient = computed(() => {
  const rgb = hexRgb(s.value.overlay_color || '#0c0c0c');
  const gradV = `linear-gradient(180deg, rgba(${rgb},${oTop.value}) 0%, rgba(${rgb},${oMid.value}) 38%, rgba(${rgb},${oBot.value}) 100%)`;
  if (!s.value.overlay_sides) return gradV;
  const sTop = Math.round(oTop.value * 0.4 * 1000) / 1000;
  const sBot = Math.round(oBot.value * 0.7 * 1000) / 1000;
  const gradS = `linear-gradient(90deg, rgba(${rgb},${oBot.value}) 0%, rgba(${rgb},${sTop}) 52%, rgba(${rgb},${sBot}) 100%)`;
  return gradS + ',' + gradV;
});

// ── Kit standard OLObuild: sfondo completo + ombra + bordo (parità col PHP) ──
// Sfondo completo: override SOLO se valorizzato (type !== 'none') → default invariato.
const bgKit = computed(() => {
  const b = s.value.bg;
  if (!b || b.type === 'none') return {};
  return buildBgStyle(b);
});

// Ombra (preset sm/md/lg/xl o custom) — stessi valori del PHP build_shadow_decl.
const shadowCss = computed(() => {
  const preset = s.value.shadow || 'none';
  if (preset === 'none' || preset === '') return '';
  if (preset === 'custom') {
    const h = parseInt(s.value.shadow_h, 10) || 0;
    const v = parseInt(s.value.shadow_v, 10) || 4;
    const blur = Math.max(0, parseInt(s.value.shadow_blur, 10) || 10);
    const spread = parseInt(s.value.shadow_spread, 10) || 0;
    const color = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  const map = {
    sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
    md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
    lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
    xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
  };
  return map[preset] || '';
});

// Bordo (sistema standard) — mirror di build_border_css (uniforme vs per-lato).
const borderCss = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return {};
  const t = parseInt(b.top, 10) || 0;
  const r = parseInt(b.right, 10) || 0;
  const bo = parseInt(b.bottom, 10) || 0;
  const l = parseInt(b.left, 10) || 0;
  if (!t && !r && !bo && !l) return {};
  const st = b.style || 'solid';
  const c = b.color || 'currentColor';
  if (t === r && r === bo && bo === l) {
    return { border: `${t}px ${st} ${c}` };
  }
  const out = {};
  if (t) out.borderTop = `${t}px ${st} ${c}`;
  if (r) out.borderRight = `${r}px ${st} ${c}`;
  if (bo) out.borderBottom = `${bo}px ${st} ${c}`;
  if (l) out.borderLeft = `${l}px ${st} ${c}`;
  return out;
});

// ── Spaziatura / Forma (additivi, default no-op) — parità col PHP ──
// build_border_radius_css() lato PHP: '' se tutti gli angoli a 0, altrimenti 4 valori px.
function radiusCss(v) {
  if (!v || typeof v !== 'object') {
    const n = parseInt(v, 10) || 0;
    return n > 0 ? n + 'px' : '';
  }
  const tl = parseInt(v.tl, 10) || 0;
  const tr = parseInt(v.tr, 10) || 0;
  const br = parseInt(v.br, 10) || 0;
  const bl = parseInt(v.bl, 10) || 0;
  if (!tl && !tr && !br && !bl) return '';
  return `${tl}px ${tr}px ${br}px ${bl}px`;
}

// Padding contenuto: responsivo di default; override SOLO se pad_custom on (= PHP).
const padCss = computed(() => {
  if (!s.value.pad_custom) return 'clamp(40px,7vh,80px) 32px';
  const cp = s.value.content_padding || {};
  const pt = parseInt(cp.top, 10) || 0;
  const pr = parseInt(cp.right, 10) || 0;
  const pb = parseInt(cp.bottom, 10) || 0;
  const pl = parseInt(cp.left, 10) || 0;
  return `${pt}px ${pr}px ${pb}px ${pl}px`;
});

// Raggio CTA: default {2,2,2,2} → resa invariata (override solo se ≠ 2 ovunque).
const ctaRadiusCss = computed(() => {
  const cr = s.value.cta_radius;
  const isDef = !cr || typeof cr !== 'object'
    || ((parseInt(cr.tl, 10) || 0) === 2 && (parseInt(cr.tr, 10) || 0) === 2 && (parseInt(cr.br, 10) || 0) === 2 && (parseInt(cr.bl, 10) || 0) === 2);
  return isDef ? '' : radiusCss(cr);
});
// Raggio contenitore: default {0,0,0,0} → '' (no-op su hero full-bleed).
const wrapRadiusCss = computed(() => radiusCss(s.value.wrap_radius));

const rootStyle = computed(() => ({
  position: 'relative', overflow: 'hidden', background: bg.value, color: txt.value, fontFamily: SANS,
  display: 'flex', flexDirection: 'column', justifyContent: justifyV.value, minHeight: minHeightCss.value,
  '--oih-accent': accent.value,
  ...bgKit.value,
  ...borderCss.value,
  ...(wrapRadiusCss.value ? { borderRadius: wrapRadiusCss.value } : {}),
  ...(shadowCss.value ? { boxShadow: shadowCss.value } : {}),
}));
const hasMediaBg = computed(() => { const m = s.value.media_bg; return !!(m && m.type && m.type !== 'none'); });
const mediaStyle = computed(() => {
  const base = { position: 'absolute', inset: 0, zIndex: 0, backgroundSize: 'cover', backgroundPosition: 'center', aspectRatio: aspect.value, minHeight: '100%' };
  if (hasMediaBg.value) return { ...base, ...buildBgStyle(s.value.media_bg) };
  return { ...base, backgroundColor: bg.value, backgroundImage: s.value.bg_image ? 'url(' + s.value.bg_image + ')' : 'repeating-linear-gradient(135deg, rgba(255,255,255,.05) 0 18px, rgba(255,255,255,0) 18px 36px)' };
});
const mediaLabelStyle = { position: 'absolute', left: '20px', bottom: '16px', fontSize: '11px', letterSpacing: '.04em', textTransform: 'uppercase', fontWeight: 600, color: 'rgba(255,255,255,.42)', maxWidth: '60%' };
const gradStyle = computed(() => ({ position: 'absolute', inset: 0, zIndex: 1, background: gradient.value }));
const inStyle = computed(() => ({ position: 'relative', zIndex: 2, width: '100%', maxWidth: '1240px', margin: '0 auto', padding: padCss.value, display: 'flex', flexDirection: 'column', alignItems: alignH.value }));
const cStyle = computed(() => ({ maxWidth: contentWidth.value + 'px', margin: marginC.value, textAlign: align.value, display: 'flex', flexDirection: 'column', alignItems: cAlignItems.value }));
const eyebrowStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '9px', fontFamily: SANS, fontWeight: 600, fontSize: '11.5px', letterSpacing: '.28em', textTransform: 'uppercase', color: eyeCol.value, margin: '0 0 22px' }));
const dotStyle = computed(() => ({ width: '6px', height: '6px', borderRadius: '50%', background: accent.value, boxShadow: '0 0 8px ' + accent.value }));
const hStyle = computed(() => ({ fontFamily: disp.value, fontWeight: 400, fontSize: 'clamp(48px,8vw,104px)', lineHeight: .98, letterSpacing: '.005em', color: txt.value, margin: 0 }));
const accStyle = computed(() => ({ color: accent.value, fontStyle: s.value.accent_italic ? 'italic' : 'normal' }));
const subStyle = computed(() => ({ fontSize: '17px', lineHeight: 1.6, color: sub.value, maxWidth: '440px', margin: '24px 0 0' }));
const metaStyle = computed(() => ({ marginTop: '22px', fontFamily: disp.value, fontSize: 'clamp(13px,1.7vw,18px)', letterSpacing: '.16em', textTransform: 'uppercase', color: sub.value, display: 'flex', gap: '14px', alignItems: 'center', flexWrap: 'wrap', ...(align.value === 'center' ? { justifyContent: 'center' } : {}) }));
const ctaStyle = computed(() => ({ display: 'flex', gap: '14px', flexWrap: 'wrap', marginTop: '32px', ...(align.value === 'center' ? { justifyContent: 'center' } : {}) }));
const btnBase = { display: 'inline-flex', alignItems: 'center', gap: '9px', padding: '15px 28px', borderRadius: '2px', fontFamily: SANS, fontWeight: 600, fontSize: '13px', letterSpacing: '.04em', textTransform: 'uppercase', textDecoration: 'none', border: '1px solid transparent' };
const btnRadiusOverride = computed(() => (ctaRadiusCss.value ? { borderRadius: ctaRadiusCss.value } : {}));
const solidStyle = computed(() => ({ ...btnBase, ...btnRadiusOverride.value, background: accent.value, color: accOn.value }));
const outStyle = computed(() => ({ ...btnBase, ...btnRadiusOverride.value, background: 'transparent', color: txt.value, borderColor: 'rgba(255,255,255,.4)' }));
const scrollStyle = { position: 'absolute', bottom: '28px', left: '50%', transform: 'translateX(-50%)', zIndex: 2, fontFamily: SANS, fontSize: '11px', letterSpacing: '.16em', textTransform: 'uppercase', color: 'rgba(255,255,255,.7)' };
</script>

<style scoped>
.oih-btn { transition: transform .15s, background .2s, color .2s, filter .2s; }
.oih-btn:hover { transform: translateY(-2px); }
.oih-btn--solid:hover { filter: brightness(1.08); }
.oih-btn:focus-visible { outline: 2px solid var(--oih-accent, #c9a86a); outline-offset: 3px; }
</style>
