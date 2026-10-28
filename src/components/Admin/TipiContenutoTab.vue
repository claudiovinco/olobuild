<template>
  <div class="cfg-page-head">
    <div>
      <h1>{{ t('Tipi di contenuto') }}</h1>
      <p>{{ t('Notizie, comunicati, rubriche: ogni tipo ha la sua voce nel menu di WordPress, le sue categorie e i suoi campi. Chi scrive lo trova nella bacheca con i permessi di WordPress; OLObuild lo disegna coi template Singolo e Archivio e ne legge i campi.') }}</p>
    </div>
    <div class="head-actions">
      <button class="cfg-btn cfg-btn-primary" @click="aggiungiTipo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
        {{ t('Nuovo tipo') }}
      </button>
    </div>
  </div>

  <!-- Gli Articoli di WordPress: si rinominano (Notizie, Blog) e prendono campi in più -->
  <div class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><span class="dashicons dashicons-admin-post" aria-hidden="true"></span></div>
      <div>
        <h3>{{ articoli.nome || t('Articoli') }}</h3>
        <p>{{ t('Gli articoli di WordPress, con le loro categorie e sottocategorie.') }}</p>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Nome nel menu') }}</label><div class="hint">{{ t('Vuoto: Articoli. Per esempio Notizie e Notizia.') }}</div></div>
        <div class="control-col tc-due">
          <div class="cfg-input"><input type="text" v-model="articoli.nome" maxlength="40" :placeholder="t('Articoli')" :aria-label="t('Nome plurale')" /></div>
          <div class="cfg-input"><input type="text" v-model="articoli.singolare" maxlength="40" :placeholder="t('Articolo')" :aria-label="t('Nome singolare')" /></div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Editor') }}</label><div class="hint">{{ t('Semplice: titoli, grassetto, corsivo, link, elenchi e citazioni, come in un programma di scrittura.') }}</div></div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': articoli.editor === 'semplice' }" @click="articoli.editor = 'semplice'">{{ t('Semplice') }}</button>
            <button :class="{ 'is-on': articoli.editor !== 'semplice' }" @click="articoli.editor = 'blocchi'">{{ t('A blocchi') }}</button>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Campi') }}</label><div class="hint">{{ t('Compaiono nella scheda di ogni articolo, nel riquadro Dettagli.') }}</div><div v-if="(categorie.post || []).length" class="hint">{{ t('Un campo può valere solo in alcune categorie, sottocategorie comprese.') }}</div></div>
        <div class="control-col"><CfgCampi :campi="articoli.campi" :formati="formati" :categorie="categorie.post || []" /></div>
      </div>
    </div>
  </div>

  <div v-for="(tipo, i) in tipi" :key="tipo._id" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic"><span class="dashicons" :class="tipo.icona" aria-hidden="true"></span></div>
      <div>
        <h3>{{ tipo.nome || t('Tipo senza nome') }}</h3>
        <p>{{ tipo.chiave ? `/${tipo.indirizzo || '…'}/ · ${tipo.chiave}` : t('Scrivi il nome per cominciare') }}<template v-if="conteggi[tipo.chiave]"> · {{ conteggi[tipo.chiave] }} {{ t('contenuti') }}</template></p>
      </div>
      <div class="head-actions">
        <button class="cfg-btn-icon cfg-btn-danger" :title="t('Elimina il tipo')" :aria-label="t('Elimina il tipo')" @click="eliminaTipo(i)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
    </div>
    <div class="cfg-card-body">
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Nome') }}</label><div class="hint">{{ t('Plurale e singolare: Notizie e Notizia, Comunicati e Comunicato.') }}</div></div>
        <div class="control-col tc-due">
          <div class="cfg-input"><input type="text" :value="tipo.nome" maxlength="40" :placeholder="t('Notizie')" :aria-label="t('Nome plurale')" @input="setNome(tipo, $event.target.value)" /></div>
          <div class="cfg-input"><input type="text" v-model="tipo.singolare" maxlength="40" :placeholder="t('Notizia')" :aria-label="t('Nome singolare')" /></div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Chiave e indirizzo') }}</label><div class="hint">{{ t('La chiave non cambia più dopo il salvataggio: i contenuti sono legati a lei. L\'indirizzo è la parte dopo il dominio.') }}</div></div>
        <div class="control-col tc-due">
          <div class="cfg-input"><input type="text" :value="tipo.chiave" maxlength="20" spellcheck="false" :disabled="!tipo._nuovo" :aria-label="t('Chiave')" @input="setChiave(tipo, $event.target.value)" /></div>
          <div class="cfg-input tc-indirizzo"><span>/</span><input type="text" :value="tipo.indirizzo" maxlength="40" spellcheck="false" :aria-label="t('Indirizzo')" @input="setIndirizzo(tipo, $event.target.value)" /><span>/</span></div>
        </div>
      </div>
      <p v-if="chiaveOccupata(tipo)" class="tc-avviso">{{ t('Questa chiave è già usata da WordPress o da un altro plugin: scegline un\'altra.') }}</p>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Icona e posizione') }}</label><div class="hint">{{ t('La posizione nel menu: 5 sotto la Bacheca, 20 sotto le Pagine, 25 sotto i Commenti.') }}</div></div>
        <div class="control-col">
          <div class="tc-icone" role="radiogroup" :aria-label="t('Icona')">
            <button v-for="ic in icone" :key="ic" type="button" role="radio" :aria-checked="tipo.icona === ic ? 'true' : 'false'" :class="{ 'is-on': tipo.icona === ic }" :title="ic.replace('dashicons-', '')" @click="tipo.icona = ic"><span class="dashicons" :class="ic" aria-hidden="true"></span></button>
          </div>
          <CfgNumber class="tc-posizione" :model-value="tipo.posizione" :min="5" :max="100" @update:model-value="tipo.posizione = $event" />
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Organizzazione') }}</label></div>
        <div class="control-col tc-interruttori">
          <label v-for="s in ORGANIZZAZIONE" :key="s.key"><button type="button" class="cfg-switch" role="switch" :aria-checked="tipo[s.key] ? 'true' : 'false'" :class="{ 'is-on': tipo[s.key] }" @click="tipo[s.key] = !tipo[s.key]"></button><span>{{ t(s.label) }}</span></label>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Nella scheda') }}</label></div>
        <div class="control-col tc-interruttori">
          <label v-for="s in SCHEDA" :key="s.key"><button type="button" class="cfg-switch" role="switch" :aria-checked="tipo[s.key] ? 'true' : 'false'" :class="{ 'is-on': tipo[s.key] }" @click="tipo[s.key] = !tipo[s.key]"></button><span>{{ t(s.label) }}</span></label>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Editor') }}</label></div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': tipo.editor !== 'blocchi' }" @click="tipo.editor = 'semplice'">{{ t('Semplice') }}</button>
            <button :class="{ 'is-on': tipo.editor === 'blocchi' }" @click="tipo.editor = 'blocchi'">{{ t('A blocchi') }}</button>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Permessi') }}</label><div class="hint">{{ permessiHint(tipo) }}</div></div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': tipo.permessi !== 'propri' }" @click="tipo.permessi = 'articoli'">{{ t('Come gli articoli') }}</button>
            <button :class="{ 'is-on': tipo.permessi === 'propri' }" @click="tipo.permessi = 'propri'">{{ t('Propri') }}</button>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col"><label>{{ t('Campi') }}</label><div class="hint">{{ t('Compaiono nella scheda di ogni contenuto, nel riquadro Dettagli, e nella Sorgente dinamica come «Campi dei contenuti».') }}</div><div v-if="(categorie[tipo.chiave] || []).length" class="hint">{{ t('Un campo può valere solo in alcune categorie, sottocategorie comprese.') }}</div></div>
        <div class="control-col"><CfgCampi :campi="tipo.campi" :formati="formati" :categorie="categorie[tipo.chiave] || []" /></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, inject, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import CfgNumber from './controls/CfgNumber.vue';
