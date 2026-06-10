<template>
  <div class="olo-introsplit ois" :style="rootStyle">
    <div v-if="left" class="ois-mediawrap" style="position:relative"><component :is="MediaBlock" /><span v-if="s.media_blob" :style="blobStyle"></span></div>
    <div class="ois-content" :style="contentStyle">
      <span v-if="s.eyebrow" :style="eyebrowStyle">{{ s.eyebrow }}</span>
      <h2 v-if="s.headline" :style="hStyle">{{ s.headline }}<span v-if="s.accent" :style="{ color: accc, fontStyle: s.accent_italic ? 'italic' : 'normal' }"> {{ s.accent }}</span><template v-if="s.headline_tail">{{ s.headline_tail }}</template></h2>
      <p v-if="s.lead" :style="leadStyle">{{ s.lead }}</p>
      <p v-if="s.signature" :style="sigStyle">{{ s.signature }}</p>
      <div v-if="stats.length" :style="statsStyle">
        <div v-for="(st, i) in stats" :key="i"><b :style="statNumStyle">{{ st.number }}</b><span :style="statLblStyle">{{ st.label }}</span></div>
      </div>
      <div v-if="s.cta_text || s.cta2_text" style="display:flex;gap:13px;flex-wrap:wrap;align-items:center;margin-top:30px">
        <a v-if="s.cta_text" :href="s.cta_url || '#'" :style="ctaUnderline ? linkStyle : (ctaOutline ? outlineStyle : ctaStyle)">{{ s.cta_text }}</a>
        <a v-if="s.cta2_text" :href="s.cta2_url || '#'" :style="cta2Underline ? linkStyle : (cta2Outline ? outlineStyle : ctaStyle)">{{ s.cta2_text }}</a>
      </div>
    </div>
    <div v-if="!left" class="ois-mediawrap" style="position:relative"><component :is="MediaBlock" /><span v-if="s.media_blob" :style="blobStyle"></span></div>
  </div>
</template>

<script setup>
import { computed, h } from 'vue';
import { buildBgStyle } from '@/composables/useBackgroundStyle';
import { radiusToCss } from '@/composables/useRadius';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  eyebrow: 'One unit · since 1974', eyebrow_color: '', headline: 'A regional club with a', accent: 'rich history', headline_tail: '', uppercase: true,
  headline_color: '', accent_color: '',
  lead: "From a handful of friends on a muddy field to eight competitive teams across men's, women's and youth football — Verdano FC is built on the people who keep showing up.",
  lead_color: '',
  stats: [{ number: '50', label: 'Years of football' }, { number: '8', label: 'Competitive teams' }, { number: '600+', label: 'Active members' }],
  stat_number_color: '', stat_label_color: '',
  cta_text: 'About the club', cta_url: '#', cta2_text: '', cta2_url: '#', cta2_style: 'outline', cta_bg: '', cta_color: '#ffffff',
  media_image: '', media_label: 'club portrait — squad on the pitch', media_light: true, media_aspect: '4/4.4', media_radius: 20, media_radius_top: 0, media_blob: false, media_blob_color: '',
  // Spaziatura/forma additive — default no-op (padding gated OFF, raggi = valori attuali).
  pad_custom: false,
  content_padding: { top: 0, right: 0, bottom: 0, left: 0 },
  badge_radius: { tl: 16, tr: 16, br: 16, bl: 16 },
  cta_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
  badge_number: '1974', badge_label: 'Established', badge_bg: '', badge_color: '', media_position: 'right',
  flush: false, content_bg: '', signature: '', cta_style: 'button', accent_italic: false, headline_weight: '900', headline_size: '',
  // KIT standard OLObuild — default no-op (parità col PHP): bg none, shadow none, border 0.
  bg: { type: 'none' },
  shadow: 'none',
  border: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: 'solid', color: '' },
  border_hover: { top: 0, right: 0, bottom: 0, left: 0, linked: true, style: '', color: '' },
  border_hover_duration: 300,
  border_effect: 'none', border_effect_intensity: 'medium', border_effect_color2: '', border_effect_angle: 135, border_effect_speed: 4,
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const stats = computed(() => Array.isArray(s.value.stats) ? s.value.stats : []);
const left = computed(() => s.value.media_position === 'left');
const flush = computed(() => !!s.value.flush);
const ctaUnderline = computed(() => s.value.cta_style === 'underline');
const ctaOutline = computed(() => s.value.cta_style === 'outline');
const cta2Underline = computed(() => s.value.cta2_style === 'underline');
const cta2Outline = computed(() => s.value.cta2_style === 'outline');

