<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Assegnazione template') }}</h1>
      <p>{{ t('Regole che decidono quale Header / Footer mostrare in ogni contesto del sito. Quando più regole matchano, vince quella con priorità più bassa.') }}</p>
    </div>
    <div class="head-actions">
      <button class="cfg-btn cfg-btn-primary" @click="addRule">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        {{ t('Nuova regola') }}
      </button>
    </div>
  </div>

  <div v-if="!rules.length" class="cfg-card">
    <div class="cfg-card-body" style="text-align:center; padding: 40px 22px;">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-text-faint); margin-bottom:12px;"><rect x="10" y="2" width="4" height="4"/><rect x="3" y="14" width="4" height="4"/><rect x="10" y="14" width="4" height="4"/><rect x="17" y="14" width="4" height="4"/><path d="M12 6v4M5 14v-2h14v2"/></svg>
      <h3 style="font-size:16px; color:var(--c-navy); margin:0 0 8px;">{{ t('Nessuna regola configurata') }}</h3>
      <p style="color:var(--c-text-mute); font-size:13px; max-width:54ch; margin:0 auto 16px;">
        {{ t('Senza regole, il sito userà i template Header/Footer impostati come "attivi" nella gestione template. Aggiungi regole per applicare template diversi su contesti specifici (es. WooCommerce, landing page, ecc.).') }}
      </p>
      <button class="cfg-btn cfg-btn-primary" @click="addRule">{{ t('Crea la prima regola') }}</button>
    </div>
  </div>

  <div v-for="(rule, idx) in rules" :key="idx" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="10" y="2" width="4" height="4"/><rect x="10" y="14" width="4" height="4"/><path d="M12 6v8"/></svg></div>
      <div>
        <h3>{{ rule.name || t('Regola senza nome') }}</h3>
        <p>{{ t(contextLabel(rule.context)) }} · {{ t('Priorità') }} {{ rule.priority }}</p>
      </div>
      <div class="head-actions">
        <button class="cfg-switch" :class="{ 'is-on': rule.enabled }" @click="setField(idx, 'enabled', !rule.enabled)" role="switch"></button>
        <button class="cfg-btn-icon cfg-btn-danger" :title="t('Elimina')" @click="removeRule(idx)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Nome regola') }}</label></div>
        <div class="control-col"><div class="cfg-input cfg-w-md"><input type="text" :value="rule.name" @input="setField(idx, 'name', $event.target.value)" :placeholder="t('Es. Header WooCommerce')" /></div></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Contesto') }}</label><div class="hint">{{ t('Dove si applica la regola.') }}</div></div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': rule.context === 'header' }" @click="setField(idx, 'context', 'header')">Header</button>
            <button :class="{ 'is-on': rule.context === 'footer' }" @click="setField(idx, 'context', 'footer')">Footer</button>
            <button :class="{ 'is-on': rule.context === 'single' }" @click="setField(idx, 'context', 'single')">{{ t('Single') }}</button>
            <button :class="{ 'is-on': rule.context === 'archive' }" @click="setField(idx, 'context', 'archive')">{{ t('Archive') }}</button>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Template') }}</label></div>
        <div class="control-col">
          <CfgSelect :model-value="rule.template_id" :options="templateOptions" @update:model-value="setField(idx, 'template_id', parseInt($event) || 0)" />
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Priorità') }}</label><div class="hint">{{ t('1-999. Più basso = vince. Default 10.') }}</div></div>
        <div class="control-col"><CfgNumber :model-value="rule.priority" :min="1" :max="999" @update:model-value="setField(idx, 'priority', $event)" /></div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Operatore condizioni') }}</label></div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': rule.conditions_logic === 'AND' }" @click="setField(idx, 'conditions_logic', 'AND')">AND ({{ t('tutte') }})</button>
            <button :class="{ 'is-on': rule.conditions_logic === 'OR' }"  @click="setField(idx, 'conditions_logic', 'OR')">OR ({{ t('almeno una') }})</button>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Condizioni di match') }}</label><div class="hint">{{ t('Aggiungi condizioni per cui la regola si applica.') }}</div></div>
        <div class="control-col">
          <div v-for="(cond, ci) in (rule.conditions || [])" :key="ci" class="cfg-cond-row">
            <CfgSelect :model-value="cond.type" :options="conditionTypeOptions" @update:model-value="setCond(idx, ci, 'type', $event)" />
            <input type="text" :value="cond.value" @input="setCond(idx, ci, 'value', $event.target.value)" :placeholder="t('Valore (se richiesto)')" />
            <button class="cfg-btn-icon cfg-btn-danger" @click="removeCond(idx, ci)" :title="t('Rimuovi')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
          </div>
          <button class="cfg-btn cfg-btn-ghost" style="padding: 6px 10px; margin-top: 6px;" @click="addCond(idx)">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            {{ t('Aggiungi condizione') }}
          </button>
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

