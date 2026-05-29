<template>
  <div class="mb-space-y-2">
    <draggable
      v-if="Array.isArray(modelValue) && modelValue.length"
      :modelValue="modelValue"
      @update:modelValue="emit('update:modelValue', $event)"
      :item-key="itemKey"
      handle=".fg-grip"
      ghost-class="fg-ghost"
      :animation="150"
      class="mb-space-y-2"
    >
      <template #item="{ element: img, index: idx }">
        <div class="mb-relative mb-group mb-flex mb-gap-2 mb-items-start">
          <!-- Grip handle -->
          <div class="fg-grip mb-flex mb-items-center mb-self-stretch mb-cursor-grab mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity mb-shrink-0 mb-text-gray-500 hover:mb-text-gray-300">
            <svg width="8" height="14" viewBox="0 0 8 14" fill="currentColor">
              <circle cx="2" cy="2" r="1.2"/><circle cx="6" cy="2" r="1.2"/>
              <circle cx="2" cy="7" r="1.2"/><circle cx="6" cy="7" r="1.2"/>
              <circle cx="2" cy="12" r="1.2"/><circle cx="6" cy="12" r="1.2"/>
            </svg>
          </div>
          <!-- Preview: immagine o video -->
          <div v-if="isVideo(img)" class="mb-relative mb-w-16 mb-h-16 mb-shrink-0 mb-rounded mb-border mb-border-gray-600 mb-overflow-hidden mb-bg-gray-800">
            <img
              v-if="img.poster"
              :src="img.poster"
              :alt="img.alt || ''"
              class="mb-w-full mb-h-full mb-object-cover"
            />
            <div v-else class="mb-w-full mb-h-full mb-flex mb-items-center mb-justify-center mb-text-gray-500 mb-text-[9px]">{{ t('Video') }}</div>
            <!-- Play icon overlay -->
            <div class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center mb-pointer-events-none">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="white" opacity="0.85"><polygon points="8,5 19,12 8,19"/></svg>
            </div>
            <!-- Badge YT/VM -->
            <span v-if="embedBadge(img)" class="mb-absolute mb-top-0.5 mb-right-0.5 mb-bg-black/70 mb-text-white mb-text-[8px] mb-px-1 mb-rounded mb-leading-tight">{{ embedBadge(img) }}</span>
          </div>
          <img
            v-else
            :src="img.url"
            :alt="img.alt || ''"
            class="mb-w-16 mb-h-16 mb-object-cover mb-rounded mb-border mb-border-gray-600 mb-shrink-0"
          />

          <div class="mb-flex-1 mb-min-w-0 mb-space-y-1">
            <input
              type="text"
              :value="img.caption || ''"
              @input="updateCaption(idx, $event.target.value)"
              :placeholder="t('Didascalia...')"
              class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200 mb-placeholder-gray-500"
            />
            <!-- Bottone poster per video -->
            <button
              v-if="isVideo(img)"
              @click="pickPoster(idx)"
              class="mb-text-[10px] mb-text-gray-400 hover:mb-text-gray-200 mb-transition-colors"
            >{{ t('Poster') }}</button>
          </div>
          <button
            @click="removeImage(idx)"
            class="mb-bg-red-600 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-text-[10px] mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity mb-shrink-0"
            :title="t('Rimuovi')"
          >{{ t('&times;') }}</button>
        </div>
      </template>
    </draggable>

    <!-- Bottoni -->
    <div class="mb-flex mb-gap-1.5 mb-flex-wrap">
      <button
        @click="addImages"
        class="mb-flex-1 mb-py-1.5 mb-px-2 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
      >
        Media
      </button>
      <button
        @click="addVideo"
        class="mb-flex-1 mb-py-1.5 mb-px-2 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
      >
        Video
      </button>
      <button
        @click="showEmbedInput = !showEmbedInput"
        class="mb-flex-1 mb-py-1.5 mb-px-2 mb-rounded-md mb-text-xs mb-transition-colors mb-border"
        :class="showEmbedInput
          ? 'mb-bg-gray-600 mb-border-gray-500 mb-text-white'
          : 'mb-bg-gray-700 mb-border-gray-600 mb-text-gray-300 hover:mb-bg-gray-600'"
      >
        YT / Vimeo
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

    <!-- Pannello embed YouTube/Vimeo -->
    <div v-if="showEmbedInput" class="mb-flex mb-gap-1 mb-items-center">
      <input
        v-model="embedUrl"
        type="text"
        :placeholder="t('URL YouTube o Vimeo...')"
        class="mb-flex-1 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-200 mb-placeholder-gray-500"
        @keydown.enter="addEmbedVideo"
      />
      <button
        @click="addEmbedVideo"
        :disabled="!embedUrl.trim()"
        class="mb-px-3 mb-py-1.5 mb-rounded-md mb-text-xs mb-text-white mb-transition-colors"
        style="background: var(--olo-color-primary, #6366F1);"
      >
        Aggiungi
      </button>
    </div>

    <!-- Pannello stock photos (Unsplash / Pexels) -->
    <div v-if="activePanel" class="mb-border mb-border-gray-600 mb-rounded-lg mb-overflow-hidden">
      <!-- Barra ricerca -->
      <div class="mb-p-2 mb-bg-gray-800 mb-space-y-1.5">
        <div class="mb-flex mb-gap-1">
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
        <!-- Toggle foto / video -->
        <div v-if="activeSvc.hasVideo" class="mb-flex mb-gap-0.5 mb-bg-gray-700 mb-rounded-md mb-p-0.5">
          <button
            @click="toggleMediaType('photo')"
            class="mb-flex-1 mb-py-0.5 mb-rounded mb-text-[10px] mb-transition-colors"
            :class="stockMediaType === 'photo' ? 'mb-bg-gray-500 mb-text-white' : 'mb-text-gray-400 hover:mb-text-gray-200'"
          >Foto</button>
          <button
            @click="toggleMediaType('video')"
            class="mb-flex-1 mb-py-0.5 mb-rounded mb-text-[10px] mb-transition-colors"
            :class="stockMediaType === 'video' ? 'mb-bg-gray-500 mb-text-white' : 'mb-text-gray-400 hover:mb-text-gray-200'"
          >Video</button>
        </div>
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
            @click="handleStockClick(photo)"
          >
            <!-- Thumbnail: video con <video> per Pixabay, immagine per il resto -->
            <video
              v-if="photo.is_video_thumb && photo.thumb"
              :src="photo.thumb + '#t=0.5'"
              preload="metadata"
              muted
              class="mb-w-full mb-h-full mb-object-cover"
            ></video>
            <img
              v-else
              :src="photo.thumb"
              :alt="photo.alt"
              class="mb-w-full mb-h-full mb-object-cover"
              loading="lazy"
            />
            <!-- Video overlay: play icon + durata -->
            <template v-if="photo.duration != null">
              <div class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center mb-pointer-events-none">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="white" opacity="0.85"><polygon points="8,5 19,12 8,19"/></svg>
              </div>
              <span class="mb-absolute mb-bottom-1 mb-right-1 mb-bg-black/70 mb-text-white mb-text-[9px] mb-px-1 mb-rounded mb-leading-tight">{{ formatDuration(photo.duration) }}</span>
            </template>
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
import { t } from '@/i18n';
import { ref, reactive, computed } from 'vue';
import draggable from 'vuedraggable';
import { useMediaPicker } from '@/composables/useMediaPicker';
import { useToast } from '@/composables/useToast';

