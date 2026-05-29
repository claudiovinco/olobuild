<template>
  <div
    class="olo-accordion"
    :class="'olo-accordion--preset-' + presetId"
    :style="accordionStyle"
  >
    <div
      v-for="(panel, i) in panels"
      :key="panel.id || i"
      class="olo-accordion-panel"
      :class="[
        separatorClass,
        s.panel_hover_lift ? 'olo-accordion-panel--lift' : '',
        (s.panel_hover_shadow && s.panel_hover_shadow !== 'none') ? ('olo-accordion-panel--hover-shadow-' + s.panel_hover_shadow) : '',
      ]"
      :style="panelStyle(i)"
    >
      <!-- Header -->
      <div
        class="olo-accordion-header"
        role="button"
        tabindex="0"
        :aria-expanded="isOpen(i)"
        @pointerdown.stop
        @mousedown.stop
        @click.stop="onHeaderClick($event, i)"
        @keydown.enter.prevent="toggle(i)"
        @keydown.space.prevent="toggle(i)"
        :style="headerStyle(i)"
      >
        <!-- Icon left -->
        <span
          v-if="s.icon_position === 'left'"
          class="olo-accordion-icon olo-accordion-icon--left"
          :class="{ 'olo-accordion-icon--open': isOpen(i), 'olo-accordion-icon--animated': s.animate_icon }"
          :style="[iconTransition, iconShapeStyle()]"
          v-html="iconSvg(isOpen(i))"
        ></span>

        <span v-if="panel.icon" class="olo-accordion-panel-icon">{{ panel.icon }}</span>
        <span class="olo-accordion-title" :data-olo-editable="'panels.' + i + '.title'">{{ panel.title }}</span>

        <!-- Icon right -->
        <span
          v-if="s.icon_position === 'right'"
          class="olo-accordion-icon olo-accordion-icon--right"
          :class="{ 'olo-accordion-icon--open': isOpen(i), 'olo-accordion-icon--animated': s.animate_icon }"
          :style="[iconTransition, iconShapeStyle()]"
          v-html="iconSvg(isOpen(i))"
        ></span>
      </div>

      <!-- Body with CSS grid animation -->
      <div
        class="olo-accordion-panel-body"
        :class="{ 'is-open': isOpen(i) }"
        :style="bodyTransition"
      >
        <div class="olo-accordion-panel-inner">
          <!-- Panel media -->
          <div v-if="panel.image || panel.video" class="olo-accordion-media" :style="mediaStyle">
            <img v-if="panel.image" :src="panel.image" alt="" class="olo-accordion-media-img" :style="{ borderRadius: (parseInt(s.media_radius) || 0) + 'px' }" />
            <div v-else-if="panel.video" class="olo-accordion-media-video">{{ t('&#x1F3AC; Video') }}</div>
          </div>
          <div
            class="olo-accordion-content"
            :style="contentStyle"
            style="white-space:pre-wrap;"
            :data-olo-editable="'panels.' + i + '.content'"
            data-olo-multiline
          >{{ panel.content }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { t } from '@/i18n';
import { useBuilderStore } from '@/stores/builder';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const builderStore = useBuilderStore();

// V3.22: Preset overrides — same behaviour as PHP renderer.
// When `preset` is set (and not 'custom'), the preset values override
// the user-controlled fields for the visual identity keys.
const PRESETS = {
  'card-soft': {
    header_bg: '#ffffff', header_bg_active: '#fdf2ec', header_text_color: '#1e293b',
    content_bg: '#ffffff', text_color: '#475569', border_color: '#e5e7eb',
    gap: 12, border_radius: 12, icon_style: 'plus', separator_style: 'border', shadow: 'sm',
  },
  'minimal-underline': {
    header_bg: '', header_bg_active: '', header_text_color: '#0f172a',
    content_bg: '', text_color: '#475569', border_color: '#e5e7eb',
    gap: 0, border_radius: 0, icon_style: 'plus', separator_style: 'border', shadow: 'none',
  },
  'pill-brand': {
    header_bg: '#ffffff', header_bg_active: '#e8622a', header_text_color: '#1e293b',
    content_bg: '#ffffff', text_color: '#475569', border_color: '',
    gap: 8, border_radius: 999, icon_style: 'chevron', separator_style: 'shadow', shadow: 'sm',
  },
  'outline-sharp': {
    header_bg: '#ffffff', header_bg_active: '#fdf2ec', header_text_color: '#0f172a',
    content_bg: '#ffffff', text_color: '#475569', border_color: '#e8622a',
    gap: 0, border_radius: 6, icon_style: 'plus', separator_style: 'border', shadow: 'none',
  },
  'glass-soft': {
    header_bg: 'rgba(255,255,255,0.55)', header_bg_active: 'rgba(255,255,255,0.75)', header_text_color: '#0f172a',
    content_bg: 'rgba(255,255,255,0.85)', text_color: '#475569', border_color: 'rgba(255,255,255,0.6)',
    gap: 14, border_radius: 16, icon_style: 'plus', separator_style: 'border', shadow: 'lg',
  },
};

const s = computed(() => {
  const raw = props.settings || {};
  const presetId = raw.preset || 'card-soft';
  if (presetId === 'custom' || !PRESETS[presetId]) return raw;
  // Preset wins on these visual keys; everything else comes from user settings.
  return { ...raw, ...PRESETS[presetId] };
});

const presetId = computed(() => props.settings?.preset || 'card-soft');

// Parse panels with retrocompat
const panels = computed(() => {
  const raw = s.value.panels;
  // New format: array of objects
  if (Array.isArray(raw)) return raw;
  // Old format: string with --- delimiter
  if (typeof raw === 'string') {
    return raw.split('---').map((block, i) => {
      const lines = block.trim().split('\n').map(l => l.trim()).filter(Boolean);
      if (lines.length >= 2) return { id: 'p-legacy-' + i, title: lines[0], content: lines.slice(1).join('<br>') };
      if (lines.length === 1) return { id: 'p-legacy-' + i, title: lines[0], content: '' };
      return null;
    }).filter(Boolean);
  }
  return [];
});

// Open state tracking
const openPanels = ref(new Set());

// Initialize open panels based on default_open
function initOpenState() {
  openPanels.value = new Set();
  const mode = s.value.default_open || 'first';
  if (mode === 'first' && panels.value.length > 0) {
    openPanels.value.add(0);
  } else if (mode === 'all') {
    panels.value.forEach((_, i) => openPanels.value.add(i));
  }
}

initOpenState();

watch(() => s.value.default_open, () => initOpenState());
watch(() => panels.value.length, () => initOpenState());

function isOpen(index) {
  return openPanels.value.has(index);
}

function toggle(index) {
  const newSet = new Set(openPanels.value);
  if (newSet.has(index)) {
    newSet.delete(index);
  } else {
    if (!s.value.toggle_mode) {
      // Accordion mode: close all others
      newSet.clear();
    }
    newSet.add(index);
  }
  openPanels.value = newSet;
}

// V3.22.4: header click — open panel + select tile if not yet selected.
// We stop propagation on @click/@pointerdown/@mousedown so the GridCell
// drag/select wrapper doesn't intercept the gesture and break the toggle.
function onHeaderClick(event, i) {
  toggle(i);
  if (props.tileId && builderStore.selectedTileId !== props.tileId) {
    builderStore.selectTile(props.tileId);
  }
}

// Icon SVGs
const icons = {
  chevron: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
  plus: {
    closed: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
    open: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
  },
  arrow: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 3v10m0 0l-3-3m3 3l3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
  caret: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4 6l4 5 4-5" fill="currentColor"/></svg>',
};

function iconSvg(isOpenState) {
  const style = s.value.icon_style || 'chevron';
  if (style === 'plus') {
    return isOpenState ? icons.plus.open : icons.plus.closed;
  }
  return icons[style] || icons.chevron;
}

// Computed styles
const speed = computed(() => (parseInt(s.value.animation_speed) || 300) + 'ms');

const iconTransition = computed(() => ({
  transition: `transform ${speed.value} ease`,
}));

const bodyTransition = computed(() => ({
  transition: `grid-template-rows ${speed.value} ease, opacity ${speed.value} ease`,
}));

const accordionStyle = computed(() => ({
  display: 'flex',
  flexDirection: 'column',
  gap: (parseInt(s.value.gap) || 0) + 'px',
  borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
  overflow: 'hidden',
  minHeight: '60px',
}));

function panelStyle(index) {
  const gap = parseInt(s.value.gap) || 0;
  const radius = parseInt(s.value.border_radius) || 0;
  const style = {};

  if (gap > 0) {
    style.borderRadius = radius + 'px';
    style.overflow = 'hidden';
  } else {
    // No gap: only round first/last
    if (index === 0) {
      style.borderRadius = `${radius}px ${radius}px 0 0`;
    } else if (index === panels.value.length - 1) {
      style.borderRadius = `0 0 ${radius}px ${radius}px`;
    } else {
      style.borderRadius = '0';
    }
    style.overflow = 'hidden';
  }

  return style;
}

const separatorClass = computed(() => {
  return 'olo-accordion-panel--sep-' + (s.value.separator_style || 'border');
});

function headerStyle(index) {
  const open = isOpen(index);
  const v = s.value;
  const py = parseInt(v.header_padding_y) || 16;
  const px = parseInt(v.header_padding_x) || 20;
  const fs = parseInt(v.header_font_size) || 15;
  const fw = v.header_font_weight || '600';
  const ffMap = { mono: 'ui-monospace, SFMono-Regular, Menlo, monospace', serif: "Georgia, 'Times New Roman', serif", sans: 'inherit' };
  const ff = ffMap[v.header_font_family || 'sans'] || 'inherit';
  const bw = Math.max(0, parseInt(v.border_width ?? 1));
  const blur = Math.max(0, parseInt(v.backdrop_blur ?? 0));
  const sat  = Math.max(100, parseInt(v.backdrop_saturate ?? 100));
  const headerBg = open
    ? (v.header_bg_active || v.header_bg || 'var(--olo-color-muted, #F3F4F6)')
    : (v.header_bg || 'var(--olo-color-background, #FFFFFF)');
  const headerColor = open && v.header_text_color_active
    ? v.header_text_color_active
    : (v.header_text_color || 'var(--olo-color-text, #374151)');
  const style = {
    background: headerBg,
    color: headerColor,
    padding: `${py}px ${px}px`,
    fontSize: fs + 'px',
    fontWeight: fw,
    fontFamily: ff,
    borderBottom: open && v.separator_style === 'border' && bw > 0
      ? `${bw}px solid ${v.border_color || 'var(--olo-color-border, #E5E7EB)'}`
      : 'none',
  };
  if (blur > 0) {
    style.backdropFilter = `blur(${blur}px) saturate(${sat}%)`;
    style.webkitBackdropFilter = style.backdropFilter;
  }
  return style;
}

function iconShapeStyle() {
  const v = s.value;
  const shape = v.icon_shape || 'none';
  if (shape === 'none') return {};
  const size = Math.max(16, parseInt(v.icon_shape_size ?? 32));
  return {
    width: size + 'px',
    height: size + 'px',
    borderRadius: shape === 'circle' ? '50%' : '4px',
    background: v.icon_shape_bg || 'transparent',
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
  };
}

const mediaStyle = computed(() => {
  const align = s.value.media_align || 'right';
  const w = parseInt(s.value.media_width) || 40;
  return {
    float: align === 'left' ? 'left' : 'right',
    width: w + '%',
    margin: align === 'left' ? '0 12px 8px 0' : '0 0 8px 12px',
  };
});

const contentStyle = computed(() => {
  const v = s.value;
  const py = parseInt(v.content_padding_y) || 20;
  const px = parseInt(v.content_padding_x) || 20;
  const fs = parseInt(v.content_font_size) || 14;
  const blur = Math.max(0, parseInt(v.backdrop_blur ?? 0));
  const sat  = Math.max(100, parseInt(v.backdrop_saturate ?? 100));
  const out = {
    background: v.content_bg || 'var(--olo-color-background, #FFFFFF)',
    color: v.text_color || 'var(--olo-color-text, #374151)',
    padding: `4px ${px}px ${py}px`,
    fontSize: fs + 'px',
  };
  if (blur > 0) {
    const cBlur = Math.max(0, blur - 4);
    const cSat  = Math.max(100, sat - 20);
    out.backdropFilter = `blur(${cBlur}px) saturate(${cSat}%)`;
    out.webkitBackdropFilter = out.backdropFilter;
  }
  return out;
});

</script>

<style scoped>
.olo-accordion-panel {
  border: 1px solid v-bind('s.border_color || "var(--olo-color-border, #E5E7EB)"');
}

.olo-accordion-panel--sep-shadow {
  border: none;
  box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

/* V3.22 hover lift + shadow on panel */
.olo-accordion-panel--lift {
  transition: transform 0.2s, box-shadow 0.2s;
}
.olo-accordion-panel--lift:hover {
  transform: translateY(-1px);
}
.olo-accordion-panel--hover-shadow-sm:hover {
  box-shadow: 0 1px 2px rgba(16,24,40,0.06), 0 1px 3px rgba(16,24,40,0.08);
}
.olo-accordion-panel--hover-shadow-md:hover {
  box-shadow: 0 4px 6px rgba(16,24,40,0.08), 0 2px 4px rgba(16,24,40,0.06);
}
.olo-accordion-panel--hover-shadow-lg:hover {
  box-shadow: 0 12px 24px rgba(16,24,40,0.10), 0 4px 8px rgba(16,24,40,0.08);
}

.olo-accordion-panel--sep-none {
  border: none;
}

.olo-accordion-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 18px;
  cursor: pointer;
  user-select: none;
  font-weight: 600;
  font-size: 14px;
  transition: background 0.15s;
}

.olo-accordion-header:hover {
  filter: brightness(1.1);
}

.olo-accordion-title {
  flex: 1;
  min-width: 0;
}

.olo-accordion-icon {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
}

.olo-accordion-icon--animated.olo-accordion-icon--open {
  transform: rotate(180deg);
}

/* Animated expand/collapse via grid-template-rows fr values.
   Requires .olo-accordion-panel-inner { min-height: 0 } to allow the row
   to actually shrink to 0fr in modern browsers. */
.olo-accordion-panel-body {
  display: grid;
  grid-template-rows: 0fr;
  opacity: 0;
}

.olo-accordion-panel-body.is-open {
  grid-template-rows: 1fr;
  opacity: 1;
}

.olo-accordion-panel-inner {
  overflow: hidden;
  min-height: 0;
}

.olo-accordion-content {
  padding: 14px 18px;
  font-size: 13px;
  line-height: 1.6;
}

.olo-accordion-panel-icon {
  flex-shrink: 0;
  font-size: 16px;
}
.olo-accordion-media {
  max-width: 50%;
}
.olo-accordion-media-img {
  width: 100%;
  display: block;
  object-fit: cover;
}
.olo-accordion-media-video {
  background: var(--olo-color-muted, #F3F4F6);
  padding: 12px;
  border-radius: 4px;
  text-align: center;
  font-size: 12px;
  color: var(--olo-color-text-muted, #9CA3AF);
}

/* ── V3.22 Preset overrides (canvas preview) ───────────── */

/* Minimal Underline (Apple-style) */
.olo-accordion--preset-minimal-underline .olo-accordion-panel {
  border: none !important;
  border-bottom: 1px solid #e5e7eb !important;
  border-radius: 0 !important;
  background: transparent !important;
  box-shadow: none !important;
}
.olo-accordion--preset-minimal-underline .olo-accordion-panel:last-child {
  border-bottom: none !important;
}
.olo-accordion--preset-minimal-underline .olo-accordion-header {
  padding: 22px 0 !important;
  font-size: 17px !important;
  font-weight: 600 !important;
  background: transparent !important;
}
.olo-accordion--preset-minimal-underline .olo-accordion-content {
  padding: 0 0 22px 0 !important;
  background: transparent !important;
  font-size: 15px !important;
}
.olo-accordion--preset-minimal-underline .olo-accordion-icon {
  color: #e8622a;
}

/* Pill Brand (Linear-style) */
.olo-accordion--preset-pill-brand .olo-accordion-panel {
  border: none !important;
  border-radius: 14px !important;
  overflow: hidden;
  transition: box-shadow 0.2s, transform 0.2s;
}
.olo-accordion--preset-pill-brand .olo-accordion-panel:hover {
  box-shadow: 0 6px 20px rgba(232,98,42,0.18) !important;
  transform: translateY(-1px);
}
.olo-accordion--preset-pill-brand .olo-accordion-header {
  border-radius: 14px !important;
  font-size: 15px !important;
  transition: all 0.2s ease;
}
.olo-accordion--preset-pill-brand .olo-accordion-panel:has(.is-open) .olo-accordion-header,
.olo-accordion--preset-pill-brand .olo-accordion-header[aria-expanded="true"] {
  color: #fff !important;
  border-radius: 14px 14px 0 0 !important;
}
.olo-accordion--preset-pill-brand .olo-accordion-header[aria-expanded="true"] .olo-accordion-icon,
.olo-accordion--preset-pill-brand .olo-accordion-header[aria-expanded="true"] .olo-accordion-panel-icon {
  color: #fff !important;
}

/* Outline Sharp (brutalist) */
.olo-accordion--preset-outline-sharp .olo-accordion-panel {
  border: 2px solid #e8622a !important;
  border-radius: 6px !important;
  box-shadow: none !important;
}
.olo-accordion--preset-outline-sharp .olo-accordion-panel:has(.is-open) {
  box-shadow: 0 0 0 4px rgba(232,98,42,0.12) !important;
}
.olo-accordion--preset-outline-sharp .olo-accordion-header {
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  font-size: 14px !important;
  letter-spacing: -0.02em;
}
.olo-accordion--preset-outline-sharp .olo-accordion-icon {
  background: #fdf2ec;
  width: 32px;
  height: 32px;
  border-radius: 4px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #e8622a;
}

/* Glass Soft (glassmorphism) */
.olo-accordion--preset-glass-soft {
  padding: 4px;
}
.olo-accordion--preset-glass-soft .olo-accordion-panel {
  border: 1px solid rgba(255,255,255,0.6) !important;
  border-radius: 16px !important;
  backdrop-filter: blur(12px) saturate(160%);
  -webkit-backdrop-filter: blur(12px) saturate(160%);
  box-shadow: 0 12px 40px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04) !important;
}
.olo-accordion--preset-glass-soft .olo-accordion-header {
  backdrop-filter: blur(12px) saturate(160%);
  -webkit-backdrop-filter: blur(12px) saturate(160%);
}
.olo-accordion--preset-glass-soft .olo-accordion-content {
  backdrop-filter: blur(8px) saturate(140%);
  -webkit-backdrop-filter: blur(8px) saturate(140%);
}
.olo-accordion--preset-glass-soft .olo-accordion-icon {
  color: #e8622a;
}

/* Card Soft active accent (default preset, also enhanced) */
.olo-accordion--preset-card-soft .olo-accordion-panel {
  transition: border-color 0.25s, box-shadow 0.25s;
  box-shadow: 0 1px 2px rgba(16,24,40,0.05);
}
.olo-accordion--preset-card-soft .olo-accordion-header[aria-expanded="true"] + .olo-accordion-panel-body,
.olo-accordion--preset-card-soft .olo-accordion-panel:has([aria-expanded="true"]) {
  border-color: #e8622a !important;
  box-shadow: 0 1px 2px rgba(232,98,42,0.05), 0 4px 12px rgba(232,98,42,0.08) !important;
}
.olo-accordion--preset-card-soft .olo-accordion-icon {
  color: #e8622a;
}
</style>
