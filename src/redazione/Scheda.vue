<template>
  <section class="orz-scheda" :aria-label="f.titolo || t('(senza titolo)')">
    <header class="orz-scheda-testa">
      <button type="button" class="orz-btn orz-btn--quieto" @click="emit('chiudi')">
        <Icona nome="indietro" /> {{ tipo.nome }}
      </button>
      <span class="orz-scheda-stato">
        <span v-if="s" class="orz-stato" :class="'orz-stato--' + s.stato">{{ nomeStato(s.stato) }}</span>
        <span v-else class="orz-stato orz-stato--draft">{{ t('Nuovo') }}</span>
        <span class="orz-nota" aria-live="polite">{{ notaSalvataggio }}</span>
      </span>
      <span class="orz-scheda-azioni">
        <button
          v-if="secondaria"
          type="button"
          class="orz-btn"
          :disabled="occupato || spento || caricamento"
          @click="salva(secondaria.azione)"
        >{{ secondaria.etichetta }}</button>
        <button
          type="button"
          class="orz-btn orz-btn--primario"
          :disabled="occupato || spento || caricamento"
          @click="salva(primaria.azione)"
        >{{ occupato ? t('Salvataggio…') : primaria.etichetta }}</button>
      </span>
    </header>

    <div class="orz-messaggi">
      <p v-if="errore" class="orz-avviso orz-avviso--errore" role="alert">{{ errore }}</p>
      <p v-if="avviso" class="orz-avviso orz-avviso--ok" role="status">{{ avviso }}</p>
      <div v-if="conflitto" class="orz-avviso orz-avviso--attenzione" role="alert">
        <span>{{ conflitto.messaggio }}</span>
        <span class="orz-avviso-azioni">
          <button type="button" class="orz-btn orz-btn--piccolo" @click="salva(conflitto.azione, true)">{{ t('Salva comunque la mia versione') }}</button>
          <button type="button" class="orz-btn orz-btn--piccolo orz-btn--quieto" @click="ricarica">{{ t('Carica la loro versione') }}</button>
        </span>
      </div>
      <div v-if="spento && bloccatoDa" class="orz-avviso orz-avviso--attenzione" role="alert">
        <span><Icona nome="lucchetto" :dim="14" /> {{ t('%s sta modificando questo contenuto. Puoi leggerlo; per cambiarlo prendi il controllo.').replace('%s', bloccatoDa) }}</span>
        <span class="orz-avviso-azioni">
          <button type="button" class="orz-btn orz-btn--piccolo" @click="prendi">{{ t('Prendi il controllo') }}</button>
        </span>
      </div>
    </div>

    <p v-if="caricamento" class="orz-nota orz-centro">{{ t('Caricamento…') }}</p>
    <p v-else-if="erroreCarica" class="orz-avviso orz-avviso--errore" role="alert">{{ erroreCarica }}</p>

    <div v-else class="orz-scheda-corpo">
      <div class="orz-principale">
        <input
          v-model="f.titolo"
          class="orz-titolo"
          type="text"
          :placeholder="t('Titolo')"
          :aria-label="t('Titolo')"
          :disabled="spento"
        />

        <div v-if="tipo.estratto" class="orz-blocco">
          <div class="orz-etichetta">
            <label for="orz-riassunto">{{ t('Riassunto') }}</label>
            <InfoTip :titolo="t('Riassunto')" :testo="t('Due righe che vanno negli elenchi del sito, nei risultati di ricerca e, se non scrivi un testo apposta, nei social. Vuoto: le prime parole del testo.')" />
          </div>
          <textarea id="orz-riassunto" v-model="f.riassunto" rows="2" :disabled="spento"></textarea>
        </div>

        <div class="orz-blocco">
          <div class="orz-etichetta"><span>{{ t('Testo') }}</span></div>
          <div v-if="s && s.solaLettura" class="orz-sola-lettura">
            <p class="orz-sola-lettura-perche">
              {{ s.solaLettura === 'blocchi'
                ? t('Questo testo è scritto con l\'editor a blocchi: si cambia lì. Qui puoi cambiare tutto il resto.')
                : t('Questo testo ha parti che l\'editor della Redazione perderebbe: si cambia nella scheda di WordPress. Qui puoi cambiare tutto il resto.') }}
            </p>
            <p v-if="s.anteprimaTesto" class="orz-sola-lettura-testo">{{ s.anteprimaTesto }}</p>
            <a class="orz-btn orz-btn--piccolo" :href="s.wpModifica" target="_blank" rel="noopener">
              <Icona nome="esterno" :dim="14" /> {{ s.solaLettura === 'blocchi' ? t('Apri nell\'editor a blocchi') : t('Apri nella scheda di WordPress') }}
            </a>
          </div>
          <EditorTesto v-else v-model="f.testo" :spento="spento" :segnaposto="t('Scrivi qui il testo…')" />
        </div>

        <div v-if="tipo.campi.length" class="orz-blocco">
          <h2 class="orz-sezione">{{ t('Dettagli') }}</h2>
          <div class="orz-campi">
            <Campo
              v-for="c in tipo.campi"
              :key="c.chiave"
              v-model="f.campi[c.chiave]"
              :campo="c"
              :media="media"
              :spento="spento"
              @media="aggiungiMedia"
            />
          </div>
        </div>
      </div>

      <aside class="orz-pannello">
        <div class="orz-riquadro">
          <h2 class="orz-sezione">{{ t('Pubblicazione') }}</h2>
          <template v-if="uscito">
            <p class="orz-riga-info"><Icona nome="calendario" :dim="14" /> {{ t('Pubblicato il %s').replace('%s', s.dataLeggibile) }}</p>
            <button v-if="!cambiaData" type="button" class="orz-link" :disabled="spento" @click="apriData">{{ t('Cambia la data') }}</button>
            <input v-else v-model="f.data" type="datetime-local" :aria-label="t('Data di pubblicazione')" :disabled="spento" />
          </template>
          <fieldset v-else class="orz-quando" :disabled="spento">
            <legend>{{ t('Quando esce') }}</legend>
            <label class="orz-spunta"><input v-model="f.quando" type="radio" value="subito" /> <span>{{ t('Appena lo pubblichi') }}</span></label>
            <label class="orz-spunta"><input v-model="f.quando" type="radio" value="data" @change="dataProposta" /> <span>{{ t('In una data') }}</span></label>
            <input v-if="f.quando === 'data'" v-model="f.data" type="datetime-local" :aria-label="t('Data di uscita')" />
          </fieldset>
          <p v-if="s && s.autore" class="orz-riga-info">{{ t('Autore') }}: {{ s.autore }}</p>
          <p v-if="!tipo.pubblica" class="orz-nota">{{ t('Puoi scrivere e inviare in revisione: lo pubblica chi può farlo.') }}</p>
          <a v-if="s && s.anteprima" class="orz-link" :href="s.anteprima" target="_blank" rel="noopener" :title="modificata ? t('Mostra l\'ultima versione salvata') : ''">
            <Icona nome="occhio" :dim="14" /> {{ uscito ? t('Vedi la pagina') : t('Anteprima') }}
          </a>
        </div>

        <div v-if="tipo.tassonomie.length" class="orz-riquadro">
          <h2 class="orz-sezione">{{ t('Organizzazione') }}</h2>
          <Termini
            v-for="x in tipo.tassonomie"
            :key="x.nome"
            v-model="f.termini[x.nome]"
            :tassonomia="x"
            :spento="spento"
          />
        </div>

        <div v-if="tipo.immagine" class="orz-riquadro">
          <div class="orz-etichetta">
            <h2 class="orz-sezione">{{ t('Immagine in evidenza') }}</h2>
            <InfoTip :titolo="t('Immagine in evidenza')" :testo="t('La foto del contenuto negli elenchi, nelle card e, se non ne scegli una per i social, nei post dei social.')" />
          </div>
          <img v-if="immagineInfo && immagineInfo.mini" class="orz-evidenza" :src="immagineInfo.mini" alt="" />
          <span class="orz-riga-pulsanti">
            <button type="button" class="orz-btn orz-btn--piccolo" :disabled="spento" @click="scegliImmagine">
              <Icona nome="foto" :dim="14" /> {{ f.immagine ? t('Cambia la foto') : t('Scegli la foto') }}
            </button>
            <button v-if="f.immagine" type="button" class="orz-btn orz-btn--piccolo orz-btn--quieto" :disabled="spento" @click="f.immagine = 0">{{ t('Togli') }}</button>
          </span>
        </div>

        <div v-if="s" class="orz-riquadro orz-riquadro--altro">
          <a class="orz-link" :href="s.wpModifica" target="_blank" rel="noopener"><Icona nome="esterno" :dim="14" /> {{ t('Apri nella scheda di WordPress') }}</a>
          <button v-if="uscito" type="button" class="orz-link" :disabled="spento || occupato" @click="togliDallaPubblicazione">{{ t('Togli dalla pubblicazione') }}</button>
          <button v-if="s.puoCestinare" type="button" class="orz-link orz-link--pericolo" :disabled="spento || occupato" @click="cestina">
            <Icona nome="cestino" :dim="14" /> {{ t('Sposta nel cestino') }}
          </button>
        </div>
      </aside>
    </div>
  </section>
