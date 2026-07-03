<template>
  <section class="olo-audiohero ah" :style="rootStyle">
    <span class="ah-bg" aria-hidden="true" :style="bgStyle"></span>
    <div class="ah-in" :style="inStyle">
      <div class="ah-col">
        <span v-if="s.tag_text" class="ah-tag" :style="tagStyle">
          <span class="ah-eq" aria-hidden="true" :style="eqStyle">
            <i v-for="n in 5" :key="n" :style="eqBarStyle(n)"></i>
          </span>{{ s.tag_text }}
        </span>
        <h1 class="ah-h" :style="hStyle">{{ s.headline_text }}</h1>
        <p v-if="s.subhead" class="ah-sub" :style="subStyle">{{ s.subhead }}</p>
        <div class="ah-cta" :style="ctaStyle">
          <a v-if="s.cta1_text" class="ah-btn ah-btn--solid" :style="solidStyle" :href="s.cta1_url || '#'">
            <svg viewBox="0 0 24 24" fill="currentColor" style="width:17px;height:17px"><path d="M8 5v14l11-7z"/></svg>{{ s.cta1_text }}
          </a>
          <a v-if="s.cta2_text" class="ah-btn ah-btn--ghost" :style="ghostStyle" :href="s.cta2_url || '#'">{{ s.cta2_text }}</a>
        </div>
      </div>
      <div class="ah-art" :style="{ position: 'relative' }">
        <div class="ah-cover" :style="coverStyle">
          <video
            v-if="isMediaVideo"
            class="ah-cover-video"
            :src="s.media_bg.video_url"
            autoplay loop muted playsinline
            style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:0"
          ></video>
          <span v-if="!hasMediaBg && !s.cover_image && s.cover_label" class="ah-cover-lab" :style="coverLabStyle">{{ s.cover_label }}</span>
        </div>
        <div v-if="s.show_player" class="ah-player" :style="playerStyle">
          <span class="ah-play" aria-hidden="true" :style="playStyle">
            <svg viewBox="0 0 24 24" fill="currentColor" :style="{ width: '16px', height: '16px', color: accOn }"><path d="M8 5v14l11-7z"/></svg>
          </span>
          <div class="ah-pmeta" :style="{ flex: 1, minWidth: 0 }">
            <b :style="pTrackStyle">{{ s.player_track }}</b><span :style="pMetaStyle">{{ s.player_meta }}</span>
          </div>
          <div class="ah-wave" aria-hidden="true" :style="waveStyle">
            <i v-for="(h, i) in wave" :key="i" :style="waveBarStyle(h)"></i>
          </div>
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
  tag_text: 'New album · out now',
  headline_text: 'Nightglass',
  subhead: 'Eleven tracks recorded between Berlin and a cabin with bad wifi. Late-night electronics for headphones and dancefloors alike.',
  cta1_text: 'Play album', cta1_url: '#listen',
  cta2_text: 'See tour dates', cta2_url: '#tour',
  cover_image: '', cover_label: 'album cover — Nightglass, neon on black',
  object_position: 'center center',
  player_track: 'Glasshouse', player_meta: 'Kova · Nightglass', show_player: true,
  bg_color: '#0c0c10', panel_color: '#16161d',
  accent: '#27e0a3', accent_2: '#ff5d9e', accent_on: '#060608',
  text_color: '#ffffff', sub_color: '#b6b6c2', meta_color: '#74747f',
  split_ratio: '1.1fr .9fr',

  // Spaziatura (override gated del padding interno responsivo) — default no-op.
  pad_custom: false,
  content_padding: { top: 72, right: 28, bottom: 72, left: 28 },

  // Forma — raggi additivi (default = raggi attuali hardcoded → no-op).
  cover_radius: { tl: 18, tr: 18, br: 18, bl: 18 },
  player_radius: { tl: 14, tr: 14, br: 14, bl: 14 },

  // KIT standard OLObuild — sfondo completo + ombra + bordo (default no-op)
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

// ── Copertina universale (pannello media_bg) con fallback all'immagine legacy cover_image ──
const hasMediaBg = computed(() => {
  const m = s.value.media_bg;
  return !!(m && typeof m === 'object' && m.type && m.type !== 'none');
});
const mediaBgStyle = computed(() => (hasMediaBg.value ? buildBgStyle(s.value.media_bg) : {}));
const isMediaVideo = computed(() => {
  const m = s.value.media_bg;
  return hasMediaBg.value && m.type === 'video' && !!m.video_url;
});

const DISP = "var(--olo-font-family-heading, 'Unbounded',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Figtree',-apple-system,sans-serif)";

const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #27e0a3)');
const accent2 = computed(() => s.value.accent_2 || '#ff5d9e');
const accOn = computed(() => s.value.accent_on || '#060608');
const txt = computed(() => s.value.text_color || '#ffffff');
const sub = computed(() => s.value.sub_color || '#b6b6c2');
const meta = computed(() => s.value.meta_color || '#74747f');
const panel = computed(() => s.value.panel_color || '#16161d');

