<template>
  <div class="mb-space-y-2">
    <div v-if="Array.isArray(modelValue) && modelValue.length" class="mb-space-y-2">
      <div
        v-for="(img, idx) in modelValue"
        :key="idx"
        class="mb-relative mb-group mb-flex mb-gap-2 mb-items-start"
      >
        <img
          :src="img.url"
          :alt="img.alt || ''"
          class="mb-w-16 mb-h-16 mb-object-cover mb-rounded mb-border mb-border-gray-600 mb-shrink-0"
        />
        <div class="mb-flex-1 mb-min-w-0">
          <input
            type="text"
            :value="img.caption || ''"
            @input="updateCaption(idx, $event.target.value)"
            placeholder="Didascalia..."
            class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200 mb-placeholder-gray-500"
          />
        </div>
        <button
          @click="removeImage(idx)"
          class="mb-bg-red-600 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-text-[10px] mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity mb-shrink-0"
          title="Rimuovi"
        >&times;</button>
      </div>
    </div>

    <!-- Bottoni -->
    <div class="mb-flex mb-gap-1.5">
      <button
        @click="addImages"
        class="mb-flex-1 mb-py-1.5 mb-px-2 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
      >
        Media
      </button>
      <button
        v-for="svc in services"
        :key="svc.key"
        @click="togglePanel(svc.key)"
        class="mb-flex-1 mb-py-1.5 mb-px-2 mb-rounded-md mb-text-xs mb-transition-colors mb-border"
        :class="activePanel === svc.key
          ? 'mb-bg-gray-600 mb-border-gray-500 mb-text-white'
          : 'mb-bg-gray-700 mb-border-gray-600 mb-text-gray-300 hover:mb-bg-gray-600'"
      >
        {{ svc.label }}
      </button>
    </div>

    <!-- Pannello stock photos (Unsplash / Pexels) -->
    <div v-if="activePanel" class="mb-border mb-border-gray-600 mb-rounded-lg mb-overflow-hidden">
      <!-- Barra ricerca -->
      <div class="mb-flex mb-gap-1 mb-p-2 mb-bg-gray-800">
        <input
          v-model="stockQuery"
          @keydown.enter="searchStock(1)"
          type="text"
          :placeholder="`Cerca su ${activeSvc.label}...`"
          class="mb-flex-1 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200 mb-placeholder-gray-500"
        />
        <button
          @click="searchStock(1)"
          :disabled="stockLoading || !stockQuery.trim()"
          class="mb-px-3 mb-py-1 mb-rounded-md mb-text-xs mb-text-white mb-transition-colors"
          style="background: var(--olo-color-primary, #6366F1);"
        >
          Cerca
        </button>
      </div>

      <!-- Loading -->
      <div v-if="stockLoading" class="mb-flex mb-items-center mb-justify-center mb-py-6">
        <div class="mb-w-5 mb-h-5 mb-border-2 mb-border-gray-500 mb-border-t-white mb-rounded-full mb-animate-spin"></div>
      </div>

      <!-- Risultati -->
      <div v-else-if="stockResults.length" class="mb-p-2">
        <div class="mb-grid mb-grid-cols-3 mb-gap-1.5">
          <div
            v-for="photo in stockResults"
            :key="photo.id"
            class="mb-relative mb-cursor-pointer mb-group mb-rounded mb-overflow-hidden"
            style="aspect-ratio: 1;"
            @click="downloadStockPhoto(photo)"
          >
            <img
              :src="photo.thumb"
              :alt="photo.alt"
              class="mb-w-full mb-h-full mb-object-cover"
              loading="lazy"
            />
            <div class="mb-absolute mb-inset-x-0 mb-bottom-0 mb-bg-gradient-to-t mb-from-black/70 mb-to-transparent mb-px-1 mb-pb-1 mb-pt-3 mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity">
              <span class="mb-text-[9px] mb-text-white mb-leading-tight mb-block mb-truncate">{{ photo.photographer }}</span>
            </div>
            <div
              v-if="downloadingIds.has(photo.id)"
              class="mb-absolute mb-inset-0 mb-bg-black/60 mb-flex mb-items-center mb-justify-center"
            >
              <div class="mb-w-5 mb-h-5 mb-border-2 mb-border-gray-400 mb-border-t-white mb-rounded-full mb-animate-spin"></div>
            </div>
          </div>
        </div>

        <div v-if="stockPage < stockTotalPages" class="mb-mt-2">
          <button
            @click="searchStock(stockPage + 1)"
            class="mb-w-full mb-py-1.5 mb-text-xs mb-text-gray-400 hover:mb-text-gray-200 mb-transition-colors"
          >
            Carica altre...
          </button>
        </div>

        <div class="mb-text-[9px] mb-text-gray-500 mb-mt-1 mb-text-center">
          {{ stockTotal }} risultati su {{ activeSvc.label }}
        </div>
      </div>

      <!-- Nessun risultato -->
      <div v-else-if="stockSearched" class="mb-py-4 mb-text-center mb-text-xs mb-text-gray-500">
        Nessun risultato
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { useMediaPicker } from '@/composables/useMediaPicker';

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
});
const emit = defineEmits(['update:modelValue']);