import CfgCampi from './controls/CfgCampi.vue';
import { okOrThrow, cfgJob, assertLoaded, reloadJob } from './cfgSave';
import { useCfgLettura } from './composables/useCfgLettura';

const TAB_ID = 'tipicontenuto';
const shellDirty = inject('setDirty', () => {});
const setDirty = (v) => shellDirty(v, TAB_ID);
const loaded = ref(false);
const lettura = useCfgLettura(TAB_ID, loaded, () => carica());

const ORGANIZZAZIONE = [
  { key: 'archivio', label: 'Pagina con tutti i contenuti' },
  { key: 'categorie', label: 'Categorie e sottocategorie' },
  { key: 'tag', label: 'Tag' },
];
const SCHEDA = [
  { key: 'estratto', label: 'Riassunto' },
  { key: 'immagine', label: 'Immagine in evidenza' },
  { key: 'autore', label: 'Autore' },
  { key: 'commenti', label: 'Commenti' },
];

const articoli = ref({ nome: '', singolare: '', editor: 'blocchi', campi: [] });
const tipi = ref([]);
const icone = ref([]);
const formati = ref([]);
// Le categorie di ogni tipo (chiave => [{ value, label }]), per «Solo nelle categorie» sui campi.
const categorie = ref({});
const riservati = ref([]);
const conteggi = ref({});
let pronto = false;

