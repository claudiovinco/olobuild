<template>
  <div class="olo-sitelogo-preview" :style="{ minHeight: '40px' }">
    <!-- Logo image -->
    <img
      v-if="logoUrl"
      :src="logoUrl"
      :alt="siteName"
      :style="{ maxHeight: (s.max_height || 50) + 'px', width: 'auto', display: 'block' }"
    />
    <!-- Text fallback -->
    <span
      v-else
      class="olo-sitelogo-text"
    >{{ siteName }}</span>

    <!-- Tagline -->
    <p v-if="s.show_tagline" class="olo-sitelogo-tagline">
      {{ siteTagline }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  source: 'auto',
  custom_image: '',
  max_height: 50,
  link_home: true,
  show_tagline: false,
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const oloData = window.oloData || {};
const siteInfo = oloData.siteInfo || {};

const siteName = computed(() => siteInfo.name || 'Site Name');
const siteTagline = computed(() => siteInfo.tagline || '');

const logoUrl = computed(() => {
  if (s.value.source === 'custom_image' && s.value.custom_image) {
    return s.value.custom_image;
  }
  // Auto: use WP site logo if available
  return siteInfo.logo_url || '';
});
</script>

<style scoped>
.olo-sitelogo-preview {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 2px;
}
.olo-sitelogo-text {
  font-size: 20px;
  font-weight: 700;
  color: var(--olo-color-text, #374151);
  line-height: 1.2;
}
.olo-sitelogo-tagline {
  font-size: 11px;
  color: var(--olo-color-text-faint, #94a3b8);
  margin: 0;
}
</style>
