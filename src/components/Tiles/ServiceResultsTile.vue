<template>
  <div class="olo-svresults-preview" :class="'olo-svresults-preview--' + (settings.layout || 'map-left')">

    <div v-if="visibleFilters.length > 0" class="olo-svresults-preview-filters">
      <div class="olo-svresults-preview-selects">
        <span v-if="hasFilter('valley')" class="olo-svresults-preview-sel">Località</span>
        <span v-if="hasFilter('guests')" class="olo-svresults-preview-sel">Ospiti</span>
        <span v-if="hasFilter('bedrooms')" class="olo-svresults-preview-sel">Camere</span>
        <span v-if="hasFilter('altitude')" class="olo-svresults-preview-sel">Altitudine</span>
        <span v-if="hasFilter('type')" class="olo-svresults-preview-sel">Tipologia</span>
      </div>
      <div v-if="hasFilter('amenities')" class="olo-svresults-preview-amenities">
        <span v-for="a in amenitiesList" :key="a" class="olo-svresults-preview-pill">{{ a }}</span>
      </div>
      <div class="olo-svresults-preview-counter">12 / 45 strutture</div>
    </div>

    <div class="olo-svresults-preview-body">
      <div v-if="settings.layout !== 'cards-only'" class="olo-svresults-preview-map" :style="{ height: (settings.map_height || 500) + 'px' }">
        <div class="olo-svresults-preview-map-placeholder">
          <span>Mappa Leaflet</span>
          <small>{{ tileLayerLabel }} &middot; zoom {{ settings.default_zoom || 10 }}</small>
        </div>
      </div>
      <div class="olo-svresults-preview-grid" :style="gridStyle">
        <div v-for="i in 4" :key="i" class="olo-svresults-preview-card" :style="{ borderRadius: (settings.card_radius || 8) + 'px' }">
          <div class="olo-svresults-preview-card-img" :style="{ height: (settings.image_height || 180) + 'px' }"></div>
          <div class="olo-svresults-preview-card-body">
            <div class="olo-svresults-preview-card-title">Struttura {{ i }}</div>
            <div v-if="hasContent('stats')" class="olo-svresults-preview-card-stats">4 ospiti &middot; 2 camere &middot; 1.200m</div>
            <div v-if="hasContent('excerpt')" class="olo-svresults-preview-card-excerpt">Lorem ipsum dolor sit amet...</div>
            <div class="olo-svresults-preview-card-footer">
              <span v-if="hasContent('price')" class="olo-svresults-preview-card-price">{{ settings.price_prefix || '\u20ac' }}85{{ settings.price_suffix || '/notte' }}</span>
              <span class="olo-svresults-preview-card-link">{{ settings.link_text || 'Dettagli' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="olo-svresults-preview-pagination">
      <template v-if="settings.pagination_style === 'dots'">
        <span class="olo-svresults-preview-dot active"></span>
        <span class="olo-svresults-preview-dot"></span>
        <span class="olo-svresults-preview-dot"></span>
      </template>
      <template v-else-if="settings.pagination_style === 'arrows'">
        <span class="olo-svresults-preview-arrow">&larr;</span>
        <span style="font-size:0.8em;color:#888;">1 / 3</span>
        <span class="olo-svresults-preview-arrow">&rarr;</span>
      </template>
      <template v-else-if="settings.pagination_style === 'loadmore'">
        <span class="olo-svresults-preview-loadmore">Carica altro</span>
      </template>
      <template v-else>
        <span class="olo-svresults-preview-num active">1</span>
        <span class="olo-svresults-preview-num">2</span>
        <span class="olo-svresults-preview-num">3</span>
      </template>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const visibleFilters = computed(() => {
  const raw = props.settings.visible_filters || '';
  return raw.split(',').map(s => s.trim()).filter(Boolean);
});

function hasFilter(name) {
  return visibleFilters.value.includes(name);
}

const cardContent = computed(() => {
  const raw = props.settings.card_content || '';
  return raw.split(',').map(s => s.trim()).filter(Boolean);
});

function hasContent(name) {
  return cardContent.value.includes(name);
}

const amenitiesList = computed(() => {
  const raw = props.settings.amenities_list || '';
  return raw.split(',').map(s => s.trim()).filter(Boolean).slice(0, 8);
});

const tileLayerLabel = computed(() => {
  const map = { osm: 'OSM', positron: 'Positron', dark: 'Dark' };
  return map[props.settings.tile_layer] || 'Positron';
});

const gapMap = { collapse: '0', small: '8px', medium: '16px', large: '24px' };

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: 'repeat(' + (props.settings.columns || 2) + ', 1fr)',
  gap: gapMap[props.settings.gap] || '16px',
}));
</script>

