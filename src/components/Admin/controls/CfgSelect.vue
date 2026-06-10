<template>
  <div class="cfg-select cfg-select--custom" :class="[sizeClass, { 'is-open': open, 'is-disabled': disabled }]" ref="rootEl">
    <button
      type="button"
      class="csel-trigger"
      :disabled="disabled"
      :aria-expanded="open"
      aria-haspopup="listbox"
      @click="toggle"
      @keydown="onTriggerKeydown"
    >
      <span class="csel-value" :class="{ 'is-placeholder': !selectedOption }">{{ displayLabel }}</span>
    </button>
    <span class="chev">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
    </span>

    <Teleport to="body">
      <div v-if="open" class="csel-backdrop" @click="close()"></div>
      <div
        v-if="open"
        ref="popEl"
        class="cfg-layer csel-pop"
        :style="popStyle"
        role="listbox"
        @keydown="onPopKeydown"
      >
        <div class="csel-list" ref="listEl">
          <button
            v-for="(opt, i) in options"
            :key="String(opt.value)"
            type="button"
            class="csel-item"
            :class="{ 'is-selected': isSelected(opt), 'is-highlighted': i === highlight }"
            role="option"
            :aria-selected="isSelected(opt)"
            @click="pick(opt)"
            @mousemove="highlight = i"
          >
            <span class="csel-item-label">{{ opt.label }}</span>
            <svg v-if="isSelected(opt)" class="csel-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
          </button>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
// Select custom della pagina cfg: rimpiazza i <select> nativi (popup OS non
// stilizzabile, focus ring blu di sistema). Stesso modello dati: emette i
// value originali invariati. Le label arrivano già tradotte dal chiamante.
import { ref, computed, nextTick, onBeforeUnmount } from 'vue';

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  options: { type: Array, default: () => [] }, // [{ value, label }]
  placeholder: { type: String, default: '—' },
  disabled: { type: Boolean, default: false },
  size: { type: String, default: '' }, // '', 'xs', 'sm', 'md', 'lg'
});
const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const highlight = ref(-1);
const rootEl = ref(null);
const popEl = ref(null);
const listEl = ref(null);
const popStyle = ref({});

const sizeClass = computed(() => (props.size ? `cfg-w-${props.size}` : ''));
const selectedOption = computed(() =>
  props.options.find(o => String(o.value) === String(props.modelValue))
);
const displayLabel = computed(() => selectedOption.value?.label ?? props.placeholder);

function isSelected(opt) {
  return String(opt.value) === String(props.modelValue);
}

function toggle() {
  if (props.disabled) return;
  open.value ? close() : openPop();
}

async function openPop() {
  open.value = true;
  highlight.value = Math.max(0, props.options.findIndex(o => isSelected(o)));
  await nextTick();
  position();
  popEl.value?.focus?.();
  // Focus sulla lista per la keyboard nav
  const el = popEl.value?.querySelector('.csel-item.is-selected') || popEl.value?.querySelector('.csel-item');
  el?.focus({ preventScroll: true });
  scrollHighlightIntoView();
  window.addEventListener('resize', close, { once: true });
}

function close(refocus = true) {
  if (!open.value) return;
  open.value = false;
  if (refocus) rootEl.value?.querySelector('.csel-trigger')?.focus();
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
  const popH = Math.min(pop.scrollHeight + 12, 292);
  const below = window.innerHeight - r.bottom;
  const openUp = below < popH + 8 && r.top > popH + 8;
  popStyle.value = {
    position: 'fixed',
    left: `${Math.round(r.left)}px`,
    width: `${Math.round(r.width)}px`,
    top: openUp ? `${Math.round(r.top - popH - 6)}px` : `${Math.round(r.bottom + 6)}px`,
    maxHeight: '292px',
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
    const items = listEl.value?.querySelectorAll('.csel-item');
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
.cfg-select--custom { position: relative; padding: 0; cursor: pointer; }
.cfg-select--custom.is-disabled { opacity: .55; cursor: not-allowed; }
.csel-trigger {
  flex: 1;
  display: flex; align-items: center;
  min-width: 0;
  border: 0; outline: none;
  background: transparent;
  font: inherit; color: inherit;
  padding: 8px 30px 8px 12px;
  cursor: inherit;
  text-align: left;
}
.csel-value { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.csel-value.is-placeholder { color: var(--c-text-faint); }
.cfg-select--custom .chev {
  position: absolute; right: 10px; top: 50%;
  transform: translateY(-50%);
  transition: transform .15s;
  pointer-events: none;
}
.cfg-select--custom.is-open .chev { transform: translateY(-50%) rotate(180deg); }

.csel-backdrop { position: fixed; inset: 0; z-index: 99999; }
.csel-pop {
  background: #fff;
  border: 1px solid var(--c-line);
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(15,23,42,.06), 0 12px 32px rgba(15,23,42,.14);
  overflow: hidden;
  display: flex;
  animation: csel-in .14s ease;
}
@keyframes csel-in {
  from { opacity: 0; transform: translateY(-4px); }
  to   { opacity: 1; transform: translateY(0); }
}
.csel-list { flex: 1; overflow-y: auto; padding: 5px; min-width: 0; }
.csel-item {
  display: flex; align-items: center; gap: 10px;
  width: 100%;
  border: 0; background: transparent;
  font: inherit;
  font-size: 13.5px;
  color: var(--c-text);
  padding: 7px 10px;
  border-radius: 7px;
  cursor: pointer;
  text-align: left;
  outline: none;
}
.csel-item-label { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.csel-item.is-highlighted { background: var(--c-bg); }
.csel-item.is-selected { color: var(--c-navy); font-weight: 600; }
.csel-check { width: 14px; height: 14px; color: var(--c-red); flex-shrink: 0; }
</style>
