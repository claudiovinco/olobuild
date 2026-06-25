<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Breakpoint') }} <em>{{ t('responsive') }}</em></h1>
      <p>{{ t('I punti di transizione dove il layout cambia. Sono le viewport che vedi nell\'editor in alto per testare il design.') }}</p>
    </div>
    <div class="head-actions">
      <button class="cfg-btn cfg-btn-secondary" @click="resetDefaults">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"/><path d="M3 13a9 9 0 1 0 3-6.7L3 9"/></svg>
        {{ t('Ripristina default') }}
      </button>
      <button class="cfg-btn cfg-btn-secondary" @click="addBreakpoint">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        {{ t('Aggiungi breakpoint') }}
      </button>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="14" height="11" rx="1.5"/><rect x="14" y="9" width="8" height="11" rx="1.5"/><path d="M5 20h6"/></svg>
      </div>
      <div>
        <h3>{{ t('Visualizzazione scala') }}</h3>
        <p>{{ t('Anteprima della copertura dei tuoi breakpoint sulle larghezze reali.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="scale-bar">
        <div
          v-for="(s, i) in scaleSegments" :key="s.l + i"
          class="scale-seg"
          :style="{ left: s.left + '%', width: s.pct + '%', background: s.color, color: s.dark ? '#7a1d23' : '#fff' }"
        >{{ s.l }}</div>
      </div>
      <div class="scale-ticks-wrap">
        <span
          v-for="tk in scaleTicks" :key="tk.label + tk.left"
          class="scale-tick"
          :style="{ left: tk.left + '%' }"
        >{{ tk.label }}</span>
      </div>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 9 5-9 5-9-5 9-5z"/><path d="m3 17 9 5 9-5"/><path d="m3 12 9 5 9-5"/></svg>
      </div>
      <div>
        <h3>{{ t('Breakpoint configurati') }}</h3>
        <p>{{ t('Trascina per riordinare. Il primo dall\'alto è il device di default in cui si apre l\'editor.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="bp-header">
        <span></span><span></span>
        <span>{{ t('Nome') }}</span>
        <span>{{ t('Da (px)') }}</span>
        <span>{{ t('A (px)') }}</span>
        <span>{{ t('Default') }}</span>
        <span></span>
      </div>
      <div v-for="(b, i) in breakpoints" :key="b.id" class="bp-row" :class="{ 'is-active': b.is_default }">
        <div class="bp-handle" :title="t('Trascina')">⋮⋮</div>
        <div class="bp-icon">{{ b.icon }}</div>
        <div class="bp-name">{{ b.name }}</div>
        <div class="cfg-input mono">
          <input type="text" :value="b.min" @input="setField(i, 'min', $event.target.value)" />
        </div>
        <div class="cfg-input mono">
          <input type="text" :value="b.max" @input="setField(i, 'max', $event.target.value)" />
        </div>
        <button class="cfg-switch" :class="{ 'is-on': b.is_default }" @click="setDefault(i)" role="switch"></button>
        <button class="cfg-btn-icon cfg-btn-ghost" :title="t('Rimuovi')" @click="removeBreakpoint(i)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L2 19l3 3 7.3-7.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4 2.6-2.6z"/></svg>
      </div>
      <div>
        <h3>{{ t('Comportamento avanzato') }}</h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Strategia generazione CSS') }}</label>
          <div class="hint">{{ t('Mobile-first usa min-width, desktop-first usa max-width.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': advanced.strategy === 'mobile' }"  @click="setAdv('strategy', 'mobile')">{{ t('Mobile-first (consigliata)') }}</button>
            <button :class="{ 'is-on': advanced.strategy === 'desktop' }" @click="setAdv('strategy', 'desktop')">{{ t('Desktop-first') }}</button>
          </div>
        </div>
      </div>
      <div class="bp-moved-hint">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        <span>{{ t('Larghezza container e gutter responsive sono ora in') }} <a href="#" @click.prevent="goToSpaziature">{{ t('Spaziature & layout') }}</a></span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const DEFAULT_BPS = [
  { id: 'desktop_xl', name: 'Desktop XL', min: '1440', max: '∞',    icon: '🖥️', is_default: false },
  { id: 'desktop',    name: 'Desktop',    min: '1200', max: '1439', icon: '🖥️', is_default: true  },
  { id: 'laptop',     name: 'Laptop',     min: '992',  max: '1199', icon: '💻', is_default: false },
  { id: 'tablet',     name: 'Tablet',     min: '768',  max: '991',  icon: '📱', is_default: false },
  { id: 'mobile_l',   name: 'Mobile L',   min: '576',  max: '767',  icon: '📱', is_default: false },
  { id: 'mobile',     name: 'Mobile',     min: '0',    max: '575',  icon: '📱', is_default: false },
];

const breakpoints = ref(JSON.parse(JSON.stringify(DEFAULT_BPS)));
const advanced = ref({ strategy: 'mobile' });
const fullStyles = ref({});   // tutto olo_styles, per non perdere gli altri blocchi al PUT

const PALETTE = ['#fde2e4', '#fbd5d8', '#f5959c', '#ec5a62', '#c8323a', '#8a1f24', '#5a1015'];
const INFINITE_SHARE = 15; // % a destra riservata al breakpoint senza max

function parseMin(b) { const n = parseInt(b.min, 10); return isNaN(n) ? 0 : n; }
function parseMax(b) {
  if (b.max === '∞' || b.max === '' || b.max === undefined) return null;
  const n = parseInt(b.max, 10);
  return isNaN(n) ? null : n;
}

const sortedBps = computed(() => {
  return [...breakpoints.value].sort((a, b) => parseMin(a) - parseMin(b));
});

const refMax = computed(() => {
  const finite = sortedBps.value.map(parseMax).filter(v => v !== null);
  return finite.length ? Math.max(...finite) : 1920;
});

const scaleSegments = computed(() => {
  const ref = refMax.value || 1920;
  const finiteShare = 100 - INFINITE_SHARE;
  let cumLeft = 0;
  return sortedBps.value.map((bp, i) => {
    const min = parseMin(bp);
    const max = parseMax(bp);
    const isInfinite = max === null;
    const pct = isInfinite
      ? INFINITE_SHARE
      : Math.max(0, ((max - min) / ref) * finiteShare);
    const colorIdx = sortedBps.value.length - 1 - i;
    const seg = {
      l: bp.name || '',
      pct,
      left: cumLeft,
      color: PALETTE[Math.min(colorIdx, PALETTE.length - 1)],
      dark: colorIdx >= PALETTE.length - 4,
    };
    cumLeft += pct;
    return seg;
  });
});

const scaleTicks = computed(() => {
  const segs = scaleSegments.value;
  const ticks = segs.map((s, i) => ({
    label: parseMin(sortedBps.value[i]).toString(),
    left: s.left,
  }));
  ticks.push({ label: '∞', left: 100 });
  return ticks;
});

function setField(i, k, v) { breakpoints.value[i][k] = v; setDirty(true); }
function setDefault(i) {
  breakpoints.value.forEach((b, idx) => { b.is_default = idx === i; });
  setDirty(true);
}
function removeBreakpoint(i) {
  if (breakpoints.value[i].is_default) {
    showToast(t('Non puoi rimuovere il breakpoint di default'), 'error'); return;
  }
  if (!confirm(t('Rimuovere questo breakpoint?'))) return;
  breakpoints.value.splice(i, 1); setDirty(true);
}
function addBreakpoint() {
  breakpoints.value.push({ id: 'custom_' + Date.now(), name: 'Custom', min: '0', max: '999', icon: '📱', is_default: false });
  setDirty(true);
}
function resetDefaults() {
  if (!confirm(t('Ripristinare i breakpoint di default? I tuoi custom andranno persi.'))) return;
  breakpoints.value = JSON.parse(JSON.stringify(DEFAULT_BPS));
  setDirty(true);
}
function setAdv(k, v) { advanced.value[k] = v; setDirty(true); }
function goToSpaziature() {
  const url = new URL(window.location.href);
  url.searchParams.set('tab', 'spaziature');
  window.location.href = url.toString();
}

async function loadSettings() {
  try {
    const res = await fetch(`${window.oloData.restUrl}styles`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      fullStyles.value = data.styles || {};
      const s = data.styles || {};
      if (Array.isArray(s.breakpoints) && s.breakpoints.length) breakpoints.value = s.breakpoints;
      if (s.breakpoint_strategy) advanced.value.strategy = s.breakpoint_strategy;
    }
  } catch (e) { /* defaults */ }
}

async function saveSettings() {
  try {
    const payload = { ...fullStyles.value, breakpoints: breakpoints.value, breakpoint_strategy: advanced.value.strategy };
    const res = await fetch(`${window.oloData.restUrl}styles`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(payload),
    });
    if (!res.ok) throw new Error();
    fullStyles.value = payload;
  } catch (e) { showToast(t('Errore di salvataggio breakpoint'), 'error'); }
}

const onSave = () => saveSettings();
const onDiscard = () => loadSettings();

onMounted(() => {
  loadSettings();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.scale-bar { position: relative; height: 60px; background: var(--c-bg); border-radius: 10px; border: 1px solid var(--c-line-soft); overflow: hidden; }
.scale-seg { position: absolute; top: 0; bottom: 0; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 12px; border-right: 1px solid rgba(0,0,0,.08); overflow: hidden; white-space: nowrap; }
.scale-seg:last-child { border-right: 0; }
.scale-ticks-wrap { position: relative; height: 18px; margin-top: 8px; }
.scale-tick { position: absolute; top: 0; transform: translateX(-50%); font-family: var(--c-mono); font-size: 11px; color: var(--c-text-faint); white-space: nowrap; }
.scale-tick:first-child { transform: translateX(0); }
.scale-tick:last-child { transform: translateX(-100%); }
.bp-header { display: grid; grid-template-columns: 28px 36px 1fr 100px 100px 80px 36px; gap: 10px; font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--c-text-faint); padding: 0 12px 8px; }
.bp-row { display: grid; grid-template-columns: 28px 36px 1fr 100px 100px 80px 36px; gap: 10px; align-items: center; padding: 10px 12px; background: #fff; border: 1px solid var(--c-line-soft); border-radius: 10px; margin-bottom: 8px; }
.bp-row.is-active { border: 1.5px solid var(--c-red-soft-2); }
.bp-handle { color: var(--c-text-faint); cursor: grab; display: grid; place-items: center; user-select: none; }
.bp-icon { font-size: 18px; text-align: center; }
.bp-name { font-weight: 600; font-size: 14px; color: var(--c-navy); }
.bp-moved-hint {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 12px;
  padding: 10px 14px;
  background: var(--c-warning-soft);
  border: 1px dashed var(--c-warning);
  border-radius: 8px;
  font-size: 12.5px;
  color: var(--c-warning);
}
.bp-moved-hint svg { flex-shrink: 0; color: var(--c-warning); }
.bp-moved-hint a { color: var(--c-warning); font-weight: 600; text-decoration: underline; cursor: pointer; }
.bp-moved-hint a:hover { color: var(--c-navy); }
</style>