<style scoped>
.olo-svresults-preview {
  font-size: 13px;
}
.olo-svresults-preview-filters {
  padding: 10px 0;
  border-bottom: 1px solid #e5e7eb;
  margin-bottom: 12px;
}
.olo-svresults-preview-selects {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 6px;
}
.olo-svresults-preview-sel {
  padding: 4px 10px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  font-size: 0.82em;
  color: #666;
}
.olo-svresults-preview-amenities {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-bottom: 6px;
}
.olo-svresults-preview-pill {
  padding: 2px 8px;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 999px;
  font-size: 0.72em;
  color: #666;
}
.olo-svresults-preview-counter {
  font-size: 0.78em;
  color: #999;
}
.olo-svresults-preview-body {
  display: flex;
  gap: 12px;
}
.olo-svresults-preview--map-right .olo-svresults-preview-body {
  flex-direction: row-reverse;
}
.olo-svresults-preview--map-top .olo-svresults-preview-body {
  flex-direction: column;
}
.olo-svresults-preview--cards-only .olo-svresults-preview-body {
  flex-direction: column;
}
.olo-svresults-preview-map {
  flex: 0 0 45%;
  min-width: 0;
}
.olo-svresults-preview--map-top .olo-svresults-preview-map {
  flex: none;
  height: 200px !important;
}
.olo-svresults-preview-map-placeholder {
  width: 100%;
  height: 100%;
  background: #e5e7eb;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #999;
  font-size: 0.85em;
}
.olo-svresults-preview-grid {
  flex: 1;
  min-width: 0;
}
.olo-svresults-preview-card {
  border: 1px solid #e5e7eb;
  overflow: hidden;
  background: #fff;
}
.olo-svresults-preview-card-img {
  background: linear-gradient(135deg, #e0e7ff, #f0f0f0);
}
.olo-svresults-preview-card-body {
  padding: 10px;
}
.olo-svresults-preview-card-title {
  font-weight: 600;
  font-size: 0.9em;
  margin-bottom: 4px;
}
.olo-svresults-preview-card-stats {
  font-size: 0.75em;
  color: #888;
  margin-bottom: 4px;
}
.olo-svresults-preview-card-excerpt {
  font-size: 0.78em;
  color: #888;
  margin-bottom: 6px;
}
.olo-svresults-preview-card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.olo-svresults-preview-card-price {
  font-weight: 700;
  font-size: 0.9em;
  color: var(--olo-color-primary, #6366F1);
}
.olo-svresults-preview-card-link {
  font-size: 0.78em;
  color: var(--olo-color-primary, #6366F1);
}
.olo-svresults-preview-pagination {
  display: flex;
  justify-content: center;
  gap: 6px;
  margin-top: 12px;
}
.olo-svresults-preview-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  border: 2px solid #ccc;
}
.olo-svresults-preview-dot.active {
  background: var(--olo-color-primary, #6366F1);
  border-color: var(--olo-color-primary, #6366F1);
}
.olo-svresults-preview-num {
  min-width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  font-size: 0.8em;
  border: 1px solid #ddd;
  color: #666;
}
.olo-svresults-preview-num.active {
  background: var(--olo-color-primary, #6366F1);
  color: #fff;
  border-color: var(--olo-color-primary, #6366F1);
}
.olo-svresults-preview-arrow {
  font-size: 0.9em;
  color: #888;
}
.olo-svresults-preview-loadmore {
  padding: 4px 16px;
  border: 1px solid var(--olo-color-primary, #6366F1);
  border-radius: 999px;
  font-size: 0.78em;
  color: var(--olo-color-primary, #6366F1);
}
</style>
