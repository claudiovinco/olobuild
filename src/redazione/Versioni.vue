<template>
  <Teleport to="body">
    <div class="orz orz-modale" role="dialog" aria-modal="true" :aria-label="t('Versioni')">
      <header class="orz-modale-testa">
        <h2 class="orz-modale-titolo"><Icona nome="orologio" :dim="18" /> {{ t('Versioni') }}</h2>
        <span class="orz-spazio"></span>
        <button ref="chiudiBtn" type="button" class="orz-btn-icona" :aria-label="t('Chiudi')" :title="t('Chiudi')" @click="emit('chiudi')">
          <Icona nome="x" :dim="18" />
        </button>
      </header>
      <div class="orz-versioni">
        <nav class="orz-versioni-elenco" :aria-label="t('Versioni salvate')">
          <p v-if="caricamento" class="orz-nota orz-centro">{{ t('Caricamento…') }}</p>
          <p v-else-if="errore" class="orz-avviso orz-avviso--errore" role="alert">{{ errore }}</p>
          <p v-else-if="!voci.length" class="orz-nota orz-centro">{{ t('Nessuna versione salvata') }}</p>
          <button
            v-for="v in voci"
            :key="v.id"
            type="button"
            class="orz-versione"
            :class="{ on: scelta === v.id }"
            :aria-current="scelta === v.id ? 'true' : undefined"
            @click="apri(v.id)"
          >
            <span class="orz-versione-quando">{{ v.quando }}</span>
            <span class="orz-versione-chi">{{ v.autore }}</span>
            <span v-if="v.adesso" class="orz-stato orz-stato--publish">{{ t('Adesso') }}</span>
          </button>
        </nav>

        <section class="orz-versione-dettaglio">
          <p v-if="caricaDettaglio" class="orz-nota orz-centro">{{ t('Caricamento…') }}</p>
          <p v-else-if="erroreDettaglio" class="orz-avviso orz-avviso--errore" role="alert">{{ erroreDettaglio }}</p>
          <p v-else-if="!d" class="orz-nota orz-centro">{{ t('Scegli una versione per vedere cosa cambia.') }}</p>
          <template v-else>
            <div class="orz-versione-testa">
              <div>
                <h3 class="orz-versione-titolo">{{ d.titolo || t('(senza titolo)') }}</h3>
                <p class="orz-nota">{{ t('Salvata il %1$s da %2$s').replace('%1$s', d.quando).replace('%2$s', d.autore) }}</p>
              </div>
              <button type="button" class="orz-btn orz-btn--primario" :disabled="uguale" @click="emit('riprendi', d)">
                <Icona nome="annulla" /> {{ t('Riprendi questa versione') }}
              </button>
            </div>

            <p v-if="uguale" class="orz-avviso orz-avviso--ok">{{ t('È uguale al contenuto di adesso.') }}</p>
            <template v-else>
              <div v-if="d.cambia.length" class="orz-cambia">
                <span class="orz-cambia-nome">{{ t('Diverso da adesso') }}:</span>
                <span v-for="c in d.cambia" :key="c" class="orz-chip">{{ c }}</span>
              </div>
              <p v-if="!d.fotografia" class="orz-nota">{{ t('Di questa versione restano solo titolo, testo e riassunto.') }}</p>

              <div class="orz-blocco">
                <div class="orz-etichetta orz-etichetta--fila">
                  <span>{{ t('Testo') }}</span>
                  <span v-if="d.differenze" class="orz-legenda">
                    <span class="orz-legenda-voce orz-legenda--prima">{{ t('Questa versione') }}</span>
                    <span class="orz-legenda-voce orz-legenda--ora">{{ t('Adesso') }}</span>
                  </span>
                </div>
                <!-- La tabella di wp_text_diff(): il testo vi arriva già protetto. -->
                <div v-if="d.differenze" class="orz-diff" v-html="d.differenze"></div>
                <p v-else class="orz-nota">{{ t('Il testo è uguale a quello di adesso.') }}</p>
                <p v-if="d.differenze && !d.testoRipristinabile" class="orz-riga-info orz-riga-info--a-capo">
                  {{ t('Questo testo si riprende dalle revisioni di WordPress.') }}
                  <a class="orz-link" :href="d.wpConfronta" target="_blank" rel="noopener"><Icona nome="esterno" :dim="14" /> {{ t('Apri le revisioni') }}</a>
                </p>
              </div>
            </template>
          </template>
        </section>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
/**
 * Le versioni di un contenuto (le revisioni di WordPress, con la fotografia di campi e categorie dalla 1.4.860): a
 * sinistra l'elenco, a destra cosa cambia rispetto ad adesso. «Riprendi» la rimette nella scheda: si salva poi come
 * qualsiasi modifica, così nulla cambia sul sito per un clic sbagliato.
 */
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { t } from '@/i18n';
import Icona from './Icona.vue';
import { chiama } from './api.js';

const props = defineProps({
  id: { type: Number, required: true },
});
const emit = defineEmits(['chiudi', 'riprendi']);

const voci = ref([]);
const caricamento = ref(true);
const errore = ref('');
const scelta = ref(0);
const d = ref(null);
const caricaDettaglio = ref(false);
const erroreDettaglio = ref('');
const chiudiBtn = ref(null);

const uguale = computed(() => !!d.value && !d.value.cambia.length && !d.value.differenze);

async function apri(rid) {
  scelta.value = rid;
  caricaDettaglio.value = true;
  erroreDettaglio.value = '';
  try {
    d.value = await chiama('redazione/' + props.id + '/versioni/' + rid);
  } catch (e) {
    d.value = null;
    erroreDettaglio.value = e.message;
  } finally {
    caricaDettaglio.value = false;
  }
}

function tasti(e) {
  if (e.key === 'Escape') emit('chiudi');
}

onMounted(async () => {
  window.addEventListener('keydown', tasti);
  document.documentElement.classList.add('orz-modale-aperta');
  nextTick(() => chiudiBtn.value && chiudiBtn.value.focus());
  try {
    voci.value = await chiama('redazione/' + props.id + '/versioni');
    // Si parte dalla versione prima di adesso: è quella che di solito si cerca.
    const prima = voci.value.find((v) => !v.adesso);
    if (prima) apri(prima.id);
  } catch (e) {
    errore.value = e.message;
  } finally {
    caricamento.value = false;
  }
});
onBeforeUnmount(() => {
  window.removeEventListener('keydown', tasti);
  document.documentElement.classList.remove('orz-modale-aperta');
});
</script>
