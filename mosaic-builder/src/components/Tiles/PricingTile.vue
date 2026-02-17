<template>
  <div
    class="mb-rounded-xl mb-p-8 mb-text-center mb-relative"
    :style="{
      background: settings.bg_color || '#1F2937',
      color: settings.text_color || '#F3F4F6',
      border: settings.is_popular ? `2px solid ${settings.accent_color || '#6366F1'}` : '2px solid transparent',
      minHeight: '80px'
    }"
  >
    <div
      v-if="settings.is_popular"
      class="mb-absolute mb-top-0 mb-left-1/2 mb--translate-x-1/2 mb--translate-y-1/2 mb-px-4 mb-py-1 mb-rounded-full mb-text-xs mb-font-semibold mb-uppercase mb-text-white"
      :style="{ background: settings.accent_color || '#6366F1' }"
    >Popular</div>
    <h3 class="mb-text-xl mb-font-semibold mb-mb-4" v-html="settings.plan_name || 'Plan'"></h3>
    <div class="mb-mb-6">
      <span class="mb-text-5xl mb-font-bold">${{ settings.price || '0' }}</span>
      <span class="mb-text-sm mb-opacity-70" v-html="settings.period || '/month'"></span>
    </div>
    <ul v-if="features.length" class="mb-text-left mb-mb-6 mb-space-y-2">
      <li
        v-for="(f, i) in features"
        :key="i"
        class="mb-py-2 mb-border-b mb-border-white/10 mb-text-sm"
      >&#10003; {{ f }}</li>
    </ul>
    <div
      class="mb-inline-block mb-w-full mb-py-3 mb-rounded-lg mb-font-semibold mb-text-white"
      :style="{ background: settings.accent_color || '#6366F1' }"
      v-html="settings.cta_text || 'Get Started'"
    ></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const features = computed(() => {
  const text = props.settings.features || '';
  return text.split('\n').map(l => l.trim()).filter(Boolean);
});
</script>
