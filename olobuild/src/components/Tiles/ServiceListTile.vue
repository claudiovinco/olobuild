<template>
  <div class="mb-font-sans">
    <div :style="gridStyle">
      <div v-for="(svc, i) in sampleServices" :key="i" :style="cardStyle">
        <!-- Color bar -->
        <div v-if="s.color_bar" :style="{ height: s.color_bar_height+'px', background: svc.color, borderRadius: s.card_border_radius+'px '+s.card_border_radius+'px 0 0' }"></div>
        <!-- Image -->
        <div v-if="s.show_image" :style="imageStyle(svc)">
          <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:28px">&#128247;</div>
        </div>
        <!-- Content -->
        <div :style="{ padding: s.card_padding+'px' }">
          <div :style="{ fontSize: s.title_size+'px', fontWeight: s.title_weight, color: s.title_color || '#1f2937', marginBottom: '6px' }">
            {{ svc.title }}
          </div>
          <div style="display:flex;gap:10px;margin-bottom:8px">
            <span v-if="s.show_duration" :style="{ fontSize:'13px', color: s.duration_color }">&#9201; {{ svc.duration }} min</span>
            <span v-if="s.show_price" :style="{ fontSize: s.price_size+'px', fontWeight:'600', color: s.price_color }">&euro; {{ svc.price }}</span>
          </div>
          <p v-if="s.show_excerpt" :style="{ fontSize: s.excerpt_size+'px', color: s.excerpt_color, margin:'0 0 14px', lineHeight:'1.5' }">
            {{ svc.excerpt }}
          </p>
          <div v-if="s.show_booking_btn" :style="btnStyle">
            {{ s.booking_btn_text }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  columns: 3, gap: 24, style: 'cards', show_image: true, show_price: true, show_duration: true,
  show_excerpt: true, show_booking_btn: true, booking_btn_text: 'Prenota', link_to: 'service_page',
  card_bg: '#FFFFFF', card_border_radius: 12, card_border_color: '#E5E7EB', card_shadow: 'sm',
  card_padding: 24, card_hover_shadow: 'md', title_size: 18, title_weight: '600', title_color: '',
  price_color: '#6366F1', price_size: 16, duration_color: '#6B7280', excerpt_color: '#6B7280',
  excerpt_size: 14, btn_bg: '#6366F1', btn_color: '#FFFFFF', btn_radius: 8, btn_full_width: false,
  image_height: 200, image_radius: 8, color_bar: true, color_bar_height: 4,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const shadowMap = { none: 'none', sm: '0 1px 3px rgba(0,0,0,0.1)', md: '0 4px 12px rgba(0,0,0,0.1)', lg: '0 8px 24px rgba(0,0,0,0.15)' };

const sampleServices = [
  { title: 'Consulenza Fiscale', duration: 60, price: '80,00', color: '#6366F1', excerpt: 'Consulenza personalizzata su tasse e contabilità.' },
  { title: 'Massaggio Rilassante', duration: 90, price: '120,00', color: '#EC4899', excerpt: 'Trattamento completo per il massimo relax.' },
  { title: 'Campo da Tennis', duration: 60, price: '25,00', color: '#10B981', excerpt: 'Prenotazione campo coperto.' },
];

const gridStyle = computed(() => {
  if (s.value.style === 'list') return { display: 'flex', flexDirection: 'column', gap: s.value.gap + 'px' };
  return { display: 'grid', gridTemplateColumns: `repeat(${s.value.columns}, 1fr)`, gap: s.value.gap + 'px' };
});

const cardStyle = computed(() => ({
  background: s.value.card_bg,
  borderRadius: s.value.card_border_radius + 'px',
  border: '1px solid ' + s.value.card_border_color,
  boxShadow: shadowMap[s.value.card_shadow] || 'none',
  overflow: 'hidden',
  transition: 'box-shadow 0.2s ease',
}));

function imageStyle(svc) {
  return {
    height: s.value.image_height + 'px',
    background: svc.color + '15',
    borderRadius: s.value.color_bar ? '0' : s.value.image_radius + 'px ' + s.value.image_radius + 'px 0 0',
    position: 'relative',
    overflow: 'hidden',
  };
}

const btnStyle = computed(() => ({
  padding: '10px 20px',
  background: s.value.btn_bg,
  color: s.value.btn_color,
  borderRadius: s.value.btn_radius + 'px',
  textAlign: 'center',
  fontWeight: '600',
  fontSize: '14px',
  cursor: 'pointer',
  width: s.value.btn_full_width ? '100%' : 'auto',
  display: s.value.btn_full_width ? 'block' : 'inline-block',
}));
</script>
