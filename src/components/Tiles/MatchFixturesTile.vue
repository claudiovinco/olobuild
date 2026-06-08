<template>
  <div class="olo-matchfixtures omf" :style="rootStyle">
    <article v-for="(it, i) in items" :key="'f'+i" class="omf-fix" :style="fixStyle">
      <div class="omf-top" :style="{ display:'flex', alignItems:'center', justifyContent:'space-between', gap:'10px' }">
        <div>
          <b :style="dayStyle">{{ it.day }}</b>
          <span :style="metaStyle">{{ it.time_place }}</span>
        </div>
        <div class="omf-league" :style="leagueStyle">
          <span class="omf-badge" :style="badgeStyle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" :style="{width:'14px',height:'14px',color:accent}"><path d="M12 2 4 6v6c0 5 8 8 8 8s8-3 8-8V6Z"/></svg></span>
          <span>{{ it.league }}<br v-if="it.matchday">{{ it.matchday }}</span>
        </div>
      </div>
      <div class="omf-teams" :style="teamsStyle">
        <div :style="sideStyle"><span class="omf-crest" :style="crestStyle(it.home_crest_bg || '#15543c')">{{ it.home_crest }}</span><span :style="nameStyle">{{ it.home_name }}</span></div>
        <div :style="scoreStyle(it.score)">{{ it.score && String(it.score).trim() ? it.score : 'vs' }}</div>
        <div :style="sideStyle"><span class="omf-crest" :style="crestStyle(it.away_crest_bg || '#7a2230')">{{ it.away_crest }}</span><span :style="nameStyle">{{ it.away_name }}</span></div>
      </div>
      <div v-if="it.venue" class="omf-venue" :style="venueStyle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="{width:'14px',height:'14px',flex:'none',opacity:.85}"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
        {{ it.venue }}
      </div>
    </article>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  items: [
    { day: 'Sat, 14.03', time_place: '15:00 · Verdano Park', league: 'Super League', matchday: 'Matchday 04', home_crest: 'VF', home_crest_bg: '#15543c', home_name: 'Verdano FC', away_crest: 'RA', away_crest_bg: '#7a2230', away_name: 'Real Alta', score: '', venue: "First Men's Team" },
  ],
  columns: 3, gap: 16, card_bg: '#0f3a2a', card_border: 'rgba(255,255,255,0.1)', accent: '',
  day_color: '#ffffff', meta_color: 'rgba(255,255,255,0.55)', name_color: '#ffffff', score_color: '#ffffff', crest_text_color: '#ffffff', radius: 18,

  // ── Spaziatura / Forma (additivi, no-op coi default) — 1:1 col PHP ──
  content_padding: { top: 22, right: 22, bottom: 22, left: 22 },
  card_radius: { tl: 0, tr: 0, br: 0, bl: 0 },

  // KIT standard OLObuild: sfondo completo opzionale + ombra + bordo (no-op coi default)
  bg: { type: 'none' },
  shadow: 'none',
  border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
  border_hover: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' },
  border_hover_duration: 300,
  border_effect: 'none', border_effect_intensity: 'medium', border_effect_color2: '', border_effect_angle: 135, border_effect_speed: 4,
};

// ── KIT standard: mappa preset ombra (1:1 col PHP build_shadow_decl) ──
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);

const DISP = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";
const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #c8ff3c)');
const cols = computed(() => Math.max(1, Math.min(4, parseInt(s.value.columns, 10) || 3)));
const cbd = computed(() => s.value.card_border || 'rgba(255,255,255,0.1)');

