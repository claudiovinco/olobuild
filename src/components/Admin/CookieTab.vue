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
          <button class="cfg-switch" :class="{ 'is-on': form.enabled }" @click="set('enabled', !form.enabled)" role="switch"></button>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Modalità') }}</label>
          <div class="hint">{{ t('Opt-in è richiesto in UE. Opt-out solo dove ammesso.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': form.mode === 'optin' }"     @click="set('mode', 'optin')">{{ t('Opt-in (GDPR)') }}</button>
            <button :class="{ 'is-on': form.mode === 'optout' }"    @click="set('mode', 'optout')">{{ t('Opt-out') }}</button>
            <button :class="{ 'is-on': form.mode === 'notify' }"    @click="set('mode', 'notify')">{{ t('Solo notifica') }}</button>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Blocca script fino al consenso') }}</label>
          <div class="hint">{{ t('Google Analytics, Meta Pixel, ecc. non partono finché l\'utente non accetta.') }}</div>
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': form.block_scripts }" @click="set('block_scripts', !form.block_scripts)" role="switch"></button>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Re-richiedi consenso dopo') }}</label>
          <div class="hint">{{ t('Mesi dopo i quali il banner ricompare.') }}</div>
        </div>
        <div class="control-col">
          <CfgNumber :model-value="form.reshow_months" :min="1" :max="36" :suffix="t('mesi')" @update:model-value="set('reshow_months', $event)" />
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
        <button class="cfg-switch" :class="{ 'is-on': cat.required || cat.active }" :disabled="cat.required" @click="!cat.required && toggleCategory(i)" role="switch"></button>
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
        <h3>{{ t('Copy del banner') }}</h3>
        <p>{{ t('Testi mostrati al visitatore. Supporta multilingua.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Lingua attiva') }}</label>
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': activeLang === 'it' }" @click="activeLang = 'it'">🇮🇹 Italiano</button>
            <button :class="{ 'is-on': activeLang === 'en' }" @click="activeLang = 'en'">🇬🇧 English</button>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Titolo banner') }}</label></div>
        <div class="control-col">
          <div class="cfg-input"><input type="text" :value="copy[activeLang].title" @input="setCopy('title', $event.target.value)" /></div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Testo banner') }}</label></div>
        <div class="control-col">
          <div class="cfg-textarea"><textarea rows="3" :value="copy[activeLang].body" @input="setCopy('body', $event.target.value)"></textarea></div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('CTA primario') }}</label></div>
        <div class="control-col">
          <div class="cta-grid">
            <div class="cfg-input"><input type="text" :value="copy[activeLang].accept_all" @input="setCopy('accept_all', $event.target.value)" :placeholder="t('Accetta tutti')" /></div>
            <div class="cfg-input"><input type="text" :value="copy[activeLang].only_essentials" @input="setCopy('only_essentials', $event.target.value)" :placeholder="t('Solo essenziali')" /></div>
            <div class="cfg-input"><input type="text" :value="copy[activeLang].customize" @input="setCopy('customize', $event.target.value)" :placeholder="t('Personalizza')" /></div>
          </div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col"><label>{{ t('Posizione') }}</label></div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': form.position === 'bottom' }"        @click="set('position', 'bottom')">{{ t('In basso') }}</button>
            <button :class="{ 'is-on': form.position === 'bottom_left' }"   @click="set('position', 'bottom_left')">{{ t('In basso a sx') }}</button>
            <button :class="{ 'is-on': form.position === 'bottom_right' }"  @click="set('position', 'bottom_right')">{{ t('In basso a dx') }}</button>
            <button :class="{ 'is-on': form.position === 'center' }"        @click="set('position', 'center')">{{ t('Centro overlay') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgNumber from './controls/CfgNumber.vue';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const form = ref({
  enabled: true,
  mode: 'optin',
  block_scripts: true,
  reshow_months: 6,
  position: 'bottom_left',
});

const categories = ref([
  { id: 'necessary',  label: 'Strettamente necessari', desc: 'Carrello, login, lingua. Non disattivabili.',         required: true,  active: true,  count: 4 },
  { id: 'functional', label: 'Funzionali',             desc: 'Chat live, salvataggio form, preferenze.',              required: false, active: true,  count: 2 },
  { id: 'analytics',  label: 'Analytics',              desc: 'Google Analytics, Hotjar.',                             required: false, active: true,  count: 3 },
  { id: 'marketing',  label: 'Marketing & Pixel',      desc: 'Meta Pixel, LinkedIn Insight, Google Ads.',             required: false, active: false, count: 5 },
]);

const activeLang = ref('it');
const copy = ref({
  it: { title: 'Utilizziamo i cookie', body: 'Per offrirti la migliore esperienza utilizziamo cookie. Puoi accettare tutti, solo gli essenziali o personalizzare le tue preferenze.', accept_all: 'Accetta tutti', only_essentials: 'Solo essenziali', customize: 'Personalizza' },
  en: { title: 'We use cookies', body: 'To give you the best experience we use cookies. Accept all, essentials only, or customize your preferences.', accept_all: 'Accept all', only_essentials: 'Essentials only', customize: 'Customize' },
});

function set(k, v) { form.value[k] = v; setDirty(true); }
function setCopy(k, v) { copy.value[activeLang.value][k] = v; setDirty(true); }
function toggleCategory(i) {
  categories.value[i].active = !categories.value[i].active;
  setDirty(true);
}

function catDot(cat) {
  if (cat.required) return 'var(--c-text-faint)';
  if (cat.active)   return 'var(--c-red)';
  return 'var(--c-line)';
}

function previewBanner() {
  window.open((window.oloData?.siteUrl || '/') + '?olo_cookie_preview=1', '_blank', 'noopener');
}

async function loadSettings() {
  try {
    const res = await fetch(`${window.oloData.restUrl}cookie-consent`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      if (data) {
        if (typeof data.enabled === 'boolean') form.value.enabled = data.enabled;
        if (data.mode) form.value.mode = data.mode;
        if (typeof data.block_scripts === 'boolean') form.value.block_scripts = data.block_scripts;
        if (data.reshow_months) form.value.reshow_months = data.reshow_months;
        if (data.position) form.value.position = data.position;
        if (Array.isArray(data.categories)) categories.value = data.categories;
        if (data.copy) Object.assign(copy.value, data.copy);
      }
    }
  } catch (e) { /* defaults */ }
}

async function saveSettings() {
  try {
    await fetch(`${window.oloData.restUrl}cookie-consent`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ ...form.value, categories: categories.value, copy: copy.value }),
    });
  } catch (e) { showToast(t('Errore di salvataggio Cookie'), 'error'); }
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
.cta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
@media (max-width: 900px) { .cta-grid { grid-template-columns: 1fr; } }
</style>
