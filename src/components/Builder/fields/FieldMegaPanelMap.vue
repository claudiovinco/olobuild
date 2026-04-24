<template>
  <div class="mb-space-y-2">
    <div v-if="loading" class="mb-text-xs mb-text-gray-500">{{ t('Caricamento voci menu...') }}</div>
    <div v-else-if="!menuItems.length" class="mb-text-xs mb-text-gray-500">
      {{ t('Seleziona prima un menu WordPress.') }}
    </div>
    <div
      v-for="item in menuItems"
      :key="item.id"
      class="mb-flex mb-items-center mb-gap-2 mb-bg-gray-800 mb-rounded mb-px-2 mb-py-1.5"
    >
      <span class="mb-text-xs mb-text-gray-300 mb-flex-1 mb-truncate" :title="item.title">
        {{ item.title }}
      </span>
      <select
        :value="getMapping(item.id)"
        @change="setMapping(item.id, $event.target.value)"
        class="mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-1.5 mb-py-0.5 mb-text-xs mb-text-gray-200 mb-w-36"
      >
        <option
          v-for="opt in templateOptions"
          :key="opt.value"
          :value="opt.value"
        >
          {{ opt.label }}
        </option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, watch, onMounted } from 'vue';
import { useBuilderStore } from '../../../stores/builder';
import { useTilesStore } from '../../../stores/tiles';

const props = defineProps({
  modelValue: { default: () => ({}) },
});

const emit = defineEmits(['update:modelValue']);

const builderStore = useBuilderStore();
const tilesStore = useTilesStore();
const oloData = window.oloData || {};

const menuItems = ref([]);
const templateOptions = ref([{ value: 0, label: 'Auto (colonne)' }]);
const loading = ref(false);

// Get current menu_id from the selected tile's settings
function getCurrentMenuId() {
  const tileId = builderStore.selectedTileId;
  if (!tileId) return 0;
  const tile = tilesStore.getTileById(tileId);
  return tile?.settings?.menu_id || 0;
}

function getMapping(itemId) {
  const map = props.modelValue || {};
  return map[String(itemId)] || 0;
}

function setMapping(itemId, value) {
  const map = { ...(props.modelValue || {}) };
  const v = parseInt(value) || 0;
  if (v === 0) {
    delete map[String(itemId)];
  } else {
    map[String(itemId)] = v;
  }
  emit('update:modelValue', map);
}

async function fetchMenuItems(menuId) {
  if (!menuId) {
    menuItems.value = [];
    return;
  }
  loading.value = true;
  try {
    const res = await fetch(`${oloData.restUrl}/menu-items/${menuId}`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      menuItems.value = await res.json();
    } else {
      menuItems.value = [];
    }
  } catch (e) {
    console.error('fetchMenuItems error:', e);
    menuItems.value = [];
  } finally {
    loading.value = false;
  }
}

async function fetchMegapanelTemplates() {
  try {
    const res = await fetch(`${oloData.restUrl}/templates?type=megapanel&status=published&per_page=100`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      const data = await res.json();
      const items = data.items || [];
      templateOptions.value = [
        { value: 0, label: 'Auto (colonne)' },
        ...items.map(t => ({ value: t.id, label: t.title })),
      ];
    }
  } catch (e) {
    console.error('fetchMegapanelTemplates error:', e);
  }
}

// Watch for menu_id changes by polling the tile settings
watch(
  () => getCurrentMenuId(),
  (newId) => { fetchMenuItems(newId); },
  { immediate: true }
);

onMounted(() => {
  fetchMegapanelTemplates();
  const menuId = getCurrentMenuId();
  if (menuId) fetchMenuItems(menuId);
});
</script>
