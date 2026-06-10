<template>
  <div class="mb-space-y-4">
    <div class="mb-flex mb-items-center mb-gap-2 mb-mb-3">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-text-purple-400">
        <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
      </svg>
      <h3 class="mb-text-sm mb-font-semibold mb-text-gray-200 mb-m-0">{{ t('Impostazioni AI') }}</h3>
    </div>

    <!-- API Key Anthropic (principale) -->
    <div>
      <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Chiave API Anthropic (Claude)') }}</label>
      <div class="mb-flex mb-gap-1">
        <div class="mb-relative mb-flex-1">
          <input
            :type="showKey ? 'text' : 'password'"
            v-model="settings.anthropic_key"
            :placeholder="t('sk-ant-...')"
            class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-lg mb-px-3 mb-py-2 mb-text-sm mb-text-gray-200 mb-outline-none focus:mb-border-purple-500 mb-pr-8"
          />
          <button
            @click="showKey = !showKey"
            class="mb-absolute mb-right-2 mb-top-1/2 mb-transform mb--translate-y-1/2 mb-text-gray-500 hover:mb-text-gray-300"
            type="button"
            :title="showKey ? 'Nascondi' : 'Mostra'"
          >
            <svg v-if="showKey" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>
      <p class="mb-text-[10px] mb-text-gray-500 mb-mt-1 mb-m-0">
        Chiave per testo, traduzioni, layout, stile, alt text e CSS. Ottienila su
        <a href="https://console.anthropic.com/settings/keys" target="_blank" class="mb-text-purple-400 hover:mb-underline mb-no-underline">{{ t('console.anthropic.com') }}</a>
      </p>
    </div>

    <!-- Modello testo -->
    <div>
      <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Modello Claude') }}</label>
      <FieldSelect ui="dropdown" :model-value="settings.model" :options="MODEL_OPTS" @update:model-value="settings.model = $event" />
    </div>

    <!-- Separatore immagini -->
    <div class="mb-border-t mb-border-gray-700 mb-pt-3">
      <p class="mb-text-[10px] mb-text-gray-500 mb-m-0 mb-mb-2">{{ t('Opzionale: chiave OpenAI per generazione immagini (DALL-E)') }}</p>
    </div>

    <!-- API Key OpenAI (opzionale, solo immagini) -->
    <div>
      <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Chiave API OpenAI (opzionale)') }}</label>
      <div class="mb-relative">
        <input
          :type="showOpenaiKey ? 'text' : 'password'"
          v-model="settings.openai_key"
          :placeholder="t('sk-...')"
          class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-lg mb-px-3 mb-py-2 mb-text-sm mb-text-gray-200 mb-outline-none focus:mb-border-purple-500 mb-pr-8"
        />
        <button
          @click="showOpenaiKey = !showOpenaiKey"
          class="mb-absolute mb-right-2 mb-top-1/2 mb-transform mb--translate-y-1/2 mb-text-gray-500 hover:mb-text-gray-300"
          type="button"
        >
          <svg v-if="showOpenaiKey" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </div>
    </div>

    <!-- Modello immagine -->
    <div>
      <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">{{ t('Modello immagine') }}</label>
      <FieldSelect ui="dropdown" :model-value="settings.image_model" :options="IMAGE_MODEL_OPTS" @update:model-value="settings.image_model = $event" />
    </div>

    <!-- Pulsanti -->
    <div class="mb-flex mb-gap-2">
      <button
        @click="saveSettings"
        :disabled="saving"
        class="mb-flex-1 mb-px-4 mb-py-2 mb-bg-purple-600 mb-text-white mb-text-sm mb-font-medium mb-rounded-lg hover:mb-bg-purple-500 disabled:mb-opacity-50 disabled:mb-cursor-not-allowed mb-transition-colors"
      >
        {{ saving ? 'Salvataggio...' : 'Salva' }}
      </button>
      <button
        @click="testConnection"
        :disabled="testing"
        class="mb-px-4 mb-py-2 mb-bg-gray-700 mb-text-gray-300 mb-text-sm mb-rounded-lg hover:mb-bg-gray-600 mb-border mb-border-gray-600 disabled:mb-opacity-50 disabled:mb-cursor-not-allowed mb-transition-colors"
      >
        {{ testing ? 'Test...' : 'Testa connessione' }}
      </button>
    </div>

    <!-- Status messaggi -->
    <div v-if="statusMessage" :class="[
      'mb-text-xs mb-rounded-lg mb-px-3 mb-py-2 mb-border',
      statusType === 'success'
        ? 'mb-bg-green-900/30 mb-border-green-800/50 mb-text-green-400'
        : 'mb-bg-red-900/30 mb-border-red-800/50 mb-text-red-400'
    ]">
      {{ statusMessage }}
    </div>

    <!-- Stato corrente -->
    <div v-if="hasKey" class="mb-flex mb-items-center mb-gap-2 mb-text-xs mb-text-gray-500">
      <div class="mb-w-2 mb-h-2 mb-rounded-full mb-bg-green-500"></div>
      API Anthropic configurata
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useToast } from '@/composables/useToast.js';
import { t } from '@/i18n';
import FieldSelect from './fields/FieldSelect.vue';

const toast = useToast();

// Opzioni dei FieldSelect (label RAW: t() la applica FieldSelect internamente)
const MODEL_OPTS = [
  { value: 'claude-sonnet-4-6', label: 'Claude Sonnet 4.6 (bilanciato)' },
  { value: 'claude-haiku-4-5-20251001', label: 'Claude Haiku 4.5 (veloce, economico)' },
  { value: 'claude-opus-4-6', label: 'Claude Opus 4.6 (massima qualità)' },
];

const IMAGE_MODEL_OPTS = [
  { value: 'dall-e-3', label: 'DALL-E 3 (alta qualità)' },
  { value: 'dall-e-2', label: 'DALL-E 2 (più economico)' },
];

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
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': olo.nonce,
      },
      body: JSON.stringify({
        anthropic_key: settings.anthropic_key,
        openai_key: settings.openai_key,
        model: settings.model,
        image_model: settings.image_model,
      }),
    });
    if (res.ok) {
      statusMessage.value = 'Impostazioni salvate con successo.';
      statusType.value = 'success';
      hasKey.value = true;
      toast.success(t('Impostazioni AI salvate'));
    } else {
      const data = await res.json();
      throw new Error(data.message || 'Errore nel salvataggio');
    }
  } catch (e) {
    statusMessage.value = e.message;
    statusType.value = 'error';
  } finally {
    saving.value = false;
  }
}

async function testConnection() {
  testing.value = true;
  statusMessage.value = '';
  const olo = getOloData();

  // Prima salva le impostazioni correnti
  try {
    await fetch(olo.restUrl + '/ai/settings', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': olo.nonce,
      },
      body: JSON.stringify({
        anthropic_key: settings.anthropic_key,
        openai_key: settings.openai_key,
        model: settings.model,
        image_model: settings.image_model,
      }),
    });
  } catch (e) {
    // ignora errori di salvataggio, proviamo comunque
  }

  // Poi testa con una generazione breve
  try {
    const res = await fetch(olo.restUrl + '/ai/generate-text', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': olo.nonce,
      },
      body: JSON.stringify({
        prompt: 'Scrivi "Connessione riuscita" e nient\'altro.',
        type: 'headline',
        tone: 'professionale',
        language: 'it',
        max_length: 10,
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

onMounted(() => {
  loadSettings();
});
</script>
