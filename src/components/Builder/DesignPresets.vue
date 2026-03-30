<template>
  <div class="mb-space-y-3">
    <!-- Header -->
    <div class="mb-flex mb-items-center mb-justify-between">
      <h4 class="mb-text-xs mb-font-semibold mb-text-gray-300 mb-uppercase mb-tracking-wider">
        Design Presets
      </h4>
      <button
        v-if="builderStore.selectedTileId"
        @click="showSaveDialog = true"
        class="mb-text-[10px] mb-px-2 mb-py-1 mb-bg-primary-600 mb-text-white mb-rounded hover:mb-bg-primary-500 mb-transition-colors"
        title="Salva stile corrente come preset"
      >
        + Salva
      </button>
    </div>

    <!-- Built-in presets -->
    <div v-if="builtinPresets.length > 0" class="mb-space-y-1 mb-mb-3">
      <h5 class="mb-text-[10px] mb-font-semibold mb-text-gray-500 mb-uppercase mb-tracking-wider mb-mb-1">Preset integrati</h5>
      <div class="mb-grid mb-grid-cols-2 mb-gap-1">
        <button
          v-for="bp in builtinPresets"
          :key="bp.name"
          @click="applyBuiltinPreset(bp)"
          :disabled="!builderStore.selectedTileId"
          class="mb-text-[10px] mb-py-1.5 mb-px-2 mb-rounded mb-border mb-border-gray-600 mb-text-gray-300 hover:mb-border-primary-500 hover:mb-text-primary-300 mb-transition-colors mb-truncate disabled:mb-opacity-40 disabled:mb-cursor-not-allowed"
          :title="bp.name"
        >
          <span class="mb-inline-block mb-w-2 mb-h-2 mb-rounded-full mb-mr-1" :style="{ background: bp.colors?.primary || '#6366F1' }"></span>
          {{ bp.name }}
        </button>
      </div>
    </div>

    <!-- No presets message -->
    <div v-if="presets.length === 0 && !loading" class="mb-text-center mb-py-6">
      <div class="mb-text-3xl mb-mb-2" style="opacity:0.4;">&#x1F3A8;</div>
      <p class="mb-text-xs mb-text-gray-500">Nessun preset salvato</p>
      <p class="mb-text-[10px] mb-text-gray-600 mb-mt-1">Seleziona una tile e salva il suo stile</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="mb-text-center mb-py-4">
      <span class="mb-text-xs mb-text-gray-500">Caricamento...</span>
    </div>

    <!-- Preset cards -->
    <div v-if="presets.length > 0" class="mb-space-y-2">
      <div
        v-for="preset in presets"
        :key="preset.id"
        class="mb-bg-gray-700 mb-rounded-lg mb-p-3 mb-border mb-border-gray-600 hover:mb-border-gray-500 mb-transition-colors"
      >
        <!-- Top row: swatches + name -->
        <div class="mb-flex mb-items-center mb-gap-2 mb-mb-2">
          <!-- Color swatches -->
          <div class="mb-flex mb-gap-1 mb-shrink-0">
            <div
              :style="swatchStyle(preset.style, 'bg')"
              class="mb-w-4 mb-h-4 mb-rounded mb-border mb-border-gray-500"
              title="Background"
            ></div>
            <div
              :style="swatchStyle(preset.style, 'border')"
              class="mb-w-4 mb-h-4 mb-rounded mb-border mb-border-gray-500"
              title="Bordo"
            ></div>
          </div>
          <!-- Editable name -->
          <input
            v-if="editingId === preset.id"
            ref="nameInput"
            v-model="editingName"
            @blur="savePresetName(preset.id)"
            @keydown.enter="savePresetName(preset.id)"
            @keydown.escape="editingId = null"
            class="mb-flex-1 mb-text-xs mb-bg-gray-600 mb-text-gray-200 mb-px-2 mb-py-0.5 mb-rounded mb-border mb-border-gray-500 mb-outline-none focus:mb-border-primary-500"
          />
          <span
            v-else
            @dblclick="startEditName(preset)"
            class="mb-flex-1 mb-text-xs mb-text-gray-300 mb-truncate mb-cursor-pointer"
            title="Doppio click per rinominare"
          >
            {{ preset.name }}
          </span>
        </div>

        <!-- Style summary -->
        <div class="mb-text-[10px] mb-text-gray-500 mb-mb-2">
          <span v-if="preset.style.padding_top || preset.style.padding_bottom">P</span>
          <span v-if="preset.style.margin_top || preset.style.margin_bottom"> M</span>
          <span v-if="preset.style.bg_color || preset.style.bg_type"> BG</span>
          <span v-if="preset.style.border_width && parseInt(preset.style.border_width) > 0"> B</span>
          <span v-if="preset.style.shadow && preset.style.shadow !== 'none'"> S</span>
        </div>

        <!-- Actions -->
        <div class="mb-flex mb-gap-1">
          <button
            v-if="builderStore.selectedTileId"
            @click="applyPreset(preset)"
            class="mb-flex-1 mb-text-[10px] mb-py-1 mb-bg-primary-600 mb-text-white mb-rounded hover:mb-bg-primary-500 mb-transition-colors"
          >
            Applica
          </button>
          <button
            @click="confirmDelete(preset)"
            class="mb-px-2 mb-py-1 mb-text-[10px] mb-text-red-400 mb-bg-gray-600 mb-rounded hover:mb-bg-red-600 hover:mb-text-white mb-transition-colors"
          >
            Elimina
          </button>
        </div>
      </div>
    </div>

    <!-- Save dialog overlay -->
    <div
      v-if="showSaveDialog"
      class="mb-fixed mb-inset-0 mb-z-50 mb-flex mb-items-center mb-justify-center"
      style="background: rgba(0,0,0,0.6);"
      @click.self="showSaveDialog = false"
    >
      <div class="mb-bg-gray-800 mb-rounded-xl mb-p-5 mb-w-72 mb-border mb-border-gray-600 mb-shadow-xl">
        <h4 class="mb-text-sm mb-font-semibold mb-text-gray-200 mb-mb-3">Salva Design Preset</h4>
        <input
          ref="saveNameInput"
          v-model="newPresetName"
          placeholder="Nome preset..."
          @keydown.enter="saveCurrentStyle"
          class="mb-w-full mb-text-sm mb-bg-gray-700 mb-text-gray-200 mb-px-3 mb-py-2 mb-rounded-lg mb-border mb-border-gray-600 mb-outline-none focus:mb-border-primary-500 mb-mb-3"
        />
        <div class="mb-flex mb-gap-2">
          <button
            @click="showSaveDialog = false"
            class="mb-flex-1 mb-py-2 mb-text-xs mb-text-gray-400 mb-bg-gray-700 mb-rounded-lg hover:mb-bg-gray-600 mb-transition-colors"
          >
            Annulla
          </button>
          <button
            @click="saveCurrentStyle"
            :disabled="!newPresetName.trim()"
            class="mb-flex-1 mb-py-2 mb-text-xs mb-text-white mb-bg-primary-600 mb-rounded-lg hover:mb-bg-primary-500 mb-transition-colors disabled:mb-opacity-40"
          >
            Salva
          </button>
        </div>
      </div>
    </div>

    <!-- Delete confirm dialog -->
    <div
      v-if="deleteTarget"
      class="mb-fixed mb-inset-0 mb-z-50 mb-flex mb-items-center mb-justify-center"
      style="background: rgba(0,0,0,0.6);"
      @click.self="deleteTarget = null"
    >
      <div class="mb-bg-gray-800 mb-rounded-xl mb-p-5 mb-w-72 mb-border mb-border-gray-600 mb-shadow-xl">
        <h4 class="mb-text-sm mb-font-semibold mb-text-gray-200 mb-mb-2">Elimina preset</h4>
        <p class="mb-text-xs mb-text-gray-400 mb-mb-4">
          Vuoi eliminare il preset "{{ deleteTarget.name }}"?
        </p>
        <div class="mb-flex mb-gap-2">
          <button
            @click="deleteTarget = null"
            class="mb-flex-1 mb-py-2 mb-text-xs mb-text-gray-400 mb-bg-gray-700 mb-rounded-lg hover:mb-bg-gray-600 mb-transition-colors"
          >
            Annulla
          </button>
          <button
            @click="doDelete"
            class="mb-flex-1 mb-py-2 mb-text-xs mb-text-white mb-bg-red-600 mb-rounded-lg hover:mb-bg-red-500 mb-transition-colors"
          >
            Elimina
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { useBuilderStore } from '@/stores/builder';

