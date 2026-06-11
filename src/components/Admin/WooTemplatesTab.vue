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
      <div v-for="pt in pageTypes" :key="pt.key" class="cfg-row">
        <div class="label-col">
          <label>{{ t(pt.label) }}</label>
          <div class="hint">{{ t(pt.hint) }}</div>
        </div>
        <div class="control-col">
          <CfgSelect :model-value="form[pt.optionKey]" :options="templateOptions" @update:model-value="set(pt.optionKey, parseInt($event) || 0)" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgSelect from './controls/CfgSelect.vue';

const showToast = inject('showToast', () => {});
const setDirty  = inject('setDirty',  () => {});

const pageTypes = [
  { key: 'product_single',   optionKey: 'olo_woo_tpl_product_single',   label: 'Singolo prodotto',   hint: 'Pagina del singolo prodotto (is_product).' },
  { key: 'product_archive',  optionKey: 'olo_woo_tpl_product_archive',  label: 'Archivio prodotti',  hint: 'Pagina Shop (is_shop).' },
  { key: 'product_category', optionKey: 'olo_woo_tpl_product_category', label: 'Categoria prodotto', hint: 'Archivi categoria/tag prodotto. Nella griglia usa la categoria "current". Se vuoto: vale Archivio prodotti.' },
  { key: 'cart',             optionKey: 'olo_woo_tpl_cart',             label: 'Carrello',           hint: 'Pagina /cart.' },
  { key: 'checkout',         optionKey: 'olo_woo_tpl_checkout',         label: 'Checkout',           hint: 'Pagina /checkout.' },
  { key: 'myaccount',        optionKey: 'olo_woo_tpl_myaccount',        label: 'My Account',         hint: 'Area cliente loggato.' },
];

const form = ref({
  olo_woo_tpl_product_single: 0,
  olo_woo_tpl_product_archive: 0,
  olo_woo_tpl_product_category: 0,
  olo_woo_tpl_cart: 0,
  olo_woo_tpl_checkout: 0,
  olo_woo_tpl_myaccount: 0,
});

const templates = ref([]);
const wooActive = ref(true);

const templateOptions = computed(() => [
  { value: 0, label: t('Default WooCommerce') },
  ...templates.value.map(tpl => ({ value: tpl.id, label: tpl.title })),
]);

function set(k, v) { form.value[k] = v; setDirty(true); }

async function loadTemplates() {
  try {
    const res = await fetch(`${window.oloData.restUrl}templates?per_page=200`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      const list = data?.items || data?.templates || (Array.isArray(data) ? data : []);
      templates.value = list.map(t => ({ id: t.id || t.ID, title: t.title || t.post_title || '(no title)' }));
    }
  } catch (e) { /* keep empty */ }
}

async function loadSettings() {
  try {
    const res = await fetch(`${window.oloData.restUrl}woo-templates`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      for (const k of Object.keys(form.value)) {
        if (data && data[k] !== undefined) form.value[k] = parseInt(data[k]) || 0;
      }
      if (typeof data?.woo_active === 'boolean') wooActive.value = data.woo_active;
    }
  } catch (e) { /* defaults */ }
}

async function saveSettings() {
  try {
    await fetch(`${window.oloData.restUrl}woo-templates`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
      body: JSON.stringify(form.value),
    });
  } catch (e) {
    showToast(t('Errore di salvataggio template Woo'), 'error');
  }
}

const onSave = () => saveSettings();
const onDiscard = () => loadSettings();

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
