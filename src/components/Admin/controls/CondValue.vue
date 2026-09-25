<template>
  <template v-if="scelta">
    <CfgSelect
      :model-value="mostrato"
      :options="voci"
      :placeholder="scelta.placeholder ? t(scelta.placeholder) : '—'"
      :remote-search="cerca"
      @update:model-value="scegli"
    />
    <p v-if="avviso" class="cond-hint">{{ avviso }}</p>
  </template>
  <div v-else-if="libero" class="cfg-input">
    <input type="text" :value="modelValue" :placeholder="t('Valore')" :aria-label="t('Valore')" @input="emit('update:modelValue', $event.target.value)" />
  </div>
</template>

<script setup>
// Il controllo del «Valore» di una condizione (Assegnazione template), secondo il tipo:
// - nessuno per i tipi che non hanno un valore (Tutto il sito, Homepage, 404…);
// - un menu con ricerca per pagine, articoli, categorie, tag, tipi, archivi e ruoli;
// - un campo di testo per i tipi che il menu non offre ma il server valuta (salvati
//   via API o importati: taxonomy, author, date_before…), per non nasconderne il dato.
// Emette sempre stringhe, come le salva il server. Nessuna etichetta propria: le
// etichette della scheda restano nel Tab (indice della ricerca).
import { computed, watch } from 'vue';
import { t } from '@/i18n';
import CfgSelect from './CfgSelect.vue';
import {
  SCELTE, TIPI_SENZA_VALORE, isNumerico, valoreMostrato,
  cercaScelte, risolviValore, etichettaValore, esitoValore,
} from '../composables/useConditionChoices';

const props = defineProps({
  type: { type: String, default: '' },
  modelValue: { type: [String, Number], default: '' },
});
const emit = defineEmits(['update:modelValue']);

const scelta = computed(() => SCELTE[props.type] || null);
const libero = computed(() => !scelta.value && !TIPI_SENZA_VALORE.has(props.type));
const mostrato = computed(() => valoreMostrato(props.type, props.modelValue));

const voci = computed(() => {
  const s = scelta.value;
  if (!s) return [];
  const out = [];
  if (s.any) out.push({ value: '', label: t(s.any) });
  if (mostrato.value !== '') out.push({ value: mostrato.value, label: etichettaValore(props.type, mostrato.value) });
  return out;
});

function cerca(q) {
  return cercaScelte(props.type, q);
}

// Scegliere la voce già mostrata non cambia niente: 'all' resta 'all'.
function scegli(v) {
  const nuovo = String(v ?? '');
  if (nuovo === mostrato.value) return;
  emit('update:modelValue', nuovo);
}

// Numero in testa di un testo ('7-prova'): intval() ne teneva l'ID. Si chiede anche
// se quell'ID è davvero una pagina (articolo, categoria): l'avviso dice se vale come prima.
function idInTesta(v) {
  const s = scelta.value;
  if (!s || !s.perId || v === '' || isNumerico(v)) return 0;
  const id = parseInt(v, 10);
  return id > 0 ? id : 0;
}

watch([() => props.type, mostrato], () => {
  if (!scelta.value || mostrato.value === '') return;
  risolviValore(props.type, mostrato.value);
  const id = idInTesta(mostrato.value);
  if (id) risolviValore(props.type, String(id));
}, { immediate: true });

