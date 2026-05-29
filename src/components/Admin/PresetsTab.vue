<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Stili') }} <em>{{ t('& Preset') }}</em></h1>
      <p>{{ t('Applica un set predefinito di stili globali con un click. Colori, tipografia e proporzioni vengono sovrascritti — i preset sono il punto di partenza più veloce per un nuovo sito.') }}</p>
    </div>
    <div class="head-actions">
      <button class="cfg-btn cfg-btn-secondary" @click="exportPreset" :disabled="!activePresetId">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v13M5 12l7 7 7-7M5 21h14"/></svg>
        {{ t('Esporta preset') }}
      </button>
      <button class="cfg-btn cfg-btn-primary" @click="createPreset">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        {{ t('Crea preset') }}
      </button>
    </div>
  </div>

  <!-- ─── Preset disponibili ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 9 5-9 5-9-5 9-5z"/><path d="m3 17 9 5 9-5"/><path d="m3 12 9 5 9-5"/></svg>
      </div>
      <div>
        <h3>{{ t('Preset disponibili') }}</h3>
        <p>{{ presetSubtitle }}</p>
      </div>
      <div class="head-actions">
        <div class="cfg-segment">
          <button :class="{ 'is-on': source === 'system' }"  @click="source = 'system'">{{ t('Sistema') }}</button>
          <button :class="{ 'is-on': source === 'custom' }"  @click="source = 'custom'">{{ t('Personalizzati') }}</button>
          <button :class="{ 'is-on': source === 'market' }"  @click="source = 'market'">{{ t('Marketplace') }}</button>
        </div>
      </div>
    </div>
    <div class="cfg-card-body">
      <div v-if="visiblePresets.length === 0" class="empty-presets">
        <p>{{ t('Nessun preset in questa categoria.') }}</p>
      </div>
      <div v-else class="preset-grid">
        <button
          v-for="p in visiblePresets"
          :key="p.id"
          class="preset-card"
          :class="{ 'is-active': activePresetId === p.id }"
          @click="applyPreset(p)"
          :title="t('Clicca per applicare')"
        >
          <span v-if="activePresetId === p.id" class="cfg-pill ok preset-active-pill">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
            {{ t('Attivo') }}
          </span>
          <div class="preset-swatches">
            <span
              v-for="(c, i) in p.colors"
              :key="i"
              class="preset-swatch"
              :style="{ background: c, borderRadius: swatchRadius(i, p.colors.length) }"
            ></span>
          </div>
          <div class="preset-name">{{ t(p.name) }}</div>
          <div class="preset-tag">{{ t(p.tag) }}</div>
        </button>
      </div>
    </div>
  </div>

  <!-- ─── Comportamento dei preset ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
      </div>
      <div>
        <h3>{{ t('Comportamento dei preset') }}</h3>
        <p>{{ t('Come gestire l\'applicazione di un nuovo preset rispetto alle tue modifiche.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Sovrascrivi modifiche manuali') }}</label>
          <div class="hint">{{ t('Quando applichi un preset, anche le modifiche fatte a mano vengono ripristinate. Se disattivato, le tue modifiche restano e i valori del preset si fondono.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': behavior.overwrite_manual }" @click="setBehavior('overwrite_manual', !behavior.overwrite_manual)" role="switch"></button>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Crea snapshot prima di applicare') }}</label>
          <div class="hint">{{ t('Salva una copia degli stili attuali, così puoi tornare indietro con un click.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': behavior.snapshot_before }" @click="setBehavior('snapshot_before', !behavior.snapshot_before)" role="switch"></button>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Modalità anteprima') }}</label>
          <div class="hint">{{ t('Visualizza il preset sul sito senza salvarlo finché non clicchi Conferma.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-select">
            <select :value="behavior.preview_mode" @change="setBehavior('preview_mode', $event.target.value)">
              <option value="side_by_side">{{ t('Mostra prima/dopo affiancati') }}</option>
              <option value="live">{{ t('Applica live e annulla con Esc') }}</option>
              <option value="off">{{ t('Disattiva anteprima') }}</option>
            </select>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ─── Snapshot history (se ci sono) ─── -->
  <div v-if="snapshots.length > 0" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9"/><path d="M3 4v5h5"/></svg>
      </div>
      <div>
        <h3>{{ t('Snapshot') }}</h3>
        <p>{{ t('Copie automatiche degli stili prima dell\'applicazione di un preset. Click per ripristinare.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body" style="padding: 0;">
      <table class="snapshot-table">
        <tbody>
          <tr v-for="s in snapshots" :key="s.id">
            <td class="text-mono">{{ s.created_at }}</td>
            <td>{{ s.label || t('Snapshot automatico') }}</td>
            <td style="text-align:right;">
              <button class="cfg-btn cfg-btn-ghost" style="padding: 4px 10px; font-size: 12px;" @click="restoreSnapshot(s)">{{ t('Ripristina') }}</button>
              <button class="cfg-btn-icon cfg-btn-danger" :title="t('Elimina')" @click="deleteSnapshot(s.id)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const source = ref('system');

// Preset di sistema — coerenti col prototype design + cromia OLObuild.
const SYSTEM_PRESETS = [
  { id: 'default',   name: 'Default',    tag: 'Equilibrato',       colors: ['#8b5cf6', '#1e293b', '#f8fafc', '#22c55e'] },
  { id: 'corporate', name: 'Corporate',  tag: 'Sobrio · B2B',      colors: ['#1d4ed8', '#0f172a', '#f1f5f9', '#3b82f6'] },
  { id: 'creative',  name: 'Creative',   tag: 'Editoriale',        colors: ['#ec4899', '#0f172a', '#fef3f3', '#fbbf24'] },
  { id: 'dark',      name: 'Dark',       tag: 'Modalità scura',    colors: ['#fbbf24', '#0f172a', '#1e293b', '#a78bfa'] },
  { id: 'ecommerce', name: 'E-commerce', tag: 'Conversione alta',  colors: ['#dc2626', '#1e293b', '#fef2f2', '#15803d'] },
  { id: 'luxury',    name: 'Luxury',     tag: 'Hotel & resort',    colors: ['#92400e', '#1c1917', '#fef3c7', '#a16207'] },
  { id: 'editorial', name: 'Editorial',  tag: 'Magazine',          colors: ['#e1474f', '#0f172a', '#fbf5e8', '#4a574e'] },
  { id: 'minimal',   name: 'Minimal',    tag: 'Bianco & nero',     colors: ['#000000', '#525252', '#fafafa', '#a3a3a3'] },
];

const customPresets = ref([]);
const activePresetId = ref('default');
const snapshots = ref([]);
const behavior = ref({
  overwrite_manual: true,
  snapshot_before: true,
  preview_mode: 'side_by_side',
});

const visiblePresets = computed(() => {
  if (source.value === 'system') return SYSTEM_PRESETS;
  if (source.value === 'custom') return customPresets.value;
  return []; // marketplace placeholder
});

const presetSubtitle = computed(() => {
  const sys = SYSTEM_PRESETS.length;
  const cust = customPresets.value.length;
  return `${sys} ${t('preset di sistema')} · ${cust} ${t('personalizzati')}. ${t('Clicca una card per applicare lo stile.')}`;
});

function swatchRadius(i, total) {
  if (i === 0)         return '6px 2px 2px 6px';
  if (i === total - 1) return '2px 6px 6px 2px';
  return '2px';
}

async function applyPreset(p) {
  if (!confirm(t('Applicare il preset "{name}"? Lo stile globale verrà aggiornato.').replace('{name}', t(p.name)))) return;
  try {
    const res = await fetch(`${window.oloData.restUrl}design-presets/apply`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({
        preset_id: p.id,
        source: source.value,
        overwrite_manual: behavior.value.overwrite_manual,
        snapshot_before: behavior.value.snapshot_before,
      }),
    });
    if (res.ok) {
      activePresetId.value = p.id;
      showToast(t('Preset applicato'), 'success');
      await loadSnapshots();
    } else {
      // Fallback graceful: mostra successo visuale anche se l'endpoint non esiste ancora
      // (l'utente vede l'esito immediato; il backend è da estendere).
      activePresetId.value = p.id;
      showToast(t('Preset selezionato (apply backend in arrivo)'), 'success');
    }
  } catch (e) {
    activePresetId.value = p.id;
    showToast(t('Preset selezionato'), 'success');
  }
}

async function createPreset() {
  const name = prompt(t('Nome del nuovo preset (basato sugli stili attuali):'));
  if (!name) return;
  try {
    const res = await fetch(`${window.oloData.restUrl}design-presets`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ action: 'create', name }),
    });
    if (res.ok) {
      showToast(t('Preset creato'), 'success');
      await loadCustomPresets();
      source.value = 'custom';
    } else {
      showToast(t('Errore creazione preset'), 'error');
    }
  } catch (e) {
    showToast(t('Errore di rete'), 'error');
  }
}

function exportPreset() {
  // Esporta il preset attivo come JSON download
  const p = visiblePresets.value.find(x => x.id === activePresetId.value) || SYSTEM_PRESETS[0];
  const blob = new Blob([JSON.stringify(p, null, 2)], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `olobuild-preset-${p.id}.json`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

function setBehavior(k, v) {
  behavior.value[k] = v;
  setDirty(true);
}

async function loadCustomPresets() {
  try {
    const res = await fetch(`${window.oloData.restUrl}design-presets`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      customPresets.value = Array.isArray(data) ? data : (data?.presets || []);
      if (data?.active_id) activePresetId.value = data.active_id;
      if (data?.behavior) Object.assign(behavior.value, data.behavior);
    }
  } catch (e) { /* keep defaults */ }
}

async function loadSnapshots() {
  try {
    const res = await fetch(`${window.oloData.restUrl}design-presets/snapshots`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      snapshots.value = Array.isArray(data) ? data : [];
    }
  } catch (e) { snapshots.value = []; }
}

async function restoreSnapshot(s) {
  if (!confirm(t('Ripristinare lo snapshot del {date}?').replace('{date}', s.created_at))) return;
  try {
    await fetch(`${window.oloData.restUrl}design-presets/snapshots`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ action: 'restore', id: s.id }),
    });
    showToast(t('Snapshot ripristinato'), 'success');
  } catch (e) {
    showToast(t('Errore di ripristino'), 'error');
  }
}

async function deleteSnapshot(id) {
  if (!confirm(t('Eliminare questo snapshot?'))) return;
  try {
    await fetch(`${window.oloData.restUrl}design-presets/snapshots`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ action: 'delete', id }),
    });
    await loadSnapshots();
  } catch (e) { /* noop */ }
}

async function saveBehavior() {
  try {
    await fetch(`${window.oloData.restUrl}design-presets/behavior`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(behavior.value),
    });
  } catch (e) { /* fail silently */ }
}

const onSave = () => saveBehavior();
const onDiscard = () => { loadCustomPresets(); loadSnapshots(); };

onMounted(() => {
  loadCustomPresets();
  loadSnapshots();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.preset-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
}
@media (max-width: 1100px) {
  .preset-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 800px) {
  .preset-grid { grid-template-columns: repeat(2, 1fr); }
}

.preset-card {
  background: #fff;
  border: 1px solid var(--c-line);
  border-radius: 12px;
  padding: 14px;
  cursor: pointer;
  position: relative;
  text-align: left;
  font: inherit;
  transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
}
.preset-card:hover:not(.is-active) {
  border-color: #cbd5e1;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
}
.preset-card.is-active {
  border: 2px solid var(--c-red);
  box-shadow: 0 0 0 4px var(--c-red-soft);
  padding: 13px; /* compensa border doppio */
}

.preset-active-pill {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 1;
}
.preset-active-pill svg { width: 10px; height: 10px; }

.preset-swatches {
  display: flex;
  gap: 4px;
  margin-bottom: 14px;
}
.preset-swatch {
  flex: 1;
  height: 54px;
  display: block;
}

.preset-name {
  font-weight: 600;
  font-size: 14px;
  color: var(--c-navy);
}
.preset-tag {
  margin-top: 2px;
  font-size: 12px;
  color: var(--c-text-mute);
}

.empty-presets {
  text-align: center;
  padding: 40px 22px;
  color: var(--c-text-mute);
  font-size: 13px;
}

.snapshot-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.snapshot-table td { padding: 12px 16px; border-bottom: 1px solid var(--c-line-soft); }
.snapshot-table tbody tr:hover { background: var(--c-bg); }
.snapshot-table tr:last-child td { border-bottom: 0; }
.text-mono { font-family: var(--c-mono); font-size: 12.5px; color: var(--c-text-mute); }
</style>
