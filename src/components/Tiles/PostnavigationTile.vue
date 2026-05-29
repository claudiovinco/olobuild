<template>
  <div class="olo-postnav-preview" :style="wrapStyle">
    <!-- Prev card -->
    <div :style="cardStyle" class="olo-postnav-card">
      <div v-if="s.show_thumbnail" :style="thumbStyle" class="olo-postnav-thumb">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <rect x="2" y="2" width="20" height="20" rx="3" fill="#E5E7EB"/>
          <path d="M8 16l4-4 3 3 5-5" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="8" cy="8" r="2" fill="#6B7280"/>
        </svg>
      </div>
      <div style="flex:1;min-width:0">
        <div v-if="showLabel" :style="{ color: s.text_color || 'var(--olo-color-text, #374151)', fontSize: '11px', opacity: 0.7, marginBottom: '2px' }">
          &larr; <span data-olo-editable="prev_label">{{ s.prev_label || 'Precedente' }}</span>
        </div>
        <div v-if="showTitle" :style="{ color: s.link_color || 'var(--olo-color-primary, #e1474f)', fontSize: '13px', fontWeight: 600, whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }">
          {{ t('Titolo articolo precedente') }}
        </div>
      </div>
    </div>

    <!-- Next card -->
    <div :style="cardStyleNext" class="olo-postnav-card">
      <div style="flex:1;min-width:0;text-align:right">
        <div v-if="showLabel" :style="{ color: s.text_color || 'var(--olo-color-text, #374151)', fontSize: '11px', opacity: 0.7, marginBottom: '2px' }">
          <span data-olo-editable="next_label">{{ s.next_label || 'Successivo' }}</span> &rarr;
        </div>
        <div v-if="showTitle" :style="{ color: s.link_color || 'var(--olo-color-primary, #e1474f)', fontSize: '13px', fontWeight: 600, whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }">
          {{ t('Titolo articolo successivo') }}
        </div>
      </div>
      <div v-if="s.show_thumbnail" :style="thumbStyle" class="olo-postnav-thumb">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <rect x="2" y="2" width="20" height="20" rx="3" fill="#E5E7EB"/>
          <path d="M8 16l4-4 3 3 5-5" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="8" cy="8" r="2" fill="#6B7280"/>
        </svg>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';

const defaults = {
  show_thumbnail: true,
  show_label: true,
  prev_label: 'Precedente',
  next_label: 'Successivo',
  show_title: true,
  title_length: '30',
  layout: 'side-by-side',
  gap: '20',
  thumbnail_size: '60',
  text_color: 'var(--olo-color-text, #374151)',
  link_color: '',
  hover_color: '',
  background_color: 'var(--olo-color-muted, #F3F4F6)',
  border_radius: '8',
  padding: '16',
  same_taxonomy: false,
  taxonomy: 'category',
  shadow: 'none',
  border_width: '0',
  border_color: '#e5e7eb',
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));

const showLabel = computed(() => {
  const v = s.value.show_label;
  return v && v !== 'false' && v !== '0';
});

const showTitle = computed(() => {
  const v = s.value.show_title;
  return v && v !== 'false' && v !== '0';
});

const gap = computed(() => (parseInt(s.value.gap) || 20) + 'px');
const pad = computed(() => (parseInt(s.value.padding) || 16) + 'px');
const radius = computed(() => ((v => isNaN(v) ? 8 : v)(parseInt(s.value.border_radius))) + 'px');
const thumbSize = computed(() => (parseInt(s.value.thumbnail_size) || 60) + 'px');

const wrapStyle = computed(() => {
  const isStacked = s.value.layout === 'stacked';
  return {
    display: 'flex',
    flexDirection: isStacked ? 'column' : 'row',
    gap: gap.value,
    padding: '8px',
  };
});

const cardBase = computed(() => {
  const bw = parseInt(s.value.border_width) || 0;
  const st = {
    display: 'flex',
    alignItems: 'center',
    gap: '12px',
    flex: '1',
    minWidth: '0',
    padding: pad.value,
    borderRadius: radius.value,
    background: s.value.background_color || 'var(--olo-color-muted, #F3F4F6)',
    transition: 'box-shadow 0.3s',
    cursor: 'pointer',
  };
  if (bw > 0) {
    st.border = bw + 'px solid ' + (s.value.border_color || '#e5e7eb');
  }
  return st;
});

const cardStyle = computed(() => ({ ...cardBase.value }));
const cardStyleNext = computed(() => ({ ...cardBase.value }));

const thumbStyle = computed(() => ({
  width: thumbSize.value,
  height: thumbSize.value,
  borderRadius: ((v => isNaN(v) ? 8 : v)(parseInt(s.value.border_radius))) + 'px',
  background: 'var(--olo-color-muted, #F3F4F6)',
  flexShrink: '0',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  overflow: 'hidden',
}));
</script>
