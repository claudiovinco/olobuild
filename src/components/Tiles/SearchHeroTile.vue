<template>
  <section class="olo-searchhero sh" :style="rootStyle">
    <span class="sh-glow" :style="glowStyle"></span>
    <div class="sh-in" :style="inStyle">
      <span v-if="s.eyebrow_text" class="sh-eyebrow" :style="eyebrowStyle">{{ s.eyebrow_text }}</span>
      <h1 class="sh-h" :style="hStyle">{{ s.headline_text }}<br/>{{ s.headline_line2 ? s.headline_line2 + ' ' : '' }}<span v-if="s.accent_text" class="sh-acc" :style="{ color: accent }">{{ s.accent_text }}</span></h1>
      <p v-if="s.subhead" class="sh-sub" :style="subStyle">{{ s.subhead }}</p>
      <div class="sh-search" :style="searchStyle">
        <input type="search" :placeholder="s.search_placeholder" :aria-label="s.search_placeholder" :style="inputStyle"/>
        <a v-if="s.search_button" class="sh-btn" :style="btnStyle" :href="s.search_url || '#'">{{ s.search_button }}</a>
      </div>
      <div v-if="chips.length" class="sh-chips" :style="chipsStyle">
        <a v-for="(chip, i) in chips" :key="i" class="sh-chip" :style="chipStyle" :href="s.search_url || '#'">{{ chip }}</a>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  eyebrow_text: 'Marketplace for independent makers',
  headline_text: 'Everything good,',
  headline_line2: 'from',
  accent_text: 'small shops.',
  subhead: 'Thousands of independent sellers, one cart, one checkout. Find the thing — and support the person who made it.',
  search_placeholder: 'Search 90,000+ handmade things…',
  search_button: 'Search',
  search_url: '#',
  chips: 'Ceramics, Prints, Jewellery, Homeware, Vintage, Gifts',
  bg_color: '#1a1a22',
  panel_color: '#26262f',
  accent: '#ff5a5f',
  accent_on: '#ffffff',
  glow_color: 'rgba(255,90,95,0.22)',
  text_color: '#ffffff',
  sub_color: '#6c6c7c',
  chip_color: '#a6a6b4',
  border_color: 'rgba(255,255,255,0.09)',
  search_border: 'rgba(255,90,95,0.4)',
  min_height: 0,

  // Spaziatura (additivo, no-op): override GATED del padding responsive del contenitore.
  pad_custom: false,
  content_padding: { top: 52, right: 0, bottom: 52, left: 0 },

  // Raggio (additivo, no-op): raggio della barra di ricerca (firma) — default 14px.
  search_radius: { tl: 14, tr: 14, br: 14, bl: 14 },

  // KIT standard OLObuild (additivo, no-op coi default) — contenitore principale
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

const DISP = "var(--olo-font-family-heading, 'Mona Sans',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Mona Sans',-apple-system,sans-serif)";

const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #ff5a5f)');
const accOn = computed(() => s.value.accent_on || '#ffffff');
const txt = computed(() => s.value.text_color || '#ffffff');
const sub = computed(() => s.value.sub_color || '#6c6c7c');
const glow = computed(() => s.value.glow_color || 'rgba(255,90,95,0.22)');
const panel = computed(() => s.value.panel_color || '#26262f');
const line = computed(() => s.value.border_color || 'rgba(255,255,255,0.09)');
const sline = computed(() => s.value.search_border || 'rgba(255,90,95,0.4)');
const chipCol = computed(() => s.value.chip_color || '#a6a6b4');

const chips = computed(() => String(s.value.chips || '').split(',').map(c => c.trim()).filter(c => c.length));

// ── Spaziatura: override GATED del padding (parità col PHP, no-op coi default) ──
// pad_custom false → mantiene clamp(52px,7vw,92px) 0 (responsivo, invariato).
const padDecl = computed(() => {
  const cp = s.value.content_padding;
  if (s.value.pad_custom && cp && typeof cp === 'object') {
    const pt = Math.max(0, parseInt(cp.top ?? 0, 10) || 0);
    const pr = Math.max(0, parseInt(cp.right ?? 0, 10) || 0);
    const pb = Math.max(0, parseInt(cp.bottom ?? 0, 10) || 0);
    const pl = Math.max(0, parseInt(cp.left ?? 0, 10) || 0);
    return `${pt}px ${pr}px ${pb}px ${pl}px`;
  }
  return 'clamp(52px,7vw,92px) 0';
});
// ── Raggio barra ricerca (parità col PHP, default 14px → invariato) ──
// Collassa 4 angoli uguali alla forma breve per parità byte-per-byte col render originale.
const searchRadius = computed(() => {
  const r = s.value.search_radius;
  if (r && typeof r === 'object') {
    const tl = parseInt(r.tl ?? 0, 10) || 0;
    const tr = parseInt(r.tr ?? 0, 10) || 0;
    const br = parseInt(r.br ?? 0, 10) || 0;
    const bl = parseInt(r.bl ?? 0, 10) || 0;
    if (tl === tr && tr === br && br === bl) return `${tl}px`;
    return `${tl}px ${tr}px ${br}px ${bl}px`;
  }
  return '14px';
});

