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
          <span v-if="wantsFlag" class="ols-flag" :class="'ols-flag--' + flagShape" :style="flagBoxStyle" v-html="flagHtml(lang)"></span>
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
        <span v-if="wantsFlag" class="ols-flag" :class="'ols-flag--' + flagShape" :style="flagBoxStyle" v-html="flagHtml(lang)"></span>
        <span v-if="showCode" class="ols-code">{{ lang.code.toUpperCase() }}</span>
        <span v-if="showName" class="ols-name-label">{{ lang.name }}</span>
        <span v-if="s.show_label && (s.style === 'flags' || s.style === 'flags_circle')" class="ols-sublabel">
          {{ s.label_format === 'code' ? lang.code.toUpperCase() : lang.name }}
        </span>
      </div>
      <div v-if="s.layout === 'floating'" class="ols-float-badge">{{ s.floating_pos || 'bottom-right' }}</div>
    </template>

    <!-- Dropdown — anteprima: trigger + menu APERTO (così vedi/stili le voci;
         sul frontend il menu si apre al click). -->
    <template v-else>
      <div class="ols-dd-preview">
        <div class="ols-dropdown-preview" :style="itemStyle(false)">
          <span v-if="wantsFlag" class="ols-flag" :class="'ols-flag--' + flagShape" :style="flagBoxStyle" v-html="flagHtml(currentLang)"></span>
          <span v-if="showCode" class="ols-code">{{ currentLang.code.toUpperCase() }}</span>
          <span v-if="showName || !wantsFlag" class="ols-name-label">{{ currentLang.name }}</span>
          <svg v-if="s.show_dropdown_arrow" class="ols-arrow ols-arrow--open" width="12" height="12" viewBox="0 0 12 12">
            <path d="M2 4l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </div>
        <div class="ols-menu-preview">
          <div v-for="lang in otherLangs" :key="lang.code" class="ols-option-preview">
            <span v-if="wantsFlag" class="ols-flag" :class="'ols-flag--' + flagShape" :style="flagBoxStyle" v-html="flagHtml(lang)"></span>
            <span v-if="showCode" class="ols-code">{{ lang.code.toUpperCase() }}</span>
            <span v-if="showName || wantsFlag" class="ols-opt-name">{{ lang.name }}</span>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { flagSvg } from '@/utils/flagSvg';

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

const languages = computed(() => [
  { code: 'it', name: 'Italiano' },
  { code: 'en', name: 'English' },
  { code: 'de', name: 'Deutsch' },
]);

const defaultLang = 'it';
const currentLang = computed(() => languages.value[0]);
const otherLangs = computed(() => languages.value.filter(l => l.code !== currentLang.value.code));

const wantsFlag = computed(() => s.value.style === 'flags' || s.value.style === 'flags_text' || s.value.style === 'flags_circle');
const isCircle = computed(() => s.value.style === 'flags_circle' || (wantsFlag.value && s.value.flag_shape === 'circle' && s.value.style !== 'flags_text'));
const showCode = computed(() => s.value.style === 'codes' || s.value.style === 'flags_text');
const showName = computed(() => s.value.style === 'names');

// Dimensione della "moneta"/rettangolo bandiera
const flagSize = computed(() => {
  if (s.value.style === 'flags_circle') return parseInt(s.value.circle_size) || 36;
  return parseInt(s.value.flag_size) || 24;
});

// Bandiera moderna: "moneta" (cerchio) o rettangolo arrotondato, anello sottile +
// micro-ombra. La forma/dimensione è uguale per tutte le lingue; il contenuto è
// l'SVG (o un badge col codice se la bandiera manca). Reso via v-html (affidabile).
const flagShape = computed(() => (isCircle.value ? 'circle' : 'rounded'));
const flagBoxStyle = computed(() => {
  const sz = flagSize.value;
  const circle = isCircle.value;
  const w = circle ? sz : Math.round(sz * 1.38);
  const st = {
    width: w + 'px',
    height: sz + 'px',
    borderRadius: circle ? '50%' : Math.max(3, Math.round(sz * 0.22)) + 'px',
  };
  if (s.value.style === 'flags_circle') {
    if (s.value.circle_bg) { st.background = s.value.circle_bg; }
    if (s.value.circle_border) { st.boxShadow = '0 0 0 2px ' + s.value.circle_border; }
  }
  return st;
});
function flagHtml(lang) {
  return flagSvg(lang.code, lang.name) || `<span class="ols-flag-code">${lang.code.toUpperCase()}</span>`;
}

const wrapperStyle = computed(() => ({
  gap: (s.value.gap || 8) + 'px',
}));

