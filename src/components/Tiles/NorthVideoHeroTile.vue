<template>
  <section class="olo-northvideohero nvh" :style="rootStyle">
    <span v-if="s.bg_fixed_image" class="nvh-grass" :style="grassStyle" aria-hidden="true"></span>
    <div class="nvh-in" :style="inStyle">
      <div class="nvh-head" :style="headStyle">
        <span v-if="s.crest_on" class="nvh-crest" :style="crestStyle" aria-hidden="true">
          <svg viewBox="0 0 100 100" fill="none" :stroke="crestC" stroke-width="1">
            <circle cx="50" cy="50" r="46" />
            <ellipse cx="50" cy="50" rx="46" ry="16" transform="rotate(35 50 50)" />
            <ellipse cx="50" cy="50" rx="16" ry="46" transform="rotate(35 50 50)" />
            <line x1="50" y1="4" x2="50" y2="96" />
          </svg>
        </span>
        <div class="nvh-text" style="flex:1 1 auto;min-width:0">
          <span v-if="s.eyebrow_text" class="nvh-eyebrow" :style="eyebrowStyle">{{ s.eyebrow_text }}</span>
          <h1 class="nvh-h" :style="hStyle">{{ s.headline_text }}<template v-if="s.accent_text"> <span :style="{ color: accent }">{{ s.accent_text }}</span></template></h1>
          <p v-if="s.subhead" class="nvh-sub" :style="subStyle">{{ s.subhead }}</p>
          <div v-if="s.cta1_text || s.cta2_text" class="nvh-cta" :style="{ display: 'flex', gap: '12px', flexWrap: 'wrap', marginTop: '32px' }">
            <a v-if="s.cta1_text" class="nvh-btn" :style="solidBtnStyle" :href="s.cta1_url || '#'">{{ s.cta1_text }}</a>
            <a v-if="s.cta2_text" class="nvh-btn" :style="ghostBtnStyle" :href="s.cta2_url || '#'">{{ s.cta2_text }}</a>
          </div>
        </div>
      </div>
    </div>
    <div v-if="mode !== 'none'" class="nvh-mockwrap" :style="mockWrapStyle">
      <div class="nvh-frame" :style="frameStyle">
        <video v-if="mode === 'video' && s.video_src" class="nvh-video" :style="videoStyle"
               preload="metadata" :muted="!!s.muted" :loop="!!s.loop" controls
               :poster="s.video_poster || undefined" playsinline>
          <source :src="s.video_src" :type="videoMime" />
        </video>
        <img v-else-if="mode === 'video' && s.video_poster" class="nvh-video" :style="videoStyle" :src="s.video_poster" alt="" loading="lazy" />
        <div v-else class="nvh-media" :style="mediaStyle">
          <span v-if="s.media_label" class="nvh-medialabel" :style="mediaLabelStyle">{{ s.media_label }}</span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { borderDefault, borderHoverDefault, borderEffectDefaults } from '@/config/elements/_shared.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  eyebrow_text: 'NORTH',
  crest_on: true,
  headline_text: 'AI for business that turns complexity into clarity',
  accent_text: '',
  subhead: '',
  cta1_text: '', cta1_url: '#',
  cta2_text: '', cta2_url: '#',
  mock_mode: 'video',
  video_src: '', video_poster: '',
  show_controls: true, autoplay: false, muted: true, loop: false,
  media_label: 'product — North workspace',
  mock_reveal: true,
  bg_fixed_image: '', bg_fixed_from: 42,
  headline_max: 1100,
  frame_radius: { tl: 20, tr: 20, br: 20, bl: 20 },
  content_padding: { top: 160, right: 40, bottom: 96, left: 40 },
  bg_color: '#062C22',
  text_color: '#ffffff',
  eyebrow_color: 'rgba(255,255,255,0.78)',
  sub_color: 'rgba(255,255,255,0.72)',
  accent: '',
  crest_color: 'rgba(255,255,255,0.5)',
  frame_bg: '#0a201a',
  frame_border: 'rgba(255,255,255,0.12)',
  bg: { type: 'none' },
  shadow: 'none',
  border: { ...borderDefault },
  border_hover: { ...borderHoverDefault },
  border_hover_duration: 300,
  ...borderEffectDefaults,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const DISP = "var(--olo-font-family-heading, 'Space Grotesk',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Inter','Work Sans',-apple-system,sans-serif)";
const MONO = "var(--olo-font-family-mono, 'Space Mono',ui-monospace,'SF Mono',Menlo,monospace)";

const txt = computed(() => s.value.text_color || '#ffffff');
const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #ff7759)');
const crestC = computed(() => s.value.crest_color || 'rgba(255,255,255,0.5)');
const frameBg = computed(() => s.value.frame_bg || '#0a201a');
const frameBd = computed(() => s.value.frame_border || 'rgba(255,255,255,0.12)');
const mode = computed(() => (['video', 'media', 'none'].includes(s.value.mock_mode) ? s.value.mock_mode : 'video'));

const videoMime = computed(() => {
  const ext = String(s.value.video_src || '').split('?')[0].split('.').pop().toLowerCase();
  return ({ mp4: 'video/mp4', webm: 'video/webm', ogg: 'video/ogg', m3u8: 'application/x-mpegURL' })[ext] || 'video/mp4';
});

function radiusCss(v) {
  if (v && typeof v === 'object') {
    const tl = parseInt(v.tl ?? 0, 10) || 0, tr = parseInt(v.tr ?? 0, 10) || 0;
    const br = parseInt(v.br ?? 0, 10) || 0, bl = parseInt(v.bl ?? 0, 10) || 0;
    return (tl || tr || br || bl) ? `${tl}px ${tr}px ${br}px ${bl}px` : '0';
  }
  const n = parseInt(v, 10) || 0;
  return n > 0 ? `${n}px` : '0';
}
const frameRadius = computed(() => radiusCss(s.value.frame_radius));

