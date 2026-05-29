<template>
  <div class="olo-icontabs" :style="{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '20px' }">
    <!-- Pill tab selector -->
    <div
      class="oit-pill"
      role="tablist"
      :style="{
        display: 'inline-flex',
        alignItems: 'center',
        gap: '6px',
        padding: '6px',
        background: s.pill_bg,
        borderRadius: '999px',
      }"
    >
      <button
        v-for="(item, i) in items"
        :key="item.id || i"
        type="button"
        class="oit-tab"
        :class="{ 'is-active': activeIndex === i }"
        role="tab"
        :aria-selected="activeIndex === i"
        :title="item.label || ''"
        :style="tabStyle(i)"
        @click.stop="setActive(i)"
      >
        <span class="oit-icon" v-html="renderIcon(item.icon)"></span>
        <span v-if="activeIndex === i" class="oit-label" :style="{ display: 'inline', fontSize: '14px', fontWeight: 600 }">{{ item.label }}</span>
      </button>
    </div>

    <!-- Active card -->
    <div
      v-if="activeItem"
      class="oit-card"
      :style="cardStyle"
    >
      <div
        v-if="activeItem.heading"
        class="oit-heading"
        :style="{ fontSize: '16px', fontWeight: 700, color: headingColor, margin: '0 0 8px', letterSpacing: '-0.01em' }"
        :data-olo-editable="'items.' + activeIndex + '.heading'"
      >{{ activeItem.heading }}</div>
      <h3
        v-if="activeItem.title"
        class="oit-title"
        :style="{ fontSize: '28px', fontWeight: 700, color: titleColor, margin: '0 0 12px', letterSpacing: '-0.01em', lineHeight: 1.2 }"
        :data-olo-editable="'items.' + activeIndex + '.title'"
      >{{ activeItem.title }}</h3>
      <p
        v-if="activeItem.content || activeItem.link_text"
        class="oit-content"
        :style="{ fontSize: '15px', color: textColor, lineHeight: 1.6, margin: 0 }"
      >
        <span v-if="activeItem.content" :data-olo-editable="'items.' + activeIndex + '.content'">{{ activeItem.content }}</span>
        <a
          v-if="activeItem.link_text"
          :href="activeItem.link_url || '#'"
          class="oit-link"
          :style="{ color: linkColor, textDecoration: 'underline', fontWeight: 600, marginLeft: '6px' }"
          @click.prevent
          :data-olo-editable="'items.' + activeIndex + '.link_text'"
        >{{ activeItem.link_text }}</a>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

// Token-first link: il blu #2563EB era il fallback del ruolo link del tema
const LINK_TOKEN = 'var(--olo-color-link, #2563eb)';

const defaults = {
  items: [],
  pill_bg: '#F5F2EB',
  active_bg: '',            // '' ⇒ TOKENS.primary (era #e1474f off-brand)
  active_color: '',         // '' ⇒ TOKENS.onPrimary
  inactive_color: '',       // '' ⇒ TOKENS.text
  card_bg: '#F9D7D7',
  card_radius: '16',
  heading_color: '',        // '' ⇒ TOKENS.primary (era #e1474f off-brand)
  title_color: '',          // '' ⇒ TOKENS.text
  text_color: '',           // '' ⇒ TOKENS.textSoft
  link_color: '',           // '' ⇒ token link
  default_index: '0',
};

const s = computed(() => ({ ...defaults, ...(props.settings || {}) }));
const items = computed(() => Array.isArray(s.value.items) ? s.value.items : []);

// Colori risolti token-first (default '' ⇒ token brand/tema)
const headingColor = computed(() => resolveColor(s.value.heading_color, 'var(--olo-color-primary, #e1474f)'));
const titleColor = computed(() => resolveColor(s.value.title_color, TOKENS.text));
const textColor = computed(() => resolveColor(s.value.text_color, TOKENS.textSoft));
const linkColor = computed(() => resolveColor(s.value.link_color, LINK_TOKEN));

const activeIndex = ref(Math.max(0, Math.min(parseInt(s.value.default_index) || 0, Math.max(0, items.value.length - 1))));

function setActive(i) {
  activeIndex.value = i;
}

const activeItem = computed(() => items.value[activeIndex.value] || null);

