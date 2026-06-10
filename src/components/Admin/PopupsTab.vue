<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Popup globali') }}</h1>
      <p>{{ t('Popup riusabili che appaiono su una o più pagine, con regole condizionali e trigger (page load, scroll, exit intent, timer).') }}</p>
    </div>
    <div class="head-actions">
      <button class="cfg-btn cfg-btn-primary" @click="addPopup">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        {{ t('Nuovo popup') }}
      </button>
    </div>
  </div>

  <div v-if="!popups.length" class="cfg-card">
    <div class="cfg-card-body" style="text-align:center; padding: 40px 22px;">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-text-faint); margin-bottom:12px;"><rect x="3" y="5" width="18" height="15" rx="2"/><path d="M3 10h18"/></svg>
      <h3 style="font-size:16px; color:var(--c-navy); margin:0 0 8px;">{{ t('Nessun popup configurato') }}</h3>
      <p style="color:var(--c-text-mute); font-size:13px; max-width:48ch; margin:0 auto 16px;">
        {{ t('Crea un template Olobuild di tipo "Popup", poi associalo qui con le tue regole di visualizzazione.') }}
      </p>
      <button class="cfg-btn cfg-btn-primary" @click="addPopup">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        {{ t('Crea il primo popup') }}
      </button>
    </div>
  </div>

  <div v-for="(popup, idx) in popups" :key="idx" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="15" rx="2"/><path d="M3 10h18"/></svg></div>
      <div>
        <h3>{{ popup.name || t('Popup senza nome') }}</h3>
        <p>{{ t(triggerLabel(popup.trigger)) }} · {{ t(frequencyLabel(popup.frequency)) }}</p>
      </div>
      <div class="head-actions">
        <button class="cfg-switch" :class="{ 'is-on': popup.enabled }" @click="setField(idx, 'enabled', !popup.enabled)" role="switch" :title="t('Attiva/disattiva')"></button>
        <button class="cfg-btn-icon cfg-btn-danger" :title="t('Elimina popup')" @click="removePopup(idx)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Nome interno') }}</label></div>
        <div class="control-col"><div class="cfg-input cfg-w-md"><input type="text" :value="popup.name" @input="setField(idx, 'name', $event.target.value)" :placeholder="t('Es. Newsletter signup')" /></div></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Template') }}</label><div class="hint">{{ t('Quale template Olobuild usare come contenuto del popup.') }}</div></div>
        <div class="control-col">
          <CfgSelect :model-value="popup.template_id" :options="templateOptions" @update:model-value="setField(idx, 'template_id', parseInt($event) || 0)" />
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Trigger') }}</label></div>
        <div class="control-col">
          <CfgSelect size="md" :model-value="popup.trigger" :options="TRIGGER_OPTIONS" @update:model-value="setField(idx, 'trigger', $event)" />
        </div>
      </div>
      <div v-show="popup.trigger === 'page_load' || popup.trigger === 'timer'" class="cfg-row">
        <div class="label-col"><label>{{ t('Ritardo (secondi)') }}</label></div>
        <div class="control-col"><CfgNumber :model-value="popup.delay" :min="0" suffix="s" @update:model-value="setField(idx, 'delay', $event)" /></div>
      </div>
      <div v-show="popup.trigger === 'scroll_percent'" class="cfg-row">
        <div class="label-col"><label>{{ t('% scroll') }}</label></div>
        <div class="control-col"><CfgNumber :model-value="popup.scroll_percent" :min="1" :max="100" suffix="%" @update:model-value="setField(idx, 'scroll_percent', $event)" /></div>
      </div>
      <div v-show="popup.trigger === 'inactivity'" class="cfg-row">
        <div class="label-col"><label>{{ t('Secondi di inattività') }}</label></div>
        <div class="control-col"><CfgNumber :model-value="popup.inactivity_delay" :min="5" suffix="s" @update:model-value="setField(idx, 'inactivity_delay', $event)" /></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Frequenza') }}</label></div>
        <div class="control-col">
          <CfgSelect size="md" :model-value="popup.frequency" :options="FREQUENCY_OPTIONS" @update:model-value="setField(idx, 'frequency', $event)" />
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Overlay opacità') }}</label></div>
        <div class="control-col">
          <div class="cfg-slider">
            <input type="range" min="0" max="100" step="5" :value="popup.overlay_opacity" @input="setField(idx, 'overlay_opacity', parseInt($event.target.value))" />
            <span class="val">{{ popup.overlay_opacity }}%</span>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Larghezza massima (px)') }}</label></div>
        <div class="control-col"><CfgNumber :model-value="popup.max_width" :min="200" :max="1400" @update:model-value="setField(idx, 'max_width', $event)" /></div>
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