const tilesStore = useTilesStore();
const builderStore = useBuilderStore();

const oloData = window.oloData || {};

const presets = ref([]);
const builtinPresets = ref([]);
const loading = ref(false);
const showSaveDialog = ref(false);
const newPresetName = ref('');
const editingId = ref(null);
const editingName = ref('');
const deleteTarget = ref(null);
const saveNameInput = ref(null);
const nameInput = ref(null);

// ── Fetch presets ──
async function fetchPresets() {
  loading.value = true;
  try {
    const res = await fetch(`${oloData.restUrl}/design-presets`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      presets.value = await res.json();
    }
  } catch (err) {
    console.error('fetchPresets error:', err);
  } finally {
    loading.value = false;
  }
}

// ── Save current style as preset ──
async function saveCurrentStyle() {
  const name = newPresetName.value.trim();
  if (!name) return;

  const tileId = builderStore.selectedTileId;
  if (!tileId) return;

  const tile = tilesStore.getTileById(tileId);
  if (!tile) return;

  const styleObj = JSON.parse(JSON.stringify(tile.style || {}));

  try {
    const res = await fetch(`${oloData.restUrl}/design-presets`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({ name, style: styleObj }),
    });
    if (res.ok) {
      await fetchPresets();
      showSaveDialog.value = false;
      newPresetName.value = '';
    }
  } catch (err) {
    console.error('saveCurrentStyle error:', err);
  }
}