const { openGallery } = useMediaPicker();

// ── WP Media Library ──
function addImages() {
  openGallery((newImages) => {
    const current = props.modelValue || [];
    const merged = [...current, ...newImages.map(img => ({ url: img.url, alt: img.alt, id: img.id, caption: '' }))];
    emit('update:modelValue', merged);
  });
}

function removeImage(index) {
  const updated = (props.modelValue || []).filter((_, i) => i !== index);
  emit('update:modelValue', updated);
}

function updateCaption(index, value) {
  const updated = (props.modelValue || []).map((img, i) =>
    i === index ? { ...img, caption: value } : img
  );
  emit('update:modelValue', updated);
}

// ── Stock photo services ──
const services = [
  { key: 'unsplash', label: 'Unsplash', searchPath: '/unsplash/search', downloadPath: '/unsplash/download', hasTracking: true },
  { key: 'pexels', label: 'Pexels', searchPath: '/pexels/search', downloadPath: '/pexels/download', hasTracking: false },
  { key: 'pixabay', label: 'Pixabay', searchPath: '/pixabay/search', downloadPath: '/pixabay/download', hasTracking: false },
  { key: 'openverse', label: 'Openverse', searchPath: '/openverse/search', downloadPath: '/openverse/download', hasTracking: false },
];

const activePanel = ref(null);
const activeSvc = computed(() => services.find(s => s.key === activePanel.value) || services[0]);

// Per-service state
const stateMap = reactive({});
function getState(key) {
  if (!stateMap[key]) {
    stateMap[key] = { query: '', results: [], loading: false, searched: false, page: 1, total: 0, totalPages: 0 };
  }
  return stateMap[key];
}

const stockQuery = computed({
  get: () => getState(activePanel.value).query,
  set: (v) => { getState(activePanel.value).query = v; },
});
const stockResults = computed(() => getState(activePanel.value).results);
const stockLoading = computed(() => getState(activePanel.value).loading);
const stockSearched = computed(() => getState(activePanel.value).searched);
const stockPage = computed(() => getState(activePanel.value).page);
const stockTotal = computed(() => getState(activePanel.value).total);
const stockTotalPages = computed(() => getState(activePanel.value).totalPages);

const downloadingIds = reactive(new Set());

function togglePanel(key) {
  activePanel.value = activePanel.value === key ? null : key;
}

async function searchStock(page = 1) {
  const svc = activeSvc.value;
  const st = getState(svc.key);
  const q = st.query.trim();
  if (!q) return;

  st.loading = true;
  st.searched = true;

  try {
    const params = new URLSearchParams({ query: q, page, per_page: 30 });
    const resp = await fetch(`${window.oloData.restUrl}${svc.searchPath}?${params}`, {
      headers: { 'X-WP-Nonce': window.oloData.nonce },
    });
    const data = await resp.json();

    if (page === 1) {
      st.results = data.results || [];
    } else {
      st.results = [...st.results, ...(data.results || [])];
    }
    st.page = page;
    st.total = data.total || 0;
    st.totalPages = data.total_pages || 0;
  } catch (err) {
    console.error(`${svc.label} search error:`, err);
  } finally {
    st.loading = false;
  }
}

async function downloadStockPhoto(photo) {
  if (downloadingIds.has(photo.id)) return;
  downloadingIds.add(photo.id);

  const svc = activeSvc.value;

  try {
    const body = {
      photo_id: String(photo.id),
      regular_url: photo.regular,
      alt: photo.alt,
      photographer: photo.photographer,
    };
    if (svc.hasTracking && photo.download_location) {
      body.download_location = photo.download_location;
    }

    const resp = await fetch(`${window.oloData.restUrl}${svc.downloadPath}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.oloData.nonce,
      },
      body: JSON.stringify(body),
    });
    const data = await resp.json();

    if (data.id && data.url) {
      const current = props.modelValue || [];
      emit('update:modelValue', [
        ...current,
        { url: data.url, alt: data.alt || '', id: data.id, caption: data.caption || '' },
      ]);
    }
  } catch (err) {
    console.error(`${svc.label} download error:`, err);
  } finally {
    downloadingIds.delete(photo.id);
  }
}
</script>
