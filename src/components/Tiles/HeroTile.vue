<template>
  <div class="mb-relative" :style="containerStyle">
    <!-- Scena: media + glow + velo + watermark (montata solo se attiva → default no-op) -->
    <div v-if="hasScene" :style="sceneStyle">
      <div v-if="hasMediaBg" :style="mediaLayerStyle"></div>
      <span v-if="s.glow_on" :style="glowStyle"></span>
      <div v-if="hasOverlay" :style="overlayStyle"></div>
      <span v-if="s.watermark_text" :style="watermarkStyle" aria-hidden="true">{{ s.watermark_text }}</span>
    </div>
    <!-- Content -->
    <div class="mb-relative mb-flex mb-w-full" :style="contentFlexStyle">
      <div :style="contentBlockStyle">
        <span v-if="s.eyebrow_text" :style="eyebrowStyle"><span v-if="s.eyebrow_dot" :style="dotStyle"></span><span data-olo-editable="eyebrow_text">{{ s.eyebrow_text }}</span></span>
        <component :is="s.title_tag || 'h1'" :style="titleStyle" style="margin:0 0 12px 0" class="olo-hero-title-v" data-olo-editable="title" v-html="safeInline(s.title || 'Hero Title')"></component>
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

        <!-- Riga meta / data (opzionale) -->
        <div v-if="s.meta_text" :style="metaStyle"><span data-olo-editable="meta_text">{{ s.meta_text }}</span></div>
      </div>
    </div>

    <!-- Modulo sotto il contenuto (opzionale, unificazione hero Fase 1) -->
    <div v-if="s.module === 'strip' && stripItems.length" :style="moduleWrapStyle">
      <div :style="stripRowStyle">
        <div v-for="(it, i) in stripItems" :key="i" :style="stripMediaStyle(it, i)">
          <span v-if="it.caption" :style="stripCapStyle">{{ it.caption }}</span>
        </div>
      </div>
    </div>
    <div v-else-if="s.module === 'search'" :style="moduleWrapStyle">
      <div :style="searchBoxStyle">
        <span :style="searchInputStyle">{{ s.search_placeholder || 'Cerca…' }}</span>
        <span v-if="s.search_button" class="mb-inline-block mb-font-semibold mb-text-sm" :style="ctaStyle(1)">{{ s.search_button }}</span>
      </div>
      <div v-if="searchChips.length" :style="chipsWrapStyle">
        <span v-for="(c, i) in searchChips" :key="i" :style="chipStyle">{{ c }}</span>
      </div>
    </div>
    <div v-else-if="s.module === 'mockup'" :style="moduleWrapStyle">
      <div :style="mockFrameStyle">
        <div :style="winBarStyle">
          <span :style="winDotStyle"></span><span :style="winDotStyle"></span><span :style="winDotStyle"></span>
          <span v-if="s.mock_url" :style="winLabelStyle">{{ s.mock_url }}</span>
        </div>
        <div v-if="mockMode === 'media'" :style="mockMediaStyle">
          <span v-if="s.mock_label && !hasMockMedia" :style="mockLabelStyle">{{ s.mock_label }}</span>
        </div>
        <div v-else :style="mockBodyStyle">
          <div v-for="(k, i) in mockKpis" :key="'k' + i" :style="kpiCardStyle">
            <div :style="kpiLabelStyle">{{ k.label }}</div>
            <div :style="kpiValueStyle">{{ k.value }}</div>
            <div v-if="k.delta" :style="kpiDeltaStyle(k)">{{ k.delta }}</div>
          </div>
          <div :style="chartCardStyle">
            <div :style="chartHeadStyle"><b>{{ s.mock_chart_title }}</b><span :style="chartMetaStyle">{{ s.mock_chart_meta }}</span></div>
            <div :style="chartBarsStyle">
              <div v-for="(b, i) in mockBars" :key="'b' + i" :style="chartColStyle">
                <span :style="chartBarStyle(b)"></span><span v-if="b.label" :style="chartBarLabelStyle">{{ b.label }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else-if="s.module === 'chat'" :style="moduleWrapStyle">
      <div :style="chatWinStyle">
        <div :style="winBarStyle">
          <span :style="winDotStyle"></span><span :style="winDotStyle"></span><span :style="winDotStyle"></span>
          <span v-if="s.chat_label" :style="winLabelStyle">{{ s.chat_label }}</span>
        </div>
        <div :style="chatBodyStyle">
          <div v-for="(m, i) in chatMessages" :key="i" :style="chatMsgStyle(m)">{{ m.text }}</div>
        </div>
      </div>
    </div>

    <!-- Hint di scroll (opzionale) -->
    <span v-if="s.scroll_hint" :style="scrollHintStyle">{{ s.scroll_hint }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  title: 'Benvenuto nel nostro sito',
  subtitle: 'Scopri qualcosa di straordinario',
  // Scena (unificazione hero, Fase 1) — default no-op, parità col PHP
  eyebrow_text: '',
  eyebrow_dot: false,
  media_bg: { type: 'none' },
  overlay_color: '',
  overlay_top: 0,
  overlay_bottom: 0,
  overlay_sides: false,
  glow_on: false,
  glow_color: '',
  glow_w: 760,
  glow_h: 560,
  glow_blur: 100,
  glow_x: 50,
  glow_y: 20,
  arch: false,
  frame_on: false,
  frame_inset: 24,
  watermark_text: '',
  watermark_color: '',
  accent: '',
  meta_text: '',
  scroll_hint: '',
  // Modulo sotto il contenuto — default no-op, parità col config/PHP
  module: '',
  strip_items: [],
  strip_offset: 28,
  strip_radius: 200,
  search_placeholder: 'Cerca…',
  search_button: 'Cerca',
  search_url: '',
  search_chips: '',
  mock_mode: 'media',
  mock_media: { type: 'none' },
  mock_label: 'screenshot prodotto — 16/8.5',
  mock_url: 'app.tuoprodotto.com',
  mock_kpis: [],
  mock_chart_title: 'Revenue',
  mock_chart_meta: 'ultimi 12 mesi',
  mock_bars: [],
  chat_label: 'workspace',
  chat_messages: [],
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

/* ══ Scena (unificazione hero, Fase 1) — media + glow + velo + watermark ══
   Tutti i computed sono no-op coi default: hasScene false → markup identico a prima. */
const accentColor = computed(() => s.value.accent || 'var(--olo-color-primary, #e1474f)');
const hasMediaBg = computed(() => { const m = s.value.media_bg; return !!(m && m.type && m.type !== 'none'); });
const oTop = computed(() => Math.max(0, Math.min(1, parseFloat(s.value.overlay_top) || 0)));
const oBot = computed(() => Math.max(0, Math.min(1, parseFloat(s.value.overlay_bottom) || 0)));
const hasOverlay = computed(() => oTop.value > 0 || oBot.value > 0);
const hasScene = computed(() => hasMediaBg.value || s.value.glow_on || hasOverlay.value || !!s.value.watermark_text);

/* color-mix: velo su QUALSIASI colore, token inclusi (parità col PHP) */
function mix(color, alpha) {
  return `color-mix(in srgb, ${color} ${Math.round(alpha * 100)}%, transparent)`;
}

const sceneStyle = computed(() => {
  const st = { position: 'absolute', inset: 0, zIndex: 0, overflow: 'hidden', pointerEvents: 'none' };
  if (s.value.frame_on) {
    const fi = Math.max(0, parseInt(s.value.frame_inset, 10) || 24);
    st.inset = fi + 'px';
  }
  if (s.value.arch) {
    const m = 'radial-gradient(150% 125% at 50% 0%, #000 87%, transparent 87.5%)';
    st.webkitMask = m;
    st.mask = m;
  }
  return st;
});

const mediaLayerStyle = computed(() => ({
  position: 'absolute', inset: 0, backgroundSize: 'cover', backgroundPosition: 'center center',
  ...buildBgStyle(s.value.media_bg),
}));

const overlayStyle = computed(() => {
  const oc = s.value.overlay_color || 'var(--olo-color-dark, #16263d)';
  const oMid = Math.round(oTop.value * 0.6 * 1000) / 1000;
  const gradV = `linear-gradient(180deg, ${mix(oc, oTop.value)} 0%, ${mix(oc, oMid)} 38%, ${mix(oc, oBot.value)} 100%)`;
  let grad = gradV;
  if (s.value.overlay_sides) {
    const sTop = Math.round(oTop.value * 0.4 * 1000) / 1000;
    const sBot = Math.round(oBot.value * 0.7 * 1000) / 1000;
    grad = `linear-gradient(90deg, ${mix(oc, oBot.value)} 0%, ${mix(oc, sTop)} 52%, ${mix(oc, sBot)} 100%), ` + gradV;
  }
  return { position: 'absolute', inset: 0, background: grad };
});

const glowStyle = computed(() => {
  const gc = s.value.glow_color || mix(accentColor.value, 0.2);
  const gw = Math.max(100, parseInt(s.value.glow_w, 10) || 760);
  const gh = Math.max(100, parseInt(s.value.glow_h, 10) || 560);
  const gb = Math.max(0, parseInt(s.value.glow_blur, 10) || 100);
  const gxRaw = parseInt(s.value.glow_x, 10);
  const gx = Math.max(0, Math.min(100, isNaN(gxRaw) ? 50 : gxRaw));
  const gyRaw = parseInt(s.value.glow_y, 10);
  const gy = Math.max(-50, Math.min(100, isNaN(gyRaw) ? 20 : gyRaw));
  return {
    position: 'absolute', top: gy + '%', left: gx + '%', transform: 'translate(-50%,-30%)',
    width: gw + 'px', height: gh + 'px', borderRadius: '50%', filter: `blur(${gb}px)`,
    background: `radial-gradient(circle, ${gc}, transparent 70%)`,
  };
});

const watermarkStyle = computed(() => ({
  position: 'absolute', inset: 0, display: 'flex', alignItems: 'center', justifyContent: 'center',
  fontSize: 'clamp(120px, 26vw, 380px)', fontWeight: 800, lineHeight: 1, letterSpacing: '-0.02em',
  color: s.value.watermark_color || 'rgba(255,255,255,.06)', userSelect: 'none', whiteSpace: 'nowrap',
}));

/* ══ Modulo sotto il contenuto (unificazione hero, Fase 1) ══ */
const moduleActive = computed(() => ['strip', 'search', 'mockup', 'chat'].includes(s.value.module));
const stripItems = computed(() => (Array.isArray(s.value.strip_items) ? s.value.strip_items : []));
const searchChips = computed(() => String(s.value.search_chips || '').split(',').map((c) => c.trim()).filter(Boolean));

const hJustifyMap = { left: 'flex-start', center: 'center', right: 'flex-end' };
const moduleWrapStyle = computed(() => ({
  position: 'relative', zIndex: 1, padding: '0 30px clamp(40px,6vh,64px)', width: '100%',
}));
const stripRowStyle = computed(() => ({
  display: 'flex', gap: '14px', justifyContent: 'center', flexWrap: 'wrap',
  maxWidth: '1180px', margin: '0 auto',
}));
function stripMediaStyle(it, i) {
  const rad = Math.max(0, parseInt(s.value.strip_radius, 10) || 0);
  const off = Math.max(0, parseInt(s.value.strip_offset, 10) || 0);
  const st = {
    position: 'relative', overflow: 'hidden', width: 'clamp(150px,22vw,240px)', aspectRatio: '3/4',
    borderRadius: `${rad}px ${rad}px 8px 8px`,
    background: 'rgba(255,255,255,.06)',
    backgroundImage: it.image
      ? `url(${it.image})`
      : 'repeating-linear-gradient(135deg, rgba(243,233,239,.05) 0 16px, transparent 16px 32px)',
    backgroundSize: 'cover', backgroundPosition: 'center center',
  };
  if (i === 1 && off) st.marginTop = `-${off}px`;
  return st;
}
const stripCapStyle = {
  position: 'absolute', left: '14px', bottom: '12px', right: '14px',
  fontSize: '10.5px', letterSpacing: '.1em', textTransform: 'uppercase', color: 'rgba(243,233,239,.4)',
};
const searchBoxStyle = computed(() => {
  const h = s.value.horizontal_align || 'center';
  const margin = h === 'center' ? '0 auto' : (h === 'right' ? '0 0 0 auto' : '0');
  return {
    display: 'flex', gap: '8px', alignItems: 'center', maxWidth: '560px', margin,
    background: 'rgba(255,255,255,.07)',
    border: `1px solid ${mix(accentColor.value, 0.4)}`,
    borderRadius: '14px', padding: '8px',
  };
});
const searchInputStyle = { flex: 1, padding: '12px 14px', fontSize: '15px', opacity: 0.55, minWidth: 0, textAlign: 'left' };
const chipsWrapStyle = computed(() => ({
  display: 'flex', gap: '8px', flexWrap: 'wrap', maxWidth: '560px',
  justifyContent: hJustifyMap[s.value.horizontal_align] || 'center',
  margin: (s.value.horizontal_align || 'center') === 'center' ? '18px auto 0' : '18px 0 0',
}));
const chipStyle = {
  fontSize: '13px', fontWeight: 600, opacity: 0.85,
  border: '1px solid rgba(255,255,255,.16)', borderRadius: '999px', padding: '7px 15px',
};

/* ── Moduli mockup + chat — pannelli in color-mix(currentColor) così si adattano
   da soli a scene chiare e scure, zero campi colore aggiuntivi. ── */
const mixC = (pct) => `color-mix(in srgb, currentColor ${pct}%, transparent)`;
const MONO = "var(--olo-font-family-mono, ui-monospace,'SF Mono',Menlo,monospace)";
const mockMode = computed(() => (s.value.mock_mode === 'dashboard' ? 'dashboard' : 'media'));
const hasMockMedia = computed(() => { const m = s.value.mock_media; return !!(m && m.type && m.type !== 'none'); });
const mockKpis = computed(() => (Array.isArray(s.value.mock_kpis) ? s.value.mock_kpis : []));
const mockBars = computed(() => (Array.isArray(s.value.mock_bars) ? s.value.mock_bars : []));
const chatMessages = computed(() => (Array.isArray(s.value.chat_messages) ? s.value.chat_messages : []).filter((m) => m && m.text));

const winBarStyle = computed(() => ({
  display: 'flex', alignItems: 'center', gap: '7px', padding: '13px 16px',
  borderBottom: `1px solid ${mixC(10)}`, background: mixC(7),
}));
const winDotStyle = computed(() => ({ width: '11px', height: '11px', borderRadius: '50%', background: mixC(18), flex: 'none' }));
const winLabelStyle = computed(() => ({ marginLeft: '13px', fontFamily: MONO, fontSize: '11px', opacity: 0.6 }));

const mockFrameStyle = computed(() => ({
  maxWidth: '1020px', margin: '0 auto', border: `1px solid ${mixC(10)}`, borderRadius: '16px 16px 0 0',
  background: mixC(5), overflow: 'hidden', textAlign: 'left',
  boxShadow: `0 -10px 80px -20px ${mix(accentColor.value, 0.4)}`,
}));
const mockMediaStyle = computed(() => ({
  position: 'relative', overflow: 'hidden', aspectRatio: '16/8.5',
  backgroundImage: 'repeating-linear-gradient(135deg, rgba(255,255,255,.035) 0 16px, transparent 16px 32px)',
  backgroundSize: 'cover', backgroundPosition: 'center center',
  ...(hasMockMedia.value ? buildBgStyle(s.value.mock_media) : {}),
}));
const mockLabelStyle = computed(() => ({
  position: 'absolute', left: '14px', bottom: '12px', right: '14px', fontFamily: MONO,
  fontSize: '10.5px', letterSpacing: '.03em', textTransform: 'uppercase', opacity: 0.45, textAlign: 'left',
}));
const mockBodyStyle = { display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '14px', padding: '20px', textAlign: 'left' };
const kpiCardStyle = computed(() => ({ background: mixC(5), border: `1px solid ${mixC(10)}`, borderRadius: '11px', padding: '16px' }));
const kpiLabelStyle = computed(() => ({ fontFamily: MONO, fontSize: '10.5px', letterSpacing: '.06em', textTransform: 'uppercase', opacity: 0.6 }));
const kpiValueStyle = { fontWeight: 700, fontSize: '26px', margin: '7px 0 4px', lineHeight: 1.1 };
function kpiDeltaStyle(k) {
  return { fontFamily: MONO, fontSize: '11px', ...(k.down ? { opacity: 0.6 } : { color: accentColor.value }) };
}
const chartCardStyle = computed(() => ({ gridColumn: '1/-1', background: mixC(5), border: `1px solid ${mixC(10)}`, borderRadius: '11px', padding: '18px 18px 10px' }));
const chartHeadStyle = { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px', fontSize: '14px' };
const chartMetaStyle = computed(() => ({ fontFamily: MONO, fontSize: '11px', opacity: 0.6 }));
const chartBarsStyle = { display: 'flex', alignItems: 'flex-end', gap: '7px', height: '120px' };
const chartColStyle = { flex: 1, display: 'flex', flexDirection: 'column', justifyContent: 'flex-end', gap: '3px', height: '100%' };
function chartBarStyle(b) {
  const h = Math.max(0, Math.min(100, parseInt(b.h, 10) || 0));
  const grad = b.alt
    ? `linear-gradient(180deg, ${mixC(50)}, ${mixC(15)})`
    : `linear-gradient(180deg, ${accentColor.value}, ${mix(accentColor.value, 0.25)})`;
  return { display: 'block', width: '100%', borderRadius: '3px 3px 0 0', background: grad, minHeight: '4px', height: h + '%' };
}
const chartBarLabelStyle = computed(() => ({ fontFamily: MONO, fontSize: '9.5px', textAlign: 'center', opacity: 0.55 }));

const chatWinStyle = computed(() => ({
  maxWidth: '760px', margin: '0 auto', border: `1px solid ${mixC(10)}`, borderRadius: '16px 16px 0 0',
  background: mixC(5), overflow: 'hidden', textAlign: 'left',
  boxShadow: `0 -10px 90px -24px ${mix(accentColor.value, 0.4)}`,
}));
const chatBodyStyle = { padding: '22px', display: 'flex', flexDirection: 'column', gap: '16px' };
function chatMsgStyle(m) {
  const base = { maxWidth: '80%', padding: '13px 16px', borderRadius: '14px', fontSize: '14.5px', lineHeight: 1.5 };
  if (m.side === 'you') {
    return { ...base, alignSelf: 'flex-end', background: accentColor.value, color: '#fff', borderBottomRightRadius: '4px' };
  }
  return { ...base, alignSelf: 'flex-start', background: mixC(8), border: `1px solid ${mixC(10)}`, borderBottomLeftRadius: '4px', opacity: 0.92 };
}

/* ── Container (sfondo gestito dal wrapper esterno via tile.style.bg) ── */
const containerStyle = computed(() => {
  const st = {
    minHeight: s.value.min_height || '500px',
    color: s.value.text_color || 'var(--olo-color-on-primary, #FFFFFF)',
    display: 'flex',
  };
  if (moduleActive.value) st.flexDirection = 'column';
  if (hasScene.value) st.overflow = 'hidden';
  if (s.value.accent) st['--olo-hero-accent'] = s.value.accent;
  return st;
});

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
    zIndex: 1,
    alignItems: vAlignMap[s.value.vertical_align] || 'center',
    justifyContent: hAlignMap[s.value.horizontal_align] || 'center',
    padding: `${t}px ${r}px ${b}px ${l}px`,
  };
});

