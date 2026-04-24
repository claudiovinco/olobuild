<template>
  <Teleport to="body">
    <transition name="finder-fade">
      <div
        v-if="visible"
        class="mb-fixed mb-inset-0 mb-z-[99999] mb-flex mb-items-start mb-justify-center mb-pt-[12vh]"
        @click.self="close"
        @keydown.escape="close"
      >
        <div class="mb-absolute mb-inset-0 mb-bg-black/25" @click="close"></div>

        <div
          class="finder-dialog mb-relative mb-w-full mb-max-w-lg mb-rounded-xl mb-shadow-2xl mb-border mb-overflow-hidden"
          role="dialog"
          :aria-label="t('Cerca elementi')"
        >
          <!-- Input ricerca -->
          <div class="finder-header mb-flex mb-items-center mb-gap-2 mb-px-4 mb-py-3 mb-border-b">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mb-shrink-0" style="color: #999">
              <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
            </svg>
            <input
              ref="inputRef"
              v-model="query"
              type="text"
              :placeholder="targetColumnId ? 'Cerca elemento da inserire...' : 'Cerca elemento da aggiungere o tile nel canvas...'"
              class="finder-input mb-flex-1 mb-bg-transparent mb-text-sm mb-outline-none"
              @keydown.down.prevent="moveSelection(1)"
              @keydown.up.prevent="moveSelection(-1)"
              @keydown.enter.prevent="selectCurrent"
            />
            <kbd class="finder-kbd mb-text-[10px] mb-px-1.5 mb-py-0.5 mb-rounded mb-font-mono">ESC</kbd>
          </div>

          <!-- Risultati -->
          <div class="mb-max-h-[50vh] mb-overflow-y-auto mb-py-1">
            <!-- Vuoto -->
            <div
              v-if="allResults.length === 0 && query.length > 0"
              class="mb-px-4 mb-py-6 mb-text-center mb-text-sm" style="color: #999"
            >
              Nessun risultato per "{{ query }}"
            </div>

            <div
              v-if="allResults.length === 0 && query.length === 0"
              class="mb-px-4 mb-py-6 mb-text-center mb-text-sm" style="color: #999"
            >
              {{ t('Digita per cercare elementi da aggiungere o tile nel canvas') }}
            </div>

            <!-- Sezione: Aggiungi elemento -->
            <template v-if="addResults.length > 0">
              <div class="finder-section-label mb-px-4 mb-py-1.5 mb-text-[10px] mb-font-semibold mb-uppercase mb-tracking-wider mb-border-b">
                {{ t('Aggiungi elemento') }}
              </div>
              <button
                v-for="(item, idx) in addResults"
                :key="'add-' + item.type"
                @click="addElement(item.type)"
                @mouseenter="selectedIndex = idx"
                :class="[
                  'finder-result mb-w-full mb-flex mb-items-center mb-gap-3 mb-px-4 mb-py-2 mb-text-left mb-transition-colors',
                  idx === selectedIndex ? 'finder-result--active' : ''
                ]"
              >
                <span class="mb-w-6 mb-h-6 mb-flex mb-items-center mb-justify-center mb-rounded mb-text-xs mb-shrink-0" style="background: rgba(232,98,42,0.12); color: #e8622a">+</span>
                <div class="mb-flex-1 mb-min-w-0">
                  <div class="mb-text-sm mb-font-medium">{{ item.name }}</div>
                  <div class="mb-text-xs" style="color: #999">{{ item.categoryLabel }}</div>
                </div>
              </button>
            </template>

            <!-- Sezione: Tile nel canvas -->
            <template v-if="canvasResults.length > 0">
              <div class="finder-section-label mb-px-4 mb-py-1.5 mb-text-[10px] mb-font-semibold mb-uppercase mb-tracking-wider mb-border-b" :class="addResults.length > 0 ? 'mb-mt-1 mb-border-t' : ''">
                {{ targetColumnId ? 'Copia tile dal canvas' : 'Tile nel canvas' }}
              </div>
              <button
                v-for="(item, idx) in canvasResults"
                :key="item.id"
                @click="selectCanvasTile(item)"
                @mouseenter="selectedIndex = addResults.length + idx"
                :class="[
                  'finder-result mb-w-full mb-flex mb-items-center mb-gap-3 mb-px-4 mb-py-2 mb-text-left mb-transition-colors',
                  (addResults.length + idx) === selectedIndex ? 'finder-result--active' : ''
                ]"
              >
                <span class="finder-type-icon mb-w-6 mb-h-6 mb-flex mb-items-center mb-justify-center mb-rounded mb-text-xs mb-shrink-0 mb-font-mono">
                  {{ typeIcon(item.type) }}
                </span>
                <div class="mb-flex-1 mb-min-w-0">
                  <div class="mb-text-sm mb-font-medium mb-truncate">{{ item.label }}</div>
                  <div class="mb-text-xs mb-truncate" style="color: #999">{{ item.typeName }} &middot; {{ item.id.substring(0, 8) }}</div>
                </div>
                <span
                  v-if="item.preview"
                  class="mb-text-xs mb-truncate mb-max-w-[140px]" style="color: #999"
                >{{ item.preview }}</span>
              </button>
            </template>
          </div>

          <!-- Footer -->
          <div class="finder-footer mb-flex mb-items-center mb-justify-between mb-px-4 mb-py-2 mb-border-t mb-text-[10px]">
            <span>{{ allResults.length }} risultati</span>
            <span>
              <kbd class="finder-kbd mb-px-1 mb-rounded mb-font-mono">&#8593;&#8595;</kbd> naviga
              <kbd class="finder-kbd mb-px-1 mb-rounded mb-font-mono mb-ml-1">&#9166;</kbd> seleziona
            </span>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { useTilesStore, deepCloneWithNewIds, generateId as oloGenerateId } from '../../stores/tiles';
