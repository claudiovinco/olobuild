<template>
  <section class="olo-northquoteslider nqs" :style="rootStyle">
    <div class="nqs-in" :style="inStyle">
      <div class="nqs-head">
        <h2 v-if="s.heading" class="nqs-title" :style="titleStyle">{{ s.heading }}</h2>
        <span v-else></span>
        <div v-if="multi" class="nqs-nav">
          <button type="button" class="nqs-arrow" :style="arrowStyle" :aria-label="t('Precedente')" @click="go(-1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6" /></svg>
          </button>
          <button type="button" class="nqs-arrow" :style="arrowStyle" :aria-label="t('Successivo')" @click="go(1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6" /></svg>
          </button>
        </div>
      </div>
      <div class="nqs-grid" :style="gridStyle">
        <div class="nqs-left">
          <div v-for="(it, i) in items" :key="i" class="nqs-slide" :class="{ 'is-active': i === cur }">
            <p v-if="it.logo_text" class="nqs-logo" :style="logoStyle">{{ it.logo_text }}</p>
            <blockquote class="nqs-quote" :style="quoteStyle">{{ it.quote }}</blockquote>
            <p v-if="it.author_name" class="nqs-author" :style="authorStyle">{{ it.author_name }}</p>
            <p v-if="it.author_role" class="nqs-role" :style="roleStyle">{{ it.author_role }}</p>
          </div>
          <div v-if="multi" class="nqs-dots">
            <button v-for="(it, i) in items" :key="'d' + i" type="button" class="nqs-dot" :class="{ 'is-active': i === cur }" :style="i === cur ? dotActiveStyle : dotStyle" :aria-label="'Slide ' + (i + 1)" @click="cur = i" />
          </div>
        </div>
        <div class="nqs-right">
          <div class="nqs-graphic" :class="{ 'is-slant': slantOn }" :style="graphicStyle" aria-hidden="true">
            <svg viewBox="0 0 400 640" preserveAspectRatio="xMidYMid slice">
              <g class="nqs-lines" :style="linesStyle"><path v-for="(d, i) in topo" :key="i" :d="d" :style="pathStyle" /></g>
            </svg>
            <span class="nqs-glabel" :style="glabelStyle">North · enterprise AI</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref } from 'vue';
import { t } from '@/i18n';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { borderDefault, borderHoverDefault, borderEffectDefaults } from '@/config/elements/_shared.js';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  heading: 'Why enterprises and innovators choose Cohere',
  items: [
    { quote: "We jointly announced a customized platform, North for Banking, to enable RBC to accelerate the development of our genAI solutions securely and efficiently and we're pleased with our results to date.", author_name: 'Dr. Foteini Agrafioti', author_role: 'SVP, Data & AI & Chief Science Officer, RBC', logo_text: 'RBC' },
    { quote: 'North lets our teams move from question to verified answer in seconds — grounded in our own data, without the risk.', author_name: 'Head of Data', author_role: 'Global Enterprise', logo_text: '' },
  ],
  slant: true, autoplay: false, autoplay_speed: 6,
  bg_color: '#ffffff', heading_color: '#212121', quote_color: '#212121', author_color: '#212121',
  role_color: '#6B7280', logo_color: '#062C22', arrow_color: '#212121',
  graphic_color: '#0A2E22', graphic_line_color: '#9DF5D6', quote_size: 26,
  bg: { type: 'none' }, shadow: 'none',
  border: { ...borderDefault }, border_hover: { ...borderHoverDefault }, border_hover_duration: 300,
  ...borderEffectDefaults,
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const items = computed(() => (Array.isArray(s.value.items) && s.value.items.length ? s.value.items : defaults.items));
const multi = computed(() => items.value.length > 1);

const cur = ref(0);
function go(d) { const n = items.value.length; cur.value = ((cur.value + d) % n + n) % n; }
const slantOn = computed(() => !!s.value.slant && cur.value % 2 === 1);

const DISP = "var(--olo-font-family-heading, 'Space Grotesk',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Inter','Work Sans',-apple-system,sans-serif)";
const MONO = "var(--olo-font-family-mono, 'Space Mono',ui-monospace,'SF Mono',Menlo,monospace)";

// linee topografiche (stesse sinusoidi del PHP)
const topo = computed(() => {
  const vbW = 400, vbH = 640, n = 11, out = [];
  for (let i = 0; i < n; i++) {
    const baseY = 26 + i * ((vbH - 52) / (n - 1));
    const amp = 14 + 9 * Math.abs(Math.sin(i * 1.1));
    const waves = 1.3 + 0.25 * (i % 3);
    const phase = i * 0.6;
    let d = 'M';
    for (let x = 0; x <= vbW; x += 16) {
      const y = baseY + amp * Math.sin((x / vbW) * Math.PI * 2 * waves + phase);
      d += (x === 0 ? '' : ' L') + x + ' ' + (Math.round(y * 10) / 10);
    }
    out.push(d);
  }
  return out;
});

const qSize = computed(() => { const n = parseInt(s.value.quote_size, 10) || 26; return Math.max(16, Math.min(56, n)); });

