<template>
  <section class="olo-masthead mst" :style="rootStyle">
    <div class="mst-mast" :style="mastStyle">
      <div class="mst-wrap mst-top" :style="topStyle">
        <span v-if="s.edition_text" class="mst-date" :style="dateStyle">{{ s.edition_text }}</span>
        <a class="mst-name" :style="nameStyle" href="#">{{ s.nameplate_text }}</a>
        <div class="mst-act" :style="actStyle">
          <a v-if="s.action1_text" class="mst-byline" :style="bylineLinkStyle" :href="s.action1_url || '#'">{{ s.action1_text }}</a>
          <a v-if="s.action2_text" class="mst-btn" :style="btnStyle" :href="s.action2_url || '#'">{{ s.action2_text }}</a>
        </div>
      </div>
    </div>
    <div class="mst-nav-rule" :style="navRuleStyle"></div>
    <div class="mst-wrap">
      <div class="mst-lead" :style="leadStyle">
        <span v-if="s.kicker_text" class="mst-kicker" :style="kickerStyle">{{ s.kicker_text }}</span>
        <h1 class="mst-h" :style="hStyle">{{ s.headline_text }}<template v-if="s.headline_italic_text"> <em style="font-style:italic">{{ s.headline_italic_text }}</em></template></h1>
        <p v-if="s.subhead" class="mst-sub" :style="subStyle">{{ s.subhead }}</p>
        <div v-if="s.byline_text" class="mst-lead-by" :style="leadByStyle">{{ s.byline_text }}</div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { borderDefault, borderHoverDefault } from '@/config/elements/_shared.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  edition_text: 'Friday, 6 March 2026 · Milan',
  nameplate_text: 'The Dispatch',
  action1_text: 'Sign in', action1_url: '#',
  action2_text: 'Subscribe', action2_url: '#newsletter',
  kicker_text: 'Politics · Analysis',
  headline_text: 'Inside the budget deal',
  headline_italic_text: 'that almost didn\'t happen',
  subhead: 'For seventy-two hours the talks looked dead. Then a late-night compromise on housing rewrote the maths — and the coalition with it. We reconstruct the week.',
  byline_text: 'By Elena Marchetti · 12 min read',
  bg_color: '#f4f1ea',
  ink_color: '#16161a',
  ink_soft_color: '#44444c',
  ink_faint_color: '#86848c',
  accent: '#cf2e2e',
  rule_color: '#ddd8cc',
  nameplate_size: 52,
  headline_size: 54,

  // Spaziatura (override GATED del padding lead, responsive clamp). Default off = invariato.
  pad_custom: false,
  lead_padding: { top: 52, right: 0, bottom: 60, left: 0 },

  // Forma — raggio bottone "Subscribe" (default 2px = invariato).
  btn_radius: { tl: 2, tr: 2, br: 2, bl: 2 },

  // KIT standard OLObuild (additivo, no-op coi default)
  bg: { type: 'none' },
  shadow: 'none',
  border: { ...borderDefault },
  border_hover: { ...borderHoverDefault },
  border_hover_duration: 300,
  border_effect: 'none',
  border_effect_intensity: 'medium',
  border_effect_color2: '',
  border_effect_angle: 135,
  border_effect_speed: 4,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const DISP = "var(--olo-font-family-heading, 'Newsreader',Georgia,serif)";
const SANS = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";

const bg = computed(() => s.value.bg_color || '#f4f1ea');
const ink = computed(() => s.value.ink_color || '#16161a');
const inkS = computed(() => s.value.ink_soft_color || '#44444c');
const inkF = computed(() => s.value.ink_faint_color || '#86848c');
const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #cf2e2e)');
const rule = computed(() => s.value.rule_color || '#ddd8cc');

const npSize = computed(() => Math.max(24, Math.min(96, Number(s.value.nameplate_size) || 52)));
const hSize = computed(() => Math.max(24, Math.min(96, Number(s.value.headline_size) || 54)));

const WRAP = { maxWidth: '1200px', margin: '0 auto', padding: '0 28px' };

// ── Spaziatura: override GATED del padding lead (default off → clamp invariato) ──
const leadPad = computed(() => {
  const lp = s.value.lead_padding;
  if (!s.value.pad_custom || !lp || typeof lp !== 'object') {
    return 'clamp(32px,4vw,52px) 0 clamp(36px,5vw,60px)';
  }
  const top = parseInt(lp.top ?? 0, 10) || 0;
  const right = parseInt(lp.right ?? 0, 10) || 0;
  const bottom = parseInt(lp.bottom ?? 0, 10) || 0;
  const left = parseInt(lp.left ?? 0, 10) || 0;
  return `${top}px ${right}px ${bottom}px ${left}px`;
});

