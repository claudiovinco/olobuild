<template>
  <div class="olo-relatedposts">
    <div :style="gridStyle">
      <div
        v-for="n in cardCount"
        :key="n"
        class="olo-relatedposts-card"
        :class="hoverClass"
        :style="cardStyle"
      >
        <!-- Image placeholder -->
        <div
          v-if="s.show_image"
          class="olo-relatedposts-img"
          :style="imgStyle"
        >
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
          </svg>
        </div>

        <!-- Card body -->
        <div :style="bodyStyle">
          <!-- Category badge -->
          <span
            v-if="s.show_category"
            style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;opacity:0.7;"
            :style="{ color: s.text_color }"
          >{{ t('WordPress') }}</span>

          <!-- Title -->
          <component
            :is="s.title_tag || 'h4'"
            style="margin:4px 0 0;font-weight:600;line-height:1.3;"
            :style="{ color: s.title_color, fontSize: s.title_tag === 'h3' ? '1.1em' : '1em' }"
          >Titolo Articolo {{ n }}</component>

          <!-- Date -->
          <div
            v-if="s.show_date"
            style="margin-top:6px;font-size:0.85em;"
            :style="{ color: s.date_color }"
          >{{ t('5 Mar 2026') }}</div>

          <!-- Excerpt -->
          <p
            v-if="s.show_excerpt"
            style="margin:8px 0 0;font-size:0.9em;line-height:1.5;"
            :style="{ color: s.text_color }"
          >{{ t('Questo è un estratto di esempio per l\'anteprima dell\'articolo correlato.') }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  source: 'categories',
  count: '3',
  columns: '3',
  show_image: true,
  show_date: true,
  show_excerpt: false,
  show_category: false,
  excerpt_length: '20',
  image_ratio: '16/9',
  gap: '20',
  title_tag: 'h4',
  title_color: 'var(--olo-color-text, #374151)',
  text_color: 'var(--olo-color-text-faint, #9CA3AF)',
  date_color: 'var(--olo-color-text-soft, #6B7280)',
  card_background: 'var(--olo-color-background, #FFFFFF)',
  card_padding: '16',
  card_border_radius: '8',
  hover_effect: 'shadow',
  fallback_text: 'Nessun articolo correlato',
  orderby: 'rand',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const cardCount = computed(() => Math.max(1, Math.min(12, parseInt(s.value.count) || 3)));

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${Math.max(1, Math.min(4, parseInt(s.value.columns) || 3))}, 1fr)`,
  gap: (parseInt(s.value.gap) || 20) + 'px',
}));

const cardStyle = computed(() => ({
  background: s.value.card_background,
  borderRadius: ((v => isNaN(v) ? 8 : v)(parseInt(s.value.card_border_radius))) + 'px',
  overflow: 'hidden',
}));

const bodyStyle = computed(() => ({
  padding: (parseInt(s.value.card_padding) || 16) + 'px',
}));

const imgStyle = computed(() => {
  const st = {
    background: 'var(--olo-color-muted, #F3F4F6)',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    overflow: 'hidden',
  };
  if (s.value.image_ratio !== 'auto') {
    st.aspectRatio = s.value.image_ratio;
  } else {
    st.height = '160px';
  }
  return st;
});

const hoverClass = computed(() => {
  if (s.value.hover_effect === 'shadow') return 'olo-rp-hover-shadow';
  if (s.value.hover_effect === 'scale') return 'olo-rp-hover-scale';
  return '';
});
</script>

<style scoped>
.olo-relatedposts-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.olo-rp-hover-shadow:hover {
  box-shadow: 0 8px 30px rgba(0,0,0,0.3);
}
.olo-rp-hover-scale:hover {
  transform: scale(1.03);
}
</style>
