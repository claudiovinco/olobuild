<template>
  <section class="orz-elenco orz-categorie" :aria-label="t('Categorie') + ': ' + tipo.nome">
    <div class="orz-filtri">
      <div class="orz-categorie-testa">
        <div v-if="tassonomie.length > 1" class="orz-stati" role="group" :aria-label="t('Categorie')">
          <button
            v-for="x in tassonomie"
            :key="x.nome"
            type="button"
            class="orz-pillola"
            :class="{ on: tassonomia && tassonomia.nome === x.nome }"
            :aria-pressed="tassonomia && tassonomia.nome === x.nome"
            @click="scegli(x.nome)"
          >{{ x.etichetta }}</button>
        </div>
        <h3 v-else-if="tassonomia" class="orz-sezione">{{ tassonomia.etichetta }}</h3>
        <span class="orz-spazio"></span>
        <button v-if="tassonomia && tassonomia.crea && !aperta" type="button" class="orz-btn orz-btn--primario" @click="apri(null)">
          <Icona nome="piu" /> {{ t('Aggiungi') }}: {{ tassonomia.singolare }}
        </button>
      </div>
      <input
        v-if="voci.length > 12"
        v-model="filtro"
        type="search"
        class="orz-termini-cerca"
        :placeholder="t('Cerca')"
        :aria-label="t('Cerca') + ': ' + (tassonomia ? tassonomia.etichetta : '')"
      />
    </div>

    <p v-if="errore" class="orz-avviso orz-avviso--errore" role="alert">{{ errore }}</p>
    <p v-if="fatto" class="orz-avviso orz-avviso--ok" role="status">{{ fatto }}</p>

    <form v-if="aperta && aperta.id === 0" class="orz-categoria-modulo" @submit.prevent="salva">
      <ModuloCategoria v-model="modulo" :tassonomia="tassonomia" :madri="madriPer(0)" :lingue="lingue" />
      <span class="orz-nuovo-azioni">
        <button type="submit" class="orz-btn orz-btn--primario" :disabled="!modulo.nome.trim() || occupato">{{ t('Aggiungi') }}</button>
        <button type="button" class="orz-btn orz-btn--quieto" @click="chiudi">{{ t('Annulla') }}</button>
      </span>
    </form>

    <ul v-if="albero.length" class="orz-righe">
      <li v-for="x in albero" :key="x.id" class="orz-riga orz-categoria" :class="{ 'orz-categoria--aperta': aperta && aperta.id === x.id }">
        <form v-if="aperta && aperta.id === x.id" class="orz-categoria-modulo" @submit.prevent="salva">
          <ModuloCategoria v-model="modulo" :tassonomia="tassonomia" :madri="madriPer(x.id)" :lingue="lingue" />
          <span class="orz-nuovo-azioni">
            <button type="submit" class="orz-btn orz-btn--primario" :disabled="!modulo.nome.trim() || occupato">{{ t('Salva') }}</button>
            <button type="button" class="orz-btn orz-btn--quieto" @click="chiudi">{{ t('Annulla') }}</button>
            <span class="orz-spazio"></span>
            <button
              v-if="tassonomia.predefinita && !x.predefinita"
              type="button"
              class="orz-btn orz-btn--quieto"
              :disabled="occupato"
              @click="rendiPredefinita(x)"
            >{{ t('Rendi predefinita') }}</button>
            <button
              v-if="tassonomia.elimina && !x.predefinita"
              type="button"
              class="orz-btn orz-btn--quieto orz-btn--rischio"
              :disabled="occupato"
              @click="elimina(x)"
            ><Icona nome="cestino" :dim="14" /> {{ t('Elimina') }}</button>
          </span>
        </form>
        <template v-else>
          <button type="button" class="orz-riga-apri" :style="{ paddingLeft: (16 + x.livello * 22) + 'px' }" :disabled="!tassonomia.crea" @click="apri(x)">
            <span class="orz-riga-testo">
              <span class="orz-riga-titolo">{{ x.nome }}</span>
              <span class="orz-riga-meta">
                <span>{{ x.conta === 1 ? t('1 contenuto') : t('%d contenuti').replace('%d', x.conta) }}</span>
                <span v-if="x.predefinita">{{ t('predefinita') }}</span>
              </span>
            </span>
            <span v-if="lingue.length" class="orz-riga-lingue">
              <span
                v-for="l in lingue"
                :key="l.codice"
                class="orz-riga-lingua"
                :class="{ 'orz-riga-lingua--publish': nomeIn(x, l.codice) }"
                :title="l.nome + ': ' + (nomeIn(x, l.codice) || t('da tradurre'))"
              ><span aria-hidden="true">{{ l.codice.toUpperCase() }}</span><span class="screen-reader-text">{{ l.nome + ': ' + (nomeIn(x, l.codice) || t('da tradurre')) }}</span></span>
            </span>
            <span v-if="tassonomia.crea" class="orz-btn orz-btn--piccolo orz-btn--quieto" aria-hidden="true">{{ t('Modifica') }}</span>
          </button>
        </template>
      </li>
    </ul>
    <div v-else-if="caricato" class="orz-vuoto">
      <p class="orz-vuoto-titolo">{{ filtro ? t('Nessuna corrispondenza') : t('Ancora nessuna voce') }}</p>
    </div>
  </section>
