<template>
  <div class="olo-portfolio-tile">
    <!-- Filter bar preview -->
    <div v-if="s.filter_bar" class="olo-pf-filter-preview" :style="filterBarStyle">
      <span
        v-for="(cat, idx) in previewCategories"
        :key="idx"
        :style="idx === 0 ? activeFilterStyle : inactiveFilterStyle"
        :class="filterItemClass"
        v-bind="idx === 0 ? { 'data-olo-editable': 'filter_all_label' } : {}"
      >
        {{ cat }}
      </span>
    </div>

    <!-- Grid preview -->
    <div :style="gridStyle">
      <div
        v-for="(item, idx) in previewItems"
        :key="item.id || idx"
        class="olo-pf-card"
        :style="cardStyle"
      >
        <!-- Image area -->
        <div :style="imageAreaStyle">
          <img
            v-if="item.image_url"
            :src="item.image_url"
            :alt="item.title"
            style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block;"
          />
          <div v-else :style="placeholderStyle">
            <span style="font-size:24px;opacity:0.5;">{{ t('&#x1F5BC;') }}</span>
          </div>
          <!-- Hover overlay preview -->
          <div
            v-if="s.hover_effect !== 'none'"
            class="olo-pf-overlay"
            :style="overlayPreviewStyle"
          >
            <span v-if="s.show_title" style="font-weight:600;font-size:12px;">
              {{ item.title }}
            </span>
          </div>
        </div>

        <!-- Text content below image -->
        <div v-if="(s.show_title || s.show_category || s.show_excerpt) && s.hover_effect !== 'overlay'" style="padding:10px 0 0;">
          <div
            v-if="s.show_category && item.category"
            :style="{ fontSize: '10px', color: s.filter_active_color || '#6366F1', fontWeight: '600', textTransform: 'uppercase', letterSpacing: '0.5px', marginBottom: '2px' }"
            :data-olo-editable="`items.${idx}.category`"
          >
            {{ item.category }}
          </div>
          <div
            v-if="s.show_title"
            :style="{ fontSize: '13px', fontWeight: '600', color: s.title_color || '#374151' }"
            :data-olo-editable="`items.${idx}.title`"
          >
            {{ item.title }}
          </div>
          <div
            v-if="s.show_excerpt && item.description"
            :style="{ fontSize: '11px', color: s.text_color || '#374151', marginTop: '4px', lineHeight: '1.4' }"
            :data-olo-editable="`items.${idx}.description`"
          >{{ item.description }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  source: 'manual',
  columns: '3',
  gap: '20',
  filter_bar: true,
  filter_style: 'buttons',
  filter_all_label: 'Tutti',
  filter_color: '#9CA3AF',
  filter_active_color: '#6366F1',
  layout: 'grid',
  hover_effect: 'fade',
  show_title: true,
  show_category: true,
  show_excerpt: false,
  title_color: '#374151',
  text_color: '#6B7280',
  overlay_color: '#000000',
  overlay_opacity: '80',
  image_ratio: '4:3',
  border_radius: '8',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const previewItems = computed(() => {
  const items = Array.isArray(props.settings.items) ? props.settings.items : [];
  if (items.length > 0) return items.slice(0, 6);
  return [
    { id: 'ph-1', title: 'Progetto Alpha', category: 'Design', description: 'Descrizione progetto.', image_url: '', link_url: '' },
    { id: 'ph-2', title: 'Progetto Beta', category: 'Web', description: 'Descrizione progetto.', image_url: '', link_url: '' },
    { id: 'ph-3', title: 'Progetto Gamma', category: 'Design', description: 'Descrizione progetto.', image_url: '', link_url: '' },
    { id: 'ph-4', title: 'Progetto Delta', category: 'Foto', description: 'Descrizione progetto.', image_url: '', link_url: '' },
    { id: 'ph-5', title: 'Progetto Epsilon', category: 'Web', description: 'Descrizione progetto.', image_url: '', link_url: '' },
    { id: 'ph-6', title: 'Progetto Zeta', category: 'Foto', description: 'Descrizione progetto.', image_url: '', link_url: '' },
  ];
});

const previewCategories = computed(() => {
  const cats = new Set();
  previewItems.value.forEach(item => {
    if (item.category) cats.add(item.category);
  });
  return [s.value.filter_all_label || 'Tutti', ...Array.from(cats)];
});

const ratioMap = {
  '1:1': '100%',
  '4:3': '75%',
  '16:9': '56.25%',
  '3:2': '66.67%',
  'auto': '70%',
};

const cols = computed(() => Math.max(1, Math.min(6, parseInt(s.value.columns) || 3)));

const gridStyle = computed(() => {
  if (s.value.layout === 'masonry') {
    return {
      columnCount: cols.value,
      columnGap: (parseInt(s.value.gap) || 20) + 'px',
    };
  }
  return {
    display: 'grid',
    gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
    gap: (parseInt(s.value.gap) || 20) + 'px',
  };
});

const cardStyle = computed(() => {
  const style = {
    borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
    overflow: 'hidden',
  };
  if (s.value.layout === 'masonry') {
    style.breakInside = 'avoid';
    style.marginBottom = (parseInt(s.value.gap) || 20) + 'px';
  }
  return style;
});

const imageAreaStyle = computed(() => ({
  position: 'relative',
  paddingTop: ratioMap[s.value.image_ratio] || '75%',
  borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
  overflow: 'hidden',
  background: '#F3F4F6',
}));

const placeholderStyle = computed(() => ({
  position: 'absolute',
  inset: '0',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  background: '#E5E7EB',
}));

const overlayPreviewStyle = computed(() => ({
  position: 'absolute',
  inset: '0',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  background: s.value.overlay_color || '#000000',
  opacity: '0.35',
  color: '#fff',
  transition: 'opacity 0.3s ease',
  borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
}));

const filterBarStyle = computed(() => ({
  display: 'flex',
  flexWrap: 'wrap',
  gap: '8px',
  marginBottom: '16px',
  padding: '4px 0',
}));

const filterItemClass = computed(() => {
  const style = s.value.filter_style || 'buttons';
  return 'olo-pf-filter-' + style;
});

const activeFilterStyle = computed(() => {
  const style = s.value.filter_style || 'buttons';
  const base = {
    fontSize: '11px',
    fontWeight: '600',
    cursor: 'pointer',
    transition: 'all 0.2s',
    color: '#fff',
  };
  if (style === 'pills') {
    base.padding = '4px 14px';
    base.borderRadius = '20px';
    base.background = s.value.filter_active_color || '#6366F1';
  } else if (style === 'underline') {
    base.padding = '4px 8px';
    base.borderBottom = '2px solid ' + (s.value.filter_active_color || '#6366F1');
    base.background = 'transparent';
    base.color = s.value.filter_active_color || '#6366F1';
  } else {
    base.padding = '4px 12px';
    base.borderRadius = '4px';
    base.background = s.value.filter_active_color || '#6366F1';
  }
  return base;
});

const inactiveFilterStyle = computed(() => {
  const style = s.value.filter_style || 'buttons';
  const base = {
    fontSize: '11px',
    fontWeight: '500',
    cursor: 'pointer',
    transition: 'all 0.2s',
    color: s.value.filter_color || '#374151',
  };
  if (style === 'pills') {
    base.padding = '4px 14px';
    base.borderRadius = '20px';
    base.background = '#F3F4F6';
  } else if (style === 'underline') {
    base.padding = '4px 8px';
    base.borderBottom = '2px solid transparent';
    base.background = 'transparent';
  } else {
    base.padding = '4px 12px';
    base.borderRadius = '4px';
    base.background = '#F3F4F6';
  }
  return base;
});
</script>

<style scoped>
.olo-portfolio-tile {
  min-height: 100px;
}
.olo-pf-card:hover .olo-pf-overlay {
  opacity: 0.85 !important;
}
</style>