const toast = useToast();

const YOUTUBE_RE = /(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/;
const VIMEO_RE = /vimeo\.com\/(\d+)/;

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
});
const emit = defineEmits(['update:modelValue']);

const { openGallery, openVideo, openPosterImage } = useMediaPicker();

function itemKey(item) {
  return `${item.id || 0}-${item.url || ''}-${item.embed || ''}`;
}

function isVideo(img) {
  return img && img.type === 'video';
}

function embedBadge(img) {
  if (!img || !img.embed) return '';
  if (YOUTUBE_RE.test(img.embed)) return 'YT';
  if (VIMEO_RE.test(img.embed)) return 'VM';
  return '';
}

// ── WP Media Library (immagini) ──
function addImages() {
  openGallery((newImages) => {
    const current = props.modelValue || [];
    const merged = [...current, ...newImages.map(img => ({ url: img.url, alt: img.alt, id: img.id, caption: '' }))];
    emit('update:modelValue', merged);
  });
}

// ── WP Media Library (video) ──
function addVideo() {
  openVideo((vid) => {
    const current = props.modelValue || [];
    emit('update:modelValue', [
      ...current,
      { url: vid.url, alt: vid.alt, id: vid.id, caption: '', type: 'video', poster: vid.poster || '' },
    ]);
  });
}

// ── Embed YouTube/Vimeo ──
const showEmbedInput = ref(false);
const embedUrl = ref('');

function addEmbedVideo() {
  const raw = embedUrl.value.trim();
  if (!raw) return;

  let poster = '';
  const ytMatch = raw.match(YOUTUBE_RE);
  if (ytMatch) {
    poster = 'https://img.youtube.com/vi/' + ytMatch[1] + '/hqdefault.jpg';
  }

  const current = props.modelValue || [];
  emit('update:modelValue', [
    ...current,
    { url: '', alt: '', id: 0, caption: '', type: 'video', embed: raw, poster },
  ]);

  embedUrl.value = '';
  showEmbedInput.value = false;
}