</template>

<script setup>
/**
 * Le categorie (e i tag) di un tipo, nella Redazione: chi aggiorna il sito le aggiunge, rinomina, sposta ed elimina
 * senza passare dai menu di WordPress, e scrive il loro nome nelle altre lingue del sito (OLOlang, salvato sulla voce).
 * Le voci arrivano dalla stessa rotta delle schede (redazione/termini) e la cache delle schede si aggiorna a ogni
 * modifica, così una scheda aperta dopo vede i nomi nuovi.
 */
import { ref, computed, watch, onMounted } from 'vue';
import { t } from '@/i18n';
import Icona from './Icona.vue';
import ModuloCategoria from './ModuloCategoria.vue';
import { chiama, impostaVoci } from './api.js';

const props = defineProps({
  tipo: { type: Object, required: true },
});

const datiRedazione = (window.oloData && window.oloData.redazione) || {};
const lingue = datiRedazione.nomiLingue && datiRedazione.lingue ? datiRedazione.lingue.altre || [] : [];

const tassonomie = computed(() => (props.tipo.tassonomie || []).filter((x) => x.crea || x.elimina));
const nomeTassonomia = ref('');
const tassonomia = computed(() => tassonomie.value.find((x) => x.nome === nomeTassonomia.value) || tassonomie.value[0] || null);

const voci = ref([]);
const caricato = ref(false);
const errore = ref('');
const fatto = ref('');
const filtro = ref('');
const occupato = ref(false);
const aperta = ref(null);
const modulo = ref({ nome: '', genitore: 0, nomi: {} });

async function carica() {
  if (!tassonomia.value) return;
  caricato.value = false;
  errore.value = '';
  try {
    voci.value = await chiama('redazione/termini', { parametri: { tassonomia: tassonomia.value.nome } });
    impostaVoci(tassonomia.value.nome, voci.value);
  } catch (e) {
    errore.value = e.message;
  } finally {
    caricato.value = true;
  }
}

function scegli(nome) {
  if (aperta.value && !window.confirm(t('Le modifiche non salvate andranno perse. Uscire lo stesso?'))) return;
  chiudi();
  nomeTassonomia.value = nome;
}

watch(() => tassonomia.value && tassonomia.value.nome, carica);
onMounted(carica);