// ── Forma: raggio bottone. Default {2,2,2,2} → "2px" esatto (byte-identico al render storico). ──
const btnRadius = computed(() => {
  const r = s.value.btn_radius;
  if (!r || typeof r !== 'object') return '2px';
  const tl = parseInt(r.tl ?? 2, 10) || 0;
  const tr = parseInt(r.tr ?? 2, 10) || 0;
  const br = parseInt(r.br ?? 2, 10) || 0;
  const bl = parseInt(r.bl ?? 2, 10) || 0;
  if (tl === tr && tr === br && br === bl) return `${tl}px`;
  return `${tl}px ${tr}px ${br}px ${bl}px`;
});

// ── KIT standard OLObuild (parità col PHP) ──────────────────────────────────
// Sfondo completo: override del bg base SOLO se valorizzato (default none → invariato).
const kitBgStyle = computed(() => {
  const bgObj = s.value.bg;
  if (!bgObj || !bgObj.type || bgObj.type === 'none') return {};
  return buildBgStyle(bgObj);
});

// Ombra: stesso map preset + custom del PHP (build_shadow_decl). '' coi default.
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};
const shadowDecl = computed(() => {
  const preset = s.value.shadow || 'none';
  if (preset === 'none' || preset === '') return '';
  if (preset === 'custom') {
    const h = parseInt(s.value.shadow_h ?? 0, 10) || 0;
    const v = parseInt(s.value.shadow_v ?? 4, 10) || 0;
    const blur = Math.max(0, parseInt(s.value.shadow_blur ?? 10, 10) || 0);
    const spread = parseInt(s.value.shadow_spread ?? 0, 10) || 0;
    const color = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  return SHADOW_MAP[preset] || '';
});

// Bordo base: stessa logica di parse_border/build_border_css (PHP).
const borderStyle = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return {};
  const color = (b.color || '').trim();
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
  const st = { position: 'relative', background: bg.value, color: ink.value, fontFamily: SANS, '--mst-accent': accent.value, '--mst-ink': ink.value };
  Object.assign(st, kitBgStyle.value, borderStyle.value);
  if (shadowDecl.value) st.boxShadow = shadowDecl.value;
  return st;
});
const mastStyle = computed(() => ({ borderBottom: '1px solid ' + rule.value }));
const topStyle = computed(() => ({ ...WRAP, display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '18px 0 14px', gap: '16px' }));
const dateStyle = computed(() => ({ fontSize: '12px', color: inkF.value, letterSpacing: '.02em', flex: 1 }));
const nameStyle = computed(() => ({ fontFamily: DISP, fontWeight: 600, fontSize: `clamp(34px,${Math.round(npSize.value / 11.8 * 100) / 100}vw,${npSize.value}px)`, letterSpacing: '-.02em', textAlign: 'center', flex: 1, color: ink.value, textDecoration: 'none', lineHeight: 1.04 }));
const actStyle = { flex: 1, display: 'flex', justifyContent: 'flex-end', gap: '12px', alignItems: 'center' };
const bylineLinkStyle = computed(() => ({ fontFamily: SANS, fontSize: '12px', color: inkF.value, letterSpacing: '.02em', textDecoration: 'none' }));
const btnStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '12px 22px', borderRadius: btnRadius.value, fontFamily: SANS, fontWeight: 600, fontSize: '13px', letterSpacing: '.03em', textDecoration: 'none', background: accent.value, color: '#fff', border: 0 }));
const navRuleStyle = computed(() => ({ borderTop: '1px solid ' + rule.value, borderBottom: '1px solid ' + rule.value, height: '6px' }));
const leadStyle = computed(() => ({ padding: leadPad.value, maxWidth: '820px' }));
const kickerStyle = computed(() => ({ fontFamily: SANS, fontWeight: 700, fontSize: '11px', letterSpacing: '.1em', textTransform: 'uppercase', color: accent.value, display: 'block', marginBottom: '12px' }));
const hStyle = computed(() => ({ fontFamily: DISP, fontWeight: 600, fontSize: `clamp(32px,${Math.round(hSize.value / 11.7 * 100) / 100}vw,${hSize.value}px)`, lineHeight: 1.04, letterSpacing: '-.01em', color: ink.value, margin: 0 }));
const subStyle = computed(() => ({ color: inkS.value, fontSize: '17px', lineHeight: 1.6, margin: '14px 0 0', maxWidth: '620px' }));
const leadByStyle = computed(() => ({ marginTop: '14px', fontFamily: SANS, fontSize: '12px', color: inkF.value, letterSpacing: '.02em' }));
</script>

<style scoped>
.mst-btn { transition: transform .15s, filter .2s; }
.mst-btn:hover { transform: translateY(-2px); filter: brightness(1.06); }
.mst-byline { transition: color .15s; }
.mst-byline:hover { color: var(--mst-ink, #16161a); }
.mst-name:focus-visible,
.mst-byline:focus-visible,
.mst-btn:focus-visible { outline: 2px solid var(--mst-accent, #cf2e2e); outline-offset: 3px; }
</style>
