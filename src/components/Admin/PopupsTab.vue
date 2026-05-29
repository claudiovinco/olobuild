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
        <div class="control-col"><div class="cfg-input"><input type="text" :value="popup.name" @input="setField(idx, 'name', $event.target.value)" :placeholder="t('Es. Newsletter signup')" /></div></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Template') }}</label><div class="hint">{{ t('Quale template Olobuild usare come contenuto del popup.') }}</div></div>
        <div class="control-col">
          <div class="cfg-select">
            <select :value="popup.template_id" @change="setField(idx, 'template_id', parseInt($event.target.value) || 0)">
              <option value="0">{{ t('— Seleziona template —') }}</option>
              <option v-for="tpl in templates" :key="tpl.id" :value="tpl.id">{{ tpl.title }}</option>
            </select>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Trigger') }}</label></div>
        <div class="control-col">
          <div class="cfg-select">
            <select :value="popup.trigger" @change="setField(idx, 'trigger', $event.target.value)">
              <option value="page_load">{{ t('Al caricamento pagina') }}</option>
              <option value="scroll_percent">{{ t('Dopo % scroll') }}</option>
              <option value="exit_intent">{{ t('Exit intent (mouse esce dal viewport)') }}</option>
              <option value="timer">{{ t('Dopo timer') }}</option>
              <option value="inactivity">{{ t('Dopo inattività') }}</option>
            </select>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
          </div>
        </div>
      </div>
      <div v-show="popup.trigger === 'page_load' || popup.trigger === 'timer'" class="cfg-row">
        <div class="label-col"><label>{{ t('Ritardo (secondi)') }}</label></div>
        <div class="control-col"><div class="cfg-input"><input type="number" min="0" :value="popup.delay" @input="setField(idx, 'delay', parseInt($event.target.value) || 0)" /><span class="suffix">s</span></div></div>
      </div>
      <div v-show="popup.trigger === 'scroll_percent'" class="cfg-row">
        <div class="label-col"><label>{{ t('% scroll') }}</label></div>
        <div class="control-col"><div class="cfg-input"><input type="number" min="1" max="100" :value="popup.scroll_percent" @input="setField(idx, 'scroll_percent', parseInt($event.target.value) || 50)" /><span class="suffix">%</span></div></div>
      </div>
      <div v-show="popup.trigger === 'inactivity'" class="cfg-row">
        <div class="label-col"><label>{{ t('Secondi di inattività') }}</label></div>
        <div class="control-col"><div class="cfg-input"><input type="number" min="5" :value="popup.inactivity_delay" @input="setField(idx, 'inactivity_delay', parseInt($event.target.value) || 30)" /><span class="suffix">s</span></div></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Frequenza') }}</label></div>
        <div class="control-col">
          <div class="cfg-select">
            <select :value="popup.frequency" @change="setField(idx, 'frequency', $event.target.value)">
              <option value="always">{{ t('Sempre') }}</option>
              <option value="once_session">{{ t('Una volta per sessione') }}</option>
              <option value="once_day">{{ t('Una volta al giorno') }}</option>
              <option value="once_week">{{ t('Una volta a settimana') }}</option>
              <option value="once_ever">{{ t('Una sola volta in assoluto') }}</option>
            </select>
            <span class="chev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></span>
          </div>
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
        <div class="control-col"><div class="cfg-input"><input type="number" min="200" max="1400" :value="popup.max_width" @input="setField(idx, 'max_width', parseInt($event.target.value) || 700)" /></div></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const popups = ref([]);
const templates = ref([]);

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
