<template>
  <div class="olo-hsplit" :class="hoverClass" :style="wrapStyle">
    <div class="olo-hsplit__grid" :style="gridStyle">

      <!-- LEFT -->
      <div class="olo-hsplit__left">
        <div v-if="s.eyebrow_text" class="olo-hsplit__eyebrow" :style="eyebrowStyle">
          <span class="olo-hsplit__dot" :style="{ background: s.eyebrow_dot_color || '#10b981' }"></span>
          <span>{{ s.eyebrow_text }}</span>
        </div>

        <h1 v-if="headlines.length" class="olo-hsplit__headline" :style="headlineStyle">
          <span
            v-for="(line, i) in headlines"
            :key="i"
            class="olo-hsplit__hline"
            :style="{ color: line.color || '#0f172a', fontStyle: line.italic ? 'italic' : 'normal' }"
          >{{ line.text }}</span>
        </h1>

        <p v-if="s.subhead" class="olo-hsplit__subhead" :style="subheadStyle" v-html="s.subhead"></p>

        <div v-if="s.cta1_text || s.cta2_text" class="olo-hsplit__ctas">
          <a v-if="s.cta1_text" :href="s.cta1_url || '#'" class="olo-hsplit__cta olo-hsplit__cta--primary" :style="cta1Style">{{ s.cta1_text }}</a>
          <a v-if="s.cta2_text" :href="s.cta2_url || '#'" class="olo-hsplit__cta olo-hsplit__cta--outline" :style="cta2Style">{{ s.cta2_text }}</a>
        </div>

        <div v-if="stats.length" class="olo-hsplit__stats" :style="statsStyle">
          <div v-for="(st, i) in stats" :key="i" class="olo-hsplit__stat">
            <div class="olo-hsplit__stat-val" :style="{ color: st.value_color || '#0f172a', fontFamily: headlineFamily, fontStyle: st.value === 'Gratis' ? 'italic' : 'normal' }">{{ st.value }}</div>
            <div class="olo-hsplit__stat-lbl">{{ st.label }}</div>
          </div>
        </div>
      </div>

      <!-- RIGHT -->
      <div v-if="s.showcase_enabled" class="olo-hsplit__right" :style="rightStyle">
        <div v-if="s.showcase_badge_text" class="olo-hsplit__badge" :style="{ background: s.showcase_badge_bg || '#ffffff' }">
          <span class="olo-hsplit__dot olo-hsplit__dot--sm" :style="{ background: s.showcase_badge_dot || '#dc2626' }"></span>
          <span>{{ s.showcase_badge_text }}</span>
        </div>

        <div v-if="items.length" class="olo-hsplit__cards">
          <div
            v-for="(it, i) in items"
            :key="i"
            :class="['olo-hsplit__card', 'olo-hsplit__card--' + i]"
            :style="cardStyle(it)"
          >
            <div class="olo-hsplit__card-num">{{ it.number }}</div>
            <div class="olo-hsplit__card-txt" :style="{ color: it.text_color || '#0f172a', fontFamily: headlineFamily, fontStyle: it.italic ? 'italic' : 'normal' }">{{ it.text }}</div>
          </div>
        </div>

        <div v-if="s.showcase_caption_left || s.showcase_caption_right" class="olo-hsplit__captions">
          <span>{{ s.showcase_caption_left }}</span>
          <span>{{ s.showcase_caption_right }}</span>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { SHADOW as SHADOW_SCALE, resolveFontFamily } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  eyebrow_text: 'STACK WORDPRESS · PER AGENZIE E PMI',
  eyebrow_dot_color: '#10b981',
  eyebrow_color: 'var(--olo-color-text, #1f2937)',
  headline_lines: [
    { text: 'Costruisci.', color: '#0f172a', italic: false },
    { text: 'Traduci.',    color: '#b3261e', italic: true  },
    { text: 'Prenota.',    color: '#0f172a', italic: false },
  ],
  headline_font_family: 'serif',
  headline_font_size: 96,
  headline_line_height: 1.0,
  headline_font_weight: '700',
  headline_align: 'left',
  subhead: 'Un telaio, cinque prodotti, nessuna catena.',
  subhead_color: 'var(--olo-color-text, #374151)',
  subhead_size: 18,
  subhead_italic: true,
  subhead_max_width: 520,
  subhead_align: 'left',
  cta1_text: 'Prenota demo →',
  cta1_url: '#',
  cta1_bg: '#0f172a',
  cta1_color: '#ffffff',
  cta1_size: 14,
  cta1_radius: { tl: 999, tr: 999, br: 999, bl: 999, linked: true },
  cta2_text: 'Esplora i prodotti',
  cta2_url: '#',
  cta2_bg: 'transparent',
  cta2_color: '#0f172a',
  cta2_border: '#0f172a',
  cta2_size: 14,
  cta2_radius: { tl: 999, tr: 999, br: 999, bl: 999, linked: true },
  stats: [
    { value: '5',      value_color: '#0f172a', label: 'PRODOTTI MODULARI' },
    { value: 'Gratis', value_color: '#b3261e', label: 'OLOBUILD, PER SEMPRE' },
    { value: '0 %',    value_color: '#0f172a', label: 'SAAS · LOCK-IN · COMMISSIONI' },
  ],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: '#f0e9dc' },
  showcase_padding: 28,
  showcase_radius: { tl: 24, tr: 24, br: 24, bl: 24, linked: true },
  showcase_badge_text: 'DEMO LIVE',
  showcase_badge_dot: '#dc2626',
  showcase_badge_bg: '#ffffff',
  showcase_items: [
    { number: '01', text: 'crea',     italic: true, text_color: '#0f172a', bg: { type: 'solid', color: '#ffffff' } },
    { number: '02', text: 'anima',    italic: true, text_color: '#0f172a', bg: { type: 'solid', color: '#ffffff' } },
    { number: '03', text: 'traduci',  italic: true, text_color: '#0f172a', bg: { type: 'solid', color: '#ffffff' } },
    { number: '04', text: 'pubblica', italic: true, text_color: '#0f172a', bg: { type: 'solid', color: '#ffffff' } },
  ],
  showcase_card_radius: { tl: 18, tr: 18, br: 18, bl: 18, linked: true },
  showcase_card_shadow: 'sm',
  showcase_caption_left: 'PASSA IL MOUSE SUI TILE',
  showcase_caption_right: 'BORDER-RADIUS ANIMATO',
  showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr',
  gap: 60,
  min_height: 600,
  tile_padding: { top: 80, right: 80, bottom: 60, left: 80 },
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const SERIF = "'Playfair Display','Cormorant Garamond',Georgia,'Times New Roman',serif";
const SANS  = "'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";
const MONO  = "ui-monospace,'SF Mono',Menlo,Consolas,monospace";

