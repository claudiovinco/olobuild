<template>
  <div :style="gridStyle">
    <div
      v-for="i in cols"
      :key="i"
      :style="cardStyle"
    >
      <!-- Image -->
      <div v-if="s.show_image" :style="imgStyle">
        <svg :style="{ width: '28px', height: '28px', opacity: 0.3 }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="3" y="3" width="18" height="18" rx="2" />
          <circle cx="8.5" cy="8.5" r="1.5" />
          <path d="M21 15l-5-5L5 21" />
        </svg>
        <!-- Overlay -->
        <div v-if="s.overlay" :style="overlayStyle">
          <div :style="titleStyle">Categoria {{ i }}</div>
          <div v-if="s.show_count" :style="countStyle">{{ 5 + i * 3 }} prodotti</div>
        </div>
      </div>
      <!-- No image, text below -->
      <div v-else style="padding:16px;">
        <div :style="titleStyle">Categoria {{ i }}</div>
        <div v-if="s.show_count" :style="countStyle">{{ 5 + i * 3 }} prodotti</div>
      </div>
      <!-- Description -->
      <div v-if="s.show_description" style="padding:8px 12px;font-size:12px;color:var(--olo-color-text-soft, #6b7280);">
        {{ t('Descrizione breve della categoria...') }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  columns: '4',
  show_image: true,
  show_count: true,
  show_description: false,
  overlay: true,
  overlay_color: 'rgba(0,0,0,0.4)',
  text_color: '#FFFFFF',
  gap: '24',
  image_ratio: '1-1',
  border_radius: '8',
  hover_effect: 'zoom',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const cols = computed(() => Math.max(1, Math.min(6, parseInt(s.value.columns) || 4)));

const ratioMap = {
  '1-1': '100%',
  '4-3': '75%',
  '3-4': '133%',
  '16-9': '56.25%',
};

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gap: (parseInt(s.value.gap) || 24) + 'px',
}));

const cardStyle = computed(() => ({
  borderRadius: ((v => isNaN(v) ? 8 : v)(parseInt(s.value.border_radius))) + 'px',
  overflow: 'hidden',
  background: 'var(--olo-color-surface-alt, #f6f7f9)',
}));

const imgStyle = computed(() => ({
  position: 'relative',
  width: '100%',
  paddingTop: ratioMap[s.value.image_ratio] || '100%',
  background: 'var(--olo-color-surface-alt, #f6f7f9)',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
}));

const overlayStyle = computed(() => ({
  position: 'absolute',
  inset: '0',
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  justifyContent: 'center',
  background: s.value.overlay_color || 'rgba(0,0,0,0.4)',
  transition: 'background 0.3s ease',
}));

const titleStyle = computed(() => ({
  color: s.value.text_color || '#fff',
  fontWeight: 700,
  fontSize: '16px',
}));

const countStyle = computed(() => ({
  color: s.value.text_color || '#fff',
  fontSize: '13px',
  opacity: 0.85,
  marginTop: '4px',
}));
</script>
