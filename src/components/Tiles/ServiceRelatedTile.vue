<template>
  <div class="mb-rounded-lg mb-overflow-hidden mb-p-4" :style="{ background: 'var(--olo-color-secondary, #16263d)' }">
    <!-- Heading -->
    <h3 v-if="settings.heading" class="mb-font-semibold mb-mb-3" :style="{ fontSize: (settings.heading_size || 22) + 'px', textAlign: settings.heading_align || 'left', color: 'rgba(255,255,255,0.9)' }">
      {{ settings.heading }}
    </h3>

    <!-- Layout indicator -->
    <div class="mb-flex mb-items-center mb-gap-2 mb-mb-3">
      <span class="mb-text-xs mb-px-2 mb-py-0.5 mb-rounded" :style="chipBrandStyle">{{ layoutLabel }}</span>
      <span class="mb-text-xs mb-px-2 mb-py-0.5 mb-rounded" :style="chipNeutralStyle">{{ modeLabel }}</span>
      <span class="mb-text-xs" :style="{ color: 'rgba(255,255,255,0.5)' }">{{ settings.count || 3 }} strutture</span>
    </div>

    <!-- Cards preview -->
    <div class="mb-flex mb-gap-3" :class="{ 'mb-overflow-hidden': settings.layout === 'marquee' }">
      <div v-for="n in Math.min(settings.count || 3, 4)" :key="n"
           class="mb-flex-1 mb-min-w-0 mb-rounded-lg mb-overflow-hidden" :style="cardStyle">
        <div class="mb-h-20 mb-flex mb-items-center mb-justify-center olo-svrel-ph" :style="phStyle" v-html="homeSvg"></div>
        <div class="mb-p-2">
          <div class="mb-h-3 mb-w-3/4 mb-rounded mb-mb-1.5" :style="skelStyle"></div>
          <div class="mb-flex mb-gap-1.5 mb-mb-1.5">
            <div v-if="settings.show_valley !== false" class="mb-h-2 mb-w-12 mb-rounded" :style="skelStyle"></div>
            <div v-if="settings.show_altitude !== false" class="mb-h-2 mb-w-8 mb-rounded" :style="skelStyle"></div>
          </div>
          <div v-if="settings.show_price !== false" class="mb-h-3 mb-w-16 mb-rounded mb-mb-1.5" :style="skelBrandStyle"></div>
          <div v-if="settings.show_link !== false" class="mb-h-5 mb-w-14 mb-rounded mb-text-[9px] mb-text-center mb-leading-5" :style="linkChipStyle">
            {{ settings.link_text || 'Scopri' }}
          </div>
        </div>
      </div>
    </div>

    <!-- Marquee animation indicator -->
    <div v-if="settings.layout === 'marquee'" class="mb-text-center mb-text-xs mb-mt-2" :style="{ color: 'rgba(255,255,255,0.5)' }">
      {{ t('&#8592; nastro scorrevole continuo &#8594;') }}
    </div>
    <!-- Slider indicator -->
    <div v-else-if="settings.layout === 'slider'" class="mb-flex mb-justify-between mb-mt-2" :style="{ color: 'rgba(255,255,255,0.5)' }">
      <span class="mb-text-sm">&#8249;</span>
      <span class="mb-text-xs">slider con frecce{{ settings.autoplay ? ' + autoplay' : '' }}</span>
      <span class="mb-text-sm">&#8250;</span>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const homeSvg = iconsSvg['home'] || '';

// Anteprima su sfondo navy brand: neutri = bianco-alpha, accenti = tinta primary
const chipBrandStyle = { background: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 35%, transparent)', color: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 60%, #fff)' };
const chipNeutralStyle = { background: 'rgba(255,255,255,0.12)', color: 'rgba(255,255,255,0.6)' };
const cardStyle = { background: 'rgba(255,255,255,0.06)', border: '1px solid rgba(255,255,255,0.1)' };
const phStyle = { background: 'linear-gradient(to bottom right, rgba(255,255,255,0.12), rgba(255,255,255,0.04))', color: 'rgba(255,255,255,0.45)' };
const skelStyle = { background: 'rgba(255,255,255,0.14)' };
const skelBrandStyle = { background: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 40%, transparent)' };
const linkChipStyle = { background: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 45%, transparent)', color: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 70%, #fff)' };

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

<style scoped>
.olo-svrel-ph :deep(svg) { width: 24px; height: 24px; stroke: currentColor; fill: none; }
</style>
