<template>
  <div class="mb-relative mb-overflow-hidden mb-text-center" :style="wrapStyle">
    <!-- Bg image -->
    <div v-if="s.bg_type === 'image' && s.bg_image" class="mb-absolute mb-inset-0"
      :style="{ backgroundImage: `url(${s.bg_image})`, backgroundSize: 'cover', backgroundPosition: focalPos(s, 'bg_image') }"></div>
    <!-- Bg video badge -->
    <div v-if="s.bg_type === 'video'" class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center mb-bg-gray-900">
      <div class="mb-text-gray-500 mb-text-xs">{{ t('&#9654; Video') }}</div>
    </div>
    <!-- Overlay -->
    <div v-if="showOverlay" class="mb-absolute mb-inset-0" :style="overlayStyle"></div>

    <!-- Sale badge -->
    <div v-if="hasSalePrice" style="position:absolute;top:12px;right:12px;padding:2px 8px;border-radius:4px;font-size:9px;font-weight:700;color:var(--olo-color-on-primary, #ffffff);z-index:10;" :style="{ background: s.sale_badge_color || 'var(--olo-color-error, #b42318)' }">
      <span data-olo-editable="sale_badge_text">{{ s.sale_badge_text || 'OFFERTA' }}</span>
    </div>

    <!-- Content (z-index above overlay) -->
    <div class="mb-relative" style="z-index:2">
      <!-- Badge -->
      <div v-if="showBadge" class="mb-absolute mb-left-1/2 mb--translate-x-1/2 mb-text-xs mb-font-semibold mb-uppercase mb-whitespace-nowrap"
        :style="badgeStyle">
        <span data-olo-editable="badge_text">{{ s.badge_text || 'Popolare' }}</span>
      </div>

      <!-- Plan name -->
      <h3 class="mb-text-xl mb-font-semibold mb-mb-4 mb-mt-2" data-olo-editable="plan_name">{{ s.plan_name || 'Piano' }}</h3>

      <!-- Toggle mensile/annuale -->
      <div v-if="toggleEnabled" style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:16px">
        <span :style="{ fontSize:'13px', opacity: !isYearly ? 1 : 0.5, fontWeight: !isYearly ? '600' : '400', transition:'all .3s', cursor:'pointer' }" @click="isYearly = false" data-olo-editable="toggle_label_1">{{ s.toggle_label_1 || 'Mensile' }}</span>
        <button type="button" @click="isYearly = !isYearly" :style="toggleSwitchStyle">
          <span :style="toggleKnobStyle"></span>
        </button>
        <span :style="{ fontSize:'13px', opacity: isYearly ? 1 : 0.5, fontWeight: isYearly ? '600' : '400', transition:'all .3s', cursor:'pointer' }" @click="isYearly = true" data-olo-editable="toggle_label_2">{{ s.toggle_label_2 || 'Annuale' }}</span>
      </div>

      <!-- Price -->
      <div class="mb-mb-6 mb-flex mb-items-center mb-justify-center" :style="priceWrapColor">
        <div :style="priceShapeStyle">
          <span v-if="hasSalePrice" style="text-decoration:line-through;opacity:0.5;font-size:0.7em;margin-right:8px;">
            {{ formatPrice(s.price || '0', s.currency || '€') }}
          </span>
          <template v-if="!hasSalePrice">
            <span class="mb-opacity-70 mb-mr-0.5" :style="{ fontSize: (parseInt(s.currency_size) || 14) + 'px' }" data-olo-editable="currency">{{ s.currency_position === 'after' ? '' : s.currency }}</span>
            <span class="mb-text-5xl mb-font-bold" data-olo-editable="price">{{ displayPrice }}</span>
            <span v-if="s.currency_position === 'after'" class="mb-opacity-70 mb-ml-0.5" :style="{ fontSize: (parseInt(s.currency_size) || 14) + 'px' }" data-olo-editable="currency">{{ s.currency }}</span>
          </template>
          <template v-else>
            <span class="mb-opacity-70 mb-mr-0.5" :style="{ fontSize: (parseInt(s.currency_size) || 14) + 'px' }" data-olo-editable="currency">{{ s.currency_position === 'after' ? '' : s.currency }}</span>
            <span class="mb-text-5xl mb-font-bold" style="color:inherit;" data-olo-editable="price">{{ s.sale_price }}</span>
            <span v-if="s.currency_position === 'after'" class="mb-opacity-70 mb-ml-0.5" :style="{ fontSize: (parseInt(s.currency_size) || 14) + 'px' }" data-olo-editable="currency">{{ s.currency }}</span>
          </template>
        </div>
        <span class="mb-text-sm mb-opacity-70 mb-ml-1" data-olo-editable="period">{{ s.period || '/mese' }}</span>
      </div>

      <!-- Features -->
      <ul v-if="features.length" style="list-style:none;padding:0;margin:0 0 24px;text-align:left">
        <li v-for="(f, i) in features" :key="i" class="mb-text-sm" :style="featureItemStyle">
          <span v-if="checkIcon" :style="checkIconStyle">{{ checkIcon }}</span><span :data-olo-editable="'features.' + i + '.text'">{{ f }}</span>
        </li>
      </ul>

      <!-- CTA -->
      <div class="mb-py-3 mb-font-semibold mb-cursor-pointer" :style="ctaStyle" data-olo-editable="cta_text">{{ s.cta_text || 'Inizia ora' }}</div>

      <!-- Countdown -->
      <div v-if="countdownEnabled" :style="countdownWrapStyle">
        <div style="margin-bottom:4px" data-olo-editable="countdown_label">{{ s.countdown_label || 'Offerta scade tra:' }}</div>
        <div style="font-weight:700;font-variant-numeric:tabular-nums;font-size:16px">{{ countdownDisplay }}</div>
      </div>

      <!-- Additional info -->
      <div v-if="s.additional_info" style="margin-top:8px;font-size:10px;color:var(--olo-color-text-faint, #94a3b8);text-align:center;" data-olo-editable="additional_info">{{ s.additional_info }}</div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed, ref, onMounted, onUnmounted, watch } from 'vue';
