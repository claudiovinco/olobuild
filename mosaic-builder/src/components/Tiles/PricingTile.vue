<template>
  <div class="mb-relative mb-overflow-hidden mb-text-center" :style="wrapStyle">
    <!-- Bg image -->
    <div v-if="s.bg_type === 'image' && s.bg_image" class="mb-absolute mb-inset-0"
      :style="{ backgroundImage: `url(${s.bg_image})`, backgroundSize: 'cover', backgroundPosition: 'center' }"></div>
    <!-- Bg video badge -->
    <div v-if="s.bg_type === 'video'" class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center mb-bg-gray-900">
      <div class="mb-text-gray-500 mb-text-xs">&#9654; Video</div>
    </div>
    <!-- Overlay -->
    <div v-if="showOverlay" class="mb-absolute mb-inset-0" :style="overlayStyle"></div>

    <!-- Content (z-index above overlay) -->
    <div class="mb-relative" style="z-index:2">
      <!-- Badge -->
      <div v-if="showBadge" class="mb-absolute mb-left-1/2 mb--translate-x-1/2 mb-text-xs mb-font-semibold mb-uppercase mb-whitespace-nowrap"
        :style="badgeStyle">
        {{ s.badge_text || 'Popolare' }}
      </div>

      <!-- Plan name -->
      <h3 class="mb-text-xl mb-font-semibold mb-mb-4 mb-mt-2" v-html="s.plan_name || 'Piano'"></h3>

      <!-- Price -->
      <div class="mb-mb-6 mb-flex mb-items-center mb-justify-center">
        <div :style="priceShapeStyle">
          <span class="mb-opacity-70 mb-mr-0.5" :style="{ fontSize: (parseInt(s.currency_size) || 14) + 'px' }">{{ s.currency }}</span>
          <span class="mb-text-5xl mb-font-bold">{{ s.price || '0' }}</span>
        </div>
        <span class="mb-text-sm mb-opacity-70 mb-ml-1" v-html="s.period || '/mese'"></span>
      </div>

      <!-- Features -->
      <ul v-if="features.length" class="mb-text-left mb-mb-6 mb-space-y-2 mb-px-2">
        <li v-for="(f, i) in features" :key="i" class="mb-py-2 mb-border-b mb-border-white/10 mb-text-sm">
          <span v-if="checkIcon" class="mb-mr-2 mb-opacity-80" :style="{ color: s.accent_color || '#6366F1' }">{{ checkIcon }}</span>{{ f }}
        </li>
      </ul>

      <!-- CTA -->
      <div class="mb-py-3 mb-font-semibold mb-cursor-pointer" :style="ctaStyle"
        v-html="s.cta_text || 'Inizia ora'"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  plan_name: 'Piano Pro', price: '29', currency: '€', currency_size: '14', period: '/mese',
  features: 'Progetti illimitati\n10 GB di spazio\nSupporto prioritario',
  is_popular: false, badge_text: 'Popolare', badge_style: 'pill', badge_top: '-12', badge_radius: '20',
  badge_bg_color: '', badge_text_color: '#FFFFFF',
  price_shape: 'none', price_shape_color: '#374151', price_shape_glow: false,
  price_shape_glow_color: '#6366F1', price_shape_glow_intensity: '15',
  price_shape_border_width: '0', price_shape_border_color: '#6366F1',
  check_style: 'checkmark',
  bg_color: '#1F2937', accent_color: '#6366F1', text_color: '#F3F4F6',
  bg_type: 'color', bg_image: '', bg_video: '', overlay: false,
  overlay_color: '#000000', overlay_opacity: '50',
  cta_text: 'Inizia ora', cta_url: '#', cta_bg_color: '', cta_text_color: '#FFFFFF',
  cta_width: '100', cta_radius: '8', cta_border_width: '0', cta_border_color: '#FFFFFF',
  cta_hover_effect: 'lift', cta_hover_bg_color: '', cta_hover_text_color: '',
  border_radius: '12', border_width: '0', border_color: '#374151',
  ...props.settings,
}));

const features = computed(() => {
  const text = s.value.features || '';
  return text.split('\n').map(l => l.trim()).filter(Boolean);
});