function ordina(lista) {
  const figli = {};
  lista.forEach((x) => {
    (figli[x.genitore] = figli[x.genitore] || []).push(x);
  });
  Object.values(figli).forEach((l) => l.sort((a, b) => a.nome.localeCompare(b.nome)));
  const presenti = new Set(lista.map((x) => x.id));
  const out = [];
  const giu = (genitore, livello) => {
    (figli[genitore] || []).forEach((x) => {
      out.push({ ...x, livello });
      giu(x.id, livello + 1);
    });
  };
  giu(0, 0);
  lista.forEach((x) => {
    if (x.genitore && !presenti.has(x.genitore)) {
      out.push({ ...x, livello: 0 });
      giu(x.id, 1);
    }
  });
  return out;
}

const alberoTutto = computed(() => ordina(voci.value));
const albero = computed(() => {
  const f = filtro.value.trim().toLowerCase();
  if (!f) return alberoTutto.value;
  return alberoTutto.value
    .filter((x) => x.nome.toLowerCase().includes(f) || Object.values(x.nomi || {}).some((n) => n.toLowerCase().includes(f)))
    .map((x) => ({ ...x, livello: 0 }));
});

/** Dove si può mettere una voce: tutte tranne lei e le sue figlie (una voce non sta dentro sé stessa). */
function madriPer(id) {
  if (!tassonomia.value || !tassonomia.value.gerarchica) return [];
  const escluse = new Set();
  if (id) {
    escluse.add(id);
    let cambiato = true;
    while (cambiato) {
      cambiato = false;
      voci.value.forEach((x) => {
        if (escluse.has(x.genitore) && !escluse.has(x.id)) {
          escluse.add(x.id);
          cambiato = true;
        }
      });
    }
  }
  return alberoTutto.value.filter((x) => !escluse.has(x.id));
}

function nomeIn(x, codice) {
  return (x.nomi && x.nomi[codice]) || '';
}

function apri(x) {
  fatto.value = '';
  errore.value = '';
  aperta.value = x ? { id: x.id } : { id: 0 };
  modulo.value = x
    ? { nome: x.nome, genitore: x.genitore, nomi: { ...(x.nomi || {}) } }
    : { nome: '', genitore: 0, nomi: {} };
}

function chiudi() {
  aperta.value = null;
}

async function salva() {
  occupato.value = true;
  errore.value = '';
  fatto.value = '';
  const corpo = {
    tassonomia: tassonomia.value.nome,
    nome: modulo.value.nome.trim(),
    genitore: modulo.value.genitore || 0,
    nomi: modulo.value.nomi,
  };
  try {
    const id = aperta.value.id;
    const x = await chiama(id ? 'redazione/termini/' + id : 'redazione/termini', { metodo: 'POST', corpo });
    fatto.value = id ? t('Salvato') : t('Aggiunta: %s').replace('%s', x.nome);
    chiudi();
    await carica();
  } catch (e) {
    errore.value = e.message;
  } finally {
    occupato.value = false;
  }
}

async function elimina(x) {
  const domanda = x.conta
    ? t('Eliminare «%1$s»? I %2$d contenuti restano, senza questa voce.').replace('%1$s', x.nome).replace('%2$d', x.conta)
    : t('Eliminare «%s»?').replace('%s', x.nome);
  if (!window.confirm(domanda)) return;
  occupato.value = true;
  errore.value = '';
  try {
    await chiama('redazione/termini/' + x.id, { metodo: 'DELETE', parametri: { tassonomia: tassonomia.value.nome } });
    fatto.value = t('Eliminata: %s').replace('%s', x.nome);
    chiudi();
    await carica();
  } catch (e) {
    errore.value = e.message;
  } finally {
    occupato.value = false;
  }
}

async function rendiPredefinita(x) {
  occupato.value = true;
  errore.value = '';
  try {
    await chiama('redazione/termini/' + x.id + '/predefinita', { metodo: 'POST', corpo: { tassonomia: tassonomia.value.nome } });
    fatto.value = t('«%s» è la categoria predefinita').replace('%s', x.nome);
    chiudi();
    await carica();
  } catch (e) {
    errore.value = e.message;
  } finally {
    occupato.value = false;
  }
}

defineExpose({
  get modificata() {
    return !!aperta.value;
  },
});
</script>
