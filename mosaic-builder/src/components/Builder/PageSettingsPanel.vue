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

    <!-- Background -->
    <div>
      <label class="mb-block mb-text-xs mb-font-semibold mb-text-gray-300 mb-mb-2">Sfondo pagina</label>
      <BackgroundControls
        :modelValue="pageSettings.page_bg"
        :showParallax="true"
        @update:modelValue="onBgUpdate"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '@/stores/builder';
import BackgroundControls from './BackgroundControls.vue';

const builderStore = useBuilderStore();
const pageSettings = computed(() => builderStore.pageSettings);
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
