<template>
  <div class="olo-ai-tab">
    <!-- Anthropic -->
    <div class="olo-card">
      <div class="olo-card-head">
        <div class="olo-card-icon" style="background:#1a1a1a;color:#fff">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2a3 3 0 0 1 3 3v1a3 3 0 0 1-6 0V5a3 3 0 0 1 3-3z"/><path d="M19 10h-1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2h1a1 1 0 0 0 1-1v-1a1 1 0 0 0-1-1H5"/><circle cx="9" cy="18" r="1" fill="currentColor"/><circle cx="15" cy="18" r="1" fill="currentColor"/></svg>
        </div>
        <div>
          <h3>Anthropic (Claude)</h3>
          <p>Chiave per testo, traduzioni, layout, stile, alt text e CSS &mdash; <a href="https://console.anthropic.com/settings/keys" target="_blank" rel="noopener">console.anthropic.com</a></p>
        </div>
        <div v-if="hasKey" class="olo-ai-status ok">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
          Configurata
        </div>
      </div>
      <div class="olo-card-body">
        <div class="olo-ai-fields">
          <div class="olo-field-row">
            <div class="olo-field-info">
              <label>API Key</label>
            </div>
            <div class="olo-field-input-wrap olo-ai-key-wrap">
              <input
                :type="showKey ? 'text' : 'password'"
                v-model="settings.anthropic_key"
                placeholder="sk-ant-..."
                class="olo-field-input"
              />
              <button @click="showKey = !showKey" class="olo-ai-eye" type="button" :title="showKey ? 'Nascondi' : 'Mostra'">
                <svg v-if="showKey" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>
          <div class="olo-field-row">
            <div class="olo-field-info">
              <label>Modello Claude</label>
            </div>
            <div class="olo-field-input-wrap">
              <select v-model="settings.model" class="olo-field-input olo-select">
                <option value="claude-sonnet-4-6">Claude Sonnet 4.6 (bilanciato)</option>
                <option value="claude-haiku-4-5-20251001">Claude Haiku 4.5 (veloce)</option>
                <option value="claude-opus-4-6">Claude Opus 4.6 (massima qualit&agrave;)</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- OpenAI (opzionale) -->
    <div class="olo-card">
      <div class="olo-card-head">
        <div class="olo-card-icon" style="background:#e8622a;color:#fff">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
        </div>
        <div>
          <h3>OpenAI (opzionale)</h3>
          <p>Chiave per generazione immagini con DALL-E</p>
        </div>
      </div>
      <div class="olo-card-body">
        <div class="olo-ai-fields">
          <div class="olo-field-row">
            <div class="olo-field-info">
              <label>API Key</label>
            </div>
            <div class="olo-field-input-wrap olo-ai-key-wrap">
              <input
                :type="showOpenaiKey ? 'text' : 'password'"
                v-model="settings.openai_key"
                placeholder="sk-..."
                class="olo-field-input"
              />
              <button @click="showOpenaiKey = !showOpenaiKey" class="olo-ai-eye" type="button">
                <svg v-if="showOpenaiKey" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>
          <div class="olo-field-row">
            <div class="olo-field-info">
              <label>Modello immagine</label>
            </div>
            <div class="olo-field-input-wrap">
              <select v-model="settings.image_model" class="olo-field-input olo-select">
                <option value="dall-e-3">DALL-E 3 (alta qualit&agrave;)</option>
                <option value="dall-e-2">DALL-E 2 (pi&ugrave; economico)</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Status message -->
    <Transition name="olo-fade">
      <div v-if="statusMessage" class="olo-ai-msg" :class="statusType">
        <svg v-if="statusType === 'success'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
        <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
        {{ statusMessage }}
      </div>
    </Transition>

    <!-- Actions -->
    <div class="olo-actions">
      <button @click="saveSettings" :disabled="saving" class="olo-btn-save">
        <svg v-if="!saving" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <span v-if="saving" class="olo-spinner"></span>
        {{ saving ? 'Salvataggio...' : 'Salva impostazioni' }}
      </button>
      <button @click="testConnection" :disabled="testing" class="olo-btn-reset">
        <svg v-if="!testing" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span v-if="testing" class="olo-spinner" style="border-color:rgba(0,0,0,.15);border-top-color:#374151"></span>
        {{ testing ? 'Test...' : 'Testa connessione' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, inject, onMounted } from 'vue';

const showToast = inject('showToast', () => {});

const showKey = ref(false);
const showOpenaiKey = ref(false);
const saving = ref(false);
const testing = ref(false);
const statusMessage = ref('');
const statusType = ref('success');
const hasKey = ref(false);

const settings = reactive({
  anthropic_key: '',
  openai_key: '',
  model: 'claude-sonnet-4-6',
  image_model: 'dall-e-3',
});

function getOloData() {
  return window.oloData || {};
}

async function loadSettings() {
  const olo = getOloData();
  try {
    const res = await fetch(olo.restUrl + '/ai/settings', {
      headers: { 'X-WP-Nonce': olo.nonce },
    });
    if (res.ok) {
      const data = await res.json();
      settings.anthropic_key = data.anthropic_key || '';
      settings.openai_key = data.openai_key || '';
      settings.model = data.model || 'claude-sonnet-4-6';
      settings.image_model = data.image_model || 'dall-e-3';
      hasKey.value = data.has_key || false;
    }
  } catch (e) {
    console.error('Errore caricamento impostazioni AI:', e);
  }
}

async function saveSettings() {
  saving.value = true;
  statusMessage.value = '';
  const olo = getOloData();
  try {
    const res = await fetch(olo.restUrl + '/ai/settings', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': olo.nonce },
      body: JSON.stringify({
        anthropic_key: settings.anthropic_key,
        openai_key: settings.openai_key,
        model: settings.model,
        image_model: settings.image_model,
      }),
    });
    if (res.ok) {
      hasKey.value = true;
      showToast('Impostazioni AI salvate');
    } else {
      const data = await res.json();
      throw new Error(data.message || 'Errore nel salvataggio');
    }
  } catch (e) {
    statusMessage.value = e.message;
    statusType.value = 'error';
    showToast('Errore nel salvataggio', 'error');
  } finally {
    saving.value = false;
  }
}

