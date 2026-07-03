<template>
  <section class="olo-featuredstory fs" :style="rootStyle">
    <div class="fs-in" :style="inStyle">
      <div class="fs-media" :style="{ order: mediaOrder }">
        <a v-if="coverHref" :href="coverHref"><span class="fs-frame" :style="frameStyle">
          <video v-if="isCoverVideo" class="fs-frame-video" :src="s.media_cover.video_url" autoplay loop muted playsinline style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover"></video>
          <span v-if="!hasMediaCover && !s.cover_image && s.cover_label" class="fs-label" :style="labelStyle">{{ s.cover_label }}</span>
        </span></a>
        <span v-else class="fs-frame" :style="frameStyle">
          <video v-if="isCoverVideo" class="fs-frame-video" :src="s.media_cover.video_url" autoplay loop muted playsinline style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover"></video>
          <span v-if="!hasMediaCover && !s.cover_image && s.cover_label" class="fs-label" :style="labelStyle">{{ s.cover_label }}</span>
        </span>
      </div>
      <div class="fs-col" :style="{ order: colOrder }">
        <span v-if="s.kicker_text" class="fs-kicker" :style="kickerStyle">{{ s.kicker_text }}</span>
        <h1 class="fs-h" :style="hStyle">
          <a v-if="headHref" :href="headHref">{{ s.headline_text }}</a>
          <template v-else>{{ s.headline_text }}</template>
        </h1>
        <p v-if="s.standfirst" class="fs-stand" :style="standStyle">{{ s.standfirst }}</p>
        <div v-if="s.byline_name || s.byline_meta" class="fs-byline" :style="bylineStyle">
          <template v-if="s.byline_name"><template v-if="s.byline_pre">{{ s.byline_pre }} </template><b :style="{ color: bnameColor, fontWeight: 700 }">{{ s.byline_name }}</b></template>
          <template v-if="s.byline_name && s.byline_meta"> · </template>
          <template v-if="s.byline_meta">{{ s.byline_meta }}</template>
        </div>
        <div v-if="s.cta1_text || s.cta2_text" class="fs-cta" :style="ctaWrapStyle">
          <a v-if="s.cta1_text" class="fs-btn fs-btn--solid" :style="solidStyle" :href="s.cta1_url || '#'">{{ s.cta1_text }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
          <a v-if="s.cta2_text" class="fs-btn fs-btn--ghost" :style="ghostStyle" :href="s.cta2_url || '#'">{{ s.cta2_text }}</a>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { SHADOW, resolveFontFamily } from '@/composables/oloTileDefaults';
import { focalPos } from '@/utils/focalPoint';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  // Content
  kicker_text: 'The Essay · Cities',
  headline_text: 'The slow return of the city night market',
  headline_url: '#',
  standfirst: 'For a decade they were left for dead. Now, under the same lanterns, a new generation is rebuilding the night market — one stall, one recipe, one argument at a time.',
  byline_pre: 'By',
  byline_name: 'Elena Russo',
  byline_meta: '18 min read',
  cover_image: '',
  media_cover: { type: 'none' },
  cover_url: '#',
  cover_label: 'cover — empty night market, lanterns, long exposure',
  // CTAs (optional)
  cta1_text: '',
  cta1_url: '#',
  cta2_text: '',
  cta2_url: '#',
  // Layout
  media_side: 'left',
  col_ratio: '1.15fr .85fr',
  cover_aspect: '4 / 3',
  media_radius: 0,
  standfirst_italic: false,
  placeholder_dark: true,
  // Spaziatura (override gated): pad_custom=false → clamp responsivo invariato.
  pad_custom: false,
  content_padding: { top: 45, right: 0, bottom: 45, left: 0 },
  // Raggi per-angolo (additivi, no-op ai default).
  cover_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
  cta_radius: { tl: 2, tr: 2, br: 2, bl: 2 },
  // Colors
  bg_color: '#f3f0e9',
  kicker_color: '#9a2b22',
  headline_color: '#16161a',
  accent_color: '',
  standfirst_color: '#2c2c30',
  byline_color: '#76746e',
  byline_name_color: '#16161a',
  media_bg: '#e9e4d8',
  cta_solid_bg: '#16161a',
  cta_solid_text: '#f3f0e9',
  // Fonts
  heading_font: "var(--olo-font-family-heading, 'Libre Caslon Display', Georgia, serif)",
  serif_font: "var(--olo-font-family-heading, 'Libre Caslon Text', Georgia, serif)",
  sans_font: "var(--olo-font-family, 'Mulish', -apple-system, sans-serif)",
  // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
  // Default no-op: bg 'none', shadow 'none', bordo 0 → render invariato.
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

// ── Copertina universale (pannello media_cover) con fallback all'immagine legacy cover_image ──
const hasMediaCover = computed(() => {
  const m = s.value.media_cover;
  return !!(m && typeof m === 'object' && m.type && m.type !== 'none');
});
const mediaCoverStyle = computed(() => (hasMediaCover.value ? buildBgStyle(s.value.media_cover) : {}));
const isCoverVideo = computed(() => {
  const m = s.value.media_cover;
  return hasMediaCover.value && m.type === 'video' && !!m.video_url;
});

const bg = computed(() => s.value.bg_color || 'var(--olo-color-surface, #f3f0e9)');
const kicker = computed(() => s.value.kicker_color || 'var(--olo-color-primary, #9a2b22)');
const hcol = computed(() => s.value.headline_color || 'var(--olo-color-heading, #16161a)');
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #9a2b22)');
const stand = computed(() => s.value.standfirst_color || 'var(--olo-color-text, #2c2c30)');
const byline = computed(() => s.value.byline_color || 'var(--olo-color-muted, #76746e)');
const bnameColor = computed(() => s.value.byline_name_color || hcol.value);
const mediabg = computed(() => s.value.media_bg || 'var(--olo-color-surface-2, #e9e4d8)');
const csolid = computed(() => s.value.cta_solid_bg || hcol.value);
const csoltxt = computed(() => s.value.cta_solid_text || bg.value);

const disp = computed(() => resolveFontFamily(s.value.heading_font) || defaults.heading_font);
const serif = computed(() => resolveFontFamily(s.value.serif_font) || defaults.serif_font);
const sans = computed(() => resolveFontFamily(s.value.sans_font) || defaults.sans_font);

const ratioRe = /^[\d.\sfr]+$/;
const aspectRe = /^[\d.\s/]+$/;
const ratio = computed(() => ratioRe.test(String(s.value.col_ratio)) ? String(s.value.col_ratio) : '1.15fr .85fr');
const aspect = computed(() => aspectRe.test(String(s.value.cover_aspect)) ? String(s.value.cover_aspect) : '4 / 3');
const radius = computed(() => Math.max(0, parseInt(s.value.media_radius, 10) || 0));
const isRight = computed(() => String(s.value.media_side) === 'right');

// ── Spaziatura (override gated): se pad_custom è true usa content_padding,
//    altrimenti mantieni il clamp responsivo originale → default invariato. ──
const rootPad = computed(() => {
  if (!s.value.pad_custom) return 'clamp(34px,5vw,56px) 0';
  const cp = s.value.content_padding || {};
  const pt = parseInt(cp.top, 10) || 0;
  const pr = parseInt(cp.right, 10) || 0;
  const pb = parseInt(cp.bottom, 10) || 0;
  const pl = parseInt(cp.left, 10) || 0;
  return `${pt}px ${pr}px ${pb}px ${pl}px`;
});

// Border-radius da {tl,tr,br,bl}: '' se tutti 0 (così cade sul fallback). ── parità PHP build_border_radius_css.
function radiusCss(br) {
  if (!br || typeof br !== 'object') return '';
  const tl = parseInt(br.tl, 10) || 0;
  const tr = parseInt(br.tr, 10) || 0;
  const brr = parseInt(br.br, 10) || 0;
  const bl = parseInt(br.bl, 10) || 0;
  if (!tl && !tr && !brr && !bl) return '';
  return `${tl}px ${tr}px ${brr}px ${bl}px`;
}
// Forma compatta "{n}px" se i 4 angoli sono uguali (preserva byte-per-byte). ── parità PHP fs_uniform_radius.
function uniformRadius(br, fallback) {
  if (!br || typeof br !== 'object') return `${fallback}px`;
  const tl = parseInt(br.tl, 10); const tlv = Number.isNaN(tl) ? fallback : tl;
  const tr = parseInt(br.tr, 10); const trv = Number.isNaN(tr) ? fallback : tr;
  const brr = parseInt(br.br, 10); const brv = Number.isNaN(brr) ? fallback : brr;
  const bl = parseInt(br.bl, 10); const blv = Number.isNaN(bl) ? fallback : bl;
  if (tlv === trv && trv === brv && brv === blv) return `${tlv}px`;
  return `${tlv}px ${trv}px ${brv}px ${blv}px`;
}
// Raggio copertina: per-angolo se valorizzato, altrimenti media_radius (default 0 → '0px'). ── parità PHP.
const coverRadiusCss = computed(() => {
  const r = radiusCss(s.value.cover_radius);
  return r !== '' ? r : `${radius.value}px`;
});
// Raggio CTA: default {2,2,2,2} → ricade su '2px' originale. ── parità PHP.
const ctaRadiusCss = computed(() => uniformRadius(s.value.cta_radius, 2));

function mirrorRatio(r) {
  const parts = String(r).trim().split(/\s+/);
  if (parts.length === 2) return parts[1] + ' ' + parts[0];
  return r;
}
const gridRatio = computed(() => isRight.value ? mirrorRatio(ratio.value) : ratio.value);
const mediaOrder = computed(() => isRight.value ? 2 : 1);
const colOrder = computed(() => isRight.value ? 1 : 2);

function safeUrl(url) {
  const u = String(url || '').trim();
  if (u === '') return '';
  return u;
}
const coverHref = computed(() => safeUrl(s.value.cover_url));
const headHref = computed(() => safeUrl(s.value.headline_url));

// ── KIT standard: sfondo completo (override del bg sezione SOLO se valorizzato) ──
const kitBgStyle = computed(() => {
  const b = s.value.bg;
  if (b && b.type && b.type !== 'none') return buildBgStyle(b);
  return null;
});

// ── KIT standard: box-shadow (preset sm/md/lg/xl o custom) ──
const kitShadow = computed(() => {
  const k = s.value.shadow || 'none';
  if (k === 'none' || k === '') return '';
  if (SHADOW[k]) return SHADOW[k];
  if (k === 'custom') {
    const h = parseInt(s.value.shadow_h, 10) || 0;
    const v = parseInt(s.value.shadow_v, 10) || 4;
    const bl = parseInt(s.value.shadow_blur, 10) || 10;
    const sp = parseInt(s.value.shadow_spread, 10) || 0;
    const sc = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const ins = s.value.shadow_inset ? 'inset ' : '';
    return `${ins}${h}px ${v}px ${bl}px ${sp}px ${sc}`;
  }
  return '';
});

// ── KIT standard: bordo base (parità con build_border_css PHP: vuoto se color '' o lati 0) ──
const kitBorder = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return null;
  const color = String(b.color || '').trim();
  if (color === '') return null;
  const style = b.style || 'solid';
  const t = Math.max(0, parseInt(b.top, 10) || 0);
  const r = Math.max(0, parseInt(b.right, 10) || 0);
  const bo = Math.max(0, parseInt(b.bottom, 10) || 0);
  const l = Math.max(0, parseInt(b.left, 10) || 0);
  if (!t && !r && !bo && !l) return null;
  if (t === r && r === bo && bo === l) return { border: `${t}px ${style} ${color}` };
  const st = {};
  if (t) st.borderTop = `${t}px ${style} ${color}`;
  if (r) st.borderRight = `${r}px ${style} ${color}`;
  if (bo) st.borderBottom = `${bo}px ${style} ${color}`;
  if (l) st.borderLeft = `${l}px ${style} ${color}`;
  return st;
});

