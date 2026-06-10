<template>
  <!-- Segmented (auto): per select brevi i bottoni sono più rapidi del dropdown
       (1 click invece di 2, opzioni sempre visibili). Stesso modello dati del
       select: emette gli stessi value stringa — è SOLO una resa alternativa. -->
  <div v-if="isSegmented" class="fsel-seg" role="radiogroup">
    <button
      v-for="opt in options"
      :key="opt.value"
      type="button"
      role="radio"
      :aria-checked="isActive(opt)"
      class="fsel-seg-btn"
      :class="{ 'fsel-seg-btn--active': isActive(opt) }"
      :title="t(opt.label)"
      @click="$emit('update:modelValue', opt.value)"
    >{{ t(opt.label) }}</button>
  </div>

  <!-- Dropdown custom: il popup nativo del browser non è stilizzabile
       (evidenziato blu di sistema). Stesso modello dati: emette i value
       originali invariati. -->
  <div v-else class="fsel" ref="rootEl">
    <button
      type="button"
      class="fsel-trigger"
      :class="{ 'fsel-trigger--compact': size === 'compact', 'fsel-trigger--dark': theme === 'dark' }"
      :aria-expanded="open"
      :aria-label="ariaLabel || undefined"
      aria-haspopup="listbox"
      @click="toggle"
      @keydown="onTriggerKeydown"
    >
      <span class="fsel-value">{{ displayLabel }}</span>
      <svg class="fsel-chev" :class="{ 'fsel-chev--open': open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
    </button>

    <Teleport to="body">
      <div v-if="open" class="fsel-backdrop" @click="close()"></div>
      <div
        v-if="open"
        ref="popEl"
        class="fsel-pop"
        :class="{ 'fsel-pop--dark': theme === 'dark' }"
        :style="popStyle"
        role="listbox"
        @keydown="onPopKeydown"
      >
        <button
          v-for="(opt, i) in options"
          :key="String(opt.value)"
          type="button"
          class="fsel-item"
          :class="{ 'fsel-item--selected': isActive(opt), 'fsel-item--hl': i === highlight }"
          role="option"
          :aria-selected="isActive(opt)"
          @click="pick(opt)"
          @mousemove="highlight = i"
        >
          <span class="fsel-item-label">{{ t(opt.label) }}</span>
          <svg v-if="isActive(opt)" class="fsel-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
        </button>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { ref, computed, nextTick, onBeforeUnmount } from 'vue';

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  options: { type: Array, default: () => [] },
  // 'auto' (default): segmented se 2-4 opzioni con label corte; 'segmented' /
  // 'dropdown' forzano la resa. Dal config si passa con `ui: 'dropdown'`.
  ui: { type: String, default: 'auto' },
  // 'compact': trigger ridotto per select inline (es. selettore unità accanto
  // a un input). Riguarda solo la resa dropdown.
  size: { type: String, default: 'normal' },
  // 'dark': trigger e popup scuri per i pannelli scuri del builder
  // (DynamicQueryPanel, TemplateLibrary, ecc.).
  theme: { type: String, default: 'light' },
  ariaLabel: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const SEG_MAX_OPTIONS = 4;
const SEG_MAX_LABEL = 11; // char: oltre, le label vanno strette e si troncano

const isSegmented = computed(() => {
  if (props.ui === 'dropdown') return false;
  if (props.ui === 'segmented') return true;
  const opts = props.options || [];
  if (opts.length < 2 || opts.length > SEG_MAX_OPTIONS) return false;
  return opts.every(o => String(t(o.label || '')).length <= SEG_MAX_LABEL);
});

function isActive(opt) {
  return String(opt.value) === String(props.modelValue);
}

// ── Dropdown custom ──────────────────────────────────────────────────
const open = ref(false);
const highlight = ref(-1);
const rootEl = ref(null);
const popEl = ref(null);
const popStyle = ref({});

const displayLabel = computed(() => {
  const sel = props.options.find(o => isActive(o));
  return sel ? t(sel.label) : '—';
});

function toggle() {
  open.value ? close() : openPop();
}

async function openPop() {
  open.value = true;
  highlight.value = Math.max(0, props.options.findIndex(o => isActive(o)));
  await nextTick();
  position();
  const el = popEl.value?.querySelector('.fsel-item--selected') || popEl.value?.querySelector('.fsel-item');
  el?.focus({ preventScroll: true });
  scrollHighlightIntoView();
  window.addEventListener('resize', close, { once: true });
  // Lo scroll dell'inspector lascerebbe il popup fixed orfano: chiudiamo.
  window.addEventListener('scroll', onAnyScroll, { capture: true });
}

function onAnyScroll(e) {
  if (popEl.value && popEl.value.contains(e.target)) return; // scroll interno ok
  close(false);
}

function close(refocus = true) {
  if (!open.value) return;
  open.value = false;
  window.removeEventListener('scroll', onAnyScroll, { capture: true });
  if (refocus) rootEl.value?.querySelector('.fsel-trigger')?.focus();
}

function pick(opt) {
  emit('update:modelValue', opt.value);
  close();
}

