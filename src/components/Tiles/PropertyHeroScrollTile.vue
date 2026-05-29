<template>
  <div :style="wrapStyle">
    <div :style="trackStyle">
      <div v-for="(card, i) in cards" :key="i" :style="cardStyle()">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
      </div>
    </div>
    <!-- Edge gradients -->
    <div :style="{ position:'absolute',top:0,bottom:0,left:0,width:'80px',background:'linear-gradient(to right,'+bg+',transparent)',zIndex:2,pointerEvents:'none' }"></div>
    <div :style="{ position:'absolute',top:0,bottom:0,right:0,width:'80px',background:'linear-gradient(to left,'+bg+',transparent)',zIndex:2,pointerEvents:'none' }"></div>
    <!-- Overlay -->
    <div v-if="s.show_overlay !== false" :style="overlayStyle">
      <div v-if="s.show_badges !== false" style="margin-bottom:6px"><span style="padding:3px 10px;border-radius:6px;font-size:11px;font-weight:700;text-transform:uppercase;background:var(--olo-color-primary, #e1474f);color:var(--olo-color-primary-contrast, #fff)">{{ t('Vendita') }}</span></div>
      <div v-if="s.show_title !== false" :style="{ fontSize:(s.title_size||26)+'px', fontWeight:700, color:s.title_color||'#fff', lineHeight:'1.25' }">{{ t('Trilocale Centro Storico') }}</div>
      <div v-if="s.show_location !== false" style="font-size:13px;color:rgba(255,255,255,.7);margin-top:4px">{{ t('Trento — Centro') }}</div>
      <div v-if="s.show_price !== false" :style="{ fontSize:(s.price_size||22)+'px', fontWeight:800, color:s.price_color||'#fff', marginTop:'6px' }">{{ t('&euro; 325.000') }}</div>
      <div v-if="s.show_specs !== false" style="display:flex;gap:8px;margin-top:6px">
        <span style="padding:3px 8px;border-radius:6px;font-size:11px;font-weight:600;color:rgba(255,255,255,.85);background:rgba(255,255,255,.1)">{{ t('95 m²') }}</span>
        <span style="padding:3px 8px;border-radius:6px;font-size:11px;font-weight:600;color:rgba(255,255,255,.85);background:rgba(255,255,255,.1)">{{ t('4 locali') }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => ({ ...props.settings }));
const bg = computed(() => s.value.bg_color || 'var(--olo-color-dark, #16263d)');
const h = computed(() => (parseInt(s.value.height) || 380) + 'px');
const iw = computed(() => (parseInt(s.value.image_width) || 500) + 'px');
const gap = computed(() => (parseInt(s.value.gap) || 16) + 'px');
const speed = computed(() => (parseInt(s.value.speed) || 30) + 's');
const irad = computed(() => (parseInt(s.value.image_radius) || 12) + 'px');
const brad = computed(() => (parseInt(s.value.border_radius) || 0) + 'px');

const cards = Array.from({ length: 10 });

const wrapStyle = computed(() => ({
  position: 'relative', height: h.value, overflow: 'hidden',
  background: bg.value, borderRadius: brad.value,
}));

const trackStyle = computed(() => ({
  display: 'flex', gap: gap.value,
  animation: `olo-hero-scroll ${speed.value} linear infinite`,
}));

const cardStyle = () => ({
  flexShrink: 0, width: iw.value, height: h.value,
  borderRadius: irad.value,
  background: 'rgba(255,255,255,.06)',
  border: '1px solid rgba(255,255,255,.08)',
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  color: 'rgba(255,255,255,.22)',
});

const overlayStyle = computed(() => {
  const base = { position:'absolute', zIndex:3, maxWidth:'90%' };
  const pos = s.value.overlay_position || 'bottom-left';
  if (pos === 'center') Object.assign(base, { top:'50%', left:'50%', transform:'translate(-50%,-50%)', textAlign:'center' });
  else if (pos === 'bottom-center') Object.assign(base, { bottom:'24px', left:'50%', transform:'translateX(-50%)', textAlign:'center' });
  else Object.assign(base, { bottom:'24px', left:'24px' });
  const st = s.value.overlay_style || 'glass';
  if (st === 'glass') Object.assign(base, { background:'rgba(15,23,42,.5)', backdropFilter:'blur(16px)', WebkitBackdropFilter:'blur(16px)', padding:'18px 24px', borderRadius:'12px', border:'1px solid rgba(255,255,255,.08)' });
  else if (st === 'gradient') Object.assign(base, { background:'linear-gradient(135deg,rgba(0,0,0,.7),rgba(0,0,0,.3))', padding:'18px 24px', borderRadius:'12px' });
  else if (st === 'solid') Object.assign(base, { background:'rgba(0,0,0,.75)', padding:'18px 24px', borderRadius:'12px' });
  else Object.assign(base, { padding:'18px 24px', textShadow:'0 2px 12px rgba(0,0,0,.6)' });
  return base;
});
</script>

<style>
@keyframes olo-hero-scroll { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
</style>
