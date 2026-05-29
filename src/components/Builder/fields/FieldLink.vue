<template>
  <div class="mb-relative" ref="rootRef">
    <!-- Input principale -->
    <div class="mb-relative">
      <input
        ref="inputRef"
        type="text"
        :value="modelValue"
        @input="onInput"
        @focus="onFocus"
        @keydown.down.prevent="moveSel(1)"
        @keydown.up.prevent="moveSel(-1)"
        @keydown.enter.prevent="onEnter"
        @keydown.esc="closeDropdown"
        :placeholder="placeholder || t('Cerca pagine, post o incolla URL...')"
        class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-text-sm mb-text-gray-900 focus:mb-border-primary-500 mb-outline-none"
        style="padding: 6px 32px 6px 34px"
      />
      <!-- Icona link a sinistra -->
      <svg class="mb-absolute mb-top-1/2 mb-text-gray-400 mb-pointer-events-none" style="left:10px; transform:translateY(-50%)" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
      </svg>
      <!-- Pulsante clear -->
      <button
        v-if="modelValue"
        @click="clearValue"
        type="button"
        class="mb-absolute mb-top-1/2 mb-text-gray-400 hover:mb-text-gray-700 mb-flex mb-items-center mb-justify-center mb-bg-transparent mb-cursor-pointer"
        style="right:6px; transform:translateY(-50%); width:20px; height:20px; font-size:16px; line-height:1; border:0; padding:0"
        :title="t('Rimuovi link')"
      >&times;</button>
    </div>

    <!-- Pill tipo (se valore selezionato da dropdown) -->
    <div v-if="selectedMeta" class="mb-mt-1 mb-flex mb-items-center mb-gap-1.5 mb-text-xs">
      <span
        class="mb-inline-flex mb-items-center mb-gap-1 mb-px-1.5 mb-py-0.5 mb-rounded mb-bg-primary-50 mb-text-primary-700 mb-border mb-border-primary-200"
        style="font-size:10px"
      >
        <span class="mb-w-1.5 mb-h-1.5 mb-rounded-full mb-bg-primary-500"></span>
        {{ selectedMeta.type_label }}
      </span>
      <span class="mb-text-gray-500 mb-truncate" style="font-size:10px" :title="selectedMeta.title">{{ selectedMeta.title }}</span>
    </div>

    <!-- Dropdown autocomplete (positioning absolute relativo al root) -->
    <div
      v-if="open"
      ref="dropdownRef"
      class="mb-absolute mb-left-0 mb-right-0 mb-top-full mb-mt-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded-lg mb-shadow-xl mb-overflow-hidden"
      style="z-index:99999; min-width:280px"
    >
      <!-- Header -->
      <div class="mb-px-3 mb-py-1.5 mb-border-b mb-border-gray-200 mb-flex mb-items-center mb-justify-between mb-bg-gray-50" style="font-size:10px">
        <span v-if="loading" class="mb-flex mb-items-center mb-gap-1.5 mb-text-gray-500">
          <svg class="mb-animate-spin" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
          {{ t('Ricerca…') }}
        </span>
        <span v-else-if="results.length === 0" class="mb-text-gray-500">{{ t('Nessun risultato') }}</span>
        <span v-else class="mb-text-gray-500">{{ results.length }} {{ t('risultati') }}</span>
        <span class="mb-text-gray-400">{{ t('Esc per chiudere') }}</span>
      </div>

      <!-- Risultati -->
      <div class="mb-overflow-y-auto" style="max-height:320px">
        <button
          v-for="(item, i) in results"
          :key="item.type + ':' + item.subtype + ':' + item.id"
          @click.prevent="selectItem(item)"
          @mouseenter="selIdx = i"
          type="button"
          :class="[
            'mb-w-full mb-flex mb-items-center mb-gap-2 mb-px-3 mb-py-2 mb-text-left mb-border-0 mb-border-b mb-border-gray-100 mb-bg-transparent mb-cursor-pointer mb-transition-colors',
            selIdx === i ? 'mb-bg-primary-50' : 'hover:mb-bg-gray-50'
          ]"
        >
          <!-- Thumbnail / icona tipo -->
          <div class="mb-flex-shrink-0 mb-rounded mb-bg-gray-100 mb-overflow-hidden mb-flex mb-items-center mb-justify-center" style="width:36px; height:36px">
            <img v-if="item.thumbnail" :src="item.thumbnail" alt="" class="mb-w-full mb-h-full mb-object-cover" loading="lazy" />
            <svg v-else-if="item.type === 'shortcut'" class="mb-text-primary-500" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <svg v-else-if="item.type === 'term'" class="mb-text-amber-500" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41L13.42 20.58a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            <svg v-else class="mb-text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
          <!-- Testo -->
          <div class="mb-flex-1 mb-min-w-0">
            <div class="mb-flex mb-items-center mb-gap-1.5">
              <span class="mb-text-sm mb-text-gray-900 mb-font-medium mb-truncate">{{ item.title }}</span>
              <span
                class="mb-px-1 mb-py-0.5 mb-rounded mb-flex-shrink-0"
                :class="typeBadgeClass(item)"
                style="font-size:9px"
              >{{ item.type_label }}</span>
            </div>
            <div v-if="item.excerpt" class="mb-text-gray-500 mb-truncate" style="font-size:10px; margin-top:1px">{{ item.excerpt }}</div>
            <div v-else class="mb-text-gray-400 mb-truncate" style="font-size:10px; margin-top:1px">{{ item.url_relative || item.url }}</div>
          </div>
        </button>

        <!-- Fallback URL custom -->
        <button
          v-if="showCustomFallback"
          @click.prevent="selectCustomUrl"
          @mouseenter="selIdx = results.length"
          type="button"
          :class="[
            'mb-w-full mb-flex mb-items-center mb-gap-2 mb-px-3 mb-py-2 mb-text-left mb-border-0 mb-border-t mb-border-gray-200 mb-bg-transparent mb-cursor-pointer mb-transition-colors',
            selIdx === results.length ? 'mb-bg-primary-50' : 'hover:mb-bg-gray-50'
          ]"
        >
          <div class="mb-flex-shrink-0 mb-rounded mb-bg-primary-50 mb-text-primary-600 mb-flex mb-items-center mb-justify-center" style="width:36px; height:36px">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          </div>
          <div class="mb-flex-1 mb-min-w-0">
            <div class="mb-text-sm mb-text-gray-900">{{ t('Usa URL esterno') }}</div>
            <div class="mb-text-gray-500 mb-truncate" style="font-size:10px">{{ query }}</div>
          </div>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  types: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

