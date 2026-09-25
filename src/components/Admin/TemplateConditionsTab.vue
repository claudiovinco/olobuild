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
        <p>{{ t(contextLabel(rule.context)) }} · {{ templateTitle(rule.template_id) }} · {{ riassunto(rule) }} · {{ t('Priorità') }} {{ rule.priority }}</p>
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
          <CfgSelect searchable :model-value="rule.template_id" :options="optionsForRule(rule)" @update:model-value="setField(idx, 'template_id', parseInt($event) || 0)" />
          <TemplateListError />
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
            <div class="cfg-segment cond-mode" role="group" :aria-label="t('Includi o escludi')">
              <button type="button" :class="{ 'is-on': !cond.negate }" :aria-pressed="!cond.negate ? 'true' : 'false'" @click="setCond(idx, ci, 'negate', false)">{{ t('Includi') }}</button>
              <button
                type="button"
                :class="{ 'is-on': !!cond.negate }"
                :aria-pressed="cond.negate ? 'true' : 'false'"
                :disabled="cond.type === 'entire_site' && !cond.negate"
                :title="cond.type === 'entire_site' && !cond.negate ? t('Escludere tutto il sito vorrebbe dire «mai»') : ''"
                @click="setCond(idx, ci, 'negate', true)"
              >{{ t('Escludi') }}</button>
            </div>
            <CfgSelect class="cond-type" :model-value="cond.type" :options="typeOptionsFor(cond.type)" @update:model-value="setCond(idx, ci, 'type', $event)" />
            <div class="cond-value">
              <CondValue :type="cond.type" :model-value="cond.value" @update:model-value="setCond(idx, ci, 'value', $event)" />
            </div>
            <button class="cfg-btn-icon cfg-btn-danger cond-del" @click="removeCond(idx, ci)" :title="t('Rimuovi')">
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
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgSelect from './controls/CfgSelect.vue';
import CfgNumber from './controls/CfgNumber.vue';
import TemplateListError from './controls/TemplateListError.vue';
import CondValue from './controls/CondValue.vue';
import { okOrThrow, cfgJob, assertLoaded, reloadJob } from './cfgSave';
import { useCfgLettura } from './composables/useCfgLettura';
import { useTemplateOptions } from './composables/useTemplateOptions';
import { SCELTE, TIPI_SENZA_VALORE, valoreMostrato, etichettaValore } from './composables/useConditionChoices';

const TAB_ID = 'tplconditions';
const shellDirty = inject('setDirty', () => {});
const setDirty = (v) => shellDirty(v, TAB_ID);
const loaded = ref(false);
// Prima lettura: finché non riesce la shell mostra scheletro o errore al posto della scheda.
const lettura = useCfgLettura(TAB_ID, loaded, () => loadRules());

const rules = ref([]);
const { list: templateList, load: loadTemplates, optionsFor } = useTemplateOptions();

