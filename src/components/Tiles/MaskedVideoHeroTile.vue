<template>
  <section class="olo-maskedvideohero mvh" :style="rootStyle">
    <div class="mvh-bg" :style="bgStyle">
      <div class="mvh-media" :style="mediaStyle">
        <span v-if="!s.bg_image && !s.transparent_bg && s.media_label" class="mvh-medialabel" :style="mediaLabelStyle">{{ s.media_label }}</span>
      </div>
      <div class="mvh-grad" :style="gradStyle"></div>
      <div v-if="s.watermark_text" class="mvh-ghost" :style="ghostStyle">{{ s.watermark_text }}</div>
    </div>
    <div class="mvh-in" :style="inStyle">
      <span v-if="s.tag_text" class="mvh-tag" :style="tagStyle"><span class="mvh-dot" :style="dotStyle"></span>{{ s.tag_text }}</span>
      <h1 class="mvh-h" :style="hStyle">{{ s.headline_text }}<span v-if="s.accent_text" class="mvh-acc" :style="{ color: accent }"> {{ s.accent_text }}</span></h1>
      <div class="mvh-row" :style="rowStyle">
        <p v-if="s.subhead" class="mvh-sub" :style="subStyle">{{ s.subhead }}</p>
        <div class="mvh-cta" :style="{ display: 'flex', gap: '12px', flexWrap: 'wrap' }">
          <a v-if="s.cta1_text" class="mvh-btn mvh-btn--solid" :style="solidStyle" :href="s.cta1_url || '#'">{{ s.cta1_text }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="width:17px;height:17px"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
          <a v-if="s.cta2_text" class="mvh-btn mvh-btn--ghost" :style="ghostBtnStyle" :href="s.cta2_url || '#'">{{ s.cta2_text }}</a>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  tag_text: 'Next home game · Sat 14 Mar · 15:00', tag_dot_color: '',
  headline_text: 'Forged on the', accent_text: 'pitch.', uppercase: true,
  subhead: 'Eight teams, one badge. Verdano FC has played, fought and grown in this city for fifty years — and we’re only getting started.',
  cta1_text: 'View fixtures', cta1_url: '#', cta2_text: 'Become a member', cta2_url: '#',
  bg_color: '#0a2a1e', bg_image: '', media_label: 'home hero — match footage · background video',
  overlay_color: '#0a2a1e', overlay_strength: 0.55, watermark_text: 'VFC', watermark_color: 'rgba(255,255,255,0.055)',
  accent: '', accent_on: '#0a2a1e', text_color: '#ffffff', sub_color: 'rgba(255,255,255,0.72)', arch: true, transparent_bg: false, min_height: 84,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const DISP = "var(--olo-font-family-heading, 'Archivo',-apple-system,sans-serif)";
const SANS = "var(--olo-font-family, 'Work Sans',-apple-system,sans-serif)";
const accent = computed(() => s.value.accent || 'var(--olo-color-primary, #c8ff3c)');
const accOn = computed(() => s.value.accent_on || '#0a2a1e');
const txt = computed(() => s.value.text_color || '#ffffff');

function hexRgb(hex, fb = '10,42,30') {
  let h = String(hex || '').replace('#', '');
  if (h.length === 3) h = h.split('').map(c => c + c).join('');
  if (!/^[0-9a-fA-F]{6}$/.test(h)) return fb;
  return parseInt(h.slice(0, 2), 16) + ',' + parseInt(h.slice(2, 4), 16) + ',' + parseInt(h.slice(4, 6), 16);
}

const mask = computed(() => s.value.arch ? 'radial-gradient(150% 125% at 50% 0%, #000 87%, transparent 87.5%)' : 'none');
const rootStyle = computed(() => ({ position: 'relative', overflow: 'hidden', minHeight: (s.value.min_height || 84) + 'vh', display: 'flex', alignItems: 'center', color: txt.value, fontFamily: SANS, '--mvh-accent': accent.value }));
const bgStyle = computed(() => ({ position: 'absolute', inset: 0, zIndex: 0, overflow: 'hidden', background: s.value.transparent_bg ? 'transparent' : (s.value.bg_color || '#0a2a1e'), WebkitMask: mask.value, mask: mask.value }));
const mediaStyle = computed(() => {
  const tr = s.value.transparent_bg;
  const st = { position: 'absolute', inset: 0, background: tr ? 'transparent' : (s.value.bg_color || '#0a2a1e'), backgroundSize: 'cover', backgroundPosition: 'center' };
  st.backgroundImage = s.value.bg_image ? 'url(' + s.value.bg_image + ')' : (tr ? 'none' : 'repeating-linear-gradient(135deg, rgba(255,255,255,.05) 0 18px, rgba(255,255,255,0) 18px 36px)');
  return st;
});
const mediaLabelStyle = { position: 'absolute', left: '18px', bottom: '14px', fontSize: '11px', letterSpacing: '.04em', textTransform: 'uppercase', fontWeight: 600, color: 'rgba(255,255,255,.4)' };
const gradStyle = computed(() => {
  const rgb = hexRgb(s.value.overlay_color || '#0a2a1e');
  const st = Number(s.value.overlay_strength ?? 0.55);
  const a = (m, cap) => Math.min(cap, Math.round(st * m * 1000) / 1000);
  return { position: 'absolute', inset: 0, zIndex: 1, background: s.value.transparent_bg ? 'none' : `linear-gradient(180deg, rgba(${rgb},${a(0.9,0.96)}) 0%, rgba(${rgb},${a(0.27,0.96)}) 38%, rgba(${rgb},${a(1.7,0.97)}) 100%)` };
});
const ghostStyle = computed(() => ({ position: 'absolute', top: '6%', left: '50%', transform: 'translateX(-50%)', zIndex: 1, fontFamily: DISP, fontWeight: 900, fontSize: 'min(34vw,420px)', lineHeight: 1, color: s.value.watermark_color || 'rgba(255,255,255,0.055)', letterSpacing: '-.02em', pointerEvents: 'none', whiteSpace: 'nowrap' }));
const inStyle = { position: 'relative', zIndex: 2, width: '100%', maxWidth: '1240px', margin: '0 auto', padding: 'clamp(36px,7vh,80px) 28px clamp(70px,12vh,130px)' };
const tagStyle = { display: 'inline-flex', alignItems: 'center', gap: '10px', marginBottom: '22px', padding: '8px 16px', borderRadius: '999px', background: 'rgba(255,255,255,.08)', border: '1px solid rgba(255,255,255,.16)', fontSize: '12.5px', fontWeight: 700, letterSpacing: '.06em' };
const dotStyle = computed(() => ({ width: '8px', height: '8px', borderRadius: '50%', background: s.value.tag_dot_color || accent.value, boxShadow: '0 0 10px ' + (s.value.tag_dot_color || accent.value) }));
const hStyle = computed(() => ({ fontFamily: DISP, fontWeight: 900, fontSize: 'clamp(48px,8.4vw,128px)', lineHeight: .86, letterSpacing: '-.01em', textTransform: s.value.uppercase ? 'uppercase' : 'none', color: txt.value, margin: 0 }));
const rowStyle = { display: 'flex', alignItems: 'flex-end', justifyContent: 'space-between', gap: '30px', flexWrap: 'wrap', marginTop: '30px' };
const subStyle = computed(() => ({ maxWidth: '440px', fontSize: '16.5px', lineHeight: 1.6, color: s.value.sub_color || 'rgba(255,255,255,0.72)', margin: 0 }));
const solidStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '9px', padding: '17px 30px', borderRadius: '999px', fontFamily: SANS, fontWeight: 700, fontSize: '15px', textDecoration: 'none', background: accent.value, color: accOn.value, border: 0 }));
const ghostBtnStyle = computed(() => ({ display: 'inline-flex', alignItems: 'center', gap: '9px', padding: '17px 30px', borderRadius: '999px', fontFamily: SANS, fontWeight: 700, fontSize: '15px', textDecoration: 'none', background: 'rgba(255,255,255,.06)', color: txt.value, border: '1.5px solid rgba(255,255,255,.26)' }));
</script>

<style scoped>
.mvh-btn { transition: transform .15s, filter .2s; }
.mvh-btn:hover { transform: translateY(-2px); filter: brightness(1.06); }
.mvh-btn:focus-visible { outline: 2px solid var(--mvh-accent, #c8ff3c); outline-offset: 3px; }
</style>
