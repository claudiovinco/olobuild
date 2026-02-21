<template>
  <div
    class="ols-switcher-preview"
    :class="[
      'ols-style--' + (settings.style || 'flags'),
      'ols-layout--' + (settings.layout || 'inline'),
    ]"
    :style="wrapperStyle"
  >
    <!-- Inline / Floating -->
    <template v-if="settings.layout !== 'dropdown'">
      <div
        v-for="lang in languages"
        :key="lang.code"
        class="ols-item-preview"
        :class="{ 'ols-active': lang.code === defaultLang }"
        :style="itemStyle(lang.code === defaultLang)"
      >
        <span v-if="showFlag" class="ols-flag" :style="flagStyle">{{ lang.flag }}</span>
        <span v-if="showCode" class="ols-code">{{ lang.code.toUpperCase() }}</span>
        <span v-if="showName" class="ols-name-label">{{ lang.name }}</span>
        <span v-if="settings.show_label && settings.style === 'flags'" class="ols-sublabel">
          {{ settings.label_format === 'code' ? lang.code.toUpperCase() : lang.name }}
        </span>
      </div>
    </template>

    <!-- Dropdown -->
    <template v-else>
      <div class="ols-dropdown-preview" :style="itemStyle(false)">
        <span v-if="showFlag" class="ols-flag" :style="flagStyle">{{ currentLang.flag }}</span>
        <span v-if="showCode" class="ols-code">{{ currentLang.code.toUpperCase() }}</span>
        <span v-if="showName" class="ols-name-label">{{ currentLang.name }}</span>
        <svg v-if="settings.show_dropdown_arrow" class="ols-arrow" width="12" height="12" viewBox="0 0 12 12">
          <path d="M2 4l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5"/>
        </svg>
      </div>
    </template>

    <!-- Floating badge -->
    <div v-if="settings.layout === 'floating'" class="ols-float-badge">
      {{ settings.floating_pos || 'bottom-right' }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const flags = {
  it: '🇮🇹', en: '🇬🇧', de: '🇩🇪', fr: '🇫🇷', es: '🇪🇸', pt: '🇵🇹',
};

// Simula le lingue attive (nel builder non abbiamo accesso al PHP)
const languages = computed(() => [
  { code: 'it', name: 'Italiano', flag: flags.it || '🏳️' },
  { code: 'en', name: 'English', flag: flags.en || '🏳️' },
  { code: 'de', name: 'Deutsch', flag: flags.de || '🏳️' },
]);

const defaultLang = 'it';
const currentLang = computed(() => languages.value[0]);

const showFlag = computed(() =>
  props.settings.style === 'flags' || props.settings.style === 'flags_text'
);
const showCode = computed(() =>
  props.settings.style === 'codes' || props.settings.style === 'flags_text'
);
const showName = computed(() => props.settings.style === 'names');

const flagStyle = computed(() => ({
  fontSize: (props.settings.flag_size || 24) + 'px',
  lineHeight: 1,
}));

const wrapperStyle = computed(() => ({
  gap: (props.settings.gap || 8) + 'px',
}));

function itemStyle(isActive) {
  const s = props.settings;
  return {
    background: isActive ? (s.active_bg || '#6366F1') : (s.bg || '#ffffff'),
    color: isActive ? (s.active_color || '#ffffff') : (s.color || '#374151'),
    borderColor: s.border_color || '#e5e7eb',
    borderRadius: (s.border_radius || 8) + 'px',
  };
}
</script>

<style scoped>
.ols-switcher-preview {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  min-height: 32px;
}
.ols-item-preview {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border: 1px solid;
  cursor: default;
  font-size: 13px;
  font-weight: 500;
  transition: all 0.15s;
  flex-direction: row;
}
.ols-item-preview .ols-sublabel {
  font-size: 10px;
  opacity: 0.8;
}
.ols-dropdown-preview {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border: 1px solid;
  cursor: default;
  font-size: 13px;
  font-weight: 500;
}
.ols-dropdown-preview .ols-arrow {
  margin-left: 2px;
  opacity: 0.6;
}
.ols-flag {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.ols-code {
  font-weight: 700;
  font-size: 12px;
  letter-spacing: 0.5px;
}
.ols-name-label {
  font-weight: 400;
}
.ols-float-badge {
  margin-left: 8px;
  font-size: 10px;
  color: #9ca3af;
  background: #f3f4f6;
  padding: 2px 6px;
  border-radius: 4px;
}

/* Layout floating preview */
.ols-layout--floating {
  border: 1px dashed #6366F1;
  padding: 4px;
  border-radius: 6px;
}
</style>
