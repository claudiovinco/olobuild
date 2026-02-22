<template>
  <div class="olom-amenities-catalog">
    <div class="olom-page-header">
      <h2 class="olom-page-title">Catalogo Amenities</h2>
    </div>
    <p class="olom-hint">Gestisci le amenities disponibili per le strutture. Trascina per riordinare categorie e singoli elementi.</p>

    <div v-if="loading" class="olom-loading">Caricamento...</div>

    <div v-else>
      <!-- Categories -->
      <div class="olom-ac-cats">
        <div
          v-for="(cat, ci) in catalog.categories"
          :key="cat.key"
          class="olom-ac-cat"
        >
          <div class="olom-ac-cat-header">
            <div class="olom-ac-cat-left">
              <span class="olom-ac-handle" title="Trascina per riordinare" @mousedown="startDragCat($event, ci)">&#9776;</span>
              <input
                class="olom-ac-cat-name"
                v-model="cat.label"
                placeholder="Nome categoria"
              />
              <span class="olom-ac-cat-count">{{ cat.items.length }} items</span>
            </div>
            <div class="olom-ac-cat-actions">
              <button class="olom-ac-btn olom-ac-btn-sm" @click="addItem(cat)" title="Aggiungi amenity">+ Amenity</button>
              <button class="olom-ac-btn olom-ac-btn-sm olom-ac-btn-danger" @click="removeCat(ci)" title="Elimina categoria">&times;</button>
            </div>
          </div>

          <!-- Items -->
          <div class="olom-ac-items">
            <div
              v-for="(item, ii) in cat.items"
              :key="item.key"
              class="olom-ac-item"
            >
              <span class="olom-ac-handle olom-ac-handle-sm" title="Trascina" @mousedown="startDragItem($event, ci, ii)">&#9776;</span>
              <span class="olom-ac-item-icon">{{ getIconDisplay(item) }}</span>
              <input
                class="olom-ac-item-name"
                v-model="item.label"
                placeholder="Nome amenity"
              />
              <input
                class="olom-ac-item-icon-input"
                v-model="item.icon"
                placeholder="Icona (key o emoji:🍳)"
                title="Per le built-in: key icona. Per le custom: emoji:🍳"
              />
              <span class="olom-ac-item-key">{{ item.key }}</span>
              <button class="olom-ac-btn-icon olom-ac-btn-danger" @click="removeItem(ci, ii)" title="Elimina">&times;</button>
            </div>
            <div v-if="cat.items.length === 0" class="olom-ac-empty">
              Nessuna amenity in questa categoria.
            </div>
          </div>
        </div>
      </div>

      <div class="olom-ac-add-cat">
        <button class="olom-btn" @click="addCategory">+ Aggiungi categoria</button>
      </div>

      <div class="olom-perm-actions">
        <button class="olom-btn olom-btn-success" @click="save" :disabled="saving">
          {{ saving ? 'Salvataggio...' : 'Salva catalogo' }}
        </button>
        <span v-if="savedMsg" class="olom-saved-msg">{{ savedMsg }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { api } from '../stores/api.js';

const cfg = window.oloManagerConfig || {};
const loading = ref(true);
const saving = ref(false);
const savedMsg = ref('');

const catalog = reactive({ categories: [] });

// Built-in icon keys (have SVG in ServiceDetailView)
const builtinIcons = new Set([
  'wifi','heating','aircon','fireplace','tv','pets','smoking','elevator','accessible',
  'kitchen','oven','microwave','dishwasher','fridge','coffee','kettle',
  'washer','dryer','iron','hairdryer','bathtub',
  'parking','garage','garden','terrace','bbq','pool','hottub',
  'ski','bikes','playground','sauna','hiking',
  'linens','towels','cleaning','crib','highchair','safe',
]);

let nextCustomId = 1;

function generateKey() {
  return 'custom_' + Date.now().toString(36) + (nextCustomId++).toString(36);
}

function getIconDisplay(item) {
  if (item.icon && item.icon.startsWith('emoji:')) {
    return item.icon.slice(6);
  }
  if (builtinIcons.has(item.icon)) {
    return '\u2713'; // checkmark for built-in
  }
  return '\u2713';
}

function addCategory() {
  catalog.categories.push({
    key: generateKey(),
    label: 'Nuova categoria',
    order: catalog.categories.length,
    items: [],
  });
}

function removeCat(ci) {
  catalog.categories.splice(ci, 1);
}

function addItem(cat) {
  cat.items.push({
    key: generateKey(),
    label: 'Nuova amenity',
    icon: 'emoji:\u2728',
    order: cat.items.length,
  });
}

function removeItem(ci, ii) {
  catalog.categories[ci].items.splice(ii, 1);
}

// Simple drag reorder for categories
let dragType = null;
let dragCatIdx = null;
let dragItemIdx = null;
let dragStartY = 0;

function startDragCat(e, ci) {
  dragType = 'cat';
  dragCatIdx = ci;
  dragStartY = e.clientY;
  const onMove = (me) => {
    const diff = me.clientY - dragStartY;
    if (Math.abs(diff) > 40) {
      const dir = diff > 0 ? 1 : -1;
      const newIdx = ci + dir;
      if (newIdx >= 0 && newIdx < catalog.categories.length) {
        const tmp = catalog.categories.splice(ci, 1)[0];
        catalog.categories.splice(newIdx, 0, tmp);
        dragCatIdx = newIdx;
        ci = newIdx;
        dragStartY = me.clientY;
      }
    }
  };
  const onUp = () => {
    document.removeEventListener('mousemove', onMove);
    document.removeEventListener('mouseup', onUp);
  };
  document.addEventListener('mousemove', onMove);
  document.addEventListener('mouseup', onUp);
}

function startDragItem(e, ci, ii) {
  dragType = 'item';
  dragStartY = e.clientY;
  const items = catalog.categories[ci].items;
  const onMove = (me) => {
    const diff = me.clientY - dragStartY;
    if (Math.abs(diff) > 30) {
      const dir = diff > 0 ? 1 : -1;
      const newIdx = ii + dir;
      if (newIdx >= 0 && newIdx < items.length) {
        const tmp = items.splice(ii, 1)[0];
        items.splice(newIdx, 0, tmp);
        ii = newIdx;
        dragStartY = me.clientY;
      }
    }
  };
  const onUp = () => {
    document.removeEventListener('mousemove', onMove);
    document.removeEventListener('mouseup', onUp);
  };
  document.addEventListener('mousemove', onMove);
  document.addEventListener('mouseup', onUp);
}

onMounted(async () => {
  try {
    // Load from injected config or fetch from API
    const data = cfg.amenitiesCatalog || await api.get('/manager/amenities-catalog');
    if (data && data.categories) {
      catalog.categories = data.categories;
    }
  } catch (e) {
    console.error('Errore caricamento catalogo:', e);
  } finally {
    loading.value = false;
  }
});

async function save() {
  saving.value = true;
  savedMsg.value = '';
  try {
    // Update order fields
    catalog.categories.forEach((cat, ci) => {
      cat.order = ci;
      cat.items.forEach((item, ii) => {
        item.order = ii;
      });
    });
    await api.put('/manager/amenities-catalog', { categories: catalog.categories });
    savedMsg.value = 'Salvato!';
    setTimeout(() => { savedMsg.value = ''; }, 3000);
  } catch (e) {
    savedMsg.value = 'Errore: ' + (e.message || 'salvataggio fallito');
  } finally {
    saving.value = false;
  }
}
</script>

<style scoped>
.olom-amenities-catalog { max-width: 900px; }
.olom-ac-cats { display: flex; flex-direction: column; gap: 16px; margin-bottom: 16px; }
.olom-ac-cat {
  background: #fff;
  border: 1px solid #E5E7EB;
  border-radius: 10px;
  overflow: hidden;
}
.olom-ac-cat-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  background: #F9FAFB;
  border-bottom: 1px solid #E5E7EB;
  gap: 10px;
}
.olom-ac-cat-left {
  display: flex;
  align-items: center;
  gap: 10px;
  flex: 1;
  min-width: 0;
}
.olom-ac-cat-name {
  border: 1px solid transparent;
  background: transparent;
  font-size: 15px;
  font-weight: 600;
  color: #1F2937;
  padding: 4px 8px;
  border-radius: 6px;
  flex: 1;
  min-width: 0;
}
.olom-ac-cat-name:hover,
.olom-ac-cat-name:focus {
  border-color: #D1D5DB;
  background: #fff;
  outline: none;
}
.olom-ac-cat-count {
  font-size: 12px;
  color: #9CA3AF;
  white-space: nowrap;
}
.olom-ac-cat-actions {
  display: flex;
  gap: 6px;
}
.olom-ac-handle {
  cursor: grab;
  color: #9CA3AF;
  font-size: 14px;
  user-select: none;
  flex-shrink: 0;
}
.olom-ac-handle:active { cursor: grabbing; }
.olom-ac-handle-sm { font-size: 12px; }
.olom-ac-items { padding: 6px 14px 10px; }
.olom-ac-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 5px 0;
  border-bottom: 1px solid #F3F4F6;
}
.olom-ac-item:last-child { border-bottom: none; }
.olom-ac-item-icon {
  font-size: 16px;
  width: 24px;
  text-align: center;
  flex-shrink: 0;
}
.olom-ac-item-name {
  border: 1px solid transparent;
  background: transparent;
  font-size: 14px;
  color: #374151;
  padding: 3px 6px;
  border-radius: 4px;
  flex: 1;
  min-width: 0;
}
.olom-ac-item-name:hover,
.olom-ac-item-name:focus {
  border-color: #D1D5DB;
  background: #fff;
  outline: none;
}
.olom-ac-item-icon-input {
  border: 1px solid transparent;
  background: transparent;
  font-size: 12px;
  color: #6B7280;
  padding: 3px 6px;
  border-radius: 4px;
  width: 140px;
  flex-shrink: 0;
}
.olom-ac-item-icon-input:hover,
.olom-ac-item-icon-input:focus {
  border-color: #D1D5DB;
  background: #fff;
  outline: none;
}
.olom-ac-item-key {
  font-size: 11px;
  color: #9CA3AF;
  font-family: monospace;
  white-space: nowrap;
  flex-shrink: 0;
}
.olom-ac-btn {
  padding: 4px 10px;
  font-size: 13px;
  border: 1px solid #D1D5DB;
  background: #fff;
  border-radius: 6px;
  cursor: pointer;
  color: #374151;
  white-space: nowrap;
}
.olom-ac-btn:hover { background: #F3F4F6; }
.olom-ac-btn-sm { padding: 3px 8px; font-size: 12px; }
.olom-ac-btn-danger { color: #DC2626; border-color: #FECACA; }
.olom-ac-btn-danger:hover { background: #FEF2F2; }
.olom-ac-btn-icon {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 16px;
  color: #9CA3AF;
  padding: 2px 4px;
  line-height: 1;
  flex-shrink: 0;
}
.olom-ac-btn-icon:hover { color: #DC2626; }
.olom-ac-empty {
  color: #9CA3AF;
  font-size: 13px;
  padding: 8px 0;
  text-align: center;
}
.olom-ac-add-cat { margin-bottom: 20px; }
</style>
