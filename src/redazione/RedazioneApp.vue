<template>
  <div class="orz">
    <header class="orz-testa">
      <h2 class="orz-nome"><Icona nome="documento" :dim="18" /> {{ t('Redazione') }}</h2>
      <nav v-if="tipi.length > 1" class="orz-tipi" :aria-label="t('Tipi di contenuto')">
        <button
          v-for="x in tipi"
          :key="x.chiave"
          type="button"
          class="orz-pillola"
          :class="{ on: tipo && tipo.chiave === x.chiave }"
          :aria-current="tipo && tipo.chiave === x.chiave ? 'page' : undefined"
          @click="vai(x.chiave)"
        >{{ x.nome }}</button>
      </nav>
      <span class="orz-spazio"></span>
      <button v-if="tipo && tipo.crea && vista === 'elenco'" type="button" class="orz-btn orz-btn--primario" @click="vai(tipo.chiave, 'nuovo')">
        <Icona nome="piu" /> {{ t('Aggiungi') }}: {{ tipo.singolare }}
      </button>
    </header>

    <p v-if="!tipo" class="orz-avviso orz-avviso--errore">{{ t('Non ci sono contenuti che puoi scrivere.') }}</p>
    <template v-else>
      <Elenco
        v-show="vista === 'elenco'"
        ref="elenco"
        :tipo="tipo"
        @apri="(id) => vai(tipo.chiave, id)"
        @nuovo="vai(tipo.chiave, 'nuovo')"
      />
      <Scheda
        v-if="vista === 'scheda'"
        ref="scheda"
        :key="tipo.chiave"
        :tipo="tipo"
        :id="idAperto"
        @chiudi="chiudi"
        @salvato="salvato"
      />
    </template>
  </div>
</template>

<script setup>
/**
 * La Redazione (Olobuild_Redazione): i tipi in alto, poi l'elenco o la scheda. L'indirizzo della pagina dice dove si è
 * (#notizia, #notizia/123, #notizia/nuovo): si ricarica, si torna indietro e si condivide un link restando lì.
 */
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import Icona from './Icona.vue';
import Elenco from './Elenco.vue';
import Scheda from './Scheda.vue';

const tipi = ((window.oloData && window.oloData.redazione && window.oloData.redazione.tipi) || []);
// Prima i tipi del sito (Notizie, Comunicati…), poi gli Articoli di WordPress.
tipi.sort((a, b) => (a.chiave === 'post') - (b.chiave === 'post'));

const chiaveTipo = ref(tipi.length ? tipi[0].chiave : '');
const vista = ref('elenco');
const idAperto = ref(0);
const elenco = ref(null);
const scheda = ref(null);

const tipo = computed(() => tipi.find((x) => x.chiave === chiaveTipo.value) || null);

// L'indirizzo dell'ultima vista mostrata: l'Indietro del browser ci torna se si resta sulla scheda.
let ultimoHash = window.location.hash;

function leggiIndirizzo() {
  const [k, quale] = window.location.hash.replace(/^#/, '').split('/');
  if (k && tipi.some((x) => x.chiave === k)) chiaveTipo.value = k;
  if (quale === 'nuovo') {
    vista.value = 'scheda';
    idAperto.value = 0;
  } else if (/^\d+$/.test(quale || '')) {
    vista.value = 'scheda';
    idAperto.value = parseInt(quale, 10);
  } else {
    vista.value = 'elenco';
    idAperto.value = 0;
  }
  ultimoHash = window.location.hash;
}

function scriviIndirizzo(k, quale, sostituisci = false) {
  const h = '#' + k + (quale ? '/' + quale : '');
  if (window.location.hash === h) return;
  if (sostituisci) window.history.replaceState(null, '', h);
  else window.history.pushState(null, '', h);
}

function puoiLasciare() {
  return !(scheda.value && scheda.value.modificata) || window.confirm(t('Le modifiche non salvate andranno perse. Uscire lo stesso?'));
}

function vai(k, quale = '') {
  if (vista.value === 'scheda' && !puoiLasciare()) return;
  scriviIndirizzo(k, quale);
  leggiIndirizzo();
}

function chiudi(opzioni = {}) {
  vai(chiaveTipo.value);
  if (opzioni.ricarica && elenco.value) elenco.value.ricarica();
}

function salvato({ id, nuovo }) {
  if (nuovo) {
    scriviIndirizzo(chiaveTipo.value, id, true);
    idAperto.value = id;
    ultimoHash = window.location.hash;
  }
  if (elenco.value) elenco.value.ricarica();
}

function indietro() {
  // Indietro del browser con modifiche non salvate: si resta, rimettendo l'indirizzo di prima.
  if (vista.value === 'scheda' && !puoiLasciare()) {
    window.history.pushState(null, '', ultimoHash);
    return;
  }
  leggiIndirizzo();
}

onMounted(() => {
  leggiIndirizzo();
  if (!window.location.hash && tipo.value) {
    scriviIndirizzo(tipo.value.chiave, '', true);
    ultimoHash = window.location.hash;
  }
  window.addEventListener('popstate', indietro);
});
onBeforeUnmount(() => window.removeEventListener('popstate', indietro));
</script>
