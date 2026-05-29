<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Redirect & 404') }}</h1>
      <p>{{ t('Reindirizzamenti 301/302/307/410 e log dei 404. Aggiungi una redirect manuale o promuovi un 404 ricorrente a redirect.') }}</p>
    </div>
    <div class="head-actions">
      <button class="cfg-btn cfg-btn-primary" @click="addingRow = true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        {{ t('Aggiungi redirect') }}
      </button>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-body tight" style="padding: 14px 22px;">
      <div class="cfg-segment">
        <button :class="{ 'is-on': section === 'redirects' }" @click="section = 'redirects'">{{ t('Redirect attive') }} ({{ redirects.length }})</button>
        <button :class="{ 'is-on': section === 'log404' }"    @click="section = 'log404'">{{ t('Log 404') }} ({{ log404.length }})</button>
        <button :class="{ 'is-on': section === 'indexnow' }"  @click="section = 'indexnow'">IndexNow</button>
      </div>
    </div>
  </div>

  <!-- ─── Redirects table ─── -->
  <div v-show="section === 'redirects'" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h13M13 6l6 6-6 6"/></svg></div>
      <div>
        <h3>{{ t('Redirect configurate') }}</h3>
        <p>{{ t('Prefisso "~" su "Da URL" attiva il match regex.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body" style="padding: 0;">
      <table class="cfg-redirect-table">
        <thead>
          <tr>
            <th>{{ t('Da URL') }}</th>
            <th>{{ t('A URL') }}</th>
            <th>{{ t('Tipo') }}</th>
            <th>{{ t('Hit') }}</th>
            <th style="width:36px"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="addingRow" class="is-new">
            <td><input class="inline-input" v-model="newRow.from_url" :placeholder="t('/vecchia-url o ~/regex/.*')" /></td>
            <td><input class="inline-input" v-model="newRow.to_url" placeholder="/nuova-url" /></td>
            <td>
              <select v-model.number="newRow.type" class="inline-input">
                <option value="301">301</option>
                <option value="302">302</option>
                <option value="307">307</option>
                <option value="410">410</option>
              </select>
            </td>
            <td>—</td>
            <td>
              <button class="cfg-btn-icon" :title="t('Salva')" @click="saveNewRow">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12l5 5L20 7"/></svg>
              </button>
            </td>
          </tr>
          <tr v-for="r in redirects" :key="r.id">
            <td class="text-mono">{{ r.from_url }}</td>
            <td class="text-mono">{{ r.to_url }}</td>
            <td><span class="cfg-pill" :class="r.type === '301' || r.type === 301 ? 'ok' : 'warn'">{{ r.type }}</span></td>
            <td>{{ r.hits || 0 }}</td>
            <td>
              <button class="cfg-btn-icon cfg-btn-danger" :title="t('Elimina')" @click="deleteRedirect(r.id)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
              </button>
            </td>
          </tr>
          <tr v-if="!redirects.length && !addingRow">
            <td colspan="5" class="empty-row">{{ t('Nessuna redirect configurata. Clicca "Aggiungi redirect" per iniziare.') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ─── 404 Log ─── -->
  <div v-show="section === 'log404'" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 7v5l3 3"/></svg></div>
      <div>
        <h3>{{ t('Pagine non trovate') }}</h3>
        <p>{{ t('URL richieste che hanno restituito 404. Promuovi a redirect quelle ricorrenti.') }}</p>
      </div>
      <div class="head-actions">
        <button v-if="log404.length" class="cfg-btn cfg-btn-ghost" @click="clearLog404">{{ t('Svuota log') }}</button>
      </div>
    </div>
    <div class="cfg-card-body" style="padding: 0;">
      <table class="cfg-redirect-table">
        <thead>
          <tr>
            <th>{{ t('URL') }}</th>
            <th>{{ t('Hit') }}</th>
            <th>{{ t('Ultimo accesso') }}</th>
            <th style="width:160px">{{ t('Azione') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in log404" :key="row.id">
            <td class="text-mono">{{ row.url }}</td>
            <td>{{ row.hits || 0 }}</td>
            <td>{{ row.last_hit }}</td>
            <td>
              <button class="cfg-btn cfg-btn-secondary" style="padding: 4px 10px; font-size: 12px;" @click="promote404(row)">{{ t('Crea redirect →') }}</button>
            </td>
          </tr>
          <tr v-if="!log404.length"><td colspan="4" class="empty-row">{{ t('Nessun 404 registrato.') }}</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ─── IndexNow ─── -->
  <div v-show="section === 'indexnow'" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-3-6.7l3 2.7"/><path d="M21 3v6h-6"/></svg></div>
      <div>
        <h3>IndexNow</h3>
        <p>{{ t('Notifica Bing/Yandex quando pubblichi/aggiorni un post. Genera la chiave da indexnow.org.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('API Key IndexNow') }}</label></div>
        <div class="control-col">
          <div class="cfg-input mono">
            <input type="text" :value="indexNowKey" @input="onIndexNowChange($event.target.value)" placeholder="abcd1234efgh5678..." />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const section = ref('redirects');
const redirects = ref([]);
const log404 = ref([]);
const indexNowKey = ref('');
const addingRow = ref(false);
const newRow = ref({ from_url: '', to_url: '', type: 301 });

async function loadRedirects() {
  try {
    const res = await fetch(`${window.oloData.restUrl}redirects`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      redirects.value = data?.redirects || [];
      log404.value = data?.log404 || [];
      indexNowKey.value = data?.indexnow_key || '';
    }
  } catch (e) { /* keep */ }
}

async function saveNewRow() {
  if (!newRow.value.from_url || !newRow.value.to_url) {
    showToast(t('Da/A URL obbligatori'), 'error');
    return;
  }
  try {
    const res = await fetch(`${window.oloData.restUrl}redirects`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ action: 'add', ...newRow.value }),
    });
    if (res.ok) {
      addingRow.value = false;
      newRow.value = { from_url: '', to_url: '', type: 301 };
      await loadRedirects();
    } else {
      showToast(t('Errore: redirect non salvata'), 'error');
    }
  } catch (e) {
    showToast(t('Errore di rete'), 'error');
  }
}

async function deleteRedirect(id) {
  if (!confirm(t('Eliminare questa redirect?'))) return;
  try {
    const res = await fetch(`${window.oloData.restUrl}redirects`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ action: 'delete', id }),
    });
    if (res.ok) await loadRedirects();
  } catch (e) {
    showToast(t('Errore di eliminazione'), 'error');
  }
}

async function clearLog404() {
  if (!confirm(t('Svuotare tutto il log dei 404?'))) return;
  try {
    await fetch(`${window.oloData.restUrl}redirects`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ action: 'clear_404' }),
    });
    await loadRedirects();
  } catch (e) { /* noop */ }
}

function promote404(row) {
  newRow.value = { from_url: row.url, to_url: '/', type: 301 };
  addingRow.value = true;
  section.value = 'redirects';
}

function onIndexNowChange(v) {
  indexNowKey.value = v;
  setDirty(true);
}

async function saveSettings() {
  // Solo l'IndexNow key è "settings-style"; le redirect sono CRUD inline.
  try {
    await fetch(`${window.oloData.restUrl}redirects`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ action: 'save_indexnow', indexnow_key: indexNowKey.value }),
    });
  } catch (e) {
    showToast(t('Errore di salvataggio'), 'error');
  }
}

