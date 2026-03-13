<template>
  <div class="olo-pricelist-preview" :style="wrapStyle">
    <div
      v-for="(item, i) in items"
      :key="item.id || i"
      class="olo-pl-card"
      :class="{ 'olo-pl-card--hl': isHighlighted(item) }"
      :style="cardStyle(item)"
    >
      <!-- Image -->
      <div v-if="showImage" class="olo-pl-img" :style="imgStyle">
        <img v-if="item.image_url" :src="item.image_url" :style="imgInner" />
        <div v-else class="olo-pl-img-ph">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.3">
            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>
          </svg>
        </div>
      </div>

      <!-- Content -->
      <div class="olo-pl-body">
        <div class="olo-pl-top">
          <div class="olo-pl-info">
            <span class="olo-pl-title" :style="titleStyleObj" :data-olo-editable="'items.' + i + '.title'">{{ item.title || 'Piatto' }}</span>
            <span v-if="item.badge" class="olo-pl-badge" :style="badgeStyleObj" :data-olo-editable="'items.' + i + '.badge'">{{ item.badge }}</span>
          </div>
          <!-- Price right -->
          <div v-if="s.price_position !== 'below'" class="olo-pl-price" :style="priceStyleObj" :data-olo-editable="'items.' + i + '.price'">{{ item.price || '' }}</div>
        </div>
        <div v-if="item.description" class="olo-pl-desc" :style="descStyleObj" :data-olo-editable="'items.' + i + '.description'">{{ item.description }}</div>
        <!-- Price below -->
        <div v-if="s.price_position === 'below'" class="olo-pl-price olo-pl-price--below" :style="priceStyleObj" :data-olo-editable="'items.' + i + '.price'">{{ item.price || '' }}</div>
      </div>

      <!-- Separator line (between items, only if right layout) -->
      <div v-if="s.price_position !== 'below' && sepVisible" class="olo-pl-sep" :style="separatorStyleObj"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const defaults = {
  items: [
    { id: 'pl-1', title: 'Bruschetta', description: 'Pomodoro fresco, basilico e olio EVO', price: '€8', image_url: '', highlighted: false, badge: '' },
    { id: 'pl-2', title: 'Risotto ai funghi porcini', description: 'Riso Carnaroli mantecato con porcini freschi', price: '€14', image_url: '', highlighted: false, badge: '' },
    { id: 'pl-3', title: 'Tiramisù', description: 'Mascarpone, savoiardi e caffè espresso', price: '€7', image_url: '', highlighted: false, badge: 'Consigliato' },
  ],
  separator_style: 'dotted',
  separator_color: '',
  title_color: '',
  price_color: '',
  description_color: '',
  image_size: '60',
  image_border_radius: '8',
  show_image: true,
  price_position: 'right',
  highlighted_bg: '',
  badge_bg: '',
  badge_color: '',
  gap: '12',
  padding: '14',
  card_bg: '',
  card_border_radius: '12',
  card_border_color: '',
  hover_lift: true,
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));

const items = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw)) return raw;
  return [];
});

function isHighlighted(item) {
  return item.highlighted && item.highlighted !== 'false' && item.highlighted !== '0';
}

const showImage = computed(() => {
  const v = s.value.show_image;
  return v && v !== 'false' && v !== '0';
});

const imgSize = computed(() => (parseInt(s.value.image_size) || 60) + 'px');
const imgRadius = computed(() => ((v => isNaN(v) ? 8 : v)(parseInt(s.value.image_border_radius))) + 'px');
const gap = computed(() => (parseInt(s.value.gap) || 12) + 'px');
const pad = computed(() => (parseInt(s.value.padding) || 14) + 'px');
const cardRadius = computed(() => (parseInt(s.value.card_border_radius) || 12) + 'px');

const sepStyle = computed(() => {
  const v = s.value.separator_style;
  return ['dotted', 'dashed', 'solid', 'none'].includes(v) ? v : 'dotted';
});
const sepVisible = computed(() => sepStyle.value !== 'none');