// ── Poster picker ──
function pickPoster(idx) {
  openPosterImage((result) => {
    const updated = (props.modelValue || []).map((img, i) =>
      i === idx ? { ...img, poster: result.url, poster_id: result.id } : img
    );
    emit('update:modelValue', updated);
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
  { key: 'pexels', label: 'Pexels', searchPath: '/pexels/search', downloadPath: '/pexels/download', hasTracking: false, hasVideo: true, videoSearchPath: '/pexels/videos', videoDownloadPath: '/pexels/video-download' },
  { key: 'pixabay', label: 'Pixabay', searchPath: '/pixabay/search', downloadPath: '/pixabay/download', hasTracking: false, hasVideo: true, videoSearchPath: '/pixabay/videos', videoDownloadPath: '/pixabay/video-download' },
  { key: 'openverse', label: 'Openverse', searchPath: '/openverse/search', downloadPath: '/openverse/download', hasTracking: false },
];

const preferredProvider = window.oloData?.stockmedia?.preferred || 'unsplash';
const activePanel = ref(services.find(s => s.key === preferredProvider) ? preferredProvider : null);
const activeSvc = computed(() => services.find(s => s.key === activePanel.value) || services[0]);

// Per-service state
const stateMap = reactive({});
function getState(key) {
  if (!stateMap[key]) {
    stateMap[key] = { query: '', results: [], loading: false, searched: false, page: 1, total: 0, totalPages: 0, mediaType: 'photo' };
  }
  return stateMap[key];
}

const stockMediaType = computed({
  get: () => getState(activePanel.value).mediaType,
  set: (v) => { getState(activePanel.value).mediaType = v; },
});

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

function toggleMediaType(type) {
  const st = getState(activePanel.value);
  if (st.mediaType === type) return;
  st.mediaType = type;
  st.results = [];
  st.searched = false;
  st.page = 1;
  st.total = 0;
  st.totalPages = 0;
  if (st.query.trim()) searchStock(1);
}

async function searchStock(page = 1) {
  const svc = activeSvc.value;
  const st = getState(svc.key);
  const q = st.query.trim();
  if (!q) return;

  const isVideo = st.mediaType === 'video' && svc.hasVideo;
  const searchPath = isVideo ? svc.videoSearchPath : svc.searchPath;
  const perPage = isVideo ? 15 : 30;

  st.loading = true;
  st.searched = true;

  try {
    const params = new URLSearchParams({ query: q, page, per_page: perPage });
    const resp = await fetch(`${window.oloData.restUrl}${searchPath}?${params}`, {
      headers: { 'X-WP-Nonce': window.oloData.nonce },
    });

    if (!resp.ok) {
      const errText = await resp.text().catch(() => '');
      console.error(`${svc.label} search HTTP ${resp.status}:`, errText);
      toast.error(t(`Errore ${svc.label}: ${resp.status} — ${resp.statusText}`));
      return;
    }

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
    toast.error(t('Errore nella ricerca immagini. Riprova.'));
  } finally {
    st.loading = false;
  }
}

function handleStockClick(photo) {
  const st = getState(activePanel.value);
  const svc = activeSvc.value;
  if (st.mediaType === 'video' && svc.hasVideo) {
    downloadStockVideo(photo);
  } else {
    downloadStockPhoto(photo);
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

    if (!resp.ok) {
      console.error(`${svc.label} download HTTP ${resp.status}`);
      toast.error(t(`Errore download da ${svc.label}: ${resp.status} — ${resp.statusText}`));
      return;
    }

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
    toast.error(t('Errore nel download immagine. Riprova.'));
  } finally {
    downloadingIds.delete(photo.id);
  }
}

async function downloadStockVideo(video) {
  if (downloadingIds.has(video.id)) return;
  downloadingIds.add(video.id);

  const svc = activeSvc.value;

  try {
    const body = {
      photo_id: String(video.id),
      regular_url: video.regular,
      alt: video.alt,
      photographer: video.photographer,
      thumb_url: video.thumb || '',
    };

    const resp = await fetch(`${window.oloData.restUrl}${svc.videoDownloadPath}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.oloData.nonce,
      },
      body: JSON.stringify(body),
    });

    if (!resp.ok) {
      console.error(`${svc.label} video download HTTP ${resp.status}`);
      toast.error(t(`Errore download video da ${svc.label}: ${resp.status} — ${resp.statusText}`));
      return;
    }

    const data = await resp.json();

    if (data.id && data.url) {
      const current = props.modelValue || [];
      emit('update:modelValue', [
        ...current,
        { url: data.url, alt: data.alt || '', id: data.id, caption: data.caption || '', type: 'video', poster: data.poster || '' },
      ]);
    }
  } catch (err) {
    console.error(`${svc.label} video download error:`, err);
    toast.error(t('Errore nel download video. Riprova.'));
  } finally {
    downloadingIds.delete(video.id);
  }
}

function formatDuration(sec) {
  if (!sec) return '';
  const m = Math.floor(sec / 60);
  const s = sec % 60;
  return m + ':' + String(s).padStart(2, '0');
}
</script>

<style scoped>
.fg-ghost {
  opacity: 0.4;
  border: 1px dashed #6366f1;
  border-radius: 6px;
}
.fg-grip:active {
  cursor: grabbing;
}
</style>