const onSave = () => saveSettings();
const onDiscard = () => loadRedirects();

onMounted(() => {
  loadRedirects();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.cfg-redirect-table {
  width: 100%; border-collapse: collapse; font-size: 13px;
}
.cfg-redirect-table th, .cfg-redirect-table td {
  padding: 10px 16px; text-align: left;
  border-bottom: 1px solid var(--c-line-soft);
}
.cfg-redirect-table th {
  background: var(--c-bg); font-weight: 600; font-size: 11px;
  color: var(--c-text-mute); text-transform: uppercase; letter-spacing: .04em;
}
.cfg-redirect-table tr.is-new { background: var(--c-warning-soft); }
.cfg-redirect-table tbody tr:hover:not(.is-new) { background: var(--c-bg); }
.cfg-redirect-table .empty-row { text-align: center; color: var(--c-text-faint); padding: 30px; font-style: italic; }
.inline-input {
  width: 100%; padding: 6px 10px;
  background: #fff; border: 1px solid var(--c-line);
  border-radius: 6px; font: 12px var(--c-mono);
  color: var(--c-text); outline: none;
}
.inline-input:focus { border-color: var(--c-red); box-shadow: 0 0 0 2px var(--c-red-soft); }
.text-mono { font-family: var(--c-mono); font-size: 12.5px; }
</style>