const FONT_LEGACY = { serif: SERIF, 'sans-serif': SANS, mono: MONO };
const headlineFamily = computed(() => resolveFontFamily(s.value.headline_font_family, FONT_LEGACY) || SERIF);

const headlines = computed(() => (Array.isArray(s.value.headline_lines) ? s.value.headline_lines : []).filter(h => h && h.text));
const stats     = computed(() => (Array.isArray(s.value.stats)          ? s.value.stats          : []).slice(0, 4));
const items     = computed(() => (Array.isArray(s.value.showcase_items) ? s.value.showcase_items : []).slice(0, 4));

const hoverClass = computed(() => 'olo-hsplit-hover-' + (s.value.showcase_hover_effect || 'none'));

const SHADOW = SHADOW_SCALE;

const wrapStyle = computed(() => {
  const p = s.value.tile_padding || {};
  const style = { padding: `${p.top || 0}px ${p.right || 0}px ${p.bottom || 0}px ${p.left || 0}px` };
  if (s.value.min_height) style.minHeight = s.value.min_height + 'px';
  return style;
});

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: s.value.showcase_enabled ? (s.value.split_ratio || '1fr 1fr') : '1fr',
  gap: (s.value.gap || 60) + 'px',
  alignItems: 'center',
}));

const eyebrowStyle = computed(() => ({
  display: 'inline-flex',
  alignItems: 'center',
  gap: '10px',
  fontFamily: MONO,
  fontSize: '12px',
  letterSpacing: '0.08em',
  textTransform: 'uppercase',
  color: s.value.eyebrow_color || 'var(--olo-color-text, #1f2937)',
  marginBottom: '32px',
}));