const rules = ref([]);
const templates = ref([]);

const conditionTypeOptions = [
  { value: 'entire_site',      label: t('Tutto il sito') },
  { value: 'front_page',       label: t('Solo homepage') },
  { value: 'singular',         label: t('Tutte le pagine singole') },
  { value: 'page',             label: t('Pagina (slug)') },
  { value: 'post',             label: t('Post (slug)') },
  { value: 'post_type',        label: t('Custom post type') },
  { value: 'archive',          label: t('Pagina di archivio') },
  { value: 'category',         label: t('Categoria (slug)') },
  { value: 'tag',              label: t('Tag (slug)') },
  { value: 'user_logged_in',   label: t('Utente loggato') },
  { value: 'user_logged_out',  label: t('Utente non loggato') },
  { value: 'user_role',        label: t('Ruolo utente') },
  { value: '404',              label: t('Pagina 404') },
  { value: 'search',           label: t('Pagina ricerca') },
  { value: 'woo_shop',         label: t('WooCommerce shop') },
  { value: 'woo_product',      label: t('WooCommerce singolo prodotto') },
  { value: 'woo_cart',         label: t('WooCommerce carrello') },
  { value: 'woo_checkout',     label: t('WooCommerce checkout') },
];

const templateOptions = computed(() => [
  { value: 0, label: t('— Seleziona template —') },
  ...templates.value.map(tpl => ({ value: tpl.id, label: tpl.title })),
]);

function defaultRule() {
  return {
    enabled: true, name: '', template_id: 0,
    context: 'header', priority: 10,
    conditions: [], conditions_logic: 'AND',
  };
}

function addRule()        { rules.value.push(defaultRule()); setDirty(true); }
function removeRule(idx)  {
  if (!confirm(t('Eliminare questa regola?'))) return;
  rules.value.splice(idx, 1); setDirty(true);
}
function setField(idx, k, v) { rules.value[idx][k] = v; setDirty(true); }
function addCond(idx) {
  if (!Array.isArray(rules.value[idx].conditions)) rules.value[idx].conditions = [];
  rules.value[idx].conditions.push({ type: 'entire_site', value: '', negate: false });
  setDirty(true);
}
function removeCond(idx, ci) { rules.value[idx].conditions.splice(ci, 1); setDirty(true); }
function setCond(idx, ci, k, v) { rules.value[idx].conditions[ci][k] = v; setDirty(true); }
function contextLabel(c) {
  const map = { header: 'Header', footer: 'Footer', single: 'Pagine singole', archive: 'Archivi' };
  return map[c] || c;
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

async function loadRules() {
  try {
    const res = await fetch(`${window.oloData.restUrl}template-conditions`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      rules.value = Array.isArray(data) ? data : (data?.rules || []);
    }
  } catch (e) { /* keep empty */ }
}

async function saveRules() {
  try {
    await fetch(`${window.oloData.restUrl}template-conditions`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify({ rules: rules.value }),
    });
  } catch (e) {
    showToast(t('Errore di salvataggio regole'), 'error');
  }
}

const onSave = () => saveRules();
const onDiscard = () => loadRules();

onMounted(() => {
  loadRules();
  loadTemplates();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.cfg-cond-row {
  display: grid;
  grid-template-columns: 1fr 1fr 36px;
  gap: 8px;
  margin-bottom: 6px;
  align-items: center;
}
.cfg-cond-row input {
  padding: 6px 10px;
  background: #fff;
  border: 1px solid var(--c-line);
  border-radius: 6px;
  font: 13px var(--c-sans);
  outline: none;
}
.cfg-cond-row input:focus {
  border-color: var(--c-red);
  box-shadow: 0 0 0 2px var(--c-red-soft);
}
</style>
