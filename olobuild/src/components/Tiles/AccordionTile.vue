<template>
  <div
    class="olo-accordion"
    :style="accordionStyle"
  >
    <div
      v-for="(panel, i) in panels"
      :key="panel.id || i"
      class="olo-accordion-panel"
      :class="separatorClass"
      :style="panelStyle(i)"
    >
      <!-- Header -->
      <div
        class="olo-accordion-header"
        role="button"
        tabindex="0"
        :aria-expanded="isOpen(i)"
        @click="toggle(i)"
        @keydown.enter.prevent="toggle(i)"
        @keydown.space.prevent="toggle(i)"
        :style="headerStyle(i)"
      >
        <!-- Icon left -->
        <span
          v-if="s.icon_position === 'left'"
          class="olo-accordion-icon olo-accordion-icon--left"
          :class="{ 'olo-accordion-icon--open': isOpen(i), 'olo-accordion-icon--animated': s.animate_icon }"
          :style="iconTransition"
          v-html="iconSvg(isOpen(i))"
        ></span>

        <span v-if="panel.icon" class="olo-accordion-panel-icon">{{ panel.icon }}</span>
        <span class="olo-accordion-title" :data-olo-editable="'panels.' + i + '.title'">{{ panel.title }}</span>

        <!-- Icon right -->
        <span
          v-if="s.icon_position === 'right'"
          class="olo-accordion-icon olo-accordion-icon--right"
          :class="{ 'olo-accordion-icon--open': isOpen(i), 'olo-accordion-icon--animated': s.animate_icon }"
          :style="iconTransition"
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
            <div v-else-if="panel.video" class="olo-accordion-media-video">&#x1F3AC; Video</div>
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

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

// Shorthand
const s = computed(() => props.settings);

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
  transition: `grid-template-rows ${speed.value} ease`,
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
  return {
    background: open ? (s.value.header_bg_active || s.value.header_bg || 'var(--olo-color-muted, #F3F4F6)') : (s.value.header_bg || 'var(--olo-color-background, #FFFFFF)'),
    color: s.value.header_text_color || 'var(--olo-color-text, #374151)',
    borderBottom: open && s.value.separator_style === 'border' ? `1px solid ${s.value.border_color || 'var(--olo-color-border, #E5E7EB)'}` : 'none',
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

const contentStyle = computed(() => ({
  background: s.value.content_bg || 'var(--olo-color-background, #FFFFFF)',
  color: s.value.text_color || 'var(--olo-color-text, #374151)',
}));
</script>

<style scoped>
.olo-accordion-panel {
  border: 1px solid v-bind('s.border_color || "var(--olo-color-border, #E5E7EB)"');
}

.olo-accordion-panel--sep-shadow {
  border: none;
  box-shadow: 0 1px 3px rgba(0,0,0,0.3);
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

.olo-accordion-panel-body {
  display: grid;
  grid-template-rows: 0fr;
}

.olo-accordion-panel-body.is-open {
  grid-template-rows: 1fr;
}

.olo-accordion-panel-inner {
  overflow: hidden;
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
</style>
