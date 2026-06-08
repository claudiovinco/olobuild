<template>
  <section class="olo-photocover pc" :style="rootStyle">
    <div class="pc-media" :style="mediaStyle">
      <span v-if="!s.bg_image && s.media_label" class="pc-medialabel" :style="mediaLabelStyle">{{ s.media_label }}</span>
    </div>
    <div class="pc-overlay" :style="overlayStyle"></div>
    <div class="pc-in" :style="inStyle">
      <span v-if="s.kicker_text" class="pc-kicker" :style="kickerStyle">{{ s.kicker_text }}</span>
      <h1 v-if="s.headline_text" class="pc-h" :style="hStyle">{{ s.headline_text }}</h1>
      <div v-if="metaItems.length" class="pc-meta" :style="metaStyle">
        <span v-for="(m, i) in metaItems" :key="i">{{ m.text }}</span>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  kicker_text: 'Photo Essay · Issue 41',
  headline_text: 'The City After Rain',
  uppercase: true,
  meta_items: [
    { text: 'Photographs · Yuki Mori' },
    { text: '28 frames' },
    { text: '12 min' },
  ],
  bg_image: '',
  media_label: 'cover photograph — rain-soaked city street, single figure',
  aspect_ratio: '16/9',
  min_height: 560,
  overlay_top: 0.3,
  overlay_bottom: 0.85,
  frame_padding: 28,
  media_bg: '#1a1a1a',
  kicker_color: '',
  headline_color: '#ffffff',
  meta_color: '#e8e8e8',

  // SPAZIATURA — override gated del padding del contenuto (.pc-in). Default no-op.
  pad_custom: false,
  content_padding: { top: 28, right: 28, bottom: 28, left: 28 },

  // FORMA — raggio della foto di copertina (.pc-media). Default 0 = no-op.
  media_radius: { tl: 0, tr: 0, br: 0, bl: 0 },

  // KIT standard OLObuild (sfondo + ombra + bordo) sul contenitore principale.
  // Default no-op: con questi valori il render resta identico a prima.
  bg: { type: 'none' },
  shadow: 'none',
  border: {},
  border_hover: {},
  border_hover_duration: 300,
  border_effect: 'none',
  border_effect_intensity: 'medium',
  border_effect_color2: '',
  border_effect_angle: 135,
  border_effect_speed: 4,
};