import { focalPos } from '@/utils/focalPoint';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  plan_name: 'Piano Pro', price: '29', currency: '€', currency_size: '14', period: '/mese',
  features: 'Progetti illimitati\n10 GB di spazio\nSupporto prioritario',
  sale_price: '', sale_badge_text: 'OFFERTA', sale_badge_color: '',
  currency_position: 'before',
  is_popular: false, badge_text: 'Popolare', badge_style: 'pill', badge_top: '-12', badge_radius: '20',
  badge_bg_color: '', badge_text_color: 'var(--olo-color-on-primary, #ffffff)',
  price_shape: 'none', price_shape_color: 'var(--olo-color-muted, #F3F4F6)', price_shape_glow: false,
  price_shape_glow_color: 'var(--olo-color-primary, #e1474f)', price_shape_glow_intensity: '15',
  price_shape_border_width: '0', price_shape_border_color: 'var(--olo-color-primary, #e1474f)',
  check_style: 'checkmark', check_size: '14', feature_dividers: true, price_color: '',
  bg_color: 'var(--olo-color-background, #FFFFFF)', accent_color: 'var(--olo-color-primary, #e1474f)', text_color: 'var(--olo-color-text, #374151)',
  bg_type: 'color', bg_image: '', bg_video: '', overlay: false,
  overlay_color: '#000000', overlay_opacity: '50',
  cta_text: 'Inizia ora', cta_url: '#', cta_bg_color: '', cta_text_color: 'var(--olo-color-on-primary, #ffffff)',
  cta_width: '100', cta_radius: '8', cta_border_width: '0', cta_border_color: 'var(--olo-color-on-primary, #ffffff)',
  cta_hover_effect: 'lift', cta_hover_bg_color: '', cta_hover_text_color: '',
  additional_info: '',
  enable_toggle: false, toggle_label_1: 'Mensile', toggle_label_2: 'Annuale', toggle_color: '', price_yearly: '',
  countdown_enabled: false, countdown_date: '', countdown_label: 'Offerta scade tra:', countdown_expired_text: 'Offerta scaduta', countdown_bg_color: '', countdown_text_color: '',
  border_radius: '12', border_width: '0', border_color: 'var(--olo-color-border, #E5E7EB)',
  ...props.settings,
}));

// ── Toggle mensile/annuale ──
const toggleEnabled = computed(() => {
  const v = s.value.enable_toggle;
  return v && v !== 'false' && v !== '0' && v !== '';
});
const isYearly = ref(false);

const displayPrice = computed(() => {
  if (toggleEnabled.value && isYearly.value && s.value.price_yearly) {
    return s.value.price_yearly;
  }
  return s.value.price || '0';
});

const toggleSwitchStyle = computed(() => {
  const color = s.value.toggle_color || s.value.accent_color || 'var(--olo-color-primary, #e1474f)';
  return {
    position: 'relative',
    width: '40px',
    height: '22px',
    borderRadius: '11px',
    background: color,
    border: 'none',
    cursor: 'pointer',
    padding: '0',
    flexShrink: '0',
  };
});

const toggleKnobStyle = computed(() => ({
  position: 'absolute',
  top: '3px',
  left: isYearly.value ? '21px' : '3px',
  width: '16px',
  height: '16px',
  borderRadius: '50%',
  background: '#fff',
  transition: 'left .3s ease',
  display: 'block',
}));

const priceWrapColor = computed(() => {
  const pc = s.value.price_color;
  return pc ? { color: pc } : {};
});

const checkIconStyle = computed(() => ({
  color: s.value.accent_color || 'var(--olo-color-primary, #e1474f)',
  fontSize: (parseInt(s.value.check_size) || 14) + 'px',
  marginRight: '8px',
  opacity: '0.8',
}));

const featureItemStyle = computed(() => {
  const st = { padding: '8px 0' };
  if (showFeatureDividers.value) {
    st.borderBottom = '1px solid var(--olo-color-border, rgba(255,255,255,.1))';
  }
  return st;
});

