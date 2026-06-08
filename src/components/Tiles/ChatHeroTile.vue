<template>
  <section class="olo-chathero cht" :style="rootStyle">
    <span class="cht-glow" :style="glowStyle"></span>
    <div class="cht-in" :style="inStyle">
      <span v-if="s.pill_text" class="cht-pill" :class="{ 'cht-pill--dot': s.pill_dot }" :style="pillStyle">{{ s.pill_text }}</span>
      <h1 class="cht-h" :style="hStyle">{{ s.headline_text }}<span v-if="s.accent_text" class="grad" :style="gradStyle">{{ s.accent_text }}</span></h1>
      <p v-if="s.subhead" class="cht-sub" :style="subStyle">{{ s.subhead }}</p>
      <div v-if="s.cta1_text || s.cta2_text" class="cht-cta" :style="{ display: 'flex', gap: '12px', justifyContent: 'center', flexWrap: 'wrap' }">
        <a v-if="s.cta1_text" class="cht-btn cht-btn--solid" :style="solidStyle" :href="s.cta1_url || '#'">{{ s.cta1_text }}
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a v-if="s.cta2_text" class="cht-btn cht-btn--ghost" :style="ghostStyle" :href="s.cta2_url || '#'">{{ s.cta2_text }}</a>
      </div>
    </div>
    <div v-if="s.chat_enabled" class="cht-chatwrap" :style="chatwrapStyle">
      <div class="cht-chat" :style="chatStyle">
        <div class="cht-bar" :style="barStyle"><i :style="barDot"></i><i :style="barDot"></i><i :style="barDot"></i><span v-if="s.chat_label" class="u" :style="barLabel">{{ s.chat_label }}</span></div>
        <div class="cht-body" :style="bodyStyle">
          <div v-for="(m, i) in visibleMsgs" :key="i" class="cht-msg" :class="msgSide(m)" :style="msgStyle(m)">{{ m.text }}</div>
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
  pill_text: 'Synapse 3 · now with long-term memory',
  pill_dot: true,
  headline_text: 'The AI workspace that ',
  accent_text: 'remembers.',
  subhead: "Chat, agents and your company's knowledge in one place — grounded in your docs, your data and every conversation you've had before.",
  cta1_text: 'Try free',
  cta1_url: '#pricing',
  cta2_text: 'See how it works',
  cta2_url: '#features',
  chat_enabled: true,
  chat_label: 'synapse · workspace',
  messages: [
    { side: 'you', text: "Summarise last week's customer calls and flag anything about pricing." },
    { side: 'ai', text: 'Across 9 calls: 3 flagged pricing — two want annual billing, one found the Team tier "a jump". Drafted a follow-up for each. Want me to send?' },
    { side: 'you', text: 'Yes, and add them to the CRM.' },
    { side: 'ai', text: '…' },
  ],
  bg_color: '#140e22',
  panel_color: '#1e1633',
  panel2_color: '#271d42',
  accent: '',
  accent2: '',
  accent_on: '#ffffff',
  text_color: '#ffffff',
  sub_color: '#776e92',
  msg_text_color: '#b3a9cc',
  pill_color: '',
  glow_color: 'rgba(160,107,255,0.3)',
  glow_w: 820,
  glow_h: 560,
  glow_blur: 110,
  glow_x: 50,
  glow_y: -220,
  h_size_min: 40,
  h_size_vw: 6.6,
  h_size_max: 82,
  max_width: 840,
  chat_max_width: 760,

  // Spaziatura / Raggio (additivi, default = resa attuale invariata)
  content_padding: { top: 0, right: 28, bottom: 0, left: 28 },
  chat_radius: { tl: 16, tr: 16, br: 0, bl: 0 },

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

const DISP = "var(--olo-font-family-heading, 'Instrument Sans',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Instrument Sans',-apple-system,sans-serif)";
const MONO = "var(--olo-font-family-mono, 'Space Mono',ui-monospace,Menlo,monospace)";

const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #a06bff)');
const acc2 = computed(() => s.value.accent2 || 'var(--olo-color-secondary, #ff7ad1)');
const accOn = computed(() => s.value.accent_on || '#ffffff');
const txt = computed(() => s.value.text_color || '#ffffff');
const panel = computed(() => s.value.panel_color || '#1e1633');
const panel2 = computed(() => s.value.panel2_color || '#271d42');
const glow = computed(() => s.value.glow_color || 'rgba(160,107,255,0.3)');
const pillc = computed(() => s.value.pill_color || accent.value);