/* ── Eyebrow / meta / scroll hint (unificazione hero, Fase 1) ── */
const eyebrowStyle = computed(() => ({
  display: 'inline-flex', alignItems: 'center', gap: '9px', fontWeight: 600, fontSize: '11.5px',
  letterSpacing: '.28em', textTransform: 'uppercase', color: accentColor.value, margin: '0 0 18px',
}));
const dotStyle = computed(() => ({
  width: '6px', height: '6px', borderRadius: '50%', background: accentColor.value,
  boxShadow: '0 0 8px ' + accentColor.value, flex: 'none',
}));
const metaStyle = computed(() => ({
  marginTop: '22px', fontSize: '14px', letterSpacing: '.16em', textTransform: 'uppercase', opacity: 0.85,
}));
const scrollHintStyle = computed(() => ({
  position: 'absolute', bottom: '28px', left: '50%', transform: 'translateX(-50%)', zIndex: 2,
  fontSize: '11px', letterSpacing: '.16em', textTransform: 'uppercase', opacity: 0.7,
}));

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

<style scoped>
/* Parola accento: le parti in corsivo (em) del titolo prendono il Colore accento.
   Con accent vuoto la var non è definita → fallback inherit = resa invariata. */
.olo-hero-title-v :deep(em) { color: var(--olo-hero-accent, inherit); }
</style>