const DISP = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";
const eyec = computed(() => s.value.eyebrow_color || 'var(--olo-color-secondary, #d33a55)');
const accc = computed(() => s.value.accent_color || 'var(--olo-color-secondary, #d33a55)');
const bbg = computed(() => s.value.badge_bg || 'var(--olo-color-primary, #c8ff3c)');
const light = computed(() => !!s.value.media_light);

// KIT — sfondo completo (parità con get_bg_inline_css PHP via buildBgStyle).
const kitBgStyle = computed(() => {
  const bg = s.value.bg;
  if (!bg || !bg.type || bg.type === 'none') return {};
  return buildBgStyle(bg);
});
// KIT — ombra (parità con build_shadow_decl PHP).
const SHADOW_MAP = {
  sm: '0 1px 2px rgba(16,24,40,.06), 0 6px 16px -10px rgba(16,24,40,.18)',
  md: '0 2px 4px rgba(16,24,40,.06), 0 14px 28px -12px rgba(22,38,61,.28)',
  lg: '0 8px 24px -6px rgba(16,24,40,.18), 0 18px 40px -12px rgba(22,38,61,.30)',
  xl: '0 12px 32px -8px rgba(16,24,40,.20), 0 28px 56px -14px rgba(22,38,61,.34)',
};
const kitShadow = computed(() => {
  const p = s.value.shadow || 'none';
  if (p === 'none' || p === '') return '';
  if (p === 'custom') {
    const h = parseInt(s.value.shadow_h, 10) || 0;
    const v = parseInt(s.value.shadow_v, 10) || 4;
    const blur = Math.max(0, parseInt(s.value.shadow_blur, 10) || 10);
    const spread = parseInt(s.value.shadow_spread, 10) || 0;
    const color = s.value.shadow_color || 'rgba(0,0,0,0.15)';
    const inset = s.value.shadow_inset ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }
  return SHADOW_MAP[p] || '';
});
// KIT — bordo statico (parità con build_border_css PHP: solo se valorizzato).
const kitBorder = computed(() => {
  const b = s.value.border;
  if (!b) return {};
  const t = parseInt(b.top, 10) || 0, r = parseInt(b.right, 10) || 0, bo = parseInt(b.bottom, 10) || 0, l = parseInt(b.left, 10) || 0;
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
});

// Spaziatura/forma additive — parità col PHP. Default no-op.
// Radius: collassa 4 angoli uguali in un solo valore (byte-identico al render storico).
const radiusCss = (br, fallback) => {
  if (!br || typeof br !== 'object') return fallback;
  const tl = parseInt(br.tl, 10) || 0, tr = parseInt(br.tr, 10) || 0, b = parseInt(br.br, 10) || 0, bl = parseInt(br.bl, 10) || 0;
  if (!tl && !tr && !b && !bl) return fallback;
  if (tl === tr && tr === b && b === bl) return tl + 'px';
  return `${tl}px ${tr}px ${b}px ${bl}px`;
};
const padCss = computed(() => {
  if (!s.value.pad_custom) return '';
  const cp = (s.value.content_padding && typeof s.value.content_padding === 'object') ? s.value.content_padding : {};
  const t = parseInt(cp.top, 10) || 0, r = parseInt(cp.right, 10) || 0, b = parseInt(cp.bottom, 10) || 0, l = parseInt(cp.left, 10) || 0;
  return `${t}px ${r}px ${b}px ${l}px`;
});
const bradCss = computed(() => radiusCss(s.value.badge_radius, '16px'));
const cradCss = computed(() => radiusCss(s.value.cta_radius, '999px'));

