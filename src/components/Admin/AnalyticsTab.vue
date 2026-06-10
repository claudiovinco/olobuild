<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Tracking & Analytics') }}</h1>
      <p>{{ t('Codici di tracciamento (Google Analytics 4, Facebook Pixel, GTM, Clarity, Hotjar), eventi automatici, script custom in head/body.') }}</p>
    </div>
    <div class="head-actions">
      <span class="cfg-pill" :class="anyConnected ? 'ok' : 'off'">
        <span class="dot"></span>
        {{ anyConnected ? t('Tracker connessi') : t('Nessun tracker') }}
      </span>
    </div>
  </div>

  <!-- ─── Tracker IDs ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 4 4 5-5"/></svg></div>
      <div>
        <h3>{{ t('ID dei tracker') }}</h3>
        <p>{{ t('Lascia vuoto per disattivare un tracker.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Google Analytics 4') }}</label><div class="hint">{{ t('Measurement ID (es. G-XXXXXXXX).') }}</div></div>
        <div class="control-col"><div class="cfg-input mono cfg-w-md"><input type="text" :value="form.ga_id" @input="set('ga_id', $event.target.value)" placeholder="G-XXXXXXXX" /></div></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Facebook Pixel') }}</label></div>
        <div class="control-col"><div class="cfg-input mono cfg-w-md"><input type="text" :value="form.fb_pixel_id" @input="set('fb_pixel_id', $event.target.value)" placeholder="123456789012345" /></div></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Google Tag Manager') }}</label></div>
        <div class="control-col"><div class="cfg-input mono cfg-w-md"><input type="text" :value="form.gtm_id" @input="set('gtm_id', $event.target.value)" placeholder="GTM-XXXXXXX" /></div></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Microsoft Clarity') }}</label></div>
        <div class="control-col"><div class="cfg-input mono cfg-w-md"><input type="text" :value="form.clarity_id" @input="set('clarity_id', $event.target.value)" placeholder="xxxxxxxxx" /></div></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Hotjar Site ID') }}</label></div>
        <div class="control-col"><div class="cfg-input mono cfg-w-md"><input type="text" :value="form.hotjar_id" @input="set('hotjar_id', $event.target.value)" placeholder="1234567" /></div></div>
      </div>
    </div>
  </div>

  <!-- ─── Event tracking ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3z"/></svg></div>
      <div>
        <h3>{{ t('Eventi automatici') }}</h3>
        <p>{{ t('Olobuild emette eventi GA4 / FB Pixel quando un utente interagisce con i tile.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div v-for="ev in events" :key="ev.key" class="cfg-row">
        <div class="label-col"><label>{{ t(ev.label) }}</label><div class="hint">{{ t(ev.hint) }}</div></div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': form[ev.key] }" @click="set(ev.key, !form[ev.key])" role="switch"></button>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Percentuali scroll (CSV)') }}</label><div class="hint">{{ t('Quando tracciare scroll depth, es. 25,50,75,100.') }}</div></div>
        <div class="control-col"><div class="cfg-input mono cfg-w-md"><input type="text" :value="form.scroll_milestones" @input="set('scroll_milestones', $event.target.value)" /></div></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Estensioni download (CSV)') }}</label></div>
        <div class="control-col"><div class="cfg-input mono cfg-w-md"><input type="text" :value="form.download_extensions" @input="set('download_extensions', $event.target.value)" placeholder="pdf,zip,docx" /></div></div>
      </div>
    </div>
  </div>

  <!-- ─── Privacy ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
      <div>
        <h3>{{ t('Privacy & Consenso') }}</h3>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Anonimizza IP (GA4)') }}</label></div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.anonymize_ip }" @click="set('anonymize_ip', !form.anonymize_ip)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Rispetta Do-Not-Track') }}</label></div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.respect_dnt }" @click="set('respect_dnt', !form.respect_dnt)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Escludi amministratori') }}</label><div class="hint">{{ t('Non tracciare le visite degli utenti loggati come admin.') }}</div></div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.exclude_admins }" @click="set('exclude_admins', !form.exclude_admins)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Richiedi consenso cookie') }}</label><div class="hint">{{ t('Carica i tracker solo dopo il consenso del banner.') }}</div></div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.consent_required }" @click="set('consent_required', !form.consent_required)" role="switch"></button></div>
      </div>
    </div>
  </div>

  <!-- ─── Custom scripts ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 18 6-6-6-6M8 6l-6 6 6 6"/></svg></div>
      <div>
        <h3>{{ t('Script custom') }}</h3>
        <p>{{ t('Codice arbitrario da iniettare nel sito. Usa con attenzione — possono rompere la pagina o esporre dati sensibili.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('In <head>') }}</label><div class="hint">{{ t('Meta tag, fonts, link prefetch, codice di verifica.') }}</div></div>
        <div class="control-col"><div class="cfg-textarea"><textarea rows="5" :value="form.head_scripts" @input="set('head_scripts', $event.target.value)" placeholder="<meta ...> <script>...</script>"></textarea></div></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('In <body>') }}</label><div class="hint">{{ t('Subito dopo l\'apertura del body (GTM noscript, chat widget…).') }}</div></div>
        <div class="control-col"><div class="cfg-textarea"><textarea rows="5" :value="form.body_scripts" @input="set('body_scripts', $event.target.value)"></textarea></div></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const events = [
  { key: 'track_buttons',   label: 'Click sui pulsanti',  hint: 'Emette event click su ogni button tile.' },
  { key: 'track_forms',     label: 'Invio form',          hint: 'Tracking submit di tutti i form tile.' },
  { key: 'track_video',     label: 'Riproduzione video',  hint: 'Play/pause su video tile.' },
  { key: 'track_scroll',    label: 'Profondità scroll',   hint: 'Vedi "Percentuali scroll" qui sotto.' },
  { key: 'track_pricing',   label: 'Click su pricing CTA',hint: 'CTA delle tile pricing.' },
  { key: 'track_downloads', label: 'Download file',       hint: 'Link verso file con estensioni elencate sotto.' },
  { key: 'track_outbound',  label: 'Link esterni',        hint: 'Click su link verso altri domini.' },
];

const form = ref({
  ga_id: '', fb_pixel_id: '', gtm_id: '', clarity_id: '', hotjar_id: '',
  track_buttons: true, track_forms: true, track_video: true,
  track_scroll: true, track_pricing: true, track_downloads: true, track_outbound: true,
  scroll_milestones: '25,50,75,100',
  download_extensions: 'pdf,zip,doc,docx,xls,xlsx',
  anonymize_ip: true, respect_dnt: false, exclude_admins: true, consent_required: true,
  head_scripts: '', body_scripts: '',
});

const anyConnected = computed(() => !!(form.value.ga_id || form.value.fb_pixel_id || form.value.gtm_id || form.value.clarity_id || form.value.hotjar_id));

function set(k, v) { form.value[k] = v; setDirty(true); }

async function loadSettings() {
  try {
    const res = await fetch(`${window.oloData.restUrl}analytics`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) Object.assign(form.value, await res.json());
  } catch (e) { /* defaults */ }
}

async function saveSettings() {
  try {
    await fetch(`${window.oloData.restUrl}analytics`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(form.value),
    });
  } catch (e) {
    showToast(t('Errore di salvataggio tracking'), 'error');
  }
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