const inputRef = ref(null);
const dropdownRef = ref(null);
const rootRef = ref(null);
const open = ref(false);
const loading = ref(false);
const results = ref([]);
const query = ref('');
const selIdx = ref(0);
const selectedMeta = ref(null);

let debounceTimer = null;
let abortCtrl = null;

const showCustomFallback = computed(() => {
  const q = (query.value || '').trim();
  if (!q) return false;
  const looksLikeUrl = /^(https?:\/\/|\/|www\.)/i.test(q) || /[a-z0-9-]+\.[a-z]{2,}/i.test(q);
  if (!looksLikeUrl) return false;
  return !results.value.some(r => r.url === q);
});

function typeBadgeClass(item) {
  if (item.type === 'shortcut') return 'mb-bg-primary-100 mb-text-primary-700';
  if (item.type === 'term') return 'mb-bg-amber-100 mb-text-amber-700';
  if (item.subtype === 'page') return 'mb-bg-blue-100 mb-text-blue-700';
  if (item.subtype === 'post') return 'mb-bg-green-100 mb-text-green-700';
  return 'mb-bg-gray-100 mb-text-gray-700';
}

function onInput(e) {
  const v = e.target.value;
  emit('update:modelValue', v);
  query.value = v;
  selectedMeta.value = null;
  scheduleSearch();
  open.value = true;
}

function onFocus() {
  query.value = props.modelValue || '';
  scheduleSearch();
  open.value = true;
}

function onEnter() {
  if (!open.value) return;
  if (selIdx.value < results.value.length) {
    selectItem(results.value[selIdx.value]);
  } else if (showCustomFallback.value) {
    selectCustomUrl();
  } else {
    closeDropdown();
  }
}

function moveSel(delta) {
  const max = results.value.length + (showCustomFallback.value ? 1 : 0) - 1;
  if (max < 0) return;
  selIdx.value = Math.max(0, Math.min(max, selIdx.value + delta));
}

function scheduleSearch() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(doSearch, 220);
}

async function doSearch() {
  if (abortCtrl) abortCtrl.abort();
  abortCtrl = new AbortController();
  loading.value = true;
  try {
    const olo = window.oloData || {};
    const params = new URLSearchParams({ q: query.value || '', per_page: '15' });
    if (props.types) params.set('types', props.types);
    const base = (olo.restUrl || '/wp-json/olo/v1').replace(/\/$/, '');
    const url = `${base}/link-search?${params.toString()}`;
    const res = await fetch(url, {
      headers: { 'X-WP-Nonce': olo.nonce || '' },
      credentials: 'same-origin',
      signal: abortCtrl.signal,
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    results.value = Array.isArray(data.results) ? data.results : [];
    selIdx.value = 0;
  } catch (e) {
    if (e.name !== 'AbortError') {
      console.warn('[FieldLink] search failed:', e);
      results.value = [];
    }
  } finally {
    loading.value = false;
  }
}

function selectItem(item) {
  // Per link interni (post/term/shortcut) salva il path relativo (portabile tra domini).
  // Per i link esterni o se manca url_relative, salva l'URL assoluto.
  const saveUrl = item.url_relative || item.url;
  emit('update:modelValue', saveUrl);
  selectedMeta.value = item;
  query.value = saveUrl;
  closeDropdown();
}

function selectCustomUrl() {
  selectedMeta.value = null;
  closeDropdown();
}

function clearValue() {
  emit('update:modelValue', '');
  query.value = '';
  selectedMeta.value = null;
  results.value = [];
  nextTick(() => inputRef.value?.focus());
}

function closeDropdown() {
  open.value = false;
}

const onDocMouseDown = (e) => {
  if (!open.value) return;
  const inRoot = rootRef.value && rootRef.value.contains(e.target);
  if (!inRoot) closeDropdown();
};

onMounted(() => {
  document.addEventListener('mousedown', onDocMouseDown);
});
onBeforeUnmount(() => {
  document.removeEventListener('mousedown', onDocMouseDown);
  if (abortCtrl) abortCtrl.abort();
});
</script>