// ── KIT standard: sfondo completo + ombra + bordo sul contenitore (no-op coi default) ──
const kitStyle = computed(() => {
  const out = {};
  // Sfondo completo (override SOLO se valorizzato → default invariato)
  const bg = s.value.bg;
  if (bg && bg.type && bg.type !== 'none') Object.assign(out, buildBgStyle(bg));
  // Ombra
  const sh = s.value.shadow;
  if (sh && sh !== 'none') {
    if (sh === 'custom') {
      const h = parseInt(s.value.shadow_h, 10) || 0;
      const v = parseInt(s.value.shadow_v ?? 4, 10) || 0;
      const blur = Math.max(0, parseInt(s.value.shadow_blur ?? 10, 10) || 0);
      const spread = parseInt(s.value.shadow_spread, 10) || 0;
      const color = s.value.shadow_color || 'rgba(0,0,0,0.15)';
      const inset = s.value.shadow_inset ? 'inset ' : '';
      out.boxShadow = `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
    } else if (SHADOW_MAP[sh]) {
      out.boxShadow = SHADOW_MAP[sh];
    }
  }
  // Bordo base (1:1 col PHP build_border_css: serve color non vuoto + un lato > 0)
  const b = s.value.border;
  if (b && (b.color || '').trim()) {
    const t = Math.max(0, parseInt(b.top, 10) || 0);
    const r = Math.max(0, parseInt(b.right, 10) || 0);
    const bo = Math.max(0, parseInt(b.bottom, 10) || 0);
    const l = Math.max(0, parseInt(b.left, 10) || 0);
    if (t || r || bo || l) {
      const st = b.style || 'solid';
      const c = b.color;
      if (t === r && r === bo && bo === l) {
        out.border = `${t}px ${st} ${c}`;
      } else {
        if (t) out.borderTop = `${t}px ${st} ${c}`;
        if (r) out.borderRight = `${r}px ${st} ${c}`;
        if (bo) out.borderBottom = `${bo}px ${st} ${c}`;
        if (l) out.borderLeft = `${l}px ${st} ${c}`;
      }
      out.position = 'relative';
    }
  }
  return out;
});

const rootStyle = computed(() => ({ display: 'grid', gridTemplateColumns: 'repeat(' + cols.value + ', 1fr)', gap: (parseInt(s.value.gap, 10) || 16) + 'px', fontFamily: SANS, '--omf-accent': accent.value, ...kitStyle.value }));

// ── Spaziatura card: padding da 'content_padding' (default 22 su 4 lati = invariato) ──
// 4 lati uguali → forma compatta "22px" (byte-identica al precedente); altrimenti 4 valori.
const cardPad = computed(() => {
  const cp = s.value.content_padding || {};
  const t = Math.max(0, parseInt(cp.top, 10) || 0);
  const r = Math.max(0, parseInt(cp.right, 10) || 0);
  const b = Math.max(0, parseInt(cp.bottom, 10) || 0);
  const l = Math.max(0, parseInt(cp.left, 10) || 0);
  return (t === r && r === b && b === l) ? `${t}px` : `${t}px ${r}px ${b}px ${l}px`;
});
// ── Forma card: override 4-angoli SOLO se valorizzato, altrimenti il range 'radius' (no-op) ──
const cardRad = computed(() => {
  const cr = s.value.card_radius || {};
  const tl = Math.max(0, parseInt(cr.tl, 10) || 0);
  const tr = Math.max(0, parseInt(cr.tr, 10) || 0);
  const br = Math.max(0, parseInt(cr.br, 10) || 0);
  const bl = Math.max(0, parseInt(cr.bl, 10) || 0);
  if (tl || tr || br || bl) return `${tl}px ${tr}px ${br}px ${bl}px`;
  return (parseInt(s.value.radius, 10) || 18) + 'px';
});
const fixStyle = computed(() => ({ background: s.value.card_bg || '#0f3a2a', border: '1px solid ' + cbd.value, borderRadius: cardRad.value, padding: cardPad.value, display: 'flex', flexDirection: 'column', gap: '18px' }));
const dayStyle = computed(() => ({ fontFamily: DISP, fontWeight: 800, fontSize: '15px', color: s.value.day_color || '#fff', display: 'block' }));
const metaStyle = computed(() => ({ fontSize: '12px', color: s.value.meta_color || 'rgba(255,255,255,0.55)' }));
const leagueStyle = computed(() => ({ display: 'flex', alignItems: 'center', gap: '8px', fontSize: '11px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '.04em', color: accent.value, textAlign: 'right' }));
const badgeStyle = computed(() => ({ width: '26px', height: '26px', borderRadius: '7px', background: 'color-mix(in srgb, ' + accent.value + ' 16%, transparent)', display: 'grid', placeItems: 'center', flex: 'none' }));
const teamsStyle = computed(() => ({ display: 'grid', gridTemplateColumns: '1fr auto 1fr', alignItems: 'center', gap: '8px', padding: '10px 0', borderTop: '1px solid ' + cbd.value, borderBottom: '1px solid ' + cbd.value }));
const sideStyle = { display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '9px', textAlign: 'center' };
function crestStyle(bg) {
  return { display: 'inline-grid', placeItems: 'center', width: '46px', height: '50px', fontFamily: DISP, fontWeight: 900, fontSize: '15px', letterSpacing: '.02em', color: s.value.crest_text_color || '#fff', borderRadius: '14px 14px 16px 16px/14px 14px 22px 22px', boxShadow: 'inset 0 0 0 2px rgba(255,255,255,.2)', background: bg };
}
const nameStyle = computed(() => ({ fontFamily: DISP, fontWeight: 800, fontSize: '13px', textTransform: 'uppercase', color: s.value.name_color || '#fff', lineHeight: 1.05 }));
function scoreStyle(score) {
  const isVs = !(score && String(score).trim());
  return { fontFamily: DISP, fontWeight: 900, fontSize: isVs ? '18px' : '26px', color: isVs ? accent.value : (s.value.score_color || '#fff'), textAlign: 'center', minWidth: '64px' };
}
const venueStyle = computed(() => ({ display: 'flex', alignItems: 'center', gap: '7px', fontSize: '12.5px', color: s.value.meta_color || 'rgba(255,255,255,0.6)' }));
</script>

<style scoped>
.omf-fix { transition: border-color .2s, transform .3s; }
.omf-fix:hover { transform: translateY(-4px); border-color: color-mix(in srgb, var(--omf-accent, #c8ff3c) 40%, transparent); }
</style>
