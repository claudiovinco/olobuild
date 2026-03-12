<template>
  <div class="mb-space-y-4">
    <!-- Header -->
    <div class="mb-flex mb-items-center mb-justify-between">
      <h3 class="mb-text-sm mb-font-semibold mb-text-gray-200">Impostazioni Pagina</h3>
      <button
        @click="builderStore.togglePageSettings()"
        class="mb-text-gray-500 hover:mb-text-gray-300 mb-text-lg"
      >
        &times;
      </button>
    </div>

    <!-- Single Post Type selector (only for single templates) -->
    <div v-if="isSingleTemplate">
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Post Type</label>
      <select
        :value="pageSettings.single_post_type || ''"
        @change="builderStore.updatePageSetting('single_post_type', $event.target.value)"
        class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
      >
        <option value="" disabled>Seleziona post type...</option>
        <option v-for="pt in postTypes" :key="pt.value" :value="pt.value">{{ pt.label }}</option>
      </select>
    </div>

    <!-- Separator (single) -->
    <div v-if="isSingleTemplate" class="mb-border-t mb-border-gray-700"></div>

    <!-- Layout -->
    <div>
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Larghezza max contenuto</label>
      <select
        :value="pageSettings.content_max_width"
        @change="builderStore.updatePageSetting('content_max_width', parseInt($event.target.value))"
        class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
      >
        <option :value="960">960px (Stretto)</option>
        <option :value="1200">1200px (Predefinito)</option>
        <option :value="1400">1400px (Largo)</option>
        <option :value="9999">Larghezza piena</option>
      </select>
    </div>

    <!-- Separator -->
    <div class="mb-border-t mb-border-gray-700"></div>

    <!-- Breakpoints Responsive -->
    <div>
      <button
        @click="showBreakpoints = !showBreakpoints"
        class="mb-flex mb-items-center mb-justify-between mb-w-full mb-text-left"
      >
        <span class="mb-text-xs mb-font-semibold mb-text-gray-300">Breakpoint Responsive</span>
        <svg
          :class="['mb-text-gray-500 mb-transition-transform', showBreakpoints ? 'mb-rotate-180' : '']"
          width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        ><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div v-if="showBreakpoints" class="mb-mt-3 mb-space-y-2">
        <p class="mb-text-[10px] mb-text-gray-500 mb-mb-2">Larghezze (px) delle media query per ogni dispositivo.</p>
        <div v-for="bp in breakpointDefs" :key="bp.key" class="mb-flex mb-items-center mb-gap-2">
          <span class="mb-text-[10px] mb-text-gray-400 mb-w-20 mb-shrink-0" :title="bp.hint">{{ bp.label }}</span>
          <input
            type="number"
            :value="bp.key === 'desktop' ? (pageSettings.content_max_width || bp.default) : (pageSettings.breakpoints?.[bp.key] ?? bp.default)"
            @change="updateBreakpoint(bp.key, parseInt($event.target.value) || bp.default)"
            min="320" max="2560" step="10"
            class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900 mb-w-0"
          />
          <span class="mb-text-[10px] mb-text-gray-500">px</span>
        </div>
        <button
          @click="resetBreakpoints"
          class="mb-text-[10px] mb-text-gray-500 hover:mb-text-gray-300 mb-mt-1"
        >Ripristina predefiniti</button>
      </div>
    </div>

    <!-- Separator -->
    <div class="mb-border-t mb-border-gray-700"></div>

    <!-- Background -->
    <div>
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Sfondo pagina</label>
      <BackgroundControls
        :modelValue="pageSettings.page_bg"
        :showParallax="true"
        @update:modelValue="onBgUpdate"
      />
    </div>

    <!-- Separator -->
    <div class="mb-border-t mb-border-gray-700"></div>

    <!-- Scroll flash settings -->
    <div>
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-1">Evidenziazione scroll</label>
      <p class="mb-text-[10px] mb-text-gray-500 mb-mb-3">Effetto visivo quando selezioni un tile dalla Struttura.</p>

      <!-- Effect type -->
      <div class="mb-mb-3">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Tipo effetto</label>
        <select
          :value="sf.effect"
          @change="updateSf('effect', $event.target.value)"
          class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
        >
          <option value="flash">Flash (singolo)</option>
          <option value="pulse">Pulse (ripetuto)</option>
        </select>
      </div>

      <!-- Color -->
      <div class="mb-mb-3">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Colore</label>
        <div class="mb-flex mb-items-center mb-gap-2">
          <input type="color" :value="sf.color" @input="updateSf('color', $event.target.value)" class="mb-w-8 mb-h-8 mb-rounded mb-cursor-pointer mb-border-0" />
          <input type="text" :value="sf.color" @change="updateSf('color', $event.target.value)" class="mb-flex-1 mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1 mb-text-sm mb-text-gray-900" />
        </div>
      </div>

      <!-- Size -->
      <div class="mb-mb-3">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Dimensione effetto</label>
        <div class="mb-flex mb-items-center mb-gap-2">
          <input type="range" :value="sf.size" @input="updateSf('size', parseInt($event.target.value))" min="2" max="20" step="1" class="mb-flex-1" />
          <span class="mb-text-[10px] mb-text-gray-400 mb-w-8 mb-text-right">{{ sf.size }}px</span>
        </div>
      </div>

      <!-- Duration -->
      <div class="mb-mb-3">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Durata effetto</label>
        <div class="mb-flex mb-items-center mb-gap-2">
          <input type="range" :value="sf.duration" @input="updateSf('duration', parseInt($event.target.value))" min="300" max="3000" step="100" class="mb-flex-1" />
          <span class="mb-text-[10px] mb-text-gray-400 mb-w-12 mb-text-right">{{ sf.duration }}ms</span>
        </div>
      </div>

      <!-- Pulse count -->
      <div v-if="sf.effect === 'pulse'" class="mb-mb-3">
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Ripetizioni pulse</label>
        <div class="mb-flex mb-items-center mb-gap-2">
          <input type="range" :value="sf.pulse_count" @input="updateSf('pulse_count', parseInt($event.target.value))" min="1" max="6" step="1" class="mb-flex-1" />
          <span class="mb-text-[10px] mb-text-gray-400 mb-w-6 mb-text-right">{{ sf.pulse_count }}x</span>
        </div>
      </div>

      <!-- Scroll speed -->
      <div>
        <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-1">Velocit&agrave; scorrimento</label>
        <div class="mb-flex mb-items-center mb-gap-2">
          <input type="range" :value="sf.scroll_ms" @input="updateSf('scroll_ms', parseInt($event.target.value))" min="0" max="1500" step="50" class="mb-flex-1" />
          <span class="mb-text-[10px] mb-text-gray-400 mb-w-12 mb-text-right">{{ sf.scroll_ms === 0 ? 'Istant.' : sf.scroll_ms + 'ms' }}</span>
        </div>
      </div>
    </div>

    <!-- Separator -->
    <div class="mb-border-t mb-border-gray-700"></div>

    <!-- AI Settings (collapsible) -->
    <div>
      <button
        @click="showAiSettings = !showAiSettings"
        class="mb-flex mb-items-center mb-justify-between mb-w-full mb-text-left"
      >
        <span class="mb-text-xs mb-font-semibold mb-text-gray-300">Impostazioni AI</span>
        <svg
          :class="['mb-text-gray-500 mb-transition-transform', showAiSettings ? 'mb-rotate-180' : '']"
          width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        ><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div v-if="showAiSettings" class="mb-mt-3">
        <AISettingsPanel />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import BackgroundControls from './BackgroundControls.vue';