// ── Spaziatura: padding interno. Default (pad_custom=false) = clamp responsivo
// invariato (parità col PHP); se attivo, padding fisso dal field spacing.
const padIn = computed(() => {
  if (!s.value.pad_custom) return 'clamp(48px,7vw,96px) 28px';
  const cp = s.value.content_padding || {};
  const t = Math.max(0, parseInt(cp.top, 10) || 0);
  const r = Math.max(0, parseInt(cp.right, 10) || 0);
  const b = Math.max(0, parseInt(cp.bottom, 10) || 0);
  const l = Math.max(0, parseInt(cp.left, 10) || 0);
  return `${t}px ${r}px ${b}px ${l}px`;
});

// ── Forma: raggi (default = valori hardcoded attuali → no-op). ──
function radiusCss(v, fallback) {
  if (v && typeof v === 'object') {
    const tl = parseInt(v.tl, 10) || 0;
    const tr = parseInt(v.tr, 10) || 0;
    const br = parseInt(v.br, 10) || 0;
    const bl = parseInt(v.bl, 10) || 0;
    if (tl || tr || br || bl) return `${tl}px ${tr}px ${br}px ${bl}px`;
    return fallback;
  }
  const n = parseInt(v, 10) || 0;
  return n > 0 ? `${n}px` : fallback;
}
const coverRadius = computed(() => radiusCss(s.value.cover_radius, '18px'));
const playerRadius = computed(() => radiusCss(s.value.player_radius, '14px'));

function rgbaFrom(color, alpha) {
  let h = String(color || '').replace('#', '');
  if (h.length === 3) h = h.split('').map(c => c + c).join('');
  if (!/^[0-9a-fA-F]{6}$/.test(h)) return `rgba(39,224,163,${alpha})`;
  return `rgba(${parseInt(h.slice(0, 2), 16)},${parseInt(h.slice(2, 4), 16)},${parseInt(h.slice(4, 6), 16)},${alpha})`;
}

const wave = [30, 60, 90, 50, 75, 40, 85, 55, 95, 35, 70, 45];
const EQ_H = [40, 90, 60, 100, 50];
const EQ_DELAY = [0, 0.2, 0.4, 0.1, 0.3];

// ── KIT standard OLObuild — sfondo completo + ombra + bordo (parità col PHP) ──
// Sfondo completo: override SOLO se valorizzato (default 'none' = {} no-op).
const bgKitStyle = computed(() => buildBgStyle(s.value.bg));

// Ombra: preset sm/md/lg/xl o custom (stessi valori del PHP build_shadow_decl).
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};
const shadowKit = computed(() => {
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

// Bordo base: mirror esatto del PHP build_border_css/parse_border.
// Nessun bordo se il colore è vuoto (= default no-op). Lati letti direttamente.
const borderKit = computed(() => {
  const b = s.value.border || {};
  const c = (b.color || '').trim();
  const out = {};
  if (c === '') return out;
  const t = Math.max(0, parseInt(b.top, 10) || 0);
  const r = Math.max(0, parseInt(b.right, 10) || 0);
  const bo = Math.max(0, parseInt(b.bottom, 10) || 0);
  const l = Math.max(0, parseInt(b.left, 10) || 0);
  const st = b.style || 'solid';
  if (!t && !r && !bo && !l) return out;
  if (t === r && r === bo && bo === l) {
    out.border = `${t}px ${st} ${c}`;
    return out;
  }
  if (t) out.borderTop = `${t}px ${st} ${c}`;
  if (r) out.borderRight = `${r}px ${st} ${c}`;
  if (bo) out.borderBottom = `${bo}px ${st} ${c}`;
  if (l) out.borderLeft = `${l}px ${st} ${c}`;
  return out;
});

const rootStyle = computed(() => {
  const st = { position: 'relative', overflow: 'hidden', background: s.value.bg_color || '#0c0c10', color: txt.value, fontFamily: SANS, '--ah-accent': accent.value };
  // Sfondo completo (override) solo se valorizzato.
  Object.assign(st, bgKitStyle.value);
  if (shadowKit.value) st.boxShadow = shadowKit.value;
  Object.assign(st, borderKit.value);
  return st;
});
const bgStyle = computed(() => ({ position: 'absolute', inset: 0, zIndex: 0, background: `radial-gradient(70% 70% at 80% 15%, ${rgbaFrom(accent.value, 0.2)}, transparent 55%),radial-gradient(60% 60% at 15% 90%, ${rgbaFrom(accent2.value, 0.14)}, transparent 55%)` }));
const inStyle = computed(() => ({ position: 'relative', zIndex: 2, width: '100%', maxWidth: '1180px', margin: '0 auto', padding: padIn.value, display: 'grid', gridTemplateColumns: s.value.split_ratio || '1.1fr .9fr', gap: '48px', alignItems: 'center' }));

const tagStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '10px', fontFamily: SANS, fontWeight: 700, fontSize: '12px', letterSpacing: '.14em', textTransform: 'uppercase', color: accent.value, marginBottom: '22px' }));
const eqStyle = { display: 'inline-flex', alignItems: 'flex-end', gap: '3px', height: '18px' };
const eqBarStyle = (n) => ({ width: '3px', background: accent.value, borderRadius: '2px', height: EQ_H[n - 1] + '%', animation: 'oloAhEq 1s ease-in-out infinite', animationDelay: EQ_DELAY[n - 1] + 's' });

