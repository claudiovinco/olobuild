<template>
  <div class="glp-overlay" @click.self="$emit('close')">
    <div class="glp-panel">
      <div class="glp-header">
        <h3 class="glp-title">Choose Layout</h3>
        <button class="glp-close" @click="$emit('close')">&times;</button>
      </div>

      <!-- Tabs -->
      <div class="glp-tabs">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          :class="['glp-tab', { 'glp-tab--active': activeTab === tab.key }]"
          @click="activeTab = tab.key"
        >{{ tab.label }}</button>
      </div>

      <!-- Templates grid -->
      <div class="glp-body">
        <!-- Category sections -->
        <template v-for="cat in visibleCategories" :key="cat.key">
          <div v-if="cat.templates.length > 0" class="glp-category">
            <div class="glp-cat-label">
              <span class="glp-cat-badge">Grid</span>
              {{ cat.label }}
            </div>
            <div class="glp-grid">
              <button
                v-for="tpl in cat.templates"
                :key="tpl.id"
                :class="['glp-card', { 'glp-card--active': selectedId === tpl.id }]"
                @click="selectTemplate(tpl)"
                :title="tpl.name"
              >
                <svg viewBox="0 0 80 50" class="glp-svg">
                  <rect
                    v-for="(r, ri) in normRects(tpl.preview)"
                    :key="ri"
                    :x="r.x" :y="r.y" :width="r.w" :height="r.h"
                    rx="2"
                    :fill="selectedId === tpl.id ? '#818cf8' : '#c7d2e0'"
                  />
                </svg>
              </button>
            </div>
          </div>
        </template>

        <!-- Flex presets section -->
        <div v-if="activeTab === 'all' || activeTab === 'columns'" class="glp-category">
          <div class="glp-cat-label">
            <span class="glp-cat-badge glp-cat-badge--flex">Flex</span>
            Colonne Flex (classico)
          </div>
          <div class="glp-grid">
            <button
              v-for="fp in flexPresets"
              :key="fp.key"
              :class="['glp-card', { 'glp-card--active': selectedFlex === fp.key }]"
              @click="selectFlex(fp)"
              :title="fp.label"
            >
              <svg viewBox="0 0 80 50" class="glp-svg">
                <rect
                  v-for="(c, ci) in fp.normRects"
                  :key="ci"
                  :x="c.x" :y="c.y" :width="c.w" :height="c.h"
                  rx="2"
                  :fill="selectedFlex === fp.key ? '#818cf8' : '#c7d2e0'"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { columns, multirow, masonry, sidebar } from '@/config/gridTemplates.js';

const props = defineProps({
  currentLayout: { type: String, default: '' },
  currentMode: { type: String, default: 'flex' },
});
const emit = defineEmits(['close', 'select-grid', 'select-flex']);

const activeTab = ref('all');
const selectedId = ref(props.currentMode === 'grid' ? props.currentLayout : '');
const selectedFlex = ref(props.currentMode !== 'grid' ? props.currentLayout : '');

const tabs = [
  { key: 'all', label: 'Tutti' },
  { key: 'columns', label: 'Colonne' },
  { key: 'multirow', label: 'Multi-Row' },
  { key: 'masonry', label: 'Masonry' },
  { key: 'sidebar', label: 'Sidebar' },
];

const categories = [
  { key: 'columns', label: 'Colonne', templates: columns },
  { key: 'multirow', label: 'Multi-Row', templates: multirow },
  { key: 'masonry', label: 'Masonry', templates: masonry },
  { key: 'sidebar', label: 'Sidebar', templates: sidebar },
];

const visibleCategories = computed(() => {
  if (activeTab.value === 'all') return categories;
  return categories.filter(c => c.key === activeTab.value);
});

