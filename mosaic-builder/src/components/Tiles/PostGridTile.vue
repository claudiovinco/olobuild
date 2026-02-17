<template>
  <div class="olo-postgrid-preview">
    <div class="mpg-grid" :style="gridStyle">
      <div v-for="n in cardCount" :key="n" class="mpg-card" :class="cardClasses">
        <!-- Image area -->
        <div v-if="settings.show_image !== false" class="mpg-img-wrap" :style="{ height: imgHeight + 'px' }">
          <div :class="['mpg-img-bg', hoverImgClass]"></div>
          <!-- Category badge -->
          <span v-if="settings.show_category !== false" class="mpg-category">Categoria</span>
          <!-- Ribbon -->
          <span
            v-if="settings.ribbon_field"
            class="mpg-ribbon"
            :class="'mpg-ribbon--' + (settings.ribbon_position || 'top-right')"
            :style="{ background: settings.ribbon_bg || '#e11d48', color: settings.ribbon_color || '#fff' }"
          >Ribbon</span>
        </div>
        <!-- Card body -->
        <div class="mpg-body">
          <div class="mpg-title">Titolo articolo {{ n }}</div>
          <div v-if="settings.show_meta !== false" class="mpg-meta">12 Feb 2026 · Autore</div>
          <div v-if="settings.show_excerpt !== false" class="mpg-excerpt">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
          <div v-if="settings.show_price" class="mpg-price">{{ settings.price_prefix || '€' }}99{{ settings.price_suffix || '' }}</div>
          <div v-if="settings.link_style === 'button'" class="mpg-btn">{{ settings.link_text || 'Vedi' }}</div>
          <div v-else-if="settings.link_style === 'text'" class="mpg-link">{{ settings.link_text || 'Vedi' }} →</div>
        </div>
      </div>
    </div>
    <!-- Info bar -->
    <div class="mpg-info">
      {{ settings.post_type || 'post' }} · {{ settings.posts_per_page || 12 }} articoli · {{ cols }} colonne
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const cols = computed(() => parseInt(props.settings.columns) || 3);
const cardCount = computed(() => Math.min(parseInt(props.settings.posts_per_page) || 6, cols.value * 2));

const imgHeight = computed(() => {
  const h = parseInt(props.settings.image_height) || 200;
  const c = cols.value;
  if (c >= 4) return Math.min(h, 100);
  if (c >= 3) return Math.min(h, 130);
  return Math.min(h, 180);
});

const gapMap = { collapse: '0px', small: '8px', default: '12px', medium: '16px', large: '24px' };
const gap = computed(() => gapMap[props.settings.gap || 'medium'] || '16px');

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gap: gap.value,
}));

const cardClasses = computed(() => {
  const cs = props.settings.card_style || 'default';
  return ['mpg-card--' + cs];
});

const hoverImgClass = computed(() => {
  const fx = props.settings.hover_effect || 'none';
  return fx !== 'none' ? 'mpg-hover-' + fx : '';
});
</script>

<style scoped>
.olo-postgrid-preview {
  min-height: 120px;
}
.mpg-grid {
  display: grid;
}
.mpg-card {
  background: #fff;
  border-radius: 4px;
  overflow: hidden;
  border: 1px solid #e5e7eb;
}
.mpg-card--hover {
  transition: box-shadow 0.3s ease;
}
.mpg-card--hover:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.mpg-card--primary {
  background: var(--olo-color-primary, #6366F1);
  border-color: var(--olo-color-primary, #6366F1);
}
.mpg-card--primary .mpg-title,
.mpg-card--primary .mpg-meta,
.mpg-card--primary .mpg-excerpt {
  color: #e0e7ff;
}
.mpg-card--primary .mpg-title {
  color: #fff;
}

/* Image area */
.mpg-img-wrap {
  position: relative;
  overflow: hidden;
}
.mpg-img-bg {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
  transition: transform 0.5s ease, filter 0.5s ease;
}

/* Hover effects */
.mpg-card:hover .mpg-hover-zoom { transform: scale(1.08); }
.mpg-card:hover .mpg-hover-zoom-rotate { transform: scale(1.08) rotate(2deg); }
.mpg-hover-brightness { filter: brightness(0.7); }
.mpg-card:hover .mpg-hover-brightness { filter: brightness(1); }
.mpg-hover-desaturate { filter: grayscale(100%); }
.mpg-card:hover .mpg-hover-desaturate { filter: grayscale(0%); }
.mpg-hover-blur-in { filter: blur(3px); }
.mpg-card:hover .mpg-hover-blur-in { filter: blur(0); }

/* Category badge */
.mpg-category {
  position: absolute;
  bottom: 8px;
  left: 8px;
  background: rgba(0,0,0,0.7);
  color: #fff;
  font-size: 9px;
  padding: 2px 6px;
  border-radius: 3px;
}

/* Ribbon */
.mpg-ribbon {
  position: absolute;
  font-size: 9px;
  font-weight: 700;
  padding: 3px 10px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  z-index: 2;
}
.mpg-ribbon--top-right {
  top: 0;
  right: 12px;
  border-radius: 0 0 4px 4px;
}
.mpg-ribbon--top-left {
  top: 0;
  left: 12px;
  border-radius: 0 0 4px 4px;
}

/* Card body */
.mpg-body {
  padding: 8px 10px;
}
.mpg-title {
  font-size: 11px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 2px;
}
.mpg-meta {
  font-size: 9px;
  color: #9ca3af;
  margin-bottom: 4px;
}
.mpg-excerpt {
  font-size: 9px;
  color: #6b7280;
  line-height: 1.3;
  margin-bottom: 4px;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
.mpg-price {
  font-size: 11px;
  font-weight: 700;
  color: #059669;
  margin-bottom: 4px;
}
.mpg-btn {
  display: inline-block;
  font-size: 9px;
  padding: 3px 8px;
  background: var(--olo-color-primary, #6366F1);
  color: #fff;
  border-radius: 3px;
}
.mpg-link {
  font-size: 9px;
  color: var(--olo-color-primary, #6366F1);
}

/* Info bar */
.mpg-info {
  margin-top: 8px;
  text-align: center;
  font-size: 9px;
  color: #9ca3af;
  padding: 4px;
  background: rgba(0,0,0,0.05);
  border-radius: 3px;
}
</style>
