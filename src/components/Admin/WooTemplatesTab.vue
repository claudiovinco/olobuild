<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Template WooCommerce') }}</h1>
      <p>{{ t('Assegna template Olobuild alle pagine standard di WooCommerce: singolo prodotto, archivio, categorie, carrello, checkout, account.') }}</p>
    </div>
    <div class="head-actions">
      <span v-if="!wooActive" class="cfg-pill warn"><span class="dot"></span> {{ t('WooCommerce non rilevato') }}</span>
      <span v-else class="cfg-pill ok"><span class="dot"></span> {{ t('WooCommerce attivo') }}</span>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.5"/><circle cx="19" cy="20" r="1.5"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg></div>
      <div>
        <h3>{{ t('Mappa template') }}</h3>
        <p>{{ t('Lascia "Default WooCommerce" per usare il rendering nativo di Woo.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <TemplateListError />
      <div v-for="pt in pageTypes" :key="pt.key" class="cfg-row">
        <div class="label-col">
          <label>{{ t(pt.label) }}</label>
          <div class="hint">{{ t(pt.hint) }}</div>
        </div>
        <div class="control-col">
          <CfgSelect searchable :model-value="form[pt.optionKey]" :options="optionsFor({ selected: form[pt.optionKey], emptyLabel: t('Default WooCommerce') })" @update:model-value="set(pt.optionKey, parseInt($event) || 0)" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgSelect from './controls/CfgSelect.vue';
import TemplateListError from './controls/TemplateListError.vue';
import { okOrThrow, cfgJob, assertLoaded, reloadJob, conIniziali, salvaERileggi } from './cfgSave';
import { useTemplateOptions } from './composables/useTemplateOptions';

const TAB_ID = 'wootemplates';
const shellDirty = inject('setDirty', () => {});
const setDirty = (v) => shellDirty(v, TAB_ID);
const loaded = ref(false);

// Le option che legge Olobuild_Woo_Template_Integration, stessi nomi della rotta
// /woo-templates (trait-olobuild-builder-settings.php). Con i vecchi nomi olo_* la
// scheda mostrava «Default WooCommerce» ovunque e il server ignorava il salvataggio.
const pageTypes = [
  { key: 'product_single',   optionKey: 'olobuild_woo_tpl_product_single',   label: 'Singolo prodotto',   hint: 'Pagina del singolo prodotto (is_product).' },
  { key: 'product_archive',  optionKey: 'olobuild_woo_tpl_product_archive',  label: 'Archivio prodotti',  hint: 'Pagina Shop (is_shop).' },
  { key: 'product_category', optionKey: 'olobuild_woo_tpl_product_category', label: 'Categoria prodotto', hint: 'Archivi categoria/tag prodotto. Nella griglia usa la categoria "current". Se vuoto: vale Archivio prodotti.' },
  { key: 'cart',             optionKey: 'olobuild_woo_tpl_cart',             label: 'Carrello',           hint: 'Pagina /cart.' },
  { key: 'checkout',         optionKey: 'olobuild_woo_tpl_checkout',         label: 'Checkout',           hint: 'Pagina /checkout.' },
  { key: 'myaccount',        optionKey: 'olobuild_woo_tpl_myaccount',        label: 'My Account',         hint: 'Area cliente loggato.' },
];

const INIZIALE = Object.fromEntries(pageTypes.map((pt) => [pt.optionKey, 0]));
const CHIAVI = Object.keys(INIZIALE);
const form = ref({ ...INIZIALE });

const wooActive = ref(true);

// L'elenco condiviso delle schede della Configurazione (useTemplateOptions): tutti i
// template, di ogni tipo e stato come prima, a pagine, ordinati per titolo. Prima
// arrivavano i 200 modificati più di recente e oltre quelli un template non si poteva
// assegnare. Il template assegnato resta sempre fra le voci, anche se non esiste più
// («Template non trovato #id»); se l'elenco non arriva lo dice, con «Riprova».
const { load: loadTemplates, optionsFor } = useTemplateOptions();

function set(k, v) { form.value[k] = v; setDirty(true); }

async function loadSettings(invariato) {
  try {
    const res = await fetch(`${window.oloData.restUrl}woo-templates`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      // Rilettura dopo un salvataggio: una scelta fatta nel frattempo resta a video.
      if (invariato && !invariato()) return;
      // Si riparte dai default: «Annulla modifiche» riporta anche le righe cambiate a video.
      const nostre = {};
      CHIAVI.forEach((k) => { if (data && data[k] !== undefined) nostre[k] = parseInt(data[k]) || 0; });
      form.value = conIniziali(INIZIALE, nostre);
      if (typeof data?.woo_active === 'boolean') wooActive.value = data.woo_active;
      loaded.value = true;
    }
  } catch (e) { /* defaults */ }
}

async function saveSettings() {
  assertLoaded(loaded);
  // Rilettura: a video resta ciò che il server ha scritto davvero, tranne le scelte
  // fatte mentre la richiesta era in volo, che restano da salvare (salvaERileggi).
  await salvaERileggi(() => JSON.stringify(form.value), () => okOrThrow(fetch(`${window.oloData.restUrl}woo-templates`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
    body: JSON.stringify(form.value),
  })), loadSettings);
}

const onSave = cfgJob(TAB_ID, saveSettings);
const onDiscard = cfgJob(TAB_ID, reloadJob(loaded, loadSettings));

onMounted(() => {
  loadSettings();
  loadTemplates();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>
