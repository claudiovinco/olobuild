<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Stock') }} <em>{{ t('media') }}</em></h1>
      <p>{{ t('Connetti i provider di immagini gratuite per cercarli e inserirli direttamente dall\'editor — senza scaricare/ricaricare a mano.') }}</p>
    </div>
    <div class="head-actions">
      <a class="cfg-btn cfg-btn-secondary" href="https://olotheme.com/docs/stock-media-keys/" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4h6a4 4 0 0 1 4 4v12a3 3 0 0 0-3-3H2zM22 4h-6a4 4 0 0 0-4 4v12a3 3 0 0 1 3-3h7z"/></svg>
        {{ t('Come ottenere le chiavi') }}
      </a>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>
      </div>
      <div>
        <h3>{{ t('Provider connessi') }}</h3>
        <p>{{ providerSummary }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="stock-list">
        <div v-for="s in services" :key="s.id" class="stock-row">
          <div class="stock-logo">{{ s.name.charAt(0) }}</div>
          <div class="stock-info">
            <div class="stock-name">{{ s.name }}</div>
            <div class="stock-desc">{{ t(s.desc) }}</div>
          </div>
          <div v-if="s.key" class="cfg-input mono stock-key">
            <input
              :type="s.reveal ? 'text' : 'password'"
              :value="s.key"
              @input="onKeyInput(s.id, $event.target.value)"
              autocomplete="off"
              spellcheck="false"
              :name="'olo-stock-' + s.id"
              data-1p-ignore
              data-lpignore="true"
            />
            <button class="reveal" type="button" :title="s.reveal ? t('Nascondi') : t('Mostra')" @click="s.reveal = !s.reveal">
              <svg v-if="!s.reveal" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l18 18M10.6 6.1A10.5 10.5 0 0 1 12 6c6.5 0 10 6 10 6a17.3 17.3 0 0 1-4 4.5M6.6 6.6A17.3 17.3 0 0 0 2 12s3.5 6 10 6c1.3 0 2.5-.3 3.6-.7"/></svg>
            </button>
          </div>
          <button v-else class="cfg-btn cfg-btn-secondary stock-add-key" @click="focusKey(s.id)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="14" r="4"/><path d="m11 12 9-9 3 3-3 3-2-2-2 2-2-2-3 3"/></svg>
            {{ t('Aggiungi chiave') }}
          </button>
          <span class="cfg-pill" :class="statusClass(s)">
            <span class="dot"></span>
            {{ t(statusLabel(s)) }}
          </span>
          <button class="cfg-btn-icon cfg-btn-ghost" :title="t('Dettagli')" @click="openDocs(s)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L2 19l3 3 7.3-7.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4 2.6-2.6z"/></svg>
      </div>
      <div>
        <h3>{{ t('Comportamento default') }}</h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Provider preferito') }}</label>
          <div class="hint">{{ t('Quello che si apre per primo dalla ricerca media nell\'editor.') }}</div>
        </div>
        <div class="control-col">
          <CfgSelect size="md" :model-value="behavior.preferred" :options="providerOptions" @update:model-value="setBehavior('preferred', $event)" />
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Scarica in locale') }}</label>
          <div class="hint">{{ t('Quando inserisci un\'immagine, viene scaricata nella Libreria media di WordPress. Disattivato = hotlink al provider.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': behavior.download_local }" @click="setBehavior('download_local', !behavior.download_local)" role="switch"></button>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Ottimizza al download') }}</label>
          <div class="hint">{{ t('Comprime e converte in WebP automaticamente. Richiede modulo Performance attivo.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': behavior.optimize_on_download }" @click="setBehavior('optimize_on_download', !behavior.optimize_on_download)" role="switch"></button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgSelect from './controls/CfgSelect.vue';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const services = ref([
  { id: 'unsplash',  name: 'Unsplash',  desc: '3M+ foto royalty-free, alta qualità editoriale', key: '', reveal: false, optionKey: 'olo_unsplash_api_key', docUrl: 'https://unsplash.com/developers' },
  { id: 'pexels',    name: 'Pexels',    desc: '1M+ foto e video, license CC0',                  key: '', reveal: false, optionKey: 'olo_pexels_api_key',   docUrl: 'https://www.pexels.com/api/' },
  { id: 'pixabay',   name: 'Pixabay',   desc: '4M+ media, anche illustrazioni e vector',        key: '', reveal: false, optionKey: 'olo_pixabay_api_key',  docUrl: 'https://pixabay.com/api/docs/' },
  { id: 'freesound', name: 'Freesound', desc: 'Audio creative-commons, effetti, loop',          key: '', reveal: false, optionKey: 'olo_freesound_api_key',docUrl: 'https://freesound.org/help/developers/' },
]);

const behavior = ref({
  preferred: 'unsplash',
  download_local: true,
  optimize_on_download: false,
});

// Option per CfgSelect: nomi propri dei provider, niente t()
const providerOptions = computed(() => services.value.map(s => ({ value: s.id, label: s.name })));

const providerSummary = computed(() => {
  const connected = services.value.filter(s => !!s.key).length;
  const total = services.value.length;
  return t('{c} di {t} provider connessi. Click per inserire/aggiornare la chiave.')
    .replace('{c}', connected).replace('{t}', total);
});

function statusClass(s) {
  if (!s.key) return 'off';
  return 'ok';
}
function statusLabel(s) {
  if (!s.key) return 'Non connesso';
  return 'Connesso';
}

function onKeyInput(id, val) {
  const s = services.value.find(x => x.id === id);
  if (!s) return;
  s.key = val;
  setDirty(true);
}

function focusKey(id) {
  const s = services.value.find(x => x.id === id);
  if (s) s.key = ' ';
  setDirty(true);
}

function openDocs(s) {
  if (s.docUrl) window.open(s.docUrl, '_blank', 'noopener');
}

function setBehavior(k, v) { behavior.value[k] = v; setDirty(true); }

async function loadKeys() {
  try {
    const res = await fetch(`${window.oloData.restUrl}api-keys`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      services.value.forEach(s => {
        if (data[s.optionKey]) s.key = data[s.optionKey];
      });
    }
  } catch (e) { /* defaults */ }
  try {
    const res2 = await fetch(`${window.oloData.restUrl}stockmedia-behavior`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res2.ok) Object.assign(behavior.value, await res2.json());
  } catch (e) { /* defaults */ }
}

async function saveKeys() {
  const body = {};
  services.value.forEach(s => { body[s.optionKey] = (s.key || '').trim(); });
  try {
    const r1 = await fetch(`${window.oloData.restUrl}api-keys`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(body),
    });
    const r2 = await fetch(`${window.oloData.restUrl}stockmedia-behavior`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(behavior.value),
    });
    if (!r1.ok || !r2.ok) throw new Error();
  } catch (e) {
    showToast(t('Errore di salvataggio'), 'error');
  }
}

const onSave = () => saveKeys();
const onDiscard = () => loadKeys();

onMounted(() => {
  loadKeys();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.stock-list { display: grid; gap: 10px; }
.stock-row {
  display: grid;
  grid-template-columns: 48px 1fr 280px 110px 36px;
  gap: 14px;
  align-items: center;
  padding: 14px 16px;
  background: #fff;
  border: 1px solid var(--c-line-soft);
  border-radius: 10px;
}
.stock-logo {
  width: 48px; height: 48px;
  border-radius: 10px;
  background: var(--c-bg);
  display: grid; place-items: center;
  color: var(--c-navy);
  font-weight: 700;
  font-family: var(--c-display);
  font-size: 22px;
}
.stock-info {}
.stock-name { font-weight: 600; font-size: 14px; color: var(--c-navy); }
.stock-desc { font-size: 12px; color: var(--c-text-mute); margin-top: 2px; }
.stock-key { padding: 6px 10px; font-size: 11.5px; }
.stock-add-key { padding: 6px 10px; font-size: 12px; }
@media (max-width: 1100px) {
  .stock-row { grid-template-columns: 48px 1fr 110px 36px; }
  .stock-key, .stock-add-key { grid-column: 1 / -1; }
}
</style>