function position() {
  const trigger = rootEl.value;
  const pop = popEl.value;
  if (!trigger || !pop) return;
  const r = trigger.getBoundingClientRect();
  const popH = Math.min(pop.scrollHeight + 8, 264);
  const below = window.innerHeight - r.bottom;
  const openUp = below < popH + 8 && r.top > popH + 8;
  // I trigger compact possono essere molto stretti (es. 44px per "px"):
  // il popup ha comunque una larghezza leggibile, clampata nel viewport.
  const popW = Math.max(Math.round(r.width), 110);
  const left = Math.max(8, Math.min(Math.round(r.left), window.innerWidth - popW - 8));
  popStyle.value = {
    position: 'fixed',
    left: `${left}px`,
    width: `${popW}px`,
    top: openUp ? `${Math.round(r.top - popH - 4)}px` : `${Math.round(r.bottom + 4)}px`,
    maxHeight: '264px',
    zIndex: 100000,
  };
}

function move(delta) {
  if (!props.options.length) return;
  const n = props.options.length;
  highlight.value = ((highlight.value + delta) % n + n) % n;
  scrollHighlightIntoView();
}

function scrollHighlightIntoView() {
  nextTick(() => {
    const items = popEl.value?.querySelectorAll('.fsel-item');
    items?.[highlight.value]?.scrollIntoView({ block: 'nearest' });
  });
}

function onTriggerKeydown(e) {
  if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(e.key)) {
    e.preventDefault();
    if (!open.value) openPop();
  }
}

function onPopKeydown(e) {
  if (e.key === 'ArrowDown') { e.preventDefault(); move(1); }
  else if (e.key === 'ArrowUp') { e.preventDefault(); move(-1); }
  else if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault();
    const opt = props.options[highlight.value];
    if (opt) pick(opt);
  } else if (e.key === 'Escape' || e.key === 'Tab') {
    e.preventDefault();
    close();
  }
}

onBeforeUnmount(() => close(false));
</script>

<style scoped>
.fsel-seg {
  display: flex;
  width: 100%;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 2px;
  gap: 2px;
}
.fsel-seg-btn {
  flex: 1 1 0;
  min-width: 0;
  padding: 5px 4px;
  border: none;
  background: transparent;
  border-radius: 6px;
  font-size: 12px;
  line-height: 1.2;
  color: #6b7280;
  cursor: pointer;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  transition: background 0.12s, color 0.12s, box-shadow 0.12s;
}
.fsel-seg-btn:hover {
  color: #1f2937;
}
.fsel-seg-btn:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: -2px;
}
.fsel-seg-btn--active {
  background: #fff;
  color: #111827;
  font-weight: 600;
  box-shadow: 0 1px 2px rgba(16, 24, 40, 0.1), 0 0 0 1px rgba(16, 24, 40, 0.06);
}

/* Trigger: stessa resa del vecchio select nativo dell'inspector */
.fsel { position: relative; width: 100%; }
.fsel-trigger {
  display: flex;
  align-items: center;
  gap: 6px;
  width: 100%;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 6px 8px;
  font-size: 13px;
  color: #111827;
  cursor: pointer;
  text-align: left;
}
.fsel-trigger:hover { border-color: #9ca3af; }
.fsel-trigger:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: -1px;
}
.fsel-trigger--compact {
  padding: 4px 6px;
  font-size: 11.5px;
  gap: 3px;
  border-radius: 5px;
}
.fsel-trigger--compact .fsel-chev { width: 11px; height: 11px; }
.fsel-trigger--dark {
  background: #111827;
  border-color: #374151;
  color: #e5e7eb;
}
.fsel-trigger--dark:hover { border-color: #4b5563; }
.fsel-trigger--dark .fsel-chev { color: #6b7280; }
.fsel-value {
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.fsel-chev {
  width: 13px; height: 13px;
  color: #9ca3af;
  flex-shrink: 0;
  transition: transform .15s;
}
.fsel-chev--open { transform: rotate(180deg); }

.fsel-backdrop { position: fixed; inset: 0; z-index: 99999; }
.fsel-pop {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(16, 24, 40, 0.05), 0 12px 28px rgba(16, 24, 40, 0.14);
  overflow-y: auto;
  padding: 4px;
  animation: fsel-in .12s ease;
}
@keyframes fsel-in {
  from { opacity: 0; transform: translateY(-3px); }
  to   { opacity: 1; transform: translateY(0); }
}
.fsel-item {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  border: 0;
  background: transparent;
  font-size: 12.5px;
  color: #374151;
  padding: 6px 8px;
  border-radius: 5px;
  cursor: pointer;
  text-align: left;
  outline: none;
}
.fsel-item-label {
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.fsel-item--hl { background: #f3f4f6; }
.fsel-item--selected { color: #111827; font-weight: 600; }
.fsel-pop--dark {
  background: #1f2937;
  border-color: #374151;
}
.fsel-pop--dark .fsel-item { color: #d1d5db; }
.fsel-pop--dark .fsel-item--hl { background: #374151; }
.fsel-pop--dark .fsel-item--selected { color: #f9fafb; }
.fsel-check {
  width: 13px; height: 13px;
  color: var(--olo-ui-accent, #e8622a);
  flex-shrink: 0;
}
</style>