function tabStyle(i) {
  const active = activeIndex.value === i;
  return {
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    gap: '8px',
    minWidth: '56px',
    height: '48px',
    padding: active ? '0 22px' : '0 14px',
    border: 'none',
    // TOKEN-FIRST: attivo → primary, testo attivo → onPrimary, inattivo → text
    background: active ? resolveColor(s.value.active_bg, 'var(--olo-color-primary, #e1474f)') : 'transparent',
    color: active ? resolveColor(s.value.active_color, TOKENS.onPrimary) : resolveColor(s.value.inactive_color, TOKENS.text),
    borderRadius: '999px',
    cursor: 'pointer',
    fontSize: '14px',
    fontWeight: 600,
    transition: 'all 0.25s ease',
  };
}

const cardStyle = computed(() => ({
  width: '100%',
  background: s.value.card_bg,
  borderRadius: (parseInt(s.value.card_radius) || 16) + 'px',
  padding: '32px',
  boxSizing: 'border-box',
}));

// Render icon: supporta UIkit icon name (usa SVG mappato) o SVG inline raw
const UIKIT_ICONS = {
  location: '<svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.1"><circle cx="10" cy="7.5" r="2"/><path d="M10 .5C6.4.5 3.5 3.4 3.5 7c0 4.9 6.5 12.5 6.5 12.5S16.5 11.9 16.5 7c0-3.6-2.9-6.5-6.5-6.5z"/></svg>',
  tablet:   '<svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.1"><rect x="4.5" y="1.5" width="11" height="17" rx="1.5"/><line x1="4.5" y1="15" x2="15.5" y2="15"/></svg>',
  lock:     '<svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.1"><path d="M10 1.5C7.5 1.5 5.5 3.5 5.5 6v3h-1c-0.5 0-1 0.5-1 1v7c0 0.5 0.5 1 1 1h11c0.5 0 1-0.5 1-1v-7c0-0.5-0.5-1-1-1h-1V6c0-2.5-2-4.5-4.5-4.5zM7 6c0-1.7 1.3-3 3-3s3 1.3 3 3v3H7V6z"/></svg>',
  star:     '<svg width="22" height="22" viewBox="0 0 20 20" fill="currentColor"><polygon points="10,2 12.5,7.5 18.5,8 14,12 15.5,18 10,15 4.5,18 6,12 1.5,8 7.5,7.5"/></svg>',
  heart:    '<svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M10 17.5s-6.5-4-6.5-9a3.5 3.5 0 016.5-2 3.5 3.5 0 016.5 2c0 5-6.5 9-6.5 9z"/></svg>',
  cart:     '<svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.1"><circle cx="7.5" cy="17" r="1.5"/><circle cx="14" cy="17" r="1.5"/><path d="M2 2.5h2l2 10h10l2-7H5"/></svg>',
  phone:    '<svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.1"><path d="M17 14v2.5c0 0.8-0.7 1.5-1.5 1.5C9 18 2 11 2 4.5 2 3.7 2.7 3 3.5 3H6l1.5 4-2 1c1 2 3 4 5 5l1-2 4 1.5z"/></svg>',
  mail:     '<svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.1"><rect x="1.5" y="4" width="17" height="12" rx="1"/><polyline points="1.5,4 10,11 18.5,4"/></svg>',
  check:    '<svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="3,10 8,15 17,5"/></svg>',
  info:     '<svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.1"><circle cx="10" cy="10" r="8.5"/><line x1="10" y1="9" x2="10" y2="14"/><circle cx="10" cy="6.5" r="0.8" fill="currentColor"/></svg>',
  settings: '<svg width="22" height="22" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.1"><circle cx="10" cy="10" r="2.5"/><path d="M10 1v3M10 16v3M19 10h-3M4 10H1M16.4 3.6l-2.1 2.1M5.7 14.3l-2.1 2.1M16.4 16.4l-2.1-2.1M5.7 5.7L3.6 3.6"/></svg>',
};

function renderIcon(icon) {
  if (!icon) return '';
  const s2 = String(icon).trim();
  if (s2.startsWith('<svg')) return s2;
  return UIKIT_ICONS[s2] || UIKIT_ICONS.info;
}
</script>

<style scoped>
.oit-tab:hover:not(.is-active) {
  background: rgba(0, 0, 0, 0.04);
}
/* a11y: anello di focus visibile da tastiera */
.oit-tab:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
.oit-link:focus-visible {
  outline: none;
  border-radius: 3px;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
.oit-icon { display: inline-flex; align-items: center; justify-content: center; }
.oit-icon :deep(svg) { display: block; width: 22px; height: 22px; }
</style>