import { useBuilderStore } from '../../stores/builder';
import { useDragDrop } from '../../composables/useDragDrop';
import { getAllElements, getElementDef } from '../../config/elementRegistry';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();
const { handleDropFromSidebar, handleDropIntoColumn } = useDragDrop();

const visible = ref(false);
const query = ref('');
const selectedIndex = ref(0);
const inputRef = ref(null);
const targetColumnId = ref(null);

// Tile strutturali da escludere dalla ricerca
const STRUCTURAL = new Set(['section', 'row', 'column', 'inner-columns']);

const categoryLabels = {
  essential: 'Essenziale',
  layout: 'Layout',
  text: 'Testo',
  media: 'Media',
  marketing: 'Marketing',
  interactive: 'Interattivo',
  navigation: 'Navigazione',
  dynamic: 'Dinamico',
  booking: 'Olo Booking',
  'olo-space': 'Olo Space',
};

// ─── Elementi disponibili (per "Aggiungi") ───
const availableElements = computed(() => {
  return getAllElements().filter(el => !STRUCTURAL.has(el.type));
});

const addResults = computed(() => {
  const q = query.value.trim().toLowerCase();
  if (!q) return [];

  const matched = [];
  for (const el of availableElements.value) {
    if (matched.length >= 8) break;
    const searchText = `${el.type} ${el.name} ${el.category || ''}`.toLowerCase();
    if (searchText.includes(q)) {
      matched.push({
        type: el.type,
        name: el.name,
        categoryLabel: categoryLabels[el.category] || el.category || '',
      });
    }
  }
  return matched;
});

// ─── Tile nel canvas ───
function flattenNodes(nodes, result = []) {
  if (!Array.isArray(nodes)) return result;
  for (const node of nodes) {
    result.push(node);
    if (Array.isArray(node.children)) {
      flattenNodes(node.children, result);
    }
  }
  return result;
}

const allCanvasTiles = computed(() =>
  flattenNodes(tilesStore.canvasTiles).filter(t => !STRUCTURAL.has(t.type))
);

const canvasResults = computed(() => {
  const q = query.value.trim().toLowerCase();
  if (!q) return [];

  const matched = [];
  for (const tile of allCanvasTiles.value) {
    if (matched.length >= 12) break;

    const type = tile.type || '';
    const id = tile.id || '';
    const settings = tile.settings || {};
    const elDef = getElementDef(type);

    const searchableTexts = [type, id, elDef?.name || ''];
    for (const val of Object.values(settings)) {
      if (typeof val === 'string') {
        searchableTexts.push(val);
      }
    }

    const combined = searchableTexts.join(' ').toLowerCase();
    if (combined.includes(q)) {
      matched.push({
        id: tile.id,
        type: tile.type,
        typeName: elDef?.name || tile.type,
        label: getLabel(tile),
        preview: getPreview(tile),
      });
    }
  }
  return matched;
});

const allResults = computed(() => [...addResults.value, ...canvasResults.value]);

function getLabel(tile) {
  const s = tile.settings || {};
  const textKeys = ['heading', 'title', 'text', 'name', 'plan_name', 'label', 'quote', 'alert_type'];
  for (const k of textKeys) {
    if (s[k] && typeof s[k] === 'string' && s[k].trim()) {
      const clean = s[k].replace(/<[^>]*>/g, '').trim();
      if (clean) return clean.substring(0, 60);
    }
  }
  const def = getElementDef(tile.type);
  return def?.name || (tile.type.charAt(0).toUpperCase() + tile.type.slice(1));
}

