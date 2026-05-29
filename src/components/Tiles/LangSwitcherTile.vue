<template>
  <div
    class="ols-switcher-preview"
    :class="[
      'ols-style--' + s.style,
      'ols-layout--' + s.layout,
      { 'ols-compact': s.compact },
    ]"
    :style="wrapperStyle"
  >
    <!-- Tabs (linguette) -->
    <template v-if="s.layout === 'tabs'">
      <div class="ols-tabs-preview" :class="'ols-tabs--' + (s.tabs_edge || 'top')">
        <div
          v-for="lang in languages"
          :key="lang.code"
          class="ols-tab-item"
          :class="{ 'ols-active': lang.code === defaultLang }"
          :style="tabItemStyle(lang.code === defaultLang)"
        >
          <span v-if="hasFlag" class="ols-flag" :style="flagDisplayStyle">{{ lang.flag }}</span>
          <span v-if="isCircle" class="ols-circle-flag" :style="circleStyle">{{ lang.flag }}</span>
          <span v-if="showCode" class="ols-code">{{ lang.code.toUpperCase() }}</span>
        </div>
      </div>
      <span class="ols-tabs-badge">linguette {{ s.tabs_edge || 'top' }}</span>
    </template>

    <!-- Inline / Floating -->
    <template v-else-if="s.layout !== 'dropdown'">
      <div
        v-for="lang in languages"
        :key="lang.code"
        class="ols-item-preview"
        :class="{ 'ols-active': lang.code === defaultLang }"
        :style="itemStyle(lang.code === defaultLang)"
      >
        <span v-if="hasFlag" class="ols-flag" :style="flagDisplayStyle">{{ lang.flag }}</span>
        <span v-if="isCircle" class="ols-circle-flag" :style="circleStyle">{{ lang.flag }}</span>
        <span v-if="showCode" class="ols-code">{{ lang.code.toUpperCase() }}</span>
        <span v-if="showName" class="ols-name-label">{{ lang.name }}</span>
        <span v-if="s.show_label && (s.style === 'flags' || s.style === 'flags_circle')" class="ols-sublabel">
          {{ s.label_format === 'code' ? lang.code.toUpperCase() : lang.name }}
        </span>
      </div>
      <div v-if="s.layout === 'floating'" class="ols-float-badge">{{ s.floating_pos || 'bottom-right' }}</div>
    </template>

    <!-- Dropdown -->
    <template v-else>
      <div class="ols-dropdown-preview" :style="itemStyle(false)">
        <span v-if="hasFlag" class="ols-flag" :style="flagDisplayStyle">{{ currentLang.flag }}</span>
        <span v-if="isCircle" class="ols-circle-flag" :style="circleStyle">{{ currentLang.flag }}</span>
        <span v-if="showCode" class="ols-code">{{ currentLang.code.toUpperCase() }}</span>
        <span v-if="showName" class="ols-name-label">{{ currentLang.name }}</span>
        <svg v-if="s.show_dropdown_arrow" class="ols-arrow" width="12" height="12" viewBox="0 0 12 12">
          <path d="M2 4l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5"/>
        </svg>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  style: 'flags', flag_shape: 'circle', flag_size: 24, show_label: false,
  label_format: 'name', layout: 'inline', floating_pos: 'bottom-right',
  gap: 8, active_bg: '', active_color: '', bg: '', color: '',
  border_color: '', border_radius: 8, show_dropdown_arrow: true,
  tabs_edge: 'top', tabs_offset: 20, tabs_size: 'normal', compact: false,
  circle_bg: '', circle_border: '', circle_size: 36,
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const flags = { it: '🇮🇹', en: '🇬🇧', de: '🇩🇪', fr: '🇫🇷', es: '🇪🇸', pt: '🇵🇹' };

const languages = computed(() => [
  { code: 'it', name: 'Italiano', flag: flags.it },
  { code: 'en', name: 'English', flag: flags.en },
  { code: 'de', name: 'Deutsch', flag: flags.de },
]);

const defaultLang = 'it';
const currentLang = computed(() => languages.value[0]);

