<template>
  <div class="mb-rounded-lg mb-overflow-hidden mb-bg-gray-800 mb-p-4">
    <!-- Heading -->
    <h3 v-if="settings.heading" class="mb-font-semibold mb-text-gray-200 mb-mb-3" :style="{ fontSize: (settings.heading_size || 22) + 'px', textAlign: settings.heading_align || 'left' }">
      {{ settings.heading }}
    </h3>

    <!-- Layout indicator -->
    <div class="mb-flex mb-items-center mb-gap-2 mb-mb-3">
      <span class="mb-text-xs mb-px-2 mb-py-0.5 mb-rounded mb-bg-indigo-900/50 mb-text-indigo-300">{{ layoutLabel }}</span>
      <span class="mb-text-xs mb-px-2 mb-py-0.5 mb-rounded mb-bg-gray-700 mb-text-gray-400">{{ modeLabel }}</span>
      <span class="mb-text-xs mb-text-gray-500">{{ settings.count || 3 }} strutture</span>
    </div>

    <!-- Cards preview -->
    <div class="mb-flex mb-gap-3" :class="{ 'mb-overflow-hidden': settings.layout === 'marquee' }">
      <div v-for="n in Math.min(settings.count || 3, 4)" :key="n"
           class="mb-flex-1 mb-min-w-0 mb-rounded-lg mb-overflow-hidden mb-bg-gray-700/50 mb-border mb-border-gray-600/30">
        <div class="mb-h-20 mb-bg-gradient-to-br mb-from-gray-600 mb-to-gray-700 mb-flex mb-items-center mb-justify-center mb-text-2xl">
          &#127968;
        </div>
        <div class="mb-p-2">
          <div class="mb-h-3 mb-w-3/4 mb-bg-gray-600 mb-rounded mb-mb-1.5"></div>
          <div class="mb-flex mb-gap-1.5 mb-mb-1.5">
            <div v-if="settings.show_valley !== false" class="mb-h-2 mb-w-12 mb-bg-gray-600/60 mb-rounded"></div>
            <div v-if="settings.show_altitude !== false" class="mb-h-2 mb-w-8 mb-bg-gray-600/60 mb-rounded"></div>
          </div>
          <div v-if="settings.show_price !== false" class="mb-h-3 mb-w-16 mb-bg-indigo-800/50 mb-rounded mb-mb-1.5"></div>
          <div v-if="settings.show_link !== false" class="mb-h-5 mb-w-14 mb-bg-indigo-600/40 mb-rounded mb-text-[9px] mb-text-center mb-leading-5 mb-text-indigo-300">
            {{ settings.link_text || 'Scopri' }}
          </div>
        </div>
      </div>
    </div>

    <!-- Marquee animation indicator -->
    <div v-if="settings.layout === 'marquee'" class="mb-text-center mb-text-xs mb-text-gray-500 mb-mt-2">
      &#8592; nastro scorrevole continuo &#8594;
    </div>
    <!-- Slider indicator -->
    <div v-else-if="settings.layout === 'slider'" class="mb-flex mb-justify-between mb-mt-2">
      <span class="mb-text-gray-500 mb-text-sm">&#8249;</span>
      <span class="mb-text-xs mb-text-gray-500">slider con frecce{{ settings.autoplay ? ' + autoplay' : '' }}</span>
      <span class="mb-text-gray-500 mb-text-sm">&#8250;</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const modeLabels = {
  same_valley: 'Stessa valle',
  nearest: 'Pi\u00f9 vicine',
  similar_altitude: 'Altitudine simile',
  same_club: 'Stesso club',
  random: 'Casuali',
};

const layoutLabels = {
  grid: 'Griglia',
  slider: 'Slider',
  marquee: 'Nastro',
};

const modeLabel = computed(() => modeLabels[props.settings.mode] || 'Stessa valle');
const layoutLabel = computed(() => layoutLabels[props.settings.layout] || 'Griglia');
</script>