// ── KIT standard OLObuild — sfondo completo + ombra + bordo (parità col PHP) ──
// Sfondo completo: override del bg di sezione SOLO se valorizzato (default none → invariato).
const kitBgStyle = computed(() => {
  const bg = s.value.bg;
  if (!bg || !bg.type || bg.type === 'none') return {};
  return buildBgStyle(bg);
});
// Ombra: preset/custom, mirror 1:1 di build_shadow_decl PHP. '' = nessuna → invariato.
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
  const map = {
    sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
    md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
    lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
    xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
  };
  return map[p] || '';
});
// Bordo base: mirror 1:1 di parse_border + build_border_css PHP. Vuoto se inattivo.
const kitBorderStyle = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return {};
  const color = String(b.color || '').trim();
  if (color === '') return {};
  const style = b.style || 'solid';
  const top = Math.max(0, parseInt(b.top ?? 0, 10) || 0);
  const right = Math.max(0, parseInt(b.right ?? 0, 10) || 0);
  const bottom = Math.max(0, parseInt(b.bottom ?? 0, 10) || 0);
  const left = Math.max(0, parseInt(b.left ?? 0, 10) || 0);
  if (!top && !right && !bottom && !left) return {};
  if (top === right && right === bottom && bottom === left) {
    return { border: `${top}px ${style} ${color}` };
  }
  const out = {};
  if (top) out.borderTop = `${top}px ${style} ${color}`;
  if (right) out.borderRight = `${right}px ${style} ${color}`;
  if (bottom) out.borderBottom = `${bottom}px ${style} ${color}`;
  if (left) out.borderLeft = `${left}px ${style} ${color}`;
  return out;
});

const rootStyle = computed(() => {
  const mh = Math.max(0, Math.min(100, parseInt(s.value.min_height) || 0));
  const st = { position: 'relative', overflow: 'hidden', padding: padDecl.value, textAlign: 'center', background: s.value.bg_color || '#1a1a22', color: txt.value, fontFamily: SANS, '--sh-accent': accent.value, '--sh-accent-on': accOn.value };
  if (mh > 0) { st.minHeight = mh + 'vh'; st.display = 'flex'; st.alignItems = 'center'; }
  // KIT: sfondo completo (override), ombra, bordo — applicati DOPO il base, no-op coi default.
  Object.assign(st, kitBgStyle.value, kitBorderStyle.value);
  if (kitShadow.value) st.boxShadow = kitShadow.value;
  return st;
});
const glowStyle = computed(() => ({ position: 'absolute', top: '-160px', left: '50%', transform: 'translateX(-50%)', width: '720px', height: '480px', borderRadius: '50%', filter: 'blur(120px)', background: `radial-gradient(circle,${glow.value},transparent 70%)`, pointerEvents: 'none' }));
const inStyle = { position: 'relative', zIndex: 2, maxWidth: '760px', margin: '0 auto', width: '100%', padding: '0 28px' };
const eyebrowStyle = computed(() => ({ display: 'block', marginBottom: '18px', fontFamily: SANS, fontWeight: 700, fontSize: '12px', letterSpacing: '.12em', textTransform: 'uppercase', color: accent.value }));
const hStyle = computed(() => ({ fontFamily: DISP, fontWeight: 800, fontSize: 'clamp(40px,6.6vw,80px)', lineHeight: 1.0, letterSpacing: '-.02em', color: txt.value, margin: 0 }));
const subStyle = computed(() => ({ fontSize: '18px', lineHeight: 1.6, color: sub.value, maxWidth: '460px', margin: '20px auto 30px' }));
const searchStyle = computed(() => ({ display: 'flex', gap: '8px', maxWidth: '560px', margin: '0 auto', background: panel.value, border: '1px solid ' + sline.value, borderRadius: searchRadius.value, padding: '8px' }));
const inputStyle = computed(() => ({ flex: 1, background: 'transparent', border: 0, padding: '12px 14px', fontFamily: SANS, fontSize: '15px', color: txt.value, minWidth: 0 }));
const btnStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '13px 24px', borderRadius: '10px', fontFamily: SANS, fontWeight: 700, fontSize: '14px', textDecoration: 'none', cursor: 'pointer', border: 0, background: accent.value, color: accOn.value, boxShadow: '0 10px 28px -10px ' + glow.value, whiteSpace: 'nowrap' }));
const chipsStyle = { display: 'flex', gap: '8px', justifyContent: 'center', flexWrap: 'wrap', marginTop: '18px' };
const chipStyle = computed(() => ({ fontFamily: SANS, fontWeight: 600, fontSize: '13px', color: chipCol.value, border: '1px solid ' + line.value, borderRadius: '999px', padding: '7px 15px', textDecoration: 'none' }));
</script>

<style scoped>
.sh-btn { transition: transform .15s, filter .2s, box-shadow .2s; }
.sh-btn:hover { transform: translateY(-2px); filter: brightness(1.06); }
.sh-btn:focus-visible { outline: 2px solid var(--sh-accent-on, #fff); outline-offset: 2px; }
.sh-chip { transition: border-color .15s, color .15s; }
.sh-chip:hover { border-color: var(--sh-accent, #ff5a5f); color: var(--sh-accent, #ff5a5f); }
.sh-chip:focus-visible { outline: 2px solid var(--sh-accent, #ff5a5f); outline-offset: 2px; }
.sh-search input:focus { outline: none; }
.sh-search input:focus-visible { outline: 2px solid var(--sh-accent, #ff5a5f); outline-offset: 2px; border-radius: 8px; }
</style>
