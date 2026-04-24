<template>
  <div class="olo-queryloop-preview">
    <div class="ql-header">
      <span class="ql-icon">
        <span class="dashicons dashicons-database"></span>
      </span>
      <span class="ql-label">{{ t('Query Loop') }}</span>
      <span class="ql-info">{{ settings.post_type || 'post' }} &middot; {{ settings.layout || 'grid' }} &middot; {{ settings.columns || 3 }} col</span>
    </div>
    <div class="ql-grid" :style="gridStyle">
      <div
        v-for="n in cardCount"
        :key="n"
        class="ql-card"
        :class="cardClasses"
      >
        <div v-if="settings.show_image !== false" class="ql-img" :style="imgStyle"></div>
        <div class="ql-body">
          <div v-if="settings.show_category !== false" class="ql-cat">{{ t('Categoria') }}</div>
          <div v-if="settings.show_title !== false" class="ql-title" :style="{ color: settings.title_color || undefined }">Titolo articolo {{ n }}</div>
          <div v-if="settings.show_date !== false || settings.show_author" class="ql-meta" :style="{ color: settings.meta_color || undefined }">
            <span v-if="settings.show_date !== false">{{ t('5 Mar 2026') }}</span>
            <span v-if="settings.show_author"> {{ t('&middot; Autore') }}</span>
          </div>
          <div v-if="settings.show_excerpt !== false" class="ql-excerpt" :style="{ color: settings.text_color || undefined }">{{ t('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor...') }}</div>
          <div v-if="settings.show_read_more !== false" class="ql-readmore" :style="{ color: settings.link_color || undefined }" data-olo-editable="read_more_text">{{ settings.read_more_text || 'Leggi tutto' }} &rarr;</div>
        </div>
      </div>
    </div>
    <div v-if="settings.pagination_type && settings.pagination_type !== 'none'" class="ql-pagination">
      <template v-if="settings.pagination_type === 'numbers'">
        <span class="ql-page ql-page-active">1</span>
        <span class="ql-page">2</span>
        <span class="ql-page">3</span>
      </template>
      <template v-else-if="settings.pagination_type === 'loadmore'">
        <button class="ql-loadmore">Carica altro</button>
      </template>
      <template v-else-if="settings.pagination_type === 'infinite'">
        <div class="ql-infinite">&#8595; Scroll infinito attivo</div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const cardCount = computed(() => {
  const ppp = parseInt(props.settings.posts_per_page) || 6;
  return Math.min(ppp, 6);
});

const gridStyle = computed(() => {
  const layout = props.settings.layout || 'grid';
  const cols = parseInt(props.settings.columns) || 3;
  const gap = parseInt(props.settings.gap) || 30;
  if (layout === 'list') {
    return { display: 'flex', flexDirection: 'column', gap: gap + 'px' };
  }
  if (layout === 'masonry') {
    return { columnCount: cols, columnGap: gap + 'px' };
  }
  return {
    display: 'grid',
    gridTemplateColumns: `repeat(${cols}, 1fr)`,
    gap: gap + 'px',
  };
});

const ratioMap = { '16:9': '56.25%', '4:3': '75%', '1:1': '100%', '3:2': '66.67%', 'auto': '60%' };

const imgStyle = computed(() => {
  const ratio = props.settings.image_ratio || '16:9';
  return { paddingBottom: ratioMap[ratio] || '56.25%' };
});

const cardClasses = computed(() => {
  const style = props.settings.card_style || 'none';
  return {
    'ql-card--shadow': style === 'shadow',
    'ql-card--border': style === 'border',
    'ql-card--filled': style === 'filled',
  };
});
</script>

<style scoped>
.olo-queryloop-preview {
  padding: 8px;
}
.ql-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
  font-size: 13px;
  color: #6b7280;
}
.ql-icon { color: var(--olo-color-primary, #6366F1); }
.ql-label { font-weight: 600; color: #374151; }
.ql-info { font-size: 11px; background: #f3f4f6; padding: 2px 8px; border-radius: 4px; }

.ql-card {
  overflow: hidden;
  border-radius: 6px;
  background: #fff;
}
.ql-card--shadow { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.ql-card--border { border: 1px solid #e5e7eb; }
.ql-card--filled { background: #f9fafb; }

.ql-img {
  width: 100%;
  background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
  position: relative;
}
.ql-body { padding: 12px; }
.ql-cat { font-size: 10px; text-transform: uppercase; color: var(--olo-color-primary, #6366F1); font-weight: 600; margin-bottom: 4px; }
.ql-title { font-size: 14px; font-weight: 600; color: var(--olo-color-text, #374151); margin-bottom: 4px; }
.ql-meta { font-size: 11px; color: #9ca3af; margin-bottom: 6px; }
.ql-excerpt { font-size: 12px; color: #6b7280; line-height: 1.5; margin-bottom: 8px; }
.ql-readmore { font-size: 12px; color: var(--olo-color-primary, #6366F1); font-weight: 500; }

.ql-pagination {
  display: flex;
  justify-content: center;
  gap: 6px;
  margin-top: 16px;
}
.ql-page {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 4px;
  font-size: 12px;
  background: #f3f4f6;
  color: #374151;
  cursor: pointer;
}
.ql-page-active { background: var(--olo-color-primary, #6366F1); color: #fff; }
.ql-loadmore {
  padding: 6px 20px;
  font-size: 12px;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  background: #fff;
  cursor: pointer;
}
.ql-infinite {
  font-size: 11px;
  color: #9ca3af;
  text-align: center;
}
</style>