const wrapStyle = computed(() => ({
  display: 'flex',
  flexDirection: 'column',
  gap: gap.value,
}));

function cardStyle(item) {
  const hl = isHighlighted(item);
  return {
    display: 'flex',
    alignItems: 'center',
    gap: '14px',
    padding: pad.value,
    borderRadius: cardRadius.value,
    background: hl
      ? (s.value.highlighted_bg || 'rgba(232, 98, 42, 0.06)')
      : (s.value.card_bg || 'rgba(255, 255, 255, 0.8)'),
    border: '1px solid ' + (s.value.card_border_color || (hl ? 'rgba(232, 98, 42, 0.2)' : 'rgba(0, 0, 0, 0.06)')),
    transition: 'transform 0.2s, box-shadow 0.2s, border-color 0.2s',
    position: 'relative',
    overflow: 'hidden',
  };
}

const imgStyle = computed(() => ({
  width: imgSize.value,
  height: imgSize.value,
  borderRadius: imgRadius.value,
  flexShrink: '0',
  overflow: 'hidden',
  background: 'rgba(0, 0, 0, 0.03)',
}));

const imgInner = computed(() => ({
  width: '100%',
  height: '100%',
  objectFit: 'cover',
  display: 'block',
}));

const titleStyleObj = computed(() => ({
  color: s.value.title_color || 'var(--olo-color-text, #1a1a1a)',
  fontWeight: '600',
  fontSize: '15px',
  display: 'inline',
  letterSpacing: '-0.01em',
}));

const descStyleObj = computed(() => ({
  color: s.value.description_color || 'var(--olo-color-text-muted, #888)',
  fontSize: '13px',
  marginTop: '4px',
  lineHeight: '1.5',
}));

const priceStyleObj = computed(() => ({
  color: s.value.price_color || 'var(--olo-color-primary, #e8622a)',
  fontWeight: '700',
  fontSize: '17px',
  whiteSpace: 'nowrap',
  flexShrink: '0',
  letterSpacing: '-0.02em',
}));

const separatorStyleObj = computed(() => ({
  position: 'absolute',
  bottom: '0',
  left: pad.value,
  right: pad.value,
  height: '0',
  borderBottom: '1px ' + sepStyle.value + ' ' + (s.value.separator_color || 'rgba(0, 0, 0, 0.06)'),
}));

const badgeStyleObj = computed(() => {
  const bw = parseInt(s.value.badge_border_width) || 0;
  const st = {
    display: 'inline-flex',
    alignItems: 'center',
    background: s.value.badge_bg || 'var(--olo-color-primary, #e8622a)',
    color: s.value.badge_color || '#fff',
    fontSize: '9px',
    fontWeight: '700',
    padding: '3px 7px',
    borderRadius: (parseInt(s.value.badge_border_radius) ?? 6) + 'px',
    textTransform: 'uppercase',
    whiteSpace: 'nowrap',
    lineHeight: '1',
    marginLeft: '8px',
    verticalAlign: 'middle',
    letterSpacing: '0.04em',
  };
  if (bw > 0) {
    st.border = bw + 'px ' + (s.value.badge_border_style || 'solid') + ' ' + (s.value.badge_border_color || 'var(--olo-color-primary, #e8622a)');
  }
  return st;
});
</script>

<style scoped>
.olo-pricelist-preview {
  font-family: inherit;
}
.olo-pl-card {
  cursor: default;
}
.olo-pl-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
  border-color: rgba(0, 0, 0, 0.12) !important;
}
.olo-pl-card--hl:hover {
  border-color: rgba(232, 98, 42, 0.35) !important;
}
.olo-pl-img-ph {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.02);
}
.olo-pl-body {
  flex: 1;
  min-width: 0;
}
.olo-pl-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
}
.olo-pl-info {
  flex: 1;
  min-width: 0;
}
.olo-pl-desc {
  opacity: 0.9;
}
.olo-pl-price--below {
  margin-top: 6px;
  font-size: 15px !important;
}
.olo-pl-sep {
  pointer-events: none;
}
</style>