// Flex presets for backward compat
const flexPresets = [
  { key: '100', label: '100%', cols: [100] },
  { key: '50-50', label: '50/50', cols: [50, 50] },
  { key: '33-33-33', label: '33/33/33', cols: [33, 33, 34] },
  { key: '25-50-25', label: '25/50/25', cols: [25, 50, 25] },
  { key: '25-25-25-25', label: '25×4', cols: [25, 25, 25, 25] },
  { key: '66-33', label: '66/33', cols: [66, 34] },
  { key: '33-66', label: '33/66', cols: [34, 66] },
].map(fp => {
  const total = fp.cols.reduce((a, b) => a + b, 0);
  const pad = 3, gap = 3, vw = 80, vh = 50;
  const usable = vw - pad * 2 - (fp.cols.length - 1) * gap;
  let x = pad;
  const normRects = fp.cols.map(c => {
    const w = Math.round((c / total) * usable);
    const rect = { x, y: pad, w, h: vh - pad * 2 };
    x += w + gap;
    return rect;
  });
  return { ...fp, normRects };
});

/**
 * Normalize preview rects into a fixed 80×50 viewBox.
 * Each template has different cols/rows counts — this makes them all look consistent.
 */
function normRects(preview) {
  const { cols, rows, rects } = preview;
  const pad = 3; // padding around grid
  const gap = 3; // gap between cells
  const vw = 80, vh = 50;
  const cellW = (vw - pad * 2 - (cols - 1) * gap) / cols;
  const cellH = (vh - pad * 2 - (rows - 1) * gap) / rows;
  return rects.map(r => ({
    x: pad + r.x * (cellW + gap),
    y: pad + r.y * (cellH + gap),
    w: r.w * cellW + (r.w - 1) * gap,
    h: r.h * cellH + (r.h - 1) * gap,
  }));
}

function selectTemplate(tpl) {
  selectedId.value = tpl.id;
  selectedFlex.value = '';
  emit('select-grid', tpl.id);
  emit('close');
}

function selectFlex(fp) {
  selectedFlex.value = fp.key;
  selectedId.value = '';
  emit('select-flex', fp.key);
  emit('close');
}
</script>

<style scoped>
.glp-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding-top: 60px;
}

.glp-panel {
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  width: 520px;
  max-height: calc(100vh - 100px);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.glp-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px 12px;
  border-bottom: 1px solid #f0f0f5;
}

.glp-title {
  font-size: 15px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
}

.glp-close {
  width: 28px;
  height: 28px;
  border: none;
  background: #f5f5f8;
  border-radius: 8px;
  font-size: 18px;
  color: #888;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
}
.glp-close:hover { background: #eee; color: #333; }

.glp-tabs {
  display: flex;
  gap: 2px;
  padding: 8px 16px;
  background: #fafafc;
  border-bottom: 1px solid #f0f0f5;
}

.glp-tab {
  flex: 1;
  padding: 6px 0;
  border: none;
  background: none;
  font-size: 12px;
  font-weight: 500;
  color: #888;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.15s;
}
.glp-tab:hover { background: #f0f0f5; color: #555; }
.glp-tab--active { background: #1a1a1a; color: #fff; }

.glp-body {
  overflow-y: auto;
  padding: 12px 16px 20px;
  flex: 1;
}

.glp-category {
  margin-bottom: 16px;
}
.glp-category:last-child { margin-bottom: 0; }

.glp-cat-label {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 600;
  color: #888;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
}

.glp-cat-badge {
  display: inline-flex;
  padding: 1px 6px;
  border-radius: 4px;
  font-size: 9px;
  font-weight: 700;
  background: #fff3e0;
  color: #e65100;
  text-transform: uppercase;
}
.glp-cat-badge--flex {
  background: #e3f2fd;
  color: #1565c0;
}

.glp-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
}

.glp-card {
  aspect-ratio: 16 / 10;
  border: 2px solid #e8e8ee;
  border-radius: 8px;
  background: #fafafc;
  cursor: pointer;
  transition: all 0.15s;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px;
}
.glp-card:hover { border-color: #b4b4c8; background: #f0f0f8; }
.glp-card--active { border-color: #6366f1; background: #eef2ff; }

.glp-svg {
  width: 100%;
  height: auto;
  display: block;
}
</style>