const rootStyle = computed(() => {
  const cols = flush.value ? '1fr 1fr' : (left.value ? '.95fr 1.05fr' : '1.05fr .95fr');
  const base = { position: 'relative', display: 'grid', gridTemplateColumns: cols, gap: flush.value ? '0' : '54px', alignItems: flush.value ? 'stretch' : 'center', fontFamily: SANS, ...kitBgStyle.value, ...kitBorder.value };
  if (padCss.value) base.padding = padCss.value;
  if (kitShadow.value) base.boxShadow = kitShadow.value;
  return base;
});
const contentStyle = computed(() => {
  const st = {};
  if (flush.value) { st.display = 'flex'; st.flexDirection = 'column'; st.justifyContent = 'center'; st.padding = 'clamp(40px,6vw,80px)'; }
  if (s.value.content_bg) st.background = s.value.content_bg;
  return st;
});
const eyebrowStyle = computed(() => ({ fontWeight: 700, fontSize: '12px', letterSpacing: '.18em', textTransform: 'uppercase', color: eyec.value, display: 'block', marginBottom: '16px' }));
const hStyle = computed(() => {
  const n = parseInt(s.value.headline_size, 10);
  const fs = n > 0 ? `clamp(30px,4.2vw,${n}px)` : 'clamp(34px,5.2vw,68px)';
  return { fontFamily: DISP, fontWeight: s.value.headline_weight || '900', fontSize: fs, lineHeight: flush.value ? 1.05 : .9, letterSpacing: '-.01em', textTransform: s.value.uppercase ? 'uppercase' : 'none', margin: 0, color: s.value.headline_color || 'var(--olo-color-text, #142019)' };
});
const leadStyle = computed(() => ({ margin: '24px 0', fontSize: '16.5px', lineHeight: 1.65, color: s.value.lead_color || 'var(--olo-color-text-muted, #4f5b54)', maxWidth: '440px' }));
const statsStyle = { display: 'flex', gap: '34px', margin: '26px 0 30px', flexWrap: 'wrap' };
const statNumStyle = computed(() => ({ fontFamily: DISP, fontWeight: 900, fontSize: '46px', color: s.value.stat_number_color || 'var(--olo-color-text, #142019)', display: 'block', lineHeight: 1 }));
const statLblStyle = computed(() => ({ fontSize: '12.5px', color: s.value.stat_label_color || 'var(--olo-color-text-muted, #8a948d)', textTransform: 'uppercase', letterSpacing: '.06em', marginTop: '5px', display: 'block' }));
const ctaStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', background: s.value.cta_bg || 'var(--olo-color-text, #0a2a1e)', color: s.value.cta_color || '#fff', fontWeight: 700, fontSize: '14px', padding: '14px 24px', borderRadius: cradCss.value, textDecoration: 'none' }));
const sigStyle = computed(() => ({ fontFamily: DISP, fontStyle: 'italic', fontSize: '24px', color: s.value.headline_color || 'var(--olo-color-text, #142019)', margin: '18px 0 0' }));
const linkStyle = computed(() => ({ display: 'inline-block', marginTop: '24px', fontWeight: 500, fontSize: '12px', letterSpacing: '.18em', textTransform: 'uppercase', color: s.value.cta_color || 'var(--olo-color-text, #142019)', borderBottom: `1px solid ${accc.value}`, paddingBottom: '3px', textDecoration: 'none' }));
const outlineStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '8px', background: 'transparent', color: s.value.cta_color || '#fff', fontWeight: 700, fontSize: '14px', padding: '14px 24px', borderRadius: cradCss.value, textDecoration: 'none', border: `1px solid ${accc.value}` }));

