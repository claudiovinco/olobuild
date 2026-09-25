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
        :class="{ 'has-search': showSearch }"
        :style="popStyle"
        :role="showSearch ? null : 'listbox'"
        @keydown="onPopKeydown"
      >
        <input
          v-if="showSearch"
          ref="searchEl"
          v-model="query"
          type="search"
          class="csel-search"
          :placeholder="t('Cerca…')"
          :aria-label="t('Cerca')"
          autocomplete="off"
        />
        <div class="csel-list" ref="listEl" :role="showSearch ? 'listbox' : null">
          <button
            v-for="(opt, i) in filtered"
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
          <div v-if="remoteSearch && remoteState === 'loading'" class="csel-empty" role="status">{{ t('Ricerca…') }}</div>
          <div v-else-if="remoteSearch && remoteState === 'error'" class="csel-empty" role="alert">{{ t('Ricerca non riuscita: scrivi di nuovo o riapri il menu.') }}</div>
          <div v-else-if="noMatch" class="csel-empty">{{ t('Nessun risultato') }}</div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
// Select custom della pagina cfg: rimpiazza i <select> nativi (popup OS non
// stilizzabile, focus ring blu di sistema). Stesso modello dati: emette i
// value originali invariati. Le label arrivano già tradotte dal chiamante.
import { ref, computed, watch, nextTick, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  options: { type: Array, default: () => [] }, // [{ value, label }]
  placeholder: { type: String, default: '—' },
  disabled: { type: Boolean, default: false },
  size: { type: String, default: '' }, // '', 'xs', 'sm', 'md', 'lg'
  // Campo di ricerca in testa al menu quando le voci sono più di SEARCH_MIN
  // (es. i template). Spento di serie: gli altri menu restano come sono.
  searchable: { type: Boolean, default: false },
  // Ricerca sul server, facoltativa: async (testo) => [{ value, label }]. Chiamata
  // all'apertura (testo '') e a ogni ricerca; i risultati seguono le voci di
  // `options` (la voce vuota e quella scelta), senza doppioni. Senza, come prima.
  remoteSearch: { type: Function, default: null },
});
const emit = defineEmits(['update:modelValue']);

const SEARCH_MIN = 12;
const REMOTE_DELAY = 250;

const open = ref(false);
const highlight = ref(-1);
const rootEl = ref(null);
const popEl = ref(null);
const listEl = ref(null);
const searchEl = ref(null);
const popStyle = ref({});
const query = ref('');
let openUp = false;

const remote = ref([]);          // voci della ricerca sul server
const remoteState = ref('idle'); // 'idle' | 'loading' | 'ready' | 'error'
let remoteSeq = 0;
let remoteTimer = null;

const showSearch = computed(() => !!props.remoteSearch || (props.searchable && props.options.length > SEARCH_MIN));
// Confronto senza maiuscole né accenti («perché» trova «Perche»).
function fold(s) {
  return String(s ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
}
// La voce vuota in testa (valore 0 / '') resta sempre, per poter togliere la scelta.
function isEmptyValue(v) {
  return v === 0 || v === '' || v === '0' || v == null;
}
const filtered = computed(() => {
  const q = fold(query.value).trim();
  const base = (!showSearch.value || !q)
    ? props.options
    : props.options.filter((o, i) => (i === 0 && isEmptyValue(o.value)) || fold(o.label).includes(q));
  if (!props.remoteSearch) return base;
  const seen = new Set(base.map(o => String(o.value)));
  return base.concat(remote.value.filter(o => !seen.has(String(o.value))));
});
const noMatch = computed(() =>
  showSearch.value && fold(query.value).trim() !== '' && !filtered.value.some(o => !isEmptyValue(o.value))
);

// Una risposta superata da una ricerca più recente (o dalla chiusura) si scarta.
function runRemote(q) {
  clearTimeout(remoteTimer);
  const mine = ++remoteSeq;
  remoteState.value = 'loading';
  Promise.resolve()
    .then(() => props.remoteSearch(q))
    .then((items) => {
      if (mine !== remoteSeq) return;
      remote.value = Array.isArray(items) ? items : [];
      remoteState.value = 'ready';
      if (fold(query.value).trim()) {
        const first = filtered.value.findIndex(o => !isEmptyValue(o.value));
        highlight.value = first >= 0 ? first : -1;
      }
    })
    .catch(() => {
      if (mine !== remoteSeq) return;
      remote.value = [];
      remoteState.value = 'error';
    })
    .then(() => {
      if (mine === remoteSeq && open.value) nextTick(() => position(true));
    });
}

function stopRemote() {
  clearTimeout(remoteTimer);
  remoteSeq++;
  remote.value = [];
  remoteState.value = 'idle';
}

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
  if (props.remoteSearch) runRemote('');
  await nextTick();
  position();
  popEl.value?.focus?.();
  if (searchEl.value) {
    // Con la ricerca si scrive subito; frecce e Invio agiscono sulle voci trovate.
    searchEl.value.focus({ preventScroll: true });
  } else {
    // Focus sulla lista per la keyboard nav
    const el = popEl.value?.querySelector('.csel-item.is-selected') || popEl.value?.querySelector('.csel-item');
    el?.focus({ preventScroll: true });
  }
  scrollHighlightIntoView();
  window.addEventListener('resize', close, { once: true });
}