const headlineStyle = computed(() => ({
  fontFamily: headlineFamily.value,
  fontSize: (s.value.headline_font_size || 96) + 'px',
  lineHeight: s.value.headline_line_height || 1,
  fontWeight: s.value.headline_font_weight || '700',
  letterSpacing: '-0.02em',
  textAlign: s.value.headline_align || 'left',
  margin: '0 0 28px',
}));

const subheadStyle = computed(() => {
  const style = {
    fontFamily: headlineFamily.value,
    fontSize: (s.value.subhead_size || 18) + 'px',
    lineHeight: 1.5,
    color: s.value.subhead_color || 'var(--olo-color-text, #374151)',
    textAlign: s.value.subhead_align || 'left',
    margin: '0 0 40px',
  };
  if (s.value.subhead_italic) style.fontStyle = 'italic';
  if (s.value.subhead_max_width) style.maxWidth = s.value.subhead_max_width + 'px';
  return style;
});

function radiusToCss(r) {
  if (!r) return '0';
  if (typeof r === 'number') return r + 'px';
  const tl = r.tl ?? 0, tr = r.tr ?? 0, br = r.br ?? 0, bl = r.bl ?? 0;
  return `${tl}px ${tr}px ${br}px ${bl}px`;
}

const cta1Style = computed(() => ({
  background: s.value.cta1_bg || '#0f172a',
  color:      s.value.cta1_color || '#ffffff',
  border:     `1px solid ${s.value.cta1_bg || '#0f172a'}`,
  borderRadius: radiusToCss(s.value.cta1_radius),
  fontSize:   (s.value.cta1_size || 14) + 'px',
}));

const cta2Style = computed(() => ({
  background: s.value.cta2_bg === 'transparent' ? 'transparent' : (s.value.cta2_bg || 'transparent'),
  color:      s.value.cta2_color || '#0f172a',
  border:     `1px solid ${s.value.cta2_border || '#0f172a'}`,
  borderRadius: radiusToCss(s.value.cta2_radius),
  fontSize:   (s.value.cta2_size || 14) + 'px',
}));

const statsStyle = computed(() => ({
  gridTemplateColumns: `repeat(${stats.value.length || 3}, 1fr)`,
}));

// Background helper for showcase wrap + each card
function bgToCss(bg) {
  if (!bg || bg.type === 'none') return '';
  if (bg.type === 'solid') return `background:${bg.color || '#ffffff'};`;
  if (bg.type === 'gradient') {
    const angle = bg.gradient_angle ?? 135;
    const from  = bg.gradient_from  || '#f0e9dc';
    const to    = bg.gradient_to    || '#ffffff';
    return `background:linear-gradient(${angle}deg, ${from}, ${to});`;
  }
  if (bg.type === 'image' && bg.image_url) {
    return `background-image:url(${bg.image_url});background-size:${bg.image_size || 'cover'};background-position:${bg.image_position || 'center'};background-repeat:${bg.image_repeat || 'no-repeat'};`;
  }
  // pattern/video/gallery: anteprima minimale tramite color fallback
  if (bg.color) return `background:${bg.color};`;
  return '';
}

const rightStyle = computed(() => {
  const out = {
    borderRadius: radiusToCss(s.value.showcase_radius),
    padding: (s.value.showcase_padding ?? 28) + 'px',
    minHeight: '480px',
    display: 'flex',
    flexDirection: 'column',
  };
  const css = bgToCss(s.value.showcase_bg);
  if (css) Object.assign(out, parseCssString(css));
  else out.background = '#f0e9dc';
  return out;
});