// ── KIT: box-shadow (gemello PHP build_shadow_decl) ──
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};
function shadowDecl(set) {
  const preset = set.shadow || 'none';
  if (preset === 'none' || preset === '') return '';
  if (preset === 'custom') {
    const h = parseInt(set.shadow_h ?? 0, 10) || 0;
    const v = parseInt(set.shadow_v ?? 4, 10) || 0;
    const blur = Math.max(0, parseInt(set.shadow_blur ?? 10, 10) || 0);
    const spread = parseInt(set.shadow_spread ?? 0, 10) || 0;
    const color = set.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = set.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  return SHADOW_MAP[preset] || '';
}

// ── KIT: bordo (gemello PHP build_border_css) ──
function borderStyle(b) {
  if (!b || typeof b !== 'object') return {};
  const t = parseInt(b.top ?? 0, 10) || 0;
  const r = parseInt(b.right ?? 0, 10) || 0;
  const bo = parseInt(b.bottom ?? 0, 10) || 0;
  const l = parseInt(b.left ?? 0, 10) || 0;
  if (!t && !r && !bo && !l) return {};
  const st = b.style || 'solid';
  const c = b.color || '';
  if (t === r && r === bo && bo === l) return { border: `${t}px ${st} ${c}` };
  const out = {};
  if (t) out.borderTop = `${t}px ${st} ${c}`;
  if (r) out.borderRight = `${r}px ${st} ${c}`;
  if (bo) out.borderBottom = `${bo}px ${st} ${c}`;
  if (l) out.borderLeft = `${l}px ${st} ${c}`;
  return out;
}

const s = computed(() => ({ ...defaults, ...props.settings }));

const DISP = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Archivo',-apple-system,sans-serif)";
const MONO = "var(--olo-font-family-mono, 'Archivo Narrow','Archivo',sans-serif)";

const kicker = computed(() => s.value.kicker_color || 'var(--olo-color-primary, #ff4438)');
const headline = computed(() => s.value.headline_color || '#ffffff');
const meta = computed(() => s.value.meta_color || '#e8e8e8');

const metaItems = computed(() =>
  (Array.isArray(s.value.meta_items) ? s.value.meta_items : [])
    .filter((m) => m && String(m.text || '').trim() !== '')
);

const aspect = computed(() => (/^\d+\s*\/\s*\d+$/.test(String(s.value.aspect_ratio)) ? s.value.aspect_ratio : '16/9'));
const mh = computed(() => Math.max(0, Math.min(1400, parseInt(s.value.min_height, 10) || 0)));
const fp = computed(() => Math.max(0, Math.min(200, parseInt(s.value.frame_padding, 10) || 0)));
const pad = computed(() => {
  // SPAZIATURA gated: se pad_custom è attivo usa content_padding, altrimenti il clamp responsivo.
  if (s.value.pad_custom) {
    const cp = s.value.content_padding || {};
    const pt = Math.max(0, parseInt(cp.top ?? 0, 10) || 0);
    const pr = Math.max(0, parseInt(cp.right ?? 0, 10) || 0);
    const pb = Math.max(0, parseInt(cp.bottom ?? 0, 10) || 0);
    const pl = Math.max(0, parseInt(cp.left ?? 0, 10) || 0);
    return `${pt}px ${pr}px ${pb}px ${pl}px`;
  }
  const lo = Math.max(8, fp.value);
  const hi = Math.max(16, fp.value * 2);
  return `clamp(${lo}px,5vw,${hi}px)`;
});

// FORMA: raggio foto (gemello PHP build_border_radius_css). '' (default 0) = no-op.
function radiusCss(br) {
  if (!br || typeof br !== 'object') return '';
  const tl = parseInt(br.tl ?? 0, 10) || 0;
  const tr = parseInt(br.tr ?? 0, 10) || 0;
  const brr = parseInt(br.br ?? 0, 10) || 0;
  const bl = parseInt(br.bl ?? 0, 10) || 0;
  if (tl || tr || brr || bl) return `${tl}px ${tr}px ${brr}px ${bl}px`;
  return '';
}
const mediaRadius = computed(() => radiusCss(s.value.media_radius));

const aTop = computed(() => Math.round(Math.max(0, Math.min(1, Number(s.value.overlay_top ?? 0.3))) * 1000) / 1000);
const aBot = computed(() => Math.round(Math.max(0, Math.min(1, Number(s.value.overlay_bottom ?? 0.85))) * 1000) / 1000);

const kitBgStyle = computed(() => {
  const bg = s.value.bg;
  if (!bg || !bg.type || bg.type === 'none') return {};
  return buildBgStyle(bg);
});
const rootStyle = computed(() => {
  const st = { position: 'relative', overflow: 'hidden', fontFamily: SANS, '--pc-accent': kicker.value };
  Object.assign(st, kitBgStyle.value, borderStyle(s.value.border));
  const sh = shadowDecl(s.value);
  if (sh) st.boxShadow = sh;
  return st;
});
const mediaStyle = computed(() => {
  const st = {
    position: 'relative', overflow: 'hidden', aspectRatio: aspect.value,
    background: s.value.media_bg || '#1a1a1a', backgroundSize: 'cover', backgroundPosition: 'center',
  };
  if (mh.value > 0) st.minHeight = mh.value + 'px';
  st.backgroundImage = s.value.bg_image
    ? 'url(' + s.value.bg_image + ')'
    : 'repeating-linear-gradient(135deg, rgba(255,255,255,.04) 0 16px, transparent 16px 32px)';
  if (mediaRadius.value) st.borderRadius = mediaRadius.value;
  return st;
});
const mediaLabelStyle = { position: 'absolute', left: '14px', bottom: '12px', right: '14px', fontFamily: MONO, fontSize: '11px', letterSpacing: '.04em', textTransform: 'uppercase', color: 'rgba(255,255,255,.4)' };
const overlayStyle = computed(() => ({ content: '""', position: 'absolute', inset: 0, zIndex: 1, background: `linear-gradient(180deg,rgba(0,0,0,${aTop.value}),transparent 35%,rgba(0,0,0,${aBot.value}))`, pointerEvents: 'none' }));
const inStyle = computed(() => ({ position: 'absolute', inset: 0, zIndex: 2, display: 'flex', flexDirection: 'column', justifyContent: 'flex-end', padding: pad.value }));
const kickerStyle = computed(() => ({ display: 'block', marginBottom: '14px', fontFamily: MONO, fontWeight: 600, fontSize: '12px', letterSpacing: '.16em', textTransform: 'uppercase', color: kicker.value }));
const hStyle = computed(() => ({ fontFamily: DISP, fontWeight: 800, fontSize: 'clamp(40px,8vw,120px)', lineHeight: .9, letterSpacing: '-.02em', textTransform: s.value.uppercase ? 'uppercase' : 'none', color: headline.value, maxWidth: '14ch', margin: 0 }));
const metaStyle = computed(() => ({ display: 'flex', gap: '18px', marginTop: '18px', fontFamily: MONO, fontSize: '13px', letterSpacing: '.06em', textTransform: 'uppercase', color: meta.value, flexWrap: 'wrap' }));
</script>

<style scoped>
.pc-meta a { color: inherit; text-decoration: none; }
.pc a:focus-visible { outline: 2px solid var(--pc-accent, #ff4438); outline-offset: 3px; }
</style>