const MediaBlock = computed(() => {
  const img = String(s.value.media_image || '').trim();
  const stripe = light.value ? 'rgba(20,32,25,.06)' : 'rgba(255,255,255,.05)';
  const mbg = light.value ? '#d8d2c2' : 'var(--olo-color-surface-alt, #0f3a2a)';
  const lblcol = light.value ? 'rgba(20,32,25,.45)' : 'rgba(255,255,255,.4)';
  const mbgObj = s.value.media_bg;
  const hasBg = !!(mbgObj && mbgObj.type && mbgObj.type !== 'none');
  // media_radius dual-format: numero legacy O oggetto {tl,tr,br,bl} (type border-radius).
  const mrRaw = s.value.media_radius;
  const mrObj = (mrRaw && typeof mrRaw === 'object') ? mrRaw : null;
  const mrt = parseInt(s.value.media_radius_top, 10) || 0;
  let mediaRadius;
  if (mrt > 0) {
    // Arco: angoli superiori da media_radius_top, inferiori da media_radius (scalare o br/bl dell'oggetto).
    const mrb = Number.isNaN(parseInt(mrRaw, 10)) ? 20 : parseInt(mrRaw, 10);
    const brC = mrObj ? (parseInt(mrObj.br, 10) || 0) : mrb;
    const blC = mrObj ? (parseInt(mrObj.bl, 10) || 0) : mrb;
    mediaRadius = `${mrt}px ${mrt}px ${brC}px ${blC}px`;
  } else {
    mediaRadius = radiusToCss(mrRaw, { fallback: '20px' });
  }
  const media = {
    position: 'relative', zIndex: 1,
    borderRadius: mediaRadius,
    overflow: 'hidden', backgroundSize: 'cover', backgroundPosition: 'center',
  };
  if (hasBg) { Object.assign(media, buildBgStyle(mbgObj)); }
  else { media.background = mbg; media.backgroundImage = img ? 'url(' + img + ')' : 'repeating-linear-gradient(135deg, ' + stripe + ' 0 18px, transparent 18px 36px)'; }
  if (flush.value) { media.height = '100%'; media.minHeight = '440px'; } else { media.aspectRatio = String(s.value.media_aspect || '4/4.4').replace(/[^0-9.\/]/g, ''); }
  const children = [];
  if (!hasBg && !img && s.value.media_label) children.push(h('span', { style: { position: 'absolute', left: '16px', bottom: '14px', fontSize: '11px', letterSpacing: '.04em', textTransform: 'uppercase', fontWeight: 600, color: lblcol } }, s.value.media_label));
  if (s.value.badge_number || s.value.badge_label) {
    const bc = [];
    if (s.value.badge_number) bc.push(h('b', { style: { fontFamily: DISP, fontWeight: 900, fontSize: '34px', display: 'block', lineHeight: 1 } }, s.value.badge_number));
    if (s.value.badge_label) bc.push(h('span', { style: { fontSize: '12px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '.06em' } }, s.value.badge_label));
    children.push(h('div', { style: { position: 'absolute', left: '-18px', bottom: '24px', background: bbg.value, color: s.value.badge_color || 'var(--olo-color-primary-contrast, #0a2a1e)', borderRadius: bradCss.value, padding: '18px 22px', boxShadow: '0 18px 40px -16px rgba(10,42,30,.5)' } }, bc));
  }
  return () => h('div', { style: media }, children);
});
const blobStyle = computed(() => ({ position: 'absolute', left: '-24px', bottom: '-24px', width: '130px', height: '130px', borderRadius: '50%', background: s.value.media_blob_color || 'var(--olo-color-primary, #e7a0b4)', mixBlendMode: 'screen', opacity: 0.5, filter: 'blur(6px)', pointerEvents: 'none', zIndex: 0 }));
</script>