function getPreview(tile) {
  const s = tile.settings || {};
  const previewKeys = ['subtitle', 'description', 'message', 'html_content', 'url', 'image_url', 'link_url', 'video_url'];
  for (const k of previewKeys) {
    if (s[k] && typeof s[k] === 'string' && s[k].trim()) {
      const clean = s[k].replace(/<[^>]*>/g, '').trim();
      if (clean) return clean.substring(0, 50);
    }
  }
  return '';
}

function typeIcon(type) {
  const icons = {
    headline: 'H', button: 'B', image: 'I', content: 'T', video: 'V',
    gallery: 'G', form: 'F', hero: 'Hr', list: 'Li', table: 'Tb',
    accordion: 'Ac', alert: 'Al', social: 'So', icon: 'Ic', spacer: 'Sp',
    divider: 'Di', map: 'Mp', overlay: 'Ov', slideshow: 'Sl',
    countdown: 'Cd', counter: 'Co', pricing: 'Pr', progress: 'Pg',
    team: 'Tm', testimonial: 'Te',
  };
  return icons[type] || type.substring(0, 2).toUpperCase();
}

// ─── Azioni ───
function addElement(tileType) {
  const afterId = builderStore.insertAfterTileId;
  if (afterId) {
    const reg = tilesStore.registeredTiles.find(t => t.type === tileType);
    const newTile = {
      id: oloGenerateId(),
      type: tileType,
      settings: JSON.parse(JSON.stringify((reg || {}).defaults || {})),
      style: {},
      advanced: {},
    };
    tilesStore.insertAfter(afterId, newTile);
    builderStore.isDirty = true;
    builderStore.selectTile(newTile.id);
    builderStore.insertAfterTileId = null;
  } else if (targetColumnId.value) {
    handleDropIntoColumn(tileType, targetColumnId.value);
  } else {
    handleDropFromSidebar(tileType);
  }
  close();
}

function selectCanvasTile(item) {
  if (targetColumnId.value) {
    // Copia la tile esistente e inseriscila nella colonna target
    const original = tilesStore.getTileById(item.id);
    if (original) {
      const clone = deepCloneWithNewIds(original);
      tilesStore.addChild(targetColumnId.value, clone);
      builderStore.isDirty = true;
      builderStore.selectTile(clone.id);
    }
  } else {
    builderStore.selectTile(item.id);
  }
  close();
}

// ─── Navigazione tastiera ───
function moveSelection(delta) {
  const len = allResults.value.length;
  if (len === 0) return;
  selectedIndex.value = (selectedIndex.value + delta + len) % len;
}

function selectCurrent() {
  const total = allResults.value.length;
  if (total === 0) return;
  const idx = selectedIndex.value;
  if (idx < addResults.value.length) {
    addElement(addResults.value[idx].type);
  } else {
    selectCanvasTile(canvasResults.value[idx - addResults.value.length]);
  }
}

watch(query, () => {
  selectedIndex.value = 0;
});

// ─── Apri/Chiudi ───
function open(columnId) {
  targetColumnId.value = columnId || null;
  visible.value = true;
  query.value = '';
  selectedIndex.value = 0;
  nextTick(() => {
    inputRef.value?.focus();
  });
}

function close() {
  visible.value = false;
  targetColumnId.value = null;
}

function toggle() {
  if (visible.value) {
    close();
  } else {
    open();
  }
}

// ─── Scorciatoia Ctrl+K ───
function handleKeydown(e) {
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault();
    toggle();
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown);
});

defineExpose({ open, close, toggle });
</script>

<style scoped>
.finder-fade-enter-active,
.finder-fade-leave-active {
  transition: opacity 0.15s ease;
}
.finder-fade-enter-from,
.finder-fade-leave-to {
  opacity: 0;
}

/* ── Light / Glass theme ── */
.finder-dialog {
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(24px) saturate(180%);
  -webkit-backdrop-filter: blur(24px) saturate(180%);
  border-color: rgba(0, 0, 0, 0.1);
  color: #1a1a1a;
}
.finder-header {
  border-color: rgba(0, 0, 0, 0.08);
}
.finder-input {
  color: #1a1a1a;
}
.finder-input::placeholder {
  color: #999;
}
.finder-kbd {
  background: rgba(0, 0, 0, 0.06);
  color: #888;
}
.finder-section-label {
  color: #999;
  border-color: rgba(0, 0, 0, 0.06);
}
.finder-result {
  color: #333;
  background: transparent;
  border: none;
}
.finder-result:hover {
  background: rgba(0, 0, 0, 0.04);
}
.finder-result--active {
  background: rgba(232, 98, 42, 0.1) !important;
  color: #1a1a1a;
}
.finder-type-icon {
  background: rgba(0, 0, 0, 0.06);
  color: #888;
}
.finder-footer {
  border-color: rgba(0, 0, 0, 0.08);
  color: #999;
}
</style>
