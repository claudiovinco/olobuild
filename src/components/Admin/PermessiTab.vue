<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Permessi') }} <em>{{ t('& Ruoli') }}</em></h1>
      <p>{{ t('Chi può usare il builder, letto dai ruoli di WordPress. La scheda è in sola lettura: non ha niente da salvare.') }}</p>
    </div>
  </div>

  <!-- Una restrizione per ruolo salvata (anche per errore dalla vecchia scheda,
       che la riduceva ai soli amministratori): si dice e si può togliere. -->
  <div v-if="state && state.configured" class="perm-banner" role="status">
    <div class="perm-banner-text">
      <b>{{ t('Restrizione per ruolo salvata') }}</b>
      <span>{{ t('Le API del builder rispondono solo a') }}: {{ allowedNames }}. {{ t('Gli altri ruoli con «Modifica pagine» ricevono un errore 403.') }}</span>
    </div>
    <button type="button" class="cfg-btn cfg-btn-secondary perm-reset" :disabled="resetting" @click="resetAccess">
      {{ resetting ? t('Ripristino…') : t('Ripristina comportamento predefinito') }}
    </button>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L2 19l3 3 7.3-7.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4 2.6-2.6z"/></svg>
      </div>
      <div>
        <h3>{{ t('Ruoli del sito') }}</h3>
        <p>{{ t('L\'editor visuale si apre solo con il permesso «Gestire le opzioni» (amministratori). I livelli per ruolo (solo contenuti, solo design) non sono ancora applicati nel builder.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body perm-body">
      <p v-if="loading" class="perm-msg">{{ t('Caricamento…') }}</p>
      <p v-else-if="error" class="perm-msg perm-error" role="alert">{{ error }}</p>
      <table v-else-if="state" class="perm-table">
        <thead>
          <tr>
            <th scope="col">{{ t('Ruolo') }}</th>
            <th scope="col">{{ t('Utenti') }}</th>
            <th scope="col">{{ t('Può aprire il builder') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in state.roles" :key="r.slug">
            <th scope="row" class="perm-name">{{ r.name }}</th>
            <td class="perm-num">{{ r.users }}</td>
            <td>
              <span class="cfg-pill" :class="r.manage_options ? 'ok' : 'off'"><span class="dot"></span>{{ r.manage_options ? t('Sì') : t('No') }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onActivated } from 'vue';
import { t } from '@/i18n';
import { okOrThrow } from './cfgSave';

// Sola lettura sui dati veri (GET /role-manager): niente valori scritti nel
// codice, niente puntino «da salvare», niente partecipazione a «Salva
// impostazioni». La vecchia matrice inventata mandava { roles, matrix,
// advanced } e il server riduceva l'accesso ai soli amministratori.
const showToast = inject('showToast', () => {});

const state = ref(null);      // { configured, allowed: [slug], roles: [{ slug, name, users, edit_pages, manage_options }] }
const loading = ref(true);
const error = ref('');
const resetting = ref(false);

const allowedNames = computed(() => {
  if (!state.value) return '';
  const names = {};
  (state.value.roles || []).forEach((r) => { names[r.slug] = r.name; });
  return (state.value.allowed || []).map((slug) => names[slug] || slug).join(', ');
});

async function load() {
  loading.value = !state.value;
  try {
    const res = await okOrThrow(fetch(`${window.oloData.restUrl}role-manager`, { headers: { 'X-WP-Nonce': window.oloData.nonce } }));
    state.value = await res.json();
    error.value = '';
  } catch (e) {
    // Mai dati di riserva: se la lettura fallisce lo si dice.
    state.value = null;
    error.value = `${t('Impossibile leggere i permessi')} (${(e && e.message) || ''})`;
  } finally {
    loading.value = false;
  }
}

async function resetAccess() {
  if (resetting.value) return;
  if (!confirm(t('Togliere la restrizione per ruolo salvata? L\'accesso alle API del builder tornerà a dipendere solo dai permessi di WordPress.'))) return;
  resetting.value = true;
  try {
    const res = await okOrThrow(fetch(`${window.oloData.restUrl}role-manager`, {
      method: 'DELETE',
      headers: { 'X-WP-Nonce': window.oloData.nonce },
    }));
    state.value = await res.json();
    error.value = '';
    showToast(t('Comportamento predefinito ripristinato'), 'success');
  } catch (e) {
    showToast(`${t('Ripristino non riuscito')} (${(e && e.message) || ''})`, 'error', 8000);
  } finally {
    resetting.value = false;
  }
}

onMounted(load);
// Sotto KeepAlive la scheda resta montata: tornandoci si rilegge lo stato vero
// (la prima attivazione coincide col montaggio, già coperto da onMounted).
let primaAttivazione = true;
onActivated(() => {
  if (primaAttivazione) { primaAttivazione = false; return; }
  load();
});
</script>

<style scoped>
.perm-body { padding: 0; overflow: auto; }
.perm-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}
.perm-table thead tr { background: var(--c-bg); }
.perm-table thead th {
  padding: 12px 22px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: var(--c-text-faint);
  text-align: left;
}
.perm-table tbody tr { border-top: 1px solid var(--c-line-soft); }
.perm-table tbody th,
.perm-table tbody td { padding: 10px 22px; text-align: left; }
.perm-name { font-weight: 500; color: var(--c-navy); }
.perm-num { font-variant-numeric: tabular-nums; color: var(--c-text-mute); }
.perm-msg { margin: 0; padding: 18px 22px; font-size: 13px; color: var(--c-text-mute); }
.perm-error { color: var(--c-red-dark); }
.perm-banner {
  display: flex; flex-wrap: wrap; align-items: center; gap: 12px 16px;
  padding: 14px 18px;
  margin-bottom: 16px;
  background: var(--c-warning-soft);
  border: 1px solid var(--c-warning-soft);
  border-left: 3px solid var(--c-warning);
  border-radius: 10px;
  font-size: 13px;
  color: var(--c-text);
}
.perm-banner-text { display: grid; gap: 2px; flex: 1 1 280px; }
.perm-banner-text b { color: var(--c-navy); }
.perm-reset { flex-shrink: 0; }
.perm-reset:focus-visible { outline: 2px solid var(--c-red); outline-offset: 2px; }
</style>
