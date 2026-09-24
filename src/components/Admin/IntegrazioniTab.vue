<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Integrazioni') }} <em>{{ t('form') }}</em></h1>
      <p>{{ t('Le chiavi dei servizi che ricevono gli invii del Form contatti. Valgono per tutto il sito: nel form accendi il servizio (Contenuto → Integrazioni) e scegli la lista o il modulo.') }}</p>
    </div>
    <div class="head-actions">
      <span class="cfg-pill" :class="pronti > 0 ? 'ok' : 'off'"><span class="dot"></span> {{ riepilogo }}</span>
    </div>
  </div>

  <!-- ─── reCAPTCHA ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
      </div>
      <div>
        <h3>{{ t('reCAPTCHA v3') }}</h3>
        <p>{{ t('Anti-spam di Google, senza caselle da spuntare. Nel form si accende da Anti-spam; servono entrambe le chiavi.') }}</p>
      </div>
      <div class="head-actions">
        <span class="cfg-pill" :class="stato.recaptcha ? 'ok' : 'off'"><span class="dot"></span> {{ stato.recaptcha ? t('Pronto') : t('Chiave mancante') }}</span>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Site key') }}</label>
          <div class="hint">{{ t('Pubblica: viene stampata nella pagina del form.') }}</div>
        </div>
        <div class="control-col">
          <CfgSecret v-model="form.olobuild_recaptcha_site_key" :secret="false" name="olo-int-recaptcha-site" :label="t('Site key')" @update:model-value="setDirty(true)" />
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Secret key') }}</label>
          <div class="hint">{{ t('Resta sul server: verifica ogni invio.') }}</div>
        </div>
        <div class="control-col">
          <CfgSecret v-model="form.olobuild_recaptcha_secret_key" name="olo-int-recaptcha-secret" :label="t('Secret key')" @update:model-value="setDirty(true)" />
        </div>
      </div>
    </div>
  </div>

  <!-- ─── Marketing e CRM ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><path d="m22 6-10 7L2 6"/></svg>
      </div>
      <div>
        <h3>{{ t('Liste e CRM') }}</h3>
        <p>{{ t('Chi compila il form viene aggiunto alla lista del servizio. Senza la chiave il servizio non riceve niente, anche se nel form è acceso.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Mailchimp — API key') }}</label>
          <div class="hint">{{ t('Profilo → Extras → API keys. Finisce con il datacenter, per esempio -us21.') }}</div>
        </div>
        <div class="control-col integ-control">
          <CfgSecret v-model="form.olobuild_mailchimp_api_key" name="olo-int-mailchimp" :label="t('Mailchimp — API key')" placeholder="xxxxxxxxxxxxxxxx-us21" @update:model-value="setDirty(true)" />
          <span class="cfg-pill" :class="pillola('mailchimp').cls"><span class="dot"></span> {{ pillola('mailchimp').testo }}</span>
          <div v-if="dettaglio('mailchimp')" class="integ-err">{{ dettaglio('mailchimp') }}</div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('ActiveCampaign — URL dell\'account') }}</label>
          <div class="hint">{{ t('Impostazioni → Sviluppatore. Senza https:// lo aggiunge Olobuild.') }}</div>
        </div>
        <div class="control-col">
          <CfgSecret v-model="form.olobuild_activecampaign_url" :secret="false" name="olo-int-ac-url" :label="t('ActiveCampaign — URL dell\'account')" placeholder="https://account.api-us1.com" @update:model-value="setDirty(true)" />
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('ActiveCampaign — API key') }}</label>
          <div class="hint">{{ t('Nella stessa pagina dell\'URL. Servono tutti e due.') }}</div>
        </div>
        <div class="control-col integ-control">
          <CfgSecret v-model="form.olobuild_activecampaign_key" name="olo-int-ac-key" :label="t('ActiveCampaign — API key')" @update:model-value="setDirty(true)" />
          <span class="cfg-pill" :class="pillola('activecampaign').cls"><span class="dot"></span> {{ pillola('activecampaign').testo }}</span>
          <div v-if="dettaglio('activecampaign')" class="integ-err">{{ dettaglio('activecampaign') }}</div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('ConvertKit (Kit) — API key v3') }}</label>
          <div class="hint">{{ t('Impostazioni → Sviluppatore: la API key v3, non la API secret.') }}</div>
        </div>
        <div class="control-col integ-control">
          <CfgSecret v-model="form.olobuild_convertkit_key" name="olo-int-convertkit" :label="t('ConvertKit (Kit) — API key v3')" @update:model-value="setDirty(true)" />
          <span class="cfg-pill" :class="pillola('convertkit').cls"><span class="dot"></span> {{ pillola('convertkit').testo }}</span>
          <div v-if="dettaglio('convertkit')" class="integ-err">{{ dettaglio('convertkit') }}</div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Brevo — API key') }}</label>
          <div class="hint">{{ t('SMTP e API → Chiavi API: una chiave v3 (inizia con xkeysib-).') }}</div>
        </div>
        <div class="control-col integ-control">
          <CfgSecret v-model="form.olobuild_brevo_key" name="olo-int-brevo" :label="t('Brevo — API key')" placeholder="xkeysib-…" @update:model-value="setDirty(true)" />
          <span class="cfg-pill" :class="pillola('brevo').cls"><span class="dot"></span> {{ pillola('brevo').testo }}</span>
          <div v-if="dettaglio('brevo')" class="integ-err">{{ dettaglio('brevo') }}</div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('HubSpot e webhook') }}</label>
          <div class="hint">{{ t('Nessuna chiave: si impostano nel form. Per HubSpot Portal ID, Form GUID e quali campi mandare, con il nome della proprietà HubSpot: un campo che il modulo HubSpot non ha fa scartare l\'intero invio.') }}</div>
        </div>
        <div class="control-col integ-control">
          <span v-if="errori.hubspot" class="cfg-pill warn"><span class="dot"></span> {{ t('HubSpot') }}: {{ pillola('hubspot').testo }}</span>
          <span v-if="errori.webhook" class="cfg-pill warn"><span class="dot"></span> {{ t('Webhook') }}: {{ pillola('webhook').testo }}</span>
          <span v-if="!errori.hubspot && !errori.webhook" class="cfg-pill ok"><span class="dot"></span> {{ t('Nessuna chiave richiesta') }}</span>
          <div v-if="dettaglio('hubspot')" class="integ-err">{{ t('HubSpot') }} — {{ dettaglio('hubspot') }}</div>
          <div v-if="dettaglio('webhook')" class="integ-err">{{ t('Webhook') }} — {{ dettaglio('webhook') }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgSecret from './controls/CfgSecret.vue';
import { okOrThrow, cfgJob, assertLoaded, reloadJob, conIniziali } from './cfgSave';

const TAB_ID = 'integrazioni';
const shellDirty = inject('setDirty', () => {});
const setDirty = (v) => shellDirty(v, TAB_ID);
const loaded = ref(false);

// Le option che leggono il render del Form e Olobuild_Form_Handler; stessi nomi di
// rest_get_api_keys()/rest_put_api_keys() (trait-olobuild-builder-settings.php).
// I segreti già salvati tornano mascherati («****abcd»): rimandati così, il server
// li lascia com'erano; un campo svuotato cancella la chiave.
const INIZIALE = {
  olobuild_recaptcha_site_key: '',
  olobuild_recaptcha_secret_key: '',
  olobuild_mailchimp_api_key: '',
  olobuild_activecampaign_url: '',
  olobuild_activecampaign_key: '',
  olobuild_convertkit_key: '',
  olobuild_brevo_key: '',
};
const CHIAVI = Object.keys(INIZIALE);
const form = ref({ ...INIZIALE });

// Stesse condizioni di Olobuild_Form_Handler::integrations_status().
const pieno = (k) => String(form.value[k] || '').trim() !== '';
const stato = computed(() => ({
  recaptcha: pieno('olobuild_recaptcha_site_key') && pieno('olobuild_recaptcha_secret_key'),
  mailchimp: pieno('olobuild_mailchimp_api_key'),
  activecampaign: pieno('olobuild_activecampaign_url') && pieno('olobuild_activecampaign_key'),
  convertkit: pieno('olobuild_convertkit_key'),
  brevo: pieno('olobuild_brevo_key'),
}));
// Ultimo invio rifiutato da ogni servizio (Olobuild_Form_Handler::integrations_errors():
// codice HTTP, data, messaggio del provider; mai chiavi né email). «Pronto» diceva solo
// che il campo non era vuoto: una chiave sbagliata risultava pronta.
const errori = ref({});
const pillola = (servizio) => {
  const e = errori.value[servizio];
  if (servizio in stato.value && !stato.value[servizio]) return { cls: 'off', testo: t('Chiave mancante') };
  if (e) {
    return {
      cls: 'warn',
      testo: e.code ? t('Rifiutato ({c})').replace('{c}', e.code) : t('Non raggiunto'),
    };
  }
  return { cls: 'ok', testo: t('Pronto') };
};
const dettaglio = (servizio) => {
  const e = errori.value[servizio];
  if (!e) return '';
  const quando = e.time ? new Date(e.time * 1000).toLocaleString() : '';
  return t('Ultimo invio non arrivato') + (quando ? ' (' + quando + ')' : '') + (e.message ? ': ' + e.message : '');
};
const pronti = computed(() => Object.keys(stato.value).filter((k) => stato.value[k] && !errori.value[k]).length);
const riepilogo = computed(() => t('{c} di {t} servizi pronti')
  .replace('{c}', pronti.value).replace('{t}', Object.keys(stato.value).length));

async function loadKeys() {
  try {
    const res = await fetch(`${window.oloData.restUrl}settings/api-keys`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      // Solo le chiavi di questa scheda: la stessa rotta rimanda anche quelle degli stock media.
      const nostre = {};
      CHIAVI.forEach((k) => { if (data && typeof data[k] === 'string') nostre[k] = data[k]; });
      form.value = conIniziali(INIZIALE, nostre);
      const err = data && data.form_integrations_errors;
      errori.value = err && typeof err === 'object' && !Array.isArray(err) ? err : {};
      loaded.value = true;
    }
  } catch (e) { /* resta non caricata: assertLoaded blocca il salvataggio */ }
}

async function saveKeys() {
  assertLoaded(loaded);
  const body = {};
  CHIAVI.forEach((k) => { body[k] = String(form.value[k] || '').trim(); });
  await okOrThrow(fetch(`${window.oloData.restUrl}settings/api-keys`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
    body: JSON.stringify(body),
  }));
  // Rilettura: le chiavi appena scritte tornano mascherate e l'URL come l'ha salvato il server.
  // Il salvataggio è già riuscito: se la rilettura non va, restano a video i valori inviati.
  await loadKeys();
}

const onSave = cfgJob(TAB_ID, saveKeys);
const onDiscard = cfgJob(TAB_ID, reloadJob(loaded, loadKeys));

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
.integ-control { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
.integ-control .cfg-secret { flex: 1 1 220px; min-width: 0; }
.integ-control .cfg-pill { flex-shrink: 0; }
.integ-err { flex: 1 0 100%; font-size: 12px; line-height: 1.4; color: var(--c-warning); overflow-wrap: anywhere; }
</style>