const inPad = computed(() => {
  const v = s.value.content_padding;
  if (v && typeof v === 'object') {
    const tp = parseInt(v.top ?? 0, 10) || 0, r = parseInt(v.right ?? 0, 10) || 0;
    const b = parseInt(v.bottom ?? 0, 10) || 0, l = parseInt(v.left ?? 0, 10) || 0;
    return `${tp}px ${r}px ${b}px ${l}px`;
  }
  return '160px 40px 96px';
});

const kitBgStyle = computed(() => {
  const bg = s.value.bg;
  if (!bg || !bg.type || bg.type === 'none') return {};
  return buildBgStyle(bg);
});
const kitShadow = computed(() => {
  const p = s.value.shadow || 'none';
  if (p === 'none' || p === '') return '';
  if (p === 'custom') {
    const h = parseInt(s.value.shadow_h ?? 0, 10) || 0, v = parseInt(s.value.shadow_v ?? 4, 10) || 0;
    const blur = Math.max(0, parseInt(s.value.shadow_blur ?? 10, 10) || 0), spread = parseInt(s.value.shadow_spread ?? 0, 10) || 0;
    const color = s.value.shadow_color || 'rgba(0,0,0,0.15)', inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  const map = {
    sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
    md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
    lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
    xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
  };
  return map[p] || '';
});

const rootStyle = computed(() => {
  const st = { position: 'relative', overflow: 'hidden', background: s.value.bg_color || '#062C22', color: txt.value, fontFamily: SANS, '--nvh-accent': accent.value };
  Object.assign(st, kitBgStyle.value);
  if (kitShadow.value) st.boxShadow = kitShadow.value;
  return st;
});
const grassStyle = computed(() => {
  const from = Math.max(0, Math.min(100, parseInt(s.value.bg_fixed_from ?? 42, 10) || 0));
  const mask = `linear-gradient(180deg,transparent 0%,transparent ${from}%,#000 100%)`;
  return { position: 'absolute', inset: 0, zIndex: 0, backgroundImage: `url('${s.value.bg_fixed_image}')`, backgroundSize: 'cover', backgroundPosition: 'center', WebkitMask: mask, mask, pointerEvents: 'none' };
});
const inStyle = computed(() => ({ position: 'relative', zIndex: 2, maxWidth: '1280px', margin: '0 auto', padding: inPad.value }));
const headStyle = { display: 'flex', alignItems: 'flex-start', gap: 'clamp(20px,4vw,72px)' };
const crestStyle = { flex: '0 0 auto', width: 'clamp(56px,7vw,92px)', height: 'clamp(56px,7vw,92px)', marginTop: '6px' };
const eyebrowStyle = computed(() => ({ display: 'block', fontFamily: MONO, fontSize: '14px', lineHeight: 1.4, letterSpacing: '.02em', textTransform: 'uppercase', color: s.value.eyebrow_color || 'rgba(255,255,255,0.78)', margin: '0 0 26px' }));
const hStyle = computed(() => ({ fontFamily: DISP, fontWeight: 500, fontSize: 'clamp(40px,6.6vw,72px)', lineHeight: 1.0, letterSpacing: '-.02em', color: txt.value, margin: 0, maxWidth: (parseInt(s.value.headline_max, 10) || 1100) + 'px' }));
const subStyle = computed(() => ({ fontSize: '18px', lineHeight: 1.4, color: s.value.sub_color || 'rgba(255,255,255,0.72)', maxWidth: '560px', margin: '26px 0 0' }));
const solidBtnStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '14px 26px', borderRadius: '999px', fontFamily: SANS, fontWeight: 500, fontSize: '16px', textDecoration: 'none', background: '#fff', color: s.value.bg_color || '#062C22', border: 0 }));
const ghostBtnStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '14px 26px', borderRadius: '999px', fontFamily: SANS, fontWeight: 500, fontSize: '16px', textDecoration: 'none', background: 'rgba(255,255,255,.08)', color: txt.value, border: '1px solid rgba(255,255,255,.22)' }));
const mockWrapStyle = { position: 'relative', zIndex: 2, maxWidth: '1180px', margin: 'clamp(40px,6vw,72px) auto 0', padding: '0 40px' };
const frameStyle = computed(() => ({ position: 'relative', border: '1px solid ' + frameBd.value, borderRadius: frameRadius.value, background: frameBg.value, overflow: 'hidden', boxShadow: '0 40px 80px -40px rgba(0,0,0,.6)' }));
const videoStyle = computed(() => ({ display: 'block', width: '100%', height: 'auto', aspectRatio: '16/9.4', objectFit: 'cover', background: frameBg.value }));
const mediaStyle = computed(() => ({ position: 'relative', aspectRatio: '16/9.4', background: frameBg.value, backgroundImage: 'repeating-linear-gradient(135deg, rgba(255,255,255,.035) 0 16px, transparent 16px 32px)' }));
const mediaLabelStyle = { position: 'absolute', left: '18px', bottom: '14px', fontFamily: MONO, fontSize: '11px', letterSpacing: '.03em', color: 'rgba(255,255,255,.42)', textTransform: 'uppercase' };
</script>

<style scoped>
.nvh-btn { transition: transform .15s, filter .2s, background .2s; }
.nvh-btn:hover { transform: translateY(-2px); }
.nvh-btn:focus-visible { outline: 2px solid var(--nvh-accent, #ff7759); outline-offset: 3px; }
@media (max-width: 780px) {
  .nvh-head { flex-direction: column !important; gap: 24px !important; }
  .nvh-mockwrap { padding: 0 20px !important; }
}
</style>
