<template>
  <div class="olo-api-tab">
    <!-- Stock Media -->
    <div class="olo-card">
      <div class="olo-card-head">
        <div class="olo-card-icon media">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
        </div>
        <div>
          <h3>Stock Media</h3>
          <p>Chiavi API per immagini, video e audio stock</p>
        </div>
      </div>
      <div class="olo-card-body">
        <div v-for="field in mediaFields" :key="field.key" class="olo-field-row">
          <div class="olo-field-info">
            <label :for="field.key">{{ field.label }}</label>
            <p v-if="field.help" class="olo-field-hint" v-html="field.help"></p>
          </div>
          <div class="olo-field-input-wrap">
            <input
              :id="field.key"
              type="text"
              v-model="values[field.key]"
              :placeholder="field.placeholder || 'Inserisci la chiave...'"
              class="olo-field-input"
            />
            <span v-if="values[field.key]" class="olo-field-status ok" title="Configurata">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- reCAPTCHA -->
    <div class="olo-card">
      <div class="olo-card-head">
        <div class="olo-card-icon security">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div>
          <h3>reCAPTCHA v3</h3>
          <p>Protezione anti-spam per i form &mdash; <a href="https://www.google.com/recaptcha" target="_blank" rel="noopener">Ottieni le chiavi</a></p>
        </div>
      </div>
      <div class="olo-card-body">
        <div class="olo-field-row">
          <div class="olo-field-info">
            <label for="recaptcha_site">Site Key</label>
          </div>
          <div class="olo-field-input-wrap">
            <input id="recaptcha_site" type="text" v-model="values.olo_recaptcha_site_key" placeholder="6L..." class="olo-field-input" />
          </div>
        </div>
        <div class="olo-field-row">
          <div class="olo-field-info">
            <label for="recaptcha_secret">Secret Key</label>
          </div>
          <div class="olo-field-input-wrap">
            <input id="recaptcha_secret" type="password" v-model="values.olo_recaptcha_secret_key" placeholder="6L..." class="olo-field-input" />
          </div>
        </div>
      </div>
    </div>

    <!-- Mailchimp -->
    <div class="olo-card">
      <div class="olo-card-head">
        <div class="olo-card-icon mail">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4l-10 8L2 4"/></svg>
        </div>
        <div>
          <h3>Mailchimp</h3>
          <p>API key per integrazioni form contatti</p>
        </div>
      </div>
      <div class="olo-card-body">
        <div class="olo-field-row">
          <div class="olo-field-info">
            <label for="mailchimp">API Key</label>
          </div>
          <div class="olo-field-input-wrap">
            <input id="mailchimp" type="text" v-model="values.olo_mailchimp_api_key" placeholder="xxxxxxxx-us1" class="olo-field-input" />
          </div>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="olo-actions">
      <button @click="save" :disabled="saving" class="olo-btn-save">
        <svg v-if="!saving" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <span v-if="saving" class="olo-spinner"></span>
        {{ saving ? 'Salvataggio...' : 'Salva modifiche' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, onMounted } from 'vue';

const oloData = window.oloData || {};
const showToast = inject('showToast', () => {});

const keys = [
  'olo_pexels_api_key', 'olo_pixabay_api_key', 'olo_unsplash_api_key',
  'olo_freesound_api_key', 'olo_recaptcha_site_key', 'olo_recaptcha_secret_key',
  'olo_mailchimp_api_key',
];

const values = ref({});
const saving = ref(false);

const mediaFields = [
  { key: 'olo_pexels_api_key', label: 'Pexels', help: '<a href="https://www.pexels.com/api/" target="_blank">pexels.com/api</a>' },
  { key: 'olo_pixabay_api_key', label: 'Pixabay', help: '<a href="https://pixabay.com/api/docs/" target="_blank">pixabay.com/api</a>' },
  { key: 'olo_unsplash_api_key', label: 'Unsplash', help: '<a href="https://unsplash.com/developers" target="_blank">unsplash.com/developers</a>' },
  { key: 'olo_freesound_api_key', label: 'Freesound', help: '<a href="https://freesound.org/apiv2/apply" target="_blank">freesound.org</a>' },
];

onMounted(async () => {
  try {
    const res = await fetch(`${oloData.restUrl}/settings/api-keys`, {
      headers: { 'X-WP-Nonce': oloData.nonce },
    });
    if (res.ok) {
      values.value = await res.json();
    }
  } catch (e) {
    console.error('Load API keys failed:', e);
  }
});

async function save() {
  saving.value = true;
  try {
    const res = await fetch(`${oloData.restUrl}/settings/api-keys`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': oloData.nonce,
      },
      body: JSON.stringify(values.value),
    });
    if (res.ok) {
      showToast('API keys salvate con successo');
    } else {
      showToast('Errore nel salvataggio', 'error');
    }
  } catch (e) {
    console.error('Save API keys failed:', e);
    showToast('Errore nel salvataggio', 'error');
  } finally {
    saving.value = false;
  }
}
</script>
