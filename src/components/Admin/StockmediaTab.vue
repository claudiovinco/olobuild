<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Stock') }} <em>{{ t('media') }}</em></h1>
      <p>{{ t('Connetti i provider di immagini gratuite per cercarli e inserirli direttamente dall\'editor — senza scaricare/ricaricare a mano.') }}</p>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>
      </div>
      <div>
        <h3>{{ t('Provider connessi') }} <InfoTip :titolo="t('Come ottenere le chiavi')" :testo="infoChiavi" /></h3>
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
          <CfgSecret
            v-if="s.key"
            class="stock-key"
            :model-value="s.key"
            :name="'olo-stock-' + s.id"
            :label="t('{nome} — API key').replace('{nome}', s.name)"
            @update:model-value="onKeyInput(s.id, $event)"
          />
          <button v-else class="cfg-btn cfg-btn-secondary stock-add-key" @click="focusKey(s.id)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="14" r="4"/><path d="m11 12 9-9 3 3-3 3-2-2-2 2-2-2-3 3"/></svg>
            {{ t('Aggiungi chiave') }}
          </button>
          <span class="cfg-pill" :class="statusClass(s)">
            <span class="dot"></span>
            {{ t(statusLabel(s)) }}
          </span>
          <a
            class="cfg-btn-icon cfg-btn-ghost"
            :href="s.docUrl"
            target="_blank"
            rel="noopener noreferrer"
            :title="t('Ottieni la chiave su {nome}').replace('{nome}', s.name)"
            :aria-label="t('Ottieni la chiave su {nome}').replace('{nome}', s.name)"
          >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h7v7M10 14 21 3M21 14v7H3V3h7"/></svg>
          </a>
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
import InfoTip from '@/components/Builder/InfoTip.vue';
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgSelect from './controls/CfgSelect.vue';
import CfgSecret from './controls/CfgSecret.vue';
import { okOrThrow, cfgJob, assertLoaded, reloadJob, salvaERileggi } from './cfgSave';
import { useCfgLettura } from './composables/useCfgLettura';

const TAB_ID = 'stockmedia';
const shellDirty = inject('setDirty', () => {});
const setDirty = (v) => shellDirty(v, TAB_ID);
const loaded = ref(false);
// Prima lettura: finché non riesce la shell mostra scheletro o errore al posto della scheda.
const lettura = useCfgLettura(TAB_ID, loaded, () => loadKeys());

// Le option che leggono class-unsplash/pexels/pixabay/freesound, stessi nomi della rotta
// settings/api-keys (come «Integrazioni form»): le chiavi salvate tornano mascherate
// («****abcd») e, rimandate così, il server le lascia com'erano; un campo svuotato
// cancella la chiave. `saved` = la chiave c'è sul server: è quello che dice «Connesso».
const services = ref([
  { id: 'unsplash',  name: 'Unsplash',  desc: '3M+ foto royalty-free, alta qualità editoriale', key: '', saved: false, optionKey: 'olobuild_unsplash_api_key',  docUrl: 'https://unsplash.com/developers' },
  { id: 'pexels',    name: 'Pexels',    desc: '1M+ foto e video, license CC0',                  key: '', saved: false, optionKey: 'olobuild_pexels_api_key',    docUrl: 'https://www.pexels.com/api/' },
  { id: 'pixabay',   name: 'Pixabay',   desc: '4M+ media, anche illustrazioni e vector',        key: '', saved: false, optionKey: 'olobuild_pixabay_api_key',   docUrl: 'https://pixabay.com/api/docs/' },
  { id: 'freesound', name: 'Freesound', desc: 'Audio creative-commons, effetti, loop',          key: '', saved: false, optionKey: 'olobuild_freesound_api_key', docUrl: 'https://freesound.org/apiv2/apply/' },
]);

// La guida alle chiavi stava su una pagina di olotheme.com che non esiste (404): ogni provider
// spiega la sua, e l'icona della riga apre quella pagina.
const infoChiavi = [
  t('Ogni provider dà una chiave gratuita: apri il suo sito con l'icona in fondo alla riga, registrati e crea un'applicazione.'),
  t('Unsplash: la Access Key dell'applicazione. Pexels e Pixabay: la chiave compare nella pagina API dopo l'accesso. Freesound: la chiave API della richiesta.'),
  t('Incollala nel campo e salva: lo stato passa a «Connesso».'),
];

const behavior = ref({
  preferred: 'unsplash',
  download_local: true,
  optimize_on_download: false,
});

// Option per CfgSelect: nomi propri dei provider, niente t()
const providerOptions = computed(() => services.value.map(s => ({ value: s.id, label: s.name })));

const providerSummary = computed(() => {
  const connected = services.value.filter(s => s.saved).length;
  const total = services.value.length;
  return t('{c} di {t} provider connessi. Click per inserire/aggiornare la chiave.')
    .replace('{c}', connected).replace('{t}', total);
});

function statusClass(s) {
  if (!s.saved) return 'off';
  return 'ok';
}
function statusLabel(s) {
  if (!s.saved) return 'Non connesso';
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

function setBehavior(k, v) { behavior.value[k] = v; setDirty(true); }

async function loadKeys(invariato) {
  // Le chiavi vuote a video si salverebbero sopra quelle vere: servono entrambe le letture.
  let chiavi = null;
  let comportamento = null;
  try {
    const res = await lettura.fetch(`${window.oloData.restUrl}settings/api-keys`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) chiavi = (await res.json()) || {};
  } catch (e) { /* defaults */ }
  try {
    const res2 = await lettura.fetch(`${window.oloData.restUrl}stockmedia-behavior`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res2.ok) comportamento = (await res2.json()) || {};
  } catch (e) { /* defaults */ }
  // Rilettura dopo un salvataggio: una modifica fatta nel frattempo resta a video.
  // Per questo si scrive a video solo dopo entrambe le letture.
  if (invariato && !invariato()) return;
  if (chiavi) {
    services.value.forEach(s => {
      // Vuota sul server = vuota a video, anche dopo «Annulla».
      s.key = typeof chiavi[s.optionKey] === 'string' ? chiavi[s.optionKey] : '';
      s.saved = s.key.trim() !== '';
    });
  }
  if (comportamento) Object.assign(behavior.value, comportamento);
  if (chiavi && comportamento) loaded.value = true;
}

// Ciò che la scheda manda: chiavi e comportamento.
const fotoStato = () => JSON.stringify([services.value.map(s => s.key), behavior.value]);

async function saveKeys() {
  assertLoaded(loaded);
  // Rilettura: le chiavi appena scritte tornano mascherate e i badge dicono cosa c'è sul server.
  // Il salvataggio è già riuscito: se la rilettura non va, restano a video i valori inviati.
  // Una modifica fatta mentre le richieste sono in volo non si rilegge: resta da salvare.
  await salvaERileggi(fotoStato, async () => {
    const body = {};
    services.value.forEach(s => { body[s.optionKey] = (s.key || '').trim(); });
    const comportamento = JSON.stringify(behavior.value);
    await okOrThrow(fetch(`${window.oloData.restUrl}settings/api-keys`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(body),
    }));
    await okOrThrow(fetch(`${window.oloData.restUrl}stockmedia-behavior`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: comportamento,
    }));
  }, loadKeys);
}

const onSave = cfgJob(TAB_ID, saveKeys);
const onDiscard = cfgJob(TAB_ID, reloadJob(loaded, loadKeys));

onMounted(() => {
  lettura.leggi();
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
