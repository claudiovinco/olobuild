<template>
  <div class="olo-pricelist-preview" :style="wrapStyle">
    <div
      v-for="(item, i) in items"
      :key="item.id || i"
      :style="itemStyle(item)"
    >
      <!-- Image -->
      <template v-if="showImage">
        <div :style="imgStyle">
          <img v-if="item.image_url" :src="item.image_url" :style="imgInner" />
        </div>
      </template>

      <!-- Content -->
      <div style="flex:1;min-width:0">
        <div>
          <span :style="titleStyle" :data-olo-editable="'items.' + i + '.title'">{{ item.title || 'Piatto' }}</span>
          <!-- Badge -->
          <span v-if="item.badge" :style="badgeStyle" :data-olo-editable="'items.' + i + '.badge'">{{ item.badge }}</span>
        </div>
        <div v-if="item.description" :style="descStyle" :data-olo-editable="'items.' + i + '.description'">{{ item.description }}</div>
        <!-- Price below -->
        <div v-if="s.price_position === 'below'" :style="priceStyleBelow" :data-olo-editable="'items.' + i + '.price'">{{ item.price || '' }}</div>
      </div>

      <!-- Separator dots + Price right -->
      <template v-if="s.price_position !== 'below'">
        <div v-if="sepVisible" :style="separatorStyle"></div>
        <div :style="priceStyle" :data-olo-editable="'items.' + i + '.price'">{{ item.price || '' }}</div>
      </template>
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
  gap: '16',
  padding: '12',
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

const showImage = computed(() => {
  const v = s.value.show_image;
  return v && v !== 'false' && v !== '0';
});

const imgSize = computed(() => (parseInt(s.value.image_size) || 60) + 'px');
const imgRadius = computed(() => ((v => isNaN(v) ? 8 : v)(parseInt(s.value.image_border_radius))) + 'px');
const gap = computed(() => (parseInt(s.value.gap) || 16) + 'px');
const pad = computed(() => (parseInt(s.value.padding) || 12) + 'px');

const sepStyle = computed(() => {
  const v = s.value.separator_style;
  return ['dotted', 'dashed', 'solid', 'none'].includes(v) ? v : 'dotted';
});
const sepVisible = computed(() => sepStyle.value !== 'none');

const wrapStyle = computed(() => ({
  display: 'flex',
  flexDirection: 'column',
  gap: gap.value,
  padding: '8px',
}));

function itemStyle(item) {
  const highlighted = item.highlighted && item.highlighted !== 'false' && item.highlighted !== '0';
  const st = {
    display: 'flex',
    alignItems: 'center',
    gap: '12px',
    padding: pad.value,
    borderRadius: '6px',
    transition: 'background 0.2s',
  };
  if (highlighted) {
    st.background = s.value.highlighted_bg || 'var(--olo-color-muted, #F3F4F6)';
  }
  return st;
}

const imgStyle = computed(() => ({
  width: imgSize.value,
  height: imgSize.value,
  borderRadius: imgRadius.value,
  flexShrink: '0',
  overflow: 'hidden',
}));

const imgInner = computed(() => ({
  width: '100%',
  height: '100%',
  objectFit: 'cover',
  display: 'block',
}));

const titleStyle = computed(() => ({
  color: s.value.title_color || 'var(--olo-color-text, #374151)',
  fontWeight: '600',
  fontSize: '15px',
  display: 'inline',
}));

const descStyle = computed(() => ({
  color: s.value.description_color || 'var(--olo-color-text-muted, #9CA3AF)',
  fontSize: '13px',
  marginTop: '2px',
  lineHeight: '1.4',
}));

const separatorStyle = computed(() => ({
  flex: '1',
  borderBottom: '1px ' + sepStyle.value + ' ' + (s.value.separator_color || 'var(--olo-color-border, #E5E7EB)'),
  alignSelf: 'center',
  minWidth: '20px',
}));

const priceStyle = computed(() => ({
  color: s.value.price_color || 'var(--olo-color-primary, #6366F1)',
  fontWeight: '700',
  fontSize: '16px',
  whiteSpace: 'nowrap',
  flexShrink: '0',
}));

const priceStyleBelow = computed(() => ({
  color: s.value.price_color || 'var(--olo-color-primary, #6366F1)',
  fontWeight: '700',
  fontSize: '15px',
  marginTop: '4px',
}));

const badgeStyle = computed(() => {
  const bw = parseInt(s.value.badge_border_width) || 0;
  const st = {
    display: 'inline-block',
    background: s.value.badge_bg || 'var(--olo-color-primary, #6366F1)',
    color: s.value.badge_color || 'var(--olo-color-primary-contrast, #FFFFFF)',
    fontSize: '9px',
    fontWeight: '700',
    padding: '2px 6px',
    borderRadius: (parseInt(s.value.badge_border_radius) ?? 4) + 'px',
    textTransform: 'uppercase',
    whiteSpace: 'nowrap',
    lineHeight: '1',
    marginLeft: '6px',
    verticalAlign: 'middle',
  };
  if (bw > 0) {
    st.border = bw + 'px ' + (s.value.badge_border_style || 'solid') + ' ' + (s.value.badge_border_color || 'var(--olo-color-primary, #6366F1)');
  }
  return st;
});
</script>