let prossimo = 1;
function nuovoId() { prossimo += 1; return 't' + Date.now().toString(36) + prossimo; }
function slug(testo, sep) {
  return String(testo || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase()
    .replace(/[^a-z0-9]+/g, sep).replace(new RegExp(`^\\${sep}+|\\${sep}+$`, 'g'), '');
}

function aggiungiTipo() {
  tipi.value.push({
    _id: nuovoId(), _nuovo: true, chiave: '', nome: '', singolare: '', indirizzo: '', icona: 'dashicons-megaphone',
    posizione: 21, archivio: true, categorie: true, tag: false, editor: 'semplice', estratto: true, immagine: true,
    autore: false, commenti: false, permessi: 'articoli', campi: [],
  });
}
// Finché il tipo è nuovo, chiave e indirizzo seguono il nome (a meno che non siano stati scritti a mano).
function setNome(tipo, v) {
  tipo.nome = v;
  if (tipo._nuovo && !tipo._chiaveScritta) tipo.chiave = slug(tipo.singolare || v, '_').slice(0, 20);
  if (!tipo._indirizzoScritto) tipo.indirizzo = slug(v, '-').slice(0, 40);
}
function setChiave(tipo, v) { tipo._chiaveScritta = true; tipo.chiave = slug(v, '_').slice(0, 20); }
function setIndirizzo(tipo, v) { tipo._indirizzoScritto = true; tipo.indirizzo = slug(v, '-').slice(0, 40); }
function chiaveOccupata(tipo) {
  return !!tipo._nuovo && !!tipo.chiave && (riservati.value.includes(tipo.chiave) || tipi.value.filter((x) => x.chiave === tipo.chiave).length > 1);
}
function permessiHint(tipo) {
  return tipo.permessi === 'propri'
    ? t('Amministratori e redattori possono tutto; il ruolo «Redazione {nome}» scrive e pubblica solo qui. Assegnalo in Utenti.').replace('{nome}', tipo.nome || '…')
    : t('Chi può scrivere articoli può scrivere anche qui.');
}
function eliminaTipo(i) {
  const tipo = tipi.value[i];
  const n = conteggi.value[tipo.chiave] || 0;
  const msg = n
    ? t('Eliminare il tipo? I suoi {n} contenuti restano nel database ma spariscono dalla bacheca finché non ricrei un tipo con la stessa chiave.').replace('{n}', n)
    : t('Eliminare il tipo?');
  if (!confirm(msg)) return;
  tipi.value.splice(i, 1);
}

function applica(data) {
  pronto = false;
  const imp = data?.impostazioni || {};
  // Un id per ogni campo: la chiave dell'elenco resta stabile mentre si riordina.
  const conId = (campi) => (campi || []).map((c) => ({ ...c, _id: nuovoId(), _chiaveScritta: true }));
  articoli.value = { nome: '', singolare: '', editor: 'blocchi', ...(imp.articoli || {}), campi: conId(imp.articoli?.campi) };
  tipi.value = (imp.tipi || []).map((x) => ({ ...x, campi: conId(x.campi), _id: nuovoId(), _nuovo: false, _chiaveScritta: true, _indirizzoScritto: true }));
  icone.value = data?.icone || [];
  formati.value = data?.formati || [];
  categorie.value = data?.categorie || {};
  riservati.value = data?.riservati || [];
  conteggi.value = data?.conteggi || {};
  nextTick(() => { pronto = true; });
}

async function carica(invariato) {
  try {
    const res = await lettura.fetch(`${window.oloData.restUrl}tipi-contenuto`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    if (res.ok) {
      const data = await res.json();
      if (invariato && !invariato()) return;
      applica(data);
      loaded.value = true;
    }
  } catch (e) { /* resta lo stato di lettura */ }
}

async function salva() {
  assertLoaded(loaded);
  const res = await okOrThrow(fetch(`${window.oloData.restUrl}tipi-contenuto`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce },
    body: JSON.stringify({ impostazioni: { articoli: articoli.value, tipi: tipi.value.filter((x) => !chiaveOccupata(x)) } }),
  }));
  applica(await res.json());
}

// Ogni modifica (anche dentro i campi) segna la scheda da salvare.
watch([articoli, tipi], () => { if (pronto) setDirty(true); }, { deep: true });

const onSave = cfgJob(TAB_ID, salva);
const onDiscard = cfgJob(TAB_ID, reloadJob(loaded, carica));
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

<style scoped>
.tc-due { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
.tc-indirizzo { display: flex; align-items: center; gap: 2px; }
.tc-indirizzo span { color: var(--c-text-mute); }
.tc-indirizzo input { flex: 1; min-width: 0; }
.tc-avviso { margin: -4px 0 8px; color: var(--c-red); font-size: 12px; }
.tc-icone { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
.tc-icone button { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--c-border); border-radius: 6px; background: transparent; color: var(--c-text-mute); cursor: pointer; }
.tc-icone button.is-on { border-color: var(--c-red); color: var(--c-red); }
.tc-icone button:focus-visible { outline: 2px solid var(--c-red); outline-offset: 2px; }
.tc-posizione { max-width: 140px; }
.tc-interruttori { display: flex; flex-wrap: wrap; gap: 10px 20px; }
.tc-interruttori label { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
.head-ic .dashicons { width: 20px; height: 20px; font-size: 20px; }
@media (max-width: 720px) {
  .tc-due { grid-template-columns: minmax(0, 1fr); }
}
</style>
