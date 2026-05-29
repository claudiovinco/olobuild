<template>
  <div class="cfg-page-head">
    <div>
      <h1>White <em>Label</em></h1>
      <p>{{ t('Personalizza nome, logo e branding del plugin per consegnarlo ai clienti senza riferimenti a Olobuild.') }}</p>
    </div>
    <div class="head-actions">
      <span class="cfg-pill ok"><span class="dot"></span> {{ t('Licenza Agency') }}</span>
    </div>
  </div>

  <!-- Identità del plugin -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3s7 8 7 13a7 7 0 0 1-14 0c0-5 7-13 7-13z"/></svg>
      </div>
      <div>
        <h3>{{ t('Identità del plugin') }}</h3>
        <p>{{ t('Come appare nel menu di WordPress e nel builder.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Nome plugin') }}</label>
          <div class="hint">{{ t('Sostituisce "Olobuild" nelle voci di menu WP, nell\'editor e nei messaggi di sistema.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-input"><input type="text" :value="form.plugin_name" @input="set('plugin_name', $event.target.value)" :placeholder="t('Es. Studio Builder')" /></div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Nome agenzia') }}</label>
          <div class="hint">{{ t('Visibile in footer di alcune schermate e nei meta dei file esportati.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-input"><input type="text" :value="form.author_name" @input="set('author_name', $event.target.value)" :placeholder="t('Es. Studio Conti & Associati')" /></div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Logo (chiaro)') }}</label>
          <div class="hint">{{ t('32×32px. Visibile in voce di menu WordPress.') }}</div>
        </div>
        <div class="control-col logo-row">
          <div class="logo-preview light" :style="logoLightStyle">
            <span v-if="!form.plugin_logo_light">{{ initial }}</span>
          </div>
          <button class="cfg-btn cfg-btn-secondary" @click="pickLogo('light')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            {{ t('Sostituisci') }}
          </button>
          <button v-if="form.plugin_logo_light" class="cfg-btn cfg-btn-icon cfg-btn-danger" :title="t('Rimuovi')" @click="set('plugin_logo_light', '')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Logo (scuro)') }}</label>
          <div class="hint">{{ t('Usato in barre scure dell\'editor.') }}</div>
        </div>
        <div class="control-col logo-row">
          <div class="logo-preview dark" :style="logoDarkStyle">
            <span v-if="!form.plugin_logo_dark">{{ initial }}</span>
          </div>
          <button class="cfg-btn cfg-btn-secondary" @click="pickLogo('dark')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            {{ t('Sostituisci') }}
          </button>
          <button v-if="form.plugin_logo_dark" class="cfg-btn cfg-btn-icon cfg-btn-danger" :title="t('Rimuovi')" @click="set('plugin_logo_dark', '')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('URL sito agenzia') }}</label>
          <div class="hint">{{ t('Link "Powered by" nei file esportati (se attivo).') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-input mono"><input type="url" :value="form.author_url" @input="set('author_url', $event.target.value)" placeholder="https://miaagenzia.it" /></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Visibilità -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18M10.6 6.1A10.5 10.5 0 0 1 12 6c6.5 0 10 6 10 6a17.3 17.3 0 0 1-4 4.5M6.6 6.6A17.3 17.3 0 0 0 2 12s3.5 6 10 6c1.3 0 2.5-.3 3.6-.7"/></svg>
      </div>
      <div>
        <h3>{{ t('Visibilità') }}</h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Nascondi "Powered by Olobuild"') }}</label>
          <div class="hint">{{ t('Toglie attribuzione nel footer dell\'editor e nei file generati.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.hide_credits }" @click="set('hide_credits', !form.hide_credits)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Nascondi changelog & roadmap') }}</label>
          <div class="hint">{{ t('Il cliente non vede comunicazioni del team Olobuild.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.hide_changelog }" @click="set('hide_changelog', !form.hide_changelog)" role="switch"></button></div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Nascondi a non-admin') }}</label>
          <div class="hint">{{ t('Gli utenti senza ruolo amministratore non vedono il menu Olobuild in admin.') }}</div>
        </div>
        <div class="control-col"><button class="cfg-switch" :class="{ 'is-on': form.hide_for_non_admins }" @click="set('hide_for_non_admins', !form.hide_for_non_admins)" role="switch"></button></div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Link Documentazione custom') }}</label>
          <div class="hint">{{ t('Sostituisci il link "Documentazione" con uno tuo.') }}</div>
        </div>
        <div class="control-col" style="display:flex; gap:8px; align-items:center;">
          <button class="cfg-switch" :class="{ 'is-on': form.custom_doc_enabled }" @click="set('custom_doc_enabled', !form.custom_doc_enabled)" role="switch"></button>
          <div class="cfg-input" style="flex:1;"><input type="url" :value="form.custom_doc_url" @input="set('custom_doc_url', $event.target.value)" placeholder="https://miaagenzia.it/guida" :disabled="!form.custom_doc_enabled" /></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const form = ref({
  enabled: true,
  plugin_name: '',
  plugin_description: '',
  plugin_logo_light: '',
  plugin_logo_dark: '',
  author_name: '',
  author_url: '',
  hide_credits: false,
  hide_changelog: false,
  hide_for_non_admins: false,
  custom_doc_enabled: false,
  custom_doc_url: '',
});

const initial = computed(() => (form.value.plugin_name || form.value.author_name || 'O').charAt(0).toUpperCase());

const logoLightStyle = computed(() => ({
  background: form.value.plugin_logo_light
    ? `center / contain no-repeat #fff url(${form.value.plugin_logo_light})`
    : 'var(--c-bg)',
}));
const logoDarkStyle = computed(() => ({
  background: form.value.plugin_logo_dark
    ? `center / contain no-repeat var(--c-navy) url(${form.value.plugin_logo_dark})`
    : 'var(--c-navy)',
}));

function set(k, v) { form.value[k] = v; setDirty(true); }

function pickLogo(kind) {
  if (!window.wp || !window.wp.media) {
    showToast(t('WP Media Library non disponibile'), 'error');
    return;
  }
  const frame = window.wp.media({ title: t('Scegli logo'), button: { text: t('Usa questo logo') }, multiple: false });
  frame.on('select', () => {
    const att = frame.state().get('selection').first().toJSON();
    if (kind === 'light') form.value.plugin_logo_light = att.url;
    else                  form.value.plugin_logo_dark  = att.url;
    setDirty(true);
  });
  frame.open();
}

async function loadSettings() {
  try {
    const res = await fetch(`${window.oloData.restUrl}white-label`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) Object.assign(form.value, await res.json());
  } catch (e) { /* defaults */ }
}

async function saveSettings() {
  try {
    await fetch(`${window.oloData.restUrl}white-label`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(form.value),
    });
  } catch (e) { showToast(t('Errore di salvataggio White Label'), 'error'); }
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
.logo-row { display: flex; gap: 12px; align-items: center; }
.logo-preview {
  width: 48px; height: 48px;
  border-radius: 10px;
  display: grid; place-items: center;
  border: 1px solid var(--c-line);
  font-family: var(--c-display);
  font-size: 24px;
  flex-shrink: 0;
}
.logo-preview.light { color: var(--c-navy); }
.logo-preview.dark  { color: #fff; border-color: var(--c-navy); }
</style>