async function testConnection() {
  testing.value = true;
  statusMessage.value = '';
  const olo = getOloData();

  try {
    await fetch(olo.restUrl + '/ai/settings', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': olo.nonce },
      body: JSON.stringify({
        anthropic_key: settings.anthropic_key,
        openai_key: settings.openai_key,
        model: settings.model,
        image_model: settings.image_model,
      }),
    });
  } catch (e) { /* ignore */ }

  try {
    const res = await fetch(olo.restUrl + '/ai/generate-text', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': olo.nonce },
      body: JSON.stringify({
        prompt: 'Scrivi "Connessione riuscita" e nient\'altro.',
        type: 'headline', tone: 'professionale', language: 'it', max_length: 10,
      }),
    });
    if (res.ok) {
      statusMessage.value = 'Connessione riuscita! Claude funziona correttamente.';
      statusType.value = 'success';
      hasKey.value = true;
    } else {
      const data = await res.json();
      throw new Error(data.message || 'Test fallito');
    }
  } catch (e) {
    statusMessage.value = 'Test fallito: ' + e.message;
    statusType.value = 'error';
  } finally {
    testing.value = false;
  }
}

onMounted(() => { loadSettings(); });
</script>

<style scoped>
.olo-ai-fields {
  display: flex;
  flex-direction: column;
  gap: 0;
}
.olo-ai-key-wrap {
  position: relative;
}
.olo-ai-key-wrap .olo-field-input {
  padding-right: 38px;
}
.olo-ai-eye {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  display: flex;
  transition: color 0.15s;
}
.olo-ai-eye:hover {
  color: #6b7280;
}
.olo-select {
  cursor: pointer;
  appearance: auto;
}
.olo-ai-status {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  font-weight: 500;
  margin-left: auto;
  flex-shrink: 0;
  padding: 4px 10px;
  border-radius: 20px;
}
.olo-ai-status.ok {
  color: #059669;
  background: #ecfdf5;
}
.olo-ai-msg {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 500;
  border: 1px solid;
}
.olo-ai-msg.success {
  background: #f0fdf4;
  border-color: #bbf7d0;
  color: #15803d;
}
.olo-ai-msg.error {
  background: #fef2f2;
  border-color: #fecaca;
  color: #dc2626;
}
.olo-fade-enter-active { animation: olo-fadein .3s ease; }
.olo-fade-leave-active { animation: olo-fadein .2s ease reverse; }
@keyframes olo-fadein {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