function close(refocus = true) {
  if (!open.value) return;
  open.value = false;
  query.value = '';
  if (props.remoteSearch) stopRemote();
  if (refocus) rootEl.value?.querySelector('.csel-trigger')?.focus();
}

// A ogni ricerca si evidenzia la prima voce trovata (o la scelta, a campo vuoto)
// e il menu resta attaccato al campo dalla stessa parte in cui si è aperto.
// Senza risultati non si evidenzia niente: Invio non deve scegliere la voce vuota
// e azzerare la scelta (resta sceglibile con le frecce o col clic).
watch(query, (q) => {
  if (!open.value) return;
  if (props.remoteSearch) {
    // I risultati della ricerca precedente non valgono per questa: via subito.
    clearTimeout(remoteTimer);
    remoteSeq++;
    remote.value = [];
    remoteState.value = 'loading';
    remoteTimer = setTimeout(() => runRemote(String(q ?? '').trim()), REMOTE_DELAY);
  }
  const list = filtered.value;
  if (fold(q).trim()) {
    const first = list.findIndex(o => !isEmptyValue(o.value));
    highlight.value = first >= 0 ? first : -1;
  } else {
    highlight.value = Math.max(0, list.findIndex(o => isSelected(o)));
  }
  nextTick(() => { position(true); scrollHighlightIntoView(); });
});

function pick(opt) {
  emit('update:modelValue', opt.value);
  close();
}

function position(keepSide = false) {
  const trigger = rootEl.value;
  const pop = popEl.value;
  if (!trigger || !pop) return;
  const r = trigger.getBoundingClientRect();
  const popH = Math.min(pop.scrollHeight + 12, 292);
  if (!keepSide) {
    const below = window.innerHeight - r.bottom;
    openUp = below < popH + 8 && r.top > popH + 8;
  }
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
  if (!filtered.value.length) return;
  const n = filtered.value.length;
  // Da «nessuna voce evidenziata» (ricerca senza risultati): giù va alla prima, su all'ultima.
  const from = highlight.value < 0 ? (delta > 0 ? -1 : n) : highlight.value;
  highlight.value = ((from + delta) % n + n) % n;
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
  // Nel campo di ricerca lo spazio si scrive, non sceglie.
  if (e.key === ' ' && e.target === searchEl.value) return;
  if (e.key === 'ArrowDown') { e.preventDefault(); move(1); }
  else if (e.key === 'ArrowUp') { e.preventDefault(); move(-1); }
  else if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault();
    const opt = highlight.value >= 0 ? filtered.value[highlight.value] : null;
    if (opt) pick(opt);
  } else if (e.key === 'Escape' || e.key === 'Tab') {
    e.preventDefault();
    close();
  }
}

onBeforeUnmount(() => { close(false); clearTimeout(remoteTimer); });
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

/* Ricerca (prop searchable) */
.csel-pop.has-search { flex-direction: column; }
.csel-search {
  flex: 0 0 auto;
  margin: 6px 6px 0;
  min-height: 0;
  padding: 6px 10px;
  border: 1px solid var(--c-line);
  border-radius: 7px;
  background: transparent;
  font: inherit;
  font-size: 13px;
  line-height: 1.4;
  color: var(--c-text);
}
.csel-search:focus-visible { outline: 2px solid var(--c-red); outline-offset: 1px; box-shadow: none; }
.csel-empty { padding: 7px 10px; font-size: 12.5px; color: var(--c-text-mute); }
</style>