</template>

<script setup>
/**
 * La scheda di un contenuto: titolo, riassunto, testo, i campi del tipo; a destra pubblicazione, categorie e tag,
 * immagine in evidenza. Salva con Olobuild_Redazione::scrivi(); tiene il blocco di modifica di WordPress mentre è aperta
 * e non scrive sopra a chi ha salvato dopo (conflitto: si sceglie quale versione tenere).
 */
import { ref, reactive, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import InfoTip from '@/components/Builder/InfoTip.vue';
import Icona from './Icona.vue';
import EditorTesto from './EditorTesto.vue';
import Campo from './Campo.vue';
import Termini from './Termini.vue';
import { chiama } from './api.js';
import { scegli } from './media.js';
import { nomeStato } from './stati.js';

const props = defineProps({
  tipo: { type: Object, required: true },
  id: { type: Number, default: 0 },
});
const emit = defineEmits(['chiudi', 'salvato']);

const s = ref(null);
const f = reactive({ titolo: '', testo: '', riassunto: '', quando: 'subito', data: '', termini: {}, immagine: 0, campi: {} });
const media = reactive({});
const immagineInfo = computed(() => (f.immagine ? media[f.immagine] : null));
const originale = ref('');
const caricamento = ref(false);
const erroreCarica = ref('');
const occupato = ref(false);
const errore = ref('');
const avviso = ref('');
const conflitto = ref(null);
const bloccatoDa = ref('');
const spento = ref(false);
const salvatoAlle = ref('');
const cambiaData = ref(false);

function istantanea() {
  return JSON.stringify(f);
}
const modificata = computed(() => originale.value !== '' && istantanea() !== originale.value);

const uscito = computed(() => !!s.value && ['publish', 'private'].includes(s.value.stato));

const notaSalvataggio = computed(() => {
  if (modificata.value) return t('Modifiche non salvate');
  if (salvatoAlle.value) return t('Salvato alle %s').replace('%s', salvatoAlle.value);
  return s.value && s.value.modificatoLeggibile ? t('Modificato il %s').replace('%s', s.value.modificatoLeggibile) : '';
});

function dataFutura() {
  return f.quando === 'data' && f.data && new Date(f.data) > new Date();
}

const primaria = computed(() => {
  const st = s.value ? s.value.stato : 'draft';
  if (uscito.value) return { etichetta: t('Aggiorna'), azione: 'salva' };
  if (!props.tipo.pubblica) return { etichetta: t('Invia in revisione'), azione: 'revisione' };
  if (st === 'future') return f.quando === 'data' ? { etichetta: t('Aggiorna'), azione: 'programma' } : { etichetta: t('Pubblica adesso'), azione: 'pubblica' };
  if (dataFutura()) return { etichetta: t('Programma'), azione: 'programma' };
  return { etichetta: t('Pubblica'), azione: 'pubblica' };
});
const secondaria = computed(() => {
  if (uscito.value) return null;
  const st = s.value ? s.value.stato : 'draft';
  if (st === 'pending') return { etichetta: t('Salva'), azione: 'salva' };
  if (st === 'future') return { etichetta: t('Torna in bozza'), azione: 'bozza' };
  return { etichetta: t('Salva bozza'), azione: 'bozza' };
});

function aggiungiMedia(lista) {
  (lista || []).forEach((m) => {
    if (m && m.id) media[m.id] = { ...(media[m.id] || {}), ...m };
  });
}

/** Mette nella scheda ciò che arriva dal sito (lettura o salvataggio). */
function applica(r) {
  s.value = r;
  f.titolo = r.titolo || '';
  f.testo = r.testo || '';
  f.riassunto = r.riassunto || '';
  f.quando = r.stato === 'future' || (r.dataFissata && !['publish', 'private'].includes(r.stato)) ? 'data' : 'subito';
  f.data = r.data || '';
  const termini = {};
  props.tipo.tassonomie.forEach((x) => {
    termini[x.nome] = ((r.termini && r.termini[x.nome]) || []).slice();
  });
  f.termini = termini;
  f.immagine = r.immagine ? r.immagine.id : 0;
  const campi = {};
  props.tipo.campi.forEach((c) => {
    campi[c.chiave] = (r.campi && r.campi[c.chiave]) || '';
  });
  f.campi = campi;
  Object.values(r.media || {}).forEach((m) => aggiungiMedia([m]));
  if (r.immagine) aggiungiMedia([r.immagine]);
  cambiaData.value = false;
  originale.value = istantanea();
}

function vuota() {
  s.value = null;
  Object.assign(f, { titolo: '', testo: '', riassunto: '', quando: 'subito', data: '', immagine: 0 });
  const termini = {};
  props.tipo.tassonomie.forEach((x) => {
    termini[x.nome] = [];
  });
  f.termini = termini;
  const campi = {};
  props.tipo.campi.forEach((c) => {
    campi[c.chiave] = '';
  });
  f.campi = campi;
  spento.value = false;
  bloccatoDa.value = '';
  originale.value = istantanea();
}

async function carica() {
  errore.value = '';
  avviso.value = '';
  conflitto.value = null;
  erroreCarica.value = '';
  if (!props.id) {
    vuota();
    return;
  }
  caricamento.value = true;
  try {
    const r = await chiama('redazione/' + props.id);
    applica(r);
    bloccatoDa.value = r.bloccato || '';
    spento.value = !!r.bloccato;
  } catch (e) {
    erroreCarica.value = e.message;
  } finally {
    caricamento.value = false;
  }
}

async function ricarica() {
  if (modificata.value && !window.confirm(t('Le tue modifiche non salvate andranno perse. Continuare?'))) return;
  await carica();
}

function corpo(azione, forza) {
  const c = {
    tipo: props.tipo.chiave,
    titolo: f.titolo,
    riassunto: f.riassunto,
    azione,
    termini: f.termini,
    immagine: f.immagine || 0,
    campi: f.campi,
    modificato: s.value ? s.value.modificato : 0,
    forza: !!forza,
  };
  if (!s.value || !s.value.solaLettura) c.testo = f.testo;
  if (uscito.value) {
    c.data = s.value && f.data !== s.value.data ? f.data : '';
  } else {
    c.data = f.quando === 'data' ? f.data : '';
  }
  return c;
}

const MESSAGGI = {
  pubblica: () => t('Pubblicato.'),
  programma: () => t('Programmato.'),
  bozza: () => t('Salvato come bozza.'),
  revisione: () => t('Inviato in revisione.'),
  salva: () => t('Salvato.'),
};

let timerAvviso = null;
async function salva(azione, forza = false) {
  if (occupato.value || spento.value) return;
  if (azione === 'programma' && f.quando === 'data' && !f.data) {
    errore.value = t('Scegli la data di uscita.');
    return;
  }
  occupato.value = true;
  errore.value = '';
  conflitto.value = null;
  try {
    const nuovo = !props.id;
    const r = await chiama(nuovo ? 'redazione' : 'redazione/' + props.id, { metodo: 'POST', corpo: corpo(azione, forza) });
    applica(r);
    salvatoAlle.value = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    avviso.value = r.avviso || (r.stato === 'future' && azione !== 'bozza' ? t('Programmato.') : MESSAGGI[azione]());
    clearTimeout(timerAvviso);
    timerAvviso = setTimeout(() => {
      avviso.value = '';
    }, 5000);
    emit('salvato', { id: r.id, nuovo });
  } catch (e) {
    if (e.codice === 'olo_redazione_conflitto' || e.codice === 'olo_redazione_bloccato') {
      conflitto.value = { messaggio: e.message, azione };
    } else {
      errore.value = e.message;
    }
  } finally {
    occupato.value = false;
  }
}

function apriData() {
  cambiaData.value = true;
}
function dataProposta() {
  if (f.data) return;
  const d = new Date();
  d.setDate(d.getDate() + 1);
  d.setHours(9, 0, 0, 0);
  const due = (n) => String(n).padStart(2, '0');
  f.data = `${d.getFullYear()}-${due(d.getMonth() + 1)}-${due(d.getDate())}T09:00`;
}

function scegliImmagine() {
  scegli('immagine', (scelti) => {
    if (!scelti[0]) return;
    aggiungiMedia(scelti);
    f.immagine = scelti[0].id;
  });
}

function togliDallaPubblicazione() {
  if (window.confirm(t('Il contenuto sparisce dal sito e torna una bozza. Continuare?'))) salva('bozza');
}

async function cestina() {
  if (!window.confirm(t('Spostarlo nel cestino? Si può recuperare dal cestino di WordPress.'))) return;
  occupato.value = true;
  try {
    await chiama('redazione/' + props.id, { metodo: 'DELETE' });
    originale.value = istantanea();
    emit('chiudi', { ricarica: true });
  } catch (e) {
    errore.value = e.message;
  } finally {
    occupato.value = false;
  }
}

// Il blocco di modifica: rinnovato ogni minuto (WordPress lo tiene due minuti e mezzo).
let timerBlocco = null;
async function rinnova(prendere = false) {
  if (!props.id) return;
  try {
    const r = await chiama('redazione/' + props.id + '/blocco', { metodo: 'POST', corpo: prendere ? { prendi: true } : null });
    bloccatoDa.value = r.bloccato || '';
    spento.value = !!r.bloccato;
  } catch (e) {
    // Una rete che va e viene non deve fermare chi scrive: riprova al giro dopo.
  }
}
async function prendi() {
  await rinnova(true);
  if (!spento.value) await carica();
}

function tasti(e) {
  if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
    e.preventDefault();
    if (!spento.value && !occupato.value) salva('salva');
  }
}
function primaDiUscire(e) {
  if (modificata.value) {
    e.preventDefault();
    e.returnValue = '';
  }
}

watch(() => props.id, (nuovo) => {
  // Appena creato: la scheda ha già i dati dal salvataggio.
  if (s.value && s.value.id === nuovo) return;
  carica();
});

onMounted(() => {
  carica();
  timerBlocco = setInterval(() => {
    if (!spento.value) rinnova(false);
  }, 60000);
  window.addEventListener('keydown', tasti);
  window.addEventListener('beforeunload', primaDiUscire);
});
onBeforeUnmount(() => {
  clearInterval(timerBlocco);
  clearTimeout(timerAvviso);
  window.removeEventListener('keydown', tasti);
  window.removeEventListener('beforeunload', primaDiUscire);
});

defineExpose({ modificata });
</script>