const popups = ref([]);
const templates = ref([]);

const TRIGGER_OPTIONS = [
  { value: 'page_load',      label: t('Al caricamento pagina') },
  { value: 'scroll_percent', label: t('Dopo % scroll') },
  { value: 'exit_intent',    label: t('Exit intent (mouse esce dal viewport)') },
  { value: 'timer',          label: t('Dopo timer') },
  { value: 'inactivity',     label: t('Dopo inattività') },
];
const FREQUENCY_OPTIONS = [
  { value: 'always',       label: t('Sempre') },
  { value: 'once_session', label: t('Una volta per sessione') },
  { value: 'once_day',     label: t('Una volta al giorno') },
  { value: 'once_week',    label: t('Una volta a settimana') },
  { value: 'once_ever',    label: t('Una sola volta in assoluto') },
];

const templateOptions = computed(() => [
  { value: 0, label: t('— Seleziona template —') },
  ...templates.value.map(tpl => ({ value: tpl.id, label: tpl.title })),
]);

function defaultPopup() {
  return {
    enabled: true, name: '', template_id: 0,
    trigger: 'page_load', delay: 3, scroll_percent: 50, inactivity_delay: 30,
    frequency: 'once_session', animation: 'fade',
    overlay_opacity: 60, overlay_blur: 0, close_overlay: true,
    radius: 12, max_width: 700,
    conditions: [], conditions_logic: 'OR',
  };
}

function addPopup() {
  popups.value.push(defaultPopup());
  setDirty(true);
}

function removePopup(idx) {
  if (!confirm(t('Eliminare questo popup?'))) return;
  popups.value.splice(idx, 1);
  setDirty(true);
}

function setField(idx, k, v) {
  popups.value[idx][k] = v;
  setDirty(true);
}

function triggerLabel(trigger) {
  const map = { page_load: 'Al caricamento', scroll_percent: 'Dopo % scroll', exit_intent: 'Exit intent', timer: 'Timer', inactivity: 'Inattività' };
  return map[trigger] || trigger;
}
function frequencyLabel(freq) {
  const map = { always: 'Ogni volta', once_session: '1/sessione', once_day: '1/giorno', once_week: '1/settimana', once_ever: '1 sola volta' };
  return map[freq] || freq;
}

async function loadTemplates() {
  try {
    const res = await fetch(`${window.oloData.restUrl}templates?per_page=200`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      templates.value = (data?.templates || data || []).map(t => ({ id: t.id || t.ID, title: t.title || t.post_title || '(no title)' }));
    }
  } catch (e) { /* keep empty */ }
}

async function loadPopups() {
  try {
    const res = await fetch(`${window.oloData.restUrl}global-popups`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      popups.value = Array.isArray(data) ? data : (data?.popups || []);
    }
  } catch (e) { /* keep empty */ }
}

async function savePopups() {
  try {
    await fetch(`${window.oloData.restUrl}global-popups`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ popups: popups.value }),
    });
  } catch (e) {
    showToast(t('Errore di salvataggio popup'), 'error');
  }
}

const onSave = () => savePopups();
const onDiscard = () => loadPopups();

onMounted(() => {
  loadPopups();
  loadTemplates();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>
