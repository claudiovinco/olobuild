<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Cookie Consent') }} <em>{{ t('& GDPR') }}</em></h1>
      <p>{{ t('Banner di consenso, categorie di cookie, e gestione delle preferenze utente in conformità al GDPR.') }}</p>
    </div>
    <div class="head-actions">
      <button class="cfg-btn cfg-btn-secondary" @click="previewBanner">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
        {{ t('Anteprima banner') }}
      </button>
    </div>
  </div>

  <!-- Senza la lettura i controlli restano bloccati: lo si dice, con lo stato della risposta. -->
  <div v-if="!loaded && loadError" class="cookie-load-error" role="alert">
    {{ t('Lettura delle impostazioni cookie non riuscita') }} ({{ loadError }}). {{ t('Ricarica la pagina: finché i dati non arrivano la scheda non si può modificare.') }}
  </div>

  <!-- Stato e modalità -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="14" r="4"/><path d="m11 12 9-9 3 3-3 3-2-2-2 2-2-2-3 3"/></svg>
      </div>
      <div>
        <h3>{{ t('Stato e modalità') }}</h3>
        <p>{{ t('Il banner viene mostrato al primo accesso e ai visitatori che non hanno ancora scelto.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Cookie banner') }}</label>
          <div class="hint">{{ t('Disattiva solo se il sito non usa cookie non-essenziali.') }}</div>
        </div>
        <div class="control-col">
          <button type="button" ref="switchEl" class="cfg-switch cookie-enabled" :class="{ 'is-on': form.enabled }" :disabled="!loaded" @click="onToggleEnabled" role="switch" :aria-checked="form.enabled ? 'true' : 'false'" :aria-label="t('Cookie banner')"></button>
        </div>
      </div>
      <!-- Accendere il banner cambia il sito pubblico: prima lo si dice (dai valori del runtime). -->
      <div v-if="confirmOn" class="cookie-confirm" role="alertdialog" aria-labelledby="cookie-confirm-title" aria-describedby="cookie-confirm-list" @keydown.esc.stop="annullaAccensione">
        <b id="cookie-confirm-title">{{ t('Accendere il banner sul sito pubblico?') }}</b>
        <ul id="cookie-confirm-list">
          <li>{{ t('Il banner compare a ogni visitatore che non ha ancora scelto.') }}</li>
          <li v-if="runtime.auto_block">{{ t('Gli script noti di analytics e marketing (Google Analytics, Meta Pixel, Hotjar…) non partono finché il visitatore non accetta.') }}</li>
          <li v-if="runtime.block_iframes">{{ t('Video YouTube e Vimeo, mappe Google e contenuti incorporati di Facebook e Instagram restano bloccati finché il visitatore non accetta.') }}</li>
          <li>{{ t('Testi in uso:') }} {{ runtime.banner_title ? '«' + runtime.banner_title + '» ' : '' }}{{ runtime.banner_message }}</li>
          <li>{{ t('Vale dopo «Salva impostazioni».') }}</li>
        </ul>
        <div class="cookie-confirm-actions">
          <button type="button" ref="confirmBtn" class="cfg-btn cfg-btn-primary" @click="confermaAccensione">{{ t('Accendi il banner') }}</button>
          <button type="button" class="cfg-btn cfg-btn-secondary" @click="annullaAccensione">{{ t('Annulla') }}</button>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Modalità') }}</label>
          <div class="hint">{{ t('Opt-in è richiesto in UE. Opt-out solo dove ammesso.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': form.mode === 'optin' }"     :disabled="!loaded" @click="set('mode', 'optin')">{{ t('Opt-in (GDPR)') }}</button>
            <button :class="{ 'is-on': form.mode === 'optout' }"    :disabled="!loaded" @click="set('mode', 'optout')">{{ t('Opt-out') }}</button>
            <button :class="{ 'is-on': form.mode === 'notify' }"    :disabled="!loaded" @click="set('mode', 'notify')">{{ t('Solo notifica') }}</button>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Blocca script fino al consenso') }}</label>
          <div class="hint">{{ t('Google Analytics, Meta Pixel, ecc. non partono finché l\'utente non accetta.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': form.block_scripts }" :disabled="!loaded" @click="set('block_scripts', !form.block_scripts)" role="switch"></button>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Re-richiedi consenso dopo') }}</label>
          <div class="hint">{{ t('Mesi dopo i quali il banner ricompare.') }}</div>
        </div>
        <div class="control-col">
          <CfgNumber :model-value="form.reshow_months" :min="1" :max="36" :suffix="t('mesi')" :disabled="!loaded" @update:model-value="set('reshow_months', $event)" />
        </div>
      </div>
    </div>
  </div>

  <!-- Categorie -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 9 5-9 5-9-5 9-5z"/><path d="m3 17 9 5 9-5"/><path d="m3 12 9 5 9-5"/></svg>
      </div>
      <div>
        <h3>{{ t('Categorie di cookie') }}</h3>
        <p>{{ t('Le categorie mostrate nel pannello di preferenze.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body" style="padding: 0;">
      <div v-for="(cat, i) in categories" :key="cat.id" class="category-row" :class="{ 'has-border': i < categories.length - 1 }">
        <div class="cat-dot" :style="{ background: catDot(cat) }"></div>
        <div class="cat-info">
          <div class="cat-name">
            {{ t(cat.label) }}
            <span v-if="cat.required" class="cfg-pill off cat-required"><span class="dot"></span> {{ t('OBBLIGATORIO') }}</span>
          </div>
          <div class="cat-desc">{{ t(cat.desc) }}</div>
        </div>
        <div class="cat-count">{{ cat.count }} {{ t('cookie') }}</div>
        <button class="cfg-switch" :class="{ 'is-on': cat.required || cat.active }" :disabled="cat.required || !loaded" @click="!cat.required && toggleCategory(i)" role="switch"></button>
      </div>
    </div>
  </div>

  <!-- Copy banner -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V5h16v2"/><path d="M9 20h6"/><path d="M12 5v15"/></svg>
      </div>
      <div>
        <h3>{{ t('Testi del banner') }}</h3>
        <p>{{ t('Testi mostrati al visitatore, in ogni lingua del sito.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div v-if="lingue.length > 1" class="cfg-row">
        <div class="label-col">
          <label>{{ t('Lingua') }}</label>
          <div class="hint">{{ t('Nelle altre lingue, un testo vuoto resta quello della lingua principale.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button v-for="l in lingue" :key="l.codice" :class="{ 'is-on': activeLang === l.codice }" @click="activeLang = l.codice">{{ l.nome }}</button>
          </div>
        </div>
      </div>
      <div v-for="campo in CAMPI_TESTO" :key="campo.k" class="cfg-row">
        <div class="label-col"><label :for="'cc-' + campo.k">{{ t(campo.label) }}</label></div>
        <div class="control-col">
          <div v-if="campo.lungo" class="cfg-textarea"><textarea :id="'cc-' + campo.k" rows="3" :value="testo(campo.k)" :placeholder="segnaposto(campo.k)" :disabled="!loaded" @input="setTesto(campo.k, $event.target.value)"></textarea></div>
          <div v-else class="cfg-input"><input :id="'cc-' + campo.k" type="text" :value="testo(campo.k)" :placeholder="segnaposto(campo.k)" :disabled="!loaded" @input="setTesto(campo.k, $event.target.value)" /></div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col"><label>{{ t('Posizione') }}</label></div>
        <div class="control-col">
          <div class="cfg-segment">
            <!-- Solo le posizioni che il banner conosce (top | bottom): le altre il salvataggio
                 le riporta a «In basso», e la scheda continuava a mostrarle scelte. -->
            <button :class="{ 'is-on': form.position === 'bottom' }" :disabled="!loaded" @click="set('position', 'bottom')">{{ t('In basso') }}</button>
            <button :class="{ 'is-on': form.position === 'top' }"    :disabled="!loaded" @click="set('position', 'top')">{{ t('In alto') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgNumber from './controls/CfgNumber.vue';
import { okOrThrow, cfgJob, assertLoaded, reloadJob } from './cfgSave';
import { useCfgLettura } from './composables/useCfgLettura';

const TAB_ID = 'cookie';
const shellDirty = inject('setDirty', () => {});
const setDirty = (v) => shellDirty(v, TAB_ID);
const loaded = ref(false);
// Prima lettura: finché non riesce la shell mostra scheletro o errore al posto della scheda.
const lettura = useCfgLettura(TAB_ID, loaded, () => loadSettings());
// Esito della lettura fallita (stato HTTP o «nessuna risposta valida»): con la
// scheda bloccata, senza questa riga non si capiva perché nulla rispondesse.
const loadError = ref('');

// Valori della prima apertura: ogni lettura riparte da qui (vedi loadSettings).
const copia = (v) => JSON.parse(JSON.stringify(v));
const INIZIALE_FORM = {
  // Spento come il default del runtime: un sito mai salvato NON ha il banner.
  enabled: false,
  mode: 'optin',
  block_scripts: true,
  reshow_months: 6,
  position: 'bottom',
};
const form = ref(copia(INIZIALE_FORM));

const INIZIALE_CATEGORIE = [
  { id: 'necessary',  label: 'Strettamente necessari', desc: 'Carrello, login, lingua. Non disattivabili.',         required: true,  active: true,  count: 4 },
  { id: 'functional', label: 'Funzionali',             desc: 'Chat live, salvataggio form, preferenze.',              required: false, active: true,  count: 2 },
  { id: 'analytics',  label: 'Analytics',              desc: 'Google Analytics, Hotjar.',                             required: false, active: true,  count: 3 },
  { id: 'marketing',  label: 'Marketing & Pixel',      desc: 'Meta Pixel, LinkedIn Insight, Google Ads.',             required: false, active: false, count: 5 },
];
const categories = ref(copia(INIZIALE_CATEGORIE));

// I testi che il banner del sito mostra davvero (Olobuild_Cookie_Consent::TESTI): nella lingua principale le chiavi
// del runtime, nelle altre lingue di OLOlang testi_lingue[lingua][chiave] (vuoto = il testo principale). Prima qui
// c'erano testi italiano/inglese che il banner non leggeva.
const CAMPI_TESTO = [
  { k: 'banner_title', label: 'Titolo banner' },
  { k: 'banner_message', label: 'Testo banner', lungo: true },
  { k: 'accept_all_text', label: 'Pulsante Accetta' },
  { k: 'reject_all_text', label: 'Pulsante Rifiuta' },
  { k: 'preferences_text', label: 'Pulsante Preferenze' },
  { k: 'save_text', label: 'Pulsante Salva preferenze' },
  { k: 'privacy_text', label: 'Link alla privacy' },
  { k: 'cookie_policy_text', label: 'Link alla cookie policy' },
  { k: 'cat_necessary_label', label: 'Cookie necessari: nome' },
  { k: 'cat_necessary_desc', label: 'Cookie necessari: descrizione', lungo: true },
  { k: 'cat_analytics_label', label: 'Cookie di statistica: nome' },
  { k: 'cat_analytics_desc', label: 'Cookie di statistica: descrizione', lungo: true },
  { k: 'cat_marketing_label', label: 'Cookie di marketing: nome' },
  { k: 'cat_marketing_desc', label: 'Cookie di marketing: descrizione', lungo: true },
  { k: 'cat_preferences_label', label: 'Cookie di preferenza: nome' },
  { k: 'cat_preferences_desc', label: 'Cookie di preferenza: descrizione', lungo: true },
];
const lingue = ref([]);
const principale = ref('');
const activeLang = ref('');
const testi = ref({});
const testiLingue = ref({});

function testo(k) {
  if (!activeLang.value || activeLang.value === principale.value) return testi.value[k] || '';
  return (testiLingue.value[activeLang.value] || {})[k] || '';
}
function segnaposto(k) {
  return activeLang.value && activeLang.value !== principale.value ? (testi.value[k] || '') : '';
}

// Cosa fa DAVVERO il sito col banner acceso (chiavi del runtime lette dal GET):
// la conferma d'accensione lo elenca da qui, non dai controlli della scheda.
const INIZIALE_RUNTIME = { auto_block: true, block_iframes: true, banner_title: '', banner_message: '' };
const runtime = ref(copia(INIZIALE_RUNTIME));
const confirmOn = ref(false);
const switchEl = ref(null);
const confirmBtn = ref(null);

// Niente «modifiche» che lasciano il valore com'era: CfgNumber riemette il numero
// a ogni uscita dal campo, e bastava passarci col Tab perché «Salva impostazioni»
// rispedisse tutta la scheda. Prima della lettura i controlli sono disabilitati;
// una modifica che arrivasse comunque segna la scheda, e il salvataggio si ferma
// a voce alta con «dati non caricati» (assertLoaded) invece di perderla in silenzio.
function set(k, v) {
  if (form.value[k] === v) return;
  form.value[k] = v;
  setDirty(true);
}
function setTesto(k, v) {
  if (testo(k) === v) return;
  if (!activeLang.value || activeLang.value === principale.value) {
    testi.value[k] = v;
  } else {
    testiLingue.value[activeLang.value] = { ...(testiLingue.value[activeLang.value] || {}), [k]: v };
  }
  setDirty(true);
}
function toggleCategory(i) {
  categories.value[i].active = !categories.value[i].active;
  setDirty(true);
}

// Spegnere è immediato; accendere passa da una conferma in linea che dice
// cosa cambia sul sito pubblico.
function onToggleEnabled() {
  if (!loaded.value) return;
  if (form.value.enabled) {
    confirmOn.value = false;
    set('enabled', false);
    return;
  }
  confirmOn.value = true;
  nextTick(() => { if (confirmBtn.value) confirmBtn.value.focus(); });
}
function chiudiConferma() {
  confirmOn.value = false;
  nextTick(() => { if (switchEl.value) switchEl.value.focus(); });
}
function confermaAccensione() {
  set('enabled', true);
  chiudiConferma();
}
function annullaAccensione() { chiudiConferma(); }

function catDot(cat) {
  if (cat.required) return 'var(--c-text-faint)';
  if (cat.active)   return 'var(--c-red)';
  return 'var(--c-line)';
}

function previewBanner() {
  window.open((window.oloData?.siteUrl || '/') + '?olo_cookie_preview=1', '_blank', 'noopener');
}

async function loadSettings() {
  loadError.value = '';
  try {
    // okOrThrow: un 403 (nonce scaduto, plugin di sicurezza) o un 500 diventa un errore con lo stato.
    const res = await okOrThrow(lettura.fetch(`${window.oloData.restUrl}cookie-consent`, { headers: { 'X-WP-Nonce': window.oloData.nonce } }));
    const data = await res.json();
    // Si riparte dai valori iniziali: l'option mai salvata vale [] e i rami qui
    // sotto non toccherebbero niente, lasciando a video ciò che «Annulla» deve togliere.
    form.value = copia(INIZIALE_FORM);
    categories.value = copia(INIZIALE_CATEGORIE);
    testi.value = {};
    testiLingue.value = {};
    runtime.value = copia(INIZIALE_RUNTIME);
    confirmOn.value = false;
    if (data) {
      // Il GET dà lo stato del runtime, booleani già giudicati come li giudica il sito.
      if (data.enabled !== undefined) form.value.enabled = !!data.enabled;
      if (data.auto_block !== undefined) runtime.value.auto_block = !!data.auto_block;
      if (data.block_iframes !== undefined) runtime.value.block_iframes = !!data.block_iframes;
      if (typeof data.banner_title === 'string') runtime.value.banner_title = data.banner_title;
      if (typeof data.banner_message === 'string') runtime.value.banner_message = data.banner_message;
      if (data.mode) form.value.mode = data.mode;
      if (typeof data.block_scripts === 'boolean') form.value.block_scripts = data.block_scripts;
      if (data.reshow_months) form.value.reshow_months = data.reshow_months;
      if (data.position) form.value.position = data.position;
      if (Array.isArray(data.categories)) categories.value = data.categories;
      CAMPI_TESTO.forEach(({ k }) => { testi.value[k] = typeof data[k] === 'string' ? data[k] : ''; });
      testiLingue.value = data.testi_lingue && !Array.isArray(data.testi_lingue) ? copia(data.testi_lingue) : {};
      const l = data.lingue;
      principale.value = l && l.principale ? l.principale.codice : '';
      lingue.value = l ? [l.principale, ...(l.altre || [])].filter(Boolean) : [];
      if (!lingue.value.some((x) => x.codice === activeLang.value)) activeLang.value = principale.value;
    }
    loaded.value = true;
  } catch (e) {
    // Senza lettura la scheda resta bloccata (loaded false): si dice perché.
    // Rete assente o corpo non JSON non hanno uno stato HTTP da mostrare.
    loadError.value = e && /^\d{3}\b/.test(String(e.message)) ? String(e.message) : t('nessuna risposta valida');
  }
}

async function saveSettings() {
  assertLoaded(loaded);
  await okOrThrow(fetch(`${window.oloData.restUrl}cookie-consent`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
    body: JSON.stringify({ ...form.value, categories: categories.value, ...testi.value, testi_lingue: testiLingue.value }),
  }));
}

const onSave = cfgJob(TAB_ID, saveSettings);
const onDiscard = cfgJob(TAB_ID, reloadJob(loaded, loadSettings));

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
.category-row {
  display: grid;
  grid-template-columns: 24px 1fr 80px 50px;
  gap: 14px;
  align-items: center;
  padding: 14px 22px;
}
.category-row.has-border { border-bottom: 1px solid var(--c-line-soft); }
.cat-dot { width: 8px; height: 8px; border-radius: 2px; }
.cat-name { font-weight: 600; font-size: 14px; color: var(--c-navy); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.cat-desc { font-size: 12px; color: var(--c-text-mute); margin-top: 2px; }
.cat-count { font-size: 12px; font-family: var(--c-mono); color: var(--c-text-mute); }
.cat-required { font-size: 9px; padding: 1px 5px; }
.cat-required .dot { width: 5px; height: 5px; }
.cookie-confirm {
  display: grid; gap: 10px;
  margin: 4px 0 14px;
  padding: 14px 18px;
  background: var(--c-warning-soft);
  border: 1px solid var(--c-warning-soft);
  border-left: 3px solid var(--c-warning);
  border-radius: 10px;
  font-size: 13px;
  color: var(--c-text);
}
.cookie-confirm b { color: var(--c-navy); }
.cookie-confirm ul { margin: 0; padding-left: 18px; display: grid; gap: 4px; line-height: 1.5; }
.cookie-confirm ul li { margin: 0; list-style: disc; }
.cookie-confirm-actions { display: flex; flex-wrap: wrap; gap: 8px; }
.cookie-confirm .cfg-btn:focus-visible,
.cookie-enabled:focus-visible { outline: 2px solid var(--c-red); outline-offset: 2px; }
.cookie-load-error {
  margin: 0 0 16px;
  padding: 12px 16px;
  background: var(--c-red-soft);
  border: 1px solid var(--c-red-soft-2);
  border-left: 3px solid var(--c-red);
  border-radius: 10px;
  font-size: 13px;
  color: var(--c-red-dark);
}
/* Finché la lettura non arriva i controlli sono disabilitati, e deve vedersi
   (l'interruttore ha già lo stile :disabled globale). */
.cfg-segment button:disabled,
.cfg-input input:disabled,
.cfg-textarea textarea:disabled,
.cfg-number :deep(input:disabled) { opacity: .55; cursor: not-allowed; }
.cta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
@media (max-width: 900px) { .cta-grid { grid-template-columns: 1fr; } }
</style>