import AISettingsPanel from './AISettingsPanel.vue';
import { loadScrollFlashPrefs, saveScrollFlashPrefs } from '@/utils/scrollFlashPrefs';

const showAiSettings = ref(false);
const showBreakpoints = ref(false);

const breakpointDefs = [
  { key: 'widescreen', label: 'Widescreen', hint: 'min-width (schermi grandi)', default: 1400 },
  { key: 'desktop', label: 'Desktop', hint: 'Larghezza massima contenuto desktop', default: 1200 },
  { key: 'tablet_landscape', label: 'Tablet L', hint: 'max-width tablet orizzontale', default: 1200 },
  { key: 'tablet', label: 'Tablet', hint: 'max-width tablet verticale', default: 960 },
  { key: 'mobile_landscape', label: 'Mobile L', hint: 'max-width mobile orizzontale', default: 640 },
  { key: 'mobile', label: 'Mobile', hint: 'max-width mobile verticale', default: 480 },
];

function updateBreakpoint(key, value) {
  if (key === 'desktop') {
    builderStore.updatePageSetting('content_max_width', value);
    return;
  }
  const current = { ...(builderStore.pageSettings.breakpoints || {}) };
  current[key] = value;
  builderStore.updatePageSetting('breakpoints', current);
}

function resetBreakpoints() {
  builderStore.updatePageSetting('content_max_width', 1200);
  builderStore.updatePageSetting('breakpoints', {
    widescreen: 1400,
    tablet_landscape: 1200,
    tablet: 960,
    mobile_landscape: 640,
    mobile: 480,
  });
}

const builderStore = useBuilderStore();
const pageSettings = computed(() => builderStore.pageSettings);
const sf = reactive(loadScrollFlashPrefs());

function updateSf(key, value) {
  sf[key] = value;
  saveScrollFlashPrefs(sf);
}
const oloData = window.oloData || {};
const postTypes = oloData.postTypes || [];
const isSingleTemplate = computed(() => builderStore.currentTemplate?.type === 'single');

function onBgUpdate(newBg) {
  // Update the entire page_bg object
  if (!builderStore.currentTemplate) return;
  if (!builderStore.currentTemplate.settings) {
    builderStore.currentTemplate.settings = {};
  }
  builderStore.currentTemplate.settings.page_bg = newBg;
  builderStore.isDirty = true;
}
</script>
