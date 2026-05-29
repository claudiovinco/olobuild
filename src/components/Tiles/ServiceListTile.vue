<template>
  <div class="mb-font-sans">
    <div :style="gridStyle">
      <div v-for="(svc, i) in sampleServices" :key="i" :style="cardStyle">
        <!-- Color bar -->
        <div v-if="s.color_bar" :style="{ height: s.color_bar_height+'px', background: svc.color, borderRadius: s.card_border_radius+'px '+s.card_border_radius+'px 0 0' }"></div>
        <!-- Image -->
        <div v-if="s.show_image" :style="imageStyle(svc)">
          <div class="olo-svl-ph" :style="{ position:'absolute', inset:'0', display:'flex', alignItems:'center', justifyContent:'center', color: TOKENS.textFaint }" v-html="imageSvg"></div>
        </div>
        <!-- Content -->
        <div :style="{ padding: s.card_padding+'px' }">
          <div :style="{ fontSize: s.title_size+'px', fontWeight: s.title_weight, color: s.title_color || TOKENS.text, marginBottom: '6px' }">
            {{ svc.title }}
          </div>
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
            <span v-if="s.show_duration" style="display:inline-flex;align-items:center;gap:4px" :style="{ fontSize:'13px', color: durationColor }"><span class="olo-svl-clock" v-html="clockSvg"></span>{{ svc.duration }} min</span>
            <span v-if="s.show_price" :style="{ fontSize: s.price_size+'px', fontWeight:'600', color: priceColor }">&euro; {{ svc.price }}</span>
          </div>
          <p v-if="s.show_excerpt" :style="{ fontSize: s.excerpt_size+'px', color: excerptColor, margin:'0 0 14px', lineHeight:'1.5' }">
            {{ svc.excerpt }}
          </p>
          <button v-if="s.show_booking_btn" type="button" class="olo-svl-btn" :style="btnStyle">
            {{ s.booking_btn_text }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { getShadowValue } from '@/composables/useShadowMap';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const imageSvg = iconsSvg['image'] || '';
const clockSvg = iconsSvg['clock'] || '';

const defaults = {
  columns: 3, gap: 24, style: 'cards', show_image: true, show_price: true, show_duration: true,
  show_excerpt: true, show_booking_btn: true, booking_btn_text: 'Prenota', link_to: 'service_page',
  card_bg: '#FFFFFF', card_border_radius: 12, card_border_color: '', card_shadow: 'sm',
  card_padding: 24, card_hover_shadow: 'md', title_size: 18, title_weight: '600', title_color: '',
  price_color: 'var(--olo-color-primary, #e1474f)', price_size: 16, duration_color: '', excerpt_color: '',
  excerpt_size: 14, btn_bg: 'var(--olo-color-primary, #e1474f)', btn_color: '', btn_radius: 8, btn_full_width: false,
  image_height: 200, image_radius: 8, color_bar: true, color_bar_height: 4,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

// Palette demo per-servizio: ruoli brand/semantici globali (no più hex off-brand)
const sampleServices = [
  { title: 'Consulenza Fiscale', duration: 60, price: '80,00', color: 'var(--olo-color-primary, #e1474f)', excerpt: 'Consulenza personalizzata su tasse e contabilità.' },
  { title: 'Massaggio Rilassante', duration: 90, price: '120,00', color: 'var(--olo-color-accent, #f4a23b)', excerpt: 'Trattamento completo per il massimo relax.' },
  { title: 'Campo da Tennis', duration: 60, price: '25,00', color: 'var(--olo-color-success, #15803d)', excerpt: 'Prenotazione campo coperto.' },
];

const durationColor = computed(() => resolveColor(s.value.duration_color, TOKENS.textSoft));
const excerptColor = computed(() => resolveColor(s.value.excerpt_color, TOKENS.textSoft));
const priceColor = computed(() => resolveColor(s.value.price_color, TOKENS.primary));
const cardBorderColor = computed(() => resolveColor(s.value.card_border_color, TOKENS.border));

const gridStyle = computed(() => {
  if (s.value.style === 'list') return { display: 'flex', flexDirection: 'column', gap: s.value.gap + 'px' };
  return { display: 'grid', gridTemplateColumns: `repeat(${s.value.columns}, 1fr)`, gap: s.value.gap + 'px' };
});

const cardStyle = computed(() => ({
  background: s.value.card_bg,
  borderRadius: s.value.card_border_radius + 'px',
  border: '1px solid ' + cardBorderColor.value,
  boxShadow: getShadowValue(s.value, 'card_shadow'),
  overflow: 'hidden',
  transition: 'box-shadow 0.2s ease',
}));

function imageStyle(svc) {
  return {
    height: s.value.image_height + 'px',
    background: `color-mix(in srgb, ${svc.color} 12%, #fff)`,
    borderRadius: s.value.color_bar ? '0' : s.value.image_radius + 'px ' + s.value.image_radius + 'px 0 0',
    position: 'relative',
    overflow: 'hidden',
  };
}

const btnStyle = computed(() => ({
  padding: '10px 20px',
  background: s.value.btn_bg,
  color: resolveColor(s.value.btn_color, TOKENS.onPrimary),
  borderRadius: s.value.btn_radius + 'px',
  border: 'none', fontFamily: 'inherit',
  textAlign: 'center',
  fontWeight: '600',
  fontSize: '14px',
  cursor: 'pointer',
  width: s.value.btn_full_width ? '100%' : 'auto',
  display: s.value.btn_full_width ? 'block' : 'inline-block',
}));
</script>

<style scoped>
.olo-svl-ph :deep(svg) { width: 30px; height: 30px; stroke: currentColor; fill: none; opacity: 0.55; }
.olo-svl-clock { display: inline-flex; }
.olo-svl-clock :deep(svg) { width: 13px; height: 13px; stroke: currentColor; fill: none; }
.olo-svl-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