const kitBgStyle = computed(() => { const bg = s.value.bg; if (!bg || !bg.type || bg.type === 'none') return {}; return buildBgStyle(bg); });
const kitShadow = computed(() => {
  const p = s.value.shadow || 'none'; if (p === 'none' || p === '') return '';
  const map = { sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)', md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)', lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)', xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)' };
  return map[p] || '';
});

const rootStyle = computed(() => { const st = { position: 'relative', background: s.value.bg_color || '#ffffff', fontFamily: SANS }; Object.assign(st, kitBgStyle.value); if (kitShadow.value) st.boxShadow = kitShadow.value; return st; });
const inStyle = { maxWidth: '1280px', margin: '0 auto', padding: '0 40px' };
const gridStyle = { display: 'grid', gridTemplateColumns: '1.45fr .9fr', gap: 'clamp(28px,4vw,56px)', alignItems: 'stretch' };
const titleStyle = computed(() => ({ fontFamily: DISP, fontWeight: 500, fontSize: 'clamp(28px,3.4vw,44px)', lineHeight: 1.05, letterSpacing: '-.02em', color: s.value.heading_color || '#212121', margin: 0, maxWidth: '760px' }));
const arrowStyle = computed(() => ({ width: '48px', height: '48px', borderRadius: '999px', border: '1px solid rgba(0,0,0,.16)', background: 'transparent', color: s.value.arrow_color || '#212121', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer' }));
const logoStyle = computed(() => ({ fontFamily: DISP, fontWeight: 700, fontSize: '22px', letterSpacing: '.02em', color: s.value.logo_color || '#062C22', margin: '0 0 24px' }));
const quoteStyle = computed(() => ({ fontFamily: DISP, fontWeight: 500, fontSize: qSize.value + 'px', lineHeight: 1.34, letterSpacing: '-.01em', color: s.value.quote_color || '#212121', margin: '0 0 28px' }));
const authorStyle = computed(() => ({ fontWeight: 600, fontSize: '16px', color: s.value.author_color || '#212121', margin: 'auto 0 2px' }));
const roleStyle = computed(() => ({ fontSize: '15px', color: s.value.role_color || '#6B7280', margin: 0 }));
const dotStyle = { height: '8px', width: '8px', borderRadius: '999px', border: 0, background: 'rgba(0,0,0,.18)', cursor: 'pointer', padding: 0, transition: 'width .3s,background .3s' };
const dotActiveStyle = computed(() => ({ ...dotStyle, width: '24px', background: s.value.arrow_color || '#212121' }));
const graphicStyle = computed(() => ({ background: s.value.graphic_color || '#0A2E22', borderRadius: '22px' }));
const linesStyle = { transition: 'transform .85s cubic-bezier(.66,0,.34,1)' };
const pathStyle = computed(() => ({ fill: 'none', stroke: s.value.graphic_line_color || '#9DF5D6', strokeWidth: 2.2, strokeLinecap: 'round', strokeDasharray: '.1 13', opacity: .85 }));
const glabelStyle = { position: 'absolute', left: '24px', bottom: '22px', fontFamily: MONO, fontSize: '11px', letterSpacing: '.05em', textTransform: 'uppercase', color: 'rgba(255,255,255,.52)', zIndex: 2 };
</script>

<style scoped>
.nqs-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 24px; margin: 0 0 42px; }
.nqs-nav { display: flex; gap: 10px; flex: 0 0 auto; }
.nqs-arrow:hover { background: rgba(0, 0, 0, .05) !important; transform: translateY(-1px); }
.nqs-arrow svg { width: 20px; height: 20px; display: block; }
.nqs-left { position: relative; min-height: 380px; display: flex; }
.nqs-slide { position: absolute; inset: 0; opacity: 0; visibility: hidden; transform: translateY(14px); transition: opacity .5s ease, transform .5s ease; display: flex; flex-direction: column; }
.nqs-slide.is-active { position: relative; opacity: 1; visibility: visible; transform: none; }
.nqs-dots { display: flex; gap: 8px; margin-top: 30px; }
.nqs-right { position: relative; min-height: 500px; }
.nqs-graphic { position: absolute; inset: 0; overflow: hidden; clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%); transition: clip-path .85s cubic-bezier(.66, 0, .34, 1); will-change: clip-path; }
.nqs-graphic.is-slant { clip-path: polygon(20% 0, 100% 0, 80% 100%, 0 100%); }
.nqs-graphic.is-slant .nqs-lines { transform: skewX(-10deg) translateX(5%); }
.nqs-graphic svg { position: absolute; inset: 0; width: 100%; height: 100%; }
.nqs-lines path { animation: nqs-flow 16s linear infinite; }
@keyframes nqs-flow { to { stroke-dashoffset: -260; } }
@media (prefers-reduced-motion: reduce) { .nqs-graphic, .nqs-lines { transition: none; } .nqs-lines path { animation: none; } }
@media (max-width: 860px) { .nqs-grid { grid-template-columns: 1fr !important; } .nqs-left { min-height: 0 !important; } .nqs-right { min-height: 340px; } }
</style>