// I value sono quelli che il server valuta (class-template-conditions.php): non cambiano.
const conditionTypeOptions = [
  { value: 'entire_site',      label: t('Tutto il sito') },
  { value: 'front_page',       label: t('Solo homepage') },
  { value: 'singular',         label: t('Tutte le pagine singole') },
  { value: 'page',             label: t('Pagina') },
  { value: 'post',             label: t('Articolo') },
  { value: 'post_type',        label: t('Tipo di contenuto') },
  { value: 'archive',          label: t('Archivio') },
  { value: 'category',         label: t('Categoria') },
  { value: 'tag',              label: t('Tag') },
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

// Header e footer: solo i pubblicati del loro tipo (le bozze il sito non le
// disegna). Single e Archive: tutti, col tipo accanto. Il template già scelto
// resta comunque fra le voci e cambiare contesto non lo azzera.
function optionsForRule(rule) {
  const zone = rule.context === 'header' || rule.context === 'footer';
  return optionsFor({
    types: zone ? [rule.context] : null,
    publishedOnly: zone,
    showType: !zone,
    selected: rule.template_id,
  });
}

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
// Cambiando tipo il valore si azzera: l'ID di una pagina non è un ruolo né una categoria.
// Passando a «Tutto il sito» torna Includi: «Escludi Tutto il sito» vorrebbe dire «mai»,
// e il segmento lo impedisce anche quando il tipo è già quello.
function setCond(idx, ci, k, v) {
  const cond = rules.value[idx].conditions[ci];
  if (k === 'negate' ? !!cond.negate === v : cond[k] === v) return;
  cond[k] = v;
  if (k === 'type') {
    cond.value = '';
    if (v === 'entire_site') cond.negate = false;
  }
  setDirty(true);
}
function contextLabel(c) {
  const map = { header: 'Header', footer: 'Footer', single: 'Pagine singole', archive: 'Archivi' };
  return map[c] || c;
}

// Un tipo che il menu non offre (salvato via API o importato: taxonomy, author…)
// resta visibile col suo nome invece di «—».
function typeOptionsFor(type) {
  if (!type || conditionTypeOptions.some((o) => o.value === type)) return conditionTypeOptions;
  return [...conditionTypeOptions, { value: type, label: type }];
}

function templateTitle(id) {
  const n = parseInt(id, 10) || 0;
  if (!n) return t('nessun template');
  const tpl = templateList.value.find((x) => x.id === n);
  return tpl ? tpl.title : `${t('Template')} #${n}`;
}

function descriviCondizione(cond) {
  const tipo = String(cond.type || '');
  const nome = conditionTypeOptions.find((o) => o.value === tipo)?.label || tipo;
  const s = SCELTE[tipo];
  if (s) {
    if (valoreMostrato(tipo, cond.value) === '') return s.any ? t(s.any) : `${nome} (${t('nessuno')})`;
    return `${nome} ${etichettaValore(tipo, cond.value)}`;
  }
  if (TIPI_SENZA_VALORE.has(tipo) || cond.value === '' || cond.value == null) return nome;
  return `${nome} ${cond.value}`;
}

// Il riassunto segue la logica della regola: in AND le esclusioni sono «tranne …»,
// in OR ogni condizione è un'alternativa («… oppure non …»). Senza condizioni la
// regola vale ovunque (evaluate_conditions).
function riassunto(rule) {
  const conds = Array.isArray(rule.conditions) ? rule.conditions : [];
  if (!conds.length) return t('ovunque');
  if (rule.conditions_logic === 'OR') {
    return conds.map((c) => (c.negate ? `${t('non')} ` : '') + descriviCondizione(c)).join(` ${t('oppure')} `);
  }
  const si = conds.filter((c) => !c.negate).map(descriviCondizione);
  const no = conds.filter((c) => c.negate).map(descriviCondizione);
  let testo = si.length ? si.join(` ${t('e')} `) : t('ovunque');
  if (no.length) testo += `, ${t('tranne')} ${no.join(` ${t('e')} `)}`;
  return testo;
}

async function loadRules() {
  try {
    const res = await lettura.fetch(`${window.oloData.restUrl}template-conditions`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      rules.value = Array.isArray(data) ? data : (data?.rules || []);
      loaded.value = true;
    }
  } catch (e) { /* keep empty */ }
}

async function saveRules() {
  assertLoaded(loaded);
  await okOrThrow(fetch(`${window.oloData.restUrl}template-conditions`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
    body: JSON.stringify({ rules: rules.value }),
  }));
}

const onSave = cfgJob(TAB_ID, saveRules);
const onDiscard = cfgJob(TAB_ID, reloadJob(loaded, loadRules));

onMounted(() => {
  lettura.leggi();
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
  grid-template-columns: auto minmax(0, 1fr) minmax(0, 1fr) 36px;
  gap: 8px;
  margin-bottom: 6px;
  align-items: start;
}
.cond-value { min-width: 0; }
.cond-mode button:focus-visible { outline: 2px solid var(--c-red); outline-offset: 2px; }
.cond-mode button:disabled { opacity: .45; cursor: not-allowed; }
@media (max-width: 720px) {
  .cfg-cond-row {
    grid-template-columns: minmax(0, 1fr) 36px;
    grid-template-areas: 'mode mode' 'type del' 'value value';
  }
  .cond-mode  { grid-area: mode; justify-self: start; }
  .cond-type  { grid-area: type; }
  .cond-value { grid-area: value; }
  .cond-del   { grid-area: del; }
}
</style>
