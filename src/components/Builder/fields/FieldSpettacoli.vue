<template>
  <div class="olo-spf">
    <p class="olo-spf-conto" role="status">
      <template v-if="stato">
        <b>{{ stato.pubblicati }}</b> {{ stato.pubblicati === 1 ? t('spettacolo pubblicato nel sito') : t('spettacoli pubblicati nel sito') }}<template v-if="stato.altri"> · {{ t('altri %s in bozza').replace('%s', () => stato.altri) }}</template>
      </template>
      <template v-else-if="errore">{{ t('Non riesco a leggere gli spettacoli del sito.') }}</template>
      <template v-else>{{ t('Caricamento…') }}</template>
    </p>
    <div class="olo-spf-riga">
      <a class="olo-spf-btn" :href="indirizzo('elenco')" target="_blank" rel="noopener">{{ t('Gestisci gli spettacoli') }}</a>
      <a class="olo-spf-btn" :href="indirizzo('nuovo')" target="_blank" rel="noopener">{{ t('Aggiungi uno spettacolo') }}</a>
    </div>
    <button
      v-if="voci.length"
      type="button"
      class="olo-spf-btn olo-spf-btn--pieno"
      :disabled="lavoro"
      @click="trasforma"
    >
      {{ lavoro ? t('Creo gli spettacoli…') : t('Crea gli spettacoli dalle voci della tile (%s)').replace('%s', () => voci.length) }}
    </button>
    <p v-if="esito" class="olo-spf-esito" role="status">{{ esito }}</p>
  </div>
</template>

<script setup>
/**
 * Pannello «Spettacoli del sito» della tile Cartellone (Appuntamenti da: Spettacoli del sito): quanti spettacoli ha
 * il sito, i link al menu Spettacoli di WordPress e, se la tile ha ancora le sue voci, il pulsante che le trasforma
 * in spettacoli (una voce già trasformata non si duplica). Dopo, l'anteprima si ridisegna.
 * Server: Olobuild_Spettacoli (REST olobuild/v1/spettacoli e spettacoli/da-voci).
 */
import { t } from '@/i18n';
import { ref, computed, onMounted } from 'vue';
import { oloFetch } from '@/composables/useApi';

const props = defineProps({
  modelValue: { default: null },
  tileSettings: { type: Object, default: () => ({}) },
});

const stato = ref(null);
const errore = ref(false);
const lavoro = ref(false);
const esito = ref('');

const voci = computed(() => {
  const v = props.tileSettings && Array.isArray(props.tileSettings.voci) ? props.tileSettings.voci : [];
  return v.filter((x) => x && String(x.titolo || '').trim() !== '');
});

const admin = (window.oloData && window.oloData.adminUrl) || '/wp-admin/';
function indirizzo(chiave) {
  if (stato.value && stato.value[chiave]) return stato.value[chiave];
  return admin + (chiave === 'nuovo' ? 'post-new.php' : 'edit.php') + '?post_type=olo_spettacolo';
}

async function carica() {
  try {
    stato.value = await oloFetch('/spettacoli');
    errore.value = false;
  } catch (e) {
    errore.value = true;
  }
}

async function trasforma() {
  lavoro.value = true;
  esito.value = '';
  try {
    const r = await oloFetch('/spettacoli/da-voci', { method: 'POST', body: { voci: voci.value } });
    const creati = Number(r && r.creati) || 0;
    const gia = Number(r && r.gia) || 0;
    esito.value = (creati === 1 ? t('Creato 1 spettacolo.') : t('Creati %s spettacoli.').replace('%s', () => creati))
      + (gia ? ' ' + t('%s c\'erano già.').replace('%s', () => gia) : '');
    await carica();
    window.dispatchEvent(new CustomEvent('olo:builder-force-rerender', { detail: { reason: 'spettacoli' } }));
  } catch (e) {
    esito.value = (e && e.message) || t('Non è stato possibile creare gli spettacoli.');
  } finally {
    lavoro.value = false;
  }
}

onMounted(carica);
</script>

<style scoped>
.olo-spf { display: flex; flex-direction: column; gap: 8px; }
.olo-spf-conto { margin: 0; font-size: 12px; color: #475569; }
.olo-spf-conto b { color: #1e293b; font-variant-numeric: tabular-nums; }
.olo-spf-riga { display: flex; flex-wrap: wrap; gap: 6px; }
.olo-spf-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 10px;
  font: inherit;
  font-size: 11px;
  font-weight: 600;
  line-height: 1.3;
  color: #1e293b;
  text-decoration: none;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  cursor: pointer;
}
.olo-spf-btn:hover:not(:disabled) { background: #f1f5f9; }
.olo-spf-btn:focus-visible { outline: 2px solid #1e293b; outline-offset: 2px; }
.olo-spf-btn:disabled { opacity: .6; cursor: default; }
.olo-spf-btn--pieno { color: #ffffff; background: #1e293b; border-color: #1e293b; }
.olo-spf-btn--pieno:hover:not(:disabled) { background: #334155; }
.olo-spf-esito { margin: 0; font-size: 11px; color: #475569; }
</style>