// Spaziatura contenuto interno (.cht-in). Default {0,28,0,28} → '0 28px' invariato.
const contentPadding = computed(() => {
  const p = s.value.content_padding || {};
  const t = parseInt(p.top ?? 0, 10) || 0;
  const r = parseInt(p.right ?? 28, 10) || 0;
  const b = parseInt(p.bottom ?? 0, 10) || 0;
  const l = parseInt(p.left ?? 28, 10) || 0;
  if (t === 0 && r === 28 && b === 0 && l === 28) return '0 28px';
  return `${t}px ${r}px ${b}px ${l}px`;
});
// Raggio finestra chat. Default {16,16,0,0} → '16px 16px 0 0' invariato.
const chatRadius = computed(() => {
  const c = s.value.chat_radius || {};
  const tl = parseInt(c.tl ?? 16, 10) || 0;
  const tr = parseInt(c.tr ?? 16, 10) || 0;
  const br = parseInt(c.br ?? 0, 10) || 0;
  const bl = parseInt(c.bl ?? 0, 10) || 0;
  if (tl === 16 && tr === 16 && br === 0 && bl === 0) return '16px 16px 0 0';
  return `${tl}px ${tr}px ${br}px ${bl}px`;
});

// KIT standard OLObuild — sfondo completo (override se valorizzato) + ombra + bordo statico.
// Default no-op: bg.type='none', shadow='none', border tutti 0 → resa identica a prima.
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};
function shadowDecl(v) {
  const p = v.shadow || 'none';
  if (p === 'none' || p === '') return '';
  if (p === 'custom') {
    const h = parseInt(v.shadow_h ?? 0, 10) || 0;
    const vv = parseInt(v.shadow_v ?? 4, 10) || 0;
    const blur = Math.max(0, parseInt(v.shadow_blur ?? 10, 10) || 0);
    const spread = parseInt(v.shadow_spread ?? 0, 10) || 0;
    const color = v.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = v.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${vv}px ${blur}px ${spread}px ${color}`;
  }
  return SHADOW_MAP[p] || '';
}
function borderDecl(b) {
  if (!b || typeof b !== 'object') return '';
  const t = Math.max(0, parseInt(b.top ?? 0, 10) || 0);
  const r = Math.max(0, parseInt(b.right ?? 0, 10) || 0);
  const bo = Math.max(0, parseInt(b.bottom ?? 0, 10) || 0);
  const l = Math.max(0, parseInt(b.left ?? 0, 10) || 0);
  if (t === 0 && r === 0 && bo === 0 && l === 0) return '';
  const st = b.style || 'solid';
  const c = b.color || '';
  if (!c) return '';
  if (t === r && r === bo && bo === l) return `${t}px ${st} ${c}`;
  return '';
}
const kitStyle = computed(() => {
  const out = {};
  const bg = s.value.bg;
  if (bg && bg.type && bg.type !== 'none') {
    Object.assign(out, buildBgStyle(bg));
  }
  const sh = shadowDecl(s.value);
  if (sh) out.boxShadow = sh;
  const bd = borderDecl(s.value.border);
  if (bd) out.border = bd;
  return out;
});

const rootStyle = computed(() => ({ position: 'relative', overflow: 'hidden', background: s.value.bg_color || '#140e22', color: txt.value, fontFamily: SANS, padding: 'clamp(56px,8vw,104px) 0 0', textAlign: 'center', '--cht-accent': accent.value, ...kitStyle.value }));
const glowStyle = computed(() => ({ position: 'absolute', top: (Number(s.value.glow_y) || 0) + 'px', left: (Number(s.value.glow_x) || 0) + '%', transform: 'translateX(-50%)', width: (Number(s.value.glow_w) || 820) + 'px', height: (Number(s.value.glow_h) || 560) + 'px', borderRadius: '50%', filter: 'blur(' + (Number(s.value.glow_blur) || 0) + 'px)', pointerEvents: 'none', background: 'radial-gradient(circle, ' + glow.value + ', transparent 70%)', zIndex: 0 }));
const inStyle = computed(() => ({ position: 'relative', zIndex: 2, maxWidth: (Number(s.value.max_width) || 840) + 'px', margin: '0 auto', padding: contentPadding.value }));
const pillStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '9px', padding: '6px 14px', borderRadius: '999px', background: 'rgba(160,107,255,.12)', border: '1px solid rgba(160,107,255,.4)', fontFamily: MONO, fontSize: '12px', color: pillc.value, marginBottom: '24px' }));
const hStyle = computed(() => ({ fontFamily: DISP, fontWeight: 700, fontSize: `clamp(${Number(s.value.h_size_min) || 40}px,${Number(s.value.h_size_vw) || 6.6}vw,${Number(s.value.h_size_max) || 82}px)`, lineHeight: 1, letterSpacing: '-.01em', color: txt.value, margin: 0 }));
const gradStyle = computed(() => ({ background: `linear-gradient(110deg, ${accent.value}, ${acc2.value})`, WebkitBackgroundClip: 'text', backgroundClip: 'text', color: 'transparent' }));
const subStyle = computed(() => ({ fontSize: '18px', lineHeight: 1.6, color: s.value.sub_color || '#776e92', maxWidth: '560px', margin: '24px auto 30px' }));
const solidStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '15px 28px', borderRadius: '9px', fontFamily: SANS, fontWeight: 600, fontSize: '15px', textDecoration: 'none', background: accent.value, color: accOn.value, border: 0, boxShadow: '0 10px 28px -10px ' + glow.value }));
const ghostStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', padding: '15px 28px', borderRadius: '9px', fontFamily: SANS, fontWeight: 600, fontSize: '15px', textDecoration: 'none', background: 'rgba(255,255,255,.05)', color: '#fff', border: '1px solid rgba(255,255,255,.16)' }));

const chatwrapStyle = { position: 'relative', zIndex: 2, maxWidth: '1180px', margin: '0 auto', padding: '0 28px' };
const chatStyle = computed(() => ({ position: 'relative', zIndex: 2, maxWidth: (Number(s.value.chat_max_width) || 760) + 'px', margin: 'clamp(44px,7vw,76px) auto 0', border: '1px solid rgba(255,255,255,.08)', borderRadius: chatRadius.value, background: panel.value, overflow: 'hidden', boxShadow: '0 -10px 90px -24px ' + glow.value, textAlign: 'left' }));
const barStyle = computed(() => ({ display: 'flex', alignItems: 'center', gap: '7px', padding: '13px 16px', borderBottom: '1px solid rgba(255,255,255,.08)', background: panel2.value }));
const barDot = { width: '11px', height: '11px', borderRadius: '50%', background: 'rgba(255,255,255,.16)' };
const barLabel = computed(() => ({ marginLeft: '12px', fontFamily: MONO, fontSize: '11px', color: s.value.sub_color || '#776e92' }));
const bodyStyle = { padding: '22px', display: 'flex', flexDirection: 'column', gap: '16px' };

const visibleMsgs = computed(() => (Array.isArray(s.value.messages) ? s.value.messages : []).filter(m => m && String(m.text || '') !== ''));
function msgSide(m) { return (m.side === 'you') ? 'you' : 'ai'; }
function msgStyle(m) {
  const base = { maxWidth: '80%', padding: '13px 16px', borderRadius: '14px', fontSize: '14.5px', lineHeight: 1.5 };
  if (m.side === 'you') {
    return { ...base, alignSelf: 'flex-end', background: accent.value, color: '#fff', borderBottomRightRadius: '4px' };
  }
  return { ...base, alignSelf: 'flex-start', background: panel2.value, color: s.value.msg_text_color || '#b3a9cc', border: '1px solid rgba(255,255,255,.08)', borderBottomLeftRadius: '4px' };
}
</script>

<style scoped>
.cht-pill--dot::before {
  content: "";
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--cht-accent, #a06bff);
  box-shadow: 0 0 10px var(--cht-accent, #a06bff);
}
.cht-btn { transition: transform .15s, filter .2s, box-shadow .2s; }
.cht-btn:hover { transform: translateY(-2px); }
.cht-btn--solid:hover { filter: brightness(1.06); }
.cht-btn--ghost:hover { border-color: rgba(160,107,255,.4); }
.cht-btn:focus-visible { outline: 2px solid var(--cht-accent, #a06bff); outline-offset: 3px; }
</style>
