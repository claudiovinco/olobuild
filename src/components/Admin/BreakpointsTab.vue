<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Dispositivi') }} <em>{{ t('dell\'editor') }}</em></h1>
      <p>{{ t('Le anteprime nella barra in alto del builder e le scelte dei controlli per dispositivo.') }}</p>
    </div>
  </div>

  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="14" height="11" rx="1.5"/><rect x="14" y="9" width="8" height="11" rx="1.5"/><path d="M5 20h6"/></svg>
      </div>
      <div>
        <h3>{{ t('Dispositivi accesi') }} <InfoTip :titolo="t('Dispositivi accesi')" :testo="info" /></h3>
        <p>{{ t('Desktop c\'è sempre.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div v-for="(d, i) in DEVICES" :key="d.key" class="cfg-row" :class="{ 'no-divider': i === DEVICES.length - 1 }">
        <div class="label-col">
          <label :id="'bp-' + d.key">{{ t(d.label) }}</label>
          <div class="hint">{{ soglia(d.key) }}</div>
        </div>
        <div class="control-col">
          <button
            type="button"
            class="cfg-switch"
            :class="{ 'is-on': enabled[d.key] }"
            role="switch"
            :aria-checked="enabled[d.key] ? 'true' : 'false'"
            :aria-labelledby="'bp-' + d.key"
            @click="toggle(d.key)"
          ></button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import InfoTip from '@/components/Builder/InfoTip.vue';
import { okOrThrow, cfgJob, assertLoaded, reloadJob } from './cfgSave';
import { useCfgLettura } from './composables/useCfgLettura';

// Prima questa scheda («Breakpoint responsive») salvava soglie e strategia che
// nessun renderer leggeva: le media query del sito sono fisse
// (Olobuild_Frontend_Renderer::SOGLIE_DISPOSITIVI). Ora comanda ciò che può
// comandare davvero: quali dispositivi offre l'editor (oloData.breakpointsEnabled).
const TAB_ID = 'responsive';
const shellDirty = inject('setDirty', () => {});
const setDirty = (v) => shellDirty(v, TAB_ID);
const loaded = ref(false);
// Prima lettura: finché non riesce la shell mostra scheletro o errore al posto della scheda.
const lettura = useCfgLettura(TAB_ID, loaded, () => loadSettings());

// Stesse chiavi di oloData.breakpointsEnabled (BuilderToolbar, BuilderInspector, DeviceSwitch).
const DEVICES = [
  { key: 'widescreen', label: 'Widescreen' },
  { key: 'tablet_landscape', label: 'Tablet orizzontale' },
  { key: 'tablet', label: 'Tablet' },
  { key: 'mobile_landscape', label: 'Mobile orizzontale' },
  { key: 'mobile', label: 'Mobile' },
];
const enabled = ref({});
const widths = ref({});
const info = [
  t('Un dispositivo acceso compare fra le anteprime della barra in alto del builder e fra le scelte dei controlli per dispositivo.'),
  t('Spegnerlo non cambia il sito: i valori già impostati per quel dispositivo restano attivi.'),
  t('Le soglie in pixel sono quelle del sito e non si cambiano da qui. La modifica vale dalla prossima apertura del builder.'),
];

function soglia(k) {
  const w = widths.value[k];
  if (!w) return '';
  return k === 'widescreen' ? `${w} px · ${t('solo anteprima')}` : `${t('fino a')} ${w} px`;
}
function toggle(k) { enabled.value[k] = !enabled.value[k]; setDirty(true); }

async function loadSettings() {
  try {
    const res = await lettura.fetch(`${window.oloData.restUrl}settings/breakpoints`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const d = await res.json();
      enabled.value = { ...(d.enabled || {}) };
      widths.value = { ...(d.widths || {}) };
      loaded.value = true;
    }
  } catch (e) { /* la shell mostra l'errore di lettura */ }
}

async function saveSettings() {
  assertLoaded(loaded);
  await okOrThrow(fetch(`${window.oloData.restUrl}settings/breakpoints`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
    body: JSON.stringify({ enabled: enabled.value }),
  }));
}

const onSave = cfgJob(TAB_ID, saveSettings);
const onDiscard = cfgJob(TAB_ID, reloadJob(loaded, loadSettings));

onMounted(() => {
  lettura.leggi();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>