const rootStyle = computed(() => {
  const st = { padding: rootPad.value, background: bg.value, fontFamily: sans.value, position: 'relative', '--fs-accent': accent.value, '--fs-hcol': hcol.value, '--fs-byline': byline.value };
  // KIT bg vince sul bg_color storico se impostato
  if (kitBgStyle.value) { delete st.background; Object.assign(st, kitBgStyle.value); }
  if (kitShadow.value) st.boxShadow = kitShadow.value;
  if (kitBorder.value) Object.assign(st, kitBorder.value);
  return st;
});
const inStyle = computed(() => ({ maxWidth: '1200px', margin: '0 auto', padding: '0 30px', display: 'grid', gridTemplateColumns: gridRatio.value, gap: '48px', alignItems: 'center' }));
const phDark = computed(() => s.value.placeholder_dark !== false);
const phRgb = computed(() => phDark.value ? '22,22,26' : '238,242,247');
const phLine = computed(() => 'rgba(' + phRgb.value + ',.05)');
const phLabel = computed(() => 'rgba(' + phRgb.value + ',' + (phDark.value ? '.4' : '.42') + ')');
const frameStyle = computed(() => {
  const st = { position: 'relative', display: 'block', overflow: 'hidden', aspectRatio: aspect.value, background: mediabg.value, borderRadius: coverRadiusCss.value, backgroundSize: 'cover', backgroundPosition: focalPos(s.value, 'cover_image') };
  if (hasMediaCover.value) {
    // COPERTINA universale: precedenza sul media legacy. buildBgStyle sovrascrive
    // le proprietà background-* (image/gradient/solid…); il video ha layer proprio.
    Object.assign(st, mediaCoverStyle.value);
  } else {
    st.backgroundImage = s.value.cover_image ? 'url(' + s.value.cover_image + ')' : 'repeating-linear-gradient(135deg, ' + phLine.value + ' 0 15px, transparent 15px 30px)';
  }
  return st;
});
const labelStyle = computed(() => ({ position: 'absolute', left: '13px', right: '13px', bottom: '11px', fontWeight: 600, fontSize: '10.5px', letterSpacing: '.05em', textTransform: 'uppercase', color: phLabel.value }));
const kickerStyle = computed(() => ({ display: 'block', marginBottom: '14px', fontFamily: sans.value, fontWeight: 700, fontSize: '11.5px', letterSpacing: '.16em', textTransform: 'uppercase', color: kicker.value }));
const hStyle = computed(() => ({ fontFamily: disp.value, fontWeight: 400, fontSize: 'clamp(38px,5.6vw,72px)', lineHeight: 1.02, letterSpacing: '.002em', color: hcol.value, margin: 0 }));
const standStyle = computed(() => ({ fontFamily: serif.value, fontStyle: s.value.standfirst_italic ? 'italic' : 'normal', fontSize: '19px', lineHeight: 1.55, color: stand.value, margin: '20px 0 22px' }));
const bylineStyle = computed(() => ({ fontFamily: sans.value, fontSize: '12.5px', letterSpacing: '.04em', textTransform: 'uppercase', color: byline.value }));
const ctaWrapStyle = { display: 'flex', gap: '12px', flexWrap: 'wrap', marginTop: '22px' };
const solidStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '13px 24px', borderRadius: ctaRadiusCss.value, fontFamily: sans.value, fontWeight: 700, fontSize: '12.5px', letterSpacing: '.08em', textTransform: 'uppercase', textDecoration: 'none', background: csolid.value, color: csoltxt.value, border: 0 }));
const ghostStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '13px 24px', borderRadius: ctaRadiusCss.value, fontFamily: sans.value, fontWeight: 700, fontSize: '12.5px', letterSpacing: '.08em', textTransform: 'uppercase', textDecoration: 'none', background: 'transparent', color: hcol.value, borderWidth: '1.5px', borderStyle: 'solid' }));
</script>

<style scoped>
.fs-media a { display: block; }
.fs-h a { color: inherit; text-decoration: none; transition: color .2s; }
.fs-h a:hover { color: var(--fs-accent, #9a2b22); }
.fs-btn { transition: transform .15s, background .2s, filter .2s, border-color .2s; }
.fs-btn:hover { transform: translateY(-2px); }
.fs-btn--solid:hover { filter: brightness(1.12); }
.fs-btn--ghost { border-color: var(--fs-byline, #76746e); }
.fs-btn--ghost:hover { border-color: var(--fs-hcol, #16161a); }
.fs-btn:focus-visible,
.fs-h a:focus-visible,
.fs-media a:focus-visible { outline: 2px solid var(--fs-accent, #9a2b22); outline-offset: 3px; }
@media (max-width: 880px) {
  .fs-in { grid-template-columns: 1fr !important; gap: 28px !important; }
  .fs-media { order: 1 !important; }
  .fs-col { order: 2 !important; }
}
</style>