const features = computed(() => {
  const text = s.value.features || '';
  return text.split('\n').map(l => l.trim()).filter(Boolean);
});

const checkIcons = { checkmark: '✓', 'circle-check': '●', dot: '•', star: '★', arrow: '→', none: '' };
const checkIcon = computed(() => checkIcons[s.value.check_style] || '✓');

function formatPrice(price, currency) {
  if (s.value.currency_position === 'after') return price + currency;
  return currency + price;
}

const hasSalePrice = computed(() => !!s.value.sale_price);

const showFeatureDividers = computed(() => {
  const v = s.value.feature_dividers;
  return v && v !== 'false' && v !== '0' && v !== '';
});

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
    background: s.value.bg_type === 'color' ? (s.value.bg_color || 'var(--olo-color-background, #FFFFFF)') : 'var(--olo-color-muted, #F3F4F6)',
    color: s.value.text_color || 'var(--olo-color-text, #374151)',
    borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
    padding: '32px 24px',
    minHeight: '80px',
  };
  if (bw > 0) st.border = `${bw}px solid ${s.value.border_color || 'var(--olo-color-border, #E5E7EB)'}`;
  return st;
});

const overlayStyle = computed(() => ({
  backgroundColor: s.value.overlay_color || '#000000',
  opacity: (parseInt(s.value.overlay_opacity) || 50) / 100,
  zIndex: 1,
}));

const badgeStyle = computed(() => {
  const accent = s.value.accent_color || 'var(--olo-color-primary, #e1474f)';
  const bg = s.value.badge_bg_color || accent;
  const fg = s.value.badge_text_color || '#FFFFFF';
  const radius = ((v => isNaN(v) ? 20 : v)(parseInt(s.value.badge_radius))) + 'px';
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
  const color = s.value.price_shape_color || 'var(--olo-color-muted, #F3F4F6)';
  const bw = parseInt(s.value.price_shape_border_width) || 0;
  const bc = s.value.price_shape_border_color || 'var(--olo-color-primary, #e1474f)';
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
    const gc = s.value.price_shape_glow_color || 'var(--olo-color-primary, #e1474f)';
    const gi = parseInt(s.value.price_shape_glow_intensity) || 15;
    st.boxShadow = `inset 0 0 ${gi}px color-mix(in srgb, ${gc} 25%, transparent), inset 0 0 ${gi * 2}px color-mix(in srgb, ${gc} 12%, transparent)`;
  }
  return st;
});

// ── Countdown ──
const countdownEnabled = computed(() => {
  const v = s.value.countdown_enabled;
  return v && v !== 'false' && v !== '0' && v !== '';
});

const countdownRemaining = ref({ d: 0, h: 0, m: 0, s: 0 });
let cdInterval = null;

function updateCountdown() {
  const dateStr = s.value.countdown_date;
  if (!dateStr) { countdownRemaining.value = { d: 0, h: 0, m: 0, s: 0 }; return; }
  const target = new Date(dateStr.replace(' ', 'T')).getTime();
  const now = Date.now();
  const diff = Math.max(0, target - now);
  countdownRemaining.value = {
    d: Math.floor(diff / 86400000),
    h: Math.floor((diff % 86400000) / 3600000),
    m: Math.floor((diff % 3600000) / 60000),
    s: Math.floor((diff % 60000) / 1000),
  };
}

function startCountdown() {
  if (cdInterval) clearInterval(cdInterval);
  updateCountdown();
  cdInterval = setInterval(updateCountdown, 1000);
}

onMounted(() => { if (countdownEnabled.value) startCountdown(); });
onUnmounted(() => { if (cdInterval) clearInterval(cdInterval); });
watch(countdownEnabled, (v) => { if (v) startCountdown(); else if (cdInterval) { clearInterval(cdInterval); cdInterval = null; } });
watch(() => s.value.countdown_date, () => { if (countdownEnabled.value) updateCountdown(); });

const countdownDisplay = computed(() => {
  const r = countdownRemaining.value;
  const total = r.d * 86400 + r.h * 3600 + r.m * 60 + r.s;
  if (total <= 0) return s.value.countdown_expired_text || 'Offerta scaduta';
  const pad = (n) => String(n).padStart(2, '0');
  if (r.d > 0) return `${r.d}g ${pad(r.h)}:${pad(r.m)}:${pad(r.s)}`;
  return `${pad(r.h)}:${pad(r.m)}:${pad(r.s)}`;
});

const countdownWrapStyle = computed(() => ({
  textAlign: 'center',
  padding: '10px 12px',
  fontSize: '13px',
  marginTop: '10px',
  borderRadius: '6px',
  background: s.value.countdown_bg_color || 'var(--olo-color-muted, #F3F4F6)',
  color: s.value.countdown_text_color || 'inherit',
}));

const ctaStyle = computed(() => {
  const accent = s.value.accent_color || 'var(--olo-color-primary, #e1474f)';
  const bg = s.value.cta_bg_color || accent;
  const fg = s.value.cta_text_color || '#FFFFFF';
  const radius = ((v => isNaN(v) ? 8 : v)(parseInt(s.value.cta_radius))) + 'px';
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