const hStyle = computed(() => ({ fontFamily: DISP, fontWeight: 700, fontSize: 'clamp(48px,8vw,108px)', lineHeight: .94, letterSpacing: '-.01em', color: txt.value, margin: 0 }));
const subStyle = computed(() => ({ fontSize: '18px', lineHeight: 1.6, color: sub.value, maxWidth: '420px', margin: '24px 0 30px' }));
const ctaStyle = { display: 'flex', gap: '13px', flexWrap: 'wrap', alignItems: 'center' };

const solidStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '9px', padding: '16px 30px', borderRadius: '999px', fontFamily: SANS, fontWeight: 700, fontSize: '15px', textDecoration: 'none', background: accent.value, color: accOn.value, border: 0, boxShadow: `0 10px 28px -10px ${rgbaFrom(accent.value, 0.6)}` }));
const ghostStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '9px', padding: '16px 30px', borderRadius: '999px', fontFamily: SANS, fontWeight: 700, fontSize: '15px', textDecoration: 'none', background: 'rgba(255,255,255,.05)', color: txt.value, border: '1px solid rgba(255,255,255,.18)' }));

const coverStyle = computed(() => {
  const st = { position: 'relative', aspectRatio: '1 / 1', borderRadius: coverRadius.value, overflow: 'hidden', border: '1px solid rgba(255,255,255,.09)', boxShadow: `0 30px 80px -30px ${rgbaFrom(accent.value, 0.4)}`, background: panel.value, backgroundSize: 'cover', backgroundPosition: (s.value.object_position || 'center center') };
  if (hasMediaBg.value) {
    // COPERTINA universale: precedenza sul media legacy. buildBgStyle sovrascrive
    // le proprietà background-* (image/gradient/solid…); il video ha layer proprio.
    Object.assign(st, mediaBgStyle.value);
  } else {
    st.backgroundImage = s.value.cover_image ? `url(${s.value.cover_image})` : 'repeating-linear-gradient(135deg, rgba(255,255,255,.04) 0 16px, transparent 16px 32px)';
  }
  return st;
});
const coverLabStyle = { position: 'absolute', left: '14px', bottom: '12px', right: '14px', fontFamily: SANS, fontWeight: 600, fontSize: '10.5px', letterSpacing: '.04em', color: 'rgba(255,255,255,.4)', textTransform: 'uppercase' };

const playerStyle = computed(() => ({ display: 'flex', alignItems: 'center', gap: '14px', marginTop: '16px', background: panel.value, border: '1px solid rgba(255,255,255,.09)', borderRadius: playerRadius.value, padding: '14px 16px' }));
const playStyle = computed(() => ({ width: '42px', height: '42px', borderRadius: '50%', background: accent.value, display: 'grid', placeItems: 'center', flex: 'none' }));
const pTrackStyle = computed(() => ({ color: txt.value, fontWeight: 700, fontSize: '14px', display: 'block', fontFamily: SANS }));
const pMetaStyle = computed(() => ({ fontSize: '12px', color: meta.value }));
const waveStyle = { display: 'flex', alignItems: 'center', gap: '2px', height: '26px', flex: 'none' };
const waveBarStyle = (h) => ({ width: '2.5px', background: accent.value, borderRadius: '2px', opacity: .5, height: h + '%' });
</script>

<style scoped>
@keyframes oloAhEq { 0%, 100% { transform: scaleY(.4); } 50% { transform: scaleY(1); } }
.ah-btn { transition: transform .15s, background .2s, box-shadow .2s, filter .2s; }
.ah-btn:hover { transform: translateY(-2px); }
.ah-btn--solid:hover { filter: brightness(1.04); }
.ah-btn--ghost:hover { border-color: var(--ah-accent, #27e0a3); color: var(--ah-accent, #27e0a3); }
.ah-btn:focus-visible { outline: 2px solid var(--ah-accent, #27e0a3); outline-offset: 3px; }
@media (prefers-reduced-motion: reduce) { .ah-eq i { animation: none !important; } }
@media (max-width: 880px) { .ah-in { grid-template-columns: 1fr !important; gap: 40px !important; } }
</style>
