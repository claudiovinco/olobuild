<template>
  <div class="cfg-page-head">
    <div>
      <h1>AI <em>{{ t('Assistant') }}</em></h1>
      <p>{{ t('Generazione testi, alt-text immagini, traduzioni e suggerimenti UX all\'interno del builder. Le chiamate vengono fatturate dal provider AI che scegli.') }}</p>
    </div>
    <div class="head-actions">
      <span class="cfg-pill" :class="anyKey ? 'ok' : 'off'"><span class="dot"></span> {{ anyKey ? t('Provider connesso') : t('Non configurato') }}</span>
    </div>
  </div>

  <!-- ─── Provider ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3z"/><path d="M19 14l.7 2.3L22 17l-2.3.7L19 20l-.7-2.3L16 17l2.3-.7L19 14z"/></svg>
      </div>
      <div>
        <h3>{{ t('Provider') }}</h3>
        <p>{{ t('Scegli il modello che muove le funzioni AI dentro l\'editor.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Provider AI') }}</label>
          <div class="hint">{{ t('Puoi cambiarlo in qualsiasi momento — la chiave API è salvata per provider.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': form.provider === 'anthropic' }" @click="setField('provider', 'anthropic')">Anthropic</button>
            <button :class="{ 'is-on': form.provider === 'openai' }"    @click="setField('provider', 'openai')">OpenAI</button>
            <button :class="{ 'is-on': form.provider === 'mistral' }"   @click="setField('provider', 'mistral')">Mistral</button>
            <button :class="{ 'is-on': form.provider === 'selfhost' }"  @click="setField('provider', 'selfhost')">Self-hosted</button>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('API key') }} <span class="req">*</span></label>
          <div class="hint">{{ t('La chiave è criptata nel database. Inseriscila una sola volta.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-input mono">
            <!-- autocomplete="new-password": con "off" Chrome tratta il campo
                 come login e AUTOFILLA lo username nel primo input di testo
                 della pagina (la ricerca in topbar si riempiva di "admin"). -->
            <input
              :type="revealKey ? 'text' : 'password'"
              :value="currentKey"
              @input="updateKey($event.target.value)"
              :placeholder="placeholderKey"
              autocomplete="new-password"
              spellcheck="false"
              name="olo-ai-apikey"
              data-1p-ignore
              data-lpignore="true"
            />
            <button type="button" class="reveal" @click="revealKey = !revealKey" :title="revealKey ? t('Nascondi') : t('Mostra')">
              <svg v-if="!revealKey" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3l18 18M10.6 6.1A10.5 10.5 0 0 1 12 6c6.5 0 10 6 10 6a17.3 17.3 0 0 1-4 4.5M6.6 6.6A17.3 17.3 0 0 0 2 12s3.5 6 10 6c1.3 0 2.5-.3 3.6-.7"/></svg>
            </button>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Modello') }}</label>
          <div class="hint">{{ t('Modelli più potenti = risposte migliori ma costo maggiore per chiamata.') }}</div>
        </div>
        <div class="control-col">
          <CfgSelect :model-value="form.model" :options="availableModels" @update:model-value="setField('model', $event)" />
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Budget mensile') }}</label>
          <div class="hint">{{ t('Soglia di spesa oltre la quale le funzioni AI vengono disattivate.') }}</div>
        </div>
        <div class="control-col">
          <CfgNumber size="sm" :model-value="form.budget" :min="0" :step="5" :suffix="'€ / ' + t('mese')" @update:model-value="setField('budget', $event)" />
        </div>
      </div>
    </div>
  </div>

  <!-- ─── Comportamento ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L2 19l3 3 7.3-7.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-.6-.6-2.4 2.6-2.6z"/></svg>
      </div>
      <div>
        <h3>{{ t('Comportamento') }}</h3>
        <p>{{ t('Tono, lingua e creatività del modello.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Lingua di default') }}</label>
          <div class="hint">{{ t('Il modello risponderà in questa lingua se non specifichi altrimenti.') }}</div>
        </div>
        <div class="control-col">
          <CfgSelect size="md" :model-value="form.language" :options="LANGUAGE_OPTIONS" @update:model-value="setField('language', $event)" />
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Temperatura') }}</label>
          <div class="hint">{{ t('0 = preciso e ripetibile · 1 = creativo e variabile.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-slider">
            <input type="range" min="0" max="1" step="0.05" :value="form.temperature" @input="setField('temperature', parseFloat($event.target.value))" />
            <span class="val">{{ form.temperature.toFixed(2) }}</span>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Tono di voce') }}</label>
          <div class="hint">{{ t('Personalità di base che il modello adotta in tutti i task.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': form.tone === 'neutral' }"   @click="setField('tone', 'neutral')">{{ t('Neutrale') }}</button>
            <button :class="{ 'is-on': form.tone === 'warm' }"      @click="setField('tone', 'warm')">{{ t('Caldo') }}</button>
            <button :class="{ 'is-on': form.tone === 'technical' }" @click="setField('tone', 'technical')">{{ t('Tecnico') }}</button>
            <button :class="{ 'is-on': form.tone === 'editorial' }" @click="setField('tone', 'editorial')">{{ t('Editoriale') }}</button>
          </div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Istruzioni di sistema') }}</label>
          <div class="hint">{{ t('Contesto del brand che viene incluso in ogni chiamata. Max 500 caratteri.') }}</div>
        </div>
        <div class="control-col">
          <div class="cfg-textarea">
            <textarea rows="4" :value="form.system_prompt" @input="setField('system_prompt', $event.target.value.slice(0, 500))" :placeholder="systemPromptPlaceholder"></textarea>
          </div>
          <div class="text-xs mt-2" style="color:var(--c-text-faint);">{{ (form.system_prompt || '').length }} / 500 {{ t('caratteri') }}</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ─── Utilizzo ─── -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M7 17V8M12 17V4M17 17v-6"/></svg>
      </div>
      <div>
        <h3>{{ t('Utilizzo questo mese') }}</h3>
        <p>{{ t('Statistiche delle chiamate API negli ultimi 30 giorni.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="usage-grid">
        <div v-for="s in usageStats" :key="s.l" class="usage-card">
          <div class="usage-label">{{ t(s.l) }}</div>
          <div class="usage-value">{{ s.v }}</div>
          <div class="usage-trend">{{ s.t }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgSelect from './controls/CfgSelect.vue';
import CfgNumber from './controls/CfgNumber.vue';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const form = ref({
  provider: 'anthropic',
  anthropic_key: '',
  openai_key: '',
  mistral_key: '',
  selfhost_key: '',
  model: 'claude-sonnet-4-5',
  budget: 50,
  language: 'it',
  temperature: 0.35,
  tone: 'warm',
  system_prompt: '',
});

const MODELS = {
  anthropic: [
    { value: 'claude-sonnet-4-5',  label: 'Claude Sonnet 4.5 · ' + 'equilibrato' },
    { value: 'claude-haiku-4-5',   label: 'Claude Haiku 4.5 · veloce' },
    { value: 'claude-opus-4-1',    label: 'Claude Opus 4.1 · qualità massima' },
  ],
  openai: [
    { value: 'gpt-4o',       label: 'gpt-4o · qualità/prezzo equilibrato' },
    { value: 'gpt-4o-mini',  label: 'gpt-4o-mini · economico, veloce' },
    { value: 'o1',           label: 'o1 · ragionamento avanzato' },
  ],
  mistral: [
    { value: 'mistral-large', label: 'mistral-large' },
    { value: 'mistral-small', label: 'mistral-small' },
  ],
  selfhost: [
    { value: 'custom', label: 'Custom endpoint' },
  ],
};

const LANGUAGE_OPTIONS = [
  { value: 'it',   label: t('Italiano') },
  { value: 'en',   label: t('Inglese') },
  { value: 'auto', label: t('Auto (lingua del sito)') },
];

const revealKey = ref(false);
const availableModels = computed(() => MODELS[form.value.provider] || []);
const currentKey = computed(() => form.value[form.value.provider + '_key'] || '');
const placeholderKey = computed(() => {
  switch (form.value.provider) {
    case 'anthropic': return 'sk-ant-...';
    case 'openai':    return 'sk-proj-...';
    case 'mistral':   return 'mst-...';
    default:          return 'https://your-endpoint';
  }
});
const anyKey = computed(() => !!(form.value.anthropic_key || form.value.openai_key || form.value.mistral_key || form.value.selfhost_key));
const systemPromptPlaceholder = t('Es. Sei l’assistente di scrittura per un hotel boutique sul lago di Como. Tono caldo, professionale.');

const usageStats = ref([
  { l: 'Chiamate',       v: '—', t: t('Connetti AI per vedere statistiche') },
  { l: 'Token usati',    v: '—', t: '—' },
  { l: 'Spesa stimata',  v: '€ —', t: '—' },
  { l: 'Latenza media',  v: '—', t: '—' },
]);

function setField(k, v) {
  form.value[k] = v;
  if (k === 'provider') {
    const list = MODELS[v] || [];
    if (list.length && !list.find(m => m.value === form.value.model)) {
      form.value.model = list[0].value;
    }
  }
  setDirty(true);
}
function updateKey(val) {
  form.value[form.value.provider + '_key'] = val;
  setDirty(true);
}

async function loadSettings() {
  try {
    const res = await fetch(`${window.oloData.restUrl}ai/settings`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) Object.assign(form.value, await res.json());
  } catch (e) { /* defaults */ }
  try {
    const res2 = await fetch(`${window.oloData.restUrl}ai/usage`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res2.ok) {
      const data = await res2.json();
      if (data?.calls != null)   usageStats.value[0].v = String(data.calls);
      if (data?.tokens)          usageStats.value[1].v = data.tokens;
      if (data?.cost_estimate)   usageStats.value[2].v = '€ ' + data.cost_estimate;
      if (data?.latency_avg)     usageStats.value[3].v = data.latency_avg + ' s';
    }
  } catch (e) { /* keep dashes */ }
}

async function saveSettings() {
  try {
    await fetch(`${window.oloData.restUrl}ai/settings`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(form.value),
    });
  } catch (e) { showToast(t('Errore di salvataggio AI'), 'error'); }
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
.usage-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
@media (max-width: 900px) { .usage-grid { grid-template-columns: 1fr 1fr; } }
.usage-card { background: var(--c-bg); border-radius: 10px; padding: 14px; }
.usage-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--c-text-mute); }
.usage-value { font-family: var(--c-display); font-size: 32px; line-height: 1; margin-top: 6px; color: var(--c-navy); }
.usage-trend { font-size: 11px; color: var(--c-text-faint); margin-top: 4px; }
</style>