// Un testo scritto a mano dove il menu salva un ID (etichetta «(slug)» delle versioni
// precedenti): il server ora lo confronta alla lettera con slug e titolo (nome per le
// categorie). Un testo con una «/» dopo il primo carattere è anche un percorso, che
// WordPress normalizza (get_page_by_path: maiuscole, spazi e barra finale non contano).
// Prima intval() ne teneva solo il numero in testa, e senza numero ne faceva 0: la
// regola valeva per tutto (o per niente); con un numero negativo non valeva mai.
const ORA = {
  page: 'Valore scritto come testo: ora vale solo per le pagine con esattamente questo slug o titolo; prima valeva per tutte le pagine.',
  post: 'Valore scritto come testo: ora vale per gli articoli con esattamente questo slug o titolo; prima non valeva per nessun articolo.',
  category: 'Valore scritto come testo: ora vale solo per le categorie con esattamente questo slug o nome; prima valeva per ogni articolo con una categoria e ogni archivio di categoria.',
};
// Il server non ha trovato niente con quel testo: lo stesso esito del sito.
const NIENTE = {
  page: 'Valore scritto come testo che non è lo slug né il titolo esatto di nessuna pagina: ora non vale su nessuna pagina; prima valeva per tutte.',
  post: 'Valore scritto come testo che non è lo slug né il titolo esatto di nessun articolo: non vale su nessun articolo, come prima.',
  category: 'Valore scritto come testo che non è lo slug né il nome esatto di nessuna categoria: ora non vale per nessuna; prima valeva per ogni articolo con una categoria e ogni archivio di categoria.',
};
// Un percorso ('chi-siamo/team', 'Chi-Siamo/'): la pagina a quell'indirizzo.
const PERCORSO = {
  page: 'Valore scritto come percorso: ora vale per la pagina a questo indirizzo (maiuscole, spazi e barra finale non contano) o con esattamente questo titolo; prima valeva per tutte le pagine.',
  post: 'Valore scritto come percorso: ora vale per l\'articolo con questo slug (maiuscole, spazi e barra finale non contano) o con esattamente questo titolo; prima non valeva per nessun articolo.',
};
// Con un numero negativo in testa ('-5-prova') intval() dava un ID che non esiste:
// prima non valeva mai. Per post è già così (vedi ORA e NIENTE).
const ORA_MAI = {
  page: 'Valore scritto come testo: ora vale solo per le pagine con esattamente questo slug o titolo; prima non valeva per nessuna pagina.',
  category: 'Valore scritto come testo: ora vale solo per le categorie con esattamente questo slug o nome; prima non valeva per nessuna.',
};
const NIENTE_MAI = {
  page: 'Valore scritto come testo che non è lo slug né il titolo esatto di nessuna pagina: non vale su nessuna pagina, come prima.',
  category: 'Valore scritto come testo che non è lo slug né il nome esatto di nessuna categoria: non vale per nessuna, come prima.',
};
// Con un numero in testa ('5-stelle') che è l'ID di una pagina: quell'ID vale ancora, e in più ciò che ha il testo.
const ANCHE = {
  page: 'Valore scritto come testo: vale ancora per la pagina con ID {id}, come prima, e ora anche per quelle con esattamente questo slug o titolo.',
  post: 'Valore scritto come testo: vale ancora per l\'articolo con ID {id}, come prima, e ora anche per quelli con esattamente questo slug o titolo.',
  category: 'Valore scritto come testo: vale ancora per la categoria con ID {id}, come prima, e ora anche per quelle con esattamente questo slug o nome.',
};
// Numero in testa che non è l'ID di una pagina ('7-prova', 7 = un articolo): prima non valeva mai.
const SENZA_ID = {
  page: 'Valore scritto come testo: nessuna pagina ha ID {id}, quindi prima non valeva su nessuna pagina; ora vale per quelle con esattamente questo slug o titolo.',
  post: 'Valore scritto come testo: nessun articolo ha ID {id}, quindi prima non valeva su nessun articolo; ora vale per quelli con esattamente questo slug o titolo.',
  category: 'Valore scritto come testo: nessuna categoria ha ID {id}, quindi prima non valeva per nessuna; ora vale per quelle con esattamente questo slug o nome.',
};
const NESSUN_ID = {
  page: 'Valore scritto come testo: nessuna pagina ha ID {id} né esattamente questo slug o titolo, quindi non vale su nessuna pagina, come prima.',
  post: 'Valore scritto come testo: nessun articolo ha ID {id} né esattamente questo slug o titolo, quindi non vale su nessun articolo, come prima.',
  category: 'Valore scritto come testo: nessuna categoria ha ID {id} né esattamente questo slug o nome, quindi non vale per nessuna, come prima.',
};
// Il testo dipende anche da ciò che il server ha trovato (esitoValore): null = niente.
const avviso = computed(() => {
  const s = scelta.value;
  const v = mostrato.value;
  const tipo = props.type;
  if (!s || !s.perId || v === '' || isNumerico(v)) return '';
  const esito = esitoValore(tipo, v);
  const id = idInTesta(v);
  if (id) {
    const conId = (testi) => t(testi[tipo]).replace('{id}', String(id));
    if (esito === null) return conId(NESSUN_ID);
    // L'ID in testa non è di questo tipo: vale solo il testo.
    if (esitoValore(tipo, String(id)) === null) return conId(SENZA_ID);
    return conId(ANCHE);
  }
  if (parseInt(v, 10) < 0) {
    return esito === null ? t(NIENTE_MAI[tipo] || NIENTE[tipo]) : t(ORA_MAI[tipo] || ORA[tipo]);
  }
  if (esito === null) return t(NIENTE[tipo]);
  if (PERCORSO[tipo] && v.indexOf('/') > 0) return t(PERCORSO[tipo]);
  return t(ORA[tipo]);
});
</script>

<style scoped>
.cond-hint {
  margin: 4px 0 0;
  font-size: 12px;
  line-height: 1.45;
  color: var(--c-text-mute);
}
</style>