function itemStyle(isActive) {
  const v = s.value;
  const base = {
    background: isActive ? (v.active_bg || 'var(--olo-color-primary, #e1474f)') : (v.bg || 'var(--olo-color-surface, #ffffff)'),
    color: isActive ? (v.active_color || 'var(--olo-color-primary-contrast, #ffffff)') : (v.color || 'var(--olo-color-text, #374151)'),
    borderColor: v.border_color || 'var(--olo-color-border, #e5e7eb)',
    borderRadius: (v.border_radius || 8) + 'px',
  };
  if (v.compact) { base.padding = '3px 7px'; base.fontSize = '11px'; }
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
  gap: 7px;
  padding: 6px 12px;
  border: 1px solid;
  cursor: default;
  font-size: 13px;
  font-weight: 500;
  transition: transform 0.15s, box-shadow 0.15s, background 0.15s;
}
.ols-item-preview:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(16,24,40,0.1); }
.ols-item-preview:focus-visible,
.ols-dropdown-preview:focus-visible,
.ols-tab-item:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
.ols-compact .ols-item-preview { padding: 3px 7px; font-size: 11px; gap: 5px; }
.ols-item-preview .ols-sublabel { font-size: 10px; opacity: 0.8; }
.ols-dropdown-preview {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 6px 12px;
  border: 1px solid;
  cursor: default;
  font-size: 13px;
  font-weight: 500;
}
.ols-compact .ols-dropdown-preview { padding: 3px 7px; font-size: 11px; }
.ols-dropdown-preview .ols-arrow { margin-left: 2px; opacity: 0.55; }
.ols-arrow--open { transform: rotate(180deg); }

/* Dropdown — anteprima con menu aperto */
.ols-dd-preview { display: inline-flex; flex-direction: column; align-items: flex-start; gap: 6px; }
.ols-menu-preview {
  display: flex; flex-direction: column; gap: 2px; padding: 6px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(16,24,40,0.16);
  position: relative;
  z-index: 5;
}
.ols-option-preview {
  display: flex; align-items: center; gap: 7px; padding: 6px 10px;
  border-radius: 7px; font-size: 13px; color: #1f2937;
  cursor: default; transition: background 0.12s; white-space: nowrap;
}
.ols-option-preview:hover { background: #f1f3f5; }
.ols-opt-name { font-weight: 500; }

/* ── Bandiera "a moneta" / rettangolo arrotondato — look moderno ── */
.ols-flag {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  overflow: hidden;
  box-shadow: 0 0 0 1px rgba(16,24,40,0.12), 0 1px 2px rgba(16,24,40,0.18);
  background: #fff;
}
.ols-flag--circle { box-shadow: 0 0 0 1px rgba(16,24,40,0.12), 0 1px 3px rgba(16,24,40,0.22); }
.ols-flag-svg { display: block; width: 100%; height: 100%; line-height: 0; }
.ols-flag-svg :deep(svg) { width: 100%; height: 100%; display: block; }
/* Fallback senza bandiera: badge col codice */
.ols-flag-code {
  display: inline-flex; align-items: center; justify-content: center;
  width: 100%; height: 100%;
  font-size: 10px; font-weight: 800; letter-spacing: 0.3px;
  color: var(--olo-color-text, #374151);
  background: var(--olo-color-surface-alt, #f1f3f5);
}
.ols-code { font-weight: 700; font-size: 12px; letter-spacing: 0.5px; }
.ols-compact .ols-code { font-size: 10px; }
.ols-name-label { font-weight: 500; }
.ols-float-badge {
  margin-left: 8px; font-size: 10px; color: #9ca3af;
  background: #f3f4f6; padding: 2px 6px; border-radius: 4px;
}
.ols-layout--floating { border: 1px dashed var(--olo-color-primary, #e1474f); padding: 4px; border-radius: 8px; }

/* Tabs preview */
.ols-tabs-preview { display: flex; gap: 2px; }
.ols-tabs--top .ols-tab-item { padding: 4px 9px; border-radius: 0 0 8px 8px; font-size: 12px; }
.ols-tabs--right .ols-tab-item,
.ols-tabs--left .ols-tab-item { padding: 7px 4px; font-size: 11px; }
.ols-tabs--right, .ols-tabs--left { flex-direction: column; }
.ols-tabs--left .ols-tab-item { border-radius: 8px 0 0 8px; }
.ols-tabs--right .ols-tab-item { border-radius: 0 8px 8px 0; }
.ols-tab-item {
  display: flex; align-items: center; justify-content: center; gap: 4px;
  cursor: default; font-weight: 600; transition: all 0.15s;
}
.ols-tabs-badge { font-size: 10px; color: #9ca3af; margin-left: 8px; }
.ols-layout--tabs { border: 1px dashed #f4a23b; padding: 6px; border-radius: 8px; }
</style>
