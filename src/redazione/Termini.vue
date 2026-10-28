<template>
  <div class="orz-termini">
    <div class="orz-etichetta"><span>{{ tassonomia.etichetta }}</span></div>

    <template v-if="tassonomia.gerarchica">
      <input
        v-if="tutti.length > 10"
        v-model="filtro"
        type="search"
        class="orz-termini-cerca"
        :placeholder="t('Cerca')"
        :aria-label="t('Cerca') + ': ' + tassonomia.etichetta"
      />
      <ul class="orz-albero" :aria-label="tassonomia.etichetta">
        <li v-for="x in albero" :key="x.id" :style="{ paddingLeft: (x.livello * 16) + 'px' }">
          <label class="orz-spunta">
            <input type="checkbox" :checked="scelti.includes(x.id)" :disabled="spento || !tassonomia.assegna" @change="alterna(x.id)" />
            <span>{{ x.nome }}</span>
          </label>
        </li>
        <li v-if="caricato && !albero.length" class="orz-nota">{{ filtro ? t('Nessuna corrispondenza') : t('Ancora nessuna voce') }}</li>
      </ul>
      <div v-if="tassonomia.crea && !spento" class="orz-nuovo-termine">
        <button v-if="!aggiungi" type="button" class="orz-link" @click="aggiungi = true">
          <Icona nome="piu" :dim="14" /> {{ t('Aggiungi') }}: {{ tassonomia.singolare }}
        </button>
        <form v-else class="orz-nuovo-form" @submit.prevent="crea">
          <input v-model="nuovoNome" type="text" :placeholder="t('Nome')" :aria-label="t('Nome')" />
          <select v-if="tutti.length" v-model.number="nuovoGenitore" :aria-label="t('Dentro')">
            <option :value="0">{{ t('In cima') }}</option>
            <option v-for="x in alberoTutto" :key="x.id" :value="x.id">{{ '— '.repeat(x.livello) + x.nome }}</option>
          </select>
          <span class="orz-nuovo-azioni">
            <button type="submit" class="orz-btn orz-btn--piccolo" :disabled="!nuovoNome.trim() || occupato">{{ t('Aggiungi') }}</button>
            <button type="button" class="orz-btn orz-btn--piccolo orz-btn--quieto" @click="aggiungi = false">{{ t('Annulla') }}</button>
          </span>
          <span v-if="errore" class="orz-errore-riga" role="alert">{{ errore }}</span>
        </form>
      </div>
    </template>

    <template v-else>
      <ul v-if="scelti.length" class="orz-chip">
        <li v-for="n in scelti" :key="n">
          <span>{{ n }}</span>
          <button type="button" :aria-label="t('Togli') + ': ' + n" :disabled="spento || !tassonomia.assegna" @click="togliNome(n)"><Icona nome="x" :dim="12" /></button>
        </li>
      </ul>
      <input
        v-if="tassonomia.assegna"
        v-model="nuovoTag"
        type="text"
        :list="'orz-dl-' + tassonomia.nome"
        :placeholder="t('Scrivi e premi Invio')"
        :aria-label="t('Aggiungi') + ': ' + tassonomia.singolare"
        :disabled="spento"
        @keydown.enter.prevent="aggiungiTag"
        @keydown="virgola"
        @change="sceltoDaElenco"
      />
      <datalist :id="'orz-dl-' + tassonomia.nome">
        <option v-for="x in tutti" :key="x.id" :value="x.nome"></option>
      </datalist>
    </template>
  </div>
</template>

<script setup>
/**
 * Categorie (a cartelle: caselle, con la ricerca quando sono tante e «Aggiungi» per chi può) e tag (parole: si scrivono e
 * si creano salvando, come nella scheda di WordPress). Il valore: ID per le categorie, nomi per i tag.
 */
import { ref, computed, onMounted } from 'vue';
import { t } from '@/i18n';
import Icona from './Icona.vue';
import { chiama, vociDi, aggiungiVoce } from './api.js';

const props = defineProps({
  tassonomia: { type: Object, required: true },
  modelValue: { type: Array, default: () => [] },
  spento: { type: Boolean, default: false },
});
const emit = defineEmits(['update:modelValue']);

const tutti = ref([]);
const caricato = ref(false);
const filtro = ref('');
const scelti = computed(() => props.modelValue || []);

onMounted(async () => {
  tutti.value = await vociDi(props.tassonomia.nome);
  caricato.value = true;
});

function ordina(lista) {
  const figli = {};
  lista.forEach((x) => {
    (figli[x.genitore] = figli[x.genitore] || []).push(x);
  });
  const presenti = new Set(lista.map((x) => x.id));
  const out = [];
  const giu = (genitore, livello) => {
    (figli[genitore] || []).forEach((x) => {
      out.push({ ...x, livello });
      giu(x.id, livello + 1);
    });
  };
  giu(0, 0);
  // Figlie di una madre che non c'è (filtrata o fuori dalle prime mille): in cima.
  lista.forEach((x) => {
    if (x.genitore && !presenti.has(x.genitore)) {
      out.push({ ...x, livello: 0 });
      giu(x.id, 1);
    }
  });
  return out;
}

const alberoTutto = computed(() => ordina(tutti.value));
const albero = computed(() => {
  const f = filtro.value.trim().toLowerCase();
  if (!f) return alberoTutto.value;
  return alberoTutto.value.filter((x) => x.nome.toLowerCase().includes(f) || scelti.value.includes(x.id)).map((x) => ({ ...x, livello: 0 }));
});

function alterna(id) {
  const lista = scelti.value.slice();
  const i = lista.indexOf(id);
  if (i >= 0) lista.splice(i, 1);
  else lista.push(id);
  emit('update:modelValue', lista);
}

const aggiungi = ref(false);
const nuovoNome = ref('');
const nuovoGenitore = ref(0);
const occupato = ref(false);
const errore = ref('');
async function crea() {
  occupato.value = true;
  errore.value = '';
  try {
    const x = await chiama('redazione/termini', {
      metodo: 'POST',
      corpo: { tassonomia: props.tassonomia.nome, nome: nuovoNome.value.trim(), genitore: nuovoGenitore.value },
    });
    tutti.value = await aggiungiVoce(props.tassonomia.nome, x);
    if (!scelti.value.includes(x.id)) emit('update:modelValue', [...scelti.value, x.id]);
    nuovoNome.value = '';
    aggiungi.value = false;
  } catch (e) {
    errore.value = e.message;
  } finally {
    occupato.value = false;
  }
}

const nuovoTag = ref('');
function aggiungiTag() {
  const nomi = nuovoTag.value.split(',').map((n) => n.trim()).filter(Boolean);
  if (!nomi.length) return;
  const lista = scelti.value.slice();
  nomi.forEach((n) => {
    if (!lista.some((x) => x.toLowerCase() === n.toLowerCase())) lista.push(n);
  });
  emit('update:modelValue', lista);
  nuovoTag.value = '';
}
function virgola(e) {
  if (e.key === ',') {
    e.preventDefault();
    aggiungiTag();
  }
}
function sceltoDaElenco() {
  if (tutti.value.some((x) => x.nome === nuovoTag.value)) aggiungiTag();
}
function togliNome(n) {
  emit('update:modelValue', scelti.value.filter((x) => x !== n));
}
</script>
