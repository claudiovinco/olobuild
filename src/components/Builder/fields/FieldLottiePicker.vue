<template>
  <div class="mb-space-y-2">
    <!-- Preview animazione corrente -->
    <div v-if="modelValue" class="mb-relative mb-group">
      <div
        ref="previewContainer"
        class="mb-w-full mb-h-28 mb-rounded-md mb-border mb-border-gray-600 mb-bg-gray-900 mb-flex mb-items-center mb-justify-center mb-overflow-hidden"
      ></div>
      <div class="mb-text-[10px] mb-text-gray-500 mb-mt-1 mb-truncate" :title="modelValue">{{ fileName }}</div>
      <button
        @click="$emit('update:modelValue', '')"
        class="mb-absolute mb-top-1 mb-right-1 mb-bg-red-600 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-text-xs mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity"
        :title="t('Rimuovi')"
      >{{ t('&times;') }}</button>
    </div>

    <!-- Buttons -->
    <div class="mb-flex mb-gap-2">
      <button
        @click="pickFromMedia"
        class="mb-flex-1 mb-py-1.5 mb-px-2 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
      >
        {{ t('Media Library') }}
      </button>
      <button
        @click="showBrowser = true"
        class="mb-flex-1 mb-py-1.5 mb-px-2 mb-bg-purple-700 mb-border mb-border-purple-600 mb-rounded-md mb-text-xs mb-text-white hover:mb-bg-purple-600 mb-transition-colors"
      >
        {{ t('Sfoglia LottieFiles') }}
      </button>
    </div>

    <!-- URL manuale -->
    <input
      type="text"
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      placeholder="https://lottie.host/.../animation.json"
      class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-xs mb-text-gray-200"
    />

    <!-- ====== LottieFiles Browser Modal ====== -->
    <Teleport to="body">
      <transition name="fade">
        <div
          v-if="showBrowser"
          class="mb-fixed mb-inset-0 mb-z-[10001] mb-flex mb-items-center mb-justify-center"
          style="background:rgba(0,0,0,0.7)"
          @click.self="showBrowser = false"
        >
          <div
            class="mb-bg-gray-800 mb-border mb-border-gray-600 mb-rounded-xl mb-shadow-2xl mb-w-[800px] mb-max-h-[85vh] mb-flex mb-flex-col mb-overflow-hidden"
            @click.stop
          >
            <!-- Header -->
            <div class="mb-flex mb-items-center mb-justify-between mb-px-5 mb-py-3 mb-border-b mb-border-gray-700">
              <h3 class="mb-text-white mb-text-sm mb-font-semibold mb-m-0">Libreria Lottie Animations<span v-if="totalCount" class="mb-text-gray-500 mb-font-normal mb-ml-2">({{ totalCount.toLocaleString() }})</span></h3>
              <button @click="showBrowser = false" class="mb-text-gray-400 hover:mb-text-white">{{ t('&times;') }}</button>
            </div>

            <!-- Search -->
            <div class="mb-px-5 mb-py-3 mb-border-b mb-border-gray-700">
              <div class="mb-flex mb-gap-2">
                <input
                  v-model="searchQuery"
                  @keydown.enter="searchAnimations"
                  type="text"
                  :placeholder="t('Cerca animazioni... (es. loading, success, arrow, heart)')"
                  class="mb-flex-1 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-lg mb-px-3 mb-py-2 mb-text-sm mb-text-gray-200 mb-outline-none focus:mb-border-purple-500"
                />
                <button
                  @click="searchAnimations"
                  :disabled="searching"
                  class="mb-px-4 mb-py-2 mb-bg-purple-600 mb-text-white mb-text-sm mb-rounded-lg hover:mb-bg-purple-500 disabled:mb-opacity-50 mb-transition-colors"
                >
                  {{ searching ? '...' : 'Cerca' }}
                </button>
              </div>
              <!-- Quick categories -->
              <div class="mb-flex mb-flex-wrap mb-gap-1.5 mb-mt-2">
                <button
                  v-for="cat in categories"
                  :key="cat.query"
                  @click="searchQuery = cat.query; searchAnimations()"
                  :class="[
                    'mb-px-2.5 mb-py-1 mb-text-[10px] mb-rounded-full mb-border mb-transition-colors',
                    searchQuery === cat.query
                      ? 'mb-bg-purple-600 mb-border-purple-500 mb-text-white'
                      : 'mb-border-gray-600 mb-text-gray-400 hover:mb-border-purple-500 hover:mb-text-gray-200'
                  ]"
                >
                  {{ cat.label }}
                </button>
              </div>
            </div>

            <!-- Results grid -->
            <div class="mb-flex-1 mb-overflow-y-auto mb-p-5">
              <div v-if="searching" class="mb-flex mb-items-center mb-justify-center mb-py-12">
                <svg class="mb-animate-spin mb-text-purple-400" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                <span class="mb-ml-3 mb-text-gray-400 mb-text-sm">{{ t('Ricerca in corso...') }}</span>
              </div>

              <div v-else-if="browserError" class="mb-text-center mb-py-12">
                <p class="mb-text-red-400 mb-text-sm">{{ browserError }}</p>
              </div>

              <div v-else-if="animations.length === 0 && hasSearched" class="mb-text-center mb-py-12">
                <p class="mb-text-gray-500 mb-text-sm">{{ t('Nessun risultato trovato. Prova un altro termine.') }}</p>
              </div>

              <div v-else-if="animations.length === 0" class="mb-text-center mb-py-12">
                <div class="mb-text-4xl mb-mb-3">&#127916;</div>
                <p class="mb-text-gray-400 mb-text-sm">{{ t('Cerca un\'animazione o scegli una categoria') }}</p>
                <p class="mb-text-gray-600 mb-text-xs mb-mt-1">{{ t('Le animazioni sono fornite da LottieFiles.com (Creative Commons)') }}</p>
              </div>

              <div v-else class="mb-grid mb-grid-cols-4 mb-gap-3">
                <div
                  v-for="anim in animations"
                  :key="anim.id"
                  @click="selectAnimation(anim)"
                  class="mb-group mb-cursor-pointer mb-bg-gray-900 mb-border mb-border-gray-700 mb-rounded-lg mb-overflow-hidden hover:mb-border-purple-500 mb-transition-all hover:mb-shadow-lg hover:mb-shadow-purple-500/10"
                >
                  <div class="mb-w-full mb-aspect-square mb-bg-white mb-flex mb-items-center mb-justify-center mb-p-2">
                    <img
                      v-if="anim.preview"
                      :src="anim.preview"
                      alt=""
                      class="mb-max-w-full mb-max-h-full mb-object-contain"
                      loading="lazy"
                    />
                    <div v-else class="mb-text-3xl">&#127916;</div>
                  </div>
                  <div class="mb-px-2 mb-py-1.5">
                    <div class="mb-text-[10px] mb-text-gray-400 mb-truncate" :title="anim.name">{{ anim.name }}</div>
                  </div>
                </div>
              </div>

              <!-- Load more -->
              <div v-if="animations.length > 0 && hasMore" class="mb-text-center mb-mt-4">
                <button
                  @click="loadMore"
                  :disabled="searching"
                  class="mb-px-4 mb-py-2 mb-bg-gray-700 mb-text-gray-300 mb-text-xs mb-rounded-lg hover:mb-bg-gray-600 disabled:mb-opacity-50 mb-transition-colors"
                >
                  {{ t('Carica altri') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { useToast } from '../../../composables/useToast.js';

const toast = useToast();

const props = defineProps({
  modelValue: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

// ─── Preview ───
const previewContainer = ref(null);
let previewAnim = null;

const fileName = computed(() => {
  if (!props.modelValue) return '';
  return props.modelValue.split('/').pop().split('?')[0];
});

async function loadPreview() {
  if (previewAnim) { previewAnim.destroy(); previewAnim = null; }
  if (!props.modelValue || !previewContainer.value) return;

  await ensureLottieLib();
  previewAnim = window.lottie.loadAnimation({
    container: previewContainer.value,
    renderer: 'svg',
    loop: true,
    autoplay: true,
    path: props.modelValue,
  });
}

async function ensureLottieLib() {
  if (window.lottie) return;
  const script = document.createElement('script');
  script.src = (window.oloData?.pluginUrl || '/wp-content/plugins/olobuild/') + 'assets/vendor/lottie/lottie.min.js';
  document.head.appendChild(script);
  await new Promise(r => script.onload = r);
}

watch(() => props.modelValue, () => nextTick(loadPreview));
onMounted(() => { if (props.modelValue) nextTick(loadPreview); });
onBeforeUnmount(() => { if (previewAnim) previewAnim.destroy(); });

// ─── Media Library ───
function pickFromMedia() {
  if (!window.wp || !window.wp.media) {
    toast.error(t('Libreria Media di WordPress non disponibile.'));
    return;
  }
  const frame = wp.media({
    title: 'Seleziona file Lottie (.json)',
    button: { text: 'Usa questa animazione' },
    multiple: false,
    library: { type: 'application/json' },
  });
  frame.on('select', () => {
    const attachment = frame.state().get('selection').first().toJSON();
    emit('update:modelValue', attachment.url);
  });
  frame.open();
}

// ─── LottieFiles Browser ───
const showBrowser = ref(false);
const searchQuery = ref('');
const searching = ref(false);
const hasSearched = ref(false);
const browserError = ref('');
const animations = ref([]);
const endCursor = ref('');
const hasMore = ref(false);
const totalCount = ref(0);

const categories = [
  { label: 'Loading', query: 'loading' },
  { label: 'Success', query: 'success' },
  { label: 'Error', query: 'error' },
  { label: 'Arrow', query: 'arrow' },
  { label: 'Heart', query: 'heart' },
  { label: 'Check', query: 'check' },
  { label: 'Star', query: 'star' },
  { label: 'Rocket', query: 'rocket' },
  { label: 'Bell', query: 'bell notification' },
  { label: 'Social', query: 'social media' },
  { label: 'Weather', query: 'weather' },
  { label: 'Business', query: 'business' },
  { label: 'E-commerce', query: 'shopping cart' },
  { label: 'Avatar', query: 'avatar person' },
  { label: 'Scroll', query: 'scroll down' },
  { label: 'Hamburger', query: 'hamburger menu' },
];

async function searchAnimations() {
  if (!searchQuery.value.trim()) return;
  endCursor.value = '';
  animations.value = [];
  await fetchAnimations();
}

async function loadMore() {
  await fetchAnimations(true);
}

async function fetchAnimations(append = false) {
  searching.value = true;
  browserError.value = '';
  hasSearched.value = true;

  try {
    const olo = window.oloData || {};
    const res = await fetch(olo.restUrl + '/lottie/search', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': olo.nonce,
      },
      body: JSON.stringify({
        query: searchQuery.value,
        cursor: append ? endCursor.value : '',
      }),
    });

    const data = await res.json();

    if (!res.ok) {
      throw new Error(data.message || 'Errore nella ricerca');
    }

    if (append) {
      animations.value = [...animations.value, ...data.results];
    } else {
      animations.value = data.results;
    }
    hasMore.value = data.has_more || false;
    endCursor.value = data.end_cursor || '';
    totalCount.value = data.total || 0;
  } catch (e) {
    browserError.value = e.message;
  } finally {
    searching.value = false;
  }
}

function selectAnimation(anim) {
  emit('update:modelValue', anim.url);
  showBrowser.value = false;
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