// ── Apply preset to selected tile ──
function applyPreset(preset) {
  const tileId = builderStore.selectedTileId;
  if (!tileId) return;
  tilesStore.applyStylePreset(tileId, preset.style);
  builderStore.isDirty = true;
}

// ── Edit name ──
function startEditName(preset) {
  editingId.value = preset.id;
  editingName.value = preset.name;
  nextTick(() => {
    if (nameInput.value) {
      const el = Array.isArray(nameInput.value) ? nameInput.value[0] : nameInput.value;
      if (el) el.focus();
    }
  });
}

async function savePresetName(presetId) {
  const name = editingName.value.trim();
  if (!name) {
    editingId.value = null;
    return;
  }
  try {
    await fetch(`${oloData.restUrl}/design-presets/${presetId}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify({ name }),
    });
    await fetchPresets();
  } catch (err) {
    console.error('savePresetName error:', err);
  }
  editingId.value = null;
}

// ── Delete preset ──
function confirmDelete(preset) {
  deleteTarget.value = preset;
}

async function doDelete() {
  if (!deleteTarget.value) return;
  try {
    await fetch(`${oloData.restUrl}/design-presets/${deleteTarget.value.id}`, {
      method: 'DELETE',
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    await fetchPresets();
  } catch (err) {
    console.error('deletePreset error:', err);
  }
  deleteTarget.value = null;
}

// ── Color swatch helper ──
function swatchStyle(style, type) {
  if (type === 'bg') {
    const color = style.bg_color || 'transparent';
    return { background: color };
  }
  if (type === 'border') {
    const color = style.border_color || 'transparent';
    return { background: color };
  }
  return { background: 'transparent' };
}

// ── Apply built-in preset (applies colors to global style system) ──
function applyBuiltinPreset(bp) {
  if (!bp.colors) return;
  // Apply as tile style overrides
  const tileId = builderStore.selectedTileId;
  if (!tileId) return;
  const styleUpdate = {};
  if (bp.colors.primary) styleUpdate.bg_color = bp.colors.primary;
  if (bp.colors.text) styleUpdate.text_color = bp.colors.text;
  tilesStore.applyStylePreset(tileId, styleUpdate);
  builderStore.isDirty = true;
}

// ── Fetch built-in presets ──
async function fetchBuiltinPresets() {
  try {
    const res = await fetch(`${oloData.restUrl}/design-presets/builtin`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      builtinPresets.value = await res.json();
    }
  } catch (err) {
    console.error('fetchBuiltinPresets error:', err);
  }
}

// ── Init ──
onMounted(() => {
  fetchPresets();
  fetchBuiltinPresets();
});
</script>