function cardStyle(it) {
  const out = {
    borderRadius: radiusToCss(s.value.showcase_card_radius),
    padding: '24px',
    display: 'flex',
    flexDirection: 'column',
    justifyContent: 'space-between',
    minHeight: '180px',
    boxShadow: SHADOW[s.value.showcase_card_shadow || 'sm'] || SHADOW.sm,
    transition: 'border-radius .4s cubic-bezier(.4,0,.2,1), transform .3s ease, box-shadow .3s ease',
  };
  const css = bgToCss(it.bg);
  if (css) Object.assign(out, parseCssString(css));
  else out.background = '#ffffff';
  return out;
}

// CSS string → style object (minimal parser per le sole proprietà che usiamo)
function parseCssString(str) {
  const out = {};
  str.split(';').forEach(decl => {
    const idx = decl.indexOf(':');
    if (idx < 0) return;
    const prop = decl.slice(0, idx).trim();
    const val  = decl.slice(idx + 1).trim();
    if (!prop) return;
    const camel = prop.replace(/-([a-z])/g, (_, c) => c.toUpperCase());
    out[camel] = val;
  });
  return out;
}
</script>

<style scoped>
.olo-hsplit__dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
.olo-hsplit__dot--sm { width: 8px; height: 8px; }

.olo-hsplit__hline { display: block; }

.olo-hsplit__ctas {
  display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 60px;
}
.olo-hsplit__cta {
  display: inline-flex; align-items: center; justify-content: center;
  padding: 14px 28px;
  font-family: 'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
  font-weight: 500; text-decoration: none;
  transition: transform .2s ease, box-shadow .2s ease, background .2s, color .2s;
  cursor: pointer;
}
.olo-hsplit__cta--primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,23,42,0.18); }
.olo-hsplit__cta--outline:hover { background: #0f172a !important; color: #fff !important; }

.olo-hsplit__stats {
  display: grid; gap: 32px;
  padding-top: 32px; border-top: 1px solid rgba(15,23,42,0.12);
}
.olo-hsplit__stat-val { font-size: 36px; line-height: 1; font-weight: 600; margin-bottom: 10px; }
.olo-hsplit__stat-lbl {
  font-family: ui-monospace,'SF Mono',Menlo,Consolas,monospace;
  font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase;
  color: var(--olo-color-text-soft, #6b7280); line-height: 1.4;
}

.olo-hsplit__badge {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 6px 14px; border-radius: 999px;
  font-family: ui-monospace,'SF Mono',Menlo,Consolas,monospace;
  font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase;
  color: #0f172a; align-self: flex-start;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}

.olo-hsplit__cards {
  display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
  margin: 24px 0 18px; flex: 1;
}

.olo-hsplit__card-num {
  font-family: ui-monospace,'SF Mono',Menlo,Consolas,monospace;
  font-size: 11px; color: var(--olo-color-text-faint, #9ca3af); letter-spacing: 0.05em;
}
.olo-hsplit__card-txt { font-size: 36px; font-weight: 500; text-align: center; }

.olo-hsplit__captions {
  display: flex; justify-content: space-between; align-items: center;
  margin-top: auto;
  font-family: ui-monospace,'SF Mono',Menlo,Consolas,monospace;
  font-size: 10px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--olo-color-text-faint, #9ca3af);
}

/* Hover effects per le card (sincronizzati con PHP).
 * Il border-radius hover è gestito dal sistema standard withHover (radius oggetto +
 * transition automatica): qui restano solo gli effetti su transform/shadow. */
.olo-hsplit-hover-lift .olo-hsplit__card:hover { transform: translateY(-8px); box-shadow: 0 18px 40px rgba(0,0,0,0.18) !important; }
.olo-hsplit-hover-scale .olo-hsplit__card:hover { transform: scale(1.04); z-index: 2; }
.olo-hsplit-hover-tilt .olo-hsplit__card { transform-style: preserve-3d; }
.olo-hsplit-hover-tilt .olo-hsplit__card:hover { transform: perspective(800px) rotateX(4deg) rotateY(-4deg) scale(1.02); }

/* a11y: anello di focus visibile da tastiera sui CTA (color-mix sul primario corrente) */
.olo-hsplit__cta:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