const checkIcons = { checkmark: '✓', 'circle-check': '●', dot: '•', star: '★', arrow: '→', none: '' };
const checkIcon = computed(() => checkIcons[s.value.check_style] || '✓');

const showBadge = computed(() => {
  const v = s.value.is_popular;
  return v && v !== 'false' && v !== '0' && v !== '';
});

const showOverlay = computed(() => {
  if (s.value.bg_type === 'color') return false;
  const v = s.value.overlay;
  return v && v !== 'false' && v !== '0' && v !== '';
});

const wrapStyle = computed(() => {
  const bw = parseInt(s.value.border_width) || 0;
  const st = {
    background: s.value.bg_type === 'color' ? (s.value.bg_color || '#1F2937') : '#111827',
    color: s.value.text_color || '#F3F4F6',
    borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
    padding: '32px 24px',
    minHeight: '80px',
  };
  if (bw > 0) st.border = `${bw}px solid ${s.value.border_color || '#374151'}`;
  return st;
});

const overlayStyle = computed(() => ({
  backgroundColor: s.value.overlay_color || '#000000',
  opacity: (parseInt(s.value.overlay_opacity) || 50) / 100,
  zIndex: 1,
}));

const badgeStyle = computed(() => {
  const accent = s.value.accent_color || '#6366F1';
  const bg = s.value.badge_bg_color || accent;
  const fg = s.value.badge_text_color || '#FFFFFF';
  const radius = (parseInt(s.value.badge_radius) || 20) + 'px';
  const style = s.value.badge_style || 'pill';
  const topPx = (parseInt(s.value.badge_top) ?? -12) + 'px';
  const base = { top: topPx, color: fg, borderRadius: radius, zIndex: 5 };
  if (style === 'minimal') {
    base.background = 'transparent';
    base.color = accent;
    base.borderBottom = `2px solid ${accent}`;
    base.padding = '2px 12px';
  } else if (style === 'classic') {
    base.background = bg;
    base.padding = '4px 16px';
    base.borderRadius = radius;
  } else {
    base.background = bg;
    base.padding = '4px 20px';
  }
  return base;
});

const priceShapeStyle = computed(() => {
  const shape = s.value.price_shape;
  if (shape === 'none') return { display: 'inline-flex', alignItems: 'baseline' };
  const color = s.value.price_shape_color || '#374151';
  const bw = parseInt(s.value.price_shape_border_width) || 0;
  const bc = s.value.price_shape_border_color || '#6366F1';
  const st = {
    display: 'inline-flex', alignItems: 'baseline', justifyContent: 'center',
    background: color, padding: '16px 20px',
  };
  if (shape === 'circle') {
    st.borderRadius = '50%';
    st.width = '120px'; st.height = '120px';
    st.alignItems = 'center';
  } else {
    st.borderRadius = '16px';
  }
  if (bw > 0) st.border = `${bw}px solid ${bc}`;
  // Glow
  const glow = s.value.price_shape_glow;
  if (glow && glow !== 'false' && glow !== '0' && glow !== '') {
    const gc = s.value.price_shape_glow_color || '#6366F1';
    const gi = parseInt(s.value.price_shape_glow_intensity) || 15;
    st.boxShadow = `inset 0 0 ${gi}px ${gc}40, inset 0 0 ${gi * 2}px ${gc}20`;
  }
  return st;
});

const ctaStyle = computed(() => {
  const accent = s.value.accent_color || '#6366F1';
  const bg = s.value.cta_bg_color || accent;
  const fg = s.value.cta_text_color || '#FFFFFF';
  const radius = (parseInt(s.value.cta_radius) || 8) + 'px';
  const bw = parseInt(s.value.cta_border_width) || 0;
  const w = parseInt(s.value.cta_width) || 100;
  const st = {
    background: bg, color: fg, borderRadius: radius, transition: 'all .3s',
    textAlign: 'center', display: 'block', width: w + '%', margin: '0 auto',
  };
  if (bw > 0) st.border = `${bw}px solid ${s.value.cta_border_color || '#FFFFFF'}`;
  else st.border = 'none';
  return st;
});
</script>
