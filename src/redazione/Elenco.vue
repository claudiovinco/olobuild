<template>
  <section class="orz-elenco" :aria-label="tipo.nome">
    <div class="orz-filtri">
      <div class="orz-stati" role="group" :aria-label="t('Stato')">
        <button
          v-for="s in statiVisibili"
          :key="s.chiave"
          type="button"
          class="orz-pillola"
          :class="{ on: stato === s.chiave }"
          :aria-pressed="stato === s.chiave"
          @click="cambiaStato(s.chiave)"
        >
          {{ s.nome }} <span class="orz-conta">{{ s.conta }}</span>
        </button>
      </div>
      <div class="orz-cerca-riga">
        <label class="orz-cerca">
          <Icona nome="cerca" />
          <input v-model="cerca" type="search" :placeholder="t('Cerca nei titoli e nei testi')" :aria-label="t('Cerca')" />
        </label>
        <select v-if="principale" v-model.number="termine" :aria-label="principale.etichetta">
          <option :value="0">{{ t('Tutte') }}: {{ principale.etichetta }}</option>
          <option v-for="x in vociPrincipale" :key="x.id" :value="x.id">{{ '— '.repeat(x.livello) + x.nome }}</option>
        </select>
      </div>
    </div>

    <p v-if="errore" class="orz-avviso orz-avviso--errore" role="alert">{{ errore }}</p>

    <ul v-if="voci.length" class="orz-righe">
      <li v-for="v in voci" :key="v.id" class="orz-riga">
        <button type="button" class="orz-riga-apri" @click="emit('apri', v.id)">
          <span class="orz-riga-mini">
            <img v-if="v.miniatura" :src="v.miniatura" alt="" loading="lazy" />
            <Icona v-else nome="documento" :dim="18" />
          </span>
          <span class="orz-riga-testo">
            <span class="orz-riga-titolo">{{ v.titolo || t('(senza titolo)') }}</span>
            <span class="orz-riga-meta">
              <span v-if="v.termini.length">{{ v.termini.join(', ') }}</span>
              <span v-if="v.autore">{{ v.autore }}</span>
              <span>{{ quando(v) }}</span>
              <span v-if="v.bloccato" class="orz-riga-blocco"><Icona nome="lucchetto" :dim="12" /> {{ t('%s sta scrivendo').replace('%s', v.bloccato) }}</span>
            </span>
          </span>
          <span class="orz-stato" :class="'orz-stato--' + v.stato">{{ nomeStato(v.stato) }}</span>
        </button>
        <a v-if="v.link" class="orz-btn-icona" :href="v.link" target="_blank" rel="noopener" :title="t('Vedi la pagina')" :aria-label="t('Vedi la pagina') + ': ' + (v.titolo || t('(senza titolo)'))">
          <Icona nome="esterno" />
        </a>
        <span v-else class="orz-btn-icona orz-btn-icona--vuoto" aria-hidden="true"></span>
      </li>
    </ul>

    <div v-else-if="!caricamento && !errore" class="orz-vuoto">
      <p class="orz-vuoto-titolo">{{ cerca || termine || stato ? t('Nessun contenuto con questi filtri') : t('Ancora niente qui.') }}</p>
      <button v-if="tipo.crea && !cerca && !termine && !stato" type="button" class="orz-btn orz-btn--primario" @click="emit('nuovo')">
        <Icona nome="piu" /> {{ t('Aggiungi') }}: {{ tipo.singolare }}
      </button>
    </div>

    <p v-if="caricamento" class="orz-nota orz-centro" aria-live="polite">{{ t('Caricamento…') }}</p>
    <div v-else-if="pagina < pagine" class="orz-centro">
      <button type="button" class="orz-btn" @click="carica(pagina + 1)">{{ t('Mostra altri') }}</button>
    </div>
  </section>
</template>

<script setup>
// L'elenco di un tipo: stati col numero, ricerca, categoria; dal più recente modificato. Un clic apre la scheda.
import { ref, computed, watch, onMounted } from 'vue';
import { t } from '@/i18n';
import Icona from './Icona.vue';
import { chiama, vociDi } from './api.js';
import { nomeStato } from './stati.js';

const props = defineProps({
  tipo: { type: Object, required: true },
});
const emit = defineEmits(['apri', 'nuovo']);

const stato = ref('');
const cerca = ref('');
const termine = ref(0);
const voci = ref([]);
const pagina = ref(1);
const pagine = ref(1);
const conteggi = ref({});
const caricamento = ref(false);
const errore = ref('');

const principale = computed(() => props.tipo.tassonomie.find((x) => x.gerarchica) || null);
const vociPrincipale = ref([]);

const ORDINE = ['publish', 'future', 'draft', 'pending', 'private'];
const statiVisibili = computed(() => {
  const c = conteggi.value;
  const totale = ORDINE.reduce((s, k) => s + (c[k] || 0), 0);
  return [{ chiave: '', nome: t('Tutti'), conta: totale }].concat(
    ORDINE.filter((k) => c[k] || stato.value === k).map((k) => ({ chiave: k, nome: nomeStato(k, true), conta: c[k] || 0 })),
  );
});

function quando(v) {
  if (v.stato === 'future') return t('Esce il %s').replace('%s', v.data);
  if (v.stato === 'publish' || v.stato === 'private') return v.data;
  return t('Modificato il %s').replace('%s', v.modificato);
}

let richiesta = 0;
async function carica(p = 1) {
  const mia = ++richiesta;
  caricamento.value = true;
  errore.value = '';
  try {
    const r = await chiama('redazione', {
      parametri: {
        tipo: props.tipo.chiave,
        stato: stato.value,
        cerca: cerca.value.trim(),
        tassonomia: principale.value && termine.value ? principale.value.nome : '',
        termine: termine.value || '',
        pagina: p,
      },
    });
    if (mia !== richiesta) return;
    voci.value = p === 1 ? r.voci : voci.value.concat(r.voci);
    pagina.value = p;
    pagine.value = r.pagine;
    conteggi.value = r.conteggi;
  } catch (e) {
    if (mia === richiesta) errore.value = e.message;
  } finally {
    if (mia === richiesta) caricamento.value = false;
  }
}

function cambiaStato(s) {
  stato.value = s;
  carica(1);
}

let attesa = null;
watch(cerca, () => {
  clearTimeout(attesa);
  attesa = setTimeout(() => carica(1), 300);
});
watch(termine, () => carica(1));
watch(() => props.tipo.chiave, () => {
  stato.value = '';
  cerca.value = '';
  termine.value = 0;
  caricaPrincipale();
  carica(1);
});

async function caricaPrincipale() {
  vociPrincipale.value = [];
  if (!principale.value) return;
  const lista = await vociDi(principale.value.nome);
  const figli = {};
  lista.forEach((x) => {
    (figli[x.genitore] = figli[x.genitore] || []).push(x);
  });
  const out = [];
  const giu = (g, l) => (figli[g] || []).forEach((x) => {
    out.push({ ...x, livello: l });
    giu(x.id, l + 1);
  });
  giu(0, 0);
  vociPrincipale.value = out;
}

onMounted(() => {
  caricaPrincipale();
  carica(1);
});

defineExpose({ ricarica: () => carica(1) });
</script>
