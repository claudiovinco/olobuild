<template>
  <div class="olo-scrubtext ost" :style="rootStyle">
    <p ref="textEl" class="ost-p" :style="pStyle" data-olo-wave v-html="s.text"></p>
    <p v-if="s.show_lead && s.lead" ref="leadEl" class="ost-lead" :style="leadStyle" v-html="s.lead"></p>
  </div>
</template>

<script setup>
import { computed, ref, watch, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const borderDefault = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' };
const borderHoverDefault = { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' };

const defaults = {
  text: 'Idee che si <em>vedono.</em><br/>Progetti che <em>funzionano.</em>',
  show_lead: true,
  lead: 'La mia consulenza parte da un\'analisi della situazione reale dell\'azienda — sfide e opportunità — per identificare le soluzioni più adatte. Poi le rendo visibili: strategia, web e media originali, in un unico filo conduttore.',
  scroll_reveal: true,
  dim_opacity: 13,
  accent: '',
  text_color: '',
  lead_color: '',
  size_min: 26,
  size_max: 56,
  max_width_ch: 20,
  lead_size: 16.5,
  lead_max_width_ch: 52,

  // KIT standard OLObuild — additivi, no-op coi default (sfondo none, ombra none, bordo 0)
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

const DISP = "var(--olo-font-family-heading, 'Big Shoulders Display', sans-serif)";
const SANS = "var(--olo-font-family, 'Hanken Grotesk', sans-serif)";

const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #C6F24E)');
const txt = computed(() => s.value.text_color || 'var(--olo-color-text, #ECEAE3)');
const leadCol = computed(() => s.value.lead_color || 'var(--olo-color-text-soft, #a0a298)');

const sizeMin = computed(() => { const n = Number(s.value.size_min); return n > 0 ? n : 26; });
const sizeMax = computed(() => { const n = Number(s.value.size_max); return n > 0 ? n : 56; });
const maxCh = computed(() => { const n = Number(s.value.max_width_ch); return n > 0 ? n : 20; });
const leadSize = computed(() => { const n = Number(s.value.lead_size); return n > 0 ? n : 16.5; });
const leadCh = computed(() => { const n = Number(s.value.lead_max_width_ch); return n > 0 ? n : 52; });
const dim = computed(() => {
  const n = Number(s.value.dim_opacity);
  const v = Number.isFinite(n) ? Math.max(0, Math.min(100, n)) : 13;
  return v / 100;
});

// ── KIT standard: sfondo completo (override SOLO se valorizzato) ──
const bgKitStyle = computed(() => {
  const b = s.value.bg;
  if (!b || !b.type || b.type === 'none') return {};
  return buildBgStyle(b);
});

// ── KIT standard: ombra (preset sm/md/lg/xl o custom) — parità con build_shadow_decl PHP ──
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
  const map = {
    sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
    md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
    lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
    xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
  };
  return map[preset] || '';
});

// ── KIT standard: bordo — parità con parse_border/build_border_css PHP (no-op se vuoto) ──
const borderStyle = computed(() => {
  const b = s.value.border;
  if (!b || typeof b !== 'object') return {};
  const color = String(b.color || '').trim();
  if (color === '') return {};
  const style = b.style || 'solid';
  const t = Math.max(0, parseInt(b.top ?? 0, 10) || 0);
  const r = Math.max(0, parseInt(b.right ?? 0, 10) || 0);
  const bo = Math.max(0, parseInt(b.bottom ?? 0, 10) || 0);
  const l = Math.max(0, parseInt(b.left ?? 0, 10) || 0);
  if (!t && !r && !bo && !l) return {};
  if (t === r && r === bo && bo === l) {
    return { border: `${t}px ${style} ${color}` };
  }
  const out = {};
  if (t) out.borderTop = `${t}px ${style} ${color}`;
  if (r) out.borderRight = `${r}px ${style} ${color}`;
  if (bo) out.borderBottom = `${bo}px ${style} ${color}`;
  if (l) out.borderLeft = `${l}px ${style} ${color}`;
  return out;
});

const rootStyle = computed(() => {
  const base = {
    fontFamily: SANS,
    '--ost-accent': accent.value,
    '--ost-dim': String(dim.value),
  };
  const kitBg = bgKitStyle.value;
  if (kitBg && Object.keys(kitBg).length) Object.assign(base, kitBg);
  if (shadowDecl.value) base.boxShadow = shadowDecl.value;
  Object.assign(base, borderStyle.value);
  return base;
});

const pStyle = computed(() => ({
  fontFamily: DISP,
  fontWeight: 600,
  fontSize: `clamp(${sizeMin.value}px,4.2vw,${sizeMax.value}px)`,
  lineHeight: 1.04,
  letterSpacing: '-.01em',
  textTransform: 'none',
  maxWidth: maxCh.value + 'ch',
  margin: 0,
  color: txt.value,
}));

const leadStyle = computed(() => ({
  fontFamily: SANS,
  fontWeight: 400,
  fontSize: leadSize.value + 'px',
  lineHeight: 1.65,
  color: leadCol.value,
  maxWidth: leadCh.value + 'ch',
  margin: '28px 0 0',
}));

// ── Signature: scrub parola-per-parola allo scroll (parità con runtime PHP) ──
const textEl = ref(null);
const leadEl = ref(null);
let sets = [];
let ticking = false;
let listening = false;

function splitWords(root) {
  const ws = [];
  (function walk(n) {
    [].slice.call(n.childNodes).forEach((ch) => {
      if (ch.nodeType === 3) {
        if (!ch.textContent.trim()) return;
        const parts = ch.textContent.split(/(\s+)/);
        const fr = document.createDocumentFragment();
        parts.forEach((p) => {
          if (!p) return;
          if (/^\s+$/.test(p)) { fr.appendChild(document.createTextNode(p)); return; }
          const sp = document.createElement('span');
          sp.className = 'st-w';
          sp.textContent = p;
          fr.appendChild(sp);
          ws.push(sp);
        });
        n.replaceChild(fr, ch);
      } else if (ch.nodeType === 1) {
        if (ch.tagName !== 'BR') walk(ch);
      }
    });
  })(root);
  return ws;
}

function update() {
  ticking = false;
  const vh = window.innerHeight;
  sets.forEach((st) => {
    const r = st.el.getBoundingClientRect();
    const p = Math.max(0, Math.min(1, (vh * 0.9 - r.top) / (vh * 0.6)));
    const n = Math.round(p * st.ws.length);
    st.ws.forEach((w, i) => { w.classList.toggle('on', i < n); });
  });
}

function onScroll() {
  if (!ticking) { ticking = true; window.requestAnimationFrame(update); }
}

function teardown() {
  if (listening) {
    document.removeEventListener('scroll', onScroll, true);
    window.removeEventListener('resize', update);
    listening = false;
  }
  sets = [];
}

function setup() {
  teardown();
  // Ripristina l'HTML originale (rimuove gli span di uno split precedente).
  if (textEl.value) textEl.value.innerHTML = s.value.text || '';
  if (leadEl.value) leadEl.value.innerHTML = s.value.lead || '';
  if (!s.value.scroll_reveal) return;
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduce) return;
  const targets = [];
  if (textEl.value) targets.push(textEl.value);
  if (leadEl.value) targets.push(leadEl.value);
  if (!targets.length) return;
  sets = targets.map((t) => ({ el: t, ws: splitWords(t) }));
  // Capture: nel canvas lo scroll può avvenire su un contenitore interno.
  document.addEventListener('scroll', onScroll, true);
  window.addEventListener('resize', update);
  listening = true;
  update();
}

onMounted(() => { nextTick(setup); });
watch(
  () => [s.value.text, s.value.lead, s.value.show_lead, s.value.scroll_reveal, s.value.dim_opacity],
  () => { nextTick(setup); }
);
onBeforeUnmount(teardown);
</script>

<style scoped>
.ost :deep(.ost-p em) {
  font-style: normal;
  color: var(--ost-accent, var(--olo-color-primary, #C6F24E));
}
.ost :deep(.st-w) {
  opacity: var(--ost-dim, 0.13);
  transition: opacity .3s ease;
}
.ost :deep(.st-w.on) {
  opacity: 1;
}
@media (prefers-reduced-motion: reduce) {
  .ost :deep(.st-w) { opacity: 1; }
}
</style>