const hasFlag = computed(() => s.value.style === 'flags' || s.value.style === 'flags_text');
const isCircle = computed(() => s.value.style === 'flags_circle');
const showCode = computed(() => s.value.style === 'codes' || s.value.style === 'flags_text');
const showName = computed(() => s.value.style === 'names');

const flagDisplayStyle = computed(() => ({
  fontSize: (s.value.flag_size || 24) + 'px',
  lineHeight: 1,
}));

const circleStyle = computed(() => {
  const sz = parseInt(s.value.circle_size) || 36;
  return {
    width: sz + 'px',
    height: sz + 'px',
    fontSize: Math.round(sz * 0.55) + 'px',
    background: s.value.circle_bg || 'rgba(255,255,255,0.1)',
    borderColor: s.value.circle_border || 'rgba(255,255,255,0.2)',
  };
});

const wrapperStyle = computed(() => ({
  gap: (s.value.gap || 8) + 'px',
}));

function itemStyle(isActive) {
  const v = s.value;
  // TOKEN-FIRST: lingua attiva = primario brand (era #e1474f indaco off-brand);
  // neutri nudi (bianco/grigio testo) → token superficie/testo
  const base = {
    background: isActive ? (v.active_bg || 'var(--olo-color-primary, #e1474f)') : (v.bg || 'var(--olo-color-surface, #ffffff)'),
    color: isActive ? (v.active_color || 'var(--olo-color-primary-contrast, #ffffff)') : (v.color || 'var(--olo-color-text, #374151)'),
    borderColor: v.border_color || 'var(--olo-color-border, #e5e7eb)',
    borderRadius: (v.border_radius || 8) + 'px',
  };
  if (v.compact) {
    base.padding = '3px 6px';
    base.fontSize = '11px';
  }
  return base;
}

function tabItemStyle(isActive) {
  const v = s.value;
  return {
    background: isActive ? (v.active_bg || 'var(--olo-color-primary, #e1474f)') : (v.bg || 'rgba(255,255,255,0.9)'),
    color: isActive ? (v.active_color || 'var(--olo-color-primary-contrast, #ffffff)') : (v.color || 'var(--olo-color-text, #374151)'),
  };
}
</script>

<style scoped>
.ols-switcher-preview {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  min-height: 28px;
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
}
/* a11y tastiera: anello di focus visibile sulle voci lingua */
.ols-item-preview:focus-visible,
.ols-dropdown-preview:focus-visible,
.ols-tab-item:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
.ols-compact .ols-item-preview {
  padding: 3px 6px;
  font-size: 11px;
  gap: 4px;
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
.ols-compact .ols-dropdown-preview {
  padding: 3px 6px;
  font-size: 11px;
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
.ols-circle-flag {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  border: 2px solid;
  flex-shrink: 0;
  line-height: 1;
}
.ols-code {
  font-weight: 700;
  font-size: 12px;
  letter-spacing: 0.5px;
}
.ols-compact .ols-code {
  font-size: 10px;
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
.ols-layout--floating {
  border: 1px dashed #e1474f;
  padding: 4px;
  border-radius: 6px;
}
/* Tabs preview */
.ols-tabs-preview {
  display: flex;
  gap: 2px;
}
.ols-tabs--top .ols-tab-item {
  padding: 4px 8px;
  border-radius: 0 0 6px 6px;
  font-size: 12px;
}
.ols-tabs--right .ols-tab-item,
.ols-tabs--left .ols-tab-item {
  padding: 6px 4px;
  writing-mode: vertical-lr;
  font-size: 11px;
}
.ols-tabs--right {
  flex-direction: column;
}
.ols-tabs--left {
  flex-direction: column;
}
.ols-tabs--left .ols-tab-item {
  border-radius: 6px 0 0 6px;
}
.ols-tabs--right .ols-tab-item {
  border-radius: 0 6px 6px 0;
}
.ols-tab-item {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 3px;
  cursor: default;
  font-weight: 600;
  transition: all 0.15s;
}
.ols-tab-item .ols-circle-flag {
  writing-mode: horizontal-tb;
}
.ols-tabs-badge {
  font-size: 10px;
  color: #9ca3af;
  margin-left: 8px;
}
.ols-layout--tabs {
  border: 1px dashed #f4a23b;
  padding: 6px;
  border-radius: 6px;
}
</style>
